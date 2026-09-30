<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
$seo = isset($_GET['seo']) ? $_GET['seo'] : (isset($_GET['id']) ? $_GET['id'] : '');
$desteklemeSorgu = $db->prepare("SELECT * FROM destekleme_yollari WHERE seo = ? AND durum = ? AND dil = ?");
$desteklemeSorgu->execute(array($seo, "1", $_SESSION['k_dil']));

if($desteklemeSorgu->rowCount() > 0){
	$destekleme = $desteklemeSorgu->fetch(PDO::FETCH_ASSOC);
	
	$digerDesteklemeSorgu = $db->prepare("SELECT * FROM destekleme_yollari WHERE id != ? AND durum = ? AND dil = ? ORDER BY sira ASC");
	$digerDesteklemeSorgu->execute(array($destekleme['id'], "1", $_SESSION['k_dil']));
	$digerDesteklemeYollari = $digerDesteklemeSorgu->fetchAll(PDO::FETCH_ASSOC);
} else {
	header("Location:".$url."/404.html");
	exit;
}

$menubul = $db->query("SELECT * FROM menu WHERE menu_url = '".$htc['destekleme_yollariurl']."' OR link = '".$htc['destekleme_yollariurl']."' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
$menubas = $db->query("SELECT * FROM menu WHERE id = '{$menubul['menu_ust']}' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
?>
<section class="page-section destekleme-yollari-detay-section">
	<div class="bg-white">
		<div class="col-12 p-0 banner">
			<img src="<?php echo tema;?>/uploads/arkaplan/arkaplan16/<?php echo $arkaplan['arkaplan16'];?>" alt="<?php echo htmlspecialchars($destekleme['baslik'], ENT_QUOTES); ?>">
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
						<li><a href="<?php echo $htc['destekleme_yollariurl']; ?><?php echo $html;?>"><?=@$dil['txt273'];?></a></li>
						<li><?php echo htmlspecialchars($destekleme['baslik'], ENT_QUOTES); ?></li>
					</ol>
				</div>
				<?php include('leftbar.php');?>
				<div class="col-lg-<?php echo($moduller['alan23'] == "1" ? '9' : '12');?> col-md-<?php echo($moduller['alan23'] == "1" ? '7' : '12');?> z-index-9">
					<div class="page-content">
						<div class="destekleme-yollari-detay-wrapper">
							<div class="detay-header mb-4">
								<?php if(!empty($destekleme['ikon'])){ ?>
									<div class="detail-icon mb-3">
										<i class="<?php echo htmlspecialchars($destekleme['ikon'], ENT_QUOTES);?>"></i>
									</div>
								<?php } elseif(!empty($destekleme['resim'])){ ?>
									<div class="detay-image mb-4">
										<img src="<?php echo tema;?>/uploads/destekleme_yollari/<?php echo htmlspecialchars($destekleme['resim'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($destekleme['baslik'], ENT_QUOTES); ?>" class="img-fluid rounded">
									</div>
								<?php } ?>
								
								<h1 class="detay-title"><?php echo htmlspecialchars($destekleme['baslik'], ENT_QUOTES); ?></h1>
								<?php if(!empty($destekleme['aciklama'])): ?>
									<p class="detay-aciklama"><?php echo $destekleme['aciklama']; ?></p>
								<?php endif; ?>
							</div>
							
							<?php if(!empty($destekleme['tam_aciklama'])): ?>
							<div class="detay-content mb-5">
								<?php echo $destekleme['tam_aciklama']; ?>
							</div>
							<?php endif; ?>
							

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<style>
.destekleme-yollari-detay-wrapper {
	padding: 20px 0;
}

.detay-header {
	text-align: center;
}

.detail-icon {
	font-size: 5rem;
	color: #325C6A;
}

.detay-image img {
	max-width: 100%;
	max-height: 400px;
	object-fit: cover;
}

.detay-title {
	font-size: 2.5rem;
	font-weight: 800;
	color: #325C6A;
	text-transform: uppercase;
	margin-bottom: 15px;
}

.detay-aciklama {
	font-size: 1.1rem;
	color: #6d7d85;
	line-height: 1.6;
}

.detay-content {
	font-size: 1rem;
	color: #5a6e7a;
	line-height: 1.8;
}

.diger-destekleme-yollari h3 {
	font-size: 1.8rem;
	font-weight: 700;
	color: #325C6A;
}

.destekleme-checkbox-item {
	position: relative;
}

.custom-checkbox-label {
	display: block;
	cursor: pointer;
	padding: 20px;
	border: 2px solid #e0e0e0;
	border-radius: 8px;
	transition: all 0.3s ease;
	background: #fff;
}

.custom-checkbox-label:hover {
	border-color: #45dcb8;
	box-shadow: 0 4px 12px rgba(69, 220, 184, 0.2);
}

.destekleme-checkbox {
	position: absolute;
	opacity: 0;
	width: 0;
	height: 0;
}

.checkbox-custom {
	position: absolute;
	top: 10px;
	right: 10px;
	width: 24px;
	height: 24px;
	border: 2px solid #ddd;
	border-radius: 4px;
	background: #fff;
	transition: all 0.3s ease;
}

.destekleme-checkbox:checked ~ .checkbox-custom {
	background: #45dcb8;
	border-color: #45dcb8;
}

.destekleme-checkbox:checked ~ .checkbox-custom::after {
	content: '\2713';
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	color: #fff;
	font-weight: bold;
	font-size: 14px;
}

.destekleme-checkbox:checked ~ .checkbox-content {
	color: #45dcb8;
}

.checkbox-content {
	text-align: center;
	display: flex;
	flex-direction: column;
	align-items: center;
	transition: color 0.3s ease;
}

.checkbox-image {
	max-width: 80px;
	max-height: 80px;
	object-fit: cover;
	border-radius: 8px;
}

.checkbox-title {
	font-weight: 600;
	margin-top: 10px;
	font-size: 1rem;
}

@media (max-width: 768px) {
	.detay-title {
		font-size: 2rem;
	}
	
	.detail-icon {
		font-size: 3rem;
	}
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var checkboxes = document.querySelectorAll('.destekleme-checkbox');
	var selectedItems = [];
	
	checkboxes.forEach(function(checkbox) {
		checkbox.addEventListener('change', function() {
			if(this.checked) {
				selectedItems.push({
					id: this.value,
					baslik: this.dataset.baslik
				});
			} else {
				selectedItems = selectedItems.filter(item => item.id !== this.value);
			}
			console.log('Seçili öğeler:', selectedItems);
		});
	});
});
</script>

