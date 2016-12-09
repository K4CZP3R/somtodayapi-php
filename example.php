<?php
include("somAPI.php");
if(isset($_GET['mode'])) {
    $sMode = $_GET['mode'];
    if($sMode == null){
        die("Mode can't be empty, enter mode after '?mode='");
    }
}
else {
    die("No mode selected");
}
if(isset($_GET['raw'])){
    $raw = $_GET['raw'];
    if((int)$raw == 1 || $raw == "true"){
        $raw = true;
    }
    else{
        $raw=false;
    }
}

$somtoday=new somtodayapi("email","password","schoolname","brin");

if($sMode == "grades" || $sMode == "cijfers"){
    $jresp = $somtoday->getGrades();
    if(!$raw) {
        foreach ($jresp["json"]["data"] as $key => $val) {
            echo $val["vak"]." - ".$val["resultaat"]." - (".$val["beschrijving"].")<br>";
        }
    }
    else{
        echo $jresp["raw"];
    }
}
if($sMode == "homework" || $sMode == "huiswerk"){
    if(isset($_GET['days'])){
        $daysahead = $_GET['days'];
    }
    else{
        $daysahead = 14;
    }
    $jresp = $somtoday->getHomework($daysahead);
    if(!$raw){
        foreach($jresp["json"]["data"] as $key => $val){
            echo $val["vak"]." - ".$val["huiswerk"]."<br><br>";
        }
    }
    else{
        echo $jresp["raw"];
    }

}
?>
