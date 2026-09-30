<?php echo !defined("GUVENLIK") ? die("Erişim Engellendi!.") : null;?> 
<?php require_once('../_class/country_detector.php'); ?>

<div class="page-header">
    <div class="page-title mt-0 mb-0">
        <h3><i class="icon-wallet mr-2 text-primary"></i> <?=@$admindil['txt133'];?></h3>
    </div>
</div>

<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-3 text-muted"><i class="icon-magnifier mr-1"></i> Filtreleme Seçenekleri</h5>
        <form id="filterForm">
            <div class="row">
                <div style="display:none;" class="col-md-3 col-sm-6 mb-3">
                    <label class="small font-weight-bold text-dark text-uppercase">Ana Kampanya</label>
                    <select class="form-control shadow-sm border-primary" id="modul_filtre">
                        <option value="">Tüm Kampanyalar</option>
                        <?php 
                        $mSorgu = $db->prepare("SELECT id, adi FROM bagis_moduller WHERE durum=1 AND dil=?");
                        $mSorgu->execute([$_SESSION['admin_dil']]);
                        while($m = $mSorgu->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="'.$m['id'].'">'.$m['adi'].'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="small font-weight-bold text-dark text-uppercase">Kategori</label>
                    <select class="form-control shadow-sm border-info" id="kategori_filtre">
                        <option value="">Tüm Kategoriler</option>
                        <?php 
                        $kSorgu = $db->prepare("SELECT id, modul_id, adi FROM bagis_kategori WHERE durum=1 AND dil=?");
                        $kSorgu->execute([$_SESSION['admin_dil']]);
                        while($k = $kSorgu->fetch(PDO::FETCH_ASSOC)) {
                            // data-parent ile hangi kampanyaya bağlı olduğunu JS'e bildiriyoruz
                            echo '<option value="'.$k['id'].'" data-parent="'.$k['modul_id'].'">'.$k['adi'].'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="small font-weight-bold text-dark text-uppercase">Bağışçı / No</label>
                    <input type="text" class="form-control shadow-sm" id="bagisci_adi" placeholder="İsim veya Sipariş No...">
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <label class="small font-weight-bold text-dark text-uppercase">Ülke</label>
                    <select class="form-control shadow-sm" id="ulke_filtre">
                        <option value="">Tüm Ülkeler</option>
                        <?php 
                        $uSorgu = $db->query("SELECT DISTINCT ulke FROM bagis_odeme WHERE ulke != '' ORDER BY ulke ASC");
                        while($u = $uSorgu->fetch(PDO::FETCH_ASSOC)){ 
                            echo '<option value="'.$u['ulke'].'">'.CountryDetector::getCountryName($u['ulke']).'</option>';
                        } ?>
                    </select>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold">Başlangıç Tarihi</label>
                    <input type="date" class="form-control form-control-sm shadow-sm" id="tarih_baslangic">
                </div>
                <div class="col-md-3 col-sm-6 mb-2">
                    <label class="small font-weight-bold">Bitiş Tarihi</label>
                    <input type="date" class="form-control form-control-sm shadow-sm" id="tarih_bitis">
                </div>
                <div class="col-md-6 d-flex align-items-end justify-content-end mb-2">
                    <button type="button" class="btn btn-primary btn-sm px-4 mr-2" id="btnSorgula">
                        <i class="fa fa-search mr-1"></i> Listele
                    </button>
                    <button type="button" class="btn btn-light btn-sm px-4 mr-2 border" id="btnSifirla">
                        <i class="fa fa-refresh mr-1"></i> Sıfırla
                    </button>
                    <a href="../_class/gelen_bagislar_excel_cikti.php" class="btn btn-success btn-sm px-4 shadow-sm" target="_blank">
                        <i class="fa fa-file-excel-o mr-1"></i> Excel'e Aktar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="bagis_listesi" class="table table-hover table-custom w-100">
                <thead class="bg-light">
                    <tr>
                        <th style="width:30px;">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input select-all" id="checkAll">
                                <label class="custom-control-label" for="checkAll"></label>
                            </div>
                        </th>
                        <th>Sipariş No</th>
                        <th>Kategori</th>
                        <th>Bağışçı Bilgisi</th>
                        <th>Bağış Türü</th>
                        <th>Tarih</th>
                        <th>Tutar</th>
                        <th>Durum</th>
                        <th>Yöntem</th>
                        <th class="text-center">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .table-custom thead th { border-top: 0; font-size: 11px; text-uppercase: true; letter-spacing: 0.5px; }
    .table-custom tbody td { vertical-align: middle; font-size: 13px; }
    .badge { padding: 0.5em 0.8em; border-radius: 4px; font-weight: 500; }
    .highlight { background-color: #f8f9ff !important; }
</style>

<script>
$(document).ready(function(){
    // DataTable Başlatma
    var table = $('#bagis_listesi').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "data/gelen_bagislar.php",
            "type": "POST",
            "data": function(d) {
                d.modul_filtre = $('#modul_filtre').val();
                d.kategori_filtre = $('#kategori_filtre').val();
                d.bagisci_adi = $('#bagisci_adi').val();
                d.ulke_filtre = $('#ulke_filtre').val();
                d.tarih_baslangic = $('#tarih_baslangic').val();
                d.tarih_bitis = $('#tarih_bitis').val();
            }
        },
        "order": [[5, "desc"]], // Varsayılan tarih azalan
        "columnDefs": [
            { "targets": [0, 9], "orderable": false },
            { "targets": [7, 8, 9], "className": "text-center" }
        ],
        "language": { "url":"js/Turkish.json" },
        "drawCallback": function() {
            $(".popconfirm").popConfirm(); // Silme onayı plugin tetikleyici
        }
    });

    // Filtreleme Tetikleyicileri
    $('#btnSorgula').on('click', function() { table.draw(); });
    
    // Kampanya seçildiğinde Kategorileri dinamik olarak daralt
    $('#modul_filtre').on('change', function(){
        var mID = $(this).val();
        $('#kategori_filtre option').each(function(){
            var pID = $(this).data('parent');
            if(mID == "" || pID == mID || $(this).val() == "") {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        $('#kategori_filtre').val(""); // Kategori seçimini temizle
        table.draw();
    });

    // Kategori ve Ülke değişiminde otomatik güncelle
    $('#kategori_filtre, #ulke_filtre').on('change', function() { table.draw(); });

    // Sıfırlama Butonu
    $('#btnSifirla').on('click', function() {
        $('#filterForm')[0].reset();
        $('#kategori_filtre option').show();
        table.draw();
    });

    // Toplu Seçim İşlemi
    $(".select-all").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
        if(this.checked) {
            $('input:checkbox').closest('tr').addClass('highlight');
        } else {
            $('input:checkbox').closest('tr').removeClass('highlight');
        }
    });
});
</script>