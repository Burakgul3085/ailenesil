<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
$Sorgu = $db->prepare("SELECT * FROM hesaplar ORDER BY id DESC");
$Sorgu->execute();
$islem = $Sorgu->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3>Hesap Numaralarımız</h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li class="active"><a href="<?php echo $sayfalink; ?>">Hesap Numaralarımız</a></li>
			</ul>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
				<form id="hesap_ekle" class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
					<input type="hidden" name="hesap_id" id="hesap_id" value="" />
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Banka Adı</label>
								<input type="text" class="form-control form-control-sm" name="banka_adi" id="banka_adi" required />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>IBAN</label>
								<input type="text" class="form-control form-control-sm" name="iban" id="iban" required />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Hesap Sahibi</label>
								<input type="text" class="form-control form-control-sm" name="hesap_sahibi" id="hesap_sahibi" required />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Hesap No</label>
								<input type="text" class="form-control form-control-sm" name="hesap_no" id="hesap_no" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Şube</label>
								<input type="text" class="form-control form-control-sm" name="sube" id="sube" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Para Birimi</label>
								<select class="form-control form-control-sm" name="para_birimi" id="para_birimi">
									<option value="TRY">TRY - Türk Lirası</option>
									<option value="USD">USD - Amerikan Doları</option>
									<option value="EUR">EUR - Euro</option>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>SWIFT (BIC) Kodu</label>
								<input type="text" class="form-control form-control-sm" name="swift_kodu" id="swift_kodu" placeholder="Örn: TR330001000000000000000001" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Banka Logosu (opsiyonel)</label>
								<input type="file" class="form-control form-control-sm" name="logo" accept="image/*" />
							</div>
						</div>
						<div class="col-md-12">
							<button type="submit" name="hesap_ekle" id="hesap_submit_btn" class="btn btn-success btn-sm">Ekle</button>
						</div>
					</div>
				</form>

				<hr/>

<!-- Düzenleme Modal -->
<div class="modal fade" id="hesap_modal" tabindex="-1" role="dialog" aria-labelledby="hesap_modal_label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hesap_modal_label">Hesap Düzenle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="hesap_duzenle_form" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
                    <input type="hidden" name="hesap_id" id="modal_hesap_id" value="" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Banka Adı</label>
                                <input type="text" class="form-control form-control-sm" name="banka_adi" id="modal_banka_adi" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>IBAN</label>
                                <input type="text" class="form-control form-control-sm" name="iban" id="modal_iban" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hesap Sahibi</label>
                                <input type="text" class="form-control form-control-sm" name="hesap_sahibi" id="modal_hesap_sahibi" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hesap No</label>
                                <input type="text" class="form-control form-control-sm" name="hesap_no" id="modal_hesap_no" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Şube</label>
                                <input type="text" class="form-control form-control-sm" name="sube" id="modal_sube" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Para Birimi</label>
                                <select class="form-control form-control-sm" name="para_birimi" id="modal_para_birimi">
                                    <option value="TRY">TRY - Türk Lirası</option>
                                    <option value="USD">USD - Amerikan Doları</option>
                                    <option value="EUR">EUR - Euro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>SWIFT (BIC) Kodu</label>
                                <input type="text" class="form-control form-control-sm" name="swift_kodu" id="modal_swift_kodu" placeholder="Örn: TR330001000000000000000001" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Banka Logosu (opsiyonel)</label>
                                <input type="file" class="form-control form-control-sm" name="logo" accept="image/*" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="modal_guncelle_btn">Güncelle</button>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table id="order-listingg" class="table table-bordered table-hover">
        <thead class="headbg">
            <tr>
                <th class="noshort" style="width:20px;"><input id="checkbox-4" class="select-all checkbox-custom" type="checkbox" style="width:100px;"><label for="checkbox-4" class="checkbox-custom-label mb-0"><span class="checktext"></span></label></th>
                <th style="width:30px;">ID</th>
                <th>Banka</th>
                <th>IBAN</th>
                <th>Hesap Sahibi</th>
                <th style="width:115px;">İşlem</th>
            </tr>
        </thead>
        <tbody id="sortable"></tbody>
    </table>
</div>
<script>
$(document).ready(function(){
    var dataTable=$('#order-listingg').DataTable({
        "processing": true,
        "serverSide":true,
        "ajax":{ url:"data/hesaplar.php", type:"post" },
        "order": [[ 1, "desc" ]],
        "aLengthMenu": [[5, 10, 15, 100], [5, 10, 15, "Tümü"]],
        "columnDefs": [
            { "orderable": false, "targets": [0, 5] },
            { "targets": [ 1 ],"visible": false },
            {  "className": "secili", targets: [2] },
            {  "className": "secili text-center", targets: [3, 4] },
            {  "className": "text-center", targets: [5] }
        ],
        "iDisplayLength": 10,
        "language": { "url":"js/Turkish.json" },
        "fnCreatedRow": function( nRow, aData, iDataIndex ) { $(nRow).attr('id', 'item-'+aData[1]); },
        "fnDrawCallback": function( oSettings ) { $(".popconfirm").popConfirm(); }
    });
    
    // Düzenle butonu tıklandığında modal aç
    $(document).on('click', '.hesap-duzenle', function(){
        var id = $(this).data('id');
        $('#modal_hesap_id').val(id);
        $('#modal_banka_adi').val($(this).data('banka'));
        $('#modal_iban').val($(this).data('iban'));
        $('#modal_hesap_sahibi').val($(this).data('hesap_sahibi'));
        $('#modal_hesap_no').val($(this).data('hesap_no'));
        $('#modal_sube').val($(this).data('sube'));
        $('#modal_para_birimi').val($(this).data('para_birimi') || 'TRY');
        $('#modal_swift_kodu').val($(this).data('swift_kodu') || '');
        
        $('#hesap_modal').modal('show');
    });
    
    // Modal içindeki güncelle butonu
    $('#modal_guncelle_btn').click(function(){
        $('#hesap_duzenle_form').submit();
    });
    
    // Modal formu AJAX ile submit et
    $('#hesap_duzenle_form').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(form[0]);
        formData.append('hesap_guncelle', '1');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
                dataTable.ajax.reload(null, false);
                $('#hesap_modal').modal('hide');
                alert('Güncelleme başarılı!');
            },
            error: function(){
                alert('Bir hata oluştu!');
            }
        });
    });
    
    // Hesap ekleme ve silme formlarında AJAX kullanma; normal POST çalışsın.
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
        if(this.checked){ $("input:checkbox").not(this).closest("tr").addClass('highlight'); }
        else{ $("input:checkbox").not(this).closest("tr").removeClass('highlight'); }
    });
});
</script>
			</div>
		</div>
	</div>
</div>


<style>
.dataTables_wrapper .dataTable .btn, .dataTables_wrapper .dataTable .fc button, .fc .dataTables_wrapper .dataTable button, .dataTables_wrapper .dataTable .ajax-upload-dragdrop .ajax-file-upload, .ajax-upload-dragdrop .dataTables_wrapper .dataTable .ajax-file-upload, .dataTables_wrapper .dataTable .swal2-modal .swal2-buttonswrapper .swal2-styled, .swal2-modal .swal2-buttonswrapper .dataTables_wrapper .dataTable .swal2-styled, .dataTables_wrapper .dataTable .wizard > .actions a, .wizard > .actions .dataTables_wrapper .dataTable a {
    padding: 0.2rem 0.5rem;
    vertical-align: -webkit-baseline-middle;
}
	</style>