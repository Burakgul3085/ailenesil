<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{ 	
	session_start();
	require_once('../../_class/baglan.php');
	require_once('../../_class/fonksiyon.php');
	
	$request=$_REQUEST;
	$col =array(
		0   =>  'id',
		1   =>  'isim',
		2   =>  'telefon',
		3   =>  'konu',
		4   =>  'email',
		5   =>  'tarih',
		6   =>  'durum',
		7   =>  'islem'
	);

	//Search
	$sql ="SELECT * FROM mesajlar WHERE 1=1";
	if(!empty($request['search']['value'])){
		$sql.=" AND (id LIKE '".$request['search']['value']."%' ";
		$sql.=" OR isim LIKE '".$request['search']['value']."%' ";
		$sql.=" OR telefon LIKE '".$request['search']['value']."%' ";
		$sql.=" OR konu LIKE '".$request['search']['value']."%' ";
		$sql.=" OR email LIKE '".$request['search']['value']."%' ";
		$sql.=" OR tarih LIKE '".$request['search']['value']."%' )";
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
		  $durum = '<div class="badge badge-outline-danger">Okunmadı</div>';
		}
		if($row['durum'] == 1){
		   $durum = '<div class="badge badge-outline-success">Okundu</div>';
		}
		
		$subdata=array();
		$subdata[]='<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input"><i class="input-helper"></i></label></div>';
		$subdata[]=$row['isim'];
		$subdata[]=$row['telefon'];
		$subdata[]=$row['konu'];
		$subdata[]=$row['email'];
		$subdata[]=cVCLmHLxbS_tarih_panel($row['tarih']);
		$subdata[]=$durum;
		$subdata[]='<a href="" data-role="update" class="btn btn-inverse-primary btn-sm" data-toggle="modal" data-target="#mesaj-'.$row['id'].'" data-id="'.$row['id'].'" title="Mesajı Oku"><i class="ti-eye"></i></a>
					<a href="../_class/yonetim_islem.php?mesajsil=ok&id='.$row["id"].'" class="btn btn-inverse-danger btn-sm popconfirm" title="Sil"><i class="ti-trash"></i></a>
					<div class="modal fade" id="mesaj-'.$row['id'].'" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header p-2 pl-2">
									<h5 class="modal-title" id="ModalLabel">'.$row["isim"].' kişinin mesajı</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body text-left">
									<p class="mb-3">
										IP Adresi: <strong>'.$row["ip"].'</strong> &nbsp; Tarih: <strong>'.cVCLmHLxbS_tarih_panel($row["tarih"]).'</strong>
									</p>
									<p>'.$row["mesaj"].'</p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-light" data-dismiss="modal">Kapat</button>
								</div>
							</div>
						</div>
					</div>';
		$data[]=$subdata;
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