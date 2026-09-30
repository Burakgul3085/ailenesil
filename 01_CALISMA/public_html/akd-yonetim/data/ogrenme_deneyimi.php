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
		3   =>  'baslik',
		4   =>  'program_id',
		5   =>  'kisa_aciklama',
		6   =>  'durum',
		7   =>  'islem'
	);

	//Search
	$sql ="SELECT od.*, p.baslik as program_baslik FROM ogrenme_deneyimi od 
		   LEFT JOIN programlar p ON od.program_id = p.id 
		   WHERE od.dil = '{$_SESSION['admin_dil']}'";
	if(!empty($request['search']['value'])){
		$sql.=" AND (od.id LIKE '".$request['search']['value']."%' ";
		$sql.=" OR od.baslik LIKE '".$request['search']['value']."%' ";
		$sql.=" OR od.aciklama LIKE '".$request['search']['value']."%' ";
		$sql.=" OR od.kisa_aciklama LIKE '".$request['search']['value']."%' ";
		$sql.=" OR p.baslik LIKE '".$request['search']['value']."%' )";
	}
    $totalFilter = $db->query($sql)->rowCount();
	$totalData = $db->query("SELECT * FROM ogrenme_deneyimi WHERE dil = '{$_SESSION['admin_dil']}'")->rowCount();
	
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
		
		if($row['resim'] == "")
		{
			$resim = 'href="javascript:void(0)" class="btn btn-inverse-danger btn-sm" title="Resim Yok"';
		}
		else
		{
			$resim = 'href="../'.tema.'/uploads/ogrenme_deneyimi/'.$row['resim'].'" class="btn btn-inverse-success btn-sm" title="Resmi Göster"';
		}
		
		$program_baslik = !empty($row['program_baslik']) ? $row['program_baslik'] : '-';
		
		$subdata=array();
		$subdata[]='<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input"><i class="input-helper"></i></label></div>';
		$subdata[]=$row['id'];
		$subdata[]=$row['sira'];
		$subdata[]='<a href="ogrenme_deneyimi_ekle/islem/duzenle/id/'.$row['id'].'.html" class="renk_baslik" title="Düzenle">'.$row['baslik'].'</a>';
		$subdata[]=$program_baslik;
		$subdata[]=cVCLmHLxbS_kisa($row['kisa_aciklama'], 50);
		$subdata[]=$durum;
		$subdata[]='<a '.$resim.'><i class="ti-image"></i></a>
					<a href="ogrenme_deneyimi_ekle/islem/duzenle/id/'.$row['id'].'.html" class="btn btn-inverse-primary btn-sm" title="Düzenle"><i class="ti-pencil-alt"></i></a>
					<a href="../_class/yonetim_islem.php?tablo=ogrenme_deneyimi&islem=sil&id='.$row["id"].'" class="btn btn-inverse-danger btn-sm popconfirm" data-message="Silmek istediğinize emin misiniz?" title="Sil"><i class="ti-trash"></i></a>';
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

