<?php

// Aktif alanları veritabanından çek
$AktifAlanlarSorgu = $db->prepare("SELECT alan_kodu, sira FROM anasayfa_alanlar WHERE durum = 1 ORDER BY sira ASC");
$AktifAlanlarSorgu->execute();
$aktifAlanlar = $AktifAlanlarSorgu->fetchAll(PDO::FETCH_ASSOC);

// Alan kodu -> sıra mapping
$alanSiraMap = [];
foreach($aktifAlanlar as $alan) {
    $alanSiraMap[$alan['alan_kodu']] = (int)$alan['sira'];
}

// Alan gruplarını tanımla (hangi alan hangi section'a ait)
$alanGrupMap = [
    'slider' => ['alan1', 'alan2'],
    'haberler' => ['alan3', 'alan4'],
    'etkinlikler' => ['alan5', 'alan6', 'alan7', 'alan10', 'alan16'],
    'hizlimenu' => ['alan11'],
    'baskan' => ['alan12'],
    'projeler' => ['alan13'],
    'videogaleri' => ['alan14'],
    'fotogaleri' => ['alan15'],
    'iletisim' => ['alan17'],
    'harita' => ['alan18'],
    'impact' => ['alan27'],
    'programlar' => ['alan28'],
    'bagismoduller' => ['alan29'],
    'bilgilendirme' => ['alan33'],
    'instagram' => ['alan30']
];

// Her grubun minimum sırasını bul
$grupMinSira = [];
foreach($alanGrupMap as $grup => $kodlar) {
    $minSira = PHP_INT_MAX;
    foreach($kodlar as $kod) {
        if(isset($alanSiraMap[$kod]) && $alanSiraMap[$kod] < $minSira) {
            $minSira = $alanSiraMap[$kod];
        }
    }
    if($minSira != PHP_INT_MAX) {
        $grupMinSira[$grup] = $minSira;
    }
}

// Sıraya göre sırala
asort($grupMinSira);

// Section buffer'ları
$sectionBuffers = [];

// Buffer başlat
ob_start();
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

<?php 
// POPUP - Her zaman en başta göster
if ($popup["durum"] == 1) { ?>
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

<!-- SLİDER BAŞLANGIÇ -->
<?php 
ob_start(); 
?>
<?php 
$Sorgu = $db->prepare("SELECT * FROM slider WHERE durum = ? AND dil = ? ORDER BY sira ASC");
$Sorgu->execute(["1", $_SESSION["k_dil"]]);
$islem = $Sorgu->fetchAll(PDO::FETCH_ASSOC);
$slider_sayisi = count($islem);
$dongu_durumu = ($slider_sayisi > 1) ? 'true' : 'false';
?>
<style>
.buttons-container {
    text-shadow: 0px 2px 3px rgba(255, 255, 255, 0.8);
    -webkit-transition: .8s .4s;
    transition: .8s .4s;
    text-align: center;
    margin-top: 30px;
    top: 69%;
    position: absolute;
    left: 66%;
    z-index: 9999999;
    background: white;
    color: red;
}
</style>

<section class="position-relative">
    <div id="anaManseSlider" class="owl-carousel owl-theme">
        <?php foreach ($islem as $Sonuc) { ?>
        <div class="item position-relative">
            <div class="slider-overlay"></div>
            <div class="slider-img-container">
                <picture>
                    <?php if(!empty($Sonuc['mobil_resim'])): ?>
                        <source media="(max-width: 768px)" srcset="<?php echo tema; ?>/uploads/slider/<?php echo $Sonuc['mobil_resim']; ?>">
                    <?php endif; ?>
                    <img src="<?php echo tema; ?>/uploads/slider/<?php echo $Sonuc["resim"]; ?>" alt="<?php echo $Sonuc['adi']; ?>">
                </picture>
            </div>

            
            <div class="slider-text z-index-9">
                <?php if ($moduller["alan1"] == "1") { ?>
                <h3 class="slide-title z-index-9"><?php echo $Sonuc["adi"]; ?></h3>
                <?php if (!empty($Sonuc["aciklama"])) { ?>
                    <div class="text"><?php echo $Sonuc["aciklama"]; ?></div>
                <?php } ?>
                <?php } ?>

            </div>
                            <?php if($Sonuc['url'] != ""){?>  
                <a class="slider-full-link" <?= $Sonuc["sekme"] == 1 ? 'target="_blank"' : ""; ?> href="<?php echo $Sonuc["url"]; ?>"></a>
                <?php } ?>
        </div>
        <?php } ?>
    </div>
</section>


<style>
    .slider-full-link {
        position: absolute;
        inset: 0;
        z-index: 10;
        cursor: pointer;
    }

    /* Navigasyon Butonları */
    .owl-nav button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: transparent !important;
        color: #fff !important;
        font-size: 3rem !important;
        outline: none;
        transition: 0.3s;
    }
    .owl-nav button:hover {
        color: #ccc !important;
    }
    .owl-prev { left: 20px; }
    .owl-next { right: 20px; }
    
    .owl-nav button{
        border-color: unset !important;
        background: unset !important;
        font-size: 15px !important;
    }

    .slider-img-container {
        width: 100%;
        overflow: hidden;
    }

    @media (min-width: 992px) {
        .owl-carousel .item, 
        .slider-img-container {
            height: 65vh; 
        }
        .owl-carousel .item img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover; 
        }
    }

    @media (max-width: 991px) {
        .owl-carousel .item,
        .slider-img-container {
            height: auto !important; 
            min-height: unset !important;
        }

        .owl-carousel .item img {
            display: block;
            width: 100%; /
            height: auto !important;
            object-fit: contain; 
        }
        
        .slider-text {
            position: absolute;
            bottom: 20px;
            left: 15px;
            right: 15px;
        }
    }
</style>

<script>
$(document).ready(function(){
    var owl = $('#anaManseSlider');
    owl.owlCarousel({
        items: 1,
        loop: <?php echo $dongu_durumu; ?>,
        margin: 0,
        nav: <?php echo $dongu_durumu; ?>,
        dots: false,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        smartSpeed: 1000,
        navText: [
            '<i class="fal fa-chevron-left fa-3x"></i>', 
            '<i class="fal fa-chevron-right fa-3x"></i>'
        ],
        animateOut: 'fadeOut', 
        animateIn: 'fadeIn',
        autoHeight: true // Resim yükseklikleri farklıysa sliderın adapte olmasını sağlar
    });
});

function scrollToNextSection() {
    var slider = document.querySelector('#anaManseSlider');
    if(slider){
        window.scrollTo({
            top: slider.offsetHeight,
            behavior: 'smooth'
        });
    }
}
</script>
<?php 
$sectionBuffers['slider'] = ob_get_clean();
?>
<!-- SLİDER BİTİŞ -->

<!-- CONNECT WITH US (Haberler) -->
<?php 
if ($moduller["alan3"] == "1" || $moduller["alan4"] == "1") {
    ob_start();
?>
<section class="social" id="haberler" aria-labelledby="connect-title">
  <div class="wrap">
    <h4 id="connect-title"><?= @$dil["txt152"] ?></h4>

    <div class="feed">
      <?php
      // Kaç haber gösterelim? (varsayılan 6)
      $limit = (int) ($limitayar["limit_sayfaanasayfa_haber"] ?? 6);

      $Sorgu = $db->prepare("
          SELECT seo, resim, tarih, adi, spot
          FROM haberler
          WHERE durum = ? AND dil = ?
          ORDER BY sira ASC, tarih DESC
          LIMIT $limit
        ");
      $Sorgu->execute(["1", $_SESSION["k_dil"]]);
      $haberler = $Sorgu->fetchAll(PDO::FETCH_ASSOC);
      ?>

      <?php foreach ($haberler as $h): ?>
        <?php $img = !empty($h["resim"])
            ? tema . "/uploads/haberler/" . $h["resim"]
            : tema . "/assets/img/news-placeholder.jpg";
          // yoksa yedek
          ?>
        <article class="post">
          <a class="post-link"
             href="<?= $htc["haberdetayurl"] ?>/<?= $h["seo"] . $html ?>"
             aria-label="<?= htmlspecialchars(
                 $h["adi"],
                 ENT_QUOTES,
                 "UTF-8",
             ) ?>">
            <img
              src=""
              data-src="<?= $img ?>"
              loading="lazy"
              class="lazy"
              alt="<?= htmlspecialchars($h["adi"], ENT_QUOTES, "UTF-8") ?>">

            <div class="txt">
              <small class="date"><?= cVCLmHLxbS_tarih2($h["tarih"]) ?></small>
              <div class="title"><?= $h["adi"] ?></div>
              <?php if (!empty($h["spot"])): ?>
                <p class="excerpt">
                  <?= mb_strimwidth(
                      strip_tags($h["spot"]),
                      0,
                      140,
                      "…",
                      "UTF-8",
                  ) ?>
                </p>
              <?php endif; ?>
            </div>
          </a>
        </article>
      <?php endforeach; ?>
    </div>

<div class="blog-more">
  <a class="btn-blog" href="<?= $htc["haberurl"] . $html ?>">
    <?= @$dil["txt59"] ?>
  </a>
</div>

  </div>
</section>
<?php 
    $sectionBuffers['haberler'] = ob_get_clean();
}
?>

