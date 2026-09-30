<?php
/**
 * Instagram Widget
 *
 * Kullanim (include):
 *   include 'widget.php';
 *   echo ig_widget('ailevenesil');
 *   echo ig_widget('ailevenesil', 12, 7200, 'portrait', 2);
 *
 * Kullanim (direct / query string):
 *   widget.php?username=ailevenesil&count=9
 *   widget.php?username=ailevenesil&count=9&json
 *   widget.php?username=ailevenesil&count=6&layout=portrait&cols=2
 *   widget.php?username=ailevenesil&count=8&layout=slider
 *   widget.php?username=ailevenesil&count=4&layout=list
 *   widget.php?username=ailevenesil&count=6&layout=card&cols=2
 *   widget.php?username=ailevenesil&count=9&layout=grid&cols=3&theme=dark
 *
 * Parametreler:
 *   username  - Instagram kullanici adi
 *   count     - Post sayisi (1-30)
 *   layout    - grid | portrait | slider | list | card  (default: grid)
 *   cols      - Kolon sayisi, layout'a gore (1-6, default: 3)
 *   theme     - light | dark  (default: light)
 *   json      - Raw JSON ciktisi
 *   flush     - Cache temizle
 */

// ──────────────────────────────────────────────
//  Config
// ──────────────────────────────────────────────
define('IG_DEFAULT_USERNAME', 'ailevenesil');
define('IG_DEFAULT_COUNT', 9);
define('IG_CACHE_DIR', __DIR__ . '/ig_cache');
define('IG_CACHE_TTL', 6 * 3600); // 6 saat
define('IG_IMAGE_PROXY', 'ig_proxy.php');

// ──────────────────────────────────────────────
//  Cache
// ──────────────────────────────────────────────
function ig_cache_path(string $username, int $count): string {
    $safe = preg_replace('/[^a-zA-Z0-9._-]/', '', $username);
    return IG_CACHE_DIR . "/{$safe}_{$count}.json";
}

function ig_cache_read(string $username, int $count, int $ttl = IG_CACHE_TTL): ?array {
    $path = ig_cache_path($username, $count);
    if (!file_exists($path)) return null;
    if (time() - filemtime($path) > $ttl) return null;
    $data = json_decode(file_get_contents($path), true);
    return ($data && !empty($data['ok']) && !empty($data['items'])) ? $data : null;
}

function ig_cache_write(string $username, int $count, array $data): void {
    if (!is_dir(IG_CACHE_DIR)) mkdir(IG_CACHE_DIR, 0755, true);
    file_put_contents(
        ig_cache_path($username, $count),
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
        LOCK_EX
    );
}

// ──────────────────────────────────────────────
//  HTTP helper
// ──────────────────────────────────────────────
function ig_http(string $url, array $extra_headers = []): ?string {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_ENCODING       => '',
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) '
            . 'AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER => array_merge([
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-User: ?1',
        ], $extra_headers),
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ($code === 200 && $body) ? $body : null;
}

