<?php
function shouldShowContactSection($page_name, $db) {
    if (empty($page_name) || !$db) return false;

    $page_name = trim(strtolower($page_name));
    $page_name = str_replace(['-', ' '], '_', $page_name);

    if (in_array($page_name, ['iletisim', 'contact', 'contact_us'])) return false;

    try {
        static $cache = [];

        if (isset($cache[$page_name])) return $cache[$page_name];

        // 1️⃣ Doğrudan eşleşme
        $stmt = $db->prepare("SELECT show_contact_section FROM contact_section_settings WHERE page_name = ? LIMIT 1");
        $stmt->execute([$page_name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $cache[$page_name] = ($row['show_contact_section'] == 1);
            return $cache[$page_name];
        }

        // 2️⃣ sabit_url tablosundan SEO + yol bazlı eşleştirme
        $urls = $db->query("SELECT * FROM sabit_url LIMIT 1")->fetch(PDO::FETCH_ASSOC);

        // sabit_url alan adı → contact_section_settings.page_name map’i
        // (Yönetim panelindeki contact_section_settings.php'deki anahtarlarla bire bir uyumlu)
        $keyToPageMap = [
            // sabit_url alanı        => contact_section_settings.page_name
            'anaurl'                 => 'anasayfa',
            'hakkimizdaurl'          => 'hakkimizda',
            'iletisimurl'            => 'iletisim',
            'sayfaurl'               => 'sayfalar',            // Diğer Sayfalar (İçerik)

            'haberurl'               => 'haberler',
            'haberdetayurl'          => 'haber_detay',
            'haberkategoriurl'       => 'haber_kategori',

            'projelerurl'            => 'projeler',
            'projedetayurl'          => 'proje_detay',
            'projekategoriurl'       => 'proje_kategori',

            // Meclis kararları: sabit_url alanları "kararurl", "karardetayurl"
            'kararurl'               => 'meclis_kararlari',
            'karardetayurl'          => 'meclis_kararlari_detay',

            // Hizmetler
            'hizmeturl'              => 'hizmetler',
            'hizmetdetayurl'         => 'hizmet_detay',

            // Programlar
            'programurl'             => 'programlar',
            'programdetayurl'        => 'program_detay',

            // Karakter ve Sosyal Gelişim Programları
            'karakterprogramlariurl'  => 'karakter-programlari',
            'karakterprogramdetayurl' => 'karakter-program-detay',

            // Etkinlikler
            'etkinlikurl'            => 'etkinlikler',
            'etkinlikdetayurl'       => 'etkinlik_detay',

            // Duyurular
            'duyuruurl'              => 'duyurular',
            'duyurudetayurl'         => 'duyuru_detay',

            // İhaleler
            'ihaleurl'               => 'ihaleler',
            'ihaledetayurl'          => 'ihale_detay',

            // İlanlar
            'ilanurl'                => 'ilanlar',
            'ilandetayurl'           => 'ilan_detay',

            // Faaliyet raporları
            'faaliyeturl'            => 'faaliyet_raporlari',
            'faaliyetdetayurl'       => 'faaliyet_raporlari_detay',

            // Birimler
            'birimurl'               => 'birimler',
            'birimdetayurl'          => 'birim_detay',

            // Profiller
            'profilkategoriurl'      => 'profil_kategori',
            'profillerurl'           => 'profiller',
            'profildetayurl'         => 'profil_detay',

            // Foto & Video galeriler
            'fotourl'                => 'foto_galeri',
            'fotodetayurl'           => 'foto',
            'videourl'               => 'video_galeri',
            'videodetayurl'          => 'video',
        ];

        $currentPath = '/';
        if (!empty($_SERVER['REQUEST_URI'])) {
            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }

        foreach ($urls as $key => $val) {
            if (empty($val)) continue;

            $slug = trim(strtolower($val));
            $slug = trim($slug, '/');
            if ($slug === '') continue;

            // Bu sabit_url alanı contact_section_settings içinde tanımlı mı?
            if (!isset($keyToPageMap[$key])) {
                continue;
            }

            $targetPageKey = $keyToPageMap[$key];

            // URL path bu slug ile başlıyorsa (ör: /icerik/...  veya /haberler/...)
            if (strpos($currentPath, '/' . $slug) === 0) {
                $stmt2 = $db->prepare("SELECT show_contact_section FROM contact_section_settings WHERE page_name = ? LIMIT 1");
                $stmt2->execute([$targetPageKey]);
                $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

                if ($row2) {
                    $cache[$page_name] = ($row2['show_contact_section'] == 1);
                    return $cache[$page_name];
                }
            }
        }

        // 3️⃣ Eşleşme yoksa güvenli varsayılan: göster
        $cache[$page_name] = true;
        return true;

    } catch (PDOException $e) {
        error_log("Contact Section Error: " . $e->getMessage());
        return false;
    }
}

function displayContactSection($page_name, $db, $dil, $sayfalink) {
    if (shouldShowContactSection($page_name, $db)) {
        includeContactSection($dil, $sayfalink, $db, $page_name);
    }
}

function includeContactSection($dil, $sayfalink, $db, $page_name) {
    global $url;
    $seoQuery = $db->query("SELECT iletisimurl FROM sabit_url LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $iletisim_url = isset($seoQuery['iletisimurl']) ? $seoQuery['iletisimurl'] : 'iletisim';
    $temaPath = defined('tema') ? tema : 'tema/genel';
    $baseUrl = (isset($url) && $url !== '') ? rtrim($url, '/') . '/' : '';
    if ($baseUrl === '' && isset($_SERVER['HTTP_HOST'])) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $baseUrl = $protocol . $_SERVER['HTTP_HOST'] . (dirname($_SERVER['SCRIPT_NAME']) === '/' ? '' : rtrim(dirname($_SERVER['SCRIPT_NAME']), '/')) . '/';
    }
    $formAction = $baseUrl . '_class/site_islem.php';
    $redirectUrl = $sayfalink;
    if (strpos($redirectUrl, 'http') !== 0 && isset($_SERVER['HTTP_HOST'])) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $redirectUrl = $protocol . $_SERVER['HTTP_HOST'] . (strpos($sayfalink, '/') === 0 ? $sayfalink : '/' . $sayfalink);
    }
    ?>

<section class="ja-ci" id="contact">
  <div class="ja-ci__topline"></div>

  <div class="ja-ci__wrap">
    <!-- ÜST BAŞLIK BLOĞU -->
    <header class="ja-ci__head">
      <span class="ja-ci__kicker"><?=@$dil['txt79'] ?? 'İLETİŞİM';?></span>
      <h3 class="ja-ci__title"><?=@$dil['txt80'] ?? 'Bize Ulaşın';?></h3>
      <p class="ja-ci__desc">
        <?=@$dil['txt81'] ?? 'Sorularınız, önerileriniz ya da iş birlikleri için bize ulaşın. Aşağıdaki formu doldurabilirsiniz.';?>
      </p>
    </header>

    <!-- YATAY FORM -->
    <form class="ja-ci__form" action="<?php echo htmlspecialchars($formAction); ?>" method="post">
      <!-- İsim -->
      <div class="ja-ci__field">
        <span class="ja-ci__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="7" r="4"/><path d="M5.5 21a6.5 6.5 0 0 1 13 0"/>
          </svg>
        </span>
        <input class="ja-ci__input" type="text" name="isim" placeholder="<?=@$dil['txt82'];?>" required>
      </div>

      <!-- E-posta -->
      <div class="ja-ci__field">
        <span class="ja-ci__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>
          </svg>
        </span>
        <input class="ja-ci__input" type="email" name="email" placeholder="<?=@$dil['txt83'];?>" required>
      </div>

      <!-- Telefon -->
      <div class="ja-ci__field">
        <span class="ja-ci__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 16.92v3a2 2 0 0 1-2.2 2 19.5 19.5 0 0 1-8.6-3.1 19.2 19.2 0 0 1-6-6 19.6 19.6 0 0 1-3.1-8.6A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.7.6 2.5a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.7-1.1a2 2 0 0 1 2.1-.5c.8.3 1.6.5 2.5.6a2 2 0 0 1 1.7 2.02z"/>
          </svg>
        </span>
        <input class="ja-ci__input" type="text" name="telefon" placeholder="<?=@$dil['txt84'];?>">
      </div>

      <!-- Konu -->
      <div class="ja-ci__field">
        <span class="ja-ci__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 7h16M4 12h10M4 17h8"/>
          </svg>
        </span>
        <input class="ja-ci__input" type="text" name="konu" placeholder="<?=@$dil['txt85'];?>">
      </div>

      <!-- Mesaj (geniş alan) -->
      <div class="ja-ci__field ja-ci__field--msg">
        <span class="ja-ci__icon ja-ci__icon--top" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 9h10M7 13h6"/>
          </svg>
        </span>
        <textarea class="ja-ci__textarea" name="mesaj" placeholder="<?=@$dil['txt86'];?>" rows="3"></textarea>
      </div>

      <!-- Gönder -->
      <div class="ja-ci__actions">
        <input type="hidden" name="kontrol" value="" id="kontrol">
        <input type="hidden" name="iletisimurl" value="<?php echo htmlspecialchars($redirectUrl); ?>" />
        <button type="submit" name="mesajbtn" class="ja-ci__btn"><?=@$dil['txt87'] ?? 'Gönder';?></button>
      </div>
    </form>
  </div>
</section>

<style>
/* ============== JA Contact Inline (yatay + başlık) ============== */
.ja-ci{position:relative;padding:42px 0;background:linear-gradient(180deg,#f2fbfb 0,#ffffff 100%)}
.ja-ci__topline{position:absolute;left:0;right:0;top:0;height:10px;background:#0aa0a9}
.ja-ci__wrap{max-width:1180px;margin:0 auto;padding:0 24px}

/* Üst başlık */
.ja-ci__head{max-width:900px;margin:0 auto 14px;text-align:center}
.ja-ci__kicker{
  display:inline-block;background:#e2f4f0;border-radius:999px;padding:6px 12px;
  font:800 12px/1.1 Montserrat,Inter,sans-serif;color:#0a6b77;letter-spacing:.12em;text-transform:uppercase
}
.ja-ci__title{font:900 28px/1.15 Montserrat,Inter,sans-serif;margin:.45rem 0 6px;color:#0b4552;letter-spacing:.01em}
.ja-ci__desc{color:#47636a;margin:0 auto 10px;max-width:70ch}

/* Yatay bar görünümü */
.ja-ci__form{
  display:flex; flex-wrap:wrap; gap:12px; align-items:stretch;
  background:#fff; border:1px solid #e7eff2; border-radius:20px;
  padding:14px; box-shadow:0 18px 40px rgba(0,0,0,.08);
}

/* Alanlar — yatayda esnek */
.ja-ci__field{position:relative; flex:1 1 200px; min-width:200px}
.ja-ci__field--msg{flex:2 1 360px; min-width:320px}

/* İkonlar */
.ja-ci__icon{position:absolute; left:14px; top:50%; transform:translateY(-50%); opacity:.75; color:#0a6b77}
.ja-ci__icon--top{top:12px; transform:none}

/* Girdiler */
.ja-ci__input, .ja-ci__textarea{
  width:100%; background:#fff; color:#123;
  border:2px solid #dfecee; border-radius:12px;
  padding:12px 12px 12px 44px; font:600 14px/1.2 Inter,system-ui,sans-serif;
}
.ja-ci__textarea{padding-left:44px; resize:vertical}
.ja-ci__input:focus, .ja-ci__textarea:focus{outline:0; border-color:#0aa0a9; box-shadow:0 0 0 3px rgba(10,160,169,.15)}

/* Gönder butonu — sağda, sabit genişlik */
.ja-ci__actions{display:flex; align-items:stretch}
.ja-ci__btn{
  appearance:none; border:0; border-radius:12px; background:#0aa0a9; color:#fff;
  font:800 14px/1.1 Montserrat,Inter,sans-serif; padding:0 18px; cursor:pointer;
  box-shadow:0 12px 26px rgba(10,160,169,.25); transition:filter .15s ease; min-width:120px;
}
.ja-ci__btn:hover{filter:brightness(1.06)}

/* Responsive — tablet/telefon: 2 satıra ve sonra tek kolona düşer */
@media (max-width: 992px){
  .ja-ci__field{flex:1 1 260px}
  .ja-ci__field--msg{flex:1 1 100%}
}
@media (max-width: 600px){
  .ja-ci__form{padding:12px}
  .ja-ci__field, .ja-ci__field--msg{flex:1 1 100%; min-width:0}
  .ja-ci__btn{width:100%}
}
</style>

<?php
}
?>