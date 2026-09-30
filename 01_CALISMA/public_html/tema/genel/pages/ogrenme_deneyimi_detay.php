<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
$seo = isset($_GET['seo']) ? $_GET['seo'] : (isset($_GET['id']) ? $_GET['id'] : '');
$deneyimSorgu = $db->prepare("SELECT od.*, p.baslik as program_baslik, p.aciklama as program_aciklama 
							   FROM ogrenme_deneyimi od 
							   LEFT JOIN programlar p ON od.program_id = p.id 
							   WHERE od.seo = ? AND od.durum = ? AND od.dil = ?");
$deneyimSorgu->execute(array($seo, "1", $_SESSION['k_dil']));

if($deneyimSorgu->rowCount() > 0){
	$deneyim = $deneyimSorgu->fetch(PDO::FETCH_ASSOC);
	
	$detayBaslik = !empty($deneyim['program_id']) && !empty($deneyim['program_baslik']) ? $deneyim['program_baslik'] : $deneyim['baslik'];
	$detayAciklama = !empty($deneyim['program_id']) && !empty($deneyim['program_aciklama']) ? $deneyim['program_aciklama'] : $deneyim['aciklama'];
	$tamAciklama = !empty($deneyim['tam_aciklama']) ? $deneyim['tam_aciklama'] : $detayAciklama;
} else {
	header("Location:".$url."/404.html");
	exit;
}

$menubul = $db->query("SELECT * FROM menu WHERE menu_url = '".$htc['ogrenme_deneyimiurl']."' OR link = '".$htc['ogrenme_deneyimiurl']."' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
$menubas = $db->query("SELECT * FROM menu WHERE id = '{$menubul['menu_ust']}' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
?>
<section class="page-section ogrenme-deneyimi-detay-section">
	<div class="bg-white">
		<div class="col-12 p-0 banner">
			<img src="<?php echo tema;?>/uploads/arkaplan/arkaplan16/<?php echo $arkaplan['arkaplan16'];?>" alt="<?php echo $detayBaslik; ?>">
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
						<li><a href="<?php echo $htc['ogrenme_deneyimiurl']; ?><?php echo $html;?>"><?=@$dil['txt270'];?></a></li>
						<li><?php echo $detayBaslik; ?></li>
					</ol>
				</div>
				<?php include('leftbar.php');?>
				<div class="col-lg-<?php echo($moduller['alan23'] == "1" ? '9' : '12');?> col-md-<?php echo($moduller['alan23'] == "1" ? '7' : '12');?> z-index-9">
					<div class="page-content">
						<div class="ogrenme-deneyimi-detay-wrapper">
							<?php if(!empty($deneyim['resim'])){ ?>
							<div class="detay-image mb-4">
								<img src="<?php echo tema;?>/uploads/ogrenme_deneyimi/<?php echo $deneyim['resim']; ?>" alt="<?php echo $detayBaslik; ?>" class="img-fluid rounded">
							</div>
							<?php } ?>
							
							<div class="detay-header mb-4">
								<h1 class="detay-title"><?php echo $detayBaslik; ?></h1>
								<?php if(!empty($deneyim['program_id']) && !empty($deneyim['program_baslik'])){ ?>
								<div class="detay-program-badge">
									<i class="fas fa-tag"></i> <?php echo $deneyim['program_baslik']; ?>
								</div>
								<?php } ?>
							</div>
							
							<?php if(!empty($detayAciklama)){ ?>
							<div class="detay-intro mb-4">
								<p class="lead"><?php echo $detayAciklama; ?></p>
							</div>
							<?php } ?>
							
							<?php if(!empty($tamAciklama)){ ?>
							<div class="detay-content">
								<?php echo $tamAciklama; ?>
							</div>
							<?php } ?>
							
							<div class="detay-footer mt-5 pt-4 border-top">
								<a href="<?php echo $htc['ogrenme_deneyimiurl']; ?><?php echo $html;?>" class="btn btn-primary">
									<i class="fas fa-arrow-left"></i> <?=@$dil['txt419'];?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<style>
.ogrenme-deneyimi-detay-section {
	background: #f8f9fa;
}

.ogrenme-deneyimi-detay-wrapper {
	background: #fff;
	padding: 40px;
	border-radius: 12px;
	box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.detay-image {
	border-radius: 12px;
	overflow: hidden;
	box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.detay-image img {
	width: 100%;
	height: auto;
	display: block;
}

.detay-title {
	font-size: 2.5rem;
	font-weight: 700;
	color: #2C5F5F;
	margin-bottom: 15px;
	line-height: 1.3;
}

.detay-program-badge {
	display: inline-block;
	background: #e8f5e9;
	color: #2C5F5F;
	padding: 8px 15px;
	border-radius: 25px;
	font-size: 0.9rem;
	font-weight: 600;
}

.detay-program-badge i {
	margin-right: 5px;
}

.detay-intro .lead {
	font-size: 1.2rem;
	color: #555;
	line-height: 1.8;
	font-weight: 400;
}

.detay-content {
	font-size: 1.05rem;
	color: #444;
	line-height: 1.9;
}

.detay-content p {
	margin-bottom: 20px;
}

.detay-content h2,
.detay-content h3,
.detay-content h4 {
	color: #2C5F5F;
	margin-top: 30px;
	margin-bottom: 15px;
}

.detay-content ul,
.detay-content ol {
	margin-bottom: 20px;
	padding-left: 25px;
}

.detay-content li {
	margin-bottom: 10px;
}

@media (max-width: 768px) {
	.ogrenme-deneyimi-detay-wrapper {
		padding: 25px;
	}
	
	.detay-title {
		font-size: 1.8rem;
	}
}
</style>

<?php include('slider_menu.php');?>