// ──────────────────────────────────────────────
//  Scraping
// ──────────────────────────────────────────────
function ig_scrape(string $username, int $count = 9): array {
    $result = [
        'ok'      => false,
        'profile' => ['username' => $username, 'full_name' => null, 'profile_pic_url' => null],
        'count'   => 0,
        'items'   => [],
        'scraped_at' => date('c'),
    ];

    // ---------- Method 1: web_profile_info API ----------
    $api = ig_http(
        "https://www.instagram.com/api/v1/users/web_profile_info/?username={$username}",
        [
            'X-IG-App-ID: 936619743392459',
            'X-Requested-With: XMLHttpRequest',
            'Referer: https://www.instagram.com/',
        ]
    );
    if ($api) {
        $json = json_decode($api, true);
        $user = $json['data']['user'] ?? null;
        if ($user && !empty($user['edge_owner_to_timeline_media']['edges'])) {
            return ig_build_from_graphql($user, $username, $count);
        }
    }

    // ---------- Method 2: Profile HTML - _sharedData / embedded JSON ----------
    $html = ig_http("https://www.instagram.com/{$username}/");
    if ($html) {
        // 2a: window._sharedData
        if (preg_match('/window\._sharedData\s*=\s*(\{.+?\});\s*<\/script>/s', $html, $m)) {
            $shared = json_decode($m[1], true);
            $user = $shared['entry_data']['ProfilePage'][0]['graphql']['user'] ?? null;
            if ($user && !empty($user['edge_owner_to_timeline_media']['edges'])) {
                return ig_build_from_graphql($user, $username, $count);
            }
        }

        // 2b: Inline edge_owner_to_timeline_media blob
        if (preg_match('/"edge_owner_to_timeline_media":\{"count":\d+,"page_info":\{[^}]+\},"edges":(\[.*?\])\}/s', $html, $m)) {
            $edges = json_decode($m[1], true);
            if ($edges) {
                return ig_build_from_edges($edges, $username, $count, $result);
            }
        }

        // 2c: xdt_api relay style data (newer IG builds)
        if (preg_match_all('/"xdt_api__v1__feed__user_timeline_graphql_connection":\{.*?"edges":(\[[^\]]*?\])/s', $html, $m)) {
            $edges = json_decode($m[1][0], true);
            if ($edges) {
                return ig_build_from_edges($edges, $username, $count, $result);
            }
        }

        // 2d: LD+JSON for at least the profile info
        if (preg_match('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/s', $html, $m)) {
            $ld = json_decode($m[1], true);
            if ($ld) {
                $entity = $ld['mainEntity'] ?? $ld;
                $result['profile']['full_name']       = $entity['name'] ?? null;
                $result['profile']['profile_pic_url']  = $entity['image'] ?? null;
            }
        }

        // 2e: og:image meta (profile pic fallback)
        if (!$result['profile']['profile_pic_url'] && preg_match('/property="og:image"\s+content="([^"]+)"/', $html, $m)) {
            $result['profile']['profile_pic_url'] = $m[1];
        }
    }

    // ---------- Method 3: ?__a=1&__d=dis ----------
    $alt = ig_http("https://www.instagram.com/{$username}/?__a=1&__d=dis");
    if ($alt) {
        $json = json_decode($alt, true);
        $user = $json['graphql']['user'] ?? $json['data']['user'] ?? null;
        if ($user && !empty($user['edge_owner_to_timeline_media']['edges'])) {
            return ig_build_from_graphql($user, $username, $count);
        }
    }

    $result['note'] = 'Scraping basarisiz. Instagram rate limit veya layout degisikligi olabilir.';
    return $result;
}

function ig_build_from_graphql(array $user, string $username, int $count): array {
    $result = [
        'ok'      => true,
        'profile' => [
            'username'        => $username,
            'full_name'       => $user['full_name'] ?? null,
            'profile_pic_url' => $user['profile_pic_url_hd'] ?? $user['profile_pic_url'] ?? null,
        ],
        'count'   => 0,
        'items'   => [],
        'scraped_at' => date('c'),
    ];
    $edges = $user['edge_owner_to_timeline_media']['edges'] ?? [];
    return ig_build_from_edges($edges, $username, $count, $result);
}

function ig_build_from_edges(array $edges, string $username, int $count, array $result): array {
    $items = [];
    foreach (array_slice($edges, 0, $count) as $edge) {
        $node = $edge['node'] ?? $edge;
        $thumb = $node['thumbnail_src']
            ?? $node['display_url']
            ?? ($node['thumbnail_resources'][count($node['thumbnail_resources'] ?? []) - 1]['src'] ?? null);
        $items[] = [
            'id'        => $node['id'] ?? null,
            'shortcode' => $node['shortcode'] ?? null,
            'thumbnail' => $thumb,
            'display'   => $node['display_url'] ?? $thumb,
            'caption'   => $node['edge_media_to_caption']['edges'][0]['node']['text'] ?? '',
            'likes'     => $node['edge_liked_by']['count'] ?? $node['edge_media_preview_like']['count'] ?? 0,
            'comments'  => $node['edge_media_to_comment']['count'] ?? 0,
            'is_video'  => $node['is_video'] ?? false,
            'link'      => 'https://www.instagram.com/p/' . ($node['shortcode'] ?? ''),
            'timestamp' => $node['taken_at_timestamp'] ?? null,
        ];
    }
    $result['items'] = $items;
    $result['count'] = count($items);
    $result['ok']    = count($items) > 0;
    return $result;
}

