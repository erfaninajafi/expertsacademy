<?php

include './includer.php';

$show_per_page = '20';
$pagetitle = $lang[1830];
$Tabpage = '101';
$cat_manager_id = '0';
$thisaccess = '1,8';
$img_item = '2';

include './header.inc.php';
$select = '';
$selectfinal = '';
// if ($access == 8) {
//     $select .= " tbl_mohtava_admin='$this_user_id' and  ";
// } else {
//     $select .= '';
// }
if (!empty($_GET['status'])) {

    $select .= " tbl_mohtava_statustext='$_GET[status]' and  ";
}

if ($access == '8') {
    $h = 1;
    $backhsh = explode(",", $accessBakhsh);
    foreach ($backhsh as $key => $value) {
        if (!empty($value)) {
            if ($h == 1) {
                $selectfinal .= "(";
            }
            $selectfinal .= " tbl_mohtava_department like '%$value%' or ";
            $h++;
        }
    }
    if ($h != 1) {
        $selectfinal = rtrim($selectfinal, " or ");
        $select .= $selectfinal . ") and ";
    }
}
// if($access == '8')
// {
//     $select .= " tbl_mohtava_admin='$this_user_id' and  ";
// }

if (!empty($_GET['mohatavafile'])) {
    $mohatavafile = sqi($_GET['mohatavafile']);
    $select .= " tbl_mohtava_codefile like '%$mohatavafile%' and  ";
}

if (!empty($_GET['subtitle'])) {
    $subtitle = sqi($_GET['subtitle']);
    $select .= " tbl_mohtava_subtitle1 like '%$subtitle%' and  ";
}
if (isset($_GET['status'])) {
    $status = sqi($_GET['status']);
    $select .= " tbl_mohtava_statustext = '$status' and  ";
}




if (!empty($_GET['mohatavatitle'])) {
    $mohatavatitle = sqi($_GET['mohatavatitle']);
    $select .= " tbl_mohtava_title like '%$mohatavatitle%' and  ";
}


if (!empty($_GET['mohatavacreator'])) {
    $mohatavacreator = sqi($_GET['mohatavacreator']);
    $select .= " tbl_mohtava_admin = '$mohatavacreator' and  ";
}

if (!empty($_GET['malek'])) {
    $mohatavacreator = sqi($_GET['malek']);
    $select .= " tbl_mohtava_producer like '%$mohatavacreator%' and  ";
}

if (!empty($_GET['department'])) {
    $department = sqi($_GET['department']);
    $select .= " tbl_mohtava_department = '$department' and  ";
}
$select =  rtrim($select, " and ");
if (empty($select)) {
    $select = " tbl_mohtava_id != '' ";
}

