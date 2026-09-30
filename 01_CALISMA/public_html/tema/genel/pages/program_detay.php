<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
// SEO parametresini al
$seo = isset($_GET['seo']) ? $_GET['seo'] : '';
if(empty($seo)) {
    // URL'den SEO'yu çıkar (örn: /program-detay/duygu-dunyam)
    $request_uri = $_SERVER['REQUEST_URI'];
    $seo = basename(parse_url($request_uri, PHP_URL_PATH));
}

$Sorgu = $db->prepare("SELECT * FROM programlar WHERE seo = ? AND durum = ? AND dil = ?");
$Sorgu->execute(array($seo, "1", $_SESSION['k_dil']));
if($Sorgu->rowCount())
{
	$program = $Sorgu->fetch(PDO::FETCH_ASSOC);
}
else
{
	header("Location:".$url."/404.html");
	exit;
}

// Program içeriklerini kontrol et (bir kez sorgula, her yerde kullan)
$gosterilecekIcerikler = array();
try {
    $iceriklerSorgu = $db->prepare("SELECT * FROM program_icerikleri WHERE program_id = ? AND durum = 1 AND dil = ? ORDER BY sira ASC");
    $iceriklerSorgu->execute(array($program['id'], $_SESSION['k_dil']));
    $icerikler = $iceriklerSorgu->fetchALL(PDO::FETCH_ASSOC);
    
    // İçerik var mı ve en az bir içerik bloğu dolu mu kontrol et
    if(count($icerikler) > 0){
        foreach($icerikler as $icerik){
            // İçerik bloğu en az bir alan dolu mu kontrol et
            if(!empty($icerik['baslik']) || !empty($icerik['aciklama']) || !empty($icerik['resim']) || !empty($icerik['video_url']) || (!empty($icerik['icerik_tipi']) && (!empty($icerik['gorsel_desktop']) || !empty($icerik['gorsel_mobil'])))){
                $gosterilecekIcerikler[] = $icerik;
            }
        }
    }
} catch(Exception $e) {
    // Tablo yoksa veya hata varsa boş array döndür
    $icerikler = array();
}

