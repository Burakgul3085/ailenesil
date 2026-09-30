<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM bagislar WHERE id = ?");
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
		<h3><?=@$admindil['txt58'];?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt57'];?></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt58'];?></a></li>
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
					<div style="display:none;" class="form-group">
						<label for="modul_id">Bağış Modülü <span class="text-danger">*</span></label>
						<select class="js-example-basic-single form-control-sm" name="modul_id" id="modul_id" required style="width:100%" onchange="loadKategoriler(this.value)">
							<option value="">Modül Seçiniz</option>
						<?php $MODULSorgu = $db->prepare("SELECT * FROM bagis_moduller WHERE durum = ? AND dil = ? ORDER BY sira ASC");
						$MODULSorgu->execute(array("1",$_SESSION['admin_dil']));
						$MODULislem = $MODULSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $MODULislem as $MODULSonuc ){?>
							<option value="<?php echo $MODULSonuc['id'];?>" <?php echo($Sonuc['modul_id'] == $MODULSonuc['id'] ? 'selected' : '');?>><?php echo $MODULSonuc['adi'];?></option>
							<?php }?>
						</select>
						<small class="form-text text-muted">Önce hangi kampanyaya ait olduğunu seçin</small>
					</div>
					<div class="form-group">
						<label for="sira">Sıra</label>
						<input type="number" class="form-control form-control-sm" min="0" name="sira" id="sira" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['sira'] : '');?>" />
					</div>
					<div class="form-group">
						<label for="adi">Başlık <i class="icon-info text-info" data-toggle="popover" data-content="Proje adında tamamen BÜYÜK harf kullanmayın. 70 karakterden uzun başlıkları Google indexlemede göstermez ve değerlendirmez. Bu nedenle uzun başlıklar kullanmaktan kaçının. Başlıklarda çift tırnak kesinlikle kullanmayın." data-trigger="hover" data-original-title="Başlık"></i></label>
						<input type="text" class="form-control form-control-sm" name="adi" id="adi" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['adi'] : '');?>" />
					</div>
					<div class="form-group">
						<label for="aciklama">Kısa Açıklama</label>
						<textarea class="form-control form-control-sm" name="aciklama" id="aciklama" rows="3"><?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['aciklama'] : '');?></textarea>
						<small class="form-text text-muted">Bağış listeleme sayfasında görünecek kısa açıklama</small>
					</div>
					<div class="form-group">
						<label for="miktar">Başlangıç Bağış Miktarı <i class="icon-info text-info" data-toggle="popover" data-content="Bağış sayfasında görünecek varsayılan bağış miktarıdır." data-trigger="hover" data-original-title="Başlangıç Bağış Miktarı"></i></label>
						<input type="text" class="form-control form-control-sm" name="miktar" id="miktar" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['miktar'] : '');?>" />
					</div>
					<div class="form-group">
						<label for="kategori">Kategori <span class="text-danger">*</span></label>
						<select class="js-example-basic-single form-control-sm" name="kategori" id="kategori" required style="width:100%">
						<?php $KATEGORISorgu = $db->prepare("SELECT * FROM bagis_kategori WHERE durum = ? AND dil = ? ORDER BY id ASC");
						$KATEGORISorgu->execute(array("1",$_SESSION['admin_dil']));
						$KATEGORIislem = $KATEGORISorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $KATEGORIislem as $KATEGORISonuc ){?>
							<option value="<?php echo $KATEGORISonuc['id'];?>" <?php echo($Sonuc['kategori'] == $KATEGORISonuc['id'] ? 'selected' : '');?>><?php echo $KATEGORISonuc['adi'];?></option>
							<?php }?>
						</select>
					</div>
					<div class="form-group">
						<label for="hediye_turu">Hediye Türü (Opsiyonel)</label>
						<select class="js-example-basic-single form-control-sm" name="hediye_turu" id="hediye_turu" style="width:100%">
							<option value="0">Hediye Yok</option>
							<?php 
							try {
								$HediyeSorgu = $db->query("SELECT * FROM bagis_hediye_turu WHERE durum = 1 ORDER BY sira ASC");
								if($HediyeSorgu){
									$HediyeIslem = $HediyeSorgu->fetchALL(PDO::FETCH_ASSOC);
									foreach ( $HediyeIslem as $HediyeSonuc ){
										$selected = (isset($Sonuc['hediye_turu_id']) && $Sonuc['hediye_turu_id'] == $HediyeSonuc['id']) ? 'selected' : '';
										echo '<option value="'.$HediyeSonuc['id'].'" '.$selected.'>'.$HediyeSonuc['adi'].'</option>';
									}
								}
							} catch(Exception $e) {
								// Tablo yoksa hata verme
							}
							?>
						</select>
						<small class="form-text text-muted">Bu bağış için geçerli hediye türünü seçin.</small>
					</div>
					<?php if(isset($_GET['islem'])=="duzenle"){?>
					<div class="row">
						<?php if($Sonuc['kapak'] == true){?>
						<div class="form-group col-md-2">
							<img src="../<?php echo tema;?>/uploads/bagislar/<?php echo $Sonuc['kapak'];?>" class="img-responsive img-thumbnail" style="margin-bottom:2px;width:100%;min-height:150px;">
							<a style="width: 100%;" class="btn btn-danger btn-sm popconfirm" title="Kapak Sil" href="../_class/yonetim_islem.php?bagisresimsil=ok&sid=<?php echo $Sonuc['id'];?>"><i class="fal fa-trash"></i> Kapak Sil</a>
						</div>
						<?php }?>
					</div>
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
						<label class="switch">
							<input type="checkbox" name="yetim_bagisi" id="yetim_bagisi" value="1" <?php if(isset($Sonuc['yetim_bagisi']) && $Sonuc['yetim_bagisi'] == '1') {?> checked <?php } ?>>
							<span class="slider"></span>
						</label>
						<label class="d-inline-block" style="line-height: 34px;" for="yetim_bagisi">Bu Bağış Yetim/Hamilik Sistemine Dahildir</label>
					</div>
					<div class="form-group mb-2">						
						<?php if(isset($_GET['islem'])=="duzenle"){?>
						<label class="switch">
							<input type="checkbox" name="detay_linki" id="detay_linki" value="1" <?php if(isset($Sonuc['detay_linki']) && $Sonuc['detay_linki'] == '1') {?> checked <?php } ?>>
							<span class="slider"></span>
						</label>
						<?php }else{?>
						<label class="switch">
							<input type="checkbox" name="detay_linki" id="detay_linki" value="1">
							<span class="slider"></span>
						</label>
						<?php } ?>
						<label class="d-inline-block" style="line-height: 34px;" for="detay_linki">Detay Linki Olsun (Aktif/Pasif)</label>						
					</div>
										<div class="form-group mb-2">						
						<?php if(isset($_GET['islem'])=="duzenle"){?>
						<label class="switch">
							<input type="checkbox" name="degismeyen_fiyat" id="degismeyen_fiyat" value="1" <?php if($Sonuc['degismeyen_fiyat'] == '1') {?> checked <?php } ?>>
							<span class="slider"></span>
						</label>
						<?php }else{?>
						<label class="switch">
							<input type="checkbox" name="degismeyen_fiyat" id="degismeyen_fiyat" value="1" checked>
							<span class="slider"></span>
						</label>
						<?php } ?>
						<label class="d-inline-block" style="line-height: 34px;" for="degismeyen_fiyat">Değişmeyen Fiyat</label>						
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
					<?php if(isset($_GET['islem'])=="duzenle"){?>
					<button type="submit" name="bagis_guncelle" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-reload  btn-icon-prepend"></i>                                                    
						GÜNCELLE
					</button>
					<?php }else{?>
					<button type="submit" name="bagis_ekle" class="btn btn-primary btn-icon-text btn-sm">
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
cVCLmHLxbS_mesaj("bagis_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("bagis_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("bagis_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("bagisresimsil",1,"yes","Başarı ile siinmiştir.");
cVCLmHLxbS_mesaj("bagisresimsil",2,"no","Hata oluştu tekrar deneyiniz.!");
?>
