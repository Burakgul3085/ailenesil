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
		2 => 'veli_ad',
		3 => 'ogrenci_ad',
		4 => 'ogrenci_yas',
		5 => 'telefon',
		6 => 'email',
		7 => 'dominant_mizac',
		8 => 'tarih',
		9 => 'id'
	);

	// Enneagram tip isimleri
	$tipIsim = array(
		1 => 'Tip 1 - Reformcu',
		2 => 'Tip 2 - Yardimsever',
		3 => 'Tip 3 - Basarici',
		4 => 'Tip 4 - Bireyci',
		5 => 'Tip 5 - Arastirmaci',
		6 => 'Tip 6 - Sadik',
		7 => 'Tip 7 - Maceraci',
		8 => 'Tip 8 - Lider',
		9 => 'Tip 9 - Bariscil'
	);

	$tipRenk = array(
		1 => '#4A90D2',
		2 => '#E74C3C',
		3 => '#F39C12',
		4 => '#9B59B6',
		5 => '#2ECC71',
		6 => '#3498DB',
		7 => '#F1C40F',
		8 => '#E67E22',
		9 => '#1ABC9C'
	);

	$sql = "SELECT * FROM test_sonuclari WHERE 1=1";
	if(!empty($request['search']['value'])){
		$search = $request['search']['value'];
		$sql .= " AND (id LIKE '{$search}%'";
		$sql .= " OR veli_ad LIKE '%{$search}%'";
		$sql .= " OR ogrenci_ad LIKE '%{$search}%'";
		$sql .= " OR telefon LIKE '%{$search}%'";
		$sql .= " OR email LIKE '%{$search}%')";
	}

	$totalData = $db->query("SELECT COUNT(*) FROM test_sonuclari")->fetchColumn();
	$totalFilter = $db->query($sql)->rowCount();

	$orderCol = isset($col[$request['order'][0]['column']]) ? $col[$request['order'][0]['column']] : 'tarih';
	$orderDir = $request['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC';
	$sql .= " ORDER BY {$orderCol} {$orderDir} LIMIT " . intval($request['start']) . ", " . intval($request['length']);

	$query = $db->prepare($sql);
	$query->execute();
	$islem = $query->fetchAll(PDO::FETCH_ASSOC);

	$data = array();
	foreach($islem as $row)
	{
		$dm = intval($row['dominant_mizac']);
		$tipAdi = isset($tipIsim[$dm]) ? $tipIsim[$dm] : 'Bilinmiyor';
		$tipR = isset($tipRenk[$dm]) ? $tipRenk[$dm] : '#999';
		$sonucBadge = '<span class="badge" style="background:'.$tipR.';color:#fff;padding:4px 10px;font-size:12px;">'.$tipAdi.'</span>';

		$tarih = date('d.m.Y H:i', strtotime($row['tarih']));

		$subdata = array();
		$subdata[] = '<div class="form-check mb-0 mt-0"><label class="form-check-label"><input type="checkbox" name="id[]" value="'.$row['id'].'" class="form-check-input checkbox"><i class="input-helper"></i></label></div>';
		$subdata[] = $row['id'];
		$subdata[] = htmlspecialchars($row['veli_ad'] ?? '-');
		$subdata[] = htmlspecialchars($row['ogrenci_ad'] ?? '-');
		$subdata[] = $row['ogrenci_yas'] ?? '-';
		$subdata[] = htmlspecialchars($row['telefon'] ?? '-');
		$subdata[] = htmlspecialchars($row['email'] ?? '-');
		$subdata[] = $sonucBadge;
		$subdata[] = $tarih;
		$subdata[] = '<button onclick="showDetail('.$row['id'].')" class="btn btn-inverse-info btn-sm" title="Detay"><i class="ti-eye"></i></button>
					  <a href="../_class/yonetim_islem.php?test_sonuc_sil=ok&id='.$row['id'].'" class="btn btn-inverse-danger btn-sm popconfirm" title="Sil"><i class="ti-trash"></i></a>';
		$data[] = $subdata;
	}

	echo json_encode(array(
		"draw"            => intval($request['draw']),
		"recordsTotal"    => intval($totalData),
		"recordsFiltered" => intval($totalFilter),
		"data"            => $data
	));
}
else
{
	die("Erisim engellendi");
}
?>
