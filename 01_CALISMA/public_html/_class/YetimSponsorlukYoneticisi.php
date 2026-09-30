<?php
/**
 * Yetim Sponsorluk Yönetimi
 * Sponsor değişimi ve durum güncelleme fonksiyonları
 */
class YetimSponsorlukYoneticisi {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Her yeni bağışta yetim sponsorluk durumunu kontrol eder ve günceller
     */
    public function revizeYetimSponsorlugu($bagis_id) {
        try {
            // Bu bağışın detaylarını al
            $bagisSorgu = $this->db->prepare("
                SELECT bo.*, h.yetim_id, h.bagisci_id, h.id as hamilik_id 
                FROM bagis_odeme bo
                LEFT JOIN hamilikler h ON (bo.yetim_id = h.yetim_id AND bo.hamilik_id = h.id)
                WHERE bo.id = ?
            ");
            $bagisSorgu->execute([$bagis_id]);
            $bagis = $bagisSorgu->fetch(PDO::FETCH_ASSOC);
            
            if (!$bagis || empty($bagis['yetim_id'])) {
                return false; // Yetim sponsorluğu değil
            }
            
            // Yetimin mevcut durumunu kontrol et
            $yetimSorgu = $this->db->prepare("
                SELECT y.hami_durumu, 
                       (SELECT COUNT(*) FROM hamilikler hy WHERE hy.yetim_id = y.id AND hy.durum = 'aktif') as aktif_hami_sayisi,
                       (SELECT MAX(hy.created_at) FROM hamilikler hy WHERE hy.yetim_id = y.id AND hy.durum = 'aktif') as son_hamilik_tarihi
                FROM yetimler y WHERE y.id = ?
            ");
            $yetimSorgu->execute([$bagis['yetim_id']]);
            $yetim = $yetimSorgu->fetch(PDO::FETCH_ASSOC);
            
            if (!$yetim) {
                return false;
            }
            
            // Eğer aktif hamili yoksa ve bu bağış başarılı ise yeni hamilik ata
            if ($yetim['aktif_hami_sayisi'] == 0 && $bagis['durum'] == 'basarili' && $bagis['paytronay'] == 1) {
                return $this->yeniSponsorAta($bagis['yetim_id'], $bagis['bagisci_id'], $bagis['tutar']);
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Sponsorluk revize hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 3 parametreli sponsor ödeme kesinti methodu
     */
    public function sponsorOdemeyiKesince3($bagisci_id, $tutar, $sponsorluk_tipi) {
        return $this->sponsorOdemeyiKesince($bagisci_id, $tutar);
    }
    
    /**
     * Yeni yetim için otomatik sponsor atama
     */
    public function yeniSponsorAta($yetim_id, $bagisci_id, $tutar) {
        try {
            require_once(__DIR__ . '/Eslestirme.php');
            $eslestirme = new Eslestirme($this->db);
            
            // Hamilik kaydı oluştur
            $bitisTarihi = date('Y-m-d', strtotime("+12 months"));
            
            $hamilikEkle = $this->db->prepare("
                INSERT INTO hamilikler 
                (yetim_id, bagisci_id, baslangic_tarihi, bitis_tarihi, 
                 tur, tutar, durum, eslestirme_nedeni, eslestirme_tarihi) 
                VALUES (?, ?, NOW(), ?, 'aylik', ?, 'aktif', 'Başarılı ödeme sonrası atama', NOW())
            ");
            $hamilikEkle->execute([$yetim_id, $bagisci_id, $bitisTarihi, $tutar]);
            $hamilik_id = $this->db->lastInsertId();
            
            // Yetim durumunu güncelle
            $yetimGuncelle = $this->db->prepare("
                UPDATE yetimler 
                SET hami_durumu = 1, 
                    yeni_sponsor_bekliyor = 0,
                    son_hamilik_baslangic = NOW() 
                WHERE id = ?
            ");
            $yetimGuncelle->execute([$yetim_id]);
            
            // Eşleştirme logu
            $this->logKaydet($yetim_id, $bagisci_id, $hamilik_id, 'yeni_sponsor_atama', 'Başarılı ödeme sonrası otomatik atama');
            
            return ['status' => true, 'hamilik_id' => $hamilik_id];
            
        } catch (Exception $e) {
            error_log("Yeni sponsor atama hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Bekleyen yetimler için yeni sponsor bulma
     */
    public function bekleyenYetimlereSponsorBul() {
        try {
            // Yeni sponsor bekleyen yetimleri bul
            $bekleyenYetimler = $this->db->prepare("
                SELECT y.* FROM yetimler y
                WHERE y.yeni_sponsor_bekliyor = 1 AND y.durum = 1 AND y.hami_durumu = 0
                ORDER BY y.son_hamilik_bitis ASC
            ");
            $bekleyenYetimler->execute();
            $yetimler = $bekleyenYetimler->fetchAll(PDO::FETCH_ASSOC);
            
            $atananSayisi = 0;
            
            foreach ($yetimler as $yetim) {
                // Bu yetim için uygun sponsor bul
                $sponsorBul = $this->uygunSponsorBul($yetim['id']);
                
                if ($sponsorBul && $this->yeniSponsorAta($yetim['id'], $sponsorBul['bagisci_id'], $sponsorBul['tutar'])) {
                    $atananSayisi++;
                }
            }
            
            return ['status' => true, 'atanan_sayi' => $atananSayisi];
            
        } catch (Exception $e) {
            error_log("Bekleyen yetimlere sponsor bulma hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Yetim için uygun sponsor bulur
     */
    private function uygunSponsorBul($yetim_id) {
        // Burada AI tabanlı veya kural tabanlı sponsor bulma mantığı
        // Şimdilik en son bağış yapanı bulalım
        $sponsorSorgu = $this->db->prepare("
            SELECT b.id as bagisci_id, AVG(bo.tutar) as ortalama_tutar
            FROM bagiscilar b
            JOIN bagis_odeme bo ON b.id = bo.bagisci_id
            WHERE bo.durum = 'basarili' AND bo.paytronay = 1
            AND b.id NOT IN (
                SELECT hy.bagisci_id FROM hamilikler hy 
                WHERE hy.durum = 'aktif' AND hy.yetim_id = ?
            )
            GROUP BY b.id
            ORDER BY AVG(bo.tutar) DESC
            LIMIT 1
        ");
        $sponsorSorgu->execute([$yetim_id]);
        return $sponsorSorgu->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Log kaydı oluşturur
     */
    private function logKaydet($yetim_id, $bagisci_id, $hamilik_id, $islem_tipi, $aciklama) {
        try {
            $logEkle = $this->db->prepare("
                INSERT INTO yetim_eslestirme_loglari 
                (yetim_id, bagisci_id, hamilik_id, eslestirme_tipi, eslestirme_nedeni, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $logEkle->execute([$yetim_id, $bagisci_id, $hamilik_id, $islem_tipi, $aciklama]);
        } catch (Exception $e) {
            error_log("Log kayıt hatası: " . $e->getMessage());
        }
    }
    
    /**
     * Ödeme durumunu kontrol eder ve gerekirse yeni sponsor atar
     */
    public function odemeDurumunuKontrolEt($hamilik_id) {
        try {
            // Son 3 aydaki ödemeleri kontrol et
            $odemeKontrol = $this->db->prepare("
                SELECT COUNT(*) as odenen_ay_sayisi,
                       SUM(CASE WHEN bo.durum = 'basarili' THEN 1 ELSE 0 END) as basarili_odeme_sayisi
                FROM bagis_odeme bo
                WHERE bo.hamilik_id = ? 
                AND bo.tarih >= DATE_SUB(NOW(), INTERVAL 90 DAY)
            ");
            $odemeKontrol->execute([$hamilik_id]);
            $odemeDurumu = $odemeKontrol->fetch(PDO::FETCH_ASSOC);
            
            // Eğer son 3 ayda ödeme yoksa sponsoru değiştir
            if ($odemeDurumu['odenen_ay_sayisi'] == 0) {
                return $this->sponsorOdemeyiKesince($hamilik_id, '3 aydır ödeme yapılmadı');
            }
            
            return ['status' => true, 'odeme_durumu' => 'normal'];
            
        } catch (Exception $e) {
            error_log("Ödeme durumu kontrol hatası: " . $e->getMessage());
            return false;
        }
    }
}