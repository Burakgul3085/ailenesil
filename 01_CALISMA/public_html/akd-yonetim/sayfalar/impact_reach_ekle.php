<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php
if(isset($_GET['islem'])=="duzenle")
{
	$durum = "duzenle" ;
	$Sorgu = $db->prepare("SELECT * FROM impact_reach WHERE id = ?");
	$Sorgu->execute(array($_GET['id']));
	if($Sorgu->rowCount())
	{
		$Sonuc = $Sorgu->fetch(PDO::FETCH_ASSOC);
	}
	else
	{
		header("Location:".$url."/404.html");
		exit;
	}
}
?>
<?php // @ioncube.dk cemetery("sha256", "TsFxypqBbULWa0xu3bdZHhF43JhKfTh0") -> "2b9d27fb4ae9399e62053806d436a7fd7b89b47f65a191e843daf4ed24edce6c" RANDOM
 cemetery_f(); ?>
<div class="page-header">
	<div class="page-title mt-0 mb-0">
		<h3><?=@$admindil['txt221'];?></h3>
		<div class="crumbs">
			<ul id="breadcrumbs" class="breadcrumb">
				<li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
				<li><a href="<?php echo $sayfalink;?>">İstatistik</a></li>
				<li class="active"><a href="<?php echo $sayfalink;?>"><?=@$admindil['txt221'];?></a></li>
			</ul>
		</div>
	</div>
</div>
<link rel="stylesheet" href="assets/css/modern_forms.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<div class="modern-form-container">
	<div class="modern-form-card">
		<div class="modern-form-header"> 
			<h4>
				<i class="fas fa-chart-bar"></i>
				<?=@$admindil['txt221'];?>
			</h4>
		</div>
		
		<form method="post" action="../_class/yonetim_islem.php" enctype="multipart/form-data">
			<input type="hidden" name="id" value="<?php echo isset($Sonuc['id']) ? $Sonuc['id'] : ''; ?>">
			<input type="hidden" name="dil" value="<?php echo $_SESSION['admin_dil']; ?>">
			
			<div class="modern-form-body">
				<!-- Temel Bilgiler -->
				<div class="modern-form-section">
					<div class="modern-form-section-title">
						<i class="fas fa-info-circle"></i>
						Temel Bilgiler
					</div>
					<div class="modern-form-grid">
						<div class="modern-form-group">
							<label for="sira">Sıra</label>
							<input type="number" class="form-control form-control-sm" min="0" name="sira" id="sira" value="<?php echo(isset($_GET['islem'])=="duzenle" ? $Sonuc['sira'] : '');?>" />
						</div>
						<div class="modern-form-group">
							<label for="baslik">Başlık <span class="text-danger">*</span></label>
							<input type="text" class="form-control form-control-sm" name="baslik" id="baslik" placeholder="Örn: Üye Sayısı" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['baslik']) : '');?>" required />
						</div>
						<div class="modern-form-group">
							<label for="sayi">Sayı <span class="text-danger">*</span></label>
							<input type="text" class="form-control form-control-sm" name="sayi" id="sayi" placeholder="Örn: 2500" value="<?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['sayi']) : '');?>" required />
							<small class="form-text text-muted">Sadece sayı girin, yüzde işareti otomatik eklenir</small>
						</div>
						<div class="modern-form-group">
							<label for="ikon">İkon <span class="text-danger">*</span></label>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm" name="ikon" id="ikon" placeholder="Örn: fas fa-users" value="<?php echo(isset($_GET['islem'])=='duzenle' ? htmlspecialchars($Sonuc['ikon']) : '');?>" required>
								<div class="input-group-append">
									<button type="button" class="btn btn-outline-secondary btn-sm" id="ikonSecBtn">
										<i class="fas fa-icons"></i> Seç
									</button>
								</div>
							</div>
							<small class="form-text text-muted">Font Awesome ikon sınıfını girin (Örn: fas fa-users, fas fa-heart)</small>
							<?php if(isset($_GET['islem'])=='duzenle' && !empty($Sonuc['ikon'])): ?>
							<div class="mt-2">
								<i class="<?php echo htmlspecialchars($Sonuc['ikon']);?> fa-2x text-primary"></i>
							</div>
							<?php endif; ?>
						</div>
						<div class="modern-form-group full-width">
							<label for="aciklama">Açıklama <span class="text-danger">*</span></label>
							<textarea class="form-control form-control-sm" name="aciklama" id="aciklama" rows="3" placeholder="Kısa açıklama metni" required><?php echo(isset($_GET['islem'])=="duzenle" ? htmlspecialchars($Sonuc['aciklama']) : '');?></textarea>
						</div>
						<div class="modern-form-group">
							<label for="durum">Durum</label>
							<select class="form-control form-control-sm" id="durum" name="durum">
								<option value="1" <?php echo(isset($_GET['islem'])=="duzenle" && isset($Sonuc['durum']) && $Sonuc['durum'] == 1 ? 'selected' : ''); ?>>Aktif</option>
								<option value="0" <?php echo(isset($_GET['islem'])=="duzenle" && isset($Sonuc['durum']) && $Sonuc['durum'] == 0 ? 'selected' : ''); ?>>Pasif</option>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="modern-form-actions">
				<a href="impact_reach_listele.html" class="modern-btn modern-btn-light">
					<i class="fas fa-times"></i> İptal
				</a>
				<?php if(isset($_GET['islem'])=="duzenle"){?>
				<button type="submit" name="impact_reach_guncelle" class="modern-btn modern-btn-success">
					<i class="fas fa-save"></i> Güncelle
				</button>
				<?php }else{?>
				<button type="submit" name="impact_reach_ekle" class="modern-btn modern-btn-primary">
					<i class="fas fa-plus"></i> Kaydet
				</button>
				<?php }?>
			</div>
		</form>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="ikonModal" tabindex="-1" role="dialog" aria-labelledby="ikonModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Font Awesome 5 İkon Seçimi</h5>
