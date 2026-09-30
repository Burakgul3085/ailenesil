<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
$ayarlar = $db->query("SELECT * FROM impact_reach_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
$impactBannerImage = $ayarlar['banner_resim'] ? tema . "/uploads/impact_reach/" . $ayarlar['banner_resim'] : "";
$impactBannerTitle = $ayarlar['banner_baslik'];
$impactBannerSubtitle = $ayarlar['banner_aciklama'];
?>

<?php if($impactBannerTitle || $impactBannerImage){ ?>
<section class="impact-hero-banner<?php echo $impactBannerImage ? ' has-image' : ''; ?>" <?php echo $impactBannerImage ? 'style="--impact-banner:url(\'' . $impactBannerImage . '\');"' : ''; ?>>
    <div class="impact-hero-banner__overlay"></div>
    <div class="container">
        <div class="impact-hero-banner__content">
            <?php if($impactBannerTitle){ ?>
            <h1 class="impact-hero-banner__title"><?php echo $impactBannerTitle; ?></h1>
            <?php } ?>
            <?php if($impactBannerSubtitle){ ?>
            <p class="impact-hero-banner__subtitle"><?php echo $impactBannerSubtitle; ?></p>
            <?php } ?>
        </div>
    </div>
</section>
<?php } ?>


<style>
.impact-hero-banner {
    --impact-banner: none;
    position: relative;
    overflow: hidden;
    padding: 210px 0;
    background-size: cover;
    background-position: center;
    color: #fff;
}

.impact-hero-banner__overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top left, rgba(255,255,255,.35), rgba(13,74,97,.2));
    z-index: 1;
}

.impact-hero-banner__content {
    position: relative;
    z-index: 2;
    max-width: 640px;
}

.impact-hero-banner__title {
    font-size: clamp(2.5rem, 5vw, 3.4rem);
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 20px;
}

.impact-hero-banner__subtitle {
    font-size: 1.125rem;
    line-height: 1.8;
    color: rgba(255,255,255,0.9);
    margin: 0;
}

.impact-hero-banner.has-image {
    background-image: linear-gradient(135deg, rgb(13 74 97 / 0%) 0%, rgb(0 0 0 / 80%) 100%), var(--impact-banner);
}
.impact-reach-hero {
    padding: 80px 0;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.impact-hero-content {
    max-width: 500px;
}

.impact-hero-content h1 {
    font-size: 48px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 30px;
    line-height: 1.2;
}

.impact-hero-content p {
    font-size: 16px;
    color: #5a6c7d;
    line-height: 1.8;
    margin-bottom: 0;
}

.impact-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: -60px;
    position: relative;
    z-index: 2;
}

.stat-card {
    background: white;
    padding: 40px 30px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.stat-card.blue {
    background: #b3e5fc;
}

.stat-card.yellow {
    background: #fff59d;
}

.stat-card.teal {
    background: #b2dfdb;
}

.stat-card h2 {
    font-size: 48px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 10px 0;
    line-height: 1;
}

.stat-card p {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 5px 0;
}

.stat-card span {
    font-size: 13px;
    color: #5a6c7d;
    display: block;
}

.impact-content-section {
    padding: 100px 0;
}

.impact-image-wrapper {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

.impact-image-wrapper img {
    width: 100%;
    height: auto;
    display: block;
}

.impact-text-content h2 {
    font-size: 36px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    line-height: 1.3;
}

.impact-text-content p {
    font-size: 28px;
    color: #5a6c7d;
    line-height: 1.8;
    margin-bottom: 15px;
    font-weight: 800;
    text-transform: uppercase;
}

.expert-opinions-section {
    padding: 110px 0 130px;
    background: #f5f9fc;
    font-family: 'Montserrat', 'Inter', 'Segoe UI', sans-serif;
}

.section-title-center {
    margin-bottom: 45px;
    max-width: 1020px;
}

.section-title-center h2 {
    font-size: 48px;
    font-weight: 800;
    letter-spacing: 1.5px;
    color: #124f64;
    text-transform: uppercase; 
    margin-bottom: 14px;
}

.section-title-center p,
.section-title-center .expert-footnote {
    font-size: 18px;
    color: #366579;
    margin-bottom: 12px;
}

.section-title-center .expert-footnote {
    font-style: italic;
    display: block;
}

.expert-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 45px 55px;
    margin-bottom: 35px;
    box-shadow: 0 25px 45px rgba(16, 67, 92, 0.15);
    display: flex;
    align-items: center;
    gap: 35px;
    position: relative;
}

.expert-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 6px solid #eff6f9;
    flex-shrink: 0;
}

