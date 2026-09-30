<?php
/**
 * DİNAMİK ANASAYFA - Yönetim Panelindeki Sıraya Göre Alanları Gösterir
 * 
 * Bu dosya, anasayfa_alanlar tablosundaki sıralamaya göre
 * dinamik olarak sayfa bölümlerini yükler.
 */

// Popup modal (her zaman en başta)
?>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		var my_cookie = $.cookie($('.modal-check').attr('name'));
		if (my_cookie && my_cookie == "true") {
			$(this).prop('checked', my_cookie);
			console.log('checked checkbox');
		} else {
			$('#actionsModal').modal('show');
			console.log('uncheck checkbox');
		}
		$(".modal-check").change(function() {
			$.cookie($(this).attr("name"), $(this).prop('checked'), {
				path: '/',
				expires: 1
			});
		});
	});
</script>
<?php if ($popup["durum"] == 1) { ?>
<!-- Modal -->
<div class="modal fade" id="actionsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-1">
				<div class="row">
					<div class="col-md-12 text-center">
						<a href="<?php echo $popup["url"]; ?>" <?php echo $popup["sekme"] == 1
    ? 'target="_blank"'
    : ""; ?> title="<?php echo $popup["adi"]; ?>">
							<img src="<?php echo tema; ?>/uploads/popup/<?php echo $popup[
    "resim"
]; ?>" class="img-responsive" alt="<?php echo $popup[
    "adi"
]; ?>" title="<?php echo $popup["adi"]; ?>" style="margin: 0 auto;">
						</a>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="checkbox pull-right">
					<label>
						<input class="modal-check" name="modal-check" type="checkbox"> <?= @$dil[
          "txt55"
      ] ?>
					</label>
				</div>
			</div>
        </div>
    </div>
</div>
<?php } ?>

<?php
// Anasayfa alanlarını veritabanından sırayla çek
$AlanSorgu = $db->prepare("SELECT * FROM anasayfa_alanlar WHERE durum = 1 ORDER BY sira ASC");
$AlanSorgu->execute();
$alanlar = $AlanSorgu->fetchAll(PDO::FETCH_ASSOC);

// Alan dosya eşleştirmesi
$alanDosyalari = [
    'alan1' => null, // Slider içinde zaten var
    'alan2' => 'alan2_slider.php',
    'alan3' => 'alan3_haberler.php',
    'alan4' => 'alan3_haberler.php', // Haberlerle birlikte
    'alan5' => 'alan5_7_16_10_etkinlik.php',
    'alan6' => 'alan5_7_16_10_etkinlik.php',
    'alan7' => 'alan5_7_16_10_etkinlik.php',
    'alan10' => 'alan5_7_16_10_etkinlik.php',
    'alan11' => 'alan11_hizli_menu.php',
    'alan12' => 'alan12_baskan.php',
    'alan13' => 'alan13_projeler.php',
    'alan14' => 'alan14_video_galeri.php',
    'alan15' => 'alan15_foto_galeri.php',
    'alan16' => 'alan5_7_16_10_etkinlik.php',
    'alan17' => 'alan17_iletisim.php',
    'alan18' => 'alan18_harita.php',
    'alan27' => 'alan27_impact.php',
    'alan28' => 'alan28_programlar.php',
    'alan29' => 'alan29_bagis.php',
    'alan30' => 'alan30_instagram.php'
];

// Birleşik alanlar (tek dosyada birden fazla alan)
$birlesikAlanlar = [
    'alan5_7_16_10' => false, // İlk kez render edildi mi?
    'alan3_4' => false
];

// Alanları sırayla render et
foreach ($alanlar as $alan) {
    $alanKodu = $alan['alan_kodu'];
    
    // Bu alanın modülü aktif mi kontrol et
    if (!isset($moduller[$alanKodu]) || $moduller[$alanKodu] != "1") {
        continue;
    }
    
    // Dosya yolunu bul
    $dosya = $alanDosyalari[$alanKodu] ?? null;
    
    if (!$dosya) {
        continue;
    }
    
    $dosyaYolu = 'pages/anasayfa_partials/' . $dosya;
    
    // Birleşik alanları sadece bir kez render et
    if (in_array($dosya, ['alan5_7_16_10_etkinlik.php', 'alan3_haberler.php'])) {
        $key = str_replace('.php', '', str_replace('_', '_', explode('_', $dosya)[0] . '_' . (explode('_', $dosya)[1] ?? '')));
        
        if ($dosya == 'alan5_7_16_10_etkinlik.php') {
            if ($birlesikAlanlar['alan5_7_16_10']) {
                continue;
            }
            $birlesikAlanlar['alan5_7_16_10'] = true;
        }
        
        if ($dosya == 'alan3_haberler.php') {
            if ($birlesikAlanlar['alan3_4']) {
                continue;
            }
            $birlesikAlanlar['alan3_4'] = true;
        }
    }
    
    // Dosyayı include et
    if (file_exists($dosyaYolu)) {
        include($dosyaYolu);
    }
}
?>

<!-- Lazy fallback script -->
<script>
(function(){
  var imgs = document.querySelectorAll('img[data-src]');
  imgs.forEach(function(img){
    if(!img.getAttribute('src')) img.setAttribute('src', img.getAttribute('data-src'));
  });
})();
</script>

