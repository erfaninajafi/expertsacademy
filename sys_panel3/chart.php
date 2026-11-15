<?php
// include
            include './includer.php';
            $pagetitle = 'چارت';
            $Tabpage = '10';
            $thisaccess = '1,4';
            include './header.inc.php';
// end include 
echo '<section class="content-header">
      <h1>
        '.$pagetitle.'
      </h1>
    </section>
        <section class="content"><div class="col-md-12">
            <div class="box box-body charti">';  
            if(isset($_GET['section']))
            {
                $thisselect = " and tbl_work_coursecode = '$_GET[section]' ";
            }
            else {
                $thisselect = "";
            }
            $st_row = '';
            $title_row = '';
            for($i=0; $i<=6; $i++)
                {
                    $result = Query("SELECT * From tbl_work Where  tbl_work_incomplete !='1' and tbl_work_status='$i' $thisselect ");
                    $l = 830+$i;
                    $title_row .="'$lang[$l]',";
                    $st_row .= mysqli_num_rows($result).',';
                }
                $result = Query("SELECT * From tbl_festcourse Where  tbl_festcourse_lng ='fa' and tbl_festcourse_systemcode='$_GET[section]'");
                $read = mysqli_fetch_array($result);
                $festival = $read['tbl_festcourse_festival'];
                $result_festival = Query("SELECT * From tbl_festival Where  tbl_festival_lng ='fa' and tbl_festival_systemcode='$festival'");
                $read_festival = mysqli_fetch_array($result_festival);
                echo '<h1>'.$read_festival['tbl_festival_title'].' / '.$read['tbl_festcourse_title'].'</h1>';
                $a = rtrim($title_row , ",");
                $a = explode(",", $a);
                $b = rtrim($st_row , ",");
                $b = explode(",", $b);
                foreach ($a as $key => $value) {
                    echo '<h2>'. str_replace("'", "", $value).' : '.$b[$key].'</h2>';
                }
                ?>
<div class="ct-chart"></div>
                <script>
   new Chartist.Bar('.ct-chart', {
  labels: [<?=rtrim($title_row , ",")?>],
  series: [<?=rtrim($st_row , ",")?>]
}, {
  distributeSeries: true
});
var ctx = document.getElementById("myChart");
ctx.height = 500;
    </script>
<?php
echo '</div></div></section>';
include './footer.inc.php';
?>

