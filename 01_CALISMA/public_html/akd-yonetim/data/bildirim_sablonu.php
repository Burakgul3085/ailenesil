<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{ 	
	session_start();
	require_once('../../_class/baglan.php');
	require_once('../../_class/fonksiyon.php');
	
	$request=$_REQUEST;
	$col =array(
		0   =>  'sablon_adi',
		1   =>  'konu',
		2   =>  'islem'
	);

	//Search
	$sql ="SELECT * FROM bildirim_sablonu";
	if(!empty($request['search']['value'])){
		$sql.=" AND (id LIKE '".$request['search']['value']."%' ";
		$sql.=" OR sablon_adi LIKE '".$request['search']['value']."%' ";
		$sql.=" OR konu LIKE '".$request['search']['value']."%' )";
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
		if($row['konu'] != "")
		{
			$konu = $row['konu'];
		}
		else
		{
			$konu = $row['konu2'];
		}
		$subdata=array();
		$subdata[]=$row['sablon_adi'];
		$subdata[]=$konu;
		$subdata[]='<a href="sablon-duzenle/'.$row['id'].'.html" class="btn btn-inverse-primary btn-sm" title="Düzenle"><i class="ti-pencil-alt"></i> Düzenle</a>';
		$data[]=$subdata;
	}
	$json_data=array(
		"draw"              =>  intval($request['draw']),
		"recordsTotal"      =>  intval($totalData),
		"recordsFiltered"   =>  intval($totalFilter),
		"data"              =>  $data
	);
	echo json_encode($json_data);
}
else
{
	die("Erişim engellendi");
}
?>