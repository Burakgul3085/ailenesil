<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
$desteklemeSorgu = $db->prepare("SELECT * FROM destekleme_yollari WHERE durum = ? AND dil = ? ORDER BY sira ASC");
$desteklemeSorgu->execute(array("1", $_SESSION['k_dil']));
$desteklemeYollari = $desteklemeSorgu->fetchAll(PDO::FETCH_ASSOC);

$sayfaAyarlari = $db->query("SELECT * FROM destekleme_yollari_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
$menubul = $db->query("SELECT * FROM menu WHERE menu_url = '".$htc['destekleme_yollariurl']."' OR link = '".$htc['destekleme_yollariurl']."' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
$menubas = $db->query("SELECT * FROM menu WHERE id = '{$menubul['menu_ust']}' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
?>

<section class="ja-header-section">
    <div class="banner-background-overlay"></div>
    <div class="banner-pattern"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 col-md-6 text-center text-md-left mb-5 mb-md-0">
                <div class="header-image-wrapper">
                    <div class="image-glow"></div>
                    <div class="image-border-animation"></div>
                    <?php if(!empty($sayfaAyarlari['banner_resim'])){ ?>
                        <img src="<?php echo tema;?>/uploads/destekleme_yollari/<?php echo $sayfaAyarlari['banner_resim']; ?>" alt="<?php echo $sayfaAyarlari['banner_baslik']; ?>" class="banner-main-image">
                    <?php } else { ?>
                        <img src="https://via.placeholder.com/400x400" alt="<?=@$dil['txt417'];?>" class="banner-main-image">
                    <?php } ?>
                    <div class="geo-shape geo-shape-1"></div>
                    <div class="geo-shape geo-shape-2"></div>
                    <div class="geo-shape geo-shape-3"></div>
                </div>
            </div>

            <div class="col-lg-7 col-md-6">
                <div class="header-content-wrapper">
                    <div class="banner-badge">
                        <i class="fas fa-heart"></i>
                        <span><?=@$dil['txt273'];?></span>
                    </div>
                    <h1 class="header-title">
                        <?php echo $sayfaAyarlari['banner_baslik']; ?>
                    </h1>
                    <div class="header-desc">
                        <?php echo $sayfaAyarlari['banner_aciklama']; ?>
                    </div>
                    <div class="header-decoration-line"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ja-info-bar-section">
    <div class="container">
        <div class="info-bar">
            <h2 class="info-bar-title"><?php echo $sayfaAyarlari['sayfa_baslik_1']; ?></h2>
        </div>
        
        <div class="row mt-4 mb-5">
            <div class="col-12 text-center">
                <p class="info-bar-text">
                    <?php echo $sayfaAyarlari['sayfa_aciklama_1']; ?>
                </p>
            </div>
        </div>
    </div>
</section>

<section class="ja-cards-section mb-5">
    <div class="container">
        <?php if(!empty($desteklemeYollari)){ ?>
            <div class="row justify-content-center">
                <?php foreach($desteklemeYollari as $destekleme){ 
                    $detayUrl = $htc['destekleme_yollari_detayurl'].'/'.$destekleme['seo'].$html;
                ?>
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="ja-modern-card">
                        <div class="card-content-wrapper">
                            <a href="<?php echo $detayUrl; ?>">
                            <div class="card-icon">
                                <?php if(!empty($destekleme['resim'])) { ?>
                                    <img src="<?php echo tema;?>/uploads/destekleme_yollari/<?php echo htmlspecialchars($destekleme['resim'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($destekleme['baslik'], ENT_QUOTES); ?>" class="img-fluid">
                                <?php } elseif(!empty($destekleme['ikon'])) { ?>
                                    <i class="<?php echo $destekleme['ikon']; ?>"></i>
                                <?php } else { ?>
                                    <i class="fa fa-handshake-o"></i> 
                                <?php } ?>
                            </div>
                            </a>

                            <h3 class="card-title"><?php echo $destekleme['baslik']; ?></h3>

                            <div class="card-desc">
                                <?php echo $destekleme['kisa_aciklama']; ?>
                            </div>
                        </div>

                        <a href="<?php echo $detayUrl; ?>" class="card-btn">
                            <?php echo mb_strtoupper($destekleme['baslik']); ?>, <?=@$dil['txt418'];?>
                        </a>
                    </div>
                </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning text-center"><?=@$dil['txt139'];?></div>
        <?php } ?>
    </div>
</section>

<style>
body {
    font-family: 'Montserrat', sans-serif;
    color: #555;
}

/* Modern Banner Section */
.ja-header-section {
    position: relative;
    padding: 100px 0 120px;
    background: linear-gradient(135deg, #1C4E5F 0%, #2a6b7f 50%, #3BBCA8 100%);
    overflow: hidden;
    min-height: 500px;
    display: flex;
    align-items: center;
}

.banner-background-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 50%, rgba(59, 188, 168, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(28, 78, 95, 0.4) 0%, transparent 50%);
    z-index: 1;
}

.banner-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.05) 35px, rgba(255,255,255,.05) 70px);
    z-index: 1;
    opacity: 0.3;
}

.ja-header-section .container {
    position: relative;
    z-index: 2;
}

/* Image Wrapper */
.header-image-wrapper {
    position: relative;
    display: inline-block;
    z-index: 2;
    animation: fadeInUp 0.8s ease-out;
}

.banner-main-image {
    width: 320px;
    height: 320px;
    border-radius: 50%;
    object-fit: cover;
    border: 8px solid rgba(255, 255, 255, 0.3);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.3),
        0 0 0 20px rgba(255, 255, 255, 0.1),
        inset 0 0 40px rgba(255, 255, 255, 0.2);
    position: relative;
    z-index: 3;
    transition: transform 0.3s ease;
}

