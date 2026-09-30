<?php echo !defined("GUVENLIK") ? die("Erişim Engellendi!.") : null;?>
<?php
require_once('../_class/country_detector.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$Sorgu = $db->prepare("SELECT b.*, bm.adi as kampanya_adi, bk.adi as kategori_adi
                        FROM bagis_odeme b 
                        LEFT JOIN bagis_moduller bm ON b.modul_id = bm.id 
                        LEFT JOIN bagis_kategori bk ON b.kategori_id = bk.id
                        WHERE b.id = ?");
$Sorgu->execute(array($id));

if($Sorgu->rowCount()){
    $Row = $Sorgu->fetch(PDO::FETCH_ASSOC);
    $ulke_kodu = $Row['ulke'] ?? 'TR';
    $ulke_adi = CountryDetector::getCountryName($ulke_kodu);
    $ulke_bayrak = CountryDetector::getFlagEmoji($ulke_kodu);
}else{
    header("Location:".$url."/yonetim/gelen-bagislar.html");
    exit;
}

// Bağış Türü Tespiti
// Bağış tipine göre etiket ve metin belirleme
switch ($Row['bagis_tipi']) {
    case 'yetim_sponsorluk':
        $tur = '<span class="badge badge-primary small">YETİME SPONSOR</span>';
        $typeLabel = 'Yetim Sponsorluğu';
        break;
    case 'tek':
        $tur = '<span class="badge badge-info small">KAMPANYA BAĞIŞ</span>';
        $typeLabel = 'Kampanya Bağışı';
        break;
    case 'normal':
        $tur = '<span class="badge badge-success small">SEPETE ÖDEME</span>';
        $typeLabel = 'Sepete Ödeme';
        break;
    case 'hizli':
        $tur = '<span class="badge badge-secondary small">HIZLI BAĞIŞ</span>';
        $typeLabel = 'Hızlı Bağış';
        break;
    default:
        $tur = '<span class="badge badge-light small">' . strtoupper($Row['bagis_tipi']) . '</span>';
        $typeLabel = 'Diğer Bağış';
        break;
}$typeClass = $isSponsorluk ? 'badge-primary' : 'badge-warning';
$typeIcon = $isSponsorluk ? 'ti-heart' : 'ti-bolt';
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
    .detail-card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); background: #fff; margin-bottom: 1.5rem; }
    .card-header-custom { background: transparent; border-bottom: 1px solid #f0f0f0; padding: 1.25rem; }
    .info-label { font-size: 0.75rem; color: #adb5bd; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 0.25rem; }
    .info-value { font-size: 1rem; font-weight: 600; color: #2d3436; }
    .main-badge { padding: 8px 16px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; }
    .main-badge i { margin-right: 6px; }
    .status-confirmed { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .status-pending { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .price-tag { font-size: 1.5rem; font-weight: 800; color: #4e73df; }
    .border-left-primary { border-left: 5px solid #4e73df !important; }
    .border-left-warning { border-left: 5px solid #f6c23e !important; }
    .border-left-success { border-left: 5px solid #1cc88a !important; }
    .bg-light-soft { background-color: #fcfcfc; }
    .sep-line { border-top: 1px solid #f1f1f1; margin: 15px 0; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="font-weight-bold text-dark mb-1">
            <i class="<?php echo $typeIcon; ?> text-primary mr-2"></i> İşlem Detayı
        </h2>
        <span class="main-badge <?php echo $typeClass; ?>">
            <i class="<?php echo $typeIcon; ?>"></i> <?php echo $typeLabel; ?>
        </span>
    </div>
    <a href="gelen-bagislar.html" class="btn btn-white shadow-sm border rounded-pill px-4">
        <i class="ti-arrow-left mr-2"></i> Listeye Dön
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        
        <?php if($isSponsorluk): ?>
        <div class="card detail-card border-left-primary">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold text-primary">Sponsorluk Planı</h5>
                <span class="badge badge-light border text-uppercase px-3"><?php echo $Row['sponsortip']; ?></span>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-label">Ödeme Periyodu</div>
                        <div class="info-value text-info">Aylık Düzenli</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Sponsorluk Süresi</div>
                        <div class="info-value text-primary"><?php echo !empty($Row['sponsorluk_suresi_ay']) ? $Row['sponsorluk_suresi_ay'].' Ay' : 'Belirtilmedi'; ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Her Ayın Günü</div>
                        <div class="info-value font-weight-bold"><?php echo !empty($Row['odeme_gunu']) ? $Row['odeme_gunu'].'. Günü' : '1. Günü'; ?></div>
                    </div>
                </div>

                <?php if($Row['sponsortip'] !== 'bireysel'): ?>
                <div class="sep-line"></div>
                <div class="row">
                    <div class="col-12">
                        <div class="info-label"><?php echo ($Row['sponsortip'] == 'grup') ? 'Grup Adı' : 'Kurum Ünvanı'; ?></div>
                        <div class="info-value text-info"><?php echo ($Row['sponsortip'] == 'grup') ? $Row['grup_adi'] : $Row['kurum_unvani']; ?></div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if(!empty($Row['sorumlu_ad_soyad'])): ?>
                <div class="mt-4 p-3 bg-light-soft rounded border shadow-sm">
                    <div class="info-label mb-2"><i class="ti-id-badge"></i> Sorumlu Kişi Verileri</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="small text-muted">İsim</div>
                            <div class="font-weight-bold"><?php echo $Row['sorumlu_ad_soyad']; ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="small text-muted">Telefon</div>
                            <div class="font-weight-bold"><?php echo $Row['sorumlu_telefon']; ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="small text-muted">E-Posta</div>
                            <div class="font-weight-bold"><?php echo $Row['sorumlu_email']; ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card detail-card border-left-success">
            <div class="card-header-custom">
                <h5 class="m-0 font-weight-bold text-success">Bağışçı Kimlik Bilgileri</h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="info-label">Ad Soyad</div>
                        <div class="info-value d-flex align-items-center">
                            <?php echo $Row['ad'].' '.$Row['soyad']; ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="info-label">E-Posta Adresi</div>
                        <div class="info-value text-primary"><?php echo $Row['email']; ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Telefon Numarası</div>
                        <div class="info-value"><?php echo !empty($Row['telefon']) ? $Row['telefon'] : '-'; ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Menşei & Ağ Bilgisi</div>
                        <div class="info-value">
                            <?php echo $ulke_bayrak.' '.$ulke_adi; ?> 
                            <span class="badge badge-light border ml-2">IP: <?php echo $Row['ip']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card detail-card shadow-sm">
            <div class="card-header-custom bg-light-soft">
                <h5 class="m-0 font-weight-bold text-dark">Bağış Amacı & Kırılımı</h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="info-label">Ana Modül / Kampanya</div>
                        <div class="info-value"><?php echo $Row['kampanya_adi'] ?? 'Genel Bağış'; ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Kategori Detayı</div>
                        <div class="info-value text-muted"><?php echo $Row['kategori_adi'] ?? 'Tanımsız'; ?></div>
                    </div>
                </div>
                
                <?php if(!empty($Row['sepet'])): ?>
                <div class="table-responsive rounded border">
                    <table class="table table-hover m-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 text-muted small py-3">BAĞIŞ KALEMİ</th>
                                <th class="border-0 text-muted small py-3 text-right">TUTAR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sepet = json_decode($Row['sepet'], true);
                            if(is_array($sepet)) {
                                foreach($sepet as $item) {
                                    echo '<tr>
                                            <td class="font-weight-bold py-3">'.($item['adi'] ?? 'Genel Bağış').'</td>
                                            <td class="text-right py-3"><span class="badge badge-light px-3 py-2">'.number_format($item['tutar'], 2, ',', '.').' '.$Row['para_birimi'].'</span></td>
                                          </tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

   <div class="col-lg-4">
        
        <div class="card detail-card text-center p-4 border-bottom-primary">
            <div class="info-label mb-2">İşlem Özeti</div>
            <div class="price-tag mb-1"><?php echo number_format($Row['tutar'], 2, ',', '.'); ?> <?php echo $Row['para_birimi'] ?? 'TL'; ?></div>
            <div class="mb-3">
                <?php if($Row['paytronay'] == 1): ?>
                    <span class="badge main-badge status-confirmed"><i class="ti-check"></i> Ödeme Başarılı</span>
                <?php else: ?>
                    <span class="badge main-badge status-pending"><i class="ti-time"></i> Onay Bekliyor</span>
                <?php endif; ?>
            </div>
            <div class="sep-line"></div>
            <div class="row text-left mt-2">
                <div class="col-12 mb-2">
                    <small class="text-muted d-block">Sipariş No:</small>
                    <span class="font-weight-bold">#<?php echo $Row['spno']; ?></span>
                </div>
                <div class="col-12">
                    <small class="text-muted d-block">İşlem Tarihi:</small>
                    <span class="font-weight-bold"><?php echo date("d.m.Y - H:i", strtotime($Row['tarih'])); ?></span>
                </div>
            </div>
        </div>

        <?php 
            // Kontrol: Not boş mu? Anonim değil mi? Hediye değil mi?
            $hasNote = !empty(trim($Row['not_bilgisi'] ?? ''));
            $isAnonim = ($Row['anonim_bagis'] == 1);
            $isHediye = ($Row['hediye_bagis'] == 1);

            // Eğer bu üçünden en az biri varsa kartı göster
            if($hasNote || $isAnonim || $isHediye): 
        ?>
        <div class="card detail-card">
            <div class="card-body p-4">
                <h6 class="font-weight-bold mb-3"><i class="ti-gift text-warning mr-2"></i> Ek Seçenekler & Notlar</h6>
                
                <?php if($isAnonim): ?>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">Bağışçı Kimliği:</span>
                    <span class="font-weight-bold text-danger">Gizli (Anonim)</span>
                </div>
                <?php endif; ?>

                <?php if($isHediye): ?>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">Hediye Bağış:</span>
                    <span class="font-weight-bold text-warning">Evet</span>
                </div>
                <div class="mt-2 p-2 bg-warning text-dark rounded small">
                    <i class="ti-user mr-1"></i> <b>Alıcı:</b> <?php echo $Row['hediye_adi']; ?><br>
                    <i class="ti-mobile mr-1"></i> <b>Tel:</b> <?php echo $Row['hediye_telefon']; ?>
                </div>
                <?php endif; ?>

                <?php if($isAnonim || $isHediye): ?><div class="sep-line"></div><?php endif; ?>

                <?php if($hasNote): ?>
                <div class="info-label mb-2">Bağışçı Notu</div>
                <div class="p-3 bg-light rounded italic text-muted" style="font-style: italic; border-left: 3px solid #dee2e6;">
                    "<?php echo nl2br($Row['not_bilgisi']); ?>"
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card detail-card bg-light border-0">
            <div class="card-body p-4 text-center">
                <p class="small text-muted mb-3">Bu kayıt üzerinde işlem yapmak üzeresiniz.</p>
                <a href="../_class/yonetim_islem.php?gelen_bagissil=ok&id=<?php echo $Row['id']; ?>" 
                   class="btn btn-danger btn-block rounded-pill shadow-sm popconfirm">
                    <i class="ti-trash mr-2"></i> Kaydı Tamamen Sil
                </a>
            </div>
        </div>

    </div>