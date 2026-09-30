<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{ 	
	session_start();
	require_once('../../_class/baglan.php');
	require_once('../../_class/fonksiyon.php');

	$request=$_REQUEST;
	$col =array(
		0   =>  'id',
		1   =>  'id',
		2   =>  'adi',
		3   =>  'durum',
		4   =>  'islem'
	);

	//Search
	$sql ="SELECT * FROM bagis_turleri WHERE 1=1";
	if(!empty($request['search']['value'])){
		$searchValue = addslashes($request['search']['value']);
		$sql.=" AND (id LIKE '".$searchValue."%' ";
		$sql.=" OR adi LIKE '%".$searchValue."%' ";
		$sql.=" OR tarih LIKE '".$searchValue."%' )";
	}
    $totalFilter = $db->query($sql)->rowCount();
	$totalData = $db->query($sql)->rowCount();
	
	//Order
	if(isset($request['order'][0]['column']) && isset($col[$request['order'][0]['column']])) {
		$orderColumn = $col[$request['order'][0]['column']];
		$orderDir = isset($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'ASC';
	} else {
		$orderColumn = 'id';
		$orderDir = 'ASC';
	}
	$start = isset($request['start']) ? intval($request['start']) : 0;
	$length = isset($request['length']) ? intval($request['length']) : 10;
	$sql.=" ORDER BY ".$orderColumn." ".$orderDir." LIMIT ".$start." , ".$length." ";
	$query 	= $db->prepare($sql);
	$query->execute();
	$islem 	= $query->fetchALL(PDO::FETCH_ASSOC);
	$data	= array();
	$say 	= isset($request['start']) ? intval($request['start'])+1 : 1;
	foreach($islem as $row) 
	{
		if($row['durum'] == 0){
		  $durum = '<div class="badge badge-outline-danger">Pasif</div>';
		}
		if($row['durum'] == 1){
		   $durum = '<div class="badge badge-outline-success">Aktif</div>';
		}
		
		$subdata=array();
		$subdata[]='<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input"><i class="input-helper"></i></label></div>';
		$subdata[]=$row['id'];
		$subdata[]='<a href="bagis-turu-duzenle/'.$row['id'].'.html" class="renk_baslik" title="Düzenle">'.$row['adi'].'</a>';
		$subdata[]=$durum;
		$subdata[]='<a href="bagis-turu-duzenle/'.$row['id'].'.html" class="btn btn-inverse-primary btn-sm" title="Düzenle"><i class="ti-pencil-alt"></i></a>
					<a href="../_class/yonetim_islem.php?bagistursil=ok&id='.$row["id"].'" class="btn btn-inverse-danger btn-sm popconfirm" title="Sil"><i class="ti-trash"></i></a>';
		$data[]=$subdata;
	}
	$json_data=array(
		"draw"              		=>  intval($request['draw']),
		"recordsTotal"      	=>  intval($totalData),
		"recordsFiltered"   	=>  intval($totalFilter),
		"data"             		=>  $data
	);
	echo json_encode($json_data);
}
else
{
	die("Erişim engellendi");
}
?>

