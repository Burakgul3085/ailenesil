<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="mt-3">
                    <button type="button" class="btn btn-danger btn-sm" id="topluSil">
                        <i class="ti-trash"></i> Seçilenleri Sil
                    </button>
                    <a href="dosya-ekle.html" class="btn btn-primary btn-sm float-right">
                        <i class="ti-plus"></i> Yeni Dosya Ekle
                    </a>
                </div><br>
                <h4 class="card-title">Dosya Yönetimi</h4>
                <div class="table-responsive">
                    <table id="dosyalarTablosu" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" class="form-check-input" id="selectAll"><i class="input-helper"></i></label></div></th>
                                <th>#</th>
                                <th>Başlık</th>
                                <th>Kategori</th>
                                <th>Dosya</th>
                                <th>Boyut</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#dosyalarTablosu').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "data/dosyalar.php",
        "order": [[1, 'asc']],
        "language": {
            "url": "../tema/genel/datatable/datatable-tr.json"
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 4, 7] },
            { "className": "text-center", "targets": [0, 1, 5, 6, 7] }
        ]
    });

    $('#selectAll').on('click', function(){
        $('input[type="checkbox"]').prop('checked', this.checked);
    });

    $('#topluSil').on('click', function(){
        var selected = [];
        $('input[name="id[]"]:checked').each(function(){
            selected.push($(this).val());
        });
        if(selected.length > 0) {
            if(confirm('Seçili dosyaları silmek istediğinize emin misiniz? Bu işlem geri alınamaz.')) {
                $.ajax({
                    type: 'POST',
                    url: '../_class/yonetim_islem.php',
                    data: { dosya_toplu_sil: 'ok', id: selected },
                    success: function(cevap) {
                        var sonuc = JSON.parse(cevap);
                        if(sonuc.durum == 'success') {
                            toastr.success(sonuc.mesaj);
                            table.ajax.reload();
                        } else {
                            toastr.error(sonuc.mesaj);
                        }
                    }
                });
            }
        } else {
            toastr.warning('Lütfen silmek istediğiniz dosyaları seçiniz!');
        }
    });

    $(document).on('change', '.durumDegistir', function(){
        var id = $(this).data('id');
        var durum = $(this).prop('checked') ? 1 : 0;
        $.ajax({
            type: 'POST',
            url: '../_class/yonetim_islem.php',
            data: { dosya_durum: 'ok', id: id, durum: durum },
            success: function(cevap) {
                var sonuc = JSON.parse(cevap);
                if(sonuc.durum == 'success') {
                    toastr.success(sonuc.mesaj);
                } else {
                    toastr.error(sonuc.mesaj);
                    $('.durumDegistir[data-id='+id+']').prop('checked', !durum);
                }
            }
        });
    });
});
</script>
