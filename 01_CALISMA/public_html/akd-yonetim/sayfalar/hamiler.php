<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
$sayfa = @$_GET['sayfa'];
$SayfaBaslik = 'Hamiler Listesi';

// Arama ve filtreleme
$arama = isset($_GET['arama']) ? $_GET['arama'] : '';
$durum = isset($_GET['durum']) ? $_GET['durum'] : '';

// Sayfalama
$limit = 20;
$sayfa_numarasi = isset($_GET['sayfa_numarasi']) && is_numeric($_GET['sayfa_numarasi']) ? (int)$_GET['sayfa_numarasi'] : 1;
$baslangic = ($sayfa_numarasi - 1) * $limit;

// WHERE koşulları
$where_kosullari = [];
$params = [];

// Arama kriterini bagis_odeme (b) tablosundaki isimlere göre yapıyoruz
if(!empty($arama)) {
    $where_kosullari[] = "(b.ad LIKE ? OR y.ad_soyad LIKE ?)";
    $params[] = "%{$arama}%";
    $params[] = "%{$arama}%";
}

if(!empty($durum)) {
    $where_kosullari[] = "h.durum = ?";
    $params[] = $durum;
}

$where_sql = !empty($where_kosullari) ? "WHERE " . implode(" AND ", $where_kosullari) : "";

