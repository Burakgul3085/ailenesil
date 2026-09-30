<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
// Modül bilgisini çek
$ModulSorgu = $db->prepare("SELECT * FROM bagis_moduller WHERE seo = ? AND dil = ? AND durum = ?");
$ModulSorgu->execute(array($_GET['id'], $_SESSION['k_dil'], 1));

if(!$ModulSorgu->rowCount()){
	header("Location: 404.html");
	exit();
}

$Modul = $ModulSorgu->fetch(PDO::FETCH_ASSOC);

// Yüzde hesaplama
$yuzde = 0;
if($Modul['hedef_tutar'] > 0) {
	$yuzde = round(($Modul['toplanan_tutar'] / $Modul['hedef_tutar']) * 100, 2);
}

// Kalan gün hesaplama
$kalan_gun = 0;
$sure_doldu = false;
if($Modul['bitis_tarihi'] && $Modul['bitis_tarihi'] != '0000-00-00') {
	$bugun = new DateTime();
	$bitis = new DateTime($Modul['bitis_tarihi']);
	$fark = $bugun->diff($bitis);
	$kalan_gun = $fark->days;
	if($bugun > $bitis) {
		$kalan_gun = 0;
		$sure_doldu = true;
	}
}
?>
<!-- PAGE SECTİON BAŞLANGIÇ -->
<section class="page-section">
	<div class="bg-white">
		<?php if($Modul['banner_resmi']){?>
		<div class="col-12 p-0 banner">
			<img src="<?php echo tema;?>/uploads/bagis_moduller/<?php echo $Modul['banner_resmi'];?>" alt="<?php echo $Modul['adi'];?>">
			<div class="slide-overlay"></div>
		</div>
		<?php } else {?>
		<div class="col-12 p-0 banner">
			<img src="<?php echo tema;?>/uploads/arkaplan/arkaplan22/<?php echo $arkaplan['arkaplan22'];?>" alt="<?php echo $Modul['adi'];?>">
			<div class="slide-overlay"></div>
		</div>
		<?php }?>
		
		<div class="container banner-fix">
			<div class="row">
				<div class="col-lg-12 z-index-9">
					<ol class="breadcrumb">
						<li><a href="<?php echo $htc['anaurl'];?><?php echo $html;?>"> <i class="fa fa-home"></i> </a></li>
						<li><a href="<?php echo $htc['bagismodulurl'];?><?php echo $html;?>"><?=@$dil['txt486'];?></a></li>
						<li><?php echo $Modul['adi'];?></li>
					</ol>
				</div>
				
				<div class="col-lg-9 col-md-7 z-index-9">
					<div class="page-content">
						<h2 class="page-title"><?php echo $Modul['adi'];?></h2>
						
						<?php if($Modul['hedef_tutar'] > 0){?>
						<div class="bagis-istatistik mb-4 p-4" style="background: #f8f9fa; border-radius: 10px;">
							<div class="row">
								<div class="col-md-3 text-center mb-3">
									<div class="stat-box">
										<i class="fas fa-bullseye fa-2x text-primary mb-2"></i>
										<h4 style="font-size: 20px; font-weight: bold; color: #333;">
											<?php echo number_format($Modul['hedef_tutar'], 0, ',', '.');?> TL
										</h4>
										<p class="text-muted mb-0"><?=@$dil['txt493'];?></p>
									</div>
								</div>
								<div class="col-md-3 text-center mb-3">
									<div class="stat-box">
										<i class="fas fa-hand-holding-usd fa-2x text-success mb-2"></i>
										<h4 style="font-size: 20px; font-weight: bold; color: #28a745;">
											<?php echo number_format($Modul['toplanan_tutar'], 0, ',', '.');?> TL
										</h4>
										<p class="text-muted mb-0"><?=@$dil['txt494'];?></p>
									</div>
								</div>
								<div class="col-md-3 text-center mb-3">
									<div class="stat-box">
										<i class="fas fa-percentage fa-2x text-info mb-2"></i>
										<h4 style="font-size: 20px; font-weight: bold; color: #17a2b8;">
											%<?php echo $yuzde;?>
										</h4>
										<p class="text-muted mb-0"><?=@$dil['txt495'];?></p>
									</div>
								</div>
								<div class="col-md-3 text-center mb-3">
									<div class="stat-box">
										<i class="fas fa-calendar-alt fa-2x text-warning mb-2"></i>
										<h4 style="font-size: 20px; font-weight: bold; color: <?php echo $sure_doldu ? '#dc3545' : '#ffc107';?>;">
											<?php if($kalan_gun > 0){?>
												<?php echo $kalan_gun;?> <?=@$dil['txt497'];?>
											<?php } else if($sure_doldu) {?>
												<?=@$dil['txt438'];?>
											<?php } else {?>
												<?=@$dil['txt436'];?>
											<?php }?>
										</h4>
										<p class="text-muted mb-0"><?=@$dil['txt496'];?></p>
									</div>
								</div>
							</div>
							
							<div class="progress mt-3" style="height: 20px;">
								<div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $yuzde;?>%;" aria-valuenow="<?php echo $yuzde;?>" aria-valuemin="0" aria-valuemax="100">
									%<?php echo $yuzde;?>
								</div>
							</div>
						</div>
						<?php }?>
						
						<?php if($Modul['aciklama']){?>
						<div class="modul-aciklama mb-4">
							<h4><?=@$dil['txt498'];?></h4>
							<div class="content-text">
								<?php echo $Modul['aciklama'];?>
							</div>
						</div>
						<?php }?>
						
						<div class="bagis-component">
							<div class="tab-head">
								<div class="row m-0">
								<?php $PSorgu = $db->prepare("SELECT * FROM bagis_kategori WHERE modul_id = ? AND dil = ? and durum = ? ORDER BY id ASC");
								$PSorgu->execute(array($Modul['id'], $_SESSION['k_dil'], 1));
								$asay = 1;
								$Pislem = $PSorgu->fetchALL(PDO::FETCH_ASSOC);?>
									<?php foreach ( $Pislem as $PSonuc ){?>
									<div datatarget="#bagis-<?php echo $PSonuc['seo']; ?>" class="tab-link <?php if($asay++ == "1"){echo "active";} ?>">
										<div class="category-image"><img style="max-width: 50px;" src="<?php echo tema;?>/uploads/bagis_kategoriler/<?php echo $PSonuc['ikon']; ?>"></div>
										<div class="category-title"><?php echo $PSonuc['adi']; ?></div>
									</div>
									<?php } ?>
								</div>
							</div>
							<div class="tab-body">
								<div class="row m-0">
								<?php $PPSorgu = $db->prepare("SELECT * FROM bagis_kategori WHERE modul_id = ? AND dil = ? and durum = ? ORDER BY id ASC");
								$PPSorgu->execute(array($Modul['id'], $_SESSION['k_dil'], 1));
								$say = 1;
								$PPislem = $PPSorgu->fetchALL(PDO::FETCH_ASSOC);?>
									<?php foreach ( $PPislem as $PPSonuc ){?>
									<div id="bagis-<?php echo $PPSonuc['seo']; ?>" class="tab-panel <?php if($say++ == "1"){echo "active";} ?>">
									
										<div class="row py-3 haberler-box">
										<?php $Sorgu = $db->prepare("SELECT * FROM bagislar WHERE modul_id = ? AND dil = ? and durum = ? and kategori = ? order by sira asc");
										$Sorgu->execute(array($Modul['id'], $_SESSION['k_dil'], 1, $PPSonuc['id']));
										$islem = $Sorgu->fetchALL(PDO::FETCH_ASSOC);?>
											<?php foreach ( $islem as $Sonuc ){?>
											<div class="col-lg-4 col-md-6">
												<div class="bagis-box p-2">
													<h4 class="title bagis-title"><?php echo $Sonuc['adi']; ?></h4>
													<div class="cards-photo"> 
														<img src="<?php echo tema;?>/uploads/bagislar/<?php echo $Sonuc['kapak']; ?>" onerror="imgError(this);">										
													</div>
													<div class="content p-0 py-2">
														<div class="row">
															<div class="col">
																<input type="text" id="fiyat" data-id="<?=@$Sonuc['id'];?>" name="fiyat" class="form-control" placeholder="<?php echo $Sonuc['miktar']; ?>" value="<?php echo $Sonuc['miktar']; ?>">
															</div>
															<div class="col-1 bagis-ortala">
																<p>TL</p>
															</div>
															<div class="col">
																<button type="submit" role="button" onclick="addBasket('<?php echo $Sonuc['id']; ?>');" class="form-button iletisim-page m-0 p-2 w-100"><i class="fas fa-heart"></i> <?=@$dil['txt112'];?></button>
															</div>
														</div>
													</div>
												</div>
											</div>
											<?php } ?>											
										</div>											
									</div>
									<?php } ?>
								</div>
							</div>
						</div>							
						
					</div>
				</div>
				
				<div class="col-lg-3 col-md-5 z-index-9">
					<div class="kolay-menu">
						<h4><?=@$dil['txt113'];?> </h4>
					</div>
					
					<div class="col-md-12 basket-card" name="sepetdiv"></div>
					<img class="img-responsive tr_en_img_x" src="https://www.paytr.com/img/odeme_sayfasi/os_kartlar.png" alt="Kart Güvenliği" style="padding: 40px 0 10px 0;width: 100%;">
					
					<?php if($Modul['baslangic_tarihi'] || $Modul['bitis_tarihi']){?>
					<div class="info-box mt-4 p-3" style="background: #fff; border: 1px solid #e0e0e0; border-radius: 5px;">
						<h5><i class="fas fa-info-circle text-info"></i> <?=@$dil['txt499'];?></h5>
						<?php if($Modul['baslangic_tarihi']){?>
						<p class="mb-1"><strong><?=@$dil['txt500'];?></strong> <?php echo date('d.m.Y', strtotime($Modul['baslangic_tarihi']));?></p>
						<?php }?>
						<?php if($Modul['bitis_tarihi']){?>
						<p class="mb-0"><strong><?=@$dil['txt501'];?></strong> <?php echo date('d.m.Y', strtotime($Modul['bitis_tarihi']));?></p>
						<?php }?>
					</div>
					<?php }?>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- PAGE SECTİON BİTİŞ -->
<?php include('slider_menu.php');?>

