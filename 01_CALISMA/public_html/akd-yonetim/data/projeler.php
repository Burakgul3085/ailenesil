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
		3   =>  'kategori',
		4   =>  'durum',
		5   =>  'islem'
	);

	//Search
	$sql ="SELECT * FROM projeler WHERE dil = '{$_SESSION['admin_dil']}'";
	if(!empty($request['search']['value'])){
		$sql.=" AND (id LIKE '".$request['search']['value']."%' ";
		$sql.=" OR adi LIKE '".$request['search']['value']."%' ";
		$sql.=" OR seo LIKE '".$request['search']['value']."%' )";
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
		$kategori = $db->query("SELECT * FROM proje_kategori WHERE id = '{$row['kategori']}'")->fetch(PDO::FETCH_ASSOC);
		if($row['durum'] == 0){
		  $durum = '<div class="badge badge-outline-danger">Pasif</div>';
		}
		if($row['durum'] == 1){
		   $durum = '<div class="badge badge-outline-success">Aktif</div>';
		}
		
		if($row['kapak'] == "")
		{
			$resim = 'href="javascript:void(0)" class="btn btn-inverse-danger btn-sm" title="Resim Yok"';
		}
		else
		{
			$resim = 'href="../'.tema.'/uploads/projeler/'.$row['kapak'].'" class="btn btn-inverse-success btn-sm" title="Resmi Göster"';
		}		
		
		
		$subdata=array();
		$subdata[]='<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input checkbox"><i class="input-helper"></i></label></div>';
		$subdata[]=$row['id'];
		$subdata[]='<a href="proje-duzenle/'.$row['id'].'.html" class="renk_baslik" title="Düzenle">'.$row['adi'].'</a>';
		$subdata[]=$kategori['adi'];
		$subdata[]=$durum;
		$subdata[]='<a '.$resim.'><i class="ti-image"></i></a>
					<a href="proje-duzenle/'.$row['id'].'.html" class="btn btn-inverse-primary btn-sm"><i class="ti-pencil-alt" title="Düzenle"></i></a>
					<a href="../_class/yonetim_islem.php?projesil=ok&id='.$row["id"].'" class="btn btn-inverse-danger btn-sm popconfirm" data-original-title="" title="Sil"><i class="ti-trash"></i></a>';
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
