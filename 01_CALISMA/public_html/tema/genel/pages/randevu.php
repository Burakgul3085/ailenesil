<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
/**
 * Randevu Sayfası - Modern 3 Aşamalı Randevu Formu
 */

// Hizmet seçimi aktif mi kontrol et
$hizmetSecimAktif = isset($moduller['alan34']) && $moduller['alan34'] == 1;

// Randevu hizmetlerini çek
$HizmetSorgu = $db->prepare("SELECT * FROM randevu_hizmetler WHERE durum = 1 AND dil = ? ORDER BY sira ASC");
$HizmetSorgu->execute(array($_SESSION['k_dil']));
$hizmetler = $HizmetSorgu->fetchAll(PDO::FETCH_ASSOC);

// Menü bilgileri (breadcrumb için)
$menubul = $db->query("SELECT * FROM menu WHERE menu_url = '".$htc['randevuurl']."' OR link = '".$htc['randevuurl']."' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);
$menubas = $db->query("SELECT * FROM menu WHERE id = '{$menubul['menu_ust']}' AND dil = '{$_SESSION['k_dil']}'")->fetch(PDO::FETCH_ASSOC);

// KVKK metni için sayfayı çek
$KvkkSorgu = $db->prepare("SELECT * FROM sayfalar WHERE seo = ? AND durum = 1 LIMIT 1");
$KvkkSorgu->execute(array('kvkk-aydinlatma-metni'));
$kvkkSayfa = $KvkkSorgu->fetch(PDO::FETCH_ASSOC);
$kvkkLink = $kvkkSayfa ? $url.'/'.$htc['sayfaurl'].'/'.$kvkkSayfa['seo'].$html : '#';
?>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.randevu-container {
    background: #f5f5f5;
    padding: 3rem 0;
}

.step-indicator {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 2rem;
    position: relative;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    flex: 1;
    max-width: 200px;
}

.step-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 18px;
    position: relative;
    z-index: 2;
    transition: all 0.3s;
}

.step-item.active .step-circle {
    background: #3b82f6;
    color: white;
}

.step-item.completed .step-circle {
    background: #3b82f6;
    color: white;
}

.step-line {
    position: absolute;
    top: 24px;
    left: 50%;
    width: 100%;
    height: 2px;
    background: #e5e7eb;
    z-index: 1;
}

.step-item.completed .step-line {
    background: #3b82f6;
}

.step-label {
    margin-top: 0.5rem;
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

.step-item.active .step-label {
    color: #3b82f6;
    font-weight: 600;
}

.service-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.2s;
    height: 100%;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.service-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}

.service-card.selected {
    border-color: #3b82f6;
    background: #eff6ff;
}

.service-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    pointer-events: none;
}

.service-card * {
    pointer-events: none;
}

.service-card h3,
.service-card p,
.service-card div {
    pointer-events: none;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-top: 1rem;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
    background: white;
    border: 1px solid #e5e7eb;
}

.calendar-day:hover:not(.disabled) {
    border-color: #3b82f6;
    background: #eff6ff;
}

.calendar-day.selected {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.calendar-day.disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-bottom: 8px;
}

.calendar-header-item {
    text-align: center;
    font-size: 14px;
    font-weight: 600;
    color: #6b7280;
    padding: 8px 0;
}

.time-slots {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-top: 2rem;
}

.time-slot {
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    text-align: center;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
    background: white;
}

.time-slot:hover:not(.disabled) {
    border-color: #3b82f6;
    background: #eff6ff;
}

.time-slot.selected {
    background: #ddd6fe;
    border-color: #8b5cf6;
    color: #5b21b6;
}

