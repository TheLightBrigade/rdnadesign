<?php 
  error_reporting(E_ALL);
  require_once("includes/db.php");
  
  session_start();
	
	if(isset($_POST['submit'])) {
  	
    $Error_Message = '';
    //username and password sent from Form
    $Username = trim($_POST['username']);
    $Password = trim($_POST['password']);
    
    if($Username == '') {
      $Error_Message .= 'You must enter your Username<br>';
    }
    
    if($Password == '') {
      $Error_Message .= 'You must enter your Password<br>';
    }
    
    if($Error_Message == '') {
      
      $User_Lookup_Query = "SELECT User_ID, User_First_Name, User_Email, Username, Password FROM Users WHERE Username = :Username";
      $User_Lookup_Query = $conn->prepare($User_Lookup_Query);
      $User_Lookup_Query->bindParam(':Username', $Username);
      $User_Lookup_Query->execute();
      
      $User_Lookup_Results = $User_Lookup_Query->fetch(PDO::FETCH_ASSOC);
       
      if(count($User_Lookup_Results) > 0 && sha1($Password) == $User_Lookup_Results['Password']) {

        //Login successful, set the Session variables
        $_SESSION['User_ID'] = $User_Lookup_Results['User_ID'];
        $_SESSION['User_First_Name'] = $User_Lookup_Results['User_First_Name'];
        $_SESSION['User_Full_Name'] = $User_Lookup_Results['User_First_Name'] . $User_Lookup_Results['User_Last_Name'];
        $_SESSION['User_Email'] = $User_Lookup_Results['User_Email'];
        
        header('Location: index.php');
        exit;
      }
      else {
        $Error_Message .= "Username and/or password are not found.";
      } 
    }
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
    <?php
      if(isset($Error_Message)) {
        echo "<div class='col-md-12 col-xs-12 alert alert-danger'>$Error_Message</div>";
      }
    ?>
    
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