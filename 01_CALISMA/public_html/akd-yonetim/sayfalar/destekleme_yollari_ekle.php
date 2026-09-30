<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
$Sonuc = array(); // Varsayılan boş array
if(isset($_GET['islem']) && $_GET['islem'] == "duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM destekleme_yollari WHERE id = ?");
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
		<h3>Destekleme Yolu <?php echo (isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? 'Düzenle' : 'Ekle';?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="destekleme_yollari_listele.html">Destekleme Yolları</a></li>
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
				<i class="fas fa-hand-holding-heart"></i>
				Destekleme Yolu <?php echo (isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? 'Düzenle' : 'Ekle';?>
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
							<input type="text" class="form-control form-control-sm" name="baslik" id="baslik" value="<?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['baslik'], ENT_QUOTES) : '');?>" required />
						</div>
						<div class="modern-form-group full-width">
							<label for="aciklama">Açıklama</label>
							<textarea name="aciklama" class="form-control form-control-sm" rows="3"><?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['aciklama'], ENT_QUOTES) : '');?></textarea>
						</div>
					</div>
				</div>

				<!-- İkon veya Resim Seçimi -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-icons"></i>
						İkon veya Resim Seçimi
					</div>
					<div class="modern-form-grid">
						<div class="modern-form-group">
							<label for="ikon">İkon (Font Awesome)</label>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm" name="ikon" id="ikon" placeholder="Örn: fas fa-heart" value="<?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['ikon'] ?? '', ENT_QUOTES) : '');?>">
								<div class="input-group-append">
									<button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#ikonModal">
										<i class="fas fa-icons"></i> İkon Seç
									</button>
								</div>
							</div>
							<small class="form-text text-muted">Font Awesome ikon sınıfını girin veya seçin</small>
							<?php if((isset($_GET['islem']) && $_GET['islem'] == "duzenle") && !empty($Sonuc['ikon'])): ?>
								<div class="mt-2">
									<i class="<?php echo htmlspecialchars($Sonuc['ikon'], ENT_QUOTES);?> fa-2x text-primary"></i>
								</div>
							<?php endif; ?>
						</div>
						<div class="modern-form-group">
							<label>Resim</label>
							<?php if((isset($_GET['islem']) && $_GET['islem'] == "duzenle") && !empty($Sonuc['resim'])){?>
								<div class="modern-image-preview">
									<div id="lightgallery" class="lightGallery">
										<a href="../<?php echo tema;?>/uploads/destekleme_yollari/<?php echo $Sonuc['resim'];?>">
											<img src="../<?php echo tema;?>/uploads/destekleme_yollari/<?php echo $Sonuc['resim'];?>" alt="Destekleme Yolu Resmi">
										</a>
									</div>
									<a class="btn btn-danger btn-sm mt-2 popconfirm" title="Resmi Sil" href="../_class/yonetim_islem.php?destekleme_yollari_resimsil=ok&sid=<?php echo $Sonuc['id'];?>">
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
								<small class="form-text text-muted">İkon kullanıyorsanız resim girmenize gerek yoktur</small>
							</div>
						</div>
						<div class="modern-form-group full-width">
							<label for="kisa_aciklama">Kısa Açıklama</label>
							<textarea name="kisa_aciklama" class="form-control form-control-sm" rows="3" placeholder="Card'da gösterilecek kısa açıklama"><?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['kisa_aciklama'] ?? '', ENT_QUOTES) : '');?></textarea>
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
						<textarea name="tam_aciklama" id="myTextarea"><?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['tam_aciklama'] ?? '', ENT_QUOTES) : '');?></textarea>
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
							<input type="text" class="form-control form-control-sm" name="seo" id="seo" value="<?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['seo'] ?? '', ENT_QUOTES) : '');?>" />
							<small class="form-text text-muted">Boş bırakılırsa başlıktan otomatik oluşturulacaktır</small>
						</div>
						<div class="modern-form-group">
							<label for="maxlength-textarea">Description</label>
							<textarea id="maxlength-textarea" name="description" class="form-control" maxlength="260" rows="4"><?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['description'] ?? '', ENT_QUOTES) : '');?></textarea>
						</div>
						<div class="modern-form-group">
							<label for="tags">Keywords <small>(Kelimenin sonuna virgül koyunuz)</small></label>
							<input name="keywords" id="tags" value="<?php echo((isset($_GET['islem']) && $_GET['islem'] == "duzenle") ? htmlspecialchars($Sonuc['keywords'] ?? '', ENT_QUOTES) : '');?>" />
						</div>
					</div>
				</div>
			</div>

			<div class="modern-form-actions">
				<a href="destekleme_yollari_listele.html" class="modern-btn modern-btn-light">
					<i class="fas fa-times"></i> İptal
				</a>
				<?php if((isset($_GET['islem']) && $_GET['islem'] == "duzenle")){?>
				<button type="submit" name="destekleme_yollari_guncelle" class="modern-btn modern-btn-success">
					<i class="fas fa-save"></i> Güncelle
				</button>
				<?php }else{?>
				<button type="submit" name="destekleme_yollari_ekle" class="modern-btn modern-btn-primary">
					<i class="fas fa-plus"></i> Kaydet
				</button>
				<?php } ?>
			</div>
		</form>
	</div>