<!-- ETKİNLİK BAŞLANGIÇ (JA-stili) -->
<?php if (
    $moduller["alan5"] == "1" ||
    $moduller["alan6"] == "1" ||
    $moduller["alan7"] == "1" ||
    $moduller["alan16"] == "1" ||
    $moduller["alan10"] == "1"
) { 
    ob_start();
?>
<br/>
<section class="events-band" id="events">

  <div class="container-fluid px-lg-3">
    <div class="">

      <!-- TABS: DUYURU / İHALE / İLAN -->
      <?php if (
          $moduller["alan5"] == "1" ||
          $moduller["alan6"] == "1" ||
          $moduller["alan7"] == "1"
      ) { ?>
      <div class="col-lg-4">
        <div class="evt-card tabs" role="region" aria-label="<?=@$dil['txt569'];?>">

          <div class="evt-tabs" role="tablist">
            <?php if ($moduller["alan5"] == "1") { ?>
              <button class="tab <?= $moduller["alan5"] == "1" ? "active" : "" ?>"
                      role="tab" data-target="tab-duyuru"><?= @$dil["txt60"] ?></button>
            <?php } ?>
            <?php if ($moduller["alan6"] == "1") { ?>
              <button class="tab <?= $moduller["alan5"] == "0" ? "active" : "" ?>"
                      role="tab" data-target="tab-ihale"><?= @$dil["txt61"] ?></button>
            <?php } ?>
            <?php if ($moduller["alan7"] == "1") { ?>
              <button class="tab <?= $moduller["alan5"] == "0" && $moduller["alan6"] == "0" ? "active" : "" ?>"
                      role="tab" data-target="tab-ilan"><?= @$dil["txt62"] ?></button>
            <?php } ?>
          </div>

          <div class="evt-panes">
            <?php if ($moduller["alan5"] == "1") { ?>
            <div id="tab-duyuru" class="pane show">
              <ul class="evt-list">
                <?php
                $Sorgu = $db->prepare(
                    "SELECT * FROM duyurular WHERE durum=? AND anasayfa=? AND dil=? ORDER BY id DESC",
                );
                $Sorgu->execute(["1", "1", $_SESSION["k_dil"]]);
                $islem = $Sorgu->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php foreach ($islem as $Sonuc) { ?>
                  <li class="item">
                    <a href="<?= $htc["duyurudetayurl"] ?>/<?= $Sonuc["seo"] . $html ?>">
                      <span class="title"><?= $Sonuc["adi"] ?></span>
                      <span class="meta"><i class="fa fa-calendar-alt"></i> <?= cVCLmHLxbS_tarih2(
                          $Sonuc["tarih"],
                      ) ?></span>
                    </a>
                  </li>
                <?php } ?>
              </ul>
              <a class="evt-more" href="<?= $htc["duyuruurl"] .
                  $html ?>"><?= @$dil[
    "txt63"
] ?> <i class="far fa-arrow-right ml-1"></i></a>
            </div>
            <?php } ?>

            <?php if ($moduller["alan6"] == "1") { ?>
            <div id="tab-ihale" class="pane <?= $moduller["alan5"] == "0"
                ? "show"
                : "" ?>">
              <ul class="evt-list">
                <?php
                $Sorgu = $db->prepare(
                    "SELECT * FROM ihaleler WHERE durum=? AND anasayfa=? AND dil=? ORDER BY id DESC",
                );
                $Sorgu->execute(["1", "1", $_SESSION["k_dil"]]);
                $islem = $Sorgu->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php foreach ($islem as $Sonuc) { ?>
                  <li class="item">
                    <a href="<?= $htc["ihaledetayurl"] ?>/<?= $Sonuc["seo"] . $html ?>">
                      <span class="title"><?= $Sonuc["adi"] ?></span>
                      <span class="meta"><i class="fa fa-clock"></i> <?= cVCLmHLxbS_tarih2(
                          $Sonuc["baslama_tarih"] .
                              " " .
                              $Sonuc["baslatma_saat"],
                      ) ?></span>
                    </a>
                  </li>
                <?php } ?>
              </ul>
              <a class="evt-more" href="<?= $htc["ihaleurl"] .
                  $html ?>"><?= @$dil[
    "txt64"
] ?> <i class="far fa-arrow-right ml-1"></i></a>
            </div>
            <?php } ?>

            <?php if ($moduller["alan7"] == "1") { ?>
            <div id="tab-ilan" class="pane <?= $moduller["alan5"] == "0" &&
            $moduller["alan6"] == "0"
                ? "show"
                : "" ?>">
              <ul class="evt-list">
                <?php
                $Sorgu = $db->prepare(
                    "SELECT * FROM ilanlar WHERE durum=? AND anasayfa=? AND dil=? ORDER BY id DESC",
                );
                $Sorgu->execute(["1", "1", $_SESSION["k_dil"]]);
                $islem = $Sorgu->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php foreach ($islem as $Sonuc) { ?>
                  <li class="item">
                    <a href="<?= $htc["ilandetayurl"] ?>/<?= $Sonuc["seo"] . $html ?>">
                      <span class="title"><?= $Sonuc["adi"] ?></span>
                      <span class="meta"><i class="fas fa-map-marker-alt"></i> <?= cVCLmHLxbS_tarih(
                          $Sonuc["tarih"],
                      ) ?></span>
                    </a>
                  </li>
                <?php } ?>
              </ul>
              <a class="evt-more" href="<?= $htc["ilanurl"] .
                  $html ?>"><?= @$dil[
    "txt65"
] ?> <i class="far fa-arrow-right ml-1"></i></a>
            </div>
            <?php } ?>

          </div>
        </div>
      </div>
      <?php } ?>


      <!-- YAKLAŞAN ETKİNLİK + GEÇMİŞLER -->
