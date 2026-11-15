<?php

$code = $_GET['code'];
include './includer.php';
$site_url = 'http://'.$_SERVER['SERVER_NAME'].dirname(str_replace("system","",$_SERVER["REQUEST_URI"]).'?').'/';

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <script src="asset/js/jquery-1.11.0.js" type="text/javascript"></script>
        <script src="../asset/js/mediaelement-and-player.min.js" type="text/javascript"></script> 
    </head>
    <body>
        <?php
    
    $result = Query("select * from tbl_agahi where tbl_agahi_code ='$code' ");
    $read_agahi = mysqli_fetch_array($result);
    $thismember = $read_agahi['tbl_agahi_userid'];
    
    $result_member = Query("select * from tbl_member where tbl_member_code = '$thismember' ");
   $read_member = mysqli_fetch_array($result_member);
   
    $thispath = 'upload/'.$read_member['tbl_member_state'].'/'.$read_member['tbl_member_code'].'/';
    $img = $read_agahi['tbl_agahi_img'];
        $img = explode(",", $img);
        $count = count($img);
        for($i=0; $i<=count($img); $i++)
               {
                   if(!empty($img[$i]))
                   {
                       $first_img = $thispath.$img[$i];
                       break;;
                   }
                   else {
                       $first_img = $site_url.'asset/images/no-img.jpg';
                   }
               } 
               
    echo '<video poster="'.$site_url.$first_img.'" controls="controls" preload="none">
                <source type="video/mp4" src="'.$site_url.$thispath.$read_agahi['tbl_agahi_film'].'" />
                <object width="640" height="640" type="application/x-shockwave-flash" data="'.$site_url.'flashmediaelement.swf">
                    <param name="movie" value="'.$site_url.'flashmediaelement.swf" />
                    <param name="flashvars" value="controls=true&file='.$site_url.$thispath.$read_agahi['tbl_agahi_film'].'" />
                    <img src="'.$site_url.$first_img.'" width="640" height="640" title="No video playback capabilities" />
                </object>
            </video>';

?>
