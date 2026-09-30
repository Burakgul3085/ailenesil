<?php
date_default_timezone_set('Europe/Istanbul');


$host = getenv('DB_HOST') ?: 'localhost';
$data = getenv('DB_NAME') ?: 'ailevenesilakade_vt';
$user = getenv('DB_USER') ?: 'ailevenesilakade_vt';
$pass = getenv('DB_PASS') ?: '1;4^4(N@Fg@rU{Ve';

try {
	$db = new PDO('mysql:host=' . $host . ';dbname=' . $data . ';charset=utf8mb4;', $user, $pass);
	$db->exec("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
	//$db->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo 'Hata: ' . $e->getMessage();
}


if (isset($_GET['dil']) && is_numeric($_GET['dil'])) {
	$_SESSION['k_dil'] = $_GET['dil'];
}
if (!isset($_SESSION['k_dil'])) {
	$anadil_stmt = $db->prepare("SELECT * FROM diller WHERE anadil = ?");
	$anadil_stmt->execute([1]);
	if ($anadil_stmt->rowCount() == 0) die("Lütfen bir anadil seçiniz !!");
	$anadil = $anadil_stmt->fetch(PDO::FETCH_ASSOC);
	$_SESSION['k_dil'] = ($anadil !== false && isset($anadil['id'])) ? $anadil['id'] : 1;
} else {
	$mevcutDil_stmt = $db->prepare("SELECT * FROM diller WHERE id = ?");
	$mevcutDil_stmt->execute([(int) $_SESSION['k_dil']]);
	if ($mevcutDil_stmt->rowCount() == 0) {
		$anadil_stmt = $db->prepare("SELECT * FROM diller WHERE anadil = ?");
		$anadil_stmt->execute([1]);
		if ($anadil_stmt->rowCount() == 0) die("Lütfen bir anadil seçiniz !!");
		$anadil = $anadil_stmt->fetch(PDO::FETCH_ASSOC);
		$_SESSION['k_dil'] = ($anadil !== false && isset($anadil['id'])) ? $anadil['id'] : 1;
	} else {
		$mevcutDil = $mevcutDil_stmt->fetch(PDO::FETCH_ASSOC);
		$_SESSION['k_dil'] = ($mevcutDil !== false && isset($mevcutDil['id'])) ? $mevcutDil['id'] : 1;
	}
}
$ayar_result = $db->query("SELECT * FROM ayarlar");
$ayar = ($ayar_result !== false && ($r = $ayar_result->fetch(PDO::FETCH_ASSOC))) ? $r : [];
$baskan_result = $db->query("SELECT * FROM baskan_ayarlar");
$baskan = ($baskan_result !== false && ($r = $baskan_result->fetch(PDO::FETCH_ASSOC))) ? $r : [];
$popup_result = $db->query("SELECT * FROM popup_ayarlar");
$popup = ($popup_result !== false && ($r = $popup_result->fetch(PDO::FETCH_ASSOC))) ? $r : [];
$htc_result = $db->query("SELECT * FROM sabit_url");
$htc = ($htc_result !== false && ($r = $htc_result->fetch(PDO::FETCH_ASSOC))) ? $r : [];
$bakim_modu_result = $db->query("SELECT * FROM bakim_modu");
$bakim_modu = ($bakim_modu_result !== false && ($r = $bakim_modu_result->fetch(PDO::FETCH_ASSOC))) ? $r : [];
$moduller_result = $db->query("SELECT * FROM moduller WHERE id = '1' ORDER BY id ASC LIMIT 1");
$moduller = ($moduller_result !== false && ($r = $moduller_result->fetch(PDO::FETCH_ASSOC))) ? $r : [];
$mailayar_result = $db->query("SELECT * FROM mail_ayar");
$mailayar = ($mailayar_result !== false && ($r = $mailayar_result->fetch(PDO::FETCH_ASSOC))) ? $r : [];
$smsayar_result = $db->query("SELECT * FROM sms");
$smsayar = ($smsayar_result !== false && ($r = $smsayar_result->fetch(PDO::FETCH_ASSOC))) ? $r : [];
$arkaplan_result = $db->query("SELECT * FROM arka_plan WHERE id = '1' ORDER BY id ASC LIMIT 1");
$arkaplan = ($arkaplan_result !== false && ($r = $arkaplan_result->fetch(PDO::FETCH_ASSOC))) ? $r : [];
$kart_result = $db->query("SELECT * FROM paytr WHERE id = '1' ORDER BY id ASC LIMIT 1");
$kart = ($kart_result !== false && ($r = $kart_result->fetch(PDO::FETCH_ASSOC))) ? $r : [];
$limitayar_result = $db->query("SELECT * FROM limit_ayarlari WHERE id = '1' ORDER BY id ASC LIMIT 1");
$limitayar = ($limitayar_result !== false && ($r = $limitayar_result->fetch(PDO::FETCH_ASSOC))) ? $r : [];

$route = array_values(array_filter(explode('/', realpath('.'))));
$host = str_replace("www.", "", $_SERVER['HTTP_HOST']);
$testArray = array(
	$host
);

foreach ($testArray as $k => $v) {
	$sub = extract_subdomains($v);
}

function extract_domain($domain)
{
	if (preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches)) {
		return $matches['domain'];
	} else {
		return $domain;
	}
}

