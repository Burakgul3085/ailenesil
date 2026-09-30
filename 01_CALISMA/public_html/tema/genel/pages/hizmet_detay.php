<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(strip_tags(isset($_GET['id'])))
{
	$aSorgu = $db->prepare("SELECT * FROM hizmetler WHERE seo = ? AND durum = ? AND dil = ?");
	$aSorgu->execute(array($_GET['id'],1,$_SESSION['k_dil']));
	if($aSorgu->rowCount()){
		$aSonuc 		= $aSorgu->fetch(PDO::FETCH_ASSOC);
	}else{
		header("Location:".$url.(altklasor == "1" ? '/' : '')."404".$html."");
		exit();
	}
}
else
{
	$aSorgu = $db->prepare("SELECT * FROM hizmetler WHERE durum = ? AND dil = ? ORDER BY id ASC");
	$aSorgu->execute(array(1,$_SESSION['k_dil']));
	if($aSorgu->rowCount()){
		$aSonuc 		= $aSorgu->fetch(PDO::FETCH_ASSOC);
	}else{
		header("Location:".$url.(altklasor == "1" ? '/' : '')."404".$html."");
		exit();
	}
}
$menubul 	= $db->query("SELECT * FROM menu WHERE menu_url = '".$htc['hizmetdetayurl']."/".$aSonuc['seo']."' OR link = '".$htc['hizmetdetayurl']."/".$aSonuc['seo']."' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
$menubas 	= $db->query("SELECT * FROM menu WHERE id = '{$menubul['menu_ust']}' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);		
?>
<!-- PAGE SECTİON BAŞLANGIÇ -->
<section class="page-section">
	<div class="bg-white">
		<div class="col-12 p-0 banner">
			<img src="<?php echo tema;?>/uploads/arkaplan/arkaplan16/<?php echo $arkaplan['arkaplan16'];?>" alt="<?php echo $aSonuc['adi'];?>">
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
        <li><?php echo $aSonuc['adi'];?></li>
    </ol>	

    <?php 
    $bagis_liste = [];
    if(!empty($aSonuc['bagis_ids'])) {
        $ids = explode(',', $aSonuc['bagis_ids']);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $bSorgu = $db->prepare("SELECT * FROM bagislar WHERE id IN ($placeholders) AND durum = 1 AND dil = ? ORDER BY sira ASC");
        $bSorgu->execute(array_merge($ids, [$_SESSION['k_dil']]));
        $bagis_liste = $bSorgu->fetchAll(PDO::FETCH_ASSOC);
    }
    ?>

    <?php if(!empty($bagis_liste)): ?>
    
    <style>
    .pcdehizalama{
            margin-top: -27pc;
    }
    @media only screen and (max-width: 767px) {
.pcdehizalama {
    margin-top: 13pc;
}
}
</style>
    <style>
        .detay-bagis-section { margin-top: 30px; margin-bottom: 40px; position: relative; }
        .detay-bagis-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 10px; }
        .detay-bagis-section .section-title { font-size: 1.25rem; font-weight: 700; color: #1a202c; display: flex; align-items: center; gap: 10px; margin: 0; }
        .detay-bagis-section .section-title::before { content: ''; width: 4px; height: 24px; background: #14b8a6; border-radius: 2px; }
        .btn-all-bagis { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; padding: 6px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; transition: all 0.2s; }
        .btn-all-bagis:hover { background: #14b8a6; color: #fff; border-color: #14b8a6; }
        
        /* Carousel Kart Düzenlemeleri */
        .donation-card { transition: all 0.3s ease; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); display: flex; flex-direction: column; height: 100%; border: 1px solid #edf2f7; margin: 10px 5px; }
        .donation-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); border-color: #14b8a6; }
        .card-image-container { position: relative; width: 100%; height: 160px; overflow: hidden; }
        .donation-card .card-image { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .donation-card .card-content { padding: 1.2rem; text-align: center; flex-grow: 1; display: flex; flex-direction: column;    height: 163px; }
        @media only screen and (max-width: 767px) {

        .donation-card .card-content { padding: 1.2rem; text-align: center; flex-grow: 1; display: flex; flex-direction: column;    height: 30px; }

}
        .donation-card .card-title { font-size: 0.95rem; font-weight: 700; color: #1a202c; margin-bottom: 1rem; line-height: 1.4; height: 2.8em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        
        .price-input-wrapper { position: relative; margin-bottom: 1rem; }
        .price-input-wrapper input { width: 100%; padding: 10px 35px 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-weight: 700; color: #2d3748; background: #f9fafb; }
        .price-input-wrapper .currency-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #14b8a6; font-size: 14px; font-weight: 800; }
        
        .donate-button { width: 100%; padding: 12px; background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: auto; }
        .donate-button:hover { opacity: 0.95; box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3); }

        /* Carousel Okları */
        .bagis-slider.owl-carousel .owl-nav button { width: 36px; height: 36px; background: #fff !important; border-radius: 50% !important; box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important; color: #14b8a6 !important; position: absolute; top: 40%; transform: translateY(-50%); transition: 0.3s; }
        .bagis-slider.owl-carousel .owl-nav button.owl-prev { left: -18px; }
        .bagis-slider.owl-carousel .owl-nav button.owl-next { right: -18px; }
        .bagis-slider.owl-carousel .owl-nav button:hover { background: #14b8a6 !important; color: #fff !important; }

.view-basket-wrapper {
    display: none;
    margin-top: 25px;
    text-align: center;
    animation: fadeInUp 0.5s ease forwards;
    position: absolute;
    top: -4pc;
}
    @media only screen and (max-width: 767px) {
.view-basket-wrapper {
    display: none;
    margin-top: 25px;
    text-align: center;
    animation: fadeInUp 0.5s ease forwards;
    position: absolute;
    top: -8pc;
}
}
    @media only screen and (max-width: 767px) {
.btn-view-basket {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #059669;
    color: #fff;
    padding: 12px 28px;
    border-radius: 12px;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
    height: 101px;
    font-size: 12px;
}
}

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .btn-view-basket { display: inline-flex; align-items: center; gap: 10px; background: #059669; color: #fff; padding: 12px 28px; border-radius: 12px; font-weight: 700; box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3); }
        .donate-button {

    margin-top: 9px !important;
}
    </style>

    <div class="detay-bagis-section">
        <br/>
        <div class="detay-bagis-header">
            <h3 class="section-title"><?=@$dil['bagis_kampanyalari'] ?: 'Bağış Kampanyaları';?></h3>
            <a href="<?php echo $htc['bagisurl'].$html; ?>" class="btn-all-bagis">
                <i class="fas fa-th-large"></i> <?=@$dil['txt460'] ?: 'Tümünü Gör';?>
            </a>
        </div>

        <div class="owl-carousel bagis-slider">
            <?php foreach($bagis_liste as $bagis): 
                $miktar = $bagis['miktar'] > 0 ? $bagis['miktar'] : 1;
                $seciliParaBirimi = $_SESSION['para_birimi'] ?? 'TRY';
                $paraBirimiSembolu = ($seciliParaBirimi == 'TRY' ? '₺' : ($seciliParaBirimi == 'USD' ? '$' : '€'));
            ?>
            <div class="item">
                <div class="donation-card">
                    <div class="card-image-container">
                        <img src="<?php echo tema;?>/uploads/bagislar/<?php echo $bagis['kapak']; ?>" class="card-image" alt="<?php echo $bagis['adi']; ?>" onerror="this.src='<?php echo tema;?>/assets/images/no-image.png'">
                    </div>
                    <div class="card-content">
                        <h3 class="card-title"><?php echo $bagis['adi']; ?></h3>
                        <div class="price-input-wrapper">
                            <input type="number" id="fiyat_<?php echo $bagis['id']; ?>" data-id="<?php echo $bagis['id']; ?>" value="<?php echo $miktar; ?>" min="1" step="0.01">
                            <span class="currency-icon"><?= $paraBirimiSembolu ?></span>
                        </div>
                        <button type="button" onclick="handleAddBasket('<?php echo $bagis['id']; ?>')" class="donate-button">
                            <i class="fas fa-heart"></i> <?=@$dil['txt461'] ?: 'Bağış Yap';?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="view-basket-wrapper" id="basket-view-container">
            <a href="<?php echo $htc['bagissepeturl'].$html; ?>" class="btn-view-basket">
                <i class="fas fa-shopping-basket"></i> <?=@$dil['txt115'] ?: 'Sepeti Görüntüle';?> <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
    
    <script>
$(document).ready(function() {
    // Carousel'i başlatan ana fonksiyon
    window.initBagisSlider = function(isRtl = false) {
        var $slider = $('.bagis-slider');
        
        // Eğer zaten kurulmuşsa önce yok et (dil değişiminde çakışmaması için)
        if ($slider.hasClass('owl-loaded')) {
            $slider.trigger('destroy.owl.carousel');
            $slider.removeClass('owl-rtl owl-loaded');
            $slider.find('.owl-stage-outer').children().unwrap();
        }

        $slider.owlCarousel({
            rtl: isRtl, // Dil Arapça ise true döner
            loop: true,
            margin: 15,
            nav: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            navText: ["<i class='fas fa-chevron-left'></i>", "<i class='fas fa-chevron-right'></i>"],
            responsive: {
                0: { items: 1 },
                576: { items: 2 },
                992: { items: 4 } // Talebiniz üzerine 4'lü
            }
        });
    };

    // İlk açılışta çalıştır
    initBagisSlider(false);
});

    function handleAddBasket(id) {
        if(typeof addBasket === 'function') {
            addBasket(id);
            var container = document.getElementById('basket-view-container');
            if(container) {
                container.style.display = 'block';
                setTimeout(function() {
                    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 400);
            }
        }
    }
    </script>
    <?php endif; ?>
</div>
				<?php include('leftbar.php');?>
				<div class="col-lg-<?php echo($moduller['alan23'] == "1" ? '9' : '12');?> col-md-<?php echo($moduller['alan23'] == "1" ? '7' : '12');?> z-index-9">
					<div class="page-content">
						<h2 class="page-title">
							<?php echo $aSonuc['adi'];?>
							<span class="tarih">
								<i class="fa fa-calendar"></i>
								<?php echo cVCLmHLxbS_tarih($aSonuc['tarih']);?>
							</span>
						</h2>
						<div class="row haber-detay-box">
							<div class="col-lg-12">
								<?php if($aSonuc['resim'] != ""){?>
								<img src="<?php echo tema;?>/uploads/hizmetler/<?php echo $aSonuc['resim'];?>" class="haber-detay-image">
								<?php }?>
								<div class="detay">
									<?php echo $aSonuc['aciklama']; ?>
								</div>
							</div>
						</div>
					</div>
					<?php displayContactSection($page_name, $db, $dil, $sayfalink); ?> 
				</div>
			</div>
		</div>
	</div>
</section>
<!-- PAGE SECTİON BİTİŞ -->
<?php include('slider_menu.php');?>