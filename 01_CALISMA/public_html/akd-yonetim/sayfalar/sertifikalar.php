<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>

<style>
    .card { border: none; border-radius: 15px; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05); }
    .table-custom thead { background-color: #f8f9fa; }
    .table-custom th { border-top: none; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; color: #6c757d; }
    .badge { padding: 0.5em 0.8em; border-radius: 6px; font-weight: 500; }
    .btn-action { border-radius: 8px; transition: all 0.2s; }
    .btn-action:hover { transform: translateY(-2px); }
    .form-control, .form-select { border-radius: 8px; padding: 0.6rem 1rem; border: 1px solid #dee2e6; }
    .breadcrumb { background: transparent; padding: 0; }
    .modal.show .modal-dialog {
    transform: translate(0, 35%);
    border-bottom: 3px solid rgb(56, 100, 122);
    background: unset !important;
    box-shadow: unset !important;
    transition: margin-top .3s ease,height .3s ease;
    box-sizing: border-box;
}
</style>

<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1">Sertifika Yönetimi</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html" class="text-decoration-none text-muted"><i class="fas fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="index.html" class="text-decoration-none text-muted">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active fw-bold text-primary">Sertifikalar</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary btn-lg shadow-sm" id="btnYeniSertifika">
            <i class="fas fa-plus-circle me-2"></i> Yeni Sertifika Ekle
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Sertifika Bilgisi</th>
                                <th>Yöntem</th>
                                <th>İçerik Özeti</th>
                                <th>Durum</th>
                                <th>Kayıt Tarihi</th>
                                <th class="text-end pe-4">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sertifikalar = $db->query("SELECT * FROM sertifikalar ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
                            foreach($sertifikalar as $sertifika):
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($sertifika['baslik'] ?: 'SMS İçeriği') ?></span>
                                        <small class="text-muted">ID: #<?= $sertifika['id'] ?></small>
                                    </div>
                                </td>
                                <td>
                                    <?php if($sertifika['gonderim_yontemi'] == 'email'): ?>
                                        <span class="badge bg-soft-primary text-primary" style="background-color: #e7f1ff;"><i class="fas fa-envelope me-1"></i> E-posta</span>
                                    <?php else: ?>
                                        <span class="badge bg-soft-success text-success" style="background-color: #e6fffa;"><i class="fas fa-sms me-1"></i> SMS</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <p class="mb-0 text-truncate" style="max-width: 250px;">
                                        <small style="color: #67696d !important;" class="text-secondary"><?= htmlspecialchars($sertifika['aciklama']) ?></small>
                                    </p>
                                </td>
                                <td>
                                    <?php if($sertifika['durum'] == 1): ?>
                                        <span class="badge bg-success text-white"><i class="fas fa-check-circle me-1"></i> Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary"><i class="fas fa-times-circle me-1"></i> Pasif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        <i class="far fa-calendar-alt me-1"></i> <?= date('d.m.Y', strtotime($sertifika['created_at'])) ?>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-light btn-sm btn-action text-info btnDuzenle" 
                                            data-id="<?= $sertifika['id'] ?>"
                                            data-baslik="<?= htmlspecialchars($sertifika['baslik']) ?>"
                                            data-aciklama="<?= htmlspecialchars($sertifika['aciklama']) ?>"
                                            data-gonderim="<?= $sertifika['gonderim_yontemi'] ?>"
                                            data-durum="<?= $sertifika['durum'] ?>">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm btn-action text-danger btnSil" 
                                            data-id="<?= $sertifika['id'] ?>"
                                            data-baslik="<?= htmlspecialchars($sertifika['baslik']) ?>">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sertifikaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <form id="sertifikaForm" method="post" action="../_class/yonetim_islem.php">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalTitle">Sertifika Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="sertifika_id" id="sertifika_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Gönderim Yöntemi</label>
                        <select class="form-select" name="gonderim_yontemi" id="gonderim_yontemi" required onchange="toggleBaslik()">
                            <option value="email">E-posta</option>
                            <option value="sms">SMS</option>
                        </select>
                    </div>

                    <div class="mb-3" id="baslikGroup">
                        <label class="form-label fw-bold small">Sertifika Başlığı</label>
                        <input type="text" class="form-control" name="baslik" id="baslik" placeholder="Örn: Katılım Sertifikası">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Mesaj / Açıklama İçeriği</label>
                        <textarea class="form-control" name="aciklama" id="myTextarea" rows="5" required placeholder="Mesajınızı buraya yazın..."></textarea>
                        <div class="mt-2">
                            <span class="badge bg-light text-dark border pointer" onclick="addTag('{ad}')">{ad}</span>
                            <span class="badge bg-light text-dark border pointer" onclick="addTag('{soyad}')">{soyad}</span>
                            <span class="badge bg-light text-dark border pointer" onclick="addTag('{tutar}')">{tutar}</span>
                        </div>
                    </div>
                    
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="durum" id="durum" checked>
                        <label class="form-check-label fw-bold small" for="durum">Bu sertifika şablonunu aktifleştir</label>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn btn-primary px-4" name="sertifika_kaydet">Değişiklikleri Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleBaslik() {
    const yontem = $('#gonderim_yontemi').val();
    if(yontem === 'sms') {
        $('#baslikGroup').slideUp();
        $('#baslik').prop('required', false);
    } else {
        $('#baslikGroup').slideDown();
        $('#baslik').prop('required', true);
    }
}

function addTag(tag) {
    const textarea = document.getElementById('aciklama');
    textarea.value += tag;
    textarea.focus();
}

$(document).ready(function() {
    // Yeni Ekleme
    $('#btnYeniSertifika').click(function() {
        $('#modalTitle').text('Yeni Sertifika Oluştur');
        $('#sertifikaForm')[0].reset();
        $('#sertifika_id').val('');
        $('#sertifikaModal').modal('show');
        toggleBaslik();
    });
    
    // Düzenleme
    $('.btnDuzenle').click(function() {
        const d = $(this).data();
        $('#modalTitle').text('Sertifikayı Güncelle');
        $('#sertifika_id').val(d.id);
        $('#baslik').val(d.baslik);
        $('#aciklama').val(d.aciklama);
        $('#gonderim_yontemi').val(d.gonderim);
        $('#durum').prop('checked', d.durum == 1);
        
        toggleBaslik();
        $('#sertifikaModal').modal('show');
    });

    // Silme
    $('.btnSil').click(function() {
        const id = $(this).data('id');
        const baslik = $(this).data('baslik');
        if(confirm(baslik + ' silinecek. Emin misiniz?')) {
            window.location.href = '../_class/yonetim_islem.php?sertifika_sil=' + id;
        }
    });
});
</script>