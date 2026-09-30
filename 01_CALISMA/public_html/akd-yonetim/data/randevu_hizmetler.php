<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{ 	
	session_start();
	require_once('../../_class/baglan.php');
	require_once('../../_class/fonksiyon.php');

	$request=$_REQUEST;
	$col =array(
		0   =>  'r.id',
		1   =>  'r.id',
		2   =>  'r.baslik',
		3   =>  'r.fiyat',
		4   =>  'r.durum',
		5   =>  'islem'
	);

	//Search
	$sql ="SELECT r.* FROM randevu_hizmetler r WHERE r.dil = '".$_SESSION['admin_dil']."'";
	
	if(!empty($request['search']['value'])){
		$sql.=" AND (r.id LIKE '".$request['search']['value']."%' ";
		$sql.=" OR r.baslik LIKE '".$request['search']['value']."%' ";
		$sql.=" OR r.fiyat LIKE '".$request['search']['value']."%' )";
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
		// Durum
		if($row['durum'] == 0){
		  $durum = '<div class="badge badge-outline-danger">Pasif</div>';
		}
		if($row['durum'] == 1){
		   $durum = '<div class="badge badge-outline-success">Aktif</div>';
		}
		
		$subdata=array();
		$subdata[]='<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input"><i class="input-helper"></i></label></div>';
		$subdata[]=$row['id'];
		$subdata[]=$row['baslik'];
		$subdata[]=number_format($row['fiyat'], 2, ',', '.').' TL';
		$subdata[]=$durum;
		$subdata[] = '
		<a href="randevu-hizmet-ekle.html?islem=duzenle&id='.$row["id"].'"  
		   class="btn btn-primary btn-sm">
		   <i class="ti-pencil"></i>
		</a>
		
		<a href="../_class/yonetim_islem.php?randevuhizmetsil=ok&id='.$row["id"].'" 
		   class="btn btn-danger btn-sm popconfirm">
		   <i class="ti-trash"></i>
		</a>
		';
		
		$data[] = $subdata;
	}
	$json_data=array(
		"draw"             		=>  intval($request['draw']),
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

