<?php
// Ödeme Hatırlatma Sistemi - Cron Job
// Bu dosya her gün çalıştırılmalıdır (örn: cron: 0 9 * * * php odeme_hatirlatma_cron.php)

require_once "../_class/baglan.php";
require_once "../_class/fonksiyon.php";
require_once "../_class/class.phpmailer.php";

header('Content-Type: text/plain; charset=utf-8');

echo "=== ÖDEME HATIRLATMA SİSTEMİ ===\n";
echo "Başlangıç: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Bugünün tarihini al
    $bugun = date('Y-m-d');
    
    // Bugün hatırlatılması gereken kayıtları getir
    $hatirlatma_sorgu = $db->prepare("
        SELECT 
            oh.*,
            h.yetim_id,
            h.bagisci_id,
            h.tutar,
            h.aylik_odeme_tipi,
            h.secilen_ay_sayisi,
            b.ad_soyad as bagisci_ad,
            b.email as bagisci_email,
            b.telefon as bagisci_telefon,
            y.ad_soyad as yetim_ad
        FROM odeme_hatirlatmalar oh
        JOIN hamilikler h ON oh.hamilik_id = h.id
        JOIN bagiscilar b ON h.bagisci_id = b.id
        JOIN yetimler y ON h.yetim_id = y.id
        WHERE oh.hatirlatma_tarihi = ? 
        AND oh.durum = 'beklemede'
        AND h.durum = 'aktif'
        ORDER BY oh.id ASC
    ");
    $hatirlatma_sorgu->execute([$bugun]);
    $hatirlatmalar = $hatirlatma_sorgu->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Bugün " . count($hatirlatmalar) . " hatırlatma bulundu.\n\n";
    
    if(count($hatirlatmalar) > 0) {
        foreach($hatirlatmalar as $hatirlatma) {
            echo "İşleniyor: Hami ID: {$hatirlatma['hamilik_id']}, Bağışçı: {$hatirlatma['bagisci_ad']}\n";
            
            $mail_gonderildi = false;
            $sms_gonderildi = false;
            $hata_mesaji = '';
            
            // Bildirim gönder (ID 11: Ödeme Hatırlatma)
            $veriler = [
                'adsoyad' => $hatirlatma['bagisci_ad'],
                'yetim_ad' => $hatirlatma['yetim_ad'],
                'tutar' => number_format($hatirlatma['tutar'], 2, ',', '.') . " ₺",
                'odeme_tipi' => ($hatirlatma['aylik_odeme_tipi'] == 'tek_seferde' ? 'Tek Seferde' : 'Aylık'),
                'kalan_ay' => $hatirlatma['secilen_ay_sayisi'] . " ay",
                'odeme_url' => url . "/bagis.html"
            ];

            $sonuc = bildirim_sablon_gonder(11, $veriler, $hatirlatma['bagisci_email'], $hatirlatma['bagisci_telefon']);
            
            if($sonuc) {
                $mail_gonderildi = true; // Fonksiyon içinde kontrol yapıldığı için genel başarı kabul ediyoruz
                echo "  ✓ Bildirim (Mail/SMS) işlendi\n";
            } else {
                echo "  ✗ Bildirim gönderilemedi (Şablon bulunamadı veya ayarlar hatalı)\n";
                $hata_mesaji .= "Bildirim gönderilemedi; ";
            }
            
            // Hatırlatma durumunu güncelle
            $yeni_durum = ($mail_gonderildi || $sms_gonderildi) ? 'gonderildi' : 'hata';
            $guncelle = $db->prepare("UPDATE odeme_hatirlatmalar SET durum = ?, gonderim_tarihi = NOW(), hata_mesaji = ? WHERE id = ?");
            $guncelle->execute([$yeni_durum, $hata_mesaji, $hatirlatma['id']]);
            
            echo "  Durum güncellendi: {$yeni_durum}\n\n";
            
        }
    }
    
    echo "İşlem tamamlandı.\n";
    echo "Bitiş: " . date('Y-m-d H:i:s') . "\n";
    
} catch(PDOException $e) {
    echo "Veritabanı hatası: " . $e->getMessage() . "\n";
} catch(Exception $e) {
    echo "Genel hata: " . $e->getMessage() . "\n";
}
?>