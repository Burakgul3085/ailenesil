<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
$ogrenmeSorgu = $db->prepare("SELECT od.*, 
                               p.baslik as program_baslik, 
                               p.aciklama as program_aciklama,
                               p.resim as program_resim,
                               p.seo as program_seo
                               FROM ogrenme_deneyimi od 
                               LEFT JOIN programlar p ON od.program_id = p.id 
                               WHERE od.durum = ? AND od.dil = ? 
                               ORDER BY od.sira ASC");
$ogrenmeSorgu->execute(array("1", $_SESSION['k_dil']));
$ogrenmeDeneyimleri = $ogrenmeSorgu->fetchAll(PDO::FETCH_ASSOC);

$sayfaAyarlari = $db->query("SELECT * FROM ogrenme_deneyimi_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
$menubul = $db->query("SELECT * FROM menu WHERE menu_url = '".$htc['ogrenme_deneyimiurl']."' OR link = '".$htc['ogrenme_deneyimiurl']."' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
$menubas = $db->query("SELECT * FROM menu WHERE id = '{$menubul['menu_ust']}' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
?>
<section class="page-section ogrenme-deneyimi-section">
    <div class="bg-white">
        <div class="col-12 p-0 banner main-banner">
            <?php if(!empty($sayfaAyarlari['banner_resim'])){ ?>
                <img src="<?php echo tema;?>/uploads/ogrenme_deneyimi/<?php echo $sayfaAyarlari['banner_resim']; ?>" alt="<?php echo $sayfaAyarlari['banner_baslik']; ?>" class="banner-image">
            <?php } else { ?>
                <img src="image_1.png" alt="<?=@$dil['txt270'];?>" class="banner-image">
            <?php } ?>
            <div class="slide-overlay"></div>
            <div class="banner-content">
                <div class="container">
                    <h1 class="banner-title"><?php echo $sayfaAyarlari['banner_baslik']; ?>.</h1>
                </div>
            </div>
        </div>
        <div class="container banner-fix">
            <div class="row">
                <div class="col-lg-<?php echo($moduller['alan23'] == "1" ? '9 offset-lg-3' : '12');?> col-md-<?php echo($moduller['alan23'] == "1" ? '7 offset-md-5' : '12');?> z-index-9">
                    </div>
                </div>
        </div>
    </div>
</section>
<br/>
<section class="ja-page-wrapper">
    <div class="container">
        


        <div class="row">
            <div class="col-12">
                


                    <h2 class="ja-subtitle"><?php echo $sayfaAyarlari['sayfa_baslik_1']; ?></h2>
                    <p><?php echo $sayfaAyarlari['sayfa_aciklama_1']; ?></p>

                    <?php if(!empty($ogrenmeDeneyimleri)){ ?>
                    <div class="ja-grid">
                        <?php foreach($ogrenmeDeneyimleri as $deneyim){ 
                            $cardBaslik = !empty($deneyim['program_id']) && !empty($deneyim['program_baslik']) ? $deneyim['program_baslik'] : $deneyim['baslik'];
                            $cardResim = '';
                            $detayUrl = '';
                            
                            if(!empty($deneyim['program_id']) && !empty($deneyim['program_resim'])) {
                                $cardResim = tema.'/uploads/programlar/'.$deneyim['program_resim'];
                                $detayUrl = $htc['programdetayurl'].'/'.$deneyim['program_seo'].$html;
                            } elseif(!empty($deneyim['program_id']) && !empty($deneyim['program_seo'])) {
                                $cardResim = !empty($deneyim['resim']) ? tema.'/uploads/ogrenme_deneyimi/'.$deneyim['resim'] : '';
                                $detayUrl = $htc['programdetayurl'].'/'.$deneyim['program_seo'].$html;
                            } else {
                                $cardResim = !empty($deneyim['resim']) ? tema.'/uploads/ogrenme_deneyimi/'.$deneyim['resim'] : '';
                                $detayUrl = $htc['ogrenme_deneyimi_detayurl'].'/'.$deneyim['seo'].$html;
                            }
                        ?>
                        
                        <a href="<?php echo $detayUrl; ?>" class="ja-card">
                            <div class="ja-card-img">
                                <?php if(!empty($cardResim)){ ?>
                                    <img src="<?php echo $cardResim; ?>" alt="<?php echo htmlspecialchars($cardBaslik, ENT_QUOTES); ?>">
                                <?php } else { ?>
                                    <img src="https://via.placeholder.com/400x600" alt="<?=@$dil['txt417'];?>">
                                <?php } ?>
                                <div class="ja-card-overlay"></div>
                            </div>
                            <div class="ja-card-content">
                                <h3 class="ja-card-title"><?php echo htmlspecialchars($cardBaslik, ENT_QUOTES); ?></h3>
                                <span class="ja-btn-text">
                                    <span class="line"></span> <?=@$dil['txt271'];?>
                                </span>
                            </div>
                        </a>
                        <?php } ?>
                    </div>
                    <?php } else { ?>
                        <div class="alert alert-warning"><?=@$dil['txt139'];?></div>
                    <?php } ?>
                </div>

                <div class="ja-standards-section mt-5 mb-5">
                    <h2 class="ja-subtitle"><?php echo $sayfaAyarlari['sayfa_baslik_2']; ?></h2>
                    <p><?php echo $sayfaAyarlari['sayfa_aciklama_2']; ?></p>
                    

                </div>


            </div>
        </div>
    </div>
</section>

<style>
.main-banner {
    position: relative;
    height: 400px; /* Banner yüksekliği, ihtiyaca göre ayarlayabilirsiniz */
    overflow: hidden;
}

.banner-image {
    width: 100%;
    height: 100% !important;
    object-fit: cover; /* Görselin alanı doldurmasını sağlar */
    object-position: center;
}

.banner-content {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center; /* Dikey ortalama */
    z-index: 2; /* Overlay'in üzerinde olması için */
}

.banner-title {
    color: #fff !important;
    font-size: 3.5rem; /* Yazı boyutu */
    font-weight: 800; /* Kalınlık */
    text-transform: uppercase;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5); /* Okunabilirliği artırmak için gölge */
    margin: 0;
}

/* Mevcut banner stillerine küçük bir düzeltme */
.banner .slide-overlay {
    z-index: 1; /* İçeriğin altında kalması için */
    background: rgba(0,0,0,0.3); /* İsteğe bağlı: Görseli biraz karartmak için */
}

/* Mobil uyumluluk için */
@media (max-width: 768px) {
    .main-banner {
        height: 250px;
    }
    .banner-title {
        font-size: 2rem;
    }
}

.ja-page-wrapper {
    font-family: 'Montserrat', 'Segoe UI', sans-serif; /* Font yoksa sistem fontu */
    color: #5a6e7a; /* Resimdeki gri metin rengi */
    padding-top: 40px;
    background-color: #fff;
}

/* Breadcrumb Temizliği */
.breadcrumb-clean {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    gap: 10px;
    font-size: 0.9rem;
}
.breadcrumb-clean a { color: #888; text-decoration: none; }
.breadcrumb-clean .active { color: #333; font-weight: 600; }

/* BAŞLIKLAR (Typography) */
.ja-big-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #325C6A; /* Resimdeki koyu petrol mavisi/yeşili */
    text-transform: uppercase;
    margin-bottom: 20px;
    line-height: 1.1;
}

.ja-subtitle {
    font-size: 1.6rem;
    font-weight: 700;
    color: #325C6A;
    text-transform: uppercase;
    margin-bottom: 15px;
    margin-top: 0;
}

/* METİN BLOKLARI */
.ja-text-section {
    margin-bottom: 40px;
}
.ja-text-section p, .ja-text-content {
    font-size: 1rem;
    line-height: 1.6;
    color: #6d7d85;
}

/* GRID YAPISI (Resimdeki 3'lü yapı) */
.ja-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* 3 Kolon */
    gap: 25px;
}

/* KART TASARIMI (Özel CSS) */
.ja-card {
    position: relative;
    display: block;
    height: 450px; /* Resimdeki gibi dikey kartlar */
    border-radius: 8px;
    overflow: hidden;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.ja-card:hover {
    transform: translateY(-5px);
}

.ja-card-img {
    width: 100%;
    height: 100%;
    position: relative;
}

.ja-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Karartma Efekti */
.ja-card-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0) 50%, rgba(0,0,0,0.7) 100%);
}

/* Kart İçeriği (Alt Sol Köşe) */
.ja-card-content {
    position: absolute;
    bottom: 30px;
    left: 30px;
    right: 20px;
    z-index: 2;
    color: #fff;
}

.ja-card-title {
    font-size: 1.8rem;
    font-weight: 800;
    text-transform: uppercase;
    margin: 0 0 10px 0;
    color: #fff;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

/* Buton Görünümü (Metin + Çizgi) */
.ja-btn-text {
    font-size: 0.95rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ja-btn-text .line {
    display: inline-block;
    width: 2px;
    height: 16px;
    background-color: #fff; /* Sol taraftaki beyaz dikey çizgi */
}

/* STANDARTLAR LİSTESİ */
.ja-links-list {
    list-style: none;
    padding: 0;
    margin-top: 20px;
}
.ja-links-list li {
    margin-bottom: 10px;
    font-weight: 600;
    color: #325C6A;
}
.ja-links-list a {
    color: #325C6A;
    text-decoration: underline;
}
.ja-links-list ul {
    list-style: disc;
    margin-left: 20px;
    margin-top: 5px;
    color: #325C6A;
}

/* MOBİL UYUMLULUK */
@media (max-width: 992px) {
    .ja-grid {
        grid-template-columns: repeat(2, 1fr); /* Tablette 2'li */
    }
}

@media (max-width: 768px) {
    .ja-big-title { font-size: 2rem; }
    .ja-subtitle { font-size: 1.4rem; }
    
    .ja-grid {
        grid-template-columns: 1fr; /* Mobilde tekli */
    }
    .ja-card {
        height: 350px; /* Mobilde boyu biraz kısalt */
    }
}
</style>

