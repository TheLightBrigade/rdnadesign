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
<title>GBFO</title>
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
        <a class="navbar-brand" href="<?php echo $baseurl; ?>index.php">GBFO</a>
      </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <li <?php if(strpos($page, 'truck_tracking.php') !== false) {echo "class='active'";} ?>><a href="truck_tracking.php">Tracking</a></li>
          <li <?php if(strpos($page, 'list_of_fuel_records.php') !== false) {echo "class='active'";} ?>><a href="list_of_fuel_records.php">Fuel Sales</a></li>
          

          <li class='dropdown <?php if(strpos($page, 'list_of_trucks.php') !== false || strpos($page, 'individual_truck.php') !== false || strpos($page, 'list_of_trucks_simple.php') !== false || strpos($page, 'list_of_truck_readiness.php') !== false || strpos($page, 'list_of_trucks_and_tanks.php') !== false) {echo "active";} ?>'>
            <a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>Trucks <span class='caret'></span></a>
            <ul class='dropdown-menu' role='menu'>
              <li><a href='list_of_trucks.php'>Truck List</a></li>
              <li><a href='list_of_truck_readiness.php'>Truck Readiness</a></li>
              <li><a href='list_of_trucks_simple.php'>Truck List (simple)</a></li>
              <li><a href='list_of_trucks_and_tanks.php'>Truck/Tank Cross Reference</a></li>
              <li class='divider'></li>
              <li><a href='list_of_truck_costs.php'>List of Truck Costs</a></li>
            </ul>
          </li>
       
          <li class='dropdown <?php if(strpos($page, 'list_of_leases.php') !== false || strpos($page, 'lease_info.php') !== false) {echo "active";} ?>'>
            <a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>Leases <span class='caret'></span></a>
            <ul class='dropdown-menu' role='menu'>
              <li><a href='list_of_lease_fuel_performance.php?Sort=Current_Pace&Sort_Order=DESC'>Lease Performance</a></li>
              <li><a href='list_of_historical_lease_fuel_performance.php?Month=March&Sort=Penalty_Total&Sort_Order=DESC'>Historical Performance</a></li>
              <li><a href='list_of_leases.php'>Lease List</a></li>
            </ul>
          </li>
          
          <li <?php if(strpos($page, 'list_of_tanks.php') !== false) {echo "class='active'";}?>><a href='list_of_tanks.php'>Tanks</a></li>
          <li <?php if(strpos($page, 'list_of_a3s.php') !== false || strpos($page, 'individual_a3.php') !== false) {echo "class='active'";}?>><a href='list_of_a3s.php'>A3s</a></li>
    
          <!--<li <?php if(strpos($page, 'list_of_service.php') !== false || strpos($page, 'service.php') !== false) {echo "class='active'";} ?>><a href="list_of_service.php">Service</a></li>-->
          <li class='dropdown <?php if(strpos($page, 'list_of_invoices.php') !== false || strpos($page, 'list_of_credits.php') !== false || strpos($page, 'list_of_payable_invoices.php') !== false || strpos($page, 'list_of_fuel_prices.php') !== false || strpos($page, 'list_of_purchase_orders.php') !== false || strpos($page, 'financials.php') !== false || strpos($page, 'financial_model.php') !== false || strpos($page, 'financial_variance.php') !== false) {echo "active";} ?>'>
            <a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>Financial <span class='caret'></span></a>
            <ul class='dropdown-menu' role='menu'>
              <li><a href='list_of_invoices.php'>Invoices</a></li>
              <li><a href='list_of_credits.php'>Credits</a></li>
              <li><a href='list_of_payable_invoices.php'>Payable Invoices</a></li>
              <li><a href='list_of_fuel_prices.php'>Fuel Prices</a></li>
              <li><a href='list_of_purchase_orders.php'>Purchase Orders</a></li>
              <li class="divider"></li>
              <li><a href='financial_snapshot.php'><b>Financial Snapshot</b></a></li><!--
              <li><a href='financial_model.php'><b>Financial Model</b></a></li>
              <li><a href='financial_variance.php'><b>Financial Variance</b></a></li>-->
            </ul>
          </li>
          
          <li class='dropdown <?php if(strpos($page, 'tvac_records.php') !== false || strpos($page, 'list_of_parts.php') !== false || strpos($page, 'company_directory.php') !== false || strpos($page, 'list_of_contacts.php') !== false || strpos($page, 'list_of_drivers.php') !== false || strpos($page, 'list_of_users.php') !== false || strpos($page, 'list_of_files.php') !== false || strpos($page, 'news_feed.php') !== false || strpos($page, 'reports.php') !== false || strpos($page, 'timesheet_admin.php') !== false || strpos($page,'timesheet.php') !== false) {echo "active";} ?>'>
            <a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>Miscellaneous <span class='caret'></span></a>
            <ul class='dropdown-menu' role='menu'>
              <li><a href='tvac_records.php'>TVAC Records</a></li>
              <li><a href='list_of_parts.php'>Items</a></li>
              <li><a href='company_directory.php'>Companies</a></li>
              <?php if($_SESSION['Guest_Access'] <> 1) {echo "<li><a href='list_of_contacts.php'>Contacts</a></li>";} ?>
              <li><a href='list_of_drivers.php'>Drivers</a></li>
              <?php if($_SESSION['Guest_Access'] <> 1) {echo "<li><a href='list_of_users.php'>Users</a></li>";} ?>
              <li><a href='list_of_files.php'>Files</a></li>
              <li><a href='reports.php'>Reports</a></li>
              <?php if($_SESSION['Guest_Access'] <> 1) {
                if($_SESSION['User_Salary_Flag'] = 0 AND $_SESSION['User_Hourly_Flag'] = 0) {
                  echo"<li><a href='timesheet_admin.php>Timesheets</a></li>";
                }
                else {
                  echo"<li><a href='timesheet.php?User_ID=$User_ID&Week_Inc=0'>Timesheets</a></li>";
                }   
              }
              ?>
              <li><a href='help.php'>Help Contacts</a></li>
            </ul>
          </li>
        </ul>
        
        <p class="navbar-text navbar-right pull-right" style="padding-right: 10px"><span class="hidden-xs hidden-sm hidden-md">Signed in as <?php echo $_SESSION['Name'];?> - </span><a href="logout.php">Log Out</a></p>
      </div><!--/.nav-collapse -->
    </div>
  </div>