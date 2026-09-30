<?php echo !defined("GUVENLIK") ? die("Erişim Engellendi!.") : null;?>
<?php // @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
 cemetery_f(); ?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Gelen Randevular</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>">Gelen Randevular</a></li>
			</ul>
		</div>
	</div>
</div>

<div class="card">
	<form action="../_class/yonetim_islem.php" method="POST">
	<div class="card-body">
		<div class="row mb-3">
			<div class="col-lg-12">
				<div class="btn-toolbar" role="toolbar">					
					<div class="dropdown">
						<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuSizeButton3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="icon-options-vertical font-12"></i> Seçilenlere Uygula
						</button>
						<div class="dropdown-menu p-0 min-width-full" aria-labelledby="dropdownMenuSizeButton3">
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="randevu_onayla"><i class="icon-check"></i> Seçilenleri Onayla</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="randevu_iptal"><i class="icon-close"></i> Seçilenleri İptal Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="randevu_tumu"><i class="icon-trash"></i> Seçilenleri Sil</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Filtre seçenekleri -->
		<div class="row mb-3">
			<div class="col-lg-3">
				<div class="form-group mb-0">
					<label class="small">Hizmet Filtrele:</label>
					<select class="form-control form-control-sm" id="hizmet_filtre">
						<option value="">Tüm Hizmetler</option>
						<?php 
						$HizmetSorgu = $db->prepare("SELECT * FROM randevu_hizmetler WHERE dil = ? AND durum = 1 ORDER BY sira ASC");
						$HizmetSorgu->execute(array($_SESSION['admin_dil']));
						while($hizmet = $HizmetSorgu->fetch(PDO::FETCH_ASSOC)){ 
						?>
							<option value="<?php echo $hizmet['id'];?>"><?php echo $hizmet['baslik'];?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group mb-0"> 
					<label class="small">Durum:</label>
					<select class="form-control form-control-sm" id="durum_filtre">
						<option value="">Tümü</option>
						<option value="0">Beklemede</option>
						<option value="1">Onaylandı</option>
						<option value="2">İptal</option>
					</select>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group mb-0">
					<label class="small">Tarih Aralığı (Başlangıç):</label>
					<input type="date" class="form-control form-control-sm" id="tarih_baslangic">
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group mb-0">
					<label class="small">Tarih Aralığı (Bitiş):</label>
					<input type="date" class="form-control form-control-sm" id="tarih_bitis">
				</div>
			</div>
		</div>
		
		<div class="row mb-3">
			<div class="col-lg-12">
				<button type="button" class="btn btn-primary btn-sm" id="filtreleBtn">
					<i class="icon-magnifier"></i> Filtrele
				</button>
				<button type="button" class="btn btn-secondary btn-sm" id="temizleBtn">
					<i class="icon-refresh"></i> Temizle
				</button>
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
								<th>ID</th>
								<th>Hizmet</th>
								<th>İsim</th>
								<th>Telefon</th>
								<th>E-posta</th>
								<th>Randevu Tarihi</th>
								<th>Randevu Saati</th>
								<th style="width:70px;">Durum</th>
								<th style="width:115px;">İşlem</th>
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
			url:"data/randevular.php",
			type:"post",
			data: function(d) {
				d.hizmet_filtre = $('#hizmet_filtre').val();
				d.durum_filtre = $('#durum_filtre').val();
				d.tarih_baslangic = $('#tarih_baslangic').val();
				d.tarih_bitis = $('#tarih_bitis').val();
			}
		},
		"order": [
			[ 0, "desc" ]
		],
		"aLengthMenu": [
			[5, 10, 15, <?php echo cVCLmHLxbS_tumu("randevular",2);?>],
			[5, 10, 15, "Tümü"]
		],
		"columnDefs": [
			{ "orderable": false, "targets": [0, 9] },
			{  "className": "secili", targets: [1, 2, 3, 4, 5, 6, 7] },
			{  "className": "text-center", targets: [8, 9] }
		],
		"iDisplayLength": 10,
		"language": {
			"url":"js/Turkish.json"
		},
		"fnDrawCallback": function( oSettings ) {
		$(".popconfirm").popConfirm();
		}
	});
	
	// Filtreleme butonu
	$('#filtreleBtn').on('click', function() {
		dataTable.ajax.reload();
	});
	
	// Temizle butonu
	$('#temizleBtn').on('click', function() {
		$('#hizmet_filtre').val('');
		$('#durum_filtre').val('');
		$('#tarih_baslangic').val('');
		$('#tarih_bitis').val('');
		dataTable.ajax.reload();
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
</script>

<?php 
cVCLmHLxbS_mesaj("randevusil",1,"yes","Başarı ile silinmiştir.");
cVCLmHLxbS_mesaj("randevusil",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("randevu_tumu",1,"yes","Seçilen kayıtlar başarıyla silinmiştir.");
cVCLmHLxbS_mesaj("randevu_tumu",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("randevu_onayla",1,"yes","Randevular başarıyla onaylandı.");
cVCLmHLxbS_mesaj("randevu_onayla",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("randevu_iptal",1,"yes","Randevular başarıyla iptal edildi.");
cVCLmHLxbS_mesaj("randevu_iptal",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("secim",3,"secimyok","Hiç bir şey seçmediniz. Lütfen işlem yapmak istediğiniz eylemi ve ID leri seçin.");
?>

