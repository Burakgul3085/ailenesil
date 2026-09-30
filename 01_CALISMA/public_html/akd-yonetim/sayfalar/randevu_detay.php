<?php


// Randevu ID kontrolü
if(!isset($_GET['id']) || empty($_GET['id'])) {
	header("Location: index.php?s=randevu-listele");
	exit;
}

$id = (int)$_GET['id'];

// Randevu bilgilerini çek
$randevu = $db->prepare("SELECT r.*, h.baslik as hizmet_baslik, h.fiyat as hizmet_fiyat, h.resim as hizmet_resim 
						 FROM randevular r 
						 LEFT JOIN randevu_hizmetler h ON r.hizmet_id = h.id 
						 WHERE r.id = ?");
$randevu->execute(array($id));

if($randevu->rowCount() == 0) {
	header("Location: index.php?s=randevu-listele");
	exit;
}

$r = $randevu->fetch(PDO::FETCH_ASSOC);

// Türkçe tarih fonksiyonu
function turkce_tarih($tarih, $format = 'tam') {
	$aylar = [
		'01' => 'Ocak', '02' => 'Şubat', '03' => 'Mart', '04' => 'Nisan',
		'05' => 'Mayıs', '06' => 'Haziran', '07' => 'Temmuz', '08' => 'Ağustos',
		'09' => 'Eylül', '10' => 'Ekim', '11' => 'Kasım', '12' => 'Aralık'
	];
	
	$gunler = [
		'Monday' => 'Pazartesi', 'Tuesday' => 'Salı', 'Wednesday' => 'Çarşamba',
		'Thursday' => 'Perşembe', 'Friday' => 'Cuma', 'Saturday' => 'Cumartesi', 'Sunday' => 'Pazar'
	];
	
	$timestamp = strtotime($tarih);
	$gun = date('d', $timestamp);
	$ay_no = date('m', $timestamp);
	$yil = date('Y', $timestamp);
	$gun_adi = date('l', $timestamp);
	
	if($format == 'tam') {
		return $gun . ' ' . $aylar[$ay_no] . ' ' . $yil . ', ' . $gunler[$gun_adi];
	} elseif($format == 'ay_yil') {
		return $aylar[$ay_no] . ' ' . $yil;
	} elseif($format == 'gun') {
		return $gun;
	}
	return $tarih;
}

// Durum array
$durum_array = [
	0 => ['text' => 'Beklemede', 'badge' => 'warning', 'icon' => 'ti-time'],
	1 => ['text' => 'Onaylandı', 'badge' => 'success', 'icon' => 'ti-check'],
	2 => ['text' => 'İptal Edildi', 'badge' => 'danger', 'icon' => 'ti-close'],
	3 => ['text' => 'Tamamlandı', 'badge' => 'info', 'icon' => 'ti-flag-alt']
];

$durum_bilgi = isset($durum_array[$r['durum']]) ? $durum_array[$r['durum']] : $durum_array[0];
?>

<!-- Breadcrumb -->
<div class="row">
	<div class="col-lg-12">
		<div class="page-header">
			<h4 class="page-title">Randevu Detayı #<?=$r['id']?></h4>

		</div>
	</div>
</div>
<br/>

<!-- Bildirim Mesajları -->
<?php if(isset($_SESSION['randevu_durum_guncelle'])): ?>
	<?php if($_SESSION['randevu_durum_guncelle'] == 'yes'): ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<strong>Başarılı!</strong> Randevu durumu güncellendi.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	<?php else: ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Hata!</strong> Randevu durumu güncellenirken bir hata oluştu.
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
	<?php endif; ?>
	<?php unset($_SESSION['randevu_durum_guncelle']); ?>
<?php endif; ?>

<div class="row">
	<!-- Sol Kolon: Ana Bilgiler -->
	<div class="col-lg-8">
		
		<!-- Hizmet Bilgileri -->
		<?php if($r['hizmet_id']): ?>
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">
					<i class="ti-briefcase text-primary"></i> Hizmet Bilgileri
				</h4>
			</div>
			<div class="card-body">
				<div class="row align-items-center">
					<?php if($r['hizmet_resim']): ?>
					<div class="col-md-3 text-center">
						<img src="../tema/genel/uploads/randevu/<?=$r['hizmet_resim']?>" 
							 alt="<?=$r['hizmet_baslik']?>" 
							 class="img-fluid rounded shadow-sm" 
							 style="max-height: 150px; object-fit: cover;">
					</div>
					<?php endif; ?>
					
					<div class="<?=$r['hizmet_resim'] ? 'col-md-9' : 'col-md-12'?>">
						<table class="table table-borderless mb-0">
							<tr>
								<td width="150" class="font-weight-semibold text-muted">Hizmet Adı:</td>
								<td><strong><?=$r['hizmet_baslik']?></strong></td>
							</tr>
							<?php if($r['hizmet_fiyat']): ?>
							<tr>
								<td class="font-weight-semibold text-muted">Fiyat:</td>
								<td><span class="badge badge-primary"><?=$r['hizmet_fiyat']?> ₺</span></td>
							</tr>
							<?php endif; ?>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		<!-- Müşteri Bilgileri -->
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">
					<i class="ti-user text-success"></i> Müşteri Bilgileri
				</h4>
			</div>
			<div class="card-body">
				<table class="table table-borderless">
					<tr>
						<td width="150" class="font-weight-semibold text-muted">
							<i class="ti-user"></i> Ad Soyad:
						</td>
						<td><strong><?=strip_tags($r['isim'])?></strong></td>
					</tr>
					<tr>
						<td class="font-weight-semibold text-muted">
							<i class="ti-mobile"></i> Telefon:
						</td>
						<td>
							<a href="tel:<?=strip_tags($r['telefon'])?>" class="text-dark">
								<?=strip_tags($r['telefon'])?>
							</a>
						</td>
					</tr>
					<tr>
						<td class="font-weight-semibold text-muted">
							<i class="ti-email"></i> E-posta:
						</td>
						<td>
							<a href="mailto:<?=strip_tags($r['email'])?>" class="text-primary">
								<?=strip_tags($r['email'])?>
							</a>
						</td>
					</tr>
				</table>
			</div>
		</div>
		
		<!-- Randevu Bilgileri -->
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">
					<i class="ti-calendar text-info"></i> Randevu Bilgileri
				</h4>
			</div>
			<div class="card-body">
				<table class="table table-borderless">
					<tr>
						<td width="150" class="font-weight-semibold text-muted">
							<i class="ti-calendar"></i> Tarih:
						</td>
						<td>
							<span class="badge badge-light-primary px-3 py-2" style="font-size: 14px;">
								<?=turkce_tarih($r['tarih'], 'tam')?>
							</span>
						</td>
					</tr>
					<tr>
						<td class="font-weight-semibold text-muted">
							<i class="ti-time"></i> Saat:
						</td>
						<td>
							<span class="badge badge-light-info px-3 py-2" style="font-size: 14px;">
								<?=date('H:i', strtotime($r['saat']))?>
							</span>
						</td>
					</tr>
					<?php if($r['aciklama']): ?>
					<tr>
						<td class="font-weight-semibold text-muted align-top">
							<i class="ti-comment-alt"></i> Açıklama:
						</td>
						<td>
							<div class="bg-light p-3 rounded">
								<?=$r['aciklama']?>
							</div>
						</td>
					</tr>
					<?php endif; ?>
				</table>
			</div>
		</div>
		
		<!-- Sistem Bilgileri -->
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">
					<i class="ti-info-alt text-secondary"></i> Sistem Bilgileri
				</h4>
			</div>
			<div class="card-body">
				<table class="table table-sm table-borderless">
					<tr>
						<td width="150" class="text-muted">Randevu ID:</td>
						<td><code>#<?=$r['id']?></code></td>
					</tr>
					<tr>
						<td class="text-muted">Oluşturma Tarihi:</td>
						<td><?=date('d.m.Y H:i:s', strtotime($r['olusturma_tarihi']))?></td>
					</tr>
					<?php if($r['ip']): ?>
					<tr>
						<td class="text-muted">IP Adresi:</td>
						<td><code><?=$r['ip']?></code></td>
					</tr>
					<?php endif; ?>
				</table>
			</div>
		</div>
		
	</div>
	
	<!-- Sağ Kolon: Durum ve İşlemler -->
	<div class="col-lg-4">
		
		<!-- Durum Kartı -->
		<div class="card">
			<div class="card-header bg-<?=$durum_bilgi['badge']?> text-white">
				<h4 class="card-title mb-0">
					<i class="<?=$durum_bilgi['icon']?>"></i> Randevu Durumu
				</h4>
			</div>
			<div class="card-body">
				<div class="text-center py-3">
					<div class="mb-3">
						<span class="badge badge-<?=$durum_bilgi['badge']?> badge-lg px-4 py-3" style="font-size: 16px;">
							<?=$durum_bilgi['text']?>
						</span>
					</div>
					
					<form action="../_class/yonetim_islem.php" method="POST" class="mt-4">
						<input type="hidden" name="id" value="<?=$r['id']?>">
						<input type="hidden" name="randevu_durum_guncelle" value="ok">
						
						<div class="form-group">
							<label class="font-weight-semibold">Durum Değiştir:</label>
							<select name="durum" class="form-control">
								<?php foreach($durum_array as $key => $val): ?>
								<option value="<?=$key?>" <?=$r['durum'] == $key ? 'selected' : ''?>>
									<?=$val['text']?>
								</option>
								<?php endforeach; ?>
							</select>
						</div>
						
						<button type="submit" class="btn btn-primary btn-block">
							<i class="ti-save"></i> Durumu Güncelle
						</button>
					</form>
				</div>
			</div>
		</div>
		
		<!-- Hızlı İşlemler -->
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">
					<i class="ti-settings"></i> Hızlı İşlemler
				</h4>
			</div>
			<div class="card-body">
				<div class="d-grid gap-2">
					<a href="mailto:<?=strip_tags($r['email'])?>" class="btn btn-outline-primary btn-block mb-2">
						<i class="ti-email"></i> E-posta Gönder
					</a>
					
					<a href="tel:<?=strip_tags($r['telefon'])?>" class="btn btn-outline-success btn-block mb-2">
						<i class="ti-mobile"></i> Telefon Et
					</a>
					
					<a href="randevu-listele.html" class="btn btn-outline-secondary btn-block mb-2">
						<i class="ti-list"></i> Tüm Randevular
					</a>
					
					<hr class="my-3">
					
					<a href="../_class/yonetim_islem.php?randevusil=ok&id=<?=$r['id']?>" 
					   class="btn btn-danger btn-block popconfirm"
					   data-placement="top"
					   data-title="Randevu silinecek, emin misiniz?"
					   data-content="">
						<i class="ti-trash"></i> Randevuyu Sil
					</a>
				</div>
			</div>
		</div>
		
		<!-- Randevu Özeti -->
		<div class="card bg-light">
			<div class="card-body text-center">
				<div class="mb-2">
					<i class="ti-calendar text-primary" style="font-size: 48px;"></i>
				</div>
				<h4 class="mb-1"><?=turkce_tarih($r['tarih'], 'gun')?></h4>
				<p class="text-muted mb-1"><?=turkce_tarih($r['tarih'], 'ay_yil')?></p>
				<h5 class="text-primary mb-0"><?=date('H:i', strtotime($r['saat']))?></h5>
			</div>
		</div>
		
	</div>
</div>

<style>
.badge-lg {
	font-size: 14px;
	padding: 10px 20px;
}
.badge-light-primary {
	background-color: rgba(98, 89, 202, 0.1);
	color: #6259ca;
}
.badge-light-info {
	background-color: rgba(1, 184, 255, 0.1);
	color: #01b8ff;
}
</style>