<?php if ($moduller["alan16"] == "1") { ?>
<section class="custom-event-section">
    <div class="container">
        
        <div class="custom-top-bar">
            <div class="title-group">
                <div class="title-line"></div>
                <h3 class="main-title"><?= @$dil["txt66"] ?></h3> 
                <a class="view-all-link" href="<?= $htc["etkinlikurl"].$html ?>">
                    <?= @$dil["txt67"] ?> <i class="fas fa-long-arrow-alt-right"></i>
                </a>
            </div>
            
            <div class="slider-controls">
                <button class="ctrl-btn prev" id="btn-prev"><i class="fas fa-chevron-left"></i></button>
                <button class="ctrl-btn next" id="btn-next"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>

        <div class="slider-container" id="slider-container">
            <div class="slider-track" id="manual-slider">
                <?php 
                $Sorgu = $db->prepare("SELECT * FROM etkinlikler WHERE durum=? AND dil=? ORDER BY id DESC LIMIT 10");
                $Sorgu->execute(["1", $_SESSION["k_dil"]]);
                $etkinlikler = $Sorgu->fetchAll(PDO::FETCH_ASSOC);

                foreach ($etkinlikler as $Sonuc) { 
                    $tarihler = explode(" ", cVCLmHLxbS_unixtarih($Sonuc["baslama_tarih"]));
                    $gun = substr($tarihler[0], 0, 2);
                    $ay = str_replace($gun.".", "", $tarihler[0]);
                ?>
                <div class="slider-card">
                    <div class="card-inner">
                        <div class="image-area">
                            <div class="date-badge">
                                <span class="d"><?= $gun ?></span>
                                <span class="m"><?= $ay ?></span>
                            </div>
                            <img src="<?= tema ?>/uploads/etkinlikler/<?= $Sonuc["resim"] ?>" onerror="this.src='https://via.placeholder.com/400x500?text=Etkinlik';" alt="<?= $Sonuc["adi"] ?>">
                            <div class="status-tag"><?= @$dil["txt68"] ?></div>
                        </div>

                        <div class="content-area">
                            <h4 class="event-name"><?= $Sonuc["adi"] ?></h4>
                            <div class="event-meta">
                                <span><i class="far fa-clock"></i> <?= $tarihler[1] ?></span>
                                <span><i class="fas fa-map-marker-alt"></i> <?= cVCLmHLxbS_kisa(strip_tags($Sonuc["yer"]), 20) ?></span>
                            </div>


                            <a href="<?= $htc["etkinlikdetayurl"] ?>/<?= $Sonuc["seo"].$html ?>" class="detail-btn">
                                <span>Detayları İncele</span> <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const track = document.getElementById('manual-slider');
    const container = document.getElementById('slider-container');
    const nextBtn = document.getElementById('btn-next');
    const prevBtn = document.getElementById('btn-prev');
    const cards = document.querySelectorAll('.slider-card');
    
    let isDragging = false;
    let startX, moveX, currentIndex = 0;
    const cardCount = cards.length;

    function getCardWidth() {
        return cards[0].offsetWidth + 25; // Genişlik + Gap
    }

    function updateSlider() {
        const width = getCardWidth();
        // Loop Mantığı
        if (currentIndex >= cardCount) currentIndex = 0;
        if (currentIndex < 0) currentIndex = cardCount - 1;
        
        track.style.transition = 'transform 0.5s cubic-bezier(0.25, 1, 0.5, 1)';
        track.style.transform = `translateX(-${currentIndex * width}px)`;
    }

    // Buton Kontrolleri
    nextBtn.addEventListener('click', () => { currentIndex++; updateSlider(); });
    prevBtn.addEventListener('click', () => { currentIndex--; updateSlider(); });

    // Sürükleme Mantığı (Mouse & Touch)
    const startDrag = (e) => {
        isDragging = true;
        startX = (e.pageX || e.touches[0].pageX);
        track.style.transition = 'none';
    };

    const moveDrag = (e) => {
        if (!isDragging) return;
        const x = (e.pageX || e.touches[0].pageX);
        moveX = x - startX;
        const width = getCardWidth();
        track.style.transform = `translateX(${-currentIndex * width + moveX}px)`;
    };

    const endDrag = () => {
        if (!isDragging) return;
        isDragging = false;
        
        // Sürükleme Mesafesine Göre Karar Ver
        if (Math.abs(moveX) > 80) {
            if (moveX > 0) currentIndex--;
            else currentIndex++;
        }
        updateSlider();
        moveX = 0;
    };

    // Event Dinleyicileri
    container.addEventListener('mousedown', startDrag);
    window.addEventListener('mousemove', moveDrag);
    window.addEventListener('mouseup', endDrag);

    container.addEventListener('touchstart', startDrag);
    container.addEventListener('touchmove', moveDrag);
    container.addEventListener('touchend', endDrag);

    // Pencere Boyutu Değişirse
    window.addEventListener('resize', updateSlider);
});
</script>
<?php } ?>

      <!-- BAŞKANLA FOTOĞRAFLAR (GALERİ) -->
      <?php if ($moduller["alan10"] == "1") { ?>
      <div class="col-lg-4">
        <?php
        $GALERISorgu = $db->prepare(
            "SELECT * FROM foto_galeri WHERE durum=? AND baskan=? AND dil=? ORDER BY sira ASC",
        );
        $GALERISorgu->execute(["1", "1", $_SESSION["k_dil"]]);
        $GALERIislem = $GALERISorgu->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php foreach ($GALERIislem as $GALERISonuc) { ?>
          <div class="evt-card">
            <div class="evt-head">
              <h5 class="g-title"><?= $GALERISonuc["adi"] ?></h5>
              <a class="evt-link" href="<?= $htc[
                  "fotodetayurl"
              ] ?>/<?= $GALERISonuc["seo"] . $html ?>"><?= @$dil["txt59"] ?></a>
            </div>

            <div class="custom-owl-nav baskan-galeri-nav"></div>
            <div class="owl-carousel owl-carousel-etkinlik evt-gallery">
              <?php
              $Sorgu = $db->prepare(
                  "SELECT * FROM fotograflar WHERE resimid=? ORDER BY id DESC",
              );
              $Sorgu->execute([$GALERISonuc["id"]]);
              $islem = $Sorgu->fetchAll(PDO::FETCH_ASSOC);
              ?>
              <?php foreach ($islem as $Sonuc) { ?>
                <div class="item">
                  <img class="lazy" src="" data-src="<?= tema ?>/uploads/fotogaleri/diger/<?= $Sonuc[
    "resim"
] ?>"
                       alt="<?= $GALERISonuc["adi"] ?>">
                </div>
              <?php } ?>
            </div>
          </div>
        <?php } ?>
      </div>
      <?php } ?>

    </div>
  </div>
</section>
<?php 
    $sectionBuffers['etkinlikler'] = ob_get_clean();
}
?>
<!-- ETKİNLİK BİTİŞ (JA-stili) -->


<script>
// Tabs: Buffer sonrası çalışması için doğrudan event delegation
(function() {
  document.addEventListener('click', function(e) {
    if(e.target.classList.contains('tab') && e.target.closest('.evt-tabs')) {
      e.preventDefault();
      var box = e.target.closest('.tabs');
      if(!box) return;
      
      // Tüm tab ve pane'leri pasif yap
      box.querySelectorAll('.tab').forEach(function(b){ b.classList.remove('active'); });
      box.querySelectorAll('.pane').forEach(function(p){ p.classList.remove('show'); });
      
      // Tıklanan tab'ı aktif yap
      e.target.classList.add('active');
      
      // İlgili pane'i göster
      var targetId = e.target.getAttribute('data-target');
      var targetPane = box.querySelector('#' + targetId);
      if(targetPane) {
        targetPane.classList.add('show');
      }
    }
  });
})();

// Başkan Galeri Owl Carousel - Custom Navigation
(function($){
  $(function(){
    $('.owl-carousel-etkinlik').each(function(){
      var $owl = $(this);
      var $nav = $owl.prev('.baskan-galeri-nav');
      
      if($owl.length && typeof $owl.owlCarousel === 'function') {
        $owl.owlCarousel({
          loop: false,
          margin: 10,
          nav: true,
          dots: false,
          navContainer: $nav,
          navText: ['‹', '›'],
          smartSpeed: 450,
          responsive: {
            0: {items: 1},
            600: {items: 1},
            1000: {items: 1}
          }
        });
      }
    });
  });
})(jQuery);

/* Lazy fallback: eğer lazy script'in yoksa data-src -> src aktar */
(function(){
  var imgs = document.querySelectorAll('img[data-src]');
  imgs.forEach(function(img){
    if(!img.getAttribute('src')) img.setAttribute('src', img.getAttribute('data-src'));
  });
})();
</script>





<!-- HIZLI MENÜ BAŞLANGIÇ (JA stili) -->
<?php if ($moduller["alan11"] == "1") { 
    ob_start();
?>
<section class="quickmenu-band my-5">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="custom-owl-nav hizlimenu-nav"></div>
        <div class="owl-carousel owl-carousel-hizlimenu">
        <?php
        $Sorgu = $db->prepare(
            "SELECT * FROM slidermenu WHERE menu_durum = ? AND anasayfa = ? AND dil = ? ORDER BY menu_sira ASC",
        );
        $Sorgu->execute(["1", "1", $_SESSION["k_dil"]]);
        $islem = $Sorgu->fetchALL(PDO::FETCH_ASSOC);
        ?>
          <?php foreach ($islem as $Sonuc) { ?>
          <div class="item">
            <div class="hizli-menu-box">
              <a <?php echo $Sonuc["sekme"] == 1 ? 'target="_blank"' : ""; ?>
                 href="<?php echo $Sonuc["menu_url"] == "0"
                     ? $Sonuc["link"]
                     : $Sonuc["menu_url"]; ?>"
                 class="qm-card" style="background:<?php echo $Sonuc[
                     "menu_renk"
                 ]; ?>">
                <div class="hizli-icon"><i class="<?php echo $Sonuc[
                    "menu_icon"
                ]; ?>"></i></div>
                <p class="qm-title"><?php echo $Sonuc["menu_isim"]; ?></p>
                <p class="qm-sub"><?php echo $Sonuc["menu_kisa"]; ?></p>
              </a>
              <a <?php echo $Sonuc["sekme"] == 1 ? 'target="_blank"' : ""; ?>
                 href="<?php echo $Sonuc["menu_url"] == "0"
                     ? $Sonuc["link"]
                     : $Sonuc["menu_url"]; ?>"
                 class="hizli-back" style="background:<?php echo $Sonuc[
                     "menu_renk"
                 ]; ?>e8">
                <span><?= @$dil["txt70"] ?></span>
              </a>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php } ?>
