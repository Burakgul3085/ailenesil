<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>

<?php
// Sayfa ayarlarını çek
$sayfaAyarlari = $db->query("SELECT * FROM ogrenme_deneyimi_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
if(!$sayfaAyarlari) {
	$sayfaAyarlari = array(
		'banner_baslik' => '',
		'banner_resim' => '',
		'sayfa_baslik_1' => '',
		'sayfa_aciklama_1' => '',
		'sayfa_baslik_2' => '',
		'sayfa_aciklama_2' => ''
	);
}
?>

<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Öğrenme Deneyimleri</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>">Öğrenme Deneyimleri</a></li>
			</ul>
		</div>
	</div>
</div>

<!-- Sayfa Yönetimi (Sabit Form - Üstte) -->
<div class="accordion mb-4" id="sayfaYonetimiAccordion">
    <div class="card border-info">
        <div class="card-header bg-info text-white" id="sayfaYonetimiHeading" style="cursor:pointer;" data-toggle="collapse" data-target="#sayfaYonetimiCollapse" aria-expanded="false" aria-controls="sayfaYonetimiCollapse">
            <i class="fas fa-cog"></i> Sayfa Yönetimi
            <span class="float-right"><i class="fa fa-chevron-down"></i></span>
        </div>
        <br/>
        <div id="sayfaYonetimiCollapse" class="collapse" aria-labelledby="sayfaYonetimiHeading" data-parent="#sayfaYonetimiAccordion">
            <div class="card-body">
                <form id="sayfaYonetimiForm" method="POST" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
                    <input type="hidden" name="islem" value="ogrenme_deneyimi_sayfa_yonetimi">
                    <input type="hidden" name="ogrenme_deneyimi_id" value="1">
                    
                    <!-- Banner Bölümü -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="mb-3"><i class="ti-image mr-2"></i> Banner Ayarları</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="banner_baslik">Banner Başlık</label>
                                <input type="text" class="form-control form-control-sm" name="banner_baslik" id="banner_baslik" placeholder="Banner başlık" value="<?php echo htmlspecialchars($sayfaAyarlari['banner_baslik'] ?? '', ENT_QUOTES);?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="banner_resim">Banner Resmi</label>
                                <input type="file" class="form-control-file form-control-sm" name="banner_resim" id="banner_resim" accept="image/*">
                                <?php if(!empty($sayfaAyarlari['banner_resim'])): ?>
                                    <div class="mt-2">
                                        <img src="../<?php echo tema;?>/uploads/ogrenme_deneyimi/<?php echo htmlspecialchars($sayfaAyarlari['banner_resim'], ENT_QUOTES);?>" alt="Banner Resmi" style="max-width: 200px; max-height: 100px; object-fit: cover;" class="img-thumbnail">
                                        <br>
                                        <a href="../_class/yonetim_islem.php?ogrenme_deneyimi_banner_resim_sil=1" class="btn btn-danger btn-sm mt-2 popconfirm" data-message="Banner resmini silmek istediğinize emin misiniz?">
                                            <i class="ti-trash"></i> Banner Resmini Sil
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sayfa_baslik_1">1. Başlık</label>
                                <input type="text" class="form-control form-control-sm" name="sayfa_baslik_1" id="sayfa_baslik_1" placeholder="Birinci başlık" value="<?php echo htmlspecialchars($sayfaAyarlari['sayfa_baslik_1'] ?? '', ENT_QUOTES);?>">
                            </div>
                            <div class="form-group">
                                <label for="sayfa_aciklama_1">1. Açıklama</label>
                                <textarea class="form-control form-control-sm" name="sayfa_aciklama_1" id="myTextarea" rows="3" placeholder="Birinci açıklama"><?php echo htmlspecialchars($sayfaAyarlari['sayfa_aciklama_1'] ?? '', ENT_QUOTES);?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sayfa_baslik_2">2. Başlık</label>
                                <input type="text" class="form-control form-control-sm" name="sayfa_baslik_2" id="sayfa_baslik_2" placeholder="İkinci başlık" value="<?php echo htmlspecialchars($sayfaAyarlari['sayfa_baslik_2'] ?? '', ENT_QUOTES);?>">
                            </div>
                            <div class="form-group">
                                <label for="sayfa_aciklama_2">2. Açıklama</label>
                                <textarea class="form-control form-control-sm" name="sayfa_aciklama_2" id="myTextarea2" rows="3" placeholder="İkinci açıklama"><?php echo htmlspecialchars($sayfaAyarlari['sayfa_aciklama_2'] ?? '', ENT_QUOTES);?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Change icon direction on collapse show/hide
    document.addEventListener('DOMContentLoaded', function() {
        var heading = document.getElementById('sayfaYonetimiHeading');
        var collapse = document.getElementById('sayfaYonetimiCollapse');
        var chevron = heading.querySelector('.fa-chevron-down');
        $('#sayfaYonetimiCollapse').on('show.bs.collapse', function () {
            chevron.classList.remove('fa-chevron-down');
            chevron.classList.add('fa-chevron-up');
        });
        $('#sayfaYonetimiCollapse').on('hide.bs.collapse', function () {
            chevron.classList.remove('fa-chevron-up');
            chevron.classList.add('fa-chevron-down');
        });
    });
</script>

<div class="card">
	<form action="../_class/yonetim_islem.php" method="POST">
	<div class="card-body">
		<div class="row mb-3">
			<div class="col-lg-12">
				<div class="btn-toolbar" role="toolbar">					
					<a href="ogrenme_deneyimi_ekle.html" class="btn btn-primary btn-sm mr-1">
						<i class="icon-plus font-12"></i> Yeni Ekle
					</a>
					<div class="dropdown mr-1">
						<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuSizeButton3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="icon-options-vertical font-12"></i> Seçilenlere Uygula
						</button>
						<div class="dropdown-menu p-0 min-width-full" aria-labelledby="dropdownMenuSizeButton3">
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="ogrenme_deneyimi_aktif"><i class="icon-check"></i> Seçilenleri Aktif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="ogrenme_deneyimi_pasif"><i class="icon-close"></i> Seçilenleri Pasif Et</button>
							<button class="dropdown-item p-2 cursor-pointer" type="submit" name="ogrenme_deneyimi_tumu"><i class="icon-trash"></i> Seçilenleri Sil</button>
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
								<th>Başlık</th>
								<th>Program</th>
								<th>Kısa Açıklama</th>
								<th style="width:70px;">Durum</th>
								<th style="width:180px;">İşlem</th>
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
			url:"data/ogrenme_deneyimi.php",
			type:"post"
		},
		"order": [[ 2, "asc" ]],
		"aLengthMenu": [
			[5, 10, 15, <?php echo cVCLmHLxbS_tumu("ogrenme_deneyimi",1);?>],
			[5, 10, 15, "Tümü"]
		],
		"columnDefs": [
			{ "orderable": false, "targets": [0, 7] },
			{ "targets": [ 1 ],"visible": false },
			{  "className": "secili", targets: [3, 4, 5] },
			{  "className": "secili text-center", targets: [6] },
			{  "className": "text-center", targets: [2, 7] }
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
	
	// Sayfa Yönetimi Form Submit
	$('#sayfaYonetimiForm').on('submit', function(e) {
		e.preventDefault();
		var formData = new FormData(this);
		
		$.ajax({
			type: "POST",
			url: "../_class/yonetim_islem.php",
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function(response) {
				if(response.success) {
					alert('Sayfa yönetimi başarıyla güncellendi.');
					location.reload();
				} else {
					alert('Hata: ' + response.message);
				}
			},
			error: function() {
				alert('Bir hata oluştu.');
			}
		});
	});
});
</script>
<?php 
cVCLmHLxbS_mesaj("ogrenme_deneyimi_ekle",1,"yes","Başarı ile eklenmiştir.");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_sil",1,"yes","Başarı ile silinmiştir.");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_sil",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_tumu",1,"yes","Seçilen kayıtlar başarıyla silinmiştir.");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_tumu",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_aktif",1,"yes","Bilgileriniz başarıyla güncellendi.");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_aktif",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_pasif",1,"yes","Bilgileriniz başarıyla güncellendi.");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_pasif",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_sayfa_yonetimi",1,"yes","Sayfa yönetimi başarıyla güncellendi.");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_sayfa_yonetimi",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_banner_resim_sil",1,"yes","Banner resmi başarıyla silinmiştir.");
cVCLmHLxbS_mesaj("ogrenme_deneyimi_banner_resim_sil",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("secim",3,"secimyok","Hiç bir şey seçmediniz. Lütfen işlem yapmak istediğiniz eylemi ve ID leri seçin.");
?>

