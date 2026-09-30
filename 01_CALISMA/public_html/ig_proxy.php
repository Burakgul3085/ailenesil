<?php
/**
 * Instagram Image Proxy
 *
 * Instagram hotlink'e izin vermedigi icin resimleri bu proxy uzerinden servis eder.
 * Ayrica resimleri lokal olarak cache'ler.
 *
 * Kullanim: ig_proxy.php?url=https://scontent...instagram...jpg
 */

$allowed_hosts = [
    'cdninstagram.com',
    'instagram.com',
    'fbcdn.net',
];

$cache_dir = __DIR__ . '/ig_cache/img';
$cache_ttl = 86400; // 24 saat

$url = $_GET['url'] ?? '';
if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    exit('Bad request');
}

$host = parse_url($url, PHP_URL_HOST);
$is_allowed = false;
foreach ($allowed_hosts as $allowed) {
    if ($host === $allowed || str_ends_with($host, '.' . $allowed)) {
        $is_allowed = true;
        break;
    }
}
if (!$is_allowed) {
    http_response_code(403);
    exit('Forbidden host');
}

if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0755, true);
}

$cache_key  = md5($url);
$cache_file = $cache_dir . '/' . $cache_key;
$cache_meta = $cache_file . '.meta';

if (file_exists($cache_file) && file_exists($cache_meta) && (time() - filemtime($cache_file)) < $cache_ttl) {
    $meta = json_decode(file_get_contents($cache_meta), true);
    header('Content-Type: ' . ($meta['content_type'] ?? 'image/jpeg'));
    header('Cache-Control: public, max-age=86400');
    header('X-Cache: HIT');
    readfile($cache_file);
    exit;
}

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
    CURLOPT_REFERER        => 'https://www.instagram.com/',
    CURLOPT_HTTPHEADER     => [
        'Accept: image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
    ],
]);

$body = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

if ($info['http_code'] !== 200 || !$body) {
    http_response_code(502);
    exit('Upstream error');
}

$ct = $info['content_type'] ?? 'image/jpeg';
if (!str_starts_with($ct, 'image/')) {
    $ct = 'image/jpeg';
}

file_put_contents($cache_file, $body, LOCK_EX);
file_put_contents($cache_meta, json_encode(['content_type' => $ct, 'url' => $url]), LOCK_EX);

header('Content-Type: ' . $ct);
header('Cache-Control: public, max-age=86400');
header('X-Cache: MISS');
echo $body;
