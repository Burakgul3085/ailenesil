<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM programlar WHERE id = ?");
	$Sorgu->execute(array($_GET['id']));
	if($Sorgu->rowCount())
	{
		$Sonuc = $Sorgu->fetch(PDO::FETCH_ASSOC);
	}
	else
	{
		header("Location:".$url."/404.html");
		exit;
	}
}
?>

<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3><?=@$admindil['txt141'];?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt142'];?></a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt141'];?></a></li>
			</ul>
		</div>
	</div>
</div>
<link rel="stylesheet" href="assets/css/modern_forms.css">

<div class="modern-form-container">
	<div class="modern-form-card">
		<div class="modern-form-header">
			<h4>
				<i class="fas fa-book-open"></i>
				<?=@$admindil['txt141'];?>
			</h4>
		</div>
		
		<form method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
			<input type="hidden" name="id" value="<?php echo isset($Sonuc['id']) ? $Sonuc['id'] : ''; ?>">
			
			<div class="modern-form-body">
				<!-- Temel Bilgiler -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-info-circle"></i>
						Temel Bilgiler
					</div>
					<div class="modern-form-grid">
						<div class="modern-form-group">
							<label for="sira"><?=@$admindil['txt144'];?></label>
							<input type="number" class="form-control form-control-sm" min="0" name="sira" id="sira" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['sira'] : '');?>" />
						</div>
<div class="modern-form-group">
	<label>Banner Başlık</label>
	<div style="display:flex; gap:10px; align-items:center;">
		<input 
			type="text" 
			class="form-control form-control-sm" 
			name="baslik" 
			id="banner_baslik"
			value="<?php echo(isset($_GET['islem'])=='duzenle' ? htmlspecialchars($Sonuc['baslik']) : '');?>"
		/>

		<div style="display:flex; flex-direction:column; align-items:center;">
			<small style="font-size:11px;">Renk Değişimi</small>
			<input 
				type="color" 
				name="banner_baslik_renk"
				value="<?php echo(isset($_GET['islem'])=='duzenle' ? htmlspecialchars($Sonuc['banner_baslik_renk']) : '#000000');?>"
				style="width:40px; height:34px; padding:2px;"
			/>
		</div>
	</div>
</div>

<div class="modern-form-group full-width">
	<label>Banner Açıklama</label>
	<div style="display:flex; gap:10px; align-items:flex-start;">
		<textarea 
			name="aciklama" 
			id="myTextarea2" 
			class="form-control form-control-sm" 
			rows="3"
		><?php echo(isset($_GET['islem'])=='duzenle' ? htmlspecialchars($Sonuc['aciklama']) : '');?></textarea>

		<div style="display:none; flex-direction:column; align-items:center; margin-top:4px;">
			<small style="font-size:11px;">Renk Değişimi</small>
			<input 
				type="color" 
				name="banner_aciklama_renk"
				value="<?php echo(isset($_GET['islem'])=='duzenle' ? htmlspecialchars($Sonuc['banner_aciklama_renk']) : '#000000');?>"
				style="width:40px; height:34px; padding:2px;"
			/>
		</div>
	</div>
</div>

					</div>
				</div>

				<!-- Banner Bilgileri -->
				<div  class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-image"></i>
						Banner Bilgileri
					</div>
					<div class="modern-form-grid">
<div style="display:none;" class="modern-form-group">
	<label>Banner Başlık</label>
	<div style="display:flex; gap:10px; align-items:center;">
		<input 
			type="text" 
			class="form-control form-control-sm" 
			name="banner_baslik" 
			id="banner_baslik2"
			value="<?php echo(isset($_GET['islem'])=='duzenle' ? htmlspecialchars($Sonuc['banner_baslik']) : '');?>"
		/>

		<div style="display:flex; flex-direction:column; align-items:center;">
			<small style="font-size:11px;">Renk Değişimi</small>
			<input 
				type="color" 
				name="banner_baslik_renk2"
				value="<?php echo(isset($_GET['islem'])=='duzenle' ? htmlspecialchars($Sonuc['banner_baslik_renk2']) : '#ffffff');?>"
				style="width:40px; height:34px; padding:2px;"
			/>
		</div>
	</div>
</div>

<div style="display:none;" class="modern-form-group full-width">
	<label>Banner Açıklama</label>
	<div style="display:flex; gap:10px; align-items:flex-start;">
		<textarea 
			name="banner_aciklama" 
			id="myTextarea2"
			class="form-control form-control-sm" 
			rows="3"
		><?php echo(isset($_GET['islem'])=='duzenle' ? htmlspecialchars($Sonuc['banner_aciklama']) : '');?></textarea>

		<div style="display:flex; flex-direction:column; align-items:center; margin-top:4px;">
			<small style="font-size:11px;">Renk Değişimi</small>
			<input 
				type="color" 
				name="banner_aciklama_renk2"
				value="<?php echo(isset($_GET['islem'])=='duzenle' ? htmlspecialchars($Sonuc['banner_aciklama_renk2']) : '#ffffff');?>"
				style="width:40px; height:34px; padding:2px;"
			/>
		</div>
	</div>
