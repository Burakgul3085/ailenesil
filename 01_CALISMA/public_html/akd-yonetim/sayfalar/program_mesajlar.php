<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php // @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
 cemetery_f(); ?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Program Mesajları</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>">Program Mesajları</a></li>
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
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="program_mesaj_okundu"><i class="icon-check"></i> Okundu</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="program_mesaj_okunmadi"><i class="icon-close"></i> Okunmadı</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="program_mesaj_tumu"><i class="icon-trash"></i> Seçilenleri Sil</button>
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
								<th>Program</th>
								<th>Ad Soyad</th>
								<th>Okul/İşletme</th>
								<th>E-Posta</th>
								<th>Telefon</th>
								<th>Konum</th>
								<th>Gönderilme Tarihi</th>
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
<!-- content-wrapper ends -->


<script>
$(document).ready(function(){
	var dataTable=$('#order-listingg').DataTable({
		"processing": true,
		"serverSide":true,
		"ajax":{
			url:"data/program_mesajlar.php",
			type:"post"
		},
		"order": [
			[ 0, "desc" ]
		],
		"aLengthMenu": [
			[5, 10, 15, <?php echo cVCLmHLxbS_tumu("okullar_program_talep",2);?>],
			[5, 10, 15, "Tümü"]
		],
		"columnDefs": [
			{ "orderable": false, "targets": [0, 9] },
			{  "className": "secili", targets: [1, 2, 3, 4, 5, 6, 7, 8] },
			{  "className": "text-center", targets: [9] }
		],
		"iDisplayLength": 10,
		"language": {
			"url":"js/Turkish.json"
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
	
	
	
	$(document).on('click', 'a[data-role=update]',function(){
		
		var id =$(this).data('id');
		$.ajax({
			type: "POST",
			url: "../_class/yonetim_islem.php?programmesajokundu=ok",
			data: {id:id},
			dataType: "json",
			success: function(data){
				alert(data);
			}
		})
	})
});
</script>
<?php 
cVCLmHLxbS_mesaj("programmesajsil",1,"yes","Başarı ile silinmiştir.");
cVCLmHLxbS_mesaj("programmesajsil",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("program_mesaj_tumu",1,"yes","Seçilen kayıtlar başarıyla silinmiştir.");
cVCLmHLxbS_mesaj("program_mesaj_tumu",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("program_mesaj_okundu",1,"yes","Seçili mesajlar okundu olarak ayarlandı.");
cVCLmHLxbS_mesaj("program_mesaj_okundu",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("program_mesaj_okunmadi",1,"yes","Seçili mesajlar okunmadı olarak ayarlandı.");
cVCLmHLxbS_mesaj("program_mesaj_okunmadi",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("secim",3,"secimyok","Hiç bir şey seçmediniz. Lütfen işlem yapmak istediğiniz eylemi ve ID leri seçin.");
?>

