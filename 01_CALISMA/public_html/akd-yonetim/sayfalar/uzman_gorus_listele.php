<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Uzman Görüşleri</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>">Uzman Görüşleri</a></li>
			</ul>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-header bg-warning text-white">
		<i class="icon-people"></i> Uzman Görüşleri Listesi
	</div>
	<form action="../_class/yonetim_islem.php" method="POST">
	<div class="card-body">
		<div class="row mb-3">
			<div class="col-lg-12">
				<div class="btn-toolbar" role="toolbar">					
					<a href="uzman_gorus_ekle.html" class="btn btn-primary btn-sm mr-1">
						<i class="icon-plus font-12"></i> Yeni Görüş Ekle
					</a>
					<div class="dropdown mr-1">
						<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuSizeButton4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="icon-options-vertical font-12"></i> Seçilenlere Uygula
						</button>
						<div class="dropdown-menu p-0 min-width-full" aria-labelledby="dropdownMenuSizeButton4">
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="uzman_gorus_aktif"><i class="icon-check"></i> Seçilenleri Aktif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="uzman_gorus_pasif"><i class="icon-close"></i> Seçilenleri Pasif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="uzman_gorus_sil_toplu"><i class="icon-trash"></i> Seçilenleri Sil</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-12">
				<div class="table-responsive">
					<table id="uzman-listing" class="table table-bordered table-hover">
						<thead class="headbg">
							<tr>
								<th class="noshort" style="width:20px;">
									<input id="checkbox-uzman" class="select-all-uzman checkbox-custom" type="checkbox">
								</th>
								<th>Resim</th>
								<th>İsim</th>
								<th>Görev</th>
								<th>Yorum</th>
								<th>Durum</th>
								<th>İşlem</th>
							</tr>
						</thead>
						<tbody id="uzman-sortable"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	</form>
</div>

<script>
	$(document).ready(function(){
		var uzmanTable=$('#uzman-listing').DataTable({
			"processing": true,
			"serverSide":true,
			"ajax":{
				url:"data/uzman_gorusleri.php",
				type:"post"
			},
			"order": [[ 2, "asc" ]],
			"aLengthMenu": [
				[5, 10, 15, <?php echo cVCLmHLxbS_tumu("uzman_gorusleri",1);?>],
				[5, 10, 15, "Tümü"]
			],
			"columnDefs": [
				{ "orderable": false, "targets": [0, 6] },
				{  "className": "secili", targets: [1, 2, 3, 4] },
				{  "className": "secili text-center", targets: [5] },
				{  "className": "text-center", targets: [6] }
			],
			"iDisplayLength": 5,
			"language": {
				"url":"js/Turkish.json"
			},
			"fnDrawCallback": function( oSettings ) {
			$(".popconfirm").popConfirm();
		    }
		});
		
		$('#uzman-listing').on('click', 'tbody tr td.secili', function(event) {	
			$(this).closest("tr").find("td:eq(0)").find("input[type=checkbox]").trigger('click');
			if($(this).closest("tr").find("input[type=checkbox]").prop("checked")){
				$(this).closest("tr").addClass('highlight');
			}else{
				$(this).closest("tr").removeClass('highlight');
			}
		});

		$(".select-all-uzman").click(function () {
			$("#uzman-listing input:checkbox").not(this).prop('checked', this.checked);

			if(this.checked){
				$("#uzman-listing input:checkbox").not(this).closest("tr").addClass('highlight');
			}else{
				$("#uzman-listing input:checkbox").not(this).closest("tr").removeClass('highlight');
			}			
		});
	});
</script>
