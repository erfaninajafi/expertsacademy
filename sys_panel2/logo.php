<?php
// include
include './includer.php';
$show_per_page = '30';
$pagetitle = $lang[1546];
$Tabpage = '25';
$thisaccess = '1,4';
include './header.inc.php';

// end include 
?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle?></h4>
                            <?=(!isset($do) ? '<div class="plus">
                                <a href="?do=add"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <?=(!isset($do) ? '<div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-4.png);">
            </div>' : '') ?>
            <div class="card-body p-0 position-relative">
                    <?php
                    if (!isset($do)) { ?>
                <table>
                    </tr>
                    <td class="col-sm-6 p-3">
                        <form action="?do=search" method="POST" class="search-form">
                            <input type="text" class="form-control" value="<?= $keyword ?>" name="keyword">
                            <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                        </form>
                    </td>
                    </tr>
                </table>
<?php

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_logo Where tbl_logo_title LIKE '%$keyword%' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_logo Where  tbl_logo_title LIKE '%$keyword%' ORDER BY tbl_logo_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }
                        // start show list
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                    <tr >
                                        <th>' . $lang[60] . '</th>
                                        <th>'.$lang[1547].'</th>
                                        <th>' . $lang[63] . '</th>
                                    </tr>
                                </thead>
                                <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            echo '<tr>
                                <td class="align-middle p-2">' . $i . '</td>
                                <td class="align-middle p-2"><a href="?do=edit&id=' . $read['tbl_logo_id'] . '">' . $read['tbl_logo_title'] . '</a></td>
                                <td class="align-middle p-2">
                                <a href="?do=edit&id=' . $read['tbl_logo_id'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                                <a href="?do=delete&id=' . $read['tbl_logo_id'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>
                                </td></tr>';
                            $i++;
                        }

                        echo '</tbody></table>';
                        // start pagination                
                        if ($page > $page_number) {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
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
                    ?>
                    <table class="table">
                        <tr>
                            <td class="col-lg-9">
                                <ul class="pagination">
                                    <li><a class="page-link" href="<?= $link . 'page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= $link . 'page=' . ($page + 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-3">
                                <form action="logo.php?do=gotopage" class="search-form" method="POST">
                                    <div class="form-group">
                                        <button type="submit" name="gotopage" class="btn btn-success"><?= $lang[76] ?></button>
                                        <input type="text" class="form-control go-fld" name="page_number">
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </table>
                    <?php
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
                        echo '<div class="col-lg-6 p-3"><p>' . $lang[25] . '</p><h3>' . $read['tbl_logo_title'] . '</h3><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
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
                        echo '<div class="col-lg-10 col-12 p-3">
                          <form action="?do=additem" method="POST" enctype="multipart/form-data">';
                        ?>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label><?=$lang[1547]?></label>
                            <input class="form-control" name="title" type="text" required="">
                        </div>
                        <div class="form-group">
                            <label><?=$lang[1548]?></label>
                            <input class="form-control" name="img" type="file" required="">
                        </div>
                    </div>

                    <?php
                        echo '<div class="col-lg-12"><div class="form-group">
                <input type="submit" name="additem" value="'.$lang[31] .'" class="btn btn-primary mt-3">
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

                        echo '<div class="col-lg-10 col-12 p-3">
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
                   


                    <hr>
                    <?php
                        echo '<div class="form-group">
                <input type="hidden" value = "' . $id . '" name="id" >
                <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3">
                </div>
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
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include './footer.inc.php';

?>