.time-slot.disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.summary-panel {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    position: sticky;
    top: 20px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.summary-row:last-child {
    border-bottom: none;
}

.summary-label {
    color: #6b7280;
    font-size: 14px;
}

.summary-value {
    font-weight: 600;
    color: #111827;
    font-size: 14px;
    text-align: right;
}

.btn-primary {
    background: #3b82f6;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary:hover:not(:disabled) {
    background: #2563eb;
}

.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-success {
    background: #10b981;
    color: white;
    padding: 14px 32px;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
}

.btn-success:hover {
    background: #059669;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
    font-size: 14px;
}

.step-content {
    display: none;
}

.step-content.active {
    display: block;
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.quick-reserve-btn {
    background: #10b981;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.2s;
}

.quick-reserve-btn:hover {
    background: #059669;
    color: white;
}

.randevu-form-container {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

@media (max-width: 1024px) {
    .summary-panel {
        position: relative;
        top: 0;
        margin-top: 2rem;
    }
}

@media (max-width: 640px) {
    .time-slots {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .step-label {
        font-size: 12px;
    }
}
</style>

<!-- PAGE SECTİON BAŞLANGIÇ -->
<section class="page-section">
    <div class="bg-white">
        <div class="col-12 p-0 banner">
            <img src="<?php echo tema;?>/uploads/arkaplan/arkaplan20/<?php echo $arkaplan['arkaplan20'];?>" alt="<?=@$dil['randevu_baslik'];?>">
            <div class="slide-overlay"></div>
        </div>
        <div class="container banner-fix">
            <div class="row">
                <div class="col-lg-12 z-index-9">
                    <ol class="breadcrumb">
                        <li><a href="<?php echo $htc['anaurl'];?><?php echo $html;?>"> <i class="fa fa-home"></i> </a></li>						
                        <?php if($menubas['menu_isim'] != ""){?>
                        <li><a href="<?php echo($menubas['menu_url'] == "0" ? $menubas['link'] : $menubas['menu_url']);?>"><?php echo cVCLmHLxbS_ilkbuyuk($menubas['menu_isim']);?></a></li>
                        <?php }?>
                        <li><?=@$dil['randevu_baslik'];?></li>
                    </ol>
                </div>
                
                <div class="col-lg-12 z-index-9">
                    
                    <?php
                    // Başarı mesajı
                    if(isset($_SESSION['randevu_btn']) && $_SESSION['randevu_btn'] == 'yes') {
                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <strong>' . @$dil['txt555'] . '</strong> ' . @$dil['txt556'] . '
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
                        unset($_SESSION['randevu_btn']);
                    }
                    // Hata mesajı
                    elseif(isset($_SESSION['randevu_btn']) && $_SESSION['randevu_btn'] == 'no') {
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <strong>' . @$dil['txt557'] . '</strong> ' . @$dil['txt558'] . '
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
                        unset($_SESSION['randevu_btn']);
                    }
                    // Boş alan uyarısı
                    elseif(isset($_SESSION['randevu_btn']) && $_SESSION['randevu_btn'] == 'bos') {
                        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <strong>' . @$dil['txt559'] . '</strong> ' . @$dil['txt560'] . '
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
                        unset($_SESSION['randevu_btn']);
                    }
                    ?>
                    
                    <div class="randevu-container">
                        <div class="row">
                            <!-- Sol Taraf - Form -->
                            <div class="col-lg-8">
                                <!-- Adım Göstergesi -->
                                <div class="step-indicator mb-4">
                                    <?php
                                    $steps = [];
                                    if($hizmetSecimAktif && count($hizmetler) > 0) {
                                        $steps[] = ['id' => 1, 'name' => $dil['randevu_adim_hizmet']];
                                    }
                                    $steps[] = ['id' => count($steps) + 1, 'name' => $dil['randevu_adim_tarih_saat']];
                                    $steps[] = ['id' => count($steps) + 1, 'name' => $dil['randevu_adim_bilgiler']];
                                    
                                    foreach($steps as $index => $step):
                                    ?>
                                    <div class="step-item <?php echo $index == 0 ? 'active' : ''; ?>" data-step="<?php echo $step['id']; ?>">
                                        <div class="step-circle"><?php echo $step['id']; ?></div>
                                        <div class="step-label"><?php echo $step['name']; ?></div>
                                        <?php if($index < count($steps) - 1): ?>
                                        <div class="step-line"></div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Form -->
                                <div class="randevu-form-container">
                                    <form id="randevuForm" action="<?php echo $url; ?>/_class/site_islem.php" method="POST">
                                        <input type="hidden" name="randevuurl" value="<?php echo $url.'/'.$htc['randevuurl'].$html; ?>">
                                        <input type="hidden" name="kontrol" class="kontrol" value="">
                                        <input type="hidden" name="hizmet_id" id="hizmetId" value="">
                                        <input type="hidden" name="tarih" id="randevuTarih" value="">
                                        <input type="hidden" name="saat" id="randevuSaat" value="">
                                        
                                        <?php if($hizmetSecimAktif && count($hizmetler) > 0): ?>
                                        <!-- ADIM 1: Hizmet Seçimi -->
                                        <div class="step-content active" id="step1">
                                            <h2 class="h4 mb-4"><?=@$dil['randevu_hizmet_secin'];?></h2>
                                            
                                            <div class="row">
                                                <?php foreach($hizmetler as $hizmet): ?>
                                                <div class="col-md-6 mb-3">
                                                    <div class="service-card" 
                                                         data-hizmet-id="<?php echo $hizmet['id']; ?>"
                                                         data-hizmet-adi="<?php echo htmlspecialchars($hizmet['baslik']); ?>"
                                                         data-hizmet-fiyat="<?php echo number_format($hizmet['fiyat'], 2); ?>">
                                                        
                                                        <div class="service-icon">
                                                            <?php if(!empty($hizmet['ikon'])): ?>
                                                            <i class="<?php echo $hizmet['ikon']; ?> fa-2x text-primary"></i>
                                                            <?php elseif(!empty($hizmet['resim'])): ?>
                                                            <img src="<?php echo tema; ?>/uploads/randevu/<?php echo $hizmet['resim']; ?>" 
                                                                 alt="<?php echo htmlspecialchars($hizmet['baslik']); ?>"
                                                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                                                            <?php else: ?>
                                                            <i class="fas fa-star fa-2x text-primary"></i>
                                                            <?php endif; ?>
                                                        </div>
                                                        
                                                        <h3 class="h6 mb-2"><?php echo htmlspecialchars($hizmet['baslik']); ?></h3>
                                                        
                                                        <?php if(!empty($hizmet['aciklama'])): ?>
                                                        <p class="small text-muted mb-3">
                                                            <?php echo htmlspecialchars(substr(strip_tags($hizmet['aciklama']), 0, 80)); ?>...
                                                        </p>
                                                        <?php endif; ?>
                                                        
                                                        <div class="h5 text-primary mb-0">
                                                            <?php echo number_format($hizmet['fiyat'], 2); ?> ₺
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                            
                                            <div class="d-flex justify-content-end mt-4">
                                                <button type="button" class="btn-primary next-step" disabled>
                                                    <?=@$dil['randevu_devam'];?> <i class="fas fa-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php 
                                        $currentStep = 2;
                                        else: 
                                        $currentStep = 1;
                                        endif; 
                                        ?>
                                        
                                        <!-- ADIM: Tarih ve Saat Seçimi -->
                                        <div class="step-content <?php echo !$hizmetSecimAktif || count($hizmetler) == 0 ? 'active' : ''; ?>" id="step<?php echo $currentStep; ?>">
                                            <h2 class="h4 mb-4"><?=@$dil['randevu_tarih_saat_secin'];?></h2>
                                            
                                            <!-- Takvim Başlık -->
                                            <div class="mb-3">
                                                <label class="form-label"><?=@$dil['randevu_tarih_secin'];?></label>
                                            </div>
                                            
                                            <!-- Hafta Başlıkları -->
                                            <div class="calendar-header">
                                                <div class="calendar-header-item"><?=@$dil['randevu_gun_pt'];?></div>
                                                <div class="calendar-header-item"><?=@$dil['randevu_gun_sa'];?></div>
                                                <div class="calendar-header-item"><?=@$dil['randevu_gun_ca'];?></div>
                                                <div class="calendar-header-item"><?=@$dil['randevu_gun_pe'];?></div>
                                                <div class="calendar-header-item"><?=@$dil['randevu_gun_cu'];?></div>
                                                <div class="calendar-header-item"><?=@$dil['randevu_gun_ct'];?></div>
                                                <div class="calendar-header-item"><?=@$dil['randevu_gun_pa'];?></div>
                                            </div>
                                            
                                            <!-- Takvim -->
                                            <div class="calendar-grid" id="calendarGrid"></div>
                                            
                                            <!-- Saat Seçimi -->
                                            <div class="mt-4">
                                                <label class="form-label"><?=@$dil['randevu_saat_secin'];?></label>
                                                <div class="time-slots" id="timeSlots">
                                                    <div class="text-center text-muted py-4" style="grid-column: 1 / -1;">
                                                        <?=@$dil['randevu_tarih_once'];?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between mt-4">
                                                <?php if($hizmetSecimAktif && count($hizmetler) > 0): ?>
                                                <button type="button" class="btn-secondary prev-step">
                                                    <i class="fas fa-arrow-left"></i> <?=@$dil['randevu_geri'];?>
                                                </button>
                                                <?php endif; ?>
                                                <button type="button" class="btn-primary next-step ml-auto" disabled>
                                                    <?=@$dil['randevu_devam'];?> <i class="fas fa-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- ADIM: Kişisel Bilgiler -->
                                        <?php $currentStep++; ?>
                                        <div class="step-content" id="step<?php echo $currentStep; ?>">
                                            <h2 class="h4 mb-4"><?=@$dil['randevu_bilgileriniz'];?></h2>
                                            
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label"><?=@$dil['randevu_ad_soyad'];?> <span class="text-danger">*</span></label>
                                                    <input type="text" name="isim" id="isim" required class="form-input">
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label"><?=@$dil['randevu_telefon'];?></label>
                                                    <input type="tel" name="telefon" id="telefon" required class="form-input" placeholder="0555 123 45 67">
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label"><?=@$dil['randevu_eposta'];?></label>
                                                    <input type="email" name="email" id="email" required class="form-input">
                                                </div>
                                                
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label"><?=@$dil['randevu_notlar'];?></label>
                                                    <textarea name="aciklama" id="aciklama" rows="4" class="form-input" placeholder="<?=@$dil['randevu_notlar_placeholder'];?>"></textarea>
                                                </div>
                                                
                                                <div class="col-md-12 mb-3">
                                                    <label class="d-flex align-items-start" style="cursor: pointer;">
                                                        <input type="checkbox" id="kvkkCheck" required class="mt-1 mr-2">
                                                        <span class="small text-muted">
                                                            <a href="<?php echo $kvkkLink; ?>" target="_blank" class="text-primary"><?=@$dil['randevu_kvkk_okuma'];?></a> <span class="text-danger">*</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between mt-4">
                                                <button type="button" class="btn-secondary prev-step">
                                                    <i class="fas fa-arrow-left"></i> <?=@$dil['randevu_geri'];?>
                                                </button>
                                                <button style=" font-size: 14px; "  type="submit" name="randevu_btn" class="btn-success">
                                                    <i class="fas fa-check"></i> <?=@$dil['randevu_onayla'];?>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Sağ Taraf - Özet Paneli -->
                            <div class="col-lg-4">
                                <div class="summary-panel">
                                    <h3 class="h5 mb-4"><?=@$dil['randevu_ozet'];?></h3>
                                    
                                    <div id="summaryContent">
                                        <?php if($hizmetSecimAktif && count($hizmetler) > 0): ?>
                                        <div class="summary-row">
                                            <span class="summary-label"><?=@$dil['randevu_hizmet_secimi'];?></span>
                                            <span class="summary-value" id="summaryHizmet"><?=@$dil['randevu_secilmedi'];?></span>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="summary-row">
                                            <span class="summary-label"><?=@$dil['randevu_tarih'];?></span>
                                            <span class="summary-value" id="summaryTarih"><?=@$dil['randevu_secilmedi'];?></span>
                                        </div>
                                        
                                        <div class="summary-row">
                                            <span class="summary-label"><?=@$dil['randevu_saat'];?></span>
                                            <span class="summary-value" id="summarySaat"><?=@$dil['randevu_secilmedi'];?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 pt-4" style="border-top: 1px solid #f3f4f6;">
                                        <a href="javascript:void(0)" class="quick-reserve-btn w-100 justify-content-center" onclick="document.getElementById('randevuForm').scrollIntoView({behavior: 'smooth'})">
                                            <i class="fas fa-bolt"></i>
                                            <?=@$dil['randevu_hizlica'];?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = <?php echo count($steps); ?>;
    const hizmetSecimAktif = <?php echo $hizmetSecimAktif ? 'true' : 'false'; ?>;
    const hizmetSayisi = <?php echo count($hizmetler); ?>;
    
    if(!hizmetSecimAktif || hizmetSayisi === 0) {
        currentStep = 1;
    }
    
    // Adım göstergesi güncelle
    function updateStepIndicator() {
        const stepItems = document.querySelectorAll('.step-item');
        stepItems.forEach((item, index) => {
            const stepNum = index + 1;
            
            item.classList.remove('active', 'completed');
            
            if(stepNum < currentStep) {
                item.classList.add('completed');
            } else if(stepNum === currentStep) {
                item.classList.add('active');
            }
        });
    }
    
    // Adım göster
    function showStep(step) {
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.remove('active');
        });
        
        const stepContent = document.querySelector(`#step${step}`);
        if(stepContent) {
            stepContent.classList.add('active');
        }
        
        currentStep = step;
        updateStepIndicator();
        
        // Sayfayı yukarı kaydır
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
    
    // Hizmet seçimi
    if(hizmetSecimAktif && hizmetSayisi > 0) {
        console.log('Hizmet seçimi aktif, kart sayısı:', hizmetSayisi);
        const serviceCards = document.querySelectorAll('.service-card');
        console.log('Bulunan hizmet kartları:', serviceCards.length);
        
        serviceCards.forEach(card => {
            card.addEventListener('click', function(e) {
                console.log('Kart tıklandı:', this.dataset.hizmetAdi);
                
                // Tüm kartlardan selected class'ını kaldır
                document.querySelectorAll('.service-card').forEach(c => c.classList.remove('selected'));
                
                // Bu karta selected class'ı ekle
                this.classList.add('selected');
                
                // Hidden input'a ID'yi yaz
                const hizmetId = this.getAttribute('data-hizmet-id');
                const hizmetAdi = this.getAttribute('data-hizmet-adi');
                
                console.log('Seçilen hizmet ID:', hizmetId);
                console.log('Seçilen hizmet Adı:', hizmetAdi);
                
                document.getElementById('hizmetId').value = hizmetId;
                document.getElementById('summaryHizmet').textContent = hizmetAdi;
                
                // Devam et butonunu aktif et
                const nextBtn = document.querySelector('#step1 .next-step');
                if(nextBtn) {
                    nextBtn.disabled = false;
                    console.log('Devam et butonu aktif edildi');
                }
            });
        });
    } else {
        console.log('Hizmet seçimi pasif veya hizmet yok');
    }
    
    // Takvim oluştur
    function generateCalendar() {
        const grid = document.getElementById('calendarGrid');
        grid.innerHTML = '';
        
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Zamanı sıfırla
        const year = today.getFullYear();
        const month = today.getMonth();
        const todayDate = today.getDate();
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        
        // Bugünden başlayarak göster
        let startDay = today.getDay();
        let adjustedStartDay = startDay === 0 ? 6 : startDay - 1;
        
        // Eğer bugün ayın 1'i değilse, bugünün pozisyonunu hesapla
        if(todayDate > 1) {
            const todayInWeek = new Date(year, month, todayDate);
            startDay = todayInWeek.getDay();
            adjustedStartDay = startDay === 0 ? 6 : startDay - 1;
        }
        
        // Bugünden önceki günler için boşluk bırak
        for(let i = 0; i < adjustedStartDay; i++) {
            const empty = document.createElement('div');
            empty.className = 'calendar-day';
            empty.style.visibility = 'hidden';
            grid.appendChild(empty);
        }
        
        // Sadece bugün ve gelecek günleri göster
        for(let day = todayDate; day <= lastDay.getDate(); day++) {
            const dayElement = document.createElement('div');
            const currentDate = new Date(year, month, day);
            
            dayElement.className = 'calendar-day';
            dayElement.textContent = day;
            
            dayElement.addEventListener('click', function() {
                document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                this.classList.add('selected');
                
                const monthStr = (month + 1).toString().padStart(2, '0');
                const dayStr = day.toString().padStart(2, '0');
                const dateStr = `${dayStr}-${monthStr}-${year}`;
                
                document.getElementById('randevuTarih').value = dateStr;
                
                const months = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
                const days = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
                const displayDate = `${day} ${months[month]} ${year} ${days[currentDate.getDay()]}`;
                document.getElementById('summaryTarih').textContent = displayDate;
                
                generateTimeSlots();
            });
            
            grid.appendChild(dayElement);
        }
    }
    
    // Saat slotları oluştur
    function generateTimeSlots() {
        const container = document.getElementById('timeSlots');
        container.innerHTML = '';
        
        const times = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
        
        times.forEach(time => {
            const slot = document.createElement('div');
            slot.className = 'time-slot';
            slot.textContent = time;
            
            slot.addEventListener('click', function() {
                if(this.classList.contains('disabled')) return;
                
                document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                this.classList.add('selected');
                
                document.getElementById('randevuSaat').value = time;
                document.getElementById('summarySaat').textContent = time;
                
                const currentStepNum = hizmetSecimAktif && hizmetSayisi > 0 ? 2 : 1;
                if(currentStep === currentStepNum) {
                    document.querySelector(`#step${currentStep} .next-step`).disabled = false;
                }
            });
            
            container.appendChild(slot);
        });
    }
    
    // İlk takvimi oluştur
    generateCalendar();
    
    // İleri butonları
    document.querySelectorAll('.next-step').forEach(btn => {
        btn.addEventListener('click', function() {
            if(currentStep === 1 && hizmetSecimAktif && hizmetSayisi > 0) {
                if(!document.getElementById('hizmetId').value) {
                    alert(<?= json_encode($dil['txt561'] ?? '', JSON_UNESCAPED_UNICODE); ?>);
                    return;
                }
            }
            
            const dateStep = hizmetSecimAktif && hizmetSayisi > 0 ? 2 : 1;
            if(currentStep === dateStep) {
                if(!document.getElementById('randevuTarih').value) {
                    alert(<?= json_encode($dil['txt562'] ?? '', JSON_UNESCAPED_UNICODE); ?>);
                    return;
                }
                if(!document.getElementById('randevuSaat').value) {
                    alert(<?= json_encode($dil['txt563'] ?? '', JSON_UNESCAPED_UNICODE); ?>);
                    return;
                }
            }
            
            if(currentStep < totalSteps) {
                showStep(currentStep + 1);
            }
        });
    });
    
    // Geri butonları
    document.querySelectorAll('.prev-step').forEach(btn => {
        btn.addEventListener('click', function() {
            if(currentStep > 1) {
                showStep(currentStep - 1);
            }
        });
    });
    
    // Form gönderimi
    document.getElementById('randevuForm').addEventListener('submit', function(e) {
        console.log('Form submit edildi');
        
        // Tarih kontrolü
        const tarih = document.getElementById('randevuTarih').value;
        if(!tarih) {
            e.preventDefault();
            alert(<?= json_encode($dil['txt562'] ?? '', JSON_UNESCAPED_UNICODE); ?>);
            return false;
        }
        
        // Saat kontrolü
        const saat = document.getElementById('randevuSaat').value;
        if(!saat) {
            e.preventDefault();
            alert(<?= json_encode($dil['txt563'] ?? '', JSON_UNESCAPED_UNICODE); ?>);
            return false;
        }
        
        // Kişisel bilgiler kontrolü
        const isim = document.getElementById('isim').value.trim();
        const telefon = document.getElementById('telefon').value.trim();
        const email = document.getElementById('email').value.trim();
        
        if(!isim || !telefon || !email) {
            e.preventDefault();
            alert(<?= json_encode($dil['txt560'] ?? '', JSON_UNESCAPED_UNICODE); ?>);
            return false;
        }
        
        // Email kontrolü
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(!emailRegex.test(email)) {
            e.preventDefault();
            alert(<?= json_encode($dil['txt564'] ?? '', JSON_UNESCAPED_UNICODE); ?>);
            return false;
        }
        
        // KVKK kontrolü
        const kvkkCheck = document.getElementById('kvkkCheck');
        if(!kvkkCheck.checked) {
            e.preventDefault();
            alert(<?= json_encode($dil['txt565'] ?? '', JSON_UNESCAPED_UNICODE); ?>);
            return false;
        }
        
        console.log('Form validasyonu başarılı, gönderiliyor...');
        // Form gönderilecek
        return true;
    });
});
</script>
