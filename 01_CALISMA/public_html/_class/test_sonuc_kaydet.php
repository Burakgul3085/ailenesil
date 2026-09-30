<?php
session_start();
require_once("baglan.php");

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_id'], $_POST['sonuc'], $_POST['dominant'])) {
    $test_id = (int)$_POST['test_id'];
    $sonuc_json = $_POST['sonuc'];
    $dominant = (int)$_POST['dominant'];
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    $veli_ad = $_POST['veli_ad'] ?? '';
    $ogrenci_ad = $_POST['ogrenci_ad'] ?? '';
    $ogrenci_yas = isset($_POST['ogrenci_yas']) ? (int)$_POST['ogrenci_yas'] : null;
    $telefon = $_POST['telefon'] ?? '';
    $email = $_POST['email'] ?? '';

    $stmt = $db->prepare("INSERT INTO test_sonuclari (test_id, veli_ad, ogrenci_ad, ogrenci_yas, telefon, email, sonuc_json, dominant_mizac, ip_adresi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(array($test_id, $veli_ad, $ogrenci_ad, $ogrenci_yas, $telefon, $email, $sonuc_json, $dominant, $ip));

    echo json_encode(['status' => 'ok']);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error']);
}