// ──────────────────────────────────────────────
//  Public API
// ──────────────────────────────────────────────
function ig_get_data(string $username = IG_DEFAULT_USERNAME, int $count = IG_DEFAULT_COUNT, int $ttl = IG_CACHE_TTL): array {
    $data = ig_cache_read($username, $count, $ttl);
    if (!$data) {
        $data = ig_scrape($username, $count);
        ig_cache_write($username, $count, $data);
    }
    return $data;
}

/**
 * Widget HTML dondurur.
 *
 * @param string $layout   grid | portrait | slider | list | card
 * @param int    $cols     Kolon sayisi (grid/portrait/card icin)
 * @param string $theme    light | dark
 */
function ig_widget(
    string $username = IG_DEFAULT_USERNAME,
    int    $count    = IG_DEFAULT_COUNT,
    int    $ttl      = IG_CACHE_TTL,
    string $layout   = 'grid',
    int    $cols     = 3,
    string $theme    = 'light'
): string {
    $data   = ig_get_data($username, $count, $ttl);
    $items  = $data['items'] ?? [];
    $proxy  = IG_IMAGE_PROXY;
    $uname  = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $pLink  = "https://www.instagram.com/{$uname}/";
    $layout = in_array($layout, ['grid','portrait','slider','list','card']) ? $layout : 'grid';
    $cols   = max(1, min(6, $cols));
    $theme  = ($theme === 'dark') ? 'dark' : 'light';
    $wid    = 'igw-' . substr(md5($uname . $layout), 0, 6);

    ob_start();
    ?>
    <div class="ig-widget ig-widget--<?= $layout ?> ig-widget--<?= $theme ?>" id="<?= $wid ?>">
        <?php echo ig_render_header($uname, $pLink, $data, $proxy); ?>

        <?php if (empty($items)): ?>
        <div class="ig-empty">
            <p>Postlar yüklenemedi.</p>
            <a href="<?= $pLink ?>" target="_blank" rel="noopener">Instagram'da&nbsp;görüntüle&nbsp;&rarr;</a>
        </div>
        <?php else:
            switch ($layout) {
                case 'slider':   ig_render_slider($items, $proxy, $cols, $wid); break;
                case 'list':     ig_render_list($items, $proxy); break;
                case 'card':     ig_render_cards($items, $proxy, $cols); break;
                case 'portrait': ig_render_grid($items, $proxy, $cols, '4/5'); break;
                default:         ig_render_grid($items, $proxy, $cols, '1/1'); break;
            }
        endif; ?>

        <a href="<?= $pLink ?>" target="_blank" rel="noopener" class="ig-follow">
            Instagram'da Takip Et &rarr;
        </a>
    </div>
    <?php ig_render_styles(); ?>
    <?php
    return ob_get_clean();
}

// ── Render partials ──────────────────────────

function ig_render_header(string $uname, string $pLink, array $data, string $proxy): string {
    $pic = $data['profile']['profile_pic_url'] ?? '';
    $avatarHtml = $pic
        ? '<img src="' . htmlspecialchars($proxy . '?url=' . urlencode($pic), ENT_QUOTES, 'UTF-8') . '" alt="' . $uname . '" class="ig-avatar" width="36" height="36">'
        : '<span class="ig-avatar ig-avatar--empty"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5M12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10m0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg></span>';
    $igIcon = '<svg class="ig-ig-logo" viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5M12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10m0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>';
    return '<a href="' . $pLink . '" target="_blank" rel="noopener" class="ig-header">' . $avatarHtml . '<span class="ig-uname">@' . $uname . '</span>' . $igIcon . '</a>';
}

