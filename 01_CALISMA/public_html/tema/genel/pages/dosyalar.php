<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
$page = @intval($_GET['s']);
if(!$page) $page = 1;
$ttsorgu = $db->prepare("SELECT COUNT(*) FROM dosyalar WHERE durum = ? AND dil = ?");
$ttsorgu->execute(array(1, $_SESSION['k_dil']));
$total = $ttsorgu->fetchColumn();
$limit = 20;
$page_count = ceil($total / $limit);
if($page > $page_count) $page = 1;
$show = $page * $limit - $limit;
$DSorgu = $db->prepare("SELECT * FROM dosyalar WHERE durum = ? AND dil = ? ORDER BY sira ASC, id DESC LIMIT $show, $limit");
$DSorgu->execute(array(1, $_SESSION['k_dil']));
$Dislem = $DSorgu->fetchAll(PDO::FETCH_ASSOC);

// Kategori filtresi
$katSorgu = $db->prepare("SELECT DISTINCT kategori FROM dosyalar WHERE durum = 1 AND dil = ? AND kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC");
$katSorgu->execute(array($_SESSION['k_dil']));
$kategoriler = $katSorgu->fetchAll(PDO::FETCH_COLUMN);

$menubul = $db->query("SELECT * FROM menu WHERE menu_url = '".$htc['dosyalarurl']."' OR link = '".$htc['dosyalarurl']."' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
$menubas = $db->query("SELECT * FROM menu WHERE id = '".($menubul['menu_ust'] ?? 0)."' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);

$dosya_ikonlari = [
    'PDF'  => ['ikon' => 'fa-file-pdf',        'renk' => '#e53935', 'bg' => '#fdecea'],
    'DOC'  => ['ikon' => 'fa-file-word',        'renk' => '#1565c0', 'bg' => '#e3f0ff'],
    'DOCX' => ['ikon' => 'fa-file-word',        'renk' => '#1565c0', 'bg' => '#e3f0ff'],
    'XLS'  => ['ikon' => 'fa-file-excel',       'renk' => '#2e7d32', 'bg' => '#e8f5e9'],
    'XLSX' => ['ikon' => 'fa-file-excel',       'renk' => '#2e7d32', 'bg' => '#e8f5e9'],
    'PPT'  => ['ikon' => 'fa-file-powerpoint',  'renk' => '#bf360c', 'bg' => '#fbe9e7'],
    'PPTX' => ['ikon' => 'fa-file-powerpoint',  'renk' => '#bf360c', 'bg' => '#fbe9e7'],
    'ZIP'  => ['ikon' => 'fa-file-archive',     'renk' => '#f57f17', 'bg' => '#fff8e1'],
    'RAR'  => ['ikon' => 'fa-file-archive',     'renk' => '#f57f17', 'bg' => '#fff8e1'],
    'TXT'  => ['ikon' => 'fa-file-alt',         'renk' => '#546e7a', 'bg' => '#eceff1'],
];

// Accent color: renk2 varsa kullan, yoksa güvenli bir varsayılan
$accent = (defined('renk2') && renk2 && renk2 !== '#ffffff' && renk2 !== '#fffbfb') ? renk2 : '#1a73e8';
?>
<!-- PAGE SECTİON BAŞLANGIÇ -->
<section class="page-section">
    <div class="bg-white">
        <div class="col-12 p-0 banner">
            <img src="<?php echo tema;?>/uploads/arkaplan/arkaplan14/<?php echo $arkaplan['arkaplan14'];?>" alt="Dosyalarımız">
            <div class="slide-overlay"></div>
        </div>
        <div class="container banner-fix">
            <div class="row">
                <div class="col-lg-<?php echo($moduller['alan23'] == "1" ? '9 offset-lg-3' : '12');?> col-md-<?php echo($moduller['alan23'] == "1" ? '7 offset-md-5' : '12');?> z-index-9">
                    <ol class="breadcrumb">
                        <li><a href="<?php echo $htc['anaurl'];?><?php echo $html;?>"> <i class="fa fa-home"></i> </a></li>
                        <?php if(!empty($menubas['menu_isim'])){?>
                        <li><a href="<?php echo($menubas['menu_url'] == "0" ? $menubas['link'] : $menubas['menu_url']);?>"><?php echo cVCLmHLxbS_ilkbuyuk($menubas['menu_isim']);?></a></li>
                        <?php }?>
                        <li>Dosyalarımız</li>
                    </ol>
                </div>
                <?php include('leftbar.php');?>
                <div class="col-lg-<?php echo($moduller['alan23'] == "1" ? '9' : '12');?> col-md-<?php echo($moduller['alan23'] == "1" ? '7' : '12');?> z-index-9">
                    <div class="page-content">
                        <h2 class="page-title">Dosyalarımız</h2>

                        <?php if(count($kategoriler) > 1): ?>
                        <div class="df-filtre-wrap">
                            <button class="df-filtre-btn df-active" data-kat="*">
                                <i class="fas fa-layer-group"></i> Tümü
                                <span class="df-filtre-sayi"><?php echo count($Dislem); ?></span>
                            </button>
                            <?php foreach($kategoriler as $kat):
                                $katSayi = count(array_filter($Dislem, fn($d) => $d['kategori'] === $kat));
                            ?>
                            <button class="df-filtre-btn" data-kat="<?php echo htmlspecialchars($kat);?>">
                                <?php echo htmlspecialchars($kat);?>
                                <span class="df-filtre-sayi"><?php echo $katSayi; ?></span>
                            </button>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if($DSorgu->rowCount() > 0): ?>
                        <div class="df-liste">
                            <?php foreach($Dislem as $DSonuc):
                                $tip = $DSonuc['dosya_tip'] ?: 'TXT';
                                $ikonBilgi = $dosya_ikonlari[$tip] ?? ['ikon'=>'fa-file','renk'=>'#546e7a','bg'=>'#eceff1'];
                            ?>
                            <div class="df-kart" data-kategori="<?php echo htmlspecialchars($DSonuc['kategori']);?>">
                                <div class="df-ikon-wrap" style="background:<?php echo $ikonBilgi['bg'];?>">
                                    <i class="fas <?php echo $ikonBilgi['ikon'];?>" style="color:<?php echo $ikonBilgi['renk'];?>"></i>
                                    <span class="df-tip-badge" style="color:<?php echo $ikonBilgi['renk'];?>"><?php echo $tip;?></span>
                                </div>
                                <div class="df-icerik">
                                    <div class="df-baslik"><?php echo htmlspecialchars($DSonuc['baslik']);?></div>
                                    <?php if($DSonuc['aciklama']): ?>
                                    <div class="df-aciklama"><?php echo htmlspecialchars($DSonuc['aciklama']);?></div>
                                    <?php endif; ?>
                                    <div class="df-meta">
                                        <?php if($DSonuc['kategori']): ?>
                                        <span class="df-meta-kat"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($DSonuc['kategori']);?></span>
                                        <?php endif; ?>
                                        <?php if($DSonuc['dosya_boyut']): ?>
                                        <span class="df-meta-boyut"><i class="fas fa-hdd"></i> <?php echo $DSonuc['dosya_boyut'];?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="df-aksiyon">
                                    <a href="/uploads/dosyalar/<?php echo $DSonuc['dosya'];?>" target="_blank" class="df-indir-btn" download>
                                        <i class="fas fa-download"></i>
                                        <span>İndir</span>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if($limit < $total && $limit > 0): ?>
                        <div class="pagination">
                            <ul>
                            <?php
                            $showing = 3;
                            if($page > 1){ $previous = $page - 1;?>
                            <li class="onceki_sayfa"><a href="<?php echo $htc['dosyalarurl'];?>/<?php echo $previous;?><?php echo $html;?>"><i class="fas fa-angle-left"></i></a></li>
                            <?php }
                            for($i = $page - $showing; $i < $page + $showing + 1; $i++){
                            if($i > 0 && $i <= $page_count){
                            if($i == $page){?>
                            <li><a class="secili" href="javascript:void(0)"><?php echo $i; ?></a></li>
                            <?php }else{?>
                            <li><a href="<?php echo $htc['dosyalarurl'];?>/<?php echo $i;?><?php echo $html;?>"><?php echo $i; ?></a></li>
                            <?php } } } if($page != $page_count){
                            $next = $page + 1;?>
                            <li class="sonraki_sayfa"><a href="<?php echo $htc['dosyalarurl'];?>/<?php echo $next;?><?php echo $html;?>"><i class="fas fa-angle-right"></i></a></li>
                            <?php } ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <?php else: ?>
                        <div class="df-bos">
                            <i class="fas fa-folder-open"></i>
                            <p>Henüz dosya eklenmemiştir.</p>
                        </div>
                        <?php endif; ?>

<?php displayContactSection($page_name ?? '', $db, $dil, $sayfalink); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- PAGE SECTİON BİTİŞ -->

<style>
:root {
    --df-accent: <?php echo $accent; ?>;
    --df-accent-light: <?php echo $accent; ?>18;
    --df-border: #e8eaed;
    --df-shadow: 0 1px 4px rgba(0,0,0,.07);
    --df-shadow-hover: 0 4px 16px rgba(0,0,0,.12);
    --df-radius: 10px;
}

/* ── Filtre Butonları ── */
.df-filtre-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 24px;
}
.df-filtre-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    border: 1.5px solid var(--df-border);
    border-radius: 50px;
    background: #fff;
    color: #555;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all .2s;
}
.df-filtre-btn:hover {
    border-color: var(--df-accent);
    color: var(--df-accent);
}
.df-filtre-btn.df-active {
    background: var(--df-accent);
    border-color: var(--df-accent);
    color: #fff;
}
.df-filtre-sayi {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 5px;
    border-radius: 10px;
    background: rgba(255,255,255,.25);
    font-size: 11px;
    font-weight: 700;
    line-height: 1;
}
.df-filtre-btn:not(.df-active) .df-filtre-sayi {
    background: #f0f0f0;
    color: #777;
}