function extract_subdomains($domain)
{
	$subdomains = $domain;
	$domain = extract_domain($subdomains);
	$subdomains = rtrim(strstr($subdomains, $domain, true), '.');
	return $subdomains;
}
if (end($route) != "public_html" && end($route) != "httpdocs") {
	if ($sub) {
		$altklasor = "0";
	} else {
		$altklasor = "1";
	}
} else {
	$altklasor = "0";
}

define("baslik", $ayar["site_baslik"] ?? "");
define("instagramtoken", $ayar["instagramtoken"] ?? "");
define("instagramusername", $ayar["instagram_username"] ?? "");
define("url", $ayar["site_url"] ?? "");
define("tema_dir", $ayar["site_tema"] ?? "");
define("tema", "tema/" . ($ayar["site_tema"] ?? ""));
define("tema_url", ($ayar["site_url"] ?? "") . "tema/" . ($ayar["site_tema"] ?? ""));
define("logo", $ayar["firma_logo"] ?? "");
define("footerlogo", $ayar["firma_footerlogo"] ?? "");
define("fav", $ayar["favicon"] ?? "");
define("firma_adi", $ayar["firma_adi"] ?? "");
define("telefon", $ayar["firma_telefon"] ?? "");
define("fax", $ayar["firma_fax"] ?? "");
define("email", $ayar["firma_email"] ?? "");
define("adres", $ayar["firma_adres"] ?? "");
define("maps", $ayar["google_maps"] ?? "");
define("analytics", $ayar["google_analytics"] ?? "");
define("dogrulama", $ayar["dogrulama_kodu"] ?? "");
define("canli_destek", $ayar["canli_destek"] ?? "");
define("whatsapp", $ayar["whatsapp"] ?? "");
define("facebook", $ayar["facebook"] ?? "");
define("twitter", $ayar["twitter"] ?? "");
define("instagram", $ayar["instagram"] ?? "");
define("linkedin", $ayar["linkedin"] ?? "");
define("youtube", $ayar["youtube"] ?? "");
define("copyright", $ayar["copyright"] ?? "");
define("site_desc", $ayar["site_desc"] ?? "");
define("site_keyw", $ayar["site_keyw"] ?? "");
define("durum", $moduller["alan9"] ?? 0);
//define("altklasor", $moduller["alan8"]);
define("altklasor", $altklasor);
define("renk1", $ayar["renk1"] ?? "");
define("renk2", $ayar["renk2"] ?? "");
define("renk3", $ayar["renk3"] ?? "");
define("yonetim", $ayar["yonetim"] ?? "yonetim");

// Kredi Kartı Sabitler
define("magaza_no", $kart["magaza_no"] ?? "");
define("magaza_parola", $kart["magaza_parola"] ?? "");
define("magaza_anahtar", $kart["magaza_anahtar"] ?? "");
define("hata_mesaj", $kart["hata_mesaj"] ?? "");
define("test_modu", $kart["test_modu"] ?? "");
define("taksit", $kart["taksit"] ?? "");


// Mail Sabitler
define("m_server", 	$mailayar["m_server"] ?? "");
define("m_adresi", 	$mailayar["m_adresi"] ?? "");
define("m_parola", 	$mailayar["m_parola"] ?? "");
define("m_port", 	$mailayar["m_port"] ?? "");
define("m_sertifika", 	$mailayar["m_sertifika"] ?? "");
define("m_kime", 	$mailayar["m_kime"] ?? "");

