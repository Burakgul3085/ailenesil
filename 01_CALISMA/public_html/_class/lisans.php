<?php 
// Lisanslama sistemi kaldırıldı - Anasayfaya yönlendir
header("Location: /index.php");
exit();
?>
<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300" rel="stylesheet">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lütfen Lisans Kodunu Giriniz</title>
<meta name="description" content="Lütfen Lisans Kodunu Giriniz">
<link href="<?php echo $dosya_yolu;?>/_class/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $dosya_yolu;?>/_class/css/font-awesome.min.css" rel="stylesheet">
<link href="<?php echo $dosya_yolu;?>/_class/css/main.css" rel="stylesheet">
</head>
<body>

<!--main-->
<section class="main">
  <div class="overlay"></div>
  <div class="container">
    <div class="row">
      <div class="col-md-12"> 
        <!--welcome-message-->
        <header class="welcome-message text-center">
          <h1><span class="rotate">Lütfen Lisans Kodunu Giriniz , Lütfen Lisans Kodunu Giriniz</span></h1>
        </header>
        <!--welcome-message end--> 
        <!--sub-form-->
        <div class="sub-form text-center">
          <div class="row">
            <div class="col-md-5 center-block col-sm-8 col-xs-11">
              <form role="form" id="mc-form" method="POST">
                <div class="input-group">
                  <input type="text" id="kod" class="form-control" placeholder="Lisans Kodu" name="kod">
                  <span class="input-group-btn">
                  <button type="submit" class="btn btn-default" id="mc-subscribe" value="Subscribe" name="subscribe">Gönder<i class="fa fa-paper-plane"></i></button>
                  </span> </div>
              </form>
              <p id="mc-notification"><?php echo $bilgi;?></p>
            </div>
          </div>
        </div>
        <!--sub-form end--> 

      </div>
    </div>
  </div>
</section>
<!--main end--> 

<script src="<?php echo $dosya_yolu;?>/_class/js/jquery-2.1.4.min.js"></script> 
<script src="<?php echo $dosya_yolu;?>/_class/js/wow.min.js"></script> 
<script src="<?php echo $dosya_yolu;?>/_class/js/retina.min.js"></script> 
<script src="<?php echo $dosya_yolu;?>/_class/js/jquery.downCount.js"></script> 
<script src="<?php echo $dosya_yolu;?>/_class/js/jquery.form.min.js"></script> 
<script src="<?php echo $dosya_yolu;?>/_class/js/jquery.validate.min.js"></script> 
<script src="<?php echo $dosya_yolu;?>/_class/js/jquery.simple-text-rotator.min.js"></script> 
<script src="<?php echo $dosya_yolu;?>/_class/js/main.js"></script> 
</body>
</html>
