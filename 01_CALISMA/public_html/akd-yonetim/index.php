<?php define("GUVENLIK",true);?>
<?php
session_start();
ob_start();

require_once('../_class/baglan.php');
require_once('../_class/fonksiyon.php');
require_once('../_class/class.upload.php');
require_once('../language/admin_dil.php');
require_once('../_class/yonetim_seo.php');
?>
<?php $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
$scriptPath = str_replace('\\', '/', dirname($_SERVER['PHP_SELF'] ?? '/'));
$adminKlasor = '/' . (defined('yonetim') ? yonetim : 'akd-yonetim');
if ($scriptPath === '/' || $scriptPath === '.' || $scriptPath === $adminKlasor || str_ends_with($scriptPath, $adminKlasor)) {
	$scriptPath = '';
}
$url = $protocol . $_SERVER['HTTP_HOST'] . $scriptPath; 
$sayfalink = $protocol.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
?>
<?php
error_reporting(0);
$sayfa=isset($_GET['sayfa']) ? addslashes($_GET['sayfa']) : "";
if($sayfa=="yonetici-ekle"){?>
<?php 
$oturumkontrol = $db->prepare("SELECT * FROM kullanici WHERE BINARY id = ? AND kadi = ? AND sifre = ? AND rutbe = ?");
$oturumkontrol->execute(array($_SESSION['Yonetim_Id'],$_SESSION['Yonetim_Kadi'],$_SESSION['Yonetim_Sifre'],$_SESSION['rutbe']));
if($oturumkontrol->rowCount())
{
	$Bilgilerim = $oturumkontrol->fetch(PDO::FETCH_ASSOC);
	
}
else
{
	unset($_SESSION['Yonetim_Id']);
	unset($_SESSION['Yonetim_Kadi']);
	unset($_SESSION['Yonetim_Sifre']);
	unset($_SESSION['rutbe']);
	unset($_SESSION['guvenlik']);
	header("Location:".$url."/giris.html");
	exit();
}
?>
<?php }else{?>
<?php 
$oturumkontrol = $db->prepare("SELECT * FROM kullanici WHERE BINARY id = ? AND kadi = ? AND sifre = ? AND rutbe = ?");
$oturumkontrol->execute(array($_SESSION['Yonetim_Id'],$_SESSION['Yonetim_Kadi'],$_SESSION['Yonetim_Sifre'],$_SESSION['rutbe']));
if($oturumkontrol->rowCount())
{
$Bilgilerim = $oturumkontrol->fetch(PDO::FETCH_ASSOC);
if($Bilgilerim['sifre'] == "1234" || $Bilgilerim['sifre'] == "demo" || $Bilgilerim['sifre'] == "admin" || $Bilgilerim['sifre'] == "123" || $Bilgilerim['sifre'] == "1234" || $Bilgilerim['sifre'] == "123456" || $Bilgilerim['sifre'] == "123" || $Bilgilerim['sifre'] == "user" || $Bilgilerim['sifre'] == "1" ){
header("Location:".$url."/yonetici-duzenle/".$Bilgilerim['id'].".html");
exit();	
}
	
}
else
{
	unset($_SESSION['Yonetim_Id']);
	unset($_SESSION['Yonetim_Kadi']);
	unset($_SESSION['Yonetim_Sifre']);
	unset($_SESSION['rutbe']);
	unset($_SESSION['guvenlik']);
	header("Location:".$url."/giris.html");
	exit();
}
?>	
	<?php }?>

