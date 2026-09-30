<?php
session_start();
require_once('../../_class/baglan.php');

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

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="test_sonuclari_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

fputcsv($output, array('ID', 'Veli', 'Ogrenci', 'Yas', 'Telefon', 'E-posta', 'Sonuc', 'Secimler', 'IP', 'Tarih'), ';');

$stmt = $db->query("SELECT * FROM test_sonuclari ORDER BY tarih DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($rows as $row) {
	$dm = intval($row['dominant_mizac']);
	$tipAdi = isset($tipIsim[$dm]) ? $tipIsim[$dm] : 'Bilinmiyor';

	$sonuc = json_decode($row['sonuc_json'], true);
	$picks = isset($sonuc['picks']) ? implode(', ', $sonuc['picks']) : '';

	fputcsv($output, array(
		$row['id'],
		$row['veli_ad'] ?? '',
		$row['ogrenci_ad'] ?? '',
		$row['ogrenci_yas'] ?? '',
		$row['telefon'] ?? '',
		$row['email'] ?? '',
		$tipAdi,
		$picks,
		$row['ip_adresi'] ?? '',
		date('d.m.Y H:i', strtotime($row['tarih']))
	), ';');
}

fclose($output);
?>
