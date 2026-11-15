<?php
include './includer.php';
$pagetitle = 'خبرنامه';
$show_per_page = '10';
$Tabpage = '17';
include './header.inc.php';

// end include 

              echo '<div class="row">
                    <div class="col-lg-12">
                    <h1 class="page-header"> '.$pagetitle.' </h1>
                    </div>
                    <div class="col-lg-11 col-lg-offset-1">';
// list content
            if(!isset($do))
            {
// search and add button
                echo  '<div class="form-group">
                       <div class="error" ><span>'.$error.'</span></div>
                       </div>
                       <table></tr><td class="col-sm-9"><a class="btn btn-primary margin-left" href="custom_export.php?do=newsletter_filter" >'.$lang[89].'</a></td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="newsletter.php?do=search" method="POST">
                       <input type="text" class="form-control" id="newsletter_name" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-default" style="height: 33px; width:50px; padding:4px 8px;">'.$lang[77].'</button>
                       </form></td></tr></table><div id="result"></div>';
                
// start count db result for pagination               
                $result_for_count = Query("SELECT * From tbl_newsletter" );
                $count_result = mysqli_num_rows($result_for_count);
                $start_contnet =  ($page - 1 ) * $show_per_page;
                $page_number = ceil($count_result / $show_per_page);
// end count db result for pagination               
                $result = Query("SELECT * From tbl_newsletter Where tbl_newsletter_name LIKE '%$keyword%' ORDER BY tbl_newsletter_id DESC LIMIT $start_contnet,$show_per_page " );
// error : if not find record show no item for view
                $row = mysqli_num_rows($result);
                if(!isset($do) && $row == '0') 
                    {
                    echo '<div class="margin-top">'.$lang[57].'</div>';
                    exit();
                    }                
// start show list
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;" >
                            <thead>
                                <tr >
                                    <th>'.$lang[60].'</th>
                                    <th>نام</th>
                                    <th>موبایل</th>
                                    <th>ایمیل</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                while ($read = mysqli_fetch_array($result))
               {
                    echo '
                            <tr>
                                <td style="width: 1%">'.$i.'</td>
                                <td class="col-sm-3" >'.$read['tbl_newsletter_name'].'</td>
                                <td class="col-sm-2">'.$read['tbl_newsletter_mobile'].'</td>
                                <td class="col-sm-3">'.$read['tbl_newsletter_email'].'</td>
                                <td class="col-sm-1">
                                <a href="?do=delete&code='.$read['tbl_newsletter_id'].'" class="btn btn-xs btn-danger">'.$lang[24].'</a></td>
                                </tr>';
                            $i ++;
               }

                    echo '</tbody></table>';
// start pagination                
                                if ($page > $page_number)
                    {
                        echo '<div class="margin-top">'.$lang[57].'</div>';
                        exit();
                    }
                    if ($count_result > $show_per_page) {
                        if ($page == 1)
                        {
                            $back_page_number = 2;
                        }
                        else {
                            $back_page_number = $page;
                        }

                        // $other_page_keyword = '';
                        if(isset($keyword) && $keyword == TRUE)
                        {
                        $other_page_keyword = "&keyword=";
                        }
                         else {
                           $other_page_keyword = '';
                        }
echo '<table class="table-striped margin-top"><tr><td class="col-sm-6"><ul class="pagination pagination-sm"><li>
        <a href="?page='.($page + 1).''.$other_page_keyword.$keyword.'">'.$lang[74].'</a>
       </li>
       <li>
        <a>'.$lang[72].'&nbsp;'.$page.'&nbsp;'.$lang[73].'&nbsp;' .$page_number.'</a>
       
</li>
       <li>
        <a href="?t='.$t.'&page='.($back_page_number - 1).''.$other_page_keyword.$keyword.'">'.$lang[75].'</a>
       </li></ul></td>
       <td class="col-sm-1">
       <form action="newsletter.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
       <div class="form-group">
       <input type="text" class="form-control" name="page_number"  style="height: 28px; width:30px; padding:6px 8px;">
       </div><button type="submit" name="gotopage" class="btn btn-default" style="height: 28px; width:30px; padding:4px 8px;">'.$lang[76].'</button>
       </form></td></tr></table>';
}
            }
        if(isset($do) && $do == 'gotopage') 
        {
            if(isset($_POST['gotopage']))
            {
                $page_number = $_POST['page_number'];
                header("Location: newsletter.php?page=$page_number");
            }
            
        }
// end = pagination
// start = search result               
                            if(isset($do) && $do == 'search')
            {
                if(isset($_POST['go']))
                {
                    $keyword_search = $_POST['keyword'];
                    header("Location: newsletter.php?keyword=$keyword_search");
                    
                }

                
                
            }
// end = search result  
// ////////////////////////////////////////////////////////////////////////////
// /////////////////////////////// start actions//////////////////////////////
            
            if(isset($do) && $do == 'delete')
            {
                $result = Query("SELECT * from tbl_newsletter Where tbl_newsletter_id = '$code' ");
                $read = mysqli_fetch_array($result);
               echo $lang[25].'<br /><br />'.$read['tbl_newsletter_name'];
            echo '<div class="col-lg-6 col-lg-offset-6">
                <form action="#" method="POST">
                <input type="hidden" name="code" value="'.$code.'" >
                <div class="form-group margin-top">
                <input type="submit" name="submit" value="'.$lang[26].'" class="btn btn-success">
                <input type="submit" name="nodelete" value="'.$lang[27].'" class="btn btn-danger">
                </form></div>';
    
            if(isset($_POST['submit']))
            {
                $code = $_POST['code'];

                Query("Delete From tbl_newsletter Where tbl_newsletter_id = '$code' ");
                $_SESSION['error'] = $lang[28];
                header("Location: newsletter.php");
                exit();
            }
            if(isset($_POST['nodelete']))
            {
                header("Location: newsletter.php");
            } 
            }
            
            echo '</div>';

include './footer.inc.php';

?>
