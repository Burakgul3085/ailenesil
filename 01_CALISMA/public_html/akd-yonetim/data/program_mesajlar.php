<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{ 	
	session_start();
	require_once('../../_class/baglan.php');
	require_once('../../_class/fonksiyon.php');
	
	$request=$_REQUEST;
	$col =array(
		0   =>  'id',
		1   =>  'program_baslik',
		2   =>  'ad_soyad',
		3   =>  'okul_isletme',
		4   =>  'email',
		5   =>  'telefon',
		6   =>  'konum',
		7   =>  'tarih',
		8   =>  'durum',
		9   =>  'islem'
	);

	//Search
	$sql ="SELECT * FROM okullar_program_talep WHERE 1=1";
	if(!empty($request['search']['value'])){
		$sql.=" AND (id LIKE '".$request['search']['value']."%' ";
		$sql.=" OR program_baslik LIKE '".$request['search']['value']."%' ";
		$sql.=" OR ad_soyad LIKE '".$request['search']['value']."%' ";
		$sql.=" OR okul_isletme LIKE '".$request['search']['value']."%' ";
		$sql.=" OR email LIKE '".$request['search']['value']."%' ";
		$sql.=" OR telefon LIKE '".$request['search']['value']."%' ";
		$sql.=" OR konum LIKE '".$request['search']['value']."%' ";
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
		$subdata[]='<strong>'.$row['program_baslik'].'</strong>';
		$subdata[]=$row['ad_soyad'];
		$subdata[]=$row['okul_isletme'] ? $row['okul_isletme'] : '-';
		$subdata[]=$row['email'];
		$subdata[]=$row['telefon'] ? $row['telefon'] : '-';
		$subdata[]=$row['konum'];
		$subdata[]=cVCLmHLxbS_tarih($row['tarih']);
		$subdata[]=$durum;
		$subdata[]='<a href="" data-role="update" class="btn btn-inverse-primary btn-sm" data-toggle="modal" data-target="#programmesaj-'.$row['id'].'" data-id="'.$row['id'].'" title="Mesajı Oku"><i class="ti-eye"></i></a>
					<a href="../_class/yonetim_islem.php?programmesajsil=ok&id='.$row["id"].'" class="btn btn-inverse-danger btn-sm popconfirm" title="Sil"><i class="ti-trash"></i></a>
					<div class="modal fade" id="programmesaj-'.$row['id'].'" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header p-2 pl-2">
									<h5 class="modal-title" id="ModalLabel">'.$row["ad_soyad"].' - Program Talebi</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body text-left">
									<div class="row mb-3">
										<div class="col-md-6">
											<p class="mb-2"><strong>Program:</strong> '.$row["program_baslik"].'</p>
											<p class="mb-2"><strong>Ad Soyad:</strong> '.$row["ad_soyad"].'</p>
											<p class="mb-2"><strong>Okul/İşletme:</strong> '.($row["okul_isletme"] ? $row["okul_isletme"] : '-').'</p>
										</div>
										<div class="col-md-6">
											<p class="mb-2"><strong>E-posta:</strong> '.$row["email"].'</p>
											<p class="mb-2"><strong>Telefon:</strong> '.($row["telefon"] ? $row["telefon"] : '-').'</p>
											<p class="mb-2"><strong>Konum:</strong> '.$row["konum"].'</p>
										</div>
									</div>
									<p class="mb-3">
										IP Adresi: <strong>'.$row["ip"].'</strong> &nbsp; Tarih: <strong>'.cVCLmHLxbS_tarih_panel($row["tarih"]).'</strong>
									</p>
									'.($row["mesaj"] ? '<p class="mb-0"><strong>Mesaj:</strong><br>'.$row["mesaj"].'</p>' : '').'
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