<!-- HIZLI MENÜ BİTİŞ -->

<!-- Hızlı Menü - JS (Owl başlatma) -->
<script>
(function($){
  $(function(){
    var $owl = $('.owl-carousel-hizlimenu');
    if(!$owl.length || !$owl.owlCarousel) return;

    $owl.owlCarousel({
      loop:false,
      margin:16,
      dots:false,
      nav:true,
      navContainer: '.hizlimenu-nav',
      navText: ['‹','›'],
      smartSpeed:450,
      responsive:{
        0:{items:2},
        576:{items:3},
        992:{items:4},
        1280:{items:5}
      }
    });
  });
})(jQuery);
</script>
<?php 
    $sectionBuffers['hizlimenu'] = ob_get_clean();

?>

<!-- BAŞKAN HAKKINDA BAŞLANGIÇ (Yeni Modern Tasarım) -->
<?php if ($moduller["alan12"] == "1") { 
    ob_start();
?>
<section class="modern-baskan-section">
  <div class="container-fluid">
    <div class="row g-0">
      <!-- Sol: Başkan Fotoğraf ve Quote -->
      <div class="col-lg-6 baskan-image-side">
        <div class="baskan-image-wrapper">
          <div class="baskan-image-container">
            <img class="lazy baskan-main-image"
                 src=""
                 data-src="<?php echo tema; ?>/uploads/baskan/<?php echo $baskan[
    "gorsel"
]; ?>"
                 alt="<?php echo htmlspecialchars(
                     $baskan["adi"],
                     ENT_QUOTES,
                     "UTF-8",
                 ); ?>">
            <!-- Dekoratif elementler -->
            <div class="baskan-decor-1"></div>
            <div class="baskan-decor-2"></div>
          </div>

          <!-- Quote Box -->
          <div class="baskan-quote-box">
            <div class="quote-icon">
              <i class="fas fa-quote-left"></i>
            </div>
            <blockquote class="baskan-quote">
              <?php echo $baskan["slogan"]; ?>
            </blockquote>
            <div class="quote-author">
              - <?php echo $baskan["adi"]; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Sağ: Başkan Bilgi ve İçerik -->
      <div class="col-lg-6 baskan-content-side">
        <div class="baskan-content-wrapper">
          <!-- Başlık Bölümü -->
          <div class="baskan-header">
            <span class="baskan-subtitle"><?= @$dil["baskan_subtitle"] ?></span>
            <h2 class="baskan-name"><?php echo $baskan["adi"]; ?></h2>
            <div class="baskan-title-line"></div>
          </div>

          <!-- Başkan Hakkında Metin -->
          <div class="baskan-about">
            <p class="baskan-description">
              <?php echo $baskan["hakkinda"]; ?>
            </p>
          </div>

          <!-- İstatistik Kartları -->
          <div class="baskan-stats">
            <div class="stat-card">
              <div class="stat-number"><?= @$dil["stat_yil"] ?></div>
              <div class="stat-label"><?= @$dil["stat_baslangic_yili"] ?></div>
            </div>
            <div class="stat-card">
              <div class="stat-number"><?= @$dil["stat_proje_sayi"] ?></div>
              <div class="stat-label"><?= @$dil["stat_tamamlanan_proje"] ?></div>
            </div>
            <div class="stat-card">
              <div class="stat-number"><?= @$dil["stat_kisi_sayi"] ?></div>
              <div class="stat-label"><?= @$dil["stat_faydalanan_kisi"] ?></div>
            </div>
          </div>

          <!-- Sosyal Medya ve İletişim -->
          <div class="baskan-contact">
            <div class="social-links">
              <?php if ($baskan["facebook"]) { ?>
                <a href="<?php echo $baskan[
                    "facebook"
                ]; ?>" class="social-link facebook" title="Facebook">
                  <i class="fab fa-facebook-f"></i>
                </a>
              <?php } ?>
              <?php if ($baskan["twitter"]) { ?>
                <a href="<?php echo $baskan[
                    "twitter"
                ]; ?>" class="social-link twitter" title="Twitter">
                  <i class="fab fa-twitter"></i>
                </a>
              <?php } ?>
              <?php if ($baskan["instagram"]) { ?>
                <a href="<?php echo $baskan[
                    "instagram"
                ]; ?>" class="social-link instagram" title="Instagram">
                  <i class="fab fa-instagram"></i>
                </a>
              <?php } ?>
              <?php if ($baskan["linkedin"]) { ?>
                <a href="<?php echo $baskan[
                    "linkedin"
                ]; ?>" class="social-link linkedin" title="LinkedIn">
                  <i class="fab fa-linkedin-in"></i>
                </a>
              <?php } ?>
              <?php if ($baskan["youtube"]) { ?>
                <a href="<?php echo $baskan[
                    "youtube"
                ]; ?>" class="social-link youtube" title="YouTube">
                  <i class="fab fa-youtube"></i>
                </a>
              <?php } ?>
            </div>

            <div class="baskan-cta">
              <a href="<?= $htc['iletisimurl'] . $html ?>" class="modern-btn">
                <span><?= @$dil["iletisime_gec"] ?></span>
                <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php 
    $sectionBuffers['baskan'] = ob_get_clean();
}
?>
<!-- BAŞKAN HAKKINDA BİTİŞ -->

<!-- PROJELER BAŞLANGIÇ (JA stili) -->
<?php if ($moduller["alan13"] == "1") { 
    ob_start();
?>
<section class="projeler-section">
  <div class="projeler-topline"></div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 mt-4">
        <h3 class="g-title text-center projeler-title"><?= @$dil[
            "txt71"
        ] ?></h3>
      </div>
      <div class="col-12">
        <div class="custom-owl-nav projeler-nav"></div>
        <div class="owl-carousel owl-carousel-proje">
          <?php
          $Sorgu = $db->prepare(
              "SELECT * FROM projeler WHERE durum = ? AND dil = ? ORDER BY sira ASC",
          );
          $Sorgu->execute(["1", $_SESSION["k_dil"]]);
          $islem = $Sorgu->fetchALL(PDO::FETCH_ASSOC);
          ?>
          <?php foreach ($islem as $Sonuc) { ?>
          <div class="item">
            <article class="proje-card">
              <div class="proje-media">
                <a href="<?php echo $htc["projedetayurl"]; ?>/<?php
echo $Sonuc["seo"];
echo $html;
?>">
                  <img class="lazy" src=""
                       data-src="<?php echo tema; ?>/uploads/projeler/<?php echo $Sonuc[
    "kapak"
]; ?>"
                       alt="<?php echo htmlspecialchars(
                           $Sonuc["adi"],
                           ENT_QUOTES,
                           "UTF-8",
                       ); ?>">
                </a>
              </div>
              <div class="proje-content">
                <h4 class="g-title">
                  <a href="<?php echo $htc["projedetayurl"]; ?>/<?php
echo $Sonuc["seo"];
echo $html;
?>">
                    <?php echo $Sonuc["adi"]; ?>
                  </a>
                </h4>
                <p class="proje-spot"><?php echo $Sonuc["spot"]; ?></p>
                <a class="proje-cta" href="<?php echo $htc[
                    "projedetayurl"
                ]; ?>/<?php
echo $Sonuc["seo"];
echo $html;
?>">
                  <?= @$dil["txt59"]  ; ?>
                </a>
              </div>
            </article>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 tumunu-gor">
        <a class="btn-projeler" href="<?php
        echo $htc["projelerurl"];
        echo $html;
        ?>"><?= @$dil["txt72"] ?></a>
      </div>
    </div>
  </div>
