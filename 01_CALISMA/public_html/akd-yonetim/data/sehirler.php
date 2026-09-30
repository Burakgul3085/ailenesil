<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{   
    session_start();
    require_once('../../_class/baglan.php');
    require_once('../../_class/fonksiyon.php');
    require_once('../../language/admin_dil.php');
    
    $request = $_REQUEST;
    $col = array(
        0   =>  'id',
        1   =>  'sira',
        2   =>  'sehir_adi',
        3   =>  'durum',
        4   =>  'islem'
    );

    // Search
    $sql = "SELECT * FROM sehirler WHERE 1=1 AND dil = '{$_SESSION['admin_dil']}'";
    if(!empty($request['search']['value'])){
        $sql.=" AND (sehir_adi LIKE '%".$request['search']['value']."%' ";
        $sql.=" OR id LIKE '%".$request['search']['value']."%' )";
    }
    
    // Total records count
    $totalData = $db->query($sql)->rowCount();
    $totalFilter = $totalData;

    // Order
    $sql.=" ORDER BY ".$col[$request['order'][0]['column']]." ".$request['order'][0]['dir']." ";
    $sql.=" LIMIT ".$request['start']." ,".$request['length']."   ";
    
    $query = $db->prepare($sql);
    $query->execute();
    $data = array();
    
    while($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $subdata = array();
        
        // Durum butonu
        if($row['durum'] == 1) {
            $durum = '<span class="badge badge-outline-success durum-degistir" data-id="'.$row['id'].'" data-tablo="sehirler" data-alan="durum" style="cursor:pointer;">Aktif</span>';
        } else {
            $durum = '<span class="badge badge-outline-danger durum-degistir" data-id="'.$row['id'].'" data-tablo="sehirler" data-alan="durum" style="cursor:pointer;">Pasif</span>';
        }
        
        // İşlem butonları
        $islemler = '
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-primary sehir-duzenle" data-id="'.$row['id'].'" data-sehir="'.$row['sehir_adi'].'" data-sira="'.$row['sira'].'">
                <i class="ti-pencil-alt"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger sehir-sil" data-id="'.$row['id'].'">
                <i class="ti-trash"></i>
            </button>
        </div>';
        
        $subdata[] = $row['sira'];
        $subdata[] = $row['sehir_adi'];
        $subdata[] = $durum;
        $subdata[] = $islemler;
        
        $data[] = $subdata;
    }
    
    $json_data = array(
        "draw"              =>  intval($request['draw']),
        "recordsTotal"      =>  intval($totalData),
        "recordsFiltered"   =>  intval($totalFilter),
        "data"              =>  $data
    );
    
    echo json_encode($json_data);
} else {
    die("Erişim engellendi");
}
?>
