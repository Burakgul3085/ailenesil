<?php
// Bu dosya sunucuda cron job olarak ayarlanmalıdır (Örn: her ayın 1'inde)
// php /path/to/_class/cron_aylik_kontrol.php

define("GUVENLIK", true);
require_once '../_class/baglan.php';
require_once '../_class/Bildirim.php';

// CLI kontrolü ve tarayıcı erişimi için güvenlik
$isCli = (php_sapi_name() === 'cli');
$secretKey = "cron_secret_123"; 

if (!$isCli) {
    if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
        die("Yetkisiz erişim.");
    }
    // Browser için HTML başlığı
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Cron Raporu</title>';
    echo '<style>body{font-family:sans-serif;padding:20px;line-height:1.6}.log{background:#f4f4f4;padding:15px;border-radius:5px;border:1px solid #ddd}.success{color:green}.warning{color:orange}.error{color:red}</style>';
    echo '</head><body>';
    echo '<h2><i class="fa fa-clock"></i> Aylık Kontrol Raporu</h2>';
    echo '<div class="log"><pre>';
}

// Bildirim sınıfını başlat
$bildirimSistemi = new Bildirim($db);

echo "Kontrol Başlangıç Zamanı: " . date("Y-m-d H:i:s") . "\n";
echo "----------------------------------------\n";

// Aktif hamilikleri getir
$sorgu = $db->query("SELECT * FROM hamilikler WHERE durum = 'aktif'");
$hamilikler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

$buAy = date('Y-m');
$buYil = date('Y');
$gecikmeSayisi = 0;

echo "Toplam Aktif Hamilik: " . count($hamilikler) . "\n";

foreach ($hamilikler as $hamilik) {
    $hamilikId = $hamilik['id'];
    $yetimId = $hamilik['yetim_id'];
    $bagisciId = $hamilik['bagisci_id'];
    $tur = $hamilik['tur']; // 'aylik' veya 'yillik'
    
    $odemeYapildi = false;

    if ($tur == 'aylik') {
        // Bu ay için ödeme var mı kontrol et
        $kontrolSorgusu = $db->prepare("
            SELECT COUNT(*) 
            FROM bagis_odeme 
            WHERE yetim_id = ? 
            AND DATE_FORMAT(tarih, '%Y-%m') = ?
        ");
        $kontrolSorgusu->execute([$yetimId, $buAy]);
        $sayi = $kontrolSorgusu->fetchColumn();
        
        if ($sayi > 0) {
            $odemeYapildi = true;
        }
    } elseif ($tur == 'yillik') {
        // Bu yıl için ödeme var mı
        $kontrolSorgusu = $db->prepare("
            SELECT COUNT(*) 
            FROM bagis_odeme 
            WHERE yetim_id = ? 
            AND DATE_FORMAT(tarih, '%Y') = ?
        ");
        $kontrolSorgusu->execute([$yetimId, $buYil]);
        $sayi = $kontrolSorgusu->fetchColumn();
        
        if ($sayi > 0) {
            $odemeYapildi = true;
        }
    }

    if (!$odemeYapildi) {
        // Ödeme yapılmadıysa bildirim oluştur
        echo "[GECİKME] Hamilik ID: $hamilikId - Tür: $tur - Yetim ID: $yetimId\n";
        $gecikmeSayisi++;
        
        $bildirimSistemi->logBildirim(
            $yetimId, 
            'odeme_gecikmesi', 
            "Hamilik ID: $hamilikId için $tur ödemesi bu dönem yapılmadı."
        );
    }
}

echo "----------------------------------------\n";
echo "Kontrol Tamamlandı.\n";
echo "Toplam Tespit Edilen Gecikme: " . $gecikmeSayisi . "\n";

if (!$isCli) {
    echo '</pre></div>';
    if($gecikmeSayisi == 0) {
        echo '<p class="success"><b>Durum:</b> Herhangi bir gecikme tespit edilmedi, her şey yolunda.</p>';
    } else {
        echo '<p class="warning"><b>Durum:</b> ' . $gecikmeSayisi . ' adet ödeme gecikmesi tespit edildi ve günlüğe işlendi.</p>';
    }
    echo '</body></html>';
}
