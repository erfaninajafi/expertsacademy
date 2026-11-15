<?php
include './includer.php';
$pagetitle = 'منو پایین سایت';
$show_per_page = '10';
$Tabpage = '6';
$thisaccess = '1,3';
include './header.inc.php';

$result = Query("SELECT * From tbl_menu_footer Where tbl_menu_footer_lng='$language_default' ");
// start count db result for pagination               
$count_result = mysqli_num_rows($result);
$start_contnet =  ($page - 1) * $show_per_page;
$page_number = ceil($count_result / $show_per_page);
// end count db result for pagination 

$result = Query("SELECT * From tbl_menu_footer Where tbl_menu_footer_lng='$language_default'  ORDER BY tbl_menu_footer_order DESC ");
?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    // list content

                    if (!isset($do)) {
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table></tr><td class="col-sm-9"><a class="btn btn-primary margin-left" href="?do=add" >' . $lang[86] . '</a></td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="menu_footer.php?do=search" method="POST">
                       <input type="text" class="form-control" id="menu_title" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-primary" >' . $lang[77] . '</button>
                       </form></td></tr></table>';
                        $row = mysqli_num_rows($result);
                        // 0 result search error 
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        // start show list
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
    <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[50] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;  // for record number
                        $result = Query("SELECT * From tbl_menu_footer Where tbl_menu_footer_parentid = '0' and tbl_menu_footer_lng='$language_default' ORDER BY tbl_menu_footer_order ASC ");
                        // check parent

                        while ($read = mysqli_fetch_array($result)) {
                            // parent menu code
                            $code = $read['tbl_menu_footer_code'];
                            echo '<tr>
                                <td style="width: 1%">' . $i . '</td>
                                <td class="col-sm-6" style="line-height:34px;" >';

                            echo $read['tbl_menu_footer_title']
                                . '<a href="?code=' . $read['tbl_menu_footer_code'] . '&do=edit" class="btn btn-xs btn-success" style="font-size:8px;">' . $lang[23] . '</a>'
                                . '<a href="?code=' . $read['tbl_menu_footer_code'] . '&do=delete" class="btn btn-xs btn-danger" style="font-size:8px;">' . $lang[24] . '</a>';
                            // read sub menu 
                            $result_sub = Query("Select * from tbl_menu_footer Where tbl_menu_footer_parentid = '$code' and tbl_menu_footer_lng = '$language_default' ");
                            $row = mysqli_num_rows($result_sub);
                            if ($row !== 0) {
                                while ($read =  mysqli_fetch_array($result_sub)) {
                                    echo '<br>|_________' . $read['tbl_menu_footer_title'] . '<a href="?code=' . $read['tbl_menu_footer_code'] . '&do=edit" class="btn btn-xs btn-success" style="font-size:8px;">' . $lang[23] . '</a><a href="?code=' . $read['tbl_menu_footer_code'] . '&do=delete" class="btn btn-xs btn-danger" style="font-size:8px;">' . $lang[24] . '</a>';
                                    // sub menu code
                                    $code = $read['tbl_menu_footer_code'];
                                    // read sub sub menu
                                    $result_sub_sub = Query("Select * from tbl_menu_footer Where tbl_menu_footer_parentid = '$code' and tbl_menu_footer_lng = '$language_default' ");
                                    $row = mysqli_num_rows($result_sub_sub);
                                    if ($row !== 0) {
                                        while ($read =  mysqli_fetch_array($result_sub_sub)) {
                                            echo '<br>|_____________________' . $read['tbl_menu_footer_title'] . '<a href="?code=' . $read['tbl_menu_footer_code'] . '&do=edit" class="btn btn-xs btn-success" style="font-size:8px;">' . $lang[23] . '</a><a href="?code=' . $read['tbl_menu_footer_code'] . '&do=delete" class="btn btn-xs btn-danger" style="font-size:8px;">' . $lang[24] . '</a>';
                                        }
                                    }
                                }
                            }

                            echo ' </td></tr>';
                            $i++;
                        }
                        echo '</tbody></table>';
                    }

                    /////////////////////////////////////////////////// Start Action's
                    // start = add item
                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-6">
                          <form action="menu_footer.php?do=additem" method="POST" >
                           <input type="hidden" name="code" value="' . Code("", "no", 7) . '" >
                           <div class="form-group">
                           <label>' . $lang[68] . '</label><div>
                           <select name="cat_id"  data-placeholder="' . $lang[70] . '" style="width:100%;" class="select2" ><option value="0">' . $lang[84] . '</option>';
                        // read menu

                        while ($read_cat =  mysqli_fetch_array($result)) {
                            $parnetid = $read_cat['tbl_menu_footer_parentid'];
                            if ($parnetid == '0') {
                                echo '<option value="' . $read_cat['tbl_menu_footer_code'] . '" class="group-cat">' . $read_cat['tbl_menu_footer_title'] . '</option>';
                                // read sub menu                  
                                $parentcode = $read_cat['tbl_menu_footer_code'];
                                $result_cat = Query("SELECT * From tbl_menu_footer Where tbl_menu_footer_parentid='$parentcode' and tbl_menu_footer_lng='$language[1]' ORDER BY tbl_menu_footer_id DESC ");
                                while ($read_sub = mysqli_fetch_array($result_cat)) {
                                    //echo '<option value="'.$read_sub['tbl_menu_footer_code'].'" class="group-sub">'.$read_sub['tbl_menu_footer_title'].'</option>';
                                }
                                // end sub category        
                            }
                        }
                        echo '</select></div></div><hr>';

                        // end read menu
                        // add title
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="title_' . $language[$i] . '" class="form-control">
                              </div>';
                        }
                        // add link                   
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[83] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="link_' . $language[$i] . '" class="form-control">
                              </div>';
                        }
                        echo '<hr><div class="form-group">
                              <label>' . $lang[85] . '</label>
                              <input type="text" name="order_by" class="form-control">
                              </div><div class="form-group">
                              <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary">
                              </div></form></div>';
                    }


                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $cat_id = $_POST['cat_id'];
                            if (!empty($_POST['order_by'])) {
                                $order = $_POST['order_by'];
                            } else {
                                $order = 0;
                            }

                            // for language
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = SafeInput($_POST['title_' . $language[$i]]);
                                $link_[$i] = $_POST['link_' . $language[$i]];

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: menu_footer.php");
                                    exit();
                                }
                                $language_name = $language[$i];
                                // insert query
                                Query("INSERT INTO tbl_menu_footer (tbl_menu_footer_code,tbl_menu_footer_title,tbl_menu_footer_parentid,tbl_menu_footer_link,tbl_menu_footer_order,tbl_menu_footer_lng) values('$code','$title_[$i]','$cat_id','$link_[$i]','$order','$language_name')");
                                $_SESSION['error'] = $lang[32];
                                header("Location: menu_footer.php");
                            }
                        }
                    }
                    // end = add item

                    // start = delete category
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_menu_footer Where tbl_menu_footer_code = '$code' and tbl_menu_footer_lng='$language_default' ");
                        $read = mysqli_fetch_array($result);
                        echo $lang[25] . '<br /><br />' . $read['tbl_menu_footer_title'];
                        echo '<div class="col-lg-6">
                <form action="#" method="POST">
                <input type="hidden" name="code" value="' . $code . '" >
                <div class="form-group margin-top">
                <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger">
                </form></div>';

                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];

                            $result = Query("SELECT * from tbl_menu_footer Where tbl_menu_footer_parentid = '$code' and tbl_menu_footer_lng='$language_default' ");
                            $row_parent = mysqli_num_rows($result);
                            $read = mysqli_fetch_array($result);

                            if ($row_parent > 0) {
                                $_SESSION['error'] = $lang[71];
                                header("Location: menu_footer.php");
                                exit();
                            }
                            Query("Delete From tbl_menu_footer Where tbl_menu_footer_code = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: menu_footer.php");
                            exit();
                        }
                        if (isset($_POST['nodelete'])) {
                            header("Location: menu_footer.php");
                        }
                    }
                    // end delete category

                    // edit item start
                    if (isset($do) && $do == 'edit') {
                        // this function for read multi language content - check kardan chand zaban baraye value field ha 
                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_menu_footer_lng = '$language_id'";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_menu_footer Where tbl_menu_footer_code =  '$code'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        // end function
                        //start style selected item and read now select code
                        $result_style = Query("SELECT * FROM tbl_menu_footer Where tbl_menu_footer_code =  '$code' ");
                        $result_style_read = mysqli_fetch_array($result_style);
                        $parent_id_style = $result_style_read['tbl_menu_footer_parentid'];
                        //end style selected item
                        echo '<div class="col-lg-6">
                <form action="menu_footer.php?&do=edit" method="POST" enctype="multipart/form-data" >
                <input type="hidden" name="code" value="' . $code . '" >
                <div class="form-group">
                <label>' . $lang[68] . '</label><div>
                <select name="cat_id"  data-placeholder="' . $lang[70] . '" style="width:100%;" class="chzn-select" ><option value="0">' . $lang[69] . '</option>';
                        // read menu
                        while ($read_cat =  mysqli_fetch_array($result)) {
                            // Creat style select
                            if ($parent_id_style == $read_cat['tbl_menu_footer_code']) {
                                $style = 'selected';
                            } else {
                                $style = '';
                            }
                            $parentid = $read_cat['tbl_menu_footer_parentid'];
                            if ($parentid == '0') {
                                echo '<option  ' . $style . ' value="' . $read_cat['tbl_menu_footer_code'] . '" class="group-cat">' . $read_cat['tbl_menu_footer_title'] . '</option>';
                                // read sub category                   
                                $parentcode = $read_cat['tbl_menu_footer_code'];
                                $result_cat = Query("SELECT * From tbl_menu_footer Where tbl_menu_footer_parentid='$parentcode' and tbl_menu_footer_lng='$language[1]' ORDER BY tbl_menu_footer_id DESC ");
                                while ($read_sub = mysqli_fetch_array($result_cat)) {
                                    // Creat style select sub
                                    $parentid_sub = $read_sub['tbl_menu_footer_code'];
                                    $result_cat_sub = Query("SELECT * From tbl_menu_footer Where tbl_menu_footer_code='$parentid_sub' and tbl_menu_footer_lng='$language[1]' ORDER BY tbl_menu_footer_id DESC ");
                                    $read_cat_sub = mysqli_fetch_array($result_cat_sub);
                                    echo $result_style_read['tbl_menu_footer_parentid'] . '/' . $read_cat_sub['tbl_menu_footer_code'];
                                    if ($result_style_read['tbl_menu_footer_parentid'] == $read_cat_sub['tbl_menu_footer_code']) {
                                        $style = 'selected';
                                    } else {
                                        $style = '';
                                    }
                                    //echo '<option '.$style.' value="'.$read_sub['tbl_menu_footer_code'].'" class="group-sub">'.$read_sub['tbl_menu_footer_title'].' _____</option>';
                                }
                                // end sub category        
                            }
                        }
                        echo '</select></div></div><hr>';
                        // end read menu
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="title_' . $language[$i] . '" class="form-control" value="' . read_fild($language[$i], 'tbl_menu_footer_title') . '">
                              </div>';
                        }
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[83] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="link_' . $language[$i] . '" class="form-control" value="' . read_fild($language[$i], 'tbl_menu_footer_link') . '">
                              </div>';
                        }
                        echo '<div class="form-group">
                                <label>' . $lang[85] . '</label>
                                <input type="text" name="order_by" class="form-control" value="' . read_fild(NULL, 'tbl_menu_footer_order') . '">
                                </div>
                                <div class="form-group">
                                <input type="submit" name="update" value="' . $lang[20] . '" class="btn btn-primary">
                                </div></form></div>';

                        if (isset($_POST['update'])) {
                            $cat_id = $_POST['cat_id'];
                            $code = $_POST['code'];
                            if (!empty($_POST['order_by'])) {
                                $order = $_POST['order_by'];
                            } else {
                                $order = 0;
                            }

                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = SafeInput($_POST['title_' . $language[$i]]);
                                $link_[$i] = SafeInput($_POST['link_' . $language[$i]]);

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: menu_footer.php");
                                    exit();
                                }
                                $language_name = $language[$i];
                                // update query
                                Query("Update tbl_menu_footer SET tbl_menu_footer_parentid= '$cat_id' , tbl_menu_footer_title = '$title_[$i]' , tbl_menu_footer_link = '$link_[$i]' , tbl_menu_footer_order = '$order' where tbl_menu_footer_code= '$code' and tbl_menu_footer_lng='$language[$i]'");
                                $_SESSION['error'] = $lang[21];
                                header("Location: menu_footer.php");
                            }
                        }
                    }
                    // search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: menu_footer.php?keyword=$keyword_search");
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include './footer.inc.php';

?>