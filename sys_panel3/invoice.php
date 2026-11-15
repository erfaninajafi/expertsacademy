<?php
include './includer.php';
$show_per_page = '20';
$pagetitle = $lang[1201];
$Tabpage = '25';
$thisaccess = '1,2';
include './header.inc.php';
?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php

                    if (!empty($_GET['section'])) {
                        $section = " tbl_invoice_usercode = '$_GET[section]' and ";
                    } else {
                        $section = '';
                    }
                    $select_filter = '';
                    if (!empty($_GET['filter'])) {

                        $filter = $_GET['filter'];
                        if ($filter == 'notpay') {
                            $select_filter .= " and tbl_invoice_resnum =''  ";
                        } elseif ($filter == 'pay') {
                            $select_filter .= " and tbl_invoice_resnum !=''  ";
                        }
                    }
                    if (!empty($_GET['class'])) {
                        $select_filter .= " and tbl_invoice_class ='$_GET[class]'  and tbl_invoice_user='$_GET[user]' ";
                    }
                    $select_access = '';
                    if ($access == 2) {
                        $select_access = " and tbl_invoice_admin ='$this_user_id'  ";
                    }
                    // list content
                    if (!isset($do)) {
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table>
                       <tr>
                       <td class="col-sm-9">
                       <a class="btn btn-primary margin-left big_button" href="?do=add' . (!empty($_GET['class']) ? '&class=' . $_GET['class'] . '&user=' . $_GET['user'] : '') . '" >' . $lang[31] . '</a></td>
                       <td class="col-sm-6" > 
                       <form action="invoice.php?do=search" method="POST">
                       <input type="text" class="form-control" id="member_name" name="keyword"></td>
                       <td class="col-sm-2">
                       <button type="submit" name="go" class="btn btn-primary" >' . $lang[77] . '</button>
                       </form></td></tr><tr><td class="col-lg-12 filter_group" colspan="2">';

                        $res = Query("select tbl_invoice_id from tbl_invoice where tbl_invoice_view !='1' and tbl_invoice_id !='' $select_access");
                        echo '<a href="invoice.php">تعداد کل  <span class="translate">(' . number_format(mysqli_num_rows($res)) . ')</span></a>';
                        $res = Query("select tbl_invoice_id from tbl_invoice where tbl_invoice_resnum ='' and tbl_invoice_view !='1' and tbl_invoice_id !='' $select_access");
                        echo '<a href="?filter=notpay">پرداخت نشده <span class="translate">(' . number_format(mysqli_num_rows($res)) . ')</span></a>';
                        $res = Query("select tbl_invoice_id from tbl_invoice where tbl_invoice_resnum !='' and tbl_invoice_view !='1' and tbl_invoice_id !='' $select_access");
                        echo '<a href="?filter=pay">پرداخت شده <span class="translate">(' . number_format(mysqli_num_rows($res)) . ')</span></a>
                        </td></tr></table>';

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_invoice Where tbl_invoice_view !='1' and tbl_invoice_id !='' $select_access $select_filter $section and tbl_invoice_title LIKE '%$keyword%' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_invoice Where tbl_invoice_view !='1' and tbl_invoice_id !='' $select_access $select_filter $section and tbl_invoice_title LIKE '%$keyword%' ORDER BY tbl_invoice_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        // start show list
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
    <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>عنوان صورتحساب</th>
                                    <th>نام واریز کننده</th>
                                    <th>مبلغ به ریال</th>
                                    <th>تاریخ ایجاد </th>
                                    <th>تاریخ واریز </th>
                                    <th>کدپیگیری</th>
                                    <th>ایجاد کننده</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $admin = $read['tbl_invoice_admin'];
                            $res_admin = Query("select * from tbl_user where tbl_user_id = '$admin' ");
                            $read_admin = mysqli_fetch_array($res_admin);

                            $user = $read['tbl_invoice_user'];
                            $res = Query("select * from tbl_member where tbl_member_code = '$user' ");
                            $read_user = mysqli_fetch_array($res);
                            echo '<tr>
                                <td style="width: 1%">' . $i . '</td>
                                <td class="col-sm-1">' . $read['tbl_invoice_title'] . '</td>
                                <td class="col-sm-1">' . @$read_user['tbl_member_name'] . '</td>
                                <td class="col-sm-1">' . number_format($read['tbl_invoice_price']) . '</td>
                                <td class="col-sm-1">' . $read['tbl_invoice_datecreate'] . '</td>
                                <td class="col-sm-1">' . $read['tbl_invoice_datepay'] . '</td>
                                <td class="col-sm-1">' . $read['tbl_invoice_resnum'] . '</td>
                                <td class="col-sm-1">' . $read_admin['tbl_user_username'] . '</td></tr><tr>
                                <td class="col-sm-1 btn-class" colspan="8">' . (!empty($read['tbl_invoice_resnum']) ? '' : '<a href="?id=' . $read['tbl_invoice_id'] . '&do=edit" class="btn btn-xs btn-warning" >' . $lang[23] . '</a>' . '
                                <a href="?id=' . $read['tbl_invoice_id'] . '&do=delete" class="btn btn-xs btn-danger">' . $lang[24] . '</a>') . '</td>
                                </td>
                                </tr>';
                            $i++;
                        }

                        echo '</tbody></table>';
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
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['section']) ? '&section=' . $_GET['section'] : '') . (!empty($_GET['filter']) ? '&filter=' . $_GET['filter'] : '')  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['section']) ? '&section=' . $_GET['section'] : '') . (!empty($_GET['filter']) ? '&filter=' . $_GET['filter'] : '') ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="invoice.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
                                    <div class="form-group">
                                        <button type="submit" name="gotopage" class="btn btn-success" style="float: left;"><?= $lang[76] ?></button>
                                        <input type="text" class="form-control go-fld" name="page_number">
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </table>
                    <?php
                        }
                    }
                    if (isset($t) && isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: invoice.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: invoice.php?keyword=$keyword_search");
                        }
                    }
                    // end = search result  
                    // start = do add
                    if (isset($do) && $do == 'add') { ?>
                    <div class="col-lg-8">
                        <form action="invoice.php?do=additem" method="POST" data-parsley-validate="" id="register">
                            <input type="hidden" name="class" value="<?= (!empty($_GET['class']) ? $_GET['class'] : '0') ?>">
                            <div class="form-group">
                                <label>عنوان صورت حساب</label>
                                <input type="text" name="title" class="form-control" require>
                            </div>
                            <?php if (isset($_GET['class'])) {
                                    echo '<input type="hidden" name="name"  value="' . $_GET['user'] . '" require>';
                                } else { ?>
                            <div class="form-group">
                                <label>شماره موبایل و یا کد ملی کاربر را وارد نمایید</label>
                                <input type="text" name="name" class="form-control" id="search_title" require>
                            </div>
                            <?php } ?>
                            <div class="form-group">
                                <label>مبلغ به ریال</label>
                                <input type="text" name="price" class="form-control" require>
                            </div>
                            <div class="form-group">
                                <label>تاریخ ایجاد</label>
                                <input type="text" name="date" class="form-control datepicker" value="<?= jdate("Y/m/d") ?>" require>
                            </div>

                            <div class="form-group">
                                <input type="submit" name="additem" value="ثبت صورت حساب" class="btn btn-primary">
                            </div>
                        </form>
                    </div>


                    <?php
                    }
                    // start = add record to db
                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $title = $_POST['title'];
                            $class = $_POST['class'];
                            $name = trim($_POST['name']);
                            $price = $_POST['price'];
                            $date = $_POST['date'];
                            if (empty($title)) {
                                $_SESSION['error'] = $lang[56];
                                header("Location: invoice.php");
                                exit();
                            }
                            $get_user_id = explode(",", $name);
                            Query("INSERT INTO tbl_invoice (tbl_invoice_title,tbl_invoice_user,tbl_invoice_price,tbl_invoice_datecreate,tbl_invoice_class,tbl_invoice_view,tbl_invoice_admin) values('$title','$get_user_id[0]','$price','$date','$class','0','$this_user_id')");
                            $_SESSION['error'] = $lang[32];
                            if (!empty($class)) {
                                header("Location: invoice.php?class=$class&user=$get_user_id[0]");
                            } else {
                                header("Location: invoice.php");
                            }
                        }
                    }

                    if (isset($do) && $do == 'edit') {
                        $result = Query("select * from tbl_invoice where tbl_invoice_id = '$id' and tbl_invoice_view ='0' and tbl_invoice_resnum IS NULL ");
                        $read = mysqli_fetch_array($result);

                    ?>
                    <div class="col-lg-8">
                        <form action="invoice.php?do=update" method="POST" data-parsley-validate="" id="register">
                            <div class="form-group">
                                <label>عنوان صورت حساب</label>
                                <input type="text" name="title" class="form-control" value="<?= $read['tbl_invoice_title'] ?>">
                            </div>
                            <div class="form-group">
                                <label>مبلغ به ریال</label>
                                <input type="text" name="price" class="form-control" value="<?= $read['tbl_invoice_price'] ?>">
                            </div>

                            <div class="form-group">
                                <label>تاریخ ایجاد</label>
                                <input type="text" name="date" class="form-control datepicker" value="<?= $read['tbl_invoice_datecreate'] ?>">
                            </div>

                            <div class="form-group">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <input type="submit" name="update" value="بروزرسانی فیش" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                    <?php
                    }
                    if (isset($do) && $do == 'update') {
                        if (isset($_POST['update'])) {

                            $title = $_POST['title'];
                            $name = trim($_POST['name']);
                            $price = $_POST['price'];
                            $date = $_POST['date'];
                            $id = $_POST['id'];

                            if (empty($title)) {
                                $_SESSION['error'] = $lang[56];
                                header("Location: invoice.php");
                                exit();
                            }

                            if (strpos($name, ",") !== FALSE) {
                                $get_user_id = explode(",", $name);
                                $final_code_user = $get_user_id[0];
                            } else {
                                $final_code_user =  $name;
                            }

                            Query("update tbl_invoice set tbl_invoice_title = '$title',tbl_invoice_user='$final_code_user',tbl_invoice_price='$price',tbl_invoice_datecreate='$date' where tbl_invoice_id ='$id' ");
                            $_SESSION['error'] = $lang[21];
                            header("Location: invoice.php");
                        }
                    }

                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_invoice  Where tbl_invoice_id = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-12">' . $lang[25] . '<br /><h2>' . $read['tbl_invoice_title'] . '</h2><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                            <input type="hidden" value="' . $id . '" name="id" >
                            <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-warning">
                            <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $id = $_POST['id'];

                            Query("update tbl_invoice set tbl_invoice_view ='1'  Where tbl_invoice_id = '$id' and tbl_invoice_resnum IS NULL ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: invoice.php");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {
                            header("Location: invoice.php");
                            exit();
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