<meta charset="utf-8">
<?php
$ua=cVCLmHLxbS_getBrowser();
$tarayici= "Web tarayucınız: " . $ua['name'] . " " . $ua['version'] . " " .$ua['platform'];
//Örneğin mozilla Firefox kullananların girmesini istemiyorsak
if ($ua['name']=='Mozilla Firefox'){
	print_r($tarayici);	
	echo "<center>";
	echo "<h2>Mozilla Firefox tarayıcısı desteklenmiyor.</h2><br>" ;
	echo "<h4>Lütfen Internet Explorer, Opera, Safari, Chrome tarayıcılarından birini kullanınız.</h4>";
	echo "</center>";
} else  {?>
<?php 
if (isset($_GET['dil']) && is_numeric($_GET['dil'])) 
{
    $_SESSION['admin_dil'] = $_GET['dil'];
}
if(!isset($_SESSION['admin_dil']))
{
    $mevcutDil_result = $db->query("SELECT * FROM diller WHERE anadil = 1");
    $mevcutDil = $mevcutDil_result->fetch(PDO::FETCH_ASSOC);
    $_SESSION['admin_dil'] = $mevcutDil['id'] ?? 1;
}
else
{
    $mevcutDil_result = $db->query("SELECT * FROM diller WHERE id = " . intval($_SESSION['admin_dil']));
    $mevcutDil = $mevcutDil_result->fetch(PDO::FETCH_ASSOC);
    if($mevcutDil === false) {
        $anadil_result = $db->query("SELECT * FROM diller WHERE anadil = 1");
        $anadil = $anadil_result->fetch(PDO::FETCH_ASSOC);
        $_SESSION['admin_dil'] = $anadil['id'] ?? 1;
        $mevcutDil = $anadil;
    } else {
        $_SESSION['admin_dil'] = $mevcutDil['id'] ?? 1;
    }
}
?>
<?php

function onayliSponsorluklariEslestir($db) {
    $sorgu = $db->prepare("SELECT * FROM bagis_odeme 
                           WHERE bagis_tipi = 'yetim_sponsorluk' 
                           AND paytronay = 1 
                           AND (yetim_id = 0 OR yetim_id IS NULL) 
                           LIMIT 1");
    $sorgu->execute();
    $bagis = $sorgu->fetch(PDO::FETCH_ASSOC);

    if ($bagis) {
        require_once(__DIR__ . '/../_class/YetimSponsorlukYoneticisi3Param.php');
        $yonetici = new YetimSponsorlukYoneticisi3Param($db);

        $eslestirme = $yonetici->akilliYetimEslestir($bagis['id'], $bagis['tutar'], $bagis);

        if (isset($eslestirme['status']) && $eslestirme['status'] == true) {
            $db->prepare("UPDATE bagis_odeme SET yetim_id = ?, hamilik_id = ? WHERE id = ?")
               ->execute([$eslestirme['yetim_id'], $eslestirme['hamilik_id'], $bagis['id']]);
        }
    }
}
onayliSponsorluklariEslestir($db);

function onayliBagiscilariRehbereEkle($db) {
    $sorgu = $db->prepare("SELECT adi, email, telefon, aciklama, tarih 
                           FROM bagis_odeme 
                           WHERE paytronay = 1 
                           GROUP BY email");
    $sorgu->execute();
    $bagiscilar = $sorgu->fetchAll(PDO::FETCH_ASSOC);

    foreach ($bagiscilar as $bagisci) {
        $hamTelefon = $bagisci['telefon'];
        
        $temizTelefon = preg_replace('/[^0-9]/', '', $hamTelefon);
        
        (strlen($temizTelefon) == 11 && substr($temizTelefon, 0, 1) == '9');
            $temizTelefon = substr($temizTelefon, 1);
        

        $kontrol = $db->prepare("SELECT id FROM rehber WHERE email = ?");
        $kontrol->execute([$bagisci['email']]);
        
        if ($kontrol->rowCount() == 0) {
            $ekle = $db->prepare("INSERT INTO rehber SET 
                adi = ?, 
                email = ?, 
                telefon = ?, 
                notunuz = ?, 
                durum = 1, 
                tarih = ?");
            
            $ekle->execute([
                $bagisci['adi'],
                $bagisci['email'],
                $temizTelefon, 
                $bagisci['aciklama'],
                $bagisci['tarih']
            ]);
        }
    }
}

onayliBagiscilariRehbereEkle($db);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
	<base href="<?php echo rtrim($url, '/'); ?>/<?php echo yonetim; ?>/">
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?php echo $title;?></title>
	<!-- plugins:css -->
	<link rel="stylesheet" href="vendors/iconfonts/mdi/font/css/materialdesignicons.min.css">
	<link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
	<link rel="stylesheet" href="vendors/css/vendor.bundle.addons.css">
	<link href="https://fonts.googleapis.com/css?family=Fira+Sans:100,200,300,400,500,600,700,800,900" rel="stylesheet">
	<!-- endinject -->
	<link rel="stylesheet" href="vendors/iconfonts/ti-icons/css/themify-icons.css">
	<link rel="stylesheet" href="vendors/iconfonts/simple-line-icon/css/simple-line-icons.css">
	<link rel="stylesheet" href="vendors/iconfonts/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="vendors/iconfonts/font-awesome/css/all.css" />
	<link rel="stylesheet" href="vendors/iconfonts/flag-icon-css/css/flag-icon.min.css" />
	
	<link rel="stylesheet" href="vendors/lightgallery/css/lightgallery.css">
	<link rel="stylesheet" href="vendors/summernote/dist/summernote-bs4.css">
	<!-- inject:css -->
	<link rel="stylesheet" href="css/vertical-layout-light/style.css">
	<!-- endinject -->
	<link rel="shortcut icon" href="images/favicon.png" />
	
	<!-- codemirror css -->
	<link href="vendors/codemirror/lib/codemirror.css" rel="stylesheet" type="text/css" />
	<link href="vendors/codemirror/theme/neat.css" rel="stylesheet" type="text/css" />
	<link href="vendors/codemirror/theme/ambiance.css" rel="stylesheet" type="text/css" />
	<link href="vendors/codemirror/theme/material.css" rel="stylesheet" type="text/css" />
	<link href="vendors/codemirror/theme/neo.css" rel="stylesheet" type="text/css" />
	<link href="vendors/datetimepicker/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
	
	<!--Perfect-Scrollbar css-->
    <link rel="stylesheet" href="vendors/css/perfect-scrollbar.min.css">
	<link rel="stylesheet" href="vendors/css/jquery.mCustomScrollbar.css">
	
	<!--Multi Select css-->
	<link rel="stylesheet" href="vendors/multiselect/jquery.multiselect.css">

	
	<!-- plugins:js -->
	<script src="vendors/js/vendor.bundle.base.js"></script>
	<script src="vendors/js/vendor.bundle.addons.js"></script>
	<!-- endinject -->
	<script src="vendors/lightgallery/js/lightgallery-all.min.js"></script>
	<script src="vendors/tinymce/tinymce.min.js"></script>
	<!-- codemirror js -->
	<script src="vendors/codemirror/lib/codemirror.js" type="text/javascript"></script>
	<script src="vendors/codemirror/addon/edit/matchbrackets.js" type="text/javascript"></script>
	<script src="vendors/codemirror/mode/htmlmixed/htmlmixed.js" type="text/javascript"></script>
	<script src="vendors/codemirror/mode/xml/xml.js" type="text/javascript"></script>
	<script src="vendors/codemirror/mode/javascript/javascript.js" type="text/javascript"></script>
	<script src="vendors/codemirror/mode/css/css.js" type="text/javascript"></script>
	<script src="vendors/codemirror/mode/clike/clike.js" type="text/javascript"></script>
	<script src="vendors/codemirror/mode/php/php.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(function() {
			var pageTitle = $("title").text();
			$(window).blur(function() {
				$("title").text("Beni Kapatmayı Unutma :)");
			});
			$(window).focus(function() {
				$("title").text(pageTitle);
			});
		});
	</script>
</head>
<body>
    
<style>
    .table-custom thead th { border-top: 0; font-size: 11px; text-uppercase: true; letter-spacing: 0.5px; }
    .table-custom tbody td { vertical-align: middle; font-size: 13px; }
    .badge { padding: 0.5em 0.8em; border-radius: 4px; font-weight: 500; }
    .highlight { background-color: #f8f9ff !important; }
</style>

	<div class="container-scroller">
		
		<div class="theme-setting-wrapper">
			<div id="settings-trigger"><i class="flag-icon <?=@$mevcutDil['bayrak'];?>"></i></div>
			<div id="theme-settings" class="settings-panel">
				<i class="settings-close mdi mdi-close"></i>
				<p class="settings-heading">Düzenleme Dili</p>
				<?php $DILSorgu = $db->prepare("SELECT * FROM diller ORDER BY sira ASC");
				$DILSorgu->execute();
				$DILislem = $DILSorgu->fetchALL(PDO::FETCH_ASSOC);?>
				<?php foreach ( $DILislem as $DILSonuc ){?>
				<div class="sidebar-bg-options <?php echo($mevcutDil['id'] == $DILSonuc['id'] ? 'selected' : '' );?>">
					<a href="index.html?dil=<?=$DILSonuc['id'];?>"><div class="flag-icon <?=$DILSonuc['bayrak'];?> mr-3"></div><?=@$DILSonuc['adi'];?></a>
				</div>
				<?php } ?>
			</div>
		</div>
	
		<!-- partial:partials/_navbar.html -->
		<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
			<div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
				<a class="navbar-brand brand-logo" href="index.html">YÖNETİM PANELİ</a>
				<a class="navbar-brand brand-logo-mini" href="index.html"><img src="images/logo-mini.svg" alt="logo"/></a>
			</div>
			<div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
				<button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
				<span class="mdi mdi-apps"></span>
				</button>
				<ul class="navbar-nav mr-lg-2">
					<li class="nav-item nav-search d-none d-lg-block" style="position: relative;">
						<div class="admin-search-wrapper">
							<div class="input-group" style="width: 300px;">
								<div class="input-group-prepend">
									<span class="input-group-text bg-white border-right-0" style="border-color: #e1e4e8;">
										<i class="icon-magnifier text-muted"></i>
									</span>
								</div>
								<input type="text" class="form-control border-left-0 pl-0" id="navbar-search-input" placeholder="Menüde Ara... (Ctrl+K)" aria-label="search" style="border-color: #e1e4e8; font-size: 14px;">
							</div>
							<div id="search-results-container" class="admin-search-results"></div>
						</div>
					</li>
					<li class="nav-item nav-search d-none d-lg-block">
						<a href="../index.html" target="_blank" class="btn btn-primary btn-sm"><i class="mdi mdi-home-outline font-13"></i> Siteyi Görüntüle</a>
					</li>
				</ul>
				<ul class="navbar-nav navbar-nav-right">
					<li class="nav-item dropdown">
						<a class="nav-link count-indicator dropdown-toggle d-flex justify-content-center align-items-center" id="notificationDropdown" href="#" data-toggle="dropdown">
						<i class="mdi mdi-email-outline mx-0"></i>
						<?php
						$bgntarih = cVCLmHLxbS_tr_tarih('Y-m-d');
						$buguntarih = strtotime($bgntarih);
						$mesajSayisi = $db->prepare("SELECT COUNT(*) FROM mesajlar WHERE buguntarih = ?");
						$mesajSayisi->execute(array($buguntarih));
						$mesajAdet = $mesajSayisi->fetchColumn();

						$bagisSayisi = $db->prepare("SELECT COUNT(*) FROM bagis_odeme WHERE paytronay = 1 AND DATE(tarih) = CURDATE()");
						$bagisSayisi->execute();
						$bagisAdet = $bagisSayisi->fetchColumn();

						$toplamBildirim = $mesajAdet + $bagisAdet;
						if($toplamBildirim > 0):
						?>
						<span class="count count-email"><?php echo $toplamBildirim; ?></span>
						<?php endif; ?>
						</a>
						<div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pt-0" aria-labelledby="messageDropdown" style="min-width: 350px;">
							<p class="mb-0 font-weight-normal float-left dropdown-header bg-dark text-white w-100">Bugün Gelen Bildirimler</p>
							<?php
							$bgntarih	= cVCLmHLxbS_tr_tarih('Y-m-d');
							$buguntarih = strtotime($bgntarih);

							// Bugün gelen mesajları çek
							$Sorgu = $db->prepare("SELECT * FROM mesajlar WHERE buguntarih = ? ORDER BY id ASC");
							$Sorgu->execute(array($buguntarih));
							$islem = $Sorgu->fetchALL(PDO::FETCH_ASSOC);

							// Bugün gelen onaylanmış bağışları çek
							$BagisSorgu = $db->prepare("SELECT id, ad, soyad, tutar, para_birimi FROM bagis_odeme WHERE paytronay = 1 AND DATE(tarih) = CURDATE() ORDER BY tarih DESC LIMIT 5");
							$BagisSorgu->execute();
							$bagis_islem = $BagisSorgu->fetchALL(PDO::FETCH_ASSOC);
							?>
							<?php if($Sorgu->rowCount() != "0" || $BagisSorgu->rowCount() != "0"){?>
							<?php if($Sorgu->rowCount() != "0"){?>
							<p class="mb-0 font-weight-normal float-left dropdown-header bg-primary text-white w-100">Mesajlar</p>
							<?php foreach ( $islem as $Sonuc ){?>
							<a class="dropdown-item preview-item">
								<div class="preview-item-content flex-grow">
									<h6 class="preview-subject ellipsis font-weight-normal"><?php echo $Sonuc['isim']?>
									</h6>
									<p class="font-weight-light small-text text-muted mb-0">
										<?php echo $Sonuc['konu']?>
									</p>
								</div>
							</a>
							<?php }?>
							<?php }?>
							<?php if($BagisSorgu->rowCount() != "0"){?>
							<p class="mb-0 font-weight-normal float-left dropdown-header bg-success text-white w-100">Bağışlar</p>
							<?php foreach ( $bagis_islem as $Bagis ){?>
							<a class="dropdown-item preview-item donation-item" href="gelen-bagis-detay.html?id=<?php echo $Bagis['id'];?>">
								<div class="preview-item-content flex-grow">
									<h6 class="preview-subject ellipsis font-weight-normal"><?php echo $Bagis['ad']?> <?php echo $Bagis['soyad']?>
									</h6>
									<p class="font-weight-light small-text text-muted mb-0">
										<?php echo number_format($Bagis['tutar'], 2, ',', '.');?> <?php echo $Bagis['para_birimi'];?> bağış yapıldı
									</p>
								</div>
							</a>
							<?php }?>
							<?php }?>
							<?php }else{?>
							<p class="text-center pt-5 text-muted">Bugün gelen bildirim yok.</p>
							<?php }?>
						</div>
					</li>
					
					<li class="nav-item nav-profile dropdown">
						<a class="nav-link dropdown-toggle" href="yonetici-duzenle/<?php echo $Bilgilerim['id'];?>.html" data-toggle="dropdown" id="profileDropdown">
						<?php if($Bilgilerim['resim'] != ""){?>
						<img src="images/users/<?php echo $Bilgilerim['resim'];?>" alt="<?php echo $Bilgilerim['isim'];?>">
						<?php }else{?>
						<img src="images/users/avatar.jpg" alt="<?php echo $Bilgilerim['isim'];?>">
						<?php }?>
						</a>
						<div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
							<a href="yonetici-duzenle/<?php echo $Bilgilerim['id'];?>.html" class="dropdown-item">
								<i class="icon-user text-primary"></i>
								Profili Görüntüle
							</a>
							<a href="genel-ayarlar.html" class="dropdown-item">
								<i class="icon-settings text-primary"></i>
								Ayarlar
							</a>
							<a href="../_class/yonetim_islem.php?cikis=ok" class="dropdown-item">
								<i class="icon-power text-primary"></i>
								Oturumu Kapat
							</a>
						</div>
					</li>
				</ul>
				<button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
				<span class="mdi mdi-menu"></span>
				</button>
			</div>
		</nav>	
		<!-- partial -->
		<div class="container-fluid page-body-wrapper">
			<!-- partial:partials/_sidebar.html -->
			<nav class="sidebar sidebar-offcanvas" id="sidebar">
				<ul class="nav">
					<li class="nav-item sidebar-category mt-4">
						<span style="margin-left: -10px;"><?=@$admindil['txt1_1'];?></span>
					</li>
					<li class="nav-item <?php echo $anasayfa; ?>">
						<a class="nav-link" href="index.html">
						<i class="icon-home  menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt1'];?></span>
						</a>
					</li>
					
					<li class="nav-item <?php echo $genelayarlar; ?> <?php echo $hesapnumaralarimiz; ?> <?php echo $baskanayarlar; ?> <?php echo $apiayarlari; ?> <?php echo $iletisimayarlari; ?> <?php echo $sitebakimmodu; ?> <?php echo $resimoptimize; ?> <?php echo $limitayarlari; ?> <?php echo $modulayarlari; ?> <?php echo $sosyalmedyaayarlari; ?> <?php echo $mailayarlari; ?> <?php echo $smsayarlari; ?> <?php echo $bildirimyonetimi; ?> <?php echo $arkaplanayarlari; ?> <?php echo $popup; ?> <?php echo $sanalposlar; ?> <?php echo $anasayfaalansiralama; ?>">
						<a class="nav-link" data-toggle="collapse" href="#site-yonetimi" aria-expanded="false" aria-controls="site-yonetimi">
						<i class="icon-settings menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt2'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $ayarlarshow;?>" id="site-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $genelayarlar; ?>"> 
									<a class="nav-link <?php echo $genelayarlar; ?>" href="genel-ayarlar.html"><?=@$admindil['txt3'];?></a>
								</li>
								<li class="nav-item <?php echo $baskanayarlar; ?>"> 
									<a class="nav-link <?php echo $baskanayarlar; ?>" href="baskan-ayarlar.html"><?=@$admindil['txt114'];?></a>
								</li>
								<li class="nav-item <?php echo $popup; ?>"> 
									<a class="nav-link <?php echo $popup; ?>" href="popup.html"><?=@$admindil['txt99'];?></a>
								</li>
								<li class="nav-item <?php echo $apiayarlari; ?>"> 
									<a class="nav-link <?php echo $apiayarlari; ?>" href="api-ayarlari.html"><?=@$admindil['txt4'];?></a>
								</li>								
								<li class="nav-item <?php echo $iletisimayarlari; ?>"> 
									<a class="nav-link <?php echo $iletisimayarlari; ?>" href="iletisim-ayarlari.html"><?=@$admindil['txt5'];?></a>
								</li>
								<li class="nav-item <?php echo $sosyalmedyaayarlari; ?>"> 
									<a class="nav-link <?php echo $sosyalmedyaayarlari; ?>" href="sosyal-medya-ayarlari.html"><?=@$admindil['txt6'];?></a>
								</li>
								<li class="nav-item <?php echo $modulayarlari; ?>"> 
									<a class="nav-link <?php echo $modulayarlari; ?>" href="modul-ayarlari.html"><?=@$admindil['txt7'];?></a>
								</li>
								<li class="nav-item <?php echo $hesapnumaralarimiz; ?>"> 
									<a class="nav-link <?php echo $hesapnumaralarimiz; ?>" href="hesap-numaralarimiz.html">Hesap Numaralarımız</a>
								</li>
								<li class="nav-item <?php echo $sitebakimmodu; ?>"> 
									<a class="nav-link <?php echo $sitebakimmodu; ?>" href="site-bakim-modu.html"><?=@$admindil['txt9'];?></a>
								</li>
								<li class="nav-item <?php echo $mailayarlari; ?>"> 
									<a class="nav-link <?php echo $mailayarlari; ?>" href="mail-ayarlari.html"><?=@$admindil['txt10'];?></a>
								</li>
							<li class="nav-item <?php echo $smsayarlari; ?>"> 
								<a class="nav-link <?php echo $smsayarlari; ?>" href="sms-ayarlari.html"><?=@$admindil['txt11'];?></a>
							</li>

							<li class="nav-item <?php echo $sanalposlar; ?>"> 
								<a class="nav-link <?php echo $sanalposlar; ?>" href="sanal-poslar.html"><?=@$admindil['txt100'];?></a>
							</li>
							<li class="nav-item <?php echo $arkaplanayarlari; ?>"> 
								<a class="nav-link <?php echo $arkaplanayarlari; ?>" href="arkaplan-ayarlari.html"><?=@$admindil['txt12'];?></a>
							</li>
							<li class="nav-item <?php echo $anasayfaalansiralama; ?>"> 
								<a class="nav-link <?php echo $anasayfaalansiralama; ?>" href="anasayfa-alan-siralama.html">Anasayfa Alan Sıralama</a>
							</li>
						</ul>
					</div>
				</li>
					
					<li class="nav-item <?php echo $dilekle; ?> <?php echo $dillistele; ?> <?php echo $admindilduzenle; ?>">
						<a class="nav-link" data-toggle="collapse" href="#dil-yonetimi" aria-expanded="false" aria-controls="dil-yonetimi">
						<i class="icon-globe menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt13'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $dilshow;?>" id="dil-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $dilekle; ?>"> 
									<a class="nav-link <?php echo $dilekle; ?>" href="dil-ekle.html"><?=@$admindil['txt16'];?></a>
								</li>
								<li class="nav-item <?php echo $dillistele; ?>"> 
									<a class="nav-link <?php echo $dillistele; ?>" href="dil-listele.html"><?=@$admindil['txt15'];?></a>
								</li>
								<li class="nav-item <?php echo $admindilduzenle; ?>"> 
									<a class="nav-link <?php echo $admindilduzenle; ?>" href="admin-dil-duzenle.html"><?=@$admindil['txt15_1'];?></a>
								</li>						
							</ul>
						</div>
					</li>
					
					<li class="nav-item <?php echo $headermenu; ?> <?php echo $footermenu; ?> <?php echo $topmenu; ?> <?php echo $slidermenu; ?> <?php echo $ortamenu; ?> <?php echo $kolaymenu; ?>">
						<a class="nav-link" data-toggle="collapse" href="#menu-yonetimi" aria-expanded="false" aria-controls="menu-yonetimi">
						<i class="icon-menu menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt17'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $menushow;?>" id="menu-yonetimi">
							<ul class="nav flex-column sub-menu">								
								<li class="nav-item <?php echo $headermenu; ?>"> 
									<a class="nav-link <?php echo $headermenu; ?>" href="header-menu.html"><?=@$admindil['txt18'];?></a>
								</li>
								<li class="nav-item <?php echo $footermenu; ?>"> 
									<a class="nav-link <?php echo $footermenu; ?>" href="footer-menu.html"><?=@$admindil['txt22'];?></a>
								</li>
								<li class="nav-item <?php echo $topmenu; ?>"> 
									<a class="nav-link <?php echo $topmenu; ?>" href="top-menu.html"><?=@$admindil['txt35'];?></a>
								</li>
								<li class="nav-item <?php echo $slidermenu; ?>"> 
									<a class="nav-link <?php echo $slidermenu; ?>" href="slider-menu.html"><?=@$admindil['txt37'];?></a>
								</li>
								<li class="nav-item <?php echo $ortamenu; ?>"> 
									<a class="nav-link <?php echo $ortamenu; ?>" href="orta-menu.html"><?=@$admindil['txt20'];?></a>
								</li>
								<li class="nav-item <?php echo $kolaymenu; ?>"> 
									<a class="nav-link <?php echo $kolaymenu; ?>" href="kolay-menu.html"><?=@$admindil['txt110'];?></a>
								</li>
								<li class="nav-item <?php echo $sabitmenu; ?>"> 
									<a class="nav-link <?php echo $sabitmenu; ?>" href="sabit-linkler.html"><?=@$admindil['txt24'];?></a>
								</li>
							</ul>
						</div>
					</li>					
					
					<li class="nav-item <?php echo $rehberim; ?> <?php echo $rehberekle; ?> <?php echo $topluemail; ?> <?php echo $toplusms; ?> <?php echo $bildirimsablonlari; ?>">
						<a class="nav-link" data-toggle="collapse" href="#rehber-yonetimi" aria-expanded="false" aria-controls="rehber-yonetimi">
						<i class="icon-people menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt26'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $rehbershow;?>" id="rehber-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $rehberim; ?> <?php echo $rehberekle; ?>"> 
									<a class="nav-link <?php echo $rehberim; ?> <?php echo $rehberekle; ?>" href="rehberim.html"><?=@$admindil['txt27'];?></a>
								</li>
								<li class="nav-item <?php echo $topluemail; ?>"> 
									<a class="nav-link <?php echo $topluemail; ?>" href="toplu-email.html"><?=@$admindil['txt30'];?></a>
								</li>
								<li class="nav-item <?php echo $toplusms; ?>"> 
									<a class="nav-link <?php echo $toplusms; ?>" href="toplu-sms.html"><?=@$admindil['txt31'];?></a>
								</li>
								<li class="nav-item <?php echo $bildirimsablonlari; ?>"> 
									<a class="nav-link <?php echo $bildirimsablonlari; ?>" href="bildirim-sablonlari.html"><?=@$admindil['txt32'];?></a>
								</li>
							</ul>
						</div>
					</li>
					
					<li class="nav-item sidebar-category mt-4">
						<span style="margin-left: -10px;"><?=@$admindil['txt34'];?></span>
					</li>
					<li class="nav-item <?php echo $bagisekle; ?> <?php echo $bagislistele; ?> <?php echo $bagis_kategori; ?> <?php echo $bagis_turleri; ?> <?php echo $gelen_bagislar; ?>">
						<a class="nav-link" data-toggle="collapse" href="#bagis-yonetimi" aria-expanded="false" aria-controls="bagis-yonetimi">
						<i class="ti-heart menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt57'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $bagisshow;?>" id="bagis-yonetimi">
							<ul class="nav flex-column sub-menu">
									<li class="nav-item <?php echo $bagismoduller; ?>"> 
										<a class="nav-link <?php echo $bagismoduller; ?>" href="bagis-moduller.html"><i class="ti-layout-grid2"></i> Bağış Modülleri</a>
									</li>
									<li class="nav-item <?php echo $bagisekle; ?>"> 
										<a class="nav-link <?php echo $bagisekle; ?>" href="bagis-ekle.html"><?=@$admindil['txt60'];?></a>
									</li>
									<li class="nav-item <?php echo $bagislistele; ?>"> 
										<a class="nav-link <?php echo $bagislistele; ?>" href="bagis-listele.html"><?=@$admindil['txt59'];?></a>
									</li>
									<li class="nav-item <?php echo $bagis_kategori; ?>"> 
										<a class="nav-link <?php echo $bagis_kategori; ?>" href="bagis-kategoriler.html"><?=@$admindil['txt131'];?></a>
									</li>
									<li class="nav-item <?php echo $bagis_turleri; ?>"> 
										<a class="nav-link <?php echo $bagis_turleri; ?>" href="bagis-turleri.html"><i class="ti-list"></i> Bağış Türleri</a>
									</li>
									<li class="nav-item <?php echo $gelen_bagislar; ?>">
										<a class="nav-link <?php echo $gelen_bagislar; ?>" href="gelen-bagislar.html"><?=@$admindil['txt133'];?> <span style="padding: 2px 5px;margin-right:0px;" class="badge badge-outline-success"><?php echo cVCLmHLxbS_tumu("bagis_odeme",2); ?></span></a>
									</li>
									<li class="nav-item <?php echo isset($hamiler) ? $hamiler : ''; ?>">
										<a class="nav-link <?php echo isset($hamiler) ? $hamiler : ''; ?>" href="hamiler.html"><i class="ti-user"></i> Hamiler Listesi</a>
									</li>
									<li class="nav-item <?php echo isset($sertifikalar) ? $sertifikalar : ''; ?>">
										<a class="nav-link <?php echo isset($sertifikalar) ? $sertifikalar : ''; ?>" href="sertifikalar.html"><i class="ti-certificate"></i> Sertifikalar</a>
									</li>
								</ul>
						</div>
					</li>
					
					<li class="nav-item <?php echo $yetimekle; ?> <?php echo $yetimlistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#yetim-yonetimi" aria-expanded="false" aria-controls="yetim-yonetimi">
						<i class="icon-user menu-icon"></i>
						<span class="menu-title">Yetim Yönetimi</span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $yetimshow;?>" id="yetim-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $yetimekle; ?>"> 
									<a class="nav-link <?php echo $yetimekle; ?>" href="yetim-ekle.html">Yetim Ekle</a>
								</li>
								<li class="nav-item <?php echo $yetimlistele; ?>"> 
									<a class="nav-link <?php echo $yetimlistele; ?>" href="yetim-listele.html">Yetim Listesi</a>
								</li>
							</ul>
						</div>
					</li>
					<li style="display:none;" class="nav-item <?php echo $aidatekle; ?> <?php echo $aidatlistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#aidat-yonetimi" aria-expanded="false" aria-controls="aidat-yonetimi">
						<i class="icon-credit-card menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt134'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $aidatshow;?>" id="aidat-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $aidatekle; ?>"> 
									<a class="nav-link <?php echo $aidatekle; ?>" href="aidat-ekle.html"><?=@$admindil['txt137'];?></a>
								</li>
								<li class="nav-item <?php echo $aidatlistele; ?>"> 
									<a class="nav-link <?php echo $aidatlistele; ?>" href="aidat-listele.html"><?=@$admindil['txt136'];?></a>
								</li>
							</ul>
						</div>
					</li>
					<li class="nav-item position-relative <?php echo $projeler; ?> <?php echo $proje_kategori; ?>">
						<a class="nav-link" data-toggle="collapse" href="#proje-yonetimi" aria-expanded="false" aria-controls="proje-yonetimi">
						<i class="icon-layers menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt42'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $projeshow;?>" id="proje-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $projeler; ?>"> 
									<a class="nav-link <?php echo $projeler; ?>" href="projeler.html"><?=@$admindil['txt44'];?></a>
								</li>
								<li class="nav-item <?php echo $proje_kategori; ?>"> 
									<a class="nav-link <?php echo $proje_kategori; ?>" href="proje-kategoriler.html"><?=@$admindil['txt47'];?></a>
								</li>
							</ul>
						</div>
					</li>					
					
					<li class="nav-item <?php echo $sayfaekle; ?> <?php echo $sayfalistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#sayfa-yonetimi" aria-expanded="false" aria-controls="sayfa-yonetimi">
						<i class="icon-note menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt49'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $sayfalarshow;?>" id="sayfa-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $sayfaekle; ?>"> 
									<a class="nav-link <?php echo $sayfaekle; ?>" href="sayfa-ekle.html"><?=@$admindil['txt52'];?></a>
								</li>
								<li class="nav-item <?php echo $sayfalistele; ?>"> 
									<a class="nav-link <?php echo $sayfalistele; ?>" href="sayfa-listele.html"><?=@$admindil['txt51'];?></a>
								</li>
							</ul>
						</div>
					</li>
					<li class="nav-item <?php echo $hizmetekle; ?> <?php echo $hizmetlistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#hizmet-yonetimi" aria-expanded="false" aria-controls="hizmet-yonetimi">
						<i class="icon-docs menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt53'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $hizmetlershow;?>" id="hizmet-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $hizmetekle; ?>"> 
									<a class="nav-link <?php echo $hizmetekle; ?>" href="hizmet-ekle.html"><?=@$admindil['txt56'];?></a>
								</li>
								<li class="nav-item <?php echo $hizmetlistele; ?>"> 
									<a class="nav-link <?php echo $hizmetlistele; ?>" href="hizmet-listele.html"><?=@$admindil['txt55'];?></a>
								</li>
							</ul>
						</div>
					</li>
					<li class="nav-item <?php echo $profilekle; ?> <?php echo $profillistele; ?> <?php echo $profil_kategori; ?>">
						<a class="nav-link" data-toggle="collapse" href="#profil-yonetimi" aria-expanded="false" aria-controls="profil-yonetimi">
						<i class="icon-user menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt119'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $profilshow;?>" id="profil-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $profilekle; ?>"> 
									<a class="nav-link <?php echo $profilekle; ?>" href="profil-ekle.html"><?=@$admindil['txt122'];?></a>
								</li>
								<li class="nav-item <?php echo $profillistele; ?>"> 
									<a class="nav-link <?php echo $profillistele; ?>" href="profil-listele.html"><?=@$admindil['txt121'];?></a>
								</li>
								<li class="nav-item <?php echo $profil_kategori; ?>"> 
									<a class="nav-link <?php echo $profil_kategori; ?>" href="profil-kategoriler.html"><?=@$admindil['txt124'];?></a>
								</li>
							</ul>
						</div>
					</li>
					<li class="nav-item <?php echo $birimekle; ?> <?php echo $birimlistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#birim-yonetimi" aria-expanded="false" aria-controls="birim-yonetimi">
						<i class="icon-docs menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt115'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $birimlershow;?>" id="birim-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $birimekle; ?>"> 
									<a class="nav-link <?php echo $birimekle; ?>" href="birim-ekle.html"><?=@$admindil['txt118'];?></a>
								</li>
								<li class="nav-item <?php echo $birimlistele; ?>"> 
									<a class="nav-link <?php echo $birimlistele; ?>" href="birim-listele.html"><?=@$admindil['txt117'];?></a>
								</li>
							</ul>
						</div>
					</li>										
										
					<li class="nav-item <?php echo $etkinlikekle; ?> <?php echo $etkinliklistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#etkinlik-yonetimi" aria-expanded="false" aria-controls="etkinlik-yonetimi">
						<i class="icon-calendar menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt73'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $etkinlikshow;?>" id="etkinlik-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $etkinlikekle; ?>"> 
									<a class="nav-link <?php echo $etkinlikekle; ?>" href="etkinlik-ekle.html"><?=@$admindil['txt76'];?></a>
								</li>
								<li class="nav-item <?php echo $etkinliklistele; ?>"> 
									<a class="nav-link <?php echo $etkinliklistele; ?>" href="etkinlik-listele.html"><?=@$admindil['txt75'];?></a>
								</li>
							</ul>
						</div>
					</li>
					<li class="nav-item <?php echo $kararekle; ?> <?php echo $kararlistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#meclis-kararlari" aria-expanded="false" aria-controls="meclis-kararlari">
						<i class="icon-folder-alt menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt61'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $kararshow; ?>" id="meclis-kararlari">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $kararekle; ?>"> 
									<a class="nav-link <?php echo $kararekle; ?>" href="karar-ekle.html"><?=@$admindil['txt64'];?></a>
								</li>
								<li class="nav-item <?php echo $kararlistele; ?>"> 
									<a class="nav-link <?php echo $kararlistele; ?>" href="karar-listele.html"><?=@$admindil['txt63'];?></a>
								</li>
							</ul>
						</div>
					</li>
					<li class="nav-item <?php echo $faaliyetekle; ?> <?php echo $faaliyetlistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#faaliyet-raporlari" aria-expanded="false" aria-controls="faaliyet-raporlari">
						<i class="icon-book-open menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt126'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $faaliyetshow; ?>" id="faaliyet-raporlari">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $faaliyetekle; ?>"> 
									<a class="nav-link <?php echo $faaliyetekle; ?>" href="faaliyet-ekle.html"><?=@$admindil['txt129'];?></a>
								</li>
								<li class="nav-item <?php echo $faaliyetlistele; ?>"> 
									<a class="nav-link <?php echo $faaliyetlistele; ?>" href="faaliyet-listele.html"><?=@$admindil['txt128'];?></a>
								</li>
							</ul>
						</div>
					</li>

					<li class="nav-item <?php echo $duyuruekle; ?> <?php echo $duyurulistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#duyuru-yonetimi" aria-expanded="false" aria-controls="duyuru-yonetimi">
						<i class="ti-announcement menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt105'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $duyurushow;?>" id="duyuru-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $duyurulistele; ?>"> 
									<a class="nav-link <?php echo $duyurulistele; ?>" href="duyuru-listele.html"><?=@$admindil['txt107'];?></a>
								</li>
								<li class="nav-item <?php echo $duyuruekle; ?>"> 
									<a class="nav-link <?php echo $duyuruekle; ?>" href="duyuru-ekle.html"><?=@$admindil['txt108'];?></a>
								</li>								
							</ul>
						</div>
					</li>
					
					<li class="nav-item <?php echo $impactreachekle; ?> <?php echo $impactreachlistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#impact-reach-yonetimi" aria-expanded="false" aria-controls="impact-reach-yonetimi">
						<i class="fas fa-chart-line menu-icon"></i>
						<span class="menu-title">İstatistik Yönetimi</span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $impactreachshow;?>" id="impact-reach-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $impactreachlistele; ?>"> 
									<a class="nav-link <?php echo $impactreachlistele; ?>" href="impact_reach_listele.html">İstatistikleri Listele</a>
								</li>
								<li class="nav-item <?php echo $impactreachekle; ?>"> 
									<a class="nav-link <?php echo $impactreachekle; ?>" href="impact_reach_ekle.html">Yeni İstatistik Ekle</a>
								</li>								
							</ul>
						</div>
					</li>
					
					<li class="nav-item <?php echo $uzmangorusekle; ?> <?php echo $uzmangoruslistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#uzman-gorus-yonetimi" aria-expanded="false" aria-controls="uzman-gorus-yonetimi">
						<i class="icon-people menu-icon"></i>
						<span class="menu-title">Uzman Görüşleri</span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $uzmangorusshow;?>" id="uzman-gorus-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $uzmangoruslistele; ?>"> 
									<a class="nav-link <?php echo $uzmangoruslistele; ?>" href="uzman_gorus_listele.html">Görüşleri Listele</a>
								</li>
								<li class="nav-item <?php echo $uzmangorusekle; ?>"> 
									<a class="nav-link <?php echo $uzmangorusekle; ?>" href="uzman_gorus_ekle.html">Yeni Görüş Ekle</a>
								</li>								
							</ul>
						</div>
					</li>
					
				<li class="nav-item <?php echo $programekle; ?> <?php echo $programlistele; ?>">
					<a class="nav-link" data-toggle="collapse" href="#programlar-yonetimi" aria-expanded="false" aria-controls="programlar-yonetimi">
					<i class="fas fa-graduation-cap menu-icon"></i>
					<span class="menu-title"><?=@$admindil['txt140'];?></span>
					<i class="menu-arrow"></i>
					</a>
					<div class="collapse <?php echo $programshow;?>" id="programlar-yonetimi">
						<ul class="nav flex-column sub-menu">
							<li class="nav-item <?php echo $programlistele; ?>"> 
								<a class="nav-link <?php echo $programlistele; ?>" href="program_listele.html"><?=@$admindil['txt142'];?></a>
							</li>
							<li class="nav-item <?php echo $programekle; ?>"> 
								<a class="nav-link <?php echo $programekle; ?>" href="program_ekle.html"><?=@$admindil['txt143'];?></a>
							</li>
							<li class="nav-item <?php echo $derslikdurumlistele; ?>"> 
								<a class="nav-link <?php echo $derslikdurumlistele; ?>" href="derslik_durum_listele.html"><i class="ti-map"></i> Derslik Durumları</a>
							</li>
							<li class="nav-item <?php echo $derslikdurumekle; ?>"> 
								<a class="nav-link <?php echo $derslikdurumekle; ?>" href="derslik_durum_ekle.html"><i class="ti-plus"></i> Yeni Derslik Ekle</a>
							</li>								
						</ul>
					</div>
				</li>

				<li class="nav-item <?php echo $karakterprogramekle; ?> <?php echo $karakterprogramlistele; ?>">
					<a class="nav-link" data-toggle="collapse" href="#karakter-programlari-yonetimi" aria-expanded="false" aria-controls="karakter-programlari-yonetimi">
					<i class="fas fa-heart menu-icon"></i>
					<span class="menu-title">Karakter Programları</span>
					<i class="menu-arrow"></i>
					</a>
					<div class="collapse <?php echo $karakterprogramshow;?>" id="karakter-programlari-yonetimi">
						<ul class="nav flex-column sub-menu">
							<li class="nav-item <?php echo $karakterprogramlistele; ?>">
								<a class="nav-link <?php echo $karakterprogramlistele; ?>" href="karakter_program_listele.html">Program Listesi</a>
							</li>
							<li class="nav-item <?php echo $karakterprogramekle; ?>">
								<a class="nav-link <?php echo $karakterprogramekle; ?>" href="karakter_program_ekle.html">Yeni Program Ekle</a>
							</li>
							<li class="nav-item <?php echo $karakterprogramayar; ?>">
								<a class="nav-link <?php echo $karakterprogramayar; ?>" href="karakter_program_ayar.html"><i class="ti-settings"></i> Sayfa Üst Metni</a>
							</li>
						</ul>
					</div>
				</li>

				<li class="nav-item <?php echo $ogrenme_deneyimi_ekle; ?> <?php echo $ogrenme_deneyimi_listele; ?>">
					<a class="nav-link" data-toggle="collapse" href="#ogrenme-deneyimleri-yonetimi" aria-expanded="false" aria-controls="ogrenme-deneyimleri-yonetimi">
					<i class="ti-light-bulb menu-icon"></i>
					<span class="menu-title">Öğrenme Deneyimleri</span>
					<i class="menu-arrow"></i>
					</a>
					<div class="collapse <?php echo $ogrenme_deneyimi_show;?>" id="ogrenme-deneyimleri-yonetimi">
						<ul class="nav flex-column sub-menu">
							<li class="nav-item <?php echo $ogrenme_deneyimi_listele; ?>"> 
								<a class="nav-link <?php echo $ogrenme_deneyimi_listele; ?>" href="ogrenme_deneyimi_listele.html">Listele</a>
							</li>
							<li class="nav-item <?php echo $ogrenme_deneyimi_ekle; ?>"> 
								<a class="nav-link <?php echo $ogrenme_deneyimi_ekle; ?>" href="ogrenme_deneyimi_ekle.html">Yeni Ekle</a>
							</li>
						</ul>
					</div>
				</li>
				
				<li class="nav-item <?php echo $destekleme_yollari_ekle; ?> <?php echo $destekleme_yollari_listele; ?>">
					<a class="nav-link" data-toggle="collapse" href="#destekleme-yollari-yonetimi" aria-expanded="false" aria-controls="destekleme-yollari-yonetimi">
					<i class="ti-hand-stop menu-icon"></i>
					<span class="menu-title">Destekleme Yolları</span>
					<i class="menu-arrow"></i>
					</a>
					<div class="collapse <?php echo $destekleme_yollari_show;?>" id="destekleme-yollari-yonetimi">
						<ul class="nav flex-column sub-menu">
							<li class="nav-item <?php echo $destekleme_yollari_listele; ?>"> 
								<a class="nav-link <?php echo $destekleme_yollari_listele; ?>" href="destekleme_yollari_listele.html">Listele</a>
							</li>
							<li class="nav-item <?php echo $destekleme_yollari_ekle; ?>"> 
								<a class="nav-link <?php echo $destekleme_yollari_ekle; ?>" href="destekleme_yollari_ekle.html">Yeni Ekle</a>
							</li>
						</ul>
					</div>
				</li>
					<li class="nav-item <?php echo $haberekle; ?> <?php echo $haberlistele; ?> <?php echo $haberkategorileri; ?>">
						<a class="nav-link" data-toggle="collapse" href="#haber-yonetimi" aria-expanded="false" aria-controls="haber-yonetimi">
						<i class="icon-book-open menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt77'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $habershow; ?>" id="haber-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $haberekle; ?>"> 
									<a class="nav-link <?php echo $haberekle; ?>" href="haber-ekle.html"><?=@$admindil['txt80'];?></a>
								</li>
								<li class="nav-item <?php echo $haberlistele; ?>"> 
									<a class="nav-link <?php echo $haberlistele; ?>" href="haber-listele.html"><?=@$admindil['txt79'];?></a>
								</li>
								<li class="nav-item <?php echo $haberkategorileri; ?>"> 
									<a class="nav-link <?php echo $haberkategorileri; ?>" href="haber-kategoriler.html"><?=@$admindil['txt40'];?></a>
								</li>
							</ul>
						</div>
					</li>					
					<li class="nav-item <?php echo $sliderekle; ?> <?php echo $sliderlistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#slider-yonetimi" aria-expanded="false" aria-controls="slider-yonetimi">
						<i class="icon-picture menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt81'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $slidershow; ?>" id="slider-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $sliderekle; ?>"> 
									<a class="nav-link <?php echo $sliderekle; ?>" href="slider-ekle.html"><?=@$admindil['txt84'];?></a>
								</li>
								<li class="nav-item <?php echo $sliderlistele; ?>"> 
									<a class="nav-link <?php echo $sliderlistele; ?>" href="slider-listele.html"><?=@$admindil['txt83'];?></a>
								</li>
							</ul>
						</div>
					</li>
					<li class="nav-item <?php echo $galeriekle; ?> <?php echo $galerilistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#foto-galeri" aria-expanded="false" aria-controls="foto-galeri">
						<i class="icon-camera menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt85'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $galerishow; ?>" id="foto-galeri">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $galeriekle; ?>"> 
									<a class="nav-link <?php echo $galeriekle; ?>" href="galeri-ekle.html"><?=@$admindil['txt88'];?></a>
								</li>
								<li class="nav-item <?php echo $galerilistele; ?>"> 
									<a class="nav-link <?php echo $galerilistele; ?>" href="galeri-listele.html"><?=@$admindil['txt87'];?></a>
								</li>
							</ul>
						</div>
					</li>
					<li class="nav-item <?php echo $videoekle; ?> <?php echo $videolistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#video-galeri" aria-expanded="false" aria-controls="video-galeri">
						<i class="icon-social-youtube menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt89'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $videoshow; ?>" id="video-galeri">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $videoekle; ?>"> 
									<a class="nav-link <?php echo $videoekle; ?>" href="video-ekle.html"><?=@$admindil['txt92'];?></a>
								</li>
								<li class="nav-item <?php echo $videolistele; ?>"> 
									<a class="nav-link <?php echo $videolistele; ?>" href="video-listele.html"><?=@$admindil['txt91'];?></a>
								</li>
							</ul>
						</div>
					</li>
					<li class="nav-item <?php echo $bilgilendirmeekle; ?> <?php echo $bilgilendirmelistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#bilgilendirme-galeri" aria-expanded="false" aria-controls="bilgilendirme-galeri">
						<i class="icon-social-youtube menu-icon"></i>
						<span class="menu-title">Bilgilendirme Yönetimi</span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $bilgilendirmeshow; ?>" id="bilgilendirme-galeri">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $bilgilendirmeekle; ?>"> 
									<a class="nav-link <?php echo $bilgilendirmeekle; ?>" href="bilgilendirme-ekle.html">Bilgilendirme Ekle</a>
								</li>
								<li class="nav-item <?php echo $bilgilendirmelistele; ?>"> 
									<a class="nav-link <?php echo $bilgilendirmelistele; ?>" href="bilgilendirme-listele.html">Bilgilendirme Listele</a>
								</li>
							</ul>
						</div>
					</li>
					<li class="nav-item <?php echo $dosyaekle; ?> <?php echo $dosyalistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#dosya-yonetimi" aria-expanded="false" aria-controls="dosya-yonetimi">
						<i class="mdi mdi-file-multiple menu-icon"></i>
						<span class="menu-title">Dosyalarımız</span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $dosyashow; ?>" id="dosya-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $dosyaekle; ?>">
									<a class="nav-link <?php echo $dosyaekle; ?>" href="dosya-ekle.html">Dosya Ekle</a>
								</li>
								<li class="nav-item <?php echo $dosyalistele; ?>">
									<a class="nav-link <?php echo $dosyalistele; ?>" href="dosya-listele.html">Dosya Listele</a>
								</li>
							</ul>
						</div>
					</li>
					<li class="nav-item <?php echo $yoneticiekle; ?> <?php echo $yoneticilistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#yoneticiler" aria-expanded="false" aria-controls="yoneticiler">
						<i class="icon-user-follow menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt93'];?></span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $yoneticishow; ?>" id="yoneticiler">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $yoneticiekle; ?>">
									<a class="nav-link <?php echo $yoneticiekle; ?>" href="yonetici-ekle.html"><?=@$admindil['txt96'];?> </a>
								</li>
								<li class="nav-item <?php echo $yoneticilistele; ?>">
									<a class="nav-link <?php echo $yoneticilistele; ?>" href="yonetici-listele.html"> <?=@$admindil['txt95'];?> </a>
								</li>
							</ul>
						</div>
					</li>
					<li class="nav-item sidebar-category mt-4">
						<span style="margin-left: -10px;"><?=@$admindil['txt97'];?></span>
					</li>
									
					<li class="nav-item <?php echo $mesajlar; ?>">
						<a class="nav-link" href="mesajlar.html">
						<i class="icon-envelope menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt98'];?></span> <span style="padding: 2px 5px;" class="badge badge-outline-danger"><?php echo cVCLmHLxbS_tumu("mesajlar",3); ?></span>
						</a>
					</li>
					<li class="nav-item <?php echo $program_mesajlar; ?>">
						<a class="nav-link" href="program_mesajlar.html">
						<i class="icon-graduation menu-icon"></i>
						<span class="menu-title">Program Mesajları</span> <span style="padding: 2px 5px;" class="badge badge-outline-danger"><?php echo cVCLmHLxbS_tumu("okullar_program_talep",3); ?></span>
						</a>
					</li>
					<li class="nav-item <?php echo $randevuhizmetekle; ?> <?php echo $randevuhizmetlistele; ?> <?php echo $randevulistele; ?>">
						<a class="nav-link" data-toggle="collapse" href="#randevu-yonetimi" aria-expanded="false" aria-controls="randevu-yonetimi">
						<i class="icon-calendar menu-icon"></i>
						<span class="menu-title">Randevu Yönetimi</span>
						<i class="menu-arrow"></i>
						</a>
						<div class="collapse <?php echo $randevushow;?>" id="randevu-yonetimi">
							<ul class="nav flex-column sub-menu">
								<li class="nav-item <?php echo $randevuhizmetekle; ?>"> 
									<a class="nav-link <?php echo $randevuhizmetekle; ?>" href="randevu-hizmet-ekle.html">Randevu Hizmeti Ekle</a>
								</li>
								<li class="nav-item <?php echo $randevuhizmetlistele; ?>"> 
									<a class="nav-link <?php echo $randevuhizmetlistele; ?>" href="randevu-hizmet-listele.html">Randevu Hizmetleri</a>
								</li>
								<li class="nav-item <?php echo $randevulistele; ?>"> 
									<a class="nav-link <?php echo $randevulistele; ?>" href="randevu-listele.html">Gelen Randevular <span style="padding: 2px 5px;" class="badge badge-outline-success"><?php echo cVCLmHLxbS_tumu("randevular",2); ?></span></a>
								</li>
							</ul>
						</div>
					</li>					

					<li class="nav-item sidebar-category mt-4">
						<span style="margin-left: -10px;"><?=@$admindil['txt102'];?></span>
					</li>
					<li class="nav-item <?php echo $notdefteri; ?>"> 
						<a class="nav-link" href="not-defteri.html">
						<i class="icon-calendar menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt103'];?></span>
						</a>
					</li>
					<li class="nav-item <?php echo $contactsection; ?>">
						<a class="nav-link" href="contact-section-settings.html">
						<i class="icon-envelope menu-icon"></i>
						<span class="menu-title">İletişim Bölümü Ayarları</span>
						</a>
					</li>
					<li class="nav-item <?php echo (@$_GET['sayfa'] == 'test-ayarlari') ? 'active' : ''; ?>">
						<a class="nav-link <?php echo (@$_GET['sayfa'] == 'test-ayarlari') ? 'active' : ''; ?>" href="test-ayarlari.html">
						<i class="icon-settings menu-icon"></i>
						<span class="menu-title">Test Ayarları</span>
						</a>
					</li>
					<li class="nav-item <?php echo (@$_GET['sayfa'] == 'test-sorulari') ? 'active' : ''; ?>">
						<a class="nav-link <?php echo (@$_GET['sayfa'] == 'test-sorulari') ? 'active' : ''; ?>" href="test-sorulari.html">
						<i class="icon-pencil menu-icon"></i>
						<span class="menu-title">Test Soruları</span>
						</a>
					</li>
					<li class="nav-item <?php echo (@$_GET['sayfa'] == 'test-sonuclari') ? 'active' : ''; ?>">
						<a class="nav-link <?php echo (@$_GET['sayfa'] == 'test-sonuclari') ? 'active' : ''; ?>" href="test-sonuclari.html">
						<i class="icon-notebook menu-icon"></i>
						<span class="menu-title">Test Sonuçları</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="../_class/yonetim_islem.php?cikis=ok">
						<i class="icon-power menu-icon"></i>
						<span class="menu-title"><?=@$admindil['txt104'];?></span>
						</a>
					</li>
				</ul>
			</nav>
			<!-- partial -->
			<!-- Start content -->
			<div class="main-panel">
				<div class="content-wrapper">
				<?php if($mevcutDil['anadil'] != 1): ?>
				<div class="alert alert-fill-danger" role="alert">
                    <i class="mdi mdi-alert-circle"></i>
                    Şuanda <strong><?=@$mevcutDil['adi'];?></strong> dil versiyonundasınız. Yaptığınız tüm işlemler <strong><?=@$mevcutDil['adi'];?></strong> dili için geçerli olacaktır.
                  </div>
				<?php endif; ?>
				<?php 
				if(isset($_GET['sayfa']))
				{
					$s = $_GET['sayfa'];
					switch($s)
					{			
						case 'anasayfa';
						require_once("sayfalar/anasayfa.php");
						break;	
						
						case 'admin-dil-duzenle';
						require_once("sayfalar/admin_dil_ayar.php");
						break;
						
						case 'genel-ayarlar';
						require_once("sayfalar/genel_ayarlar.php");
						break;
						
						case 'baskan-ayarlar';
						require_once("sayfalar/baskan_ayarlar.php");
						break;
						
						case 'popup';
						require_once("sayfalar/popup.php");
						break;
						
						case 'sabit-linkler';
						require_once("sayfalar/sabit_linkler.php");
						break;
						
						case 'hizmet-ekle';
						require_once("sayfalar/hizmet_ekle.php");
						break;
						
						case 'hizmet-listele';
						require_once("sayfalar/hizmet_listele.php");
						break;
						
						case 'birim-ekle';
						require_once("sayfalar/birim_ekle.php");
						break;
						
						case 'birim-listele';
						require_once("sayfalar/birim_listele.php");
						break;
						
						case 'api-ayarlari';
						require_once("sayfalar/api_ayarlari.php");
						break;
						
						case 'iletisim-ayarlari';
						require_once("sayfalar/iletisim_ayarlari.php");
						break;
						
						case 'sosyal-medya-ayarlari';
						require_once("sayfalar/sosyal_medya_ayarlari.php");
						break;
						
						case 'modul-ayarlari';
						require_once("sayfalar/modul_ayarlari.php");
						break;
						
						case 'limit-ayarlari';
						require_once("sayfalar/limit_ayarlari.php");
						break;
						
						case 'site-bakim-modu';
						require_once("sayfalar/site_bakim_modu.php");
						break;
						
						case 'mail-ayarlari';
						require_once("sayfalar/mail_ayarlari.php");
						break;
						
					case 'sms-ayarlari';
					require_once("sayfalar/sms_ayarlari.php");
					break;
										case 'hami-detay';
					require_once("sayfalar/hami_detay.php");
					break;
					
					case 'bildirim-yonetimi';
					require_once("sayfalar/bildirim_yonetimi.php");
					break;
					
					case 'sertifikalar';
					require_once("sayfalar/sertifikalar.php");
					break;
					
					case 'sanal-poslar';
					require_once("sayfalar/sanal_poslar.php");
					break;
						
					case 'arkaplan-ayarlari';
					require_once("sayfalar/arkaplan_ayarlari.php");
					break;
					
					case 'anasayfa-alan-siralama';
					require_once("sayfalar/anasayfa_alan_siralama.php");
					break;
						
						case 'sayfa-ekle';
						require_once("sayfalar/sayfa_ekle.php");
						break;
						
						case 'sayfa-listele';
						require_once("sayfalar/sayfa_listele.php");
						break;
						
						case 'ekip-ekle';
						require_once("sayfalar/ekip_ekle.php");
						break;
						
						case 'ekip-listele';
						require_once("sayfalar/ekip_listele.php");
						break;
						
						case 'ilan-ekle';
						require_once("sayfalar/ilan_ekle.php");
						break;
						
						case 'ilan-listele';
						require_once("sayfalar/ilan_listele.php");
						break;
						
						case 'etkinlik-ekle';
						require_once("sayfalar/etkinlik_ekle.php");
						break;
						
						case 'etkinlik-listele';
						require_once("sayfalar/etkinlik_listele.php");
						break;
						
						case 'karar-ekle';
						require_once("sayfalar/karar_ekle.php");
						break;
						
						case 'karar-listele';
						require_once("sayfalar/karar_listele.php");
						break;
						
						case 'faaliyet-ekle';
						require_once("sayfalar/faaliyet_ekle.php");
						break;
						
						case 'faaliyet-listele';
						require_once("sayfalar/faaliyet_listele.php");
						break;
						
						case 'ihale-ekle';
						require_once("sayfalar/ihale_ekle.php");
						break;
						
						case 'ihale-listele';
						require_once("sayfalar/ihale_listele.php");
						break;
						
						case 'haber-ekle';
						require_once("sayfalar/haber_ekle.php");
						break;
						
						case 'haber-listele';
						require_once("sayfalar/haber_listele.php");
						break;
						
						case 'haber-fotograflar';
						require_once("sayfalar/haber_fotograflar.php");
						break;
						
						case 'haber-kategoriler';
						require_once("sayfalar/haber_kategoriler.php");
						break;
						
						case 'haber-kategori-ekle';
						require_once("sayfalar/haber_kategori_ekle.php");
						break;
						
						case 'haberler-excel';
						require_once("sayfalar/haberler_excel.php");
						break;
						
						case 'haberler-resim-excel';
						require_once("sayfalar/haberler_resim_excel.php");
						break;
						
						case 'slider-ekle';
						require_once("sayfalar/slider_ekle.php");
						break;
						
						case 'slider-listele';
						require_once("sayfalar/slider_listele.php");
						break;
						
						case 'top-menu';
						require_once("sayfalar/top_menu.php");
						break;
						
						case 'slider-menu';
						require_once("sayfalar/slider_menu.php");
						break;
						
						case 'orta-menu';
						require_once("sayfalar/orta_menu.php");
						break;
						
						case 'kolay-menu';
						require_once("sayfalar/kolay_menu.php");
						break;
						
						case 'header-menu';
						require_once("sayfalar/header_menu.php");
						break;
						
						case 'footer-menu';
						require_once("sayfalar/footer_menu.php");
						break;

						case 'hesap-numaralarimiz';
						require_once("sayfalar/hesap_numaralarimiz.php");
						break;
						
						case 'dil-ekle';
						require_once("sayfalar/dil_ekle.php");
						break;
						
						case 'dil-listele';
						require_once("sayfalar/dil_listele.php");
						break;

						case 'mesajlar';
						require_once("sayfalar/mesajlar.php");
						break;
						
						case 'program_mesajlar';
						require_once("sayfalar/program_mesajlar.php");
						break;
						
						case 'bildirim-sablonlari';
						require_once("sayfalar/bildirim_sablonlari.php");
						break;
						
						case 'sablon-duzenle';
						require_once("sayfalar/sablon_duzenle.php");
						break;
						
						case 'yonetici-ekle';
						require_once("sayfalar/yonetici_ekle.php");
						break;
						
						case 'yonetici-listele';
						require_once("sayfalar/yonetici_listele.php");
						break;
						
						case 'not-defteri';
						require_once("sayfalar/not_defteri.php");
						break;
						
						case 'rehberim';
						require_once("sayfalar/rehberim.php");
						break;
						
						case 'rehber-ekle';
						require_once("sayfalar/rehber_ekle.php");
						break;
						
						case 'toplu-email';
						require_once("sayfalar/toplu_email.php");
						break;
						
						case 'toplu-sms';
						require_once("sayfalar/toplu_sms.php");
						break;						
						
						case 'proje-kategoriler';
						require_once("sayfalar/proje_kategoriler.php");
						break;
						
						case 'proje-kategori-ekle';
						require_once("sayfalar/proje_kategori_ekle.php");
						break;
						
						case 'projeler';
						require_once("sayfalar/projeler.php");
						break;
						
						case 'proje-ekle';
						require_once("sayfalar/proje_ekle.php");
						break;

						case '404';
						require_once("sayfalar/404.php");
						break;		

						case 'galeri-ekle';
						require_once("sayfalar/galeri_ekle.php");
						break;

						case 'galeri-listele';
						require_once("sayfalar/galeri_listele.php");
						break;
						
						case 'fotograflar';
						require_once("sayfalar/fotograflar.php");
						break;
						
						case 'video-ekle';
						require_once("sayfalar/video_ekle.php");
						break;

						case 'video-listele';
						require_once("sayfalar/video_listele.php");
						break;
						
						case 'duyuru-ekle';
						require_once("sayfalar/duyuru_ekle.php");
						break;
						
						case 'duyuru-listele';
						require_once("sayfalar/duyuru_listele.php");
						break;
						
						case 'impact_reach_ekle';
						require_once("sayfalar/impact_reach_ekle.php");
						break;
						
						case 'impact_reach_listele';
						require_once("sayfalar/impact_reach_listele.php");
						break;
						
						case 'uzman_gorus_listele';
						require_once("sayfalar/uzman_gorus_listele.php");
						break;
						
						case 'uzman_gorus_ekle';
						require_once("sayfalar/uzman_gorus_ekle.php");
						break;
						

						
						case 'program_ekle';
						require_once("sayfalar/program_ekle.php");
						break;

					case 'program_listele';
					require_once("sayfalar/program_listele.php");
					break;

					case 'karakter_program_ekle';
					require_once("sayfalar/karakter_program_ekle.php");
					break;

					case 'karakter_program_listele';
					require_once("sayfalar/karakter_program_listele.php");
					break;

					case 'karakter_program_ayar';
					require_once("sayfalar/karakter_program_ayar.php");
					break;

					case 'ogrenme_deneyimi_listele';
					require_once("sayfalar/ogrenme_deneyimi_listele.php");
					break;
					
					case 'ogrenme_deneyimi_ekle';
					require_once("sayfalar/ogrenme_deneyimi_ekle.php");
					break;
					
					case 'destekleme_yollari_listele';
					require_once("sayfalar/destekleme_yollari_listele.php");
					break;
					
					case 'destekleme_yollari_ekle';
					require_once("sayfalar/destekleme_yollari_ekle.php");
					break;
					
					case 'derslik_durum_listele';
						require_once("sayfalar/derslik_durum_listele.php");
						break;
						
						case 'derslik_durum_ekle';
						require_once("sayfalar/derslik_durum_ekle.php");
						break;
						
						case 'derslik_durum_listele/islem/duzenle/id';
						require_once("sayfalar/derslik_durum_ekle.php");
						break;
						
						case 'profil-ekle';
						require_once("sayfalar/profil_ekle.php");
						break;
						
						case 'profil-listele';
						require_once("sayfalar/profil_listele.php");
						break;
						
						case 'profil-kategoriler';
						require_once("sayfalar/profil_kategoriler.php");
						break;
						
						case 'profil-kategori-ekle';
						require_once("sayfalar/profil_kategori_ekle.php");
						break;
						
						case 'bagis-kategoriler';
						require_once("sayfalar/bagis_kategoriler.php");
						break;
						
						case 'bagis-kategori-ekle';
						require_once("sayfalar/bagis_kategori_ekle.php");
						break;
						
						case 'bagis-turleri';
						require_once("sayfalar/bagis_turleri.php");
						break;
						
						case 'bagis-turu-ekle';
						require_once("sayfalar/bagis_turu_ekle.php");
						break;
						
						case 'bagis-turu-duzenle';
						require_once("sayfalar/bagis_turu_ekle.php");
						break;
						
						case 'bagis-listele';
						require_once("sayfalar/bagis_listele.php");
						break;
						
						case 'bagis-ekle';
						require_once("sayfalar/bagis_ekle.php");
						break;
						
						case 'gelen-bagislar';
							require_once("sayfalar/gelen_bagislar.php");
							break;
							
							case 'hamiler';
							require_once("sayfalar/hamiler.php");
							break;
							
							case 'gelen-bagis-detay';
							require_once("sayfalar/gelen_bagis_detay.php");
							break;
						
						case 'aidat-listele';
						require_once("sayfalar/aidat_listele.php");
						break;
						
						case 'aidat-ekle';
						require_once("sayfalar/aidat_ekle.php");
						break;
						
						case 'bagis-moduller';
						require_once("sayfalar/bagis_moduller.php");
						break;
						
						case 'bagis-modul-ekle';
						require_once("sayfalar/bagis_modul_ekle.php"); 
						break;
						
						case 'bagis-modul-duzenle';
						require_once("sayfalar/bagis_modul_ekle.php");
						break;

												
						case 'bilgilendirme-ekle';
						require_once("sayfalar/bilgilendirme_ekle.php");
						break;

						case 'bilgilendirme-listele';
						require_once("sayfalar/bilgilendirme_listele.php");
						break;

						case 'dosya-ekle';
						require_once("sayfalar/dosya_ekle.php");
						break;

						case 'dosya-listele';
						require_once("sayfalar/dosya_listele.php");
						break;
												
						case 'contact-section-settings';
						require_once("sayfalar/contact_section_settings.php");
						break;
						
						case 'randevu-hizmet-ekle';
						require_once("sayfalar/randevu_hizmet_ekle.php");
						break;
						
						case 'randevu-hizmet-listele';
						require_once("sayfalar/randevu_hizmet_listele.php");
						break;
						
						case 'randevu-listele';
						require_once("sayfalar/randevu_listele.php");
						break;
						
						case 'randevu-detay';
						require_once("sayfalar/randevu_detay.php");
						break;
						
						case 'yetim-ekle';
						require_once("sayfalar/yetim_ekle.php");
						break;
						
												case 'sertifikalar';
						require_once("sayfalar/sertifikalar.php");
						break;
						
						case 'yetim-duzenle';
						require_once("sayfalar/yetim_ekle.php");
						break;
						
						case 'yetim-listele';
						require_once("sayfalar/yetim_listele.php");
						break;
									
						case 'test-ayarlari';
						require_once("sayfalar/test_ayarlari.php");
						break;

						case 'test-sorulari';
						require_once("sayfalar/test_sorulari.php");
						break;

						case 'test-sonuclari';
						require_once("sayfalar/test_sonuclari.php");
						break;

						default:
						require_once("sayfalar/anasayfa.php");
					}
				}
				else
				{
					require_once("sayfalar/anasayfa.php");
				}
				?> 
				</div>
			<!-- content-wrapper ends -->
			<footer class="footer">
				<div class="d-sm-flex justify-content-center justify-content-sm-between">
					<span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © <?php echo date("Y");?>  Tüm hakları saklıdır.</span>
				</div>
			</footer>
			<!-- partial -->
			</div>
		</div>
		<!-- page-body-wrapper ends -->
	</div>
	
	<!-- Plugin js for this page-->
	<!-- End plugin js for this page-->
	<!-- inject:js -->
	<script src="js/off-canvas.js"></script>
	<script src="js/hoverable-collapse.js"></script>
	<script src="js/template.js"></script>
	<script src="js/settings.js"></script>
	<script src="js/todolist.js"></script>
	<!-- endinject -->
	<!-- Custom js for this page-->
	<script src="js/dashboard.js"></script>
	<!-- End custom js for this page-->
	<script src="js/file-upload.js"></script>
	<script src="js/typeahead.js"></script>
	<script src="js/select2.js"></script>
	
	<script src="js/formpickers.js"></script>
	<script src="js/form-addons.js"></script>
	<script src="js/x-editable.js"></script>
	<script src="js/dropify.js"></script>
	<script src="js/form-repeater.js"></script>
	<script src="js/bt-maxLength.js"></script>
	<script src="js/tooltips.js"></script>
	<script src="js/codeEditor_mirror.js"></script>
	<script src="js/editorDemo.js"></script>
	<script src="vendors/multiselect/jquery.multiselect.js"></script>
	<!--Custom-Scrollbar js-->
	<script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script type="text/javascript" src="js/jquery.popconfirm.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(".popconfirm").popConfirm();			
			$(".popconfirm1").popConfirm();
			$(".popconfirm2").popConfirm();			
		});
	</script>
	<script>
	tinymce.init({
    selector: 'textarea[id^="myTextarea"]',
		language: 'tr',
		theme: "silver",
		branding: false,
        height:400,
		fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
		plugins: [
			"image code",
			"advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
			"searchreplace wordcount visualblocks visualchars code filemanager responsivefilemanager fullscreen insertdatetime media nonbreaking",
			"save table contextmenu directionality emoticons template paste textcolor"
		],
		toolbar: "responsivefilemanager | undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image code | print preview media | forecolor backcolor fontsizeselect emoticons",
		image_advtab: true ,
		external_filemanager_path:"<?php echo $url;?>/vendors/filemanager/",
	    filemanager_title:"Dosya Yöneticisi" ,
		external_plugins: {
			"responsivefilemanager": "<?php echo $url;?>/vendors/tinymce/plugins/responsivefilemanager/plugin.min.js",
			"flickr": "<?php echo $url;?>/vendors/tinymce/plugins/flickr/plugin.min.js",
			"youtube": "<?php echo $url;?>/vendors/tinymce/plugins/youtube/plugin.min.js",
			"filemanager": "<?php echo $url;?>/vendors/filemanager/plugin.min.js"
		},
		filemanager_access_key:"demo"
	});
	// Form gönderilmeden önce TinyMCE içeriğini textarea'ya yaz (kalın/italik vb. temada görünsün)
	$(document).on('submit', 'form', function() {
		if (typeof tinymce !== 'undefined') {
			tinymce.triggerSave();
		}
	});
	
	</script>
	
	<script src="js/light-gallery.js"></script>
	
	<script>
	$(document).ready(function(e) {
		$('#il').bind('change', ilceleriGetir);
	});
	function ilceleriGetir(){
		var id=$(this).val();
		var ilceid=$("#ilceid").val();
		  $.ajax({
			  type:"post",
			  url:"data/dinamik.php",
			  data:{"id":id,"ilceid":ilceid},
			  dataType:"json",
			  success:function(fur){
				  $("#ilce").html(fur.basari);
			  }		  
		  });
	}
	$('#il').ready(function(){
		var id = $("#il").val();
		var ilceid=$("#ilceid").val();
		if(id != 0){
		$.ajax({
			type:"post",
			url:"data/dinamik.php",
			data:{"id":id,"ilceid":ilceid},
			dataType:"json",
			success:function(fur){
				$("#ilce").html(fur.basari);
			}		  
		  });
		}else{
			$("#ilce").html('<option value="0">İlçe Seçiniz</option>');
		}
	});	
	$(window).on("load",function(){
		$(".scroll").mCustomScrollbar({
			setWidth:false,
			setHeight:false,
			setTop:0,
			setLeft:0,
			axis:"y",
			scrollbarPosition:"inside",
			scrollInertia:950,
			autoDraggerLength:true,
			autoHideScrollbar:false,
			autoExpandScrollbar:false,
			alwaysShowScrollbar:0,
			snapAmount:null,
			snapOffset:0,
			mouseWheel:{
				enable:true,
				scrollAmount:"auto",
				axis:"y",
				preventDefault:false,
				deltaFactor:"auto",
				normalizeDelta:false,
				invert:false,
				disableOver:["select","option","keygen","datalist","textarea"]
			},
			scrollButtons:{
				enable:false,
				scrollType:"stepless",
				scrollAmount:"auto"
			},
			keyboard:{
				enable:true,
				scrollType:"stepless",
				scrollAmount:"auto"
			},
			contentTouchScroll:25,
			advanced:{
				autoExpandHorizontalScroll:false,
				autoScrollOnFocus:"input,textarea,select,button,datalist,keygen,a[tabindex],area,object,[contenteditable='true']",
				updateOnContentResize:true,
				updateOnImageLoad:true,
				updateOnSelectorChange:false,
				releaseDraggableSelectors:false
			},
			theme:"light",
			callbacks:{
				onInit:false,
				onScrollStart:false,
				onScroll:false,
				onTotalScroll:false,
				onTotalScrollBack:false,
				whileScrolling:false,
				onTotalScrollOffset:0,
				onTotalScrollBackOffset:0,
				alwaysTriggerOffsets:true,
				onOverflowY:false,
				onOverflowX:false,
				onOverflowYNone:false,
				onOverflowXNone:false
			},
			live:false,
			liveSelector:null
		});
		
	});
	</script>
	<script>
    $(function () {
		$('select#uyeler').multiselect({
			columns: 3,
			placeholder: '-Seçiniz-',
			search: true,
			searchOptions: {
				'default': 'Arama'
			},
			selectAll: true
		});
	});
	</script>
	<script>
		! function(o, e, p) {
			"use strict";
			p('[data-toggle="popover"]').popover(), p("#show-popover").popover({
				title: "Popover Show Event",
				content: "Bonbon chocolate cake. Pudding halvah pie apple pie topping marzipan pastry marzipan cupcake.",
				trigger: "click",
				placement: "right"
			}).on("show.bs.popover", function() {
				alert("Show event fired.")
			}), p("[data-popup=popover-color]").popover({
				template: '<div class="popover"><div class="bg-teal"><div class="popover-arrow"></div><div class="popover-inner"></div></div></div>'
			}), p("[data-popup=popover-border]").popover({
				template: '<div class="popover"><div class="border-orange"><div class="popover-arrow"></div><div class="popover-inner"></div></div></div>'
			})
		}(window, document, jQuery);
	</script>
	<script src="vendors/datetimepicker/jquery.datetimepicker.js"></script>
	<script>
	$('#datetimepicker_mask').datetimepicker({
		mask:'9999/19/39 29:59',
	});
	$('#datetimepicker').datetimepicker();
	$('#datetimepicker').datetimepicker({value:'2015/04/15 05:06'});
	$('#datetimepicker1').datetimepicker({
		datepicker:false,
		format:'H:i',
		step:5
	});
	$('.date-timepicker2').datetimepicker({
		timepicker:false,
		format:'d/m/Y',
		formatDate:'d/m/Y'
	});
	$('#datetimepicker3').datetimepicker({
		inline:true
	});
	$('.date-timepicker').datetimepicker();
	$('#open').click(function(){
		$('#datetimepicker4').datetimepicker('show');
	});
	$('#close').click(function(){
		$('#datetimepicker4').datetimepicker('hide');
	});
	</script>
	<style>
	/* Bildirim dropdown genişliği */
	.navbar-dropdown {
		min-width: 350px !important;
	}
	/* Bildirim count stil düzeltmesi */
	.count-indicator .count {
		position: absolute;
		top: -8px;
		right: -8px;
		background: linear-gradient(135deg, #ff6b6b, #ee5a52);
		color: white;
		border-radius: 50%;
		min-width: 20px;
		height: 20px;
		font-size: 10px;
		line-height: 20px;
		text-align: center;
		font-weight: bold;
		border: 2px solid #fff;
		box-shadow: 0 2px 4px rgba(0,0,0,0.2);
		animation: pulse 2s infinite;
	}
	@keyframes pulse {
		0% { transform: scale(1); }
		50% { transform: scale(1.1); }
		100% { transform: scale(1); }
	}
	/* Bağış bildirimleri için kalp ikonu */
	.dropdown-item.preview-item.donation-item .preview-item-content h6:before {
		content: '\f004'; /* FontAwesome heart icon */
		font-family: 'Font Awesome 5 Free';
		font-weight: 900;
		color: #e74c3c;
		margin-right: 5px;
		display: inline-block;
	}
	</style>
	<?php
	cVCLmHLxbS_mesaj("kullanici_giris",1,"yes","Sisteme başarıyla giriş yapılmıştır.");
	cVCLmHLxbS_mesaj("demohesap",3,"no","Demo hesapta işlem yapamassınız.!");
	?>	
</body>
</html>
<style>
.admin-search-wrapper {
    position: relative;
}

.admin-search-wrapper .input-group {
    transition: all 0.2s ease;
}

.admin-search-wrapper .input-group:focus-within {
    box-shadow: 0 0 0 3px rgba(3, 102, 214, 0.3);
    border-radius: 6px;
}

.admin-search-wrapper input:focus {
    outline: none;
    box-shadow: none !important;
}

.admin-search-results {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #d1d5da;
    border-radius: 6px;
    z-index: 9999;
    display: none;
    max-height: 400px;
    overflow-y: auto;
    box-shadow: 0 8px 24px rgba(149, 157, 165, 0.2);
    min-width: 300px;
}

.admin-search-results::-webkit-scrollbar {
    width: 8px;
}

.admin-search-results::-webkit-scrollbar-track {
    background: #f6f8fa;
}

.admin-search-results::-webkit-scrollbar-thumb {
    background: #d1d5da;
    border-radius: 4px;
}

.admin-search-results::-webkit-scrollbar-thumb:hover {
    background: #959da5;
}

.search-result-item {
    padding: 10px 16px;
    border-bottom: 1px solid #e1e4e8;
    cursor: pointer;
    transition: background-color 0.1s ease;
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #24292e;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-item:hover {
    background-color: #f6f8fa;
    text-decoration: none;
    color: #0366d6;
}

.search-result-item i {
    margin-right: 10px;
    color: #586069;
    font-size: 16px;
}

.search-result-item:hover i {
    color: #0366d6;
}

.search-result-title {
    font-size: 14px;
    font-weight: 500;
}

.search-result-path {
    font-size: 12px;
    color: #586069;
    margin-left: 8px;
}

.search-no-results {
    padding: 20px;
    text-align: center;
    color: #586069;
    font-size: 14px;
}

.search-kbd {
    display: inline-block;
    padding: 3px 5px;
    font-size: 11px;
    line-height: 10px;
    color: #444d56;
    vertical-align: middle;
    background-color: #fafbfc;
    border: solid 1px #d1d5da;
    border-bottom-color: #c6cbd1;
    border-radius: 3px;
    box-shadow: inset 0 -1px 0 #c6cbd1;
    margin-left: 4px;
}
</style>

<script>
    $(document).ready(function() {
        var menuItems = [];
        
        // Türkçe karakterleri normalize eden fonksiyon (büyük/küçük harf duyarsız arama için)
        function normalizeTurkish(text) {
            if (!text) return '';
            return text
                .toLowerCase()
                .replace(/ı/g, 'i')
                .replace(/İ/g, 'i')
                .replace(/ğ/g, 'g')
                .replace(/Ğ/g, 'g')
                .replace(/ü/g, 'u')
                .replace(/Ü/g, 'u')
                .replace(/ş/g, 's')
                .replace(/Ş/g, 's')
                .replace(/ö/g, 'o')
                .replace(/Ö/g, 'o')
                .replace(/ç/g, 'c')
                .replace(/Ç/g, 'c');
        }
        
        // Index all menu items
        $('#sidebar .nav-item a').each(function() {
            var $link = $(this);
            var url = $link.attr('href');
            var title = $link.find('.menu-title').text().trim();
            
            // If no menu-title class, try to get text directly
            if (!title) {
                title = $link.text().trim();
            }
            
            // Skip dropdown toggles (collapse açıcıları) - bunlar sadece dropdown açmak için, link değil
            var isDropdownToggle = $link.attr('data-toggle') === 'collapse' || 
                                   $link.attr('data-toggle') === 'dropdown' ||
                                   !url || 
                                   url === '#' || 
                                   url.startsWith('javascript');
            
            // Skip if no title or it's a dropdown toggle
            if (!title || isDropdownToggle) {
                return;
            }
            
            // Check if it's a sub-menu item (has parent with collapse)
            var parentTitle = '';
            var parent = $link.closest('.collapse').prev('.nav-link');
            if (parent.length) {
                parentTitle = parent.find('.menu-title').text().trim();
            }
            
            var searchText = (parentTitle + ' ' + title);
            
            menuItems.push({
                title: title,
                parent: parentTitle,
                url: url,
                search: normalizeTurkish(searchText),
                originalTitle: title,
                originalParent: parentTitle
            });
        });

        var searchInput = $('#navbar-search-input');
        var resultsContainer = $('#search-results-container');

        // Focus on search with Ctrl+K
        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
            }
        });

        searchInput.on('input', function() {
            var query = normalizeTurkish($(this).val().trim());
            
            if (query.length < 1) {
                resultsContainer.hide().empty();
                return;
            }

            var results = menuItems.filter(function(item) {
                return item.search.indexOf(query) > -1;
            });

            // Sort by relevance (exact match first, then starts with, then contains)
            results.sort(function(a, b) {
                var aSearch = a.search;
                var bSearch = b.search;
                
                if (aSearch === query) return -1;
                if (bSearch === query) return 1;
                if (aSearch.startsWith(query)) return -1;
                if (bSearch.startsWith(query)) return 1;
                return 0;
            });

            displayResults(results, query);
        });

        function displayResults(results, query) {
            resultsContainer.empty();
            
            if (results.length === 0) {
                resultsContainer.append('<div class="search-no-results">Sonuç bulunamadı</div>');
            } else {
                results.slice(0, 8).forEach(function(item) {
                    var resultHtml = '<a href="' + item.url + '" class="search-result-item">';
                    resultHtml += '<i class="icon-arrow-right"></i>';
                    resultHtml += '<div>';
                    resultHtml += '<span class="search-result-title">' + highlightMatch(item.originalTitle, query) + '</span>';
                    if (item.originalParent) {
                        resultHtml += '<span class="search-result-path">' + highlightMatch(item.originalParent, query) + '</span>';
                    }
                    resultHtml += '</div>';
                    resultHtml += '</a>';
                    
                    resultsContainer.append(resultHtml);
                });
            }
            
            resultsContainer.show();
        }

        function highlightMatch(text, normalizedQuery) {
            if (!normalizedQuery || !text) return text;
            
            // Orijinal metni normalize et
            var normalizedText = normalizeTurkish(text);
            
            // Normalize edilmiş metinde query'yi bul
            var index = normalizedText.indexOf(normalizedQuery);
            if (index === -1) return text;
            
            // NormalizeTurkish fonksiyonu karakter uzunluğunu değiştirmediği için
            // (ı->i, İ->i gibi 1 karakter -> 1 karakter)
            // pozisyonlar aynı kalır, direkt highlight edebiliriz
            var queryLength = normalizedQuery.length;
            var matchStart = index;
            var matchEnd = index + queryLength;
            
            // Highlight ekle
            return text.substring(0, matchStart) + 
                   '<strong>' + text.substring(matchStart, matchEnd) + '</strong>' + 
                   text.substring(matchEnd);
        }

        // Close results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.admin-search-wrapper').length) {
                resultsContainer.hide();
            }
        });

        // Clear on escape
        searchInput.on('keydown', function(e) {
            if (e.key === 'Escape') {
                $(this).val('');
                resultsContainer.hide();
            }
        });
    });
</script>
<?php ob_end_flush(); ?>
<?php } ?>