</div>

<!-- İkon Seçim Modal -->
<div class="modal fade" id="ikonModal" tabindex="-1" role="dialog" aria-labelledby="ikonModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Font Awesome 5 İkon Seçimi</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="text" id="ikonAra" class="form-control mb-3" placeholder="İkon ara...">
				<div class="icon-list" style="max-height: 500px; overflow-y: auto;"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
				<button type="button" class="btn btn-primary" id="ikonSec">Seç</button>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	// İkon listesini yükle
	$('#ikonModal').on('show.bs.modal', function () {
		var icons = ['fa-heart', 'fa-heartbeat', 'fa-hand-holding-heart', 'fa-donate', 'fa-money-bill-wave', 
			'fa-credit-card', 'fa-piggy-bank', 'fa-wallet', 'fa-handshake', 'fa-users', 'fa-user-friends',
			'fa-child', 'fa-baby', 'fa-school', 'fa-graduation-cap', 'fa-book', 'fa-book-open', 'fa-lightbulb',
			'fa-star', 'fa-gift', 'fa-hands-helping', 'fa-umbrella', 'fa-home', 'fa-building', 'fa-flag',
			'fa-globe', 'fa-globe-americas', 'fa-shield-alt', 'fa-shield', 'fa-check-circle', 'fa-check',
			'fa-thumbs-up', 'fa-smile', 'fa-smile-beam', 'fa-clipboard-check', 'fa-clipboard-list',
			'fa-calendar-check', 'fa-chart-line', 'fa-chart-bar', 'fa-award', 'fa-medal', 'fa-trophy',
			'fa-leaf', 'fa-seedling', 'fa-tree', 'fa-water', 'fa-sun', 'fa-moon', 'fa-cloud', 'fa-rainbow'];
		
		var html = '';
		$.each(icons, function(index, icon) {
			html += '<div class="icon-item d-inline-block text-center p-2 m-1 border rounded" style="width: 100px; cursor: pointer;" data-icon="fas ' + icon + '">';
			html += '<i class="fas ' + icon + ' fa-2x mb-1"></i><br>';
			html += '<small class="text-muted">' + icon + '</small>';
			html += '</div>';
		});
		
		$('.icon-list').html(html);
		
		// İkon arama
		$('#ikonAra').on('keyup', function() {
			var value = $(this).val().toLowerCase();
			$('.icon-item').filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
			});
		});
		
		// İkon seçme
		$(document).off('click', '.icon-item').on('click', '.icon-item', function() {
			$('.icon-item').removeClass('bg-primary text-white');
			$(this).addClass('bg-primary text-white');
			$('#ikonSec').data('icon', $(this).data('icon'));
		});
	});
	
	// Seçilen ikonu forma ekle
	$('#ikonSec').on('click', function() {
		var selectedIcon = $(this).data('icon');
		if(selectedIcon) {
			$('#ikon').val(selectedIcon);
			$('#ikonModal').modal('hide');
		} else {
			alert('Lütfen bir ikon seçiniz.');
		}
	});
});
</script>

<?php 
cVCLmHLxbS_mesaj("destekleme_yollari_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("destekleme_yollari_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("destekleme_yollari_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("destekleme_yollari_resimsil",1,"yes","Başarı ile silinmiştir.");
cVCLmHLxbS_mesaj("destekleme_yollari_resimsil",2,"no","Hata oluştu tekrar deneyiniz.!");
?>

