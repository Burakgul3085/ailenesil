<?php

ob_start();
session_start();
require_once "baglan.php";
require_once "fonksiyon.php";
require_once('class.upload.php');
require_once("class.phpmailer.php");
$dil_dosya = __DIR__ . "/../language/dil_".($_SESSION['k_dil'] ?? '1').".php";
if(file_exists($dil_dosya)) {
    require_once($dil_dosya);
} else {
    $dil = [];
}
$logo	= url.tema.'/uploads/logo/footer/'.footerlogo;
$domain_bilgi	= url;

if($moduller['alan20'] == "1"){
	$html = ".html";
}
else
{
	$html = "";
}


cemetery_f();

## Aidat Sorgulama ##
if(isset($_POST['aidatsorgubtn']))
{	
	$tc 			= nl2br(strip_tags($_POST['tc'], "<b><p><i>"));

	if (strlen($_POST['kontrol']) > 0)
	{
		header("Location:../".$htc['aidaturl'].$html."");
		die();
	}
	
	if(empty($tc))
	{
		$_SESSION['aidatsorgubtn'] = 'bos';
		header("Location:../".$htc['aidaturl'].$html."");
		exit();
	}
	else
	{
		$varmi = $db->prepare("SELECT * FROM aidatlar WHERE BINARY tc = ?");
		$varmi->execute(array($tc));
		if($varmi->rowCount())
		{
			$ASonuc = $varmi->fetch(PDO::FETCH_ASSOC);
			$_SESSION['aidat_TC'] 	= $ASonuc['tc'];

			header("Location:../".$htc['aidatlisteurl'].$html."");
			exit();
		}
		else
		{
			$_SESSION['aidatsorgubtn'] = 'no';
			header("Location:../".$htc['aidaturl'].$html."");
			exit();
		}
	}
		
}

