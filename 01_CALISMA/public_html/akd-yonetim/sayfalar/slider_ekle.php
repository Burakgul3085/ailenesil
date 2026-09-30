<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM slider WHERE id = ?");
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
		<h3><?=@$admindil['txt82'];?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt81'];?></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt82'];?></a></li>
			</ul>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
				<input id="id" name="id" type="hidden" value="<?php echo $Sonuc['id']; ?>">
					<div class="form-group">
						<label for="sira">Slayt Sıra Nosu</label>
						<input type="number" class="form-control form-control-sm" name="sira" id="sira" min="0" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['sira'] : '');?>" />
					</div>
					<div class="form-group">
						<label for="adi">Slayt Başlığı</label>
						<input type="text" class="form-control form-control-sm" name="adi" id="adi" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['adi'] : '');?>" />
					</div>
					<div class="form-group">
						<label for="url">Slayt URL</label>
						<input type="text" class="form-control form-control-sm" name="url" id="url" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['url'] : '');?>" />
					</div>
					<div class="form-group">
						<div class="form-check">
							<label class="form-check-label">
							<input type="checkbox" name="sekme" class="form-check-input" <?php if($Sonuc['sekme'] == '1') {?> checked <?php } ?>>Tıklandığında yeni sekmeye gitsin mi ?<i class="input-helper"></i></label>
						</div>
					</div>
					<?php if(isset($_GET['islem'])=="duzenle"){?>							
						<?php if($Sonuc['resim'] == true){?>
						<div class="form-group row col-md-2">
							<div id="lightgallery" class="row lightGallery">
								<a href="../<?php echo tema;?>/uploads/slider/<?php echo $Sonuc['resim'];?>"><img src="../<?php echo tema;?>/uploads/slider/<?php echo $Sonuc['resim'];?>" class="img-responsive img-thumbnail" style="margin-bottom:2px;width:100%;"></a>
							</div>
							<a style="width: 100%;" class="btn btn-danger btn-sm popconfirm" title="Resim Sil" href="../_class/yonetim_islem.php?sliderresimsil=ok&sid=<?php echo $Sonuc['id'];?>"><i class="fal fa-trash"></i> Resim Sil</a>
						</div>
						<?php }?>							
					<?php }?>
					<div class="form-group row col-md-6">
					<label>Desktop Slayt Görseli Seçiniz Varsayılan boyut <strong>1920 x 1080</strong></label>
						<input type="file" name="resim" class="file-upload-default">
						<div class="input-group col-xs-12">
							<input type="text" class="form-control file-upload-info form-control-sm" disabled="" placeholder="Resim dosyası seçiniz">
							<span class="input-group-append">
								<button class="file-upload-browse btn btn-primary btn-sm" type="button"><i class="icon-cloud-upload font-12"></i> Dosya Seç</button>
							</span>
						</div>
					</div>
					
					<?php if(isset($_GET['islem'])=="duzenle"){?>							
						<?php if($Sonuc['mobil_resim'] == true){?>
						<div class="form-group row col-md-2">
							<div id="lightgallery" class="row lightGallery">
								<a href="../<?php echo tema;?>/uploads/slider/<?php echo $Sonuc['mobil_resim'];?>"><img src="../<?php echo tema;?>/uploads/slider/<?php echo $Sonuc['mobil_resim'];?>" class="img-responsive img-thumbnail" style="margin-bottom:2px;width:100%;"></a>
							</div>
							<a style="width: 100%;" class="btn btn-danger btn-sm popconfirm" title="Resim Sil" href="../_class/yonetim_islem.php?sliderresimsil_mobil=ok&sid=<?php echo $Sonuc['id'];?>"><i class="fal fa-trash"></i> Mobil Resim Sil</a>
						</div>
						<?php }?>							
					<?php }?>
					<div class="form-group row col-md-6">
					<label>Mobil Slayt Görseli Seçiniz (Opsiyonel)</label>
						<input type="file" name="mobil_resim" class="file-upload-default">
						<div class="input-group col-xs-12">
							<input type="text" class="form-control file-upload-info form-control-sm" disabled="" placeholder="Mobil resim dosyası seçiniz">
							<span class="input-group-append">
								<button class="file-upload-browse btn btn-primary btn-sm" type="button"><i class="icon-cloud-upload font-12"></i> Dosya Seç</button>
							</span>
						</div>
						<small class="text-muted">Mobil cihazlarda farklı bir görsel göstermek istiyorsanız buradan yükleyiniz.</small>
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
					<div class="form-group">
						<label for="adi">Açıklama</label>
						<textarea class="form-control" name="aciklama"><?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['aciklama'] : '');?></textarea>
					</div>
					<?php if(isset($_GET['islem'])=="duzenle"){?>
					<button type="submit" name="slider_guncelle" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-reload  btn-icon-prepend"></i>                                                    
						GÜNCELLE
					</button>
					<?php }else{?>
					<button type="submit" name="slider_ekle" class="btn btn-primary btn-icon-text btn-sm">
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
cVCLmHLxbS_mesaj("slider_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("slider_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("slider_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("sliderresimsil",1,"yes","Başarı ile siinmiştir.");
cVCLmHLxbS_mesaj("sliderresimsil",2,"no","Hata oluştu tekrar deneyiniz.!");
?>
