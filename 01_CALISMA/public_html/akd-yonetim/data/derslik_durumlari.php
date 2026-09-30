<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{ 	
	session_start();
	require_once('../../_class/baglan.php');
	require_once('../../_class/fonksiyon.php');
	require_once('../../language/admin_dil.php');
	
	$request=$_REQUEST;
	$col =array(
		0   =>  'id',
		1   =>  'id',
		2   =>  'sira',
		3   =>  'sehir',
		4   =>  'sinif',
		5   =>  'gun',
		6   =>  'saat',
		7   =>  'durum',
		8   =>  'aktif',
		9   =>  'islem'
	);

	//Search
	$sql ="SELECT d.*, k.adi as kategori_adi, p.baslik as program_baslik 
		   FROM derslik_durumlari d 
		   LEFT JOIN derslik_kategorileri k ON d.kategori_id = k.id 
		   LEFT JOIN programlar p ON d.program_id = p.id 
		   WHERE d.dil = '{$_SESSION['admin_dil']}'";
	if(!empty($request['search']['value'])){
		$sql.=" AND (d.id LIKE '".$request['search']['value']."%' ";
		$sql.=" OR d.sehir LIKE '".$request['search']['value']."%' ";
		$sql.=" OR d.sinif LIKE '".$request['search']['value']."%' ";
		$sql.=" OR k.adi LIKE '".$request['search']['value']."%' ";
		$sql.=" OR p.baslik LIKE '".$request['search']['value']."%' ";
		$sql.=" OR d.gun LIKE '".$request['search']['value']."%' ";
		$sql.=" OR d.saat LIKE '".$request['search']['value']."%' )";
	}
    $totalFilter = $db->query($sql)->rowCount();
	$totalData = $db->query($sql)->rowCount();
	
	//Order
	$sql.=" ORDER BY ".$col[$request['order'][0]['column']]."   ".$request['order'][0]['dir']."  LIMIT ".$request['start']."  ,".$request['length']."  ";
	$query 	= $db->prepare($sql);
	$query->execute();
	$islem 	= $query->fetchALL(PDO::FETCH_ASSOC);
	$data	= array();
	$say 	= $request['start']+1;
	foreach($islem as $row) 
	{
		// Durum gösterimi
		if($row['durum'] == 0){
			$durum_badge = '<div class="badge badge-outline-success">Boş</div>';
		} else if($row['durum'] == 1){
			$durum_badge = '<div class="badge badge-outline-danger">Dolu</div>';
		} else {
			$durum_badge = '<div class="badge badge-outline-warning">Bekleme Listesi</div>';
		}
		
		if($row['aktif'] == 0){
		  $durum = '<div class="badge badge-outline-danger">Pasif</div>';
		}
		if($row['aktif'] == 1){
		   $durum = '<div class="badge badge-outline-success">Aktif</div>';
		}
		
		$kategoriGoster = !empty($row['kategori_adi']) ? $row['kategori_adi'] : $row['sinif'];
		$programGoster = !empty($row['program_baslik']) ? '<br><small class="text-muted">Program: '.$row['program_baslik'].'</small>' : '';
		
		$subdata=array();
		$subdata[]='<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input"><i class="input-helper"></i></label></div>';
		$subdata[]=$row['id'];
		$subdata[]=$row['sira'];
		$subdata[]='<a href="ders-duzenle/'.$row['id'].'.html" class="renk_baslik" title="Düzenle">'.$row['sehir'].'</a>';
		$subdata[]=$kategoriGoster.$programGoster;
		$subdata[]=$row['gun'];
		$subdata[]=$row['saat'];
		$subdata[]=$durum_badge;
		$subdata[]=$durum;
		$subdata[]='<a href="ders-duzenle/'.$row['id'].'.html" class="btn btn-inverse-primary btn-sm" title="Düzenle"><i class="ti-pencil-alt"></i></a>
					<a href="../_class/yonetim_islem.php?tablo=derslik_durumlari&islem=sil&id='.$row["id"].'" class="btn btn-inverse-danger btn-sm popconfirm" data-message="Bu kaydı silmek istediğinize emin misiniz?" title="Sil"><i class="ti-trash"></i></a>';
		$data[]=$subdata;
	}
	$json_data=array(
		"draw"              		=>  intval($request['draw']),
		"recordsTotal"      	=>  intval($totalData),
		"recordsFiltered"  	=>  intval($totalFilter),
		"data"              		=>  $data
	);
	echo json_encode($json_data);
}
else
{
	die("Erişim engellendi");
}
?>

