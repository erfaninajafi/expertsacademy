<?php
// include
include './includer.php';
$show_per_page = '30';
$pagetitle = 'مدیریت برگزار کننده ';
$Tabpage = '25';
$thisaccess = '1,4';
include './header.inc.php';
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
                       <table></tr><td class="col-sm-9"><a class="btn btn-primary margin-left" href="?do=add" >افزودن مورد جدید</a></td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="?do=search" method="POST">
                       <input type="text" class="form-control" id="member_id" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-default" style="height: 33px; width:50px; padding:4px 8px;">' . $lang[77] . '</button>
                       </form></td></tr></table><div id="result"></div>';
                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_logo Where tbl_logo_title LIKE '%$keyword%' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_logo Where  tbl_logo_title LIKE '%$keyword%' ORDER BY tbl_logo_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);

                        // start show list
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                                <thead class="table-dark">
                <tr >
                    <th>' . $lang[60] . '</th>
                    <th>نام شرکت</th>
                    <th>' . $lang[63] . '</th>
                </tr>
            </thead>
            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            echo '<tr>
            <td style="width: 1%">' . $i . '</td>
            <td class="col-sm-2"><a href="?do=edit&id=' . $read['tbl_logo_id'] . '">' . $read['tbl_logo_title'] . '</a></td>
            <td class="col-sm-3">
            <a href="?do=edit&id=' . $read['tbl_logo_id'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
            <a href="?do=delete&id=' . $read['tbl_logo_id'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a>
            </td></tr>';
                            $i++;
                        }

                        echo '</tbody></table>';
                        // start pagination                
                        if ($page > $page_number) {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                        }
                        if ($count_result > $show_per_page) {
                            if ($page == 1) {
                                $back_page_number = 2;
                            } else {
                                $back_page_number = $page;
                            }
                            // $other_page_keyword = '';
                            if (isset($keyword) && $keyword == TRUE) {
                                $other_page_keyword = "&keyword=";
                            } else {
                                $other_page_keyword = '';
                            }
                            echo '<table class="table-striped margin-top"><tr><td class="col-sm-6"><ul class="pagination pagination-sm">
       <li><a href="' . $link . 'page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . '">' . $lang[75] . '</a></li>
       <li><a>' . $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number . '</a></li>
       <li><a href="' . $link . 'page=' . ($page + 1) . '' . $other_page_keyword . $keyword . '">' . $lang[74] . '</a></li>
       </ul></td>
       <td class="col-sm-1">
       <form action="logo.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
       <div class="form-group">
       <input type="text" class="form-control" name="page_number"  style="height: 28px; width:30px; padding:6px 8px;">
       </div><button type="submit" name="gotopage" class="btn btn-default" style="height: 28px; width:30px; padding:4px 8px;">' . $lang[76] . '</button>
       </form></td></tr></table>';
                        }
                    }
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: logo.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: logo.php?keyword=$keyword_search");
                        }
                    }
                    // end = search result  
                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_logo  Where tbl_logo_id = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br /><h2>' . $read['tbl_logo_title'] . '</h2><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $id = $_POST['id'];

                            $result = Query("Select * From tbl_logo where tbl_logo_id = '$id' ");
                            $read = mysqli_fetch_array($result);
                            $img = $read['tbl_logo_logo'];

                            if (isset($img)) {
                                unlink($directory . $img);
                            }
                            Query("DELETE from tbl_logo Where tbl_logo_id = '$id' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: logo.php");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {
                            header("Location: logo.php");
                            exit();
                        }
                    }

                    // end = delete item

                    // start = edit record
                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-12">
                          <form action="?do=additem" method="POST" enctype="multipart/form-data">';
                    ?>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>نام شرکت</label>
                                <input class="form-control" name="title" type="text" required="">
                            </div>
                            <div class="form-group">
                                <label>لوگو</label>
                                <input class="form-control" name="img" type="file" required="">
                            </div>
                        </div>

                    <?php
                        echo '<div class="col-lg-12"><div class="form-group">
                <input type="submit" name="additem" value="افزودن شرکت" class="btn btn-primary">
                </div></div>
              </form></div>';
                    }
                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {

                            $title = protect($_POST['title']);
                            if ($_FILES['img']['name']) {
                                $img = $_FILES['img']['name'];
                                $read_file_name = explode(".", $img);
                                $extn = end($read_file_name);
                                $img = 'logo_' . time() . '.' . $extn;
                                if ($extn == 'jpg' || $extn == 'JPG'  || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                    move_uploaded_file($_FILES['img']["tmp_name"], "../upload/logo/" . $img);
                                }
                            }
                            Query("insert into tbl_logo  (
            tbl_logo_title,
            tbl_logo_img) 
            values(
            '$title',
            '$img') ");

                            $_SESSION['error'] = $lang[391];
                            header("Location: logo.php");
                            exit();
                        }
                        // end = update item
                    }
                    // end = edit item
                    if (isset($do) && $do == 'edit') {
                        $result = Query("Select * from tbl_logo Where tbl_logo_id = '$id' ");
                        $read = mysqli_fetch_array($result);

                        echo '<div class="col-lg-12">
                          <form action="?do=edititem" method="POST" enctype="multipart/form-data">';
                    ?>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label><?= $lang[127] ?></label>
                                <input class="form-control" name="title" value="<?= $read['tbl_logo_title'] ?>" type="text" required="">
                            </div>

                            <div class="form-group">
                                <label for="lastname"><?= $lang[365] ?></label>
                                <input class="form-control" name="img" type="file" <?= (!empty($read['tbl_logo_img']) ? '' : 'required=""') ?>>
                            </div>

                            <?php if (!empty($read['tbl_logo_img'])) { ?>
                                <a href="../upload/logo/<?= $read['tbl_logo_img'] ?>" target="_blank"><img width="80" height="80" src="../upload/logo/<?= $read['tbl_logo_img'] ?>" /></a>
                            <?php } else { ?>
                                <a href="../upload/logo/<?= $read['tbl_logo_img'] ?>" target="_blank"><img width="80" height="80" src="../asset/images/no-image.jpg" /></a>
                            <?php } ?>
                        </div>



                        <hr>
                    <?php
                        echo '<div class="col-lg-12"><div class="form-group">
                <input type="hidden" value = "' . $id . '" name="id" >
                <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary">
                </div></div>
              </form></div>';
                    }
                    // end = edit item
                    // start = update items
                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['updateitem'])) {
                            $id = $_POST['id'];
                            $title = protect($_POST['title']);

                            $result = Query("select * from tbl_logo where tbl_logo_id = '$id' ");
                            $read = mysqli_fetch_array($result);

                            if (empty($_FILES['img']['name'])) {
                                $img = $read['tbl_logo_img'];
                            } else {
                                if ($_FILES['img']['name']) {
                                    $img = $_FILES['img']['name'];
                                    $read_file_name = explode(".", $img);
                                    $extn = end($read_file_name);
                                    $img = 'logo_' . time() . '.' . $extn;
                                    if ($extn == 'jpg' || $extn == 'JPG'  || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                        move_uploaded_file($_FILES['img']["tmp_name"], "../upload/logo/" . $img);
                                    }
                                }
                            }

                            Query("update tbl_logo SET  tbl_logo_title='$title',tbl_logo_img='$img' where tbl_logo_id = '$id' ");
                            $_SESSION['error'] = $lang[391];
                            header("Location: logo.php");
                            exit();
                        }
                    }
                    echo '</div></div></div></section>';
                    include './footer.inc.php';
                    ?>