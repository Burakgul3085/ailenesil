<?php if($moduller['alan11'] == "1"){?>
<section class="quickmenu-band my-5">
  <div class="quickmenu-topline"></div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="custom-owl-nav hizlimenu-nav"></div>
        <div class="owl-carousel owl-carousel-hizlimenu">
        <?php $Sorgu = $db->prepare("SELECT * FROM slidermenu WHERE menu_durum = ? AND dil = ? ORDER BY menu_sira ASC");
              $Sorgu->execute(array("1",$_SESSION['k_dil']));
              $islem = $Sorgu->fetchALL(PDO::FETCH_ASSOC); ?>
          <?php foreach ( $islem as $Sonuc ){?>
          <div class="item">
            <div class="hizli-menu-box">
              <a <?php echo($Sonuc['sekme'] == 1 ? 'target="_blank"' : '');?> 
                 href="<?php echo($Sonuc['menu_url'] == "0" ? $Sonuc['link'] : $Sonuc['menu_url']); ?>" 
                 class="qm-card" style="background:<?php echo $Sonuc['menu_renk'];?>">
                <div class="hizli-icon"><i class="<?php echo $Sonuc['menu_icon'];?>"></i></div>
                <p class="qm-title"><?php echo $Sonuc['menu_isim'];?></p>
                <p class="qm-sub"><?php echo $Sonuc['menu_kisa'];?></p>
              </a>
              <a <?php echo($Sonuc['sekme'] == 1 ? 'target="_blank"' : '');?> 
                 href="<?php echo($Sonuc['menu_url'] == "0" ? $Sonuc['link'] : $Sonuc['menu_url']); ?>" 
                 class="hizli-back" style="background:<?php echo $Sonuc['menu_renk'];?>e8">
                <span><?=@$dil['txt70'];?></span>
              </a>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- HIZLI MENÜ BİTİŞ -->
 <!-- Hızlı Menü - Stil -->
<style>
/* Bant ve üst çizgi */
.quickmenu-band{position:relative;padding:42px 0 20px;background:#fff}
.quickmenu-topline{height:10px;background:#0aa0a9}

/* Carousel kapsayıcı */
.owl-carousel-hizlimenu .item{padding:6px}

/* Kart kutusu */
.hizli-menu-box{position:relative;height:100%}

/* Kartın kendisi */
.qm-card{
  display:flex; flex-direction:column; justify-content:center; gap:8px;
    height: 14pc;
  padding:18px 18px 64px; /* altta buton için alan */
  border-radius:18px; box-shadow:0 16px 34px rgba(0,0,0,.10);
  color:#083942; text-decoration:none !important; position:relative;
  transition:transform .16s ease, box-shadow .16s ease, filter .16s ease;
  overflow:hidden;
}

.qm-card::after{ /* parlaklık/texture */
  content:""; position:absolute; inset:0; pointer-events:none; opacity:.15;
  background:
    radial-gradient(130px 90px at 20% 20%, rgba(255,255,255,.9), transparent 60%),
    radial-gradient(160px 110px at 80% 35%, rgba(255,255,255,.55), transparent 70%);
}
.qm-card:hover{transform:translateY(-4px);box-shadow:0 22px 40px rgba(0,0,0,.14);filter:saturate(1.03)}

/* Simge rozeti */
.hizli-icon{
  width:48px;height:48px;border-radius:14px;display:grid;place-items:center;
  background:rgba(255,255,255,.85);box-shadow:inset 0 0 0 2px rgba(255,255,255,.5);
}
.hizli-icon i{font-size:26px;line-height:1}

/* Başlık ve alt yazı */
.qm-title{    color: white;font:800 18px/1.15 "Montserrat",sans-serif;margin:4px 0 0}
.qm-sub {
    margin: 4px 0 0;
    font-weight: 600;
    opacity: .95;
    color: #f3f3f3;
}
/* Alttaki yarı saydam buton (mevcut <a class="hizli-back">) */
.hizli-back{
  position:absolute;left:12px;right:12px;bottom:12px;height:44px;
  display:flex;align-items:center;justify-content:center;
  border-radius:12px;color:#083942;text-decoration:none !important;
  transform:translateY(6px);opacity:0;transition:.18s ease;
  box-shadow:0 8px 18px rgba(0,0,0,.10); font-weight:800;
}
.hizli-menu-box:hover .hizli-back{transform:translateY(0);opacity:1}

/* Linklerde alt-çizgiyi global kapat */
.hizli-menu-box a,
.hizli-menu-box a:hover{ text-decoration:none !important }

/* Özel Owl nav (custom-owl-nav.hizlimenu-nav içinde) */
.hizlimenu-nav{
  display:flex;gap:10px;justify-content:flex-end;margin-bottom:10px
}
.hizlimenu-nav .owl-prev, .hizlimenu-nav .owl-next{
  width:40px;height:40px;border-radius:50%;display:inline-grid;place-items:center;
  background:#0aa0a9;color:#fff;font-weight:900;font-size:18px;
  box-shadow:0 10px 20px rgba(10,160,169,.25);transition:.15s ease
}
.hizlimenu-nav .owl-prev:hover, .hizlimenu-nav .owl-next:hover{filter:brightness(1.08)}
.hizlimenu-nav .owl-prev[disabled], .hizlimenu-nav .owl-next[disabled]{opacity:.4}

/* Responsive */
@media (max-width:1200px){ .qm-card{min-height:160px} }
@media (max-width:992px){
  .hizlimenu-nav{justify-content:center;margin-bottom:14px}
.qm-card {
    min-height: 177px;
    height: 14pc;
}
}
@media (max-width:600px){
  .qm-title{font-size:16px}
  .qm-sub{font-size:14px}
}
</style>

<!-- Hızlı Menü - JS (Owl başlatma) -->
<script>
(function($){
  $(function(){
    var $owl = $('.owl-carousel-hizlimenu');
    if(!$owl.length || !$owl.owlCarousel) return;

    $owl.owlCarousel({
      loop:false,
      margin:16,
      dots:false,
      nav:true,
      navContainer: '.hizlimenu-nav',
      navText: ['‹','›'],
      smartSpeed:450,
      responsive:{
        0:{items:2},
        576:{items:3},
        992:{items:4},
        1280:{items:5}
      }
    });
  });
})(jQuery);
</script>
<?php } ?>