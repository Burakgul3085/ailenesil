<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
$testKayit = $db->query("SELECT * FROM testler WHERE seo = 'mizac-testi' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if(!$testKayit) {
    echo '<div class="alert alert-danger">Test kaydı bulunamadı.</div>';
    return;
}

$tiplerSorgu = $db->prepare("SELECT * FROM test_mizac_tipleri WHERE test_id = ? ORDER BY mizac_tipi ASC");
$tiplerSorgu->execute(array($testKayit['id']));
$tipler = $tiplerSorgu->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Test Ayarları</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li class="active"><a href="test-ayarlari.html">Test Ayarları</a></li>
			</ul>
		</div>
	</div>
</div>

<!-- Genel Ayarlar -->
<div class="row">
	<div class="col-md-8 grid-margin">
		<div class="card">
			<div class="card-body">
				<h5 class="mb-3">Genel Ayarlar</h5>
				<form class="forms-sample" method="post" action="../_class/yonetim_islem.php">
					<input type="hidden" name="id" value="<?php echo $testKayit['id']; ?>">

					<div class="form-group">
						<label for="adi"><strong>Test Adı</strong></label>
						<input type="text" class="form-control form-control-sm" name="adi" id="adi" value="<?php echo htmlspecialchars($testKayit['adi']); ?>">
					</div>

					<div class="form-group">
						<label for="aciklama"><strong>Açıklama</strong> <small class="text-muted">(Banner altındaki kısa açıklama)</small></label>
						<textarea name="aciklama" id="aciklama" class="form-control form-control-sm" rows="3"><?php echo htmlspecialchars($testKayit['aciklama'] ?? ''); ?></textarea>
					</div>

					<div class="form-group">
						<label for="giris_metni"><strong>Giriş Metni</strong> <small class="text-muted">(Teste başlamadan önce formun altında görünecek paragraf)</small></label>
						<textarea name="giris_metni" id="giris_metni" class="form-control form-control-sm" rows="5"><?php echo htmlspecialchars($testKayit['giris_metni'] ?? ''); ?></textarea>
					</div>

					<div class="form-group">
						<label for="sonuc_metni"><strong>Sonuç Metni</strong> <small class="text-muted">(Test sonucu raporunun altında görünecek paragraf)</small></label>
						<textarea name="sonuc_metni" id="sonuc_metni" class="form-control form-control-sm" rows="5"><?php echo htmlspecialchars($testKayit['sonuc_metni'] ?? ''); ?></textarea>
					</div>

					<button type="submit" name="test_ayar_guncelle" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-reload btn-icon-prepend"></i> GÜNCELLE
					</button>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Mizaç Tipleri -->
<div class="row">
	<div class="col-md-12 grid-margin">
		<div class="card">
			<div class="card-body">
				<h5 class="mb-1">Sonuç Tipleri</h5>
				<p class="text-muted mb-4">Test sonucunda kullanıcıya gösterilen tip bilgilerini buradan düzenleyebilirsiniz.</p>

				<form method="post" action="../_class/yonetim_islem.php">
					<input type="hidden" name="test_id" value="<?php echo $testKayit['id']; ?>">

					<?php foreach($tipler as $tip): ?>
					<div class="card mb-3" style="border-left: 4px solid <?php echo htmlspecialchars($tip['renk']); ?>;">
						<div class="card-body py-3">
							<input type="hidden" name="tip_id[]" value="<?php echo $tip['id']; ?>">
							<div class="row">
								<div class="col-md-2">
									<div class="form-group">
										<label><strong>Tip No</strong></label>
										<input type="number" class="form-control form-control-sm" name="tip_mizac[<?php echo $tip['id']; ?>]" value="<?php echo $tip['mizac_tipi']; ?>" min="0" max="9">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label><strong>Başlık</strong></label>
										<input type="text" class="form-control form-control-sm" name="tip_baslik[<?php echo $tip['id']; ?>]" value="<?php echo htmlspecialchars($tip['baslik']); ?>">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label><strong>Etiket</strong></label>
										<input type="text" class="form-control form-control-sm" name="tip_etiket[<?php echo $tip['id']; ?>]" value="<?php echo htmlspecialchars($tip['etiket'] ?? ''); ?>">
									</div>
								</div>
								<div class="col-md-1">
									<div class="form-group">
										<label><strong>Renk</strong></label>
										<input type="color" class="form-control form-control-sm" name="tip_renk[<?php echo $tip['id']; ?>]" value="<?php echo htmlspecialchars($tip['renk']); ?>" style="height:34px; padding:2px;">
									</div>
								</div>
								<div class="col-md-5">
									<div class="form-group">
										<label><strong>Açıklama</strong></label>
										<textarea class="form-control form-control-sm" name="tip_aciklama[<?php echo $tip['id']; ?>]" rows="3"><?php echo htmlspecialchars($tip['aciklama']); ?></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach; ?>

					<button type="submit" name="test_tipleri_guncelle" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-reload btn-icon-prepend"></i> TİPLERİ GÜNCELLE
					</button>
				</form>

				<hr class="mt-4 mb-4">

				<h5>Yeni Tip Ekle</h5>
				<form method="post" action="../_class/yonetim_islem.php">
					<input type="hidden" name="test_id" value="<?php echo $testKayit['id']; ?>">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label>Tip No</label>
								<input type="number" class="form-control form-control-sm" name="yeni_mizac_tipi" min="0" max="9" required>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Başlık</label>
								<input type="text" class="form-control form-control-sm" name="yeni_baslik" required placeholder="Tip başlığı">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Etiket</label>
								<input type="text" class="form-control form-control-sm" name="yeni_etiket" placeholder="Kısa etiket">
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group">
								<label>Renk</label>
								<input type="color" class="form-control form-control-sm" name="yeni_renk" value="#333333" style="height:34px; padding:2px;">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Açıklama</label>
								<textarea class="form-control form-control-sm" name="yeni_aciklama" rows="2" required placeholder="Tip açıklaması"></textarea>
							</div>
						</div>
						<div class="col-md-2 d-flex align-items-end">
							<button type="submit" name="test_tip_ekle" class="btn btn-primary btn-sm mb-3">
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
cVCLmHLxbS_mesaj("test_ayar_guncelle",1,"yes","Test ayarları başarıyla güncellendi.");
cVCLmHLxbS_mesaj("test_ayar_guncelle",2,"no","Hata oluştu tekrar deneyiniz!");
cVCLmHLxbS_mesaj("test_tipleri_guncelle",1,"yes","Sonuç tipleri başarıyla güncellendi.");
cVCLmHLxbS_mesaj("test_tipleri_guncelle",2,"no","Hata oluştu tekrar deneyiniz!");
cVCLmHLxbS_mesaj("test_tip_ekle",1,"yes","Yeni tip başarıyla eklendi.");
cVCLmHLxbS_mesaj("test_tip_ekle",2,"no","Hata oluştu tekrar deneyiniz!");
?>