</div>

						<div class="modern-form-group">
							<label>Banner Resim</label>
							<?php if(isset($_GET['islem'])=="duzenle" && !empty($Sonuc['banner_resim'])){?>
								<div class="modern-image-preview">
									<div id="lightgallery" class="lightGallery">
										<a href="../<?php echo tema;?>/uploads/programlar/<?php echo $Sonuc['banner_resim'];?>">
											<img src="../<?php echo tema;?>/uploads/programlar/<?php echo $Sonuc['banner_resim'];?>" alt="Banner Resmi">
										</a>
									</div>
									<a class="btn btn-danger btn-sm mt-2 popconfirm" title="Banner Resmi Sil" href="../_class/yonetim_islem.php?programbannerresimsil=ok&sid=<?php echo $Sonuc['id'];?>">
										<i class="fas fa-trash"></i> Sil
									</a>
								</div>
							<?php }?> 
							<div class="modern-file-upload">
								<input type="file" name="banner_resim" class="file-upload-default">
								<div class="input-group">
									<input type="text" class="form-control file-upload-info form-control-sm" disabled placeholder="Banner resmi seçiniz">
									<span class="input-group-append">
										<button class="file-upload-browse btn btn-primary btn-sm" type="button">
											<i class="fas fa-cloud-upload-alt"></i> Dosya Seç
										</button>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="modern-form-grid">
						<div class="modern-form-group">
							<label for="banner_renk1"><i class="fas fa-palette mr-1"></i> Banner Geçiş Rengi 1</label>
							<div class="input-group">
								<input type="color" class="form-control form-control-sm" name="banner_renk1" id="banner_renk1" value="<?php echo(isset($_GET['islem'])=="duzenle" && !empty($Sonuc['banner_renk1']) ? $Sonuc['banner_renk1'] : '#667eea');?>" style="width: 60px; height: 38px; padding: 2px;">
								<input type="text" class="form-control form-control-sm" id="banner_renk1_text" value="<?php echo(isset($_GET['islem'])=="duzenle" && !empty($Sonuc['banner_renk1']) ? $Sonuc['banner_renk1'] : '#667eea');?>" placeholder="#667eea">
							</div>
							<small class="text-muted">Banner arka plan gradient başlangıç rengi</small>
						</div>
						<div class="modern-form-group">
							<label for="banner_ara_serit"><i class="fas fa-grip-lines mr-1"></i> Banner Ara Şerit Rengi</label>
							<div class="input-group">
								<input type="color" class="form-control form-control-sm" name="banner_ara_serit" id="banner_ara_serit" value="<?php echo(isset($_GET['islem'])=="duzenle" && !empty($Sonuc['banner_ara_serit']) ? $Sonuc['banner_ara_serit'] : '#764ba2');?>" style="width: 60px; height: 38px; padding: 2px;">
								<input type="text" class="form-control form-control-sm" id="banner_ara_serit_text" value="<?php echo(isset($_GET['islem'])=="duzenle" && !empty($Sonuc['banner_ara_serit']) ? $Sonuc['banner_ara_serit'] : '#764ba2');?>" placeholder="#764ba2">
							</div>
							<small class="text-muted">Banner arka plan gradient orta rengi</small>
						</div>
						<div class="modern-form-group">
							<label for="banner_renk2"><i class="fas fa-palette mr-1"></i> Banner Geçiş Rengi 2</label>
							<div class="input-group">
								<input type="color" class="form-control form-control-sm" name="banner_renk2" id="banner_renk2" value="<?php echo(isset($_GET['islem'])=="duzenle" && !empty($Sonuc['banner_renk2']) ? $Sonuc['banner_renk2'] : '#f093fb');?>" style="width: 60px; height: 38px; padding: 2px;">
								<input type="text" class="form-control form-control-sm" id="banner_renk2_text" value="<?php echo(isset($_GET['islem'])=="duzenle" && !empty($Sonuc['banner_renk2']) ? $Sonuc['banner_renk2'] : '#f093fb');?>" placeholder="#f093fb">
							</div>
							<small class="text-muted">Banner arka plan gradient bitiş rengi</small>
						</div>
						<div class="modern-form-group">
							<label for="tab_aktif_renk"><i class="fas fa-check-circle mr-1"></i> Tab Aktif Rengi</label>
							<div class="input-group">
								<input type="color" class="form-control form-control-sm" name="tab_aktif_renk" id="tab_aktif_renk" value="<?php echo(isset($_GET['islem'])=="duzenle" && !empty($Sonuc['tab_aktif_renk']) ? $Sonuc['tab_aktif_renk'] : '#667eea');?>" style="width: 60px; height: 38px; padding: 2px;">
								<input type="text" class="form-control form-control-sm" id="tab_aktif_renk_text" value="<?php echo(isset($_GET['islem'])=="duzenle" && !empty($Sonuc['tab_aktif_renk']) ? $Sonuc['tab_aktif_renk'] : '#667eea');?>" placeholder="#667eea">
							</div>
							<small class="text-muted">Sekme aktif olduğundaki arka plan rengi</small>
						</div>
					</div>
				</div>

				<!-- Görsel Ayarları -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-photo-video"></i>
						Görsel Ayarları
					</div>
					<div class="modern-form-group">
						<label><?=@$admindil['txt148'];?></label>
						<?php if(isset($_GET['islem'])=="duzenle" && !empty($Sonuc['resim'])){?>
							<div class="modern-image-preview">
								<div id="lightgallery" class="lightGallery">
									<a href="../<?php echo tema;?>/uploads/programlar/<?php echo $Sonuc['resim'];?>">
										<img src="../<?php echo tema;?>/uploads/programlar/<?php echo $Sonuc['resim'];?>" alt="Program Resmi">
									</a>
								</div>
								<a class="btn btn-danger btn-sm mt-2 popconfirm" title="<?=@$admindil['txt158'];?>" href="../_class/yonetim_islem.php?programresimsil=ok&sid=<?php echo $Sonuc['id'];?>">
									<i class="fas fa-trash"></i> <?=@$admindil['txt158'];?>
								</a>
							</div>
						<?php }?>
						<div class="modern-file-upload">
							<input type="file" name="resim" class="file-upload-default">
							<div class="input-group">
								<input type="text" class="form-control file-upload-info form-control-sm" disabled placeholder="Resim dosyası seçiniz">
								<span class="input-group-append">
									<button class="file-upload-browse btn btn-primary btn-sm" type="button">
										<i class="fas fa-cloud-upload-alt"></i> Dosya Seç
									</button>
								</span>
							</div>
						</div>
					</div>
				</div>

				<!-- Detay Açıklama -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-align-left"></i>
						Detay Açıklama
					</div>
					<div class="modern-form-group full-width">
						<label for="detay_aciklama"><?=@$admindil['txt147'];?></label>
						<textarea name="detay_aciklama" id="myTextarea"><?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['detay_aciklama'] : '');?></textarea>
					</div>
				</div>

				<!-- Dinamik İçerik Blokları -->
				<?php if(isset($_GET['islem'])=="duzenle" && isset($Sonuc['id'])){ ?>
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-layer-group"></i>
						Program İçerik Blokları
					</div>
					
					<div id="icerikBloklari" class="accordion" id="icerikAccordion">
						<?php 
						$iceriklerSorgu = $db->prepare("SELECT * FROM program_icerikleri WHERE program_id = ? AND dil = ? ORDER BY sira ASC");
						$iceriklerSorgu->execute(array($Sonuc['id'], $_SESSION['admin_dil']));
						$icerikler = $iceriklerSorgu->fetchALL(PDO::FETCH_ASSOC);
						
						if(count($icerikler) > 0){
							foreach($icerikler as $index => $icerik){
								$accordionId = 'icerikAccordion_' . $icerik['id'];
								$baslik = !empty($icerik['baslik']) ? htmlspecialchars($icerik['baslik'], ENT_QUOTES, 'UTF-8') : 'İçerik Bloğu #' . ($index + 1);
						?>
						<div class="card icerik-blok-item" data-icerik-id="<?php echo $icerik['id']; ?>">
							<div class="card-header icerik-blok-header" id="heading_<?php echo $icerik['id']; ?>">
								<h5 class="mb-0">
									<button class="btn btn-link icerik-accordion-toggle" type="button" data-toggle="collapse" data-target="#<?php echo $accordionId; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="<?php echo $accordionId; ?>">
										<i class="fas fa-chevron-down accordion-icon"></i>
										<span class="icerik-baslik-text"><?php echo $baslik; ?></span>
									</button>
								</h5>
								<div class="icerik-blok-actions">
									<label class="switch">
										<input type="checkbox" class="icerik-durum" data-icerik-id="<?php echo $icerik['id']; ?>" <?php echo $icerik['durum'] == 1 ? 'checked' : ''; ?>>
										<span class="slider"></span>
									</label>
									<button type="button" class="btn btn-danger btn-sm icerik-sil-btn" data-icerik-id="<?php echo $icerik['id']; ?>">
										<i class="fas fa-trash"></i> Sil
									</button>
								</div>
							</div>
							<div id="<?php echo $accordionId; ?>" class="collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading_<?php echo $icerik['id']; ?>" data-parent="#icerikAccordion">
								<div class="card-body">
									<div class="modern-form-grid">
										<div class="modern-form-group icerik-baslik-grubu" data-icerik-id="<?php echo $icerik['id']; ?>" style="<?php echo (isset($icerik['icerik_tipi']) && $icerik['icerik_tipi']==1) ? 'display:none;' : ''; ?>">
											<label>Başlık</label>
											<input type="text" class="form-control form-control-sm icerik-baslik" data-icerik-id="<?php echo $icerik['id']; ?>" value="<?php echo htmlspecialchars($icerik['baslik']); ?>" placeholder="İçerik başlığı">
										</div>
										<div class="modern-form-group">
											<label>Sıra</label>
											<input type="number" class="form-control form-control-sm icerik-sira" data-icerik-id="<?php echo $icerik['id']; ?>" value="<?php echo $icerik['sira']; ?>" min="0">
										</div>
										<div class="modern-form-group">
											<label>İçerik Tipi</label>
											<select class="form-control icerik-tipi" data-icerik-id="<?php echo $icerik['id']; ?>">
												<option value="0" <?php echo (isset($icerik['icerik_tipi']) && $icerik['icerik_tipi']==0) ? 'selected' : ''; ?>>Normal (Metin/Medya)</option>
												<option value="1" <?php echo (isset($icerik['icerik_tipi']) && $icerik['icerik_tipi']==1) ? 'selected' : ''; ?>>Tek Görsel</option>
											</select>
										</div>
									</div>
									<!-- Normal içerik alanları -->
									<div class="icerik-normal-alanlar" data-icerik-id="<?php echo $icerik['id']; ?>" style="<?php echo (isset($icerik['icerik_tipi']) && $icerik['icerik_tipi']==1) ? 'display:none;' : ''; ?>">
									<div class="modern-form-group full-width">
										<label>Açıklama</label>
										<textarea id="myTextarea<?php echo $icerik['id']; ?>" class="form-control form-control-sm icerik-aciklama" data-icerik-id="<?php echo $icerik['id']; ?>" rows="3" placeholder="İçerik açıklaması"><?php echo htmlspecialchars($icerik['aciklama']); ?></textarea>
									</div>
									<div class="modern-form-grid">
										<div class="modern-form-group">
											<label>Video URL</label>
											<input type="text" class="form-control form-control-sm icerik-video" data-icerik-id="<?php echo $icerik['id']; ?>" value="<?php echo htmlspecialchars($icerik['video_url']); ?>" placeholder="YouTube, Vimeo vb. video URL">
										</div>
										<div class="modern-form-group">
											<label>Görsel formatı</label>
											<select class="form-control form-control-sm icerik-gorsel-format" data-icerik-id="<?php echo $icerik['id']; ?>">
												<option value="0" <?php echo (isset($icerik['gorsel_format']) && $icerik['gorsel_format']==0) ? 'selected' : ''; ?>>Kare (1:1)</option>
												<option value="1" <?php echo (isset($icerik['gorsel_format']) && $icerik['gorsel_format']==1) ? 'selected' : ''; ?>>Yatay (16:9)</option>
												<option value="2" <?php echo (isset($icerik['gorsel_format']) && $icerik['gorsel_format']==2) ? 'selected' : ''; ?>>Kırpmasız (orijinal)</option>
											</select>
										</div>
										<div class="modern-form-group">
											<label>Görseller</label>
											<div class="modern-image-preview-wrapper" id="icerik_resimler_<?php echo $icerik['id']; ?>">
												<?php
												if(!empty($icerik['resim'])){
													$resimler = explode(',', $icerik['resim']);
													foreach($resimler as $r){
														if(!empty($r)){
												?>
												<div class="modern-image-preview" style="display: inline-block; margin-right: 10px; margin-bottom: 10px;">
													<img src="../<?php echo tema;?>/uploads/programlar/<?php echo $r;?>" alt="İçerik Görseli" style="max-width: 150px; height: auto; display: block;">
													<a class="btn btn-danger btn-sm mt-2 icerik-resim-sil" data-icerik-id="<?php echo $icerik['id']; ?>" data-resim-ad="<?php echo $r; ?>" href="javascript:void(0);">
														<i class="fas fa-trash"></i> Sil
													</a>
												</div>
												<?php
														}
													}
												}
												?>
											</div>
											<div class="modern-file-upload">
												<input type="file" class="file-upload-default icerik-resim-input" data-icerik-id="<?php echo $icerik['id']; ?>" accept="image/*" multiple>
												<div class="input-group">
													<input type="text" class="form-control file-upload-info form-control-sm" disabled placeholder="Görsel(ler) seçiniz">
													<span class="input-group-append">
														<button class="file-upload-browse btn btn-primary btn-sm" type="button">
															<i class="fas fa-cloud-upload-alt"></i> Dosya Seç
														</button>
													</span>
												</div>
											</div>
										</div>
									</div>
									</div>
									<!-- Tek Görsel alanları -->
									<div class="icerik-gorsel-alanlar" data-icerik-id="<?php echo $icerik['id']; ?>" style="<?php echo (isset($icerik['icerik_tipi']) && $icerik['icerik_tipi']==1) ? '' : 'display:none;'; ?>">
									<div class="modern-form-grid">
										<div class="modern-form-group">
											<label>Desktop Görsel</label>
											<?php if(!empty($icerik['gorsel_desktop'])): ?>
											<div class="modern-image-preview" style="margin-bottom: 10px;">
												<img src="../<?php echo tema;?>/uploads/programlar/<?php echo $icerik['gorsel_desktop'];?>" alt="Desktop Görsel" style="max-width: 250px; height: auto; display: block;">
												<a class="btn btn-danger btn-sm mt-2 icerik-gorsel-tek-sil" data-icerik-id="<?php echo $icerik['id']; ?>" data-tip="desktop" href="javascript:void(0);">
													<i class="fas fa-trash"></i> Sil
												</a>
											</div>
											<?php endif; ?>
											<div class="modern-file-upload">
												<input type="file" class="file-upload-default icerik-gorsel-desktop" data-icerik-id="<?php echo $icerik['id']; ?>" accept="image/*">
												<div class="input-group">
													<input type="text" class="form-control file-upload-info form-control-sm" disabled placeholder="Desktop görsel seçiniz">
													<span class="input-group-append">
														<button class="file-upload-browse btn btn-primary btn-sm" type="button">
															<i class="fas fa-cloud-upload-alt"></i> Dosya Seç
														</button>
													</span>
												</div>
											</div>
										</div>
										<div class="modern-form-group">
											<label>Mobil Görsel</label>
											<?php if(!empty($icerik['gorsel_mobil'])): ?>
											<div class="modern-image-preview" style="margin-bottom: 10px;">
												<img src="../<?php echo tema;?>/uploads/programlar/<?php echo $icerik['gorsel_mobil'];?>" alt="Mobil Görsel" style="max-width: 150px; height: auto; display: block;">
												<a class="btn btn-danger btn-sm mt-2 icerik-gorsel-tek-sil" data-icerik-id="<?php echo $icerik['id']; ?>" data-tip="mobil" href="javascript:void(0);">
													<i class="fas fa-trash"></i> Sil
												</a>
											</div>
											<?php endif; ?>
											<div class="modern-file-upload">
												<input type="file" class="file-upload-default icerik-gorsel-mobil" data-icerik-id="<?php echo $icerik['id']; ?>" accept="image/*">
												<div class="input-group">
													<input type="text" class="form-control file-upload-info form-control-sm" disabled placeholder="Mobil görsel seçiniz">
													<span class="input-group-append">
														<button class="file-upload-browse btn btn-primary btn-sm" type="button">
															<i class="fas fa-cloud-upload-alt"></i> Dosya Seç
														</button>
													</span>
												</div>
											</div>
										</div>
									</div>
									</div>
									<button type="button" class="btn btn-success btn-sm icerik-kaydet-btn" data-icerik-id="<?php echo $icerik['id']; ?>">
										<i class="fas fa-save"></i> Kaydet
									</button>
								</div>
							</div>
						</div>
						<?php 
							}
						}
						?>
					</div>
					
					<button type="button" class="btn btn-primary mt-3" id="yeniIcerikEkle">
						<i class="fas fa-plus"></i> Yeni İçerik Bloğu Ekle
					</button>
				</div>
				<?php } ?>

				<!-- Derslik Programı Yönetimi -->
				<?php if(isset($_GET['islem'])=="duzenle" && isset($Sonuc['id'])){ ?>
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-calendar-alt"></i>
						Derslik Programı
					</div>
					
					<div id="derslikProgramlari" class="accordion" id="derslikAccordion">
						<?php 
						$dersliklerSorgu = $db->prepare("SELECT * FROM derslik_durumlari WHERE program_id = ? AND dil = ? ORDER BY FIELD(gun,'Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi','Pazar'), saat ASC");
						$dersliklerSorgu->execute(array($Sonuc['id'], $_SESSION['admin_dil']));
						$derslikler = $dersliklerSorgu->fetchALL(PDO::FETCH_ASSOC);
						
						if(count($derslikler) > 0){
							foreach($derslikler as $index => $derslik){
								$accordionId = 'derslikAccordion_' . $derslik['id'];
								$baslik = !empty($derslik['baslik']) ? htmlspecialchars($derslik['baslik'], ENT_QUOTES, 'UTF-8') : ($derslik['gun'] . ' - ' . $derslik['saat']);
						?>
						<div class="card derslik-blok-item" data-derslik-id="<?php echo $derslik['id']; ?>">
							<div class="card-header derslik-blok-header" id="heading_derslik_<?php echo $derslik['id']; ?>">
								<h5 class="mb-0">
									<button class="btn btn-link derslik-accordion-toggle" type="button" data-toggle="collapse" data-target="#<?php echo $accordionId; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-controls="<?php echo $accordionId; ?>">
										<i class="fas fa-chevron-down accordion-icon"></i>
										<span class="derslik-baslik-text"><?php echo $baslik; ?></span>
										<span class="badge badge-<?php echo $derslik['aktif'] == 1 ? 'success' : 'danger'; ?> ml-2"><?php echo $derslik['aktif'] == 1 ? 'Aktif' : 'Pasif'; ?></span>
									</button>
								</h5>
								<div class="derslik-blok-actions">
									<label class="switch">
										<input type="checkbox" class="derslik-aktif" data-derslik-id="<?php echo $derslik['id']; ?>" <?php echo $derslik['aktif'] == 1 ? 'checked' : ''; ?>>
										<span class="slider"></span>
									</label>
									<button type="button" class="btn btn-danger btn-sm derslik-sil-btn" data-derslik-id="<?php echo $derslik['id']; ?>">
										<i class="fas fa-trash"></i> Sil
									</button>
								</div>
							</div>
							<div id="<?php echo $accordionId; ?>" class="collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading_derslik_<?php echo $derslik['id']; ?>" data-parent="#derslikAccordion">
								<div class="card-body">
									<div class="modern-form-grid">
										<div class="modern-form-group">
											<label>Sıra</label>
											<input type="number" class="form-control form-control-sm derslik-sira" data-derslik-id="<?php echo $derslik['id']; ?>" value="<?php echo $derslik['sira']; ?>" min="0">
										</div>
										<div class="modern-form-group">
											<label>Şehir <span class="text-danger">*</span></label>
											<input type="text" class="form-control form-control-sm derslik-sehir" data-derslik-id="<?php echo $derslik['id']; ?>" value="<?php echo htmlspecialchars($derslik['sehir']); ?>" required>
										</div>
										<div class="modern-form-group">
											<label>Kategori (Sınıf)</label>
											<select class="form-control form-control-sm derslik-kategori" data-derslik-id="<?php echo $derslik['id']; ?>">
												<option value="">Kategori Seçiniz</option>
												<?php 
												$kategoriler = $db->query("SELECT * FROM derslik_kategorileri WHERE durum = 1 AND dil = '{$_SESSION['admin_dil']}' ORDER BY sira ASC, adi ASC")->fetchAll(PDO::FETCH_ASSOC);
												foreach($kategoriler as $kat): 
													$selected = ($derslik['kategori_id'] == $kat['id']) ? 'selected' : '';
												?>
												<option value="<?php echo $kat['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($kat['adi']); ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="modern-form-group">
											<label>Sınıf (Manuel)</label>
											<input type="text" class="form-control form-control-sm derslik-sinif" data-derslik-id="<?php echo $derslik['id']; ?>" value="<?php echo htmlspecialchars($derslik['sinif']); ?>" placeholder="Kategori seçilmezse manuel girebilirsiniz">
										</div>
										<div class="modern-form-group">
											<label>Başlık</label>
											<input type="text" class="form-control form-control-sm derslik-baslik" data-derslik-id="<?php echo $derslik['id']; ?>" value="<?php echo htmlspecialchars($derslik['baslik']); ?>" placeholder="Ders başlığı">
										</div>
									</div>
									<div class="modern-form-grid">
										<div class="modern-form-group">
											<label>Tarih <span class="text-danger">*</span></label>
											<input type="date" class="form-control form-control-sm derslik-tarih" data-derslik-id="<?php echo $derslik['id']; ?>" value="<?php echo $derslik['tarih']; ?>" required>
										</div>
										<div class="modern-form-group">
											<label>Gün <span class="text-danger">*</span></label>
											<select class="form-control form-control-sm derslik-gun" data-derslik-id="<?php echo $derslik['id']; ?>" required>
												<option value="">Seçiniz</option>
												<option value="Pazartesi" <?php echo $derslik['gun'] == 'Pazartesi' ? 'selected' : ''; ?>>Pazartesi</option>
												<option value="Salı" <?php echo $derslik['gun'] == 'Salı' ? 'selected' : ''; ?>>Salı</option>
												<option value="Çarşamba" <?php echo $derslik['gun'] == 'Çarşamba' ? 'selected' : ''; ?>>Çarşamba</option>
												<option value="Perşembe" <?php echo $derslik['gun'] == 'Perşembe' ? 'selected' : ''; ?>>Perşembe</option>
												<option value="Cuma" <?php echo $derslik['gun'] == 'Cuma' ? 'selected' : ''; ?>>Cuma</option>
												<option value="Cumartesi" <?php echo $derslik['gun'] == 'Cumartesi' ? 'selected' : ''; ?>>Cumartesi</option>
												<option value="Pazar" <?php echo $derslik['gun'] == 'Pazar' ? 'selected' : ''; ?>>Pazar</option>
											</select>
										</div>
										<div class="modern-form-group">
											<label>Saat <span class="text-danger">*</span></label>
											<input type="text" class="form-control form-control-sm derslik-saat" data-derslik-id="<?php echo $derslik['id']; ?>" value="<?php echo htmlspecialchars($derslik['saat']); ?>" required placeholder="Örn: 09:00-10:30">
										</div>
									</div>
									<div class="modern-form-grid">
										<div class="modern-form-group">
											<label>Kontenjan <span class="text-danger">*</span></label>
											<input type="number" class="form-control form-control-sm derslik-kontenjan" data-derslik-id="<?php echo $derslik['id']; ?>" value="<?php echo $derslik['kontenjan']; ?>" min="1" required>
										</div>
										<div class="modern-form-group">
											<label>Katılımcı Sayısı <span class="text-danger">*</span></label>
											<input type="number" class="form-control form-control-sm derslik-katilimci" data-derslik-id="<?php echo $derslik['id']; ?>" value="<?php echo $derslik['katilimci']; ?>" min="0" required>
										</div>
										<div class="modern-form-group">
											<label>Aile Katılımı</label>
											<select class="form-control form-control-sm derslik-aile-katilim" data-derslik-id="<?php echo $derslik['id']; ?>">
												<option value="0" <?php echo $derslik['aile_katilim'] == '0' ? 'selected' : ''; ?>>Hayır</option>
												<option value="1" <?php echo $derslik['aile_katilim'] == '1' ? 'selected' : ''; ?>>Evet</option>
											</select>
										</div>
										<div class="modern-form-group">
											<label>Durum</label>
											<select class="form-control form-control-sm derslik-durum" data-derslik-id="<?php echo $derslik['id']; ?>">
												<option value="0" <?php echo $derslik['durum'] == '0' ? 'selected' : ''; ?>>Boş (Kayıt Alınabilir)</option>
												<option value="1" <?php echo $derslik['durum'] == '1' ? 'selected' : ''; ?>>Dolu (Kontenjan Dolmuş)</option>
												<option value="2" <?php echo $derslik['durum'] == '2' ? 'selected' : ''; ?>>Bekleme Listesi</option>
											</select>
										</div>
									</div>
									<button type="button" class="btn btn-success btn-sm derslik-kaydet-btn" data-derslik-id="<?php echo $derslik['id']; ?>">
										<i class="fas fa-save"></i> Kaydet
									</button>
								</div>
							</div>
						</div>
						<?php 
							}
						}
						?>
					</div>
					
					<button type="button" class="btn btn-primary mt-3" id="yeniDerslikEkle">
						<i class="fas fa-plus"></i> Yeni Derslik Programı Ekle
					</button>
				</div>
				<?php } ?>

				<!-- Durum Ayarları -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-toggle-on"></i>
						Durum Ayarları
					</div>
					<div class="modern-form-grid">
						<div class="modern-switch-wrapper">
							<label for="durum"><?=@$admindil['txt149'];?></label>
							<?php if(isset($_GET['islem'])=="duzenle"){?>
							<label class="switch">
								<input type="checkbox" name="durum" id="durum" value="1" <?php if(isset($Sonuc['durum']) && $Sonuc['durum'] == '1') {?> checked <?php } ?>>
								<span class="slider"></span>
							</label>
							<?php }else{?>
							<label class="switch">
								<input type="checkbox" name="durum" id="durum" value="1" checked>
								<span class="slider"></span>
							</label>
							<?php } ?>
						</div>
						<div class="modern-switch-wrapper">
							<label for="anasayfa_durum">Anasayfada Göster</label>
							<?php if(isset($_GET['islem'])=="duzenle"){?>
							<label class="switch">
								<input type="checkbox" name="anasayfa_durum" id="anasayfa_durum" value="1" <?php if(isset($Sonuc['anasayfa_durum']) && $Sonuc['anasayfa_durum'] == '1') {?> checked <?php } ?>>
								<span class="slider"></span>
							</label>
							<?php }else{?>
							<label class="switch">
								<input type="checkbox" name="anasayfa_durum" id="anasayfa_durum" value="1" checked>
								<span class="slider"></span>
							</label>
							<?php } ?>
						</div>
					</div>
				</div>

				<!-- SEO Ayarları -->
				<div class="modern-seo-card">
					<div class="card-header">
						<h5>
							<i class="fas fa-search"></i>
							SEO AYARLARI
						</h5>
					</div>
					<div class="card-body">
						<div class="modern-form-group">
							<label for="seo"><?=@$admindil['txt150'];?></label>
							<input type="text" class="form-control form-control-sm" name="seo" id="seo" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['seo']) : '');?>" />
						</div>
						<div class="modern-form-group">
							<label for="maxlength-textarea"><?=@$admindil['txt151'];?></label>
							<textarea id="maxlength-textarea" name="description" class="form-control" maxlength="260" rows="4"><?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['description']) : '');?></textarea>
						</div>
						<div class="modern-form-group">
							<label for="tags"><?=@$admindil['txt152'];?> <small>(Kelimenin sonuna virgül koyunuz)</small></label>
							<input name="keywords" id="tags" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['keywords']) : '');?>" />
						</div>
					</div>
				</div>
			</div>

			<div class="modern-form-actions">
				<a href="program_listele.html" class="modern-btn modern-btn-light">
					<i class="fas fa-times"></i> İptal
				</a>
				<?php if(isset($_GET['islem'])=="duzenle"){?>
				<button type="submit" name="program_guncelle" class="modern-btn modern-btn-success">
					<i class="fas fa-save"></i> <?=@$admindil['txt154'];?>
				</button>
				<?php }else{?>
				<button type="submit" name="program_ekle" class="modern-btn modern-btn-primary">
					<i class="fas fa-plus"></i> <?=@$admindil['txt153'];?>
				</button>
				<?php } ?>
			</div>
		</form>
	</div>
