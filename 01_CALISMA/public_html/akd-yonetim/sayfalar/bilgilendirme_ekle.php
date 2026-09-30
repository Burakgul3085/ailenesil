<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
// Düzenleme modu kontrolü
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $durum = "duzenle";
    $sorgu = $db->prepare("SELECT * FROM bilgilendirme_kutulari WHERE id = ?");
    $sorgu->execute(array($_GET['id']));
    if($sorgu->rowCount() > 0) {
        $bilgi = $sorgu->fetch(PDO::FETCH_ASSOC);
    } else {
        header("Location: bilgilendirme-listele.html");
        exit;
    }
} else {
    $bilgi = array(
        'id' => 0,
        'baslik' => '',
        'aciklama' => '',
        'ikon' => 'fas fa-info-circle',
        'link' => '',
        'sira' => 0,
        'durum' => 1
    );
}
?>

<div class="page-header">
    <div class="page-title mt-0 mb-0">
        <h3><?php echo $bilgi['id'] > 0 ? 'Bilgilendirme Kutusu Düzenle' : 'Yeni Bilgilendirme Kutusu Ekle'; ?></h3>
        <div class="crumbs">
            <ul id="breadcrumbs" class="breadcrumb">
                <li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
                <li><a href="bilgilendirme-listele.html">Bilgilendirme Yönetimi</a></li>
                <li class="active"><a href="#"><?php echo $bilgi['id'] > 0 ? 'Düzenle' : 'Ekle'; ?></a></li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $bilgi['id']; ?>">
                    
                    <div class="form-group">
                        <label for="sira">Sıra</label>
                        <input type="number" class="form-control form-control-sm" min="0" name="sira" id="sira" value="<?php echo $bilgi['sira'] ? $bilgi['sira'] : '0'; ?>" />
                    </div>
                    
                    <div class="form-group">
                        <label for="baslik">Başlık <i class="icon-info text-info" data-toggle="popover" data-content="Bilgilendirme kutusunun başlığını giriniz." data-trigger="hover" data-original-title="Başlık"></i></label>
                        <input type="text" class="form-control form-control-sm" name="baslik" id="baslik" value="<?php echo htmlspecialchars($bilgi['baslik']); ?>" required />
                    </div>
                    
                    <div class="form-group">
                        <label for="ikon">İkon <i class="icon-info text-info" data-toggle="popover" data-content="Örnek: fas fa-info-circle" data-trigger="hover" data-original-title="İkon"></i></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i id="ikonOnizleme" class="<?php echo $bilgi['ikon']; ?>"></i></span>
                            </div>
                            <input type="text" class="form-control form-control-sm" name="ikon" id="ikon" value="<?php echo htmlspecialchars($bilgi['ikon']); ?>" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-sm" type="button" data-toggle="modal" data-target="#ikonModal">İkon Seç</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="link">Link (Opsiyonel)</label>
                        <input type="url" class="form-control form-control-sm" name="link" id="link" value="<?php echo htmlspecialchars($bilgi['link']); ?>" />
                    </div>
                    
                    <div class="form-group mb-2">
                        <label class="switch">
                            <input type="checkbox" name="durum" id="durum" value="1" <?php echo $bilgi['durum'] == '1' ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                        <label class="d-inline-block" style="line-height: 34px;" for="durum">Durum</label>
                    </div>
                    
                    <div class="form-group">
                        <label for="aciklama">Açıklama</label>
                        <textarea name="aciklama" id="aciklama" class="form-control" rows="4"><?php echo htmlspecialchars($bilgi['aciklama']); ?></textarea>
                    </div>
                    
                    <?php if($bilgi['id'] > 0) { ?>
                    <button type="submit" name="bilgilendirme_guncelle" class="btn btn-success btn-icon-text btn-sm">
                        <i class="mdi mdi-reload btn-icon-prepend"></i>                                                    
                        GÜNCELLE
                    </button>
                    <?php } else { ?>
                    <button type="submit" name="bilgilendirme_ekle" class="btn btn-primary btn-icon-text btn-sm">
                        <i class="mdi mdi-file-check btn-icon-prepend"></i>
                        KAYDET
                    </button>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- İkon Seçme Modalı -->
<div class="modal fade" id="ikonModal" tabindex="-1" role="dialog" aria-labelledby="ikonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ikonModalLabel">İkon Seç</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <input type="text" class="form-control" id="ikonAra" placeholder="İkon ara...">
                    </div>
                    <div class="col-md-12">
                        <div class="icon-list" style="max-height: 400px; overflow-y: auto;">
                            <!-- Font Awesome ikonları buraya JavaScript ile yüklenecek -->
                            <p class="text-center text-muted">İkonlar yükleniyor...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary btn-sm" id="ikonSec">Seç</button>
            </div>
        </div>
    </div>
</div>

<script>
// İkon seçme işlemleri
$(document).ready(function() {
    // İkon modalı açıldığında ikonları yükle
    $('#ikonModal').on('show.bs.modal', function () {
        if($('.icon-list').find('i').length === 0) {
            // Font Awesome ikonlarını yükle
            var icons = [
                'fa-address-book', 'fa-address-card', 'fa-bell', 'fa-bell-slash', 'fa-bookmark', 
                'fa-building', 'fa-calendar', 'fa-calendar-alt', 'fa-calendar-check', 'fa-calendar-minus',
                'fa-calendar-plus', 'fa-calendar-times', 'fa-chart-bar', 'fa-chart-line', 'fa-check',
                'fa-check-circle', 'fa-clock', 'fa-comment', 'fa-comment-alt', 'fa-comments',
                'fa-envelope', 'fa-envelope-open', 'fa-exclamation', 'fa-exclamation-circle', 'fa-exclamation-triangle',
                'fa-file', 'fa-file-alt', 'fa-file-archive', 'fa-file-audio', 'fa-file-code',
                'fa-file-excel', 'fa-file-image', 'fa-file-pdf', 'fa-file-powerpoint', 'fa-file-video',
                'fa-file-word', 'fa-folder', 'fa-folder-open', 'fa-heart', 'fa-home',
                'fa-image', 'fa-images', 'fa-info', 'fa-info-circle', 'fa-key',
                'fa-list', 'fa-list-alt', 'fa-list-ol', 'fa-list-ul', 'fa-map-marker-alt',
                'fa-paperclip', 'fa-paste', 'fa-phone', 'fa-phone-alt', 'fa-phone-square',
                'fa-phone-square-alt', 'fa-phone-volume', 'fa-question', 'fa-question-circle', 'fa-search',
                'fa-search-minus', 'fa-search-plus', 'fa-star', 'fa-star-half-alt', 'fa-tag',
                'fa-tags', 'fa-thumbs-down', 'fa-thumbs-up', 'fa-times', 'fa-times-circle',
                'fa-trash', 'fa-trash-alt', 'fa-upload', 'fa-user', 'fa-user-check',
                'fa-user-circle', 'fa-user-edit', 'fa-user-minus', 'fa-user-plus', 'fa-users',
                'fa-video', 'fa-video-slash', 'fa-volume-down', 'fa-volume-mute', 'fa-volume-off',
                'fa-volume-up', 'fa-wifi', 'fa-window-close', 'fa-window-maximize', 'fa-window-minimize',
                'fa-window-restore'
            ];
            
            var html = '';
            $.each(icons, function(index, icon) {
                html += '<div class="icon-item d-inline-block text-center p-2 m-1 border rounded" style="width: 80px; cursor: pointer;" data-icon="fas ' + icon + '">';
                html += '<i class="fas ' + icon + ' fa-2x mb-1"></i><br>';
                html += '<small class="text-muted">' + icon + '</small>';
                html += '</div>';
            });
            
            $('.icon-list').html(html);
            
            // İkon arama
            $('#ikonAra').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('.icon-item').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
            
            // İkon seçme
            $('.icon-item').on('click', function() {
                $('.icon-item').removeClass('bg-primary text-white');
                $(this).addClass('bg-primary text-white');
                $('#ikonSec').data('icon', $(this).data('icon'));
            });
        }
    });
    
    // Seçilen ikonu forma ekle
    $('#ikonSec').on('click', function() {
        var selectedIcon = $(this).data('icon');
        if(selectedIcon) {
            $('#ikon').val(selectedIcon);
            $('#ikonOnizleme').attr('class', selectedIcon);
            $('#ikonModal').modal('hide');
        } else {
            alert('Lütfen bir ikon seçiniz.');
        }
    });
    
    // Form gönderimini kontrol et
    $('form').on('submit', function(e) {
        var baslik = $('#baslik').val().trim();
        if(baslik === '') {
            e.preventDefault();
            alert('Lütfen başlık alanını doldurunuz.');
            $('#baslik').focus();
            return false;
        }
        
        var ikon = $('#ikon').val().trim();
        if(ikon === '') {
            e.preventDefault();
            alert('Lütfen bir ikon seçiniz.');
            $('#ikon').focus();
            return false;
        }
        
        return true;
    });
});
</script>

<?php 
// Başarı/uyarı mesajları
cVCLmHLxbS_mesaj("bilgilendirme_ekle", 1, "yes", "Başarıyla eklendi.");
cVCLmHLxbS_mesaj("bilgilendirme_ekle", 2, "no", "Hata oluştu tekrar deneyiniz!");
cVCLmHLxbS_mesaj("bilgilendirme_guncelle", 1, "yes", "Başarıyla güncellendi.");
cVCLmHLxbS_mesaj("bilgilendirme_guncelle", 2, "no", "Hata oluştu tekrar deneyiniz!");
?>
          