## Mesaj Oluştur ##
if(isset($_POST['mesajbtn']))
{	
	$isim 		= nl2br(strip_tags($_POST['isim'], "<b><p><i>"));
	$email 		= nl2br(strip_tags($_POST['email'], "<b><p><i>"));
	$konu 		= nl2br(strip_tags($_POST['konu'], "<b><p><i>"));		
	$telefon 	= nl2br(strip_tags($_POST['telefon'], "<b><p><i>"));	
	$mesaj 		= nl2br(strip_tags($_POST['mesaj'], "<b><p><i>"));		
	$iletisimurl= nl2br(strip_tags($_POST['iletisimurl'], "<b><p><i>"));	
	$tarih		= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
	$bgntarih	= cVCLmHLxbS_tr_tarih('Y-m-d');
	$buguntarih = strtotime($bgntarih);
	$ip			= cVCLmHLxbS_ip();

	if($ayar["demo"] == 0)
	{
		if (strlen($_POST['kontrol']) > 0)
		{
			header("Location:".$iletisimurl."");
			die();
		}
		
		$sablon 		= $db->query("SELECT * FROM bildirim_sablonu WHERE id = '1'")->fetch(PDO::FETCH_ASSOC);
		$gelendegisken 	= explode(",", $sablon['degiskenler']);		
		$yenitarih		= cVCLmHLxbS_tarih($tarih);
		$gidendegisken	= [$isim,$konu,$email,$telefon,$mesaj,$yenitarih,$ip,$logo,$domain_bilgi];

		if(empty($isim) || empty($email) || empty($mesaj))
		{
			$_SESSION['mesajbtn'] = 'bos';
			header("Location:".$iletisimurl."");
		}
		else
		{
			$sorgu = $db->prepare("INSERT INTO mesajlar SET
				isim		= :isim,
				email 		= :email,
				konu 		= :konu,
				telefon 	= :telefon,
				mesaj		= :mesaj,
				ip			= :ip,
				tarih		= :tarih,
				buguntarih 	= :buguntarih");
			$Ekle = $sorgu->execute(array(
				'isim' 		=> $isim,
				'email' 		=> $email,
				'konu' 		=> $konu,
				'telefon' 	=> $telefon,
				'mesaj' 		=> $mesaj,
				'ip'		=> $ip,
				'tarih'		=> $tarih,
				'buguntarih'=> $buguntarih
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
					'baslik' 	=> "Yeni Mesaj",
					'icon' 		=> "icon-envelope-open",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$isim."</strong> mesaj gönderdi.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				
				bildirim_sablon_gonder(1, [
					'adsoyad' => $isim,
					'mesajkonu' => $konu,
					'email' => $email,
					'telefon' => $telefon,
					'mesaj' => $mesaj
				], $email, $telefon);
$_SESSION['swal_contact'] = [
    'type' => 'success',
    'title' => 'Başarılı',
    'message' => 'Mesajınız başarıyla gönderildi.'
];

$separator = (strpos($iletisimurl, '?') !== false) ? '&' : '?';
header("Location: " . $iletisimurl . $separator . "contact=ok");
exit;

			}
			else
			{
				$_SESSION['mesajbtn'] = 'no';
				header("Location:".$iletisimurl."");
			}			
		}
	}
	else
	{
		$_SESSION['sitedemo'] = 'no';
		header("Location:".$iletisimurl."");
	}
}


## Program Talep Kaydet ##
if(isset($_POST['islem']) && $_POST['islem'] == 'program_talep')
{
	header('Content-Type: application/json; charset=utf-8');
	
	$ad_soyad 		= nl2br(strip_tags($_POST['ad_soyad'], "<b><p><i>"));
	$okul_isletme 	= nl2br(strip_tags($_POST['okul_isletme'] ?? '', "<b><p><i>"));
	$email 			= nl2br(strip_tags($_POST['email'], "<b><p><i>"));
	$telefon 		= nl2br(strip_tags($_POST['telefon'] ?? '', "<b><p><i>"));
	$konum 			= nl2br(strip_tags($_POST['konum'], "<b><p><i>"));
	$mesaj 			= nl2br(strip_tags($_POST['mesaj'] ?? '', "<b><p><i>"));
	$program_id 	= (int)$_POST['program_id'];
	$program_baslik = nl2br(strip_tags($_POST['program_baslik'], "<b><p><i>"));
	
	$tarih			= cVCLmHLxbS_tr_tarih('Y-m-d H:i:s');
	$bgntarih		= cVCLmHLxbS_tr_tarih('Y-m-d');
	$buguntarih 	= strtotime($bgntarih);
	$ip				= cVCLmHLxbS_ip();

	if($ayar["demo"] == 0)
	{
		// Validasyon
		if(empty($ad_soyad) || empty($email) || empty($konum))
		{
			echo json_encode([
				'success' => false,
				'message' => 'Lütfen zorunlu alanları doldurunuz.'
			]);
			exit;
		}
		
		// Email validasyonu - Esnek Regex
		if(!preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $email))
		{
			echo json_encode([
				'success' => false,
				'message' => 'Geçerli bir e-posta adresi giriniz.'
			]);
			exit;
		}
		
		// Veritabanına kaydet
		$sorgu = $db->prepare("INSERT INTO okullar_program_talep SET
			program_id		= :program_id,
			program_baslik	= :program_baslik,
			ad_soyad		= :ad_soyad,
			okul_isletme	= :okul_isletme,
			email			= :email,
			telefon			= :telefon,
			konum			= :konum,
			mesaj			= :mesaj,
			ip				= :ip,
			tarih			= :tarih,
			buguntarih		= :buguntarih,
			durum			= 0");
		
		$Ekle = $sorgu->execute(array(
			'program_id'	=> $program_id,
			'program_baslik'=> $program_baslik,
			'ad_soyad'		=> $ad_soyad,
			'okul_isletme'	=> $okul_isletme,
			'email'			=> $email,
			'telefon'		=> $telefon,
			'konum'			=> $konum,
			'mesaj'			=> $mesaj,
			'ip'			=> $ip,
			'tarih'			=> $tarih,
			'buguntarih'	=> $buguntarih
		));
		
		if($Ekle)
		{
			$last_id = $db->lastInsertId();
			
			// Bildirim ekle
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
				tarih		= :tarih");
			
			$BEkle = $BSorgu->execute(array(
				'baslik'	=> "Yeni Program Talebi",
				'icon'		=> "icon-graduation",
				'bid'		=> $last_id,
				'bildirim'	=> "<strong>".$ad_soyad."</strong> '".$program_baslik."' programını talep etti.",
				'ktarih'	=> $bildirimkt,
				'tarih'		=> $bildirimt
			));
			
			// Mail gönderimi
			bildirim_sablon_gonder(1, [
				'adsoyad' => $ad_soyad,
				'mesajkonu' => 'Program Talebi: ' . $program_baslik,
				'email' => $email,
				'telefon' => $telefon,
				'mesaj' => "Program: $program_baslik\nOkul/İşletme: $okul_isletme\nKonum: $konum\nMesaj: $mesaj"
			], $email, $telefon);
			
			echo json_encode([
				'success' => true,
				'message' => 'Talebiniz başarıyla gönderildi. En kısa sürede size dönüş yapacağız.'
			]);
		}
		else
		{
			echo json_encode([
				'success' => false,
				'message' => 'Bir hata oluştu. Lütfen tekrar deneyiniz.'
			]);
		}
	}
	else
	{
		echo json_encode([
			'success' => false,
			'message' => 'Site demo modunda. İşlem yapılamıyor.'
		]);
	}
	exit;
}

if (isset($_GET['randevu_dolu'])) {
	header('Content-Type: application/json; charset=utf-8');
	$parca = explode('-', (string) ($_GET['tarih'] ?? ''));
	$tarih = (count($parca) === 3) ? $parca[2] . '-' . $parca[1] . '-' . $parca[0] : '';
	$saatler = [];
	if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tarih)) {
		$doluSorgu = $db->prepare("SELECT saat FROM randevular WHERE tarih = ? AND durum <> 2");
		$doluSorgu->execute([$tarih]);
		foreach ($doluSorgu->fetchAll(PDO::FETCH_COLUMN) as $saat) {
			$saatler[] = substr((string) $saat, 0, 5);
		}
	}
	echo json_encode($saatler);
	exit;
}