.expert-card-content {
    color: #155d75;
    flex: 1;
}

.expert-quote-text {
    font-size: 20px;
    line-height: 1.9;
    font-weight: 600;
    color: #175f77;
    margin-bottom: 28px;
}

.expert-author {
    font-weight: 800;
    font-size: 18px;
    color: #0c4960;
}

.expert-author-title {
    display: block;
    font-weight: 600;
    font-size: 16px;
    margin-top: 8px;
    color: #2f6a80;
}

.expert-quote-icon {
    position: absolute;
    bottom: 30px;
    right: 35px;
    font-size: 46px;
    color: #1c6a83;
    font-family: Georgia, serif;
    font-weight: bold;
}

.expert-slider {
    position: relative;
}

.expert-carousel {
    position: relative;
    min-height: 215px;
}

.expert-slide { 
    display: none;
    opacity: 0;
    transform: scale(0.97);
    transition: opacity 0.4s ease, transform 0.4s ease;
}

.expert-slide.active {
    display: block;
    opacity: 1;
    transform: scale(1);
}

.expert-slider-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    margin-top: 30px;
}

.expert-slider-btn {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #fff;
    color: #124f64;
    box-shadow: 0 15px 30px rgba(18,79,100,0.15);
    border: none;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.expert-slider-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 35px rgba(18,79,100,0.2);
}

.expert-slider-dots {
    display: flex;
    gap: 8px;
}

.expert-dot {
    width: 14px;
    height: 14px;
    border-radius: 999px;
    background: rgba(18,79,100,0.25);
    border: none;
    padding: 0;
    transition: width 0.2s ease, background 0.2s ease;
}

.expert-dot.active {
    width: 36px;
    background: #124f64;
}

@media (max-width: 991px) {
    .section-title-center {
        text-align: center;
        margin-left: auto;
        margin-right: auto;
    }

    .expert-card {
        flex-direction: column;
        text-align: center;
        padding: 35px 30px 65px;
    }

    .expert-quote-icon {
        right: auto;

        transform: translateX(-50%);
        bottom: -5px;
    }
}

@media (max-width: 991px) {
    .impact-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .impact-hero-content h1 {
        font-size: 36px;
    }
}

@media (max-width: 767px) {
    .impact-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .impact-hero-content h1 {
        font-size: 28px;
    }
    
    .stat-card h2 {
        font-size: 36px;
    }
}

@media (max-width: 991px) {
    .impact-by-numbers-section .impact-reach-container {
        grid-template-columns: 1fr;
    }

    .impact-by-numbers-section .impact-reach-sol {
        text-align: center;
    }
}

