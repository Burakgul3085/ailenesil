<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM bagis_moduller WHERE id = ?");
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
		<h3><?php echo(isset($_GET['islem'])=="duzenle" ? 'Bağış Modülü Düzenle' : 'Bağış Modülü Ekle');?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $url;?>/yonetim/bagis-moduller.html"><?=@$admindil['txt57'];?></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?php echo(isset($_GET['islem'])=="duzenle" ? 'Düzenle' : 'Ekle');?></a></li>
			</ul>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
			<input id="id" name="id" type="hidden" value="<?php echo isset($Sonuc['id']) ? $Sonuc['id'] : ''; ?>">
			
			<!-- Temel Bilgiler -->
			<div class="card mb-4">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0"><i class="ti-info-alt mr-2"></i> Temel Bilgiler</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label for="adi">Modül Adı / Kampanya Adı <span class="text-danger">*</span></label>
								<input type="text" class="form-control form-control-sm" name="adi" id="adi" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['adi']) : '');?>" required />
								<small class="form-text text-muted"><i class="ti-light-bulb mr-1"></i> Örnek: "Ramazan Kampanyası 2024", "Deprem Yardımı", "Okul Yapımı Projesi"</small>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="sira">Sıra</label>
								<input type="number" class="form-control form-control-sm" min="0" name="sira" id="sira" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['sira'] : '0');?>" />
								<small class="form-text text-muted">Listeleme sırası (küçük sayılar önce görünür)</small>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label for="aciklama">Açıklama</label>
						<textarea rows="4" class="form-control form-control-sm" name="aciklama" id="aciklama" placeholder="Kampanya/modül hakkında detaylı açıklama yazın..."><?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['aciklama']) : '');?></textarea>
						<small class="form-text text-muted">Bu açıklama kampanya detay sayfasında gösterilir</small>
					</div>
					
					<div class="form-group">
						<label for="icon_class">Font Awesome İkon</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="icon-preview">
									<i class="<?php echo(isset($_GET['islem'])=="duzenle" && !empty($Sonuc['icon_class']) ? htmlspecialchars($Sonuc['icon_class']) : 'fas fa-heart');?>"></i>
								</span>
							</div>
							<input type="text" class="form-control form-control-sm" name="icon_class" id="icon_class" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['icon_class']) : '');?>" placeholder="fas fa-heart" />
							<div class="input-group-append">
								<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#iconPickerModal">
									<i class="ti-search"></i> İkon Seç
								</button>
							</div>
						</div>
						<small class="form-text text-muted">Font Awesome ikon sınıfını girin veya ikon seçiciyi kullanın</small>
					</div>
				</div>
			</div>
			
			<!-- Finansal Bilgiler -->
			<div class="card mb-4">
				<div class="card-header bg-success text-white">
					<h5 class="mb-0"><i class="ti-money mr-2"></i> Finansal Bilgiler</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="hedef_tutar">Hedef Tutar (TL)</label>
								<div class="input-group">
									<input type="number" step="0.01" class="form-control form-control-sm" name="hedef_tutar" id="hedef_tutar" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['hedef_tutar'] : '0.00');?>" />
									<div class="input-group-append">
										<span class="input-group-text">₺</span>
									</div>
								</div>
								<small class="form-text text-muted"><i class="ti-info-alt mr-1"></i> 0 girerseniz hedef tutar gösterilmez</small>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="toplanan_tutar">Toplanan Tutar (TL)</label>
								<div class="input-group">
									<input type="number" step="0.01" class="form-control form-control-sm bg-light" name="toplanan_tutar" id="toplanan_tutar" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['toplanan_tutar'] : '0.00');?>" readonly />
									<div class="input-group-append">
										<span class="input-group-text">₺</span>
									</div>
								</div>
								<small class="form-text text-muted"><i class="ti-reload mr-1"></i> Otomatik olarak hesaplanır (değiştirilemez)</small>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Tarih Bilgileri -->
			<div class="card mb-4">
				<div class="card-header bg-info text-white">
					<h5 class="mb-0"><i class="ti-calendar mr-2"></i> Tarih Bilgileri</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="baslangic_tarihi">Başlangıç Tarihi</label>
								<input type="date" class="form-control form-control-sm" name="baslangic_tarihi" id="baslangic_tarihi" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['baslangic_tarihi'] : '');?>" />
								<small class="form-text text-muted">Kampanyanın başlangıç tarihi</small>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="bitis_tarihi">Bitiş Tarihi</label>
								<input type="date" class="form-control form-control-sm" name="bitis_tarihi" id="bitis_tarihi" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['bitis_tarihi'] : '');?>" />
								<small class="form-text text-muted"><i class="ti-info-alt mr-1"></i> Boş bırakırsanız kampanya süresiz olur</small>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Görseller -->
			<div class="card mb-4">
				<div class="card-header bg-warning text-dark">
					<h5 class="mb-0"><i class="ti-image mr-2"></i> Görseller</h5>
				</div>
				<div class="card-body">
					<?php if(isset($_GET['islem'])=="duzenle"): ?>
					<?php if(!empty($Sonuc['kapak_resmi']) || !empty($Sonuc['banner_resmi'])): ?>
					<div class="row mb-4">
						<?php if(!empty($Sonuc['kapak_resmi'])): ?>
						<div class="col-md-6">
							<div class="form-group">
								<label>Mevcut Kapak Görseli</label>
								<div class="border rounded p-2 text-center">
									<img src="../<?php echo tema;?>/uploads/bagis_moduller/<?php echo $Sonuc['kapak_resmi'];?>" class="img-fluid rounded mb-2" style="max-height: 200px;">
									<br>
									<a class="btn btn-danger btn-sm popconfirm" title="Kapak Sil" href="../_class/yonetim_islem.php?bagismodulkapaksil=ok&sid=<?php echo $Sonuc['id'];?>">
										<i class="ti-trash mr-1"></i> Kapak Görselini Sil
									</a>
								</div>
							</div>
						</div>
						<?php endif; ?>
						<?php if(!empty($Sonuc['banner_resmi'])): ?>
						<div class="col-md-6">
							<div class="form-group">
								<label>Mevcut Banner Görseli</label>
								<div class="border rounded p-2 text-center">
									<img src="../<?php echo tema;?>/uploads/bagis_moduller/<?php echo $Sonuc['banner_resmi'];?>" class="img-fluid rounded mb-2" style="max-height: 200px;">
									<br>
									<a class="btn btn-danger btn-sm popconfirm" title="Banner Sil" href="../_class/yonetim_islem.php?bagismodulbannersil=ok&sid=<?php echo $Sonuc['id'];?>">
										<i class="ti-trash mr-1"></i> Banner Görselini Sil
									</a>
								</div>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<?php endif; ?>
					
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Kapak Görseli (Listeleme) <span class="badge badge-info">Önerilen: 400x300px</span></label>
								<input type="file" name="kapak_resmi" class="file-upload-default">
								<div class="input-group">
									<input type="text" class="form-control file-upload-info form-control-sm" disabled="" placeholder="Resim dosyası seçiniz">
									<span class="input-group-append">
										<button class="file-upload-browse btn btn-primary btn-sm" type="button">
											<i class="icon-cloud-upload font-12 mr-1"></i> Dosya Seç
										</button>
									</span>
								</div>
								<small class="form-text text-muted">Kampanya listeleme sayfasında gösterilecek görsel</small>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label>Banner Görseli (Detay Sayfa) <span class="badge badge-info">Önerilen: 1920x400px</span></label>
								<input type="file" name="banner_resmi" class="file-upload-default2">
								<div class="input-group">
									<input type="text" class="form-control file-upload-info2 form-control-sm" disabled="" placeholder="Resim dosyası seçiniz">
									<span class="input-group-append">
										<button class="file-upload-browse2 btn btn-primary btn-sm" type="button">
											<i class="icon-cloud-upload font-12 mr-1"></i> Dosya Seç
										</button>
									</span>
								</div>
								<small class="form-text text-muted">Kampanya detay sayfasının üst kısmında gösterilecek banner görseli</small>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- SEO Ayarları -->
			<div class="card mb-4">
				<div class="card-header bg-secondary text-white">
					<h5 class="mb-0"><i class="ti-search mr-2"></i> SEO Ayarları</h5>
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="keywords">SEO Anahtar Kelimeler</label>
						<input type="text" class="form-control form-control-sm" name="keywords" id="keywords" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['keywords']) : '');?>" placeholder="kelime1, kelime2, kelime3" />
						<small class="form-text text-muted">Virgülle ayrılmış anahtar kelimeler (örnek: bağış, kampanya, yardım)</small>
					</div>
					
					<div class="form-group mb-0">
						<label for="description">SEO Açıklama (Meta Description)</label>
						<textarea rows="3" class="form-control form-control-sm" name="description" id="description" maxlength="160" placeholder="Sayfa açıklaması (maksimum 160 karakter)"><?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['description']) : '');?></textarea>
						<small class="form-text text-muted">Arama motorlarında gösterilecek açıklama (160 karakter önerilir)</small>
					</div>
				</div>
			</div>
			
			<!-- Ayarlar -->
			<div class="card mb-4">
				<div class="card-header bg-dark text-white">
					<h5 class="mb-0"><i class="ti-settings mr-2"></i> Ayarlar</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group mb-3">
								<label class="d-block mb-2" for="durum">Durum</label>
								<?php if(isset($_GET['islem'])=="duzenle"){?>
								<label class="switch">
									<input type="checkbox" name="durum" id="durum" value="1" <?php if($Sonuc['durum'] == '1') {?> checked <?php } ?>>
									<span class="slider"></span>
								</label>
								<span class="ml-2"><?php echo($Sonuc['durum'] == '1' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Pasif</span>');?></span>
								<?php }else{?>
								<label class="switch">
									<input type="checkbox" name="durum" id="durum" value="1" checked>
									<span class="slider"></span>
								</label>
								<span class="ml-2"><span class="badge badge-success">Aktif</span></span>
								<?php } ?>
								<small class="form-text text-muted d-block mt-1">Kampanyanın aktif/pasif durumunu belirler</small>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group mb-3">
								<label class="d-block mb-2" for="anasayfada_goster">Anasayfada Göster</label>
								<?php if(isset($_GET['islem'])=="duzenle"){?>
								<label class="switch">
									<input type="checkbox" name="anasayfada_goster" id="anasayfada_goster" value="1" <?php if($Sonuc['anasayfada_goster'] == '1') {?> checked <?php } ?>>
									<span class="slider"></span>
								</label>
								<span class="ml-2"><?php echo($Sonuc['anasayfada_goster'] == '1' ? '<span class="badge badge-success">Gösteriliyor</span>' : '<span class="badge badge-secondary">Gizli</span>');?></span>
								<?php }else{?>
								<label class="switch">
									<input type="checkbox" name="anasayfada_goster" id="anasayfada_goster" value="1">
									<span class="slider"></span>
								</label>
								<span class="ml-2"><span class="badge badge-secondary">Gizli</span></span>
								<?php } ?>
								<small class="form-text text-muted d-block mt-1">Anasayfada bu kampanyanın gösterilip gösterilmeyeceğini belirler</small>
							</div>
							
							<div class="form-group mb-3">
								<label class="d-block mb-2" for="istatistik_durum">Kampanya İstatistikleri</label>
								<?php if(isset($_GET['islem'])=="duzenle"){?>
								<label class="switch">
									<input type="checkbox" name="istatistik_durum" id="istatistik_durum" value="1" <?php if(isset($Sonuc['istatistik_durum']) && $Sonuc['istatistik_durum'] == '1') {?> checked <?php } ?>>
									<span class="slider"></span>
								</label>
								<span class="ml-2"><?php echo(isset($Sonuc['istatistik_durum']) && $Sonuc['istatistik_durum'] == '1' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Pasif</span>');?></span>
								<?php }else{?>
								<label class="switch">
									<input type="checkbox" name="istatistik_durum" id="istatistik_durum" value="1" checked>
									<span class="slider"></span>
								</label>
								<span class="ml-2"><span class="badge badge-success">Aktif</span></span>
								<?php } ?>
								<small class="form-text text-muted d-block mt-1">Tekil bağış sayfasında kampanya istatistiklerinin (toplanan tutar, ilerleme çubuğu vb.) gösterilip gösterilmeyeceğini belirler</small>
							</div>
						</div>
					</div>
					
					<div class="form-group mb-0">
						<label class="d-block mb-2" for="tekil_sayfa">Tekil Sayfa (4 Aşamalı Form)</label>
						<label class="switch">
							<input type="checkbox" name="tekil_sayfa" id="tekil_sayfa" value="1" checked disabled>
							<span class="slider"></span>
						</label>
						<span class="ml-2"><span class="badge badge-primary">Zorunlu</span></span>
						<input type="hidden" name="tekil_sayfa" value="1">
						<small class="form-text text-muted d-block mt-1"><i class="ti-info-alt mr-1"></i> Bu seçenek bağış modülleri için zorunludur ve her zaman aktif edilmelidir</small>
					</div>
				</div>
			</div>
			
			<!-- Form Butonları -->
			<div class="card">
				<div class="card-body">
					<div class="d-flex justify-content-between align-items-center">
						<div>
							<?php if(isset($_GET['islem'])=="duzenle"){?>
							<button type="submit" name="bagismodul_guncelle" class="btn btn-success btn-lg">
								<i class="mdi mdi-reload btn-icon-prepend mr-2"></i>                                                    
								KAYDET VE GÜNCELLE
							</button>
							<?php }else{?>
							<button type="submit" name="bagismodul_ekle" class="btn btn-primary btn-lg">
								<i class="mdi mdi-file-check btn-icon-prepend mr-2"></i>
								KAYDET VE EKLE
							</button>
							<?php } ?>
						</div>
						<div>
							<a href="bagis-moduller.html" class="btn btn-light btn-lg">
								<i class="ti-close mr-2"></i> İptal
							</a>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Font Awesome Icon Picker Modal -->
<div class="modal fade" id="iconPickerModal" tabindex="-1" role="dialog" aria-labelledby="iconPickerModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="iconPickerModalLabel">
					<i class="ti-search mr-2"></i> Font Awesome İkon Seçici
				</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<input type="text" class="form-control" id="iconSearch" placeholder="İkon ara... (örn: heart, user, home, money)">
				</div>
				<div class="row" id="iconList" style="max-height: 400px; overflow-y: auto;">
					<?php
					// Popüler Font Awesome ikonları
					$popularIcons = [
						'fas fa-heart', 'fas fa-hand-holding-heart', 'fas fa-donate', 'fas fa-money-bill-wave',
						'fas fa-users', 'fas fa-user', 'fas fa-child', 'fas fa-baby',
						'fas fa-home', 'fas fa-building', 'fas fa-hospital', 'fas fa-school',
						'fas fa-book', 'fas fa-graduation-cap', 'fas fa-book-reader', 'fas fa-pencil-alt',
						'fas fa-utensils', 'fas fa-bread-slice', 'fas fa-seedling', 'fas fa-apple-alt',
						'fas fa-tshirt', 'fas fa-socks', 'fas fa-shoe-prints', 'fas fa-tshirt',
						'fas fa-medkit', 'fas fa-ambulance', 'fas fa-heartbeat', 'fas fa-stethoscope',
						'fas fa-paw', 'fas fa-dog', 'fas fa-cat', 'fas fa-dove',
						'fas fa-tree', 'fas fa-leaf', 'fas fa-water', 'fas fa-sun',
						'fas fa-star', 'fas fa-gift', 'fas fa-birthday-cake', 'fas fa-birthday-cake',
						'ti-heart', 'ti-gift', 'ti-user', 'ti-home', 'ti-book', 'ti-star', 'ti-money', 'ti-hand-stop'
					];
					foreach($popularIcons as $icon): ?>
					<div class="col-md-2 col-sm-3 col-4 mb-3 icon-item" data-icon="<?php echo htmlspecialchars($icon); ?>">
						<div class="text-center p-2 border rounded cursor-pointer icon-hover" style="transition: all 0.2s;">
							<i class="<?php echo htmlspecialchars($icon); ?> fa-2x mb-2 text-primary"></i>
							<div class="small text-muted" style="font-size: 10px; word-break: break-all;"><?php echo htmlspecialchars($icon); ?></div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					<i class="ti-close mr-1"></i> İptal
				</button>
				<button type="button" class="btn btn-primary" id="selectIconBtn">
					<i class="ti-check mr-1"></i> Seç
				</button>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	// İkinci file upload için
	$(".file-upload-browse2").on("click", function() {
		var file = $(".file-upload-default2");
		file.trigger("click");
	});
	$(".file-upload-default2").on("change", function() {
		$(".file-upload-info2").val($(this).val().replace(/C:\\fakepath\\/i, ''));
	});
	
	// Icon picker functionality
	var selectedIcon = '';
	
	// İkon arama
	$('#iconSearch').on('keyup', function() {
		var searchTerm = $(this).val().toLowerCase();
		$('.icon-item').each(function() {
			var iconClass = $(this).data('icon').toLowerCase();
			if(iconClass.indexOf(searchTerm) !== -1) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
	});
	
	// İkon seçimi
	$('.icon-item').on('click', function() {
		$('.icon-item').removeClass('selected');
		$(this).addClass('selected');
		selectedIcon = $(this).data('icon');
		$(this).find('.border').css('border', '2px solid #007bff');
		$(this).find('.icon-hover').css('background', '#e7f3ff');
	});
	
	// Seç butonu
	$('#selectIconBtn').on('click', function() {
		if(selectedIcon) {
			$('#icon_class').val(selectedIcon);
			$('#icon-preview i').attr('class', selectedIcon);
			$('#iconPickerModal').modal('hide');
			selectedIcon = '';
			$('.icon-item').removeClass('selected');
			$('.icon-item .border').css('border', '1px solid #dee2e6');
			$('.icon-item .icon-hover').css('background', 'white');
		} else {
			alert('Lütfen bir ikon seçin!');
		}
	});
	
	// İkon class değiştiğinde preview güncelle
	$('#icon_class').on('input', function() {
		var iconClass = $(this).val();
		if(iconClass) {
			$('#icon-preview i').attr('class', iconClass);
		}
	});
	
	// Modal kapandığında temizle
	$('#iconPickerModal').on('hidden.bs.modal', function() {
		$('#iconSearch').val('');
		$('.icon-item').show();
		selectedIcon = '';
		$('.icon-item').removeClass('selected');
		$('.icon-item .border').css('border', '1px solid #dee2e6');
		$('.icon-item .icon-hover').css('background', 'white');
	});
	
	// Icon hover effect
	$('.icon-item').hover(
		function() {
			if(!$(this).hasClass('selected')) {
				$(this).find('.icon-hover').css('background', '#f8f9fa');
			}
		},
		function() {
			if(!$(this).hasClass('selected')) {
				$(this).find('.icon-hover').css('background', 'white');
			}
		}
	);
});
</script>

<style>
.icon-item.selected .border {
	border: 2px solid #007bff !important;
	background: #e7f3ff !important;
}
.icon-item.selected .icon-hover {
	background: #e7f3ff !important;
}
.cursor-pointer {
	cursor: pointer;
}
.icon-hover:hover {
	background: #f8f9fa !important;
}
.card-header h5 {
	font-weight: 600;
}
</style>

<?php 
cVCLmHLxbS_mesaj("bagismodul_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("bagismodul_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("bagismodul_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("bagismodulkapaksil",1,"yes","Başarı ile silinmiştir.");
cVCLmHLxbS_mesaj("bagismodulkapaksil",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("bagismodulbannersil",1,"yes","Başarı ile silinmiştir.");
cVCLmHLxbS_mesaj("bagismodulbannersil",2,"no","Hata oluştu tekrar deneyiniz.!");
?>
