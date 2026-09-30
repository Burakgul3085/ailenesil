<?php

class Bildirim {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Yetim hamiline bildirim gönderir
     * 
     * @param int $yetimId
     * @param string $tip (egitim, saglik, yardim)
     * @param string $detay
     * @return array
     */
    public function yetimBildirimGonder($yetimId, $tip, $detay = '') {
        // 1. Yetim ve Hami bilgilerini çek
        // Hamilikler tablosundan aktif hamiyi bul, bagiscilar tablosundan iletişim bilgilerini al
        $sorgu = $this->db->prepare("
            SELECT y.ad_soyad as yetim_ad, b.ad_soyad as hami_ad, b.telefon as hami_telefon, b.email as hami_email, b.id as hami_id
            FROM yetimler y
            JOIN hamilikler h ON y.id = h.yetim_id
            JOIN bagiscilar b ON h.bagisci_id = b.id
            WHERE y.id = ? AND h.durum = 'aktif'
        ");
        $sorgu->execute([$yetimId]);
        $hamiler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
        
        if(count($hamiler) == 0) {
            return ['durum' => false, 'mesaj' => 'Bu yetime ait aktif bir hami bulunamadı.'];
        }
        
        $gonderimSayisi = 0;
        
        foreach($hamiler as $bilgi) {
            $aliciBilgi = [
                'yetim_id' => $yetimId,
                'hami_id' => $bilgi['hami_id'],
                'telefon' => $bilgi['hami_telefon'],
                'email' => $bilgi['hami_email'],
                'hami_ad' => $bilgi['hami_ad'],
                'yetim_ad' => $bilgi['yetim_ad']
            ];
            
            // Şablondaki ekstra değişkenler
            $degiskenler = [
                'HAMI_AD' => $bilgi['hami_ad'],
                'YETIM_AD' => $bilgi['yetim_ad'],
                'DETAY' => $detay,
                'TARIH' => date('d.m.Y')
            ];
            
            $sonuc = $this->gonder($aliciBilgi, $tip, $degiskenler);
            if($sonuc['sms'] || $sonuc['email']) {
                $gonderimSayisi++;
            }
        }
        
        if($gonderimSayisi > 0) {
            return ['durum' => true, 'mesaj' => $gonderimSayisi . ' hamiye bildirim gönderildi.'];
        } else {
            return ['durum' => false, 'mesaj' => 'Bildirim gönderilemedi (Şablon veya iletişim bilgisi eksik olabilir).'];
        }
    }
    
    /**
     * Bildirim gönder (Merkezi şablon sistemine bağlandı)
     */
    public function gonder($aliciBilgi, $sablonTip, $degiskenler = []) {
        $sonuc = ['sms' => true, 'email' => true];
        
        // Şablon tipini ID'ye eşle
        $sablonId = 0;
        switch($sablonTip) {
            case 'odeme_gecikmesi': $sablonId = 12; break;
            case 'egitim':           $sablonId = 13; break;
            case 'saglik':           $sablonId = 14; break;
            case 'yardim':           $sablonId = 15; break;
            default:                 $sablonId = 1;  break; // Varsayılan genel şablon
        }

        // Verileri hazırla (tag'leri temizleyerek gönderiyoruz)
        $veriler = [];
        foreach($degiskenler as $k => $v) {
            $veriler[str_replace(['{','}'], '', $k)] = $v;
        }
        
        // Hami/Yetim bilgilerini de ekle
        $veriler['adsoyad'] = $aliciBilgi['hami_ad'];
        $veriler['yetim_ad'] = $aliciBilgi['yetim_ad'];

        // Merkezi fonksiyonu çağır
        $gonderim = bildirim_sablon_gonder($sablonId, $veriler, $aliciBilgi['email'], $aliciBilgi['telefon'] ?? '');
        
        if($gonderim) {
            $this->logla($aliciBilgi['yetim_id'], $aliciBilgi['hami_id'], $sablonTip, 'merkezi_sistem', 'Bildirim şablon üzerinden gönderildi (ID: '.$sablonId.')');
        }

        return $sonuc;
    }
    
    private function logla($yetimId, $hamiId, $tip, $kanal, $mesaj) {
        $stmt = $this->db->prepare("INSERT INTO yetim_bildirim_log SET yetim_id = ?, hami_id = ?, bildirim_tipi = ?, kanal = ?, mesaj = ?");
        $stmt->execute([$yetimId, $hamiId, $tip, $kanal, $mesaj]);
    }
}
?>