// Toplam kayıt sayısı - bagis_odeme ile JOIN güncellendi
$toplam_sorgu = $db->prepare("
    SELECT COUNT(*) as toplam 
    FROM hamilikler h
    JOIN bagis_odeme b ON h.bagisci_id = b.id
    JOIN yetimler y ON h.yetim_id = y.id
    {$where_sql}
");
$toplam_sorgu->execute($params);
$toplam_kayit = $toplam_sorgu->fetch(PDO::FETCH_ASSOC)['toplam'];
$toplam_sayfa = ceil($toplam_kayit / $limit);

// Hamileri getir - ID eşleştirmesi bagis_odeme (b) tablosuna bağlandı
$hamiler_sorgu = $db->prepare("
    SELECT 
        h.*,
        b.ad as bagisci_ad,
        b.email as bagisci_email,
        b.telefon as bagisci_telefon,
        y.ad_soyad as yetim_ad,
        y.dogum_tarihi as yetim_dogum,
        y.cinsiyet as yetim_cinsiyet,
        (SELECT COUNT(*) FROM bagis_odeme bo WHERE bo.hamilik_id = h.id AND bo.paytronay = 1) as odenen_ay_sayisi,
        (SELECT MAX(tarih) FROM bagis_odeme bo WHERE bo.hamilik_id = h.id AND bo.paytronay = 1) as son_odeme_tarihi
    FROM hamilikler h
    JOIN bagis_odeme b ON h.bagisci_id = b.id
    JOIN yetimler y ON h.yetim_id = y.id
    {$where_sql}
    ORDER BY h.id DESC
    LIMIT {$baslangic}, {$limit}
");
$hamiler_sorgu->execute($params);
$hamiler = $hamiler_sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .ui-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: all 0.3s ease; }
    .table thead th { background-color: #f8f9fa; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; border: none; color: #6c757d; }
    .table td { vertical-align: middle; border-top: 1px solid #f1f1f1; }
    .badge-soft-success { background-color: #d1f7e9; color: #1aae6f; border-radius: 8px; padding: 6px 12px; font-weight: 600; }
    .badge-soft-danger { background-color: #fee2e2; color: #ef4444; border-radius: 8px; padding: 6px 12px; font-weight: 600; }
    .badge-soft-warning { background-color: #fef3c7; color: #d97706; border-radius: 8px; padding: 6px 12px; font-weight: 600; }
    .badge-soft-secondary { background-color: #e5e7eb; color: #4b5563; border-radius: 8px; padding: 6px 12px; font-weight: 600; }
    .btn-action { width: 35px; height: 35px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; border: none; }
    .donor-info strong { color: #2d3748; font-size: 0.95rem; }
    .payment-progress { width: 100px; }
    .progress { height: 6px; border-radius: 10px; background-color: #edf2f7; }
</style>

<div class="page-header d-md-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="font-weight-bold mb-1">Hamiler ve Sponsorluklar</h3>
        <p class="text-muted small">Toplam <?=$toplam_kayit;?> aktif hamilik kaydı yönetiliyor.</p>
    </div>
</div>

<div class="card ui-card mb-4">
    <div class="card-body">
        <form method="GET">
            <input type="hidden" name="sayfa" value="<?=$sayfa;?>">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label class="small font-weight-bold">Arama</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0 text-muted"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" name="arama" class="form-control border-left-0 shadow-none" placeholder="Bağışçı veya Yetim adına göre ara..." value="<?=htmlspecialchars($arama);?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="small font-weight-bold">Durum Filtresi</label>
                    <select name="durum" class="form-control custom-select shadow-none">
                        <option value="">Tüm Hamilikler</option>
                        <option value="aktif" <?=$durum=='aktif'?'selected':'';?>>Aktif</option>
                        <option value="pasif" <?=$durum=='pasif'?'selected':'';?>>Pasif</option>
                        <option value="iptal" <?=$durum=='iptal'?'selected':'';?>>İptal</option>
                        <option value="beklemede" <?=$durum=='beklemede'?'selected':'';?>>Beklemede</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-block rounded-pill shadow-sm px-4">Uygula</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card ui-card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="pl-4">Kayıt / ID</th>
                    <th>Bağışçı Detayı</th>
                    <th>Eşleşen Yetim</th>
                    <th>Plan</th>
                    <th>Ödeme Durumu</th>
                    <th>Tutar</th>
                    <th>Son İşlem</th>
                    <th class="text-center">Durum</th>
                    <th class="pr-4 text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($hamiler) > 0): ?>
                    <?php foreach($hamiler as $hami): 
                        $status_class = [
                            'aktif' => 'badge-soft-success',
                            'pasif' => 'badge-soft-secondary',
                            'iptal' => 'badge-soft-danger',
                            'beklemede' => 'badge-soft-warning'
                        ];
                        $yuzde = ($hami['secilen_ay_sayisi'] > 0) ? ($hami['odenen_ay_sayisi'] / $hami['secilen_ay_sayisi']) * 100 : 0;
                    ?>
                    <tr>
                        <td class="pl-4">
                            <span class="text-dark font-weight-bold">#<?=$hami['id'];?></span><br>
                            <small class="text-muted"><?=date('d.m.Y', strtotime($hami['baslangic_tarihi']));?></small>
                        </td>
                        <td class="donor-info">
                            <strong><?=$hami['bagisci_ad'];?></strong><br>
                            <small class="text-muted"><i class="far fa-envelope"></i> <?=$hami['bagisci_email'];?></small>
                        </td>
                        <td>
                            <span class="font-weight-bold text-dark"><?=$hami['yetim_ad'];?></span><br>
                            <small class="text-muted text-uppercase"><?=$hami['yetim_cinsiyet'];?> - <?=date('Y') - date('Y', strtotime($hami['yetim_dogum']));?> Yaş</small>
                        </td>
                        <td>
                            <span class="badge badge-light border"><?=($hami['aylik_odeme_tipi'] == 'tek_seferde' ? 'Tek Seferde' : 'Aylık');?></span><br>
                            <small class="text-muted"><?=$hami['secilen_ay_sayisi'];?> Ay Taahhüt</small>
                        </td>
                        <td>
                            <div class="payment-progress">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small font-weight-bold"><?=$hami['odenen_ay_sayisi'];?>/<?=$hami['secilen_ay_sayisi'];?></span>
                                    <span class="small text-muted"><?=round($yuzde);?>%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?=$yuzde;?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-dark font-weight-bold"><?=number_format($hami['tutar'], 2, ',', '.');?> ₺</span><br>
                            <small class="text-muted">Periyodik</small>
                        </td>
                        <td>
                            <small class="text-muted font-weight-bold">
                                <?php if($hami['son_odeme_tarihi']): ?>
                                    <i class="fas fa-check-circle text-success mr-1"></i> <?=date('d.m.Y', strtotime($hami['son_odeme_tarihi']));?>
                                <?php else: ?>
                                    <i class="fas fa-history text-warning mr-1"></i> Ödeme Yok
                                <?php endif; ?>
                            </small>
                        </td>
                        <td class="text-center">
                            <span class="badge <?=$status_class[$hami['durum']] ?? 'badge-soft-secondary';?> text-uppercase">
                                <?=$hami['durum'];?>
                            </span>
                        </td>
                        <td class="pr-4 text-right">
                            <div class="btn-group">
                                <a href="hami-detay/<?=$hami['id'];?>.html" class="btn btn-action bg-light text-info mr-1" title="Görüntüle">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <button type="button" class="btn btn-action bg-light text-warning mr-1" onclick="hamiDurumGuncelle(<?=$hami['id'];?>, 'pasif')" title="Durdur">
                                    <i class="fas fa-pause"></i>
                                </button>
                                <button type="button" class="btn btn-action bg-light text-danger" onclick="hamiDurumGuncelle(<?=$hami['id'];?>, 'iptal')" title="Kapat">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="py-5 text-center text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3"></i><br>
                            Eşleşen kayıt bulunamadı.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if($toplam_sayfa > 1): ?>
    <div class="card-footer bg-white border-0 py-4">
        <nav aria-label="Sayfalama">
            <ul class="pagination pagination-separated justify-content-center">
                <?php if($sayfa_numarasi > 1): ?>
                    <li class="page-item"><a class="page-link shadow-none" href="?sayfa=<?=$sayfa;?>&sayfa_numarasi=<?=($sayfa_numarasi-1);?>&arama=<?=urlencode($arama);?>&durum=<?=urlencode($durum);?>">Geri</a></li>
                <?php endif; ?>
                
                <?php for($i = max(1, $sayfa_numarasi-2); $i <= min($toplam_sayfa, $sayfa_numarasi+2); $i++): ?>
                    <li class="page-item <?=$i==$sayfa_numarasi?'active':'';?>">
                        <a class="page-link shadow-none" href="?sayfa=<?=$sayfa;?>&sayfa_numarasi=<?=$i;?>&arama=<?=urlencode($arama);?>&durum=<?=urlencode($durum);?>"><?=$i;?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if($sayfa_numarasi < $toplam_sayfa): ?>
                    <li class="page-item"><a class="page-link shadow-none" href="?sayfa=<?=$sayfa;?>&sayfa_numarasi=<?=($sayfa_numarasi+1);?>&arama=<?=urlencode($arama);?>&durum=<?=urlencode($durum);?>">İleri</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<script>
function hamiDurumGuncelle(id, yeniDurum) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Hamilik durumu '" + yeniDurum + "' olarak değiştirilecek.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, Güncelle',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../_class/yonetim_islem.php',
                type: 'POST',
                data: {
                    islem: 'hami_durum_guncelle',
                    id: id,
                    durum: yeniDurum
                },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if(result.success) {
                            Swal.fire('Başarılı!', 'Durum güncellendi.', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Hata!', result.message, 'error');
                        }
                    } catch(e) {
                        location.reload();
                    }
                }
            });
        }
    })
}
</script>