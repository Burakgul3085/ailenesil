<?php
/**
 * BAĞIŞ BİLDİRİM İŞLEM DOSYASI
 * Yönetim panelinden manuel bildirim gönderimi için AJAX endpoint
 */

session_start();
require_once('baglan.php');
require_once('fonksiyon.php');

// Güvenlik: Admin kontrolü
if(!isset($_SESSION['yonetim']) || $_SESSION['yonetim'] != 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Yetkisiz erişim!'
    ]);
    exit;
}

// İşlem kontrolü
if(!isset($_POST['islem'])) {
    echo json_encode([
        'success' => false,
        'message' => 'İşlem tipi belirtilmedi!'
    ]);
    exit;
}

$islem = $_POST['islem'];

// =====================================================
// BİLDİRİM GÖNDER
// =====================================================
if($islem == 'bildirim_gonder') {
    
    $bagis_id = intval($_POST['bagis_id'] ?? 0);
    $tip = $_POST['tip'] ?? 'all'; // sms, email, whatsapp veya all
    
    if(!$bagis_id) {
        echo json_encode([
            'success' => false,
            'message' => 'Bağış ID bulunamadı!'
        ]);
        exit;
    }
    
    // Bağış bilgilerini kontrol et
    $bagis_sorgu = $db->prepare("SELECT * FROM bagis_odeme WHERE id = ?");
    $bagis_sorgu->execute([$bagis_id]);
    $bagis = $bagis_sorgu->fetch(PDO::FETCH_ASSOC);
    
    if(!$bagis) {
        echo json_encode([
            'success' => false,
            'message' => 'Bağış kaydı bulunamadı!'
        ]);
        exit;
    }
    
    // Bildirim tiplerini belirle
    $bildirim_tipleri = [];
    if($tip == 'all') {
        $bildirim_tipleri = ['sms', 'email', 'whatsapp'];
    } else {
        $bildirim_tipleri = [$tip];
    }
    
    // Bildirimleri gönder
    try {
        $sonuclar = bagis_bildirim_gonder($bagis_id, $bildirim_tipleri);
        
        // Sonuçları değerlendir
        $basarili = [];
        $hatali = [];
        
        foreach($sonuclar as $t => $durum) {
            if($durum) {
                $basarili[] = $t;
            } else {
                $hatali[] = $t;
            }
        }
        
        // Bildirim tarihini güncelle
        $db->prepare("UPDATE bagis_odeme SET bildirim_gonderildi = 1, bildirim_tarihi = NOW() WHERE id = ?")
            ->execute([$bagis_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Bildirimler gönderildi!',
            'basarili' => $basarili,
            'hatali' => $hatali,
            'detay' => $sonuclar
        ]);
        
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Hata: ' . $e->getMessage()
        ]);
    }
    exit;
}

