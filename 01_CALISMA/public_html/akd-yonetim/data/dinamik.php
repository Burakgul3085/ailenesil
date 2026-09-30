<?php
ob_start();
session_start();
require_once('../../_class/baglan.php');
require_once('../../_class/fonksiyon.php');
		
	$id 	= $_POST['id'];
	$ilceid = $_POST['ilceid'];
	$dizi 	= array();
	$geridon= "<option value=''>-Seçiniz-</option>";
	$geridonb= "<option value=''>-Seçiniz-</option>";
	
	$query = $db->prepare("SELECT * FROM ilce WHERE IL_ID = ? ORDER BY ADI ASC");
	$query->execute(array($id));
	$islem = $query->fetchALL(PDO::FETCH_ASSOC);
	foreach ( $islem as $ILCESonuc )
	{			
		$geridon .= '<option value="'. $ILCESonuc['ID'].'" '.($ILCESonuc['ID'] == $ilceid ? 'selected' : '').'>'.$ILCESonuc['ADI'].'</option>';				
	}	
	if($geridon != "")
	{
		$dizi['basari']=$geridon;
	}
	else
	{
		$dizi['basari']=$geridonb;
	}
	
	echo json_encode($dizi);

?>