<?php
session_start();
require_once('_class/baglan.php');
$dil_dosya = 'language/dil_'.($_SESSION['k_dil'] ?? '1').".php";
if(file_exists($dil_dosya)) {
    require_once($dil_dosya);
} else {
    $dil = [];
}

// Güvenlik kontrolü
if (!isset($_POST['para_birimi'])) {
    header('HTTP/1.1 400 Bad Request');
    exit(isset($dil['txt593']) ? $dil['txt593'] : 'Para birimi belirtilmedi');
}

// Geçerli para birimlerini tanımla
$gecerliParaBirimleri = ['TRY', 'USD', 'EUR'];

// Gelen para birimini kontrol et
$paraBirimi = $_POST['para_birimi'];
if (!in_array($paraBirimi, $gecerliParaBirimleri)) {
    header('HTTP/1.1 400 Bad Request');
    exit(isset($dil['txt594']) ? $dil['txt594'] : 'Geçersiz para birimi');
}

// Session'a kaydet
$_SESSION['para_birimi'] = $paraBirimi;

// Başarılı yanıt
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => (isset($dil['txt595']) ? $dil['txt595'] : 'Para birimi güncellendi')]);
?>