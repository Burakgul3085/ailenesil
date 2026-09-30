<?php
/**
 * Sponsorluk Ödeme Kontrolü - Cron Job
 * Her gün çalışarak ödeme durumlarını kontrol eder ve gerekli işlemleri yapar
 */
require_once('../_class/baglan.php');
require_once('../_class/YetimSponsorlukYoneticisi.php');

echo "=== Sponsorluk Ödeme Kontrolü Başlatıldı: " . date('Y-m-d H:i:s') . " ===\n";

$sponsorlukYoneticisi = new YetimSponsorlukYoneticisi($db);

// 1. Aktif hamiliklerde ödeme kontrolü yap
$odemeKontrolSorgu = $db->prepare("
    SELECT h.*, 
           (SELECT COUNT(*) FROM bagis_odeme bo 
            WHERE bo.hamilik_id = h.id 
            AND bo.tarih >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            AND bo.durum = 'basarili') as son_30_gun_odeme_sayisi,
           
           (SELECT MAX(bo.tarih) FROM bagis_odeme bo 
            WHERE bo.hamilik_id = h.id 
            AND bo.durum = 'basarili') as son_odeme_tarihi
           
    FROM hamilikler h
    WHERE h.durum = 'aktif'
    AND h.tur = 'aylik'
");
$odemeKontrolSorgu->execute();
$aktifHamilikler = $odemeKontrolSorgu->fetchAll(PDO::FETCH_ASSOC);

$iptalEdilenHamilikSayisi = 0;
$kontrolEdilenHamilikSayisi = 0;

foreach ($aktifHamilikler as $hamilik) {
    $kontrolEdilenHamilikSayisi++;
    
    // Son ödeme kontrolü
    $sonOdemeTarihi = $hamilik['son_odeme_tarihi'];
    $bugun = date('Y-m-d');
    
    if ($sonOdemeTarihi) {
        $gunFarki = (strtotime($bugun) - strtotime($sonOdemeTarihi)) / (60 * 60 * 24);
        
        // 90 gün (3 ay) ödeme yapılmadıysa iptal et
        if ($gunFarki > 90) {
            echo "Hamilik ID: {$hamilik['id']} - 90 gündür ödeme yapılmadı, iptal ediliyor...\n";
            
            $sonuc = $sponsorlukYoneticisi->sponsorOdemeyiKesince($hamilik['id'], '90 gündür ödeme yapılmadı');
            
            if ($sonuc && $sonuc['status']) {
                $iptalEdilenHamilikSayisi++;
                echo "✓ Hamilik iptal edildi, yeni sponsor gerekli: " . ($sonuc['yeni_sponsor_gerekli'] ? 'EVET' : 'HAYIR') . "\n";
            } else {
                echo "✗ Hamilik iptal edilemedi\n";
            }
        } else if ($gunFarki > 60) {
            echo "Hamilik ID: {$hamilik['id']} - 60 gündür ödeme yapılmadı, uyarı gönderilebilir\n";
            // İsterseniz burada SMS/email uyarısı gönderebilirsiniz
        }
    }
}

// 2. Yeni sponsor bekleyen yetimlere sponsor bul
echo "\nYeni sponsor bekleyen yetimlere sponsor aranıyor...\n";
$yenSponsorSonucu = $sponsorlukYoneticisi->bekleyenYetimlereSponsorBul();

if ($yenSponsorSonucu && $yenSponsorSonucu['status']) {
    echo "✓ {$yenSponsorSonucu['atanan_sayisi']} yetime yeni sponsor atandı\n";
} else {
    echo "✗ Yeni sponsor ataması yapılamadı veya atanacak yetim bulunamadı\n";
}

// 3. Özet rapor
echo "\n=== Özet Rapor ===\n";
echo "Kontrol edilen aktif hamilik: {$kontrolEdilenHamilikSayisi}\n";
echo "İptal edilen hamilik: {$iptalEdilenHamilikSayisi}\n";
echo "Yeni atanan sponsor: " . ($yenSponsorSonucu['atanan_sayisi'] ?? 0) . "\n";
echo "İşlem tamamlandı: " . date('Y-m-d H:i:s') . "\n";
echo "=========================\n";

// Log kaydı
$logMesaji = "Ödeme kontrolü tamamlandı. Kontrol: {$kontrolEdilenHamilikSayisi}, İptal: {$iptalEdilenHamilikSayisi}, Yeni Sponsor: " . ($yenSponsorSonucu['atanan_sayisi'] ?? 0);
error_log("Sponsorluk Ödeme Kontrolü Cron: " . $logMesaji);

?>