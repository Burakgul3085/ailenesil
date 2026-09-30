<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
$Sonuc = array(); // Varsayılan boş array
if(isset($_GET['islem']) && $_GET['islem'] == "duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM ogrenme_deneyimi WHERE id = ?");
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

// Programları çek (dropdown için)
$programlarSorgu = $db->prepare("SELECT id, baslik FROM programlar WHERE durum = ? AND dil = ? ORDER BY sira ASC");
$programlarSorgu->execute(array("1", $_SESSION['admin_dil']));
$programlar = $programlarSorgu->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Öğrenme Deneyimi <?php echo (isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? 'Düzenle' : 'Ekle';?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="ogrenme_deneyimi_listele.html">Öğrenme Deneyimleri</a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?php echo (isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? 'Düzenle' : 'Ekle';?></a></li>
			</ul>
		</div>
	</div>
</div>
<link rel="stylesheet" href="assets/css/modern_forms.css">

<div class="modern-form-container">
	<div class="modern-form-card">
		<div class="modern-form-header">
			<h4>
				<i class="fas fa-graduation-cap"></i>
				Öğrenme Deneyimi <?php echo (isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? 'Düzenle' : 'Ekle';?>
			</h4>
		</div>
		
		<form method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
			<input type="hidden" name="id" value="<?php echo isset($Sonuc['id']) ? $Sonuc['id'] : ''; ?>">
			
			<div class="modern-form-body">
				<!-- Temel Bilgiler -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-info-circle"></i>
						Temel Bilgiler
					</div>
					<div class="modern-form-grid">
						<div class="modern-form-group">
							<label for="sira">Sıra</label>
							<input type="number" class="form-control form-control-sm" min="0" name="sira" id="sira" value="<?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? $Sonuc['sira'] : '');?>" />
						</div>
						<div class="modern-form-group">
							<label for="baslik">Başlık <span class="text-danger">*</span></label>
							<input type="text" class="form-control form-control-sm" name="baslik" id="baslik" value="<?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['baslik']) : '');?>" required />
						</div> 
						<div class="modern-form-group full-width">
							<label for="aciklama">Açıklama</label>
							<textarea name="aciklama" class="form-control form-control-sm" rows="3"><?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['aciklama']) : '');?></textarea>
						</div>
					</div>
				</div>

				<!-- Görsel ve İçerik Ayarları -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-image"></i>
						Görsel ve İçerik Ayarları
					</div>
					<div class="modern-form-grid">
						<div class="modern-form-group">
							<label>Resim</label>
							<?php if((isset($_GET['islem']) && $_GET['islem'] == "duzenle") && !empty($Sonuc['resim'])){?>
								<div class="modern-image-preview">
									<div id="lightgallery" class="lightGallery">
										<a href="../<?php echo tema;?>/uploads/ogrenme_deneyimi/<?php echo $Sonuc['resim'];?>">
											<img src="../<?php echo tema;?>/uploads/ogrenme_deneyimi/<?php echo $Sonuc['resim'];?>" alt="Öğrenme Deneyimi Resmi">
										</a>
									</div>
									<a class="btn btn-danger btn-sm mt-2 popconfirm" title="Resmi Sil" href="../_class/yonetim_islem.php?ogrenme_deneyimi_resimsil=ok&sid=<?php echo $Sonuc['id'];?>">
										<i class="fas fa-trash"></i> Sil
									</a>
								</div>
							<?php } ?>
							<div class="modern-file-upload">
								<input type="file" name="resim" class="file-upload-default">
								<div class="input-group">
									<input type="text" class="form-control file-upload-info form-control-sm" disabled placeholder="Resim dosyası seçiniz">
									<span class="input-group-append">
										<button class="file-upload-browse btn btn-primary btn-sm" type="button">
											<i class="fas fa-cloud-upload-alt"></i> Seç
										</button>
									</span>
								</div>
							</div>
						</div>
						<div class="modern-form-group">
							<label for="program_id">Program Seçimi</label>
							<select class="form-control form-control-sm" name="program_id" id="program_id">
								<option value="0">Program Seçiniz</option>
								<?php foreach($programlar as $program){ ?>
								<option value="<?php echo $program['id']; ?>" <?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") && isset($Sonuc['program_id']) && $Sonuc['program_id'] == $program['id'] ? 'selected' : '');?>>
									<?php echo htmlspecialchars($program['baslik']); ?>
								</option>
								<?php } ?>
							</select>
							<small class="form-text text-muted">Program seçilirse, o programın başlık ve açıklama bilgileri kullanılacaktır.</small>
						</div>
						<div class="modern-form-group full-width">
							<label for="kisa_aciklama">Kısa Açıklama</label>
							<textarea name="kisa_aciklama" class="form-control form-control-sm" rows="3" placeholder="Card'da gösterilecek kısa açıklama"><?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['kisa_aciklama']) : '');?></textarea>
						</div>
					</div>
				</div>

				<!-- Tam Açıklama -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-align-left"></i>
						Tam Açıklama
					</div>
					<div class="modern-form-group full-width">
						<label for="tam_aciklama">Tam Açıklama</label>
						<textarea name="tam_aciklama" id="myTextarea"><?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? $Sonuc['tam_aciklama'] : '');?></textarea>
					</div>
				</div>

				<!-- Durum Ayarları -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-toggle-on"></i>
						Durum Ayarları
					</div>
					<div class="modern-switch-wrapper">
						<label for="durum">Durum</label>
						<?php if((isset($_GET['islem']) && $_GET['islem'] == "duzenle")){?>
						<label class="switch">
							<input type="checkbox" name="durum" id="durum" value="1" <?php if(isset($Sonuc['durum']) && $Sonuc['durum'] == '1') {?> checked <?php } ?>>
							<span class="slider"></span>
						</label>
						<?php }else{?>
						<label class="switch">
							<input type="checkbox" name="durum" id="durum" value="1" checked>
							<span class="slider"></span>
						</label>
						<?php } ?>
					</div>
				</div>

				<!-- SEO Ayarları -->
				<div class="modern-seo-card">
					<div class="card-header">
						<h5>
							<i class="fas fa-search"></i>
							SEO AYARLARI
						</h5>
					</div>
					<div class="card-body">
						<div class="modern-form-group">
							<label for="seo">SEO URL</label>
							<input type="text" class="form-control form-control-sm" name="seo" id="seo" value="<?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['seo']) : '');?>" />
						</div>
						<div class="modern-form-group">
							<label for="maxlength-textarea">Description</label>
							<textarea id="maxlength-textarea" name="description" class="form-control" maxlength="260" rows="4"><?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['description']) : '');?></textarea>
						</div>
						<div class="modern-form-group">
							<label for="tags">Keywords <small>(Kelimenin sonuna virgül koyunuz)</small></label>
							<input name="keywords" id="tags" value="<?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['keywords']) : '');?>" />
						</div>
					</div>
				</div>
			</div>

			<div class="modern-form-actions">
				<a href="ogrenme_deneyimi_listele.html" class="modern-btn modern-btn-light">
					<i class="fas fa-times"></i> İptal
				</a>
				<?php if((isset($_GET['islem']) && $_GET['islem'] == "duzenle")){?>
				<button type="submit" name="ogrenme_deneyimi_guncelle" class="modern-btn modern-btn-success">
					<i class="fas fa-save"></i> Güncelle
				</button>
				<?php }else{?>
				<button type="submit" name="ogrenme_deneyimi_ekle" class="modern-btn modern-btn-primary">
					<i class="fas fa-plus"></i> Kaydet
				</button>
				<?php } ?>
			</div>
		</form>
	</div>
</div>
<?php 
cVCLmHLxbS_mesaj("ogrenme_deneyimi_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_resimsil",1,"yes","Başarı ile silinmiştir.");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_resimsil",2,"no","Hata oluştu tekrar deneyiniz.!");
?>

