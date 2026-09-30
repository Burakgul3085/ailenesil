<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{   
    session_start();
    require_once('../../_class/baglan.php');
    require_once('../../_class/fonksiyon.php');
    
    $request = $_REQUEST;
    $columns = array(
        0 => 'id',
        1 => 'sira',
        2 => 'baslik',
        3 => 'ikon',
        4 => 'link',
        5 => 'durum'
    );

    // Search
    $sql = "SELECT * FROM bilgilendirme_kutulari WHERE dil = '{$_SESSION['admin_dil']}'";
    if(!empty($request['search']['value'])){
        $sql.=" AND (baslik LIKE '%".$request['search']['value']."%' ";
        $sql.=" OR aciklama LIKE '%".$request['search']['value']."%' ";
        $sql.=" OR link LIKE '%".$request['search']['value']."%' )";
    }
    
    $totalFilter = $db->query($sql)->rowCount();
    $totalData = $db->query($sql)->rowCount();
    
    // Order
    if(isset($request['order'][0]['column']) && isset($request['order'][0]['dir'])) {
        $sql.=" ORDER BY ".$columns[$request['order'][0]['column']]." ".$request['order'][0]['dir']." ";
    } else {
        $sql.=" ORDER BY sira ASC, id DESC ";
    }
    
    // Limit
    if($request['length'] != -1) {
        $sql.=" LIMIT ".$request['start'].", ".$request['length'];
    }
    
    $query = $db->prepare($sql);
    $query->execute();
    $list = $query->fetchAll(PDO::FETCH_ASSOC);
    
    $data = array();
    $i = $request['start'] + 1;
    
    foreach($list as $row) {
        $nestedData = array();
        
        // Checkbox
        $nestedData[] = '<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input"><i class="input-helper"></i></label></div>';
        
        // ID
        $nestedData[] = $row['id'];
        
        // Başlık
        $nestedData[] = '<a href="bilgilendirme-duzenle/'.$row['id'].'.html" class="renk_baslik" title="Düzenle">'.$row['baslik'].'</a>';
        
        // İkon
        $nestedData[] = '<i class="'.$row['ikon'].' fa-2x"></i>';
        
        // Link
        $nestedData[] = $row['link'] ? '<a href="'.$row['link'].'" target="_blank">'.substr($row['link'], 0, 30).'...</a>' : '-';
        
        // Durum
        $durum = $row['durum'] == 1 ? 'success' : 'danger';
        $durum_metin = $row['durum'] == 1 ? 'Aktif' : 'Pasif';
        $nestedData[] = '<label class="switch">
            <input type="checkbox" class="durumDegistir" data-id="'.$row['id'].'" '.($row['durum'] == 1 ? 'checked' : '').'>
            <span class="slider round"></span>
        </label>';
        
        // İşlemler
        $nestedData[] = '
            <a href="bilgilendirme-duzenle/'.$row['id'].'.html" class="btn btn-inverse-primary btn-sm" title="Düzenle"><i class="ti-pencil-alt"></i></a>
            <a href="../_class/yonetim_islem.php?bilgilendirme_sil=ok&id='.$row['id'].'" class="btn btn-inverse-danger btn-sm popconfirm" title="Sil"><i class="ti-trash"></i></a>
        ';
        
        $data[] = $nestedData;
        $i++;
    }
    
    $json_data = array(
        "draw"            => intval($request['draw']),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFilter),
        "data"            => $data
    );
    
    echo json_encode($json_data);
}
else {
    die("Erişim engellendi");
}
?>
