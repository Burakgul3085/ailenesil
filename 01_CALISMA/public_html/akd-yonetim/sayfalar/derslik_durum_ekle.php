<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM derslik_durumlari WHERE id = ?");
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
		<h3>Derslik Durumu <?php echo(isset($_GET['islem'])=="duzenle" ? 'Düzenle' : 'Ekle');?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>">Derslik Durumları</a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?php echo(isset($_GET['islem'])=="duzenle" ? 'Düzenle' : 'Ekle');?></a></li>
			</ul>
		</div>
	</div>
</div>
<link rel="stylesheet" href="assets/css/modern_forms.css">

<div class="modern-form-container">
	<div class="modern-form-card">
		<div class="modern-form-header">
			<h4>
				<i class="fas fa-calendar-alt"></i>
				Derslik Durumu <?php echo(isset($_GET['islem'])=="duzenle" ? 'Düzenle' : 'Ekle');?>
			</h4>
		</div>
		
		<form method="post" action="../_class/yonetim_islem.php">
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
							<input type="number" class="form-control form-control-sm" min="0" name="sira" id="sira" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['sira'] : '0');?>" />
						</div>
						<div class="modern-form-group">
							<label for="sehir">Şehir <span class="text-danger">*</span></label>
							<input type="text" class="form-control form-control-sm" name="sehir" id="sehir" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['sehir']) : '');?>" required />
						</div>
						<div class="modern-form-group">
							<label for="kategori_id">Kategori (Sınıf) <span class="text-danger">*</span></label>
							<select class="form-control form-control-sm" name="kategori_id" id="kategori_id" required>
								<option value="">Kategori Seçiniz</option>
								<?php 
								$kategoriler = $db->query("SELECT * FROM derslik_kategorileri WHERE durum = 1 AND dil = '{$_SESSION['admin_dil']}' ORDER BY sira ASC, adi ASC")->fetchAll(PDO::FETCH_ASSOC);
								foreach($kategoriler as $kat): 
									$selected = (isset($_GET['islem'])=="duzenle" && isset($Sonuc['kategori_id']) && $Sonuc['kategori_id'] == $kat['id']) ? 'selected' : '';
								?>
								<option value="<?php echo $kat['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($kat['adi']); ?></option>
								<?php endforeach; ?>
							</select>
							<small class="form-text text-muted">Kategori seçildiğinde sınıf otomatik doldurulacaktır.</small>
						</div>
						<div class="modern-form-group">
							<label for="sinif">Sınıf (Manuel)</label>
							<input type="text" class="form-control form-control-sm" name="sinif" id="sinif" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['sinif']) : '');?>" placeholder="Kategori seçilmezse manuel girebilirsiniz" />
						</div>
						<div class="modern-form-group">
							<label for="program_id">Program</label>
							<select class="form-control form-control-sm" name="program_id" id="program_id">
								<option value="">Program Seçiniz (Opsiyonel)</option>
								<?php 
								$programlar = $db->query("SELECT * FROM programlar WHERE durum = 1 AND dil = '{$_SESSION['admin_dil']}' ORDER BY sira ASC, baslik ASC")->fetchAll(PDO::FETCH_ASSOC);
								foreach($programlar as $prog): 
									$selected = (isset($_GET['islem'])=="duzenle" && isset($Sonuc['program_id']) && $Sonuc['program_id'] == $prog['id']) ? 'selected' : '';
								?>
								<option value="<?php echo $prog['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($prog['baslik']); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="modern-form-group">
							<label for="baslik">Başlık</label>
							<input type="text" class="form-control form-control-sm" name="baslik" id="baslik" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['baslik']) : '');?>" placeholder="Ders başlığını giriniz" />
						</div>
					</div>
				</div>

				<!-- Tarih ve Saat Bilgileri -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-clock"></i>
						Tarih ve Saat Bilgileri
					</div>
					<div class="modern-form-grid">
						<div class="modern-form-group">
							<label for="tarih">Tarih <span class="text-danger">*</span></label>
							<input type="date" class="form-control form-control-sm" name="tarih" id="tarih" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['tarih'] : date('Y-m-d'));?>" required />
						</div>
						<div class="modern-form-group">
							<label for="gun">Gün <span class="text-danger">*</span></label>
							<select class="form-control form-control-sm" name="gun" id="gun" required>
								<option value="">Seçiniz</option>
								<option value="Pazartesi" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['gun'] == 'Pazartesi' ? 'selected' : '');?>>Pazartesi</option>
								<option value="Salı" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['gun'] == 'Salı' ? 'selected' : '');?>>Salı</option>
								<option value="Çarşamba" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['gun'] == 'Çarşamba' ? 'selected' : '');?>>Çarşamba</option>
								<option value="Perşembe" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['gun'] == 'Perşembe' ? 'selected' : '');?>>Perşembe</option>
								<option value="Cuma" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['gun'] == 'Cuma' ? 'selected' : '');?>>Cuma</option>
								<option value="Cumartesi" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['gun'] == 'Cumartesi' ? 'selected' : '');?>>Cumartesi</option>
								<option value="Pazar" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['gun'] == 'Pazar' ? 'selected' : '');?>>Pazar</option>
							</select>
						</div>
						<div class="modern-form-group">
							<label for="saat">Saat <span class="text-danger">*</span></label>
							<input type="text" class="form-control form-control-sm" name="saat" id="saat" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['saat']) : '');?>" required placeholder="Örn: 09:00-10:30" />
						</div>
					</div>
				</div>

				<!-- Kontenjan Bilgileri -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-users"></i>
						Kontenjan Bilgileri
					</div>
					<div class="modern-form-grid">
						<div class="modern-form-group">
							<label for="kontenjan">Kontenjan <span class="text-danger">*</span></label>
							<input type="number" class="form-control form-control-sm" name="kontenjan" id="kontenjan" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['kontenjan'] : '10');?>" min="1" required />
						</div>
						<div class="modern-form-group">
							<label for="katilimci">Katılımcı Sayısı <span class="text-danger">*</span></label>
							<input type="number" class="form-control form-control-sm" name="katilimci" id="katilimci" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['katilimci'] : '0');?>" min="0" required />
						</div>
						<div class="modern-form-group">
							<label for="aile_katilim">Aile Katılımı <span class="text-danger">*</span></label>
							<select class="form-control form-control-sm" name="aile_katilim" id="aile_katilim" required>
								<option value="0" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['aile_katilim'] == '0' ? 'selected' : '');?>>Hayır</option>
								<option value="1" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['aile_katilim'] == '1' ? 'selected' : '');?>>Evet</option>
							</select>
						</div>
						<div class="modern-form-group">
							<label for="durum">Durum <span class="text-danger">*</span></label>
							<select class="form-control form-control-sm" name="durum" id="durum" required>
								<option value="0" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['durum'] == '0' ? 'selected' : '');?>>Boş (Kayıt Alınabilir)</option>
								<option value="1" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['durum'] == '1' ? 'selected' : '');?>>Dolu (Kontenjan Dolmuş)</option>
								<option value="2" <?php echo(isset($_GET['islem'])=="duzenle" && $Sonuc['durum'] == '2' ? 'selected' : '');?>>Bekleme Listesi</option>
							</select>
						</div>
					</div>
				</div>

				<!-- Durum Ayarları -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-toggle-on"></i>
						Durum Ayarları
					</div>
					<div class="modern-switch-wrapper">
						<label for="aktif">Aktif/Pasif</label>
						<?php if(isset($_GET['islem'])=="duzenle"){?>
						<label class="switch">
							<input type="checkbox" name="aktif" id="aktif" value="1" <?php if(isset($Sonuc['aktif']) && $Sonuc['aktif'] == '1') {?> checked <?php } ?>>
							<span class="slider"></span>
						</label>
						<?php }else{?>
						<label class="switch">
							<input type="checkbox" name="aktif" id="aktif" value="1" checked>
							<span class="slider"></span>
						</label>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="modern-form-actions">
				<a href="derslik_durum_listele.html" class="modern-btn modern-btn-light">
					<i class="fas fa-times"></i> İptal
				</a>
				<?php if(isset($_GET['islem'])=="duzenle"){?>
				<button type="submit" name="derslik_durum_guncelle" class="modern-btn modern-btn-success">
					<i class="fas fa-save"></i> Güncelle
				</button>
				<?php }else{?>
				<button type="submit" name="derslik_durum_ekle" class="modern-btn modern-btn-primary">
					<i class="fas fa-plus"></i> Kaydet
				</button>
				<?php } ?>
			</div>
		</form>
	</div>
</div>

<script>
$(document).ready(function(){
	// Kategori seçildiğinde sınıf alanını otomatik doldur
	$('#kategori_id').on('change', function(){
		if($(this).val() != ''){
			var kategoriAdi = $(this).find('option:selected').text();
			$('#sinif').val(kategoriAdi);
		} else {
			$('#sinif').val('');
		}
	});
});
</script>
<?php 
cVCLmHLxbS_mesaj("derslik_durum_ekle",1,"yes","Başarı ile eklenmiştir.");
cVCLmHLxbS_mesaj("derslik_durum_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("derslik_durum_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("derslik_durum_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("derslikdurumsil",1,"yes","Başarı ile siinmiştir.");
cVCLmHLxbS_mesaj("derslikdurumsil",2,"no","Hata oluştu tekrar deneyiniz.!");
?>


