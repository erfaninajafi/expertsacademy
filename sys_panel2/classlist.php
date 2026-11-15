<?php
// include
include './includer.php';
$show_per_page = '10';
$pagetitle = 'مدیریت کلاس ها ';
$Tabpage = '25';
$img_item = 1;
$thisaccess = '2';
include './header.inc.php';
$thishistory = '';
if ($access == 2) {
    $thishistory = " where tbl_class_admin = '$this_user_id'  ";
} else {
    $thishistory = "";
}
$result = Query("SELECT * From tbl_class $thishistory ORDER BY tbl_class_id DESC");
// start count db result for pagination               
$count_result = mysqli_num_rows($result);
$start_contnet =  ($page - 1) * $show_per_page;
$page_number = ceil($count_result / $show_per_page);
// end count db result for pagination 
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
                       <div class="col-lg-12 padding-clear margin-bottom"></div>';
                        $result = Query("SELECT * From tbl_class $thishistory ORDER BY tbl_class_id DESC  LIMIT $start_contnet,$show_per_page ");
                        $row = mysqli_num_rows($result);
                        // 0 result search error 
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        $allfield = '';
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                        <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[50] . '</th>
                                    <th>کد دوره</th>
                                    <th>ظرفیت</th>
                                    <th>تاریخ پایان ثبت نام</th>
                                    <th>مبلغ</th>
                                    <th>ثبت نام ها</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;  // for record number
                        while ($read = mysqli_fetch_array($result)) {
                            $result_register = Query("select * from tbl_register where tbl_register_view !='1' and tbl_register_class='$read[tbl_class_code]'");
                            // parent menu code
                            echo '<tr><td style="width: 1%">' . $i . '</td>'
                                . '<td class="col-sm-2">' . $read['tbl_class_title'] . ' (' . ($read['tbl_class_pay'] == 0 ? 'پرداخت دستی' : 'پرداخت آنلاین') . ')</td>'
                                . '<td class="col-sm-1">' . $read['tbl_class_classcode'] . '</td>'
                                . '<td class="col-sm-1">' . $read['tbl_class_count'] . '</td>'
                                . '<td class="col-sm-1">' . $read['tbl_class_end'] . '</td>'
                                . '<td class="col-sm-1">' . $read['tbl_class_price'] . '</td>'
                                . '<td class="col-sm-1">' . mysqli_num_rows($result_register) . '</td>'
                                . '<td class="col-sm-3">'
                                . '<a href="register.php?historyclass=' . $read['tbl_class_code'] . '" target="_blank" class="btn btn-xs btn-warning " > ثبت نام ها</a>
                              <a href="export.php?historyclass=' . $read['tbl_class_code'] . '&do=register" class="btn btn-xs btn-danger">خروجی اکسل</a></td>'
                                . '</tr>';
                            $i++;
                        }
                        echo '</table>';

                        // start pagination                
                        if ($page > $page_number) {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
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
                            <td class="col-lg-6">
                                <ul class="pagination">
                                    <li><a class="page-link"
                                            href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a
                                            class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link"
                                            href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="class.php?do=gotopage" class="navbar-form navbar-left" role="search"
                                    method="POST">
                                    <div class="form-group">
                                        <button type="submit" name="gotopage" class="btn btn-success"
                                            style="float: left;"><?= $lang[76] ?></button>
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
                            header("Location: class.php?page=$page_number");
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