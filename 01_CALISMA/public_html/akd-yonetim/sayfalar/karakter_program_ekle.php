<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM karakter_programlari WHERE id = ?");
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
		<h3>Karakter Programı Ekle / Düzenle</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="karakter_program_listele.html">Karakter Programları</a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>">Karakter Programı Ekle / Düzenle</a></li>
			</ul>
		</div>
	</div>
</div>
<link rel="stylesheet" href="assets/css/modern_forms.css">

<div class="modern-form-container">
	<div class="modern-form-card">
		<div class="modern-form-header">
			<h4>
				<i class="fas fa-heart"></i>
				Karakter Programı Ekle / Düzenle
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
							<label for="sira"><?=@$admindil['txt144'];?></label>
							<input type="number" class="form-control form-control-sm" min="0" name="sira" id="sira" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['sira'] : '');?>" />
						</div>
						<div class="modern-form-group">
							<label for="baslik">Başlık</label>
							<input type="text" class="form-control form-control-sm" name="baslik" id="baslik" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['baslik']) : '');?>" />
						</div>
						<div class="modern-form-group full-width">
							<label for="aciklama">Kısa Açıklama</label>
							<textarea name="aciklama" id="aciklama" class="form-control form-control-sm" rows="3"><?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['aciklama']) : '');?></textarea>
						</div>
						<div class="modern-form-group full-width">
							<label for="detay_aciklama">Detaylı Açıklama</label>
							<textarea name="detay_aciklama" id="myTextarea" class="form-control form-control-sm tinymce-editor" rows="8"><?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['detay_aciklama'] : '');?></textarea>
						</div>
					</div>
				</div>

				<!-- Resim -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-image"></i>
						Görsel
					</div>
					<div class="modern-form-grid">
						<div class="modern-form-group">
							<label>Resim</label>
							<?php if(isset($_GET['islem'])=="duzenle" && !empty($Sonuc['resim'])){?>
								<div class="modern-image-preview">
									<div id="lightgallery" class="lightGallery">
										<a href="../<?php echo tema;?>/uploads/karakter_programlari/<?php echo $Sonuc['resim'];?>">
											<img src="../<?php echo tema;?>/uploads/karakter_programlari/kapak/<?php echo $Sonuc['resim'];?>" alt="Resim">
										</a>
									</div>
									<a class="btn btn-danger btn-sm mt-2 popconfirm" title="Resmi Sil" href="../_class/yonetim_islem.php?karakterprogramresimsil=ok&sid=<?php echo $Sonuc['id'];?>">
										<i class="fas fa-trash"></i> Sil
									</a>
								</div>
							<?php }?>
							<div class="modern-file-upload">
								<input type="file" name="resim" class="file-upload-default">
								<div class="input-group">
									<input type="text" class="form-control file-upload-info form-control-sm" disabled placeholder="Resim seçiniz">
									<span class="input-group-append">
										<button class="file-upload-browse btn btn-primary btn-sm" type="button">
											<i class="fas fa-cloud-upload-alt"></i> Dosya Seç
										</button>
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- SEO Ayarları -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-search"></i>
						SEO Ayarları
					</div>
					<div class="modern-form-grid">
						<div class="modern-form-group">
							<label for="seo">SEO URL</label>
							<input type="text" class="form-control form-control-sm" name="seo" id="seo" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['seo'] : '');?>" />
						</div>
						<div class="modern-form-group full-width">
							<label for="description">Meta Açıklama</label>
							<textarea name="description" id="description" class="form-control form-control-sm" rows="2"><?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['description']) : '');?></textarea>
						</div>
						<div class="modern-form-group full-width">
							<label for="keywords">Anahtar Kelimeler</label>
							<textarea name="keywords" id="keywords" class="form-control form-control-sm" rows="2"><?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['keywords']) : '');?></textarea>
						</div>
					</div>
				</div>

				<!-- Durum Ayarları -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-cog"></i>
						Durum Ayarları
					</div>
					<div class="modern-form-grid">
						<div class="modern-form-group">
							<div class="modern-toggle-group">
								<label class="modern-toggle">
									<input type="checkbox" name="durum" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['durum'] == 1 ? 'checked' : (!isset($_GET['islem']) ? 'checked' : ''));?>>
									<span class="modern-toggle-slider"></span>
								</label>
								<span><?=@$admindil['txt149'];?></span>
							</div>
						</div>
						<div class="modern-form-group">
							<label for="dil">Dil</label>
							<select class="form-control form-control-sm" name="dil">
								<?php
								$DilSorgu = $db->query("SELECT * FROM diller WHERE durum = 1 ORDER BY sira ASC");
								while($DilSonuc = $DilSorgu->fetch(PDO::FETCH_ASSOC)){
								?>
								<option value="<?php echo $DilSonuc['id'];?>" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['dil'] == $DilSonuc['id'] ? 'selected' : ($_SESSION['admin_dil'] == $DilSonuc['id'] ? 'selected' : ''));?>><?php echo $DilSonuc['baslik'];?></option>
								<?php }?>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="modern-form-actions">
				<a href="karakter_program_listele.html" class="modern-btn modern-btn-light">
					<i class="fas fa-times"></i> İptal
				</a>
				<?php if(isset($_GET['islem'])=="duzenle"){?>
				<button type="submit" name="karakter_program_guncelle" class="modern-btn modern-btn-success">
					<i class="fas fa-save"></i> <?=@$admindil['txt154'];?>
				</button>
				<?php }else{?>
				<button type="submit" name="karakter_program_ekle" class="modern-btn modern-btn-primary">
					<i class="fas fa-plus"></i> <?=@$admindil['txt153'];?>
				</button>
				<?php }?>
			</div>
		</form>
	</div>
</div>
<?php
cVCLmHLxbS_mesaj("karakter_program_guncelle",1,"yes","Bilgileriniz başarıyla güncellendi.");
cVCLmHLxbS_mesaj("karakter_program_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
?>