</section>
<?php 
    $sectionBuffers['projeler'] = ob_get_clean();
}
?>
<!-- PROJELER BİTİŞ -->



<!-- VİDEO GALERİ BAŞLANGIÇ (JA stili) -->
<?php if ($moduller["alan14"] == "1") { 
    ob_start();
?>
<section class="gallery-band video position-relative">
  <img class="bg-festival lazy" src="" data-src="<?php echo tema; ?>/uploads/arkaplan/arkaplan3/<?php echo $arkaplan[
    "arkaplan3"
]; ?>"
       alt="<?=@$dil['txt570'];?>">
  <div class="topline"></div>

  <div class="container">
    <div class="row align-items-stretch">
      <!-- Solda açıklama paneli -->
      <div class="col-xl-3 text-xl-left text-lg-center z-index-9 d-flex">
        <div class="gallery-description panel">
          <div class="title"><?= @$dil["txt73"] ?></div>
          <div class="text"><?= @$dil["txt74"] ?></div>
          <div class="buttons-container">
            <a class="button-border light" href="<?php echo $htc["videourl"] .
                $html; ?>">
              <?= @$dil[
                  "txt75"
              ] ?> <span class="icon"><i class="fal fa-arrow-right"></i></span>
            </a>
          </div>
        </div>
      </div>

      <!-- Sağda slider -->
      <div class="col-xl-9">
        <div class="row position-relative">
          <div class="custom-owl-nav videogaleri-nav"></div>
          <div class="owl-carousel owl-carousel-videogaleri gallery list">
            <?php
            $Sorgu = $db->prepare(
                "SELECT * FROM video_galeri WHERE durum = ? AND dil = ? ORDER BY id DESC",
            );
            $Sorgu->execute(["1", $_SESSION["k_dil"]]);
            $islem = $Sorgu->fetchALL(PDO::FETCH_ASSOC);
            ?>
            <?php foreach ($islem as $Sonuc) { ?>
            <div class="col-12">
              <article class="gallery-card">
                <a href="<?php echo $htc["videodetayurl"]; ?>/<?php echo $Sonuc[
    "seo"
] . $html; ?>">
                  <div class="gallery-cover">
                    <img class="lazy" src="" data-src="<?php echo tema; ?>/uploads/videogaleri/kapak/<?php echo $Sonuc[
    "resim"
]; ?>"
                         alt="<?php echo htmlspecialchars(
                             $Sonuc["adi"],
                             ENT_QUOTES,
                             "UTF-8",
                         ); ?>">
                    <span class="play-badge"><i class="fab fa-youtube"></i></span>
                  </div>
                  <div class="gallery-body">
                    <div class="title clamp-2"><?php echo $Sonuc[
                        "adi"
                    ]; ?></div>
                    <div class="date"><i class="far fa-calendar-alt"></i> <?php echo cVCLmHLxbS_tarih(
                        $Sonuc["tarih"],
                    ); ?></div>
                  </div>
                </a>
              </article>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
      <!-- /slider -->
    </div>
  </div>
</section>
<?php 
    $sectionBuffers['videogaleri'] = ob_get_clean();
}
?>
<!-- VİDEO GALERİ BİTİŞ -->

<!-- FOTO GALERİ BAŞLANGIÇ (JA stili) -->
<?php if ($moduller["alan15"] == "1") { 
    ob_start();
?>
<section class="gallery-band photo position-relative">
  <img class="bg-festival lazy" src="" data-src="<?php echo tema; ?>/uploads/arkaplan/arkaplan4/<?php echo $arkaplan[
    "arkaplan4"
]; ?>"
       alt="<?=@$dil['txt571'];?>">
  <div class="topline"></div>

  <div class="container">
    <div class="row align-items-stretch">
      <!-- Sol: slider -->
      <div class="col-xl-9">
        <div class="row position-relative">
          <div class="custom-owl-nav fotogaleri-nav"></div>
          <div class="owl-carousel owl-carousel-fotogaleri">
            <?php
            $Sorgu = $db->prepare(
                "SELECT * FROM foto_galeri WHERE durum = ? AND dil = ? ORDER BY sira ASC",
            );
            $Sorgu->execute(["1", $_SESSION["k_dil"]]);
            $islem = $Sorgu->fetchALL(PDO::FETCH_ASSOC);
            ?>
            <?php foreach ($islem as $Sonuc) { ?>
            <div class="col-12">
              <article class="gallery-card">
                <a href="<?php echo $htc["fotodetayurl"]; ?>/<?php echo $Sonuc[
    "seo"
] . $html; ?>">
                  <div class="gallery-cover">
                    <img class="lazy" src="" data-src="<?php echo tema; ?>/uploads/fotogaleri/kapak/<?php echo $Sonuc[
    "kapak"
]; ?>"
                         alt="<?php echo htmlspecialchars(
                             $Sonuc["adi"],
                             ENT_QUOTES,
                             "UTF-8",
                         ); ?>">
                    <span class="img-badge"><i class="far fa-image"></i></span>
                  </div>
                  <div class="gallery-body">
                    <div class="title clamp-2"><?php echo $Sonuc[
                        "adi"
                    ]; ?></div>
                    <div class="date"><i class="far fa-calendar-alt"></i> <?php echo cVCLmHLxbS_tarih(
                        $Sonuc["tarih"],
                    ); ?></div>
                  </div>
                </a>
              </article>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>

      <!-- Sağ: açıklama paneli -->
      <div class="col-xl-3 text-xl-right text-lg-center d-flex">
        <div class="gallery-description panel alt">
          <div class="title"><?= @$dil["txt76"] ?></div>
          <div class="text"><?= @$dil["txt77"] ?></div>
          <div class="buttons-container">
            <a class="button-border light" href="<?php echo $htc["fotourl"] .
                $html; ?>">
              <?= @$dil[
                  "txt78"
              ] ?> <span class="icon"><i class="fal fa-arrow-right"></i></span>
            </a>
          </div>
        </div>
      </div>
      <!-- /panel -->
    </div>
  </div>
</section>
<?php 
    $sectionBuffers['fotogaleri'] = ob_get_clean();
}
?>
<!-- FOTO GALERİ BİTİŞ -->

<!-- İLETİŞİM BAŞLANGIÇ (JA stili) -->
<?php if ($moduller["alan17"] == "1") { 
    ob_start();
?>
<!-- İLETİŞİM (Modern Simple Form) -->
<section class="modern-contact-section" id="contact">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-xl-7">
        
        <!-- Başlık Bölümü -->
        <div class="contact-header text-center mb-5">
          <span class="contact-pill"><?= @$dil["txt80"] ?></span>
          <h2 class="contact-title"><?= @$dil["txt81"] ?></h2>
        </div>

        <!-- Form -->
        <div class="modern-contact-card">
          <form class="modern-contact-form" action="_class/site_islem.php" method="post">
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group-modern">
                  <input type="text" name="isim" class="form-control-modern" placeholder="<?= @$dil["txt82"] ?>*" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group-modern">
                  <input type="text" name="telefon" class="form-control-modern" placeholder="<?= @$dil["txt84"] ?>">
                </div>
              </div>
            </div>

            <div class="form-group-modern">
              <input type="email" name="email" class="form-control-modern" placeholder="<?= @$dil["txt83"] ?>">
            </div>

            <div class="form-group-modern">
              <input type="text" name="konu" class="form-control-modern" placeholder="<?= @$dil["txt85"] ?>">
            </div>

            <div class="form-group-modern">
              <textarea name="mesaj" class="form-control-modern" placeholder="<?= @$dil["txt86"] ?>*" required></textarea>
            </div>

            <input type="hidden" name="kontrol" value="" id="kontrol">
            <input type="hidden" name="iletisimurl" value="<?php echo $sayfalink; ?>" />

            <div class="text-center">
              <button type="submit" name="mesajbtn" class="btn-sign-up">
                <?= @$dil["txt87"] ?>
              </button>
            </div>

          </form>
        </div>

      </div>
    </div>
  </div>
</section>


<?php 
    $sectionBuffers['iletisim'] = ob_get_clean();
}
?>
<!-- İLETİŞİM BİTİŞ -->


