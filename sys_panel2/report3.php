<?php
// include
include './includer.php';
$show_per_page = '20';
$Tabpage = '901';
$pagetitle = $lang[2000];
$thisaccess = '1';
include './header.inc.php';
// end include 

$select = '';
if (!empty($_GET['status'])) {
    
    $select .= " tbl_mohtava_statustext='$_GET[status]' and  ";
}

if($access == '8')
{
    $h = 1;
    $backhsh = explode(",",$accessBakhsh);
    foreach ($backhsh as $key => $value) {
        if(!empty($value))
        {
            if($h == 1)
            {
                $selectfinal .= "("; 
            }
            $selectfinal .= " tbl_mohtava_department like '%$value%' or ";
            $h++;
        }
    }
    if($h != 1)
    {
        $selectfinal = rtrim($selectfinal," or ");
        $select .=$selectfinal. ") and "; 
    }
}
// if($access == '8')
// {
//     $select .= " tbl_mohtava_admin='$this_user_id' and  ";
// }

if(!empty($_GET['mohatavafile']))
{
    $mohatavafile = sqi($_GET['mohatavafile']);
    $select .= " tbl_mohtava_codefile like '%$mohatavafile%' and  ";
}

if(!empty($_GET['subtitle']))
{
    $subtitle = sqi($_GET['subtitle']);
    $select .= " tbl_mohtava_subtitle1 like '%$subtitle%' and  ";
}




if(!empty($_GET['mohatavatitle']) && $_GET['mohatavatitle'] !='0')
{
    $mohatavatitle = sqi($_GET['mohatavatitle']);
    $select .= " tbl_mohtava_title like '%$mohatavatitle%' and  ";
}


if(!empty($_GET['mohatavacreator']) && $_GET['mohatavacreator'] !='0')
{
    $mohatavacreator = sqi($_GET['mohatavacreator']);
    $select .= " tbl_mohtava_admin = '$mohatavacreator' and  ";
}

if(!empty($_GET['malek']) && $_GET['malek'] !='0')
{
    $mohatavacreator = sqi($_GET['malek']);
    $select .= " tbl_mohtava_producer like '%$mohatavacreator%' and  ";
}

if(!empty($_GET['department']) && $_GET['department'] !='0')
{
    $department = sqi($_GET['department']);
    $select .= " tbl_mohtava_department = '$department' and  ";
}

if(!empty($_GET['producer']) && $_GET['producer'] !='0')
{
    $producer = sqi($_GET['producer']);
    $select .= " tbl_mohtava_producer = '$producer' and  ";
}

$selectRegisterMohtava = '';

$selectPoint = "" ;
if(!empty($_GET['enddate']))
{
    $enddate = strip_tags($_GET['enddate']);
    if(empty($_GET['startdate']))
    {
        $jdate = jdate("Y/m/d");
        $selectRegisterMohtava = " and tbl_mohtavaregister_dateen between '$jdate' and '$enddate' ";
        $selectPoint = " and dateend between '$jdate' and '$enddate' " ;
    }
    else {
        $startdate = strip_tags($_GET['startdate']);
        $selectRegisterMohtava = " and tbl_mohtavaregister_dateen between '$startdate' and '$enddate' ";
        $selectPoint = " and dateend between '$startdate' and '$enddate' " ;
    }
}
if(!empty($_GET['startdate']))
{
    $startdate = strip_tags($_GET['startdate']);
    if(empty($_GET['enddate']))
    {
        $jdate = jdate("Y/m/d");
        $selectRegisterMohtava = " and tbl_mohtavaregister_dateen between '$startdate' and '$jdate' ";
        $selectPoint = " and dateend between '$startdate' and '$jdate' " ;
    }
    else {
        $enddate = strip_tags($_GET['enddate']);
        $selectRegisterMohtava = " and tbl_mohtavaregister_dateen between '$startdate' and '$enddate' ";
        $selectPoint = " and dateend between '$startdate' and '$enddate' " ;
    }
}


