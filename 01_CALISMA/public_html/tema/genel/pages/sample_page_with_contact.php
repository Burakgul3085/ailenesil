<?php echo !defined("GUVENLIK") ? die("Erisim Engellendi!.") : null;?>
<?php 
// Include the contact section functions
require_once('_class/contact_section_functions.php');

// Set the current page name (this should match the page names in your database)
$page_name = 'anasayfa'; // Change this for each page

// Get the page link for the contact form
$sayfalink = $_SERVER['REQUEST_URI'];
?>
<!-- PAGE SECTİON BAŞLANGIÇ -->
<section class="page-section">
	<div class="bg-white">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<h1><?=@$dil['txt567'];?></h1>
					<p><?=@$dil['txt568'];?></p>
					
					<!-- Other page content goes here -->
					
					<!-- Display the contact section if enabled for this page -->
					<?php displayContactSection($page_name, $db, $dil, $sayfalink); ?>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- PAGE SECTİON BİTİŞ -->