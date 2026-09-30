<!-- ============================================
     ANASAYFA BAĞIŞ KAMPANYALARI WİDGET'I
     Bu kodu anasayfa.php dosyanızın istediğiniz yerine ekleyin
     ============================================ -->

<?php 
// Anasayfada gösterilecek kampanyaları çek
$BagisModulSorgu = $db->prepare("SELECT * FROM bagis_moduller 
                                  WHERE dil = ? AND durum = ? AND anasayfada_goster = ? 
                                  ORDER BY sira ASC LIMIT 3");
$BagisModulSorgu->execute(array($_SESSION['k_dil'], 1, 1));
$BagisModuller = $BagisModulSorgu->fetchALL(PDO::FETCH_ASSOC);
?>

<?php if($BagisModulSorgu->rowCount()){ ?>
<section class="bagis-kampanyalari-section py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="section-title"><?=@$dil['txt433'];?></h2>
                <p class="section-subtitle"><?=@$dil['txt434'];?></p>
            </div>
        </div>
        
        <div class="row">
            <?php foreach($BagisModuller as $Modul){ ?>
            <?php
            // İstatistikleri hesapla
            $yuzde = 0;
            $kalan_gun = $dil['txt436'];
            
            if($Modul['hedef_tutar'] > 0) {
                $yuzde = round(($Modul['toplanan_tutar'] / $Modul['hedef_tutar']) * 100, 2);
            }
            
            if($Modul['bitis_tarihi'] && $Modul['bitis_tarihi'] != '0000-00-00') {
                $bugun = new DateTime();
                $bitis = new DateTime($Modul['bitis_tarihi']);
                $fark = $bugun->diff($bitis);
                if($bugun < $bitis) {
                    $kalan_gun = $fark->days . ' ' . $dil['txt437'];
                } else {
                    $kalan_gun = $dil['txt438'];
                }
            }
            ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="kampanya-card h-100" style="border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden; background: #fff; transition: all 0.3s;">
                    <?php if($Modul['kapak_resmi']){?>
                    <div class="kampanya-image" style="height: 200px; overflow: hidden;">
                        <img src="<?php echo tema;?>/uploads/bagis_moduller/<?php echo $Modul['kapak_resmi'];?>" 
                             alt="<?php echo $Modul['adi'];?>" 
                             style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <?php }?>
                    
                    <div class="kampanya-content p-3">
                        <h4 style="font-size: 18px; font-weight: 600; min-height: 50px;">
                            <?php echo $Modul['adi'];?>
                        </h4>
                        
                        <?php if($Modul['aciklama']){?>
                        <p class="text-muted" style="font-size: 14px; min-height: 60px;">
                            <?php echo mb_substr(strip_tags($Modul['aciklama']), 0, 100);?>...
                        </p>
                        <?php }?>
                        
                        <?php if($Modul['hedef_tutar'] > 0){?>
                        <div class="kampanya-stats mb-3">
                            <div class="progress mb-2" style="height: 8px; border-radius: 10px;">
                                <div class="progress-bar bg-success" 
                                     style="width: <?php echo min($yuzde, 100);?>%;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted" style="font-size: 13px;">
                                    <strong><?php echo number_format($Modul['toplanan_tutar'], 0, ',', '.');?> TL</strong> <?=@$dil['txt435'];?>
                                </span>
                                <span class="text-success" style="font-size: 13px; font-weight: 600;">
                                    %<?php echo $yuzde;?>
                                </span>
                            </div>
                        </div>
                        <?php }?>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge badge-primary"><?php echo $kalan_gun;?></span>
                            <a href="<?php echo $htc['bagismoduldetayurl'];?>/<?php echo $Modul['seo'];?><?php echo $html;?>" 
                               class="btn btn-sm btn-success">
                                <i class="fas fa-hand-holding-heart"></i> <?=@$dil['txt423'];?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <div class="row">
            <div class="col-12 text-center mt-3">
                <a href="<?php echo $htc['bagismodulurl'];?><?php echo $html;?>" class="btn btn-outline-primary">
                    <?=@$dil['txt439'];?> <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.kampanya-card:hover {
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    transform: translateY(-5px);
}
.section-title {
    font-size: 32px;
    font-weight: 700;
    color: #333;
}
.section-subtitle {
    font-size: 16px;
    color: #666;
}
</style>
<?php } ?>

