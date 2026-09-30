<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM bagis_turleri WHERE id = ?");
	$Sorgu->execute(array($_GET['id']));
	if($Sorgu->rowCount())
	{
		$Sonuc = $Sorgu->fetch(PDO::FETCH_ASSOC);
	}
	else
	{
		header("Location:".$url."/404.html");
		exit;
	}
}
?>

<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3><?php echo (isset($_GET['islem'])=="duzenle" ? "Bağış Türü Düzenle" : "Yeni Bağış Türü Ekle");?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>">Site Yönetimi</a></li>
				<li><a href="bagis-turleri.html">Bağış Türleri</a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?php echo (isset($_GET['islem'])=="duzenle" ? "Düzenle" : "Ekle");?></a></li>
			</ul>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<form class="forms-sample" method="post" action="../_class/yonetim_islem.php">
				<input id="id" name="id" type="hidden" value="<?php echo isset($Sonuc['id']) ? $Sonuc['id'] : ''; ?>">
					<div class="form-group">
						<label for="adi">Bağış Türü Adı <span class="text-danger">*</span></label>
						<input type="text" class="form-control form-control-sm" name="adi" id="adi" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['adi']) : '');?>" required />
						<small class="form-text text-muted">Örn: Tek Seferlik Bağış, Aylık Bağış</small>
					</div>
					<div class="form-group d-inline-block mr-2">
						<label class="d-block" for="durum">Durum</label>
						<?php if(isset($_GET['islem'])=="duzenle"){?>
						<label class="switch">
							<input type="checkbox" name="durum" id="durum" value="1" <?php if($Sonuc['durum'] == '1') {?> checked <?php } ?>>
							<span class="slider"></span>
						</label>
						<?php }else{?>
						<label class="switch">
							<input type="checkbox" name="durum" id="durum" value="1" checked>
							<span class="slider"></span>
						</label>
						<?php } ?>								
					</div>
					<div class="form-group">
					<?php if(isset($_GET['islem'])=="duzenle"){?>
					<button type="submit" name="bagis_turu_guncelle" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-reload  btn-icon-prepend"></i>                                                    
						GÜNCELLE
					</button>
					<?php }else{?>
					<button type="submit" name="bagis_turu_ekle" class="btn btn-primary btn-icon-text btn-sm">
						<i class="mdi mdi-file-check btn-icon-prepend"></i>
						KAYDET
					</button>
					<?php } ?>
					<a href="bagis-turleri.html" class="btn btn-light btn-sm">
						<i class="mdi mdi-close"></i> İptal
					</a>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>
<?php 
cVCLmHLxbS_mesaj("bagis_turu_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("bagis_turu_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("bagis_turu_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
?>

