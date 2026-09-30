<?php 
if(isset($_GET['sayfa']))
{
	$s = $_GET['sayfa'];
	switch($s)
	{			
		case 'anasayfa';
		$title 		= "Yönetim Paneli";
		$anasayfa 	= "active";
		break;	
		
		case 'hizmet-ekle';
		$title 			= @$admindil['txt54'];
		$hizmetekle		= "active";
		$hizmetlershow	= "show";
		break;
		
		case 'hizmet-listele';
		$title 			= @$admindil['txt55'];
		$hizmetlistele	= "active";
		$hizmetlershow	= "show";
		break;
		
		case 'birim-ekle';
		$title 			= @$admindil['txt116'];
		$birimekle		= "active";
		$birimlershow	= "show";
		break;
		
		case 'birim-listele';
		$title 			= @$admindil['txt117'];
		$birimlistele	= "active";
		$birimlershow	= "show";
		break;

		case 'hesap-numaralarimiz';
		$title 			= "Hesap Numaralarımız";
		$hesapnumaralarimiz	= "active";
		$ayarlarshow	= "show";
		break;
		
		case 'genel-ayarlar';
		$title 			= @$admindil['txt3'];
		$genelayarlar 	= "active";
		$ayarlarshow	= "show";
		break;
		
		case 'baskan-ayarlar';
		$title 			= @$admindil['txt114'];
		$baskanayarlar 	= "active";
		$ayarlarshow	= "show";
		break;
		
		case 'popup';
		$title 			= @$admindil['txt99'];
		$popup 			= "active";
		$ayarlarshow	= "show";
		break;
		
		case 'api-ayarlari';
		$title 			= @$admindil['txt4'];
		$apiayarlari 	= "active";
		$ayarlarshow	= "show";
		break;
		
		case 'iletisim-ayarlari';
		$title 				= @$admindil['txt5'];
		$iletisimayarlari 	= "active";
		$ayarlarshow	= "show";
		break;
		
		case 'sosyal-medya-ayarlari';
		$title 				= @$admindil['txt6'];
		$sosyalmedyaayarlari= "active";
		$ayarlarshow		= "show";
		break;
		
		case 'modul-ayarlari';
		$title 				= @$admindil['txt7'];
		$modulayarlari		= "active";
		$ayarlarshow		= "show";
		break;
		
		case 'limit-ayarlari';
		$title 				= @$admindil['txt8'];
		$limitayarlari		= "active";
		$ayarlarshow		= "show";
		break;
		
		case 'site-bakim-modu';
		$title 			= @$admindil['txt9'];
		$sitebakimmodu	= "active";
		$ayarlarshow	= "show";
		break;
		
		case 'mail-ayarlari';
		$title 			= @$admindil['txt10'];
		$mailayarlari	= "active";
		$ayarlarshow	= "show";
		break;
		
		case 'sms-ayarlari';
		$title 			= @$admindil['txt11'];
		$smsayarlari	= "active";
		$ayarlarshow	= "show";
		break;
		
		case 'sanal-poslar';
		$title 			= @$admindil['txt100'];
		$sanalposlar	= "active";
		$ayarlarshow	= "show";
		break;
		
	case 'arkaplan-ayarlari';
	$title 				= @$admindil['txt12'];
	$arkaplanayarlari	= "active";
	$ayarlarshow		= "show";
	break;
	
	case 'anasayfa-alan-siralama';
	$title 				= "Anasayfa Alan Sıralama";
	$anasayfaalansiralama = "active";
	$ayarlarshow		= "show";
	break;
		
		case 'sayfa-ekle';
		$title 			= @$admindil['txt50'];
		$sayfaekle		= "active";
		$sayfalarshow	= "show";
		break;
		
		case 'sayfa-listele';
		$title 			= @$admindil['txt51'];
		$sayfalistele	= "active";
		$sayfalarshow	= "show";
		break;
		
		case 'ilan-ekle';
		$title 			= @$admindil['txt70'];
		$ilanekle		= "active";
		$ilanshow		= "show";
		break;
		
		case 'ilan-listele';
		$title 			= @$admindil['txt71'];
		$ilanlistele	= "active";
		$ilanshow		= "show";
		break;
		
		case 'etkinlik-ekle';
		$title 			= @$admindil['txt74'];
		$etkinlikekle	= "active";
		$etkinlikshow	= "show";
		break;
		
		case 'etkinlik-listele';
		$title 				= @$admindil['txt75'];
		$etkinliklistele	= "active";
		$etkinlikshow		= "show";
		break;
		
		case 'karar-ekle';
		$title 			= @$admindil['txt62'];
		$kararekle		= "active";
		$kararshow		= "show";
		break;
		
		case 'karar-listele';
		$title 			= @$admindil['txt63'];
		$kararlistele	= "active";
		$kararshow		= "show";
		break;
		
		case 'faaliyet-ekle';
		$title 			= @$admindil['txt127'];
		$faaliyetekle	= "active";
		$faaliyetshow	= "show";
		break;
		
		case 'faaliyet-listele';
		$title 			= @$admindil['txt128'];
		$faaliyetlistele= "active";
		$faaliyetshow	= "show";
		break;
		
		case 'ihale-ekle';
		$title 			= @$admindil['txt66'];
		$ihaleekle		= "active";
		$ihaleshow		= "show";
		break;
		
		case 'ihale-listele';
		$title 			= @$admindil['txt67'];
		$ihalelistele	= "active";
		$ihaleshow		= "show";
		break;
		
		case 'haber-ekle';
		$title 		= @$admindil['txt78'];
		$haberekle	= "active";
		$habershow	= "show";
		break;
		
		case 'haber-listele';
		$title 			= @$admindil['txt79'];
		$haberlistele	= "active";
		$habershow		= "show";
		break;
		
		case 'haberler-excel';
		$title 			= @$admindil['txt138'];
		$haberlistele	= "active";
		$habershow		= "show";
		break;
		
		case 'haberler-resim-excel';
		$title 			= @$admindil['txt139'];
		$haberlistele	= "active";
		$habershow		= "show";
		break;
		
		case 'haber-fotograflar';
		$title 			= @$admindil['txt80_1'];
		$haberlistele	= "active";
		$habershow		= "show";
		break;
		
		case 'haber-kategoriler';
		$title 				= @$admindil['txt40'];
		$haberkategorileri	= "active";
		$habershow			= "show";
		break;
		
		case 'haber-kategori-ekle';
		$title 				= @$admindil['txt39'];
		$haberkategorileri	= "active";
		$habershow			= "show";
		break;
		
		case 'slider-ekle';
		$title 		= @$admindil['txt82'];
		$sliderekle	= "active";
		$slidershow	= "show";
		break;
		
		case 'slider-listele';
		$title 			= @$admindil['txt83'];
		$sliderlistele	= "active";
		$slidershow		= "show";
		break;
		
		case 'top-menu';
		$title 			= @$admindil['txt36'];
		$topmenu		= "active";
		$menushow		= "show";
		break;
		
		case 'slider-menu';
		$title 			= @$admindil['txt38'];
		$slidermenu		= "active";
		$menushow		= "show";
		break;
		
		case 'orta-menu';
		$title 			= @$admindil['txt21'];
		$ortamenu		= "active";
		$menushow		= "show";
		break;
		
		case 'kolay-menu';
		$title 			= @$admindil['txt111'];
		$kolaymenu		= "active";
		$menushow		= "show";
		break;
		
		case 'header-menu';
		$title 			= @$admindil['txt18'];
		$headermenu		= "active";
		$menushow		= "show";
		break;
		
		case 'footer-menu';
		$title 			= @$admindil['txt20'];
		$footermenu		= "active";
		$menushow		= "show";
		break;
		
		case 'dil-ekle';
		$title 		= @$admindil['txt14'];
		$dilekle	= "active";
		$dilshow	= "show";
		break;
		
		case 'dil-listele';
		$title 			= @$admindil['txt15'];
		$dillistele		= "active";
		$dilshow		= "show";
		break;
		
		case 'admin-dil-duzenle';
		$title 			= @$admindil['txt15_1'];
		$admindilduzenle= "active";
		$dilshow		= "show";
		break;
		
		case 'yonetici-ekle';
		$title 			= @$admindil['txt94'];
		$yoneticiekle	= "active";
		$yoneticishow	= "show";
		break;
		
		case 'yonetici-listele';
		$title 			= @$admindil['txt95'];
		$yoneticilistele= "active";
		$yoneticishow	= "show";
		break;
		
		case 'mesajlar';
		$title 			= @$admindil['txt98'];
		$mesajlar		= "active";
		break;
		
		case 'program_mesajlar';
		$title 				= "Program Mesajları";
		$program_mesajlar	= "active";
		break;
		
		case 'bildirim-sablonlari';
		$title 				= @$admindil['txt32'];
		$bildirimsablonlari	= "active";
		$rehbershow			= "show";
		break;
		
		case 'sablon-duzenle';
		$title 				= @$admindil['txt33'];
		$bildirimsablonlari	= "active";
		$rehbershow			= "show";
		break;
		
		case 'not-defteri';
		$title 			= @$admindil['txt103'];
		$notdefteri		= "active";
		break;
		
		case 'rehberim';
		$title 			= @$admindil['txt27'];
		$rehberim		= "active";
		$rehbershow		= "show";
		break;
		
		case 'rehber-ekle';
		$title 		= @$admindil['txt29'];
		$rehberekle	= "active";
		$rehbershow	= "show";
		break;
		
		case 'toplu-email';
		$title 		= @$admindil['txt30'];
		$topluemail	= "active";
		$rehbershow	= "show";
		break;
		
		case 'toplu-sms';
		$title 		= @$admindil['txt31'];
		$toplusms	= "active";
		$rehbershow	= "show";
		break;
		
		case 'bagis-kategoriler';
		$title 				= @$admindil['txt131'];
		$bagis_kategori		= "active";
		$bagisshow			= "show";
		break;
		
		case 'bagis-kategori-ekle';
		$title 				= @$admindil['txt130'];
		$bagis_kategori		= "active";
		$bagisshow			= "show";
		break;
		
		case 'bagis-listele';
		$title 				= @$admindil['txt59'];
		$bagislistele		= "active";
		$bagisshow			= "show";
		break;
		
		case 'bagis-ekle';
		$title 				= @$admindil['txt58'];
		$bagisekle			= "active";
		$bagisshow			= "show";
		break;
		
		case 'gelen-bagislar';
		$title 				= @$admindil['txt133'];
		$gelen_bagislar		= "active";
		$bagisshow			= "show";
		break;
		
	case 'hamiler';
		$title 				= 'Hamiler Listesi';
		$hamiler			= "active";
		$bagisshow			= "show";
		break;
		
		
		case 'proje-kategoriler';
		$title 				= @$admindil['txt47'];
		$proje_kategori		= "active";
		$projeshow			= "show";
		break;
		
		case 'proje-kategori-ekle';
		$title 				= @$admindil['txt46'];
		$proje_kategori		= "active";
		$projeshow			= "show";
		break;
		
		case 'projeler';
		$title 				= @$admindil['txt44'];
		$projeler			= "active";
		$projeshow			= "show";
		break;
		
		case 'proje-ekle';
		$title 				= @$admindil['txt43'];
		$projeler			= "active";
		$projeshow			= "show";
		break;

	case 'program_ekle';
	$title 				= @$admindil['txt141'];
	$programekle		= "active";
	$programshow		= "show";
	break;

	case 'program_listele';
	$title 				= @$admindil['txt142'];
	$programlistele		= "active";
	$programshow		= "show";
	break;

	case 'karakter_program_ekle';
	$title 				= "Karakter Programı Ekle / Düzenle";
	$karakterprogramekle	= "active";
	$karakterprogramshow	= "show";
	break;

	case 'karakter_program_listele';
	$title 				= "Karakter Programları Listesi";
	$karakterprogramlistele	= "active";
	$karakterprogramshow	= "show";
	break;

	case 'karakter_program_ayar';
	$title 				= "Karakter Programları - Sayfa Üst Metni";
	$karakterprogramayar	= "active";
	$karakterprogramshow	= "show";
	break;

	case 'ogrenme_deneyimi_listele';
	$title 						= "Öğrenme Deneyimleri";
	$ogrenme_deneyimi_listele	= "active";
	$ogrenme_deneyimi_show		= "show";
	break;
	
	case 'ogrenme_deneyimi_ekle';
	$title 					= "Öğrenme Deneyimi Ekle/Düzenle";
	$ogrenme_deneyimi_ekle	= "active";
	$ogrenme_deneyimi_show	= "show";
	break;
	
	case 'destekleme_yollari_listele';
	$title 						= "Destekleme Yolları";
	$destekleme_yollari_listele	= "active";
	$destekleme_yollari_show	= "show";
	break;
	
	case 'destekleme_yollari_ekle';
	$title 					= "Destekleme Yolu Ekle/Düzenle";
	$destekleme_yollari_ekle	= "active";
	$destekleme_yollari_show	= "show";
	break;
	
	case 'derslik_durum_listele';
	$title 				= 'Derslik Durumları';
	$derslikdurumlistele = "active";
	$derslikdurumekle  = "";
	$programshow		= "show";
	break;
	
	case 'derslik_durum_ekle';
	$title 				= 'Derslik Durumu Ekle';
	$derslikdurumlistele = "";
	$derslikdurumekle  = "active";
	$programshow		= "show";
	break;

		case 'galeri-ekle';
		$title 			= @$admindil['txt86'];
		$galeriekle		= "active";
		$galerishow		= "show";
		break;

		case 'galeri-listele';
		$title 			= @$admindil['txt87'];
		$galerilistele	= "active";
		$galerishow		= "show";
		break;
		
		case 'fotograflar';
		$title 			= @$admindil['txt88_1'];
		$galerilistele	= "active";
		$galerishow		= "show";
		break;
		
		case 'video-ekle';
		$title 			= @$admindil['txt90'];
		$videoekle		= "active";
		$videoshow		= "show";
		break;

		case 'video-listele';
		$title 			= @$admindil['txt91'];
		$videolistele	= "active";
		$videoshow		= "show";
		break;
		
		case 'duyuru-ekle';
		$title 			= @$admindil['txt106'];
		$duyuruekle		= "active";
		$duyurushow		= "show";
		break;

		case 'duyuru-listele';
		$title 			= @$admindil['txt107'];
		$duyurulistele	= "active";
		$duyurushow		= "show";
		break;
		
		case 'profil-kategoriler';
		$title 				= @$admindil['txt124'];
		$profil_kategori	= "active";
		$profilshow			= "show";
		break;
		
		case 'profil-kategori-ekle';
		$title 				= @$admindil['txt125'];
		$profil_kategori	= "active";
		$profilshow			= "show";
		break;
		
		case 'profil-listele';
		$title 				= @$admindil['txt121'];
		$profillistele		= "active";
		$profilshow			= "show";
		break;
		
		case 'profil-ekle';
		$title 				= @$admindil['txt120'];
		$profilekle			= "active";
		$profilshow			= "show";
		break;
		
		case 'aidat-listele';
		$title 				= @$admindil['txt136'];
		$aidatlistele		= "active";
		$aidatshow			= "show";
		break;
		
	case 'aidat-ekle';
	$title 				= @$admindil['txt135'];
	$aidatekle			= "active";
	$aidatshow			= "show";
	break;
	
	case 'bagis-moduller';
	$title 				= 'Bağış Modülleri';
	$bagismoduller		= "active";
	$bagisshow			= "show";
	break;
	
	case 'bagis-modul-ekle';
	$title 				= 'Bağış Modülü Ekle';
	$bagismoduller		= "active";
	$bagisshow			= "show";
	break;
	
	case 'bagis-modul-duzenle';
	$title 				= 'Bağış Modülü Düzenle';
	$bagismoduller		= "active";
	$bagisshow			= "show";
	break;
	
	case 'randevu-hizmet-ekle';
	$title 				= 'Randevu Hizmeti Ekle';
	$randevuhizmetekle	= "active";
	$randevushow		= "show";
	break;
	
	case 'randevu-hizmet-listele';
	$title 				= 'Randevu Hizmetleri';
	$randevuhizmetlistele = "active";
	$randevushow		= "show";
	break;
	
	case 'randevu-listele';
	$title 				= 'Gelen Randevular';
	$randevulistele		= "active";
	$randevushow		= "show";
	break;
	
	case 'randevu-detay';
	$title 				= 'Randevu Detayı';
	$randevulistele		= "active";
	$randevushow		= "show";
	break;
	
	case 'uzman_gorus_ekle';
	$title 				= 'Uzman Görüşü Ekle';
	$uzmangorusekle		= "active";
	$uzmangorusshow		= "show";
	break;
	
	case 'uzman_gorus_listele';
	$title 				= 'Uzman Görüşleri';
	$uzmangoruslistele	= "active";
	$uzmangorusshow		= "show";
	break;
	
	case 'uzman_gorus_duzenle';
	$title 				= 'Uzman Görüşü Düzenle';
	$uzmangoruslistele	= "active";
	$uzmangorusshow		= "show";
	break;

	case 'yetim-ekle';
	$title 		= "Yetim Ekle";
	$yetimekle	= "active";
	$yetimshow	= "show";
	break;

	case 'yetim-duzenle';
	$title 		= "Yetim Düzenle";
	$yetimlistele	= "active";
	$yetimshow	= "show";
	break;
	
	case 'yetim-listele';
	$title 			= "Yetim Listesi";
	$yetimlistele	= "active";
	$yetimshow		= "show";
	break;
	
	case 'dosya-ekle';
	$title		= "Dosya Ekle";
	$dosyaekle	= "active";
	$dosyashow	= "show";
	break;

	case 'dosya-listele';
	$title		= "Dosya Listesi";
	$dosyalistele	= "active";
	$dosyashow	= "show";
	break;

	case '404';
	$title 			= "404 Sayfa Bulunamadı";
	break;
					
		default:
		$title 		= "Yönetim Paneli";
		$anasayfa 	= "active";
	}
}
else
{
	$title 		= "Yönetim Paneli";
	$anasayfa 	= "active";
}
?> 