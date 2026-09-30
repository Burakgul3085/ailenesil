<?php
session_start();
ob_start();

require_once('../../_class/baglan.php');
require_once('../../_class/fonksiyon.php');

// Giriş kontrolü
if(!isset($_SESSION['Yonetim_Id']) || !isset($_SESSION['Yonetim_Kadi']))
{
    echo json_encode(['success' => false, 'message' => 'Oturum bulunamadı']);
    exit;
}

// Admin bilgilerini kontrol et
$oturumkontrol = $db->prepare("SELECT * FROM kullanici WHERE BINARY id = ? AND kadi = ? AND sifre = ?");
$oturumkontrol->execute(array($_SESSION['Yonetim_Id'], $_SESSION['Yonetim_Kadi'], $_SESSION['Yonetim_Sifre']));

if($oturumkontrol->rowCount() == 0)
{
    echo json_encode(['success' => false, 'message' => 'Geçersiz oturum']);
    exit;
}

// JSON verisini al
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if(!$data || !is_array($data))
{
    echo json_encode(['success' => false, 'message' => 'Geçersiz veri formatı']);
    exit;
}

try 
{
    // Transaction başlat
    $db->beginTransaction();
    
    // Her bir öğe için sıralama güncelle
    foreach($data as $item)
    {
        if(!isset($item['id']) || !isset($item['sira']))
        {
            continue;
        }
        
        $Guncelle = $db->prepare("UPDATE anasayfa_alanlar SET sira = ? WHERE id = ?");
        $Guncelle->execute([$item['sira'], $item['id']]);
    }
    
    // Log kaydı oluştur (opsiyonel)
    $admin_id = $_SESSION['Yonetim_Id'] ?? 0;
    $degisiklik = "Alan sıralaması güncellendi";
    $yeni_sira = json_encode($data);
    
    $LogEkle = $db->prepare("INSERT INTO anasayfa_alanlar_log SET 
        admin_id = ?, 
        degisiklik = ?, 
        yeni_sira = ?,
        tarih = NOW()
    ");
    $LogEkle->execute([$admin_id, $degisiklik, $yeni_sira]);
    
    // Transaction commit
    $db->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Sıralama başarıyla kaydedildi',
        'updated_count' => count($data)
    ]);
    
} 
catch(Exception $e) 
{
    // Hata durumunda rollback
    $db->rollBack();
    
    error_log("Anasayfa Alan Sıralama Hatası: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
}
?>

