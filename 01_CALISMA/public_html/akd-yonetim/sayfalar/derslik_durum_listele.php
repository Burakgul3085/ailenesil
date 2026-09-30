<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>

<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Derslik Durumları</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>">Programlar</a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>">Derslik Durumları</a></li>
			</ul>
		</div>
	</div>
</div>

<!-- Kategori Yönetimi (Accordion) -->
<div class="accordion mb-4" id="kategoriYonetimiAccordion">
	<div class="card border-info">
		<div class="card-header bg-info text-white" id="kategoriYonetimiHeading" style="cursor:pointer;" data-toggle="collapse" data-target="#kategoriYonetimiCollapse" aria-expanded="false" aria-controls="kategoriYonetimiCollapse">
			<i class="fas fa-tags"></i> Kategori Yönetimi
			<span class="float-right"><i class="fa fa-chevron-down"></i></span>
		</div>
		<br/>
		<div id="kategoriYonetimiCollapse" class="collapse" aria-labelledby="kategoriYonetimiHeading" data-parent="#kategoriYonetimiAccordion">
			<div class="card-body">
				<div class="row mb-3">
					<div class="col-md-12">
						<h5 class="mb-3"><i class="ti-tag mr-2"></i> Kategori Ekle</h5>
					</div>
				</div>
				<form method="POST" action="../_class/yonetim_islem.php">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label>Sıra</label>
								<input type="number" class="form-control form-control-sm" name="sira" value="0" min="0">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Kategori Adı <span class="text-danger">*</span></label>
								<input type="text" class="form-control form-control-sm" name="adi" placeholder="Örn: İlkokul 1A" required>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Durum</label>
								<select class="form-control form-control-sm" name="durum">
									<option value="1">Aktif</option>
									<option value="0">Pasif</option>
								</select>
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group">
								<label>&nbsp;</label>
								<button type="submit" name="derslik_kategori_ekle" class="btn btn-primary btn-sm btn-block">
									<i class="icon-plus"></i> Ekle
								</button>
							</div>
						</div>
					</div>
				</form>
				
				<hr class="my-4">
				
				<div class="row">
					<div class="col-md-12">
						<h5 class="mb-3"><i class="ti-list mr-2"></i> Mevcut Kategoriler</h5>
						<div class="table-responsive">
							<table class="table table-bordered table-sm">
								<thead>
									<tr>
										<th style="width:60px;">Sıra</th>
										<th>Kategori Adı</th>
										<th style="width:80px;">Durum</th>
										<th style="width:120px;">İşlemler</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$kategoriler = $db->query("SELECT * FROM derslik_kategorileri WHERE dil = '{$_SESSION['admin_dil']}' ORDER BY sira ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
									foreach($kategoriler as $kat): 
									?>
									<tr>
										<td><?php echo $kat['sira']; ?></td>
										<td><?php echo htmlspecialchars($kat['adi']); ?></td>
										<td class="text-center">
											<?php if($kat['durum'] == 1): ?>
												<span class="badge badge-success">Aktif</span>
											<?php else: ?>
												<span class="badge badge-danger">Pasif</span>
											<?php endif; ?>
										</td>
										<td class="text-center">
											<button type="button" class="btn btn-warning btn-sm" onclick="editKategori(<?php echo $kat['id']; ?>, '<?php echo htmlspecialchars($kat['adi'], ENT_QUOTES); ?>', <?php echo $kat['sira']; ?>, <?php echo $kat['durum']; ?>)">
												<i class="icon-pencil"></i>
											</button>
											<a href="../_class/yonetim_islem.php?tablo=derslik_kategorileri&islem=sil&id=<?php echo $kat['id']; ?>" class="btn btn-danger btn-sm popconfirm" data-message="Bu kategoriyi silmek istediğinizden emin misiniz?">
												<i class="icon-trash"></i>
											</a>
										</td>
									</tr>
									<?php endforeach; ?>
									<?php if(empty($kategoriler)): ?>
									<tr>
										<td colspan="4" class="text-center text-muted">Henüz kategori eklenmemiş.</td>
									</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
				<!-- Kategori Düzenleme Modal -->
				<div class="modal fade" id="kategoriEditModal" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Kategori Düzenle</h5>
								<button type="button" class="close" data-dismiss="modal">
									<span>&times;</span>
								</button>
							</div>
							<form method="POST" action="../_class/yonetim_islem.php">
								<div class="modal-body">
									<input type="hidden" name="id" id="edit_kategori_id">
									<div class="form-group">
										<label>Sıra</label>
										<input type="number" class="form-control" name="sira" id="edit_kategori_sira" min="0">
									</div>
									<div class="form-group">
										<label>Kategori Adı <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="adi" id="edit_kategori_adi" required>
									</div>
									<div class="form-group">
										<label>Durum</label>
										<select class="form-control" name="durum" id="edit_kategori_durum">
											<option value="1">Aktif</option>
											<option value="0">Pasif</option>
										</select>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
									<button type="submit" name="derslik_kategori_guncelle" class="btn btn-primary">Güncelle</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="card">
	<form action="../_class/yonetim_islem.php" method="POST">
	<div class="card-body">
		<div class="row mb-3">
			<div class="col-lg-12">
				<div class="btn-toolbar" role="toolbar">					
					<a href="derslik_durum_ekle.html" class="btn btn-primary btn-sm mr-1">
						<i class="icon-plus font-12"></i> Yeni Derslik Ekle
					</a>
					<div class="dropdown mr-1">
						<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuSizeButton3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="icon-options-vertical font-12"></i> Seçilenlere Uygula
						</button>
						<div class="dropdown-menu p-0 min-width-full" aria-labelledby="dropdownMenuSizeButton3">
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="derslik_durum_aktif"><i class="icon-check"></i> Seçilenleri Aktif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="derslik_durum_pasif"><i class="icon-close"></i> Seçilenleri Pasif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="derslik_durum_tumu"><i class="icon-trash"></i> Seçilenleri Sil</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-12">
				<div class="table-responsive">
					<table id="order-listingg" class="table table-bordered table-hover">
						<thead class="headbg">
							<tr>
								<th class="noshort" style="width:20px;" data-toggle="tooltip" data-placement="top" title="Tümünü Seç">
									<input id="checkbox-4" class="select-all checkbox-custom" type="checkbox" style="width:100px;">
									<label for="checkbox-4" class="checkbox-custom-label mb-0"><span class="checktext"></span></label>
								</th>
								<th style="width:30px;">ID</th>
								<th style="width:60px;">Sıra</th>
								<th>Şehir</th>
								<th>Sınıf</th>
								<th>Gün</th>
								<th>Saat</th>
								<th>Durum</th>
								<th style="width:70px;">Aktif/Pasif</th>
								<th style="width:115px;">İşlemler</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	</form>
</div>
<script>
$(document).ready(function(){
	var dataTable=$('#order-listingg').DataTable({
		"processing": true,
		"serverSide":true,
		"ajax":{
			url:"data/derslik_durumlari.php",
			type:"post"
		},
		"order": [[ 2, "asc" ]],
		"aLengthMenu": [
			[5, 10, 15, <?php echo cVCLmHLxbS_tumu("derslik_durumlari",1);?>],
			[5, 10, 15, "Tümü"]
		],
		"columnDefs": [
			{ "orderable": false, "targets": [0, 9] },
			{ "targets": [ 1 ],"visible": false },
			{  "className": "secili", targets: [3, 4, 5, 6] },
			{  "className": "secili text-center", targets: [7, 8] },
			{  "className": "text-center", targets: [2, 9] }
		],
		"iDisplayLength": 10,
		"language": {
			"url":"js/Turkish.json"
		},
		"fnCreatedRow": function( nRow, aData, iDataIndex ) {
			$(nRow).attr('id', 'item-'+aData[1]);
		},
		"fnDrawCallback": function( oSettings ) {
			$(".popconfirm").popConfirm();
		}
	});
	$('#order-listingg').on('click', 'tbody tr td.secili', function(event) {	
		$(this).closest("tr").find("td:eq(0)").find("input[type=checkbox]").trigger('click');
		if($(this).closest("tr").find("input[type=checkbox]").prop("checked")){
			$(this).closest("tr").addClass('highlight');
		}else{
			$(this).closest("tr").removeClass('highlight');
		}
	});
	$(".select-all").click(function () {
		$("input:checkbox").not(this).prop('checked', this.checked);

		if(this.checked){
			$("input:checkbox").not(this).closest("tr").addClass('highlight');
		}else{
			$("input:checkbox").not(this).closest("tr").removeClass('highlight');
		}			
	});
});

function editKategori(id, adi, sira, durum) {
	document.getElementById('edit_kategori_id').value = id;
	document.getElementById('edit_kategori_adi').value = adi;
	document.getElementById('edit_kategori_sira').value = sira;
	document.getElementById('edit_kategori_durum').value = durum;
	$('#kategoriEditModal').modal('show');
}
</script>
<?php 
cVCLmHLxbS_mesaj("derslik_durum_ekle",1,"yes","Başarı ile eklenmiştir.");
cVCLmHLxbS_mesaj("derslikdurumsil",1,"yes","Başarı ile silinmiştir.");
cVCLmHLxbS_mesaj("derslikdurumsil",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("derslik_durum_tumu",1,"yes","Seçilen kayıtlar başarıyla silinmiştir.");
cVCLmHLxbS_mesaj("derslik_durum_tumu",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("derslik_durum_aktif",1,"yes","Bilgileriniz başarıyla güncellendi.");
cVCLmHLxbS_mesaj("derslik_durum_aktif",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("derslik_durum_pasif",1,"yes","Bilgileriniz başarıyla güncellendi.");
cVCLmHLxbS_mesaj("derslik_durum_pasif",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("derslik_kategori_ekle",1,"yes","Kategori başarıyla eklendi.");
cVCLmHLxbS_mesaj("derslik_kategori_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("derslik_kategori_guncelle",1,"yes","Kategori başarıyla güncellendi.");
cVCLmHLxbS_mesaj("derslik_kategori_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("derslik_kategori_sil",1,"yes","Kategori başarıyla silindi.");
cVCLmHLxbS_mesaj("derslik_kategori_sil",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("secim",3,"secimyok","Hiç bir şey seçmediniz. Lütfen işlem yapmak istediğiniz eylemi ve ID leri seçin.");
?>

