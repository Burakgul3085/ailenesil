<?php 
if(isset($_GET['sayfa'])){
	$s = $_GET['sayfa'];
	switch($s){
		
	case ''.$htc['anaurl'].'';
	$title 			= baslik;
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['sayfaurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM sayfalar WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/sayfalar/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['haberkategoriurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM haber_kategori WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['kapak'] != "")
	{
		$paylasim		= "".tema."/uploads/haber_kategoriler/kapak/".$TITLESonuc['kapak']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['haberurl'].'';
	$title 			= @$dil['txt152'];	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['haberdetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM haberler WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/haberler/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['projekategoriurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM proje_kategori WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['kapak'] != "")
	{
		$paylasim		= "".tema."/uploads/proje_kategoriler/kapak/".$TITLESonuc['kapak']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['projelerurl'].'';
	$title 			=@$dil['txt193'];	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['projedetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM projeler WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['kapak'] != "")
	{
		$paylasim		= "".tema."/uploads/projeler/".$TITLESonuc['kapak']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['programdetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM programlar WHERE seo = ? AND durum = ? AND dil = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['seo'], 1, $_SESSION['k_dil']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['baslik'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/programlar/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;

	case ''.$htc['programlarurl'].'';
	$title 			= @$dil['txt229'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;

	case ''.$htc['karakterprogramlariurl'].'';
	$title 			= "Karakter ve Sosyal Gelişim Programı";
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;

	case ''.$htc['karakterprogramdetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM karakter_programlari WHERE seo = ? AND durum = ? AND dil = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['seo'], 1, $_SESSION['k_dil']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['baslik'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description 	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim	= "".tema."/uploads/karakter_programlari/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim	= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['hizmeturl'].'';
	$title 			= @$dil['txt155'];	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['hizmetdetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM hizmetler WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/hizmetler/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['fotourl'].'';
	$title 			= @$dil['txt151'];	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	$anasayfa		= "hayır";
	break;
	
	case ''.$htc['fotodetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM foto_galeri WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['kapak'] != "")
	{
		$paylasim		= "".tema."/uploads/fotogaleri/".$TITLESonuc['kapak']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['videourl'].'';
	$title 			= @$dil['txt196'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;

	case ''.$htc['dosyalarurl'].'';
	$title 			= "Dosyalarımız";
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	$anasayfa		= "hayır";
	break;
	
	case ''.$htc['videodetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM video_galeri WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/videogaleri/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['birimurl'].'';
	$title 			= @$dil['txt138'];	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['birimdetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM birimler WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/birimler/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['kararurl'].'';
	$title 			= @$dil['txt102'];	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['karardetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM meclis_kararlari WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/meclis_kararlari/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['faaliyeturl'].'';
	$title 			= @$dil['txt103'];	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['faaliyetdetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM faaliyet_raporlari WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/faaliyet_raporlari/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['profilkategoriurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM profil_kategori WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['profildetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM profiller WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['kapak'] != "")
	{
		$paylasim		= "".tema."/uploads/profiller/".$TITLESonuc['kapak']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['duyuruurl'].'';
	$title 			= @$dil['txt60'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['duyurudetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM duyurular WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/duyurular/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['ihaleurl'].'';
	$title 			= @$dil['txt61'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['ihaledetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM ihaleler WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/ihaleler/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['ilanurl'].'';
	$title 			= @$dil['txt62'];	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['ilandetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM ilanlar WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/ilanlar/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['etkinlikurl'].'';
	$title 			= @$dil['txt148'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['etkinlikdetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM etkinlikler WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/etkinlikler/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['bagisurl'].'';
	$title 			= @$dil['txt111'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['bagissepeturl'].'';
	$title 			= @$dil['txt113'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['bagisodemeurl'].'';
	$title 			= @$dil['txt118'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['bagissonucurl'].'';
	$title 			= @$dil['txt137'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['aidaturl'].'';
	$title 			= @$dil['txt30'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['aidatlisteurl'].'';
	$title 			= @$dil['txt36'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['aidatodemeurl'].'';
	$title 			= @$dil['txt46'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['aidatsonucurl'].'';
	$title 			= @$dil['txt48'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['bagismodulurl'].'';
	$title 			= "Bağış Kampanyaları";	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['bagismoduldetayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM bagis_moduller WHERE seo = ? AND dil = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['id'], $_SESSION['k_dil']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['adi'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['kapak_resmi'] != "")
	{
		$paylasim		= "".tema."/uploads/bagis_moduller/".$TITLESonuc['kapak_resmi']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case 'ara';
	$title 			= @$dil['txt88'];
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['iletisimurl'].'';
	$title 			= @$dil['txt174'];	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case '404';
	$title 			= @$dil['txt19'];	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
				
	case ''.$htc['etkierisimurl'].'';
	$title 			= "Etki ve Erişim";	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['okullarurl'].'';
	$title 			= @$dil['txt250'] ? @$dil['txt250'] : "Okullar";	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['ogrenme_deneyimiurl'].'';
	$title 			= @$dil['txt270'] ? @$dil['txt270'] : "Öğrenme Deneyimleri";	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['ogrenme_deneyimi_detayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM ogrenme_deneyimi WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['seo']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['baslik'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/ogrenme_deneyimi/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	case ''.$htc['destekleme_yollariurl'].'';
	$title 			= @$dil['txt273'] ? @$dil['txt273'] : "Destekleme Yolları";	
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	break;
	
	case ''.$htc['destekleme_yollari_detayurl'].'';
	$TITLESorgu 	= $db->prepare("SELECT * FROM destekleme_yollari WHERE seo = ? ORDER BY id ASC");
	$TITLESorgu->execute(array($_GET['seo']));
	$TITLESonuc 	= $TITLESorgu->fetch(PDO::FETCH_ASSOC);
	$title 			= "".cVCLmHLxbS_ilkbuyuk($TITLESonuc['baslik'])."";
	$keywords 		= "".$TITLESonuc['keywords']."";
	$description	= "".$TITLESonuc['description']."";
	if($TITLESonuc['resim'] != "")
	{
		$paylasim		= "".tema."/uploads/destekleme_yollari/".$TITLESonuc['resim']."";
	}
	else
	{
		$paylasim		= "".tema."/uploads/logo/".logo."";
	}
	break;
	
	default:
	$title 			= baslik;
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
	}
}
else
{
	$title 			= baslik;
	$description 	= site_desc;
	$keywords 		= site_keyw;
	$paylasim		= "".tema."/uploads/logo/".logo."";
}
?>