<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
$programSorgu = $db->prepare("SELECT * FROM programlar WHERE durum = ? AND dil = ? ORDER BY sira ASC");
$programSorgu->execute(array("1", $_SESSION['k_dil']));
$programlar = $programSorgu->fetchALL(PDO::FETCH_ASSOC);
?>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

<!-- OKULLAR LİSTE SAYFASI -->
<section class="okullar-liste-section py-5">
    <div class="container">
        <!-- Sayfa Başlığı -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="okullar-liste-baslik">
                    <?= @$dil['txt250'] ?: 'Okullar' ?>
                    <span class="dot">.</span>
                </h1>
                <p class="okullar-liste-aciklama">
                    <?= @$dil['okullar_liste_aciklama'] ?: 'Eğitim programlarımızı keşfedin ve detaylı bilgi alın.' ?>
                </p>
            </div>
        </div>

        <!-- Program Kartları -->
        <div class="row">
            <?php foreach($programlar as $index => $program){ ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <article class="okullar-program-card">
                    <a href="<?php echo $htc['programdetayurl']; ?>/<?php echo $program['seo']; ?><?php echo $html;?>" class="card-link">
                        <!-- Program Resmi -->
                        <div class="program-card-image">
                            <?php if(!empty($program['banner_resim'])){ ?>
                                <img src="<?php echo tema;?>/uploads/programlar/<?php echo $program['banner_resim']; ?>" 
                                     alt="<?php echo htmlspecialchars($program['baslik'], ENT_QUOTES, 'UTF-8'); ?>" 
                                     class="img-fluid">
                            <?php } else if(!empty($program['resim'])){ ?>
                                <img src="<?php echo tema;?>/uploads/programlar/<?php echo $program['resim']; ?>" 
                                     alt="<?php echo htmlspecialchars($program['baslik'], ENT_QUOTES, 'UTF-8'); ?>" 
                                     class="img-fluid">
                            <?php } else { ?>
                                <img src="<?php echo tema;?>/uploads/ziplayan_cocuk.png" 
                                     alt="<?php echo htmlspecialchars($program['baslik'], ENT_QUOTES, 'UTF-8'); ?>" 
                                     class="img-fluid">
                            <?php } ?>
                            <div class="program-card-overlay">
                                <span class="overlay-text">
                                    <?= @$dil['txt420'] ?: 'Detayları Gör' ?> 
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Program İçeriği -->
                        <div class="program-card-content">
                            <h3 class="program-card-title">
                                <?php 
                                $card_baslik = !empty($program['banner_baslik']) ? $program['banner_baslik'] : $program['baslik'];
                                echo htmlspecialchars($card_baslik, ENT_QUOTES, 'UTF-8'); 
                                ?>
                            </h3>
                            
                            <?php if(!empty($program['banner_aciklama'])){ ?>
                            <p class="program-card-desc">
                                <?php echo mb_strimwidth(strip_tags($program['banner_aciklama']), 0, 120, '...', 'UTF-8'); ?>
                            </p>
                            <?php } else if(!empty($program['aciklama'])){ ?>
                            <p class="program-card-desc">
                                <?php echo mb_strimwidth(strip_tags($program['aciklama']), 0, 120, '...', 'UTF-8'); ?>
                            </p>
                            <?php } ?>

                            <div class="program-card-footer">
                                <span class="program-card-btn">
                                    <?= @$dil['txt420'] ?: 'Detayları Gör' ?>
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                </article>
            </div>
            <?php } ?>
        </div>

        <?php if(empty($programlar)){ ?>
        <div class="row">
            <div class="col-12 text-center py-5">
                <p class="text-muted"><?= @$dil['okullar_liste_bos'] ?: 'Henüz program eklenmemiş.' ?></p>
            </div>
        </div>
        <?php } ?>
    </div>
</section>

<style>
:root {
    --ja-teal-start: #45dcb8;
    --ja-teal-end: #1db0c8;
    --ja-yellow: #fcee21;
    --ja-light-bg: #bcf0f6;
    --ja-dark-blue: #1d3b54;
    --ja-gray: #556b7a;
}

.okullar-liste-section {
    background: linear-gradient(to bottom right, #eefafc 0%, #fdfdfd 50%, #f0fcf4 100%);
    min-height: 60vh;
    padding-top: 100px;
}

.okullar-liste-baslik {
    font-family: 'Montserrat', sans-serif;
    font-size: 3.5rem;
    font-weight: 900;
    color: var(--ja-dark-blue);
    text-transform: uppercase;
    line-height: 1.2;
    margin-bottom: 20px;
}

.okullar-liste-baslik .dot {
    color: var(--ja-yellow);
}

.okullar-liste-aciklama {
    font-family: 'Open Sans', sans-serif;
    font-size: 1.1rem;
    color: var(--ja-gray);
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Program Kartları */
.okullar-program-card {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.okullar-program-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.okullar-program-card .card-link {
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* Resim Alanı */
.program-card-image {
    position: relative;
    width: 100%;
    height: 250px;
    overflow: hidden;
    background: linear-gradient(135deg, var(--ja-teal-start) 0%, var(--ja-teal-end) 100%);
}

.program-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.okullar-program-card:hover .program-card-image img {
    transform: scale(1.1);
}

.program-card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, transparent 0%, rgba(29, 59, 84, 0.7) 100%);
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding: 20px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.okullar-program-card:hover .program-card-overlay {
    opacity: 1;
}

.overlay-text {
    color: #ffffff;
    font-weight: 700;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* İçerik Alanı */
.program-card-content {
    padding: 25px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.program-card-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--ja-dark-blue);
    margin-bottom: 15px;
    line-height: 1.3;
    text-transform: uppercase;
    min-height: 70px;
}

.program-card-desc {
    font-family: 'Open Sans', sans-serif;
    font-size: 0.95rem;
    color: var(--ja-gray);
    line-height: 1.6;
    margin-bottom: 20px;
    flex-grow: 1;
}

.program-card-footer {
    margin-top: auto;
    padding-top: 15px;
    border-top: 2px solid #f0f0f0;
}

.program-card-btn {
    display: inline-flex;
    align-items: center;
    color: var(--ja-teal-end);
    font-weight: 700;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.okullar-program-card:hover .program-card-btn {
    color: var(--ja-teal-start);
    transform: translateX(5px);
}

/* Responsive */
@media (max-width: 991px) {
    .okullar-liste-baslik {
        font-size: 2.5rem;
    }
    
    .okullar-liste-section {
        padding-top: 80px;
    }
    
    .program-card-image {
        height: 200px;
    }
    
    .program-card-title {
        font-size: 1.3rem;
        min-height: auto;
    }
}

@media (max-width: 767px) {
    .okullar-liste-baslik {
        font-size: 2rem;
    }
    
    .okullar-liste-aciklama {
        font-size: 1rem;
        padding: 0 15px;
    }
    
    .program-card-content {
        padding: 20px;
    }
}
</style>
