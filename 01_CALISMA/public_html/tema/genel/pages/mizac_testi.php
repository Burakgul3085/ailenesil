<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
// Testi çek
$testSorgu = $db->prepare("SELECT * FROM testler WHERE seo = ? AND durum = ? AND dil = ?");
$testSorgu->execute(array('mizac-testi', '1', $_SESSION['k_dil']));

if(!$testSorgu->rowCount()){
    header("Location:".$url."/404.html");
    exit;
}

$test = $testSorgu->fetch(PDO::FETCH_ASSOC);

// Enneagram tip bilgilerini çek
$mizacSorgu = $db->prepare("SELECT * FROM test_mizac_tipleri WHERE test_id = ? ORDER BY mizac_tipi ASC");
$mizacSorgu->execute(array($test['id']));
$mizacTipleri = $mizacSorgu->fetchAll(PDO::FETCH_ASSOC);

// Kutu verilerini DB'den çek
$kutuSorgu = $db->prepare("SELECT * FROM test_kutulari WHERE test_id = ? AND durum = 1 ORDER BY adim ASC, sira ASC");
$kutuSorgu->execute(array($test['id']));
$kutular = $kutuSorgu->fetchAll(PDO::FETCH_ASSOC);

// Adımlara göre grupla
$adimlar = array();
$boxContentJS = array();
$letterToTypeJS = array();
foreach($kutular as $kutu) {
    $adimlar[$kutu['adim']][] = $kutu;
    $maddelerArr = array_filter(array_map('trim', explode("\n", $kutu['maddeler'])));
    $key = !empty($kutu['harf']) ? $kutu['harf'] : strval($kutu['id']);
    $boxContentJS[$key] = array_values($maddelerArr);
    $letterToTypeJS[$key] = intval($kutu['enneagram_tipi']);
}
?>

<style>
/* Banner */
.mt-banner-inner {
    position: relative;
    height: 340px;
    overflow: hidden;
    background: linear-gradient(135deg, #b20101 0%, #e84343 100%);
}

.mt-banner-img {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    object-fit: cover;
}

.mt-banner-overlay {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: linear-gradient(135deg, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.25) 100%);
    z-index: 1;
}

.mt-banner-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 340px;
    color: #fff;
}

.mt-banner-content h1 {
    font-size: 42px;
    font-weight: 700;
    margin-bottom: 12px;
    color: #fff;
}

.mt-banner-content p {
    font-size: 18px;
    opacity: 0.9;
    max-width: 600px;
    line-height: 1.6;
    color: #fff;
}

@media (max-width: 768px) {
    .mt-banner-inner { height: 240px; }
    .mt-banner-content { height: 240px; }
    .mt-banner-content h1 { font-size: 28px; }
    .mt-banner-content p { font-size: 15px; }
}

/* Genel Quiz Wrapper */
.mt-quiz-wrapper {
    max-width: 960px;
    margin: 0 auto;
    padding: 40px 20px;
    min-height: 50vh;
}

/* Form */
.mt-form h2 {
    font-size: 28px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 8px;
    text-align: center;
}

.mt-form .mt-form-subtitle {
    font-size: 16px;
    color: #888;
    text-align: center;
    margin-bottom: 32px;
}

