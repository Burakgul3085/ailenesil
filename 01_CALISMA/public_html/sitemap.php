<?php require_once('_class/baglan.php'); require_once('_class/fonksiyon.php');require_once('_class/class.upload.php');if(!file_exists('language/dil_'.$_SESSION['k_dil'].".php")) die("Mevcut dilin dosyası bulunamadı!");
require_once('language/dil_'.$_SESSION['k_dil'].".php");
require_once('_class/seo.php'); header("Content-Type:text/xml; Charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
';
?>
<?php $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
$protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
$baseDir = str_replace('\\', '/', dirname($_SERVER['PHP_SELF'] ?? '/'));
$url = $protocol . $_SERVER['HTTP_HOST'] . $baseDir; 
?>
<?php
if($moduller['alan20'] == "1"){
	$html = ".html";
}
else
{
	$html = "";
}	
?>
<url>
  <loc><?=$url;?></loc>
</url>

<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['faaliyeturl'];?><?php echo $html;?></loc>
</url>

<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['ilanurl'];?><?php echo $html;?></loc>
</url>

<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['ihaleurl'];?><?php echo $html;?></loc>
</url>

<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['profillerurl'];?><?php echo $html;?></loc>
</url>

<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['kararurl'];?><?php echo $html;?></loc>
</url>

<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['duyuruurl'];?><?php echo $html;?></loc>
</url>

<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['etkinlikurl'];?><?php echo $html;?></loc>
</url>

<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['birimurl'];?><?php echo $html;?></loc>
</url>

<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['projelerurl'];?><?php echo $html;?></loc>
</url>

<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['hizmeturl'];?><?php echo $html;?></loc>
</url>

<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['haberurl'];?><?php echo $html;?></loc>
</url>

<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['fotourl'];?><?php echo $html;?></loc>
</url>

<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['videourl'];?><?php echo $html;?></loc>
</url>


<url>
  <loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['iletisimurl'];?><?php echo $html;?></loc>
</url>


<?php 
// Sayfalar
$cek=$db->query("SELECT * FROM sayfalar WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['sayfaurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Hizmetler
$cek=$db->query("SELECT * FROM hizmetler WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['hizmetdetayurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Birimler
$cek=$db->query("SELECT * FROM birimler WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['birimdetayurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Projeler Kategori
$cek=$db->query("SELECT * FROM proje_kategori WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['projekategoriurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Projeler
$cek=$db->query("SELECT * FROM projeler WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['projedetayurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Haber Kategori
$cek=$db->query("SELECT * FROM haber_kategori WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['haberkategoriurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Haberler
$cek=$db->query("SELECT * FROM haberler WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['haberdetayurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Duyurular
$cek=$db->query("SELECT * FROM duyurular WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['duyurudetayurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// İhaleler
$cek=$db->query("SELECT * FROM ihaleler WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['ihaledetayurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// İlanlar
$cek=$db->query("SELECT * FROM ilanlar WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['ilandetayurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Meclis Kararları
$cek=$db->query("SELECT * FROM meclis_kararlari WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['karardetayurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Faaliyet Raporları
$cek=$db->query("SELECT * FROM faaliyet_raporlari WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['faaliyetdetayurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Etkinlikler
$cek=$db->query("SELECT * FROM etkinlikler WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['etkinlikdetayurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Profil Kategori
$cek=$db->query("SELECT * FROM profil_kategori WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['profilkategoriurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Profiller
$cek=$db->query("SELECT * FROM profiller WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['profildetayurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Foto Galeri
$cek=$db->query("SELECT * FROM foto_galeri WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['fotodetayurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

// Video Galeri
$cek=$db->query("SELECT * FROM video_galeri WHERE durum=1 ORDER BY id DESC");
while($veri=$cek->fetch(PDO::FETCH_OBJ)){?>
<url>
<loc><?=$url;?><?php echo(altklasor == "1" ? '/' : '');?><?php echo $htc['videodetayurl'];?>/<?php echo $veri->seo; ?><?php echo $html;?></loc>
</url>
<?php
echo "\n";
}

?>
</urlset>