<?php
define('GUVENLIK', true);
require_once '../../_class/ayar.php';
require_once '../../_class/fonksiyon.php';

$islem = isset($_GET['islem']) ? $_GET['islem'] : '';

// Check if user is logged in and has permission
if (!isset($_SESSION['Yonetim']) || $_SESSION['Yonetim'] != md5(sha1(md5('Admin')))) {
    echo json_encode(['durum' => 'error', 'mesaj' => 'Yetkisiz erişim!']);
    exit;
}

// List all classrooms
if ($islem == 'liste' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $start = $_POST['start'];
    $length = $_POST['length'];
    $search = $_POST['search']['value'];
    
    $where = [];
    $params = [];
    
    // Search filter
    if (!empty($search)) {
        $where[] = "(s.sinif_adi LIKE :search OR seh.sehir_adi LIKE :search2 OR p.baslik LIKE :search3)";
        $params[':search'] = "%$search%";
        $params[':search2'] = "%$search%";
        $params[':search3'] = "%$search%";
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Get total records
    $totalRecords = $db->query("SELECT COUNT(*) as total FROM siniflar")->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get filtered records
    $sql = "SELECT s.*, seh.sehir_adi, p.baslik as program_adi 
            FROM siniflar s 
            LEFT JOIN sehirler seh ON s.sehir_id = seh.id 
            LEFT JOIN programlar p ON s.program_id = p.id 
            $whereClause 
            ORDER BY s.sira ASC 
            LIMIT :start, :length";
    
    $query = $db->prepare($sql);
    
    // Bind parameters
    foreach ($params as $key => $value) {
        $query->bindValue($key, $value);
    }
    
    $query->bindValue(':start', (int)$start, PDO::PARAM_INT);
    $query->bindValue(':length', (int)$length, PDO::PARAM_INT);
    
    $query->execute();
    $records = $query->fetchAll(PDO::FETCH_ASSOC);
    
    // Prepare data for DataTables
    $data = [];
    foreach ($records as $row) {
        $durum = $row['durum'] == 1 ? 
            '<div class="label label-success">Aktif</div>' : 
            '<div class="label label-danger">Pasif</div>';
            
        $islemler = '<div class="btn-group">';
        $islemler .= '<button class="btn btn-xs btn-default btn-edit" data-id="'.$row['id'].'" title="Düzenle"><i class="icon-pencil"></i></button>';
        $islemler .= '<button class="btn btn-xs btn-default btn-delete" data-id="'.$row['id'].'" title="Sil"><i class="icon-trash"></i></button>';
        $islemler .= '</div>';
        
        $data[] = [
            $row['id'],
            $row['sehir_adi'],
            $row['sinif_adi'],
            ucfirst($row['seviye']),
            $row['program_adi'],
            $durum,
            $row['sira'],
            $islemler
        ];
    }
    
    $response = [
        'draw' => intval($_POST['draw']),
        'recordsTotal' => intval($totalRecords),
        'recordsFiltered' => intval($totalRecords),
        'data' => $data
    ];
    
    echo json_encode($response);
    exit;
}

// Get classroom by ID
elseif ($islem == 'getir' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    $query = $db->prepare("SELECT * FROM siniflar WHERE id = :id");
    $query->execute([':id' => $id]);
    $kayit = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($kayit) {
        echo json_encode([
            'durum' => 'success',
            'kayit' => $kayit
        ]);
    } else {
        echo json_encode([
            'durum' => 'error',
            'mesaj' => 'Kayıt bulunamadı!'
        ]);
    }
    exit;
}

// Invalid request
echo json_encode([
    'durum' => 'error',
    'mesaj' => 'Geçersiz istek!'
]);
?>
