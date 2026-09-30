<?php echo !defined("GUVENLIK") ? die("Güvenlik Hatası!") : null; ?>
<style>
.gradient-header {
    background: linear-gradient(135deg, #818181 0%, #343437 100%) !important;
    border: none;
    padding: 1.5rem;
}
.number-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #007bff 0%, rgb(155 155 155) 100%) !important;
    color: white;
    border-radius: 10px;
    font-weight: 700;
    font-size: 18px;
    box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
}
    </style>

<!-- Content Wrapper START -->
<div class="main-content">
    <div class="container-fluid">


        <div class="row">
            <div class="col-lg-12">
                <div class="card modern-card">
                    <div class="card-header gradient-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1 text-white">
                                    <i class="mdi mdi-view-dashboard"></i> Alan Sıralama Yönetimi
                                </h4>
                                <p class="text-white-50 mb-0 small">Alanları sürükleyip bırakarak anasayfa sıralamasını düzenleyin</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info-custom mb-4">
                            <div class="d-flex align-items-center">
                                <div class="alert-content">
                                    <strong>Nasıl Kullanılır?</strong>
                                    <p class="mb-0">Her satırın solundaki <i class="ti-menu"></i> ikonunu tutup sürükleyerek alanların sırasını değiştirebilirsiniz. Bıraktığınız anda değişiklik otomatik olarak kaydedilir.</p>
                                </div>
                            </div>
                        </div>

                        <div id="sortable-list" class="sortable-container">
                            <?php
                            // Anasayfa alanlarını çek
                            $Sorgu = $db->prepare("SELECT * FROM anasayfa_alanlar ORDER BY sira ASC");
                            $Sorgu->execute();
                            $alanlar = $Sorgu->fetchAll(PDO::FETCH_ASSOC);
                            
                            if($Sorgu->rowCount() > 0):
                                foreach($alanlar as $index => $alan):
                                    $iconMap = [
                                        'alan1' => 'mdi-text',
                                        'alan2' => 'mdi-view-carousel',
                                        'alan3' => 'mdi-newspaper',
                                        'alan5' => 'mdi-bullhorn',
                                        'alan6' => 'mdi-gavel',
                                        'alan10' => 'mdi-image-multiple',
                                        'alan11' => 'mdi-menu',
                                        'alan12' => 'mdi-gavel',
                                        'alan13' => 'mdi-briefcase',
                                        'alan14' => 'mdi-video',
                                        'alan15' => 'mdi-camera',
                                        'alan16' => 'mdi-calendar-star',
                                        'alan17' => 'mdi-email',
                                        'alan18' => 'mdi-map',
                                        'alan27' => 'mdi-chart-line',
                                        'alan28' => 'mdi-school',
                                        'alan29' => 'mdi-heart',
                                        'alan30' => 'mdi-instagram'
                                    ];
                                    $icon = $iconMap[$alan['alan_kodu']] ?? 'mdi-cube';
                                    
                                    $colorMap = [
                                        'alan1' => 'primary',
                                        'alan2' => 'danger',
                                        'alan3' => 'info',
                                        'alan5' => 'warning',
                                        'alan6' => 'secondary',
                                        'alan10' => 'success',
                                        'alan11' => 'primary',
                                        'alan12' => 'info',
                                        'alan13' => 'danger',
                                        'alan14' => 'warning',
                                        'alan15' => 'success',
                                        'alan16' => 'primary',
                                        'alan17' => 'info',
                                        'alan18' => 'danger',
                                        'alan27' => 'warning',
                                        'alan28' => 'success',
                                        'alan29' => 'danger',
                                        'alan30' => 'info'
                                    ];
                                    $color = $colorMap[$alan['alan_kodu']] ?? 'secondary';
                            ?>
                            <div class="sortable-item" data-id="<?php echo $alan['id']; ?>" data-sira="<?php echo $alan['sira']; ?>">
                                <div class="modern-sort-card">
                                    <div class="sort-number">
                                        <span class="number-badge"><?php echo $alan['sira']; ?></span>
                                    </div>
                                    <div class="drag-indicator">
                                        <i class="ti-menu drag-handle"></i>
                                        <div class="drag-dots">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </div>
                                    <div class="alan-icon">
                                        <div class="icon-circle bg-<?php echo $color; ?>">
                                            <i class="mdi <?php echo $icon; ?>"></i>
                                        </div>
                                    </div>
                                    <div class="alan-info">
                                        <div class="alan-header">
                                            <h5 class="alan-title"><?php echo $alan['alan_adi']; ?></h5>
                                        </div>
                                        <p class="alan-desc"><?php echo $alan['aciklama']; ?></p>
                                    </div>
                                    <div class="alan-status">
                                        <div class="status-badge <?php echo $alan['durum'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                                            <i class="mdi <?php echo $alan['durum'] == 1 ? 'mdi-check-circle' : 'mdi-close-circle'; ?>"></i>
                                            <span><?php echo $alan['durum'] == 1 ? 'Aktif' : 'Pasif'; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                endforeach;
                            else:
                            ?>
                            <div class="alert alert-warning-custom">
                                <i class="mdi mdi-alert"></i>
                                <strong>Uyarı!</strong> Henüz alan tanımlanmamış. Lütfen <code>SQL_ANASAYFA_ALAN_SIRALAMA.sql</code> dosyasını çalıştırın.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sortable.js -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var sortableList = document.getElementById('sortable-list');
    
    if(sortableList) {
        var sortable = new Sortable(sortableList, {
            animation: 250,
            easing: "cubic-bezier(0.4, 0.0, 0.2, 1)",
            handle: '.drag-indicator',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            forceFallback: false,
            fallbackTolerance: 3,
            
            onStart: function(evt) {
                // Sürüklenme başladığında
                document.body.classList.add('is-dragging');
                evt.item.classList.add('dragging-item');
            },
            
            onEnd: function(evt) {
                // Sürüklenme bittiğinde
                document.body.classList.remove('is-dragging');
                evt.item.classList.remove('dragging-item');
                
                // Yeni sıralamayı al
                var items = sortableList.querySelectorAll('.sortable-item');
                var newOrder = [];
                
                items.forEach(function(item, index) {
                    newOrder.push({
                        id: item.getAttribute('data-id'),
                        sira: index + 1
                    });
                    
                    // Sıra numarasını hemen güncelle
                    var numberBadge = item.querySelector('.number-badge');
                    if(numberBadge) {
                        numberBadge.textContent = index + 1;
                        numberBadge.classList.add('pulse-animation');
                        setTimeout(function() {
                            numberBadge.classList.remove('pulse-animation');
                        }, 600);
                    }
                });
                
                // Kaydetme loading göster
                showToast('info', 'Kaydediliyor...', 'Sıralama kaydediliyor...');
                
                // AJAX ile kaydet
                fetch('data/anasayfa_alan_siralama_kaydet.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(newOrder)
                })
                .then(response => {
                    if(!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response:', data);
                    if(data.success) {
                        // Başarılı mesajı göster - Toast.js kullan
                        showToast('success', 'Başarılı!', 'Sıralama başarıyla kaydedildi.');
                    } else {
                        showToast('error', 'Hata!', data.message || 'Sıralama kaydedilemedi.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Hata!', 'Bir hata oluştu: ' + error.message);
                });
            },
            
            onMove: function(evt) {
                // Taşınma sırasında hedef konumu göster
                var targetIndex = evt.to.children.length;
                return true;
            }
        });
    }
});

