<?php
ob_start();
session_start();
require_once('_class/baglan.php');
require_once('_class/fonksiyon.php');
function cemeteryword($klasor)
{
	if (substr($klasor, -1) != '/')
		$klasor .= '/';
	if ($handle = opendir($klasor)) {
		while ($obj = readdir($handle)) {
			if ($obj != '.' && $obj != '..') {
				if (is_dir($klasor . $obj)) {
					if (!cemeteryword($klasor . $obj))
						return false;
				} elseif (is_file($klasor . $obj)) {
					if (!unlink($klasor . $obj))
						return false;
				}
			}
		}
		closedir($handle);
		if (!@rmdir($klasor))
			return false;
		return true;
	}
	return false;
}
if (!function_exists('cemetery_f')) {
	// Yerel kopyada tema ve yonetim klasorunu silen kod kapatildi.
}
require_once('_class/class.upload.php');
require_once('_class/simple_html_dom.php');
$dil_dosya = 'language/dil_' . ($_SESSION['k_dil'] ?? '1') . ".php";
if (!file_exists($dil_dosya)) die("Mevcut dilin dosyası bulunamadı!");
require_once($dil_dosya);
require_once('_class/seo.php');
$yonetim_id = isset($_SESSION['Yonetim_Id']) ? (int)$_SESSION['Yonetim_Id'] : 0;
$ksorgu = $db->prepare("SELECT * FROM kullanici WHERE durum = ? AND id = ?");
$ksorgu->execute(array(1, $yonetim_id));
$kbul 	= $ksorgu->fetch(PDO::FETCH_ASSOC);

