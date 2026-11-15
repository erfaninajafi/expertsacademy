<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = $lang[2058];
$Tabpage = '901';
$thisaccess = '1';
include './header.inc.php';
$status = '';
if (isset($_GET['status'])) {
    $stt = $_GET['status'];
    $status = "tbl_member_status='$stt' and";
    $link = '?status=' . $stt . '&';
} elseif (isset($_GET['hobby'])) {
    $hobby = $_GET['hobby'];
    $status .= " tbl_member_hobby like '%[$hobby]%' and";
} else {
    $status = '';
    $link = '?';
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
                        $result_for_count = Query("SELECT * From tbl_member Where tbl_member_delete ='0' and $status (tbl_member_lname LIKE '%$keyword%'  or  tbl_member_fname LIKE '%$keyword%'  or tbl_member_email LIKE '%$keyword%' or tbl_member_mobile LIKE '%$keyword%' or tbl_member_nationalcode LIKE '%$keyword%') ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_member Where tbl_member_delete ='0' and $status (tbl_member_lname LIKE '%$keyword%'  or  tbl_member_fname LIKE '%$keyword%' or tbl_member_email LIKE '%$keyword%' or tbl_member_mobile LIKE '%$keyword%' or tbl_member_nationalcode LIKE '%$keyword%') ORDER BY tbl_member_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);

                        // start show list
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[53] . '</th>
                                    <th>' . $lang[92] . '</th>
                                    <th>' . $lang[95] . '</th>
                                    <th>'.$lang[378].'</th>
                                    <th>'.$lang[1554].'</th>
                                    <th>'.$lang[1555].'</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            if ($read['tbl_member_status'] == 0) {
                                $st = $lang[1556];
                                $style = 'style="color:red;"';
                            } else {
                                $st = $lang[1557];
                                $style = 'style="color:green;"';
                            }
                            $thisid = $read['tbl_member_id'];
                            $result_document = Query("select * from tbl_document where tbl_document_status='0' and tbl_document_userid ='$thisid' ");
                            echo '<tr>
                                <td class="align-middle p-2">' . $i . '</td>
                                <td class="align-middle p-2">
                                <div class="avatar avatar-2xl border border-3 border-light rounded-circle">
                                            <img class="rounded-circle" src="'.(!empty($read['tbl_member_profile']) ? '../upload/profile/' . $read['tbl_member_profile'] : 'assets/img/no-image.jpg').'" alt="'.$read['tbl_member_fname'].' '.$read['tbl_member_lname'].'">
                                            </div></div>
                                            <td class="align-middle p-2">
                                ' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</td>
                                <td class="align-middle p-2" >' . $read['tbl_member_email'] . '</td>
                                <td class="align-middle p-2" >' . $read['tbl_member_mobile'] . '</td>
                                <td class="align-middle p-2" >' . $read['tbl_member_logincode'] . '</td>
                                <td class="align-middle p-2" >' . mysqli_num_rows($result_document) . '</td>
                                
                                <td class="align-middle p-2">
                                <a href="archive.php?user=' . $read['tbl_member_id'] . '" class="btn me-1 mb-1 btn-sm btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1804] . '"><span class="bi-file-earmark-spreadsheet-fill"></span></a>
                                <a href="archiveorder.php?user=' . $read['tbl_member_id'] . '" class="btn me-1 mb-1 btn-sm btn-default" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[2033] . '"><span class="bi-file-earmark-spreadsheet-fill"></span></a>
                                <a href="document.php?history=' . $read['tbl_member_systemcode'] . '" class="btn me-1 mb-1 btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1312] . '" target="_blank" style="display:none;"><span class="bi-file-arrow-down-fill"></span></a> 
                                <a href="cer.php?history=' . $read['tbl_member_id'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1810] . '" target="_blank" ><span class="bi-file-arrow-down-fill"></span></a> 
                                <a href="../fa/resume/' . $read['tbl_member_id'] . '/" class="btn me-1 mb-1 btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1588]. '" target="_blank"><span class="bi-file-earmark-person"></span></a>
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
                                <li><a class="page-link" href="<?= $link . 'page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['hobby']) ? '&hobby=' . $_GET['hobby'] : '')  ?>"><?= $lang[75] ?></a>
                                </li>
                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                </li>
                                <li><a class="page-link" href="<?= $link . 'page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['hobby']) ? '&hobby=' . $_GET['hobby'] : '') ?>"><?= $lang[74] ?></a>
                                </li>
                            </ul>
                        </td>
                        <td class="col-lg-3">
                            <form action="report7.php?do=gotopage" class="search-form" method="POST">
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
                            header("Location: report7.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: report7.php?keyword=$keyword_search");
                        }
                    }
                    // end = search result  
                    ?>
            </div>
        </div>
    </div>
</div>
</div>
<?php
include './footer.inc.php';

?>