function showToast(type, heading, text) {
    // Toast.js fonksiyonu (fonksiyon.php'deki gibi)
    if(typeof $ !== 'undefined' && $.toast) {
        var iconType = type === 'error' ? 'error' : (type === 'info' ? 'info' : (type === 'warning' ? 'warning' : 'success'));
        
        $.toast({
            heading: heading,
            text: text,
            showHideTransition: 'slide',
            icon: iconType,
            loaderBg: '#fff',
            position: 'top-right',
            hideAfter: 3000
        });
    } else {
        alert(heading + '\n' + text);
    }
}
</script>

<style>
/* ========== Modern Card Styling ========== */
.modern-card {
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border-radius: 12px;
    overflow: hidden;
}

.gradient-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 1.5rem;
}

/* ========== Custom Alerts ========== */
.alert-info-custom {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 2px solid #2196f3;
    border-radius: 10px;
    padding: 1rem;
}

.alert-info-custom .alert-icon {
    width: 50px;
    height: 50px;
    background: #2196f3;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.alert-info-custom .alert-icon i {
    font-size: 24px;
    color: white;
}

.alert-info-custom .alert-content strong {
    color: #1565c0;
    display: block;
    margin-bottom: 0.25rem;
}

.alert-info-custom .alert-content p {
    color: #0d47a1;
    font-size: 14px;
}

.alert-warning-custom {
    background: #fff3cd;
    border: 2px solid #ffc107;
    border-radius: 10px;
    padding: 1.5rem;
    text-align: center;
}

/* ========== Sortable Container ========== */
.sortable-container {
    position: relative;
    min-height: 100px;
}

.sortable-item {
    position: relative;
    margin-bottom: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
}

/* ========== Modern Sort Card ========== */
.modern-sort-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: #ffffff;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1.25rem;
    transition: all 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.modern-sort-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.modern-sort-card:hover {
    border-color: #667eea;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
    transform: translateX(4px);
}

.modern-sort-card:hover::before {
    transform: scaleY(1);
}

/* ========== Sort Number ========== */
.sort-number {
    flex-shrink: 0;
}

.number-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    font-weight: 700;
    font-size: 18px;
    box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
}

/* ========== Drag Indicator ========== */
.drag-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: grab;
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    user-select: none;
    flex-shrink: 0;
}

.drag-indicator:hover {
    background: #f8f9fa;
}

