<?php
require_once('_class/baglan.php');
require_once('_class/fonksiyon.php');
require_once('vakifBank.php');

$sonuc = $_GET['sonuc'] ?? '';

// URL standardizasyonu: www/non-www tutarlılığı (non-www standardı)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? '';
$host = preg_replace('/^www\./i', '', $host);
$base_url = $protocol . $host;

$merchant_ok_url = $base_url . "/bagis-sonuc?sonuc=basarili";
$merchant_fail_url = $base_url . "/" . ($htc['bagissonucurl'] ?? 'bagis-sonuc') . ($html ?? '') . "?sonuc=hata";

// Vakıfbank ayarlarını vakifbank tablosundan al (paytr_tekil_bagis.php ile uyumlu)
$vakifbank_ayar = $db->query("SELECT * FROM vakifbank WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

if(!$vakifbank_ayar) {
    // Ayar yoksa logla veya hata dön
    error_log("Vakıfbank ayarı bulunamadı.");
    header("Location: " . $merchant_fail_url);
    exit;
}

$setting = [
    'init' => ($vakifbank_ayar['test_modu'] == '1') ? 'test' : 'prod',
    'HostMerchantId' => $vakifbank_ayar['host_merchant_id'],
    'MerchantPassword' => $vakifbank_ayar['merchant_password'],
    'HostTerminalId' => $vakifbank_ayar['host_terminal_id'],
];

$vakifBank = new vakifBank($setting);
require_once('_class/vakifbank_logger.php');
$vakifLogger = new VakifbankLogger($db);

// Vakıf Katılım POST ile döner
$rawInput = file_get_contents('php://input');
$callbackData = $_POST;

// Eğer POST boşsa GET'i kontrol et (bazı durumlar için fallback)
if (empty($callbackData)) {
    $callbackData = $_GET;
}

$orderIdForLog = $callbackData['MerchantOrderId'] ?? $callbackData['OrderId'] ?? $callbackData['TransactionId'] ?? 'UNKNOWN';
$vakifLogger->logCallbackReception(
    $orderIdForLog,
    [
        'post' => $_POST,
        'get' => $_GET,
        'raw_input' => $rawInput
    ],
    empty($callbackData) ? 'empty_callback' : 'callback_received'
);

$callbackResult = $vakifBank->callback($callbackData);

// Vakıf Katılım başarılı kodu "00"
if (isset($callbackResult['Rc']) && $callbackResult['Rc'] == '00') {
    /** Ödeme Başarılı **/
    // Bankadan dönen sipariş numarası (BAGIS öneki çıkarılmış olabilir, kontrol et)
    $orderId = $callbackResult['TransactionId'];
    
    // Veritabanındaki sipariş numarası formatı "BAGIS" ile başlıyorsa ve gelen ID sadece sayı ise:
    $siparis = "BAGIS" . $orderId;
    
    // Veritabanında bu sipariş var mı kontrol et
    $BagisKontrol = $db->prepare("SELECT * FROM bagis_odeme WHERE spno = ?");
    $BagisKontrol->execute([$siparis]);
    $BagisBilgi = $BagisKontrol->fetch(PDO::FETCH_ASSOC);
    
    // Eğer BAGIS eklenince bulunamadıysa, belki de ham haliyle kayıtlıdır veya banka BAGIS ile döndürmüştür
    if (!$BagisBilgi) {
        $siparis = $orderId; // Ham hali dene
        $BagisKontrol->execute([$siparis]);
        $BagisBilgi = $BagisKontrol->fetch(PDO::FETCH_ASSOC);
    }

    if ($BagisBilgi) {
        // Banka yanıtını logla - BAŞARILI
        $vakifLogger->logBankResponse($orderId, $callbackResult, true);
        
        // Bağış ödemesini güncelle
        $PayGuncelle = $db->prepare("UPDATE bagis_odeme SET paytronay = '1', durum = '1', odeme_yontemi = 'vakifbank' WHERE id = ?");
        $PayGuncelle->execute([$BagisBilgi['id']]);
        
        // Kampanya toplam tutarını güncelle
        if(isset($BagisBilgi['tutar']) && $BagisBilgi['tutar'] > 0 && isset($BagisBilgi['modul_id'])) {
            $KampanyaGuncelle = $db->prepare("UPDATE bagis_moduller SET toplanan_tutar = toplanan_tutar + ? WHERE id = ?");
            $KampanyaGuncelle->execute(array($BagisBilgi['tutar'], $BagisBilgi['modul_id']));
        }
        
        $veriler = [
            'adi' => $BagisBilgi['adi'],
            'email'   => $BagisBilgi['email'],
            'telefon' => $BagisBilgi['telefon'],
            'tutar'   => $BagisBilgi['tutar'],
            'spno'    => $BagisBilgi['spno']
        ];

        bildirim_sablon_gonder(2, $veriler, $BagisBilgi['email'], $BagisBilgi['telefon']);
        // ----------------------------------------
        
        // Başarılı ödeme sayfasına yönlendir
        header("Location: " . $merchant_ok_url);
        exit;
    } else {
        // Sipariş veritabanında bulunamadı
        error_log("Sipariş bulunamadı: " . $siparis);
        header("Location: " . $merchant_fail_url);
        exit;
    }
} else {
    /** Ödeme Hata **/
    // Banka yanıtını logla - BAŞARISIZ
    $orderId = $callbackResult['TransactionId'] ?? 'UNKNOWN';
    $vakifLogger->logBankResponse($orderId, $callbackResult, false);
    
    // Hata detayını loglayabiliriz
    error_log("Ödeme Hatası: " . ($callbackResult['ErrorCode'] ?? 'Bilinmeyen Hata'));
    
    // Hata sayfasına yönlendir
    header("Location: " . $merchant_fail_url);
    exit;
}
?>

