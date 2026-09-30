<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
$menubul 	= $db->query("SELECT * FROM menu WHERE menu_url = 'bagis-moduller' OR link = 'bagis-moduller' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
$menubas 	= $db->query("SELECT * FROM menu WHERE id = '{$menubul['menu_ust']}' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
?>
<!-- PAGE SECTİON BAŞLANGIÇ -->
<section class="page-section">
	<div class="bg-white">
		<div class="col-12 p-0 banner">
			<img src="<?php echo tema;?>/uploads/arkaplan/arkaplan22/<?php echo $arkaplan['arkaplan22'];?>" alt="<?=@$dil['txt111'];?>">
			<div class="slide-overlay"></div>
		</div>
		<div class="container banner-fix">
			<div class="row">
				<div class="col-lg-12 z-index-9">
					<ol class="breadcrumb">
						<li><a href="<?php echo $htc['anaurl'];?><?php echo $html;?>"> <i class="fa fa-home"></i> </a></li>						
						<?php if($menubas['menu_isim'] != ""){?>
						<li><a href="<?php echo($menubas['menu_url'] == "0" ? $menubas['link'] : $menubas['menu_url']);?>"><?php echo cVCLmHLxbS_ilkbuyuk($menubas['menu_isim']);?></a></li>
						<?php }?>
						<li><?=@$dil['txt486'];?></li>
					</ol>
				</div>
				
				<div class="col-lg-12 z-index-9">
					<div class="page-content">
						<h2 class="page-title"><?=@$dil['txt486'];?></h2>
						
						<div class="row haberler-box">
							<?php 
							$Sorgu = $db->prepare("SELECT * FROM bagis_moduller WHERE dil = ? AND durum = ? ORDER BY sira ASC");
							$Sorgu->execute(array($_SESSION['k_dil'], 1));
							$islem = $Sorgu->fetchALL(PDO::FETCH_ASSOC);
							?>
							<?php if($Sorgu->rowCount()){ ?>
								<?php foreach ( $islem as $Sonuc ){?>
								<?php
								// Yüzde hesaplama
								$yuzde = 0;
								$kalan_gun = 0;
								if($Sonuc['hedef_tutar'] > 0) {
									$yuzde = round(($Sonuc['toplanan_tutar'] / $Sonuc['hedef_tutar']) * 100, 2);
								}
								
								// Kalan gün hesaplama
								if($Sonuc['bitis_tarihi'] && $Sonuc['bitis_tarihi'] != '0000-00-00') {
									$bugun = new DateTime();
									$bitis = new DateTime($Sonuc['bitis_tarihi']);
									$fark = $bugun->diff($bitis);
									$kalan_gun = $fark->days;
									if($bugun > $bitis) $kalan_gun = 0; // Süre bitmişse
								}
								?>
								<div class="col-lg-4 col-md-6 mb-4">
									<div class="bagis-modul-card">
										<?php if($Sonuc['kapak_resmi']){?>
										<div class="bagis-modul-image">
											<img src="<?php echo tema;?>/uploads/bagis_moduller/<?php echo $Sonuc['kapak_resmi'];?>" alt="<?php echo $Sonuc['adi'];?>">
										</div>
										<?php }?>
										<div class="bagis-modul-content p-3">
											<h3 class="bagis-modul-title"><?php echo $Sonuc['adi'];?></h3>
											<?php if($Sonuc['aciklama']){?>
											<p class="bagis-modul-desc"><?php echo mb_substr(strip_tags($Sonuc['aciklama']), 0, 120);?>...</p>
											<?php }?>
											
											<?php if($Sonuc['hedef_tutar'] > 0){?>
											<div class="bagis-modul-progress mb-3">
												<div class="progress" style="height: 10px;">
													<div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $yuzde;?>%;" aria-valuenow="<?php echo $yuzde;?>" aria-valuemin="0" aria-valuemax="100"></div>
												</div>
												<div class="row mt-2">
													<div class="col-6">
														<small class="text-muted"><?=@$dil['txt487'];?></small><br>
														<strong><?php echo number_format($Sonuc['toplanan_tutar'], 2, ',', '.');?> TL</strong>
													</div>
													<div class="col-6 text-right">
														<small class="text-muted"><?=@$dil['txt488'];?></small><br>
														<strong><?php echo number_format($Sonuc['hedef_tutar'], 2, ',', '.');?> TL</strong>
													</div>
												</div>
												<div class="text-center mt-2">
													<span class="badge badge-info">%<?php echo $yuzde;?> <?=@$dil['txt489'];?></span>
													<?php if($kalan_gun > 0){?>
													<span class="badge badge-warning"><?php echo $kalan_gun;?> <?=@$dil['txt490'];?></span>
													<?php } else if($Sonuc['bitis_tarihi'] && $kalan_gun == 0) {?>
													<span class="badge badge-danger"><?=@$dil['txt491'];?></span>
													<?php }?>
												</div>
											</div>
											<?php }?>
											
											<a href="<?php echo $htc['bagismoduldetayurl'];?>/<?php echo $Sonuc['seo'];?><?php echo $html;?>" class="form-button iletisim-page w-100 text-center">
												<i class="fas fa-hand-holding-heart"></i> <?=@$dil['txt423'];?>
											</a>
										</div>
									</div>
								</div>
								<?php }?>
							<?php } else {?>
								<div class="col-12">
									<div class="alert alert-info">
										<i class="fas fa-info-circle"></i> <?=@$dil['txt492'];?>
									</div>
								</div>
							<?php }?>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- PAGE SECTİON BİTİŞ -->
<style>
.bagis-modul-card {
	border: 1px solid #e0e0e0;
	border-radius: 8px;
	overflow: hidden;
	transition: all 0.3s;
	background: #fff;
	height: 100%;
	display: flex;
	flex-direction: column;
}
.bagis-modul-card:hover {
	box-shadow: 0 5px 15px rgba(0,0,0,0.15);
	transform: translateY(-5px);
}
.bagis-modul-image {
	width: 100%;
	height: 200px;
	overflow: hidden;
}
.bagis-modul-image img {
	width: 100%;
	height: 100%;
	object-fit: cover;
}
.bagis-modul-content {
	flex: 1;
	display: flex;
	flex-direction: column;
}
.bagis-modul-title {
	font-size: 18px;
	font-weight: 600;
	margin-bottom: 10px;
	min-height: 50px;
}
.bagis-modul-desc {
	font-size: 14px;
	color: #666;
	margin-bottom: 15px;
	flex: 1;
}
.progress {
	border-radius: 5px;
}
.badge {
	padding: 5px 10px;
	margin: 0 3px;
}
</style>
<?php include('slider_menu.php');?>