<button type="button" class="btn btn-sm btn-light border-0" aria-label="Kapat">
  <i class="fas fa-times"></i>
</button>



      </div>
      <div class="modal-body">
        <!-- Arama -->
        <input type="text" id="ikonAra" class="form-control mb-3" placeholder="İkon ara...">

        <!-- İkon Listesi -->
        <div style=" max-height: 500px; overflow: scroll; " id="ikonListesi" class="d-flex flex-wrap gap-3"></div>
      </div>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modalEl   = document.getElementById('ikonModal');
  const openBtn   = document.getElementById('ikonSecBtn');
  const closeBtn  = modalEl.querySelector('[aria-label="Kapat"]');

  function removeBootstrapBackdrops() {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
  }

  function openModal() {
    modalEl.classList.add('is-open');
    modalEl.style.display = 'block';
    document.body.classList.add('modal-open');

    // Bootstrap kalıntılarını temizle
    removeBootstrapBackdrops();

    // Elle eklediğimiz arka plan
    const bd = document.createElement('div');
    bd.className = 'custom-backdrop';
    bd.addEventListener('click', closeModal);
    document.body.appendChild(bd);
  }

  function closeModal() {
    modalEl.classList.remove('is-open');
    modalEl.style.display = 'none';
    document.body.classList.remove('modal-open');

    // Kendi backdrop’umuzu ve Bootstrap’in varsa kalanı temizle
    document.querySelectorAll('.custom-backdrop, .modal-backdrop').forEach(el => el.remove());
  }

  // Açma
  openBtn.addEventListener('click', openModal);
  // Kapatma
  closeBtn.addEventListener('click', closeModal);
  // Modal dışına tıklama
  modalEl.addEventListener('click', e => { if (e.target === modalEl) closeModal(); });
  // ESC ile kapatma
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
});
</script>