/* ── Liste ── */
.df-liste {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 40px;
}

/* ── Kart (Satır) ── */
.df-kart {
    display: flex;
    align-items: center;
    gap: 0;
    background: #fff;
    border: 1.5px solid var(--df-border);
    border-radius: var(--df-radius);
    overflow: hidden;
    box-shadow: var(--df-shadow);
    transition: background .15s, box-shadow .15s, border-color .15s;
}
.df-kart:hover {
    background: #fafbfd;
    border-color: var(--df-accent);
    box-shadow: var(--df-shadow-hover);
}

/* ── İkon Alanı ── */
.df-ikon-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    min-width: 72px;
    width: 72px;
    align-self: stretch;
    padding: 16px 8px;
    flex-shrink: 0;
}
.df-ikon-wrap .fas {
    font-size: 26px;
}
.df-tip-badge {
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .5px;
    text-transform: uppercase;
    opacity: .85;
}

/* ── İçerik ── */
.df-icerik {
    flex: 1;
    min-width: 0;
    padding: 14px 16px;
    border-left: 1px solid var(--df-border);
}
.df-baslik {
    font-weight: 600;
    font-size: 14.5px;
    color: #222;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.df-aciklama {
    font-size: 12.5px;
    color: #777;
    margin-top: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.df-meta {
    display: flex;
    gap: 12px;
    margin-top: 6px;
    flex-wrap: wrap;
}
.df-meta-kat,
.df-meta-boyut {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11.5px;
    color: #888;
}
.df-meta-kat { color: var(--df-accent); font-weight: 500; }
.df-meta-kat .fas,
.df-meta-boyut .fas { font-size: 10px; }

/* ── İndir Butonu ── */
.df-aksiyon {
    padding: 14px 18px;
    flex-shrink: 0;
}
.df-indir-btn {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    width: 60px;
    height: 56px;
    border-radius: 8px;
    background: var(--df-accent);
    color: #fff !important;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none !important;
    transition: all .2s;
    letter-spacing: .3px;
}
.df-indir-btn .fas {
    font-size: 18px;
}
.df-indir-btn:hover {
    opacity: .88;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,.2);
    color: #fff !important;
    text-decoration: none !important;
}