<!-- BILGILENDİRME BAŞLANGIÇ (JA stili) -->
<?php if ($moduller["alan33"] == "1") { 
    ob_start();
?>
<main class="ways-layout">

  <!-- SOL METİN ALANI -->
  <section class="ways-panel">
    <div class="ways-panel-inner">

      <span class="ways-badge"><?= @$dil["bilgilendire_baslik"] ?></span>

      <h1 class="ways-title">
        <?= @$dil["bilgilendire_kisa"] ?>
      </h1>

      <p class="ways-lead">
        <?= @$dil["bilgilendire_aciklama"] ?>
      </p>

      <div class="ways-grid">


                  <?php 
            $Sorgu = $db->prepare(
                "SELECT * FROM bilgilendirme_kutulari WHERE durum = ? AND dil = ? ORDER BY sira ASC",
            );
            $Sorgu->execute(["1", $_SESSION["k_dil"]]);
            $islem = $Sorgu->fetchALL(PDO::FETCH_ASSOC);
            ?>
            <?php foreach ($islem as $Sonuc) { ?>
        <article class="ways-card">
          <div class="ways-icon"><i class="<?php echo $Sonuc['ikon']; ?>"></i></div>
          <div>
            <h3><?php echo $Sonuc['baslik']; ?></h3>
            <p><?php echo $Sonuc['aciklama']; ?></p>
            <?php if($Sonuc['link'] != ""){?>
                <a href="<?php echo $Sonuc['link']; ?><?php echo $html; ?>" class="ways-btn"><?= @$dil["bilgilendire_button"] ?></a>
            <?php } ?>
          </div>
        </article>
        <?php } ?>

        

      </div>

    </div>
  </section>

  <!-- SAĞ RESİM ALANI -->
  <section class="ways-hero"></section>

</main>
<style>
    .ways-hero{
      background:url('<?php echo tema; ?>/uploads/arkaplan/arkaplan24/<?php echo $arkaplan[ "arkaplan24" ]; ?>')
      center right/cover no-repeat;
      position:relative;
    }
    .ways-panel {
      center right/cover no-repeat;
      position:relative;
}
.ways-layout {
    background:url('<?php echo tema; ?>/uploads/arkaplan/arkaplan24/<?php echo $arkaplan[ "arkaplan24" ]; ?>')
      center right/cover no-repeat;

}
  </style>

<?php 
    $sectionBuffers['bilgilendirme'] = ob_get_clean();
}
?>
<!-- BILGILENDİRME BİTİŞ -->

<!-- ========== Instagram Foto ========== -->
<?php if (!empty($moduller["alan30"]) && $moduller["alan30"] == "1") { 
    ob_start();
?>

<?php
/* ================= IG FETCH (secure + cached + fallback) ================= */
function instagramGetPost($limit = 12)
{
    $token = isset($GLOBALS["ayar"]["instagramtoken"])
        ? trim($GLOBALS["ayar"]["instagramtoken"])
        : "";
    $username = isset($GLOBALS["ayar"]["instagram_username"])
        ? trim($GLOBALS["ayar"]["instagram_username"])
        : "";

    // Token yoksa doğrudan boş dön
    if ($token === "") {
        return ["data" => []];
    }

    // Session cache (10 dk)
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }
    if (!empty($_SESSION["ig_cache"]) && !empty($_SESSION["ig_cache_time"])) {
        if (time() - $_SESSION["ig_cache_time"] < 600) {
            return $_SESSION["ig_cache"];
        }
    }

    $fields = "media_url,media_type,thumbnail_url,permalink,timestamp,caption,like_count,comments_count,username";

    // Username varsa Business Discovery API kullan
    if ($username !== "") {
        $url = "https://graph.instagram.com/me"
            . "?fields=business_discovery.fields(media.limit(" . (int)$limit . "){" . $fields . "})"
            . "&username=" . urlencode($username)
            . "&access_token=" . $token;
    } else {
        // Fallback: kendi hesabının postları
        $url = "https://graph.instagram.com/me/media"
            . "?fields=" . $fields
            . "&limit=" . (int)$limit
            . "&access_token=" . $token;
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_TIMEOUT => 3,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);
    $res = curl_exec($ch);
    if ($res === false) {
        error_log("IG cURL error: " . curl_error($ch));
        curl_close($ch);
        $bos = ["data" => []];
        $_SESSION["ig_cache"] = $bos;
        $_SESSION["ig_cache_time"] = time();
        return $bos;
    }
    curl_close($ch);

    $json = json_decode($res, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("IG JSON error: " . json_last_error_msg());
        return ["data" => []];
    }

    // Business Discovery yanıtı farklı yapıda gelir
    if ($username !== "" && isset($json["business_discovery"]["media"]["data"])) {
        $result = ["data" => $json["business_discovery"]["media"]["data"]];
    } elseif (!empty($json["data"])) {
        $result = $json;
    } else {
        error_log("IG empty response for " . ($username ?: "self"));
        return ["data" => []];
    }

    $_SESSION["ig_cache"] = $result;
    $_SESSION["ig_cache_time"] = time();
    return $result;
}

$media = instagramGetPost(12);


if (empty($media["data"])) {
    $placeholders = [];
    for ($i = 1; $i <= 6; $i++) {
        $placeholders[] = [
            "media_type" => "IMAGE",
            "media_url" => "https://placehold.co/600x400?text=Paylaşım+" . $i,
            "permalink" => "#",
            "caption" => "Örnek paylaşım metni #" . $i,
            "username" => "exampleuser",
            "timestamp" => date("Y-m-d H:i:s", strtotime("-" . $i . " days")),
            "like_count" => rand(20, 300),
            "comments_count" => rand(1, 20),
        ];
    }
    $media["data"] = $placeholders;
}
?>
<section class="insta-band" id="instagram">
  <div class="topline"></div>
  <div class="wrap">
    <div class="ig-head">
      <h4><?= @$dil["connect_with_us"] ?? "Bize Katılın" ?><?php
        $igUser = isset($GLOBALS["ayar"]["instagram_username"]) ? trim($GLOBALS["ayar"]["instagram_username"]) : "";
        if ($igUser !== ""): ?> <a href="https://instagram.com/<?= htmlspecialchars($igUser) ?>" target="_blank" rel="noopener" style="font-size:.85em;font-weight:400;margin-left:8px;text-decoration:none;">@<?= htmlspecialchars($igUser) ?></a><?php endif; ?></h4>
    </div>
    <?php
    require_once __DIR__ . '/../../../widget.php';
    echo ig_widget('ailevenesil', 9, IG_CACHE_TTL, 'slider', 3);
    ?>
  </div>
</section>

