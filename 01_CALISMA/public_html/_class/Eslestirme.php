<?php
class Eslestirme {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Yıllık Hamilik İçin Otomatik Eşleştirme
     * Boşta olan yetimlerden birini seçer ve eşleştirir.
     */
    public function yillikHamilikEslestir($bagisciId, $tutar) {
        // 1. Boşta olan bir yetim bul (Random veya öncelik sırasına göre)
        // Öncelik: En uzun süredir bekleyen (id'si küçük olan)
        $stmt = $this->db->prepare("SELECT id FROM yetimler WHERE hami_durumu = 0 AND durum = 1 ORDER BY id ASC LIMIT 1");
        $stmt->execute();
        $yetim = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($yetim) {
            // Eşleştirme yap
            $yetimId = $yetim['id'];
            
            // Hamilik kaydı oluştur
            $insertStmt = $this->db->prepare("INSERT INTO hamilikler (yetim_id, bagisci_id, tur, tutar, baslangic_tarihi, bitis_tarihi, durum) VALUES (?, ?, 'yillik', ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 'aktif')");
            $insertStmt->execute([$yetimId, $bagisciId, $tutar]);
            
            // Yetim durumunu güncelle
            $updateStmt = $this->db->prepare("UPDATE yetimler SET hami_durumu = 1 WHERE id = ?");
            $updateStmt->execute([$yetimId]);

            return ['status' => true, 'yetim_id' => $yetimId, 'message' => 'Yıllık hamilik eşleşmesi başarıyla yapıldı.'];
        } else {
            return ['status' => false, 'message' => 'Eşleştirilecek uygun yetim bulunamadı.'];
        }
    }

