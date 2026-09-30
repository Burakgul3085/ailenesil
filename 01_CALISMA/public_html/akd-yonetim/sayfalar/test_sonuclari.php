<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Test Sonuclari</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li class="active"><a href="test-sonuclari.html">Test Sonuclari</a></li>
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
					<div class="dropdown mr-1">
						<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropTestSonuc" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="icon-options-vertical font-12"></i> Secilenlere Uygula
						</button>
						<div class="dropdown-menu p-0 min-width-full" aria-labelledby="dropTestSonuc">
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="test_sonuc_sil_secili"><i class="icon-trash"></i> Secilenleri Sil</button>
						</div>
					</div>
					<a href="../_class/yonetim_islem.php?test_sonuc_tumunu_sil=ok" title="Tum Veriyi Sil" class="btn btn-danger btn-sm mr-1 popconfirm">
						<i class="ti-trash font-12"></i> Tum Veriyi Sil
					</a>
					<button type="button" class="btn btn-success btn-sm mr-1" onclick="exportCSV()">
						<i class="ti-download font-12"></i> CSV Indir
					</button>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12">
				<div class="table-responsive">
					<table id="test-sonuclari-tablo" class="table table-bordered table-hover">
						<thead class="headbg">
							<tr>
								<th class="noshort" style="width:20px;" data-toggle="tooltip" data-placement="top" title="Tumunu Sec">
									<input id="checkbox-ts" class="select-all checkbox-custom" type="checkbox" style="width:100px;">
									<label for="checkbox-ts" class="checkbox-custom-label mb-0"><span class="checktext"></span></label>
								</th>
								<th style="width:30px;">ID</th>
								<th>Veli</th>
								<th>Ogrenci</th>
								<th style="width:40px;">Yas</th>
								<th>Telefon</th>
								<th>E-posta</th>
								<th>Sonuc</th>
								<th style="width:130px;">Tarih</th>
								<th style="width:80px;">Islem</th>
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

<!-- Detay Modal -->
<div class="modal fade" id="testDetayModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Test Sonuc Detayi</h5>
				<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
			</div>
			<div class="modal-body" id="testDetayBody">
				<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> Yukleniyor...</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	var dataTable = $('#test-sonuclari-tablo').DataTable({
		"processing": true,
		"serverSide": true,
		"ajax": {
			url: "data/test_sonuclari.php",
			type: "post"
		},
		"start": 1,
		"order": [[ 8, "desc" ]],
		"aLengthMenu": [
			[10, 25, 50, 100],
			[10, 25, 50, 100]
		],
		"columnDefs": [
			{ "orderable": false, "targets": [0, 9] },
			{ "targets": [1], "visible": false },
			{ "className": "secili", "targets": [2, 3, 4, 5, 6, 7, 8] },
			{ "className": "text-center", "targets": [4, 9] }
		],
		"iDisplayLength": 25,
		"language": {
			"url": "js/Turkish.json"
		},
		"fnCreatedRow": function(nRow, aData, iDataIndex) {
			$(nRow).attr('id', 'item-' + aData[1]);
		},
		"fnDrawCallback": function(oSettings) {
			$(".popconfirm").popConfirm();
		}
	});

	$('#test-sonuclari-tablo').on('click', 'tbody tr td.secili', function(event) {
		$(this).closest("tr").find("td:eq(0)").find("input[type=checkbox]").trigger('click');
		if($(this).closest("tr").find("input[type=checkbox]").prop("checked")){
			$(this).closest("tr").addClass('highlight');
		} else {
			$(this).closest("tr").removeClass('highlight');
		}
	});

	$(".select-all").click(function() {
		$("input:checkbox").not(this).prop('checked', this.checked);
		if(this.checked){
			$("input:checkbox").not(this).closest("tr").addClass('highlight');
		} else {
			$("input:checkbox").not(this).closest("tr").removeClass('highlight');
		}
	});
});

function showDetail(id) {
	$('#testDetayBody').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> Yukleniyor...</div>');
	$('#testDetayModal').modal('show');

	$.ajax({
		url: 'data/test_sonuc_detay.php',
		type: 'POST',
		data: { id: id },
		success: function(res) {
			$('#testDetayBody').html(res);
		},
		error: function() {
			$('#testDetayBody').html('<div class="alert alert-danger">Yukleme hatasi.</div>');
		}
	});
}

function exportCSV() {
	window.location.href = 'data/test_sonuclari_csv.php';
}
</script>
<style>
.secili { cursor: pointer; }
</style>
<?php
cVCLmHLxbS_mesaj("test_sonuc_sil",1,"yes","Basari ile silinmistir.");
cVCLmHLxbS_mesaj("test_sonuc_sil",2,"no","Hata olustu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("test_sonuc_sil_secili",1,"yes","Secilen kayitlar basariyla silinmistir.");
cVCLmHLxbS_mesaj("test_sonuc_sil_secili",2,"no","Hata olustu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("test_sonuc_tumunu_sil",1,"yes","Tum kayitlar basariyla silinmistir.");
cVCLmHLxbS_mesaj("test_sonuc_tumunu_sil",2,"no","Hata olustu tekrar deneyiniz.!");
?>
