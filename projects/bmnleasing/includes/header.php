<?php
  
  session_start();
	
	//If the session is inactive longer than XX time, then log the user out
	if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
  }
  //Reset the session time if the user's interacting with the site
  $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

  //If this person isn't logged in, send them to the login page
	if(!isset($_SESSION['User_ID'])) {
  	$page = basename($_SERVER['PHP_SELF']);
		header("Location: login.php");
		exit;
		echo "Why haven't I left yet?";
	}
	
	//Include the database connection
  include ('includes/db.php');
	
	//Set the current page for future reference	  
  //$page = basename($_SERVER['PHP_SELF']); //Get the current page name
  $page = strlen($_SERVER['QUERY_STRING']) ? basename($_SERVER['PHP_SELF'])."?".$_SERVER['QUERY_STRING'] : basename($_SERVER['PHP_SELF']);
  //Store the current page in session
  $_SESSION['Current_Page'] = $page;
  
  //Set the default timezone to NY, so all times are submitted in EST 
  date_default_timezone_set("America/New_York");

  //Set the default money business to USD
  setlocale(LC_MONETARY, 'en_US'); 
 ?>

<!DOCTYPE html>
<html lang="en">
 
<head>
<meta charset="utf-8">
<title>BMN Leasing</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<!-- CSS Styles -->
<link href="/css/bootstrap.min.css" rel="stylesheet"><!-- Tablesorter: required for bootstrap -->
<link href="/css/datepicker.css" rel="stylesheet">
<link href="/css/gbfo_style.css" rel="stylesheet">
<link href="/css/jasny-bootstrap.min.css" rel="stylesheet">
<link href="/css/bootstrap-editable.css" rel="stylesheet">

<!-- Javascript -->
<script src="/js/jquery-1.11.0.min.js"></script>
<script src="/js/bootstrap.js"></script>
<script src="/js/bootstrap-datepicker.js"></script>
<script src="/js/jquery.checkboxes-1.0.5.min.js"></script>
<script src="/js/jasny-bootstrap.min.js"></script><!-- Bootstrap addons from http://jasny.github.io/bootstrap/ -->
<script src="/js/bootstrap-editable.min.js"></script>

<?php 
  if($tablesorterinclude == true) {
    include ('includes/tablesorter.php');
  }
?>
<script>
  jQuery(document).ready(function($) {
    $(".clickableRow").click(function() {
      window.document.location = $(this).attr("href");
    });
  });
</script>
  
<style type="text/css">
  body {
    padding-top: 60px;
    padding-bottom: 40px;
  }
  hr {margin-top: -10px;}
  
  li {color: black;}
</style>

</head>

<body>
  <!-- Navbar -->
  <div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?php echo $baseurl; ?>index.php">BMN Leasing</a>
      </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <li <?php if(strpos($page, 'list_of_vehicles.php') !== false) {echo "class='active'";} ?>><a href="list_of_vehicles.php">Vehicles</a></li>
        </ul>
        <p class="navbar-text navbar-right pull-right" style="padding-right: 10px"><span class="hidden-xs hidden-sm hidden-md">
          Signed in as <?php echo $_SESSION['User_Full_Name'];?> - </span><a href="logout.php">Log Out</a>
        </p>
      </div><!--/.nav-collapse -->
    </div>
  </div>