// =====================================================
// SMS ŞABLONU KAYDET/GÜNCELLE
// =====================================================
if($islem == 'sms_sablon_kaydet') {
    
    $id = intval($_POST['id'] ?? 0);
    $sablon_adi = trim($_POST['sablon_adi'] ?? '');
    $sablon_tip = trim($_POST['sablon_tip'] ?? 'bagis_tesekkur');
    $mesaj = trim($_POST['mesaj'] ?? '');
    $durum = intval($_POST['durum'] ?? 1);
    
    if(empty($sablon_adi) || empty($mesaj)) {
        echo json_encode([
            'success' => false,
            'message' => 'Şablon adı ve mesaj zorunludur!'
        ]);
        exit;
    }
    
    if($id > 0) {
        // Güncelle
        $sorgu = $db->prepare("UPDATE sms_sablonlar SET 
            sablon_adi = ?,
            sablon_tip = ?,
            mesaj = ?,
            durum = ?,
            guncelleme_tarihi = NOW()
        WHERE id = ?");
        $sorgu->execute([$sablon_adi, $sablon_tip, $mesaj, $durum, $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'SMS şablonu güncellendi!'
        ]);
    } else {
        // Yeni ekle
        $sorgu = $db->prepare("INSERT INTO sms_sablonlar SET 
            sablon_adi = ?,
            sablon_tip = ?,
            mesaj = ?,
            durum = ?");
        $sorgu->execute([$sablon_adi, $sablon_tip, $mesaj, $durum]);
        
        echo json_encode([
            'success' => true,
            'message' => 'SMS şablonu eklendi!',
            'id' => $db->lastInsertId()
        ]);
    }
    exit;
}

// =====================================================
// E-POSTA ŞABLONU KAYDET/GÜNCELLE
// =====================================================
if($islem == 'email_sablon_kaydet') {
    
    $id = intval($_POST['id'] ?? 0);
    $sablon_adi = trim($_POST['sablon_adi'] ?? '');
    $sablon_tip = trim($_POST['sablon_tip'] ?? 'bagis_tesekkur');
    $konu = trim($_POST['konu'] ?? '');
    $icerik = $_POST['icerik'] ?? '';
    $sertifika_ekle = intval($_POST['sertifika_ekle'] ?? 0);
    $durum = intval($_POST['durum'] ?? 1);
    
    if(empty($sablon_adi) || empty($konu) || empty($icerik)) {
        echo json_encode([
            'success' => false,
            'message' => 'Şablon adı, konu ve içerik zorunludur!'
        ]);
        exit;
    }
    
    if($id > 0) {
        // Güncelle
        $sorgu = $db->prepare("UPDATE mail_sablonlar SET 
            sablon_adi = ?,
            sablon_tip = ?,
            konu = ?,
            icerik = ?,
            sertifika_ekle = ?,
            durum = ?,
            guncelleme_tarihi = NOW()
        WHERE id = ?");
        $sorgu->execute([$sablon_adi, $sablon_tip, $konu, $icerik, $sertifika_ekle, $durum, $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'E-posta şablonu güncellendi!'
        ]);
    } else {
        // Yeni ekle
        $sorgu = $db->prepare("INSERT INTO mail_sablonlar SET 
            sablon_adi = ?,
            sablon_tip = ?,
            konu = ?,
            icerik = ?,
            sertifika_ekle = ?,
            durum = ?");
        $sorgu->execute([$sablon_adi, $sablon_tip, $konu, $icerik, $sertifika_ekle, $durum]);
        
        echo json_encode([
            'success' => true,
            'message' => 'E-posta şablonu eklendi!',
            'id' => $db->lastInsertId()
        ]);
    }
    exit;
}

// =====================================================
// BİLDİRİM AYARLARI GÜNCELLE
// =====================================================
if($islem == 'bildirim_ayar_guncelle') {
    
    $sms_aktif = intval($_POST['sms_aktif'] ?? 0);
    $email_aktif = intval($_POST['email_aktif'] ?? 0);
    $whatsapp_aktif = intval($_POST['whatsapp_aktif'] ?? 0);
    $otomatik_gonderim = intval($_POST['otomatik_gonderim'] ?? 1);
    $gecikme_suresi = intval($_POST['gecikme_suresi'] ?? 0);
    
    $sorgu = $db->prepare("UPDATE bildirim_ayarlari SET 
        sms_aktif = ?,
        email_aktif = ?,
        whatsapp_aktif = ?,
        otomatik_gonderim = ?,
        gecikme_suresi = ?,
        guncelleme_tarihi = NOW()
    WHERE id = 1");
    
    $sorgu->execute([
        $sms_aktif,
        $email_aktif,
        $whatsapp_aktif,
        $otomatik_gonderim,
        $gecikme_suresi
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Bildirim ayarları güncellendi!'
    ]);
    exit;
}

// =====================================================
// BİLDİRİM LOGLARİNI GETİR
// =====================================================
if($islem == 'bildirim_log_getir') {
    
    $bagis_id = intval($_POST['bagis_id'] ?? 0);
    $tip = $_POST['tip'] ?? '';
    $limit = intval($_POST['limit'] ?? 100);
    
    $where = [];
    $params = [];
    
    if($bagis_id > 0) {
        $where[] = "bagis_id = ?";
        $params[] = $bagis_id;
    }
    
    if(!empty($tip)) {
        $where[] = "tip = ?";
        $params[] = $tip;
    }
    
    $where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    
    $sorgu = $db->prepare("SELECT * FROM bildirim_log $where_sql ORDER BY tarih DESC LIMIT ?");
    $params[] = $limit;
    $sorgu->execute($params);
    $loglar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $loglar,
        'toplam' => count($loglar)
    ]);
    exit;
}

// =====================================================
// İSTATİSTİKLER
// =====================================================
if($islem == 'bildirim_istatistik') {
    
    $gun = intval($_POST['gun'] ?? 30);
    
    // Toplam gönderilen bildirimler
    $toplam_sorgu = $db->prepare("
        SELECT 
            tip,
            COUNT(*) as adet,
            SUM(CASE WHEN durum = 'gonderildi' THEN 1 ELSE 0 END) as basarili,
            SUM(CASE WHEN durum = 'hata' THEN 1 ELSE 0 END) as hatali
        FROM bildirim_log 
        WHERE tarih >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY tip
    ");
    $toplam_sorgu->execute([$gun]);
    $toplam = $toplam_sorgu->fetchAll(PDO::FETCH_ASSOC);
    
    // Günlük dağılım
    $gunluk_sorgu = $db->prepare("
        SELECT 
            DATE(tarih) as tarih,
            tip,
            COUNT(*) as adet
        FROM bildirim_log 
        WHERE tarih >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY DATE(tarih), tip
        ORDER BY tarih DESC
    ");
    $gunluk_sorgu->execute([$gun]);
    $gunluk = $gunluk_sorgu->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'toplam' => $toplam,
        'gunluk' => $gunluk
    ]);
    exit;
}

// =====================================================
// WHATSAPP AYARLARI GÜNCELLE
// =====================================================
if($islem == 'whatsapp_ayar_guncelle') {
    
    $api_url = trim($_POST['api_url'] ?? '');
    $api_token = trim($_POST['api_token'] ?? '');
    $phone_number_id = trim($_POST['phone_number_id'] ?? '');
    $business_account_id = trim($_POST['business_account_id'] ?? '');
    $durum = intval($_POST['durum'] ?? 0);
    $test_modu = intval($_POST['test_modu'] ?? 1);
    
    if(empty($api_url) || empty($api_token)) {
        echo json_encode([
            'success' => false,
            'message' => 'API URL ve Token zorunludur!'
        ]);
        exit;
    }
    
    $sorgu = $db->prepare("UPDATE whatsapp_ayarlar SET 
        api_url = ?,
        api_token = ?,
        phone_number_id = ?,
        business_account_id = ?,
        durum = ?,
        test_modu = ?,
        guncelleme_tarihi = NOW()
    WHERE id = 1");
    
    $sorgu->execute([
        $api_url,
        $api_token,
        $phone_number_id,
        $business_account_id,
        $durum,
        $test_modu
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'WhatsApp ayarları güncellendi!'
    ]);
    exit;
}

// =====================================================
// SMS ŞABLON GETİR
// =====================================================
if($islem == 'sms_sablon_getir') {
    $id = intval($_POST['id'] ?? 0);
    
    $sorgu = $db->prepare("SELECT * FROM sms_sablonlar WHERE id = ?");
    $sorgu->execute([$id]);
    $sablon = $sorgu->fetch(PDO::FETCH_ASSOC);
    
    if($sablon) {
        echo json_encode([
            'success' => true,
            'data' => $sablon
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Şablon bulunamadı!'
        ]);
    }
    exit;
}

// =====================================================
// SMS ŞABLON SİL
// =====================================================
if($islem == 'sms_sablon_sil') {
    $id = intval($_POST['id'] ?? 0);
    
    $sorgu = $db->prepare("DELETE FROM sms_sablonlar WHERE id = ?");
    $sorgu->execute([$id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'SMS şablonu silindi!'
    ]);
    exit;
}

// =====================================================
// E-POSTA ŞABLON GETİR
// =====================================================
if($islem == 'email_sablon_getir') {
    $id = intval($_POST['id'] ?? 0);
    
    $sorgu = $db->prepare("SELECT * FROM mail_sablonlar WHERE id = ?");
    $sorgu->execute([$id]);
    $sablon = $sorgu->fetch(PDO::FETCH_ASSOC);
    
    if($sablon) {
        echo json_encode([
            'success' => true,
            'data' => $sablon
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Şablon bulunamadı!'
        ]);
    }
    exit;
}

// =====================================================
// E-POSTA ŞABLON SİL
// =====================================================
if($islem == 'email_sablon_sil') {
    $id = intval($_POST['id'] ?? 0);
    
    $sorgu = $db->prepare("DELETE FROM mail_sablonlar WHERE id = ?");
    $sorgu->execute([$id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'E-posta şablonu silindi!'
    ]);
    exit;
}

// =====================================================
// BİLDİRİM LOG DETAY
// =====================================================
if($islem == 'bildirim_log_detay') {
    $id = intval($_POST['id'] ?? 0);
    
    $sorgu = $db->prepare("SELECT * FROM bildirim_log WHERE id = ?");
    $sorgu->execute([$id]);
    $log = $sorgu->fetch(PDO::FETCH_ASSOC);
    
    if($log) {
        echo json_encode([
            'success' => true,
            'data' => $log
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Log kaydı bulunamadı!'
        ]);
    }
    exit;
}

// =====================================================
// BİLDİRİM LOG GETİR (FİLTRELİ)
// =====================================================
if($islem == 'bildirim_log_getir') {
    $bagis_id = intval($_POST['bagis_id'] ?? 0);
    $tip = $_POST['tip'] ?? '';
    $durum = $_POST['durum'] ?? '';
    $limit = intval($_POST['limit'] ?? 100);
    
    $where = [];
    $params = [];
    
    if($bagis_id > 0) {
        $where[] = "bagis_id = ?";
        $params[] = $bagis_id;
    }
    
    if(!empty($tip)) {
        $where[] = "tip = ?";
        $params[] = $tip;
    }
    
    if(!empty($durum)) {
        $where[] = "durum = ?";
        $params[] = $durum;
    }
    
    $where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    
    $sorgu = $db->prepare("SELECT * FROM bildirim_log $where_sql ORDER BY tarih DESC LIMIT ?");
    $params[] = $limit;
    $sorgu->execute($params);
    $loglar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $loglar,
        'toplam' => count($loglar)
    ]);
    exit;
}

// Bilinmeyen işlem
echo json_encode([
    'success' => false,
    'message' => 'Bilinmeyen işlem: ' . $islem
]);
exit;
?>

