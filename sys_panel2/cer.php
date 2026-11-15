<?php
// include
include './includer.php';
$show_per_page = '1000';
$pagetitle = $lang[1810];
$Tabpage = '20';
$thisaccess = '1';
include './header.inc.php';

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
                        $result_for_count = Query("SELECT * From tbl_number Where tbl_number_user ='$_GET[history]' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_number Where tbl_number_user ='$_GET[history]' ORDER BY tbl_number_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);

                        // start show list
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>'.$lang[60].'</th>
                                    <th>'.$lang[1737].'</th>
                                    <th>'.$lang[1447].'</th>
                                    <th>'.$lang[1611].'</th>
                                    <th>'.$lang[1440].'</th>
                                    <th>'.$lang[1770].'</th>
                                    <th>'.$lang[865].'</th>
                                    <th>'.$lang[1620].'</th>
                                    <th>'.$lang[63].'</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $mentor = $read['tbl_number_mentor'];

                            $contract = $read['tbl_number_contract'];
                            $cerfile = $read['tbl_number_filecertificate'];
                            $cerfile2 = $read['tbl_number_filecertificate2'];
                            $number = $read['tbl_number_numbercertificate'];
                            $rank = $read['tbl_number_rank'];
                            $skill = $read['tbl_number_skill'];

                            $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$skill' and tbl_categories_lng='$language_default'");
                            $readSkill = mysqli_fetch_array($resultSkill);

                            $resultMember = Query("select * from tbl_member where tbl_member_id='$_GET[history]'  ");
                            $readMember = mysqli_fetch_array($resultMember);

                            $resultMentor = Query("select * from tbl_user where tbl_user_id='$mentor'  ");
                            $readMentor = mysqli_fetch_array($resultMentor);

                            $resultContract = Query("select * from tbl_contract where tbl_contract_id='$contract'  ");
                            $readContract = mysqli_fetch_array($resultContract);

                            echo '<tr>
                                <td class="align-middle p-2">' . $i . '</td>
                                <td class="align-middle p-2"> ' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</td>
                                <td class="align-middle p-2" >' . $readMentor['tbl_user_name'] . '</td>
                                <td class="align-middle p-2" >' . $readContract['tbl_contract_customer'] . '</td>
                                <td class="align-middle p-2" >' . $readContract['tbl_contract_code'] . '</td>
                                <td class="align-middle p-2" >' . $readSkill['tbl_categories_title'] . '</td>
                                <td class="align-middle p-2" >'.$read['tbl_number_datecertificate'].'</td>
                                <td class="align-middle p-2" >'.$read['tbl_number_rank'].'</td>
                                <td class="align-middle p-2">
                                <a href="cer.php?do=view&id='.$read['tbl_number_id'].'" class="btn me-1 mb-1 btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[87] . '" target="_blank" ><span class="bi-file-arrow-down-fill"></span></a> 
                                '.(!empty($read['tbl_number_filecertificate2']) ? '<a href="../upload/cer/' . $read['tbl_number_filecertificate2'] . '" target="_blank" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1810] . '" target="_blank" ><span class="bi-cloud-download"></span></a>' : '' ).'
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
                                <form action="member.php?do=gotopage" class="search-form"  method="POST">
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
                            header("Location: member.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: member.php?keyword=$keyword_search");
                        }
                    }

                    if (isset($do) && $do == 'view') {
                    $result = Query("SELECT * From tbl_number where tbl_number_id='$id'");
                    if (mysqli_num_rows($result) <= 0) {
                        echo '<div>Not Found !</div>';
                    } else {
                        $read = mysqli_fetch_array($result);
                        
                        $skill = $read['tbl_number_skill'];
                        $contract = $read['tbl_number_contract'];
                        $user_id = $read['tbl_number_user'];
        
                        $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_type='4' and tbl_categories_code = '$skill' and tbl_categories_lng='$language_default'");
                        $readSkill = mysqli_fetch_array($resultSkill);
        
                        $resultContract = Query("SELECT * From tbl_contract Where tbl_contract_id = '$contract'");
                        $readContract = mysqli_fetch_array($resultContract);
        
        
                                    $resultMember = Query("Select * from tbl_member where tbl_member_id = '$user_id' ");
                                    $readMember = mysqli_fetch_array($resultMember);
        
        
                                    $resultCer = Query("Select * from tbl_certificate where tbl_certificate_skill = '$skill' and tbl_certificate_lng='$language_default' ");
                                    $readCer = mysqli_fetch_array($resultCer);
                                    $userName = $readMember['tbl_member_fname'].' '.$readMember['tbl_member_lname'];
                                    
                                    
                                    $text = @str_replace("[name]","$userName",$read['tbl_number_textcertificate']);
                                    $text = @str_replace("[father]","$readMember[tbl_member_fathername]",$text);
                                    $text = @str_replace("[national]",toPersianNum($readMember['tbl_member_nationalcode']),$text);
                                    $text = @str_replace("[sadere]","$readMember[tbl_member_bcity]",$text);
                                    $text = @str_replace("[company]","$readContract[tbl_contract_customer]",$text);
                                    $text = @str_replace("[start]",toPersianNum($readContract['tbl_contract_startdate']),$text);
                                    $text = @str_replace("[end]",toPersianNum($readContract['tbl_contract_enddate']),$text);
                                    $text = @str_replace("[skill]","$readSkill[tbl_categories_title]",$text);
                                    $text = @str_replace("[marja]","$readCer[tbl_certificate_partner]",$text);
                                    $text = @str_replace("[number]","$read[tbl_number_rank]",$text);
        
                                    echo '<div class="certificate"><div class="cer" style="background:url('.(!empty($read['tbl_number_filecertificate']) ? '../upload/cer/'.$read['tbl_number_filecertificate']: 'assets/img/certificate.jpg' ).');"><div class="cerimg" style="background:url('.(!empty($readMember['tbl_member_profile']) ? '../upload/profile/'.$readMember['tbl_member_profile'] : 'assets/img/no-image.jpg' ).')"></div>';
                                    echo '<div class="track">' . toPersianNum(jdate("Y/m/d")) . '<br><span>' . $read['tbl_number_numbercertificate'] . '</span></div>';
                                    echo '<div class="cer_content ">';
                                    echo '<p>'.$text.'</p>';
        
                                    echo '</div></div></div>';
                                    
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