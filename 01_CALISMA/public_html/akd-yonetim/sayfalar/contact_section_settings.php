<?php echo !defined("GUVENLIK") ? die("Erişim Engellendi!.") : null; ?>
<?php
$settings_query = $db->prepare("SELECT * FROM contact_section_settings ORDER BY page_name ASC");
$settings_query->execute();
$settings = $settings_query->fetchAll(PDO::FETCH_ASSOC);

$pageSettings = [];
foreach ($settings as $s) {
    $pageSettings[$s['page_name']] = $s['show_contact_section'];
}

$pages = [
    'anasayfa' => 'Ana Sayfa',
    'hakkimizda' => 'Hakkımızda',
    'iletisim' => 'İletişim',
    'sayfalar' => 'Diğer Sayfalar (İçerik)',
    'haberler' => 'Haberler',
    'haber_detay' => 'Haber Detay',
    'haber_kategori' => 'Haber Kategorileri',
    'projeler' => 'Projeler',
    'proje_detay' => 'Proje Detay',
    'proje_kategori' => 'Proje Kategorileri',
    'meclis_kararlari' => 'Meclis Kararları',
    'meclis_kararlari_detay' => 'Meclis Karar Detay',
    'hizmetler' => 'Hizmetler',
    'hizmet_detay' => 'Hizmet Detay',
    'programlar' => 'Programlar',
    'program_detay' => 'Program Detay',
    'etkinlikler' => 'Etkinlikler',
    'etkinlik_detay' => 'Etkinlik Detay',
    'duyurular' => 'Duyurular',
    'duyuru_detay' => 'Duyuru Detay',
    'ihaleler' => 'İhaleler',
    'ihale_detay' => 'İhale Detay',
    'ilanlar' => 'İlanlar',
    'ilan_detay' => 'İlan Detay',
    'faaliyet_raporlari' => 'Faaliyet Raporları',
    'faaliyet_raporlari_detay' => 'Faaliyet Rapor Detay',
    'birimler' => 'Birimler',
    'birim_detay' => 'Birim Detay',
    'profil_kategori' => 'Profil Kategorileri',
    'profiller' => 'Profiller',
    'profil_detay' => 'Profil Detay',
    'foto_galeri' => 'Foto Galeri',
    'foto' => 'Fotoğraf Detay',
    'video_galeri' => 'Video Galeri',
    'video' => 'Video Detay',
];
?>

<div class="page-header">
  <div class="page-title mt-0 mb-0">
    <h3><i class="ti-settings"></i> İletişim Bölümü Ayarları</h3>
    <div class="crumbs">
      <ul id="breadcrumbs" class="breadcrumb">
        <li><a href="index.html"><i class="icon-home menu-icon"></i></a></li>
        <li class="active"><a href="#">İletişim Bölümü Ayarları</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="card shadow-sm border-0">
  <div class="card-body">
    <h4 class="card-title mb-4">Sayfa Bazlı Görünürlük</h4>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <input type="text" id="searchInput" class="form-control w-50" placeholder="Sayfa ara...">
      <span class="text-muted small" id="tableInfo"></span>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle" id="contactSettingsTable">
        <thead class="thead-light">
          <tr>
            <th width="60%">Sayfa Adı</th>
            <th width="40%">Durum</th>
          </tr>
        </thead>
        <tbody id="contactSettingsBody">
          <?php foreach ($pages as $key => $value): 
            $checked = isset($pageSettings[$key]) && $pageSettings[$key] == 1;
          ?>
          <tr>
            <td class="fw-semibold text-dark"><?php echo htmlspecialchars($value); ?></td>
            <td>
              <div class="form-check form-switch">
                <input class="form-check-input contactToggle" 
                       type="checkbox" 
                       id="toggle_<?php echo $key; ?>" 
                       data-page="<?php echo $key; ?>"
                       <?php echo $checked ? 'checked' : ''; ?>>
                <label for="toggle_<?php echo $key; ?>" class="form-check-label ms-2">
                  <?php echo $checked ? 'Gösteriliyor' : 'Gizli'; ?>
                </label>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
      <nav>
        <ul class="pagination mb-0" id="pagination"></ul>
      </nav>
    </div>
  </div>
</div>

<script>
// === Anlık Güncelleme ===
document.querySelectorAll('.contactToggle').forEach(toggle => {
  toggle.addEventListener('change', async function() {
    const pageName = this.dataset.page;
    const checked = this.checked ? 1 : 0;
    const label = this.closest('td').querySelector('label');
    label.textContent = checked ? 'Gösteriliyor' : 'Gizli';

    const formData = new FormData();
    formData.append('update_contact_settings_live', '1');
    formData.append('page_name', pageName);
    formData.append('show_contact', checked);

    const res = await fetch('../_class/yonetim_islem.php', {
      method: 'POST',
      body: formData
    });
    if (res.ok) swal("Güncellendi", "Ayar başarıyla kaydedildi.", "success");
  });
});

// === Arama ve Pagination ===
const rows = Array.from(document.querySelectorAll("#contactSettingsBody tr"));
const rowsPerPage = 10;
let currentPage = 1;

function renderTable() {
  const searchTerm = document.getElementById("searchInput").value.toLowerCase();
  const filteredRows = rows.filter(row => 
    row.textContent.toLowerCase().includes(searchTerm)
  );
  const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const visibleRows = filteredRows.slice(start, end);

  document.getElementById("contactSettingsBody").innerHTML = "";
  visibleRows.forEach(r => document.getElementById("contactSettingsBody").appendChild(r));
  
  renderPagination(totalPages);
  document.getElementById("tableInfo").textContent = 
    `Toplam ${filteredRows.length} sayfa bulundu - ${totalPages} sayfa`;
}

function renderPagination(totalPages) {
  const pagination = document.getElementById("pagination");
  pagination.innerHTML = "";

  for (let i = 1; i <= totalPages; i++) {
    const li = document.createElement("li");
    li.className = "page-item" + (i === currentPage ? " active" : "");
    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
    li.addEventListener("click", e => {
      e.preventDefault();
      currentPage = i;
      renderTable();
    });
    pagination.appendChild(li);
  }
}

document.getElementById("searchInput").addEventListener("input", () => {
  currentPage = 1;
  renderTable();
});

renderTable();
</script>
