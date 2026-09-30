<?php
require_once "../../_class/baglan.php";
require_once "../../_class/fonksiyon.php";

// DataTables için gerekli parametreler
$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);
$searchValue = $_POST['search']['value'] ?? '';
$orderColumn = intval($_POST['order'][0]['column']);
$orderDir = $_POST['order'][0]['dir'] ?? 'desc';

// Sütun isimleri
$columns = ['bo.spno', 'h.id', 'b.ad_soyad', 'y.ad_soyad', 'h.aylik_odeme_tipi', 'h.secilen_ay_sayisi', 'h.tutar', 'h.toplam_odeme_tutari', 'bo.tarih', 'bo.durum']; 
$orderBy = $columns[$orderColumn] ?? 'bo.id';

// WHERE koşulları
$whereConditions = [];
$params = [];

// Sadece sponsor bağışları (yetim_id > 0 ve hamilik_id > 0)
$whereConditions[] = "bo.yetim_id > 0";
$whereConditions[] = "bo.hamilik_id > 0";

// Arama
if(!empty($searchValue)) {
    $whereConditions[] = "(b.ad_soyad LIKE ? OR y.ad_soyad LIKE ? OR bo.spno LIKE ?)";
    $params[] = "%{$searchValue}%";
    $params[] = "%{$searchValue}%";
    $params[] = "%{$searchValue}%";
}

$whereSql = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Toplam kayıt sayısı
$totalQuery = $db->prepare("
    SELECT COUNT(*) as toplam 
    FROM bagis_odeme bo
    JOIN hamilikler h ON bo.hamilik_id = h.id
    JOIN bagiscilar b ON h.bagisci_id = b.id
    JOIN yetimler y ON h.yetim_id = y.id
    {$whereSql}
");
$totalQuery->execute($params);
$totalRecords = $totalQuery->fetch(PDO::FETCH_ASSOC)['toplam'];

// Filtrelenmiş toplam kayıt sayısı
$filteredQuery = $db->prepare("
    SELECT COUNT(*) as toplam 
    FROM bagis_odeme bo
    JOIN hamilikler h ON bo.hamilik_id = h.id
    JOIN bagiscilar b ON h.bagisci_id = b.id
    JOIN yetimler y ON h.yetim_id = y.id
    {$whereSql}
");
$filteredQuery->execute($params);
$filteredRecords = $filteredQuery->fetch(PDO::FETCH_ASSOC)['toplam'];

// Verileri getir
$dataQuery = $db->prepare("
    SELECT 
        bo.id,
        bo.spno,
        bo.tarih,
        bo.tutar,
        bo.durum,
        bo.odeme_yontemi,
        h.id as hamilik_id,
        h.aylik_odeme_tipi,
        h.secilen_ay_sayisi,
        h.tutar as aylik_tutar,
        h.toplam_odeme_tutari,
        b.ad_soyad as bagisci_ad,
        y.ad_soyad as yetim_ad
    FROM bagis_odeme bo
    JOIN hamilikler h ON bo.hamilik_id = h.id
    JOIN bagiscilar b ON h.bagisci_id = b.id
    JOIN yetimler y ON h.yetim_id = y.id
    {$whereSql}
    ORDER BY {$orderBy} {$orderDir}
    LIMIT {$start}, {$length}
");
$dataQuery->execute($params);
$bagislar = $dataQuery->fetchAll(PDO::FETCH_ASSOC);

// Verileri hazırla
$data = [];
foreach($bagislar as $bagis) {
    // Durum badge
    $durum_badge = '';
    switch($bagis['durum']) {
        case 'basarili':
            $durum_badge = '<span class="badge badge-success">Başarılı</span>';
            break;
        case 'beklemede':
            $durum_badge = '<span class="badge badge-warning">Beklemede</span>';
            break;
        case 'hata':
            $durum_badge = '<span class="badge badge-danger">Hata</span>';
            break;
        default:
            $durum_badge = '<span class="badge badge-secondary">' . ucfirst($bagis['durum']) . '</span>';
    }
    
    // Sponsorluk tipi
    $sponsorluk_tip = $bagis['aylik_odeme_tipi'] == 'tek_seferde' ? 
        '<span class="badge badge-info">Tek Seferde</span>' : 
        '<span class="badge badge-primary">Aylık</span>';
    
    // İşlem butonları
    $islem = '<div class="btn-group" role="group">';
    $islem .= '<a href="gelen-bagis-detay.html?id='.$bagis["id"].'" class="btn btn-sm btn-info" title="Detay">';
    $islem .= '<i class="fas fa-eye"></i>';
    $islem .= '</a>';
    $islem .= '<button type="button" class="btn btn-sm btn-danger popconfirm" data-toggle="tooltip" title="Sil" onclick="bagisSil(' . $bagis['id'] . ')">';
    $islem .= '<i class="fas fa-trash"></i>';
    $islem .= '</button>';
    $islem .= '</div>';
    
    $data[] = [
        $bagis['spno'],
        '#' . $bagis['hamilik_id'],
        '<strong>' . htmlspecialchars($bagis['bagisci_ad']) . '</strong>',
        '<strong>' . htmlspecialchars($bagis['yetim_ad']) . '</strong>',
        $sponsorluk_tip,
        $bagis['secilen_ay_sayisi'] . ' Ay',
        number_format($bagis['aylik_tutar'], 2, ',', '.') . ' ₺',
        number_format($bagis['toplam_odeme_tutari'], 2, ',', '.') . ' ₺',
        date('d.m.Y H:i', strtotime($bagis['tarih'])),
        $durum_badge,
        $islem
    ];
}

// JSON çıktı
$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>