// SMS Sabitler
define("postUrl", 	$smsayar["postUrl"] ?? "");
define("sms_kadi", 	$smsayar["KULLANICIADI"] ?? "");
define("sms_sifre", 	$smsayar["SIFRE"] ?? "");
define("sms_baslik", $smsayar["ORGINATOR"] ?? "");
define("sms_kime", $smsayar["m_kime"] ?? "");


function mailgonder($gelendegisken, $gidendegisken, $mailsablon, $kullanici_mail, $mailKonu, $mesaj_yedek)
{
	// Değişkenleri şablon içine yerleştir
	$mesaj = str_replace($gelendegisken, $gidendegisken, $mailsablon);

	// Mail Başlıkları (Headers) - UTF-8 desteği ve Gönderen bilgisi
	$headers  = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= "From: " . firma_adi . " <" . m_adresi . ">" . "\r\n";
	$headers .= "Reply-To: " . m_adresi . "\r\n";
	$headers .= "X-Mailer: PHP/" . phpversion();

	// Gönderim Denemesi
	if (@mail($kullanici_mail, $mailKonu, $mesaj, $headers)) {
		return true;
	} else {
		return false;
	}
}

/**
 * Merkezi Bildirim Gönderme Fonksiyonu
 * bildirim_sablonu tablosuna bağlı çalışır.
 */
function bildirim_sablon_gonder($sablon_id, $veriler, $alici_email = '', $alici_telefon = '')
{
	global $db;

	// Şablonu çek
	$sablon = $db->query("SELECT * FROM bildirim_sablonu WHERE id = '$sablon_id'")->fetch(PDO::FETCH_ASSOC);
	if (!$sablon) return false;

	$gelen_taglar = explode(",", $sablon['degiskenler']);
	$giden_degerler = [];

	// Standart değişkenleri hazırla
	$logo   = url . "tema/" . tema_dir . '/uploads/logo/footer/' . footerlogo;
	$domain = url;
	$tarih  = cVCLmHLxbS_tarih(cVCLmHLxbS_tr_tarih('Y-m-d H:i:s'));
	$ip     = cVCLmHLxbS_ip();

	foreach ($gelen_taglar as $tag) {
		$tag_trimmed = trim($tag);
		$key = str_replace(['{', '}'], '', $tag_trimmed);

		if (isset($veriler[$key])) {
			$giden_degerler[] = $veriler[$key];
		} elseif (isset($veriler['{' . $key . '}'])) {
			$giden_degerler[] = $veriler['{' . $key . '}'];
		} elseif ($key == 'logo') {
			$giden_degerler[] = $logo;
		} elseif ($key == 'domain') {
			$giden_degerler[] = $domain;
		} elseif ($key == 'tarih') {
			$giden_degerler[] = $tarih;
		} elseif ($key == 'ip') {
			$giden_degerler[] = $ip;
		} else {
			$giden_degerler[] = '';
		}
	}

	// Üye E-Posta (ubildirim)
	if ($sablon["ubildirim"] == "1" && !empty($alici_email)) {
		$konu = cVCLmHLxbS_turkce($sablon['konu']);
		mailgonder($gelen_taglar, $giden_degerler, $sablon['icerik'], $alici_email, $konu, $sablon['icerik']);
	}

	// Üye SMS (sbildirim)
	if ($sablon["sbildirim"] == "1" && !empty($alici_telefon)) {
		smsgonder($gelen_taglar, $giden_degerler, $sablon['icerik3'], $alici_telefon, $sablon['icerik3']);
	}

	// Admin E-Posta (abildirim)
	if ($sablon["abildirim"] == "1") {
		$konu = cVCLmHLxbS_turkce($sablon['konu2']);
		// m_kime: Admin e-posta adresidir, veritabanından/ayarlardan gelir
		mailgonder($gelen_taglar, $giden_degerler, $sablon['icerik2'], m_kime, $konu, $sablon['icerik2']);
	}

	// Admin SMS (ysbildirim)
	if ($sablon["ysbildirim"] == "1") {
		smsgonder($gelen_taglar, $giden_degerler, $sablon['icerik4'], sms_kime, $sablon['icerik4']);
	}

	return true;
}