.banner-main-image:hover {
    transform: scale(1.05);
}

.image-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 360px;
    height: 360px;
    background: radial-gradient(circle, rgba(59, 188, 168, 0.4) 0%, transparent 70%);
    border-radius: 50%;
    z-index: 1;
    animation: pulse 3s ease-in-out infinite;
}

.image-border-animation {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 340px;
    height: 340px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    z-index: 2;
    animation: rotate 20s linear infinite;
}

.image-border-animation::before {
    content: '';
    position: absolute;
    top: -5px;
    left: 50%;
    width: 10px;
    height: 10px;
    background: #3BBCA8;
    border-radius: 50%;
    box-shadow: 0 0 20px #3BBCA8;
}

/* Geometric Shapes */
.geo-shape {
    position: absolute;
    border-radius: 50%;
    z-index: 1;
    opacity: 0.6;
    animation: float 6s ease-in-out infinite;
}

.geo-shape-1 {
    top: -40px;
    right: -50px;
    width: 180px;
    height: 180px;
    background: linear-gradient(135deg, rgba(59, 188, 168, 0.4), rgba(28, 78, 95, 0.3));
    animation-delay: 0s;
}

.geo-shape-2 {
    bottom: -30px;
    left: -40px;
    width: 140px;
    height: 140px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(59, 188, 168, 0.3));
    animation-delay: 2s;
}

.geo-shape-3 {
    top: 50%;
    right: -80px;
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.15);
    animation-delay: 4s;
}

/* Content Wrapper */
.header-content-wrapper {
    animation: fadeInRight 0.8s ease-out 0.2s both;
}

.banner-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 10px 20px;
    border-radius: 50px;
    margin-bottom: 25px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #fff;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.banner-badge i {
    color: #3BBCA8;
    font-size: 1.1rem;
}

.header-title {
    font-size: 3.2rem;
    font-weight: 900;
    color: #ffffff !important;
    text-transform: uppercase;
    line-height: 1.2;
    margin-bottom: 25px;
    text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
    letter-spacing: -1px;
}

.header-desc {
    font-size: 1.25rem;
    line-height: 1.8;
    color: rgba(255, 255, 255, 0.95);
    margin-bottom: 30px;
    text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.1);
}

