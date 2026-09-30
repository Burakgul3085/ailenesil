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
		2   =>  'rh.baslik',
		3   =>  'r.isim',
		4   =>  'r.telefon',
		5   =>  'r.email',
		6   =>  'r.tarih',
		7   =>  'r.saat',
		8   =>  'r.durum',
		9   =>  'islem'
	);

	//Search
	$sql ="SELECT r.*, rh.baslik as hizmet_baslik 
	       FROM randevular r 
	       LEFT JOIN randevu_hizmetler rh ON r.hizmet_id = rh.id 
	       WHERE 1=1";
	
	// Hizmet filtresi
	if(isset($request['hizmet_filtre']) && !empty($request['hizmet_filtre'])){
		$sql.=" AND r.hizmet_id = '".$request['hizmet_filtre']."'";
	}
	
	// Durum filtresi
	if(isset($request['durum_filtre']) && $request['durum_filtre'] !== ''){
		$sql.=" AND r.durum = '".$request['durum_filtre']."'";
	}
	
	// Tarih başlangıç filtresi
	if(isset($request['tarih_baslangic']) && !empty($request['tarih_baslangic'])){
		$sql.=" AND r.tarih >= '".$request['tarih_baslangic']."'";
	}
	
	// Tarih bitiş filtresi
	if(isset($request['tarih_bitis']) && !empty($request['tarih_bitis'])){
		$sql.=" AND r.tarih <= '".$request['tarih_bitis']."'";
	}
	
	if(!empty($request['search']['value'])){
		$sql.=" AND (r.id LIKE '".$request['search']['value']."%' "; 
		$sql.=" OR r.isim LIKE '".$request['search']['value']."%' ";
		$sql.=" OR r.telefon LIKE '".$request['search']['value']."%' ";
		$sql.=" OR r.email LIKE '".$request['search']['value']."%' ";
		$sql.=" OR rh.baslik LIKE '".$request['search']['value']."%' ";
		$sql.=" OR r.tarih LIKE '".$request['search']['value']."%' )";
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
		  $durum = '<div class="badge badge-outline-warning">Beklemede</div>';
		}
		if($row['durum'] == 1){
		   $durum = '<div class="badge badge-outline-success">Onaylandı</div>';
		}
		if($row['durum'] == 2){
		   $durum = '<div class="badge badge-outline-danger">İptal</div>';
		}
		
		$hizmet_adi = $row['hizmet_baslik'] ?? ($row['hizmet_adi'] ?? 'Hizmet Seçilmedi');
		
		$subdata=array();
		$subdata[]='<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input"><i class="input-helper"></i></label></div>';
		$subdata[]=$row['id'];
		$subdata[]='<span class="badge badge-outline-info">'.$hizmet_adi.'</span>';
		$subdata[]=$row['isim'];
		$subdata[]=$row['telefon'];
		$subdata[]=$row['email'];
		$subdata[]=date('d.m.Y', strtotime($row['tarih']));
		$subdata[]=date('H:i', strtotime($row['saat']));
		$subdata[]=$durum;
		$subdata[] = '
		<a href="randevu-detay/'.$row["id"].'.html" 
		   class="btn btn-primary btn-sm" title="Detay">
		   <i class="ti-eye"></i>
		</a>
		
		<a href="../_class/yonetim_islem.php?randevusil=ok&id='.$row["id"].'" 
		   class="btn btn-danger btn-sm popconfirm" title="Sil">
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

