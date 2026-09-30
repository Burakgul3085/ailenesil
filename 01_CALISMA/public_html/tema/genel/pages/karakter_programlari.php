<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
$page = @intval($_GET['s']);
if(!$page) $page = 1;
$ttsorgu = $db->prepare("SELECT COUNT(*) FROM programlar WHERE durum = ? AND dil = ?");
$ttsorgu->execute(array("1",$_SESSION['k_dil']));
$total = $ttsorgu->fetchColumn();
$limit= 12;
$page_count = ceil($total/$limit);
if($page > $page_count) $page = 1;
$show = $page * $limit - $limit;
$BSorgu = $db->prepare("SELECT * FROM programlar WHERE durum = ? AND dil = ? ORDER BY sira ASC LIMIT $show,$limit");
$BSorgu->execute(array("1",$_SESSION['k_dil']));
$Bislem = $BSorgu->fetchALL(PDO::FETCH_ASSOC);
$menubul 	= $db->query("SELECT * FROM menu WHERE menu_url = '".$htc['karakterprogramlariurl']."' OR link = '".$htc['karakterprogramlariurl']."' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
$menubas 	= $db->query("SELECT * FROM menu WHERE id = '{$menubul['menu_ust']}' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
?>
<section class="page-section">
	<div class="bg-white">
		<div class="col-12 p-0 banner">
			<img src="<?php echo tema;?>/uploads/arkaplan/arkaplan16/<?php echo $arkaplan['arkaplan16'];?>" alt="Karakter ve Sosyal Gelişim Programları">
			<div class="slide-overlay"></div>
		</div>
		<div class="container banner-fix">
			<div class="row">
				<div class="col-lg-<?php echo($moduller['alan23'] == "1" ? '9 offset-lg-3' : '12');?> col-md-<?php echo($moduller['alan23'] == "1" ? '7 offset-md-5' : '12');?> z-index-9">
					<ol class="breadcrumb z-index-9">
						<li><a href="<?php echo $htc['anaurl'];?><?php echo $html;?>"> <i class="fa fa-home"></i> </a></li>
						<?php if($menubas['menu_isim'] != ""){?>
						<li><a href="<?php echo($menubas['menu_url'] == "0" ? $menubas['link'] : $menubas['menu_url']);?>"><?php echo cVCLmHLxbS_ilkbuyuk($menubas['menu_isim']);?></a></li>
						<?php }?>
					</ol>
				</div>
				<?php include('leftbar.php');?>
				<div class="col-lg-<?php echo($moduller['alan23'] == "1" ? '9' : '12');?> col-md-<?php echo($moduller['alan23'] == "1" ? '7' : '12');?> z-index-9">
					<div class="page-content">
						<h2 class="page-title">
							Karakter ve Sosyal Gelişim Programı
						</h2>
						<?php if(!empty($htc['karakterprogramlari_text'])){ ?>
						<div class="kp-text-content kp-text-inline">
							<?php echo $htc['karakterprogramlari_text']; ?>
						</div>
						<?php } ?>
						<?php
						$kpVideo = isset($htc['karakterprogramlari_video']) ? trim($htc['karakterprogramlari_video']) : '';
						if(!empty($kpVideo)):
							// YouTube/Vimeo embed URL'e çevir
							$embedUrl = '';
							if(preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $kpVideo, $m)) {
								$embedUrl = 'https://www.youtube.com/embed/' . $m[1];
							} elseif(preg_match('/vimeo\.com\/(\d+)/', $kpVideo, $m)) {
								$embedUrl = 'https://player.vimeo.com/video/' . $m[1];
							}
							if(!empty($embedUrl)):
						?>
						<div class="kp-video-section">
							<div class="kp-video-wrapper">
								<iframe src="<?php echo $embedUrl; ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
							</div>
						</div>
						<?php endif; endif; ?>
						<div class="row">
							<div class="col-lg-12">
								<?php if($BSorgu->rowCount() != "0"){?>
								<div class="programlar-grid-list">
									<div class="row">
										<?php foreach ( $Bislem as $BSonuc ){?>
										<div class="col-lg-6 col-md-6 mb-4">
											<div class="program-list-card">
												<div class="program-list-image">
													<?php if($BSonuc['resim']): ?>
														<img src="<?php echo tema;?>/uploads/programlar/<?php echo $BSonuc['resim']; ?>" alt="<?php echo $BSonuc['baslik']; ?>">
													<?php else: ?>
														<div class="program-list-placeholder">
															<i class="fas fa-graduation-cap"></i>
														</div>
													<?php endif; ?>
												</div>
												<div class="program-list-info">
													<h3 class="program-list-title"><?php echo $BSonuc['baslik']; ?></h3>
													<p class="program-list-desc"><?php echo cVCLmHLxbS_kisa($BSonuc['aciklama'], 150); ?></p>
													<a href="<?php echo $htc['programdetayurl']; ?>/<?php echo $BSonuc['seo']; ?><?php echo $html;?>" class="program-list-button">
														<?=@$dil['txt237'];?>
													</a>
												</div>
											</div>
										</div>
										<?php }?>
									</div>
								</div>
								<div class="pagination">
									<ul>
									<?php if($limit < $total && $limit > 0){
									$showing = 3;
									if($page > 1){ $previous = $page - 1;?>
									<li class="onceki_sayfa"><a href="<?php echo $htc['karakterprogramlariurl'];?>/<?php echo $previous;?><?php echo $html;?>"><i class="fas fa-angle-left"></i></a></li>
									<?php }
									for($i= $page - $showing; $i < $page + $showing + 1; $i++){
									if($i > 0 and $i <= $page_count){
									if($i == $page){?>
									<li><a class="secili" href="javascript:void(0)"><?php echo $i; ?></a></li>
									<?php }else{?>
									<li><a href="<?php echo $htc['karakterprogramlariurl'];?>/<?php echo $i; ?><?php echo $html;?>"><?php echo $i; ?></a></li>
									<?php } } } if($page != $page_count){?>
									<?php  $next = $page +1;?>
									<li class="sonraki_sayfa"><a href="<?php echo $htc['karakterprogramlariurl'];?>/<?php echo $next; ?><?php echo $html;?>"><i class="fas fa-angle-right"></i></a></li>
									<?php }} ?>
									</ul>
								</div>
								<?php }else{?>
								<div class="alert alert-warning text-left" style="width:100%;" role="alert">
									<p><?=@$dil['txt139'];?></p>
									<?=@$dil['txt140'];?></br>
									<?=@$dil['txt141'];?>
								</div>
								<?php }?>
							</div>
							<?php displayContactSection($page_name, $db, $dil, $sayfalink); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<style>
.kp-text-banner {
	position: relative;
	padding: 48px 0 44px;
	background: #fffbfb;
	overflow: hidden;
	border-bottom: 3px solid #c0392b;
}
.kp-text-banner::before {
	content: '';
	position: absolute;
	top: 0; left: 0;
	width: 6px;
	height: 100%;
	background: linear-gradient(180deg, #c0392b 0%, #e74c3c 50%, #f39c12 100%);
}
.kp-text-banner-bg {
	position: absolute;
	top: -40px; right: -40px;
	width: 280px; height: 280px;
	border-radius: 50%;
	background: radial-gradient(circle, rgba(192,57,43,0.06) 0%, transparent 70%);
	pointer-events: none;
}
.kp-text-content {
	color: #3d2c2c;
	font-size: 16px;
	line-height: 1.85;
	padding-left: 20px;
}
.kp-text-content h1, .kp-text-content h2, .kp-text-content h3,
.kp-text-content h4, .kp-text-content h5, .kp-text-content h6 {
	color: #c0392b;
	font-weight: 800;
	margin-bottom: 10px;
}
.kp-text-content p {
	color: #4a3636;
	margin-bottom: 10px;
}
.kp-text-content strong, .kp-text-content b {
	color: #2c1a1a;
	font-weight: 700;
}
.kp-text-content a {
	color: #c0392b;
	text-decoration: underline;
	text-underline-offset: 3px;
}
.kp-text-content a:hover {
	color: #e74c3c;
}
.kp-text-content ul, .kp-text-content ol {
	color: #4a3636;
	padding-left: 24px;
}
.kp-text-content ul li::marker {
	color: #c0392b;
}
.kp-text-inline {
	margin-bottom: 25px;
	padding: 0;
	border-left: 4px solid #c0392b;
	padding-left: 16px;
}
@media (max-width: 768px) {
	.kp-text-inline { font-size: 14.5px; }
}
.kp-video-section {
	margin-bottom: 30px;
	width: 100%;
}
.kp-video-wrapper {
	position: relative;
	padding-bottom: 56.25%;
	height: 0;
	width: 100%;
	border-radius: 12px;
	overflow: hidden;
	box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.kp-video-wrapper iframe {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
}
.programlar-grid-list {
	margin-bottom: 30px;
}

.program-list-card {
	background: #ffffff;
	border-radius: 12px;
	overflow: hidden;
	box-shadow: 0 4px 12px rgba(0,0,0,0.1);
	transition: transform 0.3s ease, box-shadow 0.3s ease;
	height: 100%;
	display: flex;
	flex-direction: column;
}

.program-list-card:hover {
	transform: translateY(-5px);
	box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.program-list-image {
	width: 100%;
	height: 250px;
	overflow: hidden;
	position: relative;
}

.program-list-image img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	transition: transform 0.3s ease;
}

.program-list-card:hover .program-list-image img {
	transform: scale(1.05);
}

.program-list-placeholder {
	width: 100%;
	height: 100%;
	background: linear-gradient(135deg, #C9EBF2 0%, #DFF29C 100%);
	display: flex;
	align-items: center;
	justify-content: center;
}

.program-list-placeholder i {
	font-size: 4rem;
	color: #2C5F5F;
}

.program-list-info {
	padding: 25px;
	flex: 1;
	display: flex;
	flex-direction: column;
}

.program-list-title {
	font-size: 1.5rem;
	font-weight: 700;
	color: #2C5F5F;
	margin-bottom: 15px;
	font-family: 'Arial', sans-serif;
}

.program-list-desc {
	font-size: 1rem;
	color: #666;
	line-height: 1.6;
	margin-bottom: 20px;
	font-family: 'Arial', sans-serif;
	flex: 1;
}

.program-list-button {
	display: inline-block;
	background: #2C5F5F;
	color: #ffffff;
	padding: 10px 20px;
	border-radius: 6px;
	text-decoration: none;
	font-size: 1rem;
	font-weight: 500;
	transition: background 0.3s ease;
	font-family: 'Arial', sans-serif;
	text-align: center;
	align-self: flex-start;
}

.program-list-button:hover {
	background: #1e4a4a;
	color: #ffffff;
	text-decoration: none;
}

@media (max-width: 768px) {
	.program-list-image {
		height: 200px;
	}

	.program-list-title {
		font-size: 1.25rem;
	}

	.program-list-desc {
		font-size: 0.95rem;
	}
}
</style>
<?php include('slider_menu.php');?>
