<?php
include './includer.php';
$pagetitle = '- '.$lang[37];
$Tabpage = '9';
$thisaccess = '1';
include './header.inc.php';
$result = Query("SELECT * From tbl_seo");
$read = mysqli_fetch_array($result);
echo '
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"> '.$lang[37].' </h1>
        </div>
    
        <div class="col-lg-6 col-lg-offset-6">
    <form action="?do=update" method="POST">
     <div class="form-group">
                  <div class="error" style="font:14px BYekan; color:green;"><span>'.$error.'</span></div>
                </div>
                <div class="form-group">
                <label>'.$lang[38].'</label>
                <input type="text" name="webmaster" value="'.$read['tbl_seo_webmaster'].'" class="form-control">
            </div>
          <div class="form-group">
                <label>'.$lang[39].'</label>
                <textarea name="analysis" class="form-control">'.$read['tbl_seo_analysis'].'</textarea>
            </div>
            <div class="form-group">
                <input type="submit" name="submit" value="'.$lang[20].'" class="btn btn-primary">
            </div>
                  </form>
        </div>
    </div>';

if(isset($do) && $do == 'update')
{
    $webmaster = '';
    $analysis = '';
    
    if(isset($_POST['submit']))
    {
        $webmaster = htmlspecialchars($_POST['webmaster'], ENT_COMPAT,'ISO-8859-1', true);
        $analysis = htmlspecialchars($_POST['analysis'], ENT_COMPAT,'ISO-8859-1', true);
            
        $result = Query("Select * from tbl_seo");
        $count = mysqli_num_rows($result);
        if($count == '1')
        {
            Query("Update tbl_seo SET tbl_seo_webmaster = '$webmaster' , tbl_seo_analysis = '$analysis' ");
            $_SESSION['error'] = $lang[21];
            header("Location: seo_setting.php"); 
        }
 else {
            Query("Insert INTO tbl_seo  (tbl_seo_webmaster,tbl_seo_analysis) values('$webmaster','$analysis') ");
            $_SESSION['error'] = $lang[21];
            header("Location: seo_setting.php"); 
 }
        
    }
}
echo '</div>';
include './footer.inc.php';

?>