function smsgonder($gelendegisken, $gidendegisken, $smssablon, $sms_telefon, $sms_mesaj)
{
	$postUrl    = "https://api.netgsm.com.tr/sms/send/otp";
	$username   = sms_kadi;     // NetGSM Müşteri No veya Kullanıcı Adı
	$password   = sms_sifre;    // API Şifre / Şifre
	$header     = sms_baslik;   // Onaylı Başlık

	// Şablon değişiklik işlemi
	$sms_mesaj = str_replace($gelendegisken, $gidendegisken, $smssablon);

	$postData = "<?xml version='1.0' encoding='UTF-8'?>
    <mainbody>
        <header>
            <usercode>$username</usercode>
            <password>$password</password>
            <msgheader>$header</msgheader>
        </header>
        <body>
            <msg><![CDATA[$sms_mesaj]]></msg>
            <no>$sms_telefon</no>
        </body>
    </mainbody>";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $postUrl);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=UTF-8'));
	$response = curl_exec($ch);
	curl_close($ch);

	return $response;
}

function toplusmsgonder($sms_telefon, $sms_mesaj)
{
	$postUrl    = "https://api.netgsm.com.tr/sms/send/otp";
	$username   = sms_kadi;
	$password   = sms_sifre;
	$header     = sms_baslik;

	$telefonlar = explode(",", $sms_telefon);

	$gsmXml = "";
	foreach ($telefonlar as $tel) {
		$gsmXml .= "<no>" . trim($tel) . "</no>";
	}

	$postData = "<?xml version='1.0' encoding='UTF-8'?>
    <mainbody>
        <header>
            <usercode>$username</usercode>
            <password>$password</password>
            <msgheader>$header</msgheader>
        </header>
        <body>
            <msg><![CDATA[$sms_mesaj]]></msg>
            $gsmXml
        </body>
    </mainbody>";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $postUrl);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=UTF-8'));
	$response = curl_exec($ch);
	curl_close($ch);

	return $response;
}
	
	// ========================================
	// BAĞIŞ BİLDİRİM SİSTEMİ
	// ========================================

/**
 * Bağış sonrası Bildirim gönder (Merkezi sistem)
 */
function bagis_bildirim_gonder_yeni($bagis_bilgi)
{
	$veriler = [
		'AD_SOYAD' => $bagis_bilgi['adi'],
		'TUTAR' => number_format($bagis_bilgi['tutar'], 2, ',', '.') . " " . $bagis_bilgi['para_birimi'],
		'KAMPANYA' => $bagis_bilgi['kampanya_adi'] ?? 'Genel Bağış',
		'SIPARIS_NO' => $bagis_bilgi['spno']
	];

	// Şablon ID 2: Bağış Teşekkür
	return bildirim_sablon_gonder(2, $veriler, $bagis_bilgi['email'], $bagis_bilgi['telefon']);
}

/**
 * WhatsApp Business API ile bildirim gönder
 * Onaylı şablon mesajları kullanır
 */
function bagis_whatsapp_gonder($bagis_bilgi)
{
	global $db;

	// WhatsApp ayarlarını al
	$wp_ayar = $db->query("SELECT * FROM whatsapp_ayarlar WHERE durum = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
	if (!$wp_ayar) return false;

	// WhatsApp şablonunu al
	$sablon_sorgu = $db->query("SELECT * FROM whatsapp_sablonlar WHERE sablon_tip = 'bagis_tesekkur' AND durum = 1 LIMIT 1");
	if ($sablon_sorgu->rowCount() == 0) return false;

	$sablon = $sablon_sorgu->fetch(PDO::FETCH_ASSOC);

	// Telefon numarasını formatla (WhatsApp için uluslararası format)
	$telefon = preg_replace('/[^0-9]/', '', $bagis_bilgi['telefon']);
	if (substr($telefon, 0, 1) == '0') {
		$telefon = '90' . substr($telefon, 1); // Türkiye için
	}

	// WhatsApp Business API endpoint
	$api_url = $wp_ayar['api_url'];
	$api_token = $wp_ayar['api_token'];

	// Mesaj parametreleri
	$params = [
		'to' => $telefon,
		'type' => 'template',
		'template' => [
			'name' => $sablon['template_name'],
			'language' => [
				'code' => 'tr'
			],
			'components' => [
				[
					'type' => 'body',
					'parameters' => [
						['type' => 'text', 'text' => $bagis_bilgi['adi']],
						['type' => 'text', 'text' => number_format($bagis_bilgi['tutar'], 2, ',', '.') . ' ' . $bagis_bilgi['para_birimi']],
						['type' => 'text', 'text' => $bagis_bilgi['kampanya_adi']],
						['type' => 'text', 'text' => $bagis_bilgi['spno']]
					]
				]
			]
		]
	];

	// API isteği gönder
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $api_url . '/messages');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Authorization: Bearer ' . $api_token,
		'Content-Type: application/json'
	]);

	$response = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	$durum = ($http_code == 200 || $http_code == 201) ? 'gonderildi' : 'hata';

	// Loglama
	$log = $db->prepare("INSERT INTO bildirim_log SET 
			tip = 'whatsapp',
			alici = ?,
			icerik = ?,
			durum = ?,
			api_response = ?,
			bagis_id = ?,
			tarih = NOW()
		");
	$log->execute([$telefon, json_encode($params), $durum, $response, $bagis_bilgi['id']]);

	return ($durum == 'gonderildi');
}

