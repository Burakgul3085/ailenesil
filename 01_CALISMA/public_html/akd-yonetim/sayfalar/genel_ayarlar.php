<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
$ayar_dizi = $db->prepare("SELECT * FROM ayarlar WHERE id = ?");
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
		<h3><?=@$admindil['txt3'];?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt2'];?></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt3'];?></a></li>
			</ul>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">								
				<form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-4">
						<div class="card mb-4">
							<div class="card-header">
								Logo Yükle
							</div>
							<div class="card-body">
								<div class="form-group">
								<label>Logo</label>
									<input type="file" name="resim" class="file-upload-default">
									<div class="input-group col-xs-12">
										<input type="text" class="form-control file-upload-info form-control-sm" disabled="" placeholder="Resim dosyası seçiniz">
										<span class="input-group-append">
											<button class="file-upload-browse btn btn-primary btn-sm" type="button"><i class="icon-cloud-upload font-12"></i> Dosya Seç</button>
										</span>
									</div>
								</div>
								<?php if($Sonuc['firma_logo'] == true){?>
								<div class="form-group">
									<div id="lightgallery" class="row lightGallery">
										<a class="mx-auto" href="../<?php echo tema;?>/uploads/logo/<?php echo $Sonuc['firma_logo'];?>"><img src="../<?php echo tema;?>/uploads/logo/<?php echo $Sonuc['firma_logo'];?>" style="max-height:70px;"></a>
									</div>
								</div>
								<?php }?>												
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card mb-4">
							<div class="card-header">
								Mail Şablonu ve Footer logo yükle
							</div>
							<div class="card-body">
								<div class="form-group">
									<label>Logo Yükle</label>
									<input type="file" name="footer" class="file-upload-default">
									<div class="input-group col-xs-12">
										<input type="text" class="form-control file-upload-info form-control-sm" disabled="" placeholder="Resim dosyası seçiniz">
										<span class="input-group-append">
											<button class="file-upload-browse btn btn-primary btn-sm" type="button"><i class="icon-cloud-upload font-12"></i> Dosya Seç</button>
										</span>
									</div>
								</div>	
								<?php if($Sonuc['firma_footerlogo'] == true){?>
								<div class="form-group">
									<div id="lightgallery" class="row lightGallery">
										<a class="mx-auto" href="../<?php echo tema;?>/uploads/logo/footer/<?php echo $Sonuc['firma_footerlogo'];?>"><img src="../<?php echo tema;?>/uploads/logo/footer/<?php echo $Sonuc['firma_footerlogo'];?>" style="max-height:70px;"></a>
									</div>
								</div>
								<?php }?>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card mb-4">
							<div class="card-header">
								Favicon Yükle
							</div>
							<div class="card-body">
								<div class="form-group">
									<label>Favicon</label>
									<input type="file" name="favicon" class="file-upload-default">
									<div class="input-group col-xs-12">
										<input type="text" class="form-control file-upload-info form-control-sm" disabled="" placeholder="Resim dosyası seçiniz">
										<span class="input-group-append">
											<button class="file-upload-browse btn btn-primary btn-sm" type="button"><i class="icon-cloud-upload font-12"></i> Dosya Seç</button>
										</span>
									</div>
								</div>
								<?php if($Sonuc['favicon'] == true){?>
								<div class="form-group">
									<div id="lightgallery" class="row lightGallery">
										<a class="mx-auto" href="../<?php echo tema;?>/uploads/favicon/<?php echo $Sonuc['favicon'];?>"><img src="../<?php echo tema;?>/uploads/favicon/<?php echo $Sonuc['favicon'];?>" style="max-height:70px;"></a>
									</div>
								</div>
								<?php }?>
							</div>
						</div>
					</div>
					</div>
					<div class="row grid-margin">
						<div class="col-12">
							<div class="card">
								<div class="row">
									<div class="col-lg-4 grid-margin grid-margin-lg-0">
										<div class="card-body">
											<h4 class="card-title">Renk 1 (Genel Renk)</h4>
											<input type="text" name="renk1" class="color-picker" value="<?php echo $Sonuc['renk1'];?>" />
										</div>
									</div>
									<div class="col-lg-4 grid-margin grid-margin-lg-0">
										<div class="card-body">
											<h4 class="card-title">Renk 2</h4>
											<input type="text" name="renk2" class="color-picker" value="<?php echo $Sonuc['renk2'];?>" />
										</div>
									</div>
									<div class="col-lg-4">
										<div class="card-body">
											<h4 class="card-title">Renk 3</h4>
											<input type="text" name="renk3" class="color-picker" value="<?php echo $Sonuc['renk3'];?>" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="site_url">Site Url</label>
						<input type="text" class="form-control form-control-sm" name="site_url" id="site_url" value="<?php echo $Sonuc['site_url'];?>" />
					</div>	
					
					<div class="form-group">
						<label for="instagramtoken">Site İnstagram Token Kodu</label>
						<input type="text" class="form-control form-control-sm" name="instagramtoken" id="instagramtoken" value="<?php echo $Sonuc['instagramtoken'];?>" />
					</div>
					<div class="form-group">
						<label for="instagram_username">Instagram Kullanıcı Adı <small class="text-muted">(Public Business/Creator hesap, ör: ailevenesil)</small></label>
						<input type="text" class="form-control form-control-sm" name="instagram_username" id="instagram_username" value="<?php echo isset($Sonuc['instagram_username']) ? $Sonuc['instagram_username'] : '';?>" placeholder="ailevenesil" />
					</div>

					<div class="form-group">
						<label for="site_title">Site Title</label>
						<input type="text" class="form-control form-control-sm" name="site_title" id="site_title" value="<?php echo $Sonuc['site_baslik'];?>" />
					</div>
					<div class="form-group">
						<label for="gemini_api_key">Google Gemini API Key</label>
						<input type="text" class="form-control form-control-sm" name="gemini_api_key" id="gemini_api_key" value="<?php echo isset($Sonuc['gemini_api_key']) ? $Sonuc['gemini_api_key'] : '';?>" />
					</div>
					<div class="form-group">
						<label for="havadurumu">Hava Durumu İl</label>
						<select class="form-control form-control-sm" name="havadurumu" id="havadurumu" style="width:100%">
							<option value="">-Seçiniz-</option>
							<?php $ILSorgu = $db->prepare("SELECT * FROM il ORDER BY id ASC");
							$ILSorgu->execute();
							$ILislem = $ILSorgu->fetchALL(PDO::FETCH_ASSOC);?>
							<?php foreach ( $ILislem as $ILSonuc ){?>
								<option value="<?php echo $ILSonuc['ADI']; ?>" <?php echo($Sonuc['havadurumu'] == $ILSonuc['ADI'] ? 'selected' : '');?>><?php echo $ILSonuc['ADI']; ?></option>
							<?php }?>
						</select>
					</div>
					<div class="form-group">
						<label for="site_url"><b>Yönetim Paneli Girişi Link Adresi (Örnek: xyonetim, ypanel, yadminx, Not: TR Karekter Kullanmayınız.!)</b></label>
						<input type="text" class="form-control form-control-sm" name="yonetim" id="yonetim" value="<?php echo $Sonuc['yonetim'];?>" />
					</div>	
					<div class="form-group">
						<label for="tags">SEO Kelimeler (Keywords) <small>(Kelimenin sonuna virgül koyunuz)</small></label>
						<input name="site_keyw" id="tags" value="<?php echo $Sonuc['site_keyw'];?>" />
					</div>
					<div class="form-group">
						<label for="maxlength-textarea">SEO Açıklama (Description)</label>
						<textarea id="maxlength-textarea" name="site_desc" class="form-control" maxlength="260" rows="4"><?php echo $Sonuc['site_desc'];?></textarea>
					</div>
					<div class="form-group">
						<label for="copyright">Copyright Metni</label>
						<input type="text" class="form-control form-control-sm" name="copyright" id="copyright" value="<?php echo $Sonuc['copyright'];?>" />
					</div>								
					<button type="submit" name="genel_ayarlar" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-spin mdi-loading"></i>                                                   
						GÜNCELLE
					</button>
				</form>						
			</div>
		</div>
	</div>

</div>