// Programa özel derslik programlarını kontrol et (bir kez sorgula, her yerde kullan)
$derslikler = array();
try {
    // dil alanı integer ise (dil ID'si), string ise (dil kodu) her iki durumu da destekle
    $dilDegeri = is_numeric($_SESSION['k_dil']) ? intval($_SESSION['k_dil']) : $_SESSION['k_dil'];
    $dersliklerSorgu = $db->prepare("SELECT * FROM derslik_durumlari WHERE program_id = ? AND aktif = 1 AND dil = ? ORDER BY FIELD(gun,'Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi','Pazar'), saat ASC");
    $dersliklerSorgu->execute(array($program['id'], $dilDegeri));
    $derslikler = $dersliklerSorgu->fetchALL(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    // Tablo yoksa veya hata varsa boş array döndür
    $derslikler = array();
}

$menubul = $db->query("SELECT * FROM menu WHERE menu_url = '".$htc['okullarurl']."' OR link = '".$htc['okullarurl']."' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
$menubas = $db->query("SELECT * FROM menu WHERE id = '{$menubul['menu_ust']}' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
?>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800;900&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

<div class="ja-program-wrapper">

    <!-- HERO SECTION -->
    <section class="ja-hero-section">
        <?php if(empty($program['banner_resim']) || !file_exists(tema.'/uploads/programlar/'.$program['banner_resim'])): ?>
        <div class="container h-100 mobil-yukseklik">
            <div class="row h-100 align-items-center">
                <div class="col-md-6 position-relative z-2">
                    <h1 style="color:<?php echo $program['banner_baslik_renk']; ?> !important;" class="ja-hero-title">
                        <?php
                        $hero_baslik = !empty($program['banner_baslik']) ? $program['banner_baslik'] : $program['baslik'];
                        echo htmlspecialchars($hero_baslik, ENT_QUOTES, 'UTF-8');
                        ?><span class="dot">.</span>
                    </h1>
                </div>
                <div class="col-md-6 position-relative z-2">
                    <div style="color:<?php echo $program['banner_aciklama_renk']; ?> !important;" class="ja-intro-text ja-hero-text">
                        <?php echo $program['aciklama'] ?: $program['aciklama']; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </section>

    <div class="ja-yellow-bar"></div>

</div>

<!-- DETAY İÇERİK SECTION -->
<section class="ja-programs-wrapper">
    <div class="container">
        <div class="row">
            
            <!-- Sidebar Menü (Program İçerikleri) -->
            <div class="col-lg-4 col-md-4 col-12">
                <div class="ja-sidebar-menu" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a style="display:none;" href="<?php echo $htc['okullarurl']; ?><?php echo $html;?>" class="ja-menu-item">
                        <span class="menu-text">
                            <i class="fas fa-arrow-left mr-2"></i>
                            <?= @$dil['txt250'] ?: 'Tüm Programlar' ?>
                        </span>
                    </a>
                    
                    <?php 
                    if(count($gosterilecekIcerikler) > 0){
                        foreach($gosterilecekIcerikler as $index => $icerik){
                            $baslik = !empty($icerik['baslik']) ? htmlspecialchars($icerik['baslik'], ENT_QUOTES, 'UTF-8') : 'İçerik #'.($index + 1);
                            $isFirst = ($index === 0);
                    ?>
                    <a class="ja-menu-item icerik-menu-item <?php echo $isFirst ? 'active' : ''; ?>" 
                       href="#icerik-<?php echo $icerik['id']; ?>" 
                       role="tab" 
                       aria-controls="icerik-<?php echo $icerik['id']; ?>"
                       aria-selected="<?php echo $isFirst ? 'true' : 'false'; ?>">
                        <span class="menu-text"><?php echo $baslik; ?></span>
                    </a>
                    <?php 
                        }
                    }
                    ?>
                </div>
            </div>

<div class="col-lg-8 col-md-8 col-12 pl-lg-5">
    <div class="ja-content-wrapper">
        <div class="ja-actions mb-4">
            <div class="d-flex flex-wrap gap-2" style="gap: 10px;">
                <button type="button" class="btn-ja-teal program-talep-btn flex-fill" 
                        data-program-id="<?php echo $program['id']; ?>" 
                        data-program-baslik="<?php echo htmlspecialchars($program['baslik'], ENT_QUOTES, 'UTF-8'); ?>"
                        data-toggle="modal" 
                        data-target="#programTalepModal">
                    <?=@$dil['txt252'] ?: 'Program Talep Et'?>
                </button>
            </div>
        </div>

        <div class="tab-content ja-tab-content" id="v-pills-tabContent">
<?php 
            if(count($gosterilecekIcerikler) > 0){
                foreach($gosterilecekIcerikler as $index => $icerik){
                    $isFirst = ($index === 0);
            ?>
            <div class="tab-pane fade <?php echo $isFirst ? 'show active' : ''; ?>" id="icerik-<?php echo $icerik['id']; ?>" role="tabpanel">
                <div class="ja-icerik-blok">
                    <?php if(!empty($icerik['icerik_tipi']) && $icerik['icerik_tipi'] == 1): ?>
                    <!-- Tek Görsel Modu -->
                    <div class="ja-icerik-gorsel-tek">
                        <picture>
                            <?php if(!empty($icerik['gorsel_desktop'])): ?>
                            <source media="(min-width: 768px)" srcset="<?php echo tema.'/uploads/programlar/'.$icerik['gorsel_desktop']; ?>">
                            <?php endif; ?>
                            <?php
                            $fallback = !empty($icerik['gorsel_mobil']) ? $icerik['gorsel_mobil'] : ($icerik['gorsel_desktop'] ?? '');
                            ?>
                            <img src="<?php echo tema.'/uploads/programlar/'.$fallback; ?>" alt="<?php echo htmlspecialchars($icerik['baslik'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="ja-gorsel-tek-img">
                        </picture>
                    </div>
                    <?php else: ?>
                    <!-- Normal İçerik Modu -->
                    <?php if(!empty($icerik['baslik'])){ ?>
                    <h3 class="ja-icerik-baslik"><?php echo htmlspecialchars($icerik['baslik'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <?php } ?>

                    <div class="ja-icerik-icerik">
                        <?php if(!empty($icerik['aciklama'])){ ?>
                        <div class="ja-icerik-aciklama">
                            <?php echo $icerik['aciklama']; ?>
                        </div>
                        <?php } ?>
                        
<div class="ja-icerik-medya">
    <?php 
    $resimler = !empty($icerik['resim']) ? array_filter(explode(',', $icerik['resim'])) : [];
    $resim_sayisi = count($resimler);
    $video_url = $icerik['video_url'] ?? null;
    $has_video = !empty($video_url);
    $video_baslik = "Tanıtım Videosu"; // Varsayılan başlık

    if($has_video) {
        // YouTube ID Ayıklama
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|shorts)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match);
        $youtube_id = $match[1] ?? null;
        $thumbnail_url = $youtube_id ? "https://img.youtube.com/vi/$youtube_id/maxresdefault.jpg" : "varsayilan.jpg";

        // Video Başlığını Çekme (Basit Scraper)
        if($youtube_id){
            $tags = @get_meta_tags("https://www.youtube.com/watch?v=$youtube_id");
            if(!empty($tags['title'])) {
                $video_baslik = $tags['title'];
            }
        }
    }

    $gorsel_fmt = isset($icerik['gorsel_format']) ? intval($icerik['gorsel_format']) : 0;
    $img_style = ($gorsel_fmt == 2) ? 'width: 100%; height: auto; object-fit: contain; border-radius: 12px;' : (($gorsel_fmt == 1) ? 'width: 100%; aspect-ratio: 16/9; object-fit: cover; border-radius: 12px;' : 'width: 100%; aspect-ratio: 1/1; object-fit: cover; border-radius: 12px;');
    if ($resim_sayisi == 1 && $has_video) { ?>
        <div class="row g-3 mb-4">
            <div class="col-6">
                <?php $resim_yolu = tema . "/uploads/programlar/" . trim($resimler[0]); ?>
                <a href="<?php echo $resim_yolu; ?>" data-fancybox="gallery-main" class="d-block">
                    <img src="<?php echo $resim_yolu; ?>" alt="Görsel" style="<?php echo $img_style; ?>">
                </a>
            </div>
            <div class="col-6">
                <a href="<?php echo htmlspecialchars($video_url); ?>" target="_blank" class="video-card-wrapper h-100" style="aspect-ratio: 1/1;">
                    <img src="<?php echo $thumbnail_url; ?>" alt="Video" class="video-thumb">
                    <div class="video-overlay-modern">
                        <div class="play-btn-circle small-play">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                    <div class="video-info-overlay">
                        <span class="v-title"><?php echo htmlspecialchars($video_baslik); ?></span>
                    </div>
                </a>
            </div>
        </div>

    <?php } else { 
        // TASARIM B: STANDART DÜZEN (Resimler üstte, Video altta tam genişlik)
        if($resim_sayisi > 0){ ?>
            <div class="row g-3 mb-4">
                <?php foreach($resimler as $r){ 
                    $resim_yolu = tema . "/uploads/programlar/" . trim($r);
                ?>
                    <div class="col-6">
                        <a href="<?php echo $resim_yolu; ?>" data-fancybox="gallery-<?php echo $icerik['id']; ?>" class="d-block">
                            <img src="<?php echo $resim_yolu; ?>" alt="Galeri" style="<?php echo $img_style; ?> border-radius: 8px;">
                        </a>
                    </div>
                <?php } ?>
            </div>
        <?php } 

        if($has_video){ ?>
            <div class="ja-video-container mb-4">
                <a href="<?php echo htmlspecialchars($video_url); ?>" target="_blank" class="video-card-wrapper">
                    <img src="<?php echo $thumbnail_url; ?>" alt="Video" class="video-thumb">
                    <div class="video-overlay-modern">
                        <div class="play-btn-circle">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                    <div class="video-info-overlay">
                        <span class="v-badge"><i class="fab fa-youtube"></i> YouTube</span>
                        <span class="v-title"><?php echo htmlspecialchars($video_baslik); ?></span>
                    </div>
                </a>
            </div>
        <?php }
    } ?>
</div>

<style>
    .video-card-wrapper {
        position: relative;
        display: block;
        width: 100%;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        border-radius: 12px;
        background: #000;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        transition: transform 0.3s ease;
    }
    .video-card-wrapper:hover { transform: translateY(-4px); }
    .video-thumb { width: 100%; height: 100%; object-fit: cover; opacity: 0.85; transition: 0.5s ease; }
    .video-card-wrapper:hover .video-thumb { scale: 1.08; opacity: 1; }
    
    .video-overlay-modern {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .play-btn-circle {
        width: 65px; height: 65px;
        background: #ff0000; color: #fff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
        box-shadow: 0 0 25px rgba(255,0,0,0.5);
        transition: 0.3s ease;
    }
    .small-play { width: 50px; height: 50px; font-size: 18px; }
    .video-card-wrapper:hover .play-btn-circle { background: #fff; color: #ff0000; transform: scale(1.1); }

    .video-info-overlay {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        padding: 40px 15px 15px;
        background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0) 100%);
        z-index: 3;
    }
    .v-badge {
        display: inline-block;
        background: #ff0000; color: #fff;
        font-size: 10px; padding: 2px 8px;
        border-radius: 4px; text-transform: uppercase;
        margin-bottom: 5px; font-weight: 700;
    }
    .v-title {
        display: block;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
                }
            }
            ?>


        </div>
        
</div>


        </div>
                </div> <?php if(count($derslikler) > 0): ?>
        <div class="ders-programi-container mt-5 pt-4 border-top">
            <h3 class="ja-content-title mb-4">
                <i class="fas fa-calendar-alt mr-2"></i> Ders Programı
            </h3>
            
            <div class="ders-grid-wrapper">
                <?php foreach($derslikler as $row): 
                    // Durum Mantığı
                    $durum = isset($row['durum']) ? (int)$row['durum'] : 0; // 0:Müsait, 1:Dolu, 2:Yedek
                    $kontenjan = (int)$row['kontenjan'];
                    $katilimci = (int)$row['katilimci'];
                    
                    // Otomatik Doluluk Kontrolü
                    $is_full = ($durum === 1) || ($kontenjan > 0 && ($kontenjan - $katilimci) <= 0);
                    
                    // CSS Sınıfı Belirle
                    if($is_full) {
                        $cardClass = 'full';
                        $statusText = 'DOLU';
                    } elseif($durum === 2) {
                        $cardClass = 'wait';
                        $statusText = 'YEDEK LİSTE';
                    } else {
                        $cardClass = 'avail';
                        $statusText = 'MÜSAİT';
                    }
                ?>
                
                <div class="ws-card <?=$cardClass?>">
                    <span class="ws-badge"><?=$statusText?></span>

                    <div>
                        <div class="ws-time">
                            <i class="far fa-clock"></i> <?=$row['gun']?> <?=$row['saat']?>
                        </div>
                        <h4 class="ws-title">
                            <?=$row['adi'] ?: $row['sinif']?>
                        </h4>
                    </div>

                    <div>
                        <div class="ws-info-grid">
                            <?php if(!empty($row['sehir'])): ?>
                            <div class="ws-info-item">
                                <span>ŞEHİR</span>
                                <strong><?=$row['sehir']?></strong>
                            </div>
                            <?php endif; ?>

                            <div class="ws-info-item">
                                <span>KONTENJAN</span>
                                <strong><?=$katilimci?> / <?=$kontenjan?></strong>
                            </div>
                            
                            <?php if(isset($row['aile_katilim'])): ?>
                            <div class="ws-info-item">
                                <span>AİLE</span>
                                <strong><?=($row['aile_katilim']==1 ? 'EVET' : 'HAYIR')?></strong>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if(!$is_full): ?>
                            <a href="#" class="ws-btn program-talep-btn" data-toggle="modal" data-target="#programTalepModal" data-program-id="<?php echo $program['id']; ?>" data-program-baslik="<?php echo htmlspecialchars($program['baslik'], ENT_QUOTES, 'UTF-8'); ?>">
                                HEMEN KAYIT OL
                            </a>
                        <?php else: ?>
                            <span class="ws-btn ws-btn-disabled">KONTENJAN DOLU</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if(function_exists('displayContactSection')) displayContactSection($page_name, $db, $dil, $sayfalink); ?>
        
    </div>
    
</section>

<!-- Program Talep Modal -->
<div class="modal fade" id="programTalepModal" tabindex="-1" role="dialog" aria-labelledby="programTalepModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content ja-modal-content">
            <div class="modal-header ja-modal-header">
                <div class="modal-header-content">
                    <h4 class="modal-title" id="programTalepModalLabel">
                        <i style=" color: white; " class="fas fa-graduation-cap"></i>
                        <span style=" color: white; " id="modalProgramTitle"><?=@$dil['txt421'] ?: 'Program Talep Formu'?></span>
                    </h4>
                    <p style=" color: white; " class="modal-subtitle"><?=@$dil['txt422'] ?: 'Program hakkında detaylı bilgi almak için formu doldurun'?></p>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body ja-modal-body">
                <form id="programTalepForm" method="POST" action="_class/site_islem.php">
                    <input type="hidden" name="islem" value="program_talep">
                    <input type="hidden" name="program_id" id="modalProgramId">
                    <input type="hidden" name="program_baslik" id="modalProgramBaslik">
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="talep_ad_soyad">
                                <?=@$dil['txt254'] ?: 'Ad Soyad'?> <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control ja-form-control" id="talep_ad_soyad" name="ad_soyad" placeholder="<?=@$dil['txt254'] ?: 'Ad Soyad'?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="talep_okul_isletme">
                                <?=@$dil['txt255'] ?: 'Okul/İşletme'?>
                            </label>
                            <input type="text" class="form-control ja-form-control" id="talep_okul_isletme" name="okul_isletme" placeholder="<?=@$dil['txt255'] ?: 'Okul/İşletme'?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="talep_email">
                                <?=@$dil['txt256'] ?: 'E-posta'?> <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control ja-form-control" id="talep_email" name="email" placeholder="<?=@$dil['txt256'] ?: 'E-posta'?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="talep_telefon">
                                <?=@$dil['txt257'] ?: 'Telefon'?>
                            </label>
                            <input type="tel" class="form-control ja-form-control" id="talep_telefon" name="telefon" placeholder="<?=@$dil['txt257'] ?: 'Telefon'?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="talep_konum">
                            <?=@$dil['txt258'] ?: 'Konum'?> <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control ja-form-control" id="talep_konum" name="konum" placeholder="<?=@$dil['txt258'] ?: 'Konum'?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="talep_mesaj">
                            <?=@$dil['txt259'] ?: 'Mesaj'?>
                        </label>
                        <textarea class="form-control ja-form-control" id="talep_mesaj" name="mesaj" rows="4" placeholder="<?=@$dil['txt259'] ?: 'Mesaj'?>"></textarea>
                    </div>
                    
                    <div class="form-group mb-0">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="talep_kvkk" required>
                            <label class="custom-control-label" for="talep_kvkk">
                                <?=@$dil['txt260'] ?: 'KVKK metnini okudum ve kabul ediyorum'?> <span class="text-danger">*</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="modal-footer ja-modal-footer">
                        <button type="button" class="btn btn-secondary ja-btn-cancel" data-dismiss="modal">
                            <i class="fas fa-times"></i> <?=@$dil['txt261'] ?: 'İptal'?>
                        </button>
                        <button type="submit" class="btn btn-primary ja-btn-submit">
                            <i class="fas fa-paper-plane"></i> <?=@$dil['txt262'] ?: 'Gönder'?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<style>
/* Kart Tasarımı CSS */
.ders-grid-wrapper {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); /* Responsive Grid */
    gap: 20px;
    margin-top: 40px;
}

.ws-card {
    border-radius: 16px;
    padding: 20px;
    min-height: 220px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    color: #fff;
    position: relative;
    overflow: hidden;
}
/* Grid Konteyner */
.gallery-grid-4 {
    display: grid;
    /* Masaüstünde 4 kolon, mobilde sığdığı kadar */
    grid-template-columns: repeat(4, 1fr); 
    gap: 15px; /* Resimler arası boşluk */
}

/* Mobilde otomatik 2'ye düşmesi için responsive ayar */
@media (max-width: 768px) {
    .gallery-grid-4 {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Resim Kutusu */
.gallery-item {
    position: relative;
    overflow: hidden;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    aspect-ratio: 16/9; /* Tüm kutuları aynı oranda tutar (4:3 veya 1:1 de yapabilirsin) */
}

.ja-video-container {
    max-width: 100%;
    width: 100%;
}

.video-play-box {
    position: relative;
    display: flex;
    align-items: center;
    padding: 15px 25px;
    background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
    border-radius: 15px;
    text-decoration: none !important;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 10px 20px rgba(225, 29, 72, 0.2);
}

.video-play-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(225, 29, 72, 0.4);
}

.play-button-ripple {
    width: 45px;
    height: 45px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: #e11d48;
    z-index: 2;
    position: relative;
}

/* Dalgalanma Efekti */
.play-button-ripple::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: rgba(255,255,255,0.4);
    animation: ripple 1.5s infinite;
}

@keyframes ripple {
    0% { transform: scale(1); opacity: 1; }
    100% { transform: scale(1.8); opacity: 0; }
}

.video-text {
    color: #fff;
    font-weight: 700;
    font-size: 16px;
    letter-spacing: 0.5px;
    z-index: 2;
}

.video-overlay {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: linear-gradient(to right, rgba(255,255,255,0.1), transparent);
    z-index: 1;
}

/* Resim Ayarları */
.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Resmi kutuya sündürmeden doldurur */
    transition: transform 0.4s ease;
    display: block;
}

/* Hover Efekti */
.gallery-item:hover img {
    transform: scale(1.1); /* Üzerine gelince hafif büyür */
}
.ws-card:hover { transform: translateY(-5px); }

/* Renkler */
.ws-card.full  { background: linear-gradient(135deg, #ef4444, #b91c1c); } /* Kırmızı */
.ws-card.avail { background: linear-gradient(135deg, #84cc16, #4d7c0f); } /* Yeşil */
.ws-card.wait  { background: linear-gradient(135deg, #f59e0b, #b45309); } /* Turuncu */

.ws-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.3);
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
    backdrop-filter: blur(4px);
}

.ws-time { font-size: 14px; opacity: 0.9; margin-bottom: 5px; font-weight: 500;}
.ws-title {
    font-size: 18px;
    font-weight: 800;
    text-transform: uppercase;
    line-height: 1.3;
    margin-bottom: 15px;
    color: white !important;
}
.ws-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    font-size: 12px;
}

.ws-info-item span { display: block; opacity: 0.8; font-size: 10px; text-transform: uppercase; }
.ws-info-item strong { font-size: 13px; }

.ws-btn {
    display: block;
    text-align: center;
    background: #fff;
    color: #333;
    padding: 8px;
    border-radius: 50px;
    font-weight: bold;
    font-size: 12px;
    margin-top: 15px;
    text-decoration: none;
    transition: opacity 0.2s;
}
.ws-btn:hover { opacity: 0.9; text-decoration: none; color: #000; }
.ws-btn-disabled { opacity: 0.6; cursor: not-allowed; }
</style>
<style>
:root {
    --ja-teal-start: <?php echo !empty($program['banner_renk1']) ? $program['banner_renk1'] : '#45dcb8'; ?>;
    --ja-teal-end: <?php echo !empty($program['banner_renk2']) ? $program['banner_renk2'] : '#1db0c8'; ?>;
    --ja-yellow: <?php echo !empty($program['banner_ara_serit']) ? $program['banner_ara_serit'] : '#fcee21'; ?>;
    --ja-tab-active: <?php echo !empty($program['tab_aktif_renk']) ? $program['tab_aktif_renk'] : '#45dcb8'; ?>;
    --ja-light-bg: <?php echo !empty($program['banner_renk2']) ? $program['banner_renk2'] : '#1db0c8'; ?>;
    --ja-dark-blue: #1d3b54;
    --ja-gray: #556b7a;
}

body {
    overflow-x: hidden;
}

.ja-program-wrapper {
    position: relative;
    width: 100%;
    margin-bottom: 0;
    padding-bottom: 0;
    overflow: visible;
    isolation: isolate;
    top: 15px;
}

.ja-programs-wrapper {
    margin-top: 55px !important;
    background: linear-gradient(to bottom right, #eefafc 0%, #fdfdfd 50%, #f0fcf4 100%);
    padding: 80px 0;
}

@media only screen and (max-width: 767px) {
    .mobil-yukseklik {
        height: auto !important;
    }
    .ja-program-wrapper {
        top: 0px;
    }
    .ja-programs-wrapper {
        margin-top: 0px !important;
    }
}

/* HERO SECTION */
.ja-hero-section {
    position: relative;
    min-height: 500px;
    display: flex;
    align-items: center;
    padding-bottom: 80px;
    z-index: 5;
}

.ja-hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    <?php if(!empty($program['banner_resim']) && file_exists(tema.'/uploads/programlar/'.$program['banner_resim'])): ?>
    background: url('<?php echo tema.'/uploads/programlar/'.$program['banner_resim']; ?>');
    background-size: cover;
    background-position: center;
    <?php else: ?>
    background: var(--ja-teal-start);
    <?php endif; ?>
    z-index: -1;
    clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
}

.ja-hero-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 4.5rem;
    font-weight: 900;
    color: white !important;
    text-transform: uppercase;
    line-height: 1;
    margin: 0;
}

.ja-hero-title .dot { 
    color: var(--ja-yellow); 
}

.hero-img-holder { 
    position: relative; 
    width: 100%; 
    height: 100%; 
}

.ja-hero-img {
    max-height: 600px;
    width: auto;
    position: relative;
    z-index: 10;
    margin-bottom: -120px;
    filter: drop-shadow(0 10px 20px rgba(0,0,0,0.15));
    transition: transform 0.3s ease;
}

.floating-anim { 
    animation: float 5s ease-in-out infinite; 
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

/* SARI ŞERİT */
.ja-yellow-bar {
    position: relative;
    width: 100%;
    height: 120px;
    background-color: var(--ja-yellow);
    margin-top: -120px;
    z-index: 3;
    clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
    pointer-events: none;
}

/* INTRO SECTION */
.ja-intro-section {
    position: relative;
    background-color: var(--ja-light-bg);
    padding: 100px 0 80px 0;
    margin-top: -80px;
    z-index: 2;
}

.intro-img-wrapper { 
    text-align: center; 
    margin-top: 0; 
}

.intro-img-wrapper img {
    border-radius: 15px;
    max-height: 350px;
    width: auto;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.ja-intro-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--ja-dark-blue);
    text-transform: uppercase;
    margin-bottom: 20px;
}

.ja-intro-text {
    font-family: 'Open Sans', sans-serif;
    color: white;
    line-height: 1.8;
    font-size: 1.05rem;
    column-count: 2;
    column-gap: 40px;
}
.ja-hero-text {
    column-count: 1;
}
@media (min-width: 768px) {
    .ja-hero-text {
        column-count: 2;
        column-gap: 40px;
    }
}

/* SIDEBAR MENU */
.ja-sidebar-menu .ja-menu-item {
    display: block;
    background-color: #ffffff;
    border-radius: 8px;
    padding: 25px 25px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
    border: none;
    color: #375d70;
    font-weight: 700;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    position: relative;
    text-decoration: none;
    cursor: pointer;
}

.ja-sidebar-menu .ja-menu-item:hover {
    background-color: #fcfcfc;
    transform: translateY(-4px);
    color: var(--ja-dark-blue);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
}

.ja-sidebar-menu .ja-menu-item.active {
    background: var(--ja-tab-active);
    color: #ffffff;
    box-shadow: 0 8px 20px rgba(69, 220, 184, 0.3);
}

.ja-sidebar-menu .ja-menu-item.active:hover {
    background: var(--ja-tab-active);
    filter: brightness(1.1);
    color: #ffffff;
    transform: translateY(-4px);
}

.ja-tab-content {
    min-height: 300px;
}

.ja-content-wrapper {
    background: #ffffff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.ja-content-title {
    font-size: 2.4rem;
    font-weight: 800;
    color: var(--ja-dark-blue);
    margin-bottom: 20px;
    text-transform: uppercase;
}

.ja-content-desc {
    font-family: 'Open Sans', sans-serif;
    color: var(--ja-gray);
    line-height: 1.8;
    font-size: 1.05rem;
}

.detay-icerik {
    margin-top: 30px;
}

.detay-icerik h1,
.detay-icerik h2,
.detay-icerik h3,
.detay-icerik h4,
.detay-icerik h5,
.detay-icerik h6 {
    color: var(--ja-dark-blue);
    margin-top: 30px;
    margin-bottom: 15px;
    font-weight: 600;
}

/* DİNAMİK İÇERİK BLOKLARI */
.ja-icerik-blok {
    padding: 30px 0;
    border-bottom: 1px solid #e0e0e0;
}

.ja-icerik-blok:last-child {
    border-bottom: none;
}

.ja-icerik-baslik {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--ja-dark-blue);
    text-transform: uppercase;
    margin-bottom: 20px;
}

.ja-icerik-aciklama {
    font-family: 'Open Sans', sans-serif;
    color: var(--ja-gray);
    line-height: 1.8;
    font-size: 1.05rem;
    margin-bottom: 20px;
}

.ja-icerik-gorsel-tek {
    margin-top: 10px;
}

.ja-gorsel-tek-img {
    width: 100%;
    height: auto;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    display: block;
}

@media (max-width: 991px) {
    .ja-icerik-gorsel-tek {
        margin: 0 -10px;
    }
    .ja-gorsel-tek-img {
        border-radius: 8px;
    }
}

.ja-icerik-medya {
    margin-top: 25px;
}

.ja-icerik-gorsel img {
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.video-wrapper {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
    height: 0;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.video-wrapper iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 8px;
}

/* DERS PROGRAMI STİLLERİ - Minimal Modern Tasarım */
.ja-ders-programi-wrapper {
    margin-top: 10px;
}

.ja-gun-section {
    margin-bottom: 40px;
}

.ja-gun-section:last-child {
    margin-bottom: 0;
}

.ja-gun-header {
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e8f4f8;
}

.ja-gun-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--ja-dark-blue);
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ja-derslik-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.ja-derslik-item {
    background: #ffffff;
    border: 1px solid #e8eef0;
    border-radius: 10px;
    padding: 18px 20px;
    display: flex;
    align-items: flex-start;
    gap: 20px;
    transition: all 0.3s ease;
    position: relative;
}

.ja-derslik-item:hover {
    border-color: var(--ja-teal-start);
    box-shadow: 0 4px 12px rgba(69, 220, 184, 0.1);
    transform: translateX(4px);
}

.ja-derslik-time {
    min-width: 100px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: linear-gradient(135deg, var(--ja-teal-start) 0%, var(--ja-teal-end) 100%);
    border-radius: 8px;
    color: #fff;
    font-weight: 600;
    font-size: 0.9rem;
    text-align: center;
    flex-direction: column;
}

.ja-derslik-time i {
    font-size: 1.1rem;
    opacity: 0.9;
}

.ja-derslik-time span {
    font-size: 0.85rem;
    margin-top: 2px;
}

.ja-derslik-content {
    flex: 1;
    min-width: 0;
}

.ja-derslik-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
    gap: 15px;
}

.ja-derslik-baslik {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--ja-dark-blue);
    margin: 0;
    flex: 1;
}

.ja-derslik-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    white-space: nowrap;
}

.ja-derslik-status.status-avail {
    background: #e8f5e9;
    color: #2e7d32;
}

.ja-derslik-status.status-full {
    background: #ffebee;
    color: #c62828;
}

.ja-derslik-status.status-waiting {
    background: #fff3e0;
    color: #e65100;
}

.ja-derslik-status i {
    font-size: 0.85rem;
}

.ja-derslik-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 12px;
}

