<?php
ob_start();
session_start();

require_once('_class/baglan.php');
require_once('_class/fonksiyon.php');

if(!file_exists('language/dil_'.(int) $_GET['id'].".php")) die('{"hata" : true}');
$_SESSION['k_dil'] = (int) $_GET['id'];
echo json_encode(['hata' => false]);