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
        3 => 'kategori',
        4 => 'dosya',
        5 => 'dosya_boyut',
        6 => 'durum'
    );

    $sql = "SELECT * FROM dosyalar WHERE dil = '{$_SESSION['admin_dil']}'";
    if(!empty($request['search']['value'])){
        $q = $db->quote('%'.$request['search']['value'].'%');
        $sql .= " AND (baslik LIKE $q OR kategori LIKE $q OR aciklama LIKE $q OR orijinal_ad LIKE $q)";
    }

    $totalFilter = $db->query($sql)->rowCount();
    $totalData   = $db->query($sql)->rowCount();

    if(isset($request['order'][0]['column']) && isset($request['order'][0]['dir'])) {
        $col = $columns[$request['order'][0]['column']] ?? 'sira';
        $dir = $request['order'][0]['dir'] == 'desc' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $col $dir";
    } else {
        $sql .= " ORDER BY sira ASC, id DESC";
    }

    if($request['length'] != -1) {
        $sql .= " LIMIT " . intval($request['start']) . ", " . intval($request['length']);
    }

    $query = $db->prepare($sql);
    $query->execute();
    $list = $query->fetchAll(PDO::FETCH_ASSOC);

    $data = array();
    foreach($list as $row) {
        $nd = array();

        // Checkbox
        $nd[] = '<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input"><i class="input-helper"></i></label></div>';

        // ID
        $nd[] = $row['id'];

        // Başlık
        $nd[] = '<a href="dosya-duzenle/'.$row['id'].'.html" class="renk_baslik" title="Düzenle">'.htmlspecialchars($row['baslik']).'</a>';

        // Kategori
        $nd[] = $row['kategori'] ? '<span class="badge badge-outline-info">'.$row['kategori'].'</span>' : '<span class="text-muted">-</span>';

        // Dosya (indirme linki + ikon)
        if($row['dosya']) {
            $icons = ['PDF'=>'mdi-file-pdf','DOC'=>'mdi-file-word','DOCX'=>'mdi-file-word','XLS'=>'mdi-file-excel','XLSX'=>'mdi-file-excel','PPT'=>'mdi-file-powerpoint','PPTX'=>'mdi-file-powerpoint','ZIP'=>'mdi-zip-box','RAR'=>'mdi-zip-box','TXT'=>'mdi-file-document-outline'];
            $icon = $icons[$row['dosya_tip']] ?? 'mdi-file';
            $nd[] = '<a href="../../uploads/dosyalar/'.htmlspecialchars($row['dosya']).'" target="_blank" class="text-primary" title="İndir">
                        <i class="mdi '.$icon.' mr-1"></i>'.htmlspecialchars($row['orijinal_ad'] ?: $row['dosya']).'
                     </a>';
        } else {
            $nd[] = '<span class="text-muted">-</span>';
        }

        // Boyut
        $nd[] = $row['dosya_boyut'] ? '<small>'.$row['dosya_boyut'].'</small>' : '-';

        // Durum toggle
        $nd[] = '<label class="switch"><input type="checkbox" class="durumDegistir" data-id="'.$row['id'].'" '.($row['durum']==1?'checked':'').'><span class="slider round"></span></label>';

        // İşlemler
        $nd[] = '
            <a href="dosya-duzenle/'.$row['id'].'.html" class="btn btn-inverse-primary btn-sm" title="Düzenle"><i class="ti-pencil-alt"></i></a>
            <a href="../../uploads/dosyalar/'.htmlspecialchars($row['dosya']).'" target="_blank" class="btn btn-inverse-info btn-sm" title="İndir"><i class="ti-download"></i></a>
            <a href="../_class/yonetim_islem.php?dosya_sil=ok&id='.$row['id'].'" class="btn btn-inverse-danger btn-sm popconfirm" title="Sil"><i class="ti-trash"></i></a>
        ';

        $data[] = $nd;
    }

    echo json_encode(array(
        "draw"            => intval($request['draw']),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFilter),
        "data"            => $data
    ));
} else {
    die("Erişim engellendi");
}
?>
