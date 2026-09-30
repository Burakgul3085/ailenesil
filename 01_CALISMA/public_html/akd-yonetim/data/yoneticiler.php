<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{ 	
	require_once('../../_class/baglan.php');
	require_once('../../_class/fonksiyon.php');
	
	$request=$_REQUEST;
	$col =array(
		0   =>  'id',
		1   =>  'id',
		2   =>  'isim',
		3   =>  'kadi',
		4   =>  'son_giris',
		5   =>  'islem'
	);

	//Search
	$sql ="SELECT * FROM kullanici WHERE 1=1";
	if(!empty($request['search']['value'])){
		$sql.=" AND (id LIKE '".$request['search']['value']."%' ";
		$sql.=" OR isim LIKE '".$request['search']['value']."%' ";
		$sql.=" OR kadi LIKE '".$request['search']['value']."%' )";
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
	foreach($islem as $row) {
		
		if($row['resim'] == "")
		{
			$resim = 'href="javascript:void(0)" class="btn btn-inverse-danger btn-sm" title="Resim Yok"';
		}
		else
		{
			$resim = 'href="images/users/'.$row['resim'].'" class="btn btn-inverse-success btn-sm" title="Resmi Göster"';
		}
		
		
		$subdata=array();
		$subdata[]='<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input"><i class="input-helper"></i></label></div>';
		$subdata[]=$say++;
		$subdata[]=$row['isim'];
		$subdata[]=$row['kadi'];
		$subdata[]=cVCLmHLxbS_tarih_panel($row['son_giris']);
		$subdata[]='<a '.$resim.'><i class="ti-image"></i></a>
					<a href="yonetici-duzenle/'.$row['id'].'.html" class="btn btn-inverse-primary btn-sm" title="Düzenle"><i class="ti-pencil-alt"></i></a>
					<a href="../_class/yonetim_islem.php?yoneticisil=ok&id='.$row["id"].'" class="btn btn-inverse-danger btn-sm popconfirm" title="Yönetici Sil"><i class="ti-trash"></i></a>';
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