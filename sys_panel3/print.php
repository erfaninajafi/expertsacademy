<?php
session_start();
include './includer.php';
include '../include/jdf.php';
if(empty($_SESSION['id_login']))
    {
        header("location: login.php");
        exit();
    }
    else {
        $this_user_id = $_SESSION['id_login'];
    }
    $result_member = Query("select * from tbl_user where tbl_user_id = '$this_user_id' ");
    $read_member = mysqli_fetch_array($result_member);
    $member_name = $read_member['tbl_user_username'];
    $access = $read_member['tbl_user_access'];
    @$op = $_SESSION['username_name'];
    
    if(!empty($_GET['lng']))
    {
        $lng = $_GET['lng'];
    }
    else {
        $lng = 'fa';
    }
    if (isset($_GET['code']))
    {
       $code = $_GET['code'];
    }
    $pagetitle = ' - Print';
    include '../include/lang/'.$lng.'.php';
    ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        <noscript>
            <META HTTP-EQUIV="Refresh" CONTENT="0;URL=noscript.php">
        </noscript>
        <title>Print - <?=$code?></title>
        <link rel="stylesheet" href="../asset/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="../asset/css/bootstrap.min.css">
        <link rel="stylesheet" href="../asset/css/bootstrap-rtl.min.css">
        <link rel="stylesheet" href="../asset/css/style_<?=$lng?>.min.css?ver=1.1">
        <script src="asset/js/jquery-3.2.1.min.js"></script>
        <script src="asset/js/custom.js"></script>
    </head>
    <body>
        <div id="print">
        <?php
        $result = Query("Select * from tbl_work where tbl_work_systemcode='$code' and tbl_work_del='0' and tbl_work_incomplete !='1'");
        $read = mysqli_fetch_array($result);
        $festival = $read['tbl_work_festival'];
        $userid = $read['tbl_work_userid'];
        $result_festival = Query("Select * from tbl_festival where tbl_festival_systemcode='$festival' ");
        $read_festival = mysqli_fetch_array($result_festival);
        
        $coursecode = $read['tbl_work_coursecode'];
        $result_course = Query("Select * from tbl_festcourse where tbl_festcourse_systemcode='$coursecode' ");
        $read_course = mysqli_fetch_array($result_course);
        echo '<table>'
                . '<tr><td colspan="4">'.$lang[858].'</td></tr><tr><td><span>'.$lang[466].'</span>'.$read['tbl_work_code'].'</td>'
                . '<td><span>'.$lang[600].'</span>'.$read['tbl_work_date'].'</td>'
                . '<td><span>'.$lang[822].' / '.$lang[823].'</span>'.$read_festival['tbl_festival_title'].' / '.$read_course['tbl_festcourse_title'].'</td>'
                . '<td><span>'.$lang[826].'</span>'.check_work($read['tbl_work_status']).'</td></tr>'
                .user_print($code,$userid,$lng,1).''
                . '</table>';
                echo '<div class="copyright">'.$lang[857].' '.($lng == "fa" ? jdate("Y/m/d") : date("Y/m/d") ).'</div>';
        if($access == 5 || $access == 6)
        {
            echo '<div class="container"><div class="col-lg-8 col-lg-offset-2">';
            $result = Query("Select * from tbl_work where tbl_work_systemcode='$code' and tbl_work_del='0' and tbl_work_incomplete !='1'");
            $k = 1;
            while ($read = mysqli_fetch_array($result))
               {
                    $userid = $read['tbl_work_userid'];
                    $systemcode = $read['tbl_work_systemcode'];
                    $res = Query("select * from tbl_member where tbl_member_id = '$userid' ");
                    $read_p = mysqli_fetch_array($res);
                    $review = 0;
                    $h = 0;
                    $result_review = Query("select * from tbl_review where tbl_review_systemcode = '$systemcode' order by tbl_review_id DESC");
                    while ($read_review = mysqli_fetch_array($result_review))
                    {
                        $review+=$read_review['tbl_review_rank'];
                        $h++;
                    }
                    
                    $referee = 0;
                    $d = 0;
                    $result_referee = Query("select * from tbl_referee where tbl_referee_systemcode = '$systemcode' order by tbl_referee_id DESC");
                    while ($read_referee = mysqli_fetch_array($result_referee))
                    {
                        $referee+=$read_referee['tbl_referee_rank'];
                        $d++;
                    }
                    
                    
                    echo '<div id="'.$read['tbl_work_systemcode'].'" style="margin-top: 30px;border-top: 1px solid #eee;padding-top: 10px;">
                                <elm id="formwork">';
                                if($access != 5 && $access != 6) {
                                echo '<select class="form-control" id="status" style="float: right;width: 40%;">';
                    
                                for($i=0; $i<=6; $i++)
                                {
                                    echo '<option value="'.$i.'" data-code='.$read['tbl_work_systemcode'].' '.($i == $read['tbl_work_status'] ? 'selected' : '').'>'.$lang[830+$i].'</option>';
                                }
                                    
                                echo '</select>'; } else { echo '<input type="hidden" name="status" value="'.$read['tbl_work_status'].'" id="status">';}
                                echo '<input type="hidden" id="useremail" name="useremail" value="'.$read_p['tbl_member_email'].'">'
                                        . '<input type="hidden" id="country" name="country" value="'.$read_p['tbl_member_country'].'">'
                                        . '<input type="hidden" id="workcode" name="workcode" value="'.$read['tbl_work_code'].'">'
                                        . '<input type="hidden" id="usermobile" name="usermobile" value="'.$read_p['tbl_member_mobile'].'" >'
                                        . '<input type="hidden" id="idwork" value="'.$read['tbl_work_systemcode'].'" name="id" >';
                                if($access == 5) {
                               echo '<label>انتخاب امتیاز بازنگر</label><select class="form-control" id="review" name="review" style="float: right;width: 40%;">'
                              . '<option value="">انتخاب امتیاز بازنگر</option>';
                                for($j=0; $j<=10; $j++)
                                {
                                   $result_review = Query("select * from tbl_review where tbl_review_systemcode = '$systemcode' and tbl_review_userid = '$this_user_id' ");
                                   $read_review = mysqli_fetch_array($result_review);
                                   $this_j = ($j == 0 ? '0' : $j.'0');
                                   echo '<option value="'.$this_j.'" '.($read_review['tbl_review_rank'] ==$this_j ? 'selected' :'' ).'>'.$this_j.'</option>'; 
                                }
                                echo '</select>'; } else { echo '<input type="hidden" name="review" value="" id="review">'; }
                                if($access == 6) {
                               echo '<label>انتخاب امتیاز داور</label><select class="form-control" id="referee" name="referee" style="float: right;width: 40%;">'
                              . '<option value="">انتخاب امتیاز داور</option>';
                                for($j=0; $j<=10; $j++)
                                {
                                   $result_review = Query("select * from tbl_referee where tbl_referee_systemcode = '$systemcode' and tbl_referee_userid = '$this_user_id' ");
                                   $read_review = mysqli_fetch_array($result_review);
                                   $this_j = ($j == 0 ? '0' : $j.'0');
                                   echo '<option value="'.$this_j.'" '.($read_review['tbl_referee_rank'] ==$this_j ? 'selected' :'' ).'>'.$this_j.'</option>'; 
                                }
                                echo '</select>'; } else { echo '<input type="hidden" name="referee" value="" id="referee">'; }
                                echo '<input type="'.($access != 5 && $access != 6 ? 'text' : 'hidden').'" name="msg" id="msg" class="form-control" value="'.$read['tbl_work_msg'].'"><input name="submit" type="submit" value="ثبت" class="sendstatus btn btn-xs btn-success top-margin" style="margin: 0px;height: 30px;width: 100px;"></elm></td>
                                </div>';
                            $k ++;
               }
            
            echo '<br><br></div></div>';
        }
        ?>
        </div>
    </body>
</html>