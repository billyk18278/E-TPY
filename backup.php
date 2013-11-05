<?php
require_once("config.php");
require_once($CFG->libdir ."/sessionstart.php");
require_once($CFG->libdir . "/dblib.php");
require_once($CFG->libdir . "/loginlib.php");
//test!
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <?php
        $ab_dbh = db_connect($CFG->dbhost, $CFG->dbname, $CFG->dbuser, $CFG->dbpass);

        if (!$ab_dbh) {
            print "The system could not connect to your local database<p>Please contact your system administrator ";          
            print "<p><A HREF=\"logout.php\">Log Out</A>";

            die();
        }
include("./template/header.php");
if ($USER["user"]["P_PIN"]==1111)
if (backup_db($CFG,"C:/")){
    echo "<p>Backup successful</p>";
}else{
    echo "<p>Backup failed</p>";
}
?>
    <a href="index.php"><div  class="btn btn-primary small">Επιστροφή στα έσοδα</div></a>
<?php
include("./template/footer.php");
        ?>
        
    </body>
</html>