/**
 * Bağış sertifikası/teşekkür belgesi oluştur
 * PDF olarak kaydeder
 */
function bagis_sertifika_olustur($bagis_bilgi)
{
	global $db;

	// Sertifika dizini
	$sertifika_dir = 'uploads/sertifikalar/';
	if (!is_dir($sertifika_dir)) {
		mkdir($sertifika_dir, 0777, true);
	}

	// Benzersiz dosya adı
	$dosya_adi = 'sertifika_' . $bagis_bilgi['id'] . '_' . time() . '.pdf';
	$dosya_yolu = $sertifika_dir . $dosya_adi;

	// PDF içeriği (HTML template)
	$html = '
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="UTF-8">
			<style>
				body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
				.sertifika { border: 10px solid #14b8a6; padding: 50px; margin: 50px; }
				.baslik { font-size: 36px; color: #14b8a6; margin-bottom: 30px; }
				.icerik { font-size: 18px; line-height: 2; }
				.imza { margin-top: 80px; }
			</style>
		</head>
		<body>
			<div class="sertifika">
				<div class="baslik">TEŞEKKÜR BELGESİ</div>
				<div class="icerik">
					<p><strong>' . $bagis_bilgi['adi'] . '</strong></p>
					<p>' . $bagis_bilgi['kampanya_adi'] . ' kampanyasına</p>
					<p><strong>' . number_format($bagis_bilgi['tutar'], 2, ',', '.') . ' ' . $bagis_bilgi['para_birimi'] . '</strong></p>
					<p>bağışta bulunduğunuz için teşekkür ederiz.</p>
					<p style="margin-top: 40px;">Tarih: ' . date('d.m.Y') . '</p>
					<p>Bağış No: ' . $bagis_bilgi['spno'] . '</p>
				</div>
				<div class="imza">
					<p>___________________________</p>
					<p>' . firma_adi . '</p>
				</div>
			</div>
		</body>
		</html>
		';

	// PDF oluştur (TCPDF veya DOMPDF kullanılabilir)
	// Basit versiyonda HTML olarak kaydet
	file_put_contents($dosya_yolu . '.html', $html);

	// Veritabanına kaydet
	$db->prepare("UPDATE bagis_odeme SET sertifika_dosya = ? WHERE id = ?")
		->execute([$dosya_adi, $bagis_bilgi['id']]);

	return $dosya_yolu;
}

/**
 * Tüm bildirimleri toplu gönder
 * SMS, E-posta ve WhatsApp
 */
function bagis_bildirim_gonder($bagis_id, $bildirim_tipleri = ['sms', 'email', 'whatsapp'])
{
	global $db;

	// Bağış bilgilerini al
	$bagis_sorgu = $db->prepare("SELECT * FROM bagis_odeme WHERE id = ?");
	$bagis_sorgu->execute([$bagis_id]);
	$bagis = $bagis_sorgu->fetch(PDO::FETCH_ASSOC);

	if (!$bagis) return false;

	$sonuclar = [];

	// Bildirimleri gönder (Merkezi Şablon ID: 2)
	$sonuclar['merkezi'] = bagis_bildirim_gonder_yeni($bagis);

	// WhatsApp gönder
	if (in_array('whatsapp', $bildirim_tipleri) && !empty($bagis['telefon'])) {
		$sonuclar['whatsapp'] = bagis_whatsapp_gonder($bagis);
	}

	return $sonuclar;
}
