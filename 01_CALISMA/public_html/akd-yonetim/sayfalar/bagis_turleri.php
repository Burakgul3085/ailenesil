<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>

<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Bağış Türleri</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>">Site Yönetimi</a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>">Bağış Türleri</a></li>
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
					<a href="bagis-turu-ekle.html" class="btn btn-primary btn-sm mr-1">
						<i class="icon-plus font-12"></i> Yeni Bağış Türü Ekle
					</a>
					<div class="dropdown mr-1">
						<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuSizeButton3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="icon-options-vertical font-12"></i> Seçilenlere Uygula
						</button>
						<div class="dropdown-menu p-0 min-width-full" aria-labelledby="dropdownMenuSizeButton3">
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="bagis_tur_aktif"><i class="icon-check"></i> Seçilenleri Aktif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="bagis_tur_pasif"><i class="icon-close"></i> Seçilenleri Pasif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="bagis_tur_tumu"><i class="icon-trash"></i> Seçilenleri Sil</button>
						</div>
					</div>
					<a href="../_class/yonetim_islem.php?bagisturtumunusil=ok" title="Tüm Veriyi Sil" class="btn btn-danger btn-sm mr-1 popconfirm">
						<i class="ti-trash font-12"></i> Tüm Veriyi Sil
					</a>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-12">
				<div class="table-responsive">
					<table id="order-listing-turler" class="table table-bordered table-hover">
						<thead class="headbg">
							<tr>
								<th class="noshort" style="width:20px;" data-toggle="tooltip" data-placement="top" title="Tümünü Seç">
									<input id="checkbox-turler" class="select-all checkbox-custom" type="checkbox" style="width:100px;">
									<label for="checkbox-turler" class="checkbox-custom-label mb-0"><span class="checktext"></span></label>
								</th>
								<th style="width:30px;">ID</th>
								<th>Bağış Türü Adı</th>
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
    $(document).ready(function () {

	    var dataTable = $('#order-listing-turler').DataTable({
	        "processing": true,
	        "serverSide": true,
	        "ajax": {
	            url: "data/bagis_turleri.php",
	            type: "post"
	        },
			"order": [[ 2, "asc" ]],
	        "aLengthMenu": [
				[5, 10, 15, <?php  echo cVCLmHLxbS_tumu("bagis_turleri", 2);?>],
				[5, 10, 15, "Tümü"]
	        ],
	        "columnDefs": [
				{ "orderable": false, "targets": [0, 4]},
				{ "targets": [ 1 ],"visible": false },
				{ "className": "secili", targets: [2] },
				{  "className": "secili text-center", targets: [3] },
				{ "className": "text-center", targets: [4] }
	        ],
	        "iDisplayLength": 10,
	        "language": {
	            "url": "js/Turkish.json"
	        },
			"fnCreatedRow": function( nRow, aData, iDataIndex ) {
				$(nRow).attr('id', 'item-'+aData[1]);
			},
	        "fnDrawCallback": function (oSettings) {
	            console.log(oSettings);

	            $(".popconfirm").popConfirm();
	        }
	    });



		$('#order-listing-turler').on('click', 'tbody tr td.secili', function(event) {	
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
cVCLmHLxbS_mesaj("bagis_turu_ekle",1,"yes","Başarı ile eklenmiştir.");
cVCLmHLxbS_mesaj("bagistursil",1,"yes","Başarı ile silinmiştir.");
cVCLmHLxbS_mesaj("bagistursil",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("bagis_tur_tumu",1,"yes","Seçilen kayıtlar başarıyla silinmiştir.");
cVCLmHLxbS_mesaj("bagis_tur_tumu",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("bagisturtumunusil",1,"yes","Tüm kayıtlar başarıyla silinmiştir.");
cVCLmHLxbS_mesaj("bagisturtumunusil",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("bagis_tur_aktif",1,"yes","Bilgileriniz başarıyla güncellendi.");
cVCLmHLxbS_mesaj("bagis_tur_aktif",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("bagis_tur_pasif",1,"yes","Bilgileriniz başarıyla güncellendi.");
cVCLmHLxbS_mesaj("bagis_tur_pasif",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("secim",3,"secimyok","Hiç bir şey seçmediniz. Lütfen işlem yapmak istediğiniz eylemi ve ID leri seçin.");
?>

