<?php require_once("includes/db.php"); ?>
<?php require_once('includes/functions.php'); ?>
<?php

	//If the $page variable's been set AND this is an internal user, redirect them to their page
	if (isset($_GET['page'])) {
		$page = $_GET['page'];
	}
	//For everything else, just redirect to the main index.php page
	else {
  	$page = "index.php"; 
	}
	
	// START FORM PROCESSING
	if (isset($_POST['submit'])) { // Form has been submitted.
		$errors = array();

		// perform validations on the form data
		$required_fields = array('username', 'password');
		$errors = array_merge($errors, check_required_fields($required_fields, $_POST));

		$fields_with_lengths = array('username' => 50, 'password' => 50);
		$errors = array_merge($errors, check_max_field_lengths($fields_with_lengths, $_POST));

		$Username = trim(mysql_prep($_POST['username']));
		$password = trim(mysql_prep($_POST['password']));
		$Hashed_Password = sha1($password);
		
		if ( empty($errors) ) {
			// Check database to see if username and the hashed password exist there.
			$query = "SELECT * ";
			$query .= "FROM Users ";
			$query .= "WHERE Username = '{$Username}' ";
			$query .= "AND Hashed_Password = '{$Hashed_Password}' ";
			$query .= "LIMIT 1";
			$result_set = mysql_query($query) or die('There was an error: ' . mysql_error());
			confirm_query($result_set);
			if (mysql_num_rows($result_set) == 1) {
				// username/password authenticated
				// and only 1 match
				$found_user = mysql_fetch_array($result_set);
				
				$_SESSION['Employee_ID'] = $found_user['Employee_ID'];
				$_SESSION['Name'] = $found_user['Name'];
				$_SESSION['Username'] = $found_user['Username'];
				$_SESSION['Email'] = $found_user['Email'];
				$_SESSION['Admin'] = $found_user['Admin'];
				$_SESSION['User_Fueler'] = $found_user['User_Fueler'];
				$_SESSION['Finance'] = $found_user['Finance'];
				$_SESSION['User_Internal'] = $found_user['User_Internal'];
				$_SESSION['Company_ID'] = $found_user['Company_ID'];
				$_SESSION['Guest_Access'] = $found_user['Guest_Access'];
				$_SESSION['User_First_Name'] = $found_user['User_First_Name'];
				$_SESSION['Permission_Level'] = $found_user['Permission_Level'];
				$_SESSION['Redirect_Page'] = $found_user['Redirect_Page'];
				
				//Log this login
				$Login_User_ID = $_SESSION['Employee_ID'];
				$Login_Timestamp = $date = date('Y-m-d H:i:s');
				$Login_History_Query = "INSERT INTO Login_History (Login_Employee_ID, Login_Timestamp) VALUES ('$Login_User_ID', '$Login_Timestamp')"; 
				$Login_History_Result = mysql_query($Login_History_Query) or die("There was an error: " . mysql_error()); 
				
				//Put in custom "homepage" settings
				if (!isset($_GET['page'])) {
  				
				  if($_SESSION['Employee_ID'] == 9) {
  				  //Charlie
  				  $page = 'list_of_truck_readiness.php';}
  				}
  				
  				header('Location: ' . $page);
				
			} else {
				// username/password combo was not found in the database
				$message = "Username/password combination incorrect.<br />
					Please make sure your caps lock key is off and try again.";
			}
		} else {
			if (count($errors) == 1) {
				$message = "There was 1 error in the form.";
			} else {
				$message = "There were " . count($errors) . " errors in the form.";
			}
		}
		
	} else { // Form has not been submitted.
		if (isset($_GET['logout']) && $_GET['logout'] == 1) {
			$message = "<p class='alert alert-success'>You are now logged out.</p>";
		} 
		$username = "";
		$password = "";
	}
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
<link href="css/bootstrap.min.css" rel="stylesheet">

<style type="text/css">
  body {
    padding-top: 60px;
    padding-bottom: 40px;
  }
  
  hr {margin-top: -10px;}
  
  li {color: black;}
</style>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="../assets/js/html5shiv.js"></script>
<![endif]-->
</head>

<body>
  <!-- Fixed navbar -->
  <div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php">BMN Leasing</a>
      </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
        </ul>
      </div><!--/.nav-collapse -->
    </div>
  </div>
  
  <div class="container">
    <div class="row">      
      <div id="login" class="col-md-6 col-md-offset-3">
      
       <form class="form-horizontal" role="form" action="" method="post" >
          <legend>Please Login:</legend>
          
          <div class="form-group">
            <label for="username" class="col-md-2 control-label">Username</label>
            <div class="col-md-8">
              <input type="text" class="form-control" name="username" id="username" placeholder="username" required="required">
            </div>
          </div>
          
          <div class="form-group">
            <label for="password" class="col-md-2 control-label">Password</label>
            <div class="col-md-8">
              <input type="password" class="form-control" name="password" id="password" placeholder="password" required="required">
            </div>
          </div>
          
          <div class="form-group">
            <input type="submit" id="submit" value="Login" name="submit" class="btn btn-primary form-control">
          </div>

        </form>
      </div>
    </div><!-- /.row -->
  </div><!-- /.container -->
  
      <!-- INCLUDED IN FOOTER.PHP: 
    </div><!-- /.end row-fluid 
  </div><!-- /.end container-fluid 
</body>
</html>
-->
<?php include 'includes/footer.php'; ?>