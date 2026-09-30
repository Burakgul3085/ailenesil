<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM randevu_hizmetler WHERE id = ?");
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
<?php // @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
 cemetery_f(); ?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3><?php echo isset($_GET['islem'])=="duzenle" ? 'Randevu Hizmeti Düzenle' : 'Randevu Hizmeti Ekle';?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="randevu-hizmet-listele.html">Randevu Hizmetleri</a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?php echo isset($_GET['islem'])=="duzenle" ? 'Düzenle' : 'Ekle';?></a></li>
			</ul>
		</div>
	</div>
</div> 
<div class="row">
	<div class="col-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
				<input id="id" name="id" type="hidden" value="<?php echo isset($Sonuc['id']) ? $Sonuc['id'] : ''; ?>">
					<div class="form-group">
						<label for="sira">Sıra</label>
						<input type="number" class="form-control form-control-sm" min="0" name="sira" id="sira" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['sira'] : '');?>" />
					</div>
					<div class="form-group">
						<label for="baslik">Başlık</label>
						<input type="text" class="form-control form-control-sm" name="baslik" id="baslik" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['baslik'] : '');?>" required />
					</div>
					<div class="form-group">
						<label for="fiyat">Fiyat (TL)</label>
						<input type="number" step="0.01" class="form-control form-control-sm" name="fiyat" id="fiyat" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['fiyat'] : '0.00');?>" />
					</div>
					<div class="form-group">
						<label for="aciklama">Açıklama</label>
						<textarea name="aciklama" id="myTextarea" class="form-control"><?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['aciklama'] : '');?></textarea>
					</div>
					
					<?php if(isset($_GET['islem'])=="duzenle"){?>							
						<?php if($Sonuc['resim'] == true){?>
						<div class="form-group row col-md-2">
							<div id="lightgallery" class="row lightGallery">
								<a href="../<?php echo tema;?>/uploads/randevu_hizmetler/<?php echo $Sonuc['resim'];?>"><img src="../<?php echo tema;?>/uploads/randevu_hizmetler/<?php echo $Sonuc['resim'];?>" class="img-responsive img-thumbnail" style="margin-bottom:2px;width:100%;"></a>
							</div>
							<a style="width: 100%;" class="btn btn-danger btn-sm popconfirm" title="Resim Sil" href="../_class/yonetim_islem.php?randevuhizmetresimsil=ok&sid=<?php echo $Sonuc['id'];?>"><i class="fal fa-trash"></i> Resim Sil</a>
						</div>
						<?php }?>							
					<?php }?>
					
					<div class="form-group row col-md-6">
						<label>Hizmet Resmi</label>
						<input type="file" name="resim" class="file-upload-default">
						<div class="input-group col-xs-12">
							<input type="text" class="form-control file-upload-info form-control-sm" disabled="" placeholder="Resim dosyası seçiniz">
							<span class="input-group-append">
								<button class="file-upload-browse btn btn-primary btn-sm" type="button"><i class="icon-cloud-upload font-12"></i> Dosya Seç</button>
							</span>
						</div>
						<small class="form-text text-muted">Resim varsa ikon kullanılmayacak</small>
					</div>
					
					<div class="form-group">
						<label for="ikon">İkon (Font Awesome veya Material Design Icons)</label>
						<input type="text" class="form-control form-control-sm" name="ikon" id="ikon" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['ikon'] : '');?>" placeholder="Örn: mdi-calendar veya fa-calendar" />
						<small class="form-text text-muted">İkon varsa resim kullanılmayacak. Örnek: mdi-calendar-check, fa-calendar-alt</small>
					</div>
					
					<div class="form-group">
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
					
					<?php if(isset($_GET['islem'])=="duzenle"){?>
					<button type="submit" name="randevu_hizmet_guncelle" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-reload  btn-icon-prepend"></i>                                                    
						GÜNCELLE
					</button>
					<?php }else{?>
					<button type="submit" name="randevu_hizmet_ekle" class="btn btn-primary btn-icon-text btn-sm">
						<i class="mdi mdi-file-check btn-icon-prepend"></i>
						KAYDET
					</button>
					<?php } ?>
				</form>
			</div>
		</div>
	</div>
</div>
<?php 
cVCLmHLxbS_mesaj("randevu_hizmet_ekle",1,"yes","Başarı ile eklenmiştir.");
cVCLmHLxbS_mesaj("randevu_hizmet_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("randevu_hizmet_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("randevu_hizmet_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("randevuhizmetresimsil",1,"yes","Başarı ile silinmiştir.");
cVCLmHLxbS_mesaj("randevuhizmetresimsil",2,"no","Hata oluştu tekrar deneyiniz.!");
?>

