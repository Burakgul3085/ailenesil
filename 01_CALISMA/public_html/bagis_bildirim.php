<?php
session_start();
require_once '../_class/baglan.php';
require_once '../_class/fonksiyon.php';

// Bildirim gönderme AJAX endpoint
if(isset($_POST['bildirim_gonder']) && !empty($_POST['yetim_id']) && !empty($_POST['islem'])) {
    $yetim_id = intval($_POST['yetim_id']);
    $islem = $_POST['islem']; // bildirim tipi: egitim, saglik, yardim, etc.
    $mesaj = $_POST['mesaj'] ?? '';
    $bagisci_id = $_SESSION['Yonetim_Id'] ?? 0;
    
    try {
        // Yetim bilgilerini al
        $yetimSorgu = $db->prepare("SELECT y.*, b.ad_soyad as sponsor_adi FROM yetimler y LEFT JOIN bagiscilar b ON y.baba_adi LIKE CONCAT('%', b.ad_soyad, '%') OR y.anne_adi LIKE CONCAT('%', b.anne_adi, '%') WHERE y.id = ?");
        $yetimSorgu->execute([$yetim_id]);
        $yetim = $yetimSorgu->fetch(PDO::FETCH_ASSOC);
        
        if (!$yetim) {
            echo json_encode(['success' => false, 'message' => 'Yetim bulunamadı']);
            exit;
        }
        
        // Bildirim verisini oluştur
        $bildirimVerisi = [
            'yetim_id' => $yetim_id,
            'yetim_adi' => $yetim['ad_soyad'],
            'sponsor_adi' => $yetim['sponsor_adi'] ?? 'Bilinmed',
            'islem' => $islem,
            'mesaj' => $mesaj,
            'bagisci_id' => $bagisci_id,
            'tarih' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];
        
        // Bildirim işlemini yap
        $bildirimKayit = $db->prepare("
            INSERT INTO yetim_bildirimler 
            (yetim_id, bagisci_id, islem, mesaj, tarih, ip)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $bildirimKayit->execute([
            $bildirimVerisi['yetim_id'],
            $bildirimVerisi['bagisci_id'],
            $bildirimVerisi['islem'],
            $bildirimVerisi['mesaj'],
            $bildirimVerisi['tarih'],
            $bildirimVerisi['ip']
        ]);
        
        // Bildirim template'ine göre bildirim gönder
        if($bildirimKayit) {
            $templateTipi = '';
            $konu = '';
            
            switch($islem) {
                case 'egitim':
                    $templateTipi = 'egitim_bildirim';
                    $konu = 'Eğitim Bildirimi';
                    break;
                case 'saglik':
                    $templateTipi = 'saglik_bildirim';
                    $konu = 'Sağlık Bildirimi';
                    break;
                case 'yardim':
                    $templateTipi = 'yardim_bildirim';
                    $konu = 'Yardım Bildirimi';
                    break;
                case 'bagis':
                    $templateTipi = 'bagis_bildirim';
                    $konu = 'Bağış Bildirimi';
                    break;
                default:
                    $templateTipi = 'genel_bildirim';
                    $konu = 'Bildirim';
                    break;
            }
            
            // Bildirim içeriği oluştur
            $bildirimIcerigi = [];
            switch($templateTipi) {
                case 'egitim_bildirim':
                    $bildirimIcerigi[] = [
                        'Merhaba ' . $yetim['ad_soyad'] . ', 'yetimin eğitim durumu hakkında bilgilendir.',
                        'Eğitim durumu: ' . ($yetim['egitim_durumu'] ?? 'Belirtilmemiş'),
                        'Yaş: ' . (isset($yetim['dogum_tarihi']) ? date('Y-m-d', strtotime($yetim['dogum_tarihi'])) . ' yaşında',
                        'Sponsor: ' . ($yetim['sponsor_adi'] ?? 'Henüz sponsor atanmamış')
                    ];
                    break;
                    
                case 'saglik_bildirim':
                    $bildirimIcerigi[] = [
                        'Merhaba ' . $yetim['ad_soyad'] . ' için sağılık bildirimi yapılmıştır.',
                        'Sağlık durumu: ' . ($yetim['vasi_durumu'] ?? 'Belirtilmemiş'),
                        'Yaş: ' . (isset($yetim['dogum_tarihi']) ? date('Y-m-d', strtotime($yetim['dogum_tarihi'])) . ' yaşında',
                        'Sponsor: ' . ($yetim['sponsor_adi'] ?? 'Henüz sponsor atanmamış')
                    ];
                    break;
                    
                case 'yardim_bildirim':
                    $bildirimIcerigi[] = [
                        'Merhaba ' . $yetim['ad_soyad'] . ' için yardım bildirim yapılmıştır.',
                        'Yardım durumu: ' . ($yetim['aile_gelir_durumu'] ?? 'Belirtilmemiş'),
                        'Adres: ' . ($yetim['adres_detay'] ?? 'Belirtilmiş'),
                        'Sponsor: ' . ($yetim['sponsor_adi'] ?? 'Henüz sponsor atanmamış')
                    ];
                    break;
                    
                case 'bagis_bildirim':
                    $bildirimIcerigi[] = [
                        'Merhaba ' . $yetim['ad_soyad'] . ' için bağış bildirim yapılmıştır.',
                        'Bağış tutarı: ' . ($yetim['tutar'] ?? '0') . ' TL',
                        'Sponsor: ' . ($yetim['sponsor_adi'] ?? 'Henüz sponsor atanmamış'),
                        'Tarih: ' . date('d.m.Y H:i'),
                        'IP: ' . $_SERVER['REMOTE_ADDR'] ?? '')
                    ];
                    break;
                    
                default:
                    $bildirimIcerigi[] = [
                        'Merhaba ' . $yetim['ad_soyad'] . ' için bildirim yapılmıştır.',
                        'Tarih: ' . date('d.m.Y H:i')
                    ];
                    break;
            }
            
            // Bildirim metni ve logla
            foreach ($bildirimIcerigi as $icerik) {
                if(!empty($icerik)) {
                    $sonuc = $db->prepare("
                        INSERT INTO yetim_bildirim_detaylari 
                        (bildirim_id, metin, olusturulma)
                        VALUES (?, ?, ?)
                    ");
                    $sonuc->execute([$bildirimKayit->lastInsertId(), trim($icerik)]);
                }
            }
            
            // Bildirim gönderme sistemi buraya entegre edilecek
            require_once '../_class/Bagisici.php';
            $bagisici = new Bagisici($db);
            
            // E-posta bildirim
            $epostaGonder = $bagisici->epostaGonder([
                'email' => $yetim['baba_adi'] ?? 'Bilinmemiş',
                'subject' => 'Yönetim Yetim Bildirimi',
                'message' => $konu,
                'content' => implode("\n\n", $bildirimIcerigi),
                'attachments' => []
            ], [
                'aile_telefonu' => $yetim['aile_telefonu'] ?? '',
                'html' => true
            ]);
            
            if($epostaGonder['success']) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Bildirim başarıyla gönderildi.',
                    'bildirim_id' => $bildirimKayit->lastInsertId()
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Bildirim gönderilirken hata oluştu: ' . ($epostaGonder['message'] ?? 'Bilinmeli hata')
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Bildirim gönderme hatası: " . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Bildirim gönderilirken sistem hatası'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Geçersiz parametreler eksik'
        ]);
    }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Yetkim ID ve islem parametresi gerekli'
        ]);
    }
}
?>