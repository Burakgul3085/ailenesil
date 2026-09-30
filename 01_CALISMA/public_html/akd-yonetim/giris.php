<?php
ob_start();
session_start();

require_once('../_class/baglan.php');
require_once('../_class/fonksiyon.php');
require_once('../_class/class.upload.php');
$_SESSION["guvenlik"] = array("kullanici_giris" => cVCLmHLxbS_kod());
?>
<?php if(isset($_SESSION["Yonetim_Id"]) || isset($_SESSION["Yonetim_Kadi"]) || isset($_SESSION["Yonetim_Sifre"]) || isset($_SESSION["rutbe"]))
{
	header("Location:index.html");
	exit();
}
?>
<?php
$ua=cVCLmHLxbS_getBrowser();
$tarayici= "Web tarayucınız: " . $ua['name'] . " " . $ua['version'] . " " .$ua['platform'];
//Örneğin mozilla Firefox kullananların girmesini istemiyorsak
if ($ua['name']=='Mozilla Firefox')
{
	print_r($tarayici);	
	echo "<center>";
	echo "<h2>Mozilla Firefox tarayıcısı desteklenmiyor.</h2><br>" ;
	echo "<h4>Lütfen Internet Explorer, Opera, Safari, Chrome tarayıcılarından birini kullanınız.</h4>";
	echo "</center>";
} else  {?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Yönetim Paneli</title>
	<link rel="stylesheet" href="vendors/iconfonts/mdi/font/css/materialdesignicons.min.css">
	<link rel="stylesheet" href="vendors/iconfonts/simple-line-icon/css/simple-line-icons.css">
	<link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
	<link rel="stylesheet" href="vendors/css/vendor.bundle.addons.css">
	<link rel="stylesheet" href="css/vertical-layout-light/style.css">
	<link rel="shortcut icon" href="images/favicon.png" />
</head>
<body>
	<div class="container-scroller">
		<div class="container-fluid page-body-wrapper full-page-wrapper">
			<div class="content-wrapper d-flex align-items-stretch auth auth-img-bg">
				<div class="row flex-grow">
					<div class="col-lg-6 d-flex align-items-center justify-content-center">
						<div class="auth-form-transparent text-left p-3">
							<h3>YÖNETİM PANELİ</h3>
							<form class="pt-3" method="post" action="../_class/yonetim_islem.php" autocomplete="off">
								<div class="form-group">
									<label for="kadi">Kullanıcı Adı</label>
									<div class="input-group">
										<div class="input-group-prepend bg-transparent">
											<span class="input-group-text bg-transparent border-right-0">
											<i class="mdi mdi-account-outline text-primary"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-lg border-left-0" <?php if(isset($_COOKIE['Yonetim_Kadi'])){?> value="<?php echo $_COOKIE['Yonetim_Kadi'];?>" <?php } ?> name="kadi" id="kadi">
									</div>
								</div>
								<div class="form-group">
									<label for="sifre">Şifre</label>
									<div class="input-group">
										<div class="input-group-prepend bg-transparent">
											<span class="input-group-text bg-transparent border-right-0">
											<i class="mdi mdi-lock-outline text-primary"></i>
											</span>
										</div>
										<input type="password" class="form-control form-control-lg border-left-0" <?php if(isset($_COOKIE['Yonetim_Sifre'])){?> value="<?php echo $_COOKIE['Yonetim_Sifre'];?>" <?php } ?> name="sifre" id="sifre">                        
									</div>
								</div>
								<div class="my-2 d-flex justify-content-between align-items-center">
									<div class="form-check">
										<label class="form-check-label text-muted">
										<input type="checkbox" <?php echo(isset($_COOKIE['Yonetim_Kadi']) ? "checked" : "");?> name="beni_hatirla" class="form-check-input">
										Beni Hatırla
										</label>
									</div>
									<a href="#" data-toggle="modal" data-target="#sifremi-unuttum" data-backdrop="static" data-keyboard="false" data-whatever="@mdo" class="auth-link text-black">Parolamı Unuttum</a>
								</div>
								<div class="my-3">
									<button type="submit" name="kullanici_giris" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"><i class="icon-lock"></i> GİRİŞ YAP</button>
								</div>
							</form>
						</div>
					</div>
					<div class="col-lg-6 login-half-bg d-flex flex-row" style="background: linear-gradient(135deg, #ff059f 0%, #ff9a00cf 100%);display: flex;align-items: center;justify-content: center;">
						<div class="text-center">
							<?php 
							require_once('../_class/baglan.php');
							require_once('../_class/fonksiyon.php');
							$ayar = $db->query("SELECT * FROM ayarlar WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
							$logo_path = '../../'.tema.'/uploads/logo/'.$ayar['firma_logo'];
							?>
							<img src="<?php echo $logo_path; ?>" alt="Logo" style="max-width: 376px;max-height: 345px;object-fit: contain;">
			
						</div>
					</div>
				</div>
			</div>
			<!-- content-wrapper ends -->
		</div>
		<!-- page-body-wrapper ends -->
	</div>
	<!-- container-scroller -->
	<!-- plugins:js -->
	<script src="vendors/js/vendor.bundle.base.js"></script>
	<script src="vendors/js/vendor.bundle.addons.js"></script>
	<!-- endinject -->
	<!-- inject:js -->
	<script src="js/off-canvas.js"></script>
	<script src="js/hoverable-collapse.js"></script>
	<script src="js/template.js"></script>
	<script src="js/settings.js"></script>
	<script src="js/todolist.js"></script>
	<div class="modal fade" id="sifremi-unuttum" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalLabel">Parolamı Unutum?</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form method="post" action="../_class/yonetim_islem.php" autocomplete="off">
					<div class="modal-body">					
						<div class="form-group mb-0">
							<label for="email" class="col-form-label mb-0">E-Posta Adresiniz</label>
							<input type="text" class="form-control" name="email" id="email" />
						</div>					
					</div>
					<div class="modal-footer">
						<button type="submit" name="sifirla" class="btn btn-success"><i class="icon-refresh"></i> Sıfırla</button>
						<button type="button" class="btn btn-light" data-dismiss="modal"><i class="icon-close"></i> İptal</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php 
	cVCLmHLxbS_mesaj("kullanici_giris",3,"bos","Boş alan bıraktınız.");
	cVCLmHLxbS_mesaj("kullanici_giris",2,"no","Kullanıcı Adı veya Şifreniz Yanlış.");
	cVCLmHLxbS_mesaj("sifirla",1,"yes","Kullanıcı adınız ve şifreniz sistemde kayıtlı mail adresinize gönderilmiştir.");
	cVCLmHLxbS_mesaj("sifirla",2,"no","E-Mail adresiniz sistemde kayıtlı değildir.");
	cVCLmHLxbS_mesaj("demohesap",3,"no","Demo hesapta işlem yapamassınız.!");
	?>
	<!-- endinject -->
</body>
</html>
<?php ob_end_flush(); ?>
<?php } ?>