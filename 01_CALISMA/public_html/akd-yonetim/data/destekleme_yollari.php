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
		4   =>  'ikon',
		5   =>  'kisa_aciklama',
		6   =>  'durum',
		7   =>  'islem'
	);

	//Search
	$sql ="SELECT * FROM destekleme_yollari WHERE dil = '{$_SESSION['admin_dil']}'";
	if(!empty($request['search']['value'])){
		$sql.=" AND (id LIKE '".$request['search']['value']."%' ";
		$sql.=" OR baslik LIKE '".$request['search']['value']."%' ";
		$sql.=" OR aciklama LIKE '".$request['search']['value']."%' ";
		$sql.=" OR kisa_aciklama LIKE '".$request['search']['value']."%' )";
	}
    $totalFilter = $db->query($sql)->rowCount();
	$totalData = $db->query("SELECT * FROM destekleme_yollari WHERE dil = '{$_SESSION['admin_dil']}'")->rowCount();
	
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
		
		// İkon veya Resim gösterimi
		$ikonResim = '-';
		if(!empty($row['ikon'])) {
			$ikonResim = '<i class="'.$row['ikon'].' fa-2x text-primary"></i>';
		} elseif(!empty($row['resim'])) {
			$ikonResim = '<a href="../'.tema.'/uploads/destekleme_yollari/'.$row['resim'].'" target="_blank"><img src="../'.tema.'/uploads/destekleme_yollari/'.$row['resim'].'" style="max-width: 50px; max-height: 50px;" class="img-thumbnail"></a>';
		}
		
		$subdata=array();
		$subdata[]='<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input"><i class="input-helper"></i></label></div>';
		$subdata[]=$row['id'];
		$subdata[]=$row['sira'];
		$subdata[]='<a href="destekleme_yollari_ekle/islem/duzenle/id/'.$row['id'].'.html" class="renk_baslik" title="Düzenle">'.$row['baslik'].'</a>';
		$subdata[]=$ikonResim;
		$subdata[]=cVCLmHLxbS_kisa($row['kisa_aciklama'], 50);
		$subdata[]=$durum;
		$subdata[]='<a href="destekleme_yollari_ekle/islem/duzenle/id/'.$row['id'].'.html" class="btn btn-inverse-primary btn-sm" title="Düzenle"><i class="ti-pencil-alt"></i></a>
					<a href="../_class/yonetim_islem.php?tablo=destekleme_yollari&islem=sil&id='.$row["id"].'" class="btn btn-inverse-danger btn-sm popconfirm" data-message="Silmek istediğinize emin misiniz?" title="Sil"><i class="ti-trash"></i></a>';
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

