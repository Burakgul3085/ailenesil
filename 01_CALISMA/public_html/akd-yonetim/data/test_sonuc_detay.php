<?php
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
	session_start();
	require_once('../../_class/baglan.php');

	$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	if(!$id) { echo '<div class="alert alert-danger">Gecersiz ID.</div>'; exit; }

	$stmt = $db->prepare("SELECT * FROM test_sonuclari WHERE id = ?");
	$stmt->execute(array($id));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	if(!$row) { echo '<div class="alert alert-warning">Kayit bulunamadi.</div>'; exit; }

	$tipIsim = array(
		1 => 'Tip 1 - Reformcu (Mukemmeliyetci)',
		2 => 'Tip 2 - Yardimsever',
		3 => 'Tip 3 - Basarici',
		4 => 'Tip 4 - Bireyci (Romantik)',
		5 => 'Tip 5 - Arastirmaci (Gozlemci)',
		6 => 'Tip 6 - Sadik (Sorgulayici)',
		7 => 'Tip 7 - Maceraci (Coskulu)',
		8 => 'Tip 8 - Lider (Meydan Okuyan)',
		9 => 'Tip 9 - Bariscil (Uzlastirici)'
	);

	$tipRenk = array(
		1 => '#4A90D2', 2 => '#E74C3C', 3 => '#F39C12', 4 => '#9B59B6', 5 => '#2ECC71',
		6 => '#3498DB', 7 => '#F1C40F', 8 => '#E67E22', 9 => '#1ABC9C'
	);

	$harfIsim = array(
		'A' => 'A - Reformcu', 'B' => 'B - Maceraci', 'C' => 'C - Bariscil',
		'X' => 'X - Arastirmaci', 'Y' => 'Y - Lider', 'Z' => 'Z - Yardimsever',
		'K' => 'K - Sadik', 'L' => 'L - Basarici', 'M' => 'M - Bireyci'
	);

	$dm = intval($row['dominant_mizac']);
	$tipAdi = isset($tipIsim[$dm]) ? $tipIsim[$dm] : 'Bilinmiyor';
	$renk = isset($tipRenk[$dm]) ? $tipRenk[$dm] : '#999';

	$sonuc = json_decode($row['sonuc_json'], true);
	$picks = isset($sonuc['picks']) ? $sonuc['picks'] : array();
?>
<div class="row">
	<div class="col-md-6">
		<h6 class="mb-3" style="border-bottom:1px solid #eee;padding-bottom:8px;">Kisi Bilgileri</h6>
		<table class="table table-sm">
			<tr><td style="width:140px;"><strong>Veli</strong></td><td><?=htmlspecialchars($row['veli_ad'] ?? '-')?></td></tr>
			<tr><td><strong>Ogrenci</strong></td><td><?=htmlspecialchars($row['ogrenci_ad'] ?? '-')?></td></tr>
			<tr><td><strong>Yas</strong></td><td><?=$row['ogrenci_yas'] ?? '-'?></td></tr>
			<tr><td><strong>Telefon</strong></td><td><?=htmlspecialchars($row['telefon'] ?? '-')?></td></tr>
			<tr><td><strong>E-posta</strong></td><td><?=htmlspecialchars($row['email'] ?? '-')?></td></tr>
			<tr><td><strong>IP Adresi</strong></td><td><?=htmlspecialchars($row['ip_adresi'] ?? '-')?></td></tr>
			<tr><td><strong>Tarih</strong></td><td><?=date('d.m.Y H:i:s', strtotime($row['tarih']))?></td></tr>
		</table>
	</div>
	<div class="col-md-6">
		<h6 class="mb-3" style="border-bottom:1px solid #eee;padding-bottom:8px;">Test Sonucu</h6>
		<div class="text-center mb-3">
			<div style="display:inline-flex;align-items:center;justify-content:center;width:70px;height:70px;border-radius:50%;background:<?=$renk?>;color:#fff;font-size:28px;font-weight:700;"><?=$dm?></div>
		</div>
		<div class="text-center mb-3">
			<span class="badge" style="background:<?=$renk?>;color:#fff;padding:6px 16px;font-size:14px;"><?=$tipAdi?></span>
		</div>
		<?php if(!empty($picks)): ?>
		<h6 class="mt-4 mb-2">Secimler</h6>
		<table class="table table-sm">
			<?php foreach($picks as $i => $p): ?>
			<tr>
				<td style="width:80px;"><strong>Adim <?=$i+1?></strong></td>
				<td><?=isset($harfIsim[$p]) ? $harfIsim[$p] : $p?></td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php endif; ?>
	</div>
</div>
<?php
}
else
{
	die("Erisim engellendi");
}
?>