</div>
<?php 
cVCLmHLxbS_mesaj("program_ekle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("program_guncelle",1,"yes","Başarı ile guncellenmiştir.");
cVCLmHLxbS_mesaj("program_guncelle",2,"no","Hata oluştu tekrar deneyiniz.!");
cVCLmHLxbS_mesaj("programresimsil",1,"yes","Başarı ile siinmiştir.");
cVCLmHLxbS_mesaj("programresimsil",2,"no","Hata oluştu tekrar deneyiniz.!");
?>

<?php if(isset($_GET['islem'])=="duzenle" && isset($Sonuc['id'])){ ?>
<style>
.icerik-blok-item {
	margin-bottom: 15px;
	border: 1px solid #dee2e6;
	border-radius: 8px;
	overflow: hidden;
}
.icerik-blok-item .card {
	border: none;
	border-radius: 0;
}
.icerik-blok-item .card-header {
	background: #f8f9fa;
	border-bottom: 1px solid #dee2e6;
	padding: 15px 20px;
	display: flex;
	justify-content: space-between;
	align-items: center;
}
.icerik-blok-item .card-header h5 {
	margin: 0;
	flex: 1;
}
.icerik-accordion-toggle {
	width: 100%;
	text-align: left;
	padding: 0;
	color: #495057;
	font-weight: 600;
	font-size: 1rem;
	text-decoration: none;
	border: none;
	background: transparent;
	display: flex;
	align-items: center;
	gap: 10px;
}
.icerik-accordion-toggle:hover {
	color: #007bff;
	text-decoration: none;
}
.icerik-accordion-toggle:focus {
	outline: none;
	box-shadow: none;
}
.accordion-icon {
	transition: transform 0.3s ease;
	font-size: 0.8rem;
}
.icerik-accordion-toggle[aria-expanded="true"] .accordion-icon {
	transform: rotate(180deg);
}
.icerik-baslik-text {
	flex: 1;
}
.icerik-blok-actions {
	display: flex;
	align-items: center;
	gap: 10px;
	margin-left: 15px;
}
.icerik-blok-actions .switch {
	margin: 0;
}
.icerik-blok-item .card-body {
	padding: 20px;
	background: #ffffff;
}

/* Derslik Programı Stilleri */
.derslik-blok-item {
	margin-bottom: 15px;
	border: 1px solid #dee2e6;
	border-radius: 8px;
	overflow: hidden;
}
.derslik-blok-item .card {
	border: none;
	border-radius: 0;
}
.derslik-blok-item .card-header {
	background: #f0f8ff;
	border-bottom: 1px solid #dee2e6;
	padding: 15px 20px;
	display: flex;
	justify-content: space-between;
	align-items: center;
}
.derslik-blok-item .card-header h5 {
	margin: 0;
	flex: 1;
}
.derslik-accordion-toggle {
	width: 100%;
	text-align: left;
	padding: 0;
	color: #495057;
	font-weight: 600;
	font-size: 1rem;
	text-decoration: none;
	border: none;
	background: transparent;
	display: flex;
	align-items: center;
	gap: 10px;
}
.derslik-accordion-toggle:hover {
	color: #007bff;
	text-decoration: none;
}
.derslik-accordion-toggle:focus {
	outline: none;
	box-shadow: none;
}
.derslik-baslik-text {
	flex: 1;
}
.derslik-blok-actions {
	display: flex;
	align-items: center;
	gap: 10px;
	margin-left: 15px;
}
.derslik-blok-actions .switch {
	margin: 0;
}
.derslik-blok-item .card-body {
	padding: 20px;
	background: #ffffff;
}
</style>

<script>
$(document).ready(function() {
	var programId = <?php echo $Sonuc['id']; ?>;
	var icerikSayac = <?php echo isset($icerikler) ? count($icerikler) : 0; ?>;
	
	// Yeni içerik bloğu ekle
	$('#yeniIcerikEkle').on('click', function() {
		icerikSayac++;
		var accordionId = 'icerikAccordion_yeni_' + icerikSayac;
		var headingId = 'heading_yeni_' + icerikSayac;
		var yeniBlok = `
			<div class="card icerik-blok-item" data-icerik-id="yeni_${icerikSayac}">
				<div class="card-header icerik-blok-header" id="${headingId}">
					<h5 class="mb-0">
						<button class="btn btn-link icerik-accordion-toggle" type="button" data-toggle="collapse" data-target="#${accordionId}" aria-expanded="true" aria-controls="${accordionId}">
							<i class="fas fa-chevron-down accordion-icon"></i>
							<span class="icerik-baslik-text">Yeni İçerik Bloğu #${icerikSayac}</span>
						</button>
					</h5>
					<div class="icerik-blok-actions">
						<button type="button" class="btn btn-danger btn-sm icerik-sil-btn" data-icerik-id="yeni_${icerikSayac}">
							<i class="fas fa-trash"></i> Sil
						</button>
					</div>
				</div>
				<div id="${accordionId}" class="collapse show" aria-labelledby="${headingId}" data-parent="#icerikAccordion">
					<div class="card-body">
						<div class="modern-form-grid">
							<div class="modern-form-group icerik-baslik-grubu" data-icerik-id="yeni_${icerikSayac}">
								<label>Başlık</label>
								<input type="text" class="form-control form-control-sm icerik-baslik" data-icerik-id="yeni_${icerikSayac}" placeholder="İçerik başlığı">
							</div>
							<div class="modern-form-group">
								<label>Sıra</label>
								<input type="number" class="form-control form-control-sm icerik-sira" data-icerik-id="yeni_${icerikSayac}" value="${icerikSayac}" min="0">
							</div>
							<div class="modern-form-group">
								<label>İçerik Tipi</label>
								<select class="form-control icerik-tipi" data-icerik-id="yeni_${icerikSayac}">
									<option value="0">Normal (Metin/Medya)</option>
									<option value="1">Tek Görsel</option>
								</select>
							</div>
						</div>
						<!-- Normal içerik alanları -->
						<div class="icerik-normal-alanlar" data-icerik-id="yeni_${icerikSayac}">
						<div class="modern-form-group full-width">
							<label>Açıklama</label>
							<textarea id="myTextarea9${icerikSayac}"  class="form-control form-control-sm icerik-aciklama" data-icerik-id="yeni_${icerikSayac}" rows="3" placeholder="İçerik açıklaması"></textarea>
						</div>
						<div class="modern-form-grid">
							<div class="modern-form-group">
								<label>Video URL</label>
								<input type="text" class="form-control form-control-sm icerik-video" data-icerik-id="yeni_${icerikSayac}" placeholder="YouTube, Vimeo vb. video URL">
							</div>
							<div class="modern-form-group">
								<label>Görsel formatı</label>
								<select class="form-control form-control-sm icerik-gorsel-format" data-icerik-id="yeni_${icerikSayac}">
									<option value="0">Kare (1:1)</option>
									<option value="1">Yatay (16:9)</option>
									<option value="2">Kırpmasız (orijinal)</option>
								</select>
							</div>
							<div class="modern-form-group">
								<label>Görseller</label>
								<div class="modern-file-upload">
									<input type="file" class="file-upload-default icerik-resim-input" data-icerik-id="yeni_${icerikSayac}" accept="image/*" multiple>
									<div class="input-group">
										<input type="text" class="form-control file-upload-info form-control-sm" disabled placeholder="Görsel(ler) seçiniz">
										<span class="input-group-append">
											<button class="file-upload-browse btn btn-primary btn-sm" type="button">
												<i class="fas fa-cloud-upload-alt"></i> Dosya Seç
											</button>
										</span>
									</div>
								</div>
							</div>
						</div>
						</div>
						<!-- Tek Görsel alanları -->
						<div class="icerik-gorsel-alanlar" data-icerik-id="yeni_${icerikSayac}" style="display:none;">
						<div class="modern-form-grid">
							<div class="modern-form-group">
								<label>Desktop Görsel</label>
								<div class="modern-file-upload">
									<input type="file" class="file-upload-default icerik-gorsel-desktop" data-icerik-id="yeni_${icerikSayac}" accept="image/*">
									<div class="input-group">
										<input type="text" class="form-control file-upload-info form-control-sm" disabled placeholder="Desktop görsel seçiniz">
										<span class="input-group-append">
											<button class="file-upload-browse btn btn-primary btn-sm" type="button">
												<i class="fas fa-cloud-upload-alt"></i> Dosya Seç
											</button>
										</span>
									</div>
								</div>
							</div>
							<div class="modern-form-group">
								<label>Mobil Görsel</label>
								<div class="modern-file-upload">
									<input type="file" class="file-upload-default icerik-gorsel-mobil" data-icerik-id="yeni_${icerikSayac}" accept="image/*">
									<div class="input-group">
										<input type="text" class="form-control file-upload-info form-control-sm" disabled placeholder="Mobil görsel seçiniz">
										<span class="input-group-append">
											<button class="file-upload-browse btn btn-primary btn-sm" type="button">
												<i class="fas fa-cloud-upload-alt"></i> Dosya Seç
											</button>
										</span>
									</div>
								</div>
							</div>
						</div>
						</div>
						<button type="button" class="btn btn-success btn-sm icerik-kaydet-btn" data-icerik-id="yeni_${icerikSayac}">
							<i class="fas fa-save"></i> Kaydet
						</button>
					</div>
				</div>
			</div>
		`;
		$('#icerikBloklari').append(yeniBlok);
		
		// Yeni eklenen textarea için TinyMCE başlat
		var yeniTextareaId = 'myTextarea9' + icerikSayac;
		if(typeof tinymce !== 'undefined') {
			tinymce.init({
				selector: '#' + yeniTextareaId,
				language: 'tr',
				theme: "silver",
				branding: false,
				height: 300,
				menubar: false,
				plugins: [
					'advlist autolink lists link image charmap print preview anchor',
					'searchreplace visualblocks code fullscreen',
					'insertdatetime media table paste code help wordcount'
				],
				toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
			});
		}
		
		// Başlık değiştiğinde accordion başlığını güncelle
		$('.icerik-baslik[data-icerik-id="yeni_' + icerikSayac + '"]').on('input', function() {
			var baslik = $(this).val() || 'Yeni İçerik Bloğu #' + icerikSayac;
			$('[data-icerik-id="yeni_' + icerikSayac + '"]').find('.icerik-baslik-text').text(baslik);
		});
	});
	
	// İçerik tipi değiştiğinde alanları toggle et
	$(document).on('change', '.icerik-tipi', function() {
		var icerikId = $(this).data('icerik-id');
		var tip = $(this).val();
		if(tip == '1') {
			$('.icerik-baslik-grubu[data-icerik-id="' + icerikId + '"]').hide();
			$('.icerik-normal-alanlar[data-icerik-id="' + icerikId + '"]').hide();
			$('.icerik-gorsel-alanlar[data-icerik-id="' + icerikId + '"]').show();
		} else {
			$('.icerik-baslik-grubu[data-icerik-id="' + icerikId + '"]').show();
			$('.icerik-normal-alanlar[data-icerik-id="' + icerikId + '"]').show();
			$('.icerik-gorsel-alanlar[data-icerik-id="' + icerikId + '"]').hide();
		}
	});

	// Başlık değiştiğinde accordion başlığını güncelle (mevcut içerikler için)
	$(document).on('input', '.icerik-baslik', function() {
		var icerikId = $(this).data('icerik-id');
		var baslik = $(this).val() || 'İçerik Bloğu';
		var blokItem = $('.icerik-blok-item[data-icerik-id="' + icerikId + '"]');
		if(blokItem.length > 0) {
			blokItem.find('.icerik-baslik-text').text(baslik);
		}
	});
	
	// İçerik kaydet
	$(document).on('click', '.icerik-kaydet-btn', function() {
		var icerikId = $(this).data('icerik-id');
		var baslik = $('.icerik-baslik[data-icerik-id="' + icerikId + '"]').val();
		// TinyMCE kullanılıyorsa içeriği editörden al (aksi halde kalın/italik kaydedilmez, buton da boş gider)
		var textareaId = (typeof icerikId === 'string' && icerikId.toString().startsWith('yeni_'))
			? ('myTextarea9' + icerikId.toString().replace('yeni_', '')) : ('myTextarea' + icerikId);
		var ed = (typeof tinymce !== 'undefined') ? tinymce.get(textareaId) : null;
		var aciklama = ed ? ed.getContent() : $('.icerik-aciklama[data-icerik-id="' + icerikId + '"]').val();
		var sira = $('.icerik-sira[data-icerik-id="' + icerikId + '"]').val();
		var video = $('.icerik-video[data-icerik-id="' + icerikId + '"]').val();
		var gorselFormat = $('.icerik-gorsel-format[data-icerik-id="' + icerikId + '"]').val();
		if (gorselFormat === undefined || gorselFormat === '') gorselFormat = '0';
		var icerikTipi = $('.icerik-tipi[data-icerik-id="' + icerikId + '"]').val() || '0';
		var resimInput = $('.icerik-resim-input[data-icerik-id="' + icerikId + '"]')[0];
		var gorselDesktopInput = $('.icerik-gorsel-desktop[data-icerik-id="' + icerikId + '"]')[0];
		var gorselMobilInput = $('.icerik-gorsel-mobil[data-icerik-id="' + icerikId + '"]')[0];

		var formData = new FormData();
		formData.append('islem', icerikId.toString().startsWith('yeni_') ? 'program_icerik_ekle' : 'program_icerik_guncelle');
		formData.append('program_id', programId);
		if(!icerikId.toString().startsWith('yeni_')) {
			formData.append('icerik_id', icerikId);
		}
		formData.append('baslik', baslik);
		formData.append('aciklama', aciklama);
		formData.append('sira', sira);
		formData.append('video_url', video);
		formData.append('gorsel_format', gorselFormat);
		formData.append('icerik_tipi', icerikTipi);
		if(resimInput && resimInput.files.length > 0) {
			for(var i = 0; i < resimInput.files.length; i++) {
				formData.append('resim[]', resimInput.files[i]);
			}
		}
		if(gorselDesktopInput && gorselDesktopInput.files.length > 0) {
			formData.append('gorsel_desktop', gorselDesktopInput.files[0]);
		}
		if(gorselMobilInput && gorselMobilInput.files.length > 0) {
			formData.append('gorsel_mobil', gorselMobilInput.files[0]);
		}
		
		$.ajax({
			url: '../_class/yonetim_islem.php',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			dataType: 'json',
			success: function(response) {
				if(response.success) {
					alert('İçerik başarıyla kaydedildi!');
					if(icerikId.toString().startsWith('yeni_')) {
						location.reload();
					}
				} else {
					alert('Hata: ' + response.message);
				}
			},
			error: function() {
				alert('Bir hata oluştu!');
			}
		});
	});
	
	// İçerik sil
	$(document).on('click', '.icerik-sil-btn', function() {
		if(!confirm('Bu içerik bloğunu silmek istediğinizden emin misiniz?')) return;
		
		var icerikId = $(this).data('icerik-id');
		
		if(icerikId.toString().startsWith('yeni_')) {
			$('.icerik-blok-item[data-icerik-id="' + icerikId + '"]').remove();
			return;
		}
		
		$.ajax({
			url: '../_class/yonetim_islem.php',
			type: 'POST',
			data: {
				islem: 'program_icerik_sil',
				icerik_id: icerikId
			},
			dataType: 'json',
			success: function(response) {
				if(response.success) {
					$('.icerik-blok-item[data-icerik-id="' + icerikId + '"]').remove();
					alert('İçerik başarıyla silindi!');
				} else {
					alert('Hata: ' + response.message);
				}
			},
			error: function() {
				alert('Bir hata oluştu!');
			}
		});
	});
	
	// İçerik durum değiştir
	$(document).on('change', '.icerik-durum', function() {
		var icerikId = $(this).data('icerik-id');
		var durum = $(this).is(':checked') ? 1 : 0;
		
		$.ajax({
			url: '../_class/yonetim_islem.php',
			type: 'POST',
			data: {
				islem: 'program_icerik_durum',
				icerik_id: icerikId,
				durum: durum
			},
			dataType: 'json',
			success: function(response) {
				if(!response.success) {
					alert('Hata: ' + response.message);
					location.reload();
				}
			},
			error: function() {
				alert('Bir hata oluştu!');
				location.reload();
			}
		});
	});
	
	// Görsel sil
	$(document).on('click', '.icerik-resim-sil', function() {
		if(!confirm('Görseli silmek istediğinizden emin misiniz?')) return;
		
		var icerikId = $(this).data('icerik-id');
		var resimAd = $(this).data('resim-ad') || '';
		
		$.ajax({
			url: '../_class/yonetim_islem.php',
			type: 'POST',
			data: {
				islem: 'program_icerik_resim_sil',
				icerik_id: icerikId,
				resim_ad: resimAd
			},
			dataType: 'json',
			success: function(response) {
				if(response.success) {
					location.reload();
				} else {
					alert('Hata: ' + response.message);
				}
			},
			error: function() {
				alert('Bir hata oluştu!');
			}
		});
	});
	
	// Tek görsel sil (desktop/mobil)
	$(document).on('click', '.icerik-gorsel-tek-sil', function() {
		if(!confirm('Görseli silmek istediğinizden emin misiniz?')) return;
		var icerikId = $(this).data('icerik-id');
		var tip = $(this).data('tip'); // 'desktop' veya 'mobil'
		var el = $(this);
		$.ajax({
			url: '../_class/yonetim_islem.php',
			type: 'POST',
			data: {
				islem: 'program_icerik_gorsel_tek_sil',
				icerik_id: icerikId,
				tip: tip
			},
			dataType: 'json',
			success: function(response) {
				if(response.success) {
					el.closest('.modern-image-preview').remove();
				} else {
					alert('Hata: ' + response.message);
				}
			},
			error: function() {
				alert('Bir hata oluştu!');
			}
		});
	});

	// Dosya yükleme sistemi - Event delegation ile tüm dosya input'ları için
	$(document).on('click', '.file-upload-browse', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		var modernFileUpload = $(this).closest('.modern-file-upload');
		var fileInput = modernFileUpload.find('.file-upload-default, .icerik-resim-input').first();
		if(fileInput.length > 0) {
			fileInput.trigger('click');
		}
	});
	
	// Dosya seçildiğinde input alanını güncelle
	$(document).on('change', '.file-upload-default, .icerik-resim-input', function() {
		var files = this.files;
		if(files && files.length > 0) {
			var names = [];
			for(var i = 0; i < files.length; i++) {
				names.push(files[i].name);
			}
			var label = files.length === 1 ? names[0] : (files.length + ' dosya seçildi');
			$(this).closest('.modern-file-upload').find('.file-upload-info').val(label);
		}
	});
	
	// ========== DERSLİK PROGRAMI İŞLEMLERİ ==========
	var derslikSayac = <?php echo isset($derslikler) ? count($derslikler) : 0; ?>;
	
	// Yeni derslik programı ekle
	$('#yeniDerslikEkle').on('click', function() {
		derslikSayac++;
		var accordionId = 'derslikAccordion_yeni_' + derslikSayac;
		var headingId = 'heading_derslik_yeni_' + derslikSayac;
		var yeniBlok = `
			<div class="card derslik-blok-item" data-derslik-id="yeni_${derslikSayac}">
				<div class="card-header derslik-blok-header" id="${headingId}">
					<h5 class="mb-0">
						<button class="btn btn-link derslik-accordion-toggle" type="button" data-toggle="collapse" data-target="#${accordionId}" aria-expanded="true" aria-controls="${accordionId}">
							<i class="fas fa-chevron-down accordion-icon"></i>
							<span class="derslik-baslik-text">Yeni Derslik Programı #${derslikSayac}</span>
							<span class="badge badge-success ml-2">Aktif</span>
						</button>
					</h5>
					<div class="derslik-blok-actions">
						<button type="button" class="btn btn-danger btn-sm derslik-sil-btn" data-derslik-id="yeni_${derslikSayac}">
							<i class="fas fa-trash"></i> Sil
						</button>
					</div>
				</div>
				<div id="${accordionId}" class="collapse show" aria-labelledby="${headingId}" data-parent="#derslikAccordion">
					<div class="card-body">
						<div class="modern-form-grid">
							<div class="modern-form-group">
								<label>Sıra</label>
								<input type="number" class="form-control form-control-sm derslik-sira" data-derslik-id="yeni_${derslikSayac}" value="${derslikSayac}" min="0">
							</div>
							<div class="modern-form-group">
								<label>Şehir <span class="text-danger">*</span></label>
								<input type="text" class="form-control form-control-sm derslik-sehir" data-derslik-id="yeni_${derslikSayac}" required>
							</div>
							<div class="modern-form-group">
								<label>Kategori (Sınıf)</label>
								<select class="form-control form-control-sm derslik-kategori" data-derslik-id="yeni_${derslikSayac}">
									<option value="">Kategori Seçiniz</option>
									<?php 
									$kategoriler = $db->query("SELECT * FROM derslik_kategorileri WHERE durum = 1 AND dil = '{$_SESSION['admin_dil']}' ORDER BY sira ASC, adi ASC")->fetchAll(PDO::FETCH_ASSOC);
									foreach($kategoriler as $kat): 
									?>
									<option value="<?php echo $kat['id']; ?>"><?php echo htmlspecialchars($kat['adi']); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="modern-form-group">
								<label>Sınıf (Manuel)</label>
								<input type="text" class="form-control form-control-sm derslik-sinif" data-derslik-id="yeni_${derslikSayac}" placeholder="Kategori seçilmezse manuel girebilirsiniz">
							</div>
							<div class="modern-form-group">
								<label>Başlık</label>
								<input type="text" class="form-control form-control-sm derslik-baslik" data-derslik-id="yeni_${derslikSayac}" placeholder="Ders başlığı">
							</div>
						</div>
						<div class="modern-form-grid">
							<div class="modern-form-group">
								<label>Tarih <span class="text-danger">*</span></label>
								<input type="date" class="form-control form-control-sm derslik-tarih" data-derslik-id="yeni_${derslikSayac}" value="<?php echo date('Y-m-d'); ?>" required>
							</div>
							<div class="modern-form-group">
								<label>Gün <span class="text-danger">*</span></label>
								<select class="form-control form-control-sm derslik-gun" data-derslik-id="yeni_${derslikSayac}" required>
									<option value="">Seçiniz</option>
									<option value="Pazartesi">Pazartesi</option>
									<option value="Salı">Salı</option>
									<option value="Çarşamba">Çarşamba</option>
									<option value="Perşembe">Perşembe</option>
									<option value="Cuma">Cuma</option>
									<option value="Cumartesi">Cumartesi</option>
									<option value="Pazar">Pazar</option>
								</select>
							</div>
							<div class="modern-form-group">
								<label>Saat <span class="text-danger">*</span></label>
								<input type="text" class="form-control form-control-sm derslik-saat" data-derslik-id="yeni_${derslikSayac}" required placeholder="Örn: 09:00-10:30">
							</div>
						</div>
						<div class="modern-form-grid">
							<div class="modern-form-group">
								<label>Kontenjan <span class="text-danger">*</span></label>
								<input type="number" class="form-control form-control-sm derslik-kontenjan" data-derslik-id="yeni_${derslikSayac}" value="10" min="1" required>
							</div>
							<div class="modern-form-group">
								<label>Katılımcı Sayısı <span class="text-danger">*</span></label>
								<input type="number" class="form-control form-control-sm derslik-katilimci" data-derslik-id="yeni_${derslikSayac}" value="0" min="0" required>
							</div>
							<div class="modern-form-group">
								<label>Aile Katılımı</label>
								<select class="form-control form-control-sm derslik-aile-katilim" data-derslik-id="yeni_${derslikSayac}">
									<option value="0">Hayır</option>
									<option value="1">Evet</option>
								</select>
							</div>
							<div class="modern-form-group">
								<label>Durum</label>
								<select class="form-control form-control-sm derslik-durum" data-derslik-id="yeni_${derslikSayac}">
									<option value="0">Boş (Kayıt Alınabilir)</option>
									<option value="1">Dolu (Kontenjan Dolmuş)</option>
									<option value="2">Bekleme Listesi</option>
								</select>
							</div>
						</div>
						<button type="button" class="btn btn-success btn-sm derslik-kaydet-btn" data-derslik-id="yeni_${derslikSayac}">
							<i class="fas fa-save"></i> Kaydet
						</button>
					</div>
				</div>
			</div>
		`;
		$('#derslikProgramlari').append(yeniBlok);
		
		// Kategori seçildiğinde sınıf alanını otomatik doldur
		$('.derslik-kategori[data-derslik-id="yeni_' + derslikSayac + '"]').on('change', function(){
			if($(this).val() != ''){
				var kategoriAdi = $(this).find('option:selected').text();
				$('.derslik-sinif[data-derslik-id="yeni_' + derslikSayac + '"]').val(kategoriAdi);
			}
		});
		
		// Başlık, gün, saat değiştiğinde accordion başlığını güncelle
		$('.derslik-baslik[data-derslik-id="yeni_' + derslikSayac + '"], .derslik-gun[data-derslik-id="yeni_' + derslikSayac + '"], .derslik-saat[data-derslik-id="yeni_' + derslikSayac + '"]').on('input change', function() {
			var baslik = $('.derslik-baslik[data-derslik-id="yeni_' + derslikSayac + '"]').val();
			var gun = $('.derslik-gun[data-derslik-id="yeni_' + derslikSayac + '"]').val();
			var saat = $('.derslik-saat[data-derslik-id="yeni_' + derslikSayac + '"]').val();
			var baslikText = baslik || (gun && saat ? (gun + ' - ' + saat) : 'Yeni Derslik Programı #' + derslikSayac);
			$('[data-derslik-id="yeni_' + derslikSayac + '"]').find('.derslik-baslik-text').text(baslikText);
		});
	});
	
	// Kategori seçildiğinde sınıf alanını otomatik doldur (mevcut derslikler için)
	$(document).on('change', '.derslik-kategori', function(){
		var derslikId = $(this).data('derslik-id');
		if($(this).val() != ''){
			var kategoriAdi = $(this).find('option:selected').text();
			$('.derslik-sinif[data-derslik-id="' + derslikId + '"]').val(kategoriAdi);
		}
	});
	
	// Başlık, gün, saat değiştiğinde accordion başlığını güncelle (mevcut derslikler için)
	$(document).on('input change', '.derslik-baslik, .derslik-gun, .derslik-saat', function() {
		var derslikId = $(this).data('derslik-id');
		var baslik = $('.derslik-baslik[data-derslik-id="' + derslikId + '"]').val();
		var gun = $('.derslik-gun[data-derslik-id="' + derslikId + '"]').val();
		var saat = $('.derslik-saat[data-derslik-id="' + derslikId + '"]').val();
		var baslikText = baslik || (gun && saat ? (gun + ' - ' + saat) : 'Derslik Programı');
		var blokItem = $('.derslik-blok-item[data-derslik-id="' + derslikId + '"]');
		if(blokItem.length > 0) {
			blokItem.find('.derslik-baslik-text').text(baslikText);
		}
	});
	
	// Derslik kaydet
	$(document).on('click', '.derslik-kaydet-btn', function() {
		var derslikId = $(this).data('derslik-id');
		var sira = $('.derslik-sira[data-derslik-id="' + derslikId + '"]').val();
		var sehir = $('.derslik-sehir[data-derslik-id="' + derslikId + '"]').val();
		var kategori_id = $('.derslik-kategori[data-derslik-id="' + derslikId + '"]').val() || '';
		var sinif = $('.derslik-sinif[data-derslik-id="' + derslikId + '"]').val();
		var baslik = $('.derslik-baslik[data-derslik-id="' + derslikId + '"]').val();
		var tarih = $('.derslik-tarih[data-derslik-id="' + derslikId + '"]').val();
		var gun = $('.derslik-gun[data-derslik-id="' + derslikId + '"]').val();
		var saat = $('.derslik-saat[data-derslik-id="' + derslikId + '"]').val();
		var kontenjan = $('.derslik-kontenjan[data-derslik-id="' + derslikId + '"]').val();
		var katilimci = $('.derslik-katilimci[data-derslik-id="' + derslikId + '"]').val();
		var aile_katilim = $('.derslik-aile-katilim[data-derslik-id="' + derslikId + '"]').val();
		var durum = $('.derslik-durum[data-derslik-id="' + derslikId + '"]').val();
		
		if(!sehir || !tarih || !gun || !saat || !kontenjan || !katilimci) {
			alert('Lütfen zorunlu alanları doldurun!');
			return;
		}
		
		$.ajax({
			url: '../_class/yonetim_islem.php',
			type: 'POST',
			data: {
				islem: derslikId.toString().startsWith('yeni_') ? 'program_derslik_ekle' : 'program_derslik_guncelle',
				program_id: programId,
				derslik_id: derslikId.toString().startsWith('yeni_') ? '' : derslikId,
				sira: sira,
				sehir: sehir,
				kategori_id: kategori_id,
				sinif: sinif,
				baslik: baslik,
				tarih: tarih,
				gun: gun,
				saat: saat,
				kontenjan: kontenjan,
				katilimci: katilimci,
				aile_katilim: aile_katilim,
				durum: durum
			},
			dataType: 'json',
			success: function(response) {
				if(response.success) {
					alert('Derslik programı başarıyla kaydedildi!');
					if(derslikId.toString().startsWith('yeni_')) {
						location.reload();
					}
				} else {
					alert('Hata: ' + response.message);
				}
			},
			error: function() {
				alert('Bir hata oluştu!');
			}
		});
	});
	
	// Derslik sil
	$(document).on('click', '.derslik-sil-btn', function() {
		if(!confirm('Bu derslik programını silmek istediğinizden emin misiniz?')) return;
		
		var derslikId = $(this).data('derslik-id');
		
		if(derslikId.toString().startsWith('yeni_')) {
			$('.derslik-blok-item[data-derslik-id="' + derslikId + '"]').remove();
			return;
		}
		
		$.ajax({
			url: '../_class/yonetim_islem.php',
			type: 'POST',
			data: {
				islem: 'program_derslik_sil',
				derslik_id: derslikId
			},
			dataType: 'json',
			success: function(response) {
				if(response.success) {
					$('.derslik-blok-item[data-derslik-id="' + derslikId + '"]').remove();
					alert('Derslik programı başarıyla silindi!');
				} else {
					alert('Hata: ' + response.message);
				}
			},
			error: function() {
				alert('Bir hata oluştu!');
			}
		});
	});
	
	// Derslik aktif/pasif durum değiştir
	$(document).on('change', '.derslik-aktif', function() {
		var derslikId = $(this).data('derslik-id');
		var aktif = $(this).is(':checked') ? 1 : 0;
		
		$.ajax({
			url: '../_class/yonetim_islem.php',
			type: 'POST',
			data: {
				islem: 'program_derslik_aktif',
				derslik_id: derslikId,
				aktif: aktif
			},
			dataType: 'json',
			success: function(response) {
				if(!response.success) {
					alert('Hata: ' + response.message);
					location.reload();
				} else {
					// Badge'i güncelle
					var badge = $('.derslik-blok-item[data-derslik-id="' + derslikId + '"]').find('.badge');
					if(aktif == 1) {
						badge.removeClass('badge-danger').addClass('badge-success').text('Aktif');
					} else {
						badge.removeClass('badge-success').addClass('badge-danger').text('Pasif');
					}
				}
			},
			error: function() {
				alert('Bir hata oluştu!');
				location.reload();
			}
		});
	});
	
	// Renk seçici senkronizasyonu
	$('#banner_renk1').on('input', function() {
		$('#banner_renk1_text').val($(this).val());
	});
	$('#banner_renk1_text').on('input', function() {
		var val = $(this).val();
		if(/^#[0-9A-Fa-f]{6}$/.test(val)) {
			$('#banner_renk1').val(val);
		}
	});
	
	$('#banner_ara_serit').on('input', function() {
		$('#banner_ara_serit_text').val($(this).val());
	});
	$('#banner_ara_serit_text').on('input', function() {
		var val = $(this).val();
		if(/^#[0-9A-Fa-f]{6}$/.test(val)) {
			$('#banner_ara_serit').val(val);
		}
	});
	
	$('#banner_renk2').on('input', function() {
		$('#banner_renk2_text').val($(this).val());
	});
	$('#banner_renk2_text').on('input', function() {
		var val = $(this).val();
		if(/^#[0-9A-Fa-f]{6}$/.test(val)) {
			$('#banner_renk2').val(val);
		}
	});
	
	$('#tab_aktif_renk').on('input', function() {
		$('#tab_aktif_renk_text').val($(this).val());
	});
	$('#tab_aktif_renk_text').on('input', function() {
		var val = $(this).val();
		if(/^#[0-9A-Fa-f]{6}$/.test(val)) {
			$('#tab_aktif_renk').val(val);
		}
	});
}); // $(document).ready() kapanışı
</script>
<?php } ?>