<script>
/* Owl varsa Owl; yoksa pure-JS kaydırma + lazy + autoplay */
(function($){
  function lazyInit(root){
    var imgs = root.querySelectorAll('img[data-src]');
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function(entries){
        entries.forEach(function(e){
          if(e.isIntersecting){
            var im = e.target;
            im.src = im.getAttribute('data-src');
            im.removeAttribute('data-src');
            io.unobserve(im);
          }
        });
      }, {root: root, rootMargin:'120px', threshold:0.01});
      imgs.forEach(function(i){ io.observe(i); });
    } else {
      imgs.forEach(function(i){ i.src = i.getAttribute('data-src'); i.removeAttribute('data-src'); });
    }
  }

  $(function(){
    var $owl = $('.ig-carousel');
    var $prevBtn = document.querySelector('.ig-btn[data-dir="-1"]');
    var $nextBtn = document.querySelector('.ig-btn[data-dir="1"]');
    var autoplayInterval = null;
    var autoplayDelay = 4000; // 4 saniye
    var userInteracted = false;

    /* ======================================
       OWL VARSA (jQuery Carousel)
    ====================================== */
    if ($owl.length && typeof $owl.owlCarousel === 'function'){
      $owl.owlCarousel({
        loop:true,
        margin:16,
        dots:false,
        nav:false,
        smartSpeed:600,
        autoplay:true,
        autoplayTimeout:autoplayDelay,
        autoplayHoverPause:true,
        responsive:{
          0:{items:1},
          600:{items:2},
          1024:{items:3}
        },
        onInitialized: function(){ lazyInit($owl.get(0)); }
      });

      // Butonlar
      if ($prevBtn) $prevBtn.addEventListener('click', function(){
        $owl.trigger('prev.owl.carousel');
        userInteracted = true;
      });
      if ($nextBtn) $nextBtn.addEventListener('click', function(){
        $owl.trigger('next.owl.carousel');
        userInteracted = true;
      });
    }

    /* ======================================
       OWL YOKSA (Pure JS Fallback)
    ====================================== */
    else {
      var wrap = document.getElementById('igCarousel');
      if(!wrap) return;
      wrap.classList.remove('owl-carousel');
      wrap.classList.add('ig-strip');
      wrap.id = 'igStrip';

      function step(){ return Math.round(wrap.clientWidth * 0.92); }
      function scrollByDir(dir){
        wrap.scrollBy({left: dir * step(), behavior: 'smooth'});
      }
      function updateBtns(){
        if (!$prevBtn || !$nextBtn) return;
        var max = wrap.scrollWidth - wrap.clientWidth - 2;
        $prevBtn.disabled = wrap.scrollLeft <= 1;
        $nextBtn.disabled = wrap.scrollLeft >= max;
      }

      // Butonlar
      if ($prevBtn) $prevBtn.addEventListener('click', function(){
        scrollByDir(-1);
        userInteracted = true;
      });
      if ($nextBtn) $nextBtn.addEventListener('click', function(){
        scrollByDir(1);
        userInteracted = true;
      });

      wrap.addEventListener('scroll', function(){
        window.requestAnimationFrame(updateBtns);
      });
      window.addEventListener('resize', updateBtns);
      lazyInit(wrap);
      updateBtns();

      /* -------- Autoplay fallback -------- */
      function startAutoplay(){
        stopAutoplay();
        autoplayInterval = setInterval(function(){
          if(userInteracted){ // kullanıcı kaydırdıysa 1 tur bekle
            userInteracted = false;
            return;
          }
          if(wrap.scrollLeft + wrap.clientWidth >= wrap.scrollWidth - 5){
            wrap.scrollTo({left:0, behavior:'smooth'}); // en sona geldiyse başa dön
          } else {
            scrollByDir(1);
          }
        }, autoplayDelay);
      }

      function stopAutoplay(){
        if(autoplayInterval){
          clearInterval(autoplayInterval);
          autoplayInterval = null;
        }
      }

      // Başlat
      startAutoplay();
      // Üzerine gelince dur, çıkınca devam et
      wrap.addEventListener('mouseenter', stopAutoplay);
      wrap.addEventListener('mouseleave', startAutoplay);
    }

    // Lazy fallback (görseller)
    document.querySelectorAll('.ig-media img[data-src]').forEach(function(img){
      if(!img.getAttribute('src'))
        img.setAttribute('src', img.getAttribute('data-src'));
    });
  });
})(window.jQuery || function(sel){return [];});
</script>


<?php 
    $sectionBuffers['instagram'] = ob_get_clean();
} 
?>


<!-- BAĞIŞ MODÜLLERİ BAŞLANGIÇ (JA stili, dinamik) -->
 <?php if ($moduller["alan29"] == "1") { 
    ob_start();
?>
<?php
$bagisSorgu = $db->prepare(
    "SELECT * FROM bagis_moduller WHERE durum = 1 AND anasayfada_goster = 1 ORDER BY sira ASC",
);
$bagisSorgu->execute();
$bagisModulleri = $bagisSorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (!empty($bagisModulleri)): ?>
<section class="donations" id="donations">
  <div class="wrap">
    <div class="head">
      <span class="pill"><?= @$dil["take_action"] ?></span>
      <h3><?= @$dil["bagis_kampanyalari"] ?></h3>
      <p><?= @$dil["bagis_kampanya_desc"] ?></p>
    </div>

    <div class="d-grid">
      <?php foreach ($bagisModulleri as $modul): ?>
        <?php // Yüzde hesaplama
          // Yüzde hesaplama
          // Yüzde hesaplama
          // Yüzde hesaplama

        $yuzde = 0;
        if (!empty($modul["hedef_tutar"]) && $modul["hedef_tutar"] > 0) {
            $yuzde = round(
                ($modul["toplanan_tutar"] / $modul["hedef_tutar"]) * 100,
                2,
            );
            if ($yuzde > 100) {
                $yuzde = 100;
            }
            if ($yuzde < 0) {
                $yuzde = 0;
            }
        }
        $detayUrl = $htc["bagismoduldetayurl"] . "/" . $modul["seo"] . $html; // Kapak resmi / ikon
        $kapak = !empty($modul["kapak_resmi"])
            ? tema .
                "/uploads/bagis_moduller/" .
                htmlspecialchars($modul["kapak_resmi"], ENT_QUOTES, "UTF-8")
            : null;
        $iconCls = !empty($modul["icon_class"])
            ? htmlspecialchars($modul["icon_class"], ENT_QUOTES, "UTF-8")
            : null;
        ?>

        <article class="d-card">
          <div class="d-media">
            <?php if ($kapak): ?>
              <img src="<?= $kapak ?>" alt="<?= htmlspecialchars(
    $modul["adi"],
    ENT_QUOTES,
    "UTF-8",
) ?>">
            <?php else: ?>
              <div class="d-media-icon">
                <i class="<?= $iconCls ?: "fa fa-heart" ?>"></i>
              </div>
            <?php endif; ?>
          </div>

          <div class="d-body">
            <h4 class="d-title"><?= htmlspecialchars(
                $modul["adi"],
                ENT_QUOTES,
                "UTF-8",
            ) ?></h4>

            <?php if (!empty($modul["aciklama"])): ?>
              <p class="d-desc"><?= htmlspecialchars(
                  $modul["aciklama"],
                  ENT_QUOTES,
                  "UTF-8",
              ) ?></p>
            <?php endif; ?>
            
            <?php if ($modul["istatistik_durum"] == "1") { ?>

            <?php if (
                !empty($modul["hedef_tutar"]) &&
                $modul["hedef_tutar"] > 0
            ): ?>
              <div class="progress" aria-label="Bağış ilerlemesi">
                <div class="bar" style="width: <?= $yuzde ?>%" aria-valuenow="<?= $yuzde ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <div class="d-meta">
                <span><?= @$dil["toplanan"] ?> <?= number_format(
                    (float) $modul["toplanan_tutar"],
                    2,
                    ",",
                    ".",
                ) ?> TL</span>
                <span><?= @$dil["hedef"] ?> <?= number_format(
                    (float) $modul["hedef_tutar"],
                    2,
                    ",",
                    ".",
                ) ?> TL</span>
              </div>
            <?php else: ?>
              <div class="d-meta">
                <span><?= @$dil["toplanan"] ?> <?= number_format(
                    (float) $modul["toplanan_tutar"],
                    2,
                    ",",
                    ".",
                ) ?> TL</span>
              </div>
            <?php endif; ?>
            <?php  } ?>

            <a class="btn donate" href="<?= $detayUrl ?>"><?= @$dil["bagis_yap"] ?></a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
<?php 
    $sectionBuffers['bagismoduller'] = ob_get_clean();
}
?>
<!-- BAĞIŞ MODÜLLERİ BİTİŞ -->


<!-- IMPACT & REACH SECTİON BAŞLANGIÇ -->
<?php if ($moduller["alan27"] == "1") { 
    ob_start();
?>
<section class="impact-by-numbers-section py-5">
	<?php
 // Veritabanından verileri çek
 $impactSorgu = $db->prepare(
     "SELECT * FROM impact_reach WHERE durum = 1 AND dil = ? ORDER BY sira ASC, id DESC LIMIT 6",
 );
 $impactSorgu->execute([$_SESSION["k_dil"]]);
 $impactSonuclar = $impactSorgu->fetchALL(PDO::FETCH_ASSOC);
 ?>
 <style>
.impact-reach-sag .impact-card-bilgi:nth-child(odd) {
    background-color: rgba(245, 156, 28, 0.1) !important;
}
.impact-reach-sag .impact-card-bilgi:nth-child(even) {
    background-color: rgba(197, 25, 138, 0.09) !important;
}
  </style>
    <div class="container">
		<div id="impact-reach-section" class="impact-reach-wrapper">
	    <div class="impact-reach-container">
        <div class="impact-reach-sol">
            <span class="impact-icon-wrapper">
                <img width="103" height="123" src="uploads/files/impact-icon.svg" class="impact-icon-img fade-in-bottom animate" alt="" decoding="async" loading="lazy">
            </span>
            <h2 class="impact-baslik fade-in-bottom animate"><?= @$dil[
                "txt221"
            ] ?><mark style="background-color:rgba(0, 0, 0, 0)" class="impact-nokta">.</mark></h2>
            <p class="impact-aciklama fade-in-bottom animate"><?= @$dil[
                "txt222"
            ] ?></p>
        </div>

        <div class="impact-reach-sag">
            <?php foreach ($impactSonuclar as $index => $impact) {
                ?>
            <div class="impact-card-bilgi">
                <span class="card-arrow-wrapper">
                    <i style=" background: white;padding: 7px; border-radius: 34px;font-size: 35px; margin-top: -2pc; margin-left: 1pc; "  class="<?php echo $impact[ "ikon" ]; ?> card-arrow-img fade-in-bottom animate"></i>

                </span>
                <h3 class="card-sayi fade-in-bottom animate counter" data-count="<?php echo $impact[
                    "sayi"
                ]; ?>">0</h3>
                <p class="card-baslik fade-in-bottom animate"><?php echo $impact[
                    "baslik"
                ]; ?></p>
                <span><?php echo $impact[ "aciklama" ]; ?></span>
            </div>
            <?php
            } ?>
        </div>
    </div>
        </div>
    </div>
	</div>
