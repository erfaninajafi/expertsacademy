<?php
// include
include './includer.php';
$show_per_page = '30';
$Tabpage = '6';
$pagetitle = 'لیست ثبت نام شرکتها';
$thisaccess = '1';
include './header.inc.php';
// end include 
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
                       <table></tr><td class="col-sm-9"><a class="btn btn-primary margin-left" href="?do=add" >' . $lang[31] . '</a></td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="?do=search" method="POST">
                       <input type="text" class="form-control" id="search_pages_title" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-primary">' . $lang[77] . '</button>
                       </form></td></tr></table>';

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_registeruserlist Where tbl_registeruserlist_view='0' and tbl_registeruserlist_code LIKE '%$keyword%' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_registeruserlist Where tbl_registeruserlist_view='0' and tbl_registeruserlist_code LIKE '%$keyword%' ORDER BY tbl_registeruserlist_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        // start show list
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                            <thead class="table-dark">
                            <tr >
                                <th>' . $lang[60] . '</th>
                                <th>کد ملی</th>
                                <th>نام شرکت</th>
                                <th>نام دورره</th>
                                <th>وضعیت</th>
                            </tr>
                        </thead>
                        <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $result_access = Query("Select * from tbl_class where tbl_class_code='$read[tbl_registeruserlist_class]' ");
                            $read_access = mysqli_fetch_array($result_access);
                            echo '<tr>
                            <td style="width: 1%">' . $i . '</td>
                            <td class="col-sm-4">' . $read['tbl_registeruserlist_code'] . '</td>
                            <td class="col-sm-1">' . $read['tbl_registeruserlist_company'] . '</td>
                            <td class="col-sm-1">' . $read_access['tbl_class_title'] . '</td>
                            <td class="col-sm-1">' . ($read['tbl_registeruserlist_status'] == 0 ? 'استفاده نشده' : 'استفاده شده') . '</td>
                            </tr><tr>
                            <td class="col-sm-1 btn-class" colspan="5">
                            <a href="?do=delete&code=' . $read['tbl_registeruserlist_id'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>
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
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="registeruserlist.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: registeruserlist.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = do add
                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-6">
        <form action="?do=additem" method="POST" >';
                        echo '<div class="form-group"><label>انتخاب دوره</label><select name="class" class="form-control select2"><option value="0">انتخاب کلاس</option>';

                        $result_access = Query("Select * from tbl_class where tbl_class_status='1' order by tbl_class_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_class_code'] . '">' . $read_access['tbl_class_title'] . '</option>';
                        }
                        echo '</select>
            </div>';
                        echo '<div class="form-group">
        <label>نام شرکت</label>
        <input type="text" name="title" class="form-control" required>
        </div>';
                        echo '<div class="form-group">
        <label>لیست کدملی هر کدملی در یک خط</label>
        <textarea rows="10" name="code" class="form-control" style="text-align: left;direction: ltr;" required></textarea>
        </div>';
                        echo '<hr>
            <div class="form-group">
            <label>' . $lang[55] . '</label>
            <input type="text" name="date" value="' . jdate('Y/n/j ,H:i:s') . '" class="form-control">
            </div>
            <div class="form-group">
            <label> <input type="checkbox" name="register" value="1" > ثبت نام در کلاس 
            
            </label>
            </div>';

                        echo '<p>در صورتیکه نیاز به دریافت مدرک فیزیکی است یک مورد را انتخاب کنید</p>';

                        $result_cer = Query("SELECT * from tbl_certificate where tbl_certificate_workshop = '0' order by tbl_certificate_id DESC ");
                        while ($read_cer = mysqli_fetch_array($result_cer)) {
                            echo '<div class="form-group">
        <label>
        <input type="radio" value="' . $read_cer['tbl_certificate_id'] . '" name="certificate">
        
        ' . $read_cer['tbl_certificate_title'] . ($read_cer['tbl_certificate_price'] == 0 ? '' : ' ، قیمت : ' . number_format($read_cer['tbl_certificate_price']) . ' تومان') . '
        </label></div>';
                        }




                        echo '<div class="form-group">
            <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary">
            </div>
    </form></div>';
                    }
                    // start = add record to db
                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {

                            $title = $_POST['title'];
                            $date = $_POST['date'];
                            $class = $_POST['class'];
                            $register = (!empty($_POST['register']) ? $_POST['register'] : '0');
                            $code = explode(PHP_EOL, $_POST['code']);
                            if (empty($title)) {
                                $_SESSION['error'] = $lang[56];
                                header("Location: registeruserlist.php");
                                exit();
                            }
                            foreach ($code as $key => $value) {
                                $nationalcode = trim($value);
                                if ($register == 1) {
                                    $result_member = Query("select tbl_member_code from tbl_member where tbl_member_nationalcode='$nationalcode' ");
                                    $read_member = mysqli_fetch_array($result_member);
                                    $thisUser = $read_member['tbl_member_code'];

                                    Query("INSERT INTO tbl_registeruserlist (tbl_registeruserlist_view,tbl_registeruserlist_code,tbl_registeruserlist_company,tbl_registeruserlist_class,tbl_registeruserlist_status,tbl_registeruserlist_date,tbl_registeruserlist_userid) values('0','$value','$title','$class','1','$date','$user_id')");
                                    $result = Query("select * from tbl_class where tbl_class_code='$class' ");
                                    $read = mysqli_fetch_array($result);
                                    $model = ($read['tbl_class_special'] == 0 ? '0' : '1');
                                    $date = jdate("Y/m/d a:h:s ");


                                    $workshop = 0;
                                    if (!empty($_POST['certificate'])) {
                                        $certificate = sqi($_POST['certificate']);
                                        $result_cer = Query("SELECT tbl_certificate_price from tbl_certificate where tbl_certificate_workshop = '$workshop' and tbl_certificate_id ='$certificate' ");
                                        $read_cer = mysqli_fetch_array($result_cer);
                                        $pricecer = $read_cer['tbl_certificate_price'];
                                    } else {
                                        $certificate = 0;
                                        $pricecer = 0;
                                    }


                                    if ($model == 0) {
                                        $price = $read['tbl_class_price'];
                                        $discountmodel = '0';
                                        $discountvalue = '0';
                                        $discountcode = '';
                                        $discountfinalprice = '0';
                                        $finalprice = $price + $pricecer;
                                    } else {
                                        $discountmodel = '0';
                                        $discountvalue = '0';
                                        $discountcode = '';
                                        $discountfinalprice = '0';
                                        $certificate = 0;
                                        $finalprice = 0;
                                        $price = 0;
                                        $pricecer = 0;
                                    }
                                    $result_parent = Query("SELECT * from tbl_categories Where tbl_categories_id = '$read[tbl_class_cat]' ");
                                    $read_parent = mysqli_fetch_array($result_parent);
                                    $track_code = strtolower($read_parent['tbl_categories_catcode']) . '-' . strtolower($read['tbl_class_classcode']) . '-' . $thisUser;
                                    $track_code_show = strtolower($read_parent['tbl_categories_catcode']) . '-' . strtolower($read['tbl_class_classcode']) . '-' . $thisUser;
                                    Query("Insert into tbl_register (
                    tbl_register_discountmodel,
                    tbl_register_discountvalue,
                    tbl_register_discountcode,
                    tbl_register_discountfinalprice,
                    tbl_register_track,
                    tbl_register_class,
                    tbl_register_user,
                    tbl_register_date,
                    tbl_register_res,
                    tbl_register_status,
                    tbl_register_certificate,
                    tbl_register_pricefinal,
                    tbl_register_priceclass,
                    tbl_register_pricecer,
                    tbl_register_model,
                    tbl_register_workshop,
                    tbl_register_sazemani,
                    tbl_register_view) values(
                        '$discountmodel',
                        '$discountvalue',
                        '$discountcode',
                        '$discountfinalprice',
                        '$track_code',
                        '$class',
                        '$thisUser',
                        '$date',
                        '',
                        '1',
                        '$certificate',
                        '$finalprice',
                        '$price',
                        '$pricecer',
                        '$model',
                        '$workshop',
                        '1',
                        '0') ");
                                } else {
                                    Query("INSERT INTO tbl_registeruserlist (tbl_registeruserlist_view,tbl_registeruserlist_code,tbl_registeruserlist_company,tbl_registeruserlist_class,tbl_registeruserlist_status,tbl_registeruserlist_date) values('0','$nationalcode','$title','$class','0','$date')");
                                }
                            }
                            $_SESSION['error'] = $lang[32];
                            header("Location: registeruserlist.php");
                        }
                    }
                    // end  = add record          
                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_registeruserlist Where tbl_registeruserlist_id = '$code' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br /><h2>' . $read['tbl_registeruserlist_code'] . '</h2><br />
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="hidden" value="' . $read['tbl_registeruserlist_userid'] . '" name="user" >
                         <input type="hidden" value="' . $read['tbl_registeruserlist_class'] . '" name="class" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];
                            $user = $_POST['user'];
                            if (!empty($user)) {
                                $class = $_POST['class'];
                                Query("update tbl_register set tbl_register_view ='1' Where tbl_register_class = '$class' and tbl_register_user='$user' ");
                            }
                            Query("update tbl_registeruserlist set tbl_registeruserlist_view ='1' Where tbl_registeruserlist_id = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: registeruserlist.php");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {

                            header("Location: registeruserlist.php");
                            exit();
                        }
                    }

                    // end = delete item

                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: registeruserlist.php?keyword=$keyword_search");
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