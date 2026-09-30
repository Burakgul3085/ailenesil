<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
$sabitSorgu = $db->query("SELECT karakterprogramlari_text, karakterprogramlari_video FROM sabit_url LIMIT 1");
$sabitSonuc = $sabitSorgu->fetch(PDO::FETCH_ASSOC);
?>

<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Karakter Programları - Sayfa Üst Metni</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="karakter_program_listele.html">Karakter Programları</a></li>
				<li class="active">Sayfa Üst Metni</li>
			</ul>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<form method="post" action="../_class/yonetim_islem.php">
			<div class="form-group">
				<label><strong>Sayfa Üst Metni</strong></label>
				<p class="text-muted mb-2">Bu metin, Karakter ve Sosyal Gelişim Programları sayfasında banner yerine gösterilir. Boş bırakılırsa varsayılan banner görsel görünür.</p>
				<textarea name="karakterprogramlari_text" id="myTextarea"><?php echo isset($sabitSonuc['karakterprogramlari_text']) ? $sabitSonuc['karakterprogramlari_text'] : ''; ?></textarea>
			</div>
			<div class="form-group mt-4">
				<label><strong>Tanıtım Videosu (YouTube/Vimeo URL)</strong></label>
				<p class="text-muted mb-2">YouTube veya Vimeo video linkini yapıştırın. Metin altında video alanı olarak görünecektir. Boş bırakılırsa video alanı gösterilmez.</p>
				<input type="text" name="karakterprogramlari_video" class="form-control" placeholder="https://www.youtube.com/watch?v=..." value="<?php echo isset($sabitSonuc['karakterprogramlari_video']) ? htmlspecialchars($sabitSonuc['karakterprogramlari_video']) : ''; ?>">
			</div>
			<button type="submit" name="karakter_program_ayar_kaydet" class="btn btn-primary">
				<i class="fas fa-save"></i> Kaydet
			</button>
		</form>
	</div>
</div>
<?php
cVCLmHLxbS_mesaj("karakter_program_ayar_kaydet",1,"yes","Başarıyla kaydedildi.");
cVCLmHLxbS_mesaj("karakter_program_ayar_kaydet",2,"no","Hata oluştu tekrar deneyiniz!");
?>