.drag-indicator:active {
    cursor: grabbing;
}

.drag-handle {
    font-size: 24px;
    color: #6c757d;
    margin-bottom: 4px;
}

.drag-dots {
    display: flex;
    gap: 3px;
}

.drag-dots span {
    width: 4px;
    height: 4px;
    background: #adb5bd;
    border-radius: 50%;
    display: block;
}

.drag-indicator:hover .drag-handle,
.drag-indicator:hover .drag-dots span {
    color: #495057;
    background: #495057;
}

/* ========== Alan Icon ========== */
.alan-icon {
    flex-shrink: 0;
}

.icon-circle {
    width: 55px;
    height: 55px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.icon-circle i {
    font-size: 28px;
    color: white;
}

.icon-circle.bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.icon-circle.bg-success { background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%); }
.icon-circle.bg-danger { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
.icon-circle.bg-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.icon-circle.bg-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.icon-circle.bg-secondary { background: linear-gradient(135deg, #868f96 0%, #596164 100%); }

/* ========== Alan Info ========== */
.alan-info {
    flex: 1;
    min-width: 0;
}

.alan-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.alan-title {
    font-size: 16px;
    font-weight: 600;
    color: #212529;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.alan-kod {
    background: #e9ecef;
    color: #495057;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    font-family: 'Courier New', monospace;
}

.alan-desc {
    font-size: 13px;
    color: #6c757d;
    margin: 0;
    line-height: 1.4;
}

/* ========== Alan Status ========== */
.alan-status {
    flex-shrink: 0;
}

.status-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
}

.status-badge i {
    font-size: 18px;
}

.status-active {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-inactive {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* ========== Dragging States ========== */
.sortable-ghost {
    opacity: 0;
}

.sortable-chosen {
    opacity: 1;
}

.sortable-drag {
    opacity: 0.9;
    transform: rotate(2deg) scale(1.05);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2) !important;
}

.dragging-item .modern-sort-card {
    background: #f8f9fa;
    border-color: #667eea;
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
}

/* Drop zone indicator */
.is-dragging .sortable-item:not(.dragging-item) {
    position: relative;
}

.is-dragging .sortable-item:not(.dragging-item)::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    top: -6px;
    height: 4px;
    background: transparent;
    border-radius: 2px;
    transition: all 0.2s ease;
}

.is-dragging .sortable-item:not(.dragging-item):hover::after {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.5);
}

.is-dragging .sortable-item:not(.dragging-item) .modern-sort-card {
    opacity: 0.7;
}

.is-dragging .sortable-item:not(.dragging-item):hover .modern-sort-card {
    opacity: 1;
    border-color: #667eea;
    background: #f8f9fa;
}

/* ========== Animations ========== */
@keyframes pulseAnimation {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); }
}

.pulse-animation {
    animation: pulseAnimation 0.6s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.sortable-item {
    animation: slideIn 0.4s ease;
}

/* Dragging cursor */
.is-dragging {
    cursor: grabbing !important;
}

.is-dragging * {
    cursor: grabbing !important;
}

/* ========== Responsive ========== */
@media (max-width: 992px) {
    .modern-sort-card {
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .sort-number,
    .drag-indicator {
        order: 1;
    }
    
    .alan-icon {
        order: 2;
    }
    
    .alan-info {
        order: 3;
        flex-basis: 100%;
    }
    
    .alan-status {
        order: 4;
    }
    
    .alan-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}

@media (max-width: 576px) {
    .modern-sort-card {
        padding: 1rem;
    }
    
    .number-badge {
        width: 35px;
        height: 35px;
        font-size: 16px;
    }
    
    .icon-circle {
        width: 45px;
        height: 45px;
    }
    
    .icon-circle i {
        font-size: 22px;
    }
    
    .alan-title {
        font-size: 14px;
    }
    
    .alan-desc {
        font-size: 12px;
    }
}

/* ========== Visual Feedback ========== */
.modern-sort-card {
    position: relative;
}

.modern-sort-card::after {
    content: '';
    position: absolute;
    inset: 0;
    border: 3px dashed #667eea;
    border-radius: 12px;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.is-dragging .sortable-item:hover .modern-sort-card::after {
    opacity: 0.5;
}

/* Drop zone highlight */
.drop-zone-indicator {
    position: absolute;
    left: 0;
    right: 0;
    height: 50px;
    background: linear-gradient(90deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border: 2px dashed #667eea;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    pointer-events: none;
}

.drop-zone-indicator::before {
    content: 'Buraya bırak';
    color: #667eea;
    font-weight: 600;
    font-size: 14px;
}

.is-dragging .sortable-item:hover::before {
    content: '';
    position: absolute;
    left: 50%;
    top: -15px;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-top: 12px solid #667eea;
    z-index: 10;
    animation: bounceArrow 0.6s ease infinite;
}

@keyframes bounceArrow {
    0%, 100% { top: -15px; }
    50% { top: -20px; }
}
</style>

