<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
$testKayit = $db->query("SELECT * FROM testler WHERE seo = 'mizac-testi' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if(!$testKayit) {
    echo '<div class="alert alert-danger">Test kaydı bulunamadı.</div>';
    return;
}

$kutular = $db->prepare("SELECT * FROM test_kutulari WHERE test_id = ? ORDER BY adim ASC, sira ASC");
$kutular->execute(array($testKayit['id']));
$kutularData = $kutular->fetchAll(PDO::FETCH_ASSOC);

$adimlar = array();
foreach($kutularData as $k) {
    $adimlar[$k['adim']][] = $k;
}
?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Test Soruları</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li class="active"><a href="test-sorulari.html">Test Soruları</a></li>
			</ul>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12 grid-margin">
		<div class="card">
			<div class="card-body">
				<p class="text-muted mb-4">Her adımdaki kutuların içeriklerini düzenleyebilirsiniz. Her satır bir madde olarak görünür.</p>

				<form method="post" action="../_class/yonetim_islem.php">
					<input type="hidden" name="test_id" value="<?php echo $testKayit['id']; ?>">

					<?php for($adim = 1; $adim <= 3; $adim++): ?>
					<div class="card mb-4" style="border: 2px solid #e0e0e0;">
						<div class="card-header bg-light">
							<h5 class="mb-0"><i class="mdi mdi-numeric-<?php echo $adim; ?>-circle"></i> Adım <?php echo $adim; ?></h5>
						</div>
						<div class="card-body">
							<div class="row">
								<?php if(isset($adimlar[$adim])): ?>
								<?php foreach($adimlar[$adim] as $kutu): ?>
								<div class="col-md-4 mb-3">
									<input type="hidden" name="kutu_id[]" value="<?php echo $kutu['id']; ?>">
									<div class="form-group">
										<label><strong>Kutu <?php echo htmlspecialchars($kutu['harf']); ?></strong>
										<small class="text-muted">(Enneagram Tip: <?php echo $kutu['enneagram_tipi']; ?>)</small></label>
										<input type="text" class="form-control form-control-sm mb-2" name="kutu_harf[<?php echo $kutu['id']; ?>]" value="<?php echo htmlspecialchars($kutu['harf']); ?>" maxlength="1" style="width:60px;" placeholder="Harf">
										<textarea name="kutu_maddeler[<?php echo $kutu['id']; ?>]" class="form-control form-control-sm" rows="8" placeholder="Her satır bir madde"><?php echo htmlspecialchars($kutu['maddeler']); ?></textarea>
									</div>
									<div class="form-group mt-2">
										<label><small>Enneagram Tipi</small></label>
										<input type="number" class="form-control form-control-sm" name="kutu_tip[<?php echo $kutu['id']; ?>]" value="<?php echo $kutu['enneagram_tipi']; ?>" min="1" max="9" style="width:80px;">
									</div>
								</div>
								<?php endforeach; ?>
								<?php else: ?>
								<div class="col-12">
									<div class="alert alert-warning">Bu adım için henüz kutu tanımlanmamış.</div>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<?php endfor; ?>

					<button type="submit" name="test_kutulari_guncelle" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-reload btn-icon-prepend"></i> GÜNCELLE
					</button>
				</form>

				<hr class="mt-4 mb-4">

				<h5>Yeni Kutu Ekle</h5>
				<form method="post" action="../_class/yonetim_islem.php">
					<input type="hidden" name="test_id" value="<?php echo $testKayit['id']; ?>">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label>Adım</label>
								<select name="yeni_adim" class="form-control form-control-sm" required>
									<option value="1">Adım 1</option>
									<option value="2">Adım 2</option>
									<option value="3">Adım 3</option>
								</select>
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group">
								<label>Harf</label>
								<input type="text" class="form-control form-control-sm" name="yeni_harf" maxlength="1" placeholder="A">
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group">
								<label>Tip</label>
								<input type="number" class="form-control form-control-sm" name="yeni_tip" min="1" max="9" required placeholder="1">
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group">
								<label>Sıra</label>
								<input type="number" class="form-control form-control-sm" name="yeni_sira" min="1" max="9" value="1">
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								<label>Maddeler</label>
								<textarea name="yeni_maddeler" class="form-control form-control-sm" rows="4" placeholder="Her satır bir madde" required></textarea>
							</div>
						</div>
						<div class="col-md-2 d-flex align-items-end">
							<button type="submit" name="test_kutu_ekle" class="btn btn-primary btn-sm mb-3">
								<i class="mdi mdi-plus"></i> Ekle
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
cVCLmHLxbS_mesaj("test_kutulari_guncelle",1,"yes","Kutular başarıyla güncellendi.");
cVCLmHLxbS_mesaj("test_kutulari_guncelle",2,"no","Hata oluştu tekrar deneyiniz!");
cVCLmHLxbS_mesaj("test_kutu_ekle",1,"yes","Yeni kutu başarıyla eklendi.");
cVCLmHLxbS_mesaj("test_kutu_ekle",2,"no","Hata oluştu tekrar deneyiniz!");
cVCLmHLxbS_mesaj("test_kutu_sil",1,"yes","Kutu başarıyla silindi.");
?>