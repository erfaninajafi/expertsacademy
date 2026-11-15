<?php
// include
include './includer.php';
$show_per_page = '30';
$pagetitle = 'مدیریت ثبت نام ها ';
$Tabpage = '25';
$img_item = 1;
$thisaccess = '1,2,4';
include './header.inc.php';
if (!empty($history)) {
    $thishistory = " tbl_register_user = '$history' and ";
} else {
    $thishistory = "";
}

if (!empty($_GET['class'])) {
    $thishistory .= " tbl_register_class = '$_GET[class]' and ";
} else {
    $thishistory .= "";
}
if (!empty($_GET['historyworkshop'])) {
    $thishistory .= " tbl_register_class = '$_GET[historyworkshop]' and ";
} else {
    $thishistory .= "";
}

if (!empty($_GET['discount'])) {
    $thishistory .= " tbl_register_discountcode = '$_GET[discount]' and ";
} else {
    $thishistory .= "";
}

if ($access == 2) {
    $thishistory .= " tbl_register_admin = '$this_user_id' and ";
} else {
    $thishistory .= "";
}

$result = Query("SELECT * From tbl_register where $thishistory tbl_register_view ='0' and tbl_register_track COLLATE UTF8_GENERAL_CI LIKE '%$keyword%'  ORDER BY tbl_register_id DESC");
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

                    if (!empty($_GET['historyclass'])) {
                        $result_class = Query("Select tbl_class_title from tbl_class where tbl_class_code = '$_GET[historyclass]' and tbl_class_lng='$language_default'");
                        $read_class = mysqli_fetch_array($result_class);
                        $result_skill = Query("SELECT tbl_categories_title From tbl_categories where tbl_categories_code='$_GET[skill]' ");
                        $readSkill = mysqli_fetch_array($result_skill);
                        echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title"><a href="category_manage.php?t=4">لیست مهارت ها</a>  / <a href="class.php?skill=' . $_GET['skill'] . '">دوره های مهارت ' . $readSkill['tbl_categories_title'] . '</a>  /  نام دوره  : ' . $read_class['tbl_class_title'] . '</h5></div>';
                    }

                    if (!empty($_GET['class'])) {
                        $result_class = Query("Select tbl_class_title from tbl_class where tbl_class_code = '$_GET[class]' and tbl_class_lng='$language_default'");
                        $read_class = mysqli_fetch_array($result_class);
                        echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title"><a href="class-uniq.php">نام دوره</a>  / <a href="class-uniq.php">' . $read_class['tbl_class_title'] . '</a></h5></div>';
                    }

                    if (!isset($do)) {
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table></tr><td class="col-sm-9"></td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="register.php?do=search" method="POST">
                       <input type="text" class="form-control" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-primary">' . $lang[77] . '</button>
                       </form></td></tr></table>
                
                       <div class="col-lg-12 padding-clear margin-bottom"></div>';
                        $result = Query("SELECT * From tbl_register where $thishistory tbl_register_view ='0' and tbl_register_track COLLATE UTF8_GENERAL_CI LIKE '%$keyword%'  ORDER BY tbl_register_id DESC  LIMIT $start_contnet,$show_per_page ");
                        $row = mysqli_num_rows($result);
                        // 0 result search error 
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                        }
                        $allfield = '';
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                        <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>کد پیگیری</th>
                                    <th>عنوان</th>
                                    <th>مبلغ کلاس</th>
                                    <th>خدمات اضافه</th>
                                    <th>مبلغ تخفیف</th>
                                    <th>مبلغ نهایی</th>
                                    <th>تاریخ ثبت نام</th>
                                    <th>کاربر</th>
                                    <th>موبایل</th>
                                    <th>کد بانک</th>
                                    <th>کد تخفیف</th>
                                    <th>تایید قبولی</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;  // for record number
                        while ($read = mysqli_fetch_array($result)) {
                            $class = $read['tbl_register_class'];
                            $user = $read['tbl_register_user'];
                            $workshop = $read['tbl_register_workshop'];
                            $certificate = $read['tbl_register_certificate'];
                            $result_cer = Query("SELECT * from tbl_certificate where tbl_certificate_id ='$certificate' ");
                            $read_cer = mysqli_fetch_array($result_cer);
                            if ($workshop == 0) {
                                $result_class = Query("select tbl_class_title,tbl_class_emtehan from tbl_class where tbl_class_code='$class' ");
                                $read_class = mysqli_fetch_array($result_class);
                            } else {
                                $result_class = Query("select tbl_workshop_title from tbl_workshop where tbl_workshop_code='$class' ");
                                $read_class = mysqli_fetch_array($result_class);
                            }


                            $result_member = Query("select tbl_member_name,tbl_member_mobile from tbl_member where tbl_member_code='$user' ");
                            $read_member = mysqli_fetch_array($result_member);
                            $result_evaluation = Query("SELECT * FROM tbl_evaluation Where tbl_evaluation_register ='$read[tbl_register_id]' ");
                            $class = $read['tbl_register_class'];
                            $result_invoice = Query("select * from tbl_invoice where tbl_invoice_class='$class' and tbl_invoice_view='0' and tbl_invoice_user='$user' ");
                            $result_invoice_sum = Query("select SUM(tbl_invoice_price) from tbl_invoice where tbl_invoice_class='$class' and (tbl_invoice_resnum !='' or tbl_invoice_resnum !='NULL') and tbl_invoice_user='$user' ");
                            $read_invoice_sum = mysqli_fetch_array($result_invoice_sum);
                            $result_exam = Query("select * from tbl_exam Where tbl_exam_user = '$read[tbl_register_user]'  and tbl_exam_course ='$read[tbl_register_class]' ");
                            $result_question = Query("select * from tbl_qteacher where tbl_qteacher_status='0' and tbl_qteacher_userid = '$user' and tbl_qteacher_class='$read[tbl_register_class]' order by tbl_qteacher_id DESC ");

                            $result_opinion = Query("select * from tbl_opinion Where tbl_opinion_user = '$read[tbl_register_user]'  and tbl_opinion_course ='$read[tbl_register_class]' ");

                            // parent menu code
                            echo '<tr><td style="width: 1%">' . $i . '</td>'
                                . '<td class="col-sm-1">' . $read['tbl_register_track'] . (mysqli_num_rows($result_evaluation) > 0  ? ' <span style="color:red;">[ارزیابی شده]</span>' : '') . '</td>'
                                . '<td class="col-sm-1"><a href="' . ($workshop == 0 ? '../class/' . $class . '/' . title($read_class['tbl_class_title']) : '../workshop/' . $class . '/' . title($read_class['tbl_workshop_title'])) . '" target="_blank">' . ($workshop == 0 ? $read_class['tbl_class_title'] : $read_class['tbl_workshop_title']) . '</a></td>'
                                . '<td class="col-sm-1">' . number_format($read['tbl_register_priceclass']) . '</td>'
                                . '<td class="col-sm-1">' . ($certificate != 0 ? $read_cer['tbl_certificate_title'] . ' / ' . $read_cer['tbl_certificate_price'] . ' تومان' : 'بدون خدمات') . '</td>'
                                . '<td class="col-sm-1">' . (!empty($read['tbl_register_discountcode']) ?  number_format($read['tbl_register_discountfinalprice']) . ' تومان' : '-') . '</td>'
                                . '<td class="col-sm-1">' . number_format($read['tbl_register_pricefinal']) . '</td>'
                                . '<td class="col-sm-1">' . $read['tbl_register_date'] . '</td>'
                                . '<td class="col-sm-1"><a href="' . ($access == 2 ? 'register.php?do=viewuser&id=' . $user : 'member.php?do=edit&id=' . $user) . '" target="_blank">' . $read_member['tbl_member_name']  . '</a></td>'
                                . '<td class="col-sm-1">' . $read_member['tbl_member_mobile'] . '</td>'
                                . '<td class="col-sm-1">' . ($read['tbl_register_free'] == 1 ? 'رایگان' : $read['tbl_register_res']) . '<br>' . (!empty($read['tbl_register_res']) ? $read['tbl_register_datepayment'] : '') . '</td>'
                                . '<td class="col-sm-1">' . ($read['tbl_register_sazemani'] == 1 ? 'سازمانی' : $read['tbl_register_discountcode']) . '</td>'
                                . '<td class="col-sm-1"><input type="checkbox" value="1" data-name="register" data-id="' . $read['tbl_register_id'] . '" class="checkboxajax" ' . ($read['tbl_register_confirm'] == 1 ? 'checked' : '') . '></td>'

                                . '</tr><tr><td class="col-sm-1 btn-class" colspan="13"><a href="?do=delete&id=' . $read['tbl_register_id'] . '" class="btn btn-xs btn-danger" style="width: 100%;margin-bottom: 2px;">' . $lang[24] . '</a>
            ' . (!empty($read_class['tbl_class_emtehan']) && $read_class['tbl_class_emtehan'] == 1 ? '<a href="?do=viewemtehan&code=' . $read['tbl_register_class'] . '&userid=' . $read['tbl_register_user'] . '" class="btn btn-xs btn-success" style="width: 100%;margin-bottom: 2px;">تمرین پایان دوره</a>' : '') . '
            <a href="?do=view&id=' . $read['tbl_register_id'] . '" class="btn btn-xs btn-warning" style="width: 100%;margin-bottom: 2px;">درسهای دیده شده</a>
            <a href="quser.php?class=' . $read['tbl_register_class'] . '&userid=' . $read['tbl_register_user'] . '" class="btn btn-xs btn-default" style="width: 100%;margin-bottom: 2px;">سوالات کاربر (مانده ' . (mysqli_num_rows($result_question)) . ')</a>
            <a href="?do=examend&code=' . $read['tbl_register_class'] . '&userid=' . $read['tbl_register_user'] . '" class="btn btn-xs btn-warning" style="width: 100%;margin-bottom: 2px;">امتحان پایان دوره (' . (mysqli_num_rows($result_exam) == 1 ? 'شرکت کرده' : 'شرکت نکرده') . ')</a>
            <a href="?do=opinion&code=' . $read['tbl_register_class'] . '&userid=' . $read['tbl_register_user'] . '" class="btn btn-xs btn-info" style="width: 100%;margin-bottom: 2px;">نظر سنجی ' . (mysqli_num_rows($result_opinion) > 0 ? 'دارد' : 'ندارد') . '</a>
            </td></tr>';
                            $i++;
                        }
                        echo '</table>';

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
                    ?>
                    <table class="table">
                        <tr>
                            <td class="col-lg-6">
                                <ul class="pagination">
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['historyworkshop']) ? '&historyworkshop=' . $_GET['historyworkshop'] : '') . (!empty($_GET['historyclass']) ? '&historyclass=' . $_GET['historyclass'] : '') . (!empty($_GET['discount']) ? '&discount=' . $_GET['discount'] : '')  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['historyworkshop']) ? '&historyworkshop=' . $_GET['historyworkshop'] : '') . (!empty($_GET['historyclass']) ? '&historyclass=' . $_GET['historyclass'] : '') . (!empty($_GET['discount']) ? '&discount=' . $_GET['discount'] : '') ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="register.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                            header("Location: register.php?page=$page_number");
                        }
                    }
                    // end = add item
                    if (isset($do) && $do == 'opinion') {
                        $user_id = $_GET['userid'];
                        $result = Query("Select * from tbl_register where tbl_register_view='0' and tbl_register_class='$code' and tbl_register_user = '$user_id' order by tbl_register_id DESC ");
                        $read = mysqli_fetch_array($result);
                        $workshop = $read['tbl_register_workshop'];
                        $result_opinion = Query("select * from tbl_opinion Where tbl_opinion_user = '$user_id'  and tbl_opinion_course ='$code' ");
                        if (mysqli_num_rows($result_opinion) > 0) {
                            $read_opinion = mysqli_fetch_array($result_opinion);
                            $data = json_decode($read_opinion['tbl_opinion_data'], true);
                            echo '<p>تاریخ ثبت : ' . $read_opinion['tbl_opinion_date'] . '</p>';
                            if ($workshop == 1) {

                            ?>

                    <div class="form-group">
                        <label class="lablecolor">مفید بودن مطالب برای زمینه فعالیت شما</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld1" value="4" <?= ($data['fld1'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld1" value="3" <?= ($data['fld1'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld1" value="2" <?= ($data['fld1'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld1" value="1" <?= ($data['fld1'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">پوشش دهی اهداف دوره</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld2" value="4" <?= ($data['fld2'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld2" value="3" <?= ($data['fld2'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld2" value="2" <?= ($data['fld2'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld2" value="1" <?= ($data['fld2'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">ارائه راه کارهای عملی</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld3" value="4" <?= ($data['fld3'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld3" value="3" <?= ($data['fld3'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld3" value="2" <?= ($data['fld3'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld3" value="1" <?= ($data['fld3'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">سهولت فراگیری و اثر گذاری</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld4" value="4" <?= ($data['fld4'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld4" value="3" <?= ($data['fld4'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld4" value="2" <?= ($data['fld4'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld4" value="1" <?= ($data['fld4'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">استفاده مناسب از مثالها، نمودارها</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld5" value="4" <?= ($data['fld5'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld5" value="3" <?= ($data['fld5'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld5" value="2" <?= ($data['fld5'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld5" value="1" <?= ($data['fld5'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">سهولت استفاده از پلتفورم آموزشی انلاین</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld6" value="4" <?= ($data['fld6'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld6" value="3" <?= ($data['fld6'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld6" value="2" <?= ($data['fld6'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld6" value="1" <?= ($data['fld6'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">تسلط مدرس بر دوره و قدرت بیان و تفهیم مطالب</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld7" value="4" <?= ($data['fld7'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld7" value="3" <?= ($data['fld7'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld7" value="2" <?= ($data['fld7'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld7" value="1" <?= ($data['fld7'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">پوشش دادن کلیه مطالب دوره</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld8" value="4" <?= ($data['fld8'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld8" value="3" <?= ($data['fld8'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld8" value="2" <?= ($data['fld8'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld8" value="1" <?= ($data['fld8'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="lablecolor">ایجاد مشارکت افراد دربحث </label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld9" value="4" <?= ($data['fld9'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld9" value="3" <?= ($data['fld9'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld9" value="2" <?= ($data['fld9'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld9" value="1" <?= ($data['fld9'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">مدیریت جلسه و اجرای برنامه دوره </label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld10" value="4" <?= ($data['fld10'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld10" value="3" <?= ($data['fld10'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld10" value="2" <?= ($data['fld10'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld10" value="1" <?= ($data['fld10'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">نحوه اطلاع از برگزاری دوره </label>
                        <div class="form-group tikmodel">
                            <label><input type="checkbox" name="fld11" value="1" <?= (!empty($data['fld11']) && $data['fld11'] == 1 ? 'checked' : '') ?>>مکاتبه واحد
                                اموزش</label>
                            <label><input type="checkbox" name="fld12" value="1" <?= (!empty($data['fld12']) && $data['fld12'] == 1 ? 'checked' : '') ?>>در فضای
                                مجازی </label>
                            <label><input type="checkbox" name="fld13" value="1" <?= (!empty($data['fld13']) && $data['fld13'] == 1 ? 'checked' : '') ?>>کارکنان
                                IGX</label>
                            <label><input type="checkbox" name="fld14" value="1" <?= (!empty($data['fld14']) && $data['fld14'] == 1 ? 'checked' : '') ?>>سایر</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="lablecolor">نظرات و پیشنهادات اصلاحی (انتقاد سازنده شما راه گشای ما خواهد
                            بود)</label>
                        <textarea class="form-control" rows="5" name="comment"><?= $data['comment'] ?></textarea>
                    </div>

                    <?php
                            } else {
                            ?>
                    <div class="form-group">
                        <label class="lablecolor">مفید بودن مطالب برای زمینه فعالیت شما</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld1" value="4" <?= ($data['fld1'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld1" value="3" <?= ($data['fld1'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld1" value="2" <?= ($data['fld1'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld1" value="1" <?= ($data['fld1'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">پوشش دهی اهداف دوره</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld2" value="4" <?= ($data['fld2'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld2" value="3" <?= ($data['fld2'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld2" value="2" <?= ($data['fld2'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld2" value="1" <?= ($data['fld2'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">ارائه راه کارهای عملی</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld3" value="4" <?= ($data['fld3'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld3" value="3" <?= ($data['fld3'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld3" value="2" <?= ($data['fld3'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld3" value="1" <?= ($data['fld3'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">سهولت فراگیری و اثر گذاری</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld4" value="4" <?= ($data['fld4'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld4" value="3" <?= ($data['fld4'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld4" value="2" <?= ($data['fld4'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld4" value="1" <?= ($data['fld4'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">سهولت استفاده از پلتفورم آموزشی</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld5" value="4" <?= ($data['fld5'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld5" value="3" <?= ($data['fld5'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld5" value="2" <?= ($data['fld5'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld5" value="1" <?= ($data['fld5'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">تسلط مدرس بر دوره و قدرت بیان و تفهیم مطالب</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld6" value="4" <?= ($data['fld6'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld6" value="3" <?= ($data['fld6'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld6" value="2" <?= ($data['fld6'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld6" value="1" <?= ($data['fld6'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="lablecolor">پوشش دادن کلیه مطالب دوره</label>
                        <div class="form-group tikmodel">
                            <label><input type="radio" name="fld7" value="4" <?= ($data['fld7'] == 4 ? 'checked' : '') ?>>خیلی خوب</label>
                            <label><input type="radio" name="fld7" value="3" <?= ($data['fld7'] == 3 ? 'checked' : '') ?>>خوب</label>
                            <label><input type="radio" name="fld7" value="2" <?= ($data['fld7'] == 2 ? 'checked' : '') ?>>متوسط</label>
                            <label><input type="radio" name="fld7" value="1" <?= ($data['fld7'] == 1 ? 'checked' : '') ?>>نامطلوب</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="lablecolor">نحوه اطلاع از برگزاری دوره </label>
                        <div class="form-group tikmodel">
                            <label><input type="checkbox" name="fld8" value="1" <?= (!empty($data['fld8']) && $data['fld8'] == 1 ? 'checked' : '') ?>>مکاتبه واحد
                                اموزش</label>
                            <label><input type="checkbox" name="fld9" value="1" <?= (!empty($data['fld9']) && $data['fld9'] == 1 ? 'checked' : '') ?>>در فضای مجازی
                            </label>
                            <label><input type="checkbox" name="fld10" value="1" <?= (!empty($data['fld10']) && $data['fld10'] == 1 ? 'checked' : '') ?>>کارکنان
                                IGX</label>
                            <label><input type="checkbox" name="fld11" value="1" <?= (!empty($data['fld11']) && $data['fld11'] == 1 ? 'checked' : '') ?>>سایر</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="lablecolor">نظرات و پیشنهادات اصلاحی (انتقاد سازنده شما راه گشای ما خواهد
                            بود)</label>
                        <textarea class="form-control" rows="5" name="comment"><?= $data['comment'] ?></textarea>
                    </div>

                    <?php
                            }
                        }
                    }
                    if (isset($do) && $do == 'view') {
                        echo '<div class="col-12 col-lg-6 col-md-6"><div class="classview">';
                        $result = Query("SELECT * From tbl_register where tbl_register_view ='0' and tbl_register_id = '$id'  ");
                        $read = mysqli_fetch_array($result);
                        $user_id = $read['tbl_register_user'];
                        $thisParentClass = $read['tbl_register_class'];

                        $result = Query("SELECT * From tbl_courseparent where tbl_courseparent_class='$thisParentClass' and tbl_courseparent_status ='1' ORDER BY tbl_courseparent_order DESC , tbl_courseparent_id DESC ");
                        while ($read = mysqli_fetch_array($result)) {
                            //read current code for style

                            $resultcourseclass_count = Query("SELECT * From tbl_course where tbl_course_class='$read[tbl_courseparent_code]' and tbl_course_status ='1'  ");
                            $resultview_count = Query("SELECT * From tbl_view where tbl_view_course='$read[tbl_courseparent_code]' and tbl_view_user ='$user_id'  ");

                            $resultcourseclass = Query("SELECT * From tbl_course where tbl_course_class='$read[tbl_courseparent_code]' and tbl_course_status ='1'  ORDER BY tbl_course_order DESC , tbl_course_id DESC limit 0,1");
                            $readcourseclass = mysqli_fetch_array($resultcourseclass);
                            $result_exam = Query("select * from tbl_exam Where tbl_exam_user = '$user_id'  and tbl_exam_course ='$read[tbl_courseparent_code]' ");

                            echo '<div class="itemview">
                                نام درس : ' . $read['tbl_courseparent_title'] . '
                                <div class="detailview">
                                    <div>تعداد  مباحث : <span class="translate">' . mysqli_num_rows($resultcourseclass_count) . '</span></div>
                                    <div> خوانده شده : <span class="translate">' . mysqli_num_rows($resultview_count) . '</span></div>
                                    <div> خوانده نشده : <span class="translate">' . (mysqli_num_rows($resultcourseclass_count) - mysqli_num_rows($resultview_count)) . '</span></div>
                                    ' . ((mysqli_num_rows($resultcourseclass_count) - mysqli_num_rows($resultview_count)) == 0 ? '<div class="itemviewexam"><a href="' . (mysqli_num_rows($result_exam) == 0 ? '#' : '?do=exam&code=' . $read['tbl_courseparent_code'] . '&userid=' . $user_id) . '" >' . (mysqli_num_rows($result_exam) == 0 ? 'در امتحان شرکت نکرده است' : ' در این امتحان شرکت کرده ، مشاهده نتیجه') . '</a></div>' : '')  . '
                                </div>
                                </div>';
                        }
                        echo '</div></div>';
                    }
                    if (!empty($do) && $do == 'viewemtehan') {
                        $user_id = $_GET['userid'];
                        $result_exam = Query("select * from tbl_emtehan Where tbl_emtehan_user = '$user_id'  and tbl_emtehan_course ='$code' ");
                        $read_exam = mysqli_fetch_array($result_exam);
                        echo '<div class="form-group"><label><p class="translate">تاریخ ارسال فایل  : ' . $read_exam['tbl_emtehan_date'] . '</p></label></div>';
                        echo '<div class="form-group"><label>توضیحات</label><p>' . $read_exam['tbl_emtehan_comment'] . '</p></div>';
                        echo '<div class="form-group"><label>فایل ضمیمه</label><p><a href="../upload/emtehan/' . $read_exam['tbl_emtehan_file'] . '" target="_blank">دریافت فایل</a></p></div>';
                    }
                    if (!empty($do) && $do == 'examend') {
                        $user_id = $_GET['userid'];
                        $result_exam = Query("select * from tbl_exam Where tbl_exam_user = '$user_id'  and tbl_exam_course ='$code' ");
                        $read_exam = mysqli_fetch_array($result_exam);
                        echo '<div class="form-group"><label><p class="translate">تاریخ شرکت در امتحان  : ' . $read_exam['tbl_exam_date'] . '</p></label></div>';
                        $course = $read_exam['tbl_exam_course'];
                        $data = json_decode($read_exam['tbl_exam_data'], true);
                        foreach ($data as $keyexam => $valueexam) {
                            if ($keyexam != 'submit' && $keyexam != 'code') {
                                $question = explode("-", $keyexam);
                                $questionId = $question[1];
                                $result_question = Query("select * from tbl_question Where tbl_question_disable='0' and tbl_question_id = '$questionId' and tbl_question_parent = '$course' ");
                                $read_question = mysqli_fetch_array($result_question);
                                $all_value = json_decode($read_question['tbl_question_answer']);
                                echo '<div class="form-group exam"><h2>' . $read_question['tbl_question_title'] . '</h2>';
                                foreach ($all_value as $key => $value) {
                                    $answervalue = $value->answervalue;
                                    echo '<div class="exam-item"><div class="translate ' . ($answervalue == 1 ? 'correct' : '') . ' ' . ($valueexam == $key ? 'notcorrect' : '') . '">' . ($key + 1) . '</div>
                <input  id="radio' . $read_question['tbl_question_id'] . $key . '" type="radio" ' . ($valueexam == $key ? 'checked' : '') . ' disabled>
                      <label for="radio' . $read_question['tbl_question_id'] . $key . '"><span></span>' . $value->answer . '</label></div>';
                                }
                                echo '</div><hr>';
                            }
                        }
                    }
                    if (!empty($do) && $do == 'exam') {
                        $user_id = $_GET['userid'];
                        $result_exam = Query("select * from tbl_exam Where tbl_exam_user = '$user_id'  and tbl_exam_course ='$code' ");
                        $read_exam = mysqli_fetch_array($result_exam);
                        echo '<div class="form-group"><label><p class="translate">تاریخ شرکت در امتحان  : ' . $read_exam['tbl_exam_date'] . '</p></label></div>';
                        $course = $read_exam['tbl_exam_course'];
                        $data = json_decode($read_exam['tbl_exam_data'], true);
                        foreach ($data as $keyexam => $valueexam) {
                            if ($keyexam != 'submit' && $keyexam != 'code') {
                                $question = explode("-", $keyexam);
                                $questionId = $question[1];
                                $result_question = Query("select * from tbl_question Where tbl_question_disable='0' and tbl_question_id = '$questionId' and tbl_question_parent = '$course' ");
                                $read_question = mysqli_fetch_array($result_question);
                                $all_value = json_decode($read_question['tbl_question_answer']);
                                echo '<div class="form-group exam"><h2>' . $read_question['tbl_question_title'] . '</h2>';
                                foreach ($all_value as $key => $value) {
                                    $answervalue = $value->answervalue;
                                    echo '<div class="exam-item"><div class="translate ' . ($answervalue == 1 ? 'correct' : '') . ' ' . ($valueexam == $key ? 'notcorrect' : '') . '">' . ($key + 1) . '</div>
                <input  id="radio' . $read_question['tbl_question_id'] . $key . '" type="radio" ' . ($valueexam == $key ? 'checked' : '') . ' disabled>
                      <label for="radio' . $read_question['tbl_question_id'] . $key . '"><span></span>' . $value->answer . '</label></div>';
                                }
                                echo '</div><hr>';
                            }
                        }
                    }
                    if (isset($do) && $do == 'delete') {
                        if ($access != 2) {
                            $result = Query("SELECT * from tbl_register Where tbl_register_id = '$id' ");
                            $read = mysqli_fetch_array($result);
                            echo '<div class="col-lg-6">' . $lang[25] . '<br><br>' . $read['tbl_register_track'];
                            echo '<form action="#" method="POST">
        <input type="hidden" name="id" value="' . $id . '" >
        <div class="form-group margin-top">
        <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
        <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger">
        </form></div>';

                            if (isset($_POST['submit'])) {
                                $id = $_POST['id'];
                                Query("update tbl_register set tbl_register_view ='1' Where tbl_register_id = '$id' ");
                                $_SESSION['error'] = $lang[28];
                                header("Location: register.php");
                                exit();
                            }
                            if (isset($_POST['nodelete'])) {
                                header("Location: register.php");
                            }
                        }
                    }
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = strtolower($_POST['keyword']);
                            header("Location: register.php?keyword=$keyword_search");
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