</section>

<?php 
    $sectionBuffers['impact'] = ob_get_clean();
}
?>
<!-- IMPACT & REACH SECTİON BİTİŞ -->

<!-- PROGRAMLAR SECTİON BAŞLANGIÇ -->
<?php if ($moduller["alan28"] == "1") { 
    ob_start();
?>
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<section class="programs-section py-5" id="learn">
	<div class="container">
		<div class="programs-grid">
			<div class="programs-content">
				<span class="pill"><?= @$dil["txt229"] ?></span>
				<h2 class="programs-title"><?= @$dil["what_we_do"] ?></h2>
				<p class="programs-description"><?= @$dil["what_we_do_desc"] ?></p>
				<!-- <a class="callout" href="<?= $htc["programlarurl"] ?>"><?= @$dil["ja_ogrenim"] ?></a> -->
			</div>
			<!-- Mobil Slider Wrapper -->
			<div class="program-cards-wrapper">
				<!-- Mobil Navigation (üstte, ortalı) -->
				<div class="program-slider-controls">
					<button class="ctrl-btn program-ctrl-prev"><i class="fas fa-chevron-left"></i></button>
					<button class="ctrl-btn program-ctrl-next"><i class="fas fa-chevron-right"></i></button>
				</div>
				<div class="swiper program-swiper">
					<div class="swiper-wrapper program-cards">
						<?php
    $programSorgu = $db->prepare(
        "SELECT * FROM programlar WHERE durum = 1 AND anasayfa_durum = 1 AND dil = ? ORDER BY sira ASC LIMIT 4",
    );
    $programSorgu->execute([$_SESSION["k_dil"]]);
    $programSonuclar = $programSorgu->fetchAll(PDO::FETCH_ASSOC);
    $programsToShow = $programSonuclar;
    foreach ($programsToShow as $index => $program): ?>
						<div class="swiper-slide">
							<article class="p-card">
								<?php if (!empty($program["resim"])): ?>
									<img src="<?php echo tema; ?>/uploads/programlar/<?php echo $program[
    "resim"
]; ?>" alt="<?php echo $program["baslik"]; ?>">
								<?php else: ?>
									<img src="<?php echo tema; ?>/uploads/programlar/<?php echo $program[
    "resim"
]; ?>" alt="<?php echo $program["baslik"]; ?>">
								<?php endif; ?>
								<b><?php echo $program["baslik"]; ?></b>
								<a class="btn donate" href="<?php echo $htc["programdetayurl"]; ?>/<?php
echo $program["seo"];
echo $html;
?>"><?= @$dil["detay_git"] ?></a>
							</article>
						</div>
						<?php endforeach;
    ?>
					</div>
				</div>
				<div class="program-all-btn-wrap">
					<a class="btn-program-all" href="<?= $htc["programlarurl"] ?>"><?= @$dil["ja_ogrenim"] ?> <i class="fas fa-arrow-right"></i></a>
				</div>
			</div>
		</div>
		<div class="quote-wrap">
			<div class="quote">"<?= @$dil["program_quote"] ?>"</div>
		</div>
	</div>
</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Programlar Slider CSS -->
<style>
/* Desktop: kontroller gizle, 2x2 grid */
.program-slider-controls { display: none; }

.program-all-btn-wrap {
	text-align: center;
	margin-top: 30px;
}
@media (max-width: 991px) {
	.program-all-btn-wrap {
		text-align: center;
	}
}
.btn-program-all {
	display: inline-block;
	background: #f51c1c;
	color: #fff !important;
	padding: 14px 34px;
	border-radius: 50px;
	font-weight: 700;
	font-size: 15px;
	text-decoration: none !important;
	transition: 0.3s;
	letter-spacing: .02em;
}
.btn-program-all:hover {
	background: #d31616;
	transform: translateY(-2px);
	box-shadow: 0 8px 20px rgba(245,28,28,.3);
}
.btn-program-all i {
	margin-left: 8px;
}

@media (min-width: 992px) {
	.program-cards-wrapper {
		padding: 0;
		width: 100%;
		box-sizing: border-box;
	}
	.program-swiper {
		overflow: visible !important;
	}
	.program-swiper .swiper-wrapper {
		display: grid !important;
		grid-template-columns: repeat(2, 1fr) !important;
		gap: 22px !important;
		transform: none !important;
		width: 100% !important;
		height: auto !important;
	}
	.program-swiper .swiper-slide {
		width: 100% !important;
		margin: 0 !important;
		height: auto !important;
		transform: none !important;
		opacity: 1 !important;
	}
}

/* Mobil: Swiper slider, oklar üstte */
@media (max-width: 991px) {
	.programs-section .container {
		overflow-x: hidden;
	}
	.programs-grid {
		display: grid !important;
		grid-template-columns: 1fr !important;
		gap: 30px !important;
	}
	.program-slider-controls {
		display: flex;
		justify-content: center;
		gap: 12px;
		margin-bottom: 20px;
	}
	.program-cards-wrapper {
		position: relative;
		width: 100%;
		overflow: hidden;
	}
	.program-swiper {
		overflow: hidden;
		width: 100%;
		padding: 10px 0;
	}
	.program-swiper .swiper-wrapper.program-cards {
		display: flex !important;
		grid-template-columns: none !important;
		gap: 0 !important;
	}
	.program-swiper .swiper-slide {
		height: auto;
		box-sizing: border-box;
		flex-shrink: 0;
		width: 100% !important;
	}
	.program-swiper .swiper-slide .p-card {
		max-width: 100%;
		box-sizing: border-box;
	}
}
</style>

<!-- Programlar Slider JavaScript -->
<script>
(function() {
	var programSwiper = null;

	function initSwiper() {
		if (window.innerWidth <= 991) {
			if (!programSwiper) {
				programSwiper = new Swiper('.program-swiper', {
					slidesPerView: 1,
					spaceBetween: 20,
					centeredSlides: true,
					grabCursor: true,
					loop: true,
					navigation: {
						nextEl: '.program-ctrl-next',
						prevEl: '.program-ctrl-prev',
					}
				});
			}
		} else {
			if (programSwiper) {
				programSwiper.destroy(true, true);
				programSwiper = null;
			}
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initSwiper);
	} else {
		initSwiper();
	}
	window.addEventListener('resize', initSwiper);
})();
</script>

<?php 
    $sectionBuffers['programlar'] = ob_get_clean();
}
?>
<!-- PROGRAMLAR SECTİON BİTİŞ -->

<!-- HARİTA SECTİON BAŞLANGIÇ -->
<?php if ($moduller["alan18"] == "1") { 
    ob_start();
?>
<section class="harita-section pt-0">
	<?php echo maps; ?>
</section>
<?php 
    $sectionBuffers['harita'] = ob_get_clean();
}
?>
<!-- HARİTA SECTİON BİTİŞ -->

<?php
/**
 * PHP ile Dinamik Sıralama - Tüm Section'ları Sırayla Yazdır
 */

// Sıralı buffer'ları yazdır
foreach($grupMinSira as $grup => $sira) {
    if(isset($sectionBuffers[$grup])) {
        echo $sectionBuffers[$grup];
    }
}
?>
