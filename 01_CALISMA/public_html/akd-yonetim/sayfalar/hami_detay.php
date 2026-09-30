<?php
// 1. HATA RAPORLAMAYI AÇ (Beyaz ekranı engeller, hatayı ekrana basar)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Güvenlik kontrolü
echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;

// Veritabanı bağlantısının varlığını kontrol et
if (!isset($db)) {
    die("Hata: Veritabanı bağlantı değişkeni (\$db) bulunamadı. Lütfen config dosyasını kontrol edin.");
}

$sayfa = @$_GET['sayfa'];
$hami_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$hata_mesaji = "";

// --- HATA GÖSTERİCİ: ID Kontrolü ---
if($hami_id <= 0) {
    $hata_mesaji = "Geçersiz işlem kimliği. Gelen ID: " . htmlspecialchars($_GET['id'] ?? 'Yok');
} else {
    try {
        // Hami bilgilerini getir
$hami_sorgu = $db->prepare("
            SELECT 
                h.*,
                b.ad as bagisci_ad,
                b.email as bagisci_email,
                b.telefon as bagisci_telefon,
                b.adres as bagisci_adres,
                y.ad_soyad as yetim_ad,
                y.dogum_tarihi as yetim_dogum,
                y.cinsiyet as yetim_cinsiyet,
                y.egitim_durumu as yetim_egitim,
                (SELECT COUNT(*) FROM bagis_odeme bo WHERE bo.hamilik_id = h.id AND bo.paytronay = 1) as odenen_ay_sayisi,
                (SELECT SUM(tutar) FROM bagis_odeme bo WHERE bo.hamilik_id = h.id AND bo.paytronay = 1) as toplam_odenen_tutar,
                (SELECT MAX(tarih) FROM bagis_odeme bo WHERE bo.hamilik_id = h.id AND bo.paytronay = 1) as son_odeme_tarihi
            FROM hamilikler h
            JOIN bagis_odeme b ON h.bagisci_id = b.id
            JOIN yetimler y ON h.yetim_id = y.id
            WHERE h.id = ?
        ");
        
        if (!$hami_sorgu->execute([$hami_id])) {
            $errorInfo = $hami_sorgu->errorInfo();
            throw new Exception("Sorgu hatası: " . $errorInfo[2]);
        }

        $hami = $hami_sorgu->fetch(PDO::FETCH_ASSOC);

        // --- HATA GÖSTERİCİ: Veritabanı Kayıt Kontrolü ---
        if(!$hami) {
            $hata_mesaji = "Belirtilen ID (#$hami_id) ile eşleşen bir hami kaydı bulunamadı. Tabloları kontrol edin.";
        }
    } catch (Exception $e) {
        // SQL veya Bağlantı hatasını yakala
        $hata_mesaji = "Veritabanı Hatası: " . $e->getMessage();
    }
}

if($hata_mesaji == "") {
    $SayfaBaslik = 'Hami Detayı - #' . $hami_id;
    
    // Ödeme geçmişini getir
    $odemeler_sorgu = $db->prepare("
        SELECT * FROM bagis_odeme 
        WHERE hamilik_id = ? 
        ORDER BY tarih DESC
    ");
    $odemeler_sorgu->execute([$hami_id]);
    $odemeler = $odemeler_sorgu->fetchAll(PDO::FETCH_ASSOC);

    // Durum renkleri
    $durum_renk = [
        'aktif' => 'success',
        'pasif' => 'secondary', 
        'iptal' => 'danger',
        'beklemede' => 'warning'
    ];

    // Kalan ay sayısı
    $kalan_ay = $hami['secilen_ay_sayisi'] - $hami['odenen_ay_sayisi'];
    if($kalan_ay < 0) $kalan_ay = 0;

    // Yaş hesaplama
    $yas = '';
    if($hami['yetim_dogum']) {
        $dogum = new DateTime($hami['yetim_dogum']);
        $bugun = new DateTime();
        $yas = $dogum->diff($bugun)->y;
    }
} else {
    $SayfaBaslik = "Hata Oluştu";
}
?>

<div class="page-header">
    <div class="page-title">
        <h3><?=$SayfaBaslik;?></h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="anasayfa.html">Anasayfa</a></li>
                <li class="breadcrumb-item"><a href="hamiler.html">Hamiler</a></li>
                <li class="breadcrumb-item active" aria-current="page">Hami Detayı</li>
            </ol>
        </nav>
    </div>
    <div class="page-options">
        <a href="hamiler.html" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
        <button type="button" class="btn btn-warning" onclick="hamiDurumGuncelle(<?=$hami['id'];?>, 'pasif')">
            <i class="fas fa-pause"></i> Pasif Yap
        </button>
        <button type="button" class="btn btn-danger" onclick="hamiDurumGuncelle(<?=$hami['id'];?>, 'iptal')">
            <i class="fas fa-times"></i> İptal Et
        </button>
    </div>
</div>

<?php if($hata_mesaji != ""): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
            <div>
                <h5 class="mb-1 font-weight-bold">Sistem Hatası Yakalandı</h5>
                <span><?=$hata_mesaji;?></span>
                <hr>
                <small>Çözüm İpucu: .htaccess dosyasındaki ID değerinin gelip gelmediğini kontrol edin.</small>
            </div>
        </div>
    </div>
<?php else: ?>

<div class="row">
    <!-- Hami Bilgileri -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user"></i> Hami Bilgileri
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Ad Soyad:</strong></div>
                    <div class="col-sm-8"><?=$hami['bagisci_ad'];?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>E-posta:</strong></div>
                    <div class="col-sm-8">
                        <a href="mailto:<?=$hami['bagisci_email'];?>"><?=$hami['bagisci_email'];?></a>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Telefon:</strong></div>
                    <div class="col-sm-8"><?=$hami['bagisci_telefon'];?></div>
                </div>
                <?php if($hami['bagisci_adres']): ?>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Adres:</strong></div>
                    <div class="col-sm-8"><?=nl2br($hami['bagisci_adres']);?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Yetim Bilgileri -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-child"></i> Yetim Bilgileri
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Ad Soyad:</strong></div>
                    <div class="col-sm-8"><?=$hami['yetim_ad'];?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Doğum Tarihi:</strong></div>
                    <div class="col-sm-8">
                        <?=date('d.m.Y', strtotime($hami['yetim_dogum']));?>
                        <?php if($yas): ?>
                            <span class="text-muted">(<?=$yas;?> yaşında)</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Cinsiyet:</strong></div>
                    <div class="col-sm-8"><?=ucfirst($hami['yetim_cinsiyet']);?></div>
                </div>
                <?php if($hami['yetim_egitim']): ?>
                <div class="row mb-3">
                    <div class="col-sm-4"><strong>Eğitim Durumu:</strong></div>
                    <div class="col-sm-8"><?=$hami['yetim_egitim'];?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Sponsorluk Bilgileri -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-handshake"></i> Sponsorluk Bilgileri
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-primary"><?=$hami['secilen_ay_sayisi'];?></h4>
                            <p class="text-muted mb-0">Toplam Ay</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-success"><?=$hami['odenen_ay_sayisi'];?></h4>
                            <p class="text-muted mb-0">Ödenen Ay</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-warning"><?=$kalan_ay;?></h4>
                            <p class="text-muted mb-0">Kalan Ay</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-info"><?=number_format($hami['toplam_odenen_tutar'] ?? 0, 2, ',', '.');?> ₺</h4>
                            <p class="text-muted mb-0">Toplam Ödenen</p>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="row mb-2">
                            <div class="col-sm-5"><strong>Sponsorluk Tipi:</strong></div>
                            <div class="col-sm-7">
                                <?php if($hami['aylik_odeme_tipi'] == 'tek_seferde'): ?>
                                    <span class="badge badge-info">Tek Seferde</span>
                                <?php else: ?>
                                    <span class="badge badge-primary">Aylık</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-5"><strong>Aylık Tutar:</strong></div>
                            <div class="col-sm-7"><strong><?=number_format($hami['tutar'] ?? 0, 2, ',', '.');?> ₺</strong></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-5"><strong>Toplam Tutar:</strong></div>
                            <div class="col-sm-7"><strong><?=number_format($hami['toplam_odeme_tutari'] ?? 0, 2, ',', '.');?> ₺</strong></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row mb-2">
                            <div class="col-sm-5"><strong>Durum:</strong></div>
                            <div class="col-sm-7">
                                <span class="badge badge-<?=$durum_renk[$hami['durum']] ?? 'secondary';?>">
                                    <?=ucfirst($hami['durum']);?>
                                </span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-5"><strong>Başlangıç Tarihi:</strong></div>
                            <div class="col-sm-7"><?=date('d.m.Y', strtotime($hami['baslangic_tarihi']));?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-5"><strong>Bitiş Tarihi:</strong></div>
                            <div class="col-sm-7">
                                <?php if($hami['bitis_tarihi']): ?>
                                    <?=date('d.m.Y', strtotime($hami['bitis_tarihi']));?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($hami['son_odeme_tarihi']): ?>
                        <div class="row mb-2">
                            <div class="col-sm-5"><strong>Son Ödeme:</strong></div>
                            <div class="col-sm-7"><?=date('d.m.Y', strtotime($hami['son_odeme_tarihi']));?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ödeme Geçmişi -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history"></i> Ödeme Geçmişi (<?=count($odemeler);?> adet)
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if(count($odemeler) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Sipariş No</th>
                                    <th>Tarih</th>
                                    <th>Tutar</th>
                                    <th>Durum</th>
                                    <th>Ödeme Yöntemi</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($odemeler as $odeme): ?>
                                    <?php
                                    $odeme_durum_renk = [
                                        'basarili' => 'success',
                                        'beklemede' => 'warning',
                                        'basarisiz' => 'danger',
                                        'iptal' => 'secondary'
                                    ];
                                    ?>
                                    <tr>
                                        <td><strong><?=$odeme['spno'];?></strong></td>
                                        <td><?=date('d.m.Y H:i', strtotime($odeme['tarih']));?></td>
                                        <td><strong><?=number_format($odeme['tutar'], 2, ',', '.');?> ₺</strong></td>
                                        <td>
                                            <span class="badge badge-<?=$odeme_durum_renk[$odeme['durum']] ?? 'secondary';?>">
                                                <?=ucfirst($odeme['durum']);?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $odeme_yontemleri = [
                                                'paytr' => 'PayTR',
                                                'iyzico' => 'İyzico',
                                                'havale' => 'Havale/EFT',
                                                'kapida_odeme' => 'Kapıda Ödeme'
                                            ];
                                            echo $odeme_yontemleri[$odeme['odeme_yontemi']] ?? $odeme['odeme_yontemi'];
                                            ?>
                                        </td>
                                        <td>
                                            <a href="gelen-bagis-detay.html?id=<?=$odeme['id'];?>" class="btn btn-sm btn-info" title="Detay">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p>Henüz hiç ödeme kaydı bulunmamaktadır.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function hamiDurumGuncelle(hamiId, yeniDurum) {
    if(confirm('Hami durumunu ' + yeniDurum + ' olarak güncellemek istediğinize emin misiniz?')) {
        $.ajax({
            url: '../_class/yonetim_islem.php',
            type: 'POST',
            data: {
                hami_durum_guncelle: 'ok',
                hami_id: hamiId,
                yeni_durum: yeniDurum
            },
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(response.message || 'İşlem sırasında bir hata oluştu.');
                }
            },
            error: function() {
                toastr.error('Bağlantı hatası oluştu.');
            }
        });
    }
}
</script>

<?php endif; ?>