## Randevu Kaydet ##
if(isset($_POST['randevu_btn']))
{	
	$hizmet_id 	= isset($_POST['hizmet_id']) ? (int)$_POST['hizmet_id'] : 0;
	if ($hizmet_id < 1) {
		$hizmet_id = null;
	}
	$isim 		= strip_tags(trim($_POST['isim']));
	$telefon 	= strip_tags(trim($_POST['telefon']));
	$email 		= strip_tags(trim($_POST['email']));
	
	// Tarih formatını düzelt: 16-11-2025 -> 2025-11-16
	$tarih_parts = explode('-', $_POST['tarih']);
	$tarih = $tarih_parts[2] . '-' . $tarih_parts[1] . '-' . $tarih_parts[0];
	
	// Saat formatını düzelt: 16:00 -> 16:00:00
	$saat = $_POST['saat'] . ':00';
	
	$aciklama 	= !empty($_POST['aciklama']) ? nl2br(strip_tags(trim($_POST['aciklama']))) : '';
	$randevuurl = strip_tags($_POST['randevuurl']);
	$ip			= cVCLmHLxbS_ip();

	if($ayar["demo"] == 0)
	{
		if (strlen($_POST['kontrol']) > 0)
		{
			header("Location:".$randevuurl."");
			die();
		}

		if(empty($isim) || empty($telefon) || empty($email) || empty($tarih) || empty($saat))
		{
			$_SESSION['randevu_btn'] = 'bos';
			header("Location:".$randevuurl."");
			exit;
		}

		$doluKayit = $db->prepare("SELECT id FROM randevular WHERE tarih = ? AND saat = ? AND durum <> 2 LIMIT 1");
		$doluKayit->execute([$tarih, $saat]);
		if ($doluKayit->fetch(PDO::FETCH_ASSOC)) {
			$_SESSION['randevu_btn'] = 'dolu';
			header("Location:".$randevuurl."");
			exit;
		}

		{
			// Hizmet bilgisini al
			$hizmet_adi = '';
			if($hizmet_id) {
				$hizmetSorgu = $db->prepare("SELECT baslik FROM randevu_hizmetler WHERE id = ?");
				$hizmetSorgu->execute(array($hizmet_id));
				if($hizmetSorgu->rowCount()) {
					$hizmetBilgi = $hizmetSorgu->fetch(PDO::FETCH_ASSOC);
					$hizmet_adi = $hizmetBilgi['baslik'];
				}
			}
			
			$sorgu = $db->prepare("INSERT INTO randevular SET
				hizmet_id		= :hizmet_id,
				hizmet_adi		= :hizmet_adi,
				isim			= :isim,
				telefon			= :telefon,
				email			= :email,
				tarih			= :tarih,
				saat			= :saat,
				aciklama		= :aciklama,
				ip				= :ip,
				durum			= :durum,
				olusturma_tarihi = NOW()");
			$Ekle = $sorgu->execute(array(
				'hizmet_id' 	=> $hizmet_id,
				'hizmet_adi' 	=> $hizmet_adi,
				'isim' 			=> $isim,
				'telefon' 		=> $telefon,
				'email' 		=> $email,
				'tarih' 		=> $tarih,
				'saat' 			=> $saat,
				'aciklama' 		=> $aciklama,
				'ip'			=> $ip,
				'durum'			=> 0
			));	
			if($Ekle)
			{
				$last_id = $db->lastInsertId();
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
					'baslik' 	=> "Yeni Randevu",
					'icon' 		=> "icon-calendar",
					'bid' 		=> $last_id,
					'bildirim' 	=> "<strong>".$isim."</strong> randevu talebinde bulundu.",
					'ktarih'	=> $bildirimkt,
					'tarih'		=> $bildirimt
				));
				
				// E-posta bildirimi gönder
				$randevu_bilgi = "Randevu Tarihi: ".date('d.m.Y', strtotime($tarih))." - Saat: ".date('H:i', strtotime($saat));
				bildirim_sablon_gonder(1, [
					'adsoyad' => $isim,
					'mesajkonu' => 'Yeni Randevu Talebi',
					'email' => $email,
					'telefon' => $telefon,
					'mesaj' => "$randevu_bilgi\n$aciklama"
				], $email, $telefon);
				
				$_SESSION['randevu_btn'] = 'yes';
				header("Location:".$randevuurl."");
				exit;
			}
			else
			{
				$_SESSION['randevu_btn'] = 'no';
				header("Location:".$randevuurl."");
				exit;
			}			
		}
	}
	else
	{
		$_SESSION['sitedemo'] = 'no';
		header("Location:".$randevuurl."");
		exit;
	}
}



