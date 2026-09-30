<?php 
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{ 	
	session_start();
	require_once('../../_class/baglan.php');
	require_once('../../_class/fonksiyon.php');
	
	$request = $_REQUEST;
	$col = array(
		0 => 'id',
		1 => 'id',
		2 => 'banka_adi',
		3 => 'iban',
		4 => 'hesap_sahibi',
		5 => 'id'
	);

	$sql = "SELECT * FROM hesaplar";
	if(!empty($request['search']['value'])){
		$sv = $request['search']['value'];
		$sql .= " WHERE (id LIKE '".$sv."%' ";
		$sql .= " OR banka_adi LIKE '".$sv."%' ";
		$sql .= " OR iban LIKE '".$sv."%' ";
		$sql .= " OR hesap_sahibi LIKE '".$sv."%' )";
	}
	$totalFilter = $db->query($sql)->rowCount();
	$totalData = $db->query("SELECT * FROM hesaplar")->rowCount();

	$sql .= " ORDER BY ".$col[$request['order'][0]['column']]." ".$request['order'][0]['dir']."  LIMIT ".$request['start']." ,".$request['length']."  ";
	$query = $db->prepare($sql);
	$query->execute();
	$islem = $query->fetchAll(PDO::FETCH_ASSOC);
	$data = array();
	foreach($islem as $row)
	{
		$logoBtn = '';
		if(!empty($row['logo'])){
			$logoBtn = '<a href="../'.tema.'/uploads/hesaplar/'.$row['logo'].'" class="btn btn-inverse-success btn-sm" title="Logoyu Gör"><i class="ti-image"></i></a>';
		}else{
			$logoBtn = '<a href="javascript:void(0)" class="btn btn-inverse-danger btn-sm" title="Logo Yok"><i class="ti-image"></i></a>';
		}
		
		$subdata = array();
		$subdata[] = '<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input"><i class="input-helper"></i></label></div>';
		$subdata[] = $row['id'];
		$subdata[] = htmlspecialchars($row['banka_adi']);
		$subdata[] = htmlspecialchars($row['iban']);
		$subdata[] = htmlspecialchars($row['hesap_sahibi']);
		$subdata[] = $logoBtn.'
					<button type="button" class="btn btn-inverse-primary btn-sm hesap-duzenle" data-id="'.$row['id'].'" data-banka="'.htmlspecialchars($row['banka_adi']).'" data-iban="'.htmlspecialchars($row['iban']).'" data-hesap_sahibi="'.htmlspecialchars($row['hesap_sahibi']).'" data-hesap_no="'.htmlspecialchars($row['hesap_no']).'" data-sube="'.htmlspecialchars($row['sube']).'" data-para_birimi="'.htmlspecialchars(@$row['para_birimi'] ?: 'TRY').'" data-swift_kodu="'.htmlspecialchars(@$row['swift_kodu'] ?: '').'" title="Düzenle"><i class="ti-pencil-alt"></i></button>
					<form method="post" action="../_class/yonetim_islem.php" style="display:inline-block;">
						<input type="hidden" name="id" value="'.$row['id'].'" />
						<button type="submit" name="hesap_sil" class="btn btn-inverse-danger btn-sm popconfirm" data-message="Bu kaydı silmek istediğinize emin misiniz?" title="Sil"><i class="ti-trash"></i></button>
					</form>';
		$data[] = $subdata;
	}

	$json_data = array(
		"draw" => intval($request['draw']),
		"recordsTotal" => intval($totalData),
		"recordsFiltered" => intval($totalFilter),
		"data" => $data
	);
	echo json_encode($json_data);
}
else
{
	die("Erişim engellendi");
}
?>


