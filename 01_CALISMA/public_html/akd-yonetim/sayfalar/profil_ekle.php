<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM profiller WHERE id = ?");
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
		<h3><?=@$admindil['txt120'];?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt119'];?></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt120'];?></a></li>
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
						<label for="sira">Sıra</label>
						<input type="text" class="form-control form-control-sm" name="sira" id="sira" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['sira'] : '');?>" />
					</div>
					<div class="form-group">
						<label for="adi">Başlık</label>
						<input type="text" class="form-control form-control-sm" name="adi" id="adi" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['adi'] : '');?>" />
					</div>
					<div class="row">
						<div class="form-group col-md-6">
							<label for="kategori">Profil Kategorisi</label>
							<select class="js-example-basic-single form-control-sm" name="kategori" id="kategori" required style="width:100%">
							<option value="">-- Kategori Seçin --</option>
							<?php $KATEGORISorgu = $db->prepare("SELECT * FROM profil_kategori WHERE durum = ? AND dil = ? ORDER BY id ASC");
							$KATEGORISorgu->execute(array("1",$_SESSION['admin_dil']));
							$KATEGORIislem = $KATEGORISorgu->fetchALL(PDO::FETCH_ASSOC);?>
								<?php foreach ( $KATEGORIislem as $KATEGORISonuc ){?>
								<option value="<?php echo $KATEGORISonuc['id'];?>" <?php echo($Sonuc['kategori'] == $KATEGORISonuc['id'] ? 'selected' : '');?>><?php echo $KATEGORISonuc['adi'];?></option>
								<?php }?>
							</select>
						</div>
						
						<div class="form-group col-md-6">
							<label>Görevi <i class="icon-info text-info" data-toggle="popover" data-content="Tamamında büyük harf kullanmadan, profil sahibinin görevi veya unvanını kısaca belirtin. Ör: Yönetim Kurulu Başkanı, Üye, Genel Müdür" data-trigger="hover" data-original-title="Görevi"></i></label>
							<input type="text" class="form-control form-control-sm" name="gorevi" id="gorevi" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['gorevi'] : '');?>" />
						</div>
					</div>						
					<?php if(isset($_GET['islem'])=="duzenle"){?>
					<div class="row">
						<?php if($Sonuc['kapak'] == true){?>
						<div class="form-group col-md-2">
							<img src="../<?php echo tema;?>/uploads/profiller/<?php echo $Sonuc['kapak'];?>" class="img-responsive img-thumbnail" style="margin-bottom:2px;width:100%;min-height:150px;">
							<a style="width: 100%;" class="btn btn-danger btn-sm popconfirm" title="Kapak Sil" href="../_class/yonetim_islem.php?profilresimsil=ok&sid=<?php echo $Sonuc['id'];?>"><i class="fal fa-trash"></i> Kapak Sil</a>
						</div>
						<?php }?>
					</div>
					<?php }?>					
					<div class="form-group row col-md-6">
					<label>Profil Görseli</label>
						<input type="file" name="resim" class="file-upload-default">
						<div class="input-group col-xs-12">
							<input type="text" class="form-control file-upload-info form-control-sm" disabled="" placeholder="Resim dosyası seçiniz">
							<span class="input-group-append">
								<button class="file-upload-browse btn btn-primary btn-sm" type="button"><i class="icon-cloud-upload font-12"></i> Dosya Seç</button>
							</span>
						</div>
					</div>					
					<div class="form-group">
						<label for="aciklama">Kısa Özgeçmiş</label>
						<textarea name="aciklama" id="myTextarea"><?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['aciklama'] : '');?></textarea>
					</div>
					<div class="card mb-4">
						<div class="card-header">
							SOSYAL MEDYA AYARLARI
						</div>
						<div class="card-body">
						<div class="row">
							<div class="form-group col-md-6">
								<label for="facebook">Facebook Sayfa URL</label>
								<div class="input-group">
								<input type="text" class="form-control form-control-sm" name="facebook" id="facebook" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['facebook'] : '');?>" />
									<div class="input-group-append">
									<button class="btn btn-sm btn-facebook" type="button">
									  <i class="mdi mdi-facebook"></i>
									</button>
									</div>
								</div>
							</div>
							<div class="form-group col-md-6">
								<label for="twitter">Twitter Sayfa URL</label>
								<div class="input-group">
								<input type="text" class="form-control form-control-sm" name="twitter" id="twitter" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['twitter'] : '');?>" />
									<div class="input-group-append">
									<button class="btn btn-sm btn-twitter" type="button">
									  <i class="mdi mdi-twitter"></i>
									</button>
									</div>
								</div>
							</div>
							<div class="form-group col-md-6">
								<label for="instagram">Instagram Sayfa URL</label>
								<div class="input-group">
								<input type="text" class="form-control form-control-sm" name="instagram" id="instagram" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['instagram'] : '');?>" />
									<div class="input-group-append">
									<button class="btn btn-sm btn-instagram" type="button">
									  <i class="mdi mdi-instagram"></i>
									</button>
									</div>
								</div>
							</div>
							<div class="form-group col-md-6">
								<label for="linkedin">LinkedIn Sayfa URL</label>
								<div class="input-group">
								<input type="text" class="form-control form-control-sm" name="linkedin" id="linkedin" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['linkedin'] : '');?>" />
									<div class="input-group-append">
									<button class="btn btn-sm btn-linkedin" type="button">
									  <i class="mdi mdi-linkedin"></i>
									</button>
									</div>
								</div>
							</div>
							<div class="form-group col-md-6">
								<label for="youtube">Youtube Sayfa URL</label>
								<div class="input-group">
								<input type="text" class="form-control form-control-sm" name="youtube" id="youtube" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['youtube'] : '');?>" />
									<div class="input-group-append">
									<button class="btn btn-sm btn-youtube" type="button">
									  <i class="mdi mdi-youtube"></i>
									</button>
									</div>
								</div>
							</div>							
						</div>
						</div>
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
					<button type="submit" name="profil_guncelle" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-reload  btn-icon-prepend"></i>                                                    
						GÜNCELLE
					</button>
					<?php }else{?>
					<button type="submit" name="profil_ekle" class="btn btn-primary btn-icon-text btn-sm">
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
cVCLmHLxbS_mesaj("profil_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("profil_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("profil_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("profilresimsil",1,"yes","Başarı ile siinmiştir.");
cVCLmHLxbS_mesaj("profilresimsil",2,"no","Hata oluştu tekrar deneyiniz.!");
?>
