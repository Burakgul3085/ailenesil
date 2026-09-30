<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{     
    session_start();
    require_once('../../_class/baglan.php');
    require_once('../../_class/fonksiyon.php');

    $request = $_REQUEST;
    $col = array(
        0   => 'id',
        1   => 'id',
        2   => 'ad_soyad',
        3   => 'dogum_tarihi',
        4   => 'durum',
        5   => 'sponsor_bilgisi',
        6   => 'islem'
    );

    // Temel Sorgu
    $sql = "SELECT y.*, 
                   h.id as hamilik_id,
                   b.ad as sponsor_adi, 
                   h.baslangic_tarihi as sponsor_baslama_tarihi
            FROM yetimler y 
            LEFT JOIN hamilikler h ON y.id = h.yetim_id AND h.durum = 'aktif'
            LEFT JOIN bagis_odeme b ON h.bagisci_id = b.id
            WHERE 1=1";

    // Gelişmiş Arama ve Dashboard Filtreleri
    if(!empty($request['search']['value'])){
        $searchValue = $request['search']['value'];
        
        if($searchValue === 'FILTER_SPONSORLU') {
            // Sadece aktif sponsoru olanlar
            $sql .= " AND h.id IS NOT NULL";
        } else if($searchValue === 'Sponsor Yok') {
            // Sponsor bekleyenler
            $sql .= " AND h.id IS NULL";
        } else {
            // Genel metin araması
            $sql .= " AND (y.ad_soyad LIKE '%".$searchValue."%' OR b.ad LIKE '%".$searchValue."%')";
        }
    }

    $totalData = $db->query($sql)->rowCount();
    $totalFilter = $totalData;
    
    // Sıralama ve Sayfalama
    $sql .= " ORDER BY ".$col[$request['order'][0]['column']]." ".$request['order'][0]['dir']." LIMIT ".$request['start']." ,".$request['length']." ";
    
    $query = $db->prepare($sql);
    $query->execute();
    $islem = $query->fetchAll(PDO::FETCH_ASSOC);
    
    $data = array();
    foreach($islem as $row) 
    {
        // Durum Badge'leri
        $durum = '<div class="badge badge-outline-secondary">Belirsiz</div>';
        if($row['durum'] == 0){
             $durum = '<div class="badge badge-outline-danger">Pasif</div>';
        } else if($row['durum'] == 1){
             $durum = '<div class="badge badge-outline-success">Aktif</div>';
        }

        $subdata = array();
        $subdata[] = '<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input checkbox"><i class="input-helper"></i></label></div>';
        $subdata[] = $row['id'];
        $subdata[] = '<a href="yetim-duzenle/'.$row['id'].'.html" class="renk_baslik" title="Düzenle">'.$row['ad_soyad'].'</a>';
        $subdata[] = !empty($row['dogum_tarihi']) ? date('d.m.Y', strtotime($row['dogum_tarihi'])) : '-';
        $subdata[] = $durum;
        
        // Sponsor Bilgisi Badge'i
        $sponsor_bilgisi = '<span class="badge badge-warning">Sponsor Yok</span>';
        if(!empty($row['hamilik_id'])) {
            $sponsor_bilgisi = '<span class="badge badge-success" title="'.$row['sponsor_adi'].'">'.$row['sponsor_adi'].'</span>';
        }
        $subdata[] = $sponsor_bilgisi;
        
        // İşlem Butonları
        $actions = '<a href="javascript:void(0);" onclick="bildirimGonder('.$row['id'].', \''.addslashes($row['ad_soyad']).'\')" class="btn btn-inverse-warning btn-sm" title="Hamiline Bildirim Gönder"><i class="ti-bell"></i></a> ';
        $actions .= '<a href="javascript:void(0);" onclick="aiRapor('.$row['id'].')" class="btn btn-inverse-info btn-sm" title="AI Rapor Oluştur"><i class="ti-write"></i></a> ';
        $actions .= '<a href="yetim-duzenle/'.$row['id'].'.html" class="btn btn-inverse-primary btn-sm"><i class="ti-pencil-alt" title="Düzenle"></i></a> ';
        $actions .= '<a href="../_class/yonetim_islem.php?yetimsil=ok&id='.$row["id"].'" class="btn btn-inverse-danger btn-sm popconfirm" title="Sil"><i class="ti-trash"></i></a>';
        
        $subdata[] = $actions;
        $data[] = $subdata;
    }

    $json_data = array(
        "draw"            => intval($request['draw']),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFilter),
        "data"            => $data
    );
    echo json_encode($json_data);
}
else
{
    die("Erişim engellendi");
}
?>