function ig_item_vars(array $item, string $proxy): array {
    return [
        'src'     => htmlspecialchars($proxy . '?url=' . urlencode($item['thumbnail'] ?: $item['display']), ENT_QUOTES, 'UTF-8'),
        'href'    => htmlspecialchars($item['link'], ENT_QUOTES, 'UTF-8'),
        'caption' => htmlspecialchars(mb_strimwidth($item['caption'], 0, 120, '…'), ENT_QUOTES, 'UTF-8'),
        'likes'   => number_format($item['likes']),
        'comments'=> number_format($item['comments']),
        'video'   => $item['is_video'] ?? false,
        'time'    => $item['timestamp'] ? date('d.m.Y', $item['timestamp']) : '',
    ];
}

function ig_hover_overlay(array $v): void { ?>
    <span class="ig-hover">
        <?php if ($v['video']): ?><svg class="ig-play" viewBox="0 0 24 24" fill="#fff" width="28" height="28"><path d="M8 5v14l11-7z"/></svg><?php endif; ?>
        <span class="ig-stats">
            <span>&#9825; <?= $v['likes'] ?></span>
            <span>&#128172; <?= $v['comments'] ?></span>
        </span>
    </span>
<?php }

/* LAYOUT: grid / portrait */
function ig_render_grid(array $items, string $proxy, int $cols, string $ratio): void { ?>
    <div class="ig-grid" style="--ig-cols:<?= $cols ?>;--ig-ratio:<?= $ratio ?>">
        <?php foreach ($items as $item):
            $v = ig_item_vars($item, $proxy); ?>
        <a href="<?= $v['href'] ?>" target="_blank" rel="noopener" class="ig-item" title="<?= $v['caption'] ?>">
            <img src="<?= $v['src'] ?>" alt="" loading="lazy">
            <?php ig_hover_overlay($v); ?>
        </a>
        <?php endforeach; ?>
    </div>
<?php }

/* LAYOUT: slider (grid-style pages with prev/next buttons) */
function ig_render_slider(array $items, string $proxy, int $cols, string $wid): void {
    $total = count($items);
    $pages = (int) ceil($total / $cols);
    ?>
    <div class="ig-slider" data-cols="<?= $cols ?>" data-pages="<?= $pages ?>" id="<?= $wid ?>-slider">
        <div class="ig-slider-viewport">
            <div class="ig-slider-track" style="--ig-cols:<?= $cols ?>">
                <?php foreach ($items as $item):
                    $v = ig_item_vars($item, $proxy); ?>
                <a href="<?= $v['href'] ?>" target="_blank" rel="noopener" class="ig-item" title="<?= $v['caption'] ?>">
                    <img src="<?= $v['src'] ?>" alt="" loading="lazy">
                    <?php ig_hover_overlay($v); ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if ($pages > 1): ?>
        <button class="ig-slider-btn ig-slider-prev" aria-label="Önceki" type="button">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M15.41 7.41 14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
        </button>
        <button class="ig-slider-btn ig-slider-next" aria-label="Sonraki" type="button">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M10 6 8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
        </button>
        <div class="ig-slider-dots">
            <?php for ($i = 0; $i < $pages; $i++): ?>
            <span class="ig-slider-dot<?= $i === 0 ? ' ig-slider-dot--active' : '' ?>"></span>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
    <script>
    (function(){
        var el = document.getElementById('<?= $wid ?>-slider');
        if (!el) return;
        var track = el.querySelector('.ig-slider-track'),
            prev  = el.querySelector('.ig-slider-prev'),
            next  = el.querySelector('.ig-slider-next'),
            dots  = el.querySelectorAll('.ig-slider-dot'),
            pages = <?= $pages ?>,
            page  = 0;

        function go(p) {
            page = Math.max(0, Math.min(pages - 1, p));
            track.style.transform = 'translateX(-' + (page * 100) + '%)';
            if (prev) prev.classList.toggle('ig-slider-btn--hidden', page === 0);
            if (next) next.classList.toggle('ig-slider-btn--hidden', page === pages - 1);
            dots.forEach(function(d, i) { d.classList.toggle('ig-slider-dot--active', i === page); });
        }
        if (prev) prev.addEventListener('click', function() { go(page - 1); });
        if (next) next.addEventListener('click', function() { go(page + 1); });
        dots.forEach(function(d, i) { d.addEventListener('click', function() { go(i); }); });
        go(0);
    })();
    </script>
<?php }

