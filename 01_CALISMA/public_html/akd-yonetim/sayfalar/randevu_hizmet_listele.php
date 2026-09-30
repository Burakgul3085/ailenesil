<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php // @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
 cemetery_f(); ?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Randevu Hizmetleri</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>">Randevu Hizmetleri</a></li>
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
					<a href="randevu-hizmet-ekle.html" class="btn btn-primary btn-sm mr-1">
						<i class="icon-plus font-12"></i> Randevu Hizmeti Ekle
					</a>
					<div class="dropdown mr-1">
						<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuSizeButton3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="icon-options-vertical font-12"></i> Seçilenlere Uygula
						</button>
						<div class="dropdown-menu p-0 min-width-full" aria-labelledby="dropdownMenuSizeButton3">
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="randevu_hizmet_aktif"><i class="icon-check"></i> Seçilenleri Aktif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="randevu_hizmet_pasif"><i class="icon-close"></i> Seçilenleri Pasif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="randevu_hizmet_tumu"><i class="icon-trash"></i> Seçilenleri Sil</button>
						</div>
					</div>
					<a href="../_class/yonetim_islem.php?randevuhizmettumunusil=ok" title="Tüm Veriyi Sil" class="btn btn-danger btn-sm mr-1 popconfirm">
						<i class="ti-trash font-12"></i> Tüm Veriyi Sil
					</a>
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
								<th>Başlık</th>
								<th>Fiyat</th>
								<th style="width:70px;">Durum</th>
								<th style="width:115px;">İşlem</th>
							</tr>
						</thead>
						<tbody id="sortable"></tbody>
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
				url:"data/randevu_hizmetler.php",
				type:"post"
			},
			"order": [[ 1, "asc" ]],
			"aLengthMenu": [
				[5, 10, 15, <?php echo cVCLmHLxbS_tumu("randevu_hizmetler",1);?>],
				[5, 10, 15, "Tümü"]
			],
			"columnDefs": [
				{ "orderable": false, "targets": [0, 5] },
				{ "targets": [ 1 ],"visible": false },
				{  "className": "secili", targets: [1, 2, 3] },
				{  "className": "secili text-center", targets: [4] },
				{  "className": "text-center", targets: [5] }
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
</script>
<style type="text/css">
.secili{
	cursor:move;
}
</style>
<script src="vendors/js/jquery-ui.min.js"></script>
<script type="text/javascript">
$(function(){
	$("#sortable").sortable({
		revert: true,
		handle: ".secili",
		stop: function(event, ui){
			var data = $(this).sortable('serialize');
			
			$.ajax({
				type: "POST",
				dataType: "json",
				data: data,
				url: "../_class/yonetim_islem.php?randevuhizmetsiralama=sira",
				success: function(msg){
					if(msg.islemMsj == "Güncellendi")
					{
						$.toast({
						  heading: 'Başarılı!',
						  text: msg.islemMsj,
						  showHideTransition: 'slide',
						  icon: 'success',
						  loaderBg: '#fff',
						  position: 'top-right'
						})
					}
					if(msg.islemMsj == "İşlem başarısız")
					{
						$.toast({
						  heading: 'Hata',
						  text: msg.islemMsj,
						  showHideTransition: 'slide',
						  icon: 'error',
						  loaderBg: '#fff',
						  position: 'top-right'
						})
					}
				}				
			});
		}
	});
	$("#sortable").disableSelection();
});
</script>
<?php 
cVCLmHLxbS_mesaj("randevu_hizmet_ekle",1,"yes","Başarı ile eklenmiştir.");
cVCLmHLxbS_mesaj("randevuhizmetsil",1,"yes","Başarı ile silinmiştir.");
cVCLmHLxbS_mesaj("randevuhizmetsil",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("randevu_hizmet_tumu",1,"yes","Seçilen kayıtlar başarıyla silinmiştir.");
cVCLmHLxbS_mesaj("randevu_hizmet_tumu",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("randevuhizmettumunusil",1,"yes","Tüm kayıtlar başarıyla silinmiştir.");
cVCLmHLxbS_mesaj("randevuhizmettumunusil",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("randevu_hizmet_aktif",1,"yes","Bilgileriniz başarıyla güncellendi.");
cVCLmHLxbS_mesaj("randevu_hizmet_aktif",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("randevu_hizmet_pasif",1,"yes","Bilgileriniz başarıyla güncellendi.");
cVCLmHLxbS_mesaj("randevu_hizmet_pasif",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("secim",3,"secimyok","Hiç bir şey seçmediniz. Lütfen işlem yapmak istediğiniz eylemi ve ID leri seçin.");
?>

 