/* ── Boş Durum ── */
.df-bos {
    text-align: center;
    padding: 60px 20px;
    color: #bbb;
}
.df-bos .fas {
    font-size: 48px;
    margin-bottom: 12px;
    display: block;
}
.df-bos p {
    font-size: 15px;
    color: #aaa;
    margin: 0;
}

/* ── Mobil ── */
@media (max-width: 576px) {
    .df-ikon-wrap { min-width: 54px; width: 54px; }
    .df-ikon-wrap .fas { font-size: 20px; }
    .df-icerik { padding: 12px; }
    .df-baslik { font-size: 13.5px; white-space: normal; }
    .df-aksiyon { padding: 12px 12px 12px 0; }
    .df-indir-btn { width: 52px; height: 50px; font-size: 10px; }
    .df-indir-btn .fas { font-size: 16px; }
}
</style>

<script>
$(document).ready(function(){
    $('.df-filtre-btn').on('click', function(){
        $('.df-filtre-btn').removeClass('df-active');
        $(this).addClass('df-active');
        var kat = $(this).data('kat');
        if(kat === '*') {
            $('.df-kart').show();
        } else {
            $('.df-kart').each(function(){
                $(this).toggle($(this).data('kategori') === kat);
            });
        }
    });
});
</script>
<?php include('slider_menu.php');?>