// AJAX isteklerini işle
if(isset($_POST['islem'])) {
    $islem = $_POST['islem'];
    header('Content-Type: application/json; charset=utf-8');
    
    // 1. RANDEVU KAYDETME İŞLEMİ
    if($islem == 'randevu_kaydet') {
        $hizmet_id = isset($_POST['hizmet_id']) ? (int)$_POST['hizmet_id'] : 0;
        if ($hizmet_id < 1) {
            $hizmet_id = null;
        }
        $isim = $_POST['isim'] ?? '';
        $telefon = $_POST['telefon'] ?? '';
        $email = $_POST['email'] ?? '';
        $tarih = $_POST['tarih'] ?? '';
        $saat = $_POST['saat'] ?? '';
        $aciklama = $_POST['aciklama'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'];
        
        if(empty($isim) || empty($telefon) || empty($email) || empty($tarih) || empty($saat)) {
            echo json_encode(['success' => false, 'message' => ($dil['txt586'] ?? 'Lütfen tüm zorunlu alanları doldurun')]);
            exit;
        }
        
        $hizmet_adi = '';
        if($hizmet_id) {
            $hizmetSorgu = $db->prepare("SELECT baslik FROM randevu_hizmetler WHERE id = ? AND durum = 1");
            $hizmetSorgu->execute(array($hizmet_id));
            if($hizmetSorgu->rowCount()) {
                $hizmetBilgi = $hizmetSorgu->fetch(PDO::FETCH_ASSOC);
                $hizmet_adi = $hizmetBilgi['baslik'];
            }
        }
        
        try {
            $sorgu = $db->prepare("INSERT INTO randevular SET hizmet_id = ?, hizmet_adi = ?, isim = ?, telefon = ?, email = ?, tarih = ?, saat = ?, aciklama = ?, ip = ?, durum = 0, olusturma_tarihi = NOW()");
            $Ekle = $sorgu->execute([$hizmet_id, $hizmet_adi, $isim, $telefon, $email, $tarih, $saat, $aciklama, $ip]);
            
            if($Ekle) {
                $last_id = $db->lastInsertId();
                $btarih = date('Y-m-d H:i:s');
                $bildirimkt = strtotime(date('Y-m-d'));
                $bildirimt = strtotime($btarih);
                
                $BSorgu = $db->prepare("INSERT INTO bildirimler SET baslik = ?, icon = ?, bid = ?, bildirim = ?, ktarih = ?, tarih = ?");
                $BSorgu->execute(["Yeni Randevu", "icon-calendar", $last_id, "<strong>".$isim."</strong> randevu talebinde bulundu.", $bildirimkt, $bildirimt]);
                
                echo json_encode(['success' => true, 'message' => ($dil['txt587'] ?? 'Randevu talebiniz başarıyla gönderildi.')]);
            } else {
                echo json_encode(['success' => false, 'message' => ($dil['txt588'] ?? 'Veritabanı hatası')]);
            }
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // 2. YETİM SPONSORLUK KAYDET İŞLEMİ
if ($islem == 'yetim_sponsorluk_kaydet') {
        header('Content-Type: application/json; charset=utf-8');

        // Gelen verileri temizle ve ata
        $fullName     = trim($_POST['fullName'] ?? '');
        $sponsortip   = $_POST['sponsortip'] ?? 'bireysel';
        $grup_adi     = !empty($_POST['grup_adi']) ? $_POST['grup_adi'] : null;
        $kurum_unvani = !empty($_POST['kurum_unvani']) ? $_POST['kurum_unvani'] : null;
        $telefon      = trim($_POST['telefon'] ?? '');
        $email        = trim($_POST['email'] ?? '');
        $tutar        = floatval($_POST['tutar'] ?? 0);
        
        // yetim_id null string gelirse PHP null yap
        $yetim_id     = (isset($_POST['yetim_id']) && $_POST['yetim_id'] !== 'null' && $_POST['yetim_id'] !== '') ? intval($_POST['yetim_id']) : 0;
        
        $odeme_tipi   = $_POST['odeme_tipi'] ?? 'tek_seferlik';
        $ss_ay        = intval($_POST['sponsorluk_suresi_ay'] ?? 1);
        $odeme_gunu   = intval($_POST['odeme_gunu'] ?? 1);
        $para_birimi  = $_POST['para_birimi'] ?? 'TRY';
        $odeme_yontemi = $_POST['odeme_yontemi'] ?? 'paytr';
        $bagis_id_param = $_POST['bagis_id_param'] ?? null;

        if (empty($fullName) || empty($email) || $tutar <= 0) {
            echo json_encode(['success' => false, 'message' => 'Lütfen tüm alanları doldurun ve geçerli bir tutar girin']);
            exit;
        }

        // Kategori Tespiti
        $kategori_id = 0;
        if (!empty($bagis_id_param)) {
            $st_cat = $db->prepare("SELECT kategori FROM bagislar WHERE id = ?");
            $st_cat->execute([$bagis_id_param]);
            $kategori_id = intval($st_cat->fetchColumn() ?: 0);
        }

        // Döviz Çevirisi (Varsa)
        if ($para_birimi != 'TRY') {
            require_once(__DIR__ . '/exchange_rates.php');
            $kurlar = ExchangeRates::getRates(['USD', 'EUR']);
            if (isset($kurlar[$para_birimi])) {
                $tutar = round($tutar * $kurlar[$para_birimi], 2);
                $para_birimi = 'TRY';
            }
        }

        try {
            // SQL sorgusunu düzenli hale getirdik
            $sql = "INSERT INTO bagis_odeme SET 
                modul_id = 1, 
                modul_adi = 'Yetim Sponsorluğu', 
                sponsortip = :sponsortip, 
                odeme_tipi = :odeme_tipi, 
                sponsorluk_suresi_ay = :ss_ay, 
                odeme_gunu = :o_gunu, 
                grup_adi = :grup_adi, 
                kurum_unvani = :kurum_unvani, 
                sorumlu_ad_soyad = :s_ad,
                sorumlu_telefon = :s_tel,
                sorumlu_email = :s_mail,
                ad = :ad, 
                soyad = '', 
                adi = :ad_tam, 
                email = :email, 
                telefon = :telefon, 
                tutar = :tutar, 
                fiyat = :futar, 
                para_birimi = :para_birimi, 
                bagis_tipi = 'yetim_sponsorluk', 
                durum = 'beklemede', 
                tarih = NOW(), 
                spno = :spno, 
                ip = :ip, 
                kategori_id = :kategori_id, 
                yetim_id = :yetim_id, 
                odeme_yontemi = :odeme_yontemi";

            $sorgu = $db->prepare($sql);
            
            $spno = 'YS' . time() . rand(10, 99);
            $ip_adresi = $_SERVER['REMOTE_ADDR'] ?? '';

            $insert = $sorgu->execute([
                ':sponsortip'   => $sponsortip,
                ':odeme_tipi'   => $odeme_tipi,
                ':ss_ay'        => $ss_ay,
                ':o_gunu'       => $odeme_gunu,
                ':grup_adi'     => $grup_adi,
                ':kurum_unvani' => $kurum_unvani,
                ':s_ad'         => $fullName,
                ':s_tel'        => $telefon,
                ':s_mail'       => $email,
                ':ad'           => $fullName,
                ':ad_tam'       => $fullName,
                ':email'        => $email,
                ':telefon'      => $telefon,
                ':tutar'        => $tutar,
                ':futar'        => strval($tutar),
                ':para_birimi'  => $para_birimi,
                ':spno'         => $spno,
                ':ip'           => $ip_adresi,
                ':kategori_id'  => $kategori_id,
                ':yetim_id'     => $yetim_id,
                ':odeme_yontemi' => $odeme_yontemi
            ]);

            if ($insert) {
                $bagis_id = $db->lastInsertId();
                echo json_encode([
                    'success' => true, 
                    'redirect' => 'paytr_tekil_bagis.php?bagis_id=' . $bagis_id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Veritabanı kayıt hatası oluştu']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'SQL Hatası: ' . $e->getMessage()]);
        }
        exit;
    }

    // 3. YETİM LİSTESİ GETİR
    if($islem == 'yetim_listesi_getir') {
        try {
            $sorgu = $db->query("SELECT y.*, (SELECT MAX(tarih) FROM bagis_odeme bo WHERE bo.yetim_id = y.id) as son_bagis_tarihi FROM yetimler y WHERE y.hami_durumu != 'tamamlandi' AND y.durum = 1 ORDER BY y.id DESC");
            $yetimler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'yetimler' => $yetimler]);
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // 4. HIZLI BAĞIŞ KAYDETME İŞLEMİ
    if ($islem == 'hizli_bagis_kaydet') {
        $ad = trim($_POST['ad'] ?? $_POST['fullName'] ?? '');
        $soyad = trim($_POST['soyad'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefon = trim($_POST['telefon'] ?? '');
        $tutar = floatval($_POST['tutar'] ?? 0);
        $para_birimi = $_POST['para_birimi'] ?? 'TRY';
        $bagis_id_param = $_POST['bagis_id_param'] ?? $_POST['bagis_id'] ?? null;
        $odeme_yontemi = $_POST['odeme_yontemi'] ?? 'paytr';

        if (empty($ad) || empty($email) || $tutar <= 0) {
            echo json_encode(['success' => false, 'message' => 'Bilgileri kontrol edin']);
            exit;
        }

        $kategori_id = 0;
        $sepet_json = null;
        if (!empty($bagis_id_param)) {
            $bagisSorgu = $db->prepare("SELECT id, adi, kategori FROM bagislar WHERE id = ?");
            $bagisSorgu->execute([$bagis_id_param]);
            $bagisBilgi = $bagisSorgu->fetch(PDO::FETCH_ASSOC);
            if ($bagisBilgi) {
                $kategori_id = intval($bagisBilgi['kategori'] ?? 0);
                $sepet_json = json_encode([['id' => strval($bagisBilgi['id']), 'adi' => $bagisBilgi['adi'], 'tutar' => $tutar, 'sesID' => substr(md5(uniqid()), 0, 10)]], JSON_UNESCAPED_UNICODE);
            }
        }

        if ($para_birimi != 'TRY') {
            require_once(__DIR__ . '/exchange_rates.php');
            $kurlar = ExchangeRates::getRates(['USD', 'EUR']);
            if (isset($kurlar[$para_birimi])) {
                $tutar = round($tutar * $kurlar[$para_birimi], 2);
                $para_birimi = 'TRY';
            }
        }

        $query = "INSERT INTO bagis_odeme SET modul_id = 1, modul_adi = 'Hızlı Bağış', ad = ?, soyad = ?, adi = ?, email = ?, telefon = ?, tutar = ?, fiyat = ?, para_birimi = ?, bagis_tipi = 'hizli', durum = 'beklemede', tarih = NOW(), spno = ?, ip = ?, odeme_yontemi = ?, sepet = ?, kategori_id = ?";
        $spno = 'BG' . time() . rand(10, 99);
        $insert = $db->prepare($query)->execute([$ad, $soyad, ($ad.' '.$soyad), $email, $telefon, $tutar, strval($tutar), $para_birimi, $spno, $_SERVER['REMOTE_ADDR'], $odeme_yontemi, $sepet_json, $kategori_id]);

        if ($insert) {
            echo json_encode(['success' => true, 'redirect' => 'paytr_tekil_bagis.php?bagis_id=' . $db->lastInsertId()]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
        }
        exit;
    }
}




?>