<?php

class object {};

$CFG = new object;
$CFG->template="templatestyle_metro_teal";
/* administration centre phenotyping database configuration */
$CFG->dbhost = "127.0.0.1:3306";//"127.0.0.1:3307";
$CFG->dbname = "block";
$CFG->dbuser = "";
$CFG->dbpass = "";

$CFG->projectfullname="ΤΙΜΟΛΟΓΙΟ ΠΑΡΟΧΗΣ ΥΠΗΡΕΣΙΩΝ";
$CFG->projectshortname="ΤΠΥ";
$CFG->wwwroot     = "/block/";
$CFG->dirroot     = dirname(__FILE__);
$CFG->libdir      = "./lib";
$CFG->imagedir    = "$CFG->libdir/images";
$CFG->version     = "beta";
$CFG->sessionname = "block";

/* FOR BACKUP */
$CFG->mysql = "c:\\MySQL\\bin\\";
$CFG->z7 = "c:\\Program Files\\7-Zip\\7z.exe";

$GLOBALS['DB_DEBUG']=false; //now also prints $_POST
$GLOBALS['DB_DIE_ON_DEBUG']=false;
$GLOBALS['DB_DIE_ON_FAIL']=true;
//Για την επιλογή στην φόρμα
$CFG->TAX=20;
$CFG->VAT=23;
$CFG->INVTYPES=array("Γενικά έξοδα"=>"<i class=\"icon-money icon-large\"></i>","Έξοδα κινητής τηλεφωνίας"=>"<i class=\"icon-mobile-phone icon-large\"></i>","Έξοδα αυτοκινήτου"=>"<i class=\"icon-truck icon-large\"></i>");
/* Set locale to greek */
//setlocale(LC_ALL, 'eng');
setlocale(LC_ALL, 'ell');
date_default_timezone_set('Europe/Athens');
//To enable multiple contacts that can be manipulated in list of forms
?>