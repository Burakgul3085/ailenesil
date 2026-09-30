<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{ 	
	session_start();
	require_once('../../_class/baglan.php');
	require_once('../../_class/fonksiyon.php');
	
	$request=$_REQUEST;
	$col =array(
		0   =>  'id',
		1   =>  'sira',
		2   =>  'adi',
		3   =>  'tc',
		4   =>  'tariha',
		5   =>  'ucret',
		6   =>  'oucret',
		7   =>  'odeme',
		8   =>  'islem'
	);

	//Search
	$sql ="SELECT * FROM aidatlar WHERE 1=1";
	if(!empty($request['search']['value'])){
		$sql.=" AND (id LIKE '".$request['search']['value']."%' ";
		$sql.=" OR adi LIKE '".$request['search']['value']."%' ";
		$sql.=" OR ucret LIKE '".$request['search']['value']."%' ";
		$sql.=" OR oucret LIKE '".$request['search']['value']."%' ";
		$sql.=" OR tc LIKE '".$request['search']['value']."%' )";
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
		if($row['odeme'] == 0){
		  $odeme = '<div class="badge badge-outline-danger">Ödenmedi</div>';
		}
		if($row['odeme'] == 1){
		   $odeme = '<div class="badge badge-outline-success">Ödendi</div>';
		}			
		
		$subdata=array();
		$subdata[]='<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input checkbox"><i class="input-helper"></i></label></div>';
		$subdata[]=$row['id'];
		$subdata[]='<a href="aidat-duzenle/'.$row['id'].'.html" class="renk_baslik" title="Düzenle">'.$row['adi'].'</a>';
		$subdata[]=$row['tc'];
		$subdata[]=cVCLmHLxbS_ay_yil_panel($row['tariha']);
		$subdata[]=($row['ucret'] == true ? $row['ucret']." TL" : '');
		$subdata[]=($row['oucret'] == true ? $row['oucret']." TL" : '');
		$subdata[]=$odeme;
		$subdata[]='<a href="aidat-duzenle/'.$row['id'].'.html" class="btn btn-inverse-primary btn-sm"><i class="ti-pencil-alt" title="Düzenle"></i></a>
					<a href="../_class/yonetim_islem.php?aidatsil=ok&id='.$row["id"].'" class="btn btn-inverse-danger btn-sm popconfirm" data-original-title="" title="Sil"><i class="ti-trash"></i></a>';
		$data[]=$subdata;
	}
	$json_data=array(
		"draw"              		=>  intval($request['draw']),
		"recordsTotal"      	=>  intval($totalData),
		"recordsFiltered"   	=>  intval($totalFilter),
		"data"              		=>  $data
	);
	echo json_encode($json_data);
}
else
{
	die("Erişim engellendi");
}
?>
