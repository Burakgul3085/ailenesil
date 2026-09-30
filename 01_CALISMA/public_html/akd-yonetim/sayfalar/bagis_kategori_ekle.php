<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM bagis_kategori WHERE id = ?");
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
		<h3><?=@$admindil['txt130'];?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt57'];?></a></li>
				<li><a href="bagis-kategoriler.html"><?=@$admindil['txt131'];?></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt130'];?></a></li>
			</ul>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
				<input id="id" name="id" type="hidden" value="<?php echo $Sonuc['id']; ?>">

					<div class="form-group">
						<label for="sira">Sıra</label>
						<input type="number" min="0" class="form-control form-control-sm" name="sira" id="sira" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['sira'] : '');?>" />
					</div>
					<div class="form-group">
						<label for="adi">Başlık</label>
						<input type="text" class="form-control form-control-sm" name="adi" id="adi" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['adi'] : '');?>" />
					</div>				
					<div class="form-group">
						<label for="aciklama">Açıklama</label>
						<textarea class="form-control form-control-sm" name="aciklama" id="aciklama"><?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['aciklama'] : '');?>
						</textarea>
					</div>
					<div class="form-group">
						<label for="icon_class">Icon Class (Font Awesome) <small class="text-muted">Örn: fas fa-heart, ti-heart</small></label>
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
					<?php if(isset($_GET['islem'])=="duzenle"){?>
					<div class="row">
						<?php if($Sonuc['ikon'] == true){?>
						<div class="form-group col-md-2">
							<img src="../<?php echo tema;?>/uploads/bagis_kategoriler/<?php echo $Sonuc['ikon'];?>" class="img-responsive img-thumbnail" style="margin-bottom:2px;width:100%;min-height:150px;">
							<a style="width: 100%;" class="btn btn-danger btn-sm popconfirm" title="Görseli Sil" href="../_class/yonetim_islem.php?bagis_kategoriresimsil=ok&sid=<?php echo $Sonuc['id'];?>"><i class="fal fa-trash"></i> Görseli Sil</a>
						</div>
						<?php }?>
					</div>
					<?php }?>
					<div class="form-group row col-md-6">
					<label>Listeleme Görseli</label>
						<input type="file" name="ikon" class="file-upload-default">
						<div class="input-group col-xs-12">
							<input type="text" class="form-control file-upload-info form-control-sm" disabled="" placeholder="Resim dosyası seçiniz">
							<span class="input-group-append">
								<button class="file-upload-browse btn btn-primary btn-sm" type="button"><i class="icon-cloud-upload font-12"></i> Dosya Seç</button>
							</span>
						</div>
					</div>	
					<div class="card mb-4">
						<div class="card-header">
							SEO AYARLARI
						</div>
						<div class="card-body">
							<div class="form-group">
								<label for="maxlength-textarea">Sayfa Açıklama (description)</label>
								<textarea id="maxlength-textarea" name="description"  class="form-control" maxlength="260" rows="4"><?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['description'] : '');?></textarea>
							</div>
							<div class="form-group mb-0">
								<label for="tags">Sayfa Meta <small>(Kelimenin sonuna virgül koyunuz)</small></label>
								<input name="keywords" id="tags" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['keywords'] : '');?>" />
							</div>							
						</div>
					</div>
					<div class="form-group d-inline-block mr-2">
						<label class="d-block" for="durum">Durum</label>
						<?php if(isset($_GET['islem'])=="duzenle"){?>
						<label class="switch">
							<input type="checkbox" name="durum" id="durum" value="1" <?php if($Sonuc['durum'] == '1') {?> checked <?php } ?>>
							<span class="slider"></span>
						</label>
						<?php }else{?>
						<label class="switch">
							<input type="checkbox" name="durum" id="durum" value="1" checked>
							<span class="slider"></span>
						</label>
						<?php } ?>								
					</div>
					<div class="form-group">
					<?php if(isset($_GET['islem'])=="duzenle"){?>
					<button type="submit" name="bagis_kategori_guncelle" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-reload  btn-icon-prepend"></i>                                                    
						GÜNCELLE
					</button>
					<?php }else{?>
					<button type="submit" name="bagis_kategori_ekle" class="btn btn-primary btn-icon-text btn-sm">
						<i class="mdi mdi-file-check btn-icon-prepend"></i>
						KAYDET
					</button>
					<?php } ?>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>
<?php 
cVCLmHLxbS_mesaj("bagis_kategori_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("bagis_kategori_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("bagis_kategori_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("bagis_kategoriresimsil",1,"yes","Başarı ile siinmiştir.");
cVCLmHLxbS_mesaj("bagis_kategoriresimsil",2,"no","Hata oluştu tekrar deneyiniz.!");
?>

<!-- Font Awesome Icon Picker Modal -->
<div class="modal fade" id="iconPickerModal" tabindex="-1" role="dialog" aria-labelledby="iconPickerModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="iconPickerModalLabel">Font Awesome İkon Seçici</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<input type="text" class="form-control" id="iconSearch" placeholder="İkon ara... (örn: heart, user, home)">
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
						'ti-heart', 'ti-gift', 'ti-user', 'ti-home', 'ti-book', 'ti-star'
					];
					foreach($popularIcons as $icon): ?>
					<div class="col-md-2 col-sm-3 col-4 mb-3 icon-item" data-icon="<?php echo htmlspecialchars($icon); ?>">
						<div class="text-center p-2 border rounded cursor-pointer" style="cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='white'">
							<i class="<?php echo htmlspecialchars($icon); ?> fa-2x mb-2"></i>
							<div class="small text-muted" style="font-size: 10px; word-break: break-all;"><?php echo htmlspecialchars($icon); ?></div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
				<button type="button" class="btn btn-primary" id="selectIconBtn">Seç</button>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
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
	});
});
</script>
<style>
.icon-item.selected .border {
	border: 2px solid #007bff !important;
	background: #e7f3ff !important;
}
.cursor-pointer {
	cursor: pointer;
}
</style>
