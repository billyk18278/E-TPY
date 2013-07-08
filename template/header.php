<!DOCTYPE html>
<link rel="stylesheet" href="./metro/css/bootstrap-responsive.min.css" type="text/css">
<link rel="stylesheet" href="./metro/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="./metro/css/m-buttons.min.css" type="text/css">
<link rel="stylesheet" href="./metro/css/m-styles.min.css" type="text/css">
<link rel="stylesheet" href="./metro/css/typography.css" type="text/css">
<script language="javascript" src="./metro/js/jquery-1.8.0.min.js" ></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

</head>
<body >
    
  <?php
  
   if (match_referer() && ($_POST["log"]==1)) {

	$user = verify_login($_POST["pin"], $_POST["password"],$ab_dbh);

	if ($user) {
		$USER["user"] = $user;
		$USER["ip"] = $_SERVER["REMOTE_ADDR"];

		/* if wantsurl is set, that means we came from a page that required
		 * log in, so let's go back there.  otherwise go back to the main page */
//access_log_function($_POST["username"],"LOGIN",$ab_dbh);
	} else {
             unset($USER);
unset($_SESSION["USER"]);
		$errormsg = "Invalid PIN and/or password";
		
	}

}elseif ($_POST["log"]==2) {
             unset($USER);
unset($_SESSION["USER"]);
session_regenerate_id();
		$errormsg = "Logged out";
		
	}else{
            	$errormsg = "";
                }

  ?>
  
 <div class="row-fluid">
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#">e-ΤΠΥ</a>
          <div class="nav-collapse collapse">
            <ul class="nav">            

              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Πλοήγηση <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Έσοδα</a></li>
                  <li><a href="#">Έξοδα</a></li>
                  
                  <li class="divider"></li>
                  <li class="nav-header">Εκτυπώσεις</li>
                  <li><a href="#">Εκτύπωση Φ2</a></li>
                  <li><a href="#">Εκτύπωση Φ1</a></li>
                  <li><a href="#">Εκτύπωση Ε3</a></li>
                </ul>
              </li>
            </ul>
             <?php if(isset($USER["user"])){
print<<<_HTML
   <form class="navbar-form pull-right" action="{$_SERVER["PHP_SELF"]}" method="POST">
        <button type="submit" name="log" value="2" class="btn">{$USER["user"]["P_NAME"]} Αποσύνδεση</button>
            </form>
_HTML;
                 
             }else{
print<<<_HTML
   <form class="navbar-form pull-right" action="{$_SERVER["PHP_SELF"]}" method="POST">
              <input class="span2" name="pin" type="text" placeholder="PIN">
              <input class="span2" name="password" type="password" placeholder="Κωδικός">
             
              <button type="submit" name="log" value="1" class="btn">Διαπιστευμένη είσοδος</button><div class="text-error"> {$errormsg}</div>
            </form>
_HTML;

             }
             ?>
            
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
      
        
   <div class="hero-unit">
		<div  class="row-fluid">

		<div class="content span12">
                    

		<? if ($GLOBALS['DB_DEBUG']===true){print "<p>Contents of POSTDATA <pre>";print_r($_POST);print "\n Contents of GETDATA \n"; print_r($_GET);print "</pre></p>";}?>
