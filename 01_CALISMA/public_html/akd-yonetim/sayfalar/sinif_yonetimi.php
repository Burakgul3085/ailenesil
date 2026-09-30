<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>

<div class="page-header">
    <div class="page-title mt-0 mb-0">
        <h3>Sınıf Yönetimi</h3>
        <div class="crumbs">
            <ul id="breadcrumbs" class="breadcrumb">
                <li><a href="index.html"><i class="icon-home"></i> Anasayfa</a></li>
                <li><a href="#">Program Yönetimi</a></li>
                <li class="current"><a href="#">Sınıf Yönetimi</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <div class="widget-header">
                <h4><i class="icon-list"></i> Sınıf Listesi</h4>
                <div class="toolbar no-padding">
                    <div class="btn-group">
                        <span class="btn btn-xs">
                            <a href="#sinifEkle" data-toggle="modal" class="btn btn-success btn-sm">
                                <i class="icon-plus"></i> Yeni Sınıf Ekle
                            </a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="widget-content">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="sinifTablosu">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th>Şehir</th>
                                <th>Sınıf Adı</th>
                                <th>Seviye</th>
                                <th>Program</th>
                                <th>Durum</th>
                                <th>Sıra</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will load data here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Class Modal -->
<div class="modal fade" id="sinifEkle">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="sinifForm" class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Yeni Sınıf Ekle</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="sinif_id" value="0">
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Şehir <span class="required">*</span></label>
                        <div class="col-sm-9">
                            <select name="sehir_id" id="sehir_id" class="form-control required" required>
                                <option value="">Şehir Seçiniz</option>
                                <?php
                                $sehirler = $db->query("SELECT * FROM sehirler WHERE durum = 1 ORDER BY sira ASC, sehir_adi ASC");
                                foreach($sehirler as $sehir) {
                                    echo '<option value="'.$sehir['id'].'">'.$sehir['sehir_adi'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Program <span class="required">*</span></label>
                        <div class="col-sm-9">
                            <select name="program_id" id="program_id" class="form-control required" required>
                                <option value="">Program Seçiniz</option>
                                <?php
                                $programlar = $db->query("SELECT * FROM programlar WHERE durum = 1 ORDER BY sira ASC, baslik ASC");
                                foreach($programlar as $program) {
                                    echo '<option value="'.$program['id'].'">'.$program['baslik'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Sınıf Adı <span class="required">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" name="sinif_adi" id="sinif_adi" class="form-control required" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Seviye <span class="required">*</span></label>
                        <div class="col-sm-9">
                            <select name="seviye" id="seviye" class="form-control required" required>
                                <option value="ilkokul">İlkokul</option>
                                <option value="ortaokul">Ortaokul</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Açıklama</label>
                        <div class="col-sm-9">
                            <textarea name="aciklama" id="aciklama" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Sıra</label>
                        <div class="col-sm-3">
                            <input type="number" name="sira" id="sira" class="form-control" value="0">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Durum</label>
                        <div class="col-sm-9">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="durum" id="durum" value="1" checked> Aktif
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="silOnayModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Silme Onayı</h4>
            </div>
            <div class="modal-body">
                <p>Bu sınıfı silmek istediğinize emin misiniz?</p>
                <p class="text-danger"><small>Bu işlem geri alınamaz!</small></p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="silinecek_id" value="0">
                <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" id="btnSil">Sil</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script type="text/javascript">
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#sinifTablosu').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "data/siniflar.php",
            "type": "POST"
        },
        "order": [[6, 'asc']],
        "columnDefs": [
            { "orderable": false, "targets": [7] },
            { "className": "text-center", "targets": [0, 4, 5, 6, 7] },
            { "width": "10%", "targets": [0, 5, 6, 7] }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Turkish.json"
        }
    });
    
    // Form validation
    var form = $('#sinifForm');
    form.validate({
        errorElement: 'span',
        errorClass: 'help-block',
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorPlacement: function(error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });
    
    // Form submit handler
    form.on('submit', function(e) {
        e.preventDefault();
        
        if (!form.valid()) {
            return false;
        }
        
        var formData = $(this).serialize();
        var action = ($('#sinif_id').val() == '0') ? 'sinif_ekle' : 'sinif_guncelle';
        
        $.ajax({
            url: '_class/yonetim_islem.php',
            type: 'POST',
            data: formData + '&' + action + '=1',
            dataType: 'json',
            beforeSend: function() {
                $('button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> İşleniyor...');
            },
            success: function(response) {
                if (response.durum == 'success') {
                    $('#sinifEkle').modal('hide');
                    toastr.success(response.mesaj, 'Başarılı');
                    table.ajax.reload();
                } else {
                    toastr.error(response.mesaj, 'Hata!');
                }
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).html('Kaydet');
            },
            error: function() {
                toastr.error('İşlem sırasında bir hata oluştu!', 'Hata!');
            }
        });
    });
    
    // Edit button click handler
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: 'data/siniflar.php',
            type: 'POST',
            data: { id: id, islem: 'getir' },
            dataType: 'json',
            success: function(response) {
                if (response.durum == 'success') {
                    var sinif = response.kayit;
                    
                    $('#sinif_id').val(sinif.id);
                    $('#sehir_id').val(sinif.sehir_id).trigger('change');
                    $('#program_id').val(sinif.program_id).trigger('change');
                    $('#sinif_adi').val(sinif.sinif_adi);
                    $('#seviye').val(sinif.seviye);
                    $('#aciklama').val(sinif.aciklama);
                    $('#sira').val(sinif.sira);
                    $('#durum').prop('checked', sinif.durum == 1);
                    
                    $('#sinifEkle .modal-title').text('Sınıf Düzenle');
                    $('#sinifEkle').modal('show');
                } else {
                    toastr.error(response.mesaj, 'Hata!');
                }
            }
        });
    });
    
    // Delete button click handler
    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        $('#silinecek_id').val(id);
        $('#silOnayModal').modal('show');
    });
    
    // Confirm delete
    $('#btnSil').on('click', function() {
        var id = $('#silinecek_id').val();
        
        $.ajax({
            url: '_class/yonetim_islem.php',
            type: 'GET',
            data: { sinifsil: 'ok', id: id },
            dataType: 'json',
            success: function(response) {
                if (response.durum == 'success') {
                    $('#silOnayModal').modal('hide');
                    toastr.success(response.mesaj, 'Başarılı');
                    table.ajax.reload();
                } else {
                    toastr.error(response.mesaj, 'Hata!');
                }
            }
        });
    });
    
    // Status toggle
    $(document).on('change', '.durumDegistir', function() {
        var id = $(this).data('id');
        var durum = $(this).is(':checked') ? 1 : 0;
        
        $.ajax({
            url: '_class/yonetim_islem.php',
            type: 'POST',
            data: { sinif_durum: 'ok', id: id, durum: durum },
            dataType: 'json',
            success: function(response) {
                if (response.durum == 'success') {
                    toastr.success(response.mesaj, 'Başarılı');
                } else {
                    toastr.error(response.mesaj, 'Hata!');
                    table.ajax.reload();
                }
            }
        });
    });
    
    // Reset form when modal is closed
    $('#sinifEkle').on('hidden.bs.modal', function () {
        form[0].reset();
        form.validate().resetForm();
        $('.form-group').removeClass('has-error');
        $('#sinif_id').val('0');
        $('#sinifEkle .modal-title').text('Yeni Sınıf Ekle');
    });
});
</script>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Şehir <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="sehir_id" id="sehir_id" class="form-control required" required>
                                <option value="">Şehir Seçiniz</option>
                                <?php
                                $sehirler = $db->query("SELECT * FROM sehirler WHERE durum = 1 ORDER BY sira ASC");
                                while($sehir = $sehirler->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="'.$sehir['id'].'">'.$sehir['sehir_adi'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Program <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="program_id" id="program_id" class="form-control required" required>
                                <option value="">Program Seçiniz</option>
                                <?php
                                $programlar = $db->query("SELECT * FROM programlar WHERE durum = 1 ORDER BY baslik ASC");
                                while($program = $programlar->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="'.$program['id'].'">'.$program['baslik'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Sınıf Adı <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="sinif_adi" id="sinif_adi" class="form-control required" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Seviye <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="seviye" id="seviye" class="form-control required" required>
                                <option value="ilkokul">İlkokul</option>
                                <option value="ortaokul">Ortaokul</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Açıklama</label>
                        <div class="col-md-9">
                            <textarea name="aciklama" id="aciklama" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Durum</label>
                        <div class="col-md-9">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="durum" id="durum" value="1" checked> Aktif
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Sıra</label>
                        <div class="col-md-9">
                            <input type="number" name="sira" id="sira" class="form-control" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="silOnay">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Silme Onayı</h4>
            </div>
            <div class="modal-body">
                Bu sınıfı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!
                <input type="hidden" id="sil_id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" id="btnSil">Sil</button>
            </div>
        </div>
    </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="../tema/yonetim/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css">

<!-- DataTables JS -->
<script type="text/javascript" src="../tema/yonetim/assets/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../tema/yonetim/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>

<!-- Form Validation -->
<script src="../tema/yonetim/assets/plugins/validation/jquery.validate.min.js"></script>
<script src="../tema/yonetim/assets/plugins/validation/localization/messages_tr.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#sinifTablosu').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "data/siniflar.php?islem=liste",
                "type": "POST"
            },
            "order": [[6, 'asc']],
            "columnDefs": [
                { "orderable": false, "targets": [7] },
                { "width": "5%", "targets": 0 },
                { "width": "15%", "targets": 1 },
                { "width": "20%", "targets": 2 },
                { "width": "10%", "targets": 3 },
                { "width": "25%", "targets": 4 },
                { "width": "5%", "targets": 5 },
                { "width": "5%", "targets": 6 },
                { "width": "10%", "targets": 7 }
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Turkish.json"
            }
        });

        // Form validation
        var form = $("#sinifForm");
        form.validate({
            errorClass: "text-danger",
            errorElement: "span",
            errorPlacement: function(error, element) {
                error.addClass('help-block');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            submitHandler: function(form) {
                var formData = $(form).serialize();
                
                $.ajax({
                    type: 'POST',
                    url: '../_class/yonetim_islem.php',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if(response.durum == 'success') {
                            $('#sinifEkle').modal('hide');
                            table.ajax.reload(null, false);
                            toastr.success(response.mesaj, 'Başarılı');
                        } else {
                            toastr.error(response.mesaj, 'Hata!');
                        }
                    },
                    error: function() {
                        toastr.error('İşlem sırasında bir hata oluştu!', 'Hata!');
                    }
                });
                
                return false;
            }
        });

        // Edit button click
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            
            $.ajax({
                type: 'POST',
                url: 'data/siniflar.php',
                data: { islem: 'getir', id: id },
                dataType: 'json',
                success: function(response) {
                    if(response.durum == 'success') {
                        var data = response.kayit;
                        
                        $('#sinif_id').val(data.id);
                        $('#sehir_id').val(data.sehir_id);
                        $('#program_id').val(data.program_id);
                        $('#sinif_adi').val(data.sinif_adi);
                        $('#seviye').val(data.seviye);
                        $('#aciklama').val(data.aciklama);
                        $('#sira').val(data.sira);
                        $('#durum').prop('checked', data.durum == 1);
                        
                        $('.modal-title').text('Sınıf Düzenle');
                        $('#sinifEkle').modal('show');
                    } else {
                        toastr.error(response.mesaj, 'Hata!');
                    }
                },
                error: function() {
                    toastr.error('Veri yüklenirken bir hata oluştu!', 'Hata!');
                }
            });
        });

        // Delete button click
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            $('#sil_id').val(id);
            $('#silOnay').modal('show');
        });

        // Confirm delete
        $('#btnSil').click(function() {
            var id = $('#sil_id').val();
            
            $.ajax({
                type: 'POST',
                url: '../_class/yonetim_islem.php',
                data: { sinifsil: 'ok', id: id },
                dataType: 'json',
                success: function(response) {
                    if(response.durum == 'success') {
                        $('#silOnay').modal('hide');
                        table.ajax.reload(null, false);
                        toastr.success(response.mesaj, 'Başarılı');
                    } else {
                        toastr.error(response.mesaj, 'Hata!');
                    }
                },
                error: function() {
                    toastr.error('Silme işlemi sırasında bir hata oluştu!', 'Hata!');
                }
            });
        });

        // Add new button click
        $('.btn-success').click(function() {
            form[0].reset();
            $('#sinif_id').val('0');
            $('.modal-title').text('Yeni Sınıf Ekle');
            $('#sinifEkle').modal('show');
        });

        // Status toggle
        $(document).on('change', '.durumDegistir', function() {
            var id = $(this).data('id');
            var durum = $(this).is(':checked') ? 1 : 0;
            
            $.ajax({
                type: 'POST',
                url: '../_class/yonetim_islem.php',
                data: { sinif_durum: 'ok', id: id, durum: durum },
                dataType: 'json',
                success: function(response) {
                    if(response.durum == 'success') {
                        toastr.success('Durum güncellendi', 'Başarılı');
                    } else {
                        toastr.error('Durum güncellenirken bir hata oluştu!', 'Hata!');
                        table.ajax.reload(null, false);
                    }
                },
                error: function() {
                    toastr.error('İşlem sırasında bir hata oluştu!', 'Hata!');
                    table.ajax.reload(null, false);
                }
            });
        });
    });
</script>
