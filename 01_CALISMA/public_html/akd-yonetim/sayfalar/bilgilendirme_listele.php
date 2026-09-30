<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <div class="mt-3">
                    <button type="button" class="btn btn-danger btn-sm" id="topluSil"><i class="ti-trash"></i> Seçilenleri Sil</button>
                    <a href="bilgilendirme-ekle.html" class="btn btn-primary btn-sm float-right"><i class="ti-plus"></i> Yeni Ekle</a>
                </div><br>
                <h4 class="card-title">Bilgilendirme Kutucukları</h4>
                <div class="table-responsive">
                    <table id="bilgilendirmeTablosu" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" class="form-check-input" id="selectAll"><i class="input-helper"></i></label></div></th>
                                <th>#</th>
                                <th>Başlık</th>
                                <th>İkon</th>
                                <th>Link</th>
                                <th>Durum</th>
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

<script>
$(document).ready(function() {
    var table = $('#bilgilendirmeTablosu').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "data/bilgilendirme.php",
        "order": [[1, 'desc']],
        "language": {
            "url": "../tema/genel/datatable/datatable-tr.json"
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 6] },
            { "className": "text-center", "targets": [0, 1, 3, 5, 6] }
        ]
    });

    // Tümünü seç
    $('#selectAll').on('click', function(){
        $('input[type="checkbox"]').prop('checked', this.checked);
    });

    // Toplu silme işlemi
    $('#topluSil').on('click', function(){
        var selected = [];
        $('input[type="checkbox"]:checked').each(function(){
            if($(this).val() != 'on') {
                selected.push($(this).val());
            }
        });

        if(selected.length > 0) {
            if(confirm('Seçili öğeleri silmek istediğinize emin misiniz?')) {
                $.ajax({
                    type: 'POST',
                    url: '../_class/yonetim_islem.php',
                    data: { bilgilendirme_toplu_sil: 'ok', id: selected },
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
            toastr.warning('Lütfen silmek istediğiniz öğeleri seçiniz!');
        }
    });

    // Durum değiştirme
    $(document).on('change', '.durumDegistir', function(){
        var id = $(this).data('id');
        var durum = $(this).prop('checked') ? 1 : 0;
        
        $.ajax({
            type: 'POST',
            url: '../_class/yonetim_islem.php',
            data: { bilgilendirme_durum: 'ok', id: id, durum: durum },
            success: function(cevap) {
                var sonuc = JSON.parse(cevap);
                if(sonuc.durum == 'success') {
                    toastr.success(sonuc.mesaj);
                } else {
                    toastr.error(sonuc.mesaj);
                    // Hata durumunda toggle'ı geri al
                    $('.durumDegistir[data-id='+id+']').prop('checked', !durum);
                }
            }
        });
    });
});
</script>