.ja-meta-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    color: var(--ja-gray);
    font-weight: 500;
}

.ja-meta-item i {
    color: var(--ja-teal-start);
    font-size: 0.9rem;
}

.ja-derslik-progress {
    display: flex;
    align-items: center;
    gap: 12px;
}

.progress-bar-wrapper {
    flex: 1;
    height: 6px;
    background: #e8eef0;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--ja-teal-start) 0%, var(--ja-teal-end) 100%);
    border-radius: 10px;
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 0.75rem;
    color: var(--ja-gray);
    font-weight: 600;
    min-width: 50px;
    text-align: right;
}

@media (max-width: 768px) {
    .ja-derslik-item {
        flex-direction: column;
        gap: 15px;
    }
    
    .ja-derslik-time {
        width: 100%;
        flex-direction: row;
        justify-content: center;
    }
    
    .ja-derslik-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .ja-derslik-status {
        align-self: flex-start;
    }
}

@media (max-width: 576px) {
    .ja-icerik-baslik {
        font-size: 1.4rem;
    }
    
    .video-wrapper {
        padding-bottom: 75%; /* Mobilde daha yüksek */
    }
    
    .ja-gun-title {
        font-size: 1.1rem;
    }
    
    .ja-derslik-baslik {
        font-size: 1rem;
    }
    
    .ja-derslik-meta {
        flex-direction: column;
        gap: 8px;
    }
    
    .ja-derslik-progress {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .progress-text {
        text-align: left;
    }

    /* Resimler mobilde tam genişlik */
    .ja-icerik-medya .row > div.col-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (max-width: 767px) {
    .ja-hero-section {
        min-height: auto !important;
        padding: 50px 0 100px 0 !important;
    }

    .ja-hero-section .container,
    .ja-hero-section .row {
        height: auto !important;
    }

    .ja-hero-section .container {
        padding-left: 20px !important;
        padding-right: 20px !important;
    }

    .ja-hero-section::before {
        clip-path: polygon(0 0, 100% 0, 100% 92%, 0 100%);                                 
        background-size: 150% auto !important;                                             
        background-position: center center !important;     
    }

    .ja-hero-title {
        font-size: 1.75rem !important;
        line-height: 1.2 !important;
        word-break: break-word;
        text-shadow: 0 1px 6px rgba(0,0,0,0.4);
    }

    .ja-intro-text {
        font-size: 0.95rem;
        line-height: 1.6;
        text-shadow: 0 1px 4px rgba(0,0,0,0.35);
    }

    .ja-hero-text {
        column-count: 1 !important;
        margin-top: 12px;
    }

    .ja-yellow-bar {
        height: 60px;
        margin-top: -60px;
    }
}

/* BUTONLAR */
.btn-ja-teal {
    background-color: var(--ja-teal-end);
    color: #fff;
    border: none;
    padding: 16px 40px;
    border-radius: 50px;
    font-weight: 800;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(44, 92, 114, 0.25);
    display: inline-block;
}

.btn-ja-teal:hover {
    background-color: var(--ja-dark-blue);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(44, 92, 114, 0.4);
    color: #fff;
}

.btn-ja-outline {
    display: inline-block;
    padding: 12px 24px;
    border: 2px solid var(--ja-teal-start);
    background: transparent;
    color: var(--ja-teal-start);
    border-radius: 30px;
    font-weight: 700;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: center;
}

.btn-ja-outline:hover {
    background: var(--ja-teal-start);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(69, 220, 184, 0.4);
    text-decoration: none;
}

.d-flex {
    display: flex !important;
}

.flex-wrap {
    flex-wrap: wrap !important;
}

.flex-fill {
    flex: 1 1 auto !important;
    min-width: 0;
}

/* RESİM ÇERÇEVELERİ */
.img-frame {
    padding: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
}

.img-frame img {
    width: 100%;
    height: auto;
    display: block;
}

.frame-teal { 
    background-color: #c9ebf2; 
}

/* MODAL STİLLERİ */
.ja-modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    overflow: hidden;
}

.ja-modal-header {
    background: linear-gradient(135deg, var(--ja-teal-start) 0%, var(--ja-teal-end) 100%);
    color: #fff;
    padding: 30px 40px;
    border-bottom: none;
    position: relative;
}

.ja-modal-header .modal-title {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.8rem;
    font-weight: 800;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.ja-modal-header .modal-title i {
    font-size: 2rem;
    color: var(--ja-yellow);
}

.modal-subtitle {
    margin: 8px 0 0 0;
    font-size: 0.95rem;
    opacity: 0.9;
    font-weight: 400;
}

.ja-modal-body {
    padding: 40px;
    background: #fff;
}

.ja-form-control {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 12px 18px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    font-family: 'Open Sans', sans-serif;
}

.ja-form-control:focus {
    border-color: var(--ja-teal-start);
    box-shadow: 0 0 0 0.2rem rgba(69, 220, 184, 0.15);
    outline: none;
}

.ja-modal-footer {
    border-top: 1px solid #e0e0e0;
    padding: 20px 40px;
    background: #f8f9fa;
}

.ja-btn-cancel {
    border-radius: 25px;
    padding: 10px 25px;
    font-weight: 600;
    border: 2px solid #ddd;
    background: #fff;
    color: var(--ja-gray);
}

.ja-btn-submit {
    background: linear-gradient(135deg, var(--ja-teal-start) 0%, var(--ja-teal-end) 100%);
    border: none;
    border-radius: 25px;
    padding: 10px 30px;
    font-weight: 700;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(69, 220, 184, 0.4);
}

/* RESPONSIVE */
@media (max-width: 991px) {
    .ja-hero-section {
        min-height: auto;
        padding: 50px 0 60px 0;
        text-align: center;
    }

    .ja-hero-section::before {
        clip-path: polygon(0 0, 100% 0, 100% 96%, 0 100%) !important;
    }

    .ja-hero-title {
        font-size: 2.8rem;
        margin-bottom: 20px;
    }

    .ja-hero-img {
        max-height: 300px;
        margin-bottom: -50px;
    }

    .ja-yellow-bar {
        height: 50px;
        margin-top: -50px;
        clip-path: polygon(0 0, 100% 0, 100% 96%, 0 100%) !important;
    }

    .ja-intro-section {
        padding: 60px 0 40px 0;
        margin-top: -20px;
        text-align: center;
    }

    .ja-intro-text {
        column-count: 1 !important;
        text-align: left;
        padding: 0 10px;
    }

    /* Mobil Accordion Style */
    .ja-sidebar-menu {
        display: block;
        margin-bottom: 20px;
    }

    .ja-sidebar-menu .ja-menu-item {
        margin-right: 0;
        margin-bottom: 0;
        min-width: auto;
        border-radius: 0;
        border-bottom: 1px solid #e0e0e0;
        padding: 20px 20px;
    }
    
    .ja-sidebar-menu .ja-menu-item:first-child {
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    
    .ja-sidebar-menu .ja-menu-item:last-of-type {
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    
    .ja-sidebar-menu .ja-menu-item .menu-text {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }
    
    .ja-sidebar-menu .ja-menu-item .menu-text::after {
        content: '\f078';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        font-size: 0.9rem;
        transition: transform 0.3s ease;
        opacity: 0.7;
    }
    
    /* İlk item (Tüm Programlar) için ok işaretini kaldır */
    .ja-sidebar-menu .ja-menu-item:first-child .menu-text::after {
        content: '';
    }
    
    /* Aktif (açık) tab'da ok yukarı dönüyor */
    .ja-sidebar-menu .ja-menu-item.active .menu-text::after {
        transform: rotate(180deg);
        opacity: 1;
    }

    /* Mobilde içerik wrapper'ı gizle, her tab kendi içeriğini gösterecek */
    .ja-content-wrapper {
        padding: 0;
        background: transparent;
        box-shadow: none;
    }
    
    .ja-content-wrapper > .ja-actions {
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .ja-content-wrapper > .ja-tab-content {
        display: none !important;
    }

    /* Mobil tab içeriği - her tab altında gösterilecek */
    .mobile-tab-content {
        background: #ffffff;
        padding: 0;
        border-left: 3px solid var(--ja-teal-start);
        margin-bottom: 0;
        display: block;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border-radius: 0 0 8px 8px;
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: max-height 0.3s ease, opacity 0.3s ease, padding 0.3s ease;
    }
    
    .mobile-tab-content.show {
        max-height: 5000px;
        opacity: 1;
        padding: 25px;
    }

    .pl-lg-5 {
        padding-left: 15px !important;
    }
}

@media (max-width: 576px) {
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .btn-ja-teal.flex-fill,
    .btn-ja-outline.flex-fill {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // === MOBİL TAB SİSTEMİ ===
    
    function isMobile() {
        return window.innerWidth <= 991;
    }
    
    // Sayfa yüklendiğinde mobil modda mıyız?
    if (isMobile()) {
        initMobileTabs();
    }
    
    // Resize olayında
    $(window).on('resize', function() {
        if (isMobile() && !$('.mobile-tab-content').length) {
            initMobileTabs();
        }
    });
    
    function initMobileTabs() {
        
        // 1. Tüm icerik-menu-item'ları bul
        var $menuItems = $('.ja-sidebar-menu .icerik-menu-item');
        
        // 2. Her menu item için mobil içerik oluştur
        $menuItems.each(function(index) {
            var $menuItem = $(this);
            var targetId = $menuItem.attr('href');
            var $targetContent = $(targetId);
            
            // Mobil içerik zaten varsa oluşturma
            if ($menuItem.next('.mobile-tab-content').length > 0) {
                return;
            }
            
            if ($targetContent.length > 0) {
                var $mobileContent = $('<div class="mobile-tab-content" data-tab-index="' + index + '"></div>');
                $mobileContent.html($targetContent.html());
                $menuItem.after($mobileContent);
                
                // Data attribute ile index sakla
                $menuItem.attr('data-tab-index', index);
                
            }
        });
        
        // 3. İlk aktif tab'ı aç (sadece ilk olan)
        var $firstActive = $menuItems.filter('.active').first();
        if ($firstActive.length > 0) {
            // Diğer tüm active'leri kaldır
            $menuItems.removeClass('active');
            $('.mobile-tab-content').removeClass('show');
            
            // Sadece ilkini aktif yap
            $firstActive.addClass('active');
            $firstActive.next('.mobile-tab-content').addClass('show');
        }
        
    }
    
    // === CLICK HANDLER - Event Delegation ===
    $(document).on('click', '.ja-sidebar-menu .icerik-menu-item', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        var $clicked = $(this);
        

        if (!isMobile()) {
            // Desktop - normal tab davranışı
            var target = $clicked.attr('href');
            $('.tab-pane').removeClass('show active');
            $('.icerik-menu-item').removeClass('active');
            $clicked.addClass('active');
            $(target).addClass('show active');
            return;
        }
        
        // === MOBİL DAVRANIŞ ===
        var $mobileContent = $clicked.next('.mobile-tab-content');
        var isOpen = $clicked.hasClass('active');
        
            if (isOpen) {
   
            $clicked.removeClass('active');
            $mobileContent.removeClass('show');
        } else {
            // AÇ (önce diğerlerini kapat)

            // Tüm tab'ları kapat
            $('.ja-sidebar-menu .icerik-menu-item').removeClass('active');
            $('.mobile-tab-content').removeClass('show');

            // Bu tab'ı aktif yap
            $clicked.addClass('active');

            // Önce tıklanan sekmeye scroll yap
            var headerHeight = 80;
            var targetOffset = $clicked.offset().top - headerHeight - 10;
            $('html, body').stop().animate({ scrollTop: targetOffset }, 300, function() {
                // Scroll tamamlanınca içeriği aç
                $mobileContent.addClass('show');
            });
        }
    });
    
    // === PROGRAM TALEP MODAL ===
    $('.program-talep-btn').on('click', function() {
        var programId = $(this).data('program-id');
        var programBaslik = $(this).data('program-baslik');
        
        $('#modalProgramId').val(programId);
        $('#modalProgramBaslik').val(programBaslik);
        $('#modalProgramTitle').text(programBaslik);
    });
    
$('#programTalepForm').on('submit', function (e) {
    e.preventDefault();

    var form = $(this);
    var submitBtn = form.find('.ja-btn-submit');
    var originalText = submitBtn.html();

    submitBtn.addClass('loading').prop('disabled', true);

    var formData = form.serialize();

    $.ajax({
        type: "POST",
        url: "_class/site_islem.php",
        data: formData,
        dataType: "json",
        success: function (response) {
            submitBtn.removeClass('loading').prop('disabled', false);

            if (response.success) {

                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı',
                    text: response.message || 'Talebiniz başarıyla alındı.',
                    confirmButtonText: 'Tamam',
                    timer: 2500,
                    timerProgressBar: true
                }).then(() => {
                    form[0].reset();
                    $('#programTalepModal').modal('hide');
                });

            } else {

                Swal.fire({
                    icon: 'warning',
                    title: 'Uyarı',
                    text: response.message || 'Bir hata oluştu.',
                    confirmButtonText: 'Tamam'
                });

            }
        },
        error: function (xhr, status, error) {
            submitBtn.removeClass('loading').prop('disabled', false);

            console.error('AJAX Error:', error);

            Swal.fire({
                icon: 'error',
                title: 'Hata',
                text: 'Sunucu ile iletişim kurulamadı.',
                confirmButtonText: 'Tamam'
            });
        }
    });
});

    $('#programTalepModal').on('hidden.bs.modal', function() {
        $('#programTalepForm')[0].reset();
        $('.alert-success-custom').remove();
        $('#modalProgramId').val('');
        $('#modalProgramBaslik').val('');
    });
});
</script>
