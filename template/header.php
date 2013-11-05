<!DOCTYPE html>
<link rel="stylesheet" href="./metro/css/bootstrap-responsive.min.css" type="text/css">
<link rel="stylesheet" href="./metro/css/bootstrap.min.css" type="text/css">
<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

<link rel="stylesheet" href="./metro/css/typography.css" type="text/css">
<link rel="icon" type="image/png" href="template/images/e-tpy-logo3.png" />
<script language="javascript" src="./metro/js/jquery-1.8.0.min.js" ></script>
<script>var is_chrome = window.chrome;</script>
<meta name="viewport" content="width=device-width, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

</head>
<body >
    
  <?php
  
  if (match_referer() && ($_POST["reg"]==1)){
      if (($_POST["password"]!="")&&($_POST["pin"]!="")){
          $createresult=addnewlogin($_POST["pin"],$_POST["password"],$_SERVER['REMOTE_ADDR'],$ab_dbh);
          if ($createresult===TRUE){
              $errormsg="Εισάγετε τον νέο σας κωδικό και το PIN σας ({$_POST["pin"]}) για να εισέλθετε" ;
          }else{
              $errormsg=$createresult;
          }
     
      }else{
          $errormsg="Εισάγετε PIN και κωδικό για την εγγραφή σας" ;
      }
  }else if (match_referer() && ($_POST["log"]==1)) {

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
		$errormsg = "Λάθος PIN ή/και κωδικός";
		
	}

}elseif ($_POST["log"]==2) {
             unset($USER);
unset($_SESSION["USER"]);
session_regenerate_id();
		$errormsg = "Αντίος αμίγκος";
		
	}else{
            	$errormsg = "";
                }

  ?>
  
 <div class="row-fluid">
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
 <a class="brand" href="index.php">e-ΤΠΥ</a>
 <div class="nav-collapse collapse">
            <ul class="nav">            

              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Πλοήγηση <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="index.php">Έσοδα</a></li>
                  <li><a href="outcome.php">Έξοδα</a></li>
                  
                  <li class="divider"></li>
                     <?php if($USER["user"]["P_PIN"]==1111){
                         
                     echo ' <li><a href="backup.php">Backup db</a></li><li class="divider"></li>';
                     }
                     ?>
                  <li class="nav-header">Βοηθήματα</li>
                  <li><a href="summaries.php">Συγκεντρωτικές καταστάσεις</a></li>
                  
                </ul>
              </li>
            </ul>
             <?php if(isset($USER["user"])){
                 if (filter_var($USER["user"]["P_NAME"], FILTER_VALIDATE_IP)){
                     $uname="Εγγεγραμμένος χρήστης";
                 }else{
                     $uname=$USER["user"]["P_NAME"];
                 }
print<<<_HTML
   <form class="navbar-form pull-right" action="{$_SERVER["PHP_SELF"]}" method="POST">
        <button type="submit" name="log" value="2" class="btn">{$uname} Αποσύνδεση</button>
            </form>
_HTML;
                 
             }else{
print<<<_HTML
   <form class="navbar-form" action="{$_SERVER["PHP_SELF"]}" method="POST">
              <input class="span2" name="pin" type="text" placeholder="PIN">
              <input class="span2" name="password" type="password" placeholder="Κωδικός">
               
             
              <button type="submit" name="log" value="1" class="btn">Είσοδος</button><button type="submit" name="reg" value="1" class="btn">Εγγραφή</button><div class="text-error"> {$errormsg}</div>
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
             <script>
       if(typeof is_chrome == 'undefined'){
           document.write("<h1>προσοχή!μάλλον δεν έχεις Chrome επομένως ή κατέβασε τον Chrome ή δίνε τις ημερομηνίες ΥΥΥΥ-ΜΜ-ΔΔ</h1>");}
       </script>       
<img src="template/images/e-tpy-logo.png" width="20%">
		<? if ($GLOBALS['DB_DEBUG']===true){print "<p>Contents of POSTDATA <pre>";print_r($_POST);print "\n Contents of GETDATA \n"; print_r($_GET);print "</pre></p>";}?>
