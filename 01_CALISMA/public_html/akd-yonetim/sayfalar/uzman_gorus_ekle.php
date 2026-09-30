<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM uzman_gorusleri WHERE id = ?");
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
		<h3>Uzman Görüşü <?php echo (isset($_GET['islem'])=="duzenle" ? 'Düzenle' : 'Ekle');?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>">İstatistik</a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>">Uzman Görüşü <?php echo (isset($_GET['islem'])=="duzenle" ? 'Düzenle' : 'Ekle');?></a></li>
			</ul>
		</div>
	</div>
</div>
<link rel="stylesheet" href="assets/css/modern_forms.css">

<div class="modern-form-container">
	<div class="modern-form-card">
		<div class="modern-form-header">
			<h4>
				<i class="fas fa-user-tie"></i>
				Uzman Görüşü <?php echo (isset($_GET['islem'])=="duzenle" ? 'Düzenle' : 'Ekle');?>
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
							<input type="number" class="form-control form-control-sm" min="0" name="sira" id="sira" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['sira'] : '');?>" />
						</div>
						<div class="modern-form-group">
							<label for="isim">İsim Soyisim <span class="text-danger">*</span></label>
							<input type="text" class="form-control form-control-sm" name="isim" id="isim" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['isim']) : '');?>" required />
						</div>
						<div class="modern-form-group">
							<label for="gorev">Görev / Unvan <span class="text-danger">*</span></label>
							<input type="text" class="form-control form-control-sm" name="gorev" id="gorev" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['gorev']) : '');?>" required />
						</div>
					</div> 
				</div>

				<!-- Görsel Ayarları -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-image"></i>
						Profil Resmi
					</div>
					<div class="modern-form-group">
						<?php if(isset($_GET['islem']) && $_GET['islem']=="duzenle" && !empty($Sonuc['resim'])){?>
							<div class="modern-image-preview">
								<img src="../<?php echo tema;?>/uploads/uzmanlar/<?php echo $Sonuc['resim'];?>" alt="Profil Resmi">
								<div class="mt-2">
									<a href="../_class/yonetim_islem.php?uzman_gorus_resim_sil=ok&id=<?php echo $Sonuc['id'];?>" class="btn btn-danger btn-sm popconfirm" data-message="Bu resmi silmek istediğinizden emin misiniz?">
										<i class="fas fa-trash"></i> Resmi Sil
									</a>
								</div>
							</div>
						<?php } ?>
						<div class="modern-file-upload">
							<input type="file" name="resim" class="file-upload-default">
							<div class="input-group">
								<input type="text" class="form-control file-upload-info" disabled placeholder="Resim Yükle">
								<span class="input-group-append">
									<button class="file-upload-browse btn btn-primary" type="button">
										<i class="fas fa-cloud-upload-alt"></i> Seç
									</button>
								</span>
							</div>
						</div>
					</div>
				</div>

				<!-- Görüş / Yorum -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-comment-dots"></i>
						Görüş / Yorum
					</div>
					<div class="modern-form-group full-width">
						<label for="yorum">Görüş / Yorum <span class="text-danger">*</span></label>
						<textarea class="form-control form-control-sm" name="yorum" id="yorum" rows="5" required><?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['yorum']) : '');?></textarea>
					</div>
				</div>

				<!-- Durum Ayarları -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-toggle-on"></i>
						Durum Ayarları
					</div>
					<div class="modern-form-group">
						<label for="durum">Durum</label>
						<select class="form-control form-control-sm" id="durum" name="durum">
							<option value="1" <?php echo(isset($_GET['islem'])=="duzenle" && isset($Sonuc['durum']) && $Sonuc['durum'] == 1 ? 'selected' : ''); ?>>Aktif</option>
							<option value="0" <?php echo(isset($_GET['islem'])=="duzenle" && isset($Sonuc['durum']) && $Sonuc['durum'] == 0 ? 'selected' : ''); ?>>Pasif</option>
						</select>
					</div>
				</div>
			</div>

			<div class="modern-form-actions">
				<a href="uzman_gorus_listele.html" class="modern-btn modern-btn-light">
					<i class="fas fa-times"></i> İptal
				</a>
				<?php if(isset($_GET['islem'])=="duzenle"){?>
				<button type="submit" name="uzman_gorus_guncelle" class="modern-btn modern-btn-success">
					<i class="fas fa-save"></i> Güncelle
				</button>
				<?php }else{?>
				<button type="submit" name="uzman_gorus_ekle" class="modern-btn modern-btn-primary">
					<i class="fas fa-plus"></i> Kaydet
				</button>
				<?php }?>
			</div>
		</form>
	</div>
</div>