<script>
// Font Awesome ikon listesi (örnek temel grup)
const ikonlar = [
  "fas fa-user", "fas fa-users", "fas fa-heart", "fas fa-star", "fas fa-home",
  "fas fa-envelope", "fas fa-phone", "fas fa-cog", "fas fa-camera", "fas fa-check",
  "fas fa-edit", "fas fa-map-marker-alt", "fas fa-shopping-cart", "fas fa-globe",
  "fas fa-lock", "fas fa-comments", "fas fa-briefcase", "fas fa-calendar", "fas fa-bell",
  "fas fa-chart-bar", "fas fa-cloud", "fas fa-download", "fas fa-upload", "fas fa-music",
  "fas fa-play", "fas fa-pause", "fas fa-stop", "fas fa-search", "fas fa-trash",
  "fas fa-folder", "fas fa-file", "fas fa-image", "fas fa-video", "fas fa-lightbulb",
  "fas fa-laptop", "fas fa-tablet-alt", "fas fa-desktop", "fas fa-wifi", "fas fa-battery-full",
  "fas fa-gift", "fas fa-credit-card", "fas fa-car", "fas fa-bus", "fas fa-plane",
  "fas fa-hospital", "fas fa-heartbeat", "fas fa-stethoscope", "fas fa-graduation-cap",
  "fas fa-book", "fas fa-pencil-alt", "fas fa-award", "fas fa-crown", "fas fa-gem",
  "fas fa-hands-helping", "fas fa-tree", "fas fa-seedling", "fas fa-utensils",
  "fas fa-coffee", "fas fa-shopping-bag", "fas fa-tools", "fas fa-shield-alt",
  "fas fa-map", "fas fa-road", "fas fa-clipboard", "fas fa-bullhorn", "fas fa-rocket",
  "fas fa-futbol", "fas fa-basketball-ball", "fas fa-volleyball-ball", "fas fa-running", "fas fa-bicycle",
  "fas fa-microphone", "fas fa-headphones", "fas fa-podcast", "fas fa-film", "fas fa-tv",
  "fas fa-database", "fas fa-server", "fas fa-code", "fas fa-bug", "fas fa-terminal",
  "fas fa-atom", "fas fa-flask", "fas fa-magnet", "fas fa-cube", "fas fa-cubes",
  "fas fa-robot", "fas fa-brain", "fas fa-eye", "fas fa-eye-slash", "fas fa-fingerprint",
  "fas fa-handshake", "fas fa-smile", "fas fa-sad-tear", "fas fa-laugh", "fas fa-thumbs-up",
  "fas fa-thumbs-down", "fas fa-hourglass-half", "fas fa-sync", "fas fa-redo", "fas fa-undo",
    "fas fa-hammer", "fas fa-hard-hat", "fas fa-warehouse", "fas fa-industry", "fas fa-truck",
  "fas fa-ship", "fas fa-anchor", "fas fa-water", "fas fa-fire", "fas fa-snowflake",
  "fas fa-wind", "fas fa-mountain", "fas fa-tree", "fas fa-leaf", "fas fa-recycle",
  "fas fa-solar-panel", "fas fa-bolt", "fas fa-fan", "fas fa-bug", "fas fa-virus",
  "fas fa-shower", "fas fa-bath", "fas fa-bed", "fas fa-chair", "fas fa-couch",
  "fas fa-door-open", "fas fa-door-closed", "fas fa-key", "fas fa-plug", "fas fa-lightbulb",
  "fas fa-moon", "fas fa-sun", "fas fa-star-half-alt", "fas fa-cloud-sun", "fas fa-cloud-moon",
  "fas fa-rainbow", "fas fa-umbrella", "fas fa-binoculars", "fas fa-compass", "fas fa-map-signs",
  "fas fa-ticket-alt", "fas fa-passport", "fas fa-plane-departure", "fas fa-plane-arrival", "fas fa-parachute-box",
  "fas fa-box-open", "fas fa-dolly", "fas fa-people-carry", "fas fa-receipt", "fas fa-file-invoice",
  "fas fa-clipboard-check", "fas fa-clipboard-list", "fas fa-tasks", "fas fa-project-diagram", "fas fa-network-wired",
  "fas fa-chart-line", "fas fa-chart-pie", "fas fa-percentage", "fas fa-calculator", "fas fa-balance-scale",
    "fas fa-hand-point-up", "fas fa-hand-point-down", "fas fa-hand-point-left", "fas fa-hand-point-right", "fas fa-hand-rock",
  "fas fa-hand-paper", "fas fa-hand-scissors", "fas fa-hand-lizard", "fas fa-hand-peace", "fas fa-hand-spock",
  "fas fa-bell-slash", "fas fa-exclamation", "fas fa-exclamation-triangle", "fas fa-info-circle", "fas fa-question-circle",
  "fas fa-exclamation-circle", "fas fa-check-circle", "fas fa-times-circle", "fas fa-minus-circle", "fas fa-plus-circle",
  "fas fa-arrow-up", "fas fa-arrow-down", "fas fa-arrow-left", "fas fa-arrow-right", "fas fa-long-arrow-alt-up",
  "fas fa-long-arrow-alt-down", "fas fa-long-arrow-alt-left", "fas fa-long-arrow-alt-right", "fas fa-angle-up", "fas fa-angle-down",
  "fas fa-angle-left", "fas fa-angle-right", "fas fa-chevron-up", "fas fa-chevron-down", "fas fa-chevron-left",
  "fas fa-chevron-right", "fas fa-caret-up", "fas fa-caret-down", "fas fa-caret-left", "fas fa-caret-right",
  "fas fa-play-circle", "fas fa-pause-circle", "fas fa-stop-circle", "fas fa-step-forward", "fas fa-step-backward",
  "fas fa-forward", "fas fa-backward", "fas fa-random", "fas fa-volume-up", "fas fa-volume-down",
  "fas fa-volume-mute", "fas fa-volume-off", "fas fa-microphone-alt", "fas fa-microphone-slash", "fas fa-headset",
  "fas fa-gamepad", "fas fa-trophy", "fas fa-medal", "fas fa-dice", "fas fa-chess",
  "fas fa-chess-knight", "fas fa-chess-rook", "fas fa-chess-queen", "fas fa-chess-king", "fas fa-chess-pawn",
  "fas fa-chess-bishop", "fas fa-dragon", "fas fa-ghost", "fas fa-skull-crossbones", "fas fa-mask",
  "fas fa-spider", "fas fa-cat", "fas fa-dog", "fas fa-fish", "fas fa-horse",
  "fas fa-crow", "fas fa-frog", "fas fa-kiwi-bird", "fas fa-dove", "fas fa-feather",
  "fas fa-feather-alt", "fas fa-bone", "fas fa-seedling", "fas fa-leaf", "fas fa-paw"
];

// Modal referansları
const ikonModal = new bootstrap.Modal(document.getElementById('ikonModal'));
const ikonListesi = document.getElementById('ikonListesi');
const ikonInput = document.getElementById('ikon');
const ikonAra = document.getElementById('ikonAra');

// Modalı aç
document.getElementById('ikonSecBtn').addEventListener('click', () => {
  ikonModal.show();
  ikonlariYukle();
});

// İkonları listele
function ikonlariYukle(ara = "") {
  ikonListesi.innerHTML = "";
  ikonlar
    .filter(i => i.includes(ara))
    .forEach(ikon => {
      const div = document.createElement('div');
      div.className = "p-3 border text-center rounded m-1 ikon-kutu";
      div.style.cursor = "pointer";
      div.innerHTML = `<i class="${ikon}" style="font-size:22px;"></i><br><small>${ikon}</small>`;
      div.addEventListener('click', () => {
        ikonInput.value = ikon;
        ikonModal.hide();
      });
      ikonListesi.appendChild(div);
    });
}

// Arama filtresi
ikonAra.addEventListener('keyup', e => ikonlariYukle(e.target.value.toLowerCase()));
</script>


