<?php

// include
include './includer.php';
$show_per_page = '30';
$Tabpage = '90';
$pagetitle = 'لیست الحاقیه ها';
$thisaccess = '1,5';
include './header.inc.php';

?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="">
                <div class="">
                    <?php
                    echo '<section class="content"><div class="col-md-12">
            <div class="box box-body">';
                    // list content

                    if (!empty($_GET['contract'])) {
                        $thiscontract = "Where tbl_attach_contract = '$_GET[contract]'";
                        $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
                        $read_contract = mysqli_fetch_array($result_contract);
                        echo '<div class="box-info">
                            <div class="item"><img src="asset/img/icons/item-icon/case.svg" class="img-fluid"><span>نام پروژه :' . $read_contract['tbl_contract_title'] . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>شماره پروژه :' . toPersianNum($read_contract['tbl_contract_number']) . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ شروع :' . toPersianNum($read_contract['tbl_contract_startdate']) . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ پایان :' . toPersianNum($read_contract['tbl_contract_enddate']) . '</span></div>

                        </div>';
                    } else {
                        $thiscontract = "";
                    }


                    if (!isset($do)) {
                        // start count db result for pagination 

                        $result_for_count = Query("SELECT * From tbl_attach $thiscontract ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination         
                        $result = Query("SELECT * From tbl_attach $thiscontract ORDER BY tbl_attach_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                        }
                        $result = Query("Select * from tbl_attach $thiscontract order by tbl_attach_id DESC LIMIT $start_contnet,$show_per_page");
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                        <thead class="table-dark">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>شماره پروژه</th>
                                    <th>شماره الحاقیه</th>
                                    <th>عنوان پروژه</th>
                                    <th>تاریخ شروع </th>
                                    <th>تاریخ پایان</th>
                                    <th>تاریخ ویرایش</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $contractId = $read['tbl_attach_contract'];
                            $resultAttach = Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                            $readContact = mysqli_fetch_array($resultAttach);

                            echo '<tr>
                                <td style="width: 1%">' . $i . '</td>
                                <td class="col-sm-1">' . $readContact['tbl_contract_number'] . '</td>
                                <td class="col-sm-1">' . $read['tbl_attach_number'] . '</td>
                                <td class="col-sm-1"><a href="?do=view&id=' . $read['tbl_attach_id'] . '" >' . $read['tbl_attach_title'] . '</a></td>
                                <td class="col-sm-1">' . $read['tbl_attach_startdate'] . '</td>
                                <td class="col-sm-1">' . $read['tbl_attach_enddate'] . '</td>
                                <td class="col-sm-1">' . $read['tbl_attach_editdate'] . '</td>
                                <td class="col-sm-1"><a href="?do=view&id=' . $read['tbl_attach_id'] . '&contract=' . $_GET['contract'] . '" class="btn btn-xs btn-success " >نمایش</a>                                
                                </td>
                                </tr>';
                            $i++;
                        }
                        // end show status   
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
                    ?>
                    <table class="table">
                        <tr>
                            <td class="col-lg-6">
                                <ul class="pagination">
                                    <li><a class="page-link"
                                            href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($status) ? "&status=$status" : '') . (!empty($history) ? "&history=$history" : '')  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a
                                            class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link"
                                            href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($status) ? "&status=$status" : '') . (!empty($history) ? "&history=$history" : '') ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="contract.php?do=gotopage" class="navbar-form navbar-left" role="search"
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
                            header("Location: attach.php?page=$page_number");
                        }
                    }
                    // end = pagination


                    // start = edit record
                    if (isset($do) && $do == 'view') {

                        $result = Query("SELECT * FROM tbl_attach Where tbl_attach_id = '$id'");
                        $read = mysqli_fetch_array($result);

                        $contractId = $read['tbl_attach_contract'];
                        $resultAttach = Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                        $readContact = mysqli_fetch_array($resultAttach);

                        ?>
                    <div class="col-lg-8">
                        <div class="view-detail">
                            <div class="form-group">
                                <label>شماره پروژه</label>
                                <p><?= toPersianNum($readContact['tbl_contract_number']) ?></p>
                            </div>
                            <div class="form-group">
                                <label>شماره الحاقیه</label>
                                <p><?= toPersianNum($read['tbl_attach_number']) ?></p>
                            </div>
                            <div class="form-group">
                                <label>عنوان</label>
                                <p><?= $read['tbl_attach_title'] ?></p>
                            </div>
                            <div class="form-group">
                                <label>تاریخ شروع</label>
                                <p><?= toPersianNum($read['tbl_attach_startdate']) ?></p>
                            </div>
                            <div class="form-group">
                                <label>تاریخ پایان</label>
                                <p><?= toPersianNum($read['tbl_attach_enddate']) ?></p>
                            </div>
                            <div class="form-group">
                                <label>تاریخ ویرایش</label>
                                <p><?= toPersianNum($read['tbl_attach_editdate']) ?></p>
                            </div>
                            <div class="form-group">
                                <label>توضیحات</label>
                                <p><?= $read['tbl_attach_text'] ?></p>
                            </div>
                            <div class="form-group">
                                <label>فایل</label>
                                <p><?php
                                        $file = explode(",", $read['tbl_attach_file']);
                                        foreach ($file as $key => $value) {
                                            if (!empty($value)) {
                                                echo '<a href="' . $value . '">فایل [' . $key . ']</a>';
                                            }
                                        } ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
                    }

                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: attach.php?keyword=$keyword_search");
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