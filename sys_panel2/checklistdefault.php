<?php
include './includer.php';
$show_per_page = '30';
$pagetitle = $lang[1940];
$Tabpage = '25';
$thisaccess = '1,9';
include './header.inc.php';
$checklistQuerry='';
if($access !='9') {
        header("Location: index.php");
        exit();
}
?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle?></h4>
                            <div class="plus">
                                <a href="?do=add"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
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
                        $result_for_count = Query("SELECT * From tbl_checklistdefault where tbl_checklistdefault_admin='$userCode' and tbl_checklistdefault_title like '%$keyword%' and tbl_checklistdefault_delete='0' and tbl_checklistdefault_lng='$language_default'   ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_checklistdefault where tbl_checklistdefault_admin='$userCode' and  tbl_checklistdefault_title like '%$keyword%' and tbl_checklistdefault_delete='0' and tbl_checklistdefault_lng='$language_default'  order by tbl_checklistdefault_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }
                   
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>'.$lang[1944].' </th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            echo '<tr>
                        <td class="align-middle p-2">' . $i . '</td>
                        <td class="align-middle p-2">' . $read['tbl_checklistdefault_title'] . '</td>
                        <td class="align-middle p-2">
                        '.($access =='9' ? '<a href="?do=edit&code=' . $read['tbl_checklistdefault_code'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                        <a href="?do=delete&code=' . $read['tbl_checklistdefault_code'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>' : '' ).'
                        

                        </td>
                        </tr>';
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
                                <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '')  ?>"><?= $lang[75] ?></a>
                                </li>
                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                </li>
                                <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '') ?>"><?= $lang[74] ?></a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
                <?php
                        }
                    }
                  
                    // end = pagination
                    // start = do add
                    if (isset($do) && $do == 'add' && $access =='9') {
                        echo '<div class="col-lg-12 col-12 p-3">
                        <form action="?do=additem" method="POST" enctype="multipart/form-data">';

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                                  <label>' . $lang[1944] . '<span class="language">' . $language[$i] . '</span></label>
                                  <input type="text" name="title_' . $language[$i] . '" class="form-control">
                                  </div>';
                        }
                        echo '<div class="form-group">
                        <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary mt-3">
                        </div></form></div>';
                    }
                    // start = add record to db
                    if (isset($do) && $do == 'additem' && $access =='9') {
                        if (isset($_POST['additem'])) {
                            $code = Code("", "no", 7);
                            $date = jdate("Y/m/d : a:h:s");
                            $contract = $_POST['contract'];
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $language_name = $language[$i];
                                Query("insert into tbl_checklistdefault (
                                tbl_checklistdefault_title,
                                tbl_checklistdefault_dateadd,
                                tbl_checklistdefault_lng,
                                tbl_checklistdefault_code,
                                tbl_checklistdefault_delete,
                                tbl_checklistdefault_admin) values('$title_[$i]','$date','$language_name','$code','0','$userCode')");
                            }
                            $_SESSION['error'] = $lang[32];
                            header("Location: checklistdefault.php");
                            exit();
                        }
                    }
           
                    if (isset($do) && $do == 'edit' && $access =='9') {

                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_checklistdefault_lng = '$language_id'";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_checklistdefault Where tbl_checklistdefault_code =  '$code'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        // form for edit
                        echo '<div class="col-lg-8 col-12 p-3">
                            <form action="?do=edititem" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="' . $code . '" >';

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                            <label>' . $lang[1944] . '<span class="language">' . $language[$i] . '</span></label>
                            <input type="text" name="title_' . $language[$i] . '" value="' . exjection(read_fild($language[$i], 'tbl_checklistdefault_title')) . '" class="form-control">
                            </div>';
                        }
                       
                        echo '<div class="form-group">
                                <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
                                </div>
                        </form></div>';
                    }

                    if (isset($do) && $do == 'edititem' && $access =='9') {
                        if (isset($_POST['updateitem'])) {
                            $code = $_POST['code'];
                            $date = jdate("Y/m/d : a:h:s");
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $language_record = $language[$i];

                                Query("Update tbl_checklistdefault SET tbl_checklistdefault_dateedit='$date',tbl_checklistdefault_title='$title_[$i]'   Where tbl_checklistdefault_admin= '$userCode' and tbl_checklistdefault_code= '$code' and tbl_checklistdefault_lng='$language_record' ");
                            }
                            $_SESSION['error'] = $lang[21];
                            header("Location: checklistdefault.php");
                        }
                    }

                    // start = delete record
                    if (isset($do) && $do == 'delete' && $access =='9') {
                        $result = Query("SELECT * from tbl_checklistdefault Where tbl_checklistdefault_code = '$code' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-12 col-12 p-2"><p>' . $lang[25] . '</p><h3>' . $read['tbl_checklistdefault_title'] . '</h3>
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];
                            Query("update tbl_checklistdefault set  tbl_checklistdefault_delete='1' Where tbl_checklistdefault_admin= '$userCode' and tbl_checklistdefault_code = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: checklistdefault.php");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {
                            $contract = $_POST['contract'];
                            header("Location: checklistdefault.php");
                            exit();
                        }
                    }
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            $contract = $_POST['contract'];
                            header("Location: ?keyword=$keyword_search");
                        }
                    }
                    // end = delete item

                    ?>
            </div>
        </div>
    </div>
</div>
</div>
<?php
include './footer.inc.php';

?>