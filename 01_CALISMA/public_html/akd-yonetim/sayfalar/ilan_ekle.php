<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM ilanlar WHERE id = ?");
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
		<h3><?=@$admindil['txt70'];?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt69'];?></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt70'];?></a></li>
			</ul>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12 grid-margin">
		<div class="card">
			<div class="card-body">
				<form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
				<input id="id" name="id" type="hidden" value="<?php echo $Sonuc['id']; ?>">
					<div class="form-group">
						<label for="sira">Sıra</label>
						<input type="number" class="form-control form-control-sm" min="0" name="sira" id="sira" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['sira'] : '');?>" />
					</div>
					<div class="form-group">
						<label for="adi">Başlık</label>
						<input type="text" class="form-control form-control-sm" name="adi" id="adi" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['adi'] : '');?>" />
					</div>					
					<?php if(isset($_GET['islem'])=="duzenle"){?>							
						<?php if($Sonuc['resim'] == true){?>
						<div class="form-group row col-md-2">
							<div id="lightgallery" class="row lightGallery">
								<a href="../<?php echo tema;?>/uploads/ilanlar/<?php echo $Sonuc['resim'];?>"><img src="../<?php echo tema;?>/uploads/ilanlar/<?php echo $Sonuc['resim'];?>" class="img-responsive img-thumbnail" style="margin-bottom:2px;width:100%;"></a>
							</div>
							<a style="width: 100%;" class="btn btn-danger btn-sm popconfirm" title="Resim Sil" href="../_class/yonetim_islem.php?ilanresimsil=ok&sid=<?php echo $Sonuc['id'];?>"><i class="fal fa-trash"></i> Resim Sil</a>
						</div>
						<?php }?>							
					<?php }?>
					<div class="form-group row col-md-6">
					<label>Listeleme Görseli</label>
						<input type="file" name="resim" class="file-upload-default">
						<div class="input-group col-xs-12">
							<input type="text" class="form-control file-upload-info form-control-sm" disabled="" placeholder="Resim dosyası seçiniz">
							<span class="input-group-append">
								<button class="file-upload-browse btn btn-primary btn-sm" type="button"><i class="icon-cloud-upload font-12"></i> Dosya Seç</button>
							</span>
						</div>
					</div>
					<div class="form-group mb-2">						
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
						<label class="d-inline-block" style="line-height: 34px;" for="durum">Durum</label>						
					</div>
					<div class="form-group mb-2">						
						<?php if(isset($_GET['islem'])=="duzenle"){?>
						<label class="switch">
							<input type="checkbox" name="anasayfa" id="anasayfa" value="1" <?php if($Sonuc['anasayfa'] == '1') {?> checked <?php } ?>>
							<span class="slider"></span>
						</label>
						<?php }else{?>
						<label class="switch">
							<input type="checkbox" name="anasayfa" id="anasayfa" value="1" checked>
							<span class="slider"></span>
						</label>
						<?php } ?>	
						<label class="d-inline-block" style="line-height: 34px;" for="anasayfa">Anasayfada Göster</label>
					</div>
					
					<div class="form-group">
						<label for="myTextarea">İçerik</label>
						<textarea name="aciklama" id="myTextarea"><?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['aciklama'] : '');?></textarea>
					</div>
					<div class="card mb-4">
						<div class="card-header">
							SEO AYARLARI
						</div>
						<div class="card-body">
							<div class="form-group">
								<label for="maxlength-textarea">SEO Açıklama (Description)</label>
								<textarea id="maxlength-textarea" name="description"  class="form-control" maxlength="260" rows="2"><?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['description'] : '');?></textarea>
							</div>
							<div class="form-group mb-0">
								<label for="tags">SEO Kelimeler (Keywords) <small>(Kelimenin sonuna virgül koyunuz)</small></label>
								<input name="keywords" id="tags" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['keywords'] : '');?>" />
							</div>							
						</div>
					</div>
					<?php if(isset($_GET['islem'])=="duzenle"){?>
					<button type="submit" name="ilan_guncelle" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-reload  btn-icon-prepend"></i>                                                    
						GÜNCELLE
					</button>
					<?php }else{?>
					<button type="submit" name="ilan_ekle" class="btn btn-primary btn-icon-text btn-sm">
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
cVCLmHLxbS_mesaj("ilan_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("ilan_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("ilan_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("ilanresimsil",1,"yes","Başarı ile siinmiştir.");
cVCLmHLxbS_mesaj("ilanresimsil",2,"no","Hata oluştu tekrar deneyiniz.!");
?>		
