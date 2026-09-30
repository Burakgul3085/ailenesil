<?php define("GUVENLIK",true); ?>
<?php
session_start();
require_once('../../_class/baglan.php');
require_once('../../_class/fonksiyon.php');

header('Content-Type: application/json; charset=utf-8');

// Basit yetki kontrolü
if(empty($_SESSION['Yonetim_Id'])){
    echo json_encode([ 'success' => false, 'message' => 'Yetkisiz işlem' ]);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

try {
    if($action === 'toggle_durum'){
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $to = isset($_POST['to']) ? (int) $_POST['to'] : 0;
        if($id <= 0){ throw new Exception('Geçersiz ID'); }
        $upd = $db->prepare("UPDATE menu SET durum = ? WHERE id = ?");
        $ok = $upd->execute([ $to, $id ]);
        echo json_encode([ 'success' => (bool)$ok ]);
        exit;
    }

    if($action === 'bulk_update'){
        $ids = isset($_POST['ids']) ? $_POST['ids'] : [];
        $durum = isset($_POST['durum']) ? (int) $_POST['durum'] : 0;
        if(!is_array($ids) || count($ids) === 0){ throw new Exception('Seçim yok'); }
        $ids = array_map('intval', $ids);
        $in  = implode(',', array_fill(0, count($ids), '?'));
        $params = array_merge([ $durum ], $ids);
        $sql = "UPDATE menu SET durum = ? WHERE id IN ($in)";
        $upd = $db->prepare($sql);
        $ok = $upd->execute($params);
        echo json_encode([ 'success' => (bool)$ok, 'count' => count($ids) ]);
        exit;
    }

    echo json_encode([ 'success' => false, 'message' => 'Bilinmeyen işlem' ]);
} catch (Exception $e){
    echo json_encode([ 'success' => false, 'message' => $e->getMessage() ]);
}


