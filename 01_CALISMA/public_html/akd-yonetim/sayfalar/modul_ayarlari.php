<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
$ayar_dizi = $db->prepare("SELECT * FROM moduller WHERE id = ?");
$ayar_dizi->execute(array(1));
if($ayar_dizi->rowCount()){
	$Sonuc = $ayar_dizi->fetch(PDO::FETCH_ASSOC);
}else{
	header("Location:".$url."/404.html");
	exit();
}
?>

<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3><?=@$admindil['txt7'];?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt2'];?></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt7'];?></a></li>
			</ul>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body">
			<form class="forms-sample" method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
				<input id="id" name="id" type="hidden" value="<?php echo $Sonuc['id']; ?>">
				<div class="row">
					<div class="col-md-6">
						<h6 class="card-title">Anasayfa Modülleri</h6>
						<div id="dragula-event-left" class="py-2">
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">											
											<div class="media-body">
												<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>												
												<h6 class="mb-1">Slider Üzeri Bilgi</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki slider üzerindeki bilgi alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan1" id="alan1" <?php if($Sonuc['alan1'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
						
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Orta Menü Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfa orta menü alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan2" id="alan2" <?php if($Sonuc['alan2'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
						
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Haber Slider Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki haber slider alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan3" id="alan3" <?php if($Sonuc['alan3'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa 4'lü Haber Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki 4'lü haber alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan4" id="alan4" <?php if($Sonuc['alan4'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Güncel Duyurular Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki güncel duyurular alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan5" id="alan5" <?php if($Sonuc['alan5'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							

														<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Bilgilendirme Kutusu Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki bilgilendirme kutusu alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan33" id="alan33" <?php if($Sonuc['alan33'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Güncel İhaleler Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki güncel ihaleler alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan6" id="alan6" <?php if($Sonuc['alan6'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Güncel İlanlar Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki güncel İlanlar alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan7" id="alan7" <?php if($Sonuc['alan7'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Etkinlikler Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki etkinlikler alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan16" id="alan16" <?php if($Sonuc['alan16'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Başkanla Fotoğraflar Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki başkanla fotograflar alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan10" id="alan10" <?php if($Sonuc['alan10'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Slider Menü Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki slider menü alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan11" id="alan11" <?php if($Sonuc['alan11'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Başkan Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki başkan alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan12" id="alan12" <?php if($Sonuc['alan12'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Projelerimiz Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki projelerimiz alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan13" id="alan13" <?php if($Sonuc['alan13'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Video Galeri Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki video galeri alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan14" id="alan14" <?php if($Sonuc['alan14'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Foto Galeri Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki foto galeri alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan15" id="alan15" <?php if($Sonuc['alan15'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa İletişim Formu Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki iletişim formu alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan17" id="alan17" <?php if($Sonuc['alan17'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									  
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Anasayfa Google Maps Alanı</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki google maps alanını kapatıp açabilrsiniz.
												</p>
											</div> 
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan18" id="alan18" <?php if($Sonuc['alan18'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>

							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Bağış Yap Bölümü</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki Bağış Yap bölümünü kapatıp açabilirsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan29" id="alan29" <?php if($Sonuc['alan29'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Impact & Reach Bölümü</h6>
												<p class="mb-0 text-muted">
													Anasayfadaki Impact & Reach istatistik bölümünü kapatıp açabilirsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan27" id="alan27" <?php if($Sonuc['alan27'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
									<div class="col-lg-9 col-md-9 col-sm-6">
										<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
										<div class="media-body">
											<h6 class="mb-1"><?=@$admindil['txt160'];?></h6>
											<p class="mb-0 text-muted">
												<?=@$admindil['txt161'];?>
											</p>
										</div>
									</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan28" id="alan28" <?php if($Sonuc['alan28'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>


							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
									<div class="col-lg-9 col-md-9 col-sm-6">
										<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
										<div class="media-body">
											<h6 class="mb-1">Instagram Bölümü</h6>
											<p class="mb-0 text-muted">
												Anasayfadaki Instagram Bölümünü kapatıp açabilirsiniz.
											</p>
										</div>
									</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan30" id="alan30" <?php if($Sonuc['alan30'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
						
						</div>
					</div>
				
					<div class="col-md-6">
						<h6 class="card-title">Diğer Modüller</h6>
						<div id="dragula-event-right" class="py-2">
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Yükleniyor</h6>
												<p class="mb-0 text-muted">
													Sitedeki sayfa yükleniyor alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan19" id="alan19" <?php if($Sonuc['alan19'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>

							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">.html uzantı</h6>
												<p class="mb-0 text-muted">
													Sitenizin .html uzantılı açılmasını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan20" id="alan20" <?php if($Sonuc['alan20'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">SSL</h6>
												<p class="mb-0 text-muted">
													Sitenizin ssl'li şekilde açılmasını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan21" id="alan21" <?php if($Sonuc['alan21'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Hava Durumu Botu</h6>
												<p class="mb-0 text-muted">
													Sitenizde hava durumu botunu kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan22" id="alan22" <?php if($Sonuc['alan22'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Detay Sol Bar</h6>
												<p class="mb-0 text-muted">
													Sitenizde detay sayfalarında sol alanı kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan23" id="alan23" <?php if($Sonuc['alan23'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Detay Slider Menü</h6>
												<p class="mb-0 text-muted">
													Sitenizde detay sayfalarında bulunan slider menu alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan24" id="alan24" <?php if($Sonuc['alan24'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Detay Başkan Köşesi</h6>
												<p class="mb-0 text-muted">
													Sitenizde detay sayfalarında bulunan başkan alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan25" id="alan25" <?php if($Sonuc['alan25'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Detay İletişim Alanı</h6>
												<p class="mb-0 text-muted">
													Sitenizde detay sayfalarında bulunan iletişim alanını kapatıp açabilrsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan26" id="alan26" <?php if($Sonuc['alan26'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Alt Klasörde Çalışsınmı ?</h6>
												<p class="mb-0 text-muted">
													Sitenizi alt klasörde kurulu değilse bu seçeneği açmayınız.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan8" id="alan8" <?php if($Sonuc['alan8'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Site Bakım Modu</h6>
												<p class="mb-0 text-muted">
													Sitenizi bakım moduna alır.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan9" id="alan9" <?php if($Sonuc['alan9'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Header Bağış Yap Butonu</h6>
												<p class="mb-0 text-muted">
													Header'daki Bağış Yap butonunu kapatıp/açabilirsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan31" id="alan31" <?php if($Sonuc['alan31'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>										
								</div>									
							</div>
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
										<div class="col-lg-9 col-md-9 col-sm-6">
											<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
											<div class="media-body">
												<h6 class="mb-1">Header Arama Butonu</h6>
												<p class="mb-0 text-muted">
													Header'daki Arama butonunu kapatıp/açabilirsiniz.
												</p>
											</div>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan32" id="alan32" <?php if($Sonuc['alan32'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>									
								</div>								
							</div>

							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
									<div class="col-lg-9 col-md-9 col-sm-6">
										<i class="mdi float-left mt-2 mdi-check icon-sm text-primary align-self-center mr-3"></i>
										<div class="media-body">
											<h6 class="mb-1">Header Arama Butonu</h6>
											<p class="mb-0 text-muted">
												Header'daki arama ikonunu kapatıp/açabilirsiniz. (Varsayılan: Kapalı)
											</p>
										</div>
									</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan32" id="alan32" <?php if($Sonuc['alan32'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>									
								</div>								
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
									<div class="col-lg-9 col-md-9 col-sm-6">
										<i class="mdi float-left mt-2 mdi-heart icon-sm text-danger align-self-center mr-3"></i>
										<div class="media-body">
											<h6 class="mb-1">Etkinlikler Bağış Butonu</h6>
											<p class="mb-0 text-muted">
												Etkinlikler detay sayfalarında içeriğin altında bağış butonu gösterilmesini kapatıp/açabilirsiniz.
											</p>
										</div>
									</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan34" id="alan34" <?php if(isset($Sonuc['alan34']) && $Sonuc['alan34'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>									
								</div>								
							</div>
							
							<div class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
									<div class="col-lg-9 col-md-9 col-sm-6">
										<i class="mdi float-left mt-2 mdi-heart icon-sm text-danger align-self-center mr-3"></i>
										<div class="media-body">
											<h6 class="mb-1">Haberler Bağış Butonu</h6>
											<p class="mb-0 text-muted">
												Haberler detay sayfalarında içeriğin altında bağış butonu gösterilmesini kapatıp/açabilirsiniz.
											</p>
										</div>
									</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan35" id="alan35" <?php if(isset($Sonuc['alan35']) && $Sonuc['alan35'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>									
								</div>								
							</div>
							
							<!-- Şimdilik devre dışı yeni modül için kullanabilirsiniz -->
							<div style="display:none;" class="card rounded border mb-2">								
								<div class="card-body p-3">										
									<div class="media row">
									<div class="col-lg-9 col-md-9 col-sm-6">
										<i class="mdi float-left mt-2 mdi-heart icon-sm text-danger align-self-center mr-3"></i>
										<div class="media-body">
											<h6 class="mb-1">Sayfalardaki Hızlı Erişim Menüsü</h6>
											<p class="mb-0 text-muted">
												sayfalardaki hızlı erişim menüsünü kapatıp/açabilirsiniz.
											</p>
										</div>
									</div>
										<div class="col-lg-3 col-md-3 col-sm-6 text-right">
										<label class="switch mb-0" style="margin-top: 1px;">
											<input type="checkbox" name="alan36" id="alan36" <?php if(isset($Sonuc['alan36']) && $Sonuc['alan36'] == '1') {?> checked <?php } ?> value="1">
											<span class="slider"></span>
										</label>
										</div>
									</div>									
								</div>								
							</div>
							
						</div>
					</div>
					<div class="col-md-12">
					<input type="hidden" name="url" value="<?php echo $_SERVER['REQUEST_URI'];?>" />
					<button type="submit" name="modul_guncelle" class="btn btn-success btn-icon-text btn-sm">
						<i class="mdi mdi-spin mdi-loading"></i>                                                   
						GÜNCELLE
					</button>
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php	
cVCLmHLxbS_mesaj("modul_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("modul_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
?>
