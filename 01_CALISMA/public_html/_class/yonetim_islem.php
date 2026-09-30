<?php
ob_start();
session_start();
require_once "baglan.php";
require_once "fonksiyon.php";
require_once('class.upload.php');
require_once("class.phpmailer.php");
$logo	= url.tema.'/uploads/logo/footer/'.footerlogo;
$domain_bilgi	= url;
$bildirimkt 		= strtotime(cVCLmHLxbS_tr_tarih('Y-m-d'));
$bildirimt 		= strtotime(cVCLmHLxbS_tr_tarih('Y-m-d H:i:s'));


cemetery_f();

##GET POST VARMI ##
if(empty($_POST) && empty($_GET))
{
	header("Location:../".yonetim."/index.html");
	exit();
}

##Şifre Sıfırla ##
if(isset($_POST['sifirla']))
{
	$email = $_POST['email'];
	$varmi = $db->prepare("SELECT * FROM kullanici WHERE email = ?");
	$varmi->execute(array($email));
	if($varmi->rowCount())
	{
		$YSonuc = $varmi->fetch(PDO::FETCH_ASSOC);
		if($YSonuc['rutbe'] == 0)
		{			
			$isim 		= $YSonuc['isim'];
			$kullanici 	= $YSonuc['kadi'];
			$parola 		= $YSonuc['sifre'];
			$tarih		= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
			$ip			= cVCLmHLxbS_ip();
			
			bildirim_sablon_gonder(10, [
				'adsoyad' => $isim,
				'kadi' => $kullanici,
				'parola' => $parola,
				'panel_url' => $panel_url
			], $YSonuc['email']);
			$_SESSION['sifirla'] = 'yes';
			header("Location:../".yonetim."/giris.html");	
			exit();
		}
		else
		{
			$_SESSION['demohesap'] = 'no';
			header("Location:../".yonetim."/giris.html");
			exit();
		}			
	}
	else
	{
		$_SESSION['sifirla'] = 'no';
		header("Location:../".yonetim."/giris.html");
		exit();
	}	
}

##Giriş Yap ##
if (isset($_POST['kullanici_giris'])) 
{
	$kadi 		= $_POST['kadi'];
	$sifre 		= $_POST['sifre'];
	$son_giris	= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');

	if(empty($kadi) || empty($sifre))
	{
		$_SESSION['kullanici_giris'] = 'bos';
		header("Location:../".yonetim."/giris.html");
		exit();
	}
	else
	{
		$varmi = $db->prepare("SELECT * FROM kullanici WHERE BINARY kadi = ? AND sifre = ?");
		$varmi->execute(array($kadi,$sifre));
		if($varmi->rowCount())
		{
			$KSonuc = $varmi->fetch(PDO::FETCH_ASSOC);
			$sorgu = $db->prepare("UPDATE kullanici SET
				son_giris = ?
				WHERE id = ?");
			$guncelle = $sorgu->execute(array(
				$son_giris,
				$KSonuc['id']
			));
			$_SESSION['Yonetim_Id'] 		= $KSonuc['id'];
			$_SESSION['Yonetim_Kadi'] 	= $KSonuc['kadi'];
			$_SESSION['Yonetim_Adi'] 	= $KSonuc['isim'];
			$_SESSION['Yonetim_Sifre']	= $KSonuc['sifre'];
			$_SESSION['rutbe']       		= $KSonuc['rutbe'];

			if(isset($_POST['beni_hatirla']))
			{
				setcookie("Yonetim_Kadi",$KSonuc['kadi'],strtotime("+1 day"),"/", null, null, true);
				setcookie("Yonetim_Sifre",$KSonuc['sifre'],strtotime("+1 day"),"/", null, null, true);
			}
			else
			{
				setcookie("Yonetim_Kadi",$KSonuc['kadi'],strtotime("-1 day"),"/", null, null, true);
				setcookie("Yonetim_Sifre",$KSonuc['sifre'],strtotime("-1 day"),"/", null, null, true);
			}
			
			$_SESSION['kullanici_giris'] = 'yes';
			header("Location:../".yonetim."/index.html");
			exit();
		}
		else
		{
			$_SESSION['kullanici_giris'] = 'no';
			header("Location:../".yonetim."/giris.html");
			exit();
		}
	}
}

##Footer Menü Kaydet ##
if(isset($_POST['footermenu_kaydet']))
{
	cVCLmHLxbS_panelislemkontrol("footermenu_kaydet");
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= $_POST['menu_sira'];
		$menu_ust 		= $_POST['menu_ust'];
		$menu_isim 		= $_POST['menu_isim'];
		$menu_url 		= $_POST['menu_url'];
		$link	 		= $_POST['link'];
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		$menu_durum 	= $_POST['menu_durum'];
		
		$sorgu = $db->prepare("INSERT INTO footermenu SET
			menu_sira 	= ?,
			menu_ust 	= ?,
			menu_isim 	= ?,
			menu_url 	= ?,
			link 		= ?,
			sekme 		= ?,
			dil 		= ?,
			menu_durum 	= ?");
		$Ekle = $sorgu->execute(array(
			$menu_sira,
			$menu_ust,
			$menu_isim,
			$menu_url,
			$link,
			$sekme,
			$_SESSION['admin_dil'],
			$menu_durum
		));

		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Footer Menü",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_isim."</strong> adında siteye footer menü ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['footermenu_kaydet'] = 'yes';
			header("Location:../".yonetim."/footer-menu.html");
			exit();
		}
		else
		{
			$_SESSION['footermenu_kaydet'] = 'no';
			header("Location:../".yonetim."/footer-menu.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/footer-menu.html");
		exit();
	}
}

##Footer Menü Güncelle ##
if(isset($_POST['footermenu_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("footermenu_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= $_POST['menu_sira'];
		$menu_ust 		= $_POST['menu_ust'];
		$menu_isim 		= $_POST['menu_isim'];
		$menu_url 		= $_POST['menu_url'];		
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		if($_POST['menu_url'] != "0")
		{
			$link = " ";
		}
		if($_POST['menu_url'] == "0")
		{
			$link	= $_POST['link'];
		}
		$menu_durum 	= $_POST['menu_durum'];
		
		
		$sorgu = $db->prepare("UPDATE footermenu SET
				menu_sira 	= ?,
				menu_ust 	= ?,
				menu_isim 	= ?,
				menu_url 	= ?,
				link 		= ?,
				sekme 		= ?,
				menu_durum 	= ?
				WHERE id = ?");
		$guncelle = $sorgu->execute(array(
				$menu_sira,
				$menu_ust,
				$menu_isim,
				$menu_url,
				$link,
				$sekme,
				$menu_durum,
				$d_id
			));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Footer Menü Güncellendi",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_isim."</strong> başlıklı footer menüyü güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['footermenu_guncelle'] = 'yes';		
			header("Location:../".yonetim."/footer-menu-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['footermenu_guncelle'] = 'no';
			header("Location:../".yonetim."/footer-menu-duzenle/".$d_id.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/footer-menu-duzenle/".$d_id.".html");
		exit();
	}
}

##Footer Menü Sil##
if(@$_GET['footermenu_sil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("footermenu_sil");
	$id = $_GET['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$Orta_menu_bul	= $db->query("SELECT * FROM footermenu WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		$Orta_menu_sorgu 	= $db->prepare("DELETE FROM footermenu WHERE id = :id");
		$Orta_menu_sil 	= $Orta_menu_sorgu->execute(array('id' => $id));
		if($Orta_menu_sorgu->rowCount())
		{
			if($Orta_menu_sil)
			{
				$TopluSorgu = $db->prepare("SELECT * FROM footermenu WHERE menu_ust = ?");
				$TopluSorgu->execute(array($_GET['id']));
				$Topluislem = $TopluSorgu->fetchALL(PDO::FETCH_ASSOC);
				foreach ( $Topluislem as $TopluSonuc )
				{
					$last_id 		= $TopluSonuc['id'];
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Footer Menü Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$TopluSonuc['menu_isim']."</strong> başlıklı footer menüyü sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$TSorgu = $db->prepare("DELETE FROM footermenu WHERE id = :id");
					$TSorgu->execute(array('id' => $TopluSonuc['id']));
				}
				$last_id 		= $id ;
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Footer Menü Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_bul['menu_isim']."</strong> başlıklı footer menüyü sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['footermenu_sil'] = 'yes';		
				header("Location:../".yonetim."/footer-menu.html");
				exit();
			}
			else
			{
				$_SESSION['footermenu_sil'] = 'no';		
				header("Location:../".yonetim."/footer-menu.html");
				exit();
			}
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/footer-menu.html");
		exit();
	}
}

##Yetim Sil##
if(@$_GET['yetim_sil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("yetim_sil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$yetim_bul	= $db->query("SELECT * FROM yetimler WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		if($yetim_bul['foto'] != "" && file_exists("../".tema."/uploads/yetimler/".$yetim_bul['foto'])){
			unlink("../".tema."/uploads/yetimler/".$yetim_bul['foto']);
		}
		$yetim_sorgu	= $db->prepare("DELETE FROM yetimler WHERE id = :id");
		$yetim_sil 		= $yetim_sorgu->execute(array('id' => $id));
		if($yetim_sorgu->rowCount())
		{
			if($yetim_sil)
			{
				$last_id 		= $yetim_bul['id'];
				$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
				$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
				$bildirimkt 	= strtotime($kayitt);
				$bildirimt 		= strtotime($btarih);
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Yetim Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$yetim_bul['ad_soyad']."</strong> adındaki yetimi sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['yetim_sil'] = 'yes';
				header("Location:../".yonetim."/yetim-listele.html");
				exit();
			}
			else
			{
				$_SESSION['yetim_sil'] = 'no';
				header("Location:../".yonetim."/yetim-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/yetim-listele.html");
		exit();
	}
}

##Yetim Toplu Sil ##
if(isset($_POST['yetim_sil']))
{
	cVCLmHLxbS_panelislemkontrol("yetim_sil");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$yetim_bul	= $db->query("SELECT * FROM yetimler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$YetimTopluSorgu 	= $db->prepare("DELETE FROM yetimler WHERE id = :id");
				$YetimTopluSil		= $YetimTopluSorgu->execute(array('id' => $i));
				if($YetimTopluSil)
				{
					$last_id 		= $i;
					$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt 		= strtotime($kayitt);
					$bildirimt 		= strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Yetim Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$yetim_bul['ad_soyad']."</strong> adındaki yetimi sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					if($yetim_bul['foto'] != "" && file_exists("../".tema."/uploads/yetimler/".$yetim_bul['foto'])){
						unlink("../".tema."/uploads/yetimler/".$yetim_bul['foto']);
					}
				}
			}
			$_SESSION['yetim_sil'] = 'yes';
			header("Location:../".yonetim."/yetim-listele.html");
			exit();
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/yetim-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/yetim-listele.html");
		exit();
	}	
}

##Yetim Toplu Aktif ##
if(isset($_POST['yetim_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("yetim_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$yetim_bul= $db->query("SELECT * FROM yetimler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE yetimler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt 	= strtotime($kayitt);
					$bildirimt 		= strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Yetim Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$yetim_bul['ad_soyad']."</strong> adındaki yetimi aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
				}
			}
			$_SESSION['yetim_aktif'] = 'yes';
			header("Location:../".yonetim."/yetim-listele.html");
			exit();
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/yetim-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/yetim-listele.html");
		exit();
	}	
}

##Yetim Toplu Pasif ##
if(isset($_POST['yetim_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("yetim_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$yetim_bul= $db->query("SELECT * FROM yetimler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE yetimler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt 	= strtotime($kayitt);
					$bildirimt 		= strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Yetim Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$yetim_bul['ad_soyad']."</strong> adındaki yetimi pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
				}
			}
			$_SESSION['yetim_pasif'] = 'yes';
			header("Location:../".yonetim."/yetim-listele.html");
			exit();
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/yetim-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/yetim-listele.html");
		exit();
	}	
}

##Hizmet Kaydet ##
if(isset($_POST['hizmet_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("hizmet_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}								
		$aciklama 	= $_POST['aciklama'];
		$keywords	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/hizmetler");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/hizmetler/kapak");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		$Resim=''.$upload->file_dst_name.'';

		$bagis_ids	= isset($_POST['bagis_ids']) ? implode(',', $_POST['bagis_ids']) : null;
		
		$sorgu = $db->prepare("INSERT INTO hizmetler SET
				sira 		= ?,
				adi 		= ?,
				seo 		= ?,
				aciklama	= ?,
				keywords	= ?,
				description	= ?,
				durum 		= ?,
				resim 		= ?,
				bagis_ids 	= ?,
				dil 		= ?,
				tarih 		= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$adi,
				$seo,
				$aciklama,
				$keywords,
				$description,
				$durum,
				$Resim,
				$bagis_ids,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
			$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
			$bildirimkt 	= strtotime($kayitt);
			$bildirimt 		= strtotime($btarih);
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Hizmet Ekledi",
				'icon' 		=> "icon-docs",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> adında hizmet ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['hizmet_ekle'] = 'yes';
			header("Location:../".yonetim."/hizmet-listele.html");
		}
		else
		{
			$_SESSION['hizmet_ekle'] = 'no';
			header("Location:../".yonetim."/hizmet-ekle.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/hizmet-ekle.html");
	}
}

##Hizmet Güncelle ##
if(isset($_POST['hizmet_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("hizmet_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$aciklama 	= $_POST['aciklama'];
		$keywords 	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/hizmetler");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/hizmetler/kapak");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM hizmetler WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/hizmetler/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE hizmetler SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
			$Resim=''.$upload->file_dst_name.'';
		}
		
		$bagis_ids	= isset($_POST['bagis_ids']) ? implode(',', $_POST['bagis_ids']) : null;

		$sorgu = $db->prepare("UPDATE hizmetler SET
			sira 		= ?,
			adi 		= ?,
			seo 		= ?,
			aciklama	= ?,
			keywords	= ?,
			description	= ?,
			durum 		= ?,
			bagis_ids 	= ?,
			tarih 		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$adi,
			$seo,
			$aciklama,
			$keywords,
			$description,
			$durum,
			$bagis_ids,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
			$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
			$bildirimkt 	= strtotime($kayitt);
			$bildirimt 		= strtotime($btarih);
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Hizmet Güncellendi",
				'icon' 		=> "icon-docs",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı hizmeti güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['hizmet_guncelle'] = 'yes';
			header("Location:../".yonetim."/hizmet-duzenle/".$d_id.".html");
		}
		else
		{
			$_SESSION['hizmet_guncelle'] = 'no';
			header("Location:../".yonetim."/hizmet-duzenle/".$d_id.".html");
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/hizmet-duzenle/".$d_id.".html");
	}
}

##Hizmet Resim Sil##
if(@$_GET['hizmetresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("hizmetresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$hizmet_resim_bul	= $db->query("SELECT * FROM hizmetler WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/hizmetler/".$hizmet_resim_bul['resim']);
		unlink("../".tema."/uploads/hizmetler/kapak/".$hizmet_resim_bul['resim']);
		$hizmet_sorgu = $db->prepare("UPDATE hizmetler SET
				resim	= ?
				WHERE id = ?");
		$hizmet_guncelle = $hizmet_sorgu->execute(array(
				"",
				$resimid
			));
		if($hizmet_guncelle)
		{
			$last_id 		= $hizmet_resim_bul['id'];
			$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
			$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
			$bildirimkt 		= strtotime($kayitt);
			$bildirimt 		= strtotime($btarih);
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Hizmet Resim Silindi",
				'icon' 		=> "icon-trash",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$hizmet_resim_bul['adi']."</strong> başlıklı hizmetin resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['hizmetresimsil'] = 'yes';
			header("Location:../".yonetim."/hizmet-duzenle/".$resimid.".html");
		}
		else
		{
			$_SESSION['hizmetresimsil'] = 'no';
			header("Location:../".yonetim."/hizmet-duzenle/".$resimid.".html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/hizmet-duzenle/".$resimid.".html");
	}
}

##Hizmet Sil##
if(@$_GET['hizmetsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("hizmetsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$hizmet_resim_bul	= $db->query("SELECT * FROM hizmetler WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/hizmetler/".$hizmet_resim_bul['resim']);
		unlink("../".tema."/uploads/hizmetler/kapak/".$hizmet_resim_bul['resim']);
		$hizmet_sorgu	= $db->prepare("DELETE FROM hizmetler WHERE id = :id");
		$hizmet_sil 		= $hizmet_sorgu->execute(array('id' => $id));
		if($hizmet_sorgu->rowCount())
		{
			if($hizmet_sil)
			{
				$last_id 		= $hizmet_resim_bul['id'];
				$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
				$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
				$bildirimkt 		= strtotime($kayitt);
				$bildirimt 		= strtotime($btarih);
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Hizmet Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$hizmet_resim_bul['adi']."</strong> başlıklı hizmeti sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['hizmetsil'] = 'yes';
				header("Location:../".yonetim."/hizmet-listele.html");
			}
			else
			{
				$_SESSION['hizmetsil'] = 'no';
				header("Location:../".yonetim."/hizmet-listele.html");
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/hizmet-listele.html");
	}
}

##Hizmet Toplu Sil ##
if(isset($_POST['hizmet_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("hizmet_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$hizmet_resim_bul	= $db->query("SELECT * FROM hizmetler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$HizmetTopluSorgu 	= $db->prepare("DELETE FROM hizmetler WHERE id = :id");
				$HizmetTopluSil		= $HizmetTopluSorgu->execute(array('id' => $i));
				if($HizmetTopluSil)
				{
					$last_id 		= $i;
					$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt 		= strtotime($kayitt);
					$bildirimt 		= strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Hizmet Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$hizmet_resim_bul['adi']."</strong> başlıklı hizmeti sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/hizmetler/".$hizmet_resim_bul['resim']);
					unlink("../".tema."/uploads/hizmetler/kapak/".$hizmet_resim_bul['resim']);
					$_SESSION['hizmet_tumu'] = 'yes';
					header("Location:../".yonetim."/hizmet-listele.html");
				}
				else
				{
					$_SESSION['hizmet_tumu'] = 'no';
					header("Location:../".yonetim."/hizmet-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/hizmet-listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/hizmet-listele.html");
	}	
}

##Hizmet Toplu Aktif ##
if(isset($_POST['hizmet_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("hizmet_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM hizmetler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE hizmetler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt 	= strtotime($kayitt);
					$bildirimt 		= strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Hizmet Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı hizmeti aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['hizmet_aktif'] = 'yes';
					header("Location:../".yonetim."/hizmet-listele.html");
				}
				else
				{
					$_SESSION['hizmet_aktif'] = 'no';
					header("Location:../".yonetim."/hizmet-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/hizmet-listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/hizmet-listele.html");
	}	
}

##Hizmet Toplu Pasif ##
if(isset($_POST['hizmet_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("hizmet_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM hizmetler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE hizmetler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt 	= strtotime($kayitt);
					$bildirimt 		= strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Hizmet Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı hizmeti pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['hizmet_pasif'] = 'yes';
					header("Location:../".yonetim."/hizmet-listele.html");
				}
				else
				{
					$_SESSION['hizmet_pasif'] = 'no';
					header("Location:../".yonetim."/hizmet-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/hizmet-listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/hizmet-listele.html");
	}	
}

##Hizmet Tümünü Sil ##
if(@$_GET['hizmettumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("hizmettumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$HIZSorgu = $db->prepare("SELECT * FROM hizmetler");
		$HIZSorgu->execute();
		$HIZislem = $HIZSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $HIZislem as $HIZSonuc ){
			unlink("../".tema."/uploads/hizmetler/".$HIZSonuc['resim']);
			unlink("../".tema."/uploads/hizmetler/kapak/".$HIZSonuc['resim']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE hizmetler");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['hizmettumunusil'] = 'yes';
			header("Location:../".yonetim."/hizmet-listele.html");
			exit();
		}
		else
		{
			$_SESSION['hizmettumunusil'] = 'no';
			header("Location:../".yonetim."/hizmet-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/hizmet-listele.html");
		exit();
	}
}

##Hizmet Sıra Ajax##
if(isset($_GET['hizmetsiralama']))
{
	if($_GET['hizmetsiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE hizmetler SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Birim Kaydet ##
if(isset($_POST['birim_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("birim_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}								
		$aciklama 	= $_POST['aciklama'];
		$keywords	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/birimler");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/birimler/kapak");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		$Resim=''.$upload->file_dst_name.'';

		$sorgu = $db->prepare("INSERT INTO birimler SET
				sira 		= ?,
				adi 		= ?,
				seo 		= ?,
				aciklama	= ?,
				keywords	= ?,
				description	= ?,
				durum 		= ?,
				resim 		= ?,
				dil 		= ?,
				tarih 		= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$adi,
				$seo,
				$aciklama,
				$keywords,
				$description,
				$durum,
				$Resim,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
			$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
			$bildirimkt 		= strtotime($kayitt);
			$bildirimt 		= strtotime($btarih);
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Birim Ekledi",
				'icon' 		=> "icon-docs",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> adında birim ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['birim_ekle'] = 'yes';
			header("Location:../".yonetim."/birim-listele.html");
		}
		else
		{
			$_SESSION['birim_ekle'] = 'no';
			header("Location:../".yonetim."/birim-ekle.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/birim-ekle.html");
	}
}

##Birim Güncelle ##
if(isset($_POST['birim_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("birim_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$aciklama 	= $_POST['aciklama'];
		$keywords 	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/birimler");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/birimler/kapak");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM birimler WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/birimler/".$resim_bul['resim']);
			unlink("../".tema."/uploads/birimler/kapak/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE birimler SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
			$Resim=''.$upload->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE birimler SET
			sira 		= ?,
			adi 		= ?,
			seo 		= ?,
			aciklama	= ?,
			keywords	= ?,
			description	= ?,
			durum 		= ?,
			tarih 		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$adi,
			$seo,
			$aciklama,
			$keywords,
			$description,
			$durum,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
			$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
			$bildirimkt 		= strtotime($kayitt);
			$bildirimt 		= strtotime($btarih);
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Birim Güncellendi",
				'icon' 		=> "icon-docs",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı birimi güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['birim_guncelle'] = 'yes';
			header("Location:../".yonetim."/birim-duzenle/".$d_id.".html");
		}
		else
		{
			$_SESSION['birim_guncelle'] = 'no';
			header("Location:../".yonetim."/birim-duzenle/".$d_id.".html");
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/birim-duzenle/".$d_id.".html");
	}
}

##Birim Resim Sil##
if(@$_GET['birimresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("birimresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$birim_resim_bul	= $db->query("SELECT * FROM birimler WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/birimler/".$birim_resim_bul['resim']);
		unlink("../".tema."/uploads/birimler/kapak/".$birim_resim_bul['resim']);
		$birim_sorgu = $db->prepare("UPDATE birimler SET
				resim	= ?
				WHERE id = ?");
		$birim_guncelle = $birim_sorgu->execute(array(
				"",
				$resimid
			));
		if($birim_guncelle)
		{
			$last_id 		= $birim_resim_bul['id'];
			$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
			$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
			$bildirimkt 		= strtotime($kayitt);
			$bildirimt 		= strtotime($btarih);
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Birim Resim Silindi",
				'icon' 		=> "icon-trash",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$birim_resim_bul['adi']."</strong> başlıklı birim resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['birimresimsil'] = 'yes';
			header("Location:../".yonetim."/birim-duzenle/".$resimid.".html");
		}
		else
		{
			$_SESSION['birimresimsil'] = 'no';
			header("Location:../".yonetim."/birim-duzenle/".$resimid.".html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/birim-duzenle/".$resimid.".html");
	}
}

##Birim Sil##
if(@$_GET['birimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("birimsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$birim_resim_bul = $db->query("SELECT * FROM birimler WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/birimler/".$birim_resim_bul['resim']);
		unlink("../".tema."/uploads/birimler/kapak/".$birim_resim_bul['resim']);
		$birim_sorgu	= $db->prepare("DELETE FROM birimler WHERE id = :id");
		$birim_sil 		= $birim_sorgu->execute(array('id' => $id));
		if($birim_sorgu->rowCount())
		{
			if($birim_sil)
			{
				$last_id 		= $birim_resim_bul['id'];
				$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
				$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
				$bildirimkt 		= strtotime($kayitt);
				$bildirimt 		= strtotime($btarih);
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Birim Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$birim_resim_bul['adi']."</strong> başlıklı birimi sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['birimsil'] = 'yes';
				header("Location:../".yonetim."/birim-listele.html");
			}
			else
			{
				$_SESSION['birimsil'] = 'no';
				header("Location:../".yonetim."/birim-listele.html");
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/birim-listele.html");
	}
}

##Birim Toplu Sil ##
if(isset($_POST['birim_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("birim_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$birim_resim_bul	= $db->query("SELECT * FROM birimler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$BirimTopluSorgu 	= $db->prepare("DELETE FROM birimler WHERE id = :id");
				$BirimTopluSil		= $BirimTopluSorgu->execute(array('id' => $i));
				if($BirimTopluSil)
				{
					$last_id 		= $i;
					$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt 		= strtotime($kayitt);
					$bildirimt 		= strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Birim Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$birim_resim_bul['adi']."</strong> başlıklı birimi sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/birimler/".$birim_resim_bul['resim']);
					unlink("../".tema."/uploads/birimler/kapak/".$birim_resim_bul['resim']);
					$_SESSION['birim_tumu'] = 'yes';
					header("Location:../".yonetim."/birim-listele.html");
				}
				else
				{
					$_SESSION['birim_tumu'] = 'no';
					header("Location:../".yonetim."/birim-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/birim-listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/birim-listele.html");
	}	
}

##Birim Toplu Aktif ##
if(isset($_POST['birim_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("birim_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM birimler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE birimler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt 		= strtotime($kayitt);
					$bildirimt 		= strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Birim Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı birimi aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['birim_aktif'] = 'yes';
					header("Location:../".yonetim."/birim-listele.html");
				}
				else
				{
					$_SESSION['birim_aktif'] = 'no';
					header("Location:../".yonetim."/birim-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/birim-listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/birim-listele.html");
	}	
}

##Birim Toplu Pasif ##
if(isset($_POST['birim_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("birim_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM birimler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE birimler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt 	= strtotime($kayitt);
					$bildirimt 		= strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Birim Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı birimi pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['birim_pasif'] = 'yes';
					header("Location:../".yonetim."/birim-listele.html");
				}
				else
				{
					$_SESSION['birim_pasif'] = 'no';
					header("Location:../".yonetim."/birim-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/birim-listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/birim-listele.html");
	}	
}

##Birim Tümünü Sil ##
if(@$_GET['birimtumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("birimtumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$HIZSorgu = $db->prepare("SELECT * FROM birimler");
		$HIZSorgu->execute();
		$HIZislem = $HIZSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $HIZislem as $HIZSonuc ){
			unlink("../".tema."/uploads/birimler/".$HIZSonuc['resim']);
			unlink("../".tema."/uploads/birimler/kapak/".$HIZSonuc['resim']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE birimler");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['birimtumunusil'] = 'yes';
			header("Location:../".yonetim."/birim-listele.html");
			exit();
		}
		else
		{
			$_SESSION['birimtumunusil'] = 'no';
			header("Location:../".yonetim."/birim-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/birim-listele.html");
		exit();
	}
}

##Birim Sıra Ajax##
if(isset($_GET['birimsiralama']))
{
	if($_GET['birimsiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE birimler SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Sayfa Kaydet ##
if(isset($_POST['sayfa_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("sayfa_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}	
		$aciklama 	= $_POST['aciklama'];
		$keywords	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/sayfalar");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		$gitti=$Resim=''.$upload->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO sayfalar SET
				adi 	= ?,
				seo 	= ?,
				aciklama= ?,
				keywords= ?,
				description	= ?,
				durum 	= ?,
				resim 	= ?,
				dil 	= ?,
				tarih 	= ?");
		$Ekle = $sorgu->execute(array(
				$adi,
				$seo,
				$aciklama,
				$keywords,
				$description,
				$durum,
				$Resim,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Sayfa Ekledi",
				'icon' 		=> "icon-note",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> adında sayfa ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['sayfa_ekle'] = 'yes';
			header("Location:../".yonetim."/sayfa-listele.html");
			exit();
		}
		else
		{
			$_SESSION['sayfa_ekle'] = 'no';
			header("Location:../".yonetim."/sayfa-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sayfa-ekle.html");
		exit();
	}
}

##Sayfa Güncelle ##
if(isset($_POST['sayfa_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("sayfa_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$aciklama 	= $_POST['aciklama'];
		$keywords 	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/sayfalar");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM sayfalar WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/sayfalar/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE sayfalar SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
			$Resim=''.$upload->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE sayfalar SET
			adi 	= ?,
			seo 	= ?,
			aciklama= ?,
			keywords= ?,
			description	= ?,
			durum 	= ?,
			tarih 	= ?
			WHERE id = ?");
		$guncelle = $sorgu->execute(array(
			$adi,
			$seo,
			$aciklama,
			$keywords,
			$description,
			$durum,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Sayfa Güncellendi",
				'icon' 		=> "icon-note",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı sayfayı güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['sayfa_guncelle'] = 'yes';
			header("Location:../".yonetim."/sayfa-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['sayfa_guncelle'] = 'no';
			header("Location:../".yonetim."/sayfa-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sayfa-duzenle/".$d_id.".html");
		exit();
	}
}

##Sayfa Resim Sil##
if(@$_GET['sayfaresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("sayfaresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM sayfalar WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/sayfalar/".$resim_bul['resim']);
		$sorgu = $db->prepare("UPDATE sayfalar SET
					resim	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Sayfa Resim Silindi",
				'icon' 		=> "icon-trash",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı sayfanın resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['sayfaresimsil'] = 'yes';
			header("Location:../".yonetim."/sayfa-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['sayfaresimsil'] = 'no';
			header("Location:../".yonetim."/sayfa-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sayfa-duzenle/".$resimid.".html");
		exit();
	}
}

##Sayfa Sil##
if(@$_GET['sayfasil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("sayfasil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM sayfalar WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/sayfalar/".$resim_bul['resim']);
		$Sorgu = $db->prepare("DELETE FROM sayfalar WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Sayfa Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı sayfayı sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['sayfasil'] = 'yes';
				header("Location:../".yonetim."/sayfa-listele.html");
				exit();
			}
			else
			{
				$_SESSION['sayfasil'] = 'no';
				header("Location:../".yonetim."/sayfa-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sayfa-listele.html");
		exit();
	}
}

##Sayfa Toplu Sil ##
if(isset($_POST['sayfa_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("sayfa_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM sayfalar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM sayfalar WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt 		= strtotime($kayitt);
					$bildirimt 		= strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Sayfa Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı sayfayı sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/sayfalar/".$resim_bul['resim']);
					$_SESSION['sayfa_tumu'] = 'yes';
					header("Location:../".yonetim."/sayfa-listele.html");
				}
				else
				{
					$_SESSION['sayfa_tumu'] = 'no';
					header("Location:../".yonetim."/sayfa-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/sayfa-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sayfa-listele.html");
		exit();
	}	
}

##Sayfa Toplu Aktif ##
if(isset($_POST['sayfa_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("sayfa_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM sayfalar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE sayfalar SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt 	= strtotime($kayitt);
					$bildirimt 		= strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Sayfa Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı sayfayı aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['sayfa_aktif'] = 'yes';
					header("Location:../".yonetim."/sayfa-listele.html");
				}
				else
				{
					$_SESSION['sayfa_aktif'] = 'no';
					header("Location:../".yonetim."/sayfa-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/sayfa-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sayfa-listele.html");
		exit();
	}	
}

##Sayfa Toplu Pasif ##
if(isset($_POST['sayfa_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("sayfa_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM sayfalar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE sayfalar SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Sayfa Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı sayfayı pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['sayfa_pasif'] = 'yes';
					header("Location:../".yonetim."/sayfa-listele.html");
				}
				else
				{
					$_SESSION['sayfa_pasif'] = 'no';
					header("Location:../".yonetim."/sayfa-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/sayfa-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sayfa-listele.html");
		exit();
	}	
}

##Sayfa Tümünü Sil ##
if(@$_GET['sayfatumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("sayfatumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$SSorgu = $db->prepare("SELECT * FROM sayfalar");
		$SSorgu->execute();
		$Sislem = $SSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $Sislem as $SSonuc ){
			unlink("../".tema."/uploads/sayfalar/".$SSonuc['resim']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE sayfalar");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['sayfatumunusil'] = 'yes';
			header("Location:../".yonetim."/sayfa-listele.html");
			exit();
		}
		else
		{
			$_SESSION['sayfatumunusil'] = 'no';
			header("Location:../".yonetim."/sayfa-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sayfa-listele.html");
		exit();
	}
}

##Slider Kaydet ##
if(isset($_POST['slider_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("slider_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= isset($_POST['sira']) ? (int)$_POST['sira'] : 0;
		$adi 		= isset($_POST['adi']) ? trim($_POST['adi']) : '';
		$url 		= isset($_POST['url']) ? trim($_POST['url']) : '';
		$sekme 		= !empty($_POST['sekme']) ? 1 : 0;
		$durum 		= !empty($_POST['durum']) ? 1 : 0;
		$aciklama 	= isset($_POST['aciklama']) ? trim($_POST['aciklama']) : '';
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		$admin_dil	= isset($_SESSION['admin_dil']) ? (int)$_SESSION['admin_dil'] : 1;
		
		$Resim = '';
		$MobilResim = '';
		
		$slider_upload_dir = "../".tema."/uploads/slider";
		if (!is_dir($slider_upload_dir)) {
			@mkdir($slider_upload_dir, 0755, true);
		}
		
		if (!empty($_FILES['resim']['name']) && $_FILES['resim']['error'] == 0) {
			$upload = new upload($_FILES['resim']);
			if ($upload->uploaded)
			{
				$upload->file_auto_rename = true;
				$upload->image_resize = false;
				$upload->image_ratio_crop = false;
				$upload->process($slider_upload_dir);
				if ($upload->processed)
				{
					$Resim = ''.$upload->file_dst_name.'';
				}
			}
		}
		
		if (!empty($_FILES['mobil_resim']['name']) && $_FILES['mobil_resim']['error'] == 0) {
			$uploadMobil = new upload($_FILES['mobil_resim']);
			if ($uploadMobil->uploaded)
			{
				$uploadMobil->file_auto_rename = true;
				$uploadMobil->image_resize = false;
				$uploadMobil->image_ratio_crop = false;
				$uploadMobil->process($slider_upload_dir);
				if ($uploadMobil->processed)
				{
					$MobilResim = ''.$uploadMobil->file_dst_name.'';
				}
			}
		}
		
		try {
			$sorgu = $db->prepare("INSERT INTO slider SET
					sira = :sira,
					adi = :adi,
					url = :url,
					sekme = :sekme,
					aciklama = :aciklama,
					durum = :durum,
					resim = :resim,
					mobil_resim = :mobil_resim,
					dil = :dil,
					tarih = :tarih");
			$Ekle = $sorgu->execute(array(
					':sira' => $sira,
					':adi' => $adi,
					':url' => $url,
					':sekme' => $sekme,
					':aciklama' => $aciklama,
					':durum' => $durum,
					':resim' => $Resim,
					':mobil_resim' => $MobilResim,
					':dil' => $admin_dil,
					':tarih' => $tarih
					));
		} catch (Exception $e) {
			$_SESSION['slider_ekle'] = 'no';
			header("Location:../".yonetim."/slider-ekle.html");
			exit();
		}
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Slider",
				'icon' 		=> "icon-picture",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkgoldenrod;'>".$adi."</strong> başlıklı slider ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['slider_ekle'] = 'yes';
			header("Location:../".yonetim."/slider-listele.html");
			exit();
		}
		else
		{
			$_SESSION['slider_ekle'] = 'no';
			header("Location:../".yonetim."/slider-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/slider-ekle.html");
		exit();
	}
}

##Slider Güncelle ##
if(isset($_POST['slider_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("slider_guncelle");
	$d_id 		= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$url 		= $_POST['url'];
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$aciklama 	= $_POST['aciklama'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->image_resize = false;
			$upload->image_ratio_crop = false;
			$upload->process("../".tema."/uploads/slider");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		$uploadMobil = new upload($_FILES['mobil_resim']);
		if ($uploadMobil->uploaded)
		{
			$uploadMobil->file_auto_rename = true;
			$uploadMobil->image_resize = false;
			$uploadMobil->image_ratio_crop = false;
			$uploadMobil->process("../".tema."/uploads/slider");
			if ($uploadMobil->processed)
			{
				$MobilResim = ''.$uploadMobil->file_dst_name.'';
			}
		}
		
		if(isset($MobilResim)){
			$resim_bul_mobil = $db->query("SELECT * FROM slider WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			if($resim_bul_mobil['mobil_resim']){
				unlink("../".tema."/uploads/slider/".$resim_bul_mobil['mobil_resim']);
			}
			$guncelleMobil = $db->prepare("UPDATE slider SET mobil_resim = ? WHERE id = ?");
			$guncelleMobil->execute([$MobilResim,$d_id]);
		}
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM slider WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/slider/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE slider SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
		}
		$sorgu = $db->prepare("UPDATE slider SET
			sira 	= ?,
			adi 	= ?,
			url 	= ?,
			sekme 	= ?,
			aciklama= ?,
			durum 	= ?,
			tarih 	= ?
			WHERE id = ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$adi,
			$url,
			$sekme,
			$aciklama,
			$durum,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Slider Güncellendi",
				'icon' 		=> "icon-picture",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkgoldenrod;'>".$adi."</strong> başlıklı slideri güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['slider_guncelle'] = 'yes';
			header("Location:../".yonetim."/slider-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['slider_guncelle'] = 'no';
			header("Location:../".yonetim."/slider-duzenle/".$d_id.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/slider-duzenle/".$d_id.".html");
		exit();
	}
}

##Slider Resim Sil##
if(@$_GET['sliderresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("sliderresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM slider WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/slider/".$resim_bul['resim']);
		$sorgu = $db->prepare("UPDATE slider SET
					resim	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Slider Resim Silindi",
				'icon' 		=> "icon-picture",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkgoldenrod;'>".$resim_bul['adi']."</strong> başlıklı sliderin resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['sliderresimsil'] = 'yes';
			header("Location:../".yonetim."/slider-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['sliderresimsil'] = 'no';
			header("Location:../".yonetim."/slider-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/slider-duzenle/".$resimid.".html");
		exit();
	}
}

##Slider Mobil Resim Sil##
if(@$_GET['sliderresimsil_mobil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("sliderresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM slider WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/slider/".$resim_bul['mobil_resim']);
		$sorgu = $db->prepare("UPDATE slider SET
					mobil_resim	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Slider Mobil Resim Silindi",
				'icon' 		=> "icon-picture",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkgoldenrod;'>".$resim_bul['adi']."</strong> başlıklı sliderin mobil resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['sliderresimsil'] = 'yes';
			header("Location:../".yonetim."/slider-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['sliderresimsil'] = 'no';
			header("Location:../".yonetim."/slider-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/slider-duzenle/".$resimid.".html");
		exit();
	}
}

##Slider Sil##
if(@$_GET['slidersil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("slidersil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM slider WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/slider/".$resim_bul['resim']);
		$Sorgu = $db->prepare("DELETE FROM slider WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Slider Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkgoldenrod;'>".$resim_bul['adi']."</strong> başlıklı slideri sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['slidersil'] = 'yes';
				header("Location:../".yonetim."/slider-listele.html");
				exit();
			}
			else
			{
				$_SESSION['slidersil'] = 'no';
				header("Location:../".yonetim."/slider-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/slider-listele.html");
		exit();
	}
}

##Slider Toplu Sil ##
if(isset($_POST['slider_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("slider_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM slider WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM slider WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Slider Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkgoldenrod;'>".$resim_bul['adi']."</strong> başlıklı slideri sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/slider/".$resim_bul['resim']);
					$_SESSION['slider_tumu'] = 'yes';
					header("Location:../".yonetim."/slider-listele.html");
				}
				else
				{
					$_SESSION['slider_tumu'] = 'no';
					header("Location:../".yonetim."/slider-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/slider-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/slider-listele.html");
		exit();
	}	
}

##Slider Toplu Aktif ##
if(isset($_POST['slider_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("slider_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$slider_bul= $db->query("SELECT * FROM slider WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE slider SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Slider Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkgoldenrod;'>".$slider_bul['adi']."</strong> başlıklı slideri aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['slider_aktif'] = 'yes';
					header("Location:../".yonetim."/slider-listele.html");
				}
				else
				{
					$_SESSION['slider_aktif'] = 'no';
					header("Location:../".yonetim."/slider-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/slider-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/slider-listele.html");
		exit();
	}	
}

##Slider Toplu Pasif ##
if(isset($_POST['slider_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("slider_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$slider_bul= $db->query("SELECT * FROM slider WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE slider SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Slider Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkgoldenrod;'>".$slider_bul['adi']."</strong> başlıklı slideri pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['slider_pasif'] = 'yes';
					header("Location:../".yonetim."/slider-listele.html");
				}
				else
				{
					$_SESSION['slider_pasif'] = 'no';
					header("Location:../".yonetim."/slider-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/slider-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/slider-listele.html");
		exit();
	}	
}

##Slider Tümünü Sil ##
if(@$_GET['slidertumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("slidertumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$DUSorgu = $db->prepare("SELECT * FROM slider");
		$DUSorgu->execute();
		$DUislem = $DUSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $DUislem as $DUSonuc ){
			unlink("../".tema."/uploads/slider/".$DUSonuc['resim']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE slider");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['slidertumunusil'] = 'yes';
			header("Location:../".yonetim."/slider-listele.html");
			exit();
		}
		else
		{
			$_SESSION['slidertumunusil'] = 'no';
			header("Location:../".yonetim."/slider-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/slider-listele.html");
		exit();
	}
}

##Slider Sıra Ajax##
if(isset($_GET['slidersiralama']))
{
	if($_GET['slidersiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE slider SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Haber Kategori Kaydet ##
if(isset($_POST['haber_kategori_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("haber_kategori_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		$sira 		= $_POST['sira'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}		
		$keywords	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['kapak']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/haber_kategoriler");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/haber_kategoriler/kapak");
					
			if ($upload->processed)
			{
				$Kapak=''.$upload->file_dst_name.'';
			}
		}
		$Kapak=''.$upload->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO haber_kategori SET
				adi 		= ?,
				sira 		= ?,
				seo 		= ?,
				keywords	= ?,
				description	= ?,
				kapak		= ?,
				durum 		= ?,
				dil 		= ?,
				tarih 		= ?");
		$Ekle = $sorgu->execute(array(
				$adi,
				$sira,
				$seo,
				$keywords,
				$description,
				$Kapak,
				$durum,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Haber Kategorisi Ekledi",
				'icon' 		=> "icon-book-open",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı haber kategorisi ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['haber_kategori_ekle'] = 'yes';
			header("Location:../".yonetim."/haber-kategoriler.html");
			exit();
		}
		else
		{
			$_SESSION['haber_kategori_ekle'] = 'no';
			header("Location:../".yonetim."/haber-kategori-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-kategori-ekle.html");
		exit();
	}
}

##Haber Kategori Güncelle ##
if(isset($_POST['haber_kategori_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("haber_kategori_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		$sira 		= $_POST['sira'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$keywords 	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['kapak']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/haber_kategoriler");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/haber_kategoriler/kapak");
			if ($upload->processed)
			{
				$Kapak=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Kapak)){
			$resim_bul= $db->query("SELECT * FROM haber_kategori WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/haber_kategoriler/".$resim_bul['kapak']);
			unlink("../".tema."/uploads/haber_kategoriler/kapak/".$resim_bul['kapak']);
			$guncelle = $db->prepare("UPDATE haber_kategori SET kapak = ? WHERE id = ?");
			$guncelle->execute([$Kapak,$d_id]);
			$Kapak=''.$upload->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE haber_kategori SET
			adi 		= ?,
			sira 		= ?,
			seo 		= ?,
			keywords	= ?,
			description	= ?,
			durum 		= ?,
			tarih 		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$adi,
			$sira,
			$seo,
			$keywords,
			$description,
			$durum,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Haber Kategorisi Güncellendi",
				'icon' 		=> "icon-book-open",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı haber kategorisini güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['haber_kategori_guncelle'] = 'yes';
			header("Location:../".yonetim."/haber-kategori-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['haber_kategori_guncelle'] = 'no';
			header("Location:../".yonetim."/haber-kategori-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-kategori-duzenle/".$d_id.".html");
		exit();
	}
}

##Haber Kategori Kapak Sil##
if(@$_GET['haber_kategoriresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("haber_kategoriresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM haber_kategori WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/haber_kategoriler/".$resim_bul['kapak']);
		unlink("../".tema."/uploads/haber_kategoriler/kapak/".$resim_bul['kapak']);
		$sorgu = $db->prepare("UPDATE haber_kategori SET
					kapak	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Haber Kategori Kapak Resmi Silindi",
				'icon' 		=> "icon-book-open",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı haber kategorisinin kapak resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['haber_kategoriresimsil'] = 'yes';
			header("Location:../".yonetim."/haber-kategori-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['haber_kategoriresimsil'] = 'no';
			header("Location:../".yonetim."/haber-kategori-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-kategori-duzenle/".$resimid.".html");
		exit();
	}
}

##Haber Kategori Sil##
if(@$_GET['haberkatsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("haberkatsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM haber_kategori WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/haber_kategoriler/".$resim_bul['kapak']);
		unlink("../".tema."/uploads/haber_kategoriler/kapak/".$resim_bul['kapak']);
		$Sorgu = $db->prepare("DELETE FROM haber_kategori WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Haber Kategorisi Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı haber kategorisini sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['haberkatsil'] = 'yes';
				header("Location:../".yonetim."/haber-kategoriler.html");
				exit();
			}
			else
			{
				$_SESSION['haberkatsil'] = 'no';
				header("Location:../".yonetim."/haber-kategoriler.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-kategoriler.html");
		exit();
	}
}

##Haber Kategori Toplu Sil ##
if(isset($_POST['haber_kat_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("haber_kat_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM haber_kategori WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM haber_kategori WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Haber Kategorisi Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı haber kategorisini sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/haber_kategoriler/".$resim_bul['kapak']);
					unlink("../".tema."/uploads/haber_kategoriler/kapak/".$resim_bul['kapak']);
					$_SESSION['haber_kat_tumu'] = 'yes';
					header("Location:../".yonetim."/haber-kategoriler.html");
				}
				else
				{
					$_SESSION['haber_kat_tumu'] = 'no';
					header("Location:../".yonetim."/haber-kategoriler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/haber-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-kategoriler.html");
		exit();
	}	
}

##Haber Kategori Toplu Aktif ##
if(isset($_POST['haber_kat_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("haber_kat_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM haber_kategori WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE haber_kategori SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Haber Kategori Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı haber kategorisini aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['haber_kat_aktif'] = 'yes';
					header("Location:../".yonetim."/haber-kategoriler.html");
				}
				else
				{
					$_SESSION['haber_kat_aktif'] = 'no';
					header("Location:../".yonetim."/haber-kategoriler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/haber-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-kategoriler.html");
		exit();
	}	
}

##Haber Kategori Toplu Pasif ##
if(isset($_POST['haber_kat_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("haber_kat_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM haber_kategori WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE haber_kategori SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Haber Kategori Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı haber kategorisini pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['haber_kat_pasif'] = 'yes';
					header("Location:../".yonetim."/haber-kategoriler.html");
				}
				else
				{
					$_SESSION['haber_kat_pasif'] = 'no';
					header("Location:../".yonetim."/haber-kategoriler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/haber-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-kategoriler.html");
		exit();
	}	
}

##Haber Kategori Sıra Ajax##
if(isset($_GET['haberkatsiralama']))
{
	if($_GET['haberkatsiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE haber_kategori SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Haber Kategori Tümünü Sil ##
if(@$_GET['haberkattumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("haberkattumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$DUSorgu = $db->prepare("SELECT * FROM haber_kategori");
		$DUSorgu->execute();
		$DUislem = $DUSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $DUislem as $DUSonuc ){
			unlink("../".tema."/uploads/haber_kategoriler/".$DUSonuc['kapak']);
			unlink("../".tema."/uploads/haber_kategoriler/kapak/".$DUSonuc['kapak']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE haber_kategori");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['haberkattumunusil'] = 'yes';
			header("Location:../".yonetim."/haber-kategoriler.html");
			exit();
		}
		else
		{
			$_SESSION['haberkattumunusil'] = 'no';
			header("Location:../".yonetim."/haber-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-kategoriler.html");
		exit();
	}
}

##Haber Kaydet ##
if(isset($_POST['haber_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("haber_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$kategori 	= $_POST['kategori'];
		$videoid 	= $_POST['videoid'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}		
		if($_POST['manset']){$manset = 1;}else{$manset = 0;}		
		if($_POST['manset_yani']){$manset_yani = 1;}else{$manset_yani = 0;}		
		$spot 		= $_POST['spot'];
		$aciklama 	= $_POST['aciklama'];
		$keywords	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= $_POST['tarih'];
		$tarihg		= $_POST['tarihg'];
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/haberler");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 950;
			$upload->image_y = 480;
			$upload->process("../".tema."/uploads/haberler/kapak");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		$Resim=''.$upload->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO haberler SET
				kategori 	= ?,
				sira 		= ?,
				adi 		= ?,
				seo 		= ?,
				spot		= ?,
				videoid		= ?,
				aciklama	= ?,
				keywords	= ?,
				description	= ?,
				durum 		= ?,
				manset 		= ?,
				manset_yani = ?,
				resim 		= ?,
				dil 		= ?,
				tarihg 		= ?,
				tarih 		= ?");
		$Ekle = $sorgu->execute(array(
				$kategori,
				$sira,
				$adi,
				$seo,
				$spot,
				$videoid,
				$aciklama,
				$keywords,
				$description,
				$durum,
				$manset,
				$manset_yani,
				$Resim,
				$_SESSION['admin_dil'],
				$tarihg,
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Haber",
				'icon' 		=> "icon-book-open",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı haber ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['haber_ekle'] = 'yes';
			header("Location:../".yonetim."/haber-listele.html");
			exit();
		}
		else
		{
			$_SESSION['haber_ekle'] = 'no';
			header("Location:../".yonetim."/haber-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-ekle.html");
		exit();
	}
}

##Haber Güncelle ##
if(isset($_POST['haber_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("haber_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$kategori 	= $_POST['kategori'];
		$videoid 	= $_POST['videoid'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		if($_POST['manset']){$manset = 1;}else{$manset = 0;}		
		if($_POST['manset_yani']){$manset_yani = 1;}else{$manset_yani = 0;}	
		$spot 		= $_POST['spot'];
		$aciklama 	= $_POST['aciklama'];
		$keywords 	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= $_POST['tarih'];
		$tarihg		= $_POST['tarihg'];
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/haberler");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 950;
			$upload->image_y = 480;
			$upload->process("../".tema."/uploads/haberler/kapak");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM haberler WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/haberler/".$resim_bul['resim']);
			unlink("../".tema."/uploads/haberler/kapak/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE haberler SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
			$Resim=''.$upload->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE haberler SET
			kategori 	= ?,
			sira 		= ?,
			adi 		= ?,
			seo 		= ?,
			spot		= ?,
			videoid		= ?,
			aciklama	= ?,
			keywords	= ?,
			description	= ?,
			durum 		= ?,
			manset 		= ?,
			manset_yani = ?,
			tarihg 		= ?,
			tarih 		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$kategori,
			$sira,
			$adi,
			$seo,
			$spot,
			$videoid,
			$aciklama,
			$keywords,
			$description,
			$durum,
			$manset,
			$manset_yani,
			$tarihg,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Haber Güncellendi",
				'icon' 		=> "icon-book-open",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı haberi güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['haber_guncelle'] = 'yes';
			header("Location:../".yonetim."/haber-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['haber_guncelle'] = 'no';
			header("Location:../".yonetim."/haber-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-duzenle/".$d_id.".html");
		exit();
	}
}

##Haber Resim Sil##
if(@$_GET['haberresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("haberresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM haberler WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/haberler/".$resim_bul['resim']);
		unlink("../".tema."/uploads/haberler/kapak/".$resim_bul['resim']);
		$sorgu = $db->prepare("UPDATE haberler SET
					resim	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Haber Resim Silindi",
				'icon' 		=> "icon-book-open",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı haber resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['haberresimsil'] = 'yes';
			header("Location:../".yonetim."/haber-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['haberresimsil'] = 'no';
			header("Location:../".yonetim."/haber-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-duzenle/".$resimid.".html");
		exit();
	}
}

##Haber Sil##
if(@$_GET['habersil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("habersil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM haberler WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/haberler/".$resim_bul['resim']);
		unlink("../".tema."/uploads/haberler/kapak/".$resim_bul['resim']);
		$Sorgu = $db->prepare("DELETE FROM haberler WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$TopluSorgu = $db->prepare("SELECT * FROM haberfoto WHERE resimid = ?");
				$TopluSorgu->execute(array($_GET['id']));
				$Topluislem = $TopluSorgu->fetchALL(PDO::FETCH_ASSOC);
				foreach ( $Topluislem as $TopluSonuc )
				{
					$TSorgu = $db->prepare("DELETE FROM haberfoto WHERE id = :id");
					$TSorgu->execute(array('id' => $TopluSonuc['id']));
					unlink("../".tema."/uploads/haberler/fotogaleri/".$TopluSonuc['resim']);
				}
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Haber Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı haberi sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['habersil'] = 'yes';
				header("Location:../".yonetim."/haber-listele.html");
				exit();
			}
			else
			{
				$_SESSION['habersil'] = 'no';
				header("Location:../".yonetim."/haber-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-listele.html");
		exit();
	}
}

##Haber Foto Sil##
if(@$_GET['haberfotosil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("haberfotosil");
	$galeriid = $_GET['galeriid'];
	if($_SESSION['rutbe'] == 0)
	{
		$id 	= $_GET['id'];		
		$galeri_bul= $db->query("SELECT * FROM haberler WHERE id = '{$galeriid}'")->fetch(PDO::FETCH_ASSOC);
		$resim_bul= $db->query("SELECT * FROM haberfoto WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/haberler/fotogaleri/".$resim_bul['resim']);
		$Sorgu = $db->prepare("DELETE FROM haberfoto WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Haber Foto Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$galeri_bul['adi']."</strong> başlıklı habere ait fotoyu sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['haberfotosil'] = 'yes';
				header("Location:../".yonetim."/haber-fotograflar/".$galeriid.".html");
				exit();
			}
			else
			{
				$_SESSION['haberfotosil'] = 'no';
				header("Location:../".yonetim."/haber-fotograflar/".$galeriid.".html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-fotograflar/".$galeriid.".html");
		exit();
	}
}

##Haber Toplu Sil ##
if(isset($_POST['haber_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("haber_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM haberler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM haberler WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$TopluSorguAlt = $db->prepare("SELECT * FROM haberfoto WHERE resimid = ?");
					$TopluSorguAlt->execute(array($i));
					$TopluislemAlt = $TopluSorguAlt->fetchALL(PDO::FETCH_ASSOC);
					foreach ( $TopluislemAlt as $TopluSonucAlt )
					{
						$TSorgu = $db->prepare("DELETE FROM haberfoto WHERE id = :id");
						$TSorgu->execute(array('id' => $TopluSonucAlt['id']));
						unlink("../".tema."/uploads/haberler/fotogaleri/".$TopluSonucAlt['resim']);
					}
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Haber Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı haberi sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/haberler/".$resim_bul['resim']);
					unlink("../".tema."/uploads/haberler/kapak/".$resim_bul['resim']);
					$_SESSION['haber_tumu'] = 'yes';
					header("Location:../".yonetim."/haber-listele.html");
				}
				else
				{
					$_SESSION['haber_tumu'] = 'no';
					header("Location:../".yonetim."/haber-listele.html");
					exit();
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/haber-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-listele.html");
		exit();
	}	
}

##Haber Toplu Aktif ##
if(isset($_POST['haber_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("haber_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM haberler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE haberler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Haber Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$blog_bul['adi']."</strong> başlıklı haberi aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['haber_aktif'] = 'yes';
					header("Location:../".yonetim."/haber-listele.html");
				}
				else
				{
					$_SESSION['haber_aktif'] = 'no';
					header("Location:../".yonetim."/haber-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/haber-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-listele.html");
		exit();
	}	
}

##Haber Toplu Pasif ##
if(isset($_POST['haber_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("haber_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM haberler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE haberler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Haber Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$blog_bul['adi']."</strong> başlıklı haberi pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['haber_pasif'] = 'yes';
					header("Location:../".yonetim."/haber-listele.html");
				}
				else
				{
					$_SESSION['haber_pasif'] = 'no';
					header("Location:../".yonetim."/haber-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/haber-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-listele.html");
		exit();
	}	
}

##Haber Foto Toplu Sil ##
if(isset($_POST['haberfoto_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("haberfoto_tumu");
	$galeriid = $_POST['galeriid'];
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['fotoid'])
		{
			$galeri_bul= $db->query("SELECT * FROM haberler WHERE id = '{$galeriid}'")->fetch(PDO::FETCH_ASSOC);
			foreach($_POST['fotoid'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM haberfoto WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM haberfoto WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Haber Foto Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$galeri_bul['adi']."</strong> başlıklı habere ait fotoyu sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/haberler/fotogaleri/".$resim_bul['resim']);
					$_SESSION['haberfoto_tumu'] = 'yes';
					header("Location:../".yonetim."/haber-fotograflar/".$galeriid.".html");
				}
				else
				{
					$_SESSION['haberfoto_tumu'] = 'no';
					header("Location:../".yonetim."/haber-fotograflar/".$galeriid.".html");
					exit();
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/haber-fotograflar/".$galeriid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-fotograflar/".$galeriid.".html");
		exit();
	}	
}

##Haber Foto Güncelle ##
if(isset($_POST['haberfoto_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("haberfoto_guncelle");
	$galeriid = $_POST['galeriid'];
	if($_SESSION['rutbe'] == 0)
	{
		$d_id 	= $_POST['foto_id'];
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/haberler/fotogaleri");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM haberfoto WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/haberler/fotogaleri/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE haberfoto SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
			$Resim=''.$upload->file_dst_name.'';
		}

		if($guncelle)
		{
			$_SESSION['haberfoto_guncelle'] = 'yes';
			header("Location:../".yonetim."/haber-fotograflar/".$galeriid.".html");
			exit();
		}
		else
		{
			$_SESSION['haberfoto_guncelle'] = 'no';
			header("Location:../".yonetim."/haber-fotograflar/".$galeriid.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-fotograflar/".$galeriid.".html");
		exit();
	}
}

##Haber Foto Kaydet ##
if(isset($_POST['haberfoto_ekle']))
{
	$last_id  = $_POST['id'];
	cVCLmHLxbS_panelislemkontrol("haberfoto_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$files = array();
		foreach ($_FILES['resimler'] as $k => $l) {
			foreach ($l as $i => $v) {
				if (!array_key_exists($i, $files))
					$files[$i] = array();
				$files[$i][$k] = $v;
			}
		}		

		foreach ($files as $file) 
		{
			$yukle = new Upload($file);
			if($yukle->uploaded) 
			{
				$yukle->file_auto_rename = true;
				$yukle->process("../".tema."/uploads/haberler/fotogaleri");
				
				$yukle->allowed = array ( 'image/*' );
				if ($yukle->processed) 
				{
					$DigerResim=''.$yukle->file_dst_name.'';
					
					$sorgu = $db->prepare("INSERT INTO haberfoto SET
						resimid = ?,
						resim 	= ?");
					$yap = $sorgu->execute(array(
						$last_id,
						$DigerResim
					));
				}
			}
		}
		if($yap)
		{
			$_SESSION['haberfoto_ekle'] = 'yes';
			header("Location:../".yonetim."/haber-fotograflar/".$last_id.".html");
			exit();
		}
		else
		{
			$_SESSION['haberfoto_ekle'] = 'no';
			header("Location:../".yonetim."/haber-fotograflar/".$last_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-fotograflar/".$last_id.".html");
		exit();
	}
}

##Haber Tümünü Sil ##
if(@$_GET['habertumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("habertumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$HSorgu = $db->prepare("SELECT * FROM haberler");
		$HSorgu->execute();
		$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $Hislem as $HSonuc ){
			$TopluSorgu = $db->prepare("SELECT * FROM haberfoto WHERE resimid = ?");
			$TopluSorgu->execute(array($HSonuc['id']));
			$Topluislem = $TopluSorgu->fetchALL(PDO::FETCH_ASSOC);
			foreach ( $Topluislem as $TopluSonuc )
			{
				$TSorgu = $db->prepare("DELETE FROM haberfoto WHERE id = :id");
				$TSorgu->execute(array('id' => $TopluSonuc['id']));
				unlink("../".tema."/uploads/haberler/fotogaleri/".$TopluSonuc['resim']);
			}
			unlink("../".tema."/uploads/haberler/".$HSonuc['resim']);
			unlink("../".tema."/uploads/haberler/kapak/".$HSonuc['resim']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE haberler");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['habertumunusil'] = 'yes';
			header("Location:../".yonetim."/haber-listele.html");
			exit();
		}
		else
		{
			$_SESSION['habertumunusil'] = 'no';
			header("Location:../".yonetim."/haber-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haber-listele.html");
		exit();
	}
}

##Haber Sıra Ajax##
if(isset($_GET['habersiralama']))
{
	if($_GET['habersiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE haberler SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Yönetici Kaydet ##
if(isset($_POST['yonetici_kaydet']))
{
	cVCLmHLxbS_panelislemkontrol("yonetici_kaydet");
	if($_SESSION['rutbe'] == 0)
	{
		$isim 	= $_POST['isim'];
		$email 	= $_POST['email'];
		$kadi 	= $_POST['kadi'];
		$sifre 	= $_POST['sifre'];
		$tarih	= date("Y-m-d H:i:s");
		$tarih	= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".yonetim."/images/users");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		$gitti=$Resim=''.$upload->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO kullanici SET
				isim 	= ?,
				email 	= ?,
				kadi	= ?,
				sifre	= ?,
				resim	= ?,
				son_giris= ?");
		$Ekle = $sorgu->execute(array(
				$isim,
				$email,
				$kadi,
				$sifre,
				$Resim,
				$tarih
			));

		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Yönetici",
				'icon' 		=> "icon-user-follow",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkviolet;'>".$isim."</strong> adında yönetici ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['yonetici_kaydet'] = 'yes';
			header("Location:../".yonetim."/yonetici-listele.html");
			exit();
		}
		else
		{
			$_SESSION['yonetici_kaydet'] = 'no';
			header("Location:../".yonetim."/yonetici-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/yonetici-ekle.html");
		exit();
	}
}

##Yönetici Güncelle ##
if(isset($_POST['yonetici_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("yonetici_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$isim 	= $_POST['isim'];
		$email 	= $_POST['email'];
		$kadi 	= $_POST['kadi'];
		$sifre 	= $_POST['sifre'];	

		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".yonetim."/images/users");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM kullanici WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".yonetim."/images/users/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE kullanici SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
		}
		
		$sorgu = $db->prepare("UPDATE kullanici SET
				isim 	= ?,
				email 	= ?,
				kadi	= ?,
				sifre	= ?
				WHERE id = ?");
		$guncelle = $sorgu->execute(array(
				$isim,
				$email,
				$kadi,
				$sifre,
				$d_id
			));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yönetici Güncellendi",
				'icon' 		=> "icon-user-follow",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkviolet;'>".$isim."</strong> isimli yönetici hesabını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['yonetici_guncelle'] = 'yes';
			header("Location:../".yonetim."/yonetici-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['yonetici_guncelle'] = 'no';
			header("Location:../".yonetim."/yonetici-duzenle/".$d_id.".html");
			exit();
		}
		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/yonetici-duzenle/".$d_id.".html");
		exit();
	}
}

##Yönetici Resim Sil##
if(@$_GET['yoneticiresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("yoneticiresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM kullanici WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".yonetim."/assets/images/users/".$resim_bul['resim']);
		$sorgu = $db->prepare("UPDATE kullanici SET
				resim	= ?
				WHERE id = ?");
		$guncelle = $sorgu->execute(array(
				"",
				$resimid
			));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "yönetici Resmi Silindi",
				'icon' 		=> "icon-user-follow",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkviolet;'>".$resim_bul['isim']."</strong> isimli yöneticinin resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['yoneticiresimsil'] = 'yes';
			header("Location:../".yonetim."/yonetici-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['yoneticiresimsil'] = 'no';
			header("Location:../".yonetim."/yonetici-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/yonetici-duzenle/".$resimid.".html");
		exit();
	}
}

##Yönetici Sil##
if(@$_GET['yoneticisil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("yoneticisil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM kullanici WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".yonetim."/images/users/".$resim_bul['resim']);
		$Sorgu = $db->prepare("DELETE FROM kullanici WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Yönetici Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkviolet;'>".$resim_bul['isim']."</strong> isimli yöneticiyi sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['yoneticisil'] = 'yes';
				header("Location:../".yonetim."/yonetici-listele.html");
				exit();
			}
			else
			{
				$_SESSION['yoneticisil'] = 'no';
				header("Location:../".yonetim."/yonetici-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/yonetici-listele.html");
		exit();
	}
}

##Yönetici Toplu Sil ##
if(isset($_POST['yonetici_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("yonetici_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM kullanici WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM kullanici WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Yönetici Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkviolet;'>".$resim_bul['isim']."</strong> isimli yöneticiyi sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".yonetim."/images/users/".$resim_bul['resim']);
					$_SESSION['yonetici_tumu'] = 'yes';
					header("Location:../".yonetim."/yonetici-listele.html");
				}
				else
				{
					$_SESSION['yonetici_tumu'] = 'no';
					header("Location:../".yonetim."/yonetici-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/yonetici-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/yonetici-listele.html");
		exit();
	}	
}

##Top Menü Kaydet ##
if(isset($_POST['TOPmenuKaydet']))
{
	cVCLmHLxbS_panelislemkontrol("TOPmenuKaydet");
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= $_POST['menu_sira'];
		$menu_ust 		= $_POST['menu_ust'];
		$menu_isim 		= $_POST['menu_isim'];
		$menu_url 		= $_POST['menu_url'];
		$link	 		= $_POST['link'];
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		$menu_durum 	= $_POST['menu_durum'];
		
		$sorgu = $db->prepare("INSERT INTO topmenu SET
			menu_sira 	= ?,
			menu_ust 	= ?,
			menu_isim 	= ?,
			menu_url 	= ?,
			link 		= ?,
			sekme 		= ?,
			dil 		= ?,
			menu_durum 	= ?");
		$Ekle = $sorgu->execute(array(
			$menu_sira,
			"0",
			$menu_isim,
			$menu_url,
			$link,
			$sekme,
			$_SESSION['admin_dil'],
			$menu_durum
		));

		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Top Menü",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_isim."</strong> adında siteye top menü ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['TOPmenuKaydet'] = 'yes';
			header("Location:../".yonetim."/top-menu.html");
			exit();
		}
		else
		{
			$_SESSION['TOPmenuKaydet'] = 'no';
			header("Location:../".yonetim."/top-menu.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/top-menu.html");
		exit();
	}
}

##Top Menü Güncelle ##
if(isset($_POST['TOPmenuGuncelle']))
{
	cVCLmHLxbS_panelislemkontrol("TOPmenuGuncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= $_POST['menu_sira'];
		$menu_ust 		= $_POST['menu_ust'];
		$menu_isim 		= $_POST['menu_isim'];
		$menu_url 		= $_POST['menu_url'];
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		if($_POST['menu_url'] != "0")
		{
			$link = " ";
		}
		if($_POST['menu_url'] == "0")
		{
			$link	= $_POST['link'];
		}
		$menu_durum 	= $_POST['menu_durum'];
		
		
		$sorgu = $db->prepare("UPDATE topmenu SET
				menu_sira 	= ?,
				menu_ust 	= ?,
				menu_isim 	= ?,
				menu_url 	= ?,
				link 		= ?,
				sekme 		= ?,
				menu_durum 	= ?
				WHERE id = ?");
		$guncelle = $sorgu->execute(array(
				$menu_sira,
				"0",
				$menu_isim,
				$menu_url,
				$link,
				$sekme,
				$menu_durum,
				$d_id
			));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Top Menü Güncellendi",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_isim."</strong> başlıklı top menüyü güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['TOPmenuGuncelle'] = 'yes';		
			header("Location:../".yonetim."/top-menu-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['TOPmenuGuncelle'] = 'no';
			header("Location:../".yonetim."/top-menu-duzenle/".$d_id.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/top-menu-duzenle/".$d_id.".html");
		exit();
	}
}

##Top Menü Sil##
if(@$_GET['TOPmenusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("TOPmenusil");
	$id = $_GET['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$Orta_menu_bul	= $db->query("SELECT * FROM topmenu WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		$Orta_menu_sorgu 	= $db->prepare("DELETE FROM topmenu WHERE id = :id");
		$Orta_menu_sil 	= $Orta_menu_sorgu->execute(array('id' => $id));
		if($Orta_menu_sorgu->rowCount())
		{
			if($Orta_menu_sil)
			{
				$TopluSorgu = $db->prepare("SELECT * FROM topmenu WHERE menu_ust = ?");
				$TopluSorgu->execute(array($_GET['id']));
				$Topluislem = $TopluSorgu->fetchALL(PDO::FETCH_ASSOC);
				foreach ( $Topluislem as $TopluSonuc )
				{
					$last_id 		= $TopluSonuc['id'];
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Top Menü Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$TopluSonuc['menu_isim']."</strong> başlıklı top menüyü sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$TSorgu = $db->prepare("DELETE FROM topmenu WHERE id = :id");
					$TSorgu->execute(array('id' => $TopluSonuc['id']));
				}
				$last_id 		= $id ;
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Top Menü Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_bul['menu_isim']."</strong> başlıklı top menüyü sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['TOPmenusil'] = 'yes';		
				header("Location:../".yonetim."/top-menu.html");
				exit();
			}
			else
			{
				$_SESSION['TOPmenusil'] = 'no';		
				header("Location:../".yonetim."/top-menu.html");
				exit();
			}
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/top-menu.html");
		exit();
	}
}

##Header Menü Kaydet ##
if(isset($_POST['MenuKaydet']))
{
	cVCLmHLxbS_panelislemkontrol("MenuKaydet");
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= !empty($_POST['menu_sira']) ? intval($_POST['menu_sira']) : 0;
		$menu_ust 		= $_POST['menu_ust'];
		$menu_isim 		= $_POST['menu_isim'];
		$menu_icon 		= !empty($_POST['menu_icon']) ? trim($_POST['menu_icon']) : null;
		$tip 			= $_POST['tip'];
		$tipkat 			= $_POST['tipkat'];
		$kategori 		= $_POST['kategori'];
		$menu_url 		= $_POST['menu_url'];
		$tbuton 			= $_POST['tbuton'];
		if($_POST['klimit']){$klimit = $_POST['klimit'];}else{$klimit = null;}
		if($_POST['ilimit']){$ilimit = $_POST['ilimit'];}else{$ilimit = null;}
		$link	 		= $_POST['link'];
		if(!empty($_POST['sekme'])){$sekme = 1;}else{$sekme = 0;}
		$menu_durum 	= $_POST['menu_durum'];
		
		$menu_sorgu = $db->prepare("INSERT INTO menu SET
			menu_sira 	= ?,
			menu_ust 	= ?,
			menu_isim 	= ?,
			menu_icon 	= ?,
			menu_url 	= ?,
			tbuton 		= ?,
			tip 		= ?,
			tipkat 		= ?,
			kategori 	= ?,
			klimit 		= ?,
			ilimit 		= ?,
			link 		= ?,
			sekme 		= ?,
			dil 		= ?,
			menu_durum 	= ?");
		$menu_ekle = $menu_sorgu->execute(array(
			$menu_sira,
			$menu_ust,
			$menu_isim,
			$menu_icon,
			$menu_url,
			$tbuton,
			$tip,
			$tipkat,
			$kategori,
			$klimit,
			$ilimit,
			$link,
			$sekme,
			$_SESSION['admin_dil'],
			$menu_durum
		));

		if($menu_ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Header Menü",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_isim."</strong> adında siteye üst menü ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['MenuKaydet'] = 'yes';
			header("Location:../".yonetim."/header-menu.html");
			exit();
		}
		else
		{
			$_SESSION['MenuKaydet'] = 'no';
			header("Location:../".yonetim."/header-menu.html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/header-menu.html");
		exit();
	}
}

##Header Menü Güncelle ##
if(isset($_POST['MenuGuncelle']))
{
	cVCLmHLxbS_panelislemkontrol("MenuGuncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= $_POST['menu_sira'];
		$menu_ust 		= $_POST['menu_ust'];
		$menu_isim 		= $_POST['menu_isim'];
		$menu_icon 		= !empty($_POST['menu_icon']) ? trim($_POST['menu_icon']) : null;
		$menu_url 		= $_POST['menu_url'];	
		$tbuton 			= $_POST['tbuton'];	
		if($_POST['klimit']){$klimit = $_POST['klimit'];}else{$klimit = null;}
		if($_POST['ilimit']){$ilimit = $_POST['ilimit'];}else{$ilimit = null;}
		$tip 			= $_POST['tip'];
		$tipkat 			= $_POST['tipkat'];
		$kategori 		= $_POST['kategori'];		
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		if($_POST['menu_url'] != "0")
		{
			$link = " ";
		}
		if($_POST['menu_url'] == "0")
		{
			$link	= $_POST['link'];
		}
		$menu_durum 	= $_POST['menu_durum'];
		
		$menu_sorgu = $db->prepare("UPDATE menu SET
				menu_sira 	= ?,
				menu_ust 	= ?,
				menu_isim 	= ?,
				menu_icon 	= ?,
				menu_url 	= ?,
				tbuton 		= ?,
				tip 		= ?,
				tipkat 		= ?,
				kategori 	= ?,
				klimit 		= ?,
				ilimit 		= ?,
				link 		= ?,
				sekme 		= ?,
				menu_durum 	= ?
				WHERE id = ?");
		$menu_guncelle = $menu_sorgu->execute(array(
				$menu_sira,
				$menu_ust,
				$menu_isim,
				$menu_icon,
				$menu_url,
				$tbuton,
				$tip,
				$tipkat,
				$kategori,
				$klimit,
				$ilimit,
				$link,
				$sekme,
				$menu_durum,
				$d_id
			));
		if($menu_guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Üst Menü Güncellendi",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_isim."</strong> başlıklı üst menüyü güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['MenuGuncelle'] = 'yes';		
			header("Location:../".yonetim."/header-menu-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['MenuGuncelle'] = 'no';
			header("Location:../".yonetim."/header-menu-duzenle/".$d_id.".html");
			exit();
		}			

	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/header-menu-duzenle/".$d_id.".html");
		exit();
	}
}

##Header Menü Sil##
if(@$_GET['menusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("menusil");
	$header_menu_id = $_GET['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$header_bul		= $db->query("SELECT * FROM menu WHERE id = '{$header_menu_id}'")->fetch(PDO::FETCH_ASSOC);
		$HeaderSorgu 	= $db->prepare("DELETE FROM menu WHERE id = :id");
		$HeaderSil 		= $HeaderSorgu->execute(array('id' => $header_menu_id));
		if($HeaderSorgu->rowCount())
		{
			$HTopluSorgu = $db->prepare("SELECT * FROM menu WHERE menu_ust = ?");
			$HTopluSorgu->execute(array($_GET['id']));
			$HTopluislem = $HTopluSorgu->fetchALL(PDO::FETCH_ASSOC);
			foreach ( $HTopluislem as $HTopluSonuc )
			{
				$last_id 		= $HTopluSonuc['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Üst Menü Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$HTopluSonuc['menu_isim']."</strong> başlıklı üst menüyü sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$TSorgu = $db->prepare("DELETE FROM menu WHERE id = :id");
				$TSorgu->execute(array('id' => $HTopluSonuc['id']));
			}
			$last_id 		= $header_menu_id ;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Üst Menü Silindi",
				'icon' 		=> "icon-trash",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$header_bul['menu_isim']."</strong> başlıklı üst menüyü sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['menusil'] = 'yes';		
			header("Location:../".yonetim."/header-menu.html");
			exit();
		}
		else
		{
			$_SESSION['menusil'] = 'no';		
			header("Location:../".yonetim."/header-menu.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/header-menu.html");
		exit();
	}
}

##Genel Ayarlar ##
if(isset($_POST['genel_ayarlar']))
{
	cVCLmHLxbS_panelislemkontrol("genel_ayarlar");
	if($_SESSION['rutbe'] == 0)
	{
		$site_title		= $_POST['site_title'];
		$site_url 		= $_POST['site_url'];
		$havadurumu 	= $_POST['havadurumu'];
		$yonetim 		= cVCLmHLxbS_seo($_POST['yonetim']);
		$site_keyw 		= $_POST['site_keyw'];
		$site_desc 		= $_POST['site_desc'];
		$copyright 		= $_POST['copyright'];
		$kodlar 		= $_POST['ekstra'];
		$renk1 			= $_POST['renk1'];
		$renk2 			= $_POST['renk2'];
		$renk3 			= $_POST['renk3'];
		$instagramtoken	= $_POST['instagramtoken'];
		$instagram_username = isset($_POST['instagram_username']) ? trim($_POST['instagram_username']) : '';
		$gemini_api_key = $_POST['gemini_api_key'];

		
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/logo");
			if ($upload->processed)
			{
				$firmalogo=''.$upload->file_dst_name.'';
			}
		}
		
		$upload2 = new upload($_FILES['footer']);
		if ($upload2->uploaded)
		{
			$upload2->file_auto_rename = true;
			$upload2->process("../".tema."/uploads/logo/footer");
			if ($upload2->processed)
			{
				$footerlogo=''.$upload2->file_dst_name.'';
			}
		}

		$upload = new upload($_FILES['favicon']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/favicon");
			if ($upload->processed)
			{
				$favicon=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($firmalogo)){
			$resim_bul= $db->query("SELECT * FROM ayarlar WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/logo/".$resim_bul['firma_logo']);
			$guncelle = $db->prepare("UPDATE ayarlar SET firma_logo = ? WHERE id = ?");
			$guncelle->execute([$firmalogo,1]);
			$firmalogo=''.$upload->file_dst_name.'';
		}
		
		if(isset($footerlogo)){
			$resim_bul= $db->query("SELECT * FROM ayarlar WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/logo/footer/".$resim_bul['firma_footerlogo']);
			$guncelle = $db->prepare("UPDATE ayarlar SET firma_footerlogo = ? WHERE id = ?");
			$guncelle->execute([$footerlogo,1]);
			$footerlogo=''.$upload2->file_dst_name.'';
		}
		
		if(isset($favicon)){
			$resim_bul= $db->query("SELECT * FROM ayarlar WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/favicon/".$resim_bul['favicon']);
			$guncelle = $db->prepare("UPDATE ayarlar SET favicon = ? WHERE id = ?");
			$guncelle->execute([$favicon,1]);
			$favicon=''.$upload->file_dst_name.'';
		}	
		
		if(isset($yonetim)){
			rename('../'.yonetim.'', '../'.$yonetim.'');
		}	
		
		if(isset($havadurumu)){
			unlink('../havadurumu.txt');
			$HSorgu = $db->prepare("TRUNCATE TABLE havadurumu");
			$Hsil_sorgu = $HSorgu->execute();
		}	
		
		$sorgu = $db->prepare("UPDATE ayarlar SET
			site_baslik	= ?,
			site_url 	= ?,
			havadurumu 	= ?,
			yonetim 	= ?,
			site_keyw	= ?,
			site_desc	= ?,
			renk1		= ?,
			renk2		= ?,
			renk3		= ?,
			instagramtoken = ?,
			instagram_username = ?,
			gemini_api_key = ?,
			copyright	= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$site_title,
			$site_url,
			$havadurumu,
			$yonetim,
			$site_keyw,
			$site_desc,
			$renk1,
			$renk2,
			$renk3,
			$instagramtoken,
			$instagram_username,
			$gemini_api_key,
			$copyright,
			"1"
		));
		
		
		if($guncelle)
		{
			$panel_bul= $db->query("SELECT * FROM ayarlar WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			
			
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Genel Ayarlar Güncellendi",
				'icon' 		=> "icon-settings",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> genel ayarları güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['genel_ayarlar'] = 'yes';
			header("Location:../".$panel_bul['yonetim']."/genel-ayarlar.html");
			exit();
		}
		else
		{
			$_SESSION['genel_ayarlar'] = 'no';
			header("Location:../".yonetim."/genel-ayarlar.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/genel-ayarlar.html");
		exit();
	}
}

##Başkan Ayarlar ##
if(isset($_POST['baskan_ayarlar']))
{
	cVCLmHLxbS_panelislemkontrol("baskan_ayarlar");
	if($_SESSION['rutbe'] == 0)
	{
		$adi		= $_POST['adi'];
		$slogan 		= $_POST['slogan'];
		$facebook 	= $_POST['facebook'];
		$twitter 	= $_POST['twitter'];
		$instagram 	= $_POST['instagram'];
		$linkedin 	= $_POST['linkedin'];
		$youtube 	= $_POST['youtube'];
		
		
		$upload = new upload($_FILES['gorsel']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/baskan");
			if ($upload->processed)
			{
				$gorsel=''.$upload->file_dst_name.'';
			}
		}
		
		$upload2 = new upload($_FILES['gorsel2']);
		if ($upload2->uploaded)
		{
			$upload2->file_auto_rename = true;
			$upload2->process("../".tema."/uploads/baskan");
			if ($upload2->processed)
			{
				$gorsel2=''.$upload2->file_dst_name.'';
			}
		}
		
		if(isset($gorsel)){
			$resim_bul= $db->query("SELECT * FROM baskan_ayarlar WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/baskan/".$resim_bul['gorsel']);
			$guncelle = $db->prepare("UPDATE baskan_ayarlar SET gorsel = ? WHERE id = ?");
			$guncelle->execute([$gorsel,1]);
			$gorsel=''.$upload->file_dst_name.'';
		}
		
		if(isset($gorsel2)){
			$resim_bul= $db->query("SELECT * FROM baskan_ayarlar WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/baskan/".$resim_bul['gorsel2']);
			$guncelle = $db->prepare("UPDATE baskan_ayarlar SET gorsel2 = ? WHERE id = ?");
			$guncelle->execute([$gorsel2,1]);
			$gorsel2=''.$upload2->file_dst_name.'';
		}
				
		$sorgu = $db->prepare("UPDATE baskan_ayarlar SET
			adi			= ?,
			slogan 		= ?,
			facebook 	= ?,
			twitter		= ?,
			instagram	= ?,
			linkedin	= ?,
			youtube		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$adi,
			$slogan,
			$facebook,
			$twitter,
			$instagram,			
			$linkedin,			
			$youtube,			
			"1"
		));
		if($guncelle)
		{
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Başkan Ayarları Güncellendi",
				'icon' 		=> "icon-settings",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> başkan ayarlarını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['baskan_ayarlar'] = 'yes';
			header("Location:../".yonetim."/baskan-ayarlar.html");
			exit();
		}
		else
		{
			$_SESSION['baskan_ayarlar'] = 'no';
			header("Location:../".yonetim."/baskan-ayarlar.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/baskan-ayarlar.html");
		exit();
	}
}

##Popup Ayarlar ##
if(isset($_POST['popup_ayarlar']))
{
	cVCLmHLxbS_panelislemkontrol("popup_ayarlar");
	if($_SESSION['rutbe'] == 0)
	{
		$adi		= $_POST['adi'];
		$url 		= $_POST['url'];
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}	
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}	

		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/popup");
			if ($upload->processed)
			{
				$resim =''.$upload->file_dst_name.'';
			}
		}		
		
		if(isset($resim)){
			$resim_bul= $db->query("SELECT * FROM popup_ayarlar WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/popup/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE popup_ayarlar SET resim = ? WHERE id = ?");
			$guncelle->execute([$resim,1]);
			$resim=''.$upload->file_dst_name.'';
		}
	
		$sorgu = $db->prepare("UPDATE popup_ayarlar SET
			adi			= ?,
			url 		= ?,
			sekme 		= ?,
			durum		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$adi,
			$url,			
			$sekme,			
			$durum,
			"1"
		));
		if($guncelle)
		{
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Popup Ayarlar Güncellendi",
				'icon' 		=> "icon-settings",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> açılır mesajı güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['popup_ayarlar'] = 'yes';
			header("Location:../".yonetim."/popup.html");
			exit();
		}
		else
		{
			$_SESSION['popup_ayarlar'] = 'no';
			header("Location:../".yonetim."/popup.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/popup.html");
		exit();
	}
}

##sabit_url Ayarlar ##
if(isset($_POST['sabit_url']))
{
	cVCLmHLxbS_panelislemkontrol("sabit_url");
	if($_SESSION['rutbe'] == 0)
	{
		$anaurl				= cVCLmHLxbS_seo($_POST['anaurl']);
		$haberkategoriurl	= cVCLmHLxbS_seo($_POST['haberkategoriurl']);
		$haberurl 			= cVCLmHLxbS_seo($_POST['haberurl']);
		$haberdetayurl 		= cVCLmHLxbS_seo($_POST['haberdetayurl']);	
		$projekategoriurl	= cVCLmHLxbS_seo($_POST['projekategoriurl']);
		$projelerurl		= cVCLmHLxbS_seo($_POST['projelerurl']);
		$projedetayurl 		= cVCLmHLxbS_seo($_POST['projedetayurl']);		
		$fotourl 			= cVCLmHLxbS_seo($_POST['fotourl']);
		$fotodetayurl 		= cVCLmHLxbS_seo($_POST['fotodetayurl']);		
		$hizmeturl 			= cVCLmHLxbS_seo($_POST['hizmeturl']);
		$hizmetdetayurl		= cVCLmHLxbS_seo($_POST['hizmetdetayurl']);
		$birimurl 			= cVCLmHLxbS_seo($_POST['birimurl']);
		$birimdetayurl		= cVCLmHLxbS_seo($_POST['birimdetayurl']);
		$sayfaurl 			= cVCLmHLxbS_seo($_POST['sayfaurl']);
		$videourl 			= cVCLmHLxbS_seo($_POST['videourl']);		
		$videodetayurl 		= cVCLmHLxbS_seo($_POST['videodetayurl']);	
		$etkinlikurl 		= cVCLmHLxbS_seo($_POST['etkinlikurl']);		
		$etkinlikdetayurl 	= cVCLmHLxbS_seo($_POST['etkinlikdetayurl']);
		$duyuruurl 			= cVCLmHLxbS_seo($_POST['duyuruurl']);		
		$duyurudetayurl 		= cVCLmHLxbS_seo($_POST['duyurudetayurl']);	
		$ihaleurl 			= cVCLmHLxbS_seo($_POST['ihaleurl']);		
		$ihaledetayurl 		= cVCLmHLxbS_seo($_POST['ihaledetayurl']);
		$ilanurl 			= cVCLmHLxbS_seo($_POST['ilanurl']);		
		$ilandetayurl 		= cVCLmHLxbS_seo($_POST['ilandetayurl']);			
		$kararurl 			= cVCLmHLxbS_seo($_POST['kararurl']);		
		$karardetayurl 		= cVCLmHLxbS_seo($_POST['karardetayurl']);
		$profilkategoriurl	= cVCLmHLxbS_seo($_POST['profilkategoriurl']);
		$profillerurl		= cVCLmHLxbS_seo($_POST['profillerurl']);
		$profildetayurl 		= cVCLmHLxbS_seo($_POST['profildetayurl']);
		$faaliyeturl		= cVCLmHLxbS_seo($_POST['faaliyeturl']);
		$faaliyetdetayurl 	= cVCLmHLxbS_seo($_POST['faaliyetdetayurl']);	
		$iletisimurl 		= cVCLmHLxbS_seo($_POST['iletisimurl']);
		$bagisurl 			= cVCLmHLxbS_seo($_POST['bagisurl']);
		$bagissepeturl 		= cVCLmHLxbS_seo($_POST['bagissepeturl']);
		$bagisodemeurl 		= cVCLmHLxbS_seo($_POST['bagisodemeurl']);
		$bagissonucurl 		= cVCLmHLxbS_seo($_POST['bagissonucurl']);
		$aidaturl 			= cVCLmHLxbS_seo($_POST['aidaturl']);
		$aidatlisteurl 		= cVCLmHLxbS_seo($_POST['aidatlisteurl']);
		$aidatodemeurl 		= cVCLmHLxbS_seo($_POST['aidatodemeurl']);
		$aidatsonucurl 		= cVCLmHLxbS_seo($_POST['aidatsonucurl']);
		$programlarurl 		= cVCLmHLxbS_seo($_POST['programlarurl']);
		$programdetayurl 	= cVCLmHLxbS_seo($_POST['programdetayurl']);
		$bagismodulurl 		= cVCLmHLxbS_seo($_POST['bagismodulurl']);
		$bagismoduldetayurl = cVCLmHLxbS_seo($_POST['bagismoduldetayurl']);
		$randevuurl 		= cVCLmHLxbS_seo($_POST['randevuurl']);
		$hesapnumaralarimizurl = cVCLmHLxbS_seo($_POST['hesapnumaralarimizurl']);
		$derslerurl			 = cVCLmHLxbS_seo($_POST['derslerurl']);

		
		$durum 				= $_POST['durum'];
		
		
		$etkierisimurl 	= cVCLmHLxbS_seo($_POST['etkierisimurl']);
		$okullarurl 		= cVCLmHLxbS_seo($_POST['okullarurl']);
		$ogrenme_deneyimiurl = cVCLmHLxbS_seo($_POST['ogrenme_deneyimiurl']);
		$ogrenme_deneyimi_detayurl = cVCLmHLxbS_seo($_POST['ogrenme_deneyimi_detayurl']);
		$destekleme_yollariurl = cVCLmHLxbS_seo($_POST['destekleme_yollariurl']);
		$destekleme_yollari_detayurl = cVCLmHLxbS_seo($_POST['destekleme_yollari_detayurl']);
		$karakterprogramlariurl = cVCLmHLxbS_seo($_POST['karakterprogramlariurl']);
		$karakterprogramlari_text = $_POST['karakterprogramlari_text'];

		$sorgu = $db->prepare("UPDATE sabit_url SET
			anaurl			= ?,
			haberkategoriurl= ?,
			haberurl 		= ?,
			haberdetayurl 	= ?,
			projekategoriurl= ?,
			projelerurl		= ?,
			projedetayurl	= ?,
			fotourl			= ?,
			fotodetayurl 	= ?,
			hizmeturl		= ?,
			hizmetdetayurl	= ?,
			birimurl		= ?,
			birimdetayurl	= ?,
			sayfaurl		= ?,
			videourl		= ?,
			videodetayurl 	= ?,
			etkinlikurl 	= ?,
			etkinlikdetayurl= ?,
			duyuruurl 		= ?,	
			duyurudetayurl 	= ?,
			ihaleurl 		= ?,	
			ihaledetayurl 	= ?,
			ilanurl 		= ?,	
			ilandetayurl 	= ?,
			kararurl 		= ?,
			karardetayurl 	= ?,
			profilkategoriurl= ?,
			profillerurl	= ?,
			profildetayurl 	= ?,
			faaliyeturl		= ?,
			faaliyetdetayurl= ?,
			iletisimurl		= ?,
			bagisurl		= ?,
			bagissepeturl	= ?,
			bagisodemeurl	= ?,
			bagissonucurl	= ?,
			aidaturl		= ?,
			aidatlisteurl	= ?,
			aidatodemeurl	= ?,
			aidatsonucurl	= ?,
			programlarurl	= ?,
			programdetayurl	= ?,
			bagismodulurl	= ?,
			bagismoduldetayurl= ?,
			randevuurl		= ?,
			hesapnumaralarimizurl= ?,
			derslerurl= ?,
			etkierisimurl 	= ?,
			okullarurl		= ?,
			ogrenme_deneyimiurl = ?,
			ogrenme_deneyimi_detayurl = ?,
			destekleme_yollariurl = ?,
			destekleme_yollari_detayurl = ?,
			karakterprogramlariurl = ?,
			karakterprogramlari_text = ?,
			durum			= ?
			WHERE id 		= ?");
		$guncelle = $sorgu->execute(array(
			$anaurl,
			$haberkategoriurl,
			$haberurl,
			$haberdetayurl,
			$projekategoriurl,						
			$projelerurl,						
			$projedetayurl,
			$fotourl,
			$fotodetayurl,	
			$hizmeturl,			
			$hizmetdetayurl,
			$birimurl,			
			$birimdetayurl,
			$sayfaurl,
			$videourl,
			$videodetayurl,			 
			$etkinlikurl,			
			$etkinlikdetayurl,
			$duyuruurl,	
			$duyurudetayurl,
			$ihaleurl,	
			$ihaledetayurl,	
			$ilanurl,	
			$ilandetayurl,			
			$kararurl,				
			$karardetayurl,	
			$profilkategoriurl,
			$profillerurl,
			$profildetayurl,
			$faaliyeturl,
			$faaliyetdetayurl,
			$iletisimurl,					
			$bagisurl,					
			$bagissepeturl,					
			$bagisodemeurl,					
			$bagissonucurl,					
			$aidaturl,					
			$aidatlisteurl,					
			$aidatodemeurl,					
			$aidatsonucurl,					
			$programlarurl,					
			$programdetayurl,
			$bagismodulurl,
			$bagismoduldetayurl,
			$randevuurl,
			$hesapnumaralarimizurl,
			$derslerurl,
			$etkierisimurl,
			$okullarurl,
			$ogrenme_deneyimiurl,
			$ogrenme_deneyimi_detayurl,
			$destekleme_yollariurl,
			$destekleme_yollari_detayurl,
			$karakterprogramlariurl,
			$karakterprogramlari_text,
			$durum,
			"1"
		));
		if($guncelle)
		{
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Link Ayarları Güncellendi",
				'icon' 		=> "icon-settings",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> link ayarlarını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['sabit_url'] = 'yes';
			header("Location:../".yonetim."/sabit-linkler.html");
			exit();
		}
		else
		{
			$_SESSION['sabit_url'] = 'no';
			header("Location:../".yonetim."/sabit-linkler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sabit-linkler.html");
		exit();
	}
}

##Api Ayarlar ##
if(isset($_POST['api_ayarlar']))
{
	cVCLmHLxbS_panelislemkontrol("api_ayarlar");
	if($_SESSION['rutbe'] == 0)
	{
		$google_analytics	= $_POST['google_analytics'];
		$dogrulama_kodu 		= $_POST['dogrulama_kodu'];
		$google_maps 		= $_POST['google_maps'];
		$canli_destek 		= $_POST['canli_destek'];
		$whatsapp 			= $_POST['whatsapp'];
		
		$sorgu = $db->prepare("UPDATE ayarlar SET
			google_analytics= ?,
			dogrulama_kodu 	= ?,
			google_maps		= ?,
			whatsapp			= ?,
			canli_destek	= ?
			WHERE id 		= ?");
		$guncelle = $sorgu->execute(array(
			$google_analytics,
			$dogrulama_kodu,
			$google_maps,
			$whatsapp,
			$canli_destek,			
			"1"
		));
		if($guncelle)
		{
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Api Ayarları Güncellendi",
				'icon' 		=> "icon-settings",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> api ayarlarını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['api_ayarlar'] = 'yes';
			header("Location:../".yonetim."/api-ayarlari.html");
			exit();
		}
		else
		{
			$_SESSION['api_ayarlar'] = 'no';
			header("Location:../".yonetim."/api-ayarlari.html");
			exit();
		}	
		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/api-ayarlari.html");
		exit();
	}
}

##İletişim Ayarlar ##
if(isset($_POST['iletisim_ayarlar']))
{
	cVCLmHLxbS_panelislemkontrol("iletisim_ayarlar");
	if($_SESSION['rutbe'] == 0)
	{
		$firma_adi		= $_POST['firma_adi'];
		$firma_telefon 	= $_POST['firma_telefon'];
		$firma_fax 		= $_POST['firma_fax'];
		$firma_email 	= $_POST['firma_email'];
		$firma_adres 	= $_POST['firma_adres'];
		
		$sorgu = $db->prepare("UPDATE ayarlar SET
			firma_adi		= ?,
			firma_telefon 	= ?,
			firma_fax		= ?,
			firma_email		= ?,
			firma_adres		= ?
			WHERE id 		= ?");
		$guncelle = $sorgu->execute(array(
			$firma_adi,
			$firma_telefon,
			$firma_fax,
			$firma_email,			
			$firma_adres,			
			"1"
		));
		if($guncelle)
		{
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "İletişim Ayarları Güncellendi",
				'icon' 		=> "icon-settings",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> iletişim ayarlarını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['iletisim_ayarlar'] = 'yes';
			header("Location:../".yonetim."/iletisim-ayarlari.html");
			exit();
		}
		else
		{
			$_SESSION['iletisim_ayarlar'] = 'no';
			header("Location:../".yonetim."/iletisim-ayarlari.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/iletisim-ayarlari.html");
		exit();
	}
}

##Sosyal Medya Ayarları ##
if(isset($_POST['sosyal_ayarlar']))
{
	cVCLmHLxbS_panelislemkontrol("sosyal_ayarlar");
	if($_SESSION['rutbe'] == 0)
	{
		$facebook	= $_POST['facebook'];
		$twitter 	= $_POST['twitter'];
		$instagram 	= $_POST['instagram'];
		$linkedin 	= $_POST['linkedin'];
		$youtube 	= $_POST['youtube'];
		
		$sorgu = $db->prepare("UPDATE ayarlar SET
			facebook	= ?,
			twitter 	= ?,
			instagram	= ?,
			linkedin	= ?,
			youtube		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$facebook,
			$twitter,
			$instagram,			
			$linkedin,			
			$youtube,			
			"1"
		));
		if($guncelle)
		{
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Sosyal Medya Ayarları Güncellendi",
				'icon' 		=> "icon-settings",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> sosyal medya ayarlarını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['sosyal_ayarlar'] = 'yes';
			header("Location:../".yonetim."/sosyal-medya-ayarlari.html");
			exit();
		}
		else
		{
			$_SESSION['sosyal_ayarlar'] = 'no';
			header("Location:../".yonetim."/sosyal-medya-ayarlari.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sosyal-medya-ayarlari.html");
		exit();
	}
}

##Limit Güncelle ##
if(isset($_POST['limit_ayarlar']))
{
	cVCLmHLxbS_panelislemkontrol("limit_ayarlar");
	if($_SESSION['rutbe'] == 0)
	{
		$limit_birim			= $_POST['limit_birim'];
		$limit_duyuru			= $_POST['limit_duyuru'];
		$limit_etkinlik			= $_POST['limit_etkinlik'];
		$limit_faaliyet			= $_POST['limit_faaliyet'];
		$limit_haber			= $_POST['limit_haber'];
		$limit_haberler			= $_POST['limit_haberler'];
		$limit_projeler			= $_POST['limit_projeler'];
		$limit_proje			= $_POST['limit_proje'];
		$limit_foto				= $_POST['limit_foto'];
		$limit_video			= $_POST['limit_video'];
		$limit_hizmet			= $_POST['limit_hizmet'];
		$limit_ihale			= $_POST['limit_ihale'];
		$limit_ilan				= $_POST['limit_ilan'];
		$limit_karar			= $_POST['limit_karar'];
		$limit_profil			= $_POST['limit_profil'];
		$limit_profiller		= $_POST['limit_profiller'];
		$limit_anasayfa_haber	= $_POST['limit_anasayfa_haber'];
		$limit_sayfabirim		= $_POST['limit_sayfabirim'];
		$limit_sayfaduyuru		= $_POST['limit_sayfaduyuru'];
		$limit_sayfaetkinlik	= $_POST['limit_sayfaetkinlik'];
		$limit_sayfafaaliyet	= $_POST['limit_sayfafaaliyet'];
		$limit_sayfahaber		= $_POST['limit_sayfahaber'];
		$limit_sayfahaberler	= $_POST['limit_sayfahaberler'];
		$limit_sayfaprojeler	= $_POST['limit_sayfaprojeler'];
		$limit_sayfaproje		= $_POST['limit_sayfaproje'];
		$limit_sayfafoto		= $_POST['limit_sayfafoto'];
		$limit_sayfavideo		= $_POST['limit_sayfavideo'];
		$limit_sayfahaber		= $_POST['limit_sayfahaber'];
		$limit_sayfahizmetler	= $_POST['limit_sayfahizmetler'];
		$limit_sayfaihale		= $_POST['limit_sayfaihale'];
		$limit_sayfailan		= $_POST['limit_sayfailan'];
		$limit_sayfakarar		= $_POST['limit_sayfakarar'];
		$limit_sayfaprofil		= $_POST['limit_sayfaprofil'];
		$limit_sayfaprofiller	= $_POST['limit_sayfaprofiller'];
		$limit_sayfaanasayfa_haber	= $_POST['limit_sayfaanasayfa_haber'];
		$limit_sayfaslider_haber= $_POST['limit_sayfaslider_haber'];
		
		$sorgu = $db->prepare("UPDATE limit_ayarlari SET
			limit_birim			= ?,
			limit_duyuru		= ?,
			limit_etkinlik		= ?,
			limit_faaliyet		= ?,
			limit_haber			= ?,
			limit_haberler		= ?,
			limit_projeler		= ?,
			limit_proje			= ?,			
			limit_foto			= ?,
			limit_video			= ?,
			limit_hizmet		= ?,
			limit_ihale			= ?,
			limit_ilan			= ?,
			limit_karar			= ?,
			limit_profil		= ?,
			limit_profiller		= ?,
			limit_anasayfa_haber= ?,
			limit_sayfabirim	= ?,
			limit_sayfaduyuru	= ?,
			limit_sayfaetkinlik	= ?,
			limit_sayfafaaliyet	= ?,
			limit_sayfahaber	= ?,
			limit_sayfahaberler	= ?,
			limit_sayfaproje	= ?,
			limit_sayfaprojeler	= ?,
			limit_sayfafoto		= ?,
			limit_sayfavideo	= ?,
			limit_sayfahizmetler= ?,
			limit_sayfaihale	= ?,
			limit_sayfailan		= ?,
			limit_sayfakarar	= ?,
			limit_sayfaprofil	= ?,
			limit_sayfaprofiller= ?,
			limit_sayfaanasayfa_haber= ?,
			limit_sayfaslider_haber= ?
			WHERE id 			= ?");
		$guncelle = $sorgu->execute(array(
			$limit_birim,
			$limit_duyuru,
			$limit_etkinlik,
			$limit_faaliyet,
			$limit_haber,
			$limit_haberler,
			$limit_projeler,
			$limit_proje,			
			$limit_foto,
			$limit_video,
			$limit_hizmet,
			$limit_ihale,
			$limit_ilan,
			$limit_karar,
			$limit_profil,
			$limit_profiller,
			$limit_anasayfa_haber,
			$limit_sayfabirim,
			$limit_sayfaduyuru,
			$limit_sayfaetkinlik,
			$limit_sayfafaaliyet,
			$limit_sayfahaber,
			$limit_sayfahaberler,
			$limit_sayfaproje,
			$limit_sayfaprojeler,
			$limit_sayfafoto,
			$limit_sayfavideo,
			$limit_sayfahizmetler,
			$limit_sayfaihale,
			$limit_sayfailan,
			$limit_sayfakarar,
			$limit_sayfaprofil,
			$limit_sayfaprofiller,
			$limit_sayfaanasayfa_haber,
			$limit_sayfaslider_haber,
			"1"
		));
		if($guncelle)
		{
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Limit Ayarları Güncellendi",
				'icon' 		=> "icon-settings",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> limit ayarlarını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['limit_ayarlar'] = 'yes';
			header("Location:../".yonetim."/limit-ayarlari.html");
			exit();
		}
		else
		{
			$_SESSION['limit_ayarlar'] = 'no';
			header("Location:../".yonetim."/limit-ayarlari.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/limit-ayarlari.html");
		exit();
	}
}

##Mail Ayarları ##
if(isset($_POST['mail_ayarlar']))
{
	cVCLmHLxbS_panelislemkontrol("mail_ayarlar");
	if($_SESSION['rutbe'] == 0)
	{
		$m_server	= $_POST['m_server'];
		$m_adresi 	= $_POST['m_adresi'];
		$m_parola 	= $_POST['m_parola'];
		$m_port 		= $_POST['m_port'];
		$m_kime 		= $_POST['m_kime'];
		$m_sertifika = $_POST['m_sertifika'];
		$durum 		= $_POST['durum'];
		
		$sorgu = $db->prepare("UPDATE mail_ayar SET
			m_server	= ?,
			m_adresi 	= ?,
			m_parola	= ?,
			m_port		= ?,
			m_kime		= ?,
			m_sertifika	= ?,
			durum		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$m_server,
			$m_adresi,
			$m_parola,
			$m_port,			
			$m_kime,			
			$m_sertifika,			
			$durum,			
			"1"
		));
		if($guncelle)
		{
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Mail Ayarları Güncellendi",
				'icon' 		=> "icon-settings",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> mail ayarlarını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['mail_ayarlar'] = 'yes';
			header("Location:../".yonetim."/mail-ayarlari.html");
			exit();
		}
		else
		{
			$_SESSION['mail_ayarlar'] = 'no';
			header("Location:../".yonetim."/mail-ayarlari.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/mail-ayarlari.html");
		exit();
	}
}

##SMS Ayarları ##
if(isset($_POST['sms_ayarlar']))
{
	cVCLmHLxbS_panelislemkontrol("sms_ayarlar");
	if($_SESSION['rutbe'] == 0)
	{
		$postUrl		= $_POST['postUrl'];
		$KULLANICIADI 	= $_POST['KULLANICIADI'];
		$SIFRE 			= $_POST['SIFRE'];
		$ORGINATOR 		= $_POST['ORGINATOR'];
		$m_kime 			= $_POST['m_kime'];
		
		$sorgu = $db->prepare("UPDATE sms SET
			postUrl		= ?,
			KULLANICIADI= ?,
			SIFRE		= ?,
			m_kime		= ?,
			ORGINATOR	= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$postUrl,
			$KULLANICIADI,
			$SIFRE,			
			$m_kime,			
			$ORGINATOR,					
			"1"
		));
		if($guncelle)
		{
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "SMS Ayarları Güncellendi",
				'icon' 		=> "icon-settings",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> sms ayarlarını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['sms_ayarlar'] = 'yes';
			header("Location:../".yonetim."/sms-ayarlari.html");
			exit();
		}
		else
		{
			$_SESSION['sms_ayarlar'] = 'no';
			header("Location:../".yonetim."/sms-ayarlari.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sms-ayarlari.html");
		exit();
	}
}

##Sanal Pos Ayarları ##
if(isset($_POST['paytr_ayarlar']))
{
	cVCLmHLxbS_panelislemkontrol("paytr_ayarlar");
	if($_SESSION['rutbe'] == 0)
	{
		$magaza_no		= $_POST['magaza_no'];
		$magaza_parola 	= $_POST['magaza_parola'];
		$magaza_anahtar	= $_POST['magaza_anahtar'];
		$hata_mesaj		= $_POST['hata_mesaj'];
		$test_modu		= $_POST['test_modu'];
		$taksit			= $_POST['taksit'];
		$aktif_odeme_yontemi = isset($_POST['aktif_odeme_yontemi']) ? $_POST['aktif_odeme_yontemi'] : 'paytr';
		
		try {
             $cols = $db->query("SHOW COLUMNS FROM paytr LIKE 'aktif_odeme_yontemi'")->fetch();
             if(!$cols) $db->exec("ALTER TABLE paytr ADD COLUMN aktif_odeme_yontemi VARCHAR(50) DEFAULT 'paytr'");
        } catch(Exception $e) {}
		
		$sorgu = $db->prepare("UPDATE paytr SET
			magaza_no		= ?,
			aktif_odeme_yontemi = ?,
			magaza_parola 	= ?,
			hata_mesaj		= ?,
			test_modu		= ?,
			taksit			= ?,
			magaza_anahtar	= ?
			WHERE id 		= ?");
		$guncelle = $sorgu->execute(array(
			$magaza_no,
			$aktif_odeme_yontemi,
			$magaza_parola,			
			$hata_mesaj,			
			$test_modu,			
			$taksit,			
			$magaza_anahtar,			
			"1"
		));
		if($guncelle)
		{
			$last_id 		= "1";
			$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
			$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
			$bildirimkt 		= strtotime($kayitt);
			$bildirimt 		= strtotime($btarih);
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Sanal Pos Ayarları Güncellendi",
				'icon' 		=> "icon-credit-card",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> Sanalpos ayarlarını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['paytr_ayarlar'] = 'yes';
			header("Location:../".yonetim."/sanal-poslar.html");
			exit();
		}
		else
		{
			$_SESSION['paytr_ayarlar'] = 'no';
			header("Location:../".yonetim."/sanal-poslar.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sanal-poslar.html");
		exit();
	}
}

##Vakıfbank Sanal Pos Ayarları ##
if(isset($_POST['vakifbank_ayarlar']))
{
	cVCLmHLxbS_panelislemkontrol("vakifbank_ayarlar");
	if($_SESSION['rutbe'] == 0)
	{
		$host_merchant_id		= $_POST['vakifbank_host_merchant_id'];
		$user_name				= $_POST['vakifbank_user_name']; // Yeni alan
		$merchant_password 		= $_POST['vakifbank_merchant_password'];
		$host_terminal_id		= $_POST['vakifbank_host_terminal_id'];
		$gateway_url				= trim($_POST['vakifbank_gateway_url'] ?? '');
		$hata_mesaj				= $_POST['vakifbank_hata_mesaj'];
		$test_modu				= $_POST['vakifbank_test_modu'];
		$taksit					= $_POST['vakifbank_taksit'];
		
		// Tablo yoksa oluştur
		$db->exec("CREATE TABLE IF NOT EXISTS vakifbank (
			id INT(11) NOT NULL AUTO_INCREMENT,
			host_merchant_id VARCHAR(255) DEFAULT '',
			user_name VARCHAR(255) DEFAULT NULL,
			merchant_password VARCHAR(255) DEFAULT '',
			host_terminal_id VARCHAR(255) DEFAULT '',
			gateway_url VARCHAR(512) DEFAULT NULL,
			hata_mesaj TINYINT(1) DEFAULT 0,
			test_modu TINYINT(1) DEFAULT 1,
			taksit TINYINT(1) DEFAULT 0,
			PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		
		// Kayıt var mı kontrol et
		$kontrol = $db->query("SELECT * FROM vakifbank WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
		
		if($kontrol) {
			$sorgu = $db->prepare("UPDATE vakifbank SET
				host_merchant_id	= ?,
				user_name			= ?,
				merchant_password 	= ?,
				host_terminal_id	= ?,
				gateway_url			= ?,
				hata_mesaj			= ?,
				test_modu			= ?,
				taksit				= ?
				WHERE id 			= ?");
			$guncelle = $sorgu->execute(array(
				$host_merchant_id,
				$user_name,
				$merchant_password,			
				$host_terminal_id,			
				$gateway_url ?: null,			
				$hata_mesaj,			
				$test_modu,			
				$taksit,			
				"1"
			));
		} else {
			$sorgu = $db->prepare("INSERT INTO vakifbank SET
				id					= ?,
				host_merchant_id	= ?,
				user_name			= ?,
				merchant_password 	= ?,
				host_terminal_id	= ?,
				gateway_url			= ?,
				hata_mesaj			= ?,
				test_modu			= ?,
				taksit				= ?");
			$guncelle = $sorgu->execute(array(
				"1",
				$host_merchant_id,
				$user_name,
				$merchant_password,			
				$host_terminal_id,			
				$gateway_url ?: null,			
				$hata_mesaj,			
				$test_modu,			
				$taksit
			));
		}
		
		if($guncelle)
		{
			$last_id 		= "1";
			$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
			$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
			$bildirimkt 		= strtotime($kayitt);
			$bildirimt 		= strtotime($btarih);
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Vakıfbank Sanal Pos Ayarları Güncellendi",
				'icon' 		=> "icon-credit-card",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> Vakıfbank sanalpos ayarlarını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['vakifbank_ayarlar'] = 'yes';
			header("Location:../".yonetim."/sanal-poslar.html");
			exit();
		}
		else
		{
			$_SESSION['vakifbank_ayarlar'] = 'no';
			header("Location:../".yonetim."/sanal-poslar.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sanal-poslar.html");
		exit();
	}
}

##Arka Plan Görseli Güncelle ##
if(isset($_POST['arkaplan_ayarlar']))
{
	cVCLmHLxbS_panelislemkontrol("arkaplan_ayarlar");
	if($_SESSION['rutbe'] == 0)
	{
		
		$upload = new upload($_FILES['arkaplan1']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan1");
			if ($upload->processed)
			{
				$arkaplan1=''.$upload->file_dst_name.'';
			}
		}

		$upload = new upload($_FILES['arkaplan2']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan2");
			if ($upload->processed)
			{
				$arkaplan2=''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan3']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan3");
			if ($upload->processed)
			{
				$arkaplan3=''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan4']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan4");
			if ($upload->processed)
			{
				$arkaplan4=''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan5']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan5");
			if ($upload->processed)
			{
				$arkaplan5 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan6']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan6");
			if ($upload->processed)
			{
				$arkaplan6=''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan7']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan7");
			if ($upload->processed)
			{
				$arkaplan7=''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan8']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan8");
			if ($upload->processed)
			{
				$arkaplan8 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan9']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan9");
			if ($upload->processed)
			{
				$arkaplan9 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan10']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan10");
			if ($upload->processed)
			{
				$arkaplan10 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan11']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan11");
			if ($upload->processed)
			{
				$arkaplan11 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan12']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan12");
			if ($upload->processed)
			{
				$arkaplan12 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan13']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan13");
			if ($upload->processed)
			{
				$arkaplan13 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan14']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan14");
			if ($upload->processed)
			{
				$arkaplan14 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan15']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan15");
			if ($upload->processed)
			{
				$arkaplan15 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan16']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan16");
			if ($upload->processed)
			{
				$arkaplan16 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan17']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan17");
			if ($upload->processed)
			{
				$arkaplan17 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan18']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan18");
			if ($upload->processed)
			{
				$arkaplan18 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan19']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan19");
			if ($upload->processed)
			{
				$arkaplan19 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan20']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan20");
			if ($upload->processed)
			{
				$arkaplan20 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan21']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan21");
			if ($upload->processed)
			{
				$arkaplan21 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan22']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan22");
			if ($upload->processed)
			{
				$arkaplan22 =''.$upload->file_dst_name.'';
			}
		}
		
		$upload = new upload($_FILES['arkaplan23']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan23");
			if ($upload->processed)
			{
				$arkaplan23 =''.$upload->file_dst_name.'';
			}
		}

		$upload = new upload($_FILES['arkaplan24']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/arkaplan/arkaplan24");
			if ($upload->processed)
			{
				$arkaplan24 =''.$upload->file_dst_name.'';
			}
		}

		if(isset($arkaplan1)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan1/".$resim_bul['arkaplan1']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan1 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan1,1]);
			$arkaplan1=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan2)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan2/".$resim_bul['arkaplan2']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan2 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan2,1]);
			$arkaplan2=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan3)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan3/".$resim_bul['arkaplan3']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan3 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan3,1]);
			$arkaplan3=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan4)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan4/".$resim_bul['arkaplan4']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan4 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan4,1]);
			$arkaplan4=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan5)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan5/".$resim_bul['arkaplan5']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan5 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan5,1]);
			$arkaplan5=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan6)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan6/".$resim_bul['arkaplan6']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan6 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan6,1]);
			$arkaplan6=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan7)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan7/".$resim_bul['arkaplan7']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan7 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan7,1]);
			$arkaplan7=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan8)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan8/".$resim_bul['arkaplan8']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan8 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan8,1]);
			$arkaplan8=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan9)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan9/".$resim_bul['arkaplan9']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan9 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan9,1]);
			$arkaplan9=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan10)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan10/".$resim_bul['arkaplan10']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan10 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan10,1]);
			$arkaplan10=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan11)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan11/".$resim_bul['arkaplan11']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan11 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan11,1]);
			$arkaplan11=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan12)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan12/".$resim_bul['arkaplan12']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan12 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan12,1]);
			$arkaplan12=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan13)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan13/".$resim_bul['arkaplan13']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan13 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan13,1]);
			$arkaplan13=''.$upload->file_dst_name.'';
		}
		
		
		if(isset($arkaplan14)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan14/".$resim_bul['arkaplan14']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan14 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan14,1]);
			$arkaplan14=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan15)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan15/".$resim_bul['arkaplan15']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan15 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan15,1]);
			$arkaplan15=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan16)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan16/".$resim_bul['arkaplan16']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan16 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan16,1]);
			$arkaplan16=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan17)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan17/".$resim_bul['arkaplan17']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan17 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan17,1]);
			$arkaplan17=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan18)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan18/".$resim_bul['arkaplan18']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan18 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan18,1]);
			$arkaplan18=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan19)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan19/".$resim_bul['arkaplan19']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan19 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan19,1]);
			$arkaplan19=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan20)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan20/".$resim_bul['arkaplan20']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan20 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan20,1]);
			$arkaplan20=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan21)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan21/".$resim_bul['arkaplan21']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan21 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan21,1]);
			$arkaplan21=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan22)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan22/".$resim_bul['arkaplan22']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan22 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan22,1]);
			$arkaplan22=''.$upload->file_dst_name.'';
		}
		
		if(isset($arkaplan23)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan23/".$resim_bul['arkaplan23']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan23 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan23,1]);
			$arkaplan23=''.$upload->file_dst_name.'';
		}

		if(isset($arkaplan24)){
			$resim_bul= $db->query("SELECT * FROM arka_plan WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/arkaplan/arkaplan24/".$resim_bul['arkaplan24']);
			$guncelle = $db->prepare("UPDATE arka_plan SET arkaplan24 = ? WHERE id = ?");
			$guncelle->execute([$arkaplan24,1]);
			$arkaplan24=''.$upload->file_dst_name.'';
		}

		if($guncelle)
		{
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Arka Plan Görselleri Güncellendi",
				'icon' 		=> "icon-settings",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> site arka plan görsellerini güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['arkaplan_ayarlar'] = 'yes';
			header("Location:../".yonetim."/arkaplan-ayarlari.html");
			exit();
		}
		else
		{
			$_SESSION['arkaplan_ayarlar'] = 'no';
			header("Location:../".yonetim."/arkaplan-ayarlari.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/arkaplan-ayarlari.html");
		exit();
	}
}

##Site Bakım Modu Ayarları ##
if(isset($_POST['site_bakim_modu']))
{
	cVCLmHLxbS_panelislemkontrol("site_bakim_modu");
	if($_SESSION['rutbe'] == 0)
	{
		$acilis_tarih	= $_POST['acilis_tarih'];
		$acilis_zaman 	= $_POST['acilis_zaman'];
		$baslik 		= $_POST['baslik'];
		$aciklama 		= $_POST['aciklama'];
		
		$sorgu = $db->prepare("UPDATE bakim_modu SET
			acilis_tarih= ?,
			acilis_zaman= ?,
			baslik		= ?,
			aciklama	= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$acilis_tarih,
			$acilis_zaman,
			$baslik,			
			$aciklama,					
			"1"
		));
		if($guncelle)
		{
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Bakım Modu Ayarları Güncellendi",
				'icon' 		=> "icon-settings",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> bakım modu ayarları güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['site_bakim_modu'] = 'yes';
			header("Location:../".yonetim."/site-bakim-modu.html");
			exit();
		}
		else
		{
			$_SESSION['site_bakim_modu'] = 'no';
			header("Location:../".yonetim."/site-bakim-modu.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/site-bakim-modu.html");
		exit();
	}
}

#Dil Kaydet ##
if(isset($_POST['dil_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("dil_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= (int) $_POST['sira'];
		$adi 		= $_POST['adi'];
		$bayrak 		= $_POST['bayrak'];
		$anadil 		= (int) $_POST['anadil'];
		$durum 		= (int) $_POST['durum'];
		
		$varsayilan = $db->query("SELECT * FROM diller WHERE anadil = 1")->fetch(PDO::FETCH_ASSOC);
		
		$sorgu = $db->prepare("INSERT INTO diller SET
			sira 	= ?,
			adi 	= ?,
			anadil 	= ?,
			durum 	= ?,
			bayrak	= ?");
		$Ekle = $sorgu->execute(array(
			$sira,
			$adi,
			$anadil,
			$durum,
			$bayrak
		));
		$lastid = $db->lastInsertId();
		if ($anadil == 1)
		{			
			$db->query("UPDATE diller SET anadil = 0 WHERE id NOT IN($lastid)");
		}
	
		if($Ekle)
		{
			$last_id 		= $lastid;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Dil Eklendi",
				'icon' 		=> "icon-globe",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: deeppink;'>".$adi."</strong> dilini sisteme ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['dil_ekle'] = 'yes';
			copy("../".tema."/../../language/dil_".@$varsayilan['id'].".php", "../".tema."/../../language/dil_".$lastid.".php");
			header("Location:../".yonetim."/dil-listele.html");
			exit();
		}
		else
		{
			$_SESSION['dil_ekle'] = 'no';
			header("Location:../".yonetim."/dil-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/dil-ekle.html");
		exit();
	}
}

##Dil Güncelle ##
if(isset($_POST['dil_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("dil_guncelle");
	$d_id 		= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= (int) $_POST['sira'];
		$adi 		= $_POST['adi'];
		$bayrak 		= $_POST['bayrak'];
		$anadil 		= (int) $_POST['anadil'];
		$durum 		= (int) $_POST['durum'];
		
		$sorgu = $db->prepare("UPDATE diller SET
			sira 	= ?,
			adi 	= ?,
			bayrak 	= ?,
			durum 	= ?
			WHERE id = ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$adi,
			$bayrak,
			$durum,
			$d_id
		));
		if($anadil == 0)
		{
			$_SESSION['dil_guncelle'] = 'anadil';
			header("Location:../".yonetim."/dil-duzenle/".$d_id.".html");
			exit();			
		}
		else
		{
			$db->query("UPDATE diller SET anadil = 0");
			$db->query("UPDATE diller SET anadil = 1 WHERE id = {$d_id}");
		}
		
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Dil Güncellendi",
				'icon' 		=> "icon-globe",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: deeppink;'>".$adi."</strong> dilini güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['dil_guncelle'] = 'yes';
			header("Location:../".yonetim."/dil-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['dil_guncelle'] = 'no';
			header("Location:../".yonetim."/dil-duzenle/".$d_id.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/dil-duzenle/".$d_id.".html");
		exit();
	}
}

##Dil Sil##
if(@$_GET['dilsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("dilsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$dil_bul= $db->query("SELECT * FROM diller WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink('../language/dil_'.@$dil_bul['id'].".php");
		$Sorgu = $db->prepare("DELETE FROM diller WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $dil_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Dil Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: deeppink;'>".$dil_bul['adi']."</strong> dilini sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['dilsil'] = 'yes';
				header("Location:../".yonetim."/dil-listele.html");
				exit();
			}
			else
			{
				$_SESSION['dilsil'] = 'no';
				header("Location:../".yonetim."/dil-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/dil-listele.html");
		exit();
	}
}

##Dil Toplu Sil ##
if(isset($_POST['dil_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("dil_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$dil_bul= $db->query("SELECT * FROM diller WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM diller WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Dil Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: deeppink;'>".$dil_bul['adi']."</strong> dilini sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink('../language/dil_'.@$dil_bul['id'].".php");
					$_SESSION['dil_tumu'] = 'yes';
					header("Location:../".yonetim."/dil-listele.html");
				}
				else
				{
					$_SESSION['dil_tumu'] = 'no';
					header("Location:../".yonetim."/dil-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/dil-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/dil-listele.html");
		exit();
	}	
}

##Dil Toplu Aktif ##
if(isset($_POST['dil_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("dil_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$dil_bul= $db->query("SELECT * FROM diller WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE diller SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Dil Aktif Edildi",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: deeppink;'>".$dil_bul['adi']."</strong> dilini aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['dil_aktif'] = 'yes';
					header("Location:../".yonetim."/dil-listele.html");
				}
				else
				{
					$_SESSION['dil_aktif'] = 'no';
					header("Location:../".yonetim."/dil-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/dil-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/dil-listele.html");
		exit();
	}	
}

##Dil Toplu Pasif ##
if(isset($_POST['dil_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("dil_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$dil_bul= $db->query("SELECT * FROM diller WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE diller SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Dil Pasif Edildi",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: deeppink;'>".$dil_bul['adi']."</strong> dilini pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['dil_pasif'] = 'yes';
					header("Location:../".yonetim."/dil-listele.html");
				}
				else
				{
					$_SESSION['dil_pasif'] = 'no';
					header("Location:../".yonetim."/dil-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/dil-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/dil-listele.html");
		exit();
	}	
}

##Dil Sıra Ajax##
if(isset($_GET['dilsiralama']))
{
	if($_GET['dilsiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE diller SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Çoklu Mesaj Sil ##
if(isset($_POST['mesaj_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("mesaj_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$mesaj_bul	= $db->query("SELECT * FROM mesajlar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM mesajlar WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Mesaj Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: thistle;'>".$mesaj_bul['konu']."</strong> konulu mesajı sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['mesaj_tumu'] = 'yes';
					header("Location:../".yonetim."/mesajlar.html");
				}
				else
				{
					$_SESSION['mesaj_tumu'] = 'no';
					header("Location:../".yonetim."/mesajlar.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/mesajlar.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/mesajlar.html");
		exit();
	}	
}

##Çoklu Mesaj Okundu ##
if(isset($_POST['mesaj_okundu']))
{
	cVCLmHLxbS_panelislemkontrol("mesaj_okundu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$mesaj_bul	= $db->query("SELECT * FROM mesajlar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE mesajlar SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Mesaj Okundu",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: thistle;'>".$mesaj_bul['konu']."</strong> konulu mesajı okundu olarak ayarladı.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['mesaj_okundu'] = 'yes';
					header("Location:../".yonetim."/mesajlar.html");
				}
				else
				{
					$_SESSION['mesaj_okundu'] = 'no';
					header("Location:../".yonetim."/mesajlar.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/mesajlar.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/mesajlar.html");
		exit();
	}	
}

##Çoklu Mesaj Okunmadı ##
if(isset($_POST['mesaj_okunmadi']))
{
	cVCLmHLxbS_panelislemkontrol("mesaj_okunmadi");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$mesaj_bul	= $db->query("SELECT * FROM mesajlar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE mesajlar SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Mesaj Okunmadı",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: thistle;'>".$mesaj_bul['konu']."</strong> konulu mesajı okunmadı olarak ayarladı.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['mesaj_okunmadi'] = 'yes';
					header("Location:../".yonetim."/mesajlar.html");
				}
				else
				{
					$_SESSION['mesaj_okunmadi'] = 'no';
					header("Location:../".yonetim."/mesajlar.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/mesajlar.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/mesajlar.html");
		exit();
	}	
}

##Mesaj Sil##
if(@$_GET['mesajsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("mesajsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$mesaj_bul	= $db->query("SELECT * FROM mesajlar WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		$Sorgu = $db->prepare("DELETE FROM mesajlar WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $i;
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Mesaj Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: thistle;'>".$mesaj_bul['konu']."</strong> konulu mesajı sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['mesajsil'] = 'yes';
				header("Location:../".yonetim."/mesajlar.html");
				exit();
			}
			else
			{
				$_SESSION['mesajsil'] = 'no';
				header("Location:../".yonetim."/mesajlar.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/mesajlar.html");
		exit();
	}
}

##Detay Mesaj Okundu##
if(@$_GET['mesajokundu'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("mesajokundu");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_POST['id'];
		$guncellendi = $db->query("UPDATE mesajlar SET durum = '1' WHERE id = {$id}");
	}
}

##Program Mesajları İşlemleri##

##Çoklu Program Mesaj Sil ##
if(isset($_POST['program_mesaj_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("program_mesaj_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$mesaj_bul	= $db->query("SELECT * FROM okullar_program_talep WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM okullar_program_talep WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id = $i;
					$btarih = cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt = cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt = strtotime($kayitt);
					$bildirimt = strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Program Mesajı Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: thistle;'>".$mesaj_bul['program_baslik']."</strong> programına ait mesajı sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['program_mesaj_tumu'] = 'yes';
					header("Location:../".yonetim."/program_mesajlar.html");
				}
				else
				{
					$_SESSION['program_mesaj_tumu'] = 'no';
					header("Location:../".yonetim."/program_mesajlar.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/program_mesajlar.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/program_mesajlar.html");
		exit();
	}	
}

##Çoklu Program Mesaj Okundu ##
if(isset($_POST['program_mesaj_okundu']))
{
	cVCLmHLxbS_panelislemkontrol("program_mesaj_okundu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$mesaj_bul	= $db->query("SELECT * FROM okullar_program_talep WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE okullar_program_talep SET durum = ? WHERE id = ?");
				$guncelle = $sorgu->execute(array("1", $i));
				if($guncelle)
				{
					$last_id = $i;
					$btarih = cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt = cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt = strtotime($kayitt);
					$bildirimt = strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Program Mesajı Okundu",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: thistle;'>".$mesaj_bul['program_baslik']."</strong> programına ait mesajı okundu olarak ayarladı.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['program_mesaj_okundu'] = 'yes';
					header("Location:../".yonetim."/program_mesajlar.html");
				}
				else
				{
					$_SESSION['program_mesaj_okundu'] = 'no';
					header("Location:../".yonetim."/program_mesajlar.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/program_mesajlar.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/program_mesajlar.html");
		exit();
	}	
}

##Çoklu Program Mesaj Okunmadı ##
if(isset($_POST['program_mesaj_okunmadi']))
{
	cVCLmHLxbS_panelislemkontrol("program_mesaj_okunmadi");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$mesaj_bul	= $db->query("SELECT * FROM okullar_program_talep WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE okullar_program_talep SET durum = ? WHERE id = ?");
				$guncelle = $sorgu->execute(array("0", $i));
				if($guncelle)
				{
					$last_id = $i;
					$btarih = cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
					$kayitt = cVCLmHLxbS_tr_tarih('Y-m-d');
					$bildirimkt = strtotime($kayitt);
					$bildirimt = strtotime($btarih);
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Program Mesajı Okunmadı",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: thistle;'>".$mesaj_bul['program_baslik']."</strong> programına ait mesajı okunmadı olarak ayarladı.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['program_mesaj_okunmadi'] = 'yes';
					header("Location:../".yonetim."/program_mesajlar.html");
				}
				else
				{
					$_SESSION['program_mesaj_okunmadi'] = 'no';
					header("Location:../".yonetim."/program_mesajlar.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/program_mesajlar.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/program_mesajlar.html");
		exit();
	}	
}

##Program Mesaj Sil##
if(@$_GET['programmesajsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("programmesajsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$mesaj_bul	= $db->query("SELECT * FROM okullar_program_talep WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		$Sorgu = $db->prepare("DELETE FROM okullar_program_talep WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($sil_sorgu)
		{
			$last_id = $id;
			$btarih = cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
			$kayitt = cVCLmHLxbS_tr_tarih('Y-m-d');
			$bildirimkt = strtotime($kayitt);
			$bildirimt = strtotime($btarih);
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Program Mesajı Silindi",
				'icon' 		=> "icon-trash",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: thistle;'>".$mesaj_bul['program_baslik']."</strong> programına ait mesajı sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['programmesajsil'] = 'yes';
			header("Location:../".yonetim."/program_mesajlar.html");
			exit();
		}
		else
		{
			$_SESSION['programmesajsil'] = 'no';
			header("Location:../".yonetim."/program_mesajlar.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/program_mesajlar.html");
		exit();
	}
}

##Detay Program Mesaj Okundu##
if(@$_GET['programmesajokundu'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("programmesajokundu");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_POST['id'];
		$guncellendi = $db->query("UPDATE okullar_program_talep SET durum = '1' WHERE id = {$id}");
		echo json_encode("Mesaj okundu olarak işaretlendi.");
	}
}

## Modül Güncelle ##
if(isset($_POST['modul_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("modul_guncelle");
	$url		= $_POST['url'];
	if($_SESSION['rutbe'] == 0)
	{			
		if($_POST['alan1']){$alan1 = 1;}else{$alan1 = 0;}
		if($_POST['alan2']){$alan2 = 1;}else{$alan2 = 0;}
		if($_POST['alan3']){$alan3 = 1;}else{$alan3 = 0;}
		if($_POST['alan4']){$alan4 = 1;}else{$alan4 = 0;}
		if($_POST['alan5']){$alan5 = 1;}else{$alan5 = 0;}
		if($_POST['alan6']){$alan6 = 1;}else{$alan6 = 0;}
		if($_POST['alan7']){$alan7 = 1;}else{$alan7 = 0;}
		if($_POST['alan8']){$alan8 = 1;}else{$alan8 = 0;}
		if($_POST['alan9']){$alan9 = 1;}else{$alan9 = 0;}
		if($_POST['alan10']){$alan10 = 1;}else{$alan10 = 0;}
		if($_POST['alan11']){$alan11 = 1;}else{$alan11 = 0;}
		if($_POST['alan12']){$alan12 = 1;}else{$alan12 = 0;}
		if($_POST['alan13']){$alan13 = 1;}else{$alan13 = 0;}
		if($_POST['alan14']){$alan14 = 1;}else{$alan14 = 0;}
		if($_POST['alan15']){$alan15 = 1;}else{$alan15 = 0;}
		if($_POST['alan16']){$alan16 = 1;}else{$alan16 = 0;}
		if($_POST['alan17']){$alan17 = 1;}else{$alan17 = 0;}
		if($_POST['alan18']){$alan18 = 1;}else{$alan18 = 0;}
		if($_POST['alan19']){$alan19 = 1;}else{$alan19 = 0;}
		if($_POST['alan20']){$alan20 = 1;}else{$alan20 = 0;}
		if($_POST['alan21']){$alan21 = 1;}else{$alan21 = 0;}
		if($_POST['alan22']){$alan22 = 1;}else{$alan22 = 0;}
		if($_POST['alan23']){$alan23 = 1;}else{$alan23 = 0;}
		if($_POST['alan24']){$alan24 = 1;}else{$alan24 = 0;}
		if($_POST['alan25']){$alan25 = 1;}else{$alan25 = 0;}
		if($_POST['alan26']){$alan26 = 1;}else{$alan26 = 0;}
		if($_POST['alan26']){$alan26 = 1;}else{$alan26 = 0;}
		if($_POST['alan27']){$alan27 = 1;}else{$alan27 = 0;}
		if($_POST['alan28']){$alan28 = 1;}else{$alan28 = 0;}
		if($_POST['alan29']){$alan29 = 1;}else{$alan29 = 0;}
        if($_POST['alan30']){$alan30 = 1;}else{$alan30 = 0;}
        if($_POST['alan31']){$alan31 = 1;}else{$alan31 = 0;}
        if($_POST['alan32']){$alan32 = 1;}else{$alan32 = 0;}
		if($_POST['alan33']){$alan33 = 1;}else{$alan33 = 0;}
		if($_POST['alan34']){$alan34 = 1;}else{$alan34 = 0;}
        if($_POST['alan35']){$alan35 = 1;}else{$alan35 = 0;}
        if($_POST['alan36']){$alan36 = 1;}else{$alan36 = 0;}
		
        $sorgu = $db->prepare("UPDATE moduller SET
			alan1	= ?,
			alan2	= ?,
			alan3	= ?,
			alan4	= ?,
			alan5	= ?,
			alan6	= ?,
			alan7	= ?,
			alan8	= ?,
			alan9	= ?,
			alan10	= ?,
			alan11	= ?,
			alan12	= ?,
			alan13	= ?,
			alan14	= ?,
			alan15	= ?,
			alan16	= ?,
			alan17	= ?,
			alan18	= ?,
			alan19	= ?,
			alan20	= ?,
			alan21	= ?,
			alan22	= ?,
			alan23	= ?,
			alan24	= ?,
			alan25	= ?,
			alan26	= ?,
			alan27	= ?,
			alan28	= ?,
            alan29	= ?,
            alan30	= ?,
            alan31	= ?,
            alan32	= ?,
			alan33	= ?,
            alan34	= ?,
            alan35	= ?,
            alan36	= ?
			WHERE id= ?");
		$guncelle = $sorgu->execute(array(
			$alan1,
			$alan2,
			$alan3,
			$alan4,
			$alan5,
			$alan6,
			$alan7,
			$alan8,
			$alan9,
			$alan10,
			$alan11,
			$alan12,
			$alan13,
			$alan14,
			$alan15,
			$alan16,
			$alan17,
			$alan18,
			$alan19,
			$alan20,
			$alan21,
			$alan22,
			$alan23,
			$alan24,
			$alan25,
			$alan26,
			$alan27,
			$alan28,
            $alan29,
            $alan30,
            $alan31,
            $alan32,
			$alan33,
            $alan34,
            $alan35,
            $alan36,
            "1"
		));
		if($guncelle)
		{
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Modül Güncellendi",
				'icon' 		=> "icon-settings",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> modül ayarlarını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['modul_guncelle'] = 'yes';
			header("Location:".$url."");
			exit();
		}
		else
		{
			$_SESSION['modul_guncelle'] = 'no';
			header("Location:".$url."");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:".$url."");
		exit();
	}
}

// Hesap Ekle
if(isset($_POST['hesap_ekle']))
{
    cVCLmHLxbS_panelislemkontrol("hesap_ekle");
    // Bu işlem için ekstra rutbe kontrolü yapma; panele giriş yapmış herkes ekleyebilsin.
    if(!empty($_SESSION['Yonetim_Id']))
    {
        $banka_adi    = trim($_POST['banka_adi']);
        $iban         = trim($_POST['iban']);
        $hesap_sahibi = trim($_POST['hesap_sahibi']);
        $hesap_no     = trim(@$_POST['hesap_no']);
        $sube         = trim(@$_POST['sube']);
        $para_birimi  = trim(@$_POST['para_birimi']) ?: 'TRY';
        $swift_kodu   = trim(@$_POST['swift_kodu']);
        $logo         = '';
        if(isset($_FILES['logo']) && @$_FILES['logo']['tmp_name'])
        {
            $uz = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $ad = 'logo_'.time().'_'.rand(1000,9999).'.'.$uz;
            @mkdir('../tema/genel/uploads/hesaplar',0777,true);
            if(move_uploaded_file($_FILES['logo']['tmp_name'], '../tema/genel/uploads/hesaplar/'.$ad))
            {
                $logo = $ad;
            }
        }
        $Ekle = $db->prepare("INSERT INTO hesaplar SET banka_adi=?, iban=?, hesap_sahibi=?, hesap_no=?, sube=?, para_birimi=?, swift_kodu=?, logo=?, tarih=NOW()");
        $ok = $Ekle->execute(array($banka_adi,$iban,$hesap_sahibi,$hesap_no,$sube,$para_birimi,$swift_kodu,$logo));
        header("Location:../".yonetim."/hesap-numaralarimiz.html");
        exit();
    }
}

// Hesap Güncelle
if(isset($_POST['hesap_guncelle']))
{
    cVCLmHLxbS_panelislemkontrol("hesap_guncelle");
    if(!empty($_SESSION['Yonetim_Id']))
    {
        $id = (int)$_POST['hesap_id'];
        $banka_adi    = trim($_POST['banka_adi']);
        $iban         = trim($_POST['iban']);
        $hesap_sahibi = trim($_POST['hesap_sahibi']);
        $hesap_no     = trim(@$_POST['hesap_no']);
        $sube         = trim(@$_POST['sube']);
        $para_birimi  = trim(@$_POST['para_birimi']) ?: 'TRY';
        $swift_kodu   = trim(@$_POST['swift_kodu']);
        
        if(isset($_FILES['logo']) && @$_FILES['logo']['tmp_name'])
        {
            $Bul = $db->prepare("SELECT logo FROM hesaplar WHERE id=?");
            $Bul->execute(array($id));
            $R = $Bul->fetch(PDO::FETCH_ASSOC);
            if(!empty($R['logo'])) @unlink('../tema/genel/uploads/hesaplar/'.$R['logo']);
            
            $uz = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $ad = 'logo_'.time().'_'.rand(1000,9999).'.'.$uz;
            @mkdir('../tema/genel/uploads/hesaplar',0777,true);
            if(move_uploaded_file($_FILES['logo']['tmp_name'], '../tema/genel/uploads/hesaplar/'.$ad))
            {
                $Guncelle = $db->prepare("UPDATE hesaplar SET banka_adi=?, iban=?, hesap_sahibi=?, hesap_no=?, sube=?, para_birimi=?, swift_kodu=?, logo=?, tarih=NOW() WHERE id=?");
                $ok = $Guncelle->execute(array($banka_adi,$iban,$hesap_sahibi,$hesap_no,$sube,$para_birimi,$swift_kodu,$ad,$id));
            }
        }
        else
        {
            $Guncelle = $db->prepare("UPDATE hesaplar SET banka_adi=?, iban=?, hesap_sahibi=?, hesap_no=?, sube=?, para_birimi=?, swift_kodu=?, tarih=NOW() WHERE id=?");
            $ok = $Guncelle->execute(array($banka_adi,$iban,$hesap_sahibi,$hesap_no,$sube,$para_birimi,$swift_kodu,$id));
        }
        header("Location:../".yonetim."/hesap-numaralarimiz.html");
        exit();
    }
}


// Hesap Sil
if(isset($_POST['hesap_sil']))
{
    cVCLmHLxbS_panelislemkontrol("hesap_sil");
    if($_SESSION['rutbe'] == 0)
    {
        $id = (int)$_POST['id'];
        $Bul = $db->prepare("SELECT logo FROM hesaplar WHERE id=?");
        $Bul->execute(array($id));
        $R = $Bul->fetch(PDO::FETCH_ASSOC);
        $Sil = $db->prepare("DELETE FROM hesaplar WHERE id=?");
        $ok = $Sil->execute(array($id));
        if($ok && !empty($R['logo'])) @unlink('../tema/genel/uploads/hesaplar/'.$R['logo']);
        header("Location:../".yonetim."/hesap-numaralarimiz.html");
        exit();
    }
}

##Şablon Güncelle ##
if(isset($_POST['sablon_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("sablon_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$ubildirim 	= $_POST['ubildirim'];
		$sbildirim 	= $_POST['sbildirim'];
		$abildirim 	= $_POST['abildirim'];
		$ysbildirim = $_POST['ysbildirim'];
		$konu 		= $_POST['konu'];
		$konu2 		= $_POST['konu2'];
		$icerik		= $_POST['icerik'];
		$icerik2	= $_POST['icerik2'];
		$icerik3	= $_POST['icerik3'];
		$icerik4	= $_POST['icerik4'];

		$sorgu = $db->prepare("UPDATE bildirim_sablonu SET
			ubildirim 	= ?,
			sbildirim 	= ?,
			abildirim 	= ?,
			ysbildirim	= ?,
			konu		= ?,
			konu2		= ?,
			icerik 		= ?,
			icerik2 	= ?,
			icerik3 	= ?,
			icerik4 	= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$ubildirim,
			$sbildirim,
			$abildirim,
			$ysbildirim,
			$konu,
			$konu2,
			$icerik,
			$icerik2,
			$icerik3,
			$icerik4,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= "1";
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Bildirim Şablonu Güncellendi",
				'icon' 		=> "icon-notebook",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> bildirim şablonlarını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['sablon_guncelle'] = 'yes';
			header("Location:../".yonetim."/sablon-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['sablon_guncelle'] = 'no';
			header("Location:../".yonetim."/sablon-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/sablon-duzenle/".$d_id.".html");
		exit();
	}
}

##Not Ekle ##
if(isset($_POST['not_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("not_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$title 	= $_POST['title'];
		$start  = $_POST["start"];
		$end 	= $_POST['end'];
		$color 	= $_POST['color'];
		$ekleyen= $_POST['ekleyen'];
		
		if(empty($title) || empty($start) || empty($end) || empty($color))
		{
			$_SESSION['not_ekle'] = 'bos';
			header("Location:../".yonetim."/not-defteri.html");
			exit();
		}
		else
		{
			$sorgu = $db->prepare("INSERT INTO not_defteri SET
				baslik		= :baslik,
				baslangic 	= :baslangic,
				bitis 		= :bitis,
				ekleyen 	= :ekleyen,
				renk 		= :renk");
			$Ekle = $sorgu->execute(array(
				"baslik" 	=> $title,
				"baslangic" => $start,
				"bitis" 	=> $end,
				"ekleyen" 	=> $ekleyen,
				"renk" 		=> $color
			));
			if($Ekle)
			{
				$last_id 		= $db->lastInsertId();
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Not Eklendi",
					'icon' 		=> "icon-calendar",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: steelblue;'>".$title."</strong> başlıklı not ekledi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['not_ekle'] = 'yes';
				header("Location:../".yonetim."/not-defteri.html");	
				exit();
			}
			else
			{
				$_SESSION['not_ekle'] = 'no';
				header("Location:../".yonetim."/not-defteri.html");
				exit();
			}
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/not-defteri.html");
		exit();
	}
}

##Not Düzenle ##
if(isset($_POST['not_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("not_guncelle");
	if($_SESSION['rutbe'] == 0)
	{
		if (isset($_POST['delete']) && isset($_POST['id']))
		{
			$id 	= $_POST['id'];
			$notbul	= $db->query("SELECT * FROM not_defteri WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
			$title 	= $notbul['baslik'];
			$Sorgu 	= $db->prepare("DELETE FROM not_defteri WHERE id = :id");
			$Sil	= $Sorgu->execute(array('id' => $id));
			if($Sil)
			{
				$last_id 		= $id;
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Notu Sil",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: steelblue;'>".$title."</strong> başlıklı notu sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['not_guncelle'] = 'sil_yes';
				header("Location:../".yonetim."/not-defteri.html");
				exit();
			}
			else
			{
				$_SESSION['not_guncelle'] = 'sil_no';
				header("Location:../".yonetim."/not-defteri.html");
				exit();
			}
			
		}
		elseif (isset($_POST['title']) && isset($_POST['color']) && isset($_POST['id']))
		{
			$id 	= $_POST['id'];
			$title 	= $_POST['title'];
			$color 	= $_POST['color'];
			
			$sorgu = $db->prepare("UPDATE not_defteri SET
				baslik 	= ?,
				renk	= ?
				WHERE id= ?");
			$guncelle = $sorgu->execute(array(
				$title,
				$color,
				$id
			));
			if($guncelle)
			{
				$last_id 		= $id;
				$btarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
				$kayitt			= cVCLmHLxbS_tr_tarih('Y-m-d');
				$bildirimkt 	= strtotime($kayitt);
				$bildirimt 		= strtotime($btarih);
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Notu Güncelle",
					'icon' 		=> "icon-calendar",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: steelblue;'>".$title."</strong> başlıklı notu güncelledi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['not_guncelle'] = 'guncelle_yes';
				header("Location:../".yonetim."/not-defteri.html");
				exit();
			}
			else
			{
				$_SESSION['not_guncelle'] = 'guncelle_no';
				header("Location:../".yonetim."/not-defteri.html");
				exit();
			}
			
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/not-defteri.html");
		exit();
	}
}

##Ajax Not Güncelle##
if(@$_GET['notguncelle'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("notguncelle");
	if($_SESSION['rutbe'] == 0)
	{
		if (isset($_POST['Event'][0]) && isset($_POST['Event'][1]) && isset($_POST['Event'][2]))
		{
			$id 	= $_POST['Event'][0];
			$notbul	= $db->query("SELECT * FROM not_defteri WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
			$title 	= $notbul['baslik'];
			$start 	= $_POST['Event'][1];
			$end 	= $_POST['Event'][2];
			
			$sorgu = $db->prepare("UPDATE not_defteri SET
				baslangic 	= ?,
				bitis		= ?
				WHERE id	= ?");
			$guncelle = $sorgu->execute(array(
				$start,
				$end,
				$id
			));
			if($guncelle)
			{
				$last_id 		= $id;
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Notu Güncelle",
					'icon' 		=> "icon-calendar",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: steelblue;'>".$title."</strong> başlıklı notu güncelledi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				die ('OK');
			}
			else
			{
				$_SESSION['notguncelle'] = 'no';
				header("Location:../".yonetim."/not-defteri.html");
				exit();
			}
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/not-defteri.html");
		exit();
	}
}

##Bildirim Sil##
if(@$_GET['bildirimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("bildirimsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$Sorgu = $db->prepare("DELETE FROM bildirimler WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$_SESSION['bildirimsil'] = 'yes';
				header("Location:../".yonetim."/index.html");
				exit();
			}
			else
			{
				$_SESSION['bildirimsil'] = 'no';
				header("Location:../".yonetim."/index.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/index.html");
		exit();
	}
}

##Bildirim Tümünü Sil ##
if(@$_GET['bildirimtumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("bildirimtumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$Sorgu = $db->prepare("TRUNCATE TABLE bildirimler");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['bildirimtumunusil'] = 'yes';
			header("Location:../".yonetim."/index.html");
			exit();
		}
		else
		{
			$_SESSION['bildirimtumunusil'] = 'no';
			header("Location:../".yonetim."/index.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/index.html");
		exit();
	}
}

##Rehber Kaydet ##
if(isset($_POST['rehber_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("rehber_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}		
		$email 		= $_POST['email'];
		$telefon	= $_POST['telefon'];
		$notunuz	= $_POST['notunuz'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$sorgu = $db->prepare("INSERT INTO rehber SET
				adi 	= ?,
				email	= ?,
				telefon	= ?,
				notunuz	= ?,
				durum 	= ?,
				tarih 	= ?");
		$Ekle = $sorgu->execute(array(
				$adi,
				$email,
				$telefon,
				$notunuz,
				$durum,
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Rehber",
				'icon' 		=> "icon-notebook",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> isimli rehber kaydı ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['rehber_ekle'] = 'yes';
			header("Location:../".yonetim."/rehberim.html");
			exit();
		}
		else
		{
			$_SESSION['rehber_ekle'] = 'no';
			header("Location:../".yonetim."/rehber-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/rehber-ekle.html");
		exit();
	}
}

##Rehber Güncelle ##
if(isset($_POST['rehber_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("rehber_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$email 		= $_POST['email'];
		$telefon 	= $_POST['telefon'];
		$notunuz	= $_POST['notunuz'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$sorgu = $db->prepare("UPDATE rehber SET
			adi 	= ?,
			email	= ?,
			telefon	= ?,
			notunuz	= ?,
			durum 	= ?,
			tarih 	= ?
			WHERE id = ?");
		$guncelle = $sorgu->execute(array(
			$adi,
			$email,
			$telefon,
			$notunuz,
			$durum,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Rehber Güncellendi",
				'icon' 		=> "icon-notebook",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> isimli rehber kaydını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['rehber_guncelle'] = 'yes';
			header("Location:../".yonetim."/rehber-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['rehber_guncelle'] = 'no';
			header("Location:../".yonetim."/rehber-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/rehber-duzenle/".$d_id.".html");
		exit();
	}
}

##Rehber Sil##
if(@$_GET['rehbersil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("rehbersil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$rehber_bul= $db->query("SELECT * FROM rehber WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		$Sorgu = $db->prepare("DELETE FROM rehber WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $rehber_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Rehber Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$rehber_bul['adi']."</strong> isimli rehber kaydını sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['rehbersil'] = 'yes';
				header("Location:../".yonetim."/rehberim.html");
				exit();
			}
			else
			{
				$_SESSION['rehbersil'] = 'no';
				header("Location:../".yonetim."/rehberim.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/rehberim.html");
		exit();
	}
}

##Rehber Toplu Sil ##
if(isset($_POST['rehber_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("rehber_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$rehber_bul= $db->query("SELECT * FROM rehber WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM rehber WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Rehber Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$rehber_bul['adi']."</strong> isimli rehber kaydını sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['rehber_tumu'] = 'yes';
					header("Location:../".yonetim."/rehberim.html");
				}
				else
				{
					$_SESSION['rehber_tumu'] = 'no';
					header("Location:../".yonetim."/rehberim.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/rehberim.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/rehberim.html");
		exit();
	}	
}

##Rehber Toplu Aktif ##
if(isset($_POST['rehber_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("rehber_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$rehber_bul= $db->query("SELECT * FROM rehber WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE rehber SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Rehber Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$rehber_bul['adi']."</strong> isimli  rehber kaydını aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['rehber_aktif'] = 'yes';
					header("Location:../".yonetim."/rehberim.html");
				}
				else
				{
					$_SESSION['rehber_aktif'] = 'no';
					header("Location:../".yonetim."/rehberim.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/rehberim.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/rehberim.html");
		exit();
	}	
}

##Rehber Toplu Pasif ##
if(isset($_POST['rehber_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("rehber_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$rehber_bul= $db->query("SELECT * FROM rehber WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE rehber SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Rehber Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$rehber_bul['adi']."</strong> isimli rehber kaydını pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['rehber_pasif'] = 'yes';
					header("Location:../".yonetim."/rehberim.html");
				}
				else
				{
					$_SESSION['rehber_pasif'] = 'no';
					header("Location:../".yonetim."/rehberim.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/rehberim.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/rehberim.html");
		exit();
	}	
}

##Toplu Email Gönder##
if(isset($_POST['toplu_email_gonder']))
{
	cVCLmHLxbS_panelislemkontrol("toplu_email_gonder");
	if($_SESSION['rutbe'] == 0)
	{
		$uyeler 		= $_POST["uyeler"];
		$diger 		= $_POST["diger"];
		$digermail 	= explode(",", $diger);
		$konu		= $_POST['konu'];
		$aciklama	= $_POST['aciklama'];
		
		if($uyeler == true && $diger == true)
		{
			$uyemail = array_merge($uyeler, $digermail);
		}
		elseif($uyeler == true)
		{
			$uyemail = $uyeler;
		}
		elseif($diger == true)
		{
			$uyemail = $digermail;
		}
		
		if(empty($aciklama) || empty($konu))
		{
			$_SESSION['toplu_email_gonder'] = 'bos';	
			header("Location:../".yonetim."/toplu-email.html");
		}
		else
		{			
			$from		= m_adresi;
			$gonderici	= m_adresi;
			$m_host		= m_server;
			$m_pass		= m_parola;
		
			$mail = new PHPMailer();
			$mail->IsSMTP(true);
			$mail->From     = $from;
			$mail->Sender   = $from;
			$mail->AddReplyTo =($from);
			$mail->FromName = firma_adi;
			$mail->Host     = $m_host;
			$mail->SMTPAuth = true;
			$mail->Port     = 587;
			foreach($uyemail as $uye)
			{
				$mail->AddBCC(''.$uye.'', ''.firma_adi.'');
			}
			$mail->CharSet = 'UTF-8';
			$mail->Username = $from;
			$mail->Password = $m_pass;
			$mail->Subject = $konu;
			$mail->Body = $aciklama;	
			$mail->IsHTML(true);
			$mail->Send();
		
			$_SESSION['toplu_email_gonder'] = 'yes';	
			header("Location:../".yonetim."/toplu-email.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/toplu-email.html");
		exit();
	}
}

##Toplu SMS Gönder##
if(isset($_POST['toplu_sms_gonder']))
{
	cVCLmHLxbS_panelislemkontrol("toplu_sms_gonder");
	if($_SESSION['rutbe'] == 0)
	{
		$uyeler 		= $_POST["uyeler"];
		$diger 		= $_POST["diger"];
		$digeruyeler= explode(",", $diger);
		$aciklama	= $_POST['aciklama'];
		
		if($uyeler != "" && $diger != "")
		{
			$uyetelefon = array_merge($uyeler, $digeruyeler);
		}
		elseif($uyeler != "")
		{
			$uyetelefon = $uyeler;
		}
		elseif($diger != "")
		{
			$uyetelefon = $digeruyeler;
		}
		
		if(empty($aciklama))
		{
			$_SESSION['toplu_sms_gonder'] = 'bos';	
			header("Location:../".yonetim."/toplu-sms.html");
			exit();
		}
		else
		{
			foreach($uyetelefon as $uye)
			{
				toplusmsgonder($uye,$aciklama); 
			}
			$_SESSION['toplu_sms_gonder'] = 'yes';	
			header("Location:../".yonetim."/toplu-sms.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/toplu-sms.html");
		exit();
	}
}

##Foto Galeri Kaydet ##
if(isset($_POST['galeri_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("galeri_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}	
		if($_POST['baskan']){$baskan = 1;}else{$baskan = 0;}
		$aciklama 	= $_POST['aciklama'];
		$keywords	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['kapak']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/fotogaleri");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/fotogaleri/kapak");
			if ($upload->processed)
			{
				$Kapak=''.$upload->file_dst_name.'';
			}
		}
		$Kapak=''.$upload->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO foto_galeri SET
				sira 	= ?,
				adi 	= ?,
				seo 	= ?,
				aciklama= ?,
				keywords= ?,
				description	= ?,
				durum 	= ?,
				baskan 	= ?,
				kapak 	= ?,
				dil 	= ?,
				tarih 	= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$adi,
				$seo,
				$aciklama,
				$keywords,
				$description,
				$durum,
				$baskan,
				$Kapak,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id = $db->lastInsertId();
			if($baskan == 1)
			{
				$GALERISorgu = $db->prepare("SELECT * FROM foto_galeri");
				$GALERISorgu->execute();
				$GALERIIslem = $GALERISorgu->fetchALL(PDO::FETCH_ASSOC);
				foreach ( $GALERIIslem as $GALERISonuc )
				{
					$sorgu = $db->prepare("UPDATE foto_galeri SET
						baskan		= ?
						WHERE id 	= ?");
					$baskanguncelle = $sorgu->execute(array(
						"0",
						$GALERISonuc['id']
					));
				}
				
				$sorgu = $db->prepare("UPDATE foto_galeri SET
					baskan		= ?
					WHERE id 	= ?");
				$baskanguncelle = $sorgu->execute(array(
					"1",
					$last_id
				));
			}
			
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Galeri Ekledi",
				'icon' 		=> "icon-camera",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı foto galeri ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['galeri_ekle'] = 'yes';
			header("Location:../".yonetim."/galeri-listele.html");
			exit();
		}
		else
		{
			$_SESSION['galeri_ekle'] = 'no';
			header("Location:../".yonetim."/galeri-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/galeri-ekle.html");
		exit();
	}
}

##Foto Galeri Güncelle ##
if(isset($_POST['galeri_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("galeri_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		if($_POST['baskan']){$baskan = 1;}else{$baskan = 0;}
		$aciklama 	= $_POST['aciklama'];
		$keywords 	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['kapak']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/fotogaleri");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/fotogaleri/kapak");
			if ($upload->processed)
			{
				$Kapak=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Kapak)){
			$resim_bul= $db->query("SELECT * FROM foto_galeri WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/fotogaleri/".$resim_bul['kapak']);
			$guncelle = $db->prepare("UPDATE foto_galeri SET kapak = ? WHERE id = ?");
			$guncelle->execute([$Kapak,$d_id]);
			$Kapak=''.$upload->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE foto_galeri SET
			sira 	= ?,
			adi 	= ?,
			seo 	= ?,
			aciklama= ?,
			keywords= ?,
			description	= ?,
			durum 	= ?,
			baskan 	= ?,
			tarih 	= ?
			WHERE id = ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$adi,
			$seo,
			$aciklama,
			$keywords,
			$description,
			$durum,
			$baskan,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 	= $d_id;
			if($baskan == 1)
			{
				$GALERISorgu = $db->prepare("SELECT * FROM foto_galeri");
				$GALERISorgu->execute();
				$GALERIIslem = $GALERISorgu->fetchALL(PDO::FETCH_ASSOC);
				foreach ( $GALERIIslem as $GALERISonuc )
				{
					$sorgu = $db->prepare("UPDATE foto_galeri SET
					baskan		= ?
					WHERE id 	= ?");
					$baskanguncelle = $sorgu->execute(array(
						"0",
						$GALERISonuc['id']
					));
				}

				$sorgu = $db->prepare("UPDATE foto_galeri SET
					baskan	= ?
					WHERE id = ?");
				$adresguncelle = $sorgu->execute(array(
					"1",
					$last_id
				));
			}
			
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Galeri Güncellendi",
				'icon' 		=> "icon-camera",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı foto galeriyi güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['galeri_guncelle'] = 'yes';
			header("Location:../".yonetim."/galeri-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['galeri_guncelle'] = 'no';
			header("Location:../".yonetim."/galeri-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/galeri-duzenle/".$d_id.".html");
		exit();
	}
}

##Foto Galeri Resim Sil##
if(@$_GET['galeriresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("galeriresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM foto_galeri WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/fotogaleri/".$resim_bul['kapak']);
		unlink("../".tema."/uploads/fotogaleri/kapak/".$resim_bul['kapak']);
		$sorgu = $db->prepare("UPDATE foto_galeri SET
					kapak	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Foto Galeri Silindi",
				'icon' 		=> "icon-trash",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı foto galeriyi sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['galeriresimsil'] = 'yes';
			header("Location:../".yonetim."/galeri-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['galeriresimsil'] = 'no';
			header("Location:../".yonetim."/galeri-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/galeri-duzenle/".$resimid.".html");
		exit();
	}
}

##Foto Galeri Sil##
if(@$_GET['galerisil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("galerisil");
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM foto_galeri WHERE id = '{$_GET['id']}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/fotogaleri/".$resim_bul['kapak']);
		unlink("../".tema."/uploads/fotogaleri/kapak/".$resim_bul['kapak']);
		$TSorgu = $db->prepare("DELETE FROM foto_galeri WHERE id = :id");
		$TSil	= $TSorgu->execute(array('id' => $_GET['id']));
		if($TSorgu->rowCount())
		{
			if($TSil)
			{
				$TopluSorgu = $db->prepare("SELECT * FROM fotograflar WHERE resimid = ?");
				$TopluSorgu->execute(array($_GET['id']));
				$Topluislem = $TopluSorgu->fetchALL(PDO::FETCH_ASSOC);
				foreach ( $Topluislem as $TopluSonuc )
				{
					$TSorgu = $db->prepare("DELETE FROM fotograflar WHERE id = :id");
					$TSorgu->execute(array('id' => $TopluSonuc['id']));
					unlink("../".tema."/uploads/fotogaleri/diger/".$TopluSonuc['resim']);
				}
				$last_id 		= $_GET['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Galeri Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı foto galeriyi sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['galerisil'] = 'yes';
				header("Location:../".yonetim."/galeri-listele.html");
				exit();
			}
			else
			{
				$_SESSION['galerisil'] = 'no';
				header("Location:../".yonetim."/galeri-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/galeri-listele.html");
		exit();
	}
}

##Foto Galeri Toplu Sil ##
if(isset($_POST['galeri_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("galeri_tumu");
	$url = $_POST['url'];
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM foto_galeri WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM foto_galeri WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$TopluSorguAlt = $db->prepare("SELECT * FROM fotograflar WHERE resimid = ?");
					$TopluSorguAlt->execute(array($i));
					$TopluislemAlt = $TopluSorguAlt->fetchALL(PDO::FETCH_ASSOC);
					foreach ( $TopluislemAlt as $TopluSonucAlt )
					{
						$TSorgu = $db->prepare("DELETE FROM fotograflar WHERE id = :id");
						$TSorgu->execute(array('id' => $TopluSonucAlt['id']));
						unlink("../".tema."/uploads/fotogaleri/diger/".$TopluSonucAlt['resim']);
					}
					unlink("../".tema."/uploads/fotogaleri/".$sayfa_bul['kapak']);
					unlink("../".tema."/uploads/fotogaleri/kapak/".$sayfa_bul['kapak']);
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Galeri Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı foto galeriyi sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['galeri_tumu'] = 'yes';
					header("Location:../".yonetim."/galeri-listele.html");
				}
				else
				{
					$_SESSION['galeri_tumu'] = 'no';
					header("Location:../".yonetim."/galeri-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/galeri-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/galeri-listele.html");
		exit();
	}	
}

##Foto Galeri Toplu Aktif ##
if(isset($_POST['galeri_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("galeri_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM foto_galeri WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE foto_galeri SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Galeri Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı foto galeriyi aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['galeri_aktif'] = 'yes';
					header("Location:../".yonetim."/galeri-listele.html");
				}
				else
				{
					$_SESSION['galeri_aktif'] = 'no';
					header("Location:../".yonetim."/galeri-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/galeri-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/galeri-listele.html");
		exit();
	}	
}

##Foto Galeri Toplu Pasif ##
if(isset($_POST['galeri_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("galeri_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM foto_galeri WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE foto_galeri SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Galeri Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı foto galeriyi pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['galeri_pasif'] = 'yes';
					header("Location:../".yonetim."/galeri-listele.html");
				}
				else
				{
					$_SESSION['galeri_pasif'] = 'no';
					header("Location:../".yonetim."/galeri-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/galeri-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/galeri-listele.html");
		exit();
	}	
}

##Foto Galeri Tümünü Sil ##
if(@$_GET['galeritumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("galeritumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$HSorgu = $db->prepare("SELECT * FROM foto_galeri");
		$HSorgu->execute();
		$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $Hislem as $HSonuc ){
			$TopluSorgu = $db->prepare("SELECT * FROM fotograflar WHERE resimid = ?");
			$TopluSorgu->execute(array($HSonuc['id']));
			$Topluislem = $TopluSorgu->fetchALL(PDO::FETCH_ASSOC);
			foreach ( $Topluislem as $TopluSonuc )
			{
				$TSorgu = $db->prepare("DELETE FROM fotograflar WHERE id = :id");
				$TSorgu->execute(array('id' => $TopluSonuc['id']));
				unlink("../".tema."/uploads/fotogaleri/diger/".$TopluSonuc['resim']);
			}
			unlink("../".tema."/uploads/fotogaleri/".$HSonuc['kapak']);
			unlink("../".tema."/uploads/fotogaleri/kapak/".$HSonuc['kapak']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE foto_galeri");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['galeritumunusil'] = 'yes';
			header("Location:../".yonetim."/galeri-listele.html");
			exit();
		}
		else
		{
			$_SESSION['galeritumunusil'] = 'no';
			header("Location:../".yonetim."/galeri-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/galeri-listele.html");
		exit();
	}
}

##Foto Galeri Sıra Ajax##
if(isset($_GET['galerisiralama']))
{
	if($_GET['galerisiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE foto_galeri SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Foto Sil##
if(@$_GET['fotosil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("fotosil");
	$galeriid = $_GET['galeriid'];
	if($_SESSION['rutbe'] == 0)
	{
		$id 	= $_GET['id'];		
		$galeri_bul= $db->query("SELECT * FROM foto_galeri WHERE id = '{$galeriid}'")->fetch(PDO::FETCH_ASSOC);
		$resim_bul= $db->query("SELECT * FROM fotograflar WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/fotogaleri/diger/".$resim_bul['resim']);
		$Sorgu = $db->prepare("DELETE FROM fotograflar WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Foto Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$galeri_bul['adi']."</strong> başlıklı foto galeriye ait fotoyu sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['fotosil'] = 'yes';
				header("Location:../".yonetim."/fotograflar/".$galeriid.".html");
				exit();
			}
			else
			{
				$_SESSION['fotosil'] = 'no';
				header("Location:../".yonetim."/fotograflar/".$galeriid.".html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/fotograflar/".$galeriid.".html");
		exit();
	}
}

##Foto Toplu Sil ##
if(isset($_POST['foto_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("foto_tumu");
	$galeriid = $_POST['galeriid'];
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['fotoid'])
		{
			$galeri_bul= $db->query("SELECT * FROM foto_galeri WHERE id = '{$galeriid}'")->fetch(PDO::FETCH_ASSOC);
			foreach($_POST['fotoid'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM fotograflar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM fotograflar WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Foto Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$galeri_bul['adi']."</strong> başlıklı foto galeriye ait fotoyu sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/fotogaleri/diger/".$resim_bul['resim']);
					$_SESSION['foto_tumu'] = 'yes';
					header("Location:../".yonetim."/fotograflar/".$galeriid.".html");
				}
				else
				{
					$_SESSION['foto_tumu'] = 'no';
					header("Location:../".yonetim."/fotograflar/".$galeriid.".html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/fotograflar/".$galeriid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/fotograflar/".$galeriid.".html");
		exit();
	}	
}

##Foto Güncelle ##
if(isset($_POST['foto_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("foto_guncelle");
	$galeriid = $_POST['galeriid'];
	if($_SESSION['rutbe'] == 0)
	{
		$d_id 	= $_POST['foto_id'];
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/fotogaleri/diger");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM fotograflar WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/fotogaleri/diger/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE fotograflar SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
			$Resim=''.$upload->file_dst_name.'';
		}

		if($guncelle)
		{
			$_SESSION['foto_guncelle'] = 'yes';
			header("Location:../".yonetim."/fotograflar/".$galeriid.".html");
			exit();
		}
		else
		{
			$_SESSION['foto_guncelle'] = 'no';
			header("Location:../".yonetim."/fotograflar/".$galeriid.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/fotograflar/".$galeriid.".html");
		exit();
	}
}

##Foto Kaydet ##
if(isset($_POST['foto_ekle']))
{
	$last_id  = $_POST['id'];
	cVCLmHLxbS_panelislemkontrol("foto_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$files = array();
		foreach ($_FILES['resimler'] as $k => $l) {
			foreach ($l as $i => $v) {
				if (!array_key_exists($i, $files))
					$files[$i] = array();
				$files[$i][$k] = $v;
			}
		}		

		foreach ($files as $file) 
		{
			$yukle = new Upload($file);
			if($yukle->uploaded) 
			{
				$yukle->file_auto_rename = true;
				$yukle->process("../".tema."/uploads/fotogaleri/diger");
				
				$yukle->allowed = array ( 'image/*' );
				if ($yukle->processed) 
				{
					$DigerResim=''.$yukle->file_dst_name.'';
					
					$sorgu = $db->prepare("INSERT INTO fotograflar SET
						resimid = ?,
						resim 	= ?");
					$yap = $sorgu->execute(array(
						$last_id,
						$DigerResim
					));
				}
			}
		}
		if($yap)
		{
			$_SESSION['foto_ekle'] = 'yes';
			header("Location:../".yonetim."/fotograflar/".$last_id.".html");
			exit();
		}
		else
		{
			$_SESSION['foto_ekle'] = 'no';
			header("Location:../".yonetim."/fotograflar/".$last_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/fotograflar/".$last_id.".html");
		exit();
	}
}

##Video Kaydet ##
if(isset($_POST['video_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("video_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$kod 		= $_POST['kod'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}				
		$aciklama 	= $_POST['aciklama'];
		$keywords	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/videogaleri");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/videogaleri/kapak");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		$Resim=''.$upload->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO video_galeri SET
				sira 	= ?,
				adi 	= ?,
				seo 	= ?,
				aciklama= ?,
				keywords= ?,
				description	= ?,
				durum 	= ?,
				kod 	= ?,
				resim 	= ?,
				dil 	= ?,
				tarih 	= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$adi,
				$seo,
				$aciklama,
				$keywords,
				$description,
				$durum,
				$kod,
				$Resim,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Video",
				'icon' 		=> "icon-social-youtube",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı videoyu ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['video_ekle'] = 'yes';
			header("Location:../".yonetim."/video-listele.html");
			exit();
		}
		else
		{
			$_SESSION['video_ekle'] = 'no';
			header("Location:../".yonetim."/video-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/video-ekle.html");
		exit();
	}
}

##Video Güncelle ##
if(isset($_POST['video_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("video_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$kod 		= $_POST['kod'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$aciklama 	= $_POST['aciklama'];
		$keywords 	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/videogaleri");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/videogaleri/kapak");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM video_galeri WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/videogaleri/".$resim_bul['resim']);
			unlink("../".tema."/uploads/videogaleri/kapak/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE video_galeri SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
			$Resim=''.$upload->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE video_galeri SET
			sira 	= ?,
			adi 	= ?,
			seo 	= ?,
			aciklama= ?,
			keywords= ?,
			description	= ?,
			durum 	= ?,
			kod 	= ?,
			tarih 	= ?
			WHERE id = ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$adi,
			$seo,
			$aciklama,
			$keywords,
			$description,
			$durum,
			$kod,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Video Güncellendi",
				'icon' 		=> "icon-social-youtube",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı videoyu güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['video_guncelle'] = 'yes';
			header("Location:../".yonetim."/video-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['video_guncelle'] = 'no';
			header("Location:../".yonetim."/video-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/video-duzenle/".$d_id.".html");
		exit();
	}
}

##Video Resim Sil##
if(@$_GET['videoresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("videoresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM video_galeri WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/videogaleri/".$resim_bul['resim']);
		unlink("../".tema."/uploads/videogaleri/kapak/".$resim_bul['resim']);
		$sorgu = $db->prepare("UPDATE video_galeri SET
					resim	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Video Resim Silindi",
				'icon' 		=> "icon-social-youtube",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı video resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['videoresimsil'] = 'yes';
			header("Location:../".yonetim."/video-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['videoresimsil'] = 'no';
			header("Location:../".yonetim."/video-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/video-duzenle/".$resimid.".html");
		exit();
	}
}

##Video Sil##
if(@$_GET['videosil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("videosil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM video_galeri WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/videogaleri/".$resim_bul['resim']);
		unlink("../".tema."/uploads/videogaleri/kapak/".$resim_bul['resim']);
		$Sorgu = $db->prepare("DELETE FROM video_galeri WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Video Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı videoyu sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['videosil'] = 'yes';
				header("Location:../".yonetim."/video-listele.html");
				exit();
			}
			else
			{
				$_SESSION['videosil'] = 'no';
				header("Location:../".yonetim."/video-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/video-listele.html");
		exit();
	}
}

##Video Toplu Sil ##
if(isset($_POST['video_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("video_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM video_galeri WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM video_galeri WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Video Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı videoyu sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/videogaleri/".$resim_bul['resim']);
					unlink("../".tema."/uploads/videogaleri/kapak/".$resim_bul['resim']);
					$_SESSION['video_tumu'] = 'yes';
					header("Location:../".yonetim."/video-listele.html");
				}
				else
				{
					$_SESSION['video_tumu'] = 'no';
					header("Location:../".yonetim."/video-listele.html");
					exit();
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/video-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/video-listele.html");
		exit();
	}	
}

##Video Toplu Aktif ##
if(isset($_POST['video_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("video_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM video_galeri WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE video_galeri SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Video Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$blog_bul['adi']."</strong> başlıklı videoyu aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['video_aktif'] = 'yes';
					header("Location:../".yonetim."/video-listele.html");
				}
				else
				{
					$_SESSION['video_aktif'] = 'no';
					header("Location:../".yonetim."/video-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/video-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/video-listele.html");
		exit();
	}	
}

##Video Toplu Pasif ##
if(isset($_POST['video_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("video_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM video_galeri WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE video_galeri SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Video Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$blog_bul['adi']."</strong> başlıklı videoyu pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['video_pasif'] = 'yes';
					header("Location:../".yonetim."/video-listele.html");
				}
				else
				{
					$_SESSION['video_pasif'] = 'no';
					header("Location:../".yonetim."/video-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/video-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/video-listele.html");
		exit();
	}	
}

##Video Tümünü Sil ##
if(@$_GET['videotumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("videotumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$ILSorgu = $db->prepare("SELECT * FROM video_galeri");
		$ILSorgu->execute();
		$ILislem = $ILSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $ILislem as $ILSonuc ){
			unlink("../".tema."/uploads/videogaleri/".$ILSonuc['resim']);
			unlink("../".tema."/uploads/videogaleri/kapak/".$ILSonuc['resim']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE video_galeri");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['videotumunusil'] = 'yes';
			header("Location:../".yonetim."/video-listele.html");
			exit();
		}
		else
		{
			$_SESSION['videotumunusil'] = 'no';
			header("Location:../".yonetim."/video-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/video-listele.html");
		exit();
	}
}

##Video Sıra Ajax##
if(isset($_GET['videosiralama']))
{
	if($_GET['videosiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE video_galeri SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Etkinlik Kaydet ##
if(isset($_POST['etkinlik_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("etkinlik_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$adi 			= $_POST['adi'];
		$yer 			= $_POST['yer'];
		$gmap 			= $_POST['gmap'];
		$seo			= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}			
		$baslama_tarih 	= strtotime($_POST['baslama_tarih']);
		$bitis_tarih 	= strtotime($_POST['bitis_tarih']);
		$aciklama 		= $_POST['aciklama'];
		$keywords		= $_POST['keywords'];
		$description	= $_POST['description'];		
		$tarih			= date('Y-m-d H:i:s');
		$tarih			= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/etkinlikler");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		$gitti=$Resim=''.$upload->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO etkinlikler SET
				adi 			= ?,
				yer 			= ?,
				gmap 			= ?,
				seo 			= ?,
				baslama_tarih 	= ?,
				bitis_tarih 	= ?,
				aciklama		= ?,
				keywords		= ?,
				description		= ?,
				durum 			= ?,
				resim 			= ?,
				dil 			= ?,
				tarih 			= ?");
		$Ekle = $sorgu->execute(array(
				$adi,
				$yer,
				$gmap,
				$seo,
				$baslama_tarih,
				$bitis_tarih,
				$aciklama,
				$keywords,
				$description,
				$durum,
				$Resim,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Etkinlik Ekledi",
				'icon' 		=> "icon-calendar",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> adında etkinlik ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['etkinlik_ekle'] = 'yes';
			header("Location:../".yonetim."/etkinlik-listele.html");
			exit();
		}
		else
		{
			$_SESSION['etkinlik_ekle'] = 'no';
			header("Location:../".yonetim."/etkinlik-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/etkinlik-ekle.html");
		exit();
	}
}

##Etkinlik Güncelle ##
if(isset($_POST['etkinlik_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("etkinlik_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$adi 			= $_POST['adi'];
		$yer 			= $_POST['yer'];
		$gmap 			= $_POST['gmap'];
		$seo			= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$baslama_tarih 	= strtotime($_POST['baslama_tarih']);
		$bitis_tarih 	= strtotime($_POST['bitis_tarih']);
		$aciklama 		= $_POST['aciklama'];
		$keywords 		= $_POST['keywords'];
		$description	= $_POST['description'];
		$tarih			= date('Y-m-d H:i:s');
		$tarih			= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/etkinlikler");
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM etkinlikler WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/etkinlikler/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE etkinlikler SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
			$Resim=''.$upload->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE etkinlikler SET
			adi 			= ?,
			yer 			= ?,
			gmap 			= ?,
			seo 			= ?,
			baslama_tarih 	= ?,
			bitis_tarih 	= ?,
			aciklama		= ?,
			keywords		= ?,
			description		= ?,
			durum 			= ?,
			tarih 			= ?
			WHERE id 		= ?");
		$guncelle = $sorgu->execute(array(
			$adi,
			$yer,
			$gmap,
			$seo,
			$baslama_tarih,
			$bitis_tarih,
			$aciklama,
			$keywords,
			$description,
			$durum,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Etkinlik Güncellendi",
				'icon' 		=> "icon-calendar",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı etkinliği güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['etkinlik_guncelle'] = 'yes';
			header("Location:../".yonetim."/etkinlik-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['etkinlik_guncelle'] = 'no';
			header("Location:../".yonetim."/etkinlik-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/etkinlik-duzenle/".$d_id.".html");
		exit();
	}
}

##Etkinlik Resim Sil##
if(@$_GET['etkinlikresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("etkinlikresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM etkinlikler WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/etkinlikler/".$resim_bul['resim']);
		$sorgu = $db->prepare("UPDATE etkinlikler SET
					resim	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Etkinlik Resim Silindi",
				'icon' 		=> "icon-trash",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı etkinliğim resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['etkinlikresimsil'] = 'yes';
			header("Location:../".yonetim."/etkinlik-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['etkinlikresimsil'] = 'no';
			header("Location:../".yonetim."/etkinlik-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/etkinlik-duzenle/".$resimid.".html");
		exit();
	}
}

##Etkinlik Sil##
if(@$_GET['etkinliksil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("etkinliksil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM etkinlikler WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/etkinlikler/".$resim_bul['resim']);
		$Sorgu = $db->prepare("DELETE FROM etkinlikler WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Etkinlik Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı etkinliği sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['etkinliksil'] = 'yes';
				header("Location:../".yonetim."/etkinlik-listele.html");
				exit();
			}
			else
			{
				$_SESSION['etkinliksil'] = 'no';
				header("Location:../".yonetim."/etkinlik-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/etkinlik-listele.html");
		exit();
	}
}

##Etkinlik Toplu Sil ##
if(isset($_POST['etkinlik_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("etkinlik_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM etkinlikler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM etkinlikler WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 	= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Etkinlik Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı etkinliği sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/etkinlikler/".$resim_bul['resim']);
					$_SESSION['etkinlik_tumu'] = 'yes';
					header("Location:../".yonetim."/etkinlik-listele.html");
				}
				else
				{
					$_SESSION['etkinlik_tumu'] = 'no';
					header("Location:../".yonetim."/etkinlik-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/etkinlik-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/etkinlik-listele.html");
		exit();
	}	
}

##Etkinlik Toplu Aktif ##
if(isset($_POST['etkinlik_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("etkinlik_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM etkinlikler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE etkinlikler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Etkinlik Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı etkinliği aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['etkinlik_aktif'] = 'yes';
					header("Location:../".yonetim."/etkinlik-listele.html");
				}
				else
				{
					$_SESSION['etkinlik_aktif'] = 'no';
					header("Location:../".yonetim."/etkinlik-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/etkinlik-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/etkinlik-listele.html");
		exit();
	}	
}

##Etkinlik Toplu Pasif ##
if(isset($_POST['etkinlik_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("etkinlik_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM etkinlikler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE etkinlikler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Etkinlik Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı etkinliği pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['etkinlik_pasif'] = 'yes';
					header("Location:../".yonetim."/etkinlik-listele.html");
				}
				else
				{
					$_SESSION['etkinlik_pasif'] = 'no';
					header("Location:../".yonetim."/etkinlik-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/etkinlik-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/etkinlik-listele.html");
		exit();
	}	
}

##Proje Kategori Kaydet ##
if(isset($_POST['proje_kategori_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("proje_kategori_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		$sira 		= $_POST['sira'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}		
		$keywords	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['kapak']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/proje_kategoriler");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/proje_kategoriler/kapak");
					
			if ($upload->processed)
			{
				$Kapak=''.$upload->file_dst_name.'';
			}
		}
		$Kapak=''.$upload->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO proje_kategori SET
				adi 	= ?,
				sira 	= ?,
				seo 	= ?,
				keywords= ?,
				description	= ?,
				kapak	= ?,
				durum 	= ?,
				dil 	= ?,
				tarih 	= ?");
		$Ekle = $sorgu->execute(array(
				$adi,
				$sira,
				$seo,
				$keywords,
				$description,
				$Kapak,
				$durum,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Proje Kategorisi Ekledi",
				'icon' 		=> "icon-layers",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı proje kategorisi ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['proje_kategori_ekle'] = 'yes';
			header("Location:../".yonetim."/proje-kategoriler.html");
			exit();
		}
		else
		{
			$_SESSION['proje_kategori_ekle'] = 'no';
			header("Location:../".yonetim."/proje-kategori-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/proje-kategori-ekle.html");
		exit();
	}
}

##Proje Kategori Güncelle ##
if(isset($_POST['proje_kategori_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("proje_kategori_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		$sira 		= $_POST['sira'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$keywords 	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['kapak']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/proje_kategoriler");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/proje_kategoriler/kapak");
			if ($upload->processed)
			{
				$Kapak=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Kapak)){
			$resim_bul= $db->query("SELECT * FROM proje_kategori WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/proje_kategoriler/".$resim_bul['kapak']);
			unlink("../".tema."/uploads/proje_kategoriler/kapak/".$resim_bul['kapak']);
			$guncelle = $db->prepare("UPDATE proje_kategori SET kapak = ? WHERE id = ?");
			$guncelle->execute([$Kapak,$d_id]);
			$Kapak=''.$upload->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE proje_kategori SET
			adi 	= ?,
			sira 	= ?,
			seo 	= ?,
			keywords= ?,
			description	= ?,
			durum 	= ?,
			tarih 	= ?
			WHERE id = ?");
		$guncelle = $sorgu->execute(array(
			$adi,
			$sira,
			$seo,
			$keywords,
			$description,
			$durum,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Proje Kategorisi Güncellendi",
				'icon' 		=> "icon-layers",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı proje kategorisini güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['proje_kategori_guncelle'] = 'yes';
			header("Location:../".yonetim."/proje-kategori-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['proje_kategori_guncelle'] = 'no';
			header("Location:../".yonetim."/proje-kategori-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/proje-kategori-duzenle/".$d_id.".html");
		exit();
	}
}

##Proje Kategori Kapak Sil##
if(@$_GET['proje_kategoriresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("proje_kategoriresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM proje_kategori WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/proje_kategoriler/".$resim_bul['kapak']);
		unlink("../".tema."/uploads/proje_kategoriler/kapak/".$resim_bul['kapak']);
		$sorgu = $db->prepare("UPDATE proje_kategori SET
					kapak	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Proje Kategori Kapak Resmi Silindi",
				'icon' 		=> "icon-layers",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı proje kategorisinin kapak resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['proje_kategoriresimsil'] = 'yes';
			header("Location:../".yonetim."/proje-kategori-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['proje_kategoriresimsil'] = 'no';
			header("Location:../".yonetim."/proje-kategori-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/proje-kategori-duzenle/".$resimid.".html");
		exit();
	}
}

##Proje Kategori Sil##
if(@$_GET['projekatsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("projekatsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM proje_kategori WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/proje_kategoriler/".$resim_bul['kapak']);
		unlink("../".tema."/uploads/proje_kategoriler/kapak/".$resim_bul['kapak']);
		$Sorgu = $db->prepare("DELETE FROM proje_kategori WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Proje Kategorisi Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı proje kategorisini sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['projekatsil'] = 'yes';
				header("Location:../".yonetim."/proje-kategoriler.html");
				exit();
			}
			else
			{
				$_SESSION['projekatsil'] = 'no';
				header("Location:../".yonetim."/proje-kategoriler.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/proje-kategoriler.html");
		exit();
	}
}

##Proje Kategori Toplu Sil ##
if(isset($_POST['proje_kat_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("proje_kat_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM proje_kategori WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM proje_kategori WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Proje Kategorisi Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı proje kategorisini sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/proje_kategoriler/".$resim_bul['kapak']);
					unlink("../".tema."/uploads/proje_kategoriler/kapak/".$resim_bul['kapak']);
					$_SESSION['proje_kat_tumu'] = 'yes';
					header("Location:../".yonetim."/proje-kategoriler.html");
				}
				else
				{
					$_SESSION['proje_kat_tumu'] = 'no';
					header("Location:../".yonetim."/proje-kategoriler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/proje-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/proje-kategoriler.html");
		exit();
	}	
}

##Proje Kategori Toplu Aktif ##
if(isset($_POST['proje_kat_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("proje_kat_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM proje_kategori WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE proje_kategori SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Proje Kategori Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı proje kategorisini aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['proje_kat_aktif'] = 'yes';
					header("Location:../".yonetim."/proje-kategoriler.html");
				}
				else
				{
					$_SESSION['proje_kat_aktif'] = 'no';
					header("Location:../".yonetim."/proje-kategoriler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/proje-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/proje-kategoriler.html");
		exit();
	}	
}

##Proje Kategori Toplu Pasif ##
if(isset($_POST['proje_kat_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("proje_kat_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM proje_kategori WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE proje_kategori SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Proje Kategori Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı proje kategorisini pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['proje_kat_pasif'] = 'yes';
					header("Location:../".yonetim."/proje-kategoriler.html");
				}
				else
				{
					$_SESSION['proje_kat_pasif'] = 'no';
					header("Location:../".yonetim."/proje-kategoriler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/proje-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/proje-kategoriler.html");
		exit();
	}	
}

##Proje Kategori Sıra Ajax##
if(isset($_GET['projekatsiralama']))
{
	if($_GET['projekatsiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE proje_kategori SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Proje Kategori Tümünü Sil ##
if(@$_GET['projekattumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("projekattumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$DUSorgu = $db->prepare("SELECT * FROM proje_kategori");
		$DUSorgu->execute();
		$DUislem = $DUSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $DUislem as $DUSonuc ){
			unlink("../".tema."/uploads/proje_kategoriler/".$DUSonuc['kapak']);
			unlink("../".tema."/uploads/proje_kategoriler/kapak/".$DUSonuc['kapak']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE proje_kategori");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['projekattumunusil'] = 'yes';
			header("Location:../".yonetim."/proje-kategoriler.html");
			exit();
		}
		else
		{
			$_SESSION['projekattumunusil'] = 'no';
			header("Location:../".yonetim."/proje-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
}
}

## Bilgilendirme Kutusu Ekle ##
if(isset($_POST['bilgilendirme_ekle']))
{
    cVCLmHLxbS_panelislemkontrol("bilgilendirme_ekle");
    if($_SESSION['rutbe'] == 0)
    {
        $baslik = $_POST['baslik'];
        $aciklama = $_POST['aciklama'];
        $ikon = $_POST['ikon'];
        $link = $_POST['link'];
        $sira = intval($_POST['sira']);
        $durum = isset($_POST['durum']) ? 1 : 0;
        
        $sorgu = $db->prepare("INSERT INTO bilgilendirme_kutulari SET
            baslik = ?,
            aciklama = ?,
            ikon = ?,
            link = ?,
            sira = ?,
            durum = ?,
            dil = ?");
        $sorgu->execute(array(
            $baslik,
            $aciklama,
            $ikon,
            $link,
            $sira,
            $durum,
            $_SESSION['admin_dil']
        ));
        
        if($sorgu) {
            $son_id = $db->lastInsertId();
			$_SESSION['bilgilendirme_ekle'] = 'yes';
			header("Location:".$_SERVER['HTTP_REFERER']."");
			exit();
        } else {
            $_SESSION['bilgilendirme_ekle'] = 'no';
			header("Location:".$_SERVER['HTTP_REFERER']."");
			exit();
        }
    } else {
        $_SESSION['bilgilendirme_ekle'] = 'demo';
		header("Location:".$_SERVER['HTTP_REFERER']."");
		exit();
    }
    exit();
}

## Bilgilendirme Kutusu Düzenle ##
if(isset($_POST['bilgilendirme_guncelle']))
{
    cVCLmHLxbS_panelislemkontrol("bilgilendirme_duzenle");
    if($_SESSION['rutbe'] == 0)
    {
        $baslik = $_POST['baslik'];
        $aciklama = $_POST['aciklama'];
        $ikon = $_POST['ikon'];
        $link = $_POST['link'];
        $sira = intval($_POST['sira']);
        $durum = isset($_POST['durum']) ? 1 : 0;
        $id = intval($_POST['id']);
        
        $sorgu = $db->prepare("UPDATE bilgilendirme_kutulari SET
            baslik = ?,
            aciklama = ?,
            ikon = ?,
            link = ?,
            sira = ?,
            durum = ?
            WHERE id = ? AND dil = ?");
        $sorgu->execute(array(
            $baslik,
            $aciklama,
            $ikon,
            $link,
            $sira,
            $durum,
            $id,
            $_SESSION['admin_dil']
        ));
        
        if($sorgu) {
			$_SESSION['bilgilendirme_duzenle'] = 'yes';
			header("Location:".$_SERVER['HTTP_REFERER']."");
			exit();
        } else {
			$_SESSION['bilgilendirme_duzenle'] = 'no';
			header("Location:".$_SERVER['HTTP_REFERER']."");
			exit();
        }
    } else {
        $_SESSION['bilgilendirme_duzenle'] = 'demo';
		header("Location:".$_SERVER['HTTP_REFERER']."");
		exit();
    }
    exit();
}

## Bilgilendirme Kutusu Durum Değiştir ##
if(isset($_POST['bilgilendirme_durum']))
{
    cVCLmHLxbS_panelislemkontrol("bilgilendirme_durum");
    if($_SESSION['rutbe'] == 0)
    {
        $id = intval($_POST['id']);
        $durum = intval($_POST['durum']);
        
        $sorgu = $db->prepare("UPDATE bilgilendirme_kutulari SET durum = ? WHERE id = ? AND dil = ?");
        $sorgu->execute(array($durum, $id, $_SESSION['admin_dil']));
        
        if($sorgu) {
					$_SESSION['bilgilendirme_durum'] = 'yes';
					header("Location:".$_SERVER['HTTP_REFERER']."");
					exit();
        } else {
					$_SESSION['bilgilendirme_durum'] = 'no';
					header("Location:".$_SERVER['HTTP_REFERER']."");
					exit();
        }
    } else {
        $_SESSION['bilgilendirme_durum'] = 'demo';
		header("Location:".$_SERVER['HTTP_REFERER']."");
		exit();
    }
    exit();
}

## Bilgilendirme Kutusu Sil ##
if(isset($_GET['bilgilendirme_sil']) && $_GET['bilgilendirme_sil'] == 'ok')
{
    cVCLmHLxbS_panelislemkontrol("bilgilendirme_sil");
    if($_SESSION['rutbe'] == 0)
    {
        $id = intval($_GET['id']);
        
        $sorgu = $db->prepare("DELETE FROM bilgilendirme_kutulari WHERE id = ? AND dil = ?");
        $sorgu->execute(array($id, $_SESSION['admin_dil']));
        
        if($sorgu) {
					$_SESSION['bilgilendirme_sil'] = 'yes';
					header("Location:".$_SERVER['HTTP_REFERER']."");
					exit();
        } else {
					$_SESSION['bilgilendirme_sil'] = 'no';
					header("Location:".$_SERVER['HTTP_REFERER']."");
					exit();
        }
    } else {
        $_SESSION['bilgilendirme_sil'] = 'demo';
		header("Location:".$_SERVER['HTTP_REFERER']."");
		exit();
    }
    exit();
}

## Bilgilendirme Kutusu Toplu Sil ##
if(isset($_POST['bilgilendirme_toplu_sil']))
{
    cVCLmHLxbS_panelislemkontrol("bilgilendirme_toplu_sil");
    if($_SESSION['rutbe'] == 0 && !empty($_POST['id']) && is_array($_POST['id']))
    {
        $silinen = 0;
        $hata = 0;
        
        foreach($_POST['id'] as $id) {
            $id = intval($id);
            if($id > 0) {
                $sorgu = $db->prepare("DELETE FROM bilgilendirme_kutulari WHERE id = ? AND dil = ?");
                if($sorgu->execute(array($id, $_SESSION['admin_dil']))) {
                    $silinen++;
                } else {
                    $hata++;
                }
            }
        }
        
        $mesaj = $silinen . ' adet bilgilendirme kutusu silindi.';
        if($hata > 0) {
            $mesaj .= ' ' . $hata . ' adet bilgilendirme kutusu silinirken hata oluştu.';
        }
        
			$_SESSION['bilgilendirme_toplu_sil'] = 'yes';
			header("Location:".$_SERVER['HTTP_REFERER']."");
			exit();
    } else {
        $_SESSION['bilgilendirme_toplu_sil'] = 'demo';
		header("Location:".$_SERVER['HTTP_REFERER']."");
		exit();
    }
    exit();
}

##Proje Kaydet ##
if(isset($_POST['proje_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("proje_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 				= $_POST["sira"];
		$videoid 			= $_POST["videoid"];
		$spot 				= $_POST["spot"];
		$kategori 			= $_POST["kategori"];
		$adi 				= $_POST['adi'];
		$seo				= cVCLmHLxbS_seo($adi);
		$aciklama			= $_POST["aciklama"];
		$description		= $_POST["description"];
		$keywords			= $_POST["keywords"];
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}				
		if($_POST['anasayfa']){$anasayfa = 1;}else{$anasayfa = 0;}				
		$tarih				= $_POST['tarih'];
		$tarihg				= $_POST['tarihg'];
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/projeler");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/projeler/kapak");
					
			if ($upload->processed)
			{
				$Kapak=''.$upload->file_dst_name.'';
			}
		}
		$Kapak=''.$upload->file_dst_name.'';
		
		$files = array();
		foreach ($_FILES['resimler'] as $k => $l) {
			foreach ($l as $i => $v) {
				if (!array_key_exists($i, $files))
					$files[$i] = array();
				$files[$i][$k] = $v;
			}
		}
		
		$sorgu = $db->prepare("INSERT INTO projeler SET
				sira 				= ?,
				kategori 			= ?,
				adi 				= ?,
				seo 				= ?,
				aciklama 			= ?,
				spot 				= ?,
				videoid 			= ?,
				description			= ?,
				keywords			= ?,
				kapak 				= ?,
				durum 				= ?,
				anasayfa 			= ?,
				dil 				= ?,
				tarih 				= ?,
				tarihg 				= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$kategori,
				$adi,
				$seo,
				$aciklama,
				$spot,
				$videoid,
				$description,
				$keywords,
				$Kapak,
				$durum,
				$anasayfa,
				$_SESSION['admin_dil'],
				$tarih,
				$tarihg
				));
		if($Ekle)
		{
			$last_id  = $db->lastInsertId();
			foreach ($files as $file) 
			{
				$yukle = new Upload($file);
				if($yukle->uploaded) 
				{
					$yukle->file_auto_rename = true;
					$yukle->process("../".tema."/uploads/projeler/diger");
					
					$yukle->allowed = array ( 'image/*' );
					if ($yukle->processed) 
					{
						$DigerResim=''.$yukle->file_dst_name.'';
						
						$sorgu = $db->prepare("INSERT INTO projeresim SET
							pid 	= ?,
							resim 	= ?
							");
						$yap = $sorgu->execute(array(
							$last_id,
							$DigerResim
						));
					}
				}
			}
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Proje Ekledi",
				'icon' 		=> "icon-layers",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı proje ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['proje_ekle'] = 'yes';
			header("Location:../".yonetim."/projeler.html");
			exit();
		}
		else
		{
			$_SESSION['proje_ekle'] = 'no';
			header("Location:../".yonetim."/proje-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/proje-ekle.html");
		exit();
	}
}

##Proje Güncelle ##
if(isset($_POST['proje_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("proje_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 				= $_POST["sira"];
		$videoid 			= $_POST["videoid"];
		$spot 				= $_POST["spot"];
		$kategori 			= $_POST["kategori"];
		$adi 				= $_POST['adi'];
		$seo				= cVCLmHLxbS_seo($adi);
		$aciklama			= $_POST["aciklama"];
		$description		= $_POST["description"];
		$keywords			= $_POST["keywords"];
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		if($_POST['anasayfa']){$anasayfa = 1;}else{$anasayfa = 0;}
		$tarih				= $_POST['tarih'];
		$tarihg				= $_POST['tarihg'];
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/projeler");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/projeler/kapak");
			if ($upload->processed)
			{
				$Kapak=''.$upload->file_dst_name.'';
			}
		}
		
		$files = array();
		foreach ($_FILES['resimler'] as $k => $l) {
			foreach ($l as $i => $v) {
				if (!array_key_exists($i, $files))
					$files[$i] = array();
				$files[$i][$k] = $v;
			}
		}
		
		if(isset($Kapak)){
			$resim_bul= $db->query("SELECT * FROM projeler WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/projeler/".$resim_bul['kapak']);
			unlink("../".tema."/uploads/projeler/kapak/".$resim_bul['kapak']);
			$guncelle = $db->prepare("UPDATE projeler SET kapak = ? WHERE id = ?");
			$guncelle->execute([$Kapak,$d_id]);
			$Kapak=''.$upload->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE projeler SET
			sira 			= ?,
			kategori 		= ?,
			adi 			= ?,
			seo 			= ?,
			aciklama 		= ?,
			spot 			= ?,
			videoid 		= ?,
			description		= ?,
			keywords		= ?,
			durum 			= ?,
			anasayfa 		= ?,
			tarih 			= ?,
			tarihg 			= ?
			WHERE id 		= ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$kategori,
			$adi,
			$aciklama,
			$detay_linki,
			$seo,
			$aciklama,
			$spot,
			$videoid,
			$description,
			$keywords,
			$durum,
			$anasayfa,
			$tarih,
			$tarihg,
			$d_id
		));
		if($guncelle)
		{
			$sonid = $_POST['id'];
			foreach ($files as $file) 
			{
				$yukle = new Upload($file);
				if($yukle->uploaded) 
				{
					$yukle->file_auto_rename = true;
					$yukle->process("../".tema."/uploads/projeler/diger");
					
					$yukle->allowed = array ( 'image/*' );
					if ($yukle->processed) 
					{
						$DigerResim=''.$yukle->file_dst_name.'';
						
						$sorgu = $db->prepare("INSERT INTO projeresim SET
							pid 	= ?,
							resim 	= ?
							");
						$yap = $sorgu->execute(array(
							$sonid,
							$DigerResim
						));
					}
				}
			}
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Proje Güncellendi",
				'icon' 		=> "icon-layers",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı projeyi güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['proje_guncelle'] = 'yes';
			header("Location:../".yonetim."/proje-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['proje_guncelle'] = 'no';
			header("Location:../".yonetim."/proje-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/proje-duzenle/".$d_id.".html");
		exit();
	}
}

##Proje Kapak Sil##
if(@$_GET['projeresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("projeresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM projeler WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/projeler/".$resim_bul['kapak']);
		unlink("../".tema."/uploads/projeler/kapak/".$resim_bul['kapak']);
		$sorgu = $db->prepare("UPDATE projeler SET
					kapak	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Proje Kapak Resmi Silindi",
				'icon' 		=> "icon-layers",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı projenin kapak resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['projeresimsil'] = 'yes';
			header("Location:../".yonetim."/proje-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['projeresimsil'] = 'no';
			header("Location:../".yonetim."/proje-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/proje-duzenle/".$resimid.".html");
		exit();
	}
}

##Proje Toplu Resim Sil##
if(@$_GET['projetopluresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("projetopluresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM projeresim WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/projeler/diger/".$resim_bul['resim']);
		$TSorgu = $db->prepare("DELETE FROM projeresim WHERE id = :id");
		$TSil	= $TSorgu->execute(array('id' => $resimid));
		if($TSil)
		{
			$_SESSION['projetopluresimsil'] = 'yes';
			header("Location:../".yonetim."/proje-duzenle/".$_GET['id'].".html");
			exit();
		}
		else
		{
			$_SESSION['projetopluresimsil'] = 'no';
			header("Location:../".yonetim."/proje-duzenle/".$_GET['id'].".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/proje-duzenle/".$_GET['id'].".html");
		exit();
	}
}

##Proje Sil##
if(@$_GET['projesil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("projesil");
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM projeler WHERE id = '{$_GET['id']}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/projeler/".$resim_bul['kapak']);
		unlink("../".tema."/uploads/projeler/kapak/".$resim_bul['kapak']);
		$TSorgu = $db->prepare("DELETE FROM projeler WHERE id = :id");
		$TSil	= $TSorgu->execute(array('id' => $_GET['id']));
		if($TSorgu->rowCount())
		{
			if($TSil)
			{
				$TopluSorgu = $db->prepare("SELECT * FROM projeresim WHERE pid = ?");
				$TopluSorgu->execute(array($_GET['id']));
				$Topluislem = $TopluSorgu->fetchALL(PDO::FETCH_ASSOC);
				foreach ( $Topluislem as $TopluSonuc )
				{
					$TSorgu = $db->prepare("DELETE FROM projeresim WHERE id = :id");
					$TSorgu->execute(array('id' => $TopluSonuc['id']));
					unlink("../".tema."/uploads/projeler/diger/".$TopluSonuc['resim']);
				}
				$last_id 		= $_GET['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Proje Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı projeyi sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['projesil'] = 'yes';
				header("Location:../".yonetim."/projeler.html");
				exit();
			}
			else
			{
				$_SESSION['projesil'] = 'no';
				header("Location:../".yonetim."/projeler.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/projeler.html");
		exit();
	}
}

##Proje Toplu Sil ##
if(isset($_POST['proje_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("proje_tumu");
	$url = $_POST['url'];
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM projeler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM projeler WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$TopluSorguAlt = $db->prepare("SELECT * FROM projeresim WHERE pid = ?");
					$TopluSorguAlt->execute(array($i));
					$TopluislemAlt = $TopluSorguAlt->fetchALL(PDO::FETCH_ASSOC);
					foreach ( $TopluislemAlt as $TopluSonucAlt )
					{
						$TSorgu = $db->prepare("DELETE FROM projeresim WHERE id = :id");
						$TSorgu->execute(array('id' => $TopluSonucAlt['id']));
						unlink("../".tema."/uploads/projeler/diger/".$TopluSonucAlt['resim']);
					}
					unlink("../".tema."/uploads/projeler/".$sayfa_bul['kapak']);
					unlink("../".tema."/uploads/projeler/kapak/".$sayfa_bul['kapak']);
					$last_id 		= $i;					
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Proje Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı projeyi sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['proje_tumu'] = 'yes';
					header("Location:../".yonetim."/projeler.html");
				}
				else
				{
					$_SESSION['proje_tumu'] = 'no';
					header("Location:../".yonetim."/projeler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/projeler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/projeler.html");
		exit();
	}	
}

##Proje Toplu Aktif ##
if(isset($_POST['proje_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("proje_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM projeler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE projeler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Proje Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı projeyi aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['proje_aktif'] = 'yes';
					header("Location:../".yonetim."/projeler.html");
				}
				else
				{
					$_SESSION['proje_aktif'] = 'no';
					header("Location:../".yonetim."/projeler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/projeler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/projeler.html");
		exit();
	}	
}

##Proje Toplu Pasif ##
if(isset($_POST['proje_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("proje_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM projeler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE projeler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Proje Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı projeyi pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['proje_pasif'] = 'yes';
					header("Location:../".yonetim."/projeler.html");
				}
				else
				{
					$_SESSION['proje_pasif'] = 'no';
					header("Location:../".yonetim."/projeler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/projeler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/projeler.html");
		exit();
	}	
}

##Proje Tümünü Sil ##
if(@$_GET['projetumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("projetumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$HSorgu = $db->prepare("SELECT * FROM projeler");
		$HSorgu->execute();
		$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $Hislem as $HSonuc ){
			$TopluSorgu = $db->prepare("SELECT * FROM projeresim WHERE pid = ?");
			$TopluSorgu->execute(array($HSonuc['id']));
			$Topluislem = $TopluSorgu->fetchALL(PDO::FETCH_ASSOC);
			foreach ( $Topluislem as $TopluSonuc )
			{
				$TSorgu = $db->prepare("DELETE FROM projeresim WHERE id = :id");
				$TSorgu->execute(array('id' => $TopluSonuc['id']));
				unlink("../".tema."/uploads/projeler/diger/".$TopluSonuc['resim']);
			}
			unlink("../".tema."/uploads/projeler/".$HSonuc['kapak']);
			unlink("../".tema."/uploads/projeler/kapak/".$HSonuc['kapak']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE projeler");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['projetumunusil'] = 'yes';
			header("Location:../".yonetim."/projeler.html");
			exit();
		}
		else
		{
			$_SESSION['projetumunusil'] = 'no';
			header("Location:../".yonetim."/projeler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/projeler.html");
		exit();
	}
}

##Proje Sıra Ajax##
if(isset($_GET['projesiralama']))
{
	if($_GET['projesiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE projeler SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Duyuru Kaydet ##
if(isset($_POST['duyuru_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("duyuru_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}		
		if($_POST['anasayfa']){$anasayfa = 1;}else{$anasayfa = 0;}				
		$aciklama 	= $_POST['aciklama'];
		$keywords	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= $_POST['tarih'];
		$tarihg		= $_POST['tarihg'];
		
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/duyurular");
			
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		$Resim=''.$upload->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO duyurular SET
				sira 		= ?,
				adi 		= ?,
				seo 		= ?,
				aciklama	= ?,
				keywords	= ?,
				description	= ?,
				durum 		= ?,
				anasayfa 	= ?,
				resim 		= ?,
				dil 		= ?,
				tarih 		= ?,
				tarihg 		= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$adi,
				$seo,
				$aciklama,
				$keywords,
				$description,
				$durum,
				$anasayfa,
				$Resim,
				$_SESSION['admin_dil'],
				$tarih,
				$tarihg
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Duyuru",
				'icon' 		=> "ti-announcement",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı duyuru ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['duyuru_ekle'] = 'yes';
			header("Location:../".yonetim."/duyuru-listele.html");
			exit();
		}
		else
		{
			$_SESSION['duyuru_ekle'] = 'no';
			header("Location:../".yonetim."/duyuru-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/duyuru-ekle.html");
		exit();
	}
}

##Duyuru Güncelle ##
if(isset($_POST['duyuru_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("duyuru_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		if($_POST['anasayfa']){$anasayfa = 1;}else{$anasayfa = 0;}		
		$aciklama 	= $_POST['aciklama'];
		$keywords 	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= $_POST['tarih'];
		$tarihg		= $_POST['tarihg'];
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/duyurular");
	
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM duyurular WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/duyurular/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE duyurular SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
			$Resim=''.$upload->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE duyurular SET
			sira 		= ?,
			adi 		= ?,
			seo 		= ?,
			aciklama	= ?,
			keywords	= ?,
			description	= ?,
			durum 		= ?,
			anasayfa 	= ?,
			tarih 		= ?,
			tarihg 		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$adi,
			$seo,
			$aciklama,
			$keywords,
			$description,
			$durum,
			$anasayfa,
			$tarih,
			$tarihg,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Duyuru Güncellendi",
				'icon' 		=> "ti-announcement",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı duyuruyu güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['duyuru_guncelle'] = 'yes';
			header("Location:../".yonetim."/duyuru-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['duyuru_guncelle'] = 'no';
			header("Location:../".yonetim."/duyuru-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/duyuru-duzenle/".$d_id.".html");
		exit();
	}
}

##Duyuru Resim Sil##
if(@$_GET['duyururesimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("duyururesimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM duyurular WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/duyurular/".$resim_bul['resim']);
		$sorgu = $db->prepare("UPDATE duyurular SET
					resim	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Duyuru Resim Silindi",
				'icon' 		=> "icon-book-open",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı duyuru resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['duyururesimsil'] = 'yes';
			header("Location:../".yonetim."/duyuru-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['duyururesimsil'] = 'no';
			header("Location:../".yonetim."/duyuru-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/duyuru-duzenle/".$resimid.".html");
		exit();
	}
}

##Duyuru Sil##
if(@$_GET['duyurusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("duyurusil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM duyurular WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/duyurular/".$resim_bul['resim']);
		$Sorgu = $db->prepare("DELETE FROM duyurular WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Duyuru Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı duyuruyu sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['duyurusil'] = 'yes';
				header("Location:../".yonetim."/duyuru-listele.html");
				exit();
			}
			else
			{
				$_SESSION['duyurusil'] = 'no';
				header("Location:../".yonetim."/duyuru-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/duyuru-listele.html");
		exit();
	}
}

##Duyuru Toplu Sil ##
if(isset($_POST['duyuru_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("duyuru_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM duyurular WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM duyurular WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Duyuru Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı duyuruyu sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/duyurular/".$resim_bul['resim']);
					$_SESSION['duyuru_tumu'] = 'yes';
					header("Location:../".yonetim."/duyuru-listele.html");
				}
				else
				{
					$_SESSION['duyuru_tumu'] = 'no';
					header("Location:../".yonetim."/duyuru-listele.html");
					exit();
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/duyuru-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/duyuru-listele.html");
		exit();
	}	
}

##Duyuru Toplu Aktif ##
if(isset($_POST['duyuru_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("duyuru_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM duyurular WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE duyurular SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Duyuru Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$blog_bul['adi']."</strong> başlıklı duyuruyu aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['duyuru_aktif'] = 'yes';
					header("Location:../".yonetim."/duyuru-listele.html");
				}
				else
				{
					$_SESSION['duyuru_aktif'] = 'no';
					header("Location:../".yonetim."/duyuru-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/duyuru-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/duyuru-listele.html");
		exit();
	}	
}

##Duyuru Toplu Pasif ##
if(isset($_POST['duyuru_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("duyuru_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM duyurular WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE duyurular SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Duyuru Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$blog_bul['adi']."</strong> başlıklı duyuruyu pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['duyuru_pasif'] = 'yes';
					header("Location:../".yonetim."/duyuru-listele.html");
				}
				else
				{
					$_SESSION['duyuru_pasif'] = 'no';
					header("Location:../".yonetim."/duyuru-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/duyuru-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/duyuru-listele.html");
		exit();
	}	
}

##Duyuru Tümünü Sil ##
if(@$_GET['duyurutumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("duyurutumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$DUSorgu = $db->prepare("SELECT * FROM duyurular");
		$DUSorgu->execute();
		$DUislem = $DUSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $DUislem as $DUSonuc ){
			unlink("../".tema."/uploads/duyurular/".$DUSonuc['resim']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE duyurular");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['duyurutumunusil'] = 'yes';
			header("Location:../".yonetim."/duyuru-listele.html");
			exit();
		}
		else
		{
			$_SESSION['duyurutumunusil'] = 'no';
			header("Location:../".yonetim."/duyuru-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/duyuru-listele.html");
		exit();
	}
}

##Duyuru Sıra Ajax##
if(isset($_GET['duyurusiralama']))
{
	if($_GET['duyurusiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE duyurular SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##İlan Kaydet ##
if(isset($_POST['ilan_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("ilan_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}		
		if($_POST['anasayfa']){$anasayfa = 1;}else{$anasayfa = 0;}				
		$aciklama 	= $_POST['aciklama'];
		$keywords	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/ilanlar");
			
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		$Resim=''.$upload->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO ilanlar SET
				sira 		= ?,
				adi 		= ?,
				seo 		= ?,
				aciklama	= ?,
				keywords	= ?,
				description	= ?,
				durum 		= ?,
				anasayfa 	= ?,
				resim 		= ?,
				dil 		= ?,
				tarih 		= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$adi,
				$seo,
				$aciklama,
				$keywords,
				$description,
				$durum,
				$anasayfa,
				$Resim,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni İlan",
				'icon' 		=> "icon-note",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı ilan ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['ilan_ekle'] = 'yes';
			header("Location:../".yonetim."/ilan-listele.html");
			exit();
		}
		else
		{
			$_SESSION['ilan_ekle'] = 'no';
			header("Location:../".yonetim."/ilan-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ilan-ekle.html");
		exit();
	}
}

##İlan Güncelle ##
if(isset($_POST['ilan_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("ilan_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		if($_POST['anasayfa']){$anasayfa = 1;}else{$anasayfa = 0;}		
		$aciklama 	= $_POST['aciklama'];
		$keywords 	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/ilanlar");
	
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM ilanlar WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/ilanlar/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE ilanlar SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
			$Resim=''.$upload->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE ilanlar SET
			sira 		= ?,
			adi 		= ?,
			seo 		= ?,
			aciklama	= ?,
			keywords	= ?,
			description	= ?,
			durum 		= ?,
			anasayfa 	= ?,
			tarih 		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$adi,
			$seo,
			$aciklama,
			$keywords,
			$description,
			$durum,
			$anasayfa,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "İlan Güncellendi",
				'icon' 		=> "icon-note",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı ilanı güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['ilan_guncelle'] = 'yes';
			header("Location:../".yonetim."/ilan-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['ilan_guncelle'] = 'no';
			header("Location:../".yonetim."/ilan-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ilan-duzenle/".$d_id.".html");
		exit();
	}
}

##İlan Resim Sil##
if(@$_GET['ilanresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("ilanresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM ilanlar WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/ilanlar/".$resim_bul['resim']);
		$sorgu = $db->prepare("UPDATE ilanlar SET
					resim	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "İlan Resim Silindi",
				'icon' 		=> "icon-book-open",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı ilan resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['ilanresimsil'] = 'yes';
			header("Location:../".yonetim."/ilan-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['ilanresimsil'] = 'no';
			header("Location:../".yonetim."/ilan-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ilan-duzenle/".$resimid.".html");
		exit();
	}
}

##İlan Sil##
if(@$_GET['ilansil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("ilansil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM ilanlar WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/ilanlar/".$resim_bul['resim']);
		$Sorgu = $db->prepare("DELETE FROM ilanlar WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "İlan Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı ilanı sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['ilansil'] = 'yes';
				header("Location:../".yonetim."/ilan-listele.html");
				exit();
			}
			else
			{
				$_SESSION['ilansil'] = 'no';
				header("Location:../".yonetim."/ilan-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ilan-listele.html");
		exit();
	}
}

##İlan Toplu Sil ##
if(isset($_POST['ilan_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("ilan_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM ilanlar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM ilanlar WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "İlan Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı ilanı sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/ilanlar/".$resim_bul['resim']);
					$_SESSION['ilan_tumu'] = 'yes';
					header("Location:../".yonetim."/ilan-listele.html");
				}
				else
				{
					$_SESSION['ilan_tumu'] = 'no';
					header("Location:../".yonetim."/ilan-listele.html");
					exit();
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/ilan-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ilan-listele.html");
		exit();
	}	
}

##İlan Toplu Aktif ##
if(isset($_POST['ilan_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("ilan_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM ilanlar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE ilanlar SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "İlan Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$blog_bul['adi']."</strong> başlıklı ilanı aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['ilan_aktif'] = 'yes';
					header("Location:../".yonetim."/ilan-listele.html");
				}
				else
				{
					$_SESSION['ilan_aktif'] = 'no';
					header("Location:../".yonetim."/ilan-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/ilan-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ilan-listele.html");
		exit();
	}	
}

##İlan Toplu Pasif ##
if(isset($_POST['ilan_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("ilan_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM ilanlar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE ilanlar SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "İlan Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$blog_bul['adi']."</strong> başlıklı ilanı pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['ilan_pasif'] = 'yes';
					header("Location:../".yonetim."/ilan-listele.html");
				}
				else
				{
					$_SESSION['ilan_pasif'] = 'no';
					header("Location:../".yonetim."/ilan-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/ilan-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ilan-listele.html");
		exit();
	}	
}

##İlan Tümünü Sil ##
if(@$_GET['ilantumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("ilantumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$ILSorgu = $db->prepare("SELECT * FROM ilanlar");
		$ILSorgu->execute();
		$ILislem = $ILSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $ILislem as $ILSonuc ){
			unlink("../".tema."/uploads/ilanlar/".$ILSonuc['resim']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE ilanlar");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['ilantumunusil'] = 'yes';
			header("Location:../".yonetim."/ilan-listele.html");
			exit();
		}
		else
		{
			$_SESSION['ilantumunusil'] = 'no';
			header("Location:../".yonetim."/ilan-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ilan-listele.html");
		exit();
	}
}

##İlan Sıra Ajax##
if(isset($_GET['ilansiralama']))
{
	if($_GET['ilansiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE ilanlar SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##İhale Kaydet ##
if(isset($_POST['ihale_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("ihale_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 			= $_POST['sira'];
		$adi 			= $_POST['adi'];
		$birim 			= $_POST['birim'];
		$baslama_tarih 	= $_POST['baslama_tarih'];
		$baslatma_saat 	= $_POST['baslatma_saat'];
		$bitis_tarih 	= $_POST['bitis_tarih'];
		$bitis_saat 		= $_POST['bitis_saat'];
		$ihale_durum 	= $_POST['ihale_durum'];
		$seo			= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}		
		if($_POST['anasayfa']){$anasayfa = 1;}else{$anasayfa = 0;}				
		$aciklama 		= $_POST['aciklama'];
		$keywords		= $_POST['keywords'];
		$description	= $_POST['description'];
		$tarih			= date('Y-m-d H:i:s');
		$tarih			= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/ihaleler");
			
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		$Resim=''.$upload->file_dst_name.'';
		
		$upload2 = new upload($_FILES['dosya']);
		if ($upload2->uploaded)
		{
			$upload2->file_auto_rename = true;
			$upload2->process("../".tema."/uploads/ihaleler/dosya");
			if ($upload2->processed)
			{
				$Dosya=''.$upload2->file_dst_name.'';
			}
		}
		$Dosya=''.$upload2->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO ihaleler SET
				sira 		= ?,
				adi 		= ?,
				birim 		= ?,
				baslama_tarih = ?,
				baslatma_saat = ?,
				bitis_tarih = ?,
				bitis_saat = ?,
				ihale_durum = ?,
				seo 		= ?,
				aciklama	= ?,
				keywords	= ?,
				description	= ?,
				durum 		= ?,
				anasayfa 	= ?,
				resim 		= ?,
				dosya 		= ?,
				dil 		= ?,
				tarih 		= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$adi,
				$birim,
				$baslama_tarih,
				$baslatma_saat,
				$bitis_tarih,
				$bitis_saat,
				$ihale_durum,
				$seo,
				$aciklama,
				$keywords,
				$description,
				$durum,
				$anasayfa,
				$Resim,
				$Dosya,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni İhale",
				'icon' 		=> "icon-hourglass",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong class='text-info'>".$adi."</strong> başlıklı ihale ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['ihale_ekle'] = 'yes';
			header("Location:../".yonetim."/ihale-listele.html");
			exit();
		}
		else
		{
			$_SESSION['ihale_ekle'] = 'no';
			header("Location:../".yonetim."/ihale-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ihale-ekle.html");
		exit();
	}
}

##İhale Güncelle ##
if(isset($_POST['ihale_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("ihale_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 			= $_POST['sira'];
		$adi 			= $_POST['adi'];
		$birim 			= $_POST['birim'];
		$baslama_tarih 	= $_POST['baslama_tarih'];
		$baslatma_saat 	= $_POST['baslatma_saat'];
		$bitis_tarih 	= $_POST['bitis_tarih'];
		$bitis_saat 		= $_POST['bitis_saat'];
		$ihale_durum 	= $_POST['ihale_durum'];
		$seo			= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		if($_POST['anasayfa']){$anasayfa = 1;}else{$anasayfa = 0;}		
		$aciklama 		= $_POST['aciklama'];
		$keywords 		= $_POST['keywords'];
		$description	= $_POST['description'];
		$tarih			= date('Y-m-d H:i:s');
		$tarih			= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/ihaleler");
	
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		$upload2 = new upload($_FILES['dosya']);
		if ($upload2->uploaded)
		{
			$upload2->file_auto_rename = true;
			$upload2->process("../".tema."/uploads/ihaleler/dosya");
			if ($upload2->processed)
			{
				$Dosya=''.$upload2->file_dst_name.'';
			}
		}
		
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM ihaleler WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/ihaleler/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE ihaleler SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
			$Resim=''.$upload->file_dst_name.'';
		}
		
		if(isset($Dosya)){
			$resim_bul= $db->query("SELECT * FROM ihaleler WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/ihaleler/dosya/".$resim_bul['dosya']);
			$guncelle = $db->prepare("UPDATE ihaleler SET dosya = ? WHERE id = ?");
			$guncelle->execute([$Dosya,$d_id]);
			$Dosya=''.$upload2->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE ihaleler SET
			sira 		= ?,
			adi 		= ?,
			birim 		= ?,
			baslama_tarih = ?,
			baslatma_saat = ?,
			bitis_tarih = ?,
			bitis_saat 	= ?,
			ihale_durum = ?,
			seo 		= ?,
			aciklama	= ?,
			keywords	= ?,
			description	= ?,
			durum 		= ?,
			anasayfa 	= ?,
			tarih 		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$adi,
			$birim,
			$baslama_tarih,
			$baslatma_saat,
			$bitis_tarih,
			$bitis_saat,
			$ihale_durum,
			$seo,
			$aciklama,
			$keywords,
			$description,
			$durum,
			$anasayfa,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "İhale Güncellendi",
				'icon' 		=> "icon-hourglass",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong class='text-info'>".$adi."</strong> başlıklı ihaleyi güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['ihale_guncelle'] = 'yes';
			header("Location:../".yonetim."/ihale-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['ihale_guncelle'] = 'no';
			header("Location:../".yonetim."/ihale-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ihale-duzenle/".$d_id.".html");
		exit();
	}
}

##İhale Resim Sil##
if(@$_GET['ihaleresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("ihaleresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM ihaleler WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/ihaleler/".$resim_bul['resim']);
		$sorgu = $db->prepare("UPDATE ihaleler SET
					resim	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "İhale Resim Silindi",
				'icon' 		=> "icon-book-open",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong class='text-info'>".$resim_bul['adi']."</strong> başlıklı ihale resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['ihaleresimsil'] = 'yes';
			header("Location:../".yonetim."/ihale-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['ihaleresimsil'] = 'no';
			header("Location:../".yonetim."/ihale-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ihale-duzenle/".$resimid.".html");
		exit();
	}
}

##İhale Dosya Sil##
if(@$_GET['ihaledosyasil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("ihaledosyasil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM ihaleler WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/ihaleler/dosya/".$resim_bul['dosya']);
		$sorgu = $db->prepare("UPDATE ihaleler SET
					dosya	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "İhale Dosya Silindi",
				'icon' 		=> "icon-trash",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong class='text-info'>".$resim_bul['adi']."</strong> başlıklı ihalenin dosyasını sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['ihaledosyasil'] = 'yes';
			header("Location:../".yonetim."/ihale-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['ihaledosyasil'] = 'no';
			header("Location:../".yonetim."/ihale-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ihale-duzenle/".$resimid.".html");
		exit();
	}
}

##İhale Sil##
if(@$_GET['ihalesil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("ihalesil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM ihaleler WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/ihaleler/".$resim_bul['resim']);
		unlink("../".tema."/uploads/ihaleler/dosya/".$resim_bul['dosya']);
		$Sorgu = $db->prepare("DELETE FROM ihaleler WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "İhale Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong class='text-info'>".$resim_bul['adi']."</strong> başlıklı ihaleyi sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['ihalesil'] = 'yes';
				header("Location:../".yonetim."/ihale-listele.html");
				exit();
			}
			else
			{
				$_SESSION['ihalesil'] = 'no';
				header("Location:../".yonetim."/ihale-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ihale-listele.html");
		exit();
	}
}

##İhale Toplu Sil ##
if(isset($_POST['ihale_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("ihale_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM ihaleler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM ihaleler WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "İhale Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong class='text-info'>".$resim_bul['adi']."</strong> başlıklı ihaleyi sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/ihaleler/".$resim_bul['resim']);
					unlink("../".tema."/uploads/ihaleler/dosya/".$resim_bul['dosya']);
					$_SESSION['ihale_tumu'] = 'yes';
					header("Location:../".yonetim."/ihale-listele.html");
				}
				else
				{
					$_SESSION['ihale_tumu'] = 'no';
					header("Location:../".yonetim."/ihale-listele.html");
					exit();
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/ihale-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ihale-listele.html");
		exit();
	}	
}

##İhale Toplu Aktif ##
if(isset($_POST['ihale_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("ihale_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM ihaleler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE ihaleler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "İhale Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong class='text-info'>".$blog_bul['adi']."</strong> başlıklı ihaleyi aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['ihale_aktif'] = 'yes';
					header("Location:../".yonetim."/ihale-listele.html");
				}
				else
				{
					$_SESSION['ihale_aktif'] = 'no';
					header("Location:../".yonetim."/ihale-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/ihale-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ihale-listele.html");
		exit();
	}	
}

##İhale Toplu Pasif ##
if(isset($_POST['ihale_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("ihale_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM ihaleler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE ihaleler SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "İhale Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong class='text-info'>".$blog_bul['adi']."</strong> başlıklı ihaleyi pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['ihale_pasif'] = 'yes';
					header("Location:../".yonetim."/ihale-listele.html");
				}
				else
				{
					$_SESSION['ihale_pasif'] = 'no';
					header("Location:../".yonetim."/ihale-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/ihale-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ihale-listele.html");
		exit();
	}	
}

##İhale Tümünü Sil ##
if(@$_GET['ihaletumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("ihaletumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$IHSorgu = $db->prepare("SELECT * FROM ihaleler");
		$IHSorgu->execute();
		$IHislem = $IHSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $IHislem as $IHSonuc ){
			unlink("../".tema."/uploads/ihaleler/".$IHSonuc['resim']);
			unlink("../".tema."/uploads/ihaleler/dosya/".$IHSonuc['dosya']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE ihaleler");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['ihaletumunusil'] = 'yes';
			header("Location:../".yonetim."/ihale-listele.html");
		}
		else
		{
			$_SESSION['ihaletumunusil'] = 'no';
			header("Location:../".yonetim."/ihale-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/ihale-listele.html");
		exit();
	}
}

##İhale Sıra Ajax##
if(isset($_GET['ihalesiralama']))
{
	if($_GET['ihalesiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE ihaleler SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Slider Menü Kaydet ##
if(isset($_POST['SmenuKaydet']))
{
	cVCLmHLxbS_panelislemkontrol("SmenuKaydet");
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= $_POST['menu_sira'];
		$menu_ust 		= $_POST['menu_ust'];
		$menu_kisa 		= $_POST['menu_kisa'];
		$menu_renk 		= $_POST['menu_renk'];
		$menu_icon 		= cVCLmHLxbS_icon($_POST['menu_icon']);
		$menu_isim 		= $_POST['menu_isim'];
		$menu_url 		= $_POST['menu_url'];
		$link	 		= $_POST['link'];
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		$menu_durum 	= $_POST['menu_durum'];
		$anasayfa 	= $_POST['anasayfa'];

		$sorgu = $db->prepare("INSERT INTO slidermenu SET
			menu_sira 	= ?,
			menu_ust 	= ?,
			menu_icon 	= ?,
			menu_isim 	= ?,
			menu_kisa 	= ?,
			menu_renk 	= ?,
			menu_url 	= ?,
			link 		= ?,
			sekme 		= ?,
			dil 		= ?,
			menu_durum 	= ?,
			anasayfa 	= ?");
		$Ekle = $sorgu->execute(array(
			$menu_sira,
			"0",
			$menu_icon,
			$menu_isim,
			$menu_kisa,
			$menu_renk,
			$menu_url,
			$link,
			$sekme,
			$_SESSION['admin_dil'],
			$menu_durum,
			$anasayfa
		));

		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Slider Menü",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_isim."</strong> adında siteye slider menü ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['SmenuKaydet'] = 'yes';
			header("Location:../".yonetim."/slider-menu.html");
			exit();
		}
		else
		{
			$_SESSION['SmenuKaydet'] = 'no';
			header("Location:../".yonetim."/slider-menu.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/slider-menu.html");
		exit();
	}
}

##Slider Menü Güncelle ##
if(isset($_POST['SmenuGuncelle']))
{
	cVCLmHLxbS_panelislemkontrol("SmenuGuncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= $_POST['menu_sira'];
		$menu_ust 		= $_POST['menu_ust'];
		$menu_isim 		= $_POST['menu_isim'];
		$menu_kisa 		= $_POST['menu_kisa'];
		$menu_renk 		= $_POST['menu_renk'];
		$menu_icon 		= cVCLmHLxbS_icon($_POST['menu_icon']);
		$menu_url 		= $_POST['menu_url'];		
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		if($_POST['menu_url'] != "0")
		{
			$link = " ";
		}
		if($_POST['menu_url'] == "0")
		{
			$link	= $_POST['link'];
		}
		$menu_durum 	= $_POST['menu_durum'];
		$anasayfa 	= $_POST['anasayfa'];
		
		$sorgu = $db->prepare("UPDATE slidermenu SET
				menu_sira 	= ?,
				menu_ust 	= ?,
				menu_isim 	= ?,
				menu_kisa 	= ?,
				menu_renk 	= ?,
				menu_icon 	= ?,
				menu_url 	= ?,
				link 		= ?,
				sekme 		= ?,
				menu_durum 	= ?,
				anasayfa 	= ?
				WHERE id = ?");
		$guncelle = $sorgu->execute(array(
				$menu_sira,
				"0",
				$menu_isim,
				$menu_kisa,
				$menu_renk,
				$menu_icon,
				$menu_url,
				$link,
				$sekme,
				$menu_durum,
				$anasayfa,
				$d_id
			));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Slider Menü Güncellendi",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_isim."</strong> başlıklı slider menüyü güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['SmenuGuncelle'] = 'yes';		
			header("Location:../".yonetim."/slider-menu-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['SmenuGuncelle'] = 'no';
			header("Location:../".yonetim."/slider-menu-duzenle/".$d_id.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/slider-menu-duzenle/".$d_id.".html");
		exit();
	}
}

##Slider Menü Sil##
if(@$_GET['Smenusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("Smenusil");
	$id = $_GET['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$Orta_menu_bul	= $db->query("SELECT * FROM slidermenu WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		$Orta_menu_sorgu 	= $db->prepare("DELETE FROM slidermenu WHERE id = :id");
		$Orta_menu_sil 	= $Orta_menu_sorgu->execute(array('id' => $id));
		if($Orta_menu_sorgu->rowCount())
		{
			if($Orta_menu_sil)
			{
				$TopluSorgu = $db->prepare("SELECT * FROM slidermenu WHERE menu_ust = ?");
				$TopluSorgu->execute(array($_GET['id']));
				$Topluislem = $TopluSorgu->fetchALL(PDO::FETCH_ASSOC);
				foreach ( $Topluislem as $TopluSonuc )
				{
					$last_id 		= $TopluSonuc['id'];
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Slider Menü Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$TopluSonuc['menu_isim']."</strong> başlıklı slider menüyü sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$TSorgu = $db->prepare("DELETE FROM slidermenu WHERE id = :id");
					$TSorgu->execute(array('id' => $TopluSonuc['id']));
				}
				$last_id 		= $id ;
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Slider Menü Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_bul['menu_isim']."</strong> başlıklı slider menüyü sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['Smenusil'] = 'yes';		
				header("Location:../".yonetim."/slider-menu.html");
				exit();
			}
			else
			{
				$_SESSION['Smenusil'] = 'no';		
				header("Location:../".yonetim."/slider-menu.html");
				exit();
			}
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/slider-menu.html");
		exit();
	}
}

##Orta Menü Kaydet ##
if(isset($_POST['OmenuKaydet']))
{
	cVCLmHLxbS_panelislemkontrol("OmenuKaydet");
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= $_POST['menu_sira'];
		$menu_ust 		= $_POST['menu_ust'];
		$menu_kisa 		= $_POST['menu_kisa'];
		$menu_renk 		= $_POST['menu_renk'];
		$menu_icon 		= cVCLmHLxbS_icon($_POST['menu_icon']);
		$menu_isim 		= $_POST['menu_isim'];
		$menu_url 		= $_POST['menu_url'];
		$link	 		= $_POST['link'];
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		$menu_durum 	= $_POST['menu_durum'];
		
		$sorgu = $db->prepare("INSERT INTO ortamenu SET
			menu_sira 	= ?,
			menu_ust 	= ?,
			menu_icon 	= ?,
			menu_isim 	= ?,
			menu_kisa 	= ?,
			menu_renk 	= ?,
			menu_url 	= ?,
			link 		= ?,
			sekme 		= ?,
			dil 		= ?,
			menu_durum 	= ?");
		$Ekle = $sorgu->execute(array(
			$menu_sira,
			"0",
			$menu_icon,
			$menu_isim,
			$menu_kisa,
			$menu_renk,
			$menu_url,
			$link,
			$sekme,
			$_SESSION['admin_dil'],
			$menu_durum
		));

		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Orta Menü",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: blue;'>".$menu_isim."</strong> adında siteye orta menü ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['OmenuKaydet'] = 'yes';
			header("Location:../".yonetim."/orta-menu.html");
			exit();
		}
		else
		{
			$_SESSION['OmenuKaydet'] = 'no';
			header("Location:../".yonetim."/orta-menu.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/orta-menu.html");
		exit();
	}
}

##Orta Menü Güncelle ##
if(isset($_POST['OmenuGuncelle']))
{
	cVCLmHLxbS_panelislemkontrol("OmenuGuncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= $_POST['menu_sira'];
		$menu_ust 		= $_POST['menu_ust'];
		$menu_isim 		= $_POST['menu_isim'];
		$menu_kisa 		= $_POST['menu_kisa'];
		$menu_renk 		= $_POST['menu_renk'];
		$menu_icon 		= cVCLmHLxbS_icon($_POST['menu_icon']);
		$menu_url 		= $_POST['menu_url'];		
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		if($_POST['menu_url'] != "0")
		{
			$link = " ";
		}
		if($_POST['menu_url'] == "0")
		{
			$link	= $_POST['link'];
		}
		$menu_durum 	= $_POST['menu_durum'];
		
		
		$sorgu = $db->prepare("UPDATE ortamenu SET
				menu_sira 	= ?,
				menu_ust 	= ?,
				menu_isim 	= ?,
				menu_kisa 	= ?,
				menu_renk 	= ?,
				menu_icon 	= ?,
				menu_url 	= ?,
				link 		= ?,
				sekme 		= ?,
				menu_durum 	= ?
				WHERE id = ?");
		$guncelle = $sorgu->execute(array(
				$menu_sira,
				"0",
				$menu_isim,
				$menu_kisa,
				$menu_renk,
				$menu_icon,
				$menu_url,
				$link,
				$sekme,
				$menu_durum,
				$d_id
			));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Orta Menü Güncellendi",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: blue;'>".$menu_isim."</strong> başlıklı orta menüyü güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['OmenuGuncelle'] = 'yes';		
			header("Location:../".yonetim."/orta-menu-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['OmenuGuncelle'] = 'no';
			header("Location:../".yonetim."/orta-menu-duzenle/".$d_id.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/orta-menu-duzenle/".$d_id.".html");
		exit();
	}
}

##Orta Menü Sil##
if(@$_GET['Omenusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("Omenusil");
	$id = $_GET['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$Orta_menu_bul	= $db->query("SELECT * FROM ortamenu WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		$Orta_menu_sorgu 	= $db->prepare("DELETE FROM ortamenu WHERE id = :id");
		$Orta_menu_sil 	= $Orta_menu_sorgu->execute(array('id' => $id));
		if($Orta_menu_sorgu->rowCount())
		{
			if($Orta_menu_sil)
			{
				$TopluSorgu = $db->prepare("SELECT * FROM ortamenu WHERE menu_ust = ?");
				$TopluSorgu->execute(array($_GET['id']));
				$Topluislem = $TopluSorgu->fetchALL(PDO::FETCH_ASSOC);
				foreach ( $Topluislem as $TopluSonuc )
				{
					$last_id 		= $TopluSonuc['id'];
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Orta Menü Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: blue;'>".$TopluSonuc['menu_isim']."</strong> başlıklı orta menüyü sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$TSorgu = $db->prepare("DELETE FROM ortamenu WHERE id = :id");
					$TSorgu->execute(array('id' => $TopluSonuc['id']));
				}
				$last_id 		= $id ;
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Orta Menü Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: blue;'>".$menu_bul['menu_isim']."</strong> başlıklı orta menüyü sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['Omenusil'] = 'yes';		
				header("Location:../".yonetim."/orta-menu.html");
				exit();
			}
			else
			{
				$_SESSION['Omenusil'] = 'no';		
				header("Location:../".yonetim."/orta-menu.html");
				exit();
			}
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/orta-menu.html");
		exit();
	}
}

##Kolay Menü Kaydet ##
if(isset($_POST['KOLAYmenuKaydet']))
{
	cVCLmHLxbS_panelislemkontrol("KOLAYmenuKaydet");
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= $_POST['menu_sira'];
		$menu_ust 		= $_POST['menu_ust'];
		$menu_isim 		= $_POST['menu_isim'];
		$menu_url 		= $_POST['menu_url'];
		$link	 		= $_POST['link'];
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		$menu_durum 	= $_POST['menu_durum'];
		
		$sorgu = $db->prepare("INSERT INTO kolaymenu SET
			menu_sira 	= ?,
			menu_ust 	= ?,
			menu_isim 	= ?,
			menu_url 	= ?,
			link 		= ?,
			sekme 		= ?,
			dil 		= ?,
			menu_durum 	= ?");
		$Ekle = $sorgu->execute(array(
			$menu_sira,
			"0",
			$menu_isim,
			$menu_url,
			$link,
			$sekme,
			$_SESSION['admin_dil'],
			$menu_durum
		));

		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Kolay Menü",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_isim."</strong> adında siteye kolay menü ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['KOLAYmenuKaydet'] = 'yes';
			header("Location:../".yonetim."/kolay-menu.html");
			exit();
		}
		else
		{
			$_SESSION['KOLAYmenuKaydet'] = 'no';
			header("Location:../".yonetim."/kolay-menu.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/kolay-menu.html");
		exit();
	}
}

##Kolay Menü Güncelle ##
if(isset($_POST['KOLAYmenuGuncelle']))
{
	cVCLmHLxbS_panelislemkontrol("KOLAYmenuGuncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= $_POST['menu_sira'];
		$menu_ust 		= $_POST['menu_ust'];
		$menu_isim 		= $_POST['menu_isim'];
		$menu_url 		= $_POST['menu_url'];
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		if($_POST['menu_url'] != "0")
		{
			$link = " ";
		}
		if($_POST['menu_url'] == "0")
		{
			$link	= $_POST['link'];
		}
		$menu_durum 	= $_POST['menu_durum'];
		
		
		$sorgu = $db->prepare("UPDATE kolaymenu SET
				menu_sira 	= ?,
				menu_ust 	= ?,
				menu_isim 	= ?,
				menu_url 	= ?,
				link 		= ?,
				sekme 		= ?,
				menu_durum 	= ?
				WHERE id = ?");
		$guncelle = $sorgu->execute(array(
				$menu_sira,
				"0",
				$menu_isim,
				$menu_url,
				$link,
				$sekme,
				$menu_durum,
				$d_id
			));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Kolay Menü Güncellendi",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_isim."</strong> başlıklı kolay menüyü güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['KOLAYmenuGuncelle'] = 'yes';		
			header("Location:../".yonetim."/kolay-menu-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['KOLAYmenuGuncelle'] = 'no';
			header("Location:../".yonetim."/kolay-menu-duzenle/".$d_id.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/kolay-menu-duzenle/".$d_id.".html");
		exit();
	}
}

##Kolay Menü Sil##
if(@$_GET['KOLAYmenusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("KOLAYmenusil");
	$id = $_GET['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$Orta_menu_bul	= $db->query("SELECT * FROM kolaymenu WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		$Orta_menu_sorgu 	= $db->prepare("DELETE FROM kolaymenu WHERE id = :id");
		$Orta_menu_sil 	= $Orta_menu_sorgu->execute(array('id' => $id));
		if($Orta_menu_sorgu->rowCount())
		{
			if($Orta_menu_sil)
			{
				$TopluSorgu = $db->prepare("SELECT * FROM kolaymenu WHERE menu_ust = ?");
				$TopluSorgu->execute(array($_GET['id']));
				$Topluislem = $TopluSorgu->fetchALL(PDO::FETCH_ASSOC);
				foreach ( $Topluislem as $TopluSonuc )
				{
					$last_id 		= $TopluSonuc['id'];
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Kolay Menü Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$TopluSonuc['menu_isim']."</strong> başlıklı kolay menüyü sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$TSorgu = $db->prepare("DELETE FROM kolaymenu WHERE id = :id");
					$TSorgu->execute(array('id' => $TopluSonuc['id']));
				}
				$last_id 		= $id ;
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Kolay Menü Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_bul['menu_isim']."</strong> başlıklı kolay menüyü sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['KOLAYmenusil'] = 'yes';		
				header("Location:../".yonetim."/kolay-menu.html");
				exit();
			}
			else
			{
				$_SESSION['KOLAYmenusil'] = 'no';		
				header("Location:../".yonetim."/kolay-menu.html");
				exit();
			}
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/kolay-menu.html");
		exit();
	}
}

##Başkan Sayfalar Kaydet ##
if(isset($_POST['BASKANmenuKaydet']))
{
	cVCLmHLxbS_panelislemkontrol("BASKANmenuKaydet");
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= $_POST['menu_sira'];
		$menu_ust 		= $_POST['menu_ust'];
		$menu_isim 		= $_POST['menu_isim'];
		$menu_url 		= $_POST['menu_url'];
		$link	 		= $_POST['link'];
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		$menu_durum 	= $_POST['menu_durum'];
		
		$sorgu = $db->prepare("INSERT INTO baskanmenu SET
			menu_sira 	= ?,
			menu_ust 	= ?,
			menu_isim 	= ?,
			menu_url 	= ?,
			link 		= ?,
			sekme 		= ?,
			dil 		= ?,
			menu_durum 	= ?");
		$Ekle = $sorgu->execute(array(
			$menu_sira,
			"0",
			$menu_isim,
			$menu_url,
			$link,
			$sekme,
			$_SESSION['admin_dil'],
			$menu_durum
		));

		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Başkan Sayfa",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_isim."</strong> adında siteye başkan sayfa ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['BASKANmenuKaydet'] = 'yes';
			header("Location:../".yonetim."/baskan-ayarlar.html#baskan_sayfalar");			
			exit();
		}
		else
		{
			$_SESSION['BASKANmenuKaydet'] = 'no';
			header("Location:../".yonetim."/baskan-ayarlar.html#baskan_sayfalar");	
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/baskan-ayarlar.html#baskan_sayfalar");	
		exit();
	}
}

##Başkan Sayfalar Güncelle ##
if(isset($_POST['BASKANmenuGuncelle']))
{
	cVCLmHLxbS_panelislemkontrol("BASKANmenuGuncelle");
	$d_id 	= $_POST['baskanid'];
	if($_SESSION['rutbe'] == 0)
	{
		$menu_sira 		= $_POST['menu_sira'];
		$menu_ust 		= $_POST['menu_ust'];
		$menu_isim 		= $_POST['menu_isim'];
		$menu_url 		= $_POST['menu_url'];
		if($_POST['sekme']){$sekme = 1;}else{$sekme = 0;}
		if($_POST['menu_url'] != "0")
		{
			$link = " ";
		}
		if($_POST['menu_url'] == "0")
		{
			$link	= $_POST['link'];
		}
		$menu_durum 	= $_POST['menu_durum'];
		
		
		$sorgu = $db->prepare("UPDATE baskanmenu SET
				menu_sira 	= ?,
				menu_ust 	= ?,
				menu_isim 	= ?,
				menu_url 	= ?,
				link 		= ?,
				sekme 		= ?,
				menu_durum 	= ?
				WHERE id = ?");
		$guncelle = $sorgu->execute(array(
				$menu_sira,
				"0",
				$menu_isim,
				$menu_url,
				$link,
				$sekme,
				$menu_durum,
				$d_id
			));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Başkan Sayfa Güncellendi",
				'icon' 		=> "icon-menu",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_isim."</strong> başlıklı başkan sayfayı güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['BASKANmenuGuncelle'] = 'yes';		
			header("Location:../".yonetim."/baskan-ayarlar/".$d_id.".html#baskan_sayfalar");
			exit();
		}
		else
		{
			$_SESSION['BASKANmenuGuncelle'] = 'no';
			header("Location:../".yonetim."/baskan-ayarlar/".$d_id.".html#baskan_sayfalar");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/baskan-ayarlar/".$d_id.".html#baskan_sayfalar");
		exit();
	}
}

##Başkan Sayfalar Sil##
if(@$_GET['BASKANmenusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("BASKANmenusil");
	$id = $_GET['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$Orta_menu_bul	= $db->query("SELECT * FROM baskanmenu WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		$Orta_menu_sorgu 	= $db->prepare("DELETE FROM baskanmenu WHERE id = :id");
		$Orta_menu_sil 	= $Orta_menu_sorgu->execute(array('id' => $id));
		if($Orta_menu_sorgu->rowCount())
		{
			if($Orta_menu_sil)
			{
				$TopluSorgu = $db->prepare("SELECT * FROM baskanmenu WHERE menu_ust = ?");
				$TopluSorgu->execute(array($_GET['id']));
				$Topluislem = $TopluSorgu->fetchALL(PDO::FETCH_ASSOC);
				foreach ( $Topluislem as $TopluSonuc )
				{
					$last_id 		= $TopluSonuc['id'];
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Başkan Sayfa Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$TopluSonuc['menu_isim']."</strong> başlıklı başkan sayfayı sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$TSorgu = $db->prepare("DELETE FROM baskanmenu WHERE id = :id");
					$TSorgu->execute(array('id' => $TopluSonuc['id']));
				}
				$last_id 		= $id ;
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Başkan Sayfa Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkseagreen;'>".$menu_bul['menu_isim']."</strong> başlıklı başkan sayfa sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['BASKANmenusil'] = 'yes';		
				header("Location:../".yonetim."/baskan-ayarlar.html#baskan_sayfalar");
				exit();
			}
			else
			{
				$_SESSION['BASKANmenusil'] = 'no';		
				header("Location:../".yonetim."/baskan-ayarlar.html#baskan_sayfalar");
				exit();
			}
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/baskan-ayarlar.html#baskan_sayfalar");
		exit();
	}
}

##Meclis Kararı Kaydet ##
if(isset($_POST['karar_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("karar_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 			= $_POST['sira'];
		$adi 			= $_POST['adi'];
		$kararno 		= $_POST['kararno'];
		$seo			= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}					
		$spot 			= $_POST['spot'];
		$aciklama 		= $_POST['aciklama'];
		$keywords		= $_POST['keywords'];
		$description	= $_POST['description'];
		$tarih			= $_POST['tarih'];
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/meclis_kararlari");
			
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		$Resim=''.$upload->file_dst_name.'';
		
		$upload2 = new upload($_FILES['dosya']);
		if ($upload2->uploaded)
		{
			$upload2->file_auto_rename = true;
			$upload2->process("../".tema."/uploads/meclis_kararlari/dosya");
			if ($upload2->processed)
			{
				$Dosya=''.$upload2->file_dst_name.'';
			}
		}
		$Dosya=''.$upload2->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO meclis_kararlari SET
				sira 		= ?,
				adi 		= ?,
				kararno 	= ?,
				seo 		= ?,
				spot		= ?,
				aciklama	= ?,
				keywords	= ?,
				description	= ?,
				durum 		= ?,				
				resim 		= ?,
				dosya 		= ?,
				dil 		= ?,
				tarih 		= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$adi,
				$kararno,				
				$seo,
				$spot,
				$aciklama,
				$keywords,
				$description,
				$durum,
				$Resim,
				$Dosya,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Meclis Kararı",
				'icon' 		=> "icon-folder-alt",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı meclis kararı ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['karar_ekle'] = 'yes';
			header("Location:../".yonetim."/karar-listele.html");
			exit();
		}
		else
		{
			$_SESSION['karar_ekle'] = 'no';
			header("Location:../".yonetim."/karar-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/karar-ekle.html");
		exit();
	}
}

##Meclis Karar Güncelle ##
if(isset($_POST['karar_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("karar_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 			= $_POST['sira'];
		$adi 			= $_POST['adi'];
		$kararno 		= $_POST['kararno'];
		$seo			= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}	
		$spot 			= $_POST['spot'];
		$aciklama 		= $_POST['aciklama'];
		$keywords 		= $_POST['keywords'];
		$description	= $_POST['description'];
		$tarih			= $_POST['tarih'];
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/meclis_kararlari");
	
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		$upload2 = new upload($_FILES['dosya']);
		if ($upload2->uploaded)
		{
			$upload2->file_auto_rename = true;
			$upload2->process("../".tema."/uploads/meclis_kararlari/dosya");
			if ($upload2->processed)
			{
				$Dosya=''.$upload2->file_dst_name.'';
			}
		}
		
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM meclis_kararlari WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/meclis_kararlari/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE meclis_kararlari SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
			$Resim=''.$upload->file_dst_name.'';
		}
		
		if(isset($Dosya)){
			$resim_bul= $db->query("SELECT * FROM meclis_kararlari WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/meclis_kararlari/dosya/".$resim_bul['dosya']);
			$guncelle = $db->prepare("UPDATE meclis_kararlari SET dosya = ? WHERE id = ?");
			$guncelle->execute([$Dosya,$d_id]);
			$Dosya=''.$upload2->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE meclis_kararlari SET
			sira 		= ?,
			adi 		= ?,
			kararno 	= ?,
			seo 		= ?,
			spot		= ?,
			aciklama	= ?,
			keywords	= ?,
			description	= ?,
			durum 		= ?,
			tarih 		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$adi,
			$kararno,
			$seo,
			$spot,
			$aciklama,
			$keywords,
			$description,
			$durum,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Meclis Kararı Güncellendi",
				'icon' 		=> "icon-folder-alt",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı meclis kararını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['karar_guncelle'] = 'yes';
			header("Location:../".yonetim."/karar-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['karar_guncelle'] = 'no';
			header("Location:../".yonetim."/karar-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/karar-duzenle/".$d_id.".html");
		exit();
	}
}

##Meclis Kararı Resim Sil##
if(@$_GET['kararresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("kararresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM meclis_kararlari WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/meclis_kararlari/".$resim_bul['resim']);
		$sorgu = $db->prepare("UPDATE meclis_kararlari SET
					resim	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Meclis Karar Resim Silindi",
				'icon' 		=> "icon-book-open",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı meclis kararının resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['kararresimsil'] = 'yes';
			header("Location:../".yonetim."/karar-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['kararresimsil'] = 'no';
			header("Location:../".yonetim."/karar-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/karar-duzenle/".$resimid.".html");
		exit();
	}
}

##Meclis Kararı Dosya Sil##
if(@$_GET['karardosyasil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("karardosyasil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM meclis_kararlari WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/meclis_kararlari/dosya/".$resim_bul['dosya']);
		$sorgu = $db->prepare("UPDATE meclis_kararlari SET
					dosya	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Meclis Kararları Dosya Silindi",
				'icon' 		=> "icon-trash",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı meclis kararlarının dosyasını sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['karardosyasil'] = 'yes';
			header("Location:../".yonetim."/karar-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['karardosyasil'] = 'no';
			header("Location:../".yonetim."/karar-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/karar-duzenle/".$resimid.".html");
		exit();
	}
}

##Meclis Kararı Sil##
if(@$_GET['kararsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("kararsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM meclis_kararlari WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/meclis_kararlari/".$resim_bul['resim']);
		unlink("../".tema."/uploads/meclis_kararlari/dosya/".$resim_bul['dosya']);
		$Sorgu = $db->prepare("DELETE FROM meclis_kararlari WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Meclis Kararı Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı meclis kararını sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['kararsil'] = 'yes';
				header("Location:../".yonetim."/karar-listele.html");
				exit();
			}
			else
			{
				$_SESSION['kararsil'] = 'no';
				header("Location:../".yonetim."/karar-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/karar-listele.html");
		exit();
	}
}

##Meclis Kararı Toplu Sil ##
if(isset($_POST['karar_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("karar_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM meclis_kararlari WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM meclis_kararlari WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Meclis Kararı Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı meclis kararını sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/meclis_kararlari/".$resim_bul['resim']);
					unlink("../".tema."/uploads/meclis_kararlari/dosya/".$resim_bul['dosya']);
					$_SESSION['karar_tumu'] = 'yes';
					header("Location:../".yonetim."/karar-listele.html");
				}
				else
				{
					$_SESSION['karar_tumu'] = 'no';
					header("Location:../".yonetim."/karar-listele.html");
					exit();
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/karar-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/karar-listele.html");
		exit();
	}	
}

##Meclis Kararı Toplu Aktif ##
if(isset($_POST['karar_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("karar_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM meclis_kararlari WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE meclis_kararlari SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Meclis Kararı Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$blog_bul['adi']."</strong> başlıklı meclis kararını aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['karar_aktif'] = 'yes';
					header("Location:../".yonetim."/karar-listele.html");
				}
				else
				{
					$_SESSION['karar_aktif'] = 'no';
					header("Location:../".yonetim."/karar-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/karar-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/karar-listele.html");
		exit();
	}	
}

##Meclis Kararları Toplu Pasif ##
if(isset($_POST['karar_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("karar_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM meclis_kararlari WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE meclis_kararlari SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Karar Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$blog_bul['adi']."</strong> başlıklı meclis kararını pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['karar_pasif'] = 'yes';
					header("Location:../".yonetim."/karar-listele.html");
				}
				else
				{
					$_SESSION['karar_pasif'] = 'no';
					header("Location:../".yonetim."/karar-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/karar-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/karar-listele.html");
		exit();
	}	
}

##Meclis Kararları Tümünü Sil ##
if(@$_GET['karartumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("karartumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$IHSorgu = $db->prepare("SELECT * FROM meclis_kararlari");
		$IHSorgu->execute();
		$IHislem = $IHSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $IHislem as $IHSonuc ){
			unlink("../".tema."/uploads/meclis_kararlari/".$IHSonuc['resim']);
			unlink("../".tema."/uploads/meclis_kararlari/dosya/".$IHSonuc['dosya']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE meclis_kararlari");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['karartumunusil'] = 'yes';
			header("Location:../".yonetim."/karar-listele.html");
		}
		else
		{
			$_SESSION['karartumunusil'] = 'no';
			header("Location:../".yonetim."/karar-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/karar-listele.html");
		exit();
	}
}

##Meclis Kararları Sıra Ajax##
if(isset($_GET['kararsiralama']))
{
	if($_GET['kararsiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE meclis_kararlari SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Faaliyet Raporları Kaydet ##
if(isset($_POST['faaliyet_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("faaliyet_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 			= $_POST['sira'];
		$adi 			= $_POST['adi'];
		$seo			= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}					
		$aciklama 		= $_POST['aciklama'];
		$keywords		= $_POST['keywords'];
		$description	= $_POST['description'];
		$tarih			= $_POST['tarih'];
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/faaliyet_raporlari");
			
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		$Resim=''.$upload->file_dst_name.'';
		
		$upload2 = new upload($_FILES['dosya']);
		if ($upload2->uploaded)
		{
			$upload2->file_auto_rename = true;
			$upload2->process("../".tema."/uploads/faaliyet_raporlari/dosya");
			if ($upload2->processed)
			{
				$Dosya=''.$upload2->file_dst_name.'';
			}
		}
		$Dosya=''.$upload2->file_dst_name.'';
		
		$bagis_ids	= isset($_POST['bagis_ids']) ? implode(',', $_POST['bagis_ids']) : null;
		
		$sorgu = $db->prepare("INSERT INTO faaliyet_raporlari SET
				sira 		= ?,
				adi 		= ?,
				seo 		= ?,
				aciklama	= ?,
				keywords	= ?,
				description	= ?,
				durum 		= ?,				
				resim 		= ?,
				dosya 		= ?,
				bagis_ids 	= ?,
				dil 		= ?,
				tarih 		= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$adi,				
				$seo,
				$aciklama,
				$keywords,
				$description,
				$durum,
				$Resim,
				$Dosya,
				$bagis_ids,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Yeni Faaliyet Raporu",
				'icon' 		=> "icon-book-open",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı faaliyet raporunu ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['faaliyet_ekle'] = 'yes';
			header("Location:../".yonetim."/faaliyet-listele.html");
			exit();
		}
		else
		{
			$_SESSION['faaliyet_ekle'] = 'no';
			header("Location:../".yonetim."/faaliyet-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/faaliyet-ekle.html");
		exit();
	}
}

##Faaliyet Raporları Güncelle ##
if(isset($_POST['faaliyet_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("faaliyet_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 			= $_POST['sira'];
		$adi 			= $_POST['adi'];
		$seo			= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}	
		$aciklama 		= $_POST['aciklama'];
		$keywords 		= $_POST['keywords'];
		$description	= $_POST['description'];
		$tarih			= $_POST['tarih'];
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/faaliyet_raporlari");
	
			if ($upload->processed)
			{
				$Resim=''.$upload->file_dst_name.'';
			}
		}
		
		$upload2 = new upload($_FILES['dosya']);
		if ($upload2->uploaded)
		{
			$upload2->file_auto_rename = true;
			$upload2->process("../".tema."/uploads/faaliyet_raporlari/dosya");
			if ($upload2->processed)
			{
				$Dosya=''.$upload2->file_dst_name.'';
			}
		}
		
		if(isset($Resim)){
			$resim_bul= $db->query("SELECT * FROM faaliyet_raporlari WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/faaliyet_raporlari/".$resim_bul['resim']);
			$guncelle = $db->prepare("UPDATE faaliyet_raporlari SET resim = ? WHERE id = ?");
			$guncelle->execute([$Resim,$d_id]);
			$Resim=''.$upload->file_dst_name.'';
		}
		
		if(isset($Dosya)){
			$resim_bul= $db->query("SELECT * FROM faaliyet_raporlari WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/faaliyet_raporlari/dosya/".$resim_bul['dosya']);
			$guncelle = $db->prepare("UPDATE faaliyet_raporlari SET dosya = ? WHERE id = ?");
			$guncelle->execute([$Dosya,$d_id]);
			$Dosya=''.$upload2->file_dst_name.'';
		}
		
		$bagis_ids	= isset($_POST['bagis_ids']) ? implode(',', $_POST['bagis_ids']) : null;

		$sorgu = $db->prepare("UPDATE faaliyet_raporlari SET
			sira 		= ?,
			adi 		= ?,
			seo 		= ?,
			aciklama	= ?,
			keywords	= ?,
			description	= ?,
			durum 		= ?,
			bagis_ids 	= ?,
			tarih 		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$adi,
			$seo,
			$aciklama,
			$keywords,
			$description,
			$durum,
			$bagis_ids,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Faaliyet Raporu Güncellendi",
				'icon' 		=> "icon-book-open",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı faaliyet raporunu güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['faaliyet_guncelle'] = 'yes';
			header("Location:../".yonetim."/faaliyet-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['faaliyet_guncelle'] = 'no';
			header("Location:../".yonetim."/faaliyet-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/faaliyet-duzenle/".$d_id.".html");
		exit();
	}
}

##Faaliyet Raporları Resim Sil##
if(@$_GET['faaliyetresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("faaliyetresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM faaliyet_raporlari WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/faaliyet_raporlari/".$resim_bul['resim']);
		$sorgu = $db->prepare("UPDATE faaliyet_raporlari SET
					resim	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Faaliyet Raporu Resim Silindi",
				'icon' 		=> "icon-trash",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı faaliyet raporu resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['faaliyetresimsil'] = 'yes';
			header("Location:../".yonetim."/faaliyet-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['faaliyetresimsil'] = 'no';
			header("Location:../".yonetim."/faaliyet-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/faaliyet-duzenle/".$resimid.".html");
		exit();
	}
}

##Faaliyet Raporları Dosya Sil##
if(@$_GET['faaliyetdosyasil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("faaliyetdosyasil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM faaliyet_raporlari WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/faaliyet_raporlari/dosya/".$resim_bul['dosya']);
		$sorgu = $db->prepare("UPDATE faaliyet_raporlari SET
					dosya	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Faaliyet Raporu Dosya Silindi",
				'icon' 		=> "icon-trash",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı faaliyet raporu dosyasını sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['faaliyetdosyasil'] = 'yes';
			header("Location:../".yonetim."/faaliyet-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['faaliyetdosyasil'] = 'no';
			header("Location:../".yonetim."/faaliyet-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/faaliyet-duzenle/".$resimid.".html");
		exit();
	}
}

##Faaliyet Raporları Sil##
if(@$_GET['faaliyetsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("faaliyetsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM faaliyet_raporlari WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/faaliyet_raporlari/".$resim_bul['resim']);
		unlink("../".tema."/uploads/faaliyet_raporlari/dosya/".$resim_bul['dosya']);
		$Sorgu = $db->prepare("DELETE FROM faaliyet_raporlari WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Faaliyet Raporunu Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı faaliyet raporunu sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['faaliyetsil'] = 'yes';
				header("Location:../".yonetim."/faaliyet-listele.html");
				exit();
			}
			else
			{
				$_SESSION['faaliyetsil'] = 'no';
				header("Location:../".yonetim."/faaliyet-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/faaliyet-listele.html");
		exit();
	}
}

##Faaliyet Raporları Toplu Sil ##
if(isset($_POST['faaliyet_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("faaliyet_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM faaliyet_raporlari WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM faaliyet_raporlari WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Faaliyet Raporu Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı faaliyet raporunu sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/faaliyet_raporlari/".$resim_bul['resim']);
					unlink("../".tema."/uploads/faaliyet_raporlari/dosya/".$resim_bul['dosya']);
					$_SESSION['faaliyet_tumu'] = 'yes';
					header("Location:../".yonetim."/faaliyet-listele.html");
				}
				else
				{
					$_SESSION['faaliyet_tumu'] = 'no';
					header("Location:../".yonetim."/faaliyet-listele.html");
					exit();
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/faaliyet-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/faaliyet-listele.html");
		exit();
	}	
}

##Faaliyet Raporları Toplu Aktif ##
if(isset($_POST['faaliyet_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("faaliyet_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM faaliyet_raporlari WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE faaliyet_raporlari SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "faaliyet Raporu Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$blog_bul['adi']."</strong> başlıklı faaliyet raporunu aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['faaliyet_aktif'] = 'yes';
					header("Location:../".yonetim."/faaliyet-listele.html");
				}
				else
				{
					$_SESSION['faaliyet_aktif'] = 'no';
					header("Location:../".yonetim."/faaliyet-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/faaliyet-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/faaliyet-listele.html");
		exit();
	}	
}

##Faaliyet Raporları Toplu Pasif ##
if(isset($_POST['faaliyet_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("faaliyet_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$blog_bul= $db->query("SELECT * FROM faaliyet_raporlari WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE faaliyet_raporlari SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Faaliyet Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$blog_bul['adi']."</strong> başlıklı faaliyet raporlarını pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['faaliyet_pasif'] = 'yes';
					header("Location:../".yonetim."/faaliyet-listele.html");
				}
				else
				{
					$_SESSION['faaliyet_pasif'] = 'no';
					header("Location:../".yonetim."/faaliyet-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/faaliyet-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/faaliyet-listele.html");
		exit();
	}	
}

##Faaliyet Raporları Tümünü Sil ##
if(@$_GET['faaliyettumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("faaliyettumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$IHSorgu = $db->prepare("SELECT * FROM faaliyet_raporlari");
		$IHSorgu->execute();
		$IHislem = $IHSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $IHislem as $IHSonuc ){
			unlink("../".tema."/uploads/faaliyet_raporlari/".$IHSonuc['resim']);
			unlink("../".tema."/uploads/faaliyet_raporlari/dosya/".$IHSonuc['dosya']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE faaliyet_raporlari");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['faaliyettumunusil'] = 'yes';
			header("Location:../".yonetim."/faaliyet-listele.html");
		}
		else
		{
			$_SESSION['faaliyettumunusil'] = 'no';
			header("Location:../".yonetim."/faaliyet-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/faaliyet-listele.html");
		exit();
	}
}

##Faaliyet Raporları Sıra Ajax##
if(isset($_GET['faaliyetsiralama']))
{
	if($_GET['faaliyetsiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE faaliyet_raporlari SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Profil Kategori Kaydet ##
if(isset($_POST['profil_kategori_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("profil_kategori_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}		
		$keywords	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);

		
		$sorgu = $db->prepare("INSERT INTO profil_kategori SET
				adi 	= ?,
				seo 	= ?,
				keywords= ?,
				description	= ?,
				durum 	= ?,
				dil 	= ?,
				tarih 	= ?");
		$Ekle = $sorgu->execute(array(
				$adi,
				$seo,
				$keywords,
				$description,
				$durum,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Profil Kategorisi Ekledi",
				'icon' 		=> "icon-drawer",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı profil kategorisi ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['profil_kategori_ekle'] = 'yes';
			header("Location:../".yonetim."/profil-kategoriler.html");
			exit();
		}
		else
		{
			$_SESSION['profil_kategori_ekle'] = 'no';
			header("Location:../".yonetim."/profil-kategori-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-kategori-ekle.html");
		exit();
	}
}

##Profil Kategori Güncelle ##
if(isset($_POST['profil_kategori_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("profil_kategori_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		$seo		= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$keywords 	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$sorgu = $db->prepare("UPDATE profil_kategori SET
			adi 	= ?,
			seo 	= ?,
			keywords= ?,
			description	= ?,
			durum 	= ?,
			tarih 	= ?
			WHERE id = ?");
		$guncelle = $sorgu->execute(array(
			$adi,
			$seo,
			$keywords,
			$description,
			$durum,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Profil Kategorisi Güncellendi",
				'icon' 		=> "icon-drawer",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı profil kategorisini güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['profil_kategori_guncelle'] = 'yes';
			header("Location:../".yonetim."/profil-kategori-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['profil_kategori_guncelle'] = 'no';
			header("Location:../".yonetim."/profil-kategori-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-kategori-duzenle/".$d_id.".html");
		exit();
	}
}

##Profil Kategori Sil##
if(@$_GET['profilkatsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("profilkatsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM profil_kategori WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		$Sorgu = $db->prepare("DELETE FROM profil_kategori WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Profil Kategorisi Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı profil kategorisini sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['profilkatsil'] = 'yes';
				header("Location:../".yonetim."/profil-kategoriler.html");
				exit();
			}
			else
			{
				$_SESSION['profilkatsil'] = 'no';
				header("Location:../".yonetim."/profil-kategoriler.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-kategoriler.html");
		exit();
	}
}

##Profil Kategori Toplu Sil ##
if(isset($_POST['profil_kat_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("profil_kat_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM profil_kategori WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM profil_kategori WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Profil Kategorisi Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı profil kategorisini sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['profil_kat_tumu'] = 'yes';
					header("Location:../".yonetim."/profil-kategoriler.html");
				}
				else
				{
					$_SESSION['profil_kat_tumu'] = 'no';
					header("Location:../".yonetim."/profil-kategoriler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/profil-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-kategoriler.html");
		exit();
	}	
}

##Profil Kategori Toplu Aktif ##
if(isset($_POST['profil_kat_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("profil_kat_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM profil_kategori WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE profil_kategori SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Profil Kategori Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı profil kategorisini aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['profil_kat_aktif'] = 'yes';
					header("Location:../".yonetim."/profil-kategoriler.html");
				}
				else
				{
					$_SESSION['profil_kat_aktif'] = 'no';
					header("Location:../".yonetim."/profil-kategoriler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/profil-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-kategoriler.html");
		exit();
	}	
}

##Profil Kategori Toplu Pasif ##
if(isset($_POST['profil_kat_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("profil_kat_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM profil_kategori WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE profil_kategori SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Profil Kategori Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı profil kategorisini pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['profil_kat_pasif'] = 'yes';
					header("Location:../".yonetim."/profil-kategoriler.html");
				}
				else
				{
					$_SESSION['profil_kat_pasif'] = 'no';
					header("Location:../".yonetim."/profil-kategoriler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/profil-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-kategoriler.html");
		exit();
	}	
}

##Profil Kategori Tümünü Sil ##
if(@$_GET['profilkattumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("profilkattumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$Sorgu = $db->prepare("TRUNCATE TABLE profil_kategori");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['profilkattumunusil'] = 'yes';
			header("Location:../".yonetim."/profil-kategoriler.html");
			exit();
		}
		else
		{
			$_SESSION['profilkattumunusil'] = 'no';
			header("Location:../".yonetim."/profil-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-kategoriler.html");
		exit();
	}
}

##Profil Kategori Sıra Ajax##
if(isset($_GET['profilkategorisiralama']))
{
	if($_GET['profilkategorisiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE profil_kategori SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Profil Kaydet ##
if(isset($_POST['profil_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("profil_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 				= $_POST["sira"];
		$kategori 			= $_POST["kategori"];
		$gorevi 				= $_POST["gorevi"];
		$adi 				= $_POST['adi'];
		$seo				= cVCLmHLxbS_seo($adi);
		$aciklama			= $_POST["aciklama"];
		$description		= $_POST["description"];
		$keywords			= $_POST["keywords"];
		$facebook			= $_POST["facebook"];
		$twitter			= $_POST["twitter"];
		$instagram			= $_POST["instagram"];
		$linkedin			= $_POST["linkedin"];
		$youtube			= $_POST["youtube"];
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}								
		$tarih				= date('Y-m-d H:i:s');
		$tarih				= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/profiller");
					
			if ($upload->processed)
			{
				$Kapak=''.$upload->file_dst_name.'';
			}
		}
		$Kapak=''.$upload->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO profiller SET
				sira 			= ?,
				kategori 		= ?,
				gorevi 			= ?,
				adi 			= ?,
				seo 			= ?,
				aciklama 		= ?,
				description		= ?,
				keywords		= ?,
				facebook		= ?,
				twitter			= ?,
				instagram		= ?,
				linkedin		= ?,
				youtube			= ?,
				kapak 			= ?,
				durum 			= ?,
				dil 			= ?,
				tarih 			= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$kategori,
				$gorevi,
				$adi,
				$seo,
				$aciklama,
				$description,
				$keywords,
				$facebook,
				$twitter,
				$instagram,
				$linkedin,
				$youtube,
				$Kapak,
				$durum,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id  = $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Profil Ekledi",
				'icon' 		=> "icon-user",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı profil ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['profil_ekle'] = 'yes';
			header("Location:../".yonetim."/profil-listele.html");
			exit();
		}
		else
		{
			$_SESSION['profil_ekle'] = 'no';
			header("Location:../".yonetim."/profil-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-ekle.html");
		exit();
	}
}

##Profil Güncelle ##
if(isset($_POST['profil_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("profil_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 				= $_POST["sira"];
		$kategori 			= $_POST["kategori"];
		$gorevi 				= $_POST["gorevi"];
		$adi 				= $_POST['adi'];
		$seo				= cVCLmHLxbS_seo($adi);
		$aciklama			= $_POST["aciklama"];
		$description		= $_POST["description"];
		$keywords			= $_POST["keywords"];
		$facebook			= $_POST["facebook"];
		$twitter			= $_POST["twitter"];
		$instagram			= $_POST["instagram"];
		$linkedin			= $_POST["linkedin"];
		$youtube			= $_POST["youtube"];
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$tarih				= date('Y-m-d H:i:s');
		$tarih				= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/profiller");
			
			if ($upload->processed)
			{
				$Kapak=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($Kapak)){
			$resim_bul= $db->query("SELECT * FROM profiller WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/profiller/".$resim_bul['kapak']);
			$guncelle = $db->prepare("UPDATE profiller SET kapak = ? WHERE id = ?");
			$guncelle->execute([$Kapak,$d_id]);
			$Kapak=''.$upload->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE profiller SET
			sira 			= ?,
			kategori 		= ?,
			gorevi 			= ?,
			adi 			= ?,
			seo 			= ?,
			aciklama 		= ?,
			description		= ?,
			keywords		= ?,
			facebook		= ?,
			twitter			= ?,
			instagram		= ?,
			linkedin		= ?,
			youtube			= ?,
			durum 			= ?,
			tarih 			= ?
			WHERE id 		= ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$kategori,
			$gorevi,
			$adi,
			$seo,
			$aciklama,
			$description,
			$keywords,
			$facebook,
			$twitter,
			$instagram,
			$linkedin,
			$youtube,
			$durum,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Profil Güncellendi",
				'icon' 		=> "icon-user",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı profili güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['profil_guncelle'] = 'yes';
			header("Location:../".yonetim."/profil-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['profil_guncelle'] = 'no';
			header("Location:../".yonetim."/profil-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-duzenle/".$d_id.".html");
		exit();
	}
}

##Profil Kapak Sil##
if(@$_GET['profilresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("profilresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM profiller WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/profiller/".$resim_bul['kapak']);
		$sorgu = $db->prepare("UPDATE profiller SET
					kapak	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Profil Resmi Silindi",
				'icon' 		=> "icon-book-open",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı profilin resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['profilresimsil'] = 'yes';
			header("Location:../".yonetim."/profil-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['profilresimsil'] = 'no';
			header("Location:../".yonetim."/profil-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-duzenle/".$resimid.".html");
		exit();
	}
}

##Profil Sil##
if(@$_GET['profilsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("profilsil");
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM profiller WHERE id = '{$_GET['id']}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/profiller/".$resim_bul['kapak']);
		$TSorgu = $db->prepare("DELETE FROM profiller WHERE id = :id");
		$TSil	= $TSorgu->execute(array('id' => $_GET['id']));
		if($TSorgu->rowCount())
		{
			if($TSil)
			{
				$last_id 		= $_GET['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Profil Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı profili sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['profilsil'] = 'yes';
				header("Location:../".yonetim."/profil-listele.html");
				exit();
			}
			else
			{
				$_SESSION['profilsil'] = 'no';
				header("Location:../".yonetim."/profil-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-listele.html");
		exit();
	}
}

##Profil Toplu Sil ##
if(isset($_POST['profil_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("profil_tumu");
	$url = $_POST['url'];
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM profiller WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM profiller WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;					
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Profil Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı profili sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/profiller/".$sayfa_bul['kapak']);
					$_SESSION['profil_tumu'] = 'yes';
					header("Location:../".yonetim."/profil-listele.html");
				}
				else
				{
					$_SESSION['profil_tumu'] = 'no';
					header("Location:../".yonetim."/profil-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/profil-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-listele.html");
		exit();
	}	
}

##Profil Toplu Aktif ##
if(isset($_POST['profil_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("profil_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM profiller WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE profiller SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Profil Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı profili aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['profil_aktif'] = 'yes';
					header("Location:../".yonetim."/profil-listele.html");
				}
				else
				{
					$_SESSION['profil_aktif'] = 'no';
					header("Location:../".yonetim."/profil-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/profil-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-listele.html");
		exit();
	}	
}

##Profil Toplu Pasif ##
if(isset($_POST['profil_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("profil_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM profiller WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE profiller SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Profil Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı profili pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['profil_pasif'] = 'yes';
					header("Location:../".yonetim."/profil-listele.html");
				}
				else
				{
					$_SESSION['profil_pasif'] = 'no';
					header("Location:../".yonetim."/profil-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/profil-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-listele.html");
		exit();
	}	
}

##Profil Tümünü Sil ##
if(@$_GET['profiltumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("profiltumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$DUSorgu = $db->prepare("SELECT * FROM profiller");
		$DUSorgu->execute();
		$DUislem = $DUSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $DUislem as $DUSonuc ){
			unlink("../".tema."/uploads/profiller/".$DUSonuc['kapak']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE profiller");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['profiltumunusil'] = 'yes';
			header("Location:../".yonetim."/profil-listele.html");
			exit();
		}
		else
		{
			$_SESSION['profiltumunusil'] = 'no';
			header("Location:../".yonetim."/profil-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/profil-listele.html");
		exit();
	}
}

##Profil Sıra Ajax##
if(isset($_GET['profilsiralama']))
{
	if($_GET['profilsiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE profiller SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}


##Bağış Kategori Kaydet ##
if(isset($_POST['bagis_kategori_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_kategori_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		$aciklama 	= $_POST['aciklama'];
		$sira 		= $_POST['sira'];
		$seo		= cVCLmHLxbS_seo($adi);
		$icon_class = isset($_POST['icon_class']) ? $_POST['icon_class'] : '';
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}		
		$keywords	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['ikon']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/bagis_kategoriler");
					
			if ($upload->processed)
			{
				$ikon=''.$upload->file_dst_name.'';
			}
		}
		$ikon=''.$upload->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO bagis_kategori SET
				modul_id = ?,
				adi 	= ?,
				aciklama 	= ?,
				sira 	= ?,
				seo 	= ?,
				keywords= ?,
				description	= ?,
				ikon	= ?,
				icon_class = ?,
				durum 	= ?,
				dil 	= ?,
				tarih 	= ?");
		$Ekle = $sorgu->execute(array(
				isset($_POST['modul_id']) ? $_POST['modul_id'] : 1,
				$adi,
				$aciklama,
				$sira,
				$seo,
				$keywords,
				$description,
				$ikon,
				$icon_class,
				$durum,
				$_SESSION['admin_dil'],
				$tarih
			));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Bağış Kategorisi Ekledi",
				'icon' 		=> "ti-heart",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı bağış kategorisi ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['bagis_kategori_ekle'] = 'yes';
			header("Location:../".yonetim."/bagis-kategoriler.html");
			exit();
		}
		else
		{
			$_SESSION['bagis_kategori_ekle'] = 'no';
			header("Location:../".yonetim."/bagis-kategori-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-kategori-ekle.html");
		exit();
	}
}

##Bağış Kategori Güncelle ##
if(isset($_POST['bagis_kategori_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_kategori_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		$aciklama 	= $_POST['aciklama'];
		$sira 		= $_POST['sira'];
		$seo		= cVCLmHLxbS_seo($adi);
		$icon_class = isset($_POST['icon_class']) ? $_POST['icon_class'] : '';
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$keywords 	= $_POST['keywords'];
		$description= $_POST['description'];
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['ikon']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/bagis_kategoriler");
			
			if ($upload->processed)
			{
				$ikon=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($ikon)){
			$resim_bul= $db->query("SELECT * FROM bagis_kategori WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
			unlink("../".tema."/uploads/bagis_kategoriler/".$resim_bul['ikon']);
			$guncelle = $db->prepare("UPDATE bagis_kategori SET ikon = ? WHERE id = ?");
			$guncelle->execute([$ikon,$d_id]);
			$ikon=''.$upload->file_dst_name.'';
		}
		
		$sorgu = $db->prepare("UPDATE bagis_kategori SET
			modul_id = ?,
			adi 	= ?,
			aciklama 	= ?,
			sira 	= ?,
			seo 	= ?,
			keywords= ?,
			description	= ?,
			icon_class = ?,
			durum 	= ?,
			tarih 	= ?
			WHERE id = ?");
		$guncelle = $sorgu->execute(array(
			isset($_POST['modul_id']) ? $_POST['modul_id'] : 1,
			$adi,
			$aciklama,
			$sira,
			$seo,
			$keywords,
			$description,
			$icon_class,
			$durum,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Bağış Kategorisi Güncellendi",
				'icon' 		=> "ti-heart",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı bağış kategorisini güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['bagis_kategori_guncelle'] = 'yes';
			header("Location:../".yonetim."/bagis-kategori-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['bagis_kategori_guncelle'] = 'no';
			header("Location:../".yonetim."/bagis-kategori-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-kategori-duzenle/".$d_id.".html");
		exit();
	}
}

##Bağış Kategori İkon Sil##
if(@$_GET['bagis_kategoriresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("bagis_kategoriresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM bagis_kategori WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/bagis_kategoriler/".$resim_bul['ikon']);
		$sorgu = $db->prepare("UPDATE bagis_kategori SET
					ikon	= ?
					WHERE id = ?");
		$guncelle = $sorgu->execute(array(
					"",
					$resimid
				));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Bağış Kategori İkon Silindi",
				'icon' 		=> "icon-trash",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı bağış kategorisinin ikonunu sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['bagis_kategoriresimsil'] = 'yes';
			header("Location:../".yonetim."/bagis-kategori-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['bagis_kategoriresimsil'] = 'no';
			header("Location:../".yonetim."/bagis-kategori-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-kategori-duzenle/".$resimid.".html");
		exit();
	}
}

##Bağış Kategori Sil##
if(@$_GET['bagiskatsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("bagiskatsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM bagis_kategori WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/bagis_kategoriler/".$resim_bul['ikon']);
		$Sorgu = $db->prepare("DELETE FROM bagis_kategori WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Bağış Kategorisi Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı bağış kategorisini sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['bagiskatsil'] = 'yes';
				header("Location:../".yonetim."/bagis-kategoriler.html");
				exit();
			}
			else
			{
				$_SESSION['bagiskatsil'] = 'no';
				header("Location:../".yonetim."/bagis-kategoriler.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-kategoriler.html");
		exit();
	}
}

##Bağış Kategori Toplu Sil ##
if(isset($_POST['bagis_kat_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_kat_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM bagis_kategori WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM bagis_kategori WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Bağış Kategorisi Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı bağış kategorisini sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					unlink("../".tema."/uploads/bagis_kategoriler/".$resim_bul['ikon']);
					$_SESSION['bagis_kat_tumu'] = 'yes';
					header("Location:../".yonetim."/bagis-kategoriler.html");
				}
				else
				{
					$_SESSION['bagis_kat_tumu'] = 'no';
					header("Location:../".yonetim."/bagis-kategoriler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/bagis-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-kategoriler.html");
		exit();
	}	
}

##Bağış Kategori Toplu Aktif ##
if(isset($_POST['bagis_kat_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_kat_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM bagis_kategori WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE bagis_kategori SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Bağış Kategori Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı bağış kategorisini aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['bagis_kat_aktif'] = 'yes';
					header("Location:../".yonetim."/bagis-kategoriler.html");
				}
				else
				{
					$_SESSION['bagis_kat_aktif'] = 'no';
					header("Location:../".yonetim."/bagis-kategoriler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/bagis-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-kategoriler.html");
		exit();
	}	
}

##Bağış Kategori Toplu Pasif ##
if(isset($_POST['bagis_kat_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_kat_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM bagis_kategori WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE bagis_kategori SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Bağış Kategori Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı bağış kategorisini pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['bagis_kat_pasif'] = 'yes';
					header("Location:../".yonetim."/bagis-kategoriler.html");
				}
				else
				{
					$_SESSION['bagis_kat_pasif'] = 'no';
					header("Location:../".yonetim."/bagis-kategoriler.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/bagis-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-kategoriler.html");
		exit();
	}	
}

##Bağış Kategori Sıra Ajax##
if(isset($_GET['bagiskatsiralama']))
{
	if($_GET['bagiskatsiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE bagis_kategori SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Bağış Kategori Tümünü Sil ##
if(@$_GET['bagiskattumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("bagiskattumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$DUSorgu = $db->prepare("SELECT * FROM bagis_kategori");
		$DUSorgu->execute();
		$DUislem = $DUSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $DUislem as $DUSonuc ){
			unlink("../".tema."/uploads/bagis_kategoriler/".$DUSonuc['ikon']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE bagis_kategori");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['bagiskattumunusil'] = 'yes';
			header("Location:../".yonetim."/bagis-kategoriler.html");
			exit();
		}
		else
		{
			$_SESSION['bagiskattumunusil'] = 'no';
			header("Location:../".yonetim."/bagis-kategoriler.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-kategoriler.html");
		exit();
	}
}

##Bağış Kaydet ##
if(isset($_POST['bagis_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 				= $_POST["sira"];
		$kategori 			= $_POST["kategori"];
		$adi 				= $_POST['adi'];
		$miktar 				= $_POST['miktar'];
		
		// YENİ ALANLAR
		$aciklama = isset($_POST['aciklama']) ? $_POST['aciklama'] : '';
		$hediye_turu = isset($_POST['hediye_turu']) ? intval($_POST['hediye_turu']) : 0;
		$kampanya_durum = isset($_POST['kampanya_durum']) ? 1 : 0;
		$detay_linki = isset($_POST['detay_linki']) ? 1 : 0;
		$yetim_bagisi = isset($_POST['yetim_bagisi']) ? 1 : 0;
		$degismeyen_fiyat = isset($_POST['degismeyen_fiyat']) ? 1 : 0;
		
		$seo				= cVCLmHLxbS_seo($adi);
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}							
		$tarih				= date('Y-m-d H:i:s');
		$tarih				= cVCLmHLxbS_tr_tarih($tarih);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/bagislar");
			
			$upload->file_auto_rename = true;
			$upload->image_resize = true;
			$upload->image_ratio_crop = true;
			$upload->image_x = 560;
			$upload->image_y = 320;
			$upload->process("../".tema."/uploads/bagislar/kapak");
					
			if ($upload->processed)
			{
				$Kapak=''.$upload->file_dst_name.'';
			}
		}
		$Kapak=''.$upload->file_dst_name.'';
		
		$sorgu = $db->prepare("INSERT INTO bagislar SET
				modul_id 			= ?,
				sira 				= ?,
				kategori 			= ?,
				adi 				= ?,
				aciklama 			= ?,
				detay_linki 		= ?,
				hediye_turu_id 		= ?,
				kampanya_durum 		= ?,
				yetim_bagisi		= ?,
				degismeyen_fiyat		= ?,
				seo 				= ?,
				miktar 				= ?,
				kapak 				= ?,
				durum 				= ?,
				dil 				= ?,
				tarih 				= ?");
		$Ekle = $sorgu->execute(array(
				isset($_POST['modul_id']) ? $_POST['modul_id'] : 1,
				$sira,
				$kategori,
				$adi,
				$aciklama,
				$detay_linki,
				$hediye_turu,
				$kampanya_durum,
				$yetim_bagisi,
				$degismeyen_fiyat,
				$seo,
				$miktar,
				$Kapak,
				$durum,
				$_SESSION['admin_dil'],
				$tarih
				));
		if($Ekle)
		{
			$last_id  = $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Bağış Ekledi",
				'icon' 		=> "ti-heart",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı bağışı ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['bagis_ekle'] = 'yes';
			header("Location:../".yonetim."/bagis-listele.html");
			exit();
		}
		else
		{
			$_SESSION['bagis_ekle'] = 'no';
			header("Location:../".yonetim."/bagis-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-ekle.html");
		exit();
	}
}

## Bağış Güncelle ##
if(isset($_POST['bagis_guncelle']))
{
    cVCLmHLxbS_panelislemkontrol("bagis_guncelle");
    $d_id = $_POST['id'];

    $sira         = $_POST["sira"];
    $kategori     = $_POST["kategori"];
    $adi          = $_POST['adi'];
    $degismeyen_fiyat = isset($_POST['degismeyen_fiyat']) ? 1 : 0;
    $miktar       = $_POST['miktar'];
    $aciklama     = $_POST['aciklama']; // Alınıyor ama SQL'de yoktu
    $detay_linki  = isset($_POST['detay_linki']) ? 1 : 0; // Alınıyor ama SQL'de yoktu
    $yetim_bagisi = isset($_POST['yetim_bagisi']) ? 1 : 0;
    $seo          = cVCLmHLxbS_seo($adi);
    $durum        = isset($_POST['durum']) ? 1 : 0;
    
    $tarih        = date('Y-m-d H:i:s');
    $tarih        = cVCLmHLxbS_tr_tarih($tarih);
    
    // Resim İşlemleri
    $upload = new upload($_FILES['resim']);
    if ($upload->uploaded)
    {
        $upload->file_auto_rename = true;
        $upload->process("../".tema."/uploads/bagislar");
        
        $upload->file_auto_rename = true;
        $upload->image_resize = true;
        $upload->image_ratio_crop = true;
        $upload->image_x = 560;
        $upload->image_y = 320;
        $upload->process("../".tema."/uploads/bagislar/kapak");
        
        if ($upload->processed)
        {
            $Kapak = $upload->file_dst_name;
            
            // Eski resmi sil
            $resim_bul = $db->query("SELECT kapak FROM bagislar WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
            if($resim_bul['kapak']){
                @unlink("../".tema."/uploads/bagislar/".$resim_bul['kapak']);
                @unlink("../".tema."/uploads/bagislar/kapak/".$resim_bul['kapak']);
            }
            
            // Sadece resim güncellendiğinde tabloyu tetikle
            $guncelle_resim = $db->prepare("UPDATE bagislar SET kapak = ? WHERE id = ?");
            $guncelle_resim->execute([$Kapak, $d_id]);
        }
    }
    
    $sorgu = $db->prepare("UPDATE bagislar SET
        modul_id     = ?,
        sira         = ?,
        kategori     = ?,
        adi          = ?,
        degismeyen_fiyat		= ?,
        aciklama     = ?,  
        detay_linki  = ?,  
        yetim_bagisi = ?,
        seo          = ?,
        miktar       = ?,
        durum        = ?,
        tarih        = ?
        WHERE id     = ?");

    $guncelle = $sorgu->execute(array(
        isset($_POST['modul_id']) ? $_POST['modul_id'] : 1,
        $sira,
        $kategori,
        $adi,
        $degismeyen_fiyat,
        $aciklama,    
        $detay_linki,  
        $yetim_bagisi,
        $seo,
        $miktar,
        $durum,
        $tarih,
        $d_id
    ));

    if($guncelle)
    {
        // Bildirim Kaydı
        $BSorgu = $db->prepare("INSERT INTO bildirimler SET
            baslik   = :baslik,
            icon     = :icon,
            bid      = :bid,
            bildirim = :bildirim,
            ktarih   = :ktarih,
            tarih    = :tarih");
        
        $BSorgu->execute(array(
            'baslik'   => "Bağış Güncellendi",
            'icon'     => "ti-heart",
            'bid'      => $d_id,
            'bildirim' => "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> başlıklı bağışı güncelledi.",
            'ktarih'   => $bildirimkt,
            'tarih'    => $bildirimt
        ));
        
        $_SESSION['bagis_guncelle'] = 'yes';
    }
    else
    {
        $_SESSION['bagis_guncelle'] = 'no';
    }
    
    header("Location:../".yonetim."/bagis-duzenle/".$d_id.".html");
    exit();
}


##Bağış Kapak Sil##
if(@$_GET['bagisresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("bagisresimsil");
	@$resimid 	= $_GET['sid'];
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM bagislar WHERE id = '{$resimid}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/bagislar/".$resim_bul['kapak']);
		unlink("../".tema."/uploads/bagislar/kapak/".$resim_bul['kapak']);
		$sorgu = $db->prepare("UPDATE bagislar SET
			kapak	= ?
			WHERE id = ?");
		$guncelle = $sorgu->execute(array(
			"",
			$resimid
		));
		if($guncelle)
		{
			$last_id 		= $resim_bul['id'];
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Bağış Kapak Resmi Silindi",
				'icon' 		=> "ti-heart",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$resim_bul['adi']."</strong> başlıklı bağışın kapak resmini sildi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['bagisresimsil'] = 'yes';
			header("Location:../".yonetim."/bagis-duzenle/".$resimid.".html");
			exit();
		}
		else
		{
			$_SESSION['bagisresimsil'] = 'no';
			header("Location:../".yonetim."/bagis-duzenle/".$resimid.".html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-duzenle/".$resimid.".html");
		exit();
	}
}

##Bağış Sil##
if(@$_GET['bagissil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("bagissil");
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM bagislar WHERE id = '{$_GET['id']}'")->fetch(PDO::FETCH_ASSOC);
		unlink("../".tema."/uploads/bagislar/".$resim_bul['kapak']);
		unlink("../".tema."/uploads/bagislar/kapak/".$resim_bul['kapak']);
		$TSorgu = $db->prepare("DELETE FROM bagislar WHERE id = :id");
		$TSil	= $TSorgu->execute(array('id' => $_GET['id']));
		if($TSorgu->rowCount())
		{
			if($TSil)
			{
				$last_id 		= $_GET['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Bağış Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı bağışı sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['bagissil'] = 'yes';
				header("Location:../".yonetim."/bagis-listele.html");
				exit();
			}
			else
			{
				$_SESSION['bagissil'] = 'no';
				header("Location:../".yonetim."/bagis-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-listele.html");
		exit();
	}
}

##Bağış Toplu Sil ##
if(isset($_POST['bagis_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_tumu");
	$url = isset($_POST['url']) ? $_POST['url'] : '';
	if($_SESSION['rutbe'] == 0)
	{
		if(isset($_POST['id']) && is_array($_POST['id']) && count($_POST['id']) > 0)
		{
			$silinen_sayisi = 0;
			foreach($_POST['id'] as $i)
			{
				// ID'yi güvenli hale getir
				$i = intval($i);
				if($i <= 0) continue;
				
				$sayfa_bul= $db->prepare("SELECT * FROM bagislar WHERE id = ?");
				$sayfa_bul->execute(array($i));
				if($sayfa_bul->rowCount() > 0)
				{
					$sayfa_bul_data = $sayfa_bul->fetch(PDO::FETCH_ASSOC);
					$TopluSorgu = $db->prepare("DELETE FROM bagislar WHERE id = ?");
					$TopluSil	= $TopluSorgu->execute(array($i));
					if($TopluSil)
					{
						if(!empty($sayfa_bul_data['kapak']))
						{
							@unlink("../".tema."/uploads/bagislar/".$sayfa_bul_data['kapak']);
							@unlink("../".tema."/uploads/bagislar/kapak/".$sayfa_bul_data['kapak']);
						}
						$last_id 	= $i;					
						$BSorgu = $db->prepare("INSERT INTO bildirimler SET
							baslik		= :baslik,
							icon		= :icon,
							bid			= :bid,
							bildirim	= :bildirim,
							ktarih		= :ktarih,
							tarih 		= :tarih");
						$BEkle = $BSorgu->execute(array(
							'baslik' 	=> "Bağış Silindi",
							'icon' 		=> "icon-trash",
							'bid' 		=> $last_id,
							'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul_data['adi']."</strong> başlıklı bağışı sildi.",
							'ktarih'	=> $bildirimkt,
							'tarih'		=> $bildirimt
						));
						$silinen_sayisi++;
					}
				}
			}
			if($silinen_sayisi > 0)
			{
				$_SESSION['bagis_tumu'] = 'yes';
			}
			else
			{
				$_SESSION['bagis_tumu'] = 'no';
			}
			header("Location:../".yonetim."/bagis-listele.html");
			exit();
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/bagis-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-listele.html");
		exit();
	}	
}

##Bağış Toplu Aktif ##
if(isset($_POST['bagis_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM bagislar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE bagislar SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Bağış Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı bağışı aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['bagis_aktif'] = 'yes';
					header("Location:../".yonetim."/bagis-listele.html");
				}
				else
				{
					$_SESSION['bagis_aktif'] = 'no';
					header("Location:../".yonetim."/bagis-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/bagis-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-listele.html");
		exit();
	}	
}

##Bağış Toplu Pasif ##
if(isset($_POST['bagis_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM bagislar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE bagislar SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Bağış Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı bağışı pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['bagis_pasif'] = 'yes';
					header("Location:../".yonetim."/bagis-listele.html");
				}
				else
				{
					$_SESSION['bagis_pasif'] = 'no';
					header("Location:../".yonetim."/bagis-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/bagis-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-listele.html");
		exit();
	}	
}

##Bağış Tümünü Sil ##
if(@$_GET['bagistumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("bagistumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$HSorgu = $db->prepare("SELECT * FROM bagislar");
		$HSorgu->execute();
		$Hislem = $HSorgu->fetchALL(PDO::FETCH_ASSOC);
		foreach ( $Hislem as $HSonuc ){
			unlink("../".tema."/uploads/bagislar/".$HSonuc['kapak']);
			unlink("../".tema."/uploads/bagislar/kapak/".$HSonuc['kapak']);
		}
		$Sorgu = $db->prepare("TRUNCATE TABLE bagislar");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['bagistumunusil'] = 'yes';
			header("Location:../".yonetim."/bagis-listele.html");
			exit();
		}
		else
		{
			$_SESSION['bagistumunusil'] = 'no';
			header("Location:../".yonetim."/bagis-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-listele.html");
		exit();
	}
}

##Bağış Sıra Ajax##
if(isset($_GET['bagissiralama']))
{
	if($_GET['bagissiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE bagislar SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Gelen Bağış Sil##
if(@$_GET['gelen_bagissil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("gelen_bagissil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$Sorgu = $db->prepare("DELETE FROM bagis_odeme WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$_SESSION['gelen_bagissil'] = 'yes';
				header("Location:../".yonetim."/gelen-bagislar.html");
				exit();
			}
			else
			{
				$_SESSION['gelen_bagissil'] = 'no';
				header("Location:../".yonetim."/gelen-bagislar.html");
				exit();
			}
		}
		else
		{
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/gelen-bagislar.html");
		exit();
	}
}

##Çoklu Bağış Sil ##
if(isset($_POST['gelen_bagis_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("gelen_bagis_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$TopluSorgu = $db->prepare("DELETE FROM bagis_odeme WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$_SESSION['gelen_bagis_tumu'] = 'yes';
					header("Location:../".yonetim."/gelen-bagislar.html");
				}
				else
				{
					$_SESSION['gelen_bagis_tumu'] = 'no';
					header("Location:../".yonetim."/gelen-bagislar.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/gelen-bagislar.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/gelen-bagislar.html");
		exit();
	}	
}

##Aidat Kaydet ##
if(isset($_POST['aidat_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("aidat_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 				= $_POST["sira"];
		$adi 				= $_POST['adi'];
		$tc 					= $_POST['tc'];
		$baba_adi 			= $_POST['baba_adi'];
		$ucret 				= $_POST['ucret'];
		$oucret 				= $_POST['oucret'];
		$odeme 				= $_POST['odeme'];
		$tariha 				= $_POST['tariha'];
		$aciklama 			= $_POST['aciklama'];
		$tarih				= date('Y-m-d H:i:s');
		$tarih				= cVCLmHLxbS_tr_tarih($tarih);
		
		$sorgu = $db->prepare("INSERT INTO aidatlar SET
				sira 		= ?,
				adi 		= ?,
				tc 			= ?,
				baba_adi 	= ?,
				ucret 		= ?,
				oucret 		= ?,
				odeme 		= ?,
				tariha 		= ?,
				aciklama 	= ?,
				tarih 		= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$adi,
				$tc,
				$baba_adi,
				$ucret,
				$oucret,
				$odeme,
				$tariha,
				$aciklama,
				$tarih
		));
		if($Ekle)
		{
			$last_id  = $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Aidat Ekledi",
				'icon' 		=> "icon-credit-card",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> isimli üyeye aidat ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['aidat_ekle'] = 'yes';
			header("Location:../".yonetim."/aidat-listele.html");
			exit();
		}
		else
		{
			$_SESSION['aidat_ekle'] = 'no';
			header("Location:../".yonetim."/aidat-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/aidat-ekle.html");
		exit();
	}
}

##Aidat Güncelle ##
if(isset($_POST['aidat_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("aidat_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 				= $_POST["sira"];
		$adi 				= $_POST['adi'];
		$tc 					= $_POST['tc'];
		$baba_adi 			= $_POST['baba_adi'];
		$ucret 				= $_POST['ucret'];
		$oucret 				= $_POST['oucret'];
		$odeme 				= $_POST['odeme'];
		$tariha 				= $_POST['tariha'];
		$aciklama 			= $_POST['aciklama'];
		$tarih				= date('Y-m-d H:i:s');
		$tarih				= cVCLmHLxbS_tr_tarih($tarih);
		
		$sorgu = $db->prepare("UPDATE aidatlar SET
			sira 		= ?,
			adi 		= ?,
			tc 			= ?,
			baba_adi 	= ?,
			ucret 		= ?,
			oucret 		= ?,
			odeme 		= ?,
			tariha 		= ?,
			aciklama 	= ?,
			tarih		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$adi,
			$tc,
			$baba_adi,
			$ucret,
			$oucret,
			$odeme,
			$tariha,
			$aciklama,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Aidat Güncellendi",
				'icon' 		=> "icon-credit-card",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$adi."</strong> isimli üyenin aidatını güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['aidat_guncelle'] = 'yes';
			header("Location:../".yonetim."/aidat-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['aidat_guncelle'] = 'no';
			header("Location:../".yonetim."/aidat-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/aidat-duzenle/".$d_id.".html");
		exit();
	}
}

##Aidat Sil##
if(@$_GET['aidatsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("aidatsil");
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul	= $db->query("SELECT * FROM aidatlar WHERE id = '{$_GET['id']}'")->fetch(PDO::FETCH_ASSOC);
		$TSorgu = $db->prepare("DELETE FROM aidatlar WHERE id = :id");
		$TSil	= $TSorgu->execute(array('id' => $_GET['id']));
		if($TSorgu->rowCount())
		{
			if($TSil)
			{
				$last_id 		= $_GET['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Aidat Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> isimli üyenin aidatını sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['aidatsil'] = 'yes';
				header("Location:../".yonetim."/aidat-listele.html");
				exit();
			}
			else
			{
				$_SESSION['aidatsil'] = 'no';
				header("Location:../".yonetim."/aidat-listele.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/aidat-listele.html");
		exit();
	}
}

##Aidat Toplu Sil ##
if(isset($_POST['aidat_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("aidat_tumu");
	$url = $_POST['url'];
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM aidatlar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM aidatlar WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 	= $i;					
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Aidat Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> isimli üyenin aidatını sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['aidat_tumu'] = 'yes';
					header("Location:../".yonetim."/aidat-listele.html");
				}
				else
				{
					$_SESSION['aidat_tumu'] = 'no';
					header("Location:../".yonetim."/aidat-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/aidat-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/aidat-listele.html");
		exit();
	}	
}

##Aidat Toplu Ödendi ##
if(isset($_POST['aidat_odendi']))
{
	cVCLmHLxbS_panelislemkontrol("aidat_odendi");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM aidatlar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE aidatlar SET
					odeme 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Aidat Ödendi",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> isimli üyenin aidatını ödendi olarak ayarladı.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['aidat_odendi'] = 'yes';
					header("Location:../".yonetim."/aidat-listele.html");
				}
				else
				{
					$_SESSION['aidat_odendi'] = 'no';
					header("Location:../".yonetim."/aidat-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/aidat-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/aidat-listele.html");
		exit();
	}	
}

##Aidat Toplu Ödenmedi ##
if(isset($_POST['aidat_odenmedi']))
{
	cVCLmHLxbS_panelislemkontrol("aidat_odenmedi");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM aidatlar WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE aidatlar SET
					odeme 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Aidat Ödenmedi",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> isimli üyenin aidatını odenmedi olarak ayarladı.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['aidat_odenmedi'] = 'yes';
					header("Location:../".yonetim."/aidat-listele.html");
				}
				else
				{
					$_SESSION['aidat_odenmedi'] = 'no';
					header("Location:../".yonetim."/aidat-listele.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/aidat-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/aidat-listele.html");
		exit();
	}	
}

##Aidat Tümünü Sil ##
if(@$_GET['aidattumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("aidattumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$Sorgu = $db->prepare("TRUNCATE TABLE aidatlar");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['aidattumunusil'] = 'yes';
			header("Location:../".yonetim."/aidat-listele.html");
			exit();
		}
		else
		{
			$_SESSION['aidattumunusil'] = 'no';
			header("Location:../".yonetim."/aidat-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/aidat-listele.html");
		exit();
	}
}

##Aidat Sıra Ajax##
if(isset($_GET['aidatsiralama']))
{
	if($_GET['aidatsiralama'] == 'sira')
	{
		if(is_array($_POST['item']))
		{
			foreach( $_POST['item'] as $key => $value)
			{
				$ayarkaydet = $db->prepare("UPDATE aidatlar SET
					sira=:sira
					WHERE id={$value}");
				$update = $ayarkaydet->execute(array(
					'sira' => $key
				));
			}
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'Güncellendi');
		}
		else
		{
			$returnMsg = array( 'islemSonuc' => true , 'islemMsj' => 'İşlem başarısız');
		}
	}
}

##Haberler Excel ##
if(isset($_POST['haberler_excel']))
{
	cVCLmHLxbS_panelislemkontrol("haberler_excel");
	if($_SESSION['rutbe'] == 0)
	{
		require_once('Classes/PHPExcel/IOFactory.php');

		$allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
		if(in_array($_FILES["file"]["type"],$allowedFileType))
		{
			$inputfilename = $_FILES['file']['tmp_name'];
			$exceldata = array();
	
			$inputfiletype = PHPExcel_IOFactory::identify($inputfilename);
			$objReader = PHPExcel_IOFactory::createReader($inputfiletype);
			$objPHPExcel = $objReader->load($inputfilename);
			
			$sheet 			= $objPHPExcel->getSheet(0);
			$highestRow		= $sheet->getHighestRow();
			$highestColumn 	= $sheet->getHighestColumn();
			
			for ($row = 2; $row <= $highestRow; $row++)
			{
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
				
				$id = "1";
				if(isset($rowData[0][0])) {
					$id = strip_tags($rowData[0][0]);
				}

				$sira = "0";
				if(isset($rowData[0][1])) {
					$sira = strip_tags($rowData[0][1]);
				}
				
				$kategori = "0";
				if(isset($rowData[0][2])) {
					$kategori = strip_tags($rowData[0][2]);
				}
				
				$adi = NULL;
				if(isset($rowData[0][3])) {
					$adi = strip_tags($rowData[0][3]);
				}
				
				$aciklama = NULL;
				if(isset($rowData[0][4])) {
					$aciklama = $rowData[0][4];
				}
				
				$videoid = NULL;
				if(isset($rowData[0][5])) {
					$videoid = strip_tags($rowData[0][5]);
				}
				
				$spot = NULL;
				if(isset($rowData[0][6])) {
					$spot = strip_tags($rowData[0][6]);
				}
				
				$keywords = NULL;
				if(isset($rowData[0][7])) {
					$keywords = strip_tags($rowData[0][7]);
				}
				
				$description = NULL;
				if(isset($rowData[0][8])) {
					$description = strip_tags($rowData[0][8]);
				}

				$durum = "0";
				if(isset($rowData[0][9])) {
					$durum = strip_tags($rowData[0][9]);
				}
				
				$manset = "0";
				if(isset($rowData[0][10])) {
					$manset = strip_tags($rowData[0][10]);
				}
				
				$manset_yani = "0";
				if(isset($rowData[0][11])) {
					$manset_yani = strip_tags($rowData[0][11]);
				}
				
				$resim = NULL;
				if(isset($rowData[0][12])) {
					$resim = strip_tags($rowData[0][12]);
				}
				
				$tarih = NULL;
				if(isset($rowData[0][13])) {
					$tarih = strip_tags($rowData[0][13]);
				}
				
				$tarihg = NULL;
				if(isset($rowData[0][14])) {
					$tarihg = strip_tags($rowData[0][14]);
				}
					
				if (!empty($adi))
				{
					$kayitvarmi = $db->prepare("SELECT * FROM haberler WHERE id = ?");
					$kayitvarmi->execute(array($id));
					if($kayitvarmi->rowCount())
					{
						$KayitSonuc = $kayitvarmi->fetch(PDO::FETCH_ASSOC);
						$sorgu = $db->prepare("UPDATE haberler SET
							kategori 	= ?,
							sira 		= ?,
							adi 		= ?,
							seo 		= ?,
							spot		= ?,
							videoid		= ?,
							aciklama	= ?,
							keywords	= ?,
							description	= ?,
							durum 		= ?,
							manset 		= ?,
							manset_yani = ?,
							resim 		= ?,
							dil 		= ?,
							tarihg 		= ?,
							tarih 		= ?
							WHERE id 	= ?");
						$ekle = $sorgu->execute(array(
							$kategori,
							$sira,
							$adi,
							cVCLmHLxbS_seo($adi),
							$spot,
							$videoid,
							$aciklama,
							$keywords,
							$description,
							$durum,
							$manset,
							$manset_yani,
							$resim,
							$_SESSION['admin_dil'],
							$tarihg,
							$tarih,
							$KayitSonuc['id']
						));
					}
					else
					{
						$sorgu = $db->prepare("INSERT INTO haberler SET
							id 			= ?,
							kategori 	= ?,
							sira 		= ?,
							adi 		= ?,
							seo 		= ?,
							spot		= ?,
							videoid		= ?,
							aciklama	= ?,
							keywords	= ?,
							description	= ?,
							durum 		= ?,
							manset 		= ?,
							manset_yani = ?,
							resim 		= ?,
							dil 		= ?,
							tarihg 		= ?,
							tarih 		= ?");
						$ekle = $sorgu->execute(array(
							$id,
							$kategori,
							$sira,
							$adi,
							cVCLmHLxbS_seo($adi),
							$spot,
							$videoid,
							$aciklama,
							$keywords,
							$description,
							$durum,
							$manset,
							$manset_yani,
							$resim,
							$_SESSION['admin_dil'],
							$tarihg,
							$tarih
						));
					}					
					if ($ekle)
					{
						$_SESSION['haberler_excel'] = 'yes';
						header("Location:../".yonetim."/haberler-excel.html");
					}
					else
					{
						$_SESSION['haberler_excel'] = 'no';
						header("Location:../".yonetim."/haberler-excel.html");
					}
				}
			}
		  }
		  else
		  {
			$_SESSION['haberler_excel'] = 'dosya_tur';
			header("Location:../".yonetim."/haberler-excel.html");
		  }
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haberler-excel.html");
		exit();
	}
}

##Haber Resimler Excel ##
if(isset($_POST['haberler_resim_excel']))
{
	cVCLmHLxbS_panelislemkontrol("haberler_resim_excel");
	if($_SESSION['rutbe'] == 0)
	{
		require_once('Classes/PHPExcel/IOFactory.php');

		$allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
		if(in_array($_FILES["file"]["type"],$allowedFileType))
		{
			$inputfilename = $_FILES['file']['tmp_name'];
			$exceldata = array();
	
			$inputfiletype = PHPExcel_IOFactory::identify($inputfilename);
			$objReader = PHPExcel_IOFactory::createReader($inputfiletype);
			$objPHPExcel = $objReader->load($inputfilename);
			
			$sheet 			= $objPHPExcel->getSheet(0);
			$highestRow		= $sheet->getHighestRow();
			$highestColumn 	= $sheet->getHighestColumn();
			
			for ($row = 2; $row <= $highestRow; $row++)
			{
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
				
				$id = "1";
				if(isset($rowData[0][0])) {
					$id = strip_tags($rowData[0][0]);
				}

				$resimid = "0";
				if(isset($rowData[0][1])) {
					$resimid = strip_tags($rowData[0][1]);
				}
				
				$resim = NULL;
				if(isset($rowData[0][2])) {
					$resim = strip_tags($rowData[0][2]);
				}
					
				if (!empty($resimid))
				{
					
					$sorgu = $db->prepare("INSERT INTO haberfoto SET
							id 			= ?,
							resimid 	= ?,
							resim 		= ?");
					$ekle = $sorgu->execute(array(
							$id,
							$resimid,
							$resim
					));
					if ($ekle)
					{
						$_SESSION['haberler_resim_excel'] = 'yes';
						header("Location:../".yonetim."/haberler-resim-excel.html");
					}
					else
					{
						$_SESSION['haberler_resim_excel'] = 'no';
						header("Location:../".yonetim."/haberler-resim-excel.html");
					}
				}
			}
		  }
		  else
		  {
			$_SESSION['haberler_resim_excel'] = 'dosya_tur';
			header("Location:../".yonetim."/haberler-resim-excel.html");
			exit();
		  }
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/haberler-excel.html");
		exit();
	}
}

##Admin Dil Güncelle##
if(isset($_POST['admindilchange']))
{
	cVCLmHLxbS_panelislemkontrol("admindilchange");
	if($_SESSION['rutbe'] == 0)
	{
		$dizin = '../language/admin_dil.php';
		require_once($dizin);
		$admindil[$_POST['key']] = $_POST['value'];
		$line = "<?php ".PHP_EOL;
		foreach ($admindil as $key => $value)
		{
			$line .= '$admindil["'.$key.'"] = "'.$value.'";'.PHP_EOL;
			$file=fopen($dizin,'w');
			fwrite($file,$line);
			fclose($file);
		}
		exit;
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/admin-dil-duzenle.html");
		exit();
	}
}

##Site Dil Güncelle##
if(isset($_POST['sitedilchange']))
{
	cVCLmHLxbS_panelislemkontrol("sitedilchange");
	if($_SESSION['rutbe'] == 0)
	{
		$dilid = $_POST['dil_id'];
		$dizin = '../language/dil_'.$dilid.".php";
		$dil = [];
		require_once($dizin);
		$dil[$_POST['key']] = $_POST['value'];
		$line = "<?php ".PHP_EOL;
		foreach ($dil as $key => $value)
		{
			$line .= '$dil["'.$key.'"] = "'.$value.'";'.PHP_EOL;
			$file=fopen('../language/dil_'.$dilid.'.php','w');
			fwrite($file,$line);
			fclose($file);
		}
		exit;
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/dil-duzenle/".$dilid .".html");
		exit();
	}
}

if(isset($returnMsg))
{
	echo json_encode($returnMsg);
}

##Çıkış Yap##
if(@$_GET['cikis'] == "ok")
{
	$_SESSION["guvenlik"] = array("cikis" => cVCLmHLxbS_kod());
	cVCLmHLxbS_panelislemkontrol("cikis");
	unset($_SESSION['Yonetim_Id']);
	unset($_SESSION['Yonetim_Kadi']);
	unset($_SESSION['Yonetim_Sifre']);
	unset($_SESSION['rutbe']);
	unset($_SESSION['guvenlik']);
	header("Location:../".yonetim."/index.html");
	exit();
}
unset($_SESSION['guvenlik']);

##Impact & Reach İşlemleri##
if(isset($_POST['impact_reach_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("impact_reach_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$ikon 		= $_POST['ikon'];
		$baslik 	= $_POST['baslik'];
		$sayi 		= $_POST['sayi'];
		$aciklama 	= $_POST['aciklama'];
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);

		$sorgu = $db->prepare("INSERT INTO impact_reach SET
				sira 		= ?,
				ikon 		= ?,
				baslik 		= ?,
				sayi 		= ?,
				aciklama	= ?,
				durum 		= ?,
				dil 		= ?,
				tarih 		= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$ikon,
				$baslik,
				$sayi,
				$aciklama,
				$durum,
				$_SESSION['admin_dil'],
				$tarih
		));
		if($Ekle)
		{
			$_SESSION['yonetim_mesaj'] = 'ekleme_basarili';
			header("Location:../".yonetim."/impact_reach_listele.html");
			exit();
		}
		else
		{
			$_SESSION['yonetim_mesaj'] = 'ekleme_basarisiz';
			header("Location:../".yonetim."/impact_reach_ekle.html");
			exit();
		}
	}
}

if(isset($_POST['impact_reach_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("impact_reach_guncelle");
	if($_SESSION['rutbe'] == 0)
	{
		$id 		= $_POST['id'];
		$sira 		= $_POST['sira'];
		$ikon 		= $_POST['ikon'];
		$baslik 	= $_POST['baslik'];
		$sayi 		= $_POST['sayi'];
		$aciklama 	= $_POST['aciklama'];
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}

		$sorgu = $db->prepare("UPDATE impact_reach SET
				sira 		= ?,
				ikon 		= ?,
				baslik 		= ?,
				sayi 		= ?,
				aciklama	= ?,
				durum 		= ?
				WHERE id = ?");
		$Guncelle = $sorgu->execute(array(
				$sira,
				$ikon,
				$baslik,
				$sayi,
				$aciklama,
				$durum,
				$id
		));
		if($Guncelle)
		{
			$_SESSION['yonetim_mesaj'] = 'guncelleme_basarili';
			header("Location:../".yonetim."/impact_reach_listele.html");
			exit();
		}
		else
		{
			$_SESSION['yonetim_mesaj'] = 'guncelleme_basarisiz';
			header("Location:../".yonetim."/impact_reach_ekle/islem/duzenle/id/".$id.".html");
			exit();
		}
	}
}

if(isset($_POST['impact_reach_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("impact_reach_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		$idler = $_POST['id'];
		foreach($idler as $id)
		{
			$sorgu = $db->prepare("DELETE FROM impact_reach WHERE id = ?");
			$sorgu->execute(array($id));
		}
		$_SESSION['yonetim_mesaj'] = 'silme_basarili';
		header("Location:../".yonetim."/impact_reach_listele.html");
		exit();
	}
}

if(isset($_POST['impact_reach_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("impact_reach_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		$idler = $_POST['id'];
		foreach($idler as $id)
		{
			$sorgu = $db->prepare("UPDATE impact_reach SET durum = 1 WHERE id = ?");
			$sorgu->execute(array($id));
		}
		$_SESSION['yonetim_mesaj'] = 'aktif_basarili';
		header("Location:../".yonetim."/impact_reach_listele.html");
		exit();
	}
}

if(isset($_POST['impact_reach_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("impact_reach_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		$idler = $_POST['id'];
		foreach($idler as $id)
		{
			$sorgu = $db->prepare("UPDATE impact_reach SET durum = 0 WHERE id = ?");
			$sorgu->execute(array($id));
		}
		$_SESSION['yonetim_mesaj'] = 'pasif_basarili';
		header("Location:../".yonetim."/impact_reach_listele.html");
		exit();
	}
}

if(isset($_GET['impact_reach_tumunusil']))
{
	cVCLmHLxbS_panelislemkontrol("impact_reach_tumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$sorgu = $db->prepare("DELETE FROM impact_reach WHERE dil = ?");
		$sorgu->execute(array($_SESSION['admin_dil']));
		$_SESSION['yonetim_mesaj'] = 'tum_silme_basarili';
		header("Location:../".yonetim."/impact_reach_listele.html");
		exit();
	}
}

if(isset($_GET['tablo']) && $_GET['tablo'] == 'impact_reach' && isset($_GET['islem']) && $_GET['islem'] == 'sil')
{
	cVCLmHLxbS_panelislemkontrol("impact_reach_sil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$sorgu = $db->prepare("DELETE FROM impact_reach WHERE id = ?");
		$sorgu->execute(array($id));
		$_SESSION['yonetim_mesaj'] = 'silme_basarili';
		header("Location:../".yonetim."/impact_reach_listele.html");
		exit();
	}
}

## Programlar İşlemleri ##

if(isset($_POST['program_ekle']))
{
    cVCLmHLxbS_panelislemkontrol("program_ekle");

    if($_SESSION['rutbe'] == 0)
    {
        $sira           = $_POST['sira'];
        $baslik         = $_POST['baslik'];
        $aciklama       = $_POST['aciklama'];
        $banner_baslik  = $_POST['banner_baslik'];
        $banner_aciklama= $_POST['banner_aciklama'];
        $detay_aciklama = $_POST['detay_aciklama'];
        $seo            = $_POST['seo'];
        $description    = $_POST['description'];
        $keywords       = $_POST['keywords'];
        $durum          = isset($_POST['durum']) ? 1 : 0;
        $anasayfa_durum = isset($_POST['anasayfa_durum']) ? 1 : 0;
        $banner_renk1   = isset($_POST['banner_renk1']) ? $_POST['banner_renk1'] : '#667eea';
        $banner_ara_serit = isset($_POST['banner_ara_serit']) ? $_POST['banner_ara_serit'] : '#764ba2';
        $banner_renk2   = isset($_POST['banner_renk2']) ? $_POST['banner_renk2'] : '#f093fb';
        $tab_aktif_renk = isset($_POST['tab_aktif_renk']) ? $_POST['tab_aktif_renk'] : '#667eea';
        $banner_baslik_renk   = isset($_POST['banner_baslik_renk']) ? $_POST['banner_baslik_renk'] : '#fff';
        $banner_aciklama_renk   = isset($_POST['banner_aciklama_renk']) ? $_POST['banner_aciklama_renk'] : '#fff';
        $banner_baslik_renk2   = isset($_POST['banner_baslik_renk2']) ? $_POST['banner_baslik_renk2'] : '#fff';
        $banner_aciklama_renk2   = isset($_POST['banner_aciklama_renk2']) ? $_POST['banner_aciklama_renk2'] : '#fff';
        $tarih          = cVCLmHLxbS_tr_tarih(date('Y-m-d H:i:s'));
        $resim          = '';
        $banner_resim   = '';

        // 🔹 Dosya yükleme işlemi
        $upload = new upload($_FILES['resim']);
        if ($upload->uploaded)
        {
            $upload->file_auto_rename = true;
            $upload->process("../".tema."/uploads/programlar");
			
            
            $upload->file_auto_rename = true;
            $upload->image_resize = true;
            $upload->image_ratio_crop = true;
            $upload->image_x = 400;
            $upload->image_y = 300;
            $upload->process("../".tema."/uploads/programlar/kapak");
            if ($upload->processed)
            {
                $resim=''.$upload->file_dst_name.'';
            }
        }

        // 🔹 Banner Dosya yükleme işlemi
        $upload_banner = new upload($_FILES['banner_resim']);
        if ($upload_banner->uploaded)
        {
            $upload_banner->file_auto_rename = true;
            $upload_banner->process("../".tema."/uploads/programlar");
            if ($upload_banner->processed)
            {
                $banner_resim=''.$upload_banner->file_dst_name.'';
            }
        }

        // 🔹 Veritabanı ekleme işlemi
$sorgu = $db->prepare("INSERT INTO programlar SET
	sira = ?, 
	baslik = ?, 
	aciklama = ?, 
	banner_baslik = ?, 
	banner_aciklama = ?, 
	detay_aciklama = ?, 
	resim = ?, 
	banner_resim = ?,
	seo = ?, 
	description = ?, 
	keywords = ?, 
	durum = ?, 
	anasayfa_durum = ?, 
	banner_renk1 = ?, 
	banner_ara_serit = ?, 
	banner_renk2 = ?, 
	tab_aktif_renk = ?,
	banner_baslik_renk = ?,
	banner_aciklama_renk = ?,
	banner_baslik_renk2 = ?,
	banner_aciklama_renk2 = ?,
	dil = ?, 
	tarih = ?
");

$Ekle = $sorgu->execute([
	$sira,
	$baslik,
	$aciklama,
	$banner_baslik,
	$banner_aciklama,
	$detay_aciklama,
	$resim,
	$banner_resim,
	$seo,
	$description,
	$keywords,
	$durum,
	$anasayfa_durum,
	$banner_renk1,
	$banner_ara_serit,
	$banner_renk2,
	$tab_aktif_renk,
	$banner_baslik_renk,
	$banner_aciklama_renk,
	$banner_baslik_renk2,
	$banner_aciklama_renk2,
	$_SESSION['admin_dil'],
	$tarih
]);


        if($Ekle) {
            $_SESSION['yonetim_mesaj'] = 'ekleme_basarili';
            header("Location:../".yonetim."/program_listele.html");
        } else {
            $_SESSION['yonetim_mesaj'] = 'ekleme_basarisiz';
            header("Location:../".yonetim."/program_ekle.html");
        }
        exit();
    }
}


if(isset($_POST['program_guncelle']))
{
    cVCLmHLxbS_panelislemkontrol("program_guncelle");

    if($_SESSION['rutbe'] == 0)
    {
        $id             = $_POST['id'];
        $sira           = $_POST['sira'];
        $baslik         = $_POST['baslik'];
        $banner_renk1   = isset($_POST['banner_renk1']) ? $_POST['banner_renk1'] : '#667eea';
        $banner_ara_serit = isset($_POST['banner_ara_serit']) ? $_POST['banner_ara_serit'] : '#764ba2';
        $banner_renk2   = isset($_POST['banner_renk2']) ? $_POST['banner_renk2'] : '#f093fb';
        $tab_aktif_renk = isset($_POST['tab_aktif_renk']) ? $_POST['tab_aktif_renk'] : '#667eea';
        $banner_baslik_renk   = isset($_POST['banner_baslik_renk']) ? $_POST['banner_baslik_renk'] : '#fff';
        $banner_aciklama_renk   = isset($_POST['banner_aciklama_renk']) ? $_POST['banner_aciklama_renk'] : '#fff';
        $banner_baslik_renk2   = isset($_POST['banner_baslik_renk2']) ? $_POST['banner_baslik_renk2'] : '#fff';
        $banner_aciklama_renk2   = isset($_POST['banner_aciklama_renk2']) ? $_POST['banner_aciklama_renk2'] : '#fff';
        $aciklama       = $_POST['aciklama'];
        $banner_baslik  = $_POST['banner_baslik'];
        $banner_aciklama= $_POST['banner_aciklama'];
        $detay_aciklama = $_POST['detay_aciklama'];
        $seo            = $_POST['seo'];
        $description    = $_POST['description'];
        $keywords       = $_POST['keywords'];
        $durum          = isset($_POST['durum']) ? 1 : 0;
        $anasayfa_durum = isset($_POST['anasayfa_durum']) ? 1 : 0;
        $resim          = null;
        $banner_resim   = null;

        $upload = new upload($_FILES['resim']);
        if ($upload->uploaded)
        {
            $upload->file_auto_rename = true;
            $upload->process("../".tema."/uploads/programlar");
            
            $upload->file_auto_rename = true;
            $upload->image_resize = true;
            $upload->image_ratio_crop = true;
            $upload->image_x = 400;
            $upload->image_y = 300;
            $upload->process("../".tema."/uploads/programlar/kapak");
            if ($upload->processed)
            {
                $resim=''.$upload->file_dst_name.'';
            }
        }
        
        if(isset($resim)){
            $resim_bul= $db->query("SELECT * FROM programlar WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
            if(!empty($resim_bul['resim'])) {
                unlink("../".tema."/uploads/programlar/".$resim_bul['resim']);
                unlink("../".tema."/uploads/programlar/kapak/".$resim_bul['resim']);
            }
            $guncelle = $db->prepare("UPDATE programlar SET resim = ? WHERE id = ?");
            $guncelle->execute([$resim,$id]);
            $resim=''.$upload->file_dst_name.'';
        }

        $upload_banner = new upload($_FILES['banner_resim']);
        if ($upload_banner->uploaded)
        {
            $upload_banner->file_auto_rename = true;
            $upload_banner->process("../".tema."/uploads/programlar");
            if ($upload_banner->processed)
            {
                $banner_resim=''.$upload_banner->file_dst_name.'';
            }
        }

        if(isset($banner_resim)){
            $banner_bul= $db->query("SELECT * FROM programlar WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
            if(!empty($banner_bul['banner_resim'])) {
                if(file_exists("../".tema."/uploads/programlar/".$banner_bul['banner_resim'])){
                   unlink("../".tema."/uploads/programlar/".$banner_bul['banner_resim']);
                }
            }
            $guncelle_banner = $db->prepare("UPDATE programlar SET banner_resim = ? WHERE id = ?");
            $guncelle_banner->execute([$banner_resim,$id]);
        }

// 🔹 Güncelleme sorgusu
$sorgu = $db->prepare("UPDATE programlar SET
	sira = ?,
	baslik = ?,
	aciklama = ?,
	banner_baslik = ?,
	banner_aciklama = ?,
	detay_aciklama = ?,
	seo = ?,
	description = ?,
	keywords = ?,
	durum = ?,
	anasayfa_durum = ?,
	banner_renk1 = ?,
	banner_ara_serit = ?,
	banner_renk2 = ?,
	tab_aktif_renk = ?,
	banner_baslik_renk = ?,
	banner_aciklama_renk = ?,
	banner_baslik_renk2 = ?,
	banner_aciklama_renk2 = ?
	WHERE id = ?
");
$Guncelle = $sorgu->execute([
	$sira,
	$baslik,
	$aciklama,
	$banner_baslik,
	$banner_aciklama,
	$detay_aciklama,
	$seo,
	$description,
	$keywords,
	$durum,
	$anasayfa_durum,
	$banner_renk1,
	$banner_ara_serit,
	$banner_renk2,
	$tab_aktif_renk,
	$banner_baslik_renk,
	$banner_aciklama_renk,
	$banner_baslik_renk2,
	$banner_aciklama_renk2,
	$id
]);


        if($Guncelle) {
            $_SESSION['yonetim_mesaj'] = 'guncelleme_basarili';
		header("Location:".$_SERVER['HTTP_REFERER']."");
		exit();
        } else {
            $_SESSION['yonetim_mesaj'] = 'guncelleme_basarisiz';
		header("Location:".$_SERVER['HTTP_REFERER']."");
		exit();
        }
        exit();
    }
}

if(isset($_POST['program_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("program_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		$idler = $_POST['id'];
		foreach($idler as $id)
		{
			$sorgu = $db->prepare("DELETE FROM programlar WHERE id = ?");
			$sorgu->execute(array($id));
		}
		$_SESSION['yonetim_mesaj'] = 'silme_basarili';
		header("Location:../".yonetim."/program_listele.html");
		exit();
	}
}

if(isset($_POST['program_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("program_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		$idler = $_POST['id'];
		foreach($idler as $id)
		{
			$sorgu = $db->prepare("UPDATE programlar SET durum = 1 WHERE id = ?");
			$sorgu->execute(array($id));
		}
		$_SESSION['yonetim_mesaj'] = 'aktif_basarili';
		header("Location:../".yonetim."/program_listele.html");
		exit();
	}
}

if(isset($_POST['program_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("program_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		$idler = $_POST['id'];
		foreach($idler as $id)
		{
			$sorgu = $db->prepare("UPDATE programlar SET durum = 0 WHERE id = ?");
			$sorgu->execute(array($id));
		}
		$_SESSION['yonetim_mesaj'] = 'pasif_basarili';
		header("Location:../".yonetim."/program_listele.html");
		exit();
	}
}

if(isset($_GET['programresimsil']))
{
	cVCLmHLxbS_panelislemkontrol("programresimsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['sid'];
		$Sorgu = $db->prepare("SELECT * FROM programlar WHERE id = ?");
		$Sorgu->execute(array($id));
		if($Sorgu->rowCount())
		{
			$Sonuc = $Sorgu->fetch(PDO::FETCH_ASSOC);
			if($Sonuc['resim'])
			{
				if(file_exists(tema.'/uploads/programlar/'.$Sonuc['resim']))
				{
					unlink(tema.'/uploads/programlar/'.$Sonuc['resim']);
				}
				$sorgu = $db->prepare("UPDATE programlar SET resim = '' WHERE id = ?");
				$sorgu->execute(array($id));
			}
		}
		$_SESSION['yonetim_mesaj'] = 'resim_silme_basarili';
		header("Location:../".yonetim."/program_ekle/islem/duzenle/id/".$id.".html");
		exit();
	}
}

if(isset($_GET['programbannerresimsil']))
{
	cVCLmHLxbS_panelislemkontrol("programresimsil"); // Same permission check
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['sid'];
		$Sorgu = $db->prepare("SELECT * FROM programlar WHERE id = ?");
		$Sorgu->execute(array($id));
		if($Sorgu->rowCount())
		{
			$Sonuc = $Sorgu->fetch(PDO::FETCH_ASSOC);
			if($Sonuc['banner_resim'])
			{
				if(file_exists(tema.'/uploads/programlar/'.$Sonuc['banner_resim']))
				{
					unlink(tema.'/uploads/programlar/'.$Sonuc['banner_resim']);
				}
				$sorgu = $db->prepare("UPDATE programlar SET banner_resim = '' WHERE id = ?");
				$sorgu->execute(array($id));
			}
		}
		$_SESSION['yonetim_mesaj'] = 'resim_silme_basarili';
		header("Location:../".yonetim."/program_ekle/islem/duzenle/id/".$id.".html");
		exit();
	}
}

if(isset($_GET['program_tumunusil']))
{ 
	cVCLmHLxbS_panelislemkontrol("program_tumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$sorgu = $db->prepare("DELETE FROM programlar WHERE dil = ?");
		$sorgu->execute(array($_SESSION['admin_dil']));
		$_SESSION['yonetim_mesaj'] = 'tum_silme_basarili';
		header("Location:../".yonetim."/program_listele.html");
		exit();
	}
}

## Karakter ve Sosyal Gelişim Programları İşlemleri ##

if(isset($_POST['karakter_program_ayar_kaydet']))
{
    cVCLmHLxbS_panelislemkontrol("karakter_program_ayar_kaydet");
    if($_SESSION['rutbe'] == 0)
    {
        $karakterprogramlari_text = $_POST['karakterprogramlari_text'];
        $karakterprogramlari_video = $_POST['karakterprogramlari_video'] ?? '';
        $sorgu = $db->prepare("UPDATE sabit_url SET karakterprogramlari_text = ?, karakterprogramlari_video = ? WHERE id = 1");
        $guncelle = $sorgu->execute([$karakterprogramlari_text, $karakterprogramlari_video]);
        if($guncelle) {
            $_SESSION['yonetim_mesaj'] = 'guncelleme_basarili';
        } else {
            $_SESSION['yonetim_mesaj'] = 'guncelleme_basarisiz';
        }
        header("Location:".$_SERVER['HTTP_REFERER']."");
        exit();
    }
}

if(isset($_POST['karakter_program_ekle']))
{
    cVCLmHLxbS_panelislemkontrol("karakter_program_ekle");

    if($_SESSION['rutbe'] == 0)
    {
        $sira           = $_POST['sira'];
        $baslik         = $_POST['baslik'];
        $aciklama       = $_POST['aciklama'];
        $detay_aciklama = $_POST['detay_aciklama'];
        $seo            = $_POST['seo'];
        $description    = $_POST['description'];
        $keywords       = $_POST['keywords'];
        $durum          = isset($_POST['durum']) ? 1 : 0;
        $dil            = $_POST['dil'];
        $tarih          = cVCLmHLxbS_tr_tarih(date('Y-m-d H:i:s'));
        $resim          = '';

        $upload = new upload($_FILES['resim']);
        if ($upload->uploaded)
        {
            $upload->file_auto_rename = true;
            $upload->process("../".tema."/uploads/karakter_programlari");

            $upload->file_auto_rename = true;
            $upload->image_resize = true;
            $upload->image_ratio_crop = true;
            $upload->image_x = 400;
            $upload->image_y = 300;
            $upload->process("../".tema."/uploads/karakter_programlari/kapak");
            if ($upload->processed)
            {
                $resim=''.$upload->file_dst_name.'';
            }
        }

        $sorgu = $db->prepare("INSERT INTO karakter_programlari SET
            sira = ?,
            baslik = ?,
            aciklama = ?,
            detay_aciklama = ?,
            resim = ?,
            seo = ?,
            description = ?,
            keywords = ?,
            durum = ?,
            dil = ?,
            tarih = ?
        ");

        $Ekle = $sorgu->execute([
            $sira,
            $baslik,
            $aciklama,
            $detay_aciklama,
            $resim,
            $seo,
            $description,
            $keywords,
            $durum,
            $dil,
            $tarih
        ]);

        if($Ekle) {
            $_SESSION['yonetim_mesaj'] = 'ekleme_basarili';
            header("Location:../".yonetim."/karakter_program_listele.html");
        } else {
            $_SESSION['yonetim_mesaj'] = 'ekleme_basarisiz';
            header("Location:../".yonetim."/karakter_program_ekle.html");
        }
        exit();
    }
}

if(isset($_POST['karakter_program_guncelle']))
{
    cVCLmHLxbS_panelislemkontrol("karakter_program_guncelle");

    if($_SESSION['rutbe'] == 0)
    {
        $id             = $_POST['id'];
        $sira           = $_POST['sira'];
        $baslik         = $_POST['baslik'];
        $aciklama       = $_POST['aciklama'];
        $detay_aciklama = $_POST['detay_aciklama'];
        $seo            = $_POST['seo'];
        $description    = $_POST['description'];
        $keywords       = $_POST['keywords'];
        $durum          = isset($_POST['durum']) ? 1 : 0;
        $dil            = $_POST['dil'];
        $resim          = null;

        $upload = new upload($_FILES['resim']);
        if ($upload->uploaded)
        {
            $upload->file_auto_rename = true;
            $upload->process("../".tema."/uploads/karakter_programlari");

            $upload->file_auto_rename = true;
            $upload->image_resize = true;
            $upload->image_ratio_crop = true;
            $upload->image_x = 400;
            $upload->image_y = 300;
            $upload->process("../".tema."/uploads/karakter_programlari/kapak");
            if ($upload->processed)
            {
                $resim=''.$upload->file_dst_name.'';
            }
        }

        if(isset($resim)){
            $resim_bul= $db->query("SELECT * FROM karakter_programlari WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
            if(!empty($resim_bul['resim'])) {
                if(file_exists("../".tema."/uploads/karakter_programlari/".$resim_bul['resim'])) unlink("../".tema."/uploads/karakter_programlari/".$resim_bul['resim']);
                if(file_exists("../".tema."/uploads/karakter_programlari/kapak/".$resim_bul['resim'])) unlink("../".tema."/uploads/karakter_programlari/kapak/".$resim_bul['resim']);
            }
            $guncelle = $db->prepare("UPDATE karakter_programlari SET resim = ? WHERE id = ?");
            $guncelle->execute([$resim,$id]);
        }

        $sorgu = $db->prepare("UPDATE karakter_programlari SET
            sira = ?,
            baslik = ?,
            aciklama = ?,
            detay_aciklama = ?,
            seo = ?,
            description = ?,
            keywords = ?,
            durum = ?,
            dil = ?
            WHERE id = ?
        ");
        $Guncelle = $sorgu->execute([
            $sira,
            $baslik,
            $aciklama,
            $detay_aciklama,
            $seo,
            $description,
            $keywords,
            $durum,
            $dil,
            $id
        ]);

        if($Guncelle) {
            $_SESSION['yonetim_mesaj'] = 'guncelleme_basarili';
            header("Location:".$_SERVER['HTTP_REFERER']."");
            exit();
        } else {
            $_SESSION['yonetim_mesaj'] = 'guncelleme_basarisiz';
            header("Location:".$_SERVER['HTTP_REFERER']."");
            exit();
        }
        exit();
    }
}

if(isset($_POST['karakter_program_tumu']))
{
    cVCLmHLxbS_panelislemkontrol("karakter_program_tumu");
    if($_SESSION['rutbe'] == 0)
    {
        $idler = $_POST['id'];
        foreach($idler as $id)
        {
            $sorgu = $db->prepare("DELETE FROM karakter_programlari WHERE id = ?");
            $sorgu->execute(array($id));
        }
        $_SESSION['yonetim_mesaj'] = 'silme_basarili';
        header("Location:../".yonetim."/karakter_program_listele.html");
        exit();
    }
}

if(isset($_POST['karakter_program_aktif']))
{
    cVCLmHLxbS_panelislemkontrol("karakter_program_aktif");
    if($_SESSION['rutbe'] == 0)
    {
        $idler = $_POST['id'];
        foreach($idler as $id)
        {
            $sorgu = $db->prepare("UPDATE karakter_programlari SET durum = 1 WHERE id = ?");
            $sorgu->execute(array($id));
        }
        $_SESSION['yonetim_mesaj'] = 'aktif_basarili';
        header("Location:../".yonetim."/karakter_program_listele.html");
        exit();
    }
}

if(isset($_POST['karakter_program_pasif']))
{
    cVCLmHLxbS_panelislemkontrol("karakter_program_pasif");
    if($_SESSION['rutbe'] == 0)
    {
        $idler = $_POST['id'];
        foreach($idler as $id)
        {
            $sorgu = $db->prepare("UPDATE karakter_programlari SET durum = 0 WHERE id = ?");
            $sorgu->execute(array($id));
        }
        $_SESSION['yonetim_mesaj'] = 'pasif_basarili';
        header("Location:../".yonetim."/karakter_program_listele.html");
        exit();
    }
}

if(isset($_GET['karakterprogramresimsil']))
{
    cVCLmHLxbS_panelislemkontrol("karakterprogramresimsil");
    if($_SESSION['rutbe'] == 0)
    {
        $id = $_GET['sid'];
        $Sorgu = $db->prepare("SELECT * FROM karakter_programlari WHERE id = ?");
        $Sorgu->execute(array($id));
        if($Sorgu->rowCount())
        {
            $Sonuc = $Sorgu->fetch(PDO::FETCH_ASSOC);
            if($Sonuc['resim'])
            {
                if(file_exists(tema.'/uploads/karakter_programlari/'.$Sonuc['resim']))
                {
                    unlink(tema.'/uploads/karakter_programlari/'.$Sonuc['resim']);
                }
                if(file_exists(tema.'/uploads/karakter_programlari/kapak/'.$Sonuc['resim']))
                {
                    unlink(tema.'/uploads/karakter_programlari/kapak/'.$Sonuc['resim']);
                }
                $sorgu = $db->prepare("UPDATE karakter_programlari SET resim = '' WHERE id = ?");
                $sorgu->execute(array($id));
            }
        }
        $_SESSION['yonetim_mesaj'] = 'resim_silme_basarili';
        header("Location:../".yonetim."/karakter_program_ekle/islem/duzenle/id/".$id.".html");
        exit();
    }
}

if(isset($_GET['tablo']) && $_GET['tablo'] == 'karakter_programlari' && isset($_GET['islem']) && $_GET['islem'] == 'sil')
{
    cVCLmHLxbS_panelislemkontrol("karakter_program_sil");
    if($_SESSION['rutbe'] == 0)
    {
        $id = $_GET['id'];
        $sorgu = $db->prepare("DELETE FROM karakter_programlari WHERE id = ?");
        $sorgu->execute(array($id));
        $_SESSION['yonetim_mesaj'] = 'silme_basarili';
        header("Location:../".yonetim."/karakter_program_listele.html");
        exit();
    }
}

## Program İçerik İşlemleri ##

// Program İçerik Ekle
if(isset($_POST['islem']) && $_POST['islem'] == 'program_icerik_ekle')
{
	cVCLmHLxbS_panelislemkontrol("program_icerik_ekle");
	
	if($_SESSION['rutbe'] == 0)
	{
		$program_id = intval($_POST['program_id']);
		$baslik = $_POST['baslik'] ?? '';
		$aciklama = $_POST['aciklama'] ?? '';
		$sira = intval($_POST['sira'] ?? 0);
		$video_url = $_POST['video_url'] ?? '';
		$icerik_tipi = intval($_POST['icerik_tipi'] ?? 0);
		$resim = '';
		$resimler = array();
		$gorsel_desktop = '';
		$gorsel_mobil = '';

		// Görsel yükleme (normal mod)
		if(isset($_FILES['resim']) && !empty($_FILES['resim']['name'][0])) {
			$files = array();
			foreach ($_FILES['resim'] as $k => $l) {
				foreach ($l as $i => $v) {
					if (!array_key_exists($i, $files)) {
						$files[$i] = array();
					}
					$files[$i][$k] = $v;
				}
			}

			$gorsel_format = isset($_POST['gorsel_format']) ? intval($_POST['gorsel_format']) : 0;
			foreach ($files as $file) {
				$upload = new upload($file);
				if ($upload->uploaded)
				{
					$upload->file_auto_rename = true;
					if ($gorsel_format != 2) {
						$upload->image_resize = true;
						$upload->image_ratio_crop = true;
						$upload->image_x = ($gorsel_format == 1) ? 800 : 600;
						$upload->image_y = ($gorsel_format == 1) ? 450 : 600;
					}
					$upload->process("../".tema."/uploads/programlar");
					if ($upload->processed)
					{
						$resimler[] = $upload->file_dst_name;
					}
				}
			}
		}

		// Tek görsel modu - Desktop
		if(isset($_FILES['gorsel_desktop']) && !empty($_FILES['gorsel_desktop']['name'])) {
			$upload = new upload($_FILES['gorsel_desktop']);
			if($upload->uploaded) {
				$upload->file_auto_rename = true;
				$upload->process("../".tema."/uploads/programlar");
				if($upload->processed) {
					$gorsel_desktop = $upload->file_dst_name;
				}
			}
		}

		// Tek görsel modu - Mobil
		if(isset($_FILES['gorsel_mobil']) && !empty($_FILES['gorsel_mobil']['name'])) {
			$upload = new upload($_FILES['gorsel_mobil']);
			if($upload->uploaded) {
				$upload->file_auto_rename = true;
				$upload->process("../".tema."/uploads/programlar");
				if($upload->processed) {
					$gorsel_mobil = $upload->file_dst_name;
				}
			}
		}

		if(!empty($resimler)) {
			$resim = implode(',', $resimler);
		}
		$gorsel_format = isset($_POST['gorsel_format']) ? intval($_POST['gorsel_format']) : 0;
		$sorgu = $db->prepare("INSERT INTO program_icerikleri SET
			program_id = ?, baslik = ?, aciklama = ?, resim = ?, video_url = ?, sira = ?, durum = ?, dil = ?, gorsel_format = ?, icerik_tipi = ?, gorsel_desktop = ?, gorsel_mobil = ?");
		$Ekle = $sorgu->execute([
			$program_id, $baslik, $aciklama, $resim, $video_url, $sira, 1, $_SESSION['admin_dil'], $gorsel_format, $icerik_tipi, $gorsel_desktop, $gorsel_mobil
		]);
		
		if($Ekle) {
			echo json_encode(['success' => true, 'message' => 'İçerik başarıyla eklendi']);
		} else {
			echo json_encode(['success' => false, 'message' => 'İçerik eklenirken hata oluştu']);
		}
		exit();
	}
}

// Program İçerik Güncelle
if(isset($_POST['islem']) && $_POST['islem'] == 'program_icerik_guncelle')
{
	cVCLmHLxbS_panelislemkontrol("program_icerik_guncelle");
	
	if($_SESSION['rutbe'] == 0)
	{
		$icerik_id = intval($_POST['icerik_id']);
		$baslik = $_POST['baslik'] ?? '';
		$aciklama = $_POST['aciklama'] ?? '';
		$sira = intval($_POST['sira'] ?? 0);
		$video_url = $_POST['video_url'] ?? '';
		$icerik_tipi = intval($_POST['icerik_tipi'] ?? 0);

		// Mevcut içeriği kontrol et
		$mevcutSorgu = $db->prepare("SELECT * FROM program_icerikleri WHERE id = ?");
		$mevcutSorgu->execute(array($icerik_id));
		$mevcut = $mevcutSorgu->fetch(PDO::FETCH_ASSOC);

		if(!$mevcut) {
			echo json_encode(['success' => false, 'message' => 'İçerik bulunamadı']);
			exit();
		}

		$resimList = array();
		if(!empty($mevcut['resim'])) {
			$resimList = array_filter(explode(',', $mevcut['resim']));
		}

		$gorsel_format = isset($_POST['gorsel_format']) ? intval($_POST['gorsel_format']) : (isset($mevcut['gorsel_format']) ? intval($mevcut['gorsel_format']) : 0);
		if(isset($_FILES['resim']) && !empty($_FILES['resim']['name'][0])) {
			$files = array();
			foreach ($_FILES['resim'] as $k => $l) {
				foreach ($l as $i => $v) {
					if (!array_key_exists($i, $files)) {
						$files[$i] = array();
					}
					$files[$i][$k] = $v;
				}
			}

			foreach ($files as $file) {
				$upload = new upload($file);
				if ($upload->uploaded)
				{
					$upload->file_auto_rename = true;
					if ($gorsel_format != 2) {
						$upload->image_resize = true;
						$upload->image_ratio_crop = true;
						$upload->image_x = ($gorsel_format == 1) ? 800 : 600;
						$upload->image_y = ($gorsel_format == 1) ? 450 : 600;
					}
					$upload->process("../".tema."/uploads/programlar");
					if ($upload->processed)
					{
						$resimList[] = $upload->file_dst_name;
					}
				}
			}
		}

		$resim = !empty($resimList) ? implode(',', $resimList) : '';

		// Tek görsel modu - Desktop
		$gorsel_desktop = $mevcut['gorsel_desktop'] ?? '';
		if(isset($_FILES['gorsel_desktop']) && !empty($_FILES['gorsel_desktop']['name'])) {
			// Eski dosyayı sil
			if(!empty($gorsel_desktop) && file_exists("../".tema."/uploads/programlar/".$gorsel_desktop)) {
				unlink("../".tema."/uploads/programlar/".$gorsel_desktop);
			}
			$upload = new upload($_FILES['gorsel_desktop']);
			if($upload->uploaded) {
				$upload->file_auto_rename = true;
				$upload->process("../".tema."/uploads/programlar");
				if($upload->processed) {
					$gorsel_desktop = $upload->file_dst_name;
				}
			}
		}

		// Tek görsel modu - Mobil
		$gorsel_mobil = $mevcut['gorsel_mobil'] ?? '';
		if(isset($_FILES['gorsel_mobil']) && !empty($_FILES['gorsel_mobil']['name'])) {
			// Eski dosyayı sil
			if(!empty($gorsel_mobil) && file_exists("../".tema."/uploads/programlar/".$gorsel_mobil)) {
				unlink("../".tema."/uploads/programlar/".$gorsel_mobil);
			}
			$upload = new upload($_FILES['gorsel_mobil']);
			if($upload->uploaded) {
				$upload->file_auto_rename = true;
				$upload->process("../".tema."/uploads/programlar");
				if($upload->processed) {
					$gorsel_mobil = $upload->file_dst_name;
				}
			}
		}

		$sorgu = $db->prepare("UPDATE program_icerikleri SET
			baslik = ?, aciklama = ?, resim = ?, video_url = ?, sira = ?, gorsel_format = ?, icerik_tipi = ?, gorsel_desktop = ?, gorsel_mobil = ? WHERE id = ?");
		$Guncelle = $sorgu->execute([
			$baslik, $aciklama, $resim, $video_url, $sira, $gorsel_format, $icerik_tipi, $gorsel_desktop, $gorsel_mobil, $icerik_id
		]);
		
		if($Guncelle) {
			echo json_encode(['success' => true, 'message' => 'İçerik başarıyla güncellendi']);
		} else {
			echo json_encode(['success' => false, 'message' => 'İçerik güncellenirken hata oluştu']);
		}
		exit();
	}
}

// Program İçerik Sil
if(isset($_POST['islem']) && $_POST['islem'] == 'program_icerik_sil')
{
	cVCLmHLxbS_panelislemkontrol("program_icerik_sil");
	
	if($_SESSION['rutbe'] == 0)
	{
		$icerik_id = intval($_POST['icerik_id']);
		
		// İçeriği kontrol et
		$mevcutSorgu = $db->prepare("SELECT * FROM program_icerikleri WHERE id = ?");
		$mevcutSorgu->execute(array($icerik_id));
		$mevcut = $mevcutSorgu->fetch(PDO::FETCH_ASSOC);
		
		if($mevcut) {
			// Görselleri sil
			if(!empty($mevcut['resim'])) {
				$resimler = array_filter(explode(',', $mevcut['resim']));
				foreach($resimler as $r) {
					if(!empty($r) && file_exists("../".tema."/uploads/programlar/".$r)){
						unlink("../".tema."/uploads/programlar/".$r);
					}
				}
			}
			// Tek görsel dosyalarını sil
			foreach(['gorsel_desktop', 'gorsel_mobil'] as $gAlan) {
				if(!empty($mevcut[$gAlan]) && file_exists("../".tema."/uploads/programlar/".$mevcut[$gAlan])) {
					unlink("../".tema."/uploads/programlar/".$mevcut[$gAlan]);
				}
			}

			$sorgu = $db->prepare("DELETE FROM program_icerikleri WHERE id = ?");
			$Sil = $sorgu->execute(array($icerik_id));
			
			if($Sil) {
				echo json_encode(['success' => true, 'message' => 'İçerik başarıyla silindi']);
			} else {
				echo json_encode(['success' => false, 'message' => 'İçerik silinirken hata oluştu']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => 'İçerik bulunamadı']);
		}
		exit();
	}
}

// Program İçerik Durum Değiştir
if(isset($_POST['islem']) && $_POST['islem'] == 'program_icerik_durum')
{
	cVCLmHLxbS_panelislemkontrol("program_icerik_durum");
	
	if($_SESSION['rutbe'] == 0)
	{
		$icerik_id = intval($_POST['icerik_id']);
		$durum = intval($_POST['durum']);
		
		$sorgu = $db->prepare("UPDATE program_icerikleri SET durum = ? WHERE id = ?");
		$Guncelle = $sorgu->execute(array($durum, $icerik_id));
		
		if($Guncelle) {
			echo json_encode(['success' => true, 'message' => 'Durum başarıyla güncellendi']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Durum güncellenirken hata oluştu']);
		}
		exit();
	}
}

// Program İçerik Görsel Sil
if(isset($_POST['islem']) && $_POST['islem'] == 'program_icerik_resim_sil')
{
	cVCLmHLxbS_panelislemkontrol("program_icerik_resim_sil");
	
	if($_SESSION['rutbe'] == 0)
	{
		$icerik_id = intval($_POST['icerik_id']);
		$resim_ad = trim($_POST['resim_ad'] ?? '');
		
		$mevcutSorgu = $db->prepare("SELECT * FROM program_icerikleri WHERE id = ?");
		$mevcutSorgu->execute(array($icerik_id));
		$mevcut = $mevcutSorgu->fetch(PDO::FETCH_ASSOC);
		
		if($mevcut && !empty($mevcut['resim'])) {
			$resimler = array_filter(explode(',', $mevcut['resim']));
			$yeniResimler = array();
			
			if(!empty($resim_ad)) {
				foreach($resimler as $r) {
					if($r === $resim_ad) {
						if(file_exists("../".tema."/uploads/programlar/".$r)){
							unlink("../".tema."/uploads/programlar/".$r);
						}
					} else {
						$yeniResimler[] = $r;
					}
				}
			} else {
				foreach($resimler as $r) {
					if(!empty($r) && file_exists("../".tema."/uploads/programlar/".$r)){
						unlink("../".tema."/uploads/programlar/".$r);
					}
				}
				$yeniResimler = array();
			}
			
			$yeniResimDegeri = !empty($yeniResimler) ? implode(',', $yeniResimler) : '';
			$sorgu = $db->prepare("UPDATE program_icerikleri SET resim = ? WHERE id = ?");
			$Guncelle = $sorgu->execute(array($yeniResimDegeri, $icerik_id));
			
			if($Guncelle) {
				echo json_encode(['success' => true, 'message' => 'Görsel başarıyla silindi']);
			} else {
				echo json_encode(['success' => false, 'message' => 'Görsel silinirken hata oluştu']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => 'Görsel bulunamadı']);
		}
		exit();
	}
}

// Program İçerik Tek Görsel Sil (desktop/mobil)
if(isset($_POST['islem']) && $_POST['islem'] == 'program_icerik_gorsel_tek_sil')
{
	cVCLmHLxbS_panelislemkontrol("program_icerik_resim_sil");

	if($_SESSION['rutbe'] == 0)
	{
		$icerik_id = intval($_POST['icerik_id']);
		$tip = $_POST['tip'] ?? ''; // 'desktop' veya 'mobil'

		$mevcutSorgu = $db->prepare("SELECT * FROM program_icerikleri WHERE id = ?");
		$mevcutSorgu->execute(array($icerik_id));
		$mevcut = $mevcutSorgu->fetch(PDO::FETCH_ASSOC);

		if($mevcut && in_array($tip, ['desktop', 'mobil'])) {
			$alan = ($tip == 'desktop') ? 'gorsel_desktop' : 'gorsel_mobil';
			$dosya = $mevcut[$alan] ?? '';
			if(!empty($dosya) && file_exists("../".tema."/uploads/programlar/".$dosya)) {
				unlink("../".tema."/uploads/programlar/".$dosya);
			}
			$sorgu = $db->prepare("UPDATE program_icerikleri SET $alan = '' WHERE id = ?");
			$Guncelle = $sorgu->execute(array($icerik_id));
			if($Guncelle) {
				echo json_encode(['success' => true, 'message' => 'Görsel başarıyla silindi']);
			} else {
				echo json_encode(['success' => false, 'message' => 'Görsel silinirken hata oluştu']);
			}
		} else {
			echo json_encode(['success' => false, 'message' => 'Görsel bulunamadı']);
		}
		exit();
	}
}

## Program Derslik Programı İşlemleri ##

// Program Derslik Ekle
if(isset($_POST['islem']) && $_POST['islem'] == 'program_derslik_ekle')
{
	cVCLmHLxbS_panelislemkontrol("program_derslik_ekle");
	
	if($_SESSION['rutbe'] == 0)
	{
		$program_id = intval($_POST['program_id']);
		$sira = intval($_POST['sira'] ?? 0);
		$sehir = $_POST['sehir'] ?? '';
		$kategori_id = !empty($_POST['kategori_id']) ? intval($_POST['kategori_id']) : NULL;
		$sinif = $_POST['sinif'] ?? '';
		$baslik = $_POST['baslik'] ?? '';
		$tarih = $_POST['tarih'] ?? date('Y-m-d');
		$gun = $_POST['gun'] ?? '';
		$saat = $_POST['saat'] ?? '';
		$kontenjan = intval($_POST['kontenjan'] ?? 10);
		$katilimci = intval($_POST['katilimci'] ?? 0);
		$aile_katilim = intval($_POST['aile_katilim'] ?? 0);
		$durum = intval($_POST['durum'] ?? 0);
		$aktif = 1;
		
		$sorgu = $db->prepare("INSERT INTO derslik_durumlari SET 
			program_id = ?, sira = ?, sehir = ?, sinif = ?, kategori_id = ?, baslik = ?, 
			tarih = ?, gun = ?, saat = ?, kontenjan = ?, katilimci = ?, aile_katilim = ?, 
			durum = ?, aktif = ?, dil = ?");
		$Ekle = $sorgu->execute([
			$program_id, $sira, $sehir, $sinif, $kategori_id, $baslik,
			$tarih, $gun, $saat, $kontenjan, $katilimci, $aile_katilim,
			$durum, $aktif, $_SESSION['admin_dil']
		]);
		
		if($Ekle) {
			echo json_encode(['success' => true, 'message' => 'Derslik programı başarıyla eklendi']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Derslik programı eklenirken hata oluştu']);
		}
		exit();
	}
}

// Program Derslik Güncelle
if(isset($_POST['islem']) && $_POST['islem'] == 'program_derslik_guncelle')
{
	cVCLmHLxbS_panelislemkontrol("program_derslik_guncelle");
	
	if($_SESSION['rutbe'] == 0)
	{
		$derslik_id = intval($_POST['derslik_id']);
		$sira = intval($_POST['sira'] ?? 0);
		$sehir = $_POST['sehir'] ?? '';
		$kategori_id = !empty($_POST['kategori_id']) ? intval($_POST['kategori_id']) : NULL;
		$sinif = $_POST['sinif'] ?? '';
		$baslik = $_POST['baslik'] ?? '';
		$tarih = $_POST['tarih'] ?? date('Y-m-d');
		$gun = $_POST['gun'] ?? '';
		$saat = $_POST['saat'] ?? '';
		$kontenjan = intval($_POST['kontenjan'] ?? 10);
		$katilimci = intval($_POST['katilimci'] ?? 0);
		$aile_katilim = intval($_POST['aile_katilim'] ?? 0);
		$durum = intval($_POST['durum'] ?? 0);
		
		// Mevcut dersliği kontrol et
		$mevcutSorgu = $db->prepare("SELECT * FROM derslik_durumlari WHERE id = ?");
		$mevcutSorgu->execute(array($derslik_id));
		$mevcut = $mevcutSorgu->fetch(PDO::FETCH_ASSOC);
		
		if(!$mevcut) {
			echo json_encode(['success' => false, 'message' => 'Derslik programı bulunamadı']);
			exit();
		}
		
		$sorgu = $db->prepare("UPDATE derslik_durumlari SET 
			sira = ?, sehir = ?, sinif = ?, kategori_id = ?, baslik = ?, 
			tarih = ?, gun = ?, saat = ?, kontenjan = ?, katilimci = ?, aile_katilim = ?, 
			durum = ? WHERE id = ?");
		$Guncelle = $sorgu->execute([
			$sira, $sehir, $sinif, $kategori_id, $baslik,
			$tarih, $gun, $saat, $kontenjan, $katilimci, $aile_katilim,
			$durum, $derslik_id
		]);
		
		if($Guncelle) {
			echo json_encode(['success' => true, 'message' => 'Derslik programı başarıyla güncellendi']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Derslik programı güncellenirken hata oluştu']);
		}
		exit();
	}
}

// Program Derslik Sil
if(isset($_POST['islem']) && $_POST['islem'] == 'program_derslik_sil')
{
	cVCLmHLxbS_panelislemkontrol("program_derslik_sil");
	
	if($_SESSION['rutbe'] == 0)
	{
		$derslik_id = intval($_POST['derslik_id']);
		
		$sorgu = $db->prepare("DELETE FROM derslik_durumlari WHERE id = ?");
		$Sil = $sorgu->execute(array($derslik_id));
		
		if($Sil) {
			echo json_encode(['success' => true, 'message' => 'Derslik programı başarıyla silindi']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Derslik programı silinirken hata oluştu']);
		}
		exit();
	}
}

// Program Derslik Aktif/Pasif
if(isset($_POST['islem']) && $_POST['islem'] == 'program_derslik_aktif')
{
	cVCLmHLxbS_panelislemkontrol("program_derslik_aktif");
	
	if($_SESSION['rutbe'] == 0)
	{
		$derslik_id = intval($_POST['derslik_id']);
		$aktif = intval($_POST['aktif']);
		
		$sorgu = $db->prepare("UPDATE derslik_durumlari SET aktif = ? WHERE id = ?");
		$Guncelle = $sorgu->execute(array($aktif, $derslik_id));
		
		if($Guncelle) {
			echo json_encode(['success' => true, 'message' => 'Durum başarıyla güncellendi']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Durum güncellenirken hata oluştu']);
		}
		exit();
	}
}

if(isset($_GET['tablo']) && $_GET['tablo'] == 'programlar' && isset($_GET['islem']) && $_GET['islem'] == 'sil')
{
	cVCLmHLxbS_panelislemkontrol("program_sil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$sorgu = $db->prepare("DELETE FROM programlar WHERE id = ?");
		$sorgu->execute(array($id));
		$_SESSION['yonetim_mesaj'] = 'silme_basarili';
		header("Location:../".yonetim."/program_listele.html");
		exit();
	}
}

## Öğrenme Deneyimleri İşlemleri ##

if(isset($_POST['ogrenme_deneyimi_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("ogrenme_deneyimi_ekle");

	if($_SESSION['rutbe'] == 0)
	{
		$sira           = $_POST['sira'];
		$baslik         = $_POST['baslik'];
		$aciklama       = $_POST['aciklama'];
		$kisa_aciklama  = $_POST['kisa_aciklama'];
		$tam_aciklama   = $_POST['tam_aciklama'];
		$program_id     = isset($_POST['program_id']) ? (int)$_POST['program_id'] : 0;
		// SEO URL otomatik oluşturma
		$seo            = !empty($_POST['seo']) ? $_POST['seo'] : cVCLmHLxbS_seo($baslik);
		$description    = $_POST['description'];
		$keywords       = $_POST['keywords'];
		$durum          = isset($_POST['durum']) ? 1 : 0;
		$tarih          = cVCLmHLxbS_tr_tarih(date('Y-m-d H:i:s'));
		$resim          = '';

		// 🔹 Dosya yükleme işlemi
		$upload_klasor = "../".tema."/uploads/ogrenme_deneyimi";
		if (!file_exists($upload_klasor)) {
			mkdir($upload_klasor, 0777, true);
		}
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process($upload_klasor);
			if ($upload->processed)
			{
				$resim=''.$upload->file_dst_name.'';
			}
		}

		// 🔹 Veritabanı ekleme işlemi
		$sorgu = $db->prepare("INSERT INTO ogrenme_deneyimi SET
			sira = ?, baslik = ?, aciklama = ?, kisa_aciklama = ?, tam_aciklama = ?, program_id = ?, resim = ?,
			seo = ?, description = ?, keywords = ?, durum = ?, dil = ?, tarih = ?");
		$Ekle = $sorgu->execute([
			$sira, $baslik, $aciklama, $kisa_aciklama, $tam_aciklama, $program_id, $resim,
			$seo, $description, $keywords, $durum, $_SESSION['admin_dil'], $tarih
		]);

		if($Ekle) {
			$_SESSION['yonetim_mesaj'] = 'ekleme_basarili';
			header("Location:../".yonetim."/ogrenme_deneyimi_listele.html");
		} else {
			$_SESSION['yonetim_mesaj'] = 'ekleme_basarisiz';
			header("Location:../".yonetim."/ogrenme_deneyimi_ekle.html");
		}
		exit();
	}
}

if(isset($_POST['ogrenme_deneyimi_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("ogrenme_deneyimi_guncelle");

	if($_SESSION['rutbe'] == 0)
	{
		$id             = $_POST['id'];
		$sira           = $_POST['sira'];
		$baslik         = $_POST['baslik'];
		$aciklama       = $_POST['aciklama'];
		$kisa_aciklama  = $_POST['kisa_aciklama'];
		$tam_aciklama   = $_POST['tam_aciklama'];
		$program_id     = isset($_POST['program_id']) ? (int)$_POST['program_id'] : 0;
		// SEO URL otomatik oluşturma
		$seo            = !empty($_POST['seo']) ? $_POST['seo'] : cVCLmHLxbS_seo($baslik);
		$description    = $_POST['description'];
		$keywords       = $_POST['keywords'];
		$durum          = isset($_POST['durum']) ? 1 : 0;
		$resim          = null;

		$upload_klasor = "../".tema."/uploads/ogrenme_deneyimi";
		if (!file_exists($upload_klasor)) {
			mkdir($upload_klasor, 0777, true);
		}
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process($upload_klasor);
			if ($upload->processed)
			{
				$resim=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($resim) && $resim != ''){
			$resim_bul= $db->query("SELECT * FROM ogrenme_deneyimi WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
			if(!empty($resim_bul['resim'])) {
				if(file_exists("../".tema."/uploads/ogrenme_deneyimi/".$resim_bul['resim'])){
				   unlink("../".tema."/uploads/ogrenme_deneyimi/".$resim_bul['resim']);
				}
			}
			$guncelle = $db->prepare("UPDATE ogrenme_deneyimi SET resim = ? WHERE id = ?");
			$guncelle->execute([$resim,$id]);
		}

		// 🔹 Güncelleme sorgusu
		$sorgu = $db->prepare("UPDATE ogrenme_deneyimi SET 
			sira=?, baslik=?, aciklama=?, kisa_aciklama=?, tam_aciklama=?, program_id=?, seo=?, description=?, keywords=?, durum=?
			WHERE id=?");
		$Guncelle = $sorgu->execute([
			$sira, $baslik, $aciklama, $kisa_aciklama, $tam_aciklama, $program_id,
			$seo, $description, $keywords, $durum, $id
		]);

		if($Guncelle) {
			$_SESSION['yonetim_mesaj'] = 'guncelleme_basarili';
			header("Location:../".yonetim."/ogrenme_deneyimi_listele.html");
		} else {
			$_SESSION['yonetim_mesaj'] = 'guncelleme_basarisiz';
			header("Location:../".yonetim."/ogrenme_deneyimi_ekle/islem/duzenle/id/".$id.".html");
		}
		exit();
	}
}

if(isset($_POST['ogrenme_deneyimi_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("ogrenme_deneyimi_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		$idler = $_POST['id'];
		foreach($idler as $id)
		{
			$sorgu = $db->prepare("DELETE FROM ogrenme_deneyimi WHERE id = ?");
			$sorgu->execute(array($id));
		}
		$_SESSION['yonetim_mesaj'] = 'silme_basarili';
		header("Location:../".yonetim."/ogrenme_deneyimi_listele.html");
		exit();
	}
}

if(isset($_POST['ogrenme_deneyimi_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("ogrenme_deneyimi_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		$idler = $_POST['id'];
		foreach($idler as $id)
		{
			$sorgu = $db->prepare("UPDATE ogrenme_deneyimi SET durum = 1 WHERE id = ?");
			$sorgu->execute(array($id));
		}
		$_SESSION['yonetim_mesaj'] = 'aktif_basarili';
		header("Location:../".yonetim."/ogrenme_deneyimi_listele.html");
		exit();
	}
}

if(isset($_POST['ogrenme_deneyimi_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("ogrenme_deneyimi_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		$idler = $_POST['id'];
		foreach($idler as $id)
		{
			$sorgu = $db->prepare("UPDATE ogrenme_deneyimi SET durum = 0 WHERE id = ?");
			$sorgu->execute(array($id));
		}
		$_SESSION['yonetim_mesaj'] = 'pasif_basarili';
		header("Location:../".yonetim."/ogrenme_deneyimi_listele.html");
		exit();
	}
}

if(isset($_GET['ogrenme_deneyimi_resimsil']))
{
	cVCLmHLxbS_panelislemkontrol("ogrenme_deneyimi_resimsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['sid'];
		$Sorgu = $db->prepare("SELECT * FROM ogrenme_deneyimi WHERE id = ?");
		$Sorgu->execute(array($id));
		if($Sorgu->rowCount())
		{
			$Sonuc = $Sorgu->fetch(PDO::FETCH_ASSOC);
			if($Sonuc['resim'])
			{
				if(file_exists("../".tema."/uploads/ogrenme_deneyimi/".$Sonuc['resim']))
				{
					unlink("../".tema."/uploads/ogrenme_deneyimi/".$Sonuc['resim']);
				}
				$sorgu = $db->prepare("UPDATE ogrenme_deneyimi SET resim = '' WHERE id = ?");
				$sorgu->execute(array($id));
			}
		}
		$_SESSION['yonetim_mesaj'] = 'resim_silme_basarili';
		header("Location:../".yonetim."/ogrenme_deneyimi_ekle/islem/duzenle/id/".$id.".html");
		exit();
	}
}

if(isset($_GET['tablo']) && $_GET['tablo'] == 'ogrenme_deneyimi' && isset($_GET['islem']) && $_GET['islem'] == 'sil')
{
	cVCLmHLxbS_panelislemkontrol("ogrenme_deneyimi_sil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$sorgu = $db->prepare("DELETE FROM ogrenme_deneyimi WHERE id = ?");
		$sorgu->execute(array($id));
		$_SESSION['yonetim_mesaj'] = 'silme_basarili';
		header("Location:../".yonetim."/ogrenme_deneyimi_listele.html");
		exit();
	}
}

// Sayfa Yönetimi İşlemi
if(isset($_POST['islem']) && $_POST['islem'] == 'ogrenme_deneyimi_sayfa_yonetimi')
{
	cVCLmHLxbS_panelislemkontrol("ogrenme_deneyimi_sayfa_yonetimi");
	header('Content-Type: application/json; charset=utf-8');
	
	if($_SESSION['rutbe'] == 0)
	{
		$ogrenme_deneyimi_id = (int)$_POST['ogrenme_deneyimi_id'];
		$banner_baslik = strip_tags($_POST['banner_baslik'] ?? '');
		$sayfa_baslik_1 = nl2br(strip_tags($_POST['sayfa_baslik_1'] ?? '', "<b><p><i>"));
		$sayfa_aciklama_1 = nl2br(strip_tags($_POST['sayfa_aciklama_1'] ?? '', "<b><p><i>"));
		$sayfa_baslik_2 = nl2br(strip_tags($_POST['sayfa_baslik_2'] ?? '', "<b><p><i>"));
		$sayfa_aciklama_2 = nl2br(strip_tags($_POST['sayfa_aciklama_2'] ?? '', "<b><p><i>"));
		
		// Önce settings tablosunda kayıt var mı kontrol et
		$settingsCheck = $db->prepare("SELECT * FROM ogrenme_deneyimi_settings WHERE id = 1");
		$settingsCheck->execute();
		$settingsRowCount = $settingsCheck->rowCount();
		$mevcutSettings = $settingsRowCount > 0 ? $settingsCheck->fetch(PDO::FETCH_ASSOC) : null;
		$banner_resim = $mevcutSettings['banner_resim'] ?? '';
		
		// Banner resmi yükleme işlemi
		$upload_klasor = "../".tema."/uploads/ogrenme_deneyimi";
		if (!file_exists($upload_klasor)) {
			mkdir($upload_klasor, 0777, true);
		}
		
		if(isset($_FILES['banner_resim']) && $_FILES['banner_resim']['error'] == 0) {
			$upload_banner = new upload($_FILES['banner_resim']);
			if ($upload_banner->uploaded)
			{
				// Eğer yeni banner resmi yüklendiyse eski resmi sil
				if(!empty($banner_resim) && file_exists($upload_klasor."/".$banner_resim)){
					@unlink($upload_klasor."/".$banner_resim);
				}
				
				$upload_banner->file_auto_rename = true;
				$upload_banner->process($upload_klasor);
				if ($upload_banner->processed)
				{
					$banner_resim = $upload_banner->file_dst_name;
				}
			}
		}
		
		if($settingsRowCount > 0) {
			$sorgu = $db->prepare("UPDATE ogrenme_deneyimi_settings SET 
				banner_baslik = ?, banner_resim = ?, sayfa_baslik_1 = ?, sayfa_aciklama_1 = ?, sayfa_baslik_2 = ?, sayfa_aciklama_2 = ?
				WHERE id = 1");
			$Guncelle = $sorgu->execute([
				$banner_baslik, $banner_resim, $sayfa_baslik_1, $sayfa_aciklama_1, $sayfa_baslik_2, $sayfa_aciklama_2
			]);
		} else {
			$sorgu = $db->prepare("INSERT INTO ogrenme_deneyimi_settings SET 
				id = 1,
				banner_baslik = ?, banner_resim = ?, sayfa_baslik_1 = ?, sayfa_aciklama_1 = ?, sayfa_baslik_2 = ?, sayfa_aciklama_2 = ?");
			$Guncelle = $sorgu->execute([
				$banner_baslik, $banner_resim, $sayfa_baslik_1, $sayfa_aciklama_1, $sayfa_baslik_2, $sayfa_aciklama_2
			]);
		}
		
		if($Guncelle) {
			echo json_encode([
				'success' => true,
				'message' => 'Sayfa yönetimi başarıyla güncellendi.'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Bir hata oluştu.'
			]);
		}
	} else {
		echo json_encode([
			'success' => false,
			'message' => 'Yetkiniz yok.'
		]);
	}
	exit;
}

// Banner Resmi Silme İşlemi
if(isset($_GET['ogrenme_deneyimi_banner_resim_sil']))
{
	cVCLmHLxbS_panelislemkontrol("ogrenme_deneyimi_sayfa_yonetimi");
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul = $db->query("SELECT * FROM ogrenme_deneyimi_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
		if(!empty($resim_bul['banner_resim'])){
			$upload_klasor = "../".tema."/uploads/ogrenme_deneyimi";
			if(file_exists($upload_klasor."/".$resim_bul['banner_resim'])){
				@unlink($upload_klasor."/".$resim_bul['banner_resim']);
			}
			
			$sorgu = $db->prepare("UPDATE ogrenme_deneyimi_settings SET banner_resim = '' WHERE id = 1");
			if($sorgu->execute())
			{
				$_SESSION['ogrenme_deneyimi_banner_resim_sil'] = 'yes';
				header("Location:../".yonetim."/ogrenme_deneyimi_listele.html");
			}
			else
			{
				$_SESSION['ogrenme_deneyimi_banner_resim_sil'] = 'no';
				header("Location:../".yonetim."/ogrenme_deneyimi_listele.html");
			}
		}
		else
		{
			$_SESSION['ogrenme_deneyimi_banner_resim_sil'] = 'no';
			header("Location:../".yonetim."/ogrenme_deneyimi_listele.html");
		}
		exit();
	}
}

## Destekleme Yolları İşlemleri ##

if(isset($_POST['destekleme_yollari_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("destekleme_yollari_ekle");

	if($_SESSION['rutbe'] == 0)
	{
		$sira           = $_POST['sira'];
		$baslik         = $_POST['baslik'];
		$aciklama       = $_POST['aciklama'];
		$kisa_aciklama  = $_POST['kisa_aciklama'];
		$tam_aciklama   = $_POST['tam_aciklama'];
		$ikon           = isset($_POST['ikon']) ? $_POST['ikon'] : '';
		// SEO URL otomatik oluşturma
		$seo            = !empty($_POST['seo']) ? $_POST['seo'] : cVCLmHLxbS_seo($baslik);
		$description    = $_POST['description'];
		$keywords       = $_POST['keywords'];
		$durum          = isset($_POST['durum']) ? 1 : 0;
		$tarih          = cVCLmHLxbS_tr_tarih(date('Y-m-d H:i:s'));
		$resim          = '';

		// 🔹 Dosya yükleme işlemi
		$upload_klasor = "../".tema."/uploads/destekleme_yollari";
		if (!file_exists($upload_klasor)) {
			mkdir($upload_klasor, 0777, true);
		}
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process($upload_klasor);
			if ($upload->processed)
			{
				$resim=''.$upload->file_dst_name.'';
			}
		}

		// 🔹 Veritabanı ekleme işlemi
		$sorgu = $db->prepare("INSERT INTO destekleme_yollari SET
			sira = ?, baslik = ?, aciklama = ?, kisa_aciklama = ?, tam_aciklama = ?, ikon = ?, resim = ?,
			seo = ?, description = ?, keywords = ?, durum = ?, dil = ?, tarih = ?");
		$Ekle = $sorgu->execute([
			$sira, $baslik, $aciklama, $kisa_aciklama, $tam_aciklama, $ikon, $resim,
			$seo, $description, $keywords, $durum, $_SESSION['admin_dil'], $tarih
		]);

		if($Ekle) {
			$_SESSION['yonetim_mesaj'] = 'ekleme_basarili';
			header("Location:../".yonetim."/destekleme_yollari_listele.html");
		} else {
			$_SESSION['yonetim_mesaj'] = 'ekleme_basarisiz';
			header("Location:../".yonetim."/destekleme_yollari_ekle.html");
		}
		exit();
	}
}

if(isset($_POST['destekleme_yollari_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("destekleme_yollari_guncelle");

	if($_SESSION['rutbe'] == 0)
	{
		$id             = $_POST['id'];
		$sira           = $_POST['sira'];
		$baslik         = $_POST['baslik'];
		$aciklama       = $_POST['aciklama'];
		$kisa_aciklama  = $_POST['kisa_aciklama'];
		$tam_aciklama   = $_POST['tam_aciklama'];
		$ikon           = isset($_POST['ikon']) ? $_POST['ikon'] : '';
		// SEO URL otomatik oluşturma
		$seo            = !empty($_POST['seo']) ? $_POST['seo'] : cVCLmHLxbS_seo($baslik);
		$description    = $_POST['description'];
		$keywords       = $_POST['keywords'];
		$durum          = isset($_POST['durum']) ? 1 : 0;
		$resim          = null;

		$upload_klasor = "../".tema."/uploads/destekleme_yollari";
		if (!file_exists($upload_klasor)) {
			mkdir($upload_klasor, 0777, true);
		}
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process($upload_klasor);
			if ($upload->processed)
			{
				$resim=''.$upload->file_dst_name.'';
			}
		}
		
		if(isset($resim) && $resim != ''){
			$resim_bul= $db->query("SELECT * FROM destekleme_yollari WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
			if(!empty($resim_bul['resim'])) {
				if(file_exists("../".tema."/uploads/destekleme_yollari/".$resim_bul['resim'])){
				   unlink("../".tema."/uploads/destekleme_yollari/".$resim_bul['resim']);
				}
			}
			$guncelle = $db->prepare("UPDATE destekleme_yollari SET resim = ? WHERE id = ?");
			$guncelle->execute([$resim,$id]);
		}

		// 🔹 Güncelleme sorgusu
		$sorgu = $db->prepare("UPDATE destekleme_yollari SET 
			sira=?, baslik=?, aciklama=?, kisa_aciklama=?, tam_aciklama=?, ikon=?, seo=?, description=?, keywords=?, durum=?
			WHERE id=?");
		$Guncelle = $sorgu->execute([
			$sira, $baslik, $aciklama, $kisa_aciklama, $tam_aciklama, $ikon,
			$seo, $description, $keywords, $durum, $id
		]);

		if($Guncelle) {
			$_SESSION['yonetim_mesaj'] = 'guncelleme_basarili';
			header("Location:../".yonetim."/destekleme_yollari_listele.html");
		} else {
			$_SESSION['yonetim_mesaj'] = 'guncelleme_basarisiz';
			header("Location:../".yonetim."/destekleme_yollari_ekle/islem/duzenle/id/".$id.".html");
		}
		exit();
	}
}

if(isset($_POST['destekleme_yollari_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("destekleme_yollari_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		$idler = $_POST['id'];
		foreach($idler as $id)
		{
			$sorgu = $db->prepare("DELETE FROM destekleme_yollari WHERE id = ?");
			$sorgu->execute(array($id));
		}
		$_SESSION['yonetim_mesaj'] = 'silme_basarili';
		header("Location:../".yonetim."/destekleme_yollari_listele.html");
		exit();
	}
}

if(isset($_POST['destekleme_yollari_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("destekleme_yollari_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		$idler = $_POST['id'];
		foreach($idler as $id)
		{
			$sorgu = $db->prepare("UPDATE destekleme_yollari SET durum = 1 WHERE id = ?");
			$sorgu->execute(array($id));
		}
		$_SESSION['yonetim_mesaj'] = 'aktif_basarili';
		header("Location:../".yonetim."/destekleme_yollari_listele.html");
		exit();
	}
}

if(isset($_POST['destekleme_yollari_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("destekleme_yollari_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		$idler = $_POST['id'];
		foreach($idler as $id)
		{
			$sorgu = $db->prepare("UPDATE destekleme_yollari SET durum = 0 WHERE id = ?");
			$sorgu->execute(array($id));
		}
		$_SESSION['yonetim_mesaj'] = 'pasif_basarili';
		header("Location:../".yonetim."/destekleme_yollari_listele.html");
		exit();
	}
}

if(isset($_GET['destekleme_yollari_resimsil']))
{
	cVCLmHLxbS_panelislemkontrol("destekleme_yollari_resimsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['sid'];
		$Sorgu = $db->prepare("SELECT * FROM destekleme_yollari WHERE id = ?");
		$Sorgu->execute(array($id));
		if($Sorgu->rowCount())
		{
			$Sonuc = $Sorgu->fetch(PDO::FETCH_ASSOC);
			if($Sonuc['resim'])
			{
				if(file_exists("../".tema."/uploads/destekleme_yollari/".$Sonuc['resim']))
				{
					unlink("../".tema."/uploads/destekleme_yollari/".$Sonuc['resim']);
				}
				$sorgu = $db->prepare("UPDATE destekleme_yollari SET resim = '' WHERE id = ?");
				$sorgu->execute(array($id));
			}
		}
		$_SESSION['yonetim_mesaj'] = 'resim_silme_basarili';
		header("Location:../".yonetim."/destekleme_yollari_ekle/islem/duzenle/id/".$id.".html");
		exit();
	}
}

if(isset($_GET['tablo']) && $_GET['tablo'] == 'destekleme_yollari' && isset($_GET['islem']) && $_GET['islem'] == 'sil')
{
	cVCLmHLxbS_panelislemkontrol("destekleme_yollari_sil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$sorgu = $db->prepare("DELETE FROM destekleme_yollari WHERE id = ?");
		$sorgu->execute(array($id));
		$_SESSION['yonetim_mesaj'] = 'silme_basarili';
		header("Location:../".yonetim."/destekleme_yollari_listele.html");
		exit();
	}
}

// Sayfa Yönetimi İşlemi
if(isset($_POST['islem']) && $_POST['islem'] == 'destekleme_yollari_sayfa_yonetimi')
{
	cVCLmHLxbS_panelislemkontrol("destekleme_yollari_sayfa_yonetimi");
	header('Content-Type: application/json; charset=utf-8');
	
	if($_SESSION['rutbe'] == 0)
	{
		$destekleme_yollari_id = (int)$_POST['destekleme_yollari_id'];
		$banner_baslik = strip_tags($_POST['banner_baslik'] ?? '');
		$banner_aciklama = strip_tags($_POST['banner_aciklama'] ?? '');
		$sayfa_baslik_1 = nl2br(strip_tags($_POST['sayfa_baslik_1'] ?? '', "<b><p><i>"));
		$sayfa_aciklama_1 = nl2br(strip_tags($_POST['sayfa_aciklama_1'] ?? '', "<b><p><i>"));
		
		// Önce settings tablosunda kayıt var mı kontrol et
		$settingsCheck = $db->prepare("SELECT * FROM destekleme_yollari_settings WHERE id = 1");
		$settingsCheck->execute();
		$settingsRowCount = $settingsCheck->rowCount();
		$mevcutSettings = $settingsRowCount > 0 ? $settingsCheck->fetch(PDO::FETCH_ASSOC) : null;
		$banner_resim = $mevcutSettings['banner_resim'] ?? '';
		
		// Banner resmi yükleme işlemi
		$upload_klasor = "../".tema."/uploads/destekleme_yollari";
		if (!file_exists($upload_klasor)) {
			mkdir($upload_klasor, 0777, true);
		}
		
		if(isset($_FILES['banner_resim']) && $_FILES['banner_resim']['error'] == 0) {
			$upload_banner = new upload($_FILES['banner_resim']);
			if ($upload_banner->uploaded)
			{
				// Eğer yeni banner resmi yüklendiyse eski resmi sil
				if(!empty($banner_resim) && file_exists($upload_klasor."/".$banner_resim)){
					@unlink($upload_klasor."/".$banner_resim);
				}
				
				$upload_banner->file_auto_rename = true;
				$upload_banner->process($upload_klasor);
				if ($upload_banner->processed)
				{
					$banner_resim = $upload_banner->file_dst_name;
				}
			}
		}
		
		if($settingsRowCount > 0) {
			$sorgu = $db->prepare("UPDATE destekleme_yollari_settings SET 
				banner_baslik = ?, banner_aciklama = ?, banner_resim = ?, sayfa_baslik_1 = ?, sayfa_aciklama_1 = ?
				WHERE id = 1");
			$Guncelle = $sorgu->execute([
				$banner_baslik, $banner_aciklama, $banner_resim, $sayfa_baslik_1, $sayfa_aciklama_1
			]);
		} else {
			$sorgu = $db->prepare("INSERT INTO destekleme_yollari_settings SET 
				id = 1,
				banner_baslik = ?, banner_aciklama = ?, banner_resim = ?, sayfa_baslik_1 = ?, sayfa_aciklama_1 = ?");
			$Guncelle = $sorgu->execute([
				$banner_baslik, $banner_aciklama, $banner_resim, $sayfa_baslik_1, $sayfa_aciklama_1
			]);
		}
		
		if($Guncelle) {
			echo json_encode([
				'success' => true,
				'message' => 'Sayfa yönetimi başarıyla güncellendi.'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Bir hata oluştu.'
			]);
		}
	} else {
		echo json_encode([
			'success' => false,
			'message' => 'Yetkiniz yok.'
		]);
	}
	exit;
}

// Banner Resmi Silme İşlemi
if(isset($_GET['destekleme_yollari_banner_resim_sil']))
{
	cVCLmHLxbS_panelislemkontrol("destekleme_yollari_sayfa_yonetimi");
	if($_SESSION['rutbe'] == 0)
	{
		$resim_bul = $db->query("SELECT * FROM destekleme_yollari_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
		if(!empty($resim_bul['banner_resim'])){
			$upload_klasor = "../".tema."/uploads/destekleme_yollari";
			if(file_exists($upload_klasor."/".$resim_bul['banner_resim'])){
				@unlink($upload_klasor."/".$resim_bul['banner_resim']);
			}
			
			$sorgu = $db->prepare("UPDATE destekleme_yollari_settings SET banner_resim = '' WHERE id = 1");
			if($sorgu->execute())
			{
				$_SESSION['destekleme_yollari_banner_resim_sil'] = 'yes';
				header("Location:../".yonetim."/destekleme_yollari_listele.html");
			}
			else
			{
				$_SESSION['destekleme_yollari_banner_resim_sil'] = 'no';
				header("Location:../".yonetim."/destekleme_yollari_listele.html");
			}
		}
		else
		{
			$_SESSION['destekleme_yollari_banner_resim_sil'] = 'no';
			header("Location:../".yonetim."/destekleme_yollari_listele.html");
		}
		exit();
	}
}

// ============================================
// BAĞIŞ MODÜLÜ İŞLEMLERİ - BAŞLANGIÇ
// ============================================

// Bağış Modülü Ekleme
if (isset($_POST['bagismodul_ekle']))
{
	$seo = cVCLmHLxbS_seo($_POST['adi']);
	
	$kapak_resmi = '';
	$banner_resmi = '';
	
	if($_FILES['kapak_resmi']['name'] != '')
	{
		$handle = new upload($_FILES['kapak_resmi']);
		if ($handle->uploaded)
		{
			$handle->file_new_name_body = $seo;
			$handle->image_resize = true;
			$handle->image_x = 400;
			$handle->image_y = 300;
			$handle->image_ratio = true;
			$handle->jpeg_quality = 100;
			$handle->process('../tema/'.tema_dir.'/uploads/bagis_moduller/');
			if ($handle->processed)
			{
				$kapak_resmi = $handle->file_dst_name;
				$handle->clean();
			}
		}
	}
	
	if($_FILES['banner_resmi']['name'] != '')
	{
		$handle = new upload($_FILES['banner_resmi']);
		if ($handle->uploaded)
		{
			$handle->file_new_name_body = $seo.'_banner';
			$handle->image_resize = true;
			$handle->image_x = 1920;
			$handle->image_y = 400;
			$handle->image_ratio = true;
			$handle->jpeg_quality = 100;
			$handle->process('../tema/'.tema_dir.'/uploads/bagis_moduller/');
			if ($handle->processed)
			{
				$banner_resmi = $handle->file_dst_name;
				$handle->clean();
			}
		}
	}
	
	$sorgu = $db->prepare("INSERT INTO bagis_moduller SET
		dil = ?,
		adi = ?,
		seo = ?,
		aciklama = ?,
		hedef_tutar = ?,
		toplanan_tutar = ?,
		kapak_resmi = ?,
		banner_resmi = ?,
		baslangic_tarihi = ?,
		bitis_tarihi = ?,
		keywords = ?,
		description = ?,
		durum = ?,
		anasayfada_goster = ?,
		istatistik_durum = ?,
		icon_class = ?,
		tekil_sayfa = ?,
		sira = ?
	");
	
	$ekle = $sorgu->execute(array(
		$_SESSION['admin_dil'],
		$_POST['adi'],
		$seo,
		$_POST['aciklama'],
		$_POST['hedef_tutar'],
		0,
		$kapak_resmi,
		$banner_resmi,
		$_POST['baslangic_tarihi'] ? $_POST['baslangic_tarihi'] : NULL,
		$_POST['bitis_tarihi'] ? $_POST['bitis_tarihi'] : NULL,
		$_POST['keywords'],
		$_POST['description'],
		isset($_POST['durum']) ? 1 : 0,
		isset($_POST['anasayfada_goster']) ? 1 : 0,
		isset($_POST['istatistik_durum']) ? 1 : 0,
		// Icon class field
		isset($_POST['icon_class']) ? $_POST['icon_class'] : NULL,
		// Always set tekil_sayfa to 1 for donation modules
		1,
		$_POST['sira']
	));
	
	if ($ekle)
	{
		$_SESSION['bagismodul_ekle'] = 'yes';
		header("Location:../".yonetim."/bagis-moduller.html");
		exit();
	}
	else
	{
		$_SESSION['bagismodul_ekle'] = 'no';
		header("Location:../".yonetim."/bagis-modul-ekle.html");
		exit();
	}
}

// Bağış Modülü Güncelleme
if (isset($_POST['bagismodul_guncelle']))
{
	$seo = cVCLmHLxbS_seo($_POST['adi']);
	$id = $_POST['id'];
	
	$mevcut = $db->prepare("SELECT * FROM bagis_moduller WHERE id = ?");
	$mevcut->execute(array($id));
	$mevcut_data = $mevcut->fetch(PDO::FETCH_ASSOC);
	
	$kapak_resmi = $mevcut_data['kapak_resmi'];
	$banner_resmi = $mevcut_data['banner_resmi'];
	
	if($_FILES['kapak_resmi']['name'] != '')
	{
		if($mevcut_data['kapak_resmi'] != '' && file_exists('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$mevcut_data['kapak_resmi']))
		{
			unlink('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$mevcut_data['kapak_resmi']);
		}
		
		$handle = new upload($_FILES['kapak_resmi']);
		if ($handle->uploaded)
		{
			$handle->file_new_name_body = $seo;
			$handle->image_resize = true;
			$handle->image_x = 400;
			$handle->image_y = 300;
			$handle->image_ratio = true;
			$handle->jpeg_quality = 100;
			$handle->process('../tema/'.tema_dir.'/uploads/bagis_moduller/');
			if ($handle->processed)
			{
				$kapak_resmi = $handle->file_dst_name;
				$handle->clean();
			}
		}
	}
	
	if($_FILES['banner_resmi']['name'] != '')
	{
		if($mevcut_data['banner_resmi'] != '' && file_exists('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$mevcut_data['banner_resmi']))
		{
			unlink('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$mevcut_data['banner_resmi']);
		}
		
		$handle = new upload($_FILES['banner_resmi']);
		if ($handle->uploaded)
		{
			$handle->file_new_name_body = $seo.'_banner';
			$handle->image_resize = true;
			$handle->image_x = 1920;
			$handle->image_y = 400;
			$handle->image_ratio = true;
			$handle->jpeg_quality = 100;
			$handle->process('../tema/'.tema_dir.'/uploads/bagis_moduller/');
			if ($handle->processed)
			{
				$banner_resmi = $handle->file_dst_name;
				$handle->clean();
			}
		}
	}
	
	$sorgu = $db->prepare("UPDATE bagis_moduller SET
		adi = ?,
		seo = ?,
		aciklama = ?,
		hedef_tutar = ?,
		kapak_resmi = ?,
		banner_resmi = ?,
		baslangic_tarihi = ?,
		bitis_tarihi = ?,
		keywords = ?,
		description = ?,
		durum = ?,
		anasayfada_goster = ?,
		istatistik_durum = ?,
		icon_class = ?,
		tekil_sayfa = ?,
		sira = ?
		WHERE id = ?
	");
	
	$guncelle = $sorgu->execute(array(
		$_POST['adi'],
		$seo,
		$_POST['aciklama'],
		$_POST['hedef_tutar'],
		$kapak_resmi,
		$banner_resmi,
		$_POST['baslangic_tarihi'] ? $_POST['baslangic_tarihi'] : NULL,
		$_POST['bitis_tarihi'] ? $_POST['bitis_tarihi'] : NULL,
		$_POST['keywords'],
		$_POST['description'],
		isset($_POST['durum']) ? 1 : 0,
		isset($_POST['anasayfada_goster']) ? 1 : 0,
		isset($_POST['istatistik_durum']) ? 1 : 0,
		// Icon class field
		isset($_POST['icon_class']) ? $_POST['icon_class'] : NULL,
		// Always set tekil_sayfa to 1 for donation modules
		1,
		$_POST['sira'],
		$id
	));
	
	if ($guncelle)
	{
		$_SESSION['bagismodul_guncelle'] = 'yes';
		header("Location:../".yonetim."/bagis-modul-duzenle/".$id.".html");
		exit();
	}
	else
	{
		$_SESSION['bagismodul_guncelle'] = 'no';
		header("Location:../".yonetim."/bagis-modul-duzenle/".$id.".html");
		exit();
	}
}

// Bağış Modülü Silme
if (isset($_GET['bagismodulsil']))
{
	$id = $_GET['id'];
	
	$sorgu = $db->prepare("SELECT * FROM bagis_moduller WHERE id = ?");
	$sorgu->execute(array($id));
	$sonuc = $sorgu->fetch(PDO::FETCH_ASSOC);
	
	if($sonuc['kapak_resmi'] != '' && file_exists('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$sonuc['kapak_resmi']))
	{
		unlink('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$sonuc['kapak_resmi']);
	}
	
	if($sonuc['banner_resmi'] != '' && file_exists('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$sonuc['banner_resmi']))
	{
		unlink('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$sonuc['banner_resmi']);
	}
	
	$sorgu = $db->prepare("DELETE FROM bagis_moduller WHERE id = ?");
	$sil = $sorgu->execute(array($id));
	
	if ($sil)
	{
		$_SESSION['bagismodulsil'] = 'yes';
		header("Location:../".yonetim."/bagis-moduller.html");
		exit();
	}
	else
	{
		$_SESSION['bagismodulsil'] = 'no';
		header("Location:../".yonetim."/bagis-moduller.html");
		exit();
	}
}

// Bağış Modülü Toplu Aktif
if (isset($_POST['bagismodul_aktif']))
{
	if(!empty($_POST['id']))
	{
		foreach ($_POST['id'] as $id)
		{
			$sorgu = $db->prepare("UPDATE bagis_moduller SET durum = ? WHERE id = ?");
			$sorgu->execute(array(1, $id));
		}
		$_SESSION['bagismodul_aktif'] = 'yes';
		header("Location:../".yonetim."/bagis-moduller.html");
		exit();
	}
	else
	{
		$_SESSION['secim'] = 'secimyok';
		header("Location:../".yonetim."/bagis-moduller.html");
		exit();
	}
}

// Bağış Modülü Toplu Pasif
if (isset($_POST['bagismodul_pasif']))
{
	if(!empty($_POST['id']))
	{
		foreach ($_POST['id'] as $id)
		{
			$sorgu = $db->prepare("UPDATE bagis_moduller SET durum = ? WHERE id = ?");
			$sorgu->execute(array(0, $id));
		}
		$_SESSION['bagismodul_pasif'] = 'yes';
		header("Location:../".yonetim."/bagis-moduller.html");
		exit();
	}
	else
	{
		$_SESSION['secim'] = 'secimyok';
		header("Location:../".yonetim."/bagis-moduller.html");
		exit();
	}
}

// Bağış Modülü Toplu Silme
if (isset($_POST['bagismodul_tumu']))
{
	if(!empty($_POST['id']))
	{
		foreach ($_POST['id'] as $id)
		{
			$sorgu = $db->prepare("SELECT * FROM bagis_moduller WHERE id = ?");
			$sorgu->execute(array($id));
			$sonuc = $sorgu->fetch(PDO::FETCH_ASSOC);
			
			if($sonuc['kapak_resmi'] != '' && file_exists('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$sonuc['kapak_resmi']))
			{
				unlink('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$sonuc['kapak_resmi']);
			}
			
			if($sonuc['banner_resmi'] != '' && file_exists('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$sonuc['banner_resmi']))
			{
				unlink('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$sonuc['banner_resmi']);
			}
			
			$sorgu = $db->prepare("DELETE FROM bagis_moduller WHERE id = ?");
			$sorgu->execute(array($id));
		}
		$_SESSION['bagismodul_tumu'] = 'yes';
		header("Location:../".yonetim."/bagis-moduller.html");
		exit();
	}
	else
	{
		$_SESSION['secim'] = 'secimyok';
		header("Location:../".yonetim."/bagis-moduller.html");
		exit();
	}
}

// Bağış Modülü Sıralama
if(isset($_GET['bagismodulsiralama']))
{
	foreach ($_POST['item'] as $key => $value)
	{
		$sorgu = $db->prepare("UPDATE bagis_moduller SET sira = ? WHERE id = ?");
		$sorgu->execute(array($key, $value));
	}
	
	$json = array('islemMsj' => 'Güncellendi');
	header('Content-type: application/json; charset=utf-8');
	echo json_encode($json);
	exit();
}

// Bağış Modülü Kapak Silme
if(isset($_GET['bagismodulkapaksil']))
{
	$id = $_GET['sid'];
	
	$sorgu = $db->prepare("SELECT kapak_resmi FROM bagis_moduller WHERE id = ?");
	$sorgu->execute(array($id));
	$sonuc = $sorgu->fetch(PDO::FETCH_ASSOC);
	
	if($sonuc['kapak_resmi'] != '' && file_exists('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$sonuc['kapak_resmi']))
	{
		unlink('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$sonuc['kapak_resmi']);
	}
	
	$sorgu = $db->prepare("UPDATE bagis_moduller SET kapak_resmi = ? WHERE id = ?");
	$sil = $sorgu->execute(array('', $id));
	
	if ($sil)
	{
		$_SESSION['bagismodulkapaksil'] = 'yes';
		header("Location:../".yonetim."/bagis-modul-duzenle/".$id.".html");
		exit();
	}
	else
	{
		$_SESSION['bagismodulkapaksil'] = 'no';
		header("Location:../".yonetim."/bagis-modul-duzenle/".$id.".html");
		exit();
	}
}

// Bağış Modülü Banner Silme
if(isset($_GET['bagismodulbannersil']))
{
	$id = $_GET['sid'];
	
	$sorgu = $db->prepare("SELECT banner_resmi FROM bagis_moduller WHERE id = ?");
	$sorgu->execute(array($id));
	$sonuc = $sorgu->fetch(PDO::FETCH_ASSOC);
	
	if($sonuc['banner_resmi'] != '' && file_exists('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$sonuc['banner_resmi']))
	{
		unlink('../tema/'.tema_dir.'/uploads/bagis_moduller/'.$sonuc['banner_resmi']);
	}
	
	$sorgu = $db->prepare("UPDATE bagis_moduller SET banner_resmi = ? WHERE id = ?");
	$sil = $sorgu->execute(array('', $id));
	
	if ($sil)
	{
		$_SESSION['bagismodulbannersil'] = 'yes';
		header("Location:../".yonetim."/bagis-modul-duzenle/".$id.".html");
		exit();
	}
	else
	{
		$_SESSION['bagismodulbannersil'] = 'no';
		header("Location:../".yonetim."/bagis-modul-duzenle/".$id.".html");
		exit();
	}
}


// AJAX: Canlı güncelleme
if (isset($_POST['update_contact_settings_live'])) {
    $page = trim($_POST['page_name']);
    $show = isset($_POST['show_contact']) ? (int)$_POST['show_contact'] : 0;

    $check = $db->prepare("SELECT id FROM contact_section_settings WHERE page_name = ? LIMIT 1");
    $check->execute([$page]);

    if ($check->rowCount() > 0) {
        $update = $db->prepare("UPDATE contact_section_settings SET show_contact_section = ? WHERE page_name = ?");
        $update->execute([$show, $page]);
    } else {
        $insert = $db->prepare("INSERT INTO contact_section_settings (page_name, show_contact_section) VALUES (?, ?)");
        $insert->execute([$page, $show]);
    }

    echo "ok";
    exit;
}

// AJAX: Silme
if (isset($_POST['delete_contact_setting'])) {
    $page = trim($_POST['page_name']);
    $delete = $db->prepare("DELETE FROM contact_section_settings WHERE page_name = ?");
    $delete->execute([$page]);
    echo "deleted";
    exit;
}


// ============================================
// BAĞIŞ MODÜLÜ İŞLEMLERİ - BİTİŞ
// ============================================

## Derslik Durumları İşlemleri ##

// For derslik_durum_ekle
if(isset($_POST['derslik_durum_ekle'])) {
    cVCLmHLxbS_panelislemkontrol("derslik_durum_ekle");
    if($_SESSION['rutbe'] == 0) {
        $sira = $_POST['sira'];
        $sehir = $_POST['sehir'];
        $sinif = $_POST['sinif'];
        $kategori_id = isset($_POST['kategori_id']) && !empty($_POST['kategori_id']) ? intval($_POST['kategori_id']) : NULL;
        $program_id = isset($_POST['program_id']) && !empty($_POST['program_id']) ? intval($_POST['program_id']) : NULL;
        $gun = $_POST['gun'];
        $saat = $_POST['saat'];
        $tarih = $_POST['tarih'];
        $katilimci = intval($_POST['katilimci']);
        $kontenjan = intval($_POST['kontenjan']);
        $aile_katilim = intval($_POST['aile_katilim']);
        $baslik = $_POST['baslik'];
        $durum = $_POST['durum'];
        $aktif = isset($_POST['aktif']) ? 1 : 0;
        
        $sorgu = $db->prepare("INSERT INTO derslik_durumlari SET
            sira = ?,
            sehir = ?,
            sinif = ?,
            kategori_id = ?,
            program_id = ?,
            gun = ?,
            saat = ?,
            tarih = ?,
            katilimci = ?,
            kontenjan = ?,
            aile_katilim = ?,
            baslik = ?,
            durum = ?,
            aktif = ?,
            dil = 1");
            
        $kaydet = $sorgu->execute(array(
            $sira, $sehir, $sinif, $kategori_id, $program_id, $gun, $saat, $tarih, 
            $katilimci, $kontenjan, $aile_katilim, $baslik, 
            $durum, $aktif
        ));
        
        if($kaydet) {
		header("Location:".$_SERVER['HTTP_REFERER']."");
		exit();
        }
    }
}

// For derslik_durum_guncelle
if(isset($_POST['derslik_durum_guncelle'])) {
    cVCLmHLxbS_panelislemkontrol("derslik_durum_guncelle");
    if($_SESSION['rutbe'] == 0) {
        $id = $_POST['id'];
        $sira = $_POST['sira'];
        $sehir = $_POST['sehir'];
        $sinif = $_POST['sinif'];
        $kategori_id = isset($_POST['kategori_id']) && !empty($_POST['kategori_id']) ? intval($_POST['kategori_id']) : NULL;
        $program_id = isset($_POST['program_id']) && !empty($_POST['program_id']) ? intval($_POST['program_id']) : NULL;
        $gun = $_POST['gun'];
        $saat = $_POST['saat'];
        $tarih = $_POST['tarih'];
        $katilimci = intval($_POST['katilimci']);
        $kontenjan = intval($_POST['kontenjan']);
        $aile_katilim = intval($_POST['aile_katilim']);
        $baslik = $_POST['baslik'];
        $durum = $_POST['durum'];
        $aktif = isset($_POST['aktif']) ? 1 : 0;
        
        $sorgu = $db->prepare("UPDATE derslik_durumlari SET
            sira = ?,
            sehir = ?,
            sinif = ?,
            kategori_id = ?,
            program_id = ?,
            gun = ?,
            saat = ?,
            tarih = ?,
            katilimci = ?,
            kontenjan = ?,
            aile_katilim = ?,
            baslik = ?,
            durum = ?,
            aktif = ?
            WHERE id = ?");
            
        $guncelle = $sorgu->execute(array(
            $sira, $sehir, $sinif, $kategori_id, $program_id, $gun, $saat, $tarih, 
            $katilimci, $kontenjan, $aile_katilim, $baslik, 
            $durum, $aktif, $id
        ));
        
        if($guncelle) {
			header("Location:".$_SERVER['HTTP_REFERER']."");
			exit();
        }
    }
}

if(isset($_POST['derslik_durum_aktif']))
{
    cVCLmHLxbS_panelislemkontrol("derslik_durum_aktif");
    if($_SESSION['rutbe'] == 0)
    {
        if(isset($_POST['id']))
        {
            foreach($_POST['id'] as $id)
            {
                $sorgu = $db->prepare("UPDATE derslik_durumlari SET aktif = 1 WHERE id = ?");
                $sorgu->execute(array($id));
            }
            $_SESSION['derslik_durum_aktif'] = '1';
        }else{
            $_SESSION['secim'] = '3';
        }
        header("Location:../".yonetim."/derslik_durum_listele.html");
        exit();
    }
}

if(isset($_POST['derslik_durum_pasif']))
{
    cVCLmHLxbS_panelislemkontrol("derslik_durum_pasif");
    if($_SESSION['rutbe'] == 0)
    {
        if(isset($_POST['id']))
        {
            foreach($_POST['id'] as $id)
            {
                $sorgu = $db->prepare("UPDATE derslik_durumlari SET aktif = 0 WHERE id = ?");
                $sorgu->execute(array($id));
            }
            $_SESSION['derslik_durum_pasif'] = '1';
        }else{
            $_SESSION['secim'] = '3';
        }
        header("Location:../".yonetim."/derslik_durum_listele.html");
        exit();
    }
}

if(isset($_POST['derslik_durum_tumu']))
{
    cVCLmHLxbS_panelislemkontrol("derslik_durum_tumu");
    if($_SESSION['rutbe'] == 0)
    {
        if(isset($_POST['id']))
        {
            foreach($_POST['id'] as $id)
            {
                $sorgu = $db->prepare("DELETE FROM derslik_durumlari WHERE id = ?");
                $sorgu->execute(array($id));
            }
            $_SESSION['derslik_durum_tumu'] = '1';
        }else{
            $_SESSION['secim'] = '3';
        }
        header("Location:../".yonetim."/derslik_durum_listele.html");
        exit();
    }
}

// Derslik Kategori İşlemleri
if(isset($_POST['derslik_kategori_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("derslik_kategori_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira = isset($_POST['sira']) ? intval($_POST['sira']) : 0;
		$adi = trim($_POST['adi']);
		$seo = cVCLmHLxbS_seo($adi);
		$durum = isset($_POST['durum']) ? 1 : 0;
		
		$sorgu = $db->prepare("INSERT INTO derslik_kategorileri SET
			sira = ?,
			adi = ?,
			seo = ?,
			durum = ?,
			dil = ?");
		$ekle = $sorgu->execute(array($sira, $adi, $seo, $durum, $_SESSION['admin_dil']));
		
		if($ekle)
		{
			$_SESSION['derslik_kategori_ekle'] = 'yes';
			header("Location:../".yonetim."/derslik_durum_listele.html");
			exit();
		}
		else
		{
			$_SESSION['derslik_kategori_ekle'] = 'no';
			header("Location:../".yonetim."/derslik_durum_listele.html");
			exit();
		}
	}
}

if(isset($_POST['derslik_kategori_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("derslik_kategori_guncelle");
	if($_SESSION['rutbe'] == 0)
	{
		$id = intval($_POST['id']);
		$sira = isset($_POST['sira']) ? intval($_POST['sira']) : 0;
		$adi = trim($_POST['adi']);
		$seo = cVCLmHLxbS_seo($adi);
		$durum = isset($_POST['durum']) ? 1 : 0;
		
		$sorgu = $db->prepare("UPDATE derslik_kategorileri SET
			sira = ?,
			adi = ?,
			seo = ?,
			durum = ?
			WHERE id = ? AND dil = ?");
		$guncelle = $sorgu->execute(array($sira, $adi, $seo, $durum, $id, $_SESSION['admin_dil']));
		
		if($guncelle)
		{
			$_SESSION['derslik_kategori_guncelle'] = 'yes';
			header("Location:../".yonetim."/derslik_durum_listele.html");
			exit();
		}
		else
		{
			$_SESSION['derslik_kategori_guncelle'] = 'no';
			header("Location:../".yonetim."/derslik_durum_listele.html");
			exit();
		}
	}
}

if(isset($_GET['tablo']) && $_GET['tablo'] == 'derslik_kategorileri' && isset($_GET['islem']) && $_GET['islem'] == 'sil')
{
	cVCLmHLxbS_panelislemkontrol("derslik_kategori_sil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = intval($_GET['id']);
		$sorgu = $db->prepare("DELETE FROM derslik_kategorileri WHERE id = ?");
		$sil = $sorgu->execute(array($id));
		
		if($sil)
		{
			$_SESSION['derslik_kategori_sil'] = 'yes';
			header("Location:../".yonetim."/derslik_durum_listele.html");
			exit();
		}
		else
		{
			$_SESSION['derslik_kategori_sil'] = 'no';
			header("Location:../".yonetim."/derslik_durum_listele.html");
			exit();
		}
	}
}

##Bağış Türü Ekle ##
if(isset($_POST['bagis_turu_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_turu_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		if(isset($_POST['durum'])){$durum = 1;}else{$durum = 0;}		
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$sorgu = $db->prepare("INSERT INTO bagis_turleri SET
				adi 	= ?,
				durum 	= ?,
				tarih 	= ?");
		$Ekle = $sorgu->execute(array(
				$adi,
				$durum,
				$tarih
			));
		if($Ekle)
		{
			$last_id 		= $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Bağış Türü Eklendi",
				'icon' 		=> "icon-plus",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı bağış türünü ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['bagis_turu_ekle'] = 'yes';
			header("Location:../".yonetim."/bagis-turleri.html");
			exit();
		}
		else
		{
			$_SESSION['bagis_turu_ekle'] = 'no';
			header("Location:../".yonetim."/bagis-turu-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-turu-ekle.html");
		exit();
	}
}

##Bağış Türü Güncelle ##
if(isset($_POST['bagis_turu_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_turu_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$adi 		= $_POST['adi'];
		if(isset($_POST['durum'])){$durum = 1;}else{$durum = 0;}
		$tarih		= date('Y-m-d H:i:s');
		$tarih		= cVCLmHLxbS_tr_tarih($tarih);
		
		$sorgu = $db->prepare("UPDATE bagis_turleri SET
			adi 	= ?,
			durum 	= ?,
			tarih 	= ?
			WHERE id = ?");
		$guncelle = $sorgu->execute(array(
			$adi,
			$durum,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$last_id 		= $d_id;
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Bağış Türü Güncellendi",
				'icon' 		=> "icon-reload",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: darkturquoise;'>".$adi."</strong> başlıklı bağış türünü güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['bagis_turu_guncelle'] = 'yes';
			header("Location:../".yonetim."/bagis-turu-duzenle/".$d_id.".html");
			exit();
		}
		else
		{
			$_SESSION['bagis_turu_guncelle'] = 'no';
			header("Location:../".yonetim."/bagis-turu-duzenle/".$d_id.".html");
			exit();
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-turu-duzenle/".$d_id.".html");
		exit();
	}
}

##Bağış Türü Sil ##
if(@$_GET['bagistursil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("bagistursil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul= $db->query("SELECT * FROM bagis_turleri WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		$Sorgu = $db->prepare("DELETE FROM bagis_turleri WHERE id = :id");
		$sil_sorgu = $Sorgu->execute(array('id' => $id));
		if($Sorgu->rowCount())
		{
			if($sil_sorgu)
			{
				$last_id 		= $resim_bul['id'];
				$BSorgu = $db->prepare("INSERT INTO bildirimler SET
					baslik		= :baslik,
					icon		= :icon,
					bid			= :bid,
					bildirim	= :bildirim,
					ktarih		= :ktarih,
					tarih 		= :tarih");
				$BEkle = $BSorgu->execute(array(
					'baslik' 	=> "Bağış Türü Silindi",
					'icon' 		=> "icon-trash",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı bağış türünü sildi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				$_SESSION['bagistursil'] = 'yes';
				header("Location:../".yonetim."/bagis-turleri.html");
				exit();
			}
			else
			{
				$_SESSION['bagistursil'] = 'no';
				header("Location:../".yonetim."/bagis-turleri.html");
				exit();
			}
		}
		else
		{
			echo '<meta http-equiv="refresh" content="0; url=404.html">';
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-turleri.html");
		exit();
	}
}

##Bağış Türü Toplu Sil ##
if(isset($_POST['bagis_tur_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_tur_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul= $db->query("SELECT * FROM bagis_turleri WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$TopluSorgu = $db->prepare("DELETE FROM bagis_turleri WHERE id = :id");
				$TopluSil	= $TopluSorgu->execute(array('id' => $i));
				if($TopluSil)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Bağış Türü Silindi",
						'icon' 		=> "icon-trash",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$resim_bul['adi']."</strong> başlıklı bağış türünü sildi.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['bagis_tur_tumu'] = 'yes';
					header("Location:../".yonetim."/bagis-turleri.html");
				}
				else
				{
					$_SESSION['bagis_tur_tumu'] = 'no';
					header("Location:../".yonetim."/bagis-turleri.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/bagis-turleri.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-turleri.html");
		exit();
	}	
}

##Bağış Türü Toplu Aktif ##
if(isset($_POST['bagis_tur_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_tur_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM bagis_turleri WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE bagis_turleri SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"1",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Bağış Türü Aktif",
						'icon' 		=> "icon-check",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı bağış türünü aktif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['bagis_tur_aktif'] = 'yes';
					header("Location:../".yonetim."/bagis-turleri.html");
				}
				else
				{
					$_SESSION['bagis_tur_aktif'] = 'no';
					header("Location:../".yonetim."/bagis-turleri.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/bagis-turleri.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-turleri.html");
		exit();
	}	
}

##Bağış Türü Toplu Pasif ##
if(isset($_POST['bagis_tur_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("bagis_tur_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sayfa_bul= $db->query("SELECT * FROM bagis_turleri WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				$sorgu = $db->prepare("UPDATE bagis_turleri SET
					durum 	= ?
					WHERE id = ?");
				$guncelle = $sorgu->execute(array(
					"0",
					$i
				));
				if($guncelle)
				{
					$last_id 		= $i;
					$BSorgu = $db->prepare("INSERT INTO bildirimler SET
						baslik		= :baslik,
						icon		= :icon,
						bid			= :bid,
						bildirim	= :bildirim,
						ktarih		= :ktarih,
						tarih 		= :tarih");
					$BEkle = $BSorgu->execute(array(
						'baslik' 	=> "Bağış Türü Pasif",
						'icon' 		=> "icon-close",
						'bid' 		=> $last_id,
						'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$sayfa_bul['adi']."</strong> başlıklı bağış türünü pasif etti.",
						'ktarih'	=> $bildirimkt,
						'tarih'		=> $bildirimt
					));
					$_SESSION['bagis_tur_pasif'] = 'yes';
					header("Location:../".yonetim."/bagis-turleri.html");
				}
				else
				{
					$_SESSION['bagis_tur_pasif'] = 'no';
					header("Location:../".yonetim."/bagis-turleri.html");
				}
			}
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/bagis-turleri.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-turleri.html");
		exit();
	}	
}

##Bağış Türü Tümünü Sil ##
if(@$_GET['bagisturtumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("bagisturtumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$Sorgu = $db->prepare("TRUNCATE TABLE bagis_turleri");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['bagisturtumunusil'] = 'yes';
			header("Location:../".yonetim."/bagis-turleri.html");
			exit();
		}
		else
		{
			$_SESSION['bagisturtumunusil'] = 'no';
			header("Location:../".yonetim."/bagis-turleri.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/bagis-turleri.html");
		exit();
	}
}

##Randevu Hizmeti Ekle ##
if(isset($_POST['randevu_hizmet_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("randevu_hizmet_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$baslik 	= $_POST['baslik'];
		$aciklama 	= $_POST['aciklama'] ?? '';
		$fiyat 		= $_POST['fiyat'] ?? 0;
		$sira 		= $_POST['sira'] ?? 0;
		$ikon 		= $_POST['ikon'] ?? '';
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$tarih		= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
		
		$resim = ''; 
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/randevu_hizmetler");
			if ($upload->processed)
			{
				$resim = $upload->file_dst_name;
			}
		}
		
		$sorgu = $db->prepare("INSERT INTO randevu_hizmetler SET
			baslik 	= ?,
			aciklama = ?,
			fiyat 	= ?,
			sira 	= ?,
			resim 	= ?,
			ikon 	= ?,
			durum 	= ?,
			dil 	= ?,
			tarih 	= ?");
		$Ekle = $sorgu->execute(array(
			$baslik,
			$aciklama,
			$fiyat,
			$sira,
			$resim,
			$ikon,
			$durum,
			$_SESSION['admin_dil'],
			$tarih
		));
		if($Ekle)
		{
			$last_id = $db->lastInsertId();
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Randevu Hizmeti Eklendi",
				'icon' 		=> "icon-calendar",
				'bid' 		=> $last_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$baslik."</strong> başlıklı randevu hizmeti ekledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['randevu_hizmet_ekle'] = 'yes';
			header("Location:../".yonetim."/randevu-hizmet-listele.html");
			exit();
		}
		else
		{
			$_SESSION['randevu_hizmet_ekle'] = 'no';
			header("Location:../".yonetim."/randevu-hizmet-ekle.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/randevu-hizmet-ekle.html");
		exit();
	}
}

##Randevu Hizmeti Güncelle ##
if(isset($_POST['randevu_hizmet_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("randevu_hizmet_guncelle");
	$d_id = $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$baslik 	= $_POST['baslik'];
		$aciklama 	= $_POST['aciklama'] ?? '';
		$fiyat 		= $_POST['fiyat'] ?? 0;
		$sira 		= $_POST['sira'] ?? 0;
		$ikon 		= $_POST['ikon'] ?? '';
		if($_POST['durum']){$durum = 1;}else{$durum = 0;}
		$tarih		= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
		
		$resim_bul = $db->query("SELECT * FROM randevu_hizmetler WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/randevu_hizmetler");
			if ($upload->processed)
			{
				if($resim_bul['resim'] != '' && file_exists("../".tema."/uploads/randevu_hizmetler/".$resim_bul['resim']))
				{
					unlink("../".tema."/uploads/randevu_hizmetler/".$resim_bul['resim']);
				}
				$resim = $upload->file_dst_name;
			}
		}
		else
		{
			$resim = $resim_bul['resim'];
		}
		
		$sorgu = $db->prepare("UPDATE randevu_hizmetler SET
			baslik 	= ?,
			aciklama = ?,
			fiyat 	= ?,
			sira 	= ?,
			resim 	= ?,
			ikon 	= ?,
			durum 	= ?,
			tarih 	= ?
			WHERE id = ?");
		$guncelle = $sorgu->execute(array(
			$baslik,
			$aciklama,
			$fiyat,
			$sira,
			$resim,
			$ikon,
			$durum,
			$tarih,
			$d_id
		));
		if($guncelle)
		{
			$BSorgu = $db->prepare("INSERT INTO bildirimler SET
				baslik		= :baslik,
				icon		= :icon,
				bid			= :bid,
				bildirim	= :bildirim,
				ktarih		= :ktarih,
				tarih 		= :tarih");
			$BEkle = $BSorgu->execute(array(
				'baslik' 	=> "Randevu Hizmeti Güncellendi",
				'icon' 		=> "icon-calendar",
				'bid' 		=> $d_id,
				'bildirim' 	=> "<strong>".$_SESSION['Yonetim_Adi']."</strong> <strong style='color: crimson;'>".$baslik."</strong> başlıklı randevu hizmetini güncelledi.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			$_SESSION['randevu_hizmet_guncelle'] = 'yes';
			header("Location:../".yonetim."/randevu-hizmet-listele.html");
			exit();
		}
		else
		{
			$_SESSION['randevu_hizmet_guncelle'] = 'no';
			header("Location:../".yonetim."/randevu-hizmet-ekle.html?islem=duzenle&id=".$d_id);
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/randevu-hizmet-listele.html");
		exit();
	}
}

##Randevu Hizmeti Resim Sil ##
if(@$_GET['randevuhizmetresimsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("randevuhizmetresimsil");
	if($_SESSION['rutbe'] == 0)
	{
		$sid = $_GET['sid'];
		$resim_bul = $db->query("SELECT * FROM randevu_hizmetler WHERE id = '{$sid}'")->fetch(PDO::FETCH_ASSOC);
		if($resim_bul['resim'] != '' && file_exists("../".tema."/uploads/randevu_hizmetler/".$resim_bul['resim']))
		{
			unlink("../".tema."/uploads/randevu_hizmetler/".$resim_bul['resim']);
		}
		$sorgu = $db->prepare("UPDATE randevu_hizmetler SET resim = ? WHERE id = ?");
		$guncelle = $sorgu->execute(array('', $sid));
		if($guncelle)
		{
			$_SESSION['randevuhizmetresimsil'] = 'yes';
			header("Location:../".yonetim."/randevu-hizmet-ekle.html?islem=duzenle&id=".$sid);
			exit();
		}
		else
		{
			$_SESSION['randevuhizmetresimsil'] = 'no';
			header("Location:../".yonetim."/randevu-hizmet-ekle.html?islem=duzenle&id=".$sid);
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/randevu-hizmet-listele.html");
		exit();
	}
}

##Randevu Hizmeti Sil ##
if(@$_GET['randevuhizmetsil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("randevuhizmetsil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul = $db->query("SELECT * FROM randevu_hizmetler WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		if($resim_bul['resim'] != '' && file_exists("../".tema."/uploads/randevu_hizmetler/".$resim_bul['resim']))
		{
			unlink("../".tema."/uploads/randevu_hizmetler/".$resim_bul['resim']);
		}
		$sorgu = $db->prepare("DELETE FROM randevu_hizmetler WHERE id = ?");
		$sil = $sorgu->execute(array($id));
		if($sil)
		{
			$_SESSION['randevuhizmetsil'] = 'yes';
			header("Location:../".yonetim."/randevu-hizmet-listele.html");
			exit();
		}
		else
		{
			$_SESSION['randevuhizmetsil'] = 'no';
			header("Location:../".yonetim."/randevu-hizmet-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/randevu-hizmet-listele.html");
		exit();
	}
}

##Randevu Hizmeti Toplu Aktif ##
if(isset($_POST['randevu_hizmet_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("randevu_hizmet_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sorgu = $db->prepare("UPDATE randevu_hizmetler SET durum = ? WHERE id = ?");
				$guncelle = $sorgu->execute(array("1", $i));
			}
			$_SESSION['randevu_hizmet_aktif'] = 'yes';
			header("Location:../".yonetim."/randevu-hizmet-listele.html");
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/randevu-hizmet-listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/randevu-hizmet-listele.html");
	}
}

##Randevu Hizmeti Toplu Pasif ##
if(isset($_POST['randevu_hizmet_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("randevu_hizmet_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sorgu = $db->prepare("UPDATE randevu_hizmetler SET durum = ? WHERE id = ?");
				$guncelle = $sorgu->execute(array("0", $i));
			}
			$_SESSION['randevu_hizmet_pasif'] = 'yes';
			header("Location:../".yonetim."/randevu-hizmet-listele.html");
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/randevu-hizmet-listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/randevu-hizmet-listele.html");
	}
}

##Randevu Hizmeti Toplu Sil ##
if(isset($_POST['randevu_hizmet_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("randevu_hizmet_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$resim_bul = $db->query("SELECT * FROM randevu_hizmetler WHERE id = '{$i}'")->fetch(PDO::FETCH_ASSOC);
				if($resim_bul['resim'] != '' && file_exists("../".tema."/uploads/randevu_hizmetler/".$resim_bul['resim']))
				{
					unlink("../".tema."/uploads/randevu_hizmetler/".$resim_bul['resim']);
				}
				$sorgu = $db->prepare("DELETE FROM randevu_hizmetler WHERE id = ?");
				$sil = $sorgu->execute(array($i));
			}
			$_SESSION['randevu_hizmet_tumu'] = 'yes';
			header("Location:../".yonetim."/randevu-hizmet-listele.html");
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/randevu-hizmet-listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/randevu-hizmet-listele.html");
	}
}

##Randevu Hizmeti Tümünü Sil ##
if(@$_GET['randevuhizmettumunusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("randevuhizmettumunusil");
	if($_SESSION['rutbe'] == 0)
	{
		$Sorgu = $db->prepare("TRUNCATE TABLE randevu_hizmetler");
		$sil_sorgu = $Sorgu->execute();
		if($sil_sorgu)
		{
			$_SESSION['randevuhizmettumunusil'] = 'yes';
			header("Location:../".yonetim."/randevu-hizmet-listele.html");
			exit();
		}
		else
		{
			$_SESSION['randevuhizmettumunusil'] = 'no';
			header("Location:../".yonetim."/randevu-hizmet-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/randevu-hizmet-listele.html");
		exit();
	}
}

##Randevu Hizmeti Sıralama ##
if(@$_GET['randevuhizmetsiralama'] == "sira")
{
	cVCLmHLxbS_panelislemkontrol("randevuhizmetsiralama");
	if($_SESSION['rutbe'] == 0)
	{
		$item = $_POST['item'];
		$say = 1;
		foreach($item as $i)
		{
			$sorgu = $db->prepare("UPDATE randevu_hizmetler SET sira = ? WHERE id = ?");
			$guncelle = $sorgu->execute(array($say, $i));
			$say++;
		}
		if($guncelle)
		{
			echo json_encode(array("islemMsj" => "Güncellendi"));
		}
		else
		{
			echo json_encode(array("islemMsj" => "İşlem başarısız"));
		}
	}
	else
	{
		echo json_encode(array("islemMsj" => "İşlem başarısız"));
	}
	exit();
}

##Randevu Durum Güncelle ##
if(isset($_POST['randevu_durum_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("randevu_durum_guncelle");
	if($_SESSION['rutbe'] == 0)
	{
		$id = (int)$_POST['id'];
		$durum = (int)$_POST['durum'];
		
		// Önce randevu bilgilerini al
		$randevuSorgu = $db->prepare("SELECT r.*, h.baslik as hizmet_adi FROM randevular r 
									   LEFT JOIN randevu_hizmetler h ON r.hizmet_id = h.id 
									   WHERE r.id = ?");
		$randevuSorgu->execute(array($id));
		$randevuBilgi = $randevuSorgu->fetch(PDO::FETCH_ASSOC);
		
		$sorgu = $db->prepare("UPDATE randevular SET durum = ? WHERE id = ?");
		$guncelle = $sorgu->execute(array($durum, $id));
		
		if($guncelle && $randevuBilgi)
		{
			// Durum metinleri
			$durum_metinleri = [
				0 => ['baslik' => 'Beklemede', 'mesaj' => 'Randevunuz alınmıştır ve inceleme aşamasındadır. En kısa sürede tarafınıza dönüş yapılacaktır.', 'renk' => '#f39c12'],
				1 => ['baslik' => 'Onaylandı', 'mesaj' => 'Randevunuz onaylanmıştır. Belirtilen tarih ve saatte sizleri bekliyoruz.', 'renk' => '#27ae60'],
				2 => ['baslik' => 'İptal Edildi', 'mesaj' => 'Randevunuz iptal edilmiştir. Daha fazla bilgi için lütfen bizimle iletişime geçin.', 'renk' => '#e74c3c'],
				3 => ['baslik' => 'Tamamlandı', 'mesaj' => 'Randevunuz tamamlanmıştır. Hizmetimizden faydalandığınız için teşekkür ederiz.', 'renk' => '#3498db']
			];
			
			$durum_bilgi = $durum_metinleri[$durum];
			
			// Mail şablonu oluştur
			$mailIcerik = '
<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Randevu Durumu Güncellendi</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px;">
		<tr>
			<td align="center">
				<table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
					
					<!-- Header -->
					<tr>
						<td style="background: linear-gradient(135deg, '.$durum_bilgi['renk'].' 0%, #2c3e50 100%); padding: 40px 30px; text-align: center;">
							<h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: bold;">Randevu Durumu Güncellendi</h1>
							<p style="color: #ffffff; margin: 10px 0 0 0; font-size: 16px;">Randevunuzun güncel durumu aşağıdadır</p>
						</td>
					</tr>
					
					<!-- Durum Badge -->
					<tr>
						<td style="padding: 30px; text-align: center;">
							<div style="display: inline-block; background-color: '.$durum_bilgi['renk'].'; color: #ffffff; padding: 12px 30px; border-radius: 25px; font-size: 18px; font-weight: bold;">
								'.$durum_bilgi['baslik'].'
							</div>
						</td>
					</tr>
					
					<!-- Mesaj -->
					<tr>
						<td style="padding: 0 30px 20px 30px; text-align: center;">
							<p style="color: #555555; font-size: 16px; line-height: 1.6; margin: 0;">
								'.$durum_bilgi['mesaj'].'
							</p>
						</td>
					</tr>
					
					<!-- Randevu Detayları -->
					<tr>
						<td style="padding: 20px 30px;">
							<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8f9fa; border-radius: 8px; padding: 20px;">
								<tr>
									<td style="padding: 10px 0;">
										<h2 style="color: #2c3e50; font-size: 20px; margin: 0 0 20px 0; border-bottom: 2px solid '.$durum_bilgi['renk'].'; padding-bottom: 10px;">
											Randevu Bilgileriniz
										</h2>
									</td>
								</tr>
								<tr>
									<td style="padding: 8px 0;">
										<table width="100%" cellpadding="0" cellspacing="0" border="0">
											<tr>
												<td width="40%" style="color: #7f8c8d; font-size: 14px; padding: 8px 0;">
													<strong>Randevu No:</strong>
												</td>
												<td style="color: #2c3e50; font-size: 14px; padding: 8px 0;">
													#'.$randevuBilgi['id'].'
												</td>
											</tr>
											'.($randevuBilgi['hizmet_adi'] ? '
											<tr>
												<td width="40%" style="color: #7f8c8d; font-size: 14px; padding: 8px 0;">
													<strong>Hizmet:</strong>
												</td>
												<td style="color: #2c3e50; font-size: 14px; padding: 8px 0;">
													'.$randevuBilgi['hizmet_adi'].'
												</td>
											</tr>
											' : '').'
											<tr>
												<td width="40%" style="color: #7f8c8d; font-size: 14px; padding: 8px 0;">
													<strong>Ad Soyad:</strong>
												</td>
												<td style="color: #2c3e50; font-size: 14px; padding: 8px 0;">
													'.strip_tags($randevuBilgi['isim']).'
												</td>
											</tr>
											<tr>
												<td width="40%" style="color: #7f8c8d; font-size: 14px; padding: 8px 0;">
													<strong>Randevu Tarihi:</strong>
												</td>
												<td style="color: #2c3e50; font-size: 14px; padding: 8px 0;">
													'.date('d.m.Y', strtotime($randevuBilgi['tarih'])).'
												</td>
											</tr>
											<tr>
												<td width="40%" style="color: #7f8c8d; font-size: 14px; padding: 8px 0;">
													<strong>Randevu Saati:</strong>
												</td>
												<td style="color: #2c3e50; font-size: 14px; padding: 8px 0;">
													'.date('H:i', strtotime($randevuBilgi['saat'])).'
												</td>
											</tr>
											<tr>
												<td width="40%" style="color: #7f8c8d; font-size: 14px; padding: 8px 0;">
													<strong>E-posta:</strong>
												</td>
												<td style="color: #2c3e50; font-size: 14px; padding: 8px 0;">
													'.strip_tags($randevuBilgi['email']).'
												</td>
											</tr>
											<tr>
												<td width="40%" style="color: #7f8c8d; font-size: 14px; padding: 8px 0;">
													<strong>Telefon:</strong>
												</td>
												<td style="color: #2c3e50; font-size: 14px; padding: 8px 0;">
													'.strip_tags($randevuBilgi['telefon']).'
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					
					<!-- İletişim Bilgisi -->
					<tr>
						<td style="padding: 20px 30px 30px 30px; text-align: center; border-top: 1px solid #e0e0e0;">
							<p style="color: #7f8c8d; font-size: 14px; line-height: 1.6; margin: 0;">
								Herhangi bir sorunuz olması durumunda bizimle iletişime geçmekten çekinmeyin.<br>
								<strong style="color: #2c3e50;">'.firma_adi.'</strong>
							</p>
						</td>
					</tr>
					
					<!-- Footer -->
					<tr>
						<td style="background-color: #2c3e50; padding: 20px 30px; text-align: center;">
							<p style="color: #ffffff; font-size: 12px; margin: 0;">
								Bu mail otomatik olarak gönderilmiştir. Lütfen yanıtlamayınız.
							</p>
							<p style="color: #95a5a6; font-size: 12px; margin: 10px 0 0 0;">
								© '.date('Y').' '.firma_adi.' - Tüm hakları saklıdır.
							</p>
						</td>
					</tr>
					
				</table>
			</td>
		</tr>
	</table>
</body>
</html>';
			
			// Mail gönder
			try {
				$mailKonu = "Randevu Durumu Güncellendi - " . $durum_bilgi['baslik'];
				$kullanici_mail = strip_tags($randevuBilgi['email']);
				
				$mail = new PHPMailer();
				$mail->IsSMTP(true);
				$mail->SMTPSecure = m_sertifika;
				$mail->From = m_adresi;
				$mail->Sender = m_adresi;
				$mail->AddAddress($kullanici_mail, strip_tags($randevuBilgi['isim'])); 
				$mail->AddReplyTo(m_adresi);
				$mail->FromName = firma_adi;
				$mail->Host = m_server;
				$mail->SMTPAuth = true;
				$mail->Port = m_port;
				$mail->CharSet = 'UTF-8';
				$mail->Username = m_adresi;
				$mail->Password = m_parola;
				$mail->Subject = $mailKonu;
				$mail->IsHTML(true);
				$mail->Body = $mailIcerik;
				$mail->Send();
			} catch (Exception $e) {
				// Mail hatası sessizce yutulur
			}
			
			$_SESSION['randevu_durum_guncelle'] = 'yes';
			header("Location:../".yonetim."/randevu-detay.html?id=".$id);
			exit();
		}
		else
		{
			$_SESSION['randevu_durum_guncelle'] = 'no';
			header("Location:../".yonetim."/randevu-detay.html?id=".$id);
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/randevu-listele.html");
		exit();
	}
}

##Randevu Sil ##
if(@$_GET['randevusil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("randevusil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$sorgu = $db->prepare("DELETE FROM randevular WHERE id = ?");
		$sil = $sorgu->execute(array($id));
		if($sil)
		{
			$_SESSION['randevusil'] = 'yes';
			header("Location:../".yonetim."/randevu-listele.html");
			exit();
		}
		else
		{
			$_SESSION['randevusil'] = 'no';
			header("Location:../".yonetim."/randevu-listele.html");
			exit();
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/randevu-listele.html");
		exit();
	}
}

##Randevu Toplu Sil ##
if(isset($_POST['randevu_tumu']))
{
	cVCLmHLxbS_panelislemkontrol("randevu_tumu");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sorgu = $db->prepare("DELETE FROM randevular WHERE id = ?");
				$sil = $sorgu->execute(array($i));
			}
			$_SESSION['randevu_tumu'] = 'yes';
			header("Location:../".yonetim."/randevu-listele.html");
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/randevu-listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/randevu-listele.html");
	}
}

##Randevu Toplu Onayla ##
if(isset($_POST['randevu_onayla']))
{
	cVCLmHLxbS_panelislemkontrol("randevu_onayla");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sorgu = $db->prepare("UPDATE randevular SET durum = ? WHERE id = ?");
				$guncelle = $sorgu->execute(array("1", $i));
			}
			$_SESSION['randevu_onayla'] = 'yes';
			header("Location:../".yonetim."/randevu-listele.html");
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/randevu-listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/randevu-listele.html");
	}
}

##Randevu Toplu İptal ##
if(isset($_POST['randevu_iptal']))
{
	cVCLmHLxbS_panelislemkontrol("randevu_iptal");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sorgu = $db->prepare("UPDATE randevular SET durum = ? WHERE id = ?");
				$guncelle = $sorgu->execute(array("2", $i));
			}
			$_SESSION['randevu_iptal'] = 'yes';
			header("Location:../".yonetim."/randevu-listele.html");
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/randevu-listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/randevu-listele.html");
	}
}


## Impact Reach Ayarlar Kaydet ##
if(isset($_POST['impact_reach_ayarlar_kaydet']))
{
	cVCLmHLxbS_panelislemkontrol("impact_reach_ayarlar_kaydet");
	if($_SESSION['rutbe'] == 0)
	{
		$ek_baslik 		= $_POST['ek_baslik'];
		$ek_aciklama 	= $_POST['ek_aciklama'];
		$resim_aciklama = $_POST['resim_aciklama'];
		$banner_baslik 	= $_POST['banner_baslik'];
		$banner_aciklama = $_POST['banner_aciklama'];
		$uzman_gorusleri_aktif = isset($_POST['uzman_gorusleri_aktif']) ? 1 : 0;
		$video_url = isset($_POST['video_url']) ? trim($_POST['video_url']) : '';
		
		// Resim Yükleme
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/impact_reach");
			if ($upload->processed)
			{
				$Resim = $upload->file_dst_name;
				// Eski resmi sil (opsiyonel, eğer tek kayıt varsa)
				$eski_resim = $db->query("SELECT resim FROM impact_reach_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
				if($eski_resim['resim']){
					@unlink("../".tema."/uploads/impact_reach/".$eski_resim['resim']);
				}
			}
		}

		// Banner Arkaplanı
		if(isset($_FILES['banner_resim'])){
			$bannerUpload = new upload($_FILES['banner_resim']);
			if ($bannerUpload->uploaded)
			{
				$bannerUpload->file_auto_rename = true;
				$bannerUpload->process("../".tema."/uploads/impact_reach");
				if ($bannerUpload->processed)
				{
					$BannerResim = $bannerUpload->file_dst_name;
					$eski_banner = $db->query("SELECT banner_resim FROM impact_reach_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
					if($eski_banner && !empty($eski_banner['banner_resim'])){
						@unlink("../".tema."/uploads/impact_reach/".$eski_banner['banner_resim']);
					}
				}
			}
		}
		
		// Kayıt var mı kontrol et
		$kontrol = $db->query("SELECT * FROM impact_reach_settings WHERE id = 1");
		if($kontrol->rowCount()){
			// Güncelleme
			$sql = "UPDATE impact_reach_settings SET 
					ek_baslik = ?, 
					ek_aciklama = ?, 
					resim_aciklama = ?, 
					banner_baslik = ?,
					banner_aciklama = ?,
					uzman_gorusleri_aktif = ?,
					video_url = ?";
			$params = [$ek_baslik, $ek_aciklama, $resim_aciklama, $banner_baslik, $banner_aciklama, $uzman_gorusleri_aktif, $video_url];
			
			if(isset($Resim)){
				$sql .= ", resim = ?";
				$params[] = $Resim;
			}

			if(isset($BannerResim)){
				$sql .= ", banner_resim = ?";
				$params[] = $BannerResim;
			}
			
			$sql .= " WHERE id = 1";
			$sorgu = $db->prepare($sql);
			$islem = $sorgu->execute($params);
		} else {
			// Ekleme
			$Resim = isset($Resim) ? $Resim : "";
			$BannerResim = isset($BannerResim) ? $BannerResim : "";
			$sorgu = $db->prepare("INSERT INTO impact_reach_settings SET 
					id = 1,
					ek_baslik = ?, 
					ek_aciklama = ?, 
					resim_aciklama = ?, 
					banner_baslik = ?,
					banner_aciklama = ?,
					uzman_gorusleri_aktif = ?,
					video_url = ?,
					resim = ?,
					banner_resim = ?,
					dil = ?");
			$islem = $sorgu->execute([$ek_baslik, $ek_aciklama, $resim_aciklama, $banner_baslik, $banner_aciklama, $uzman_gorusleri_aktif, $video_url, $Resim, $BannerResim, $_SESSION['admin_dil']]);
		}

		if($islem)
		{
			$_SESSION['impact_reach_ayarlar_kaydet'] = 'yes';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
		else
		{
			$_SESSION['impact_reach_ayarlar_kaydet'] = 'no';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/impact_reach_listele.html");
	}
}

// Impact Reach Ayarlar Resim Sil
if(isset($_GET['impact_reach_ayarlar_resim_sil']))
{
	if($_SESSION['demohesap'] != 1)
	{
		cVCLmHLxbS_panelislemkontrol("impact_reach_listele");
		
		$resim_bul = $db->query("SELECT * FROM impact_reach_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
		if($resim_bul['resim']){
			@unlink("../".tema."/uploads/impact_reach/".$resim_bul['resim']);
		}
		
		$guncelle = $db->prepare("UPDATE impact_reach_settings SET resim = ? WHERE id = 1");
		$guncelle->execute(['']);
		
		if($guncelle)
		{
			$_SESSION['impact_reach_ayarlar_resim_sil'] = 'yes';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
		else
		{
			$_SESSION['impact_reach_ayarlar_resim_sil'] = 'no';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/impact_reach_listele.html");
	}
}

// Impact Reach Banner Resim Sil
if(isset($_GET['impact_reach_banner_resim_sil']))
{
	if($_SESSION['demohesap'] != 1)
	{
		cVCLmHLxbS_panelislemkontrol("impact_reach_listele");
		
		$resim_bul = $db->query("SELECT * FROM impact_reach_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
		if(!empty($resim_bul['banner_resim'])){
			@unlink("../".tema."/uploads/impact_reach/".$resim_bul['banner_resim']);
		}
		
		$guncelle = $db->prepare("UPDATE impact_reach_settings SET banner_resim = ? WHERE id = 1");
		$guncelle->execute(['']);
		
		if($guncelle)
		{
			$_SESSION['impact_reach_banner_resim_sil'] = 'yes';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
		else
		{
			$_SESSION['impact_reach_banner_resim_sil'] = 'no';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/impact_reach_listele.html");
	}
}


## Uzman Görüşü Ekle ##
if(isset($_POST['uzman_gorus_ekle']))
{
	cVCLmHLxbS_panelislemkontrol("uzman_gorus_ekle");
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$isim 		= $_POST['isim'];
		$gorev 		= $_POST['gorev'];
		$yorum 		= $_POST['yorum'];
		$durum 		= $_POST['durum'];
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/uzmanlar");
			if ($upload->processed)
			{
				$Resim = $upload->file_dst_name;
			}
		}
		$Resim = isset($Resim) ? $Resim : "";

		$sorgu = $db->prepare("INSERT INTO uzman_gorusleri SET
				sira 		= ?,
				isim 		= ?,
				gorev 		= ?,
				yorum 		= ?,
				durum 		= ?,
				resim 		= ?,
				dil 		= ?");
		$Ekle = $sorgu->execute(array(
				$sira,
				$isim,
				$gorev,
				$yorum,
				$durum,
				$Resim,
				$_SESSION['admin_dil']
				));
		if($Ekle)
		{
			$_SESSION['uzman_gorus_ekle'] = 'yes';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
		else
		{
			$_SESSION['uzman_gorus_ekle'] = 'no';
			header("Location:../".yonetim."/uzman_gorus_ekle.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/uzman_gorus_ekle.html");
	}
}

## Uzman Görüşü Güncelle ##
if(isset($_POST['uzman_gorus_guncelle']))
{
	cVCLmHLxbS_panelislemkontrol("uzman_gorus_guncelle");
	$d_id 	= $_POST['id'];
	if($_SESSION['rutbe'] == 0)
	{
		$sira 		= $_POST['sira'];
		$isim 		= $_POST['isim'];
		$gorev 		= $_POST['gorev'];
		$yorum 		= $_POST['yorum'];
		$durum 		= $_POST['durum'];
		
		$upload = new upload($_FILES['resim']);
		if ($upload->uploaded)
		{
			$upload->file_auto_rename = true;
			$upload->process("../".tema."/uploads/uzmanlar");
			if ($upload->processed)
			{
				$Resim = $upload->file_dst_name;
				
				$resim_bul= $db->query("SELECT * FROM uzman_gorusleri WHERE id = '{$d_id}'")->fetch(PDO::FETCH_ASSOC);
				@unlink("../".tema."/uploads/uzmanlar/".$resim_bul['resim']);
				
				$guncelle = $db->prepare("UPDATE uzman_gorusleri SET resim = ? WHERE id = ?");
				$guncelle->execute([$Resim,$d_id]);
			}
		}
		
		$sorgu = $db->prepare("UPDATE uzman_gorusleri SET
			sira 		= ?,
			isim 		= ?,
			gorev 		= ?,
			yorum 		= ?,
			durum 		= ?
			WHERE id 	= ?");
		$guncelle = $sorgu->execute(array(
			$sira,
			$isim,
			$gorev,
			$yorum,
			$durum,
			$d_id
		));
		if($guncelle)
		{
			$_SESSION['uzman_gorus_guncelle'] = 'yes';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
		else
		{
			$_SESSION['uzman_gorus_guncelle'] = 'no';
			header("Location:../".yonetim."/uzman_gorus_duzenle/".$d_id.".html");
		}		
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/uzman_gorus_duzenle/".$d_id.".html");
	}
}

// Uzman Görüşü Resim Sil
if(isset($_GET['uzman_gorus_resim_sil']))
{
	if($_SESSION['demohesap'] != 1)
	{
		cVCLmHLxbS_panelislemkontrol("uzman_gorus_guncelle");
		
		$id = $_GET['id'];
		
		$resim_bul = $db->query("SELECT * FROM uzman_gorusleri WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		if($resim_bul['resim']){
			@unlink("../".tema."/uploads/uzmanlar/".$resim_bul['resim']);
		}
		
		$guncelle = $db->prepare("UPDATE uzman_gorusleri SET resim = ? WHERE id = ?");
		$guncelle->execute(['', $id]);
		
		if($guncelle)
		{
			$_SESSION['uzman_gorus_resim_sil'] = 'yes';
			header("Location:../".yonetim."/uzman_gorus_duzenle/".$id.".html");
		}
		else
		{
			$_SESSION['uzman_gorus_resim_sil'] = 'no';
			header("Location:../".yonetim."/uzman_gorus_duzenle/".$id.".html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/uzman_gorus_duzenle/".$_GET['id'].".html");
	}
}


## Uzman Görüşü Sil ##
if(@$_GET['uzman_gorus_sil'] == "ok")
{
	cVCLmHLxbS_panelislemkontrol("uzman_gorus_sil");
	if($_SESSION['rutbe'] == 0)
	{
		$id = $_GET['id'];
		$resim_bul	= $db->query("SELECT * FROM uzman_gorusleri WHERE id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		@unlink("../".tema."/uploads/uzmanlar/".$resim_bul['resim']);
		
		$sorgu	= $db->prepare("DELETE FROM uzman_gorusleri WHERE id = :id");
		$sil 	= $sorgu->execute(array('id' => $id));
		if($sil)
		{
			$_SESSION['uzman_gorus_sil'] = 'yes';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
		else
		{
			$_SESSION['uzman_gorus_sil'] = 'no';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/impact_reach_listele.html");
	}
}

## Uzman Görüşü Aktif ##
if(isset($_POST['uzman_gorus_aktif']))
{
	cVCLmHLxbS_panelislemkontrol("uzman_gorus_aktif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sorgu = $db->prepare("UPDATE uzman_gorusleri SET durum = ? WHERE id = ?");
				$guncelle = $sorgu->execute(array("1", $i));
			}
			$_SESSION['uzman_gorus_aktif'] = 'yes';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/impact_reach_listele.html");
	}
}

## Uzman Görüşü Pasif ##
if(isset($_POST['uzman_gorus_pasif']))
{
	cVCLmHLxbS_panelislemkontrol("uzman_gorus_pasif");
	if($_SESSION['rutbe'] == 0)
	{
		if($_POST['id'])
		{
			foreach($_POST['id'] as $i)
			{
				$sorgu = $db->prepare("UPDATE uzman_gorusleri SET durum = ? WHERE id = ?");
				$guncelle = $sorgu->execute(array("0", $i));
			}
			$_SESSION['uzman_gorus_pasif'] = 'yes';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
		else
		{
			$_SESSION['secim'] = 'secimyok';
			header("Location:../".yonetim."/impact_reach_listele.html");
		}
	}
	else
	{
		$_SESSION['demohesap'] = 'no';
		header("Location:../".yonetim."/impact_reach_listele.html");
	}
}

## Yetim Bildirim Gönder ##
if(isset($_POST['yetim_bildirim_gonder']))
{
    // Güvenlik ve Yetki Kontrolü
    if(!isset($_SESSION['rutbe']) || $_SESSION['rutbe'] != 0) {
        die("Yetkisiz işlem!");
    }
    
    $yetimId = intval($_POST['yetim_id']);
    $bildirimTipi = htmlspecialchars($_POST['bildirim_tipi']);
    $detay = htmlspecialchars($_POST['detay']);
    $sonuc = ['durum' => false, 'mesaj' => ''];
    
    try {
        // Yetim ve hamil bilgilerini al (bagis_odeme tablosu ile eşleşecek şekilde güncellendi)
        // h.bagisci_id artık doğrudan bagis_odeme tablosundaki asıl ID'yi referans alıyor
        $yetimSorgu = $db->prepare("SELECT y.*, h.id as hamilik_id, h.bagisci_id 
                                        FROM yetimler y 
                                        LEFT JOIN hamilikler h ON h.yetim_id = y.id AND h.durum = 'aktif'
                                        WHERE y.id = ?");
        $yetimSorgu->execute([$yetimId]);
        $yetimBilgi = $yetimSorgu->fetch(PDO::FETCH_ASSOC);
        
        if(!$yetimBilgi) {
            throw new Exception("Yetim bulunamadı!");
        }
        
        // Bildirim tipine göre işlem yap
        switch($bildirimTipi) {
            case 'egitim':
            case 'saglik':
            case 'yardim':
                // Mevcut bildirim sistemi kullan
                require_once "Bildirim.php";
                $bildirim = new Bildirim($db);
                $bildirimSonuc = $bildirim->yetimBildirimGonder($yetimId, $bildirimTipi, $detay);
                $sonuc = $bildirimSonuc;
                break;
                
            case 'sertifika':
                // Sertifika gönderimi
                $sertifikaId = intval($_POST['sertifika_id'] ?? 0);
                $gonderimYontemi = $_POST['gonderim_yontemi'] ?? '';
                
                if($sertifikaId <= 0 || empty($gonderimYontemi)) {
                    throw new Exception("Sertifika seçimi zorunludur!");
                }
                
                // Sertifika bilgisini al
                $sertifikaSorgu = $db->prepare("SELECT * FROM sertifikalar WHERE id = ? AND durum = 1");
                $sertifikaSorgu->execute([$sertifikaId]);
                $sertifika = $sertifikaSorgu->fetch(PDO::FETCH_ASSOC);
                
                if(!$sertifika) {
                    throw new Exception("Sertifika bulunamadı!");
                }
                
                // Bağışçı (Hamil) bilgilerini al
                if(!$yetimBilgi['hamilik_id'] || !$yetimBilgi['bagisci_id']) {
                    throw new Exception("Bu yetime atanmış sponsor bulunamadı!");
                }
                
                // bagis_odeme tablosu üzerinden güncel iletişim bilgilerini çekiyoruz
                $bagisciSorgu = $db->prepare("SELECT * FROM bagis_odeme WHERE id = ?");
                $bagisciSorgu->execute([$yetimBilgi['bagisci_id']]);
                $bagisci = $bagisciSorgu->fetch(PDO::FETCH_ASSOC);
                
                if(!$bagisci) {
                    throw new Exception("Sponsor (Ödeme Kaydı) bulunamadı!");
                }
                
                // Değişkenleri eşleştir
                $degiskenler = ['{yetim_ad}', '{ad}', '{soyad}', '{tutar}', '{tarih}'];
                $degerler = [
                    $yetimBilgi['ad_soyad'],
                    $bagisci['ad'],
                    $bagisci['soyad'],
                    $bagisci['tutar'],
                    date('d.m.Y H:i')
                ];
                
                // Mesaj içeriğini hazırla
                $mesaj = str_replace($degiskenler, $degerler, $sertifika['aciklama']);
                $gonderimDurumu = 'beklemede';
                $hataMesaji = null;
                
                // Gönderim yap (PHPMailer bağımlılığı olmayan mailgonder fonksiyonunu kullanır)
                if($gonderimYontemi == 'email') {
                    $mailSonuc = mailgonder($degiskenler, $degerler, $mesaj, $bagisci['email'], $sertifika['baslik'], $mesaj);
                    $gonderimDurumu = $mailSonuc ? 'gönderildi' : 'hata';
                    if(!$mailSonuc) $hataMesaji = 'E-posta gönderilemedi';
                } else {
                    // bagis_odeme tablosundaki 'telefon' veya 'tel' sütununu kontrol et
                    $alici_tel = !empty($bagisci['telefon']) ? $bagisci['telefon'] : ($bagisci['tel'] ?? '');
                    $smsSonuc = smsgonder($degiskenler, $degerler, $mesaj, $alici_tel, $mesaj);
                    $gonderimDurumu = $smsSonuc ? 'gönderildi' : 'hata';
                    if(!$smsSonuc) $hataMesaji = 'SMS gönderilemedi';
                }
                
                // Bildirim kaydını logla
                $kayitSorgu = $db->prepare("INSERT INTO sponsor_bildirimleri SET 
                    bagisci_id = ?, 
                    sertifika_id = ?, 
                    gonderim_yontemi = ?, 
                    gonderim_durumu = ?, 
                    gonderim_zamani = NOW(),
                    hata_mesaji = ?");
                $kayitSorgu->execute([$yetimBilgi['bagisci_id'], $sertifikaId, $gonderimYontemi, $gonderimDurumu, $hataMesaji]);
                
                $sonuc = [
                    'durum' => $gonderimDurumu == 'gönderildi',
                    'mesaj' => $gonderimDurumu == 'gönderildi' ? 'Bildirim başarıyla gönderildi!' : ('Hata: ' . $hataMesaji)
                ];
                break;
                
            case 'yetim_takip_log':
                if(empty($detay)) {
                    throw new Exception("Detay alanı zorunludur!");
                }
                
                if(!$yetimBilgi['hamilik_id']) {
                    throw new Exception("Bu yetime atanmış hamil bulunamadı!");
                }
                
                $logSorgu = $db->prepare("INSERT INTO yetim_takip_loglari SET 
                    yetim_id = ?, 
                    hamil_id = ?, 
                    log_tipi = 'diger', 
                    aciklama = ?, 
                    created_at = NOW()");
                $logSorgu->execute([$yetimId, $yetimBilgi['hamilik_id'], $detay]);
                
                $sonuc = [
                    'durum' => true,
                    'mesaj' => 'Yetim takip loguna başarıyla eklendi!'
                ];
                break;
                
            default:
                throw new Exception("Geçersiz bildirim tipi!");
        }
        
    } catch (Exception $e) {
        $sonuc = [
            'durum' => false,
            'mesaj' => $e->getMessage()
        ];
    }
    
    // İşlem sonucunu session'a yaz ve yönlendir
    if($sonuc['durum']) {
        $_SESSION['yetim_bildirim'] = 'yes';
    } else {
        $_SESSION['yetim_bildirim'] = 'no';
    }
    
    header("Location:../".yonetim."/yetim-listele.html");
    exit();
}

## Yetim Sil ##
if(isset($_GET['yetimsil']))
{
    if($_SESSION['rutbe'] == 0)
    {
        $id = $_GET['id'];
        $sil = $db->prepare("DELETE FROM yetimler WHERE id = ?");
        $sil->execute(array($id));
        
        $_SESSION['yetim_sil'] = 'yes';
        header("Location:../".yonetim."/yetim-listele.html");
        exit();
    }
}

## AI İşlemleri ##
if(isset($_POST['ai_islem']) && ($_POST['ai_islem'] == 1 || $_POST['ai_islem'] == 'ok')) {
    // Güvenlik ve Yetki Kontrolü
    if(!isset($_SESSION['rutbe']) || $_SESSION['rutbe'] != 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Yetkisiz işlem!']);
        exit();
    }

    header('Content-Type: application/json');
    require_once "AI.php";
    $ai = new AI($db);
    $islem_tipi = htmlspecialchars($_POST['islem_tipi'] ?? '');
    
    // 1. RAPOR OLUŞTURMA
    if($islem_tipi == 'rapor_olustur') {
        $yetimId = intval($_POST['yetim_id']);
        $stmt = $db->prepare("SELECT * FROM yetimler WHERE id = ?");
        $stmt->execute([$yetimId]);
        $yetim = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($yetim) {
            $sonuc = $ai->otomatikRaporOlustur($yetim, $yetim['egitim_durumu'], 'Genel sağlık durumu iyi.');
            echo json_encode($sonuc);
        } else {
            echo json_encode(['success' => false, 'message' => 'Yetim bulunamadı']);
        }
    }
    
    // 2. AKILLI İHTİYAÇ ÖNERİSİ
    elseif($islem_tipi == 'ihtiyac_oneri') {
        try {
            $yetimId = intval($_POST['yetim_id']);
            $stmt = $db->prepare("SELECT * FROM yetimler WHERE id = ?");
            $stmt->execute([$yetimId]);
            $yetim = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!$yetim) throw new Exception('Yetim bulunamadı');
            
            $gecmisBagislar = [];
            $stmt2 = $db->prepare("
                SELECT DISTINCT b.adi as bagis_adi 
                FROM bagis_odeme bo 
                LEFT JOIN bagislar b ON bo.bagis_id = b.id 
                LEFT JOIN hamilikler h ON bo.hamilik_id = h.id
                WHERE (bo.yetim_id = ? OR h.yetim_id = ?) AND bo.durum = 'basarili' 
                ORDER BY bo.tarih DESC LIMIT 10
            ");
            $stmt2->execute([$yetimId, $yetimId]);
            $gecmisBagislar = $stmt2->fetchAll(PDO::FETCH_COLUMN) ?: ['Henüz bağış kaydı bulunmuyor'];
            
            echo json_encode($ai->akilliIhtiyacOnerisi($yetim, $gecmisBagislar));
            
        } catch(Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // 3. TOPLU GİYİM EŞLEŞTİRME (REVİZE EDİLDİ)
    elseif($islem_tipi == 'giyim_onerisi') {
        $kiyafetTuru = htmlspecialchars($_POST['kiyafet_turu']);
        $beden = htmlspecialchars($_POST['beden']);
        $cinsiyet = htmlspecialchars($_POST['cinsiyet']);
        $stokAdedi = intval($_POST['stok_adedi'] ?? 10); // Stok sınırı eklendi
        
        $sql = "SELECT id, ad_soyad, dogum_tarihi, cinsiyet, beden_bilgisi, ayak_no, mont_beden, pantolon_beden 
                FROM yetimler WHERE durum = 1";
        $params = [];
        
        if(!empty($cinsiyet)) {
            $sql .= " AND cinsiyet = ?";
            $params[] = $cinsiyet;
        }
        
        if(!empty($beden)) {
            // Dinamik kolon seçimi
            $kolon = ($kiyafetTuru == 'ayakkabi') ? 'ayak_no' : 
                     (($kiyafetTuru == 'pantolon') ? 'pantolon_beden' : 
                     (($kiyafetTuru == 'mont') ? 'mont_beden' : 'beden_bilgisi'));
            
            $sql .= " AND $kolon = ?";
            $params[] = $beden;
        }
        
        // Algoritma: En uzun süredir giyim yardımı almayanlara öncelik verilebilir 
        // (Eğer tabloda yardım tarihi varsa ORDER BY eklenebilir)
        $sql .= " LIMIT " . $stokAdedi;
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $yetimler = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $sonuc = [];
        foreach($yetimler as $yetim) {
            $dogum = new DateTime($yetim['dogum_tarihi']);
            $bugun = new DateTime('today');
            $yas = $dogum->diff($bugun)->y;
            
            $sonuc[] = [
                'id' => $yetim['id'],
                'ad_soyad' => $yetim['ad_soyad'],
                'cinsiyet' => $yetim['cinsiyet'],
                'yas' => $yas,
                'beden' => ($kiyafetTuru == 'ayakkabi') ? $yetim['ayak_no'] : 
                           (($kiyafetTuru == 'pantolon') ? $yetim['pantolon_beden'] : 
                           (($kiyafetTuru == 'mont') ? $yetim['mont_beden'] : $yetim['beden_bilgisi']))
            ];
        }
        
        echo json_encode(['success' => true, 'yetimler' => $sonuc]);
    }

    elseif($islem_tipi == 'risk_analizi') {
        try {
            // Riskli bağışçıları bul
            $riskSorgu = $db->prepare("
                SELECT h.id, b.ad_soyad as bagisci_ad, y.ad_soyad as yetim_ad, MAX(bo.tarih) as son_odeme
                FROM hamilikler h
                JOIN bagiscilar b ON h.bagisci_id = b.id
                JOIN yetimler y ON h.yetim_id = y.id
                LEFT JOIN bagis_odeme bo ON (bo.yetim_id = h.yetim_id OR bo.hamilik_id = h.id) AND bo.durum = 'basarili'
                WHERE h.durum = 'aktif'
                GROUP BY h.id, b.ad_soyad, y.ad_soyad
                HAVING son_odeme < DATE_SUB(NOW(), INTERVAL 30 DAY) OR son_odeme IS NULL
                LIMIT 10
            ");
            $riskSorgu->execute();
            $riskliBagiscilar = $riskSorgu->fetchAll(PDO::FETCH_ASSOC);

            if(count($riskliBagiscilar) > 0) {
                $sonuc = $ai->erkenUyariAnalizi($riskliBagiscilar);
                // JavaScript'te 'content' bekleniyor, 'text' yerine 'content' olarak döndür
                if($sonuc['success']) {
                    $sonuc['content'] = $sonuc['text'];
                    unset($sonuc['text']);
                }
                echo json_encode($sonuc);
            } else {
                echo json_encode(['success' => false, 'message' => 'Riskli bağışçı bulunamadı.']);
            }
        } catch(Exception $e) {
            error_log("Risk Analizi Hatası: " . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Risk analizi sırasında bir hata oluştu: ' . $e->getMessage()
            ]);
        }
    }


    
    exit();
}

## Hamilik Durum Güncelleme ##
if(isset($_POST['islem']) && $_POST['islem'] == 'hami_durum_guncelle') {
    header('Content-Type: application/json');
    
    // Güvenlik kontrolü
    if(!isset($_SESSION['Yonetim_Id']) || !isset($_SESSION['rutbe'])) {
        echo json_encode(['success' => false, 'message' => 'Yetkisiz işlem']);
        exit();
    }
    
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $durum = isset($_POST['durum']) ? $_POST['durum'] : '';
    
    if($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz hamilik ID']);
        exit();
    }
    
    $gecerli_durumlar = ['aktif', 'pasif', 'iptal', 'beklemede'];
    if(!in_array($durum, $gecerli_durumlar)) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz durum']);
        exit();
    }
    
    try {
        $guncelle = $db->prepare("UPDATE hamilikler SET durum = ? WHERE id = ?");
        $sonuc = $guncelle->execute([$durum, $id]);
        
        if($sonuc) {
            echo json_encode(['success' => true, 'message' => 'Hamilik durumu güncellendi']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Güncelleme işlemi başarısız']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
    
    exit();
}

## Hami Durum Güncelleme ##
if(isset($_POST['hami_durum_guncelle']) && $_POST['hami_durum_guncelle'] == 'ok')
{
    header('Content-Type: application/json; charset=utf-8');
    
    $hami_id = intval($_POST['hami_id'] ?? 0);
    $yeni_durum = $_POST['yeni_durum'] ?? '';
    
    if($hami_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz hami ID']);
        exit();
    }
    
    if(!in_array($yeni_durum, ['aktif', 'pasif', 'iptal'])) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz durum']);
        exit();
    }
    
    try {
        // Hami var mı kontrol et
        $kontrol = $db->prepare("SELECT id FROM hamilikler WHERE id = ?");
        $kontrol->execute([$hami_id]);
        
        if($kontrol->rowCount() == 0) {
            echo json_encode(['success' => false, 'message' => 'Hami bulunamadı']);
            exit();
        }
        
        // Durumu güncelle
        $guncelle = $db->prepare("UPDATE hamilikler SET durum = ? WHERE id = ?");
        $sonuc = $guncelle->execute([$yeni_durum, $hami_id]);
        
        if($sonuc) {
            // Log kaydı ekle
            $log = $db->prepare("INSERT INTO sistem_logs SET 
                kullanici_id = ?, 
                islem = ?, 
                aciklama = ?, 
                tarih = NOW(), 
                ip = ?");
            $log->execute([
                $_SESSION['r_id'] ?? 0,
                'hami_durum_guncelle',
                "Hami #{$hami_id} durumu {$yeni_durum} olarak güncellendi",
                cVCLmHLxbS_ip()
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Hami durumu başarıyla güncellendi']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Güncelleme sırasında hata oluştu']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
}

// Sertifika Listesi Getir (Ajax için)
if(isset($_POST['sertifika_listesi_getir'])) {
    header('Content-Type: application/json');
    
    try {
        // DISTINCT veya GROUP BY kullanarak aynı başlığa sahip verilerin tekil gelmesini sağlıyoruz
        // Başlığa göre gruplayarak her başlıktan sadece bir ID getirilir
        $sertifikalar = $db->query("
            SELECT id, baslik, gonderim_yontemi 
            FROM sertifikalar 
            WHERE durum = 1 
            GROUP BY baslik 
            ORDER BY baslik ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'sertifikalar' => $sertifikalar
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
    exit; // İşlem bittikten sonra scriptin devam etmesini engellemek için
}


/* -------------------------------------------------------------------------- */
/* YETİM EKLEME & GÜNCELLEME                        */
/* -------------------------------------------------------------------------- */

// --- YETİM EKLEME VE GÜNCELLEME ---
if (isset($_POST['yetim_ekle']) || isset($_POST['yetim_guncelle'])) {
    
    // Güvenlik Kontrolü
    if(isset($_POST['yetim_ekle'])) {
        cVCLmHLxbS_panelislemkontrol("yetim_ekle");
    } else {
        cVCLmHLxbS_panelislemkontrol("yetim_duzenle");
    }
    
    if($_SESSION['rutbe'] == 0) {
        $isUpdate = isset($_POST['yetim_guncelle']);
        $id = $isUpdate ? intval($_POST['id']) : null;

        // Mevcut verileri çek (Eski dosyaları korumak için)
        $eski_foto = "";
        $eski_video = "";
        $takip_link_hash = "";

        if($isUpdate) {
            $mevcut = $db->prepare("SELECT * FROM yetimler WHERE id = ?");
            $mevcut->execute([$id]);
            $row = $mevcut->fetch(PDO::FETCH_ASSOC);
            if($row) {
                $eski_foto = $row['foto'];
                $eski_video = $row['yetim_video'];
                $takip_link_hash = $row['takip_link_hash'];
            }
        }

        // Form Verilerini Topla
        $ad_soyad         = $_POST['ad_soyad'];
        $saglik_durumu         = $_POST['saglik_durumu'];
        $tc_no            = !empty($_POST['tc_no']) ? $_POST['tc_no'] : null;
        $dogum_tarihi     = (!empty($_POST['dogum_tarihi']) && strlen($_POST['dogum_tarihi']) == 10) ? $_POST['dogum_tarihi'] : null;
        $cinsiyet         = $_POST['cinsiyet'];
        $egitim_durumu    = $_POST['egitim_durumu'];
        $okul_ismi        = $_POST['okul_ismi'] ?? '';
        $beden_bilgisi    = $_POST['beden_bilgisi'];
        $ayak_no          = $_POST['ayak_no'];
        $mont_beden       = $_POST['mont_beden'];
        $pantolon_beden   = $_POST['pantolon_beden'];
        $baba_adi         = $_POST['baba_adi'] ?? '';
        $baba_durum       = $_POST['baba_durum'] ?? '';
        $baba_olum_nedeni = $_POST['baba_olum_nedeni'] ?? '';
        $anne_adi         = $_POST['anne_adi'] ?? '';
        $anne_durum       = $_POST['anne_durum'] ?? '';
        $anne_olum_nedeni = $_POST['anne_olum_nedeni'] ?? '';
        $vasi_yakinlik    = $_POST['vasi_yakinlik'] ?? '';
        $vasi_tel         = $_POST['vasi_tel'] ?? '';
        $kardes_sayisi    = (int)$_POST['kardes_sayisi'];
        $aile_bilgisi     = $_POST['aile_bilgisi'] ?? '';
        $il               = $_POST['il'] ?? '';
        $ilce             = $_POST['ilce'] ?? '';
        $mahalle          = $_POST['mahalle'] ?? '';
        $adres_detay      = $_POST['adres_detay'] ?? '';
        $aile_telefonu    = $_POST['aile_telefonu'] ?? '';
        $yasadigi_bolge   = $_POST['yasadigi_bolge'] ?? '';
        $durum            = isset($_POST['durum']) ? 1 : 0;
        $takip_link_acik  = isset($_POST['takip_link_acik']) ? 1 : 0;
        
        if(empty($takip_link_hash)) {
            $takip_link_hash = md5(uniqid() . time() . $ad_soyad);
        }

        // --- ANA FOTOĞRAF YÜKLEME ---
        $foto = $eski_foto;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $handle = new upload($_FILES['foto']);
            if ($handle->uploaded) {
                $handle->file_new_name_body = 'yetim_' . time();
                $handle->process('../img/yetimler/');
                if ($handle->processed) {
                    if(!empty($eski_foto) && file_exists("../img/yetimler/".$eski_foto)) unlink("../img/yetimler/".$eski_foto);
                    $foto = $handle->file_dst_name;
                }
            }
        }

        // --- VİDEO YÜKLEME ---
        $yetim_video = $eski_video;
        if (isset($_FILES['yetim_video']) && $_FILES['yetim_video']['error'] == 0) {
            $handle = new upload($_FILES['yetim_video']);
            if ($handle->uploaded) {
                $handle->file_new_name_body = 'video_' . time();
                $handle->allowed = array('video/*');
                $handle->process('../img/yetimler/');
                if ($handle->processed) {
                    if(!empty($eski_video) && file_exists("../img/yetimler/".$eski_video)) unlink("../img/yetimler/".$eski_video);
                    $yetim_video = $handle->file_dst_name;
                }
            }
        }

        // SQL SORGU HAZIRLIĞI
        $sql_data = "ad_soyad=?,saglik_durumu=?, tc_no=?, dogum_tarihi=?, 
            cinsiyet=?, egitim_durumu=?, okul_ismi=?, 
            baba_adi=?, baba_durum=?, baba_olum_nedeni=?,
            anne_adi=?, anne_durum=?, anne_olum_nedeni=?, 
            vasi_yakinlik=?, vasi_tel=?, 
            kardes_sayisi=?, aile_bilgisi=?, il=?, 
            ilce=?, mahalle=?,    adres_detay=?, 
            aile_telefonu=?, beden_bilgisi=?, ayak_no=?, 
            mont_beden=?, pantolon_beden=?,
            durum=?, paylasim_izni=?, foto=?, yetim_video=?, takip_link_hash=?, takip_link_acik=?";
        
        $params = [
            $ad_soyad,$saglik_durumu, $tc_no, $dogum_tarihi, $cinsiyet, $egitim_durumu, $okul_ismi, 
            $baba_adi, $baba_durum, $baba_olum_nedeni, $anne_adi, $anne_durum, $anne_olum_nedeni, 
            $vasi_yakinlik, $vasi_tel, $kardes_sayisi, $aile_bilgisi, $il, $ilce, $mahalle, $adres_detay,
            $aile_telefonu, $beden_bilgisi, $ayak_no, $mont_beden, $pantolon_beden,
            $durum, $paylasim_izni, $foto, $yetim_video, $takip_link_hash, $takip_link_acik
        ];

        if($isUpdate) {
            $query = $db->prepare("UPDATE yetimler SET $sql_data WHERE id = ?");
            $params[] = $id;
            $islem_sonuc = $query->execute($params);
            $last_id = $id;
        } else {
            $query = $db->prepare("INSERT INTO yetimler SET $sql_data");
            $islem_sonuc = $query->execute($params);
            $last_id = $db->lastInsertId();
        }

        if ($islem_sonuc) {
            // --- 1. KARDEŞLERİ KAYDET ---
            $db->prepare("DELETE FROM yetim_kardesler WHERE yetim_id = ?")->execute([$last_id]);
            if (isset($_POST['kardes_ad']) && is_array($_POST['kardes_ad'])) {
                foreach ($_POST['kardes_ad'] as $key => $k_ad) {
                    if (!empty($k_ad)) {
                        $k_dogum = (!empty($_POST['kardes_dogum'][$key]) && strlen($_POST['kardes_dogum'][$key]) == 10) ? $_POST['kardes_dogum'][$key] : null;
                        $k_sorgu = $db->prepare("INSERT INTO yetim_kardesler SET yetim_id=?, ad_soyad=?, dogum_tarihi=?, cinsiyet=?, egitim_durumu=?");
                        $k_sorgu->execute([$last_id, $k_ad, $k_dogum, $_POST['kardes_cinsiyet'][$key], $_POST['kardes_egitim'][$key]]);
                    }
                }
            }

            // --- 2. EK DOSYALARI KAYDET (Çoklu Fotoğraf) ---
            if (isset($_FILES['ek_dosyalar']) && !empty($_FILES['ek_dosyalar']['name'][0])) {
                $files = array();
                foreach ($_FILES['ek_dosyalar'] as $k => $l) {
                    foreach ($l as $i => $v) { $files[$i][$k] = $v; }
                }

                foreach ($files as $file) {
                    $handle = new upload($file);
                    if ($handle->uploaded) {
                        $handle->file_new_name_body = 'extra_' . uniqid() . '_' . time();
                        $handle->process('../img/yetimler/');
                        if ($handle->processed) {
                            $stmt_file = $db->prepare("INSERT INTO yetim_dosyalar (yetim_id, view_path) VALUES (?,?)");
                            $stmt_file->execute([$last_id, $handle->file_dst_name]);
                        }
                    }
                }
            }

            if($isUpdate) $_SESSION['yetim_guncelle'] = 'ok'; else $_SESSION['yetim_ekle'] = 'ok';
            header("Location:".$_SERVER['HTTP_REFERER']);
        } else {
            if($isUpdate) $_SESSION['yetim_guncelle'] = 'no'; else $_SESSION['yetim_ekle'] = 'no';
            header("Location:".$_SERVER['HTTP_REFERER']);
        }
        exit;
    }
}

// --- YETİM DOSYA/RESİM/VİDEO SİLME (AJAX) ---
if (isset($_POST['yetim_dosya_sil'])) {
    $id = intval($_POST['id']);
    $tip = $_POST['tip']; // ana_foto, video, ek_dosya
    $dosya_adi = $_POST['dosya'];
    $dosya_yolu = "../img/yetimler/" . $dosya_adi;

    if ($tip == "ana_foto") {
        $sorgu = $db->prepare("UPDATE yetimler SET foto = '' WHERE id = ?");
        $sonuc = $sorgu->execute([$id]);
    } elseif ($tip == "video") {
        $sorgu = $db->prepare("UPDATE yetimler SET yetim_video = '' WHERE id = ?");
        $sonuc = $sorgu->execute([$id]);
    } elseif ($tip == "ek_dosya") {
        $sorgu = $db->prepare("DELETE FROM yetim_dosyalar WHERE id = ?");
        $sonuc = $sorgu->execute([$id]);
    }

    if ($sonuc) {
        if (file_exists($dosya_yolu) && !empty($dosya_adi)) {
            unlink($dosya_yolu);
        }
        echo "ok";
    } else {
        echo "error";
    }
    exit;
}

// Sertifika Kaydet/Düzenle
if(isset($_POST['sertifika_kaydet'])) {
    $id = intval($_POST['sertifika_id'] ?? 0);
    $baslik = strip_tags($_POST['baslik'] ?? '');
    $aciklama = trim($_POST['aciklama'] ?? '');
    $gonderim_yontemi = $_POST['gonderim_yontemi'] ?? '';
    $durum = isset($_POST['durum']) ? 1 : 0;
    
    if(empty($aciklama) || empty($gonderim_yontemi)) {
        $_SESSION['mesaj'] = 'Lütfen tüm zorunlu alanları doldurunuz!';
        $_SESSION['mesaj_tur'] = 'danger';
        header("Location:../akd-yonetim/sertifikalar.html");
        exit();
    }
    
    if($gonderim_yontemi == 'email' && empty($baslik)) {
        $_SESSION['mesaj'] = 'E-posta gönderimi için başlık zorunludur!';
        $_SESSION['mesaj_tur'] = 'danger';
        header("Location:../akd-yonetim/sertifikalar.html");
        exit();
    }
    
    try {
        if($id > 0) {
            // Güncelleme
            $guncelle = $db->prepare("UPDATE sertifikalar SET 
                baslik = ?, 
                aciklama = ?, 
                gonderim_yontemi = ?, 
                durum = ?,
                updated_at = NOW()
                WHERE id = ?");
            $sonuc = $guncelle->execute([$baslik, $aciklama, $gonderim_yontemi, $durum, $id]);
            $islem = 'güncellendi';
        } else {
            // Yeni kayıt
            $ekle = $db->prepare("INSERT INTO sertifikalar SET 
                baslik = ?, 
                aciklama = ?, 
                gonderim_yontemi = ?, 
                durum = ?, 
                created_at = NOW()");
            $sonuc = $ekle->execute([$baslik, $aciklama, $gonderim_yontemi, $durum]);
            $islem = 'eklendi';
        }
        
        if($sonuc) {
            $_SESSION['mesaj'] = "Sertifika başarıyla {$islem}!";
            $_SESSION['mesaj_tur'] = 'success';
        } else {
            $_SESSION['mesaj'] = 'İşlem sırasında hata oluştu!';
            $_SESSION['mesaj_tur'] = 'danger';
        }
        
    } catch (Exception $e) {
        $_SESSION['mesaj'] = 'Veritabanı hatası: ' . $e->getMessage();
        $_SESSION['mesaj_tur'] = 'danger';
    }
    
    header("Location:../akd-yonetim/sertifikalar.html");
    exit();
}

// Sertifika Sil
if(isset($_GET['sertifika_sil'])) {
    $id = intval($_GET['sertifika_sil']);
    
    if($id > 0) {
        try {
            // Önce bu sertifikayla gönderilmiş bildirim var mı kontrol et
            $kontrol = $db->prepare("SELECT COUNT(*) FROM sponsor_bildirimleri WHERE sertifika_id = ?");
            $kontrol->execute([$id]);
            $bildirim_sayisi = $kontrol->fetchColumn();
            
            if($bildirim_sayisi > 0) {
                $_SESSION['mesaj'] = 'Bu sertifika kullanıldığı için silinemez!';
                $_SESSION['mesaj_tur'] = 'warning';
            } else {
                $sil = $db->prepare("DELETE FROM sertifikalar WHERE id = ?");
                $sonuc = $sil->execute([$id]);
                
                if($sonuc) {
                    $_SESSION['mesaj'] = 'Sertifika başarıyla silindi!';
                    $_SESSION['mesaj_tur'] = 'success';
                } else {
                    $_SESSION['mesaj'] = 'Silme işlemi sırasında hata oluştu!';
                    $_SESSION['mesaj_tur'] = 'danger';
                }
            }
            
        } catch (Exception $e) {
            $_SESSION['mesaj'] = 'Veritabanı hatası: ' . $e->getMessage();
            $_SESSION['mesaj_tur'] = 'danger';
        }
    }
    
    header("Location:../akd-yonetim/sertifikalar.html");
    exit();
}

// Sponsor Bildirim Gönder
if(isset($_POST['sponsor_bildirim_gonder'])) {
    $bagisci_id = intval($_POST['bagisci_id'] ?? 0);
    $sertifika_id = intval($_POST['sertifika_id'] ?? 0);
    $gonderim_yontemi = $_POST['gonderim_yontemi'] ?? '';
    
    if($bagisci_id <= 0 || $sertifika_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz parametreler']);
        exit();
    }
    
    try {
        // Bağışçı bilgilerini al
        $bagisci = $db->prepare("SELECT * FROM bagis_odeme WHERE id = ? AND durum = 'basarili'");
        $bagisci->execute([$bagisci_id]);
        $bagisci_bilgi = $bagisci->fetch(PDO::FETCH_ASSOC);
        
        if(!$bagisci_bilgi) {
            echo json_encode(['success' => false, 'message' => 'Bağışçı bulunamadı']);
            exit();
        }
        
        // Sertifika bilgilerini al
        $sertifika = $db->prepare("SELECT * FROM sertifikalar WHERE id = ? AND durum = 1");
        $sertifika->execute([$sertifika_id]);
        $sertifika_bilgi = $sertifika->fetch(PDO::FETCH_ASSOC);
        
        if(!$sertifika_bilgi) {
            echo json_encode(['success' => false, 'message' => 'Sertifika bulunamadı veya pasif']);
            exit();
        }
        
        // Değişkenleri değiştir
        $degiskenler = ['{ad}', '{soyad}', '{tutar}', '{tarih}', '{yetim_ad}'];
        $degerler = [
            $bagisci_bilgi['ad'], 
            $bagisci_bilgi['soyad'], 
            $bagisci_bilgi['tutar'], 
            date('d.m.Y H:i'),
            $bagisci_bilgi['yetim_adi'] ?? 'Belirtilmemiş'
        ];
        
        $mesaj = str_replace($degiskenler, $degerler, $sertifika_bilgi['aciklama']);
        
        $gonderim_durumu = 'beklemede';
        $hata_mesaji = null;
        
        // Bildirimi gönder
        if($gonderim_yontemi == 'email') {
            $mail_gonder = mailgonder(
                $degiskenler, 
                $degerler, 
                $mesaj, 
                $bagisci_bilgi['email'], 
                $sertifika_bilgi['baslik'], 
                $mesaj
            );
            $gonderim_durumu = $mail_gonder ? 'gönderildi' : 'hata';
            if(!$mail_gonder) $hata_mesaji = 'E-posta gönderilemedi';
        } else {
            $sms_gonder = smsgonder(
                $degiskenler, 
                $degerler, 
                $mesaj, 
                $bagisci_bilgi['telefon'], 
                $mesaj
            );
            $gonderim_durumu = $sms_gonder ? 'gönderildi' : 'hata';
            if(!$sms_gonder) $hata_mesaji = 'SMS gönderilemedi';
        }
        
        // Bildirim kaydını oluştur
        $kaydet = $db->prepare("INSERT INTO sponsor_bildirimleri SET 
            bagisci_id = ?, 
            sertifika_id = ?, 
            gonderim_yontemi = ?, 
            gonderim_durumu = ?, 
            gonderim_zamani = NOW(),
            hata_mesaji = ?");
        $kaydet->execute([$bagisci_id, $sertifika_id, $gonderim_yontemi, $gonderim_durumu, $hata_mesaji]);
        
        echo json_encode([
            'success' => $gonderim_durumu == 'gönderildi', 
            'message' => $gonderim_durumu == 'gönderildi' ? 'Bildirim başarıyla gönderildi' : 'Bildirim gönderilemedi'
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
    exit();
}

## Dosya Yönetimi — Ekle ##
if(isset($_POST['dosya_ekle']))
{
    cVCLmHLxbS_panelislemkontrol("dosya_ekle");
    if($_SESSION['rutbe'] == 0)
    {
        $sira      = intval($_POST['sira']);
        $baslik    = $_POST['baslik'];
        $kategori  = $_POST['kategori'];
        $aciklama  = $_POST['aciklama'];
        $durum     = isset($_POST['durum']) ? 1 : 0;
        $tarih     = cVCLmHLxbS_tr_tarih(date('Y-m-d H:i:s'));

        $dosyaAdi     = '';
        $orijinalAd   = '';
        $dosyaBoyut   = '';
        $dosyaTip     = '';

        if(isset($_FILES['dosya']) && $_FILES['dosya']['error'] === 0) {
            $izinliEkler = ['pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar','txt'];
            $ext = strtolower(pathinfo($_FILES['dosya']['name'], PATHINFO_EXTENSION));
            if(in_array($ext, $izinliEkler) && $_FILES['dosya']['size'] <= 20971520) {
                $uploadDir = '../uploads/dosyalar/';
                if(!is_dir($uploadDir)) { @mkdir($uploadDir, 0755, true); }
                $yeniAd = time() . '_' . rand(1000,9999) . '.' . $ext;
                if(move_uploaded_file($_FILES['dosya']['tmp_name'], $uploadDir . $yeniAd)) {
                    $dosyaAdi   = $yeniAd;
                    $orijinalAd = $_FILES['dosya']['name'];
                    $boyutByte  = $_FILES['dosya']['size'];
                    if($boyutByte >= 1048576)      $dosyaBoyut = round($boyutByte/1048576, 2) . ' MB';
                    elseif($boyutByte >= 1024)     $dosyaBoyut = round($boyutByte/1024, 1) . ' KB';
                    else                           $dosyaBoyut = $boyutByte . ' B';
                    $dosyaTip = strtoupper($ext);
                }
            }
        }

        // Dosya seçilmediyse ekleme yapma
        if(empty($dosyaAdi)) {
            $_SESSION['dosya_ekle'] = 'no';
            header("Location:../".yonetim."/dosya-ekle.html");
            exit();
        }

        $sorgu = $db->prepare("INSERT INTO dosyalar SET
            sira = ?, baslik = ?, kategori = ?, aciklama = ?,
            dosya = ?, orijinal_ad = ?, dosya_boyut = ?, dosya_tip = ?,
            durum = ?, dil = ?, tarih = ?");
        $Ekle = $sorgu->execute(array(
            $sira, $baslik, $kategori, $aciklama,
            $dosyaAdi, $orijinalAd, $dosyaBoyut, $dosyaTip,
            $durum, $_SESSION['admin_dil'], $tarih
        ));

        if($Ekle) {
            $_SESSION['dosya_ekle'] = 'yes';
            header("Location:../".yonetim."/dosya-listele.html");
        } else {
            $_SESSION['dosya_ekle'] = 'no';
            header("Location:../".yonetim."/dosya-ekle.html");
        }
        exit();
    }
    $_SESSION['dosya_ekle'] = 'demo';
    header("Location:../".yonetim."/dosya-ekle.html");
    exit();
}

## Dosya Yönetimi — Güncelle ##
if(isset($_POST['dosya_guncelle']))
{
    cVCLmHLxbS_panelislemkontrol("dosya_guncelle");
    if($_SESSION['rutbe'] == 0)
    {
        $id        = intval($_POST['id']);
        $sira      = intval($_POST['sira']);
        $baslik    = $_POST['baslik'];
        $kategori  = $_POST['kategori'];
        $aciklama  = $_POST['aciklama'];
        $durum     = isset($_POST['durum']) ? 1 : 0;

        // Mevcut kayıt
        $mevcut = $db->prepare("SELECT * FROM dosyalar WHERE id = ?");
        $mevcut->execute(array($id));
        $eskiKayit = $mevcut->fetch(PDO::FETCH_ASSOC);

        $dosyaAdi   = $eskiKayit['dosya'];
        $orijinalAd = $eskiKayit['orijinal_ad'];
        $dosyaBoyut = $eskiKayit['dosya_boyut'];
        $dosyaTip   = $eskiKayit['dosya_tip'];

        if(isset($_FILES['dosya']) && $_FILES['dosya']['error'] === 0) {
            $izinliEkler = ['pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar','txt'];
            $ext = strtolower(pathinfo($_FILES['dosya']['name'], PATHINFO_EXTENSION));
            if(in_array($ext, $izinliEkler) && $_FILES['dosya']['size'] <= 20971520) {
                $uploadDir = '../uploads/dosyalar/';
                if(!is_dir($uploadDir)) { @mkdir($uploadDir, 0755, true); }
                $yeniAd = time() . '_' . rand(1000,9999) . '.' . $ext;
                if(move_uploaded_file($_FILES['dosya']['tmp_name'], $uploadDir . $yeniAd)) {
                    // Eski dosyayı sil
                    if($eskiKayit['dosya'] && file_exists('../uploads/dosyalar/'.$eskiKayit['dosya'])) {
                        @unlink('../uploads/dosyalar/'.$eskiKayit['dosya']);
                    }
                    $dosyaAdi   = $yeniAd;
                    $orijinalAd = $_FILES['dosya']['name'];
                    $boyutByte  = $_FILES['dosya']['size'];
                    if($boyutByte >= 1048576)      $dosyaBoyut = round($boyutByte/1048576, 2) . ' MB';
                    elseif($boyutByte >= 1024)     $dosyaBoyut = round($boyutByte/1024, 1) . ' KB';
                    else                           $dosyaBoyut = $boyutByte . ' B';
                    $dosyaTip = strtoupper($ext);
                }
            }
        }

        $sorgu = $db->prepare("UPDATE dosyalar SET
            sira = ?, baslik = ?, kategori = ?, aciklama = ?,
            dosya = ?, orijinal_ad = ?, dosya_boyut = ?, dosya_tip = ?,
            durum = ?
            WHERE id = ?");
        $Guncelle = $sorgu->execute(array(
            $sira, $baslik, $kategori, $aciklama,
            $dosyaAdi, $orijinalAd, $dosyaBoyut, $dosyaTip,
            $durum, $id
        ));

        if($Guncelle) {
            $_SESSION['dosya_guncelle'] = 'yes';
        } else {
            $_SESSION['dosya_guncelle'] = 'no';
        }
        header("Location:".$_SERVER['HTTP_REFERER']."");
        exit();
    }
    $_SESSION['dosya_guncelle'] = 'demo';
    header("Location:".$_SERVER['HTTP_REFERER']."");
    exit();
}

## Dosya Yönetimi — Durum Değiştir (AJAX) ##
if(isset($_POST['dosya_durum']))
{
    cVCLmHLxbS_panelislemkontrol("dosya_durum");
    if($_SESSION['rutbe'] == 0)
    {
        $id    = intval($_POST['id']);
        $durum = intval($_POST['durum']);
        $sorgu = $db->prepare("UPDATE dosyalar SET durum = ? WHERE id = ?");
        if($sorgu->execute(array($durum, $id))) {
            echo json_encode(['durum'=>'success','mesaj'=>'Durum güncellendi.']);
        } else {
            echo json_encode(['durum'=>'error','mesaj'=>'Hata oluştu.']);
        }
    } else {
        echo json_encode(['durum'=>'error','mesaj'=>'Yetkisiz işlem.']);
    }
    exit();
}

## Dosya Yönetimi — Sil (GET) ##
if(isset($_GET['dosya_sil']) && $_GET['dosya_sil'] == 'ok')
{
    cVCLmHLxbS_panelislemkontrol("dosya_sil");
    if($_SESSION['rutbe'] == 0)
    {
        $id = intval($_GET['id']);
        $kayit = $db->prepare("SELECT dosya FROM dosyalar WHERE id = ?");
        $kayit->execute(array($id));
        $r = $kayit->fetch(PDO::FETCH_ASSOC);
        if($r && $r['dosya'] && file_exists('../uploads/dosyalar/'.$r['dosya'])) {
            @unlink('../uploads/dosyalar/'.$r['dosya']);
        }
        $sorgu = $db->prepare("DELETE FROM dosyalar WHERE id = ?");
        if($sorgu->execute(array($id))) {
            $_SESSION['dosya_sil'] = 'yes';
        } else {
            $_SESSION['dosya_sil'] = 'no';
        }
        header("Location:".$_SERVER['HTTP_REFERER']."");
        exit();
    }
    $_SESSION['dosya_sil'] = 'demo';
    header("Location:".$_SERVER['HTTP_REFERER']."");
    exit();
}

## Dosya Yönetimi — Toplu Sil (AJAX POST) ##
if(isset($_POST['dosya_toplu_sil']))
{
    cVCLmHLxbS_panelislemkontrol("dosya_toplu_sil");
    if($_SESSION['rutbe'] == 0 && !empty($_POST['id']) && is_array($_POST['id']))
    {
        $silinen = 0;
        foreach($_POST['id'] as $id) {
            $id = intval($id);
            if($id > 0) {
                $kayit = $db->prepare("SELECT dosya FROM dosyalar WHERE id = ?");
                $kayit->execute(array($id));
                $r = $kayit->fetch(PDO::FETCH_ASSOC);
                if($r && $r['dosya'] && file_exists('../uploads/dosyalar/'.$r['dosya'])) {
                    @unlink('../uploads/dosyalar/'.$r['dosya']);
                }
                $sorgu = $db->prepare("DELETE FROM dosyalar WHERE id = ?");
                if($sorgu->execute(array($id))) $silinen++;
            }
        }
        echo json_encode(['durum'=>'success','mesaj'=>$silinen.' dosya silindi.']);
    } else {
        echo json_encode(['durum'=>'error','mesaj'=>'Yetkisiz işlem.']);
    }
    exit();
}

##Test Sonucu Sil##
if(@$_GET['test_sonuc_sil'] == "ok")
{
	if($_SESSION['rutbe'] == 0)
	{
		$TSorgu = $db->prepare("DELETE FROM test_sonuclari WHERE id = :id");
		$TSorgu->execute(array('id' => $_GET['id']));
		if($TSorgu)
		{
			header("Location:../".yonetim."/test-sonuclari.html?test_sonuc_sil=1");
			exit();
		}
		else
		{
			header("Location:../".yonetim."/test-sonuclari.html?test_sonuc_sil=2");
			exit();
		}
	}
}

##Test Sonucu Toplu Sil##
if(isset($_POST['test_sonuc_sil_secili']))
{
	if($_SESSION['rutbe'] == 0)
	{
		if(isset($_POST['id']) && is_array($_POST['id']) && count($_POST['id']) > 0)
		{
			foreach($_POST['id'] as $i)
			{
				$db->prepare("DELETE FROM test_sonuclari WHERE id = ?")->execute(array($i));
			}
			header("Location:../".yonetim."/test-sonuclari.html?test_sonuc_sil_secili=1");
			exit();
		}
		else
		{
			header("Location:../".yonetim."/test-sonuclari.html?secim=3");
			exit();
		}
	}
}

##Test Sonucu Tümünü Sil##
if(@$_GET['test_sonuc_tumunu_sil'] == "ok")
{
	if($_SESSION['rutbe'] == 0)
	{
		$db->query("DELETE FROM test_sonuclari");
		header("Location:../".yonetim."/test-sonuclari.html?test_sonuc_tumunu_sil=1");
		exit();
	}
}

##Test Kutuları Güncelle##
if(isset($_POST['test_kutulari_guncelle']))
{
	if($_SESSION['rutbe'] == 0)
	{
		$kutuIds = $_POST['kutu_id'] ?? array();
		$kutuHarfler = $_POST['kutu_harf'] ?? array();
		$kutuMaddeler = $_POST['kutu_maddeler'] ?? array();
		$kutuTipler = $_POST['kutu_tip'] ?? array();

		$basarili = true;
		foreach($kutuIds as $kid) {
			$harf = strtoupper(trim($kutuHarfler[$kid] ?? ''));
			$maddeler = trim($kutuMaddeler[$kid] ?? '');
			$tip = intval($kutuTipler[$kid] ?? 0);

			if(empty($maddeler)) continue;

			$stmt = $db->prepare("UPDATE test_kutulari SET harf = ?, maddeler = ?, enneagram_tipi = ? WHERE id = ?");
			if(!$stmt->execute(array($harf, $maddeler, $tip, intval($kid)))) {
				$basarili = false;
			}
		}

		if($basarili) {
			header("Location:../".yonetim."/test-sorulari.html?test_kutulari_guncelle=1");
		} else {
			header("Location:../".yonetim."/test-sorulari.html?test_kutulari_guncelle=2");
		}
		exit();
	}
}

##Test Kutu Ekle##
if(isset($_POST['test_kutu_ekle']))
{
	if($_SESSION['rutbe'] == 0)
	{
		$test_id = intval($_POST['test_id']);
		$adim = intval($_POST['yeni_adim']);
		$harf = strtoupper(trim($_POST['yeni_harf'] ?? ''));
		$tip = intval($_POST['yeni_tip']);
		$sira = intval($_POST['yeni_sira']);
		$maddeler = trim($_POST['yeni_maddeler']);

		$stmt = $db->prepare("INSERT INTO test_kutulari (test_id, adim, harf, maddeler, enneagram_tipi, sira) VALUES (?, ?, ?, ?, ?, ?)");
		$sonuc = $stmt->execute(array($test_id, $adim, $harf, $maddeler, $tip, $sira));

		if($sonuc) {
			header("Location:../".yonetim."/test-sorulari.html?test_kutu_ekle=1");
		} else {
			header("Location:../".yonetim."/test-sorulari.html?test_kutu_ekle=2");
		}
		exit();
	}
}

##Test Ayarları Güncelle##
if(isset($_POST['test_ayar_guncelle']))
{
	if($_SESSION['rutbe'] == 0)
	{
		$id = intval($_POST['id']);
		$adi = $_POST['adi'];
		$aciklama = $_POST['aciklama'];
		$giris_metni = $_POST['giris_metni'];
		$sonuc_metni = $_POST['sonuc_metni'];

		$stmt = $db->prepare("UPDATE testler SET adi = ?, aciklama = ?, giris_metni = ?, sonuc_metni = ? WHERE id = ?");
		$sonuc = $stmt->execute(array($adi, $aciklama, $giris_metni, $sonuc_metni, $id));

		if($sonuc) {
			header("Location:../".yonetim."/test-ayarlari.html?test_ayar_guncelle=1");
		} else {
			header("Location:../".yonetim."/test-ayarlari.html?test_ayar_guncelle=2");
		}
		exit();
	}
}

##Test Mizaç Tipleri Güncelle##
if(isset($_POST['test_tipleri_guncelle']))
{
	if($_SESSION['rutbe'] == 0)
	{
		$tipIds = $_POST['tip_id'] ?? array();
		$tipMizac = $_POST['tip_mizac'] ?? array();
		$tipBaslik = $_POST['tip_baslik'] ?? array();
		$tipEtiket = $_POST['tip_etiket'] ?? array();
		$tipRenk = $_POST['tip_renk'] ?? array();
		$tipAciklama = $_POST['tip_aciklama'] ?? array();

		$basarili = true;
		foreach($tipIds as $tid) {
			$stmt = $db->prepare("UPDATE test_mizac_tipleri SET mizac_tipi = ?, baslik = ?, etiket = ?, aciklama = ?, renk = ? WHERE id = ?");
			if(!$stmt->execute(array(
				intval($tipMizac[$tid] ?? 0),
				trim($tipBaslik[$tid] ?? ''),
				trim($tipEtiket[$tid] ?? ''),
				trim($tipAciklama[$tid] ?? ''),
				trim($tipRenk[$tid] ?? '#333333'),
				intval($tid)
			))) {
				$basarili = false;
			}
		}

		if($basarili) {
			header("Location:../".yonetim."/test-ayarlari.html?test_tipleri_guncelle=1");
		} else {
			header("Location:../".yonetim."/test-ayarlari.html?test_tipleri_guncelle=2");
		}
		exit();
	}
}

##Test Mizaç Tipi Ekle##
if(isset($_POST['test_tip_ekle']))
{
	if($_SESSION['rutbe'] == 0)
	{
		$test_id = intval($_POST['test_id']);
		$mizac_tipi = intval($_POST['yeni_mizac_tipi']);
		$baslik = trim($_POST['yeni_baslik']);
		$etiket = trim($_POST['yeni_etiket'] ?? '');
		$renk = trim($_POST['yeni_renk'] ?? '#333333');
		$aciklama = trim($_POST['yeni_aciklama']);

		$stmt = $db->prepare("INSERT INTO test_mizac_tipleri (test_id, mizac_tipi, baslik, etiket, aciklama, renk) VALUES (?, ?, ?, ?, ?, ?)");
		$sonuc = $stmt->execute(array($test_id, $mizac_tipi, $baslik, $etiket, $aciklama, $renk));

		if($sonuc) {
			header("Location:../".yonetim."/test-ayarlari.html?test_tip_ekle=1");
		} else {
			header("Location:../".yonetim."/test-ayarlari.html?test_tip_ekle=2");
		}
		exit();
	}
}

ob_end_flush();
?>