?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle ?></h4>
                            <?= (!isset($do) && ($access == '8' || $access == '1') ? '<div class="plus">
                                <a href="?do=add"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?= (!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <div class="card-body p-0 position-relative">
                <?php
                if (!isset($do)) { ?>
                <table>
                    <?php if ($access == '1' || $access == '8') { ?>
                    <tr>
                        <td class="col-sm-12 p-3">
                            <form action="?do=search" method="POST" class="search-form">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12 col-lg-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control width-full" value="<?= (!empty($_GET['mohatavafile']) ? $_GET['mohatavafile'] : '') ?>" name="mohatavafile" placeholder="<?= $lang[1831] ?>">
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control width-full" value="<?= (!empty($_GET['subtitle']) ? $_GET['subtitle'] : '') ?>" name="subtitle" placeholder="<?= $lang[854] ?>">
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <div class="form-group">
                                                <input type="text" class="form-control width-full" value="<?= (!empty($_GET['mohatavatitle']) ? $_GET['mohatavatitle'] : '') ?>" name="mohatavatitle" placeholder="<?= $lang[1334] ?>">
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <div class="form-group">
                                                <select name="mohatavacreator" data-placeholder="<?= $lang[1845] ?>" class="select2 width-full" placeholder="<?= $lang[1845] ?>">
                                                    <option value=""><?= $lang[1845] ?></option>
                                                    <?php
                                                            $resultKey = Query("SELECT * From tbl_user Where tbl_user_delete='0' and tbl_user_role like '%8%' ");
                                                            while ($readKey = mysqli_fetch_array($resultKey)) {
                                                                echo '<option value="' . $readKey['tbl_user_id'] . '" class="group-cat" ' . (!empty($_GET['mohatavacreator']) && $_GET['mohatavacreator'] == $readKey['tbl_user_id'] ? 'selected' : '') . '>' . $readKey['tbl_user_name'] . '</option>';
                                                            }
                                                            ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-3">
                                            <div class="form-group">
                                                <select name="malek" data-placeholder="<?= $lang[1948] ?>" class="select2 width-full" placeholder="<?= $lang[1948] ?>">
                                                    <option value=""><?= $lang[1948] ?></option>
                                                    <?php

                                                            $resultKey = Query("SELECT * From tbl_user Where tbl_user_delete='0' and tbl_user_contentcreator='1' and tbl_user_bakhsh='1' ORDER BY tbl_user_id DESC ");
                                                            while ($readKey = mysqli_fetch_array($resultKey)) {
                                                                echo '<option value="' . $readKey['tbl_user_id'] . '" class="group-cat" ' . (!empty($_GET['malek']) && $_GET['malek'] == $readKey['tbl_user_id'] ? 'selected' : '') . '>' . $readKey['tbl_user_name'] . '</option>';
                                                            }
                                                            ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-3">
                                            <div class="form-group">
                                                <select name="status" data-placeholder="<?= $lang[1843] ?>" class="select2 width-full" placeholder="<?= $lang[1843] ?>">
                                                    <option value=""><?= $lang[1843] ?></option>
                                                    <?php
                                                            for ($i = 0; $i <= 4; $i++) {
                                                                echo '<option value="' . $i . '" class="group-cat"  ' . (isset($_GET['status']) && $_GET['status'] == $i ? 'selected' : '') . '>' . $lang[$i + 1908] . '</option>';
                                                            }
                                                            ?>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-12 col-lg-3">
                                            <div class="form-group">
                                                <select name="department" data-placeholder="<?= $lang[1838] ?>" style="width:100%;" class="select2" placeholder="<?= $lang[1838] ?>">
                                                    <option value=""><?= $lang[1838] ?></option>
                                                    <?php
                                                            $resultKey = Query("SELECT * From tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                                            while ($readKey = mysqli_fetch_array($resultKey)) {

                                                                echo '<option value="' . $readKey['tbl_categories_code'] . '" class="group-cat" ' . (!empty($_GET['department']) && $_GET['department'] == $readKey['tbl_categories_code'] ? 'selected' : '') . '>' . $readKey['tbl_categories_title'] . '</option>';
                                                            }
                                                            ?>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-12 col-lg-1">
                                            <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                                        </div>
                                        <div class="col-12 col-lg-1">
                                            <a href="csv.php?do=mohtava<?= (!empty($_GET['mohatavafile']) ? '&mohatavafile=' . $_GET['mohatavafile'] : '') .
                                                                                    (!empty($_GET['mohatavatitle']) ? '&mohatavatitle=' . $_GET['mohatavatitle'] : '') .
                                                                                    (!empty($_GET['mohatavacreator']) ? '&mohatavacreator=' . $_GET['mohatavacreator'] : '') .
                                                                                    (!empty($_GET['department']) ? '&department=' . $_GET['department'] : '') .
                                                                                    (!empty($_GET['status']) ? '&status=' . $_GET['status'] : '') .
                                                                                    (!empty($_GET['subtitle']) ? '&subtitle=' . $_GET['subtitle'] : '') .
                                                                                    (!empty($_GET['malek']) ? '&malek=' . $_GET['malek'] : '')
                                                                                ?>" class="btn  btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[2028] ?>"><span class="bi-file-text-fill"></span></a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
                <?php
                    $result_for_count = Query("SELECT * From tbl_mohtava Where tbl_mohtava_lng='$language_default' and tbl_mohtava_delete='0' and (tbl_mohtava_codefile LIKE '%$keyword%' or tbl_mohtava_title LIKE '%$keyword%' or tbl_mohtava_code LIKE '%$keyword%') and $select  ");
                    $count_result = mysqli_num_rows($result_for_count);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    $result = Query("SELECT * From tbl_mohtava Where  tbl_mohtava_lng='$language_default' and tbl_mohtava_delete='0' and (tbl_mohtava_codefile LIKE '%$keyword%' or tbl_mohtava_title LIKE '%$keyword%'  or tbl_mohtava_code LIKE '%$keyword%') and $select ORDER BY tbl_mohtava_id DESC LIMIT $start_contnet,$show_per_page ");
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                    <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[53] . '</th>
                                    <th>' . $lang[1831] . '</th>
                                    <th>' . $lang[1334] . '</th>
                                    <th>' . $lang[1845] . '</th>
                                    <th>' . $lang[1340] . '</th>
                                    <th>' . $lang[1844] . '</th>
                                    <th>' . $lang[1842] . '</th>
                                    <th>' . $lang[2030] . '</th>
                                    ' . ($access == '1' ? '<th>' . $lang[1921] . '</th>' : '') . '
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $i = 1;
                    while ($read = mysqli_fetch_array($result)) {
                        $admin = $read['tbl_mohtava_admin'];
                        $resultAdmin = Query("SELECT * From tbl_user Where tbl_user_id='$admin' ");
                        $readAdmin = mysqli_fetch_array($resultAdmin);

                        $resultEn = Query("SELECT tbl_mohtava_title From tbl_mohtava Where  tbl_mohtava_lng='en' and tbl_mohtava_code='$read[tbl_mohtava_code]'");
                        $readEn = mysqli_fetch_array($resultEn);
                        $file = explode(",", $read['tbl_mohtava_file']);

                        $res_rate = Query("SELECT total_points,rating_number, FORMAT((total_points / rating_number),1) as average_rating FROM tbl_rating WHERE post_id = '$read[tbl_mohtava_id]' ");
                        $read_rate = mysqli_fetch_array($res_rate);

                        echo '<tr>
                                <td class="align-middle p-2">' . $i . '</td>
                                <td class="align-middle p-2">
                                <div class="avatar avatar-2xl border border-3 border-light rounded-circle">
                                <img class="rounded-circle" src="' . (!empty($file[1]) ? '../upload/course/' . $file[1] : 'assets/img/no-image.jpg') . '" >
                                </div>
                                </td>
                                <td class="align-middle p-2">' . (!empty($read['tbl_mohtava_codefile']) ? strtoupper($read['tbl_mohtava_codefile']) : '<span style="color:red;">T-' . $read['tbl_mohtava_code'] . '</span>') . '</td>
                                <td class="align-middle p-2">' . $read['tbl_mohtava_title'] . '<br>' . @$readEn['tbl_mohtava_title'] . '' . (!empty($read['tbl_mohtava_ghaleb']) ? '<br>' . $lang[1832] . ' : ' . $lang[$read['tbl_mohtava_ghaleb']] : '') . '</td>
                                <td class="align-middle p-2">' . $readAdmin['tbl_user_name'] . '</td>
                                <td class="align-middle p-2">' . $lang[$read['tbl_mohtava_statustext'] + 1908] . '</td>
                                <td class="align-middle p-2">' . $read['tbl_mohtava_dateadd'] . '</td>
                                <td class="align-middle p-2">' . $read['tbl_mohtava_enteshar'] . '</td>
                                <td class="align-middle p-2">' . (!empty($read_rate['total_points']) ? round($read_rate['total_points'] / $read_rate['rating_number']) : '0') . '</td>
                                ' . ($access == '1' ? '<td class="align-middle p-2"><input type="checkbox" name="specialmohtava" value="1-' . $read['tbl_mohtava_code'] . '" ' . ($read['tbl_mohtava_special'] == '1'  ? 'checked' : '') . '> ' . $lang[1921] . '</td>' : '') . '
                                <td class="align-middle p-2">
                                ' . (!empty($read['tbl_mohtava_admin'])  && $access == '8' && $read['tbl_mohtava_admin'] == $this_user_id ? '
                                <a href="?do=edit&code=' . $read['tbl_mohtava_code'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                                <a href="?do=delete&code=' . $read['tbl_mohtava_code'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>
                                ' : ($access == '1' ? '
                                <a href="?do=edit&code=' . $read['tbl_mohtava_code'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                                <a href="?do=delete&code=' . $read['tbl_mohtava_code'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>' : '')) . '
                                ' . (!empty($file[0]) ? (!empty($read['tbl_mohtava_foldername']) ? '<a href="../uploadcourse/' . $read['tbl_mohtava_foldername'] . '/' . $file[0] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[58] . '" target="_blank"><span class="bi-eye-fill"></span></a>' : '<a href="../upload/course/' . $file[0] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[58] . '" target="_blank"><span class="bi-eye-fill"></span></a>') : '') . '
                                ' . (!empty($read['tbl_mohtava_admin']) && $read['tbl_mohtava_admin'] == $this_user_id && $access == '8' ? '<a href="?do=copy&code=' . $read['tbl_mohtava_code'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1958] . '"><span class="bi-app"></span></a>' : ($access == '1' ? '<a href="?do=copy&code=' . $read['tbl_mohtava_code'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1958] . '"><span class="bi-app"></span></a>' : '')) . '</td>
                                </tr>';
                        $i++;
                    }

                    echo '</tbody></table>';

                    if ($page > $page_number) {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    if ($count_result > $show_per_page) {
                        if ($page == 1) {
                            $back_page_number = 2;
                        } else {
                            $back_page_number = $page;
                        }

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
                                <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword .
                                                                            (!empty($_GET['mohatavafile']) ? '&mohatavafile=' . $_GET['mohatavafile'] : '') .
                                                                            (!empty($_GET['mohatavatitle']) ? '&mohatavatitle=' . $_GET['mohatavatitle'] : '') .
                                                                            (!empty($_GET['mohatavacreator']) ? '&mohatavacreator=' . $_GET['mohatavacreator'] : '') .
                                                                            (!empty($_GET['department']) ? '&department=' . $_GET['department'] : '') .
                                                                            (!empty($_GET['status']) ? '&status=' . $_GET['status'] : '') .
                                                                            (!empty($_GET['subtitle']) ? '&subtitle=' . $_GET['subtitle'] : '') .
                                                                            (!empty($_GET['malek']) ? '&malek=' . $_GET['malek'] : '')   ?>"><?= $lang[75] ?></a>
                                </li>
                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                </li>
                                <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['mohatavafile']) ? '&mohatavafile=' . $_GET['mohatavafile'] : '') .
                                                                            (!empty($_GET['mohatavatitle']) ? '&mohatavatitle=' . $_GET['mohatavatitle'] : '') .
                                                                            (!empty($_GET['mohatavacreator']) ? '&mohatavacreator=' . $_GET['mohatavacreator'] : '') .
                                                                            (!empty($_GET['department']) ? '&department=' . $_GET['department'] : '') .
                                                                            (!empty($_GET['status']) ? '&status=' . $_GET['status'] : '') .
                                                                            (!empty($_GET['subtitle']) ? '&subtitle=' . $_GET['subtitle'] : '') .
                                                                            (!empty($_GET['malek']) ? '&malek=' . $_GET['malek'] : '') ?>"><?= $lang[74] ?></a>
                                </li>
                            </ul>
                        </td>
                        <td class="col-lg-3">
                            <form action="mohtava.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
                                <div class="form-group">
                                    <button type="submit" name="gotopage" class="btn btn-success" style="float: left;"><?= $lang[76] ?></button>
                                    <input type="text" class="form-control go-fld" name="page_number" style="width: 100px;float: left;">
                                </div>
                            </form>
                        </td>
                    </tr>
                </table>
                <?php
                    }
                }
                if (isset($do) && $do == 'runupdate') {
                    $code = $_POST['code'];
                    $t = $_POST['t'];
                    $page = $_POST['page'];
                    foreach ($code as $key => $value) {
                        $val = $_POST['o' . $value];
                        Query("update tbl_mohtava set tbl_mohtava_fld5='$val' Where tbl_mohtava_admin='$this_user_id' and tbl_mohtava_code = '$value' ");
                    }
                    header("Location: mohtava.php?page=$page");
                }
                if (isset($do) && $do == 'copy') {
                    $result = Query("select * from tbl_mohtava Where tbl_mohtava_admin='$this_user_id' and tbl_mohtava_code = '$code' ");
                    $read = mysqli_fetch_array($result);
                    $tbl_mohtava_codefile = '';
                    $tbl_mohtava_code = Code("", "no", 8);
                    $tbl_mohtava_lng = $read['tbl_mohtava_lng'];
                    $tbl_mohtava_title = $read['tbl_mohtava_title'];
                    $tbl_mohtava_page = $read['tbl_mohtava_page'];
                    $tbl_mohtava_time = $read['tbl_mohtava_time'];
                    $tbl_mohtava_creator = $read['tbl_mohtava_creator'];
                    $tbl_mohtava_enteshar = $read['tbl_mohtava_enteshar'];
                    $tbl_mohtava_status = $read['tbl_mohtava_status'];
                    $tbl_mohtava_dateadd = $read['tbl_mohtava_dateadd'];
                    $tbl_mohtava_owner = $read['tbl_mohtava_owner'];
                    $tbl_mohtava_price = $read['tbl_mohtava_price'];
                    $tbl_mohtava_codehazine = $read['tbl_mohtava_codehazine'];
                    $tbl_mohtava_sellprice = $read['tbl_mohtava_sellprice'];
                    $tbl_mohtava_percentmalek = $read['tbl_mohtava_percentmalek'];
                    $tbl_mohtava_percentdepartement = $read['tbl_mohtava_percentdepartement'];
                    $tbl_mohtava_percentacademy = $read['tbl_mohtava_percentacademy'];
                    $tbl_mohtava_dashboardshow = $read['tbl_mohtava_dashboardshow'];
                    $tbl_mohtava_datehide = $read['tbl_mohtava_datehide'];
                    $tbl_mohtava_dateend = $read['tbl_mohtava_dateend'];
                    $tbl_mohtava_text = $read['tbl_mohtava_text'];
                    $tbl_mohtava_ghaleb = $read['tbl_mohtava_ghaleb'];
                    $tbl_mohtava_subtitle1 = $read['tbl_mohtava_subtitle1'];
                    $tbl_mohtava_subtitle2 = $read['tbl_mohtava_subtitle2'];
                    $tbl_mohtava_keywords = $read['tbl_mohtava_keywords'];
                    $tbl_mohtava_link = $read['tbl_mohtava_link'];
                    $tbl_mohtava_refrence = $read['tbl_mohtava_refrence'];
                    $tbl_mohtava_department = $read['tbl_mohtava_department'];
                    $tbl_mohtava_statustext = $read['tbl_mohtava_statustext'];
                    $tbl_mohtava_pricedate = $read['tbl_mohtava_pricedate'];
                    $tbl_mohtava_admin = $read['tbl_mohtava_admin'];
                    $tbl_mohtava_adminadddate = $read['tbl_mohtava_adminadddate'];
                    $tbl_mohtava_file = $read['tbl_mohtava_file'];
                    $tbl_mohtava_foldername = $read['tbl_mohtava_foldername'];
                    $tbl_mohtava_newadmin = $read['tbl_mohtava_newadmin'];
                    $tbl_mohtava_admineditdate = $read['tbl_mohtava_admineditdate'];
                    $tbl_mohtava_delete = $read['tbl_mohtava_delete'];
                    $tbl_mohtava_special = $read['tbl_mohtava_special'];
                    $tbl_mohtava_moalef = $read['tbl_mohtava_moalef'];
                    $tbl_mohtava_producer = $read['tbl_mohtava_producer'];
                    $tbl_mohtava_motarjem = $read['tbl_mohtava_motarjem'];
                    $tbl_mohtava_departmentcode = $read['tbl_mohtava_departmentcode'];
                    $tbl_mohtava_ghalebcode = $read['tbl_mohtava_ghalebcode'];

                    for ($i = 1; $i <= $language_count; $i++) {

                        $language_record = $language[$i];

                        Query("insert into tbl_mohtava (
                            tbl_mohtava_codefile,
                            tbl_mohtava_code,
                            tbl_mohtava_lng,
                            tbl_mohtava_title,
                            tbl_mohtava_page,
                            tbl_mohtava_time,
                            tbl_mohtava_creator,
                            tbl_mohtava_enteshar,
                            tbl_mohtava_status,
                            tbl_mohtava_dateadd,
                            tbl_mohtava_owner,
                            tbl_mohtava_price,
                            tbl_mohtava_codehazine,
                            tbl_mohtava_sellprice,
                            tbl_mohtava_percentmalek,
                            tbl_mohtava_percentdepartement,
                            tbl_mohtava_percentacademy,
                            tbl_mohtava_dashboardshow,
                            tbl_mohtava_datehide,
                            tbl_mohtava_dateend,
                            tbl_mohtava_text,
                            tbl_mohtava_ghaleb,
                            tbl_mohtava_subtitle1,
                            tbl_mohtava_subtitle2,
                            tbl_mohtava_keywords,
                            tbl_mohtava_link,
                            tbl_mohtava_refrence,
                            tbl_mohtava_department,
                            tbl_mohtava_statustext,
                            tbl_mohtava_pricedate,
                            tbl_mohtava_admin,
                            tbl_mohtava_adminadddate,
                            tbl_mohtava_file,
                            tbl_mohtava_foldername,
                            tbl_mohtava_newadmin,
                            tbl_mohtava_admineditdate,
                            tbl_mohtava_delete,
                            tbl_mohtava_special,
                            tbl_mohtava_moalef,
                            tbl_mohtava_producer,
                            tbl_mohtava_motarjem,
                            tbl_mohtava_departmentcode,
                            tbl_mohtava_ghalebcode
                        )  values('$tbl_mohtava_codefile',
                        '$tbl_mohtava_code',
                        '$language_record',
                        '$tbl_mohtava_title',
                        '$tbl_mohtava_page',
                        '$tbl_mohtava_time',
                        '$tbl_mohtava_creator',
                        '$tbl_mohtava_enteshar',
                        '0',
                        '$tbl_mohtava_dateadd',
                        '$tbl_mohtava_owner',
                        '$tbl_mohtava_price',
                        '$tbl_mohtava_codehazine',
                        '$tbl_mohtava_sellprice',
                        '$tbl_mohtava_percentmalek',
                        '$tbl_mohtava_percentdepartement',
                        '$tbl_mohtava_percentacademy',
                        '$tbl_mohtava_dashboardshow',
                        '$tbl_mohtava_datehide',
                        '$tbl_mohtava_dateend',
                        '$tbl_mohtava_text',
                        '$tbl_mohtava_ghaleb',
                        '$tbl_mohtava_subtitle1',
                        '$tbl_mohtava_subtitle2',
                        '$tbl_mohtava_keywords',
                        '$tbl_mohtava_link',
                        '$tbl_mohtava_refrence',
                        '$tbl_mohtava_department',
                        '$tbl_mohtava_statustext',
                        '$tbl_mohtava_pricedate',
                        '$tbl_mohtava_admin',
                        '$tbl_mohtava_adminadddate',
                        '$tbl_mohtava_file',
                        '$tbl_mohtava_foldername',
                        '$tbl_mohtava_newadmin',
                        '$tbl_mohtava_admineditdate',
                        '$tbl_mohtava_delete',
                        '$tbl_mohtava_special',
                        '$tbl_mohtava_moalef',
                        '$tbl_mohtava_producer',
                        '$tbl_mohtava_motarjem',
                        '$tbl_mohtava_departmentcode',
                        '$tbl_mohtava_ghalebcode')");
                    }
                    header("Location: mohtava.php");
                }
                if (isset($do) && $do == 'gotopage') {
                    if (isset($_POST['gotopage'])) {
                        $page_number = $_POST['page_number'];
                        header("Location: mohtava.php?page=$page_number");
                    }
                }

                if (isset($do) && $do == 'search') {
                    if (isset($_POST['go'])) {
                        $mohatavafile = $_POST['mohatavafile'];
                        $mohatavatitle = $_POST['mohatavatitle'];
                        $mohatavacreator = $_POST['mohatavacreator'];
                        $department = $_POST['department'];
                        $status = $_POST['status'];
                        $subtitle = $_POST['subtitle'];
                        $malek = $_POST['malek'];
                        header("Location: mohtava.php?mohatavafile=$mohatavafile&mohatavatitle=$mohatavatitle&mohatavacreator=$mohatavacreator&status=$status&department=$department&subtitle=$subtitle&malek=$malek");
                    }
                }

                if (isset($do) && $do == 'add') {
                    echo '<div class="col-lg-10 col-12 p-3">
                          <form action="?do=additem" method="POST" enctype="multipart/form-data" >';
                    ?>
                <!-- <div class="form-group">
                    <label><?= $lang[1831] ?><span class="required">*</span><div class="checkcodefileresult"></div></label>
                    <input type="text" name="codefile" class="form-control checkcodefile checkname" data-code="" required>
                </div> -->

                <?php

                    if ($access == '1') {
                        echo '<div class="form-group">
                        <label>' . $lang[1826] . '<span class="required">*</span></label><div>
                        <select name="owner"  data-placeholder="' . $lang[1826] . '" style="width:100%;" class="select2"  required>
                        <option value="">' . $lang[70] . '</option>';
                        $resultKey = Query("SELECT * From  tbl_user where tbl_user_access='8' and tbl_user_karfarma='0'  order by tbl_user_id DESC ");
                        while ($readKey = mysqli_fetch_array($resultKey)) {
                            echo '<option value="' . $readKey['tbl_user_id'] . '" class="group-cat">' . $readKey['tbl_user_name'] . '</option>';
                        }
                        echo '</select></div></div><hr>';
                    }

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                              <label>' . $lang[1856] . '<span class="required">*</span><span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="title_' . $language[$i] . '" class="form-control" ' . ($i == '1' ? 'required' : '') . '>
                              </div>';
                    }
                    ?>

                <?php
                    echo '<div class="form-group">
                            <label>' . $lang[1832] . '<span class="required">*</span></label><div>
                            <select name="ghaleb"  data-placeholder="' . $lang[1832] . '" style="width:100%;" class="select2" required>
                            <option value="">' . $lang[70] . '</option>';
                    for ($i = 1857; $i <= 1873; $i++) {
                        $thiscode = explode("-", $lang[$i]);
                        echo '<option value="' . $i . '-' . str_replace("کد", "", $thiscode[1]) . '" class="group-cat">' . $lang[$i] . '</option>';
                    }
                    echo '</select></div></div><hr>';

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                                  <label>' . $lang[1833] . '<span class="required">*</span><span class="language">' . $language[$i] . '</span></label>
                                  <input type="text" name="subtitle1_' . $language[$i] . '" class="form-control" ' . ($i == '1' ? 'required' : '') . '>
                                  </div>';
                    }

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                                  <label>' . $lang[1834] . '<span class="language">' . $language[$i] . '</span></label>
                                  <input type="text" name="subtitle2_' . $language[$i] . '" class="form-control">
                                  </div>';
                    }

                    echo '<div class="form-group">
                        <label>' . $lang[1838] . '<span class="required">*</span></label><div>
                        <select name="department"  data-placeholder="' . $lang[1838] . '" style="width:100%;" class="select2"  required>
                        <option value="">' . $lang[70] . '</option>';
                    $resultKey = Query("SELECT * From tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                    while ($readKey = mysqli_fetch_array($resultKey)) {
                        if ($access == '8') {
                            echo (strpos($accessBakhsh, $readKey['tbl_categories_code']) !== false ? '<option value="' . $readKey['tbl_categories_code'] . '-' . $readKey['tbl_categories_catcode'] . '" class="group-cat">' . $readKey['tbl_categories_title'] . '</option>' : '');
                        } else {
                            echo '<option value="' . $readKey['tbl_categories_code'] . '-' . $readKey['tbl_categories_catcode'] . '" class="group-cat">' . $readKey['tbl_categories_title'] . '</option>';
                        }
                    }
                    echo '</select></div></div><hr>';

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                    <label>' . $lang[2067] . '<span class="language">' . $language[$i] . '</span></label>
                    <textarea name="shortcontent_' . $language[$i] . '" class="form-control"></textarea>
                    </div>';
                    }

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                                    <label>' . $lang[1890] . '<span class="language">' . $language[$i] . '</span></label>
                                    <textarea name="text_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
                                    </div>';
                    }

                    echo '<div class="form-group">
                            <label>' . $lang[1835] . '</label><div>
                            <select name="keywords[]"  data-placeholder="' . $lang[1835] . '" style="width:100%;" class="selectmulti" multiple>
                            <option value="0">' . $lang[70] . '</option>';
                    $resultKey = Query("SELECT * From tbl_content Where tbl_content_type='21' and tbl_content_fld1='$language_default' ORDER BY tbl_content_id DESC ");
                    while ($readKey = mysqli_fetch_array($resultKey)) {
                        echo '<option value="' . $readKey['tbl_content_code'] . '" class="group-cat">' . $readKey['tbl_content_title'] . '</option>';
                    }
                    echo '</select></div></div><hr>';
                    ?>

                <div class="form-group">
                    <label><?= $lang[1837] ?><span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1964] ?>" data-bs-html="true">
                            <elm class="bi-patch-question-fill"></elm>
                        </span></label>
                    <input type="text" name="refrence" class="form-control">
                </div>

                <div class="form-group">
                    <label><?= $lang[1836] ?></label>
                    <input type="text" name="link" class="form-control">
                </div>


                <div class="form-group">
                    <label><?= $lang[1839] ?></label>
                    <input type="text" name="page" class="form-control">
                </div>
                <div class="form-group">
                    <label><?= $lang[1840] ?></label>
                    <input type="text" name="time" class="form-control">
                </div>
                <div class="form-group">
                    <label><?= $lang[1949] ?></label>
                    <input type="text" name="moalef" class="form-control">
                </div>
                <div class="form-group">
                    <label><?= $lang[1959] ?></label>
                    <input type="text" name="motarjem" class="form-control">
                </div>
                <?php
                    echo '<div class="form-group">
                <label>' . $lang[1948] . '</label><div>
                <select name="creator"  data-placeholder="' . $lang[1948] . '" style="width:100%;" class="select2" >
                <option value="0">' . $lang[70] . '</option>';
                    $resultKey = Query("SELECT * From tbl_user Where tbl_user_contentcreator='1' and tbl_user_bakhsh='1' ORDER BY tbl_user_id DESC ");
                    while ($readKey = mysqli_fetch_array($resultKey)) {
                        echo '<option value="' . $readKey['tbl_user_id'] . '" class="group-cat">' . $readKey['tbl_user_name'] . '</option>';
                    }
                    echo '</select></div></div><hr>';

                    echo '<div class="form-group">
                <label>' . $lang[1951] . '</label><div>
                <select name="producer"  data-placeholder="' . $lang[1951] . '" style="width:100%;" class="select2" >
                <option value="0">' . $lang[70] . '</option>';
                    $resultKey = Query("SELECT * From tbl_user Where tbl_user_contentcreator='1' and tbl_user_bakhsh='2' ORDER BY tbl_user_id DESC ");
                    while ($readKey = mysqli_fetch_array($resultKey)) {
                        echo '<option value="' . $readKey['tbl_user_id'] . '" class="group-cat">' . $readKey['tbl_user_name'] . '</option>';
                    }
                    echo '</select></div></div>';

                    if ($access == '1') {
                    ?>
                <div class="form-group">
                    <label><?= $lang[1842] ?><span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1963] ?>" data-bs-html="true">
                            <elm class="bi-patch-question-fill"></elm>
                        </span></label>
                    <input type="text" name="enteshar" class="form-control datepicker">
                </div>
                <?php

                        echo '<div class="form-group">
                        <label>' . $lang[1843] . '</label><div>
                        <select name="statustext"  data-placeholder="' . $lang[1843] . '" style="width:100%;" class="select2" required>
                        <option value="0">' . $lang[70] . '</option>';
                        for ($i = 0; $i <= 4; $i++) {
                            echo '<option value="' . $i . '" class="group-cat"  >' . $lang[$i + 1908] . '</option>';
                        }
                        echo '</select></div></div><hr>';

                        ?>
                <div class="form-group">
                    <label><?= $lang[1844] ?></label>
                    <input type="text" name="dateadd" class="form-control datepicker">
                </div>
                <div class="form-group">
                    <label><?= $lang[1846] ?></label>
                    <input type="text" name="price" class="form-control ">
                </div>
                <div class="form-group">
                    <label><?= $lang[1847] ?></label>
                    <input type="text" name="codehazine" class="form-control ">
                </div>
                <div class="form-group">
                    <label><?= $lang[1848] ?></label>
                    <input type="text" name="sellprice" class="form-control ">
                </div>
                <div class="form-group">
                    <label><?= $lang[1849] ?></label>
                    <input type="text" name="percentmalek" class="form-control ">
                </div>
                <div class="form-group">
                    <label><?= $lang[1850] ?></label>
                    <input type="text" name="percentdepartement" class="form-control">
                </div>
                <div class="form-group">
                    <label><?= $lang[1851] ?></label>
                    <input type="text" name="percentacademy" class="form-control ">
                </div>
                <div class="form-group">
                    <label><?= $lang[1852] ?></label>
                    <input type="text" name="pricedate" class="form-control datepicker">
                </div>
                <?php

                        echo '<div class="form-group">
                        <label>' . $lang[1853] . '</label><div>
                        <select name="dashboardshow"  data-placeholder="' . $lang[1853] . '" style="width:100%;" class="select2" >
                        <option value="0">' . $lang[70] . '</option>';
                        for ($i = 1891; $i <= 1895; $i++) {
                            echo '<option value="' . $i . '" class="group-cat">' . $lang[$i] . '</option>';
                        }
                        echo '</select></div></div><hr>';
                        ?>
                <div class="form-group">
                    <label><?= $lang[1854] ?></label>
                    <input type="text" name="datehide" class="form-control datepicker">
                </div>
                <div class="form-group">
                    <label><?= $lang[1855] ?></label>
                    <input type="text" name="dateend" class="form-control datepicker">
                </div>
                <?php } ?>
                <div class="form-group">
                    <label><?= $lang[1903] ?><span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1960] ?>" data-bs-html="true">
                            <elm class="bi-patch-question-fill"></elm>
                        </span></label>
                    <input type="text" name="foldername" class="form-control">
                </div>
                <?php
                    for ($i = 1; $i <= $img_item; $i++) {
                        echo '<div class="form-group file-margin"><table><tr>
                            <td><label class="col-sm-12 control-label">' . ($i == '2' ? $lang[1905] . '<span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1961] . '" data-bs-html="true"><elm class="bi-patch-question-fill"></elm></span>' : $lang[1906] . '<span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1961] . '" data-bs-html="true"><elm class="bi-patch-question-fill"></elm></span>') . '</label><input type="file" name="img_' . $i . '" class="form-control"></td></tr>
                            ' . ($i == '1' ? '<tr><td><label class="col-sm-12 control-label">' . $lang[1907] . '<span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1962] . '" data-bs-html="true"><elm class="bi-patch-question-fill"></elm></span></label><input type="hidden" id="ftplink_' . $i . '" name="ftplink_' . $i . '" data="ftplink_' . $i . '" class="form-control" ><input class="selectfile form-control" data="ftplink_' . $i . '" type="file"></td></tr>' : '') . '
                            </table></div>';
                    }
                    echo '<div class="form-group">
                             <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary mt-3">
                             </div>
                            </form></div>';
                }
                if (isset($do) && $do == 'additem') {
                    if (isset($_POST['additem'])) {
                        $code = Code("", "no", 7);
                        //$codefile = $_POST['codefile'];
                        $ghalebRef = explode("-", $_POST['ghaleb']);
                        $ghaleb = $ghalebRef['0'];
                        $ghalebCode = strtolower($ghalebRef['1']);
                        $departmentRef = explode("-", $_POST['department']);
                        $department = $departmentRef[0];
                        $departmentCode = strtolower($departmentRef[1]);
                        $link = sqi($_POST['link']);
                        $refrence = sqi($_POST['refrence']);
                        $page = sqi($_POST['page']);
                        $time = sqi($_POST['time']);
                        $creator = sqi($_POST['creator']);
                        $enteshar = sqi($_POST['enteshar']);
                        $statustext = (isset($_POST['statustext']) ? sqi($_POST['statustext']) : '0');
                        $dateadd = sqi($_POST['dateadd']);
                        $price = sqi($_POST['price']);
                        $moalef = sqi($_POST['moalef']);
                        $motarjem = sqi($_POST['motarjem']);
                        $producer = sqi($_POST['producer']);
                        $codehazine = sqi($_POST['codehazine']);
                        $sellprice = sqi($_POST['sellprice']);
                        $percentmalek = (!empty($_POST['percentmalek']) ? sqi($_POST['percentmalek']) : '0');
                        $percentdepartement = (!empty($_POST['percentdepartement']) ? sqi($_POST['percentdepartement']) : '0');
                        $percentacademy = (!empty($_POST['percentacademy']) ? sqi($_POST['percentacademy']) : '0');
                        $pricedate = @sqi($_POST['pricedate']);
                        $dashboardshow = @sqi($_POST['dashboardshow']);
                        $datehide = @$_POST['datehide'];
                        $dateend = @$_POST['dateend'];
                        $foldername = $_POST['foldername'];


                        $keywords_all = '';
                        if (!empty($_POST['keywords'])) {
                            $keywords = sqi($_POST['keywords']);
                            foreach ($keywords as $key => $value) {
                                if (!empty($value)) {
                                    $keywords_all .= $value . ',';
                                }
                            }
                        }


                        for ($i = 1; $i <= $language_count; $i++) {
                            $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                            $subtitle1_[$i] = addslashes($_POST['subtitle1_' . $language[$i]]);
                            $subtitle2_[$i] = addslashes($_POST['subtitle2_' . $language[$i]]);
                            $text_[$i] = addslashes($_POST['text_' . $language[$i]]);
                            $shortcontent_[$i] = addslashes($_POST['shortcontent_' . $language[$i]]);

                            $images_name = '';
                            if (!$images_name) {
                                for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                    $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                    if (!empty($img_[$i_image])) {
                                        $read_file_name = explode(".", $img_[$i_image]);
                                        $extn = end($read_file_name);
                                        $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;
                                        if ($extn == 'DOCX' || $extn == 'docx' || $extn == 'PPTX' || $extn == 'pptx' || $extn == 'mp3' || $extn == 'MP3' || $extn == 'mp4' || $extn == 'MP4' || $extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'DOC' || $extn == 'doc' || $extn == 'zip' || $extn == 'ZIP') {
                                            $images_name .= $img_[$i_image] . ',';
                                            move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "../upload/course/" . $img_[$i_image]);
                                        } else {
                                            $images_name .= ',';
                                        }
                                    } else {
                                        if (!empty($_POST['ftplink_' . $i_image])) {
                                            $images_name .= $_POST['ftplink_' . $i_image] . ',';
                                        } else {
                                            $images_name .= ',';
                                        }
                                    }
                                }
                            }

                            $language_record = $language[$i];
                            $admin = (!empty($_POST['owner']) ? $_POST['owner'] : $_SESSION['id_login']);
                            $adminadddate = jdate("Y/m/d : a:h:s");
                            Query("INSERT INTO tbl_mohtava (
                                        tbl_mohtava_code,
                                        tbl_mohtava_lng,
                                        tbl_mohtava_title,
                                        tbl_mohtava_subtitle1,
                                        tbl_mohtava_subtitle2,
                                        tbl_mohtava_text,
                                        tbl_mohtava_shortcontent,
                                        tbl_mohtava_ghaleb,
                                        tbl_mohtava_link,
                                        tbl_mohtava_refrence,
                                        tbl_mohtava_page,
                                        tbl_mohtava_time,
                                        tbl_mohtava_creator,
                                        tbl_mohtava_enteshar,
                                        tbl_mohtava_statustext,
                                        tbl_mohtava_dateadd,
                                        tbl_mohtava_price,
                                        tbl_mohtava_codehazine,
                                        tbl_mohtava_sellprice,
                                        tbl_mohtava_percentmalek,
                                        tbl_mohtava_percentdepartement,
                                        tbl_mohtava_percentacademy,
                                        tbl_mohtava_pricedate,
                                        tbl_mohtava_dashboardshow,
                                        tbl_mohtava_datehide,
                                        tbl_mohtava_dateend,
                                        tbl_mohtava_foldername,
                                        tbl_mohtava_keywords,
                                        tbl_mohtava_department,
                                        tbl_mohtava_admin,
                                        tbl_mohtava_adminadddate,
                                        tbl_mohtava_file,
                                        tbl_mohtava_delete,
                                        tbl_mohtava_special,
                                        tbl_mohtava_producer,
                                        tbl_mohtava_moalef,
                                        tbl_mohtava_motarjem,
                                        tbl_mohtava_departmentcode,
                                        tbl_mohtava_ghalebcode,
                                        tbl_mohtava_view
                                        ) 
                                        values('$code',
                                        '$language_record',
                                        '$title_[$i]',
                                        '$subtitle1_[$i]',
                                        '$subtitle2_[$i]',
                                        '$text_[$i]',
                                        '$shortcontent_[$i]',
                                        '$ghaleb',
                                        '$link',
                                        '$refrence',
                                        '$page',
                                        '$time',
                                        '$creator',
                                        '$enteshar',
                                        '$statustext',
                                        '$dateadd',
                                        '$price',
                                        '$codehazine',
                                        '$sellprice',
                                        '$percentmalek',
                                        '$percentdepartement',
                                        '$percentacademy',
                                        '$pricedate',
                                        '$dashboardshow',
                                        '$datehide',
                                        '$dateend',
                                        '$foldername',
                                        '$keywords_all',
                                        '$department',
                                        '$admin',
                                        '$adminadddate',
                                        '$images_name',
                                        '0',
                                        '0',
                                        '$producer',
                                        '$moalef',
                                        '$motarjem',
                                        '$departmentCode',
                                        '$ghalebCode',
                                        '0')");
                        }
                        $_SESSION['error'] = $lang[32];
                        header("Location: mohtava.php");
                    }
                }

                if (isset($do) && $do == 'delete') {


                    if ($access == '1') {
                        $updateRule = " ";
                    } else {
                        $updateRule = " tbl_mohtava_admin='$this_user_id' and ";
                    }

                    $result = Query("SELECT * from tbl_mohtava  Where $updateRule tbl_mohtava_code = '$code' and tbl_mohtava_lng = '$language_default' ");
                    $read = mysqli_fetch_array($result);
                    echo '<div class="col-lg-8 col-12 p-3"><p>' . $lang[25] . '</p><h3>' . $read['tbl_mohtava_title'] . '</h3>
                         <form action=" " method="POST">
                         <div class="form-group margin-top">';


                    echo  '<input type="hidden" value="' . $code . '" name="code" >
                            <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                            <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
                    if (isset($_POST['submit'])) {
                        $code = $_POST['code'];

                        Query("update tbl_mohtava set tbl_mohtava_delete='1' Where $updateRule tbl_mohtava_code = '$code' ");
                        $_SESSION['error'] = $lang[28];
                        header("Location: mohtava.php");
                        exit();
                    } elseif (isset($_POST['nodelete'])) {
                        header("Location: mohtava.php");
                        exit();
                    }
                }

                if (isset($do) && $do == 'edit') {

                    function read_fild($language_id = NULL, $field = NULL)
                    {
                        global $this_user_id, $access;
                        if ($language_id == NULL) {
                            $check_language_id = "";
                        } else {
                            $check_language_id = "and tbl_mohtava_lng = '$language_id'";
                        }
                        $code = $_GET['code'];
                        if ($access == '1') {
                            $result = Query("SELECT * FROM tbl_mohtava Where tbl_mohtava_code =  '$code'  $check_language_id ");
                        } else {
                            $result = Query("SELECT * FROM tbl_mohtava Where tbl_mohtava_admin='$this_user_id' and tbl_mohtava_code =  '$code'  $check_language_id ");
                        }

                        $read = mysqli_fetch_array($result);
                        $read = $read[$field];
                        return $read;
                    }

                    echo '<div class="col-lg-10 col-12 p-3">
                          <form action="?do=edititem" method="POST" enctype="multipart/form-data" >
                          <input type="hidden" name="code" value="' . $code . '" >';
                    ?>
                <!-- <div class="form-group">
                          <label><?= $lang[1831] ?><span class="required">*</span><div class="checkcodefileresult"></div></label>
                          <input type="text" name="codefile" class="form-control checkcodefile checkname" data-code="<?= $code ?>" value="<?= read_fild(NULL, 'tbl_mohtava_codefile') ?>" required>
                      </div> -->

                <?php
                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                                    <label>' . $lang[1856] . '<span class="required">*</span><span class="language">' . $language[$i] . '</span></label>
                                    <input type="text" name="title_' . $language[$i] . '" class="form-control" value="' . read_fild($language[$i], 'tbl_mohtava_title') . '" ' . ($i == '1' ? 'required' : '') . '>
                                    </div>';
                    }
                    ?>

                <?php
                    echo '<div class="form-group">
                                  <label>' . $lang[1832] . '<span class="required">*</span></label><div>
                                  <select name="ghaleb"  data-placeholder="' . $lang[1832] . '" style="width:100%;" class="select2" required>
                                  <option value="">' . $lang[70] . '</option>';
                    for ($i = 1857; $i <= 1873; $i++) {
                        $thiscode = explode("-", $lang[$i]);
                        echo '<option value="' . $i . '-' . str_replace("کد", "", $thiscode[1]) . '" class="group-cat" ' . (read_fild(NULL, 'tbl_mohtava_ghaleb') == $i ? 'selected' : '') . '>' . $lang[$i] . '</option>';
                    }

                    echo '</select></div></div><hr>';

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                                        <label>' . $lang[1833] . '<span class="required">*</span><span class="language">' . $language[$i] . '</span></label>
                                        <input type="text" name="subtitle1_' . $language[$i] . '" class="form-control" value="' . read_fild($language[$i], 'tbl_mohtava_subtitle1') . '" ' . ($i == '1' ? 'required' : '') . '>
                                        </div>';
                    }

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                                        <label>' . $lang[1834] . '<span class="language">' . $language[$i] . '</span></label>
                                        <input type="text" name="subtitle2_' . $language[$i] . '" class="form-control" value="' . read_fild($language[$i], 'tbl_mohtava_subtitle2') . '">
                                        </div>';
                    }
                    echo '<div class="form-group">
                                  <label>' . $lang[1838] . '<span class="required">*</span></label><div>
                                  <select name="department"  data-placeholder="' . $lang[1838] . '" style="width:100%;" class="select2"  required>
                                  <option value="">' . $lang[70] . '</option>';
                    $resultKey = Query("SELECT * From tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                    while ($readKey = mysqli_fetch_array($resultKey)) {

                        if ($access == '8') {
                            echo (strpos($accessBakhsh, $readKey['tbl_categories_code']) !== false ? '<option value="' . $readKey['tbl_categories_code'] . '-' . $readKey['tbl_categories_catcode'] . '" class="group-cat" ' . (strpos(read_fild(NULL, 'tbl_mohtava_department'), $readKey['tbl_categories_code']) !== false ? 'selected' : '') . '>' . $readKey['tbl_categories_title'] . '</option>' : '');
                        } else {
                            echo '<option value="' . $readKey['tbl_categories_code'] . '-' . $readKey['tbl_categories_catcode'] . '" class="group-cat" ' . (strpos(read_fild(NULL, 'tbl_mohtava_department'), $readKey['tbl_categories_code']) !== false ? 'selected' : '') . '>' . $readKey['tbl_categories_title'] . '</option>';
                        }
                    }
                    echo '</select></div></div><hr>';

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                    <label>' . $lang[2067] . '<span class="language">' . $language[$i] . '</span></label>
                    <textarea name="shortcontent_' . $language[$i] . '" class="form-control">' . read_fild($language[$i], 'tbl_mohtava_shortcontent') . '</textarea>
                    </div>';
                    }

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                                          <label>' . $lang[1890] . '<span class="language">' . $language[$i] . '</span></label>
                                          <textarea name="text_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . read_fild($language[$i], 'tbl_mohtava_text') . '</textarea>
                                          </div>';
                    }

                    echo '<div class="form-group">
                                  <label>' . $lang[1835] . '</label><div>
                                  <select name="keywords[]"  data-placeholder="' . $lang[1835] . '" style="width:100%;" class="selectmulti" multiple>
                                  <option value="0">' . $lang[70] . '</option>';
                    $resultKey = Query("SELECT * From tbl_content Where tbl_content_type='21' and tbl_content_fld1='$language_default' ORDER BY tbl_content_id DESC ");
                    while ($readKey = mysqli_fetch_array($resultKey)) {
                        echo '<option value="' . $readKey['tbl_content_code'] . '" class="group-cat" ' . (strpos(read_fild(NULL, 'tbl_mohtava_keywords'), $readKey['tbl_content_code']) !== false ? 'selected' : '') . '>' . $readKey['tbl_content_title'] . '</option>';
                    }
                    echo '</select></div></div><hr>';
                    ?>

                <div class="form-group">
                    <label><?= $lang[1837] ?><span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1964] ?>" data-bs-html="true">
                            <elm class="bi-patch-question-fill"></elm>
                        </span></label>
                    <input type="text" name="refrence" class="form-control" value="<?= read_fild(NULL, 'tbl_mohtava_refrence') ?>">
                </div>

                <div class="form-group">
                    <label><?= $lang[1836] ?></label>
                    <input type="text" name="link" class="form-control" value="<?= read_fild(NULL, 'tbl_mohtava_link') ?>">
                </div>


                <div class="form-group">
                    <label><?= $lang[1839] ?></label>
                    <input type="text" name="page" class="form-control" value="<?= read_fild(NULL, 'tbl_mohtava_page') ?>">
                </div>
                <div class="form-group">
                    <label><?= $lang[1840] ?></label>
                    <input type="text" name="time" class="form-control" value="<?= read_fild(NULL, 'tbl_mohtava_time') ?>">
                </div>
                <div class="form-group">
                    <label><?= $lang[1841] ?></label>
                    <input type="text" name="moalef" class="form-control" value="<?= read_fild(NULL, 'tbl_mohtava_moalef') ?>">
                </div>
                <div class="form-group">
                    <label><?= $lang[1959] ?></label>
                    <input type="text" name="motarjem" class="form-control" value="<?= read_fild(NULL, 'tbl_mohtava_motarjem') ?>">
                </div>
                <?php


                    echo '<div class="form-group">
                          <label>' . $lang[1948] . '</label><div>
                          <select name="creator"  data-placeholder="' . $lang[1948] . '" style="width:100%;" class="select2" >
                          <option value="0">' . $lang[70] . '</option>';
                    $resultKey = Query("SELECT * From tbl_user Where tbl_user_contentcreator='1' and tbl_user_bakhsh='1' ORDER BY tbl_user_id DESC ");
                    while ($readKey = mysqli_fetch_array($resultKey)) {
                        echo '<option value="' . $readKey['tbl_user_id'] . '" class="group-cat" ' . (read_fild(NULL, 'tbl_mohtava_creator') == $readKey['tbl_user_id'] ? 'selected' : '') . '>' . $readKey['tbl_user_name'] . '</option>';
                    }
                    echo '</select></div></div><hr>';
                    echo '<div class="form-group">
                        <label>' . $lang[1951] . '</label><div>
                        <select name="producer"  data-placeholder="' . $lang[1951] . '" style="width:100%;" class="select2" >
                        <option value="0">' . $lang[70] . '</option>';
                    $resultKey = Query("SELECT * From tbl_user Where tbl_user_contentcreator='1' and tbl_user_bakhsh='2' ORDER BY tbl_user_id DESC ");
                    while ($readKey = mysqli_fetch_array($resultKey)) {
                        echo '<option value="' . $readKey['tbl_user_id'] . '" class="group-cat" ' . (read_fild(NULL, 'tbl_mohtava_producer') == $readKey['tbl_user_id'] ? 'selected' : '') . '>' . $readKey['tbl_user_name'] . '</option>';
                    }
                    echo '</select></div></div>';
                    if ($access == '1') {
                    ?>
                <div class="form-group">
                    <label><?= $lang[1842] ?><span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1963] ?>" data-bs-html="true">
                            <elm class="bi-patch-question-fill"></elm>
                        </span></label>
                    <input type="text" name="enteshar" class="form-control datepicker" value="<?= read_fild(NULL, 'tbl_mohtava_enteshar') ?>">
                </div>
                <?php

                        echo '<div class="form-group">
                        <label>' . $lang[1843] . '</label><div>
                        <select name="statustext"  data-placeholder="' . $lang[1843] . '" style="width:100%;" class="select2" required>
                        <option value="0">' . $lang[70] . '</option>';
                        for ($i = 0; $i <= 4; $i++) {
                            echo '<option value="' . $i . '" class="group-cat"  ' . (read_fild(NULL, 'tbl_mohtava_statustext') == $i ? 'selected' : '') . '>' . $lang[$i + 1908] . '</option>';
                        }
                        echo '</select></div></div><hr>';

                        ?>
                <div class="form-group">
                    <label><?= $lang[1844] ?></label>
                    <input type="text" name="dateadd" class="form-control datepicker" value="<?= read_fild(NULL, 'tbl_mohtava_dateadd') ?>">
                </div>
                <div class="form-group">
                    <label><?= $lang[1846] ?></label>
                    <input type="text" name="price" class="form-control " value="<?= read_fild(NULL, 'tbl_mohtava_price') ?>">
                </div>
                <div class="form-group">
                    <label><?= $lang[1847] ?></label>
                    <input type="text" name="codehazine" class="form-control " value="<?= read_fild(NULL, 'tbl_mohtava_codehazine') ?>">
                </div>
                <div class="form-group">
                    <label><?= $lang[1848] ?></label>
                    <input type="text" name="sellprice" class="form-control " value="<?= read_fild(NULL, 'tbl_mohtava_sellprice') ?>">
                </div>
                <div class="form-group">
                    <label><?= $lang[1849] ?></label>
                    <input type="text" name="percentmalek" class="form-control " value="<?= read_fild(NULL, 'tbl_mohtava_percentmalek') ?>">
                </div>
                <div class="form-group">
                    <label><?= $lang[1850] ?></label>
                    <input type="text" name="percentdepartement" class="form-control" value="<?= read_fild(NULL, 'tbl_mohtava_percentdepartement') ?>">
                </div>
                <div class="form-group">
                    <label><?= $lang[1851] ?></label>
                    <input type="text" name="percentacademy" class="form-control " value="<?= read_fild(NULL, 'tbl_mohtava_percentacademy') ?>">
                </div>
                <div class="form-group">
                    <label><?= $lang[1852] ?></label>
                    <input type="text" name="pricedate" class="form-control datepicker" value="<?= read_fild(NULL, 'tbl_mohtava_pricedate') ?>">
                </div>
                <?php

                        echo '<div class="form-group">
                              <label>' . $lang[1853] . '</label><div>
                              <select name="dashboardshow"  data-placeholder="' . $lang[1853] . '" style="width:100%;" class="select2" >
                              <option value="0">' . $lang[70] . '</option>';
                        for ($i = 1891; $i <= 1895; $i++) {
                            echo '<option value="' . $i . '" class="group-cat" ' . (read_fild(NULL, 'tbl_mohtava_dashboardshow') == $i ? 'selected' : '') . '>' . $lang[$i] . '</option>';
                        }
                        echo '</select></div></div><hr>';
                        ?>
                <div class="form-group">
                    <label><?= $lang[1854] ?></label>
                    <input type="text" name="datehide" class="form-control datepicker" value="<?= read_fild(NULL, 'tbl_mohtava_datehide') ?>">
                </div>
                <div class="form-group">
                    <label><?= $lang[1855] ?></label>
                    <input type="text" name="dateend" class="form-control datepicker" value="<?= read_fild(NULL, 'tbl_mohtava_dateend') ?>">
                </div>
                <?php } ?>
                <div class="form-group">
                    <label><?= $lang[1903] ?><span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1960] ?>" data-bs-html="true">
                            <elm class="bi-patch-question-fill"></elm>
                        </span></label>
                    <input type="text" name="foldername" class="form-control" value="<?= read_fild(NULL, 'tbl_mohtava_foldername') ?>">
                </div>
                <?php
                    echo '<input type="hidden" name="allimages" value="' . read_fild(NULL, 'tbl_mohtava_file') . '">';
                    for ($i = 1; $i <= $img_item; $i++) {
                        $exp = explode(",", read_fild(NULL, 'tbl_mohtava_file'));
                        if ($exp[$i - 1]) {
                            echo '<div class="form-group file-margin">
                                        <table>
                                        <tr>
                                            <td><label class="col-sm-12 control-label">' . ($i == '2' ? $lang[1905] . '<span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1961] . '" data-bs-html="true"><elm class="bi-patch-question-fill"></elm></span>' : $lang[1906] . '<span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1961] . '" data-bs-html="true"><elm class="bi-patch-question-fill"></elm></span>') . '</label><input type="file" name="img_' . $i . '" class="form-control"></td></tr>
                                            <tr><td style="padding-top: 33px;"><a href="' . (!empty(read_fild(NULL, 'tbl_mohtava_foldername')) ? '../uploadcourse/' . read_fild(NULL, 'tbl_mohtava_foldername') . '/' : '../upload/course/') . $exp[$i - 1] . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                            <a href="?code=' . $code . '&do=deleteimage&img=' . $exp[$i - 1] . '" ><span class="btn me-1 mb-1 btn-sm btn-danger bi-trash-fill"></span></a></td></tr>
                                            ' . ($i == '1' ? '<tr><td><label class="col-sm-12 control-label">' . $lang[1907] . '<span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1962] . '" data-bs-html="true"><elm class="bi-patch-question-fill"></elm></span></label><input type="hidden" id="ftplink_' . $i . '" value="' . $exp[$i - 1] . '" name="ftplink_' . $i . '" data="ftplink_' . $i . '" class="form-control" ><input class="form-control selectfile" data="ftplink_' . $i . '" type="file"></td></tr>' : '') . '
                                            </table></div>';
                        } else {
                            echo '<div class="form-group file-margin"><table><tr>
                                            <td><label class="col-sm-12 control-label">' . ($i == '2' ? $lang[1905] . '<span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1961] . '" data-bs-html="true"><elm class="bi-patch-question-fill"></elm></span>' : $lang[1906] . '<span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1961] . '" data-bs-html="true"><elm class="bi-patch-question-fill"></elm></span>') . '</label><input type="file" name="img_' . $i . '" class="form-control"></td></tr>
                                            ' . ($i == '1' ? '<tr><td><label class="col-sm-12 control-label">' . $lang[1907] . '<span class="help" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1962] . '" data-bs-html="true"><elm class="bi-patch-question-fill"></elm></span></label><input type="hidden" id="ftplink_' . $i . '" name="ftplink_' . $i . '" data="ftplink_' . $i . '" class="form-control" ><input class="form-control selectfile" data="ftplink_' . $i . '" type="file"></td></tr>' : '') . '
                                            </table></div>';
                        }
                    }
                    echo  '<div class="form-group">
                                  <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3">
                                  </div>
                                  </form></div>';
                }

                if (isset($do) && $do == 'edititem') {
                    if (isset($_POST['updateitem'])) {
                        $code = $_POST['code'];
                        $codefile = @$_POST['codefile'];


                        $ghalebRef = explode("-", $_POST['ghaleb']);
                        $ghaleb = $ghalebRef['0'];
                        $ghalebCode = strtolower($ghalebRef['1']);

                        $departmentRef = explode("-", $_POST['department']);

                        $department = $departmentRef[0];
                        $departmentCode = strtolower($departmentRef[1]);


                        $link = $_POST['link'];
                        $refrence = $_POST['refrence'];
                        $page = $_POST['page'];
                        $time = $_POST['time'];
                        $creator = $_POST['creator'];
                        $enteshar = $_POST['enteshar'];
                        $statustext = (isset($_POST['statustext']) ? $_POST['statustext'] : '0');
                        $dateadd = $_POST['dateadd'];
                        $price = $_POST['price'];
                        $codehazine = $_POST['codehazine'];
                        $sellprice = $_POST['sellprice'];
                        $percentmalek = (!empty($_POST['percentmalek']) ? $_POST['percentmalek'] : '0');
                        $percentdepartement = (!empty($_POST['percentdepartement']) ? $_POST['percentdepartement'] : '0');
                        $percentacademy = (!empty($_POST['percentacademy']) ? $_POST['percentacademy'] : '0');
                        $pricedate = $_POST['pricedate'];
                        $dashboardshow = $_POST['dashboardshow'];
                        $datehide = $_POST['datehide'];
                        $dateend = $_POST['dateend'];
                        $foldername = $_POST['foldername'];
                        $moalef = $_POST['moalef'];
                        $motarjem = $_POST['motarjem'];
                        $producer = $_POST['producer'];
                        $keywords = @$_POST['keywords'];
                        $keywords_all = '';
                        if (!empty($keywords)) {
                            foreach ($keywords as $key => $value) {
                                if (!empty($value)) {
                                    $keywords_all .= $value . ',';
                                }
                            }
                        }


                        if ($statustext == '4') {
                            $resultCheck = Query("select tbl_mohtava_codefile from tbl_mohtava where tbl_mohtava_code= '$code' ");
                            $readCheck = mysqli_fetch_array($resultCheck);
                            if (empty($readCheck['tbl_mohtava_codefile'])) {
                                $resultCode = Query("select tbl_mohtava_codefile from tbl_mohtava where tbl_mohtava_codefile !='' and tbl_mohtava_departmentcode='$departmentCode' and tbl_mohtava_ghalebcode='$ghalebCode' and tbl_mohtava_lng='fa' order by tbl_mohtava_id DESC  limit 0,1");
                                $readCode = mysqli_fetch_array($resultCode);
                                $lastcode = explode("-", $readCode['tbl_mohtava_codefile']);
                                $thiscode = $departmentCode . '-' . $ghalebCode . '-' . ($lastcode[2] + 1);
                            } else {
                                $thiscode = $readCheck['tbl_mohtava_codefile'];
                            }
                        } else {

                            $resultCheck = Query("select tbl_mohtava_codefile from tbl_mohtava where tbl_mohtava_code= '$code' ");
                            $readCheck = mysqli_fetch_array($resultCheck);
                            if (empty($readCheck['tbl_mohtava_codefile'])) {
                                $thiscode = '';
                            } else {
                                $thiscode = $readCheck['tbl_mohtava_codefile'];
                            }
                        }


                        $images_name = '';
                        $allimages = $_POST['allimages'];
                        $read_allimages = explode(",", $allimages);
                        if (!$images_name) {
                            for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                if (!empty($img_[$i_image])) {
                                    if ($img_[$i_image] == $read_allimages[$i_image - 1]) {
                                        $images_name .= $read_allimages[$i_image - 1] . ',';
                                    } else {
                                        // remove old image and replace with new
                                        $read_file_name = explode(".", $img_[$i_image]);
                                        $extn = end($read_file_name);
                                        $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;
                                        // $images_name = all images after rename array
                                        if ($extn == 'DOCX' || $extn == 'docx' || $extn == 'PPTX' || $extn == 'pptx' || $extn == 'mp3' || $extn == 'MP3' || $extn == 'mp4' || $extn == 'MP4' || $extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'DOC' || $extn == 'doc' || $extn == 'zip' || $extn == 'ZIP') {
                                            $images_name .= $img_[$i_image] . ',';
                                            move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "../upload/course/" . $img_[$i_image]);
                                            @unlink('../upload/course/' . $read_allimages[$i_image - 1]);
                                        } else {
                                            $images_name .= $read_allimages[$i_image - 1] . ',';
                                        }
                                    }
                                } else {
                                    if (!empty($_POST['ftplink_' . $i_image])) {
                                        $images_name .= $_POST['ftplink_' . $i_image] . ',';
                                    } else {
                                        $images_name .= $read_allimages[$i_image - 1] . ',';
                                    }
                                }
                            }
                        }


                        if ($access == '1') {
                            $updateRule = " ";
                        } else {
                            $updateRule = " tbl_mohtava_admin='$this_user_id' and ";
                        }

                        for ($i = 1; $i <= $language_count; $i++) {
                            $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                            $subtitle1_[$i] = addslashes($_POST['subtitle1_' . $language[$i]]);
                            $subtitle2_[$i] = addslashes($_POST['subtitle2_' . $language[$i]]);
                            $text_[$i] = addslashes($_POST['text_' . $language[$i]]);
                            $shortcontent_[$i] = addslashes($_POST['shortcontent_' . $language[$i]]);

                            $language_record = $language[$i];
                            $admin = $_SESSION['id_login'];
                            $adminadddate = jdate("Y/m/d : a:h:s");
                            Query("Update tbl_mohtava SET 
                                        tbl_mohtava_title='$title_[$i]',
                                        tbl_mohtava_subtitle1='$subtitle1_[$i]',
                                        tbl_mohtava_subtitle2='$subtitle2_[$i]',
                                        tbl_mohtava_text='$text_[$i]',
                                        tbl_mohtava_shortcontent='$shortcontent_[$i]',
                                        tbl_mohtava_codefile='$thiscode',
                                        tbl_mohtava_ghaleb='$ghaleb',
                                        tbl_mohtava_link='$link',
                                        tbl_mohtava_refrence='$refrence',
                                        tbl_mohtava_page='$page',
                                        tbl_mohtava_time='$time',
                                        tbl_mohtava_creator='$creator',
                                        tbl_mohtava_enteshar='$enteshar',
                                        tbl_mohtava_statustext='$statustext',
                                        tbl_mohtava_dateadd='$dateadd',
                                        tbl_mohtava_price='$price',
                                        tbl_mohtava_codehazine='$codehazine',
                                        tbl_mohtava_sellprice='$sellprice',
                                        tbl_mohtava_percentmalek='$percentmalek',
                                        tbl_mohtava_percentdepartement='$percentdepartement',
                                        tbl_mohtava_percentacademy='$percentacademy',
                                        tbl_mohtava_pricedate='$pricedate',
                                        tbl_mohtava_dashboardshow='$dashboardshow',
                                        tbl_mohtava_datehide='$datehide',
                                        tbl_mohtava_dateend='$dateend',
                                        tbl_mohtava_foldername='$foldername',
                                        tbl_mohtava_keywords='$keywords_all',
                                        tbl_mohtava_department='$department',
                                        tbl_mohtava_file='$images_name',
                                        tbl_mohtava_newadmin='$admin',
                                        tbl_mohtava_admineditdate='$adminadddate',
                                        tbl_mohtava_moalef='$moalef',
                                        tbl_mohtava_motarjem ='$motarjem',
                                        tbl_mohtava_producer='$producer',
                                        tbl_mohtava_departmentcode='$departmentCode',
                                        tbl_mohtava_ghalebcode='$ghalebCode'
                                        Where $updateRule tbl_mohtava_code= '$code' and tbl_mohtava_lng = '$language_record' ");
                        }
                        $_SESSION['error'] = $lang[21];
                        header("Location: mohtava.php");
                    }
                }


                if (isset($do) && $do == 'deleteimage') {
                    if ($access == 2) {
                        $updateRule = " tbl_mohtava_admin='$this_user_id' and  ";
                    } else {
                        $updateRule = '';
                    }

                    $result = Query("SELECT * from tbl_mohtava Where $updateRule  tbl_mohtava_code = '$code' ");
                    $read = mysqli_fetch_array($result);

                    echo '<div class="col-lg-8 col-12 p-3"><h3>' . $lang[25] . '</h3><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="img" value="' . $img . '" >
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="submit" name="submit" value="' . $lang[26] . '"  class="btn btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
                    if (isset($_POST['submit'])) {
                        $img = $_POST['img'];
                        $code = $_POST['code'];
                        $record = str_replace("$img", "", $read['tbl_mohtava_file']);
                        $external_file = $directory . $img;
                        unlink("$external_file");
                        Query("Update tbl_mohtava SET tbl_mohtava_file = '$record' Where $updateRule tbl_mohtava_code = '$code' ");
                        header("Location: mohtava.php?code=$code&do=edit");
                    } elseif (isset($_POST['nodelete'])) {

                        header("Location: mohtava.php?code=$code&do=edit");
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