.header-decoration-line {
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #3BBCA8, rgba(59, 188, 168, 0.3));
    border-radius: 2px;
    margin-top: 20px;
}

/* Wave Bottom */
.banner-wave-bottom {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 120px;
    z-index: 2;
    overflow: hidden;
}

.banner-wave-bottom svg {
    width: 100%;
    height: 100%;
    display: block;
}

/* Animations */
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

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.4;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.1);
        opacity: 0.6;
    }
}

@keyframes rotate {
    from {
        transform: translate(-50%, -50%) rotate(0deg);
    }
    to {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) translateX(0);
    }
    33% {
        transform: translateY(-20px) translateX(10px);
    }
    66% {
        transform: translateY(10px) translateX(-10px);
    }
}

.ja-info-bar-section {
    margin-bottom: 40px;
    margin-top: 20px;
}

.info-bar {
    background-color: #1C4E5F;
    padding: 20px 0;
    width: 100%;
    text-align: center;
}

.info-bar-title {
    color: #fff !important;
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.info-bar-text {
    font-size: 0.95rem;
    color: #777;
    line-height: 1.6;
    max-width: 900px;
    margin: 0 auto;
}

.ja-modern-card {
    background-color: #F2F4F5;
    border-radius: 4px;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.ja-modern-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.card-content-wrapper {
    padding: 40px 30px;
    flex-grow: 1;
}

.card-icon {
    font-size: 3.5rem;
    color: #3BBCA8;
    margin-bottom: 20px;
}

.card-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
}

.card-desc {
    font-size: 0.95rem;
    color: #666;
    line-height: 1.6;
    margin-bottom: 10px;
}

.card-btn {
    display: block;
    background-color: #1C4E5F;
    color: #fff;
    padding: 20px 10px;
    text-decoration: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.9rem;
    transition: background-color 0.3s ease;
}

.card-btn:hover {
    background-color: #143a47;
    color: #fff;
    text-decoration: none;
}
/* Responsive Design */
@media (max-width: 992px) {
    .ja-header-section {
        padding: 80px 0 100px;
        min-height: auto;
    }
    
    .banner-main-image {
        width: 280px;
        height: 280px;
    }
    
    .image-glow {
        width: 320px;
        height: 320px;
    }
    
    .image-border-animation {
        width: 300px;
        height: 300px;
    }
    
    .header-title {
        font-size: 2.5rem;
    }
}

@media (max-width: 768px) {
    .ja-header-section {
        padding: 60px 0 80px;
    }
    
    .header-title {
        font-size: 2rem;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .header-desc {
        font-size: 1.1rem;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .header-content-wrapper {
        text-align: center;
    }
    
    .header-decoration-line {
        margin: 20px auto 0;
    }
    
    .banner-badge {
        margin: 0 auto 25px;
    }
    
    .header-image-wrapper {
        margin-bottom: 30px;
    }
    
    .banner-main-image {
        width: 240px;
        height: 240px;
    }
    
    .image-glow {
        width: 280px;
        height: 280px;
    }
    
    .image-border-animation {
        width: 260px;
        height: 260px;
    }
    
    .geo-shape-1 {
        width: 120px;
        height: 120px;
        top: -20px;
        right: -30px;
    }
    
    .geo-shape-2 {
        width: 100px;
        height: 100px;
        bottom: -20px;
        left: -20px;
    }
    
    .geo-shape-3 {
        width: 80px;
        height: 80px;
        right: -40px;
    }
    
    .info-bar-title {
        font-size: 1.2rem;
    }
    
    .banner-wave-bottom {
        height: 80px;
    }
}

@media (max-width: 576px) {
    .header-title {
        font-size: 1.75rem;
    }
    
    .header-desc {
        font-size: 1rem;
    }
    
    .banner-main-image {
        width: 200px;
        height: 200px;
    }
    
    .image-glow {
        width: 240px;
        height: 240px;
    }
    
    .image-border-animation {
        width: 220px;
        height: 220px;
    }
}
</style>

