<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>

<div class="page-header">
    <div class="page-title mt-0 mb-0">
        <h3>Şehir Yönetimi</h3>
        <div class="crumbs">
            <ul id="breadcrumbs" class="breadcrumb">
                <li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
                <li><a href="<?php echo $sayfalink;?>">Site Yönetimi</a></li>
                <li class="active"><a href="<?php echo $sayfalink;?>">Şehir Yönetimi</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-lg-12">
                <div class="btn-toolbar" role="toolbar">                  
                    <button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#sehirEkleModal">
                        <i class="icon-plus font-12"></i> Yeni Şehir Ekle
                    </button>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="sehirListesi" class="table table-bordered table-hover">
                        <thead class="headbg">
                            <tr>
                                <th style="width:50px;">Sıra</th>
                                <th>Şehir Adı</th>
                                <th style="width:100px;">Durum</th>
                                <th style="width:120px;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sehirler = $db->query("SELECT * FROM sehirler WHERE dil = '{$_SESSION['admin_dil']}' ORDER BY sira ASC, sehir_adi ASC")->fetchAll(PDO::FETCH_ASSOC);
                            foreach($sehirler as $sehir) {
                                $durum = $sehir['durum'] == 1 ? 'success' : 'danger';
                                $durum_text = $sehir['durum'] == 1 ? 'Aktif' : 'Pasif';
                                echo "
                                <tr id='sehir-{$sehir['id']}'>
                                    <td class='text-center'>{$sehir['sira']}</td>
                                    <td>{$sehir['sehir_adi']}</td>
                                    <td class='text-center'>
                                        <span class='badge badge-outline-{$durum} durum-degistir' data-id='{$sehir['id']}' data-tablo='sehirler' data-alan='durum' style='cursor:pointer;'>{$durum_text}</span>
                                    </td>
                                    <td class='text-center'>
                                        <button class='btn btn-sm btn-outline-primary sehir-duzenle' data-id='{$sehir['id']}' data-sehir='{$sehir['sehir_adi']}' data-sira='{$sehir['sira']}'>
                                            <i class='ti-pencil-alt'></i>
                                        </button>
                                        <button class='btn btn-sm btn-outline-danger sehir-sil' data-id='{$sehir['id']}'>
                                            <i class='ti-trash'></i>
                                        </button>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Şehir Ekleme Modal -->
<div class="modal fade" id="sehirEkleModal" tabindex="-1" role="dialog" aria-labelledby="sehirEkleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sehirEkleModalLabel">Yeni Şehir Ekle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="sehirEkleForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="sehir_adi">Şehir Adı</label>
                        <input type="text" class="form-control" id="sehir_adi" name="sehir_adi" required>
                    </div>
                    <div class="form-group">
                        <label for="sira">Sıra</label>
                        <input type="number" class="form-control" id="sira" name="sira" value="0">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="durum" name="durum" value="1" checked>
                            <label class="custom-control-label" for="durum">Aktif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="sehir_id" name="sehir_id" value="">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // DataTable initialization
    $('#sehirListesi').DataTable({
        "order": [[0, "asc"]],
        "language": {
            "url": "js/Turkish.json"
        },
        "columnDefs": [
            { "orderable": false, "targets": [3] }
        ]
    });

    // Yeni şehir ekleme formu
    $('#sehirEkleForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var isEdit = $('#sehir_id').val() !== '';
        var url = '../_class/yonetim_islem.php?islem=' + (isEdit ? 'sehir_duzenle' : 'sehir_ekle');
        
        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.durum == 'success') {
                    toastr.success(response.mesaj);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.mesaj || 'Bir hata oluştu!');
                }
            },
            error: function() {
                toastr.error('İşlem sırasında bir hata oluştu!');
            }
        });
    });

    // Şehir düzenleme butonu
    $('.sehir-duzenle').on('click', function() {
        var id = $(this).data('id');
        var sehir = $(this).data('sehir');
        var sira = $(this).data('sira');
        
        $('#sehirEkleModalLabel').text('Şehir Düzenle');
        $('#sehir_adi').val(sehir);
        $('#sira').val(sira);
        $('#sehir_id').val(id);
        
        $('#sehirEkleModal').modal('show');
    });

    // Şehir silme butonu
    $('.sehir-sil').on('click', function() {
        var id = $(this).data('id');
        
        if(confirm('Bu şehri silmek istediğinize emin misiniz? Bu işlem geri alınamaz!')) {
            $.ajax({
                type: 'POST',
                url: '../_class/yonetim_islem.php?islem=sehir_sil',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if(response.durum == 'success') {
                        toastr.success(response.mesaj);
                        $('#sehir-' + id).fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        toastr.error(response.mesaj || 'Bir hata oluştu!');
                    }
                },
                error: function() {
                    toastr.error('İşlem sırasında bir hata oluştu!');
                }
            });
        }
    });

    // Sıralama işlemi
    $("#sehirListesi tbody").sortable({
        update: function(event, ui) {
            var siralama = [];
            $("#sehirListesi tbody tr").each(function(index) {
                var id = $(this).attr('id').replace('sehir-', '');
                siralama.push({id: id, sira: index + 1});
            });
            
            $.ajax({
                type: 'POST',
                url: '../_class/yonetim_islem.php?islem=sehir_sirala',
                data: {siralama: siralama},
                dataType: 'json',
                success: function(response) {
                    if(response.durum == 'success') {
                        toastr.success('Sıralama güncellendi');
                    } else {
                        toastr.error('Sıralama güncellenirken bir hata oluştu!');
                    }
                },
                error: function() {
                    toastr.error('İşlem sırasında bir hata oluştu!');
                }
            });
        }
    }).disableSelection();
});
</script>
