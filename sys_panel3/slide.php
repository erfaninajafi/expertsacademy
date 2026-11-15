<?php
include './includer.php';
 $show_per_page = '10';
$pagetitle = $lang[35];
$Tabpage = '6';
$thisaccess = '1,3';
include './header.inc.php';
echo '<section class="content-header">
      <h1>'.$pagetitle.'</h1>
    </section>
        <section class="content"><div class="col-md-12">
<div class="box box-body">';
// list content
            if(!isset($do))
            {
// search and add button
                echo  '<div class="form-group">
                       '.(!empty($error) ? '<div class="error" ><span>'.$error.'</span></div>' : '' ).'
                       </div>
                       <div class="col-lg-12 padding-clear margin-bottom"><a class="btn btn-primary " href="?do=add" >'.$lang[31].'</a></td>
                       </div>';
                
// start count db result for pagination               
                $result_for_count = Query("SELECT * From tbl_slide Where tbl_slide_lng='$language_default' " );
                $count_result = mysqli_num_rows($result_for_count);
                $start_contnet =  ($page - 1 ) * $show_per_page;
                $page_number = ceil($count_result / $show_per_page);
// end count db result for pagination               
                $result = Query("SELECT * From tbl_slide Where tbl_slide_lng='$language_default' ORDER BY tbl_slide_id DESC LIMIT $start_contnet,$show_per_page " );
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
                                    <th>'.$lang[50].'</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                while ($read = mysqli_fetch_array($result))
               {
                    echo '
                            <tr>
                                <td style="width: 1%">'.$i.'</td>
                                <td class="col-sm-10"><a href="?do=edit&code='.$read['tbl_slide_code'].'" >'.$read['tbl_slide_title'].'</a></td>
                                </tr><tr>
                                <td class="col-sm-1 btn-class" colspan="2">
                                <a href="?do=edit&code='.$read['tbl_slide_code'].'" class="btn btn-xs btn-success " >'.$lang[23].'</a>
                                <a href="?do=delete&code='.$read['tbl_slide_code'].'" class="btn btn-xs btn-danger">'.$lang[24].'</a></td>
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
        <a href="?page='.($back_page_number - 1).''.$other_page_keyword.$keyword.'">'.$lang[75].'</a>
       </li></ul></td>
       <td class="col-sm-1">
       <form action="page.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                header("Location: slide.php?page=$page_number");
            }
            
        }
// end = pagination
// start = do add
            if(isset($do) && $do == 'add')
            {
                    echo '<div class="col-lg-6">
                          <form action="?do=additem" method="POST" enctype="multipart/form-data">
                           <input type="hidden" name="code" value="'.Code("", "no", 7 ).'" >';

                    for($i=1; $i<=$language_count; $i++)
                    {
                        echo '<div class="form-group">
                              <label>'.$lang[50].'<span class="language">'.$language[$i].'</span></label>
                              <input type="text" name="title_'.$language[$i].'" class="form-control">
                              </div>';
                    }
                    for($i=1; $i<=$language_count; $i++)
                    {
                        echo '<div class="form-group">
                              <label>لینک<span class="language">'.$language[$i].'</span></label>
                              <input type="text" name="link_'.$language[$i].'" class="form-control">
                              </div>';
                    }
                    for($i=1; $i<=$language_count; $i++)
                    {
                        echo '<div class="form-group">
                              <label>متن<span class="language">'.$language[$i].'</span></label>
                              <input type="text" name="text_'.$language[$i].'" class="form-control">
                              </div>';
                    }
                        echo '<div class="form-group">
                              <label>'.$lang[53].'</label>
                              <input type="file" name="img" class="form-control">
                              </div>
                              <div class="form-group">
                              <label>رده بندی</label>
                              <input type="text" name="order" class="form-control">
                              </div>
                              <hr>
                             <div class="form-group">
                             <input type="submit" name="additem" value="'.$lang[31].'" class="btn btn-primary">
                             </div>
                </form></div>';
            }