.mt-form-fields {
    max-width: 480px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.mt-form-field {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.mt-form-field label {
    font-size: 14px;
    font-weight: 600;
    color: #444;
}

.mt-form-field input {
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 16px;
    color: #333;
    transition: border-color 0.2s;
    outline: none;
}

.mt-form-field input:focus {
    border-color: var(--color-primary, #b20101);
}

.mt-form-field input.mt-input-error {
    border-color: #e74c3c;
}

.mt-btn-start {
    display: block;
    width: 100%;
    max-width: 480px;
    margin: 24px auto 0;
    padding: 16px;
    background: var(--color-primary, #b20101);
    color: #fff;
    font-size: 18px;
    font-weight: 600;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
}

.mt-btn-start:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(178, 1, 1, 0.3);
}

/* Step */
.mt-step {
    animation: mtFadeIn 0.4s ease;
}

@keyframes mtFadeIn {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
}

.mt-step h2 {
    font-size: 26px;
    font-weight: 700;
    color: #1a1a1a;
    text-align: center;
    margin-bottom: 6px;
}

.mt-step-info {
    font-size: 14px;
    font-weight: 600;
    color: var(--color-primary, #b20101);
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 32px;
}

.mt-step-progress {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 32px;
}

.mt-step-dot {
    width: 40px;
    height: 5px;
    border-radius: 3px;
    background: #e0e0e0;
    transition: background 0.3s;
}

.mt-step-dot.active {
    background: var(--color-primary, #b20101);
}

.mt-step-dot.done {
    background: #4caf50;
}

/* Boxes */
.mt-boxes {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.mt-box {
    position: relative;
    background: #fff;
    border: 2px solid #e0e0e0;
    border-radius: 16px;
    padding: 28px 24px 60px;
    cursor: pointer;
    transition: all 0.25s ease;
}

.mt-box:hover {
    border-color: var(--color-primary, #b20101);
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transform: translateY(-3px);
}

.mt-box.selected {
    border-color: var(--color-primary, #b20101);
    background: rgba(178, 1, 1, 0.03);
    box-shadow: 0 4px 20px rgba(178, 1, 1, 0.15);
}

.mt-box ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.mt-box ul li {
    font-size: 15px;
    color: #444;
    line-height: 1.5;
    padding-left: 0;
}

.mt-box-letter {
    position: absolute;
    bottom: 16px;
    left: 24px;
    font-size: 28px;
    font-weight: 800;
    color: #ddd;
    transition: color 0.25s;
}

.mt-box:hover .mt-box-letter,
.mt-box.selected .mt-box-letter {
    color: var(--color-primary, #b20101);
}

/* Result */
.mt-result {
    animation: mtFadeIn 0.5s ease;
}

.mt-result-label {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #888;
    margin-bottom: 8px;
    text-align: center;
}

.mt-result-tag {
    display: block;
    text-align: center;
    margin-bottom: 16px;
}

.mt-result-tag span {
    display: inline-block;
    padding: 6px 20px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    color: #fff;
}

.mt-result-title {
    font-size: 32px;
    font-weight: 700;
    color: #1a1a1a;
    text-align: center;
    margin-bottom: 20px;
}

.mt-result-desc {
    font-size: 16px;
    color: #555;
    line-height: 1.8;
    text-align: center;
    max-width: 680px;
    margin: 0 auto 16px;
}

.mt-result-note {
    font-size: 14px;
    color: #aaa;
    font-style: italic;
    text-align: center;
    margin-bottom: 32px;
}

.mt-result-type-icon {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin: 0 auto 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 42px;
    font-weight: 800;
    color: #fff;
}

.mt-result-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

.mt-btn-restart {
    padding: 12px 28px;
    background: #fff;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    color: #333;
    cursor: pointer;
    transition: all 0.2s;
}

.mt-btn-restart:hover {
    border-color: #ccc;
    background: #f8f8f8;
}

/* Back Button */
.mt-btn-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: none;
    border: 2px solid #ddd;
    border-radius: 30px;
    padding: 8px 20px;
    font-size: 14px;
    color: #666;
    cursor: pointer;
    margin-bottom: 18px;
    transition: all 0.2s;
}
.mt-btn-back:hover {
    border-color: var(--color-primary, #b20101);
    color: var(--color-primary, #b20101);
}
.mt-btn-back i {
    font-size: 12px;
}

/* Intro & Footer Text */
.mt-intro-text,
.mt-result-footer-text {
    max-width: 680px;
    margin: 28px auto 0;
    padding: 20px 24px;
    background: #f8f8f8;
    border-radius: 12px;
    border-left: 4px solid var(--color-primary, #b20101);
}

.mt-intro-text p,
.mt-result-footer-text p {
    font-size: 15px;
    color: #555;
    line-height: 1.7;
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .mt-quiz-wrapper {
        padding: 24px 15px;
    }
    .mt-boxes {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .mt-box {
        padding: 24px 20px 52px;
    }
    .mt-step h2 {
        font-size: 22px;
    }
    .mt-result-title {
        font-size: 24px;
    }
    .mt-form h2 {
        font-size: 24px;
    }
}
</style>

<!-- Banner -->
<section class="mt-banner">
    <div class="mt-banner-inner">
        <img src="<?php echo tema; ?>/uploads/mizac-testi-banner.jpg" alt="Mizaç Testi" class="mt-banner-img" onerror="this.parentElement.style.background='linear-gradient(135deg, #b20101 0%, #e84343 100%)'; this.style.display='none';">
        <div class="mt-banner-overlay"></div>
        <div class="container position-relative" style="z-index:2;">
            <div class="mt-banner-content">
                <h1><?php echo htmlspecialchars($test['adi']); ?></h1>
                <p><?php echo htmlspecialchars($test['aciklama']); ?></p>
            </div>
        </div>
    </div>
</section>

<section style="padding-bottom: 60px; background: #fff; min-height: 50vh;">
    <div class="container">
        <div class="mt-quiz-wrapper">

            <!-- Form: Veli/Öğrenci Bilgileri -->
            <div class="mt-form" id="mtForm">
                <h2>Bilgilerinizi Giriniz</h2>
                <?php if(!empty($test['giris_metni'])): ?>
                <div class="mt-intro-text" style="margin-bottom: 20px;">
                    <p><?php echo nl2br(htmlspecialchars($test['giris_metni'])); ?></p>
                </div>
                <?php endif; ?>
                <!-- <p class="mt-form-subtitle">Teste başlamadan önce aşağıdaki bilgileri doldurunuz.</p> -->
                <div class="mt-form-fields">
                    <div class="mt-form-field">
                        <label>İsim Soyad</label>
                        <input type="text" id="fVeliAd" placeholder="İsim soyad" required>
                    </div>
                
                    <div class="mt-form-field">
                        <label>Yaş</label>
                        <input type="number" id="fOgrenciYas" placeholder="Yaş" min="3" max="25" required>
                    </div>
                    <div class="mt-form-field">
                        <label>Telefon</label>
                        <input type="tel" id="fTelefon" placeholder="05xx xxx xx xx" required>
                    </div>
                    <div class="mt-form-field">
                        <label>E-posta</label>
                        <input type="email" id="fEmail" placeholder="ornek@mail.com" required>
                    </div>
                </div>
                <button class="mt-btn-start" onclick="submitForm()">Teste Başla</button>
            </div>

            <?php
            // Adım 1, 2, 3'ü DB'den dinamik oluştur
            $toplamAdim = count($adimlar);
            for($adim = 1; $adim <= 3; $adim++):
                if(!isset($adimlar[$adim])) continue;
                $kutularAdim = $adimlar[$adim];

                // Progress dot durumları
                $dots = '';
                for($d = 1; $d <= 4; $d++) {
                    if($d < $adim) $dots .= '<div class="mt-step-dot done"></div>';
                    elseif($d == $adim) $dots .= '<div class="mt-step-dot active"></div>';
                    else $dots .= '<div class="mt-step-dot"></div>';
                }
            ?>
            <div class="mt-step" id="mtStep<?php echo $adim; ?>" style="display:none;">
                <button class="mt-btn-back" onclick="goBack(<?php echo $adim; ?>)"><i class="fas fa-arrow-left"></i> Geri</button>
                <div class="mt-step-progress"><?php echo $dots; ?></div>
                <p class="mt-step-info">Adım <?php echo $adim; ?> / 4</p>
                <h2>Size en uygun tanımı seçiniz</h2>
                <br>
                <div class="mt-boxes">
                    <?php foreach($kutularAdim as $kutu):
                        $maddeler = array_filter(array_map('trim', explode("\n", $kutu['maddeler'])));
                    ?>
                    <div class="mt-box" onclick="pickBox(<?php echo $adim; ?>, '<?php echo !empty($kutu['harf']) ? $kutu['harf'] : $kutu['id']; ?>', this)">
                        <ul>
                            <?php foreach($maddeler as $madde): ?>
                            <li><?php echo htmlspecialchars($madde); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php if(!empty($kutu['harf'])): ?>
                        <div class="mt-box-letter"><?php echo $kutu['harf']; ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endfor; ?>

            <!-- Adım 4: Önceki 3 seçimden final -->
            <div class="mt-step" id="mtStep4" style="display:none;">
                <button class="mt-btn-back" onclick="goBack(4)"><i class="fas fa-arrow-left"></i> Geri</button>
                <div class="mt-step-progress">
                    <div class="mt-step-dot done"></div>
                    <div class="mt-step-dot done"></div>
                    <div class="mt-step-dot done"></div>
                    <div class="mt-step-dot active"></div>
                </div>
                <p class="mt-step-info">Adım 4 / 4</p>
                <h2>Seçtiğiniz tanımlardan size en uygun olanı seçiniz</h2>
                <br>
                <div class="mt-boxes" id="mtStep4Boxes"></div>
            </div>

            <!-- Sonuç -->
            <div class="mt-result" id="mtResult" style="display:none;">
                <div class="mt-result-label">SONUÇ</div>
                <div class="mt-result-type-icon" id="mtResultIcon"></div>
                <div class="mt-result-tag" id="mtResultTag"></div>
                <h2 class="mt-result-title" id="mtResultTitle"></h2>
                <p class="mt-result-desc" id="mtResultDesc"></p>
                <p class="mt-result-note">Bu test genel bir değerlendirme sunar. Detaylı analiz için bir uzmana danışınız.</p>
                <?php if(!empty($test['sonuc_metni'])): ?>
                <div class="mt-result-footer-text">
                    <p><?php echo nl2br(htmlspecialchars($test['sonuc_metni'])); ?></p>
                </div>
                <?php endif; ?>
                <div class="mt-result-actions">
                    <button class="mt-btn-restart" onclick="restartQuiz()"><i class="fas fa-redo"></i> Başa Dön</button>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
var formData = {};
var picks = [];
var mizacData = <?php echo json_encode($mizacTipleri, JSON_UNESCAPED_UNICODE); ?>;

// Harf -> Enneagram Tip eşleşmesi (DB'den)
var letterToType = <?php echo json_encode($letterToTypeJS); ?>;

// Her harfin kutu içeriği - step 4 için (DB'den)
var boxContent = <?php echo json_encode($boxContentJS, JSON_UNESCAPED_UNICODE); ?>;

function goBack(currentStep) {
    document.getElementById('mtStep' + currentStep).style.display = 'none';

    if(currentStep === 1) {
        // İlk adımdan forma dön
        document.getElementById('mtForm').style.display = 'block';
    } else {
        // Önceki adıma dön
        var prevStep = currentStep - 1;
        picks[prevStep - 1] = undefined; // Önceki seçimi sıfırla
        var prevEl = document.getElementById('mtStep' + prevStep);
        // Seçili kutuyu temizle
        prevEl.querySelectorAll('.mt-box').forEach(function(b) { b.classList.remove('selected'); });
        prevEl.style.display = 'block';
    }

    window.scrollTo({top: document.querySelector('.mt-quiz-wrapper').offsetTop - 20, behavior: 'smooth'});
}

function submitForm() {
    var fields = [
        {id: 'fVeliAd', name: 'veli_ad'},
        {id: 'fOgrenciYas', name: 'ogrenci_yas'},
        {id: 'fTelefon', name: 'telefon'},
        {id: 'fEmail', name: 'email'}
    ];

    var valid = true;
    fields.forEach(function(f) {
        var el = document.getElementById(f.id);
        el.classList.remove('mt-input-error');
        if(!el.value.trim()) {
            el.classList.add('mt-input-error');
            valid = false;
        }
        formData[f.name] = el.value.trim();
    });

    if(!valid) return;

    document.getElementById('mtForm').style.display = 'none';
    document.getElementById('mtStep1').style.display = 'block';
    window.scrollTo({top: document.querySelector('.mt-quiz-wrapper').offsetTop - 20, behavior: 'smooth'});
}

function pickBox(step, letter, el) {
    var boxes = el.parentElement.querySelectorAll('.mt-box');
    boxes.forEach(function(b) { b.classList.remove('selected'); });
    el.classList.add('selected');

    picks[step - 1] = letter;

    setTimeout(function() {
        document.getElementById('mtStep' + step).style.display = 'none';

        if(step < 3) {
            document.getElementById('mtStep' + (step + 1)).style.display = 'block';
        } else if(step === 3) {
            buildStep4();
            document.getElementById('mtStep4').style.display = 'block';
        } else {
            showResult();
        }

        window.scrollTo({top: document.querySelector('.mt-quiz-wrapper').offsetTop - 20, behavior: 'smooth'});
    }, 400);
}

function buildStep4() {
    var container = document.getElementById('mtStep4Boxes');
    container.innerHTML = '';
    for(var i = 0; i < 3; i++) {
        var letter = picks[i];
        var items = boxContent[letter] || [];
        var html = '<div class="mt-box" onclick="pickBox(4, \'' + letter + '\', this)">';
        html += '<ul>';
        for(var j = 0; j < items.length; j++) {
            html += '<li>' + items[j] + '</li>';
        }
        html += '</ul>';
        html += '<div class="mt-box-letter">' + letter + '</div>';
        html += '</div>';
        container.innerHTML += html;
    }
}

function calculateResult() {
    var finalPick = picks[3];
    return letterToType[finalPick];
}

function showResult() {
    var typeNum = calculateResult();

    document.getElementById('mtResult').style.display = 'block';

    var typeInfo = null;
    for(var i = 0; i < mizacData.length; i++) {
        if(parseInt(mizacData[i].mizac_tipi) === typeNum) {
            typeInfo = mizacData[i];
            break;
        }
    }

    if(typeInfo) {
        document.getElementById('mtResultIcon').textContent = typeNum;
        document.getElementById('mtResultIcon').style.background = typeInfo.renk;
        document.getElementById('mtResultTag').innerHTML = '<span style="background:' + typeInfo.renk + '">' + (typeInfo.etiket || '') + '</span>';
        document.getElementById('mtResultTitle').textContent = typeInfo.baslik || '';
        document.getElementById('mtResultDesc').textContent = typeInfo.aciklama || '';
    }

    saveResult(typeNum);
}

function saveResult(typeNum) {
    var params = 'test_id=<?php echo intval($test['id']); ?>';
    params += '&dominant=' + typeNum;
    params += '&sonuc=' + encodeURIComponent(JSON.stringify({picks: picks, type: typeNum}));
    params += '&veli_ad=' + encodeURIComponent(formData.veli_ad || '');
    params += '&ogrenci_yas=' + encodeURIComponent(formData.ogrenci_yas || '');
    params += '&telefon=' + encodeURIComponent(formData.telefon || '');
    params += '&email=' + encodeURIComponent(formData.email || '');

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo $url; ?>/_class/test_sonuc_kaydet.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(params);
}

function restartQuiz() {
    picks = [];
    formData = {};

    document.querySelectorAll('.mt-box').forEach(function(b) { b.classList.remove('selected'); });
    document.querySelectorAll('.mt-form-field input').forEach(function(inp) {
        inp.value = '';
        inp.classList.remove('mt-input-error');
    });

    document.getElementById('mtResult').style.display = 'none';
    document.getElementById('mtStep1').style.display = 'none';
    document.getElementById('mtStep2').style.display = 'none';
    document.getElementById('mtStep3').style.display = 'none';
    document.getElementById('mtStep4').style.display = 'none';
    document.getElementById('mtForm').style.display = 'block';

    window.scrollTo({top: document.querySelector('.mt-quiz-wrapper').offsetTop - 20, behavior: 'smooth'});
}
</script>