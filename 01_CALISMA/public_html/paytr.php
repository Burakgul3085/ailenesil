<?php
require_once('_class/baglan.php');
require_once('_class/fonksiyon.php');
require_once('_class/class.upload.php');

$post=$_POST;
$merchant_id	= magaza_no;// Mağaza numarası
$merchant_key	= magaza_parola; // Mağaza Parolası - Mağaza paneline giriş yaparak BİLGİ sayfasından alabilirsiniz.
$merchant_salt	= magaza_anahtar; // Mağaza Gizli Anahtarı - Mağaza paneline giriş yaparak BİLGİ sayfasından alabilirsiniz.


//oluşturulan hash'i, paytr'dan gelen post içindeki hash ile karşılaştır (isteğin paytr'dan geldiğine ve değişmediğine emin olmak için)
//if($hash!=$post[hash]) die('PAYTR notification failed: bad hash');

if( $post['status'] == 'success' ) 
{ 
	$siparis = $post['merchant_oid'];
	if($siparis)
	{
		// Bağış ödemesini güncelle
		$PayGuncelle = $db->prepare("UPDATE bagis_odeme SET 
			paytronay = '1',
			durum = 'onaylandi',
			odeme_tarihi = NOW()
		WHERE spno = ?");
		$PayGuncelle->execute(array($siparis));
		
		// Bağış bilgilerini al
		$bagis_sorgu = $db->prepare("SELECT * FROM bagis_odeme WHERE spno = ?");
		$bagis_sorgu->execute(array($siparis));
		$bagis = $bagis_sorgu->fetch(PDO::FETCH_ASSOC);
		
		// ✅ BAĞIŞ BİLDİRİMLERİNİ GÖNDER
		if($bagis && $bagis['id']) {
			// Bildirim ayarlarını kontrol et
			$bildirim_ayar = $db->query("SELECT * FROM bildirim_ayarlari WHERE durum = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
			
			$bildirim_tipleri = [];
			
			// SMS aktif mi?
			if($bildirim_ayar && $bildirim_ayar['sms_aktif'] == 1) {
				$bildirim_tipleri[] = 'sms';
			}
			
			// E-posta aktif mi?
			if($bildirim_ayar && $bildirim_ayar['email_aktif'] == 1) {
				$bildirim_tipleri[] = 'email';
			}
			
			// WhatsApp aktif mi?
			if($bildirim_ayar && $bildirim_ayar['whatsapp_aktif'] == 1) {
				$bildirim_tipleri[] = 'whatsapp';
			}
			
			// Bildirimleri gönder
			if(!empty($bildirim_tipleri)) {
				bagis_bildirim_gonder($bagis['id'], $bildirim_tipleri);
			}
		}
		
		// Aidat ödemelerini güncelle
		$Row = $db->query("SELECT * FROM aidat_odemeler WHERE spno = '{$siparis}'")->fetch(PDO::FETCH_ASSOC);
		$aidatbul = $db->query("SELECT * FROM aidatlar WHERE id = '{$Row['aid']}'")->fetch(PDO::FETCH_ASSOC);
		$AIGuncelle = $db->prepare("UPDATE aidat_odemeler SET paytronay = '1' WHERE spno = ?");
		$AIGuncelle->execute(array($siparis));
		if($AIGuncelle->rowCount())
		{
			$Guncelle = $db->prepare("UPDATE aidatlar SET odeme = '1', oucret = oucret+'{$Row['ucret']}' WHERE id = ?");
			$Guncelle->execute(array($aidatbul['id']));
		}
	}
	
}
else
{
	//ödeme başarısız    
	//$post[failed_reason_code] - başarısız hata kodu
	//$post[failed_reason_msg] - başarısız hata mesajı
}
echo "OK";	
?>