    /**
     * Giyim Yardımı Önerisi
     * Bağışçının beden tercihlerine veya rastgele ihtiyacı olan yetimlere göre öneri sunar.
     */
    public function giyimYardimiOner($bagisciId = null, $bedenBilgileri = []) {
        $sql = "SELECT y.*, yi.tur as ihtiyac_turu FROM yetimler y 
                JOIN yetim_ihtiyaclar yi ON y.id = yi.yetim_id 
                WHERE yi.tur = 'giyim' AND yi.durum = 0 AND y.durum = 1";

        $params = [];

        // Eğer beden filtreleri varsa ekle
        if (!empty($bedenBilgileri)) {
            // Örnek: $bedenBilgileri = ['mont_beden' => 'M', 'ayakkabi' => '38']
            // Bu kısım detaylandırılabilir.
        }

        $sql .= " ORDER BY RAND() LIMIT 5"; // 5 öneri getir

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Davranış Tabanlı Öneri
     * Bağışçının geçmiş bağışlarına bakarak (Eğitim, Sağlık vs.) yetim önerir.
     */
    public function davranisTabanliOneri($bagisciId) {
        // 1. Bağışçının geçmiş tercihlerini analiz et (Varsayımsal bagislar tablosundan)
        // Bu örnekte basitçe kullanıcının ilgi alanlarına bakıyoruz (bagiscilar tablosu)
        
        $stmt = $this->db->prepare("SELECT ilgi_alanlari FROM bagiscilar WHERE id = ?");
        $stmt->execute([$bagisciId]);
        $bagisci = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $ilgiAlani = 'genel';
        if ($bagisci && !empty($bagisci['ilgi_alanlari'])) {
            // JSON decode veya explode
            // Basitlik adına ilk ilgi alanını alalım
            $ilgiAlani = $bagisci['ilgi_alanlari']; // "egitim" varsayalım
        }

        // İlgi alanına uygun ihtiyacı olan yetimleri bul
        $sql = "SELECT y.*, yi.aciklama as ihtiyac_aciklama FROM yetimler y 
                JOIN yetim_ihtiyaclar yi ON y.id = yi.yetim_id 
                WHERE yi.tur LIKE ? AND yi.durum = 0 AND y.durum = 1 
                LIMIT 3";
        
        $stmt2 = $this->db->prepare($sql);
        $stmt2->execute(["%$ilgiAlani%"]);
        return $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * AI Destekli Akıllı Yetim Eşleştirme
     * Google Gemini AI kullanarak en uygun yetimi bulur ve eşleştirir.
     */
    public function akilliYetimEslestir($bagisciId, $tutar, $bagisTipi = 'aylik') {
        require_once __DIR__ . '/AI.php';
        
        try {
            // 1. Sponsoru olmayan yetimleri getir
            $stmt = $this->db->prepare("
                SELECT y.*, 
                       TIMESTAMPDIFF(YEAR, y.dogum_tarihi, CURDATE()) as yas,
                       DATEDIFF(CURDATE(), y.created_at) as bekleme_suresi,
                       (SELECT COUNT(*) FROM yetim_ihtiyaclar yi WHERE yi.yetim_id = y.id AND yi.durum = 0) as ihtiyac_sayisi
                FROM yetimler y 
                WHERE y.hami_durumu = 0 AND y.durum = 1 
                ORDER BY y.created_at ASC
            ");
            $stmt->execute();
            $yetimler = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($yetimler)) {
                return ['status' => false, 'message' => 'Eşleştirilecek uygun yetim bulunamadı.'];
            }

            // 2. AI analizi için veri hazırla
            $yetimData = [];
            foreach ($yetimler as $yetim) {
                $yetimData[] = [
                    'id' => $yetim['id'],
                    'ad_soyad' => $yetim['ad_soyad'],
                    'yas' => $yetim['yas'],
                    'cinsiyet' => $yetim['cinsiyet'],
                    'egitim_durumu' => $yetim['egitim_durumu'],
                    'bekleme_suresi' => $yetim['bekleme_suresi'],
                    'ihtiyac_sayisi' => $yetim['ihtiyac_sayisi'],
                    'beden_bilgisi' => $yetim['beden_bilgisi']
                ];
            }

            // 3. Bağışçı bilgilerini al
            $bagisciStmt = $this->db->prepare("SELECT ad_soyad, email FROM bagiscilar WHERE id = ?");
            $bagisciStmt->execute([$bagisciId]);
            $bagisci = $bagisciStmt->fetch(PDO::FETCH_ASSOC);

            // 4. AI prompt'u oluştur
            $prompt = $this->createMatchingPrompt($yetimData, $bagisci, $tutar, $bagisTipi);

            // 5. AI analizini yap
            $ai = new AI($this->db);
            $aiResponse = $ai->generateContent($prompt);

            if (!$aiResponse['success']) {
                // AI başarısız olursa basit FIFO eşleştirme yap
                return $this->basitFIFOEslestir($yetimler[0]['id'], $bagisciId, $tutar, $bagisTipi);
            }

            // 6. AI yanıtını parse et
            $secilenYetim = $this->parseAIResponse($aiResponse['response'], $yetimler);

            if (!$secilenYetim) {
                return $this->basitFIFOEslestir($yetimler[0]['id'], $bagisciId, $tutar, $bagisTipi);
            }

            // 7. Eşleştirmeyi yap
            return $this->eslestirmeKaydet($secilenYetim['id'], $bagisciId, $tutar, $bagisTipi, $secilenYetim['neden']);

        } catch (Exception $e) {
            error_log("AI Eşleştirme Hatası: " . $e->getMessage());
            // Hata durumunda basit eşleştirme yap
            if (!empty($yetimler)) {
                return $this->basitFIFOEslestir($yetimler[0]['id'], $bagisciId, $tutar, $bagisTipi);
            }
            return ['status' => false, 'message' => 'Eşleştirme sırasında hata oluştu.'];
        }
    }

    /**
     * AI için eşleştirme prompt'u oluşturur
     */
    private function createMatchingPrompt($yetimler, $bagisci, $tutar, $bagisTipi) {
        $prompt = "Sen bir yetim sponsorluk sistemi için AI asistanısın. Bağışçı için en uygun yetimi seçmelisin.\n\n";
        $prompt .= "Bağışçı Bilgileri:\n";
        $prompt .= "- Ad: " . ($bagisci['ad_soyad'] ?? 'Belirtilmemiş') . "\n";
        $prompt .= "- Aylık Tutar: {$tutar} TL\n";
        $prompt .= "- Sponsorluk Tipi: {$bagisTipi}\n\n";
        
        $prompt .= "Mevcut Yetimler:\n";
        foreach ($yetimler as $yetim) {
            $prompt .= "- ID: {$yetim['id']}, Ad: {$yetim['ad_soyad']}, Yaş: {$yetim['yas']}, ";
            $prompt .= "Cinsiyet: {$yetim['cinsiyet']}, Eğitim: {$yetim['egitim_durumu']}, ";
            $prompt .= "Bekleme Süresi: {$yetim['bekleme_suresi']} gün, İhtiyaç Sayısı: {$yetim['ihtiyac_sayisi']}\n";
        }

        $prompt .= "\nKriterler:\n";
        $prompt .= "1. En uzun süredir bekleyen yetim öncelikli\n";
        $prompt .= "2. İhtiyaç sayısı fazla olanlar öncelikli\n";
        $prompt .= "3. Yaş küçük olanlar öncelikli (eğitim desteği için)\n\n";

        $prompt .= "Lütfen en uygun yetimin ID'sini ve seçme nedenini belirtin. Format:\n";
        $prompt .= "SecilenID: [yetim_id]\nNeden: [kisa_aciklama]\n";

        return $prompt;
    }

    /**
     * AI yanıtını parse eder
     */
    private function parseAIResponse($aiResponse, $yetimler) {
        // Basit regex parse
        if (preg_match('/SecilenID:\s*(\d+)/', $aiResponse, $matches)) {
            $secilenId = $matches[1];
            
            foreach ($yetimler as $yetim) {
                if ($yetim['id'] == $secilenId) {
                    $neden = 'AI seçimi';
                    if (preg_match('/Neden:\s*(.+)/', $aiResponse, $nedenMatch)) {
                        $neden = trim($nedenMatch[1]);
                    }
                    return ['id' => $yetim['id'], 'neden' => $neden];
                }
            }
        }
        
        return null;
    }

    /**
     * Basit FIFO eşleştirme (Fallback)
     */
    private function basitFIFOEslestir($yetimId, $bagisciId, $tutar, $bagisTipi) {
        return $this->eslestirmeKaydet($yetimId, $bagisciId, $tutar, $bagisTipi, 'Otomatik eşleşme (FIFO)');
    }

    /**
     * Eşleştirme kaydını veritabanına işler
     */
    private function eslestirmeKaydet($yetimId, $bagisciId, $tutar, $bagisTipi, $eslestirmeNedeni) {
        try {
            // Hamilik kaydı oluştur
            $bitisTarihi = ($bagisTipi == 'yillik') ? "DATE_ADD(NOW(), INTERVAL 1 YEAR)" : "DATE_ADD(NOW(), INTERVAL 12 MONTH)";
            
            $insertStmt = $this->db->prepare("
                INSERT INTO hamilikler 
                (yetim_id, bagisci_id, tur, tutar, baslangic_tarihi, bitis_tarihi, durum, eslestirme_nedeni, eslestirme_tarihi) 
                VALUES (?, ?, ?, ?, NOW(), {$bitisTarihi}, 'aktif', ?, NOW())
            ");
            $insertStmt->execute([$yetimId, $bagisciId, $bagisTipi, $tutar, $eslestirmeNedeni]);
            $hamilikId = $this->db->lastInsertId();

            // Yetim durumunu güncelle
            $updateStmt = $this->db->prepare("UPDATE yetimler SET hami_durumu = 1 WHERE id = ?");
            $updateStmt->execute([$yetimId]);

            // Eşleştirme logu kaydet
            $logStmt = $this->db->prepare("
                INSERT INTO yetim_eslestirme_loglari 
                (yetim_id, bagisci_id, hamilik_id, eslestirme_tipi, eslestirme_nedeni, ai_kullanildi) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $logStmt->execute([
                $yetimId, 
                $bagisciId, 
                $hamilikId, 
                $bagisTipi, 
                $eslestirmeNedeni, 
                strpos($eslestirmeNedeni, 'AI') !== false ? 1 : 0
            ]);

            return [
                'status' => true, 
                'yetim_id' => $yetimId, 
                'hamilik_id' => $hamilikId,
                'eslestirme_nedeni' => $eslestirmeNedeni,
                'message' => 'Başarıyla eşleşme yapıldı: ' . $eslestirmeNedeni
            ];

        } catch (Exception $e) {
            error_log("Eşleştirme Kayıt Hatası: " . $e->getMessage());
            return ['status' => false, 'message' => 'Eşleştirme kaydedilemedi.'];
        }
    }

    /**
     * Aylık Bağış Kontrol (Cron için)
     * Ödemesi gecikenleri 'askida' veya 'pasif' yapar.
     */
    public function aylikBagisKontrol() {
        // Son ödeme tarihi üzerinden 30 gün geçmiş ve hala ödenmemiş olanları bul
        // Bu fonksiyon bagis_odeme tablosu ile hamilikler tablosunu join ederek çalışmalı
        // Şimdilik iskelet olarak bırakıyorum.
        return true;
    }
}
?>