?>
<div class="row gx-3 mb-3 ">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?php
                            
                            if(empty($do) )
                            {
                                echo $pagetitle;
                            }
                            elseif(!empty($do) && ($do !='buy' || $do !='plan')) {
                                $resultMohtava =  Query("SELECT * From tbl_mohtava Where tbl_mohtava_code='$code' and tbl_mohtava_lng='$language_default'");
                                $readMohtava = mysqli_fetch_array($resultMohtava);
                                echo $readMohtava['tbl_mohtava_title'];
                            }
                            else {
                                echo $pagetitle;
                            }
                            ?></h4>

                        </div>
                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <div class="card-body p-0 position-relative">
                <?php
                    if (!isset($do)) { ?>
                <table>
                    <form action="?do=search" method="POST" class="search-form ">
                        <div class="container-fulid p-3">
                            <div class="row">
                                <div class="col-12 col-lg-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control width-full" value="<?=(!empty($_GET['mohatavafile']) ? $_GET['mohatavafile'] : '') ?>" name="mohatavafile" placeholder="<?=$lang[1831]?>">
                                    </div>
                                </div>
                                <div class="col-12 col-lg-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control width-full" value="<?=(!empty($_GET['subtitle']) ? $_GET['subtitle'] : '') ?>" name="subtitle" placeholder="<?=$lang[854]?>">
                                    </div>
                                </div>
                                <div class="col-12 col-lg-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control width-full" value="<?=(!empty($_GET['mohatavatitle']) ? $_GET['mohatavatitle'] : '') ?>" name="mohatavatitle" placeholder="<?=$lang[1334]?>">
                                    </div>
                                </div>
                                <div class="col-12 col-lg-2">
                                    <div class="form-group">
                                        <select name="mohatavacreator" data-placeholder="<?= $lang[1845] ?>" class="select2 width-full" placeholder="<?=$lang[1845]?>">
                                            <option value=""><?= $lang[1845] ?></option>
                                            <option value="0" <?=(!empty($_GET['mohatavacreator']) && $_GET['mohatavacreator'] == '0' ? 'selected' : '') ?>><?= $lang[1997] ?></option>
                                            <?php
                                                    $resultKey = Query("SELECT * From tbl_user Where tbl_user_delete='0' and tbl_user_role like '%8%' ");
                                                    while ($readKey = mysqli_fetch_array($resultKey)) {
                                                            echo '<option value="' . $readKey['tbl_user_id'] . '" class="group-cat" '.(!empty($_GET['mohatavacreator']) && $_GET['mohatavacreator'] == $readKey['tbl_user_id'] ? 'selected' : '') .'>' . $readKey['tbl_user_name'] . '</option>';
                                                    }
                                                    ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-2">
                                    <div class="form-group">
                                        <select name="malek" data-placeholder="<?= $lang[1948] ?>" class="select2 width-full" placeholder="<?=$lang[1948]?>">
                                            <option value=""><?= $lang[1948] ?></option>
                                            <option value="0" <?=(!empty($_GET['malek']) && $_GET['malek'] == '0' ? 'selected' : '') ?>><?= $lang[1997] ?></option>
                                            <?php
                                                    $resultKey = Query("SELECT * From tbl_user Where tbl_user_delete='0' and tbl_user_contentcreator='1' and tbl_user_bakhsh='1' ORDER BY tbl_user_id DESC ");
                                                    while ($readKey = mysqli_fetch_array($resultKey)) {
                                                            echo '<option value="' . $readKey['tbl_user_id'] . '" class="group-cat" '.(!empty($_GET['malek']) && $_GET['malek'] == $readKey['tbl_user_id'] ? 'selected' : '') .'>' . $readKey['tbl_user_name'] . '</option>';
                                                    }
                                                    ?>

                                        </select>
                                    </div>
                                </div>


                                <div class="col-12 col-lg-2">
                                    <div class="form-group">
                                        <select name="producer" data-placeholder="<?= $lang[1951] ?>" class="select2 width-full" placeholder="<?=$lang[1951]?>">
                                            <option value=""><?= $lang[1951] ?></option>
                                            <option value="0" <?=(!empty($_GET['producer']) && $_GET['producer'] == '0' ? 'selected' : '') ?>><?= $lang[1997] ?></option>
                                            <?php
                                                    
                                                    $resultKey = Query("SELECT * From tbl_user Where tbl_user_delete='0' and tbl_user_contentcreator='1' and tbl_user_bakhsh='2' ORDER BY tbl_user_id DESC ");
                                                    while ($readKey = mysqli_fetch_array($resultKey)) {
                                                            echo '<option value="' . $readKey['tbl_user_id'] . '" class="group-cat" '.(!empty($_GET['producer']) && $_GET['producer'] == $readKey['tbl_user_id'] ? 'selected' : '') .'>' . $readKey['tbl_user_name'] . '</option>';
                                                    }
                                                    ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-2">
                                    <div class="form-group">
                                        <select name="status" data-placeholder="<?= $lang[1843] ?>" class="select2 width-full" placeholder="<?=$lang[1843]?>">
                                            <option value=""><?= $lang[1843] ?></option>
                                            <option value="0" <?=(!empty($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '') ?>><?= $lang[1997] ?></option>
                                            <?php
                                                    for ($i=0; $i <=4 ; $i++) { 
                                                        echo '<option value="' . $i . '" class="group-cat"  '.(!empty($_GET['status']) && $_GET['status'] == $i? 'selected' : '') .'>' . $lang[$i+1908] . '</option>';
                                                    }
                                                    ?>

                                        </select>
                                    </div>
                                </div>


                                <div class="col-12 col-lg-2">
                                    <div class="form-group">
                                        <select name="department" data-placeholder="<?= $lang[1838] ?>" style="width:100%;" class="select2" placeholder="<?=$lang[1838]?>">
                                            <option value=""><?= $lang[1838] ?></option>
                                            <option value="0" <?=(!empty($_GET['department']) && $_GET['department'] == '0' ? 'selected' : '') ?>><?= $lang[1997] ?></option>
                                            <?php
                                                $resultKey = Query("SELECT * From tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                                while ($readKey = mysqli_fetch_array($resultKey)) {
                                                    
                                                        echo '<option value="' . $readKey['tbl_categories_code'] . '" class="group-cat" '.(!empty($_GET['department']) && $_GET['department'] == $readKey['tbl_categories_code']? 'selected' : '') .'>' . $readKey['tbl_categories_title'] . '</option>';
                                                }
                                                ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control datepicker" value="<?=(!empty($_GET['startdate']) ? $_GET['startdate'] : '') ?>" name="startdate" placeholder="<?=$lang[2035]?>">
                                    </div>
                                </div>
                                <div class="col-12 col-lg-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control datepicker" value="<?=(!empty($_GET['enddate']) ? $_GET['enddate'] : '') ?>" name="enddate" placeholder="<?=$lang[2036]?>">
                                    </div>
                                </div>


                                <div class="col-12 col-lg-2 pb-3">
                                    <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>

                                    <a href="csv.php?do=report3<?=(!empty($_GET['mohatavafile']) ? '&mohatavafile='.$_GET['mohatavafile'] : '').
                                            (!empty($_GET['mohatavatitle']) ? '&mohatavatitle='.$_GET['mohatavatitle'] : '').
                                            (!empty($_GET['mohatavacreator']) ? '&mohatavacreator='.$_GET['mohatavacreator'] : '').
                                            (!empty($_GET['department']) ? '&department='.$_GET['department'] : '').
                                            (!empty($_GET['status']) ? '&status='.$_GET['status'] : '').
                                            (!empty($_GET['subtitle']) ? '&subtitle='.$_GET['subtitle'] : '').
                                            (!empty($_GET['malek']) ? '&malek='.$_GET['malek'] : '').
                                            (!empty($_GET['startdate']) ? '&startdate='.$_GET['startdate'] : '').
                                            (!empty($_GET['enddate']) ? '&enddate='.$_GET['enddate'] : '').
                                            (!empty($_GET['producer']) ? '&producer='.$_GET['producer'] : '')
                                            ?>" class="btn  btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[2028] ?>"><span class="bi-file-text-fill"></span></a>
                                </div>
                            </div>
                        </div>
                    </form>
                </table>
                <?php
                        
                        // start count db result for pagination               
                        $result_for_count =  Query("SELECT * From tbl_mohtava Where $select tbl_mohtava_lng='$language_default' and tbl_mohtava_delete='0' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_mohtava Where $select tbl_mohtava_lng='$language_default' and tbl_mohtava_delete='0'  ORDER BY tbl_mohtava_id DESC LIMIT $start_contnet,$show_per_page ");
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
                                    <th>' . $lang[1831] . '</th>
                                    <th>' . $lang[1856] . '</th>
                                    <th>'.$lang[1843].'</th>
                                    <th>' . $lang[1832] . '</th>
                                    <th>' . $lang[1833] . '</th>
                                    <th>' . $lang[1838] . '</th>
                                    <th>' . $lang[2013] . '</th>
                                    <th>' . $lang[2012] . '</th>
                                    <th>' . $lang[2004] . '</th>
                                    <th>' . $lang[2005] . '</th>
                                    <th>' . $lang[2006] . '</th>
                                    <th>' . $lang[2007] . '</th>
                                    <th>' . $lang[2008] . '</th>
                                    <th>' . $lang[2009] . '</th>
                                    <th>' . $lang[2010] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {

                            $department = $read['tbl_mohtava_department'];

                            $resultKey = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$department' and tbl_categories_lng='$language_default' ");
                            $readKey = mysqli_fetch_array($resultKey);
                            $creator = $read['tbl_mohtava_creator'];
                            $resultCreator = Query("SELECT tbl_user_name From tbl_user Where tbl_user_id='$creator' and tbl_user_contentcreator='1' and tbl_user_bakhsh='1' ORDER BY tbl_user_id DESC ");
                            $readCreator = mysqli_fetch_array($resultCreator);
                            $producer = $read['tbl_mohtava_producer'];
                            $resultProducer = Query("SELECT tbl_user_name From tbl_user Where tbl_user_id='$producer'  ");
                            $readProducer = mysqli_fetch_array($resultProducer);

                            $resultCountRegister = Query("Select * from tbl_mohtavaregister where tbl_mohtavaregister_model='0' and tbl_mohtavaregister_mohtava = '$read[tbl_mohtava_code]' $selectRegisterMohtava ");
                            $countresultCountRegister = mysqli_num_rows($resultCountRegister);
                            $resultCountUseInCourse = Query("Select * from tbl_mohtavaregister where tbl_mohtavaregister_model='1' and  tbl_mohtavaregister_mohtava = '$read[tbl_mohtava_code]' $selectRegisterMohtava ");
                            $countresultCountUseInCourse= mysqli_num_rows($resultCountUseInCourse);

                            $resultRegisterLastDate = Query("Select * from tbl_mohtavaregister where tbl_mohtavaregister_mohtava = '$read[tbl_mohtava_code]'  order by tbl_mohtavaregister_id DESC limit 0,1");
                            $readRegisterLastDate = mysqli_fetch_array($resultRegisterLastDate);

                            $resultCourse = Query("Select * from tbl_course where tbl_course_mohtava = '$read[tbl_mohtava_code]' and tbl_course_lng='$language_default'");

                            $resultFilePlan = Query("SELECT * From tbl_plan where tbl_plan_delete='0' and tbl_plan_file='$read[tbl_mohtava_code]' and tbl_plan_lng='$language_default'  ");
                            $countresultFilePlan= mysqli_num_rows($resultFilePlan);
                            $res_rate = Query("SELECT total_points,rating_number, FORMAT((total_points / rating_number),1) as average_rating FROM tbl_rating WHERE post_id = '$read[tbl_mohtava_id]' $selectPoint ");
                            $read_rate = mysqli_fetch_array($res_rate);
                            $status = $read['tbl_mohtava_statustext'];
                            echo '<tr>
                            <td class="align-middle p-2">' . $i . '</td>
                            <td class="align-middle p-2">' . (!empty($read['tbl_mohtava_codefile']) ? strtoupper($read['tbl_mohtava_codefile']) : '' ) . '</td>
                            <td class="align-middle p-2">' . $read['tbl_mohtava_title'] . '</td>
                            <td>'.$lang[$status+1908].'</td>
                            <td class="align-middle p-2">' . (!empty($read['tbl_mohtava_ghaleb']) ? $lang[$read['tbl_mohtava_ghaleb']] : '') . '</td>
                            <td class="align-middle p-2">' . $read['tbl_mohtava_subtitle1'] . '</td>
                            <td class="align-middle p-2">' . @$readKey['tbl_categories_title'] . '</td>
                            <td class="align-middle p-2">'.@$readCreator['tbl_user_name'].'</td>
                            <td class="align-middle p-2">' . @$readProducer['tbl_user_name'] . '</td>
                            <td class="align-middle p-2">' . @$read['tbl_mohtava_enteshar'] . '</td>
                            <td class="align-middle p-2">'.(!empty($read_rate['total_points']) ? round($read_rate['total_points'] / $read_rate['rating_number']) : '0').'</td>
                            <td class="align-middle p-2">' . @$readRegisterLastDate['tbl_mohtavaregister_dateregister'] . '</td>
                            <td class="align-middle p-2">'.$read['tbl_mohtava_view'].'</td>
                            <td class="align-middle p-2">'.toPersianNum($countresultFilePlan+$countresultCountUseInCourse+$countresultCountRegister).'</td>
                            <td class="align-middle p-2 ">'.($countresultCountRegister > 0 ? '<a href="report3.php?do=buy&code='.$read['tbl_mohtava_code'].'&startdate='.($_GET['startdate']).'&enddate='.($_GET['enddate']).'" target="_blank">' : '').toPersianNum($countresultCountRegister).($countresultCountRegister > 0 ? '</a>' : '').'</td>
                            <td class="align-middle p-2">'.($countresultFilePlan+$countresultCountUseInCourse > 0 ? '<a href="report3.php?do=plan&code='.$read['tbl_mohtava_code'].'&startdate='.($_GET['startdate']).'&enddate='.($_GET['enddate']).'" target="_blank">' : '').toPersianNum($countresultFilePlan+$countresultCountUseInCourse).($countresultFilePlan+$countresultCountUseInCourse > 0 ? '</a>' : '').'</td>
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
                                <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[75] ?></a>
                                </li>
                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                </li>
                                <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword  ?>"><?= $lang[74] ?></a>
                                </li>
                            </ul>
                        </td>
                        <td class="col-lg-3">
                            <form action="?do=gotopage" class="search-form" method="POST">
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
                    if (isset($do) && $do == 'buy') {
                        
                      
                        // start count db result for pagination               
                        $result_for_count =  Query("Select * from tbl_mohtavaregister where tbl_mohtavaregister_model='0' and tbl_mohtavaregister_mohtava = '$code' $selectRegisterMohtava ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("Select * from tbl_mohtavaregister where tbl_mohtavaregister_model='0' and tbl_mohtavaregister_mohtava = '$code' $selectRegisterMohtava  ORDER BY tbl_mohtavaregister_id DESC LIMIT $start_contnet,$show_per_page ");
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
                                    <th>'.$lang[2056].'</th>
                                    <th>'.$lang[2034].'</th>
                                    <th>'.$lang[1846].'</th>
                                    <th>'.$lang[1848].'</th>
                                    <th>'.$lang[1849].'</th>
                                    <th>'.$lang[1850].'</th>
                                    <th>'.$lang[1851].'</th>
                                    <th>'.$lang[1852].'</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {

                           $user = $read['tbl_mohtavaregister_user'];
                           $resultMember = Query("select * from tbl_member where tbl_member_id='$user' and tbl_member_delete='0' ");
                           $readMember = mysqli_fetch_array($resultMember);
                            echo '<tr>
                            <td class="align-middle p-2">' . $i . '</td>
                            <td class="align-middle p-2">'.$readMember['tbl_member_fname'].' '.$readMember['tbl_member_lname'].'</td>
                            <td class="align-middle p-2">' . $read['tbl_mohtavaregister_dateregister'] . '</td>
                            <td class="align-middle p-2">' . $read['tbl_mohtavaregister_price'] . '</td>
                            <td class="align-middle p-2">' . @$read['tbl_mohtavaregister_sellprice'] . '</td>
                            <td class="align-middle p-2">' . @$read['tbl_mohtavaregister_percentmalek'] . '</td>
                            <td class="align-middle p-2">' . @$read['tbl_mohtavaregister_percentdepartement'] . '</td>
                            <td class="align-middle p-2">' . @$read['tbl_mohtavaregister_percentacademy'] . '</td>
                            <td class="align-middle p-2">' . @$read['tbl_mohtavaregister_pricedate'] . '</td>
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
                        <td class="col-lg-12">
                            <ul class="pagination">
                                <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword.(!empty($_GET['startdate']) ? '&startdate='.$_GET['startdate'] : '').(!empty($_GET['enddate']) ? '&enddate='.$_GET['enddate'] : '') ?>&do=<?=$do?>&code=<?=$code?>"><?= $lang[75] ?></a>
                                </li>
                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                </li>
                                <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword.(!empty($_GET['startdate']) ? '&startdate='.$_GET['startdate'] : '').(!empty($_GET['enddate']) ? '&enddate='.$_GET['enddate'] : '')  ?>&do=<?=$do?>&code=<?=$code?>"><?= $lang[74] ?></a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
                <?php
                    }
                }

                if (isset($do) && $do == 'plan') {
                        
                      
                    // start count db result for pagination               
                    $result_for_count =  Query("Select * from tbl_mohtavaregister where tbl_mohtavaregister_model='1' and tbl_mohtavaregister_mohtava = '$code' $selectRegisterMohtava ");
                    $count_result = mysqli_num_rows($result_for_count);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    // end count db result for pagination               
                    $result = Query("Select * from tbl_mohtavaregister where tbl_mohtavaregister_model='1' and tbl_mohtavaregister_mohtava = '$code' $selectRegisterMohtava  ORDER BY tbl_mohtavaregister_id DESC LIMIT $start_contnet,$show_per_page ");
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
                                <th>'.$lang[2055].'</th>
                                <th>'.$lang[2056].'</th>
                                <th>'.$lang[2034].'</th>
                                <th>'.$lang[1846].'</th>
                                <th>'.$lang[1848].'</th>
                                <th>'.$lang[1849].'</th>
                                <th>'.$lang[1850].'</th>
                                <th>'.$lang[1851].'</th>
                                <th>'.$lang[1852].'</th>
                            </tr>
                        </thead>
                        <tbody>';
                    $i = 1;
                    while ($read = mysqli_fetch_array($result)) {
                        $contract=$read['tbl_mohtavaregister_contract'];

                    $resultContract = Query("select * from tbl_contract where tbl_contract_id='$contract' ");
                       $readContract = mysqli_fetch_array($resultContract);

                       $user = $read['tbl_mohtavaregister_user'];
                       $resultMember = Query("select * from tbl_member where tbl_member_id='$user' and tbl_member_delete='0' ");
                       $readMember = mysqli_fetch_array($resultMember);
                        echo '<tr>
                        <td class="align-middle p-2">' . $i . '</td>
                        <td class="align-middle p-2">'.$readContract['tbl_contract_title'].'</td>
                        <td class="align-middle p-2">'.$readMember['tbl_member_fname'].' '.$readMember['tbl_member_lname'].'</td>
                        <td class="align-middle p-2">' . $read['tbl_mohtavaregister_dateregister'] . '</td>
                        <td class="align-middle p-2">' . $read['tbl_mohtavaregister_price'] . '</td>
                        <td class="align-middle p-2">' . @$read['tbl_mohtavaregister_sellprice'] . '</td>
                        <td class="align-middle p-2">' . @$read['tbl_mohtavaregister_percentmalek'] . '</td>
                        <td class="align-middle p-2">' . @$read['tbl_mohtavaregister_percentdepartement'] . '</td>
                        <td class="align-middle p-2">' . @$read['tbl_mohtavaregister_percentacademy'] . '</td>
                        <td class="align-middle p-2">' . @$read['tbl_mohtavaregister_pricedate'] . '</td>
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
                        <td class="col-lg-12">
                            <ul class="pagination">
                                <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword.(!empty($_GET['startdate']) ? '&startdate='.$_GET['startdate'] : '').(!empty($_GET['enddate']) ? '&enddate='.$_GET['enddate'] : '') ?>&do=<?=$do?>&code=<?=$code?>"><?= $lang[75] ?></a>
                                </li>
                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                </li>
                                <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword.(!empty($_GET['startdate']) ? '&startdate='.$_GET['startdate'] : '').(!empty($_GET['enddate']) ? '&enddate='.$_GET['enddate'] : '')  ?>&do=<?=$do?>&code=<?=$code?>"><?= $lang[74] ?></a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
                <?php
                }
            }

                              
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            if (isset($_POST['go'])) {
                                $mohatavafile = $_POST['mohatavafile'];
                                $mohatavatitle = $_POST['mohatavatitle'];
                                $mohatavacreator = $_POST['mohatavacreator'];
                                $department = $_POST['department'];
                                $status = $_POST['status'];
                                $subtitle = $_POST['subtitle'];
                                $malek = $_POST['malek'];
                                $startdate = $_POST['startdate'];
                                $enddate = $_POST['enddate'];
                                $producer = $_POST['producer'];
                                header("Location: report3.php?producer=$producer&mohatavafile=$mohatavafile&mohatavatitle=$mohatavatitle&mohatavacreator=$mohatavacreator&status=$status&department=$department&subtitle=$subtitle&malek=$malek&startdate=$startdate&enddate=$enddate");
                            }
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