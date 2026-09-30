<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{ 	
	session_start();
	require_once('../../_class/baglan.php');
	require_once('../../_class/fonksiyon.php');

	$request=$_REQUEST;
	$col =array(
		0   =>  'id',
		1   =>  'resim',
		2   =>  'isim',
		3   =>  'gorev',
		4   =>  'yorum',
		5   =>  'durum',
		6   =>  'islem'
	);

	//Search
	$sql ="SELECT * FROM uzman_gorusleri WHERE dil = '{$_SESSION['admin_dil']}'";
	if(!empty($request['search']['value'])){
		$sql.=" AND (id LIKE '".$request['search']['value']."%' ";
		$sql.=" OR isim LIKE '".$request['search']['value']."%' ";
		$sql.=" OR gorev LIKE '".$request['search']['value']."%' ";
		$sql.=" OR yorum LIKE '".$request['search']['value']."%' )";
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
		if($row['durum'] == 0){
		  $durum = '<div class="badge badge-outline-danger">Pasif</div>';
		}
		if($row['durum'] == 1){
		   $durum = '<div class="badge badge-outline-success">Aktif</div>';
		}
		
		$subdata=array();
		$subdata[]='<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input checkbox"><i class="input-helper"></i></label></div>';
		
		if($row['resim']){
			$subdata[]='<img src="../'.tema.'/uploads/uzmanlar/'.$row['resim'].'" width="50" class="img-circle">';
		} else {
			$subdata[]='<img src="../'.tema.'/assets/img/user.png" width="50" class="img-circle">';
		}
		
		$subdata[]=$row['isim'];
		$subdata[]=$row['gorev'];
		$subdata[]=cVCLmHLxbS_kisa($row['yorum'], 50);
		$subdata[]=$durum;
		$subdata[]='<div class="btn-group" role="group">
						<a href="uzman_gorus_duzenle/'.$row['id'].'.html" class="btn btn-warning btn-sm">
							<i class="icon-pencil"></i>
						</a>
						<a href="../_class/yonetim_islem.php?uzman_gorus_sil=ok&id='.$row['id'].'" 
						   class="btn btn-danger btn-sm popconfirm" 
						   data-message="Bu uzman görüşünü silmek istediğinizden emin misiniz?">
							<i class="icon-trash"></i>
						</a>
					</div>';
		$data[] = $subdata;
		$say++;
	}
	$json_data = array(
		"draw"            => intval( $request['draw'] ),
		"recordsTotal"    => intval( $totalData ),
		"recordsFiltered" => intval( $totalFilter ),
		"data"            => $data
	);
	echo json_encode($json_data);
}
?>
