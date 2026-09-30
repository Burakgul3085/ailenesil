<?php
/**
 * Yetim Sponsorluk Yöneticisi - 3 Parametreli Sürüm
 */
class YetimSponsorlukYoneticisi3Param {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Otomatik eşleşme ile sponsor bulma (3 parametreli sürüm)
     */
public function akilliYetimEslestir($bagis_odeme_id, $tutar, $bagis_odeme_verisi = []) {
    try {
        // 1. Yetim Seçimi: 
        // - durum = 1 (Aktif)
        // - hami_durumu = 0 (Boşta)
        // - Öncelik: Önce ai_ihtiyac_skoru (en yüksek), sonra eslestirme_oncelik
        $yetim_query = $this->db->prepare("
            SELECT id, ad_soyad, ai_ihtiyac_skoru 
            FROM yetimler 
            WHERE durum = 1 
              AND hami_durumu = 0 
            ORDER BY ai_ihtiyac_skoru DESC, eslestirme_oncelik ASC, id ASC 
            LIMIT 1
        ");
        $yetim_query->execute();
        $yetim = $yetim_query->fetch(PDO::FETCH_ASSOC);
        
        if (!$yetim) {
            return ['status' => false, 'message' => 'Müsait yetim bulunamadı!'];
        }

        // 2. Veri Hazırlama (bagis_odeme'den gelen veriler)
        $ay_sayisi = isset($bagis_odeme_verisi['sponsorluk_suresi_ay']) ? intval($bagis_odeme_verisi['sponsorluk_suresi_ay']) : 1;
        $odeme_gunu = isset($bagis_odeme_verisi['odeme_gunu']) ? intval($bagis_odeme_verisi['odeme_gunu']) : date('j');
        
        $odeme_tipi_raw = $bagis_odeme_verisi['odeme_tipi'] ?? 'aylik';
        $aylik_odeme_tipi = ($odeme_tipi_raw == 'sürekli' || $odeme_tipi_raw == 'aylik') ? 'aylik' : 'tek_seferde';
        $toplam_tutar = ($aylik_odeme_tipi == 'tek_seferde') ? ($tutar * $ay_sayisi) : $tutar;

        // 3. Hamilikler Tablosuna Kayıt
        $hamilik_ekle = $this->db->prepare("
            INSERT INTO hamilikler SET 
                yetim_id = ?, 
                bagisci_id = ?, 
                baslangic_tarihi = NOW(), 
                bitis_tarihi = ?, 
                tur = 'aylik', 
                tutar = ?, 
                durum = 'aktif', 
                odeme_gunu = ?, 
                aylik_odeme_tipi = ?, 
                secilen_ay_sayisi = ?, 
                toplam_odeme_tutari = ?, 
                sonraki_odeme_tarihi = ?,
                eslestirme_nedeni = 'İhtiyaç skoruna göre otomatik atama', 
                eslestirme_tarihi = NOW()
        ");
        
        $insert = $hamilik_ekle->execute([
            $yetim['id'], 
            $bagis_odeme_id, 
            date('Y-m-d', strtotime("+$ay_sayisi months")),
            $tutar, 
            $odeme_gunu, 
            $aylik_odeme_tipi, 
            $ay_sayisi, 
            $toplam_tutar,
            ($aylik_odeme_tipi == 'aylik' ? date('Y-m-d', strtotime("+1 month")) : null)
        ]);
        
        if ($insert) {
            $last_id = $this->db->lastInsertId();
            
            // 4. Yetim Durumunu Güncelle: hami_durumu = 1 (Artık hamisi var)
            $yetim_guncelle = $this->db->prepare("UPDATE yetimler SET hami_durumu = 1 WHERE id = ?");
            $yetim_guncelle->execute([$yetim['id']]);
            
            return ['status' => true, 'yetim_id' => $yetim['id'], 'hamilik_id' => $last_id];
        }
        
    } catch (PDOException $e) {
        return ['status' => false, 'message' => $e->getMessage()];
    }
}
/**
     * Log kaydet
     */
    private function logKaydet($yetim_id, $hamilik_id, $islem_tipi, $aciklama) {
        try {
            $log_sorgu = $this->db->prepare("
                INSERT INTO yetim_takip_loglari (
                    yetim_id, hamil_id, log_tipi, aciklama, created_at
                ) VALUES (?, ?, ?, ?, NOW())
            ");
            $log_sorgu->execute([$yetim_id, $hamilik_id, $islem_tipi, $aciklama]);
        } catch (PDOException $e) {
            error_log("Log kayıt hatası: " . $e->getMessage());
        }
    }
}
?>
