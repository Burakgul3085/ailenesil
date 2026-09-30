<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
$ayar_dizi = $db->prepare("SELECT * FROM sms WHERE id = ?");
$ayar_dizi->execute(array(1));
if($ayar_dizi->rowCount()){
	$Sonuc = $ayar_dizi->fetch(PDO::FETCH_ASSOC);
}else{
	header("Location:".$url."/404.html");
	exit;
}
?>

<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3><?=@$admindil['txt11'];?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt2'];?></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt11'];?></a></li>
			</ul>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">								
				<form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
					<div class="form-group">
						<label for="postUrl">Post URL</label>
						<input type="text" class="form-control form-control-sm" name="postUrl" id="postUrl" value="<?php echo $Sonuc['postUrl'];?>" />
					</div>			
					<div class="form-group">
						<label for="KULLANICIADI">Kullanıcı Adı</label>
						<input type="text" class="form-control form-control-sm" name="KULLANICIADI" id="KULLANICIADI" value="<?php echo $Sonuc['KULLANICIADI'];?>" />
					</div>
					<div class="form-group">
						<label for="SIFRE">Api Secret</label>
						<input type="text" class="form-control form-control-sm" name="SIFRE" id="SIFRE" value="<?php echo $Sonuc['SIFRE'];?>" />
					</div>
					<div class="form-group">
						<label for="ORGINATOR">Başlık</label>
						<input type="text" class="form-control form-control-sm" name="ORGINATOR" id="ORGINATOR" value="<?php echo $Sonuc['ORGINATOR'];?>" />
					</div>
					<div class="form-group">
						<label for="m_kime">Mesajın Geleceği Telefon Numarası</label>
						<input type="text" class="form-control form-control-sm" name="m_kime" id="m_kime" value="<?php echo $Sonuc['m_kime'];?>" />
					</div>	
					<p class="mb-2"><strong>Not: Sistemde <a target="_blank" href="https://www.netgsm.com.tr/">netgsm</a> apileri mevcuttur. Farklı bir api entegrasyonu için bizimle iletişime geçiniz.</strong></p>					
					<button type="submit" name="sms_ayarlar" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-spin mdi-loading"></i>                                                   
						GÜNCELLE
					</button>
				</form>
			</div>												
		</div>
	</div>

</div>
<?php
cVCLmHLxbS_mesaj("sms_ayarlar",1,"yes","Başarı ile guncellenmiştir.");		
cVCLmHLxbS_mesaj("sms_ayarlar",2,"no","Hata oluştu tekrar deneyiniz.!");	
?>	