// start = add record to db
            if(isset($do) && $do == 'additem')
            {
                if(isset($_POST['additem']))
                {
                        $code = $_POST['code']; 
                        $order = $_POST['order'];
                        $img = $_FILES['img']['name'];
                        $read_file_name = explode(".", $img);
                        $extn = end($read_file_name);

// upload images after rename 
                     if (empty($img)) {
                             $_SESSION['error'] = $lang[111];
                             header("Location: slide.php");
                             exit();
                        }
                    elseif($extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG')
                        {
                            $img_name = 'slide_'.time().'.'.$extn;
                            move_uploaded_file($_FILES['img']["tmp_name"],"$directory" . $img_name);

                        }
                        else {
                            $_SESSION['error'] = $lang[112];
                            header("Location: slide.php");
                            exit();
                        }
                        
// for language
                    for($i=1; $i<=$language_count; $i++)
                        {
                        $title_[$i] = $_POST['title_'.$language[$i]];
                        $link_[$i] = $_POST['link_'.$language[$i]];
                        $text_[$i] = $_POST['text_'.$language[$i]];
                        
                        if(empty($title_[1]))
                        {
                            $_SESSION['error'] = $lang[56];
                            header("Location: slide.php");
                            exit();
                        }
                        $language_name = $language[$i];
// insert query
                        Query("INSERT INTO tbl_slide (tbl_slide_code,tbl_slide_title,tbl_slide_lng,tbl_slide_img,tbl_slide_order,tbl_slide_link,tbl_slide_text) values('$code','$title_[$i]','$language_name','$img_name','$order','$link_[$i]','$text_[$i]')");
                        $_SESSION['error'] = $lang[32];
                        header("Location: slide.php");
                        }
                    
                }
                
            }
 // end  = add record          
// start = delete record
                if(isset($do) && $do == 'delete')
                {
                    $result = Query("SELECT * from tbl_slide Where tbl_slide_code = '$code' and tbl_slide_lng = '$language_default' ");
                    $read = mysqli_fetch_array($result);
                    echo '<div class="col-lg-6">'.$lang[25].'<br /><h2>'.$read['tbl_slide_title'].'</h2><br /><img src="'.$directory.$read['tbl_slide_img'].'" width="30%"> <br />
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="'.$code.'" name="code" >
                         <input type="submit" name="submit" value="'.$lang[26].'" class="btn btn-success">
                         <input type="submit" name="nodelete" value="'.$lang[27].'" class="btn btn-danger"></form></div>';
                    if(isset($_POST['submit']))
                    {
                        $code = $_POST['code'];

                        Query("DELETE from tbl_slide Where tbl_slide_code = '$code' ");
                        $_SESSION['error'] = $lang[28];
                        header("Location: slide.php");
                        exit();
                    }
// no delete
                    elseif (isset($_POST['nodelete'])) {

                        header("Location: slide.php");
                        exit();

                    }
                }
                
// end = delete item

// start = edit record
                if(isset($do) && $do == 'edit')
                {
                        $code = $_GET['code'];
                        $result = Query("SELECT * FROM tbl_slide Where tbl_slide_code =  '$code'  ");
                        $read = mysqli_fetch_array($result);
// this function for read multi language content - check kardan chand zaban baraye value field ha 
                    function read_fild($language_id = NULL,$field)
                    {
                        if($language_id == NULL)
                        {
                            $check_language_id = "";
                        }
                        else
                        {
                             $check_language_id = "and tbl_slide_lng = '$language_id'";
                        }
                        $code = $_GET['code'];
                        $result = Query("SELECT * FROM tbl_slide Where tbl_slide_code =  '$code'  $check_language_id ");
                        $read = mysqli_fetch_array($result);
                        $read = $read[$field];
                        return $read;
                    }
// end function

// form for edit
                    echo '<div class="col-lg-6">
                          <form action="?do=edititem" method="POST" enctype="multipart/form-data">
                          <input type="hidden" name="code" value="'.$code.'" >
                          <input type="hidden" name="old_img" value="'.$read['tbl_slide_img'].'" >';

                    for($i=1; $i<=$language_count; $i++)
                    {
                        echo '<div class="form-group">
                              <label>'.$lang[50].'<span class="language">'.$language[$i].'</span></label>
                              <input type="text" name="title_'.$language[$i].'" value="'.read_fild($language[$i], 'tbl_slide_title').'" class="form-control">
                              </div>';
                    }
                    for($i=1; $i<=$language_count; $i++)
                    {
                        echo '<div class="form-group">
                              <label>لینک<span class="language">'.$language[$i].'</span></label>
                              <input type="text" name="link_'.$language[$i].'" value="'.read_fild($language[$i], 'tbl_slide_link').'" class="form-control">
                              </div>';
                    }
                    for($i=1; $i<=$language_count; $i++)
                    {
                        echo '<div class="form-group">
                              <label>متن<span class="language">'.$language[$i].'</span></label>
                              <input type="text" name="text_'.$language[$i].'" value="'.read_fild($language[$i], 'tbl_slide_text').'" class="form-control">
                              </div>';
                    }
                      
                    echo '<hr>';
                    $img = read_fild(NULL,'tbl_slide_img');
                    
                             if($img)
                             {
                                echo '<div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">'.$lang[53].'</label></td>
                                      <td><input type="file" name="img"></td><td><a href="../upload/'.$img.'" target="_blank" ><span class="btn btn-xs btn-success"><span class="bx bxs-show"></span></span></a>
                                      </td></tr><table></div>';
                             }
                             else
                             {
                                echo '<div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">'.$lang[53].'</label></td>
                                      <td><input type="file" name="img">
                                      </td></tr><table></div>'; 
                             }
                            echo '<div class="form-group">
                              <label>رده بندی</label>
                              <input type="text" name="order" value="'.read_fild(NULL, 'tbl_slide_order').'" class="form-control">
                              </div>
                                  <div class="form-group">
                                  <input type="submit" name="updateitem" value="'.$lang[20].'" class="btn btn-primary">
                                  </div>
                                  </form></div>';
                }
// end = edit item
// 
// start = update items
                if(isset($do) && $do == 'edititem')
                {
                    if(isset($_POST['updateitem']))
                    {
                        $code = $_POST['code'];
                        $old_img = $_POST['old_img'];
                        $order = $_POST['order'];
                        $img = $_FILES['img']['name'];
                        $read_file_name = explode(".", $img);
                        $extn = end($read_file_name);

// upload images after rename 
                        if(!empty($img))
                        {
                            
                    if($extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG')
                        {
                            $img_name = 'slide_'.time().'.'.$extn;
                            move_uploaded_file($_FILES['img']["tmp_name"],"$directory" . $img_name);

                        }
                    else {
                        $_SESSION['error'] = $lang[112];
                        header("Location: slide.php");
                        exit();
                        }
                        }
                        else {
                            $img_name = $old_img;
                        }
                                for($i=1; $i<=$language_count; $i++)
                            {
                                $title_[$i] = $_POST['title_'.$language[$i]];
                                $link_[$i] = $_POST['link_'.$language[$i]];
                                $text_[$i] = $_POST['text_'.$language[$i]];

                        if(empty($title_[1]))
                            {
                                $_SESSION['error'] = $lang[56];
                                header("Location: slide.php");
                                exit();
                            }

                            $language_name = $language[$i];                 
    // update query
                            Query("Update tbl_slide SET tbl_slide_text='$text_[$i]',tbl_slide_link='$link_[$i]', tbl_slide_title='$title_[$i]' ,  tbl_slide_img = '$img_name' , tbl_slide_order = '$order' Where tbl_slide_code= '$code' and tbl_slide_lng = '$language_name'");

                        }
                        $_SESSION['error'] = $lang[21];
                        header("Location: slide.php");
                    }
                }
// end = update item        
echo '</div></div></section>';
include './footer.inc.php';