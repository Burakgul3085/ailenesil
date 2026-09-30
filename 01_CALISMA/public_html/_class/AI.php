<?php

class AI {
    private $db;
    private $apiKey;
    /**
     * URL yapısı güncellendi:
     * v1beta sürümü gemini-1.5-flash için en geniş desteği sunar.
     */
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent';

    public function __construct($db) {
        $this->db = $db;
        $this->apiKey = $this->getApiKey();
    }

    private function getApiKey() {
        try {
            // Veritabanınızdaki 'ayarlar' tablosundan 'gemini_api_key' alanını çeker
            $stmt = $this->db->prepare("SELECT gemini_api_key FROM ayarlar WHERE id = 1");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['gemini_api_key'] ?? null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function generateContent($prompt) {
        if (!$this->apiKey) {
            return ['success' => false, 'message' => 'API Key bulunamadı.'];
        }

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 2048
            ]
        ];

        // API anahtarı URL sonuna güvenli şekilde eklenir
        $ch = curl_init($this->apiUrl . '?key=' . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['success' => false, 'message' => 'Curl Bağlantı Hatası: ' . $error];
        }

        curl_close($ch);
        $result = json_decode($response, true);

        if ($httpCode === 200 && isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return [
                'success' => true, 
                'text' => $result['candidates'][0]['content']['parts'][0]['text']
            ];
        } else {
            // Hatanın nedenini tam olarak görebilmek için API'den gelen mesajı döndürür
            $msg = $result['error']['message'] ?? 'Bilinmeyen API Hatası';
            return ['success' => false, 'message' => "Hata (Kod $httpCode): " . $msg];
        }
    }