/* LAYOUT: list (vertical, full width + caption) */
function ig_render_list(array $items, string $proxy): void { ?>
    <div class="ig-list">
        <?php foreach ($items as $item):
            $v = ig_item_vars($item, $proxy); ?>
        <a href="<?= $v['href'] ?>" target="_blank" rel="noopener" class="ig-list-item">
            <div class="ig-list-img">
                <img src="<?= $v['src'] ?>" alt="" loading="lazy">
                <?php if ($v['video']): ?><svg class="ig-play" viewBox="0 0 24 24" fill="#fff" width="28" height="28"><path d="M8 5v14l11-7z"/></svg><?php endif; ?>
            </div>
            <div class="ig-list-body">
                <p class="ig-list-caption"><?= $v['caption'] ?: '&nbsp;' ?></p>
                <span class="ig-list-meta">
                    <span>&#9825; <?= $v['likes'] ?></span>
                    <span>&#128172; <?= $v['comments'] ?></span>
                    <?php if ($v['time']): ?><span><?= $v['time'] ?></span><?php endif; ?>
                </span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
<?php }

/* LAYOUT: card (grid of cards with caption below) */
function ig_render_cards(array $items, string $proxy, int $cols): void { ?>
    <div class="ig-cards" style="--ig-cols:<?= $cols ?>">
        <?php foreach ($items as $item):
            $v = ig_item_vars($item, $proxy); ?>
        <a href="<?= $v['href'] ?>" target="_blank" rel="noopener" class="ig-card">
            <div class="ig-card-img">
                <img src="<?= $v['src'] ?>" alt="" loading="lazy">
                <?php ig_hover_overlay($v); ?>
            </div>
            <div class="ig-card-body">
                <p class="ig-card-caption"><?= $v['caption'] ?: '&nbsp;' ?></p>
                <span class="ig-card-meta">
                    <span>&#9825; <?= $v['likes'] ?></span>
                    <span>&#128172; <?= $v['comments'] ?></span>
                </span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
<?php }

// ── Styles ───────────────────────────────────

function ig_render_styles(): void {
    static $rendered = false;
    if ($rendered) return;
    $rendered = true;
    ?>
    <style>
    /* ── Base ── */
    .ig-widget{width:100%;max-width:100%;margin:0;background:#fff;border:none;border-radius:0;overflow:hidden;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;color:#262626}
    .ig-header{display:flex;align-items:center;gap:10px;padding:14px 0;text-decoration:none;color:inherit;border-bottom:1px solid #efefef;transition:background .2s}
    .ig-header:hover{background:#fafafa}
    .ig-avatar{width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid #e1306c}
    .ig-avatar--empty{display:inline-flex;align-items:center;justify-content:center;background:#efefef;color:#8e8e8e}
    .ig-uname{font-weight:600;font-size:14px;flex:1}
    .ig-ig-logo{margin-left:auto;color:#e1306c}
    .ig-follow{display:block;padding:14px 0;text-align:center;border-top:1px solid #efefef;color:#0095f6;font-weight:600;font-size:14px;text-decoration:none;transition:background .2s}
    .ig-follow:hover{background:#fafafa;color:#00376b}
    .ig-empty{padding:40px 16px;text-align:center;color:#8e8e8e}
    .ig-empty a{color:#0095f6;font-weight:600;text-decoration:none}
    .ig-hover{position:absolute;inset:0;background:rgba(0,0,0,.35);display:flex;flex-direction:column;align-items:center;justify-content:center;opacity:0;transition:opacity .25s}
    .ig-play{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);filter:drop-shadow(0 1px 3px rgba(0,0,0,.4));z-index:1}
    .ig-hover .ig-play{position:static;transform:none;margin-bottom:6px}
    .ig-stats{display:flex;gap:14px;color:#fff;font-size:13px;font-weight:600}

    /* ── grid / portrait ── */
    .ig-grid{display:grid;grid-template-columns:repeat(var(--ig-cols,3),1fr);gap:6px;padding:0}
    .ig-item{position:relative;aspect-ratio:var(--ig-ratio,1/1);overflow:hidden;display:block;background:#fafafa}
    .ig-item img{width:100%;height:100%;object-fit:cover;transition:transform .35s ease}
    .ig-item:hover img{transform:scale(1.06)}
    .ig-item:hover .ig-hover{opacity:1}

    /* ── slider ── */
    .ig-slider{position:relative}
    .ig-slider-viewport{overflow:hidden}
    .ig-slider .ig-slider-track{display:grid;grid-auto-flow:column;grid-auto-columns:calc(100% / var(--ig-cols,3));transition:transform .4s cubic-bezier(.4,0,.2,1)}
    .ig-slider .ig-item{aspect-ratio:4/5 !important;padding:0 3px;box-sizing:border-box}
    .ig-slider .ig-item img{border-radius:6px;width:100%;height:100%;object-fit:cover}
    .ig-slider-btn{position:absolute;top:50%;transform:translateY(calc(-50% - 14px));width:36px;height:36px;border-radius:50%;border:none;background:rgba(255,255,255,.9);color:#262626;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.15);z-index:2;transition:opacity .2s,background .2s}
    .ig-slider-btn:hover{background:#fff;box-shadow:0 2px 12px rgba(0,0,0,.25)}
    .ig-slider-btn--hidden{opacity:0;pointer-events:none}
    .ig-slider-prev{left:8px}
    .ig-slider-next{right:8px}
    .ig-slider-dots{display:flex;justify-content:center;gap:6px;padding:10px 0 4px}
    .ig-slider-dot{width:7px;height:7px;border-radius:50%;background:#ccc;cursor:pointer;transition:background .2s}
    .ig-slider-dot--active{background:#e1306c}
    .ig-widget--dark .ig-slider-btn{background:rgba(40,40,40,.9);color:#f5f5f5}
    .ig-widget--dark .ig-slider-btn:hover{background:rgba(60,60,60,.95)}
    .ig-widget--dark .ig-slider-dot{background:#444}
    .ig-widget--dark .ig-slider-dot--active{background:#e1306c}

    /* ── list ── */
    .ig-list{display:flex;flex-direction:column}
    .ig-list-item{display:flex;gap:14px;padding:14px 16px;text-decoration:none;color:inherit;border-bottom:1px solid #efefef;transition:background .2s}
    .ig-list-item:last-child{border-bottom:0}
    .ig-list-item:hover{background:#fafafa}
    .ig-list-img{position:relative;flex:0 0 100px;width:100px;height:100px;border-radius:8px;overflow:hidden;background:#fafafa}
    .ig-list-img img{width:100%;height:100%;object-fit:cover}
    .ig-list-body{flex:1;display:flex;flex-direction:column;justify-content:center;min-width:0}
    .ig-list-caption{margin:0 0 8px;font-size:13px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:3;line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
    .ig-list-meta{display:flex;gap:12px;font-size:12px;color:#8e8e8e}

    /* ── card ── */
    .ig-cards{display:grid;grid-template-columns:repeat(var(--ig-cols,2),1fr);gap:12px;padding:12px}
    .ig-card{display:flex;flex-direction:column;border-radius:8px;overflow:hidden;text-decoration:none;color:inherit;border:1px solid #efefef;transition:box-shadow .25s}
    .ig-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.1)}
    .ig-card-img{position:relative;aspect-ratio:4/5;overflow:hidden;background:#fafafa}
    .ig-card-img img{width:100%;height:100%;object-fit:cover;transition:transform .35s ease}
    .ig-card:hover .ig-card-img img{transform:scale(1.04)}
    .ig-card:hover .ig-hover{opacity:1}
    .ig-card-body{padding:10px 12px}
    .ig-card-caption{margin:0 0 6px;font-size:12px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
    .ig-card-meta{display:flex;gap:10px;font-size:11px;color:#8e8e8e}

    /* ── dark theme ── */
    .ig-widget--dark{background:#1a1a1a;border:none;color:#f5f5f5}
    .ig-widget--dark .ig-header{border-color:#333}
    .ig-widget--dark .ig-header:hover{background:#222}
    .ig-widget--dark .ig-avatar--empty{background:#333;color:#666}
    .ig-widget--dark .ig-follow{border-color:#333;color:#58a6ff}
    .ig-widget--dark .ig-follow:hover{background:#222;color:#79c0ff}
    .ig-widget--dark .ig-empty{color:#666}
    .ig-widget--dark .ig-empty a{color:#58a6ff}
    .ig-widget--dark .ig-item,.ig-widget--dark .ig-slide,.ig-widget--dark .ig-list-img,.ig-widget--dark .ig-card-img{background:#222}
    .ig-widget--dark .ig-list-item{border-color:#333}
    .ig-widget--dark .ig-list-item:hover{background:#222}
    .ig-widget--dark .ig-list-meta,.ig-widget--dark .ig-card-meta{color:#666}
    .ig-widget--dark .ig-card{border-color:#333}
    .ig-widget--dark .ig-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.4)}

    /* ── responsive ── */
    @media(max-width:480px){
        .ig-stats{font-size:11px;gap:8px}
        .ig-avatar{width:30px;height:30px}
        .ig-slider-btn{width:30px;height:30px}
        .ig-slider-btn svg{width:18px;height:18px}
        .ig-cards{grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:8px;padding:8px}
        .ig-list-img{flex:0 0 80px;width:80px;height:80px}
    }
    </style>
<?php }

// ──────────────────────────────────────────────
//  Direct access: widget.php?username=xxx&count=9&layout=portrait&cols=2&theme=dark
// ──────────────────────────────────────────────
if (php_sapi_name() !== 'cli' && realpath($_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)) {
    $username = preg_replace('/[^a-zA-Z0-9._]/', '', $_GET['username'] ?? IG_DEFAULT_USERNAME);
    $count    = max(1, min(30, (int) ($_GET['count'] ?? IG_DEFAULT_COUNT)));
    $layout   = $_GET['layout'] ?? 'grid';
    $cols     = max(1, min(6, (int) ($_GET['cols'] ?? 3)));
    $theme    = $_GET['theme'] ?? 'light';

    if (isset($_GET['json'])) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: public, max-age=300');
        echo json_encode(ig_get_data($username, $count), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    if (isset($_GET['flush'])) {
        $path = ig_cache_path($username, $count);
        if (file_exists($path)) unlink($path);
        header('Location: ?username=' . urlencode($username) . '&count=' . $count . '&layout=' . $layout . '&cols=' . $cols . '&theme=' . $theme);
        exit;
    }

    $bg = $theme === 'dark' ? '#111' : '#f5f5f5';
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width">';
    echo '<title>IG Widget - @' . htmlspecialchars($username) . '</title></head>';
    echo '<body style="margin:20px;background:' . $bg . '">';
    echo ig_widget($username, $count, IG_CACHE_TTL, $layout, $cols, $theme);
    echo '</body></html>';
}
