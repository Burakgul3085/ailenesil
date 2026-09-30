<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(strip_tags(isset($_GET['id'])))
{
	$Sorgu = $db->prepare("SELECT * FROM etkinlikler WHERE seo = ? AND durum = ? AND dil = ?");
	$Sorgu->execute(array($_GET['id'],"1",$_SESSION['k_dil']));
	if($Sorgu->rowCount())
	{
		$Sonuc = $Sorgu->fetch(PDO::FETCH_ASSOC);
	}
	else
	{
		header("Location:".$url.(altklasor == "1" ? '/' : '')."404".$html."");
		exit();
	}
}
else
{
	$Sorgu = $db->prepare("SELECT * FROM etkinlikler WHERE durum = ? AND dil = ? ORDER BY id ASC");
	$Sorgu->execute(array("1",$_SESSION['k_dil']));
	if($Sorgu->rowCount())
	{
		$Sonuc = $Sorgu->fetch(PDO::FETCH_ASSOC);
	}
	else
	{
		header("Location:".$url.(altklasor == "1" ? '/' : '')."404".$html."");
		exit();
	}
}
$tarihler 	= explode(" ",cVCLmHLxbS_unixtarih($Sonuc['baslama_tarih']));
$menubul 	= $db->query("SELECT * FROM menu WHERE menu_url = '".$htc['etkinlikurl']."' OR link = '".$htc['etkinlikurl']."' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
$menubas 	= $db->query("SELECT * FROM menu WHERE id = '{$menubul['menu_ust']}' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);	
?>
<!-- PAGE SECTİON BAŞLANGIÇ -->
<section class="page-section">
	<div class="bg-white">
		<div class="col-12 p-0 banner">
			<img src="<?php echo tema;?>/uploads/arkaplan/arkaplan13/<?php echo $arkaplan['arkaplan13'];?>" alt="<?php echo $Sonuc['adi'];?>">
			<div class="slide-overlay"></div>
		</div>
		<div class="container banner-fix">
			<div class="row">
				<div class="col-lg-<?php echo($moduller['alan23'] == "1" ? '9 offset-lg-3' : '12');?> col-md-<?php echo($moduller['alan23'] == "1" ? '7 offset-md-5' : '12');?> z-index-9">
					<ol class="breadcrumb">
						<li><a href="<?php echo $htc['anaurl'];?><?php echo $html;?>"> <i class="fa fa-home"></i> </a></li>						
						<?php if($menubas['menu_isim'] != ""){?>
						<li><a href="<?php echo($menubas['menu_url'] == "0" ? $menubas['link'] : $menubas['menu_url']);?>"><?php echo cVCLmHLxbS_ilkbuyuk($menubas['menu_isim']);?></a></li>
						<?php }?>
						<li><?php echo $Sonuc['adi'];?></li>
					</ol>
				</div>
				<?php include('leftbar.php');?>
				<div class="col-lg-<?php echo($moduller['alan23'] == "1" ? '9' : '12');?> col-md-<?php echo($moduller['alan23'] == "1" ? '7' : '12');?> z-index-9">
					<div class="page-content">
						<h2 class="page-title"><?php echo $Sonuc['adi'];?></h2>
						
						<div class="etkinlikdetay single large">
							<div class="etkinlikdetay-card">
								<div class="etkinlikdetay-card__content">
									<div class="etkinlikdetay-photo">
										<a href="<?php echo tema;?>/uploads/etkinlikler/<?php echo $Sonuc['resim']; ?>" data-fancybox=""><img src="<?php echo tema;?>/uploads/etkinlikler/<?php echo $Sonuc['resim']; ?>" alt="<?php echo $Sonuc['adi'];?>"></a>
									</div>
									<div class="etkinlikdetay-description">
										<div class="etkinlikdetay-info">
											<ul>
												<li>
													<a>
														<span class="icon"><i class="far fa-calendar-alt"></i></span>
														<div class="text"><?php echo $tarihler[0];?></div>
													</a>
												</li>
												<li>
													<a>
														<span class="icon"><i class="far fa-clock"></i></span>
														<div class="text"><?php echo $tarihler[1];?></div>
													</a>
												</li>
												<li>
													<a>
														<span class="icon"><i class="fas fa-map-marker-alt"></i></span>
														<div class="text"><?php echo $Sonuc['yer'];?></div>
													</a>
												</li>
												<li>
													<a target="_blank" href="<?php echo $Sonuc['gmap'];?>">
														<span class="icon"><i class="fas fa-map-marked-alt"></i></span>
														<div class="text"><?=@$dil['txt146'];?></div>
													</a>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="detay">
							<?php echo $Sonuc['aciklama']; ?>
						</div>
						
						<?php if(isset($moduller['alan34']) && $moduller['alan34'] == '1'){ ?>
						<div class="donation-button-wrapper mt-4 mb-4">
							<a href="<?php echo $htc['bagisurl']; ?><?php echo $html;?>" class="donation-button">
								<i class="fas fa-heart"></i>
								<span><?=@$dil['txt423'];?></span>
							</a>
							<p class="donation-hint"><?=@$dil['txt424'];?></p>
						</div>
						<?php } ?>
						
						<div class="row py-4 haber-detay-box">
							<div class="col-12 mt-4">
								<h2 class="page-title">
									<?=@$dil['txt147'];?>
								</h2>
								
								<div class="ordered-list">
									<ul class="news-list-animated">
									<?php $DIGERSorgu = $db->prepare("SELECT * FROM etkinlikler WHERE durum = ? AND dil = ? ORDER BY id DESC LIMIT 10");
									$DIGERSorgu->execute(array("1",$_SESSION['k_dil']));
									$DIGERislem = $DIGERSorgu->fetchALL(PDO::FETCH_ASSOC);?>
										<?php foreach ( $DIGERislem as $index => $DIGERSonuc ){?>
										<li class="is-active news-item" id="<?php echo $DIGERSonuc['id']; ?>" style="animation-delay: <?php echo $index * 0.05; ?>s;">
											<a href="<?php echo $htc['etkinlikdetayurl']; ?>/<?php echo $DIGERSonuc['seo']; ?><?php echo $html;?>">
												<h3 class="text">
													<span class="icon"><i class="far fa-calendar-alt"></i></span>
													<?php echo $DIGERSonuc['adi'];?> </h3>
												<div class="date"><?php echo cVCLmHLxbS_unixtarih($DIGERSonuc['baslama_tarih']);?></div>
											</a>
										</li>
										<?php }?>
									</ul>
									<?php $kayit	= $db->query("SELECT * FROM  etkinlikler WHERE durum = '1' AND dil = '{$_SESSION['k_dil']}'")->rowCount();?>
									<?php if($kayit > 10){?>
									<?php }?>								
								</div>	
							</div>
						</div>
						<?php displayContactSection($page_name, $db, $dil, $sayfalink); ?> 
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- PAGE SECTİON BİTİŞ -->
<style>
.donation-button-wrapper {
	text-align: center;
	padding: 50px 30px;
	margin: 50px 0;
	background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 50%, #ffffff 100%);
	border-radius: 20px;
	position: relative;
	overflow: hidden;
	border: 2px solid transparent;
	background-clip: padding-box;
	box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
	animation: fadeInUp 0.8s ease-out;
}

.donation-button-wrapper::before {
	content: '';
	position: absolute;
	top: -2px;
	left: -2px;
	right: -2px;
	bottom: -2px;
	background: linear-gradient(135deg, #e74c3c, #c0392b, #e74c3c, #c0392b);
	background-size: 300% 300%;
	border-radius: 20px;
	z-index: -1;
	animation: gradientShift 3s ease infinite;
	opacity: 0.3;
}

.donation-button-wrapper::after {
	content: '';
	position: absolute;
	top: 50%;
	left: 50%;
	width: 200px;
	height: 200px;
	background: radial-gradient(circle, rgba(231, 76, 60, 0.1) 0%, transparent 70%);
	border-radius: 50%;
	transform: translate(-50%, -50%);
	animation: pulse 2s ease-in-out infinite;
	pointer-events: none;
}

@keyframes fadeInUp {
	from {
		opacity: 0;
		transform: translateY(30px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

@keyframes gradientShift {
	0% {
		background-position: 0% 50%;
	}
	50% {
		background-position: 100% 50%;
	}
	100% {
		background-position: 0% 50%;
	}
}

@keyframes pulse {
	0%, 100% {
		transform: translate(-50%, -50%) scale(1);
		opacity: 0.5;
	}
	50% {
		transform: translate(-50%, -50%) scale(1.2);
		opacity: 0;
	}
}

.donation-button {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 15px;
	background: linear-gradient(135deg, #e74c3c 0%, #c0392b 50%, #e74c3c 100%);
	background-size: 200% 200%;
	color: #ffffff !important;
	padding: 20px 50px;
	border-radius: 50px;
	font-size: 1.15rem;
	font-weight: 800;
	text-transform: uppercase;
	letter-spacing: 2px;
	text-decoration: none;
	box-shadow: 0 10px 30px rgba(231, 76, 60, 0.4), 
	            0 0 0 0 rgba(231, 76, 60, 0.7);
	transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
	position: relative;
	overflow: hidden;
	border: 2px solid rgba(255, 255, 255, 0.2);
	animation: buttonGradient 3s ease infinite, buttonFloat 3s ease-in-out infinite;
	z-index: 1;
}

@keyframes buttonGradient {
	0% {
		background-position: 0% 50%;
	}
	50% {
		background-position: 100% 50%;
	}
	100% {
		background-position: 0% 50%;
	}
}

@keyframes buttonFloat {
	0%, 100% {
		transform: translateY(0);
	}
	50% {
		transform: translateY(-5px);
	}
}

.donation-button::before {
	content: '';
	position: absolute;
	top: 0;
	left: -100%;
	width: 100%;
	height: 100%;
	background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
	transition: left 0.6s ease;
	z-index: 2;
}

.donation-button::after {
	content: '';
	position: absolute;
	top: 50%;
	left: 50%;
	width: 0;
	height: 0;
	background: rgba(255, 255, 255, 0.3);
	border-radius: 50%;
	transform: translate(-50%, -50%);
	transition: width 0.6s ease, height 0.6s ease;
	z-index: 1;
}

.donation-button:hover::before {
	left: 100%;
}

.donation-button:hover::after {
	width: 300px;
	height: 300px;
}

.donation-button:hover {
	transform: translateY(-8px) scale(1.05);
	box-shadow: 0 15px 40px rgba(231, 76, 60, 0.5),
	            0 0 0 8px rgba(231, 76, 60, 0.1);
	color: #ffffff !important;
	text-decoration: none;
	animation: none;
	background-position: 100% 50%;
}

.donation-button:active {
	transform: translateY(-4px) scale(1.02);
	box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
}

.donation-button i {
	font-size: 1.5rem;
	animation: heartbeat 1.5s ease-in-out infinite, iconRotate 2s ease-in-out infinite;
	position: relative;
	z-index: 3;
}

.donation-button:hover i {
	animation: heartbeat 0.8s ease-in-out infinite, iconRotate 1s ease-in-out infinite;
	transform: scale(1.2);
}

@keyframes heartbeat {
	0%, 100% {
		transform: scale(1);
	}
	25% {
		transform: scale(1.15);
	}
	50% {
		transform: scale(1.1);
	}
	75% {
		transform: scale(1.15);
	}
}

@keyframes iconRotate {
	0%, 100% {
		transform: rotate(0deg);
	}
	25% {
		transform: rotate(-5deg);
	}
	75% {
		transform: rotate(5deg);
	}
}

.donation-button span {
	position: relative;
	z-index: 3;
	text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.donation-hint {
	margin-top: 20px;
	color: #555;
	font-size: 1rem;
	font-style: italic;
	margin-bottom: 0;
	position: relative;
	z-index: 1;
	animation: fadeIn 1s ease-out 0.5s both;
}

@keyframes fadeIn {
	from {
		opacity: 0;
	}
	to {
		opacity: 1;
	}
}

.donation-button-wrapper .donation-icon-wrapper {
	display: inline-block;
	margin-bottom: 15px;
	animation: iconBounce 2s ease-in-out infinite;
}

@keyframes iconBounce {
	0%, 100% {
		transform: translateY(0);
	}
	50% {
		transform: translateY(-10px);
	}
}

@media (max-width: 768px) {
	.donation-button {
		padding: 16px 35px;
		font-size: 1rem;
		gap: 12px;
	}
	
	.donation-button-wrapper {
		padding: 35px 20px;
		margin: 35px 0;
	}
	
	.donation-button i {
		font-size: 1.3rem;
	}
}

.news-list-animated {
	opacity: 0;
	animation: listFadeIn 0.4s ease-out forwards;
}

@keyframes listFadeIn {
	from {
		opacity: 0;
	}
	to {
		opacity: 1;
	}
}

.news-item {
	opacity: 0;
	transform: translateY(20px) scale(0.95);
	animation: cardAppear 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
	transition: transform 0.3s ease, box-shadow 0.3s ease;
}

@keyframes cardAppear {
	from {
		opacity: 0;
		transform: translateY(20px) scale(0.95);
	}
	to {
		opacity: 1;
		transform: translateY(0) scale(1);
	}
}

.news-item:nth-child(1) { animation-delay: 0.02s; }
.news-item:nth-child(2) { animation-delay: 0.04s; }
.news-item:nth-child(3) { animation-delay: 0.06s; }
.news-item:nth-child(4) { animation-delay: 0.08s; }
.news-item:nth-child(5) { animation-delay: 0.1s; }
.news-item:nth-child(6) { animation-delay: 0.12s; }
.news-item:nth-child(7) { animation-delay: 0.14s; }
.news-item:nth-child(8) { animation-delay: 0.16s; }
.news-item:nth-child(9) { animation-delay: 0.18s; }
.news-item:nth-child(10) { animation-delay: 0.2s; }

.news-item:hover {
	transform: translateY(-2px) scale(1.01);
	box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}
</style>
<?php include('slider_menu.php');?>
<script type="text/javascript">
$(function(){
	$(".daha").click(function(){
		var id = $(".ordered-list ul li.is-active:last").attr("id");
		var t = $(this);
		$(".more a", this).hide();
		$(".loading").show();
		$("div", this).show();
		$.ajax({
			type:"POST",
			url: "<?php echo tema;?>/ajax/diger_etkinlikler.php",
			data: {"id":id},
			success: function(cevap){
				if(cevap == "yok")
				{
					swal({
						type: 'warning',
						title: '<?=@$dil['txt6'];?>',
						text: '<?=@$dil['txt144'];?>',
						confirmButtonText: '<?=@$dil['txt8'];?>',
						timer: 5000
					})
					$(".daha").remove();
				}
				else
				{
					$(".ordered-list ul").append(cevap);
					$("div", t).hide();
					$(".loading").hide();
					$(".more a", t).show();
					
					$(".ordered-list ul li.news-item").each(function(index) {
						if($(this).css('opacity') == '0' || $(this).css('opacity') == '') {
							$(this).css({
								'animation': 'cardAppear 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards',
								'animation-delay': (index * 0.02) + 's'
							});
						}
					});
				}
			}
		})
	});
});
</script>