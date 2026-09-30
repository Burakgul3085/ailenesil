
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>İstatistik Yönetimi</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>">İstatistik</a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>">İstatistik Yönetimi</a></li>
			</ul>
		</div>
	</div>
</div>
<?php
$ayarlar = $db->query("SELECT * FROM impact_reach_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
?>

<!-- Sayfa Yönetimi (Accordion) -->
<div class="accordion mb-4" id="sayfaYonetimiAccordion">
	<div class="card border-info">
		<div class="card-header bg-info text-white" id="sayfaYonetimiHeading" style="cursor:pointer;" data-toggle="collapse" data-target="#sayfaYonetimiCollapse" aria-expanded="false" aria-controls="sayfaYonetimiCollapse">
			<i class="fas fa-cog"></i> Sayfa Yönetimi
			<span class="float-right"><i class="fa fa-chevron-down"></i></span>
		</div>
		<div id="sayfaYonetimiCollapse" class="collapse" aria-labelledby="sayfaYonetimiHeading" data-parent="#sayfaYonetimiAccordion">
			<div class="card-body">
				<form action="../_class/yonetim_islem.php" method="POST" enctype="multipart/form-data">
					<div class="row">
						<div class="col-lg-6">
							<div class="card mb-4 shadow-sm h-100">
								<div class="card-header bg-light">
									<strong><i class="icon-picture"></i> Banner Ayarları</strong>
								</div>
								<div class="card-body">
									<div class="form-group">
										<label>Banner Başlığı</label>
										<input type="text" class="form-control" name="banner_baslik" value="<?php echo isset($ayarlar['banner_baslik']) ? $ayarlar['banner_baslik'] : '';?>">
									</div>
									<div class="form-group">
										<label>Banner Açıklaması</label>
										<textarea class="form-control" name="banner_aciklama" rows="3"><?php echo isset($ayarlar['banner_aciklama']) ? $ayarlar['banner_aciklama'] : '';?></textarea>
									</div>
									<div class="form-group">
										<label>Banner Arkaplan Resmi</label>
										<input type="file" name="banner_resim" class="file-upload-default">
										<div class="input-group col-xs-12">
											<input type="text" class="form-control file-upload-info" disabled placeholder="Banner Arkaplanı Yükle">
											<span class="input-group-append">
												<button class="file-upload-browse btn btn-primary" type="button">Seç</button>
											</span>
										</div>
										<?php if(!empty($ayarlar['banner_resim'])){ ?>
											<div class="mt-2 d-flex align-items-center">
												<img src="../<?php echo tema;?>/uploads/impact_reach/<?php echo $ayarlar['banner_resim'];?>" width="150" class="img-thumbnail">
												<a href="../_class/yonetim_islem.php?impact_reach_banner_resim_sil=ok" class="btn btn-danger btn-sm ml-2 popconfirm" data-message="Bu banner arkaplanını silmek istediğinizden emin misiniz?">
													<i class="icon-trash"></i> Sil
												</a>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="card mb-4 shadow-sm h-100">
								<div class="card-header bg-light">
									<strong><i class="icon-layers"></i> İçerik Görselleri</strong>
								</div>
								<div class="card-body">
									<div class="form-group">
										<label>Sayfa Resmi</label>
										<input type="file" name="resim" class="file-upload-default">
										<div class="input-group col-xs-12">
											<input type="text" class="form-control file-upload-info" disabled placeholder="Resim Yükle">
											<span class="input-group-append">
												<button class="file-upload-browse btn btn-primary" type="button">Seç</button>
											</span>
										</div>
										<?php if($ayarlar['resim']){ ?>
											<div class="mt-2 d-flex align-items-center">
												<img src="../<?php echo tema;?>/uploads/impact_reach/<?php echo $ayarlar['resim'];?>" width="150" class="img-thumbnail">
												<a href="../_class/yonetim_islem.php?impact_reach_ayarlar_resim_sil=ok" class="btn btn-danger btn-sm ml-2 popconfirm" data-message="Bu resmi silmek istediğinizden emin misiniz?">
													<i class="icon-trash"></i> Sil
												</a>
											</div>
										<?php } ?>
									</div>
									<div class="form-group">
										<label>Resim Açıklaması</label>
										<input type="text" class="form-control"  id="myTextarea2"  name="resim_aciklama" value="<?php echo isset($ayarlar['resim_aciklama']) ? $ayarlar['resim_aciklama'] : '';?>">
									</div>
									<div class="form-group">
										<label>YouTube Video URL</label>
										<input type="url" class="form-control" name="video_url" placeholder="https://www.youtube.com/watch?v=..." value="<?php echo isset($ayarlar['video_url']) ? htmlspecialchars($ayarlar['video_url'], ENT_QUOTES) : '';?>">
										<small class="form-text text-muted">Örnek: https://www.youtube.com/watch?v=dQw4w9WgXcQ veya https://youtu.be/dQw4w9WgXcQ</small>
										<?php if(!empty($ayarlar['video_url'])){ ?>
											<div class="mt-2">
												<a href="<?php echo htmlspecialchars($ayarlar['video_url'], ENT_QUOTES);?>" target="_blank" class="btn btn-sm btn-info">
													<i class="fab fa-youtube"></i> Videoyu Görüntüle
												</a>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="card mb-4 shadow-sm">
						<div class="card-header bg-primary text-white">
							<strong><i class="icon-settings"></i> İçerik Ayarları</strong>
						</div>
						<br/>
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Ek Başlık</label>
										<input type="text" class="form-control" name="ek_baslik" value="<?php echo isset($ayarlar['ek_baslik']) ? $ayarlar['ek_baslik'] : '';?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Ek Açıklama</label>
										<textarea id="myTextarea" class="form-control" name="ek_aciklama" rows="3"><?php echo isset($ayarlar['ek_aciklama']) ? $ayarlar['ek_aciklama'] : '';?></textarea>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group mb-0">
										<div class="form-check form-check-success">
											<label class="form-check-label">
												<input type="checkbox" class="form-check-input" name="uzman_gorusleri_aktif" <?php echo (isset($ayarlar['uzman_gorusleri_aktif']) && $ayarlar['uzman_gorusleri_aktif'] == 1) ? 'checked' : '';?>>
												Uzman Görüşleri Bölümü Aktif Olsun
												<i class="input-helper"></i>
											</label>
										</div>
									</div>
								</div>
								<div class="col-md-6 text-md-right">
									<a href="uzman_gorus_listele.html" class="btn btn-outline-primary btn-sm mt-3 mt-md-0">
										<i class="icon-people"></i> Uzman Görüşleri Yönetimi
									</a>
								</div>
							</div>
						</div>
					</div>

					<div class="text-right">
						<button type="submit" name="impact_reach_ayarlar_kaydet" class="btn btn-success btn-lg">
							<i class="icon-check"></i> Ayarları Kaydet
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="card mb-4">
	<div class="card-header">
		<i class="icon-chart"></i> İstatistik Listesi
	</div>
	<form action="../_class/yonetim_islem.php" method="POST">
	<div class="card-body">
		<div class="row mb-3">
			<div class="col-lg-12">
				<div class="btn-toolbar" role="toolbar">					
					<a href="impact_reach_ekle.html" class="btn btn-primary btn-sm mr-1">
						<i class="icon-plus font-12"></i> Yeni İstatistik Ekle
					</a>
					<div class="dropdown mr-1">
						<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuSizeButton3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="icon-options-vertical font-12"></i> Seçilenlere Uygula
						</button>
						<div class="dropdown-menu p-0 min-width-full" aria-labelledby="dropdownMenuSizeButton3">
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="impact_reach_aktif"><i class="icon-check"></i> Seçilenleri Aktif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="impact_reach_pasif"><i class="icon-close"></i> Seçilenleri Pasif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="impact_reach_tumu"><i class="icon-trash"></i> Seçilenleri Sil</button>
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
								<th style="width:50px;">Sıra</th>
								<th style="width:80px;">İkon</th>
								<th>Başlık</th>
								<th style="width:100px;">Sayı</th>
								<th>Açıklama</th>
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

<?php if(isset($ayarlar['uzman_gorusleri_aktif']) && $ayarlar['uzman_gorusleri_aktif'] == 1){ ?>
<div style="display:none;" class="card">
	<div class="card-header bg-warning text-white">
		<i class="icon-people"></i> Uzman Görüşleri
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
<?php } ?>
<script>
	$(document).ready(function(){
		var dataTable=$('#order-listingg').DataTable({
			"processing": true,
			"serverSide":true,
			"ajax":{
				url:"data/impact_reach.php",
				type:"post"
			},
			"order": [[ 2, "asc" ]],
			"aLengthMenu": [
				[5, 10, 15, <?php echo cVCLmHLxbS_tumu("impact_reach",1);?>],
				[5, 10, 15, "Tümü"]
			],
			"columnDefs": [
				{ "orderable": false, "targets": [0, 8] },
				{ "targets": [ 1 ],"visible": false },
				{  "className": "secili", targets: [2, 4, 5] },
				{  "className": "secili text-center", targets: [6] },
				{  "className": "text-center", targets: [3, 7, 8] }
			],
			"iDisplayLength": 5,
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

		$('#order-listingg').on('click', 'tbody tr td.secili', function(event) {	
			$(this).closest("tr").find("td:eq(0)").find("input[type=checkbox]").trigger('click');
			if($(this).closest("tr").find("input[type=checkbox]").prop("checked")){
				$(this).closest("tr").addClass('highlight');
			}else{
				$(this).closest("tr").removeClass('highlight');
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

		$(".select-all").click(function () {
			$("#order-listingg input:checkbox").not(this).prop('checked', this.checked);

			if(this.checked){
				$("#order-listingg input:checkbox").not(this).closest("tr").addClass('highlight');
			}else{
				$("#order-listingg input:checkbox").not(this).closest("tr").removeClass('highlight');
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