@media (max-width: 767px) {
    .impact-by-numbers-section .impact-reach-sag {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .impact-by-numbers-section .impact-card-bilgi {
        text-align: center;
        padding: 25px 18px;
        border-radius: 16px;
    }

    .impact-by-numbers-section .card-arrow-wrapper {
        margin: 0 auto 12px;
    }

    .impact-by-numbers-section .card-sayi {
        font-size: 42px;
    }
}

@media (max-width: 575px) {
    .impact-reach-hero {
        padding: 60px 0;
    }

    .impact-hero-content h1 {
        font-size: 30px;
    }

    .impact-by-numbers-section .impact-reach-wrapper {
        padding: 0 15px;
    }

    .impact-content-section {
        padding: 70px 0;
    }

    .impact-text-content p {
        font-size: 20px;
        text-transform: none;
        text-align: center;
    }

    .impact-hero-banner {
        padding: 90px 0 70px;
    }

    .impact-hero-banner__content {
        text-align: center;
        margin: 0 auto;
    }

    .impact-content-section .row {
        text-align: center;
    }

    .expert-opinions-section {
        padding: 80px 0 90px;
    }

    .section-title-center h2 {
        font-size: 36px;
    }

    .section-title-center p,
    .section-title-center .expert-footnote {
        font-size: 16px;
    }

    .expert-card {
        gap: 20px;
        padding: 30px 20px 55px;
    }

    .expert-card-content {
        text-align: center;
    }

    .expert-quote-text {
        font-size: 18px;
    }

    .expert-author-title {
        font-size: 14px;
    }

    .expert-quote-icon {
        font-size: 36px;
    }

    .expert-slider-controls {
        flex-wrap: wrap;
        gap: 10px;
    }
}

</style>

<section class="impact-by-numbers-section py-5">
	<?php
$impactSorgu = $db->prepare(
    "SELECT * FROM impact_reach WHERE durum = 1 AND dil = ? ORDER BY sira ASC, id DESC LIMIT 6",
);
$impactSorgu->execute([$_SESSION["k_dil"]]);
$impactSonuclar = $impactSorgu->fetchALL(PDO::FETCH_ASSOC);
?>
 <style>
.impact-reach-sag .impact-card-bilgi:nth-child(odd) {
    background-color: #ece9e966 !important;
}
.impact-reach-sag .impact-card-bilgi:nth-child(even) {
    background-color: #ff000017 !important;
}
  </style>
    <div class="container">
		<div id="impact-reach-section" class="impact-reach-wrapper">
	    <div class="impact-reach-container">
        <div class="impact-reach-sol">
            <span class="impact-icon-wrapper">
                <img width="103" height="123" src="uploads/files/impact-icon.svg" class="impact-icon-img fade-in-bottom animate" alt="" decoding="async" loading="lazy">
            </span>
            <h2 class="impact-baslik fade-in-bottom animate"><?= @$dil["txt221"] ?><mark style="background-color:rgba(0, 0, 0, 0)" class="impact-nokta">.</mark></h2>
            <p class="impact-aciklama fade-in-bottom animate"><?= @$dil["txt222"] ?></p>
        </div>

        <div class="impact-reach-sag">
            <?php foreach ($impactSonuclar as $index => $impact) {
                ?>
            <div class="impact-card-bilgi">
                <span class="card-arrow-wrapper">
                    <i style=" background: white;padding: 7px; border-radius: 34px;font-size: 35px; margin-top: -2pc; margin-left: 1pc; "  class="<?php echo $impact["ikon"]; ?> card-arrow-img fade-in-bottom animate"></i>
                </span>
                <h3 class="card-sayi fade-in-bottom animate counter" data-count="<?php echo $impact["sayi"]; ?>">0</h3>
                <p class="card-baslik fade-in-bottom animate"><?php echo $impact["baslik"]; ?></p>
                <span><?php echo $impact["aciklama"]; ?></span>
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
// YouTube video ID çıkarma fonksiyonu
function getYouTubeVideoId($url) {
    if (empty($url)) return false;
    
    // YouTube URL formatlarını kontrol et
    $patterns = [
        '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
        '/youtu\.be\/([a-zA-Z0-9_-]+)/',
        '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
        '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }
    
    return false;
}

$hasResim = !empty($ayarlar['resim']);
$hasVideo = !empty($ayarlar['video_url']);
$videoId = $hasVideo ? getYouTubeVideoId($ayarlar['video_url']) : false;

if($hasResim || $hasVideo){ ?>
<section class="impact-content-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <?php if($hasResim && $hasVideo && $videoId){ ?>
                    <!-- Resim ve Video Var - Play Butonu ile -->
                    <div class="impact-image-wrapper position-relative" style="cursor: pointer;" onclick="openVideoModal('<?php echo $videoId; ?>')">
                        <img src="<?php echo tema;?>/uploads/impact_reach/<?php echo $ayarlar['resim'];?>" alt="<?php echo htmlspecialchars($ayarlar['resim_aciklama'], ENT_QUOTES);?>">
                        <div class="video-play-overlay">
                            <div class="video-play-button">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                    </div>
                <?php } elseif($hasResim && !$hasVideo){ ?>
                    <!-- Sadece Resim Var -->
                    <div class="impact-image-wrapper">
                        <img src="<?php echo tema;?>/uploads/impact_reach/<?php echo $ayarlar['resim'];?>" alt="<?php echo htmlspecialchars($ayarlar['resim_aciklama'], ENT_QUOTES);?>">
                    </div>
                <?php } elseif($hasVideo && $videoId){ ?>
                    <!-- Sadece Video Var - Direkt Göster -->
                    <div class="impact-video-wrapper">
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo $videoId; ?>?rel=0" allowfullscreen></iframe>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-lg-6">
                <div class="impact-text-content">
                   
                        <p><?php echo $ayarlar['resim_aciklama'];?></p>
                   
                </div>
            </div>
        </div>
    </div>
</section>

<?php if($hasResim && $hasVideo && $videoId){ ?>
<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoModalLabel"><?=@$dil['txt566'];?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" id="videoFrame" src="" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openVideoModal(videoId) {
    var iframe = document.getElementById('videoFrame');
    iframe.src = 'https://www.youtube.com/embed/' + videoId + '?rel=0&autoplay=1';
    $('#videoModal').modal('show');
}

$('#videoModal').on('hidden.bs.modal', function () {
    var iframe = document.getElementById('videoFrame');
    iframe.src = '';
});
</script>

<style>
.video-play-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    transition: background 0.3s ease;
}

.impact-image-wrapper:hover .video-play-overlay {
    background: rgba(0, 0, 0, 0.5);
}

.video-play-button {
    width: 80px;
    height: 80px;
    background: rgba(255, 0, 0, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 30px;
    transition: transform 0.3s ease, background 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.video-play-button i {
    margin-left: 5px;
}

.impact-image-wrapper:hover .video-play-button {
    transform: scale(1.1);
    background: rgba(255, 0, 0, 1);
}

.impact-video-wrapper {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.impact-image-wrapper {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}
</style>
<?php } ?>
<?php } ?>

<?php if(isset($ayarlar['uzman_gorusleri_aktif']) && $ayarlar['uzman_gorusleri_aktif'] == 1){ ?>
<section class="expert-opinions-section">
    <div class="container">
        <div class="section-title-center">
            <h2><?php echo $ayarlar['ek_baslik'];?></h2>
            <?php if($ayarlar['ek_aciklama']){ ?>
            <p><?php echo $ayarlar['ek_aciklama'];?></p>
            <?php } ?>
            <?php if(!empty($ayarlar['ek_not'])){ ?>
            <span class="expert-footnote"><?php echo $ayarlar['ek_not'];?></span>
            <?php } ?>
        </div>
        <?php
        $uzmanSorgu = $db->query("SELECT * FROM uzman_gorusleri WHERE durum = 1 AND dil='{$_SESSION['k_dil']}' ORDER BY sira ASC");
        $uzmanlar = $uzmanSorgu->fetchAll(PDO::FETCH_ASSOC);
        $uzmanSayisi = count($uzmanlar);
        if($uzmanSayisi){
        ?>
        <div class="expert-slider" data-interval="6500">
            <div class="expert-carousel">
                <?php foreach($uzmanlar as $index => $uzman){ ?>
                <div class="expert-slide<?php echo $index === 0 ? ' active' : ''; ?>">
                    <div class="expert-card">
                        <?php if($uzman['resim']){ ?>
                        <img src="<?php echo tema;?>/uploads/uzmanlar/<?php echo $uzman['resim'];?>" alt="<?php echo $uzman['isim'];?>" class="expert-avatar">
                        <?php } else { ?>
                        <img src="<?php echo tema;?>/assets/img/user.png" alt="<?php echo $uzman['isim'];?>" class="expert-avatar">
                        <?php } ?>
                        <div class="expert-card-content">
                            <div class="expert-quote">
                                <div class="expert-quote-text">“<?php echo $uzman['yorum'];?>”</div>
                            </div>
                            <div class="expert-author"><?php echo $uzman['isim'];?></div>
                            <span class="expert-author-title"><?php echo $uzman['gorev'];?></span>
                            <span class="expert-quote-icon">”</span>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <?php if($uzmanSayisi > 1){ ?>
            <div class="expert-slider-controls">
                <button class="expert-slider-btn prev" type="button" aria-label="Prev">
                    <i class="far fa-chevron-left"></i>
                </button>
                <div class="expert-slider-dots"></div>
                <button class="expert-slider-btn next" type="button" aria-label="Next">
                    <i class="far fa-chevron-right"></i>
                </button>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</section>
<?php } ?>

<?php displayContactSection($page_name, $db, $dil, $sayfalink); ?>