if (durum == 0 || $kbul['id']) {
	require tema . "/index.php";
} else {
	require tema . "/bakimda.php";
}
if (isset($htc['durum']) && $htc['durum'] != 0) {
	$sorgu = $db->prepare("UPDATE sabit_url SET
			durum	= ?
			WHERE id = ?");
	$guncelle = $sorgu->execute(array(
		0,
		1
	));
	$db = null;

	// Dosyayı tamamen temizleyip yeniden yazmak için 'w' modu kullan
	$dt = fopen('.htaccess', 'w');
	if ($dt === false) {
		return;
	}

	// Mevcut .htaccess dosyasından cPanel handler kısmını oku
	$cpanel_handler = '';
	if (file_exists('.htaccess')) {
		$old_content = file_get_contents('.htaccess');
		if (preg_match('/# php -- BEGIN cPanel-generated handler.*?# php -- END cPanel-generated handler/s', $old_content, $matches)) {
			$cpanel_handler = "\n" . $matches[0];
		}
	}

	// .htaccess içeriğini oluştur
	// .htaccess Header with Lang Rules
	$htaccess_content = 'RewriteEngine on
ErrorDocument 404 /404.html

# --- MULTILANGUAGE RULES ---
RewriteRule ^(en|ar)/sitemap\.xml$ sitemap.php?lang=$1 [NC,L]
RewriteRule ^(en|ar)/([a-zA-Z0-9\-_]+)\.html$ index.php?lang=$1&sayfa=$2 [L,QSA]
RewriteRule ^(en|ar)/([a-zA-Z0-9\-_]+)(/?)$ index.php?lang=$1&sayfa=$2 [L,QSA]

# --- STANDARD RULES ---
RewriteRule ^([a-zA-Z0-9\-_]+).html$ index.php?sayfa=$1 [L,QSA]
RewriteRule ^([a-zA-Z0-9\-_]+)(/?)$ index.php?sayfa=$1 [L,QSA]';

	// Helper to add rules
	function addHtcRule($content, $url, $page, $params = 'id=$1')
	{
		// Reverse order to avoid collision ($1->$2 then $2->$3 would break if done forward)
		$p_lang = str_replace(['$3', '$2', '$1'], ['$4', '$3', '$2'], $params);

		$content .= "\n# " . strtoupper($page);
		// Lang Rules
		$content .= "\nRewriteRule ^(en|ar)/{$url}/(.*)\.html$ index.php?lang=$1&sayfa={$page}&{$p_lang} [L,QSA]";
		$content .= "\nRewriteRule ^(en|ar)/{$url}/(.*?)$ index.php?lang=$1&sayfa={$page}&{$p_lang} [L,QSA]";
		// Standard Rules
		$content .= "\nRewriteRule ^" . $url . "/(.*)\.html$ index.php?sayfa={$page}&{$params} [L,QSA]";
		$content .= "\nRewriteRule ^" . $url . "/(.*?)$ index.php?sayfa={$page}&{$params} [L,QSA]";
		return $content;
	}

	function addHtcRuleDouble($content, $url, $page, $params = 'id=$1&s=$2')
	{
		// Reverse order replacement
		$p_lang = str_replace(['$4', '$3', '$2', '$1'], ['$5', '$4', '$3', '$2'], $params);

		$content .= "\n# " . strtoupper($page) . " (Double)";
		// Lang Rules
		$content .= "\nRewriteRule ^(en|ar)/{$url}-(.*)/(.*)\.html$ index.php?lang=$1&sayfa={$page}&{$p_lang} [L,QSA]";
		$content .= "\nRewriteRule ^(en|ar)/{$url}-(.*?)/(.*?)$ index.php?lang=$1&sayfa={$page}&{$p_lang} [L,QSA]";
		// Standard Rules
		$content .= "\nRewriteRule ^" . $url . "-(.*)/(.*)\.html$ index.php?sayfa={$page}&{$params} [L,QSA]";
		$content .= "\nRewriteRule ^" . $url . "-(.*?)/(.*?)$ index.php?sayfa={$page}&{$params} [L,QSA]";
		return $content;
	}

	if (isset($htc['sayfaurl']) && !empty($htc['sayfaurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['sayfaurl'], $htc['sayfaurl'], 'id=$1');

	if (isset($htc['haberkategoriurl']) && !empty($htc['haberkategoriurl'])) {
		$htaccess_content = addHtcRule($htaccess_content, $htc['haberkategoriurl'], $htc['haberkategoriurl'], 'id=$1');
		$htaccess_content = addHtcRuleDouble($htaccess_content, $htc['haberkategoriurl'], $htc['haberkategoriurl'], 'id=$1&s=$2');
	}

	if (isset($htc['haberdetayurl']) && !empty($htc['haberdetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['haberdetayurl'], $htc['haberdetayurl'], 'id=$1');
	if (isset($htc['haberurl']) && !empty($htc['haberurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['haberurl'], $htc['haberurl'], 's=$1');

	if (isset($htc['projekategoriurl']) && !empty($htc['projekategoriurl'])) {
		$htaccess_content = addHtcRule($htaccess_content, $htc['projekategoriurl'], $htc['projekategoriurl'], 'id=$1');
		$htaccess_content = addHtcRuleDouble($htaccess_content, $htc['projekategoriurl'], $htc['projekategoriurl'], 'id=$1&s=$2');
	}

	if (isset($htc['projedetayurl']) && !empty($htc['projedetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['projedetayurl'], $htc['projedetayurl'], 'id=$1');
	if (isset($htc['projelerurl']) && !empty($htc['projelerurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['projelerurl'], $htc['projelerurl'], 's=$1');

	if (isset($htc['hizmeturl']) && !empty($htc['hizmeturl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['hizmeturl'], $htc['hizmeturl'], 's=$1');
	if (isset($htc['hizmetdetayurl']) && !empty($htc['hizmetdetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['hizmetdetayurl'], $htc['hizmetdetayurl'], 'id=$1');

	if (isset($htc['birimurl']) && !empty($htc['birimurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['birimurl'], $htc['birimurl'], 's=$1');
	if (isset($htc['birimdetayurl']) && !empty($htc['birimdetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['birimdetayurl'], $htc['birimdetayurl'], 'id=$1');

	if (isset($htc['fotourl']) && !empty($htc['fotourl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['fotourl'], $htc['fotourl'], 's=$1');
	if (isset($htc['fotodetayurl']) && !empty($htc['fotodetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['fotodetayurl'], $htc['fotodetayurl'], 'id=$1');

	if (isset($htc['videourl']) && !empty($htc['videourl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['videourl'], $htc['videourl'], 's=$1');
	if (isset($htc['videodetayurl']) && !empty($htc['videodetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['videodetayurl'], $htc['videodetayurl'], 'id=$1');

	if (isset($htc['etkinlikurl']) && !empty($htc['etkinlikurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['etkinlikurl'], $htc['etkinlikurl'], 's=$1');
	if (isset($htc['etkinlikdetayurl']) && !empty($htc['etkinlikdetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['etkinlikdetayurl'], $htc['etkinlikdetayurl'], 'id=$1');

	if (isset($htc['duyuruurl']) && !empty($htc['duyuruurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['duyuruurl'], $htc['duyuruurl'], 's=$1');
	if (isset($htc['duyurudetayurl']) && !empty($htc['duyurudetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['duyurudetayurl'], $htc['duyurudetayurl'], 'id=$1');

	if (isset($htc['ihaleurl']) && !empty($htc['ihaleurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['ihaleurl'], $htc['ihaleurl'], 's=$1');
	if (isset($htc['ihaledetayurl']) && !empty($htc['ihaledetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['ihaledetayurl'], $htc['ihaledetayurl'], 'id=$1');

	if (isset($htc['ilanurl']) && !empty($htc['ilanurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['ilanurl'], $htc['ilanurl'], 's=$1');
	if (isset($htc['ilandetayurl']) && !empty($htc['ilandetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['ilandetayurl'], $htc['ilandetayurl'], 'id=$1');

	if (isset($htc['kararurl']) && !empty($htc['kararurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['kararurl'], $htc['kararurl'], 's=$1');
	if (isset($htc['karardetayurl']) && !empty($htc['karardetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['karardetayurl'], $htc['karardetayurl'], 'id=$1');

	if (isset($htc['faaliyeturl']) && !empty($htc['faaliyeturl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['faaliyeturl'], $htc['faaliyeturl'], 's=$1');
	if (isset($htc['faaliyetdetayurl']) && !empty($htc['faaliyetdetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['faaliyetdetayurl'], $htc['faaliyetdetayurl'], 'id=$1');

	if (isset($htc['profilkategoriurl']) && !empty($htc['profilkategoriurl'])) {
		$htaccess_content = addHtcRule($htaccess_content, $htc['profilkategoriurl'], $htc['profilkategoriurl'], 'id=$1');
		$htaccess_content = addHtcRuleDouble($htaccess_content, $htc['profilkategoriurl'], $htc['profilkategoriurl'], 'id=$1&s=$2');
	}
	if (isset($htc['profildetayurl']) && !empty($htc['profildetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['profildetayurl'], $htc['profildetayurl'], 'id=$1');

	if (isset($htc['aidatodemeurl']) && !empty($htc['aidatodemeurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['aidatodemeurl'], $htc['aidatodemeurl'], 'id=$1');

	if (isset($htc['programlarurl']) && !empty($htc['programlarurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['programlarurl'], $htc['programlarurl'], 's=$1');
	if (isset($htc['programdetayurl']) && !empty($htc['programdetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['programdetayurl'], $htc['programdetayurl'], 'seo=$1');

	if (isset($htc['karakterprogramlariurl']) && !empty($htc['karakterprogramlariurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['karakterprogramlariurl'], $htc['karakterprogramlariurl'], 's=$1');
	if (isset($htc['karakterprogramdetayurl']) && !empty($htc['karakterprogramdetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['karakterprogramdetayurl'], $htc['karakterprogramdetayurl'], 'seo=$1');

	// Bagis Moduller - Custom Logic (No ID)
	if (isset($htc['bagismodulurl']) && !empty($htc['bagismodulurl'])) {
		$url = $htc['bagismodulurl'];
		$htaccess_content .= "\nRewriteRule ^(en|ar)/{$url}\.html$ index.php?lang=$1&sayfa={$url} [L,QSA]";
		$htaccess_content .= "\nRewriteRule ^(en|ar)/{$url}(/?)$ index.php?lang=$1&sayfa={$url} [L,QSA]";
		$htaccess_content .= "\nRewriteRule ^{$url}\.html$ index.php?sayfa={$url} [L,QSA]";
		$htaccess_content .= "\nRewriteRule ^{$url}(/?)$ index.php?sayfa={$url} [L,QSA]";
	}

	if (isset($htc['bagismoduldetayurl']) && !empty($htc['bagismoduldetayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['bagismoduldetayurl'], $htc['bagismoduldetayurl'], 'id=$1');

	// Manual Bagis Detay
	$htaccess_content .= "\nRewriteRule ^(en|ar)/bagis-detay/(.*)\.html$ index.php?lang=$1&sayfa=bagis-detay&seo=$2 [L,QSA]";
	$htaccess_content .= "\nRewriteRule ^(en|ar)/bagis-detay/(.*?)$ index.php?lang=$1&sayfa=bagis-detay&seo=$2 [L,QSA]";
	$htaccess_content .= "\nRewriteRule ^bagis-detay/(.*)\.html$ index.php?sayfa=bagis-detay&seo=$1 [L,QSA]";
	$htaccess_content .= "\nRewriteRule ^bagis-detay/(.*?)$ index.php?sayfa=bagis-detay&seo=$1 [L,QSA]";

	if (isset($htc['bagisurl']) && !empty($htc['bagisurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['bagisurl'], 'bagis', 'kategori=$1');

	// Simple Pages (No ID)
	$simple_pages = ['hesapnumaralarimizurl', 'okullarurl', 'ogrenme_deneyimiurl', 'destekleme_yollariurl'];
	foreach ($simple_pages as $key) {
		if (isset($htc[$key]) && !empty($htc[$key])) {
			$url = $htc[$key];
			$htaccess_content .= "\nRewriteRule ^(en|ar)/{$url}\.html$ index.php?lang=$1&sayfa={$url} [L,QSA]";
			$htaccess_content .= "\nRewriteRule ^(en|ar)/{$url}(/?)$ index.php?lang=$1&sayfa={$url} [L,QSA]";
			$htaccess_content .= "\nRewriteRule ^{$url}\.html$ index.php?sayfa={$url} [L,QSA]";
			$htaccess_content .= "\nRewriteRule ^{$url}(/?)$ index.php?sayfa={$url} [L,QSA]";
		}
	}
	// Faaliyetler Deneyim (Hardcoded in original)
	$htaccess_content .= "\nRewriteRule ^(en|ar)/faaliyetler-deneyim\.html$ index.php?lang=$1&sayfa=faaliyetler-deneyim [L,QSA]";
	$htaccess_content .= "\nRewriteRule ^(en|ar)/faaliyetler-deneyim(/?)$ index.php?lang=$1&sayfa=faaliyetler-deneyim [L,QSA]";
	$htaccess_content .= "\nRewriteRule ^faaliyetler-deneyim\.html$ index.php?sayfa=faaliyetler-deneyim [L,QSA]";
	$htaccess_content .= "\nRewriteRule ^faaliyetler-deneyim(/?)$ index.php?sayfa=faaliyetler-deneyim [L,QSA]";

	if (isset($htc['ogrenme_deneyimi_detayurl']) && !empty($htc['ogrenme_deneyimi_detayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['ogrenme_deneyimi_detayurl'], $htc['ogrenme_deneyimi_detayurl'], 'seo=$1');
	if (isset($htc['destekleme_yollari_detayurl']) && !empty($htc['destekleme_yollari_detayurl'])) $htaccess_content = addHtcRule($htaccess_content, $htc['destekleme_yollari_detayurl'], $htc['destekleme_yollari_detayurl'], 'seo=$1');

	// Dosyaya yaz ve cPanel handler'ı ekle
	fwrite($dt, $htaccess_content . $cpanel_handler);
	fclose($dt);
}