    /**
     * Erken Uyarı Sistemi: Bağışçı verilerini analiz eder
     */
    public function erkenUyariAnalizi($riskliBagiscilar) {
        try {
            if (empty($riskliBagiscilar) || !is_array($riskliBagiscilar)) {
                return ['success' => false, 'message' => 'Analiz edilecek veri yok.'];
            }

            $dataStr = "";
            foreach ($riskliBagiscilar as $rb) {
                $bagisciAd = $rb['bagisci_ad'] ?? 'Bilinmiyor';
                $yetimAd = $rb['yetim_ad'] ?? 'Bilinmiyor';
                $sonOdeme = $rb['son_odeme'] ?? 'Hiç yapılmamış';
                if($sonOdeme != 'Hiç yapılmamış' && $sonOdeme) {
                    $sonOdeme = date('d.m.Y', strtotime($sonOdeme));
                }
                $dataStr .= "- Bağışçı: {$bagisciAd}, Yetim: {$yetimAd}, Son Ödeme: {$sonOdeme}\n";
            }

            $prompt = "Sen bir hayır kurumu yönetim sistemisin. Aşağıdaki riskli bağışçı listesini analiz et ve yönetime stratejik öneriler sun.

## Riskli Bağışçı Listesi:
{$dataStr}

## İstenen Format:
Lütfen aşağıdaki formatta, detaylı ve profesyonel bir risk analizi hazırla:

# 🚨 Risk Analizi Raporu

## 📊 Genel Değerlendirme
[Toplam risk durumu ve genel değerlendirme]

## ⚠️ Riskli Bağışçılar

[Her bağışçı için ayrı ayrı:]
**1. [Bağışçı Adı]**
- **Risk Seviyesi:** [Düşük/Orta/Yüksek]
- **Durum:** [Açıklama]
- **Öneri:** [Stratejik öneri]

**2. [Bağışçı Adı]**
- **Risk Seviyesi:** [Düşük/Orta/Yüksek]
- **Durum:** [Açıklama]
- **Öneri:** [Stratejik öneri]

[Diğer bağışçılar için devam...]

## 💡 Yönetim İçin Stratejik Öneriler

**1. [Öneri Başlığı]**
[Açıklama]

**2. [Öneri Başlığı]**
[Açıklama]

**3. [Öneri Başlığı]**
[Açıklama]

**Önemli:** 
- Gerçekçi ve uygulanabilir öneriler sun
- Türkçe karakterleri doğru kullan
- Profesyonel bir dil kullan";

            return $this->generateContent($prompt);
        } catch(Exception $e) {
            error_log("Erken Uyarı Analizi Hatası: " . $e->getMessage());
            return [
                'success' => false, 
                'message' => 'Risk analizi oluşturulurken bir hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Akıllı İhtiyaç Önerisi: Mevsim ve yaşa göre öneri sunar
     */
    public function akilliIhtiyacOnerisi($yetimData, $gecmisBagislar) {
        try {
            $mevsim = $this->getMevsim();
            
            // dogum_tarihi kolonu üzerinden yaş hesaplanır - null kontrolü
            $yas = 'Belirtilmemiş';
            if(!empty($yetimData['dogum_tarihi']) && strtotime($yetimData['dogum_tarihi']) !== false) {
                $yas = date('Y') - date('Y', strtotime($yetimData['dogum_tarihi']));
            }
            
            // Eğitim durumu bilgisi
            $egitimDurumu = $yetimData['egitim_durumu'] ?? 'Belirtilmemiş';
            $egitimDurumuText = [
                'okul_oncesi' => 'Okul Öncesi',
                'ilkokul' => 'İlkokul',
                'ortaokul' => 'Ortaokul',
                'lise' => 'Lise',
                'universite' => 'Üniversite',
                'yok' => 'Eğitim Görmüyor'
            ];
            $egitimText = $egitimDurumuText[$egitimDurumu] ?? $egitimDurumu;
            
            // Ad soyad kontrolü
            $adSoyad = $yetimData['ad_soyad'] ?? 'İsimsiz';
            
            // Cinsiyet kontrolü
            $cinsiyet = isset($yetimData['cinsiyet']) ? ucfirst($yetimData['cinsiyet']) : 'Belirtilmemiş';

            $prompt = "Sen bir hayır kurumu yönetim sistemisin. Aşağıdaki yetim çocuk için mevsimsel ve yaşa uygun ihtiyaç önerileri hazırla.

## Yetim Bilgileri:
- **Ad Soyad:** {$adSoyad}
- **Yaş:** " . ($yas != 'Belirtilmemiş' ? $yas . ' yaşında' : 'Belirtilmemiş') . "
- **Cinsiyet:** {$cinsiyet}
- **Eğitim Durumu:** {$egitimText}
- **Mevsim:** {$mevsim}
- **Geçmiş Bağışlar:** " . (!empty($gecmisBagislar) && is_array($gecmisBagislar) ? implode(', ', $gecmisBagislar) : 'Henüz bağış yapılmamış') . "

## İstenen Format:
Lütfen aşağıdaki formatta, detaylı ve profesyonel bir ihtiyaç önerisi hazırla:

### 🎯 Öncelikli İhtiyaçlar

**1. [Ürün/İhtiyaç Adı]**
- **Neden Gerekli:** [Açıklama]
- **Özellikler:** [Yaş, cinsiyet, mevsim ve eğitim durumuna göre özellikler]
- **Tahmini Maliyet:** [Uygun fiyat aralığı]

**2. [Ürün/İhtiyaç Adı]**
- **Neden Gerekli:** [Açıklama]
- **Özellikler:** [Yaş, cinsiyet, mevsim ve eğitim durumuna göre özellikler]
- **Tahmini Maliyet:** [Uygun fiyat aralığı]

**3. [Ürün/İhtiyaç Adı]**
- **Neden Gerekli:** [Açıklama]
- **Özellikler:** [Yaş, cinsiyet, mevsim ve eğitim durumuna göre özellikler]
- **Tahmini Maliyet:** [Uygun fiyat aralığı]

### 📝 Genel Değerlendirme
[Bu çocuğun genel durumu ve ihtiyaçları hakkında kısa bir değerlendirme]

### 💡 Ek Öneriler
[Varsa ek öneriler ve notlar]

**Önemli:** Lütfen sadece gerçekçi, uygulanabilir ve çocuğun yaşına, cinsiyetine, eğitim durumuna ve mevsimine uygun öneriler sun. Türkçe karakterleri doğru kullan.";

            return $this->generateContent($prompt);
        } catch(Exception $e) {
            error_log("AI İhtiyaç Önerisi Hatası: " . $e->getMessage());
            return [
                'success' => false, 
                'message' => 'İhtiyaç önerisi oluşturulurken bir hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Otomatik Rapor Oluşturma: Bağışçıya gönderilecek gelişim raporu
     */
    public function otomatikRaporOlustur($yetimData, $egitimBilgisi, $saglikBilgisi) {
        // Yaş hesaplama
        $yas = date('Y') - date('Y', strtotime($yetimData['dogum_tarihi']));
        
        // Eğitim durumu çevirisi
        $egitimDurumuText = [
            'okul_oncesi' => 'Okul Öncesi Eğitim',
            'ilkokul' => 'İlkokul',
            'ortaokul' => 'Ortaokul',
            'lise' => 'Lise',
            'universite' => 'Üniversite',
            'yok' => 'Eğitim Görmüyor'
        ];
        $egitimText = $egitimDurumuText[$egitimBilgisi] ?? $egitimBilgisi;
        
        // Cinsiyet çevirisi
        $cinsiyetText = $yetimData['cinsiyet'] == 'erkek' ? 'Erkek' : 'Kız';
        
        // Sistemde geçen süre
        $kayitTarihi = isset($yetimData['created_at']) ? date('d.m.Y', strtotime($yetimData['created_at'])) : 'Belirtilmemiş';
        $sistemdeGecenSure = isset($yetimData['created_at']) ? floor((time() - strtotime($yetimData['created_at'])) / (30 * 24 * 60 * 60)) : 0;

        $prompt = "Sen bir hayır kurumu yönetim sistemisin. Aşağıdaki yetim çocuk için bağışçıya gönderilecek profesyonel, duygusal ve bilgilendirici bir gelişim raporu hazırla.

## Yetim Bilgileri:
- **Ad Soyad:** {$yetimData['ad_soyad']}
- **Yaş:** {$yas} yaşında
- **Cinsiyet:** {$cinsiyetText}
- **Eğitim Durumu:** {$egitimText}
- **Sağlık Durumu:** {$saglikBilgisi}
- **Kayıt Tarihi:** {$kayitTarihi}
- **Sistemde Geçen Süre:** {$sistemdeGecenSure} ay

## İstenen Format:
Lütfen aşağıdaki formatta, samimi ve profesyonel bir rapor hazırla:

# 📊 {$yetimData['ad_soyad']} - Gelişim Raporu

## 👤 Genel Bilgiler
[Çocuğun genel durumu, yaşı, cinsiyeti ve sistemdeki süresi hakkında bilgi]

## 📚 Eğitim Durumu
[Eğitim durumu, başarıları, gelişim alanları hakkında detaylı bilgi]

## 🏥 Sağlık Durumu
[Sağlık durumu ve genel fiziksel gelişim hakkında bilgi]

## 💝 Destek Süreci
[Bağışçının desteği sayesinde çocuğun hayatında yapılan olumlu değişiklikler]

## 🎯 Gelecek Hedefleri
[Çocuğun geleceği için planlar ve hedefler]

## 🙏 Teşekkür
[Bağışçıya samimi bir teşekkür mesajı]

**Önemli:** 
- Rapor samimi, duygusal ve umut verici olmalı
- Gerçekçi ve profesyonel bir dil kullan
- Çocuğun gizliliğini koru, gereksiz kişisel detay verme
- Türkçe karakterleri doğru kullan
- Rapor 300-500 kelime arasında olsun
- Markdown formatında başlıklar, listeler ve vurgular kullan";

        return $this->generateContent($prompt);
    }

    private function getMevsim() {
        $month = date('n');
        if ($month >= 3 && $month <= 5) return 'İlkbahar';
        if ($month >= 6 && $month <= 8) return 'Yaz';
        if ($month >= 9 && $month <= 11) return 'Sonbahar';
        return 'Kış';
    }
}