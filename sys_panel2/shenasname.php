<?php

include './includer.php';

$show_per_page = '20';
$pagetitle = $lang[2027];
$Tabpage = '101';
$cat_manager_id = '0';
$thisaccess = '1,5';
$img_item = '2';
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
                            <?=(!isset($do) && ($access == '8' || $access == '1') ? '<div class="plus">
                                <a href="?do=add"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <div class="card-body p-0 position-relative">
                <?php
                  
                    if (isset($do) && $do == 'view') {

                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_mohtava_lng = '$language_id'";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_mohtava Where tbl_mohtava_code =  '$code'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        $mohtava = read_fild(NULL, 'tbl_mohtava_id');
                        ?>
                        <div class="col-lg-12 col-12 p-3">
                        <?php
                         $res_rate = Query("SELECT total_points,rating_number, FORMAT((total_points / rating_number),1) as average_rating FROM tbl_rating WHERE post_id = '$mohtava' ");
                         $read_rate = mysqli_fetch_array($res_rate);
                         echo '<div class="ratesystem"><input value="' . (!empty($read_rate['total_points']) ? round($read_rate['total_points'] / $read_rate['rating_number']) : '0') . '" id="rating_star_show" type="hidden" name="' . $mohtava . '" />
                            <div class="overall-rating">
                                '.$lang[2032].' <span id="totalrat">( ' . (!empty($read_rate['rating_number']) ? $read_rate['rating_number'] : '0') . ' )</span></div></div>';
                                ?>
                                </div>
                        <?php
                        echo '<div class="col-lg-12 col-12 p-3">';
                          ?>
           

                <?php
                              for ($i = 1; $i <= 1; $i++) {
                                  echo '<div class="form-group">
                                    <label class="red">' . $lang[1856] . '</label>
                                    <p>'.read_fild($language[$i], 'tbl_mohtava_title').'</p>
                                    </div>';
                              }
                              ?>

                <?php
                                  echo '<div class="form-group">
                                  <label class="red">' . $lang[1832] . '</label>';
                                  for ($i=1857; $i <=1873 ; $i++) { 
                                    $thiscode = explode("-",$lang[$i]);
                                    echo '<p>'.(read_fild(NULL, 'tbl_mohtava_ghaleb') == $i ? $lang[$i] :'').'</p>';
                                }

                                  echo '</div><hr>';
                              
                                  for ($i = 1; $i <= 1; $i++) {
                                      echo '<div class="form-group">
                                        <label class="red">' . $lang[1833] . '</label>
                                        <p>'.read_fild($language[$i], 'tbl_mohtava_subtitle1').'</p>
                                        </div>';
                                  }
      
                                  for ($i = 1; $i <= 1; $i++) {
                                      echo '<div class="form-group">
                                        <label class="red">' . $lang[1834] . '</label>
                                        <p>'.read_fild($language[$i], 'tbl_mohtava_subtitle2').'</p>
                                        </div>';
                                  }
                                  echo '<div class="form-group">
                                  <label class="red">' . $lang[1838] . '</label>';
                                  $resultKey = Query("SELECT * From tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                  while ($readKey = mysqli_fetch_array($resultKey)) {
    
                                        echo '<p>'.(strpos(read_fild(NULL, 'tbl_mohtava_department'),$readKey['tbl_categories_code']) !== false ? $readKey['tbl_categories_title'] :'').'</p>';
                                  
                                  }
                                  echo '</div><hr>';
                                  for ($i = 1; $i <= 1; $i++) {
                                      echo '<div class="form-group">
                                          <label class="red">' . $lang[1890] . '</label>
                                          <p>'.read_fild($language[$i], 'tbl_mohtava_text').'</p>
                                          </div>';
                                  }
      
                                  echo '<div class="form-group">
                                  <label class="red">' . $lang[1835] . '</label>';
                                  $resultKey = Query("SELECT * From tbl_content Where tbl_content_type='21' and tbl_content_fld1='$language_default' ORDER BY tbl_content_id DESC ");
                                  while ($readKey = mysqli_fetch_array($resultKey)) {
                                      echo '<p>'.(strpos(read_fild(NULL, 'tbl_mohtava_keywords'),$readKey['tbl_content_code']) !== false ? $readKey['tbl_content_title'].',' :'').'</p>';
                                  }
                                  echo '</div><hr>';
                              ?>

                <div class="form-group">
                    <label class="red"><?=$lang[1837]?></label>
                    <p><?=read_fild(NULL, 'tbl_mohtava_refrence')?></p>
                </div>

                <div class="form-group">
                    <label class="red"><?=$lang[1836]?></label>
                    <p><?=read_fild(NULL, 'tbl_mohtava_link')?></p>
                </div>


                <div class="form-group">
                    <label class="red"><?=$lang[1839]?></label>
                    <p><?=read_fild(NULL, 'tbl_mohtava_page')?></p>
                </div>
                <div class="form-group">
                    <label class="red"><?=$lang[1840]?></label>
                    <p><?=read_fild(NULL, 'tbl_mohtava_time')?></p>
                </div>
                <div class="form-group">
                    <label class="red"><?=$lang[1841]?></label>
                    <p><?=read_fild(NULL, 'tbl_mohtava_moalef')?></p>
                </div>
                <div class="form-group">
                    <label class="red"><?=$lang[1959]?></label>
                    <p><?=read_fild(NULL, 'tbl_mohtava_motarjem')?></p>
                </div>
                <?php
                          echo '<div class="form-group">
                          <label class="red">' . $lang[1948] . '</label><p>';
                          $resultKey = Query("SELECT * From tbl_user Where tbl_user_contentcreator='1' and tbl_user_bakhsh='2' ORDER BY tbl_user_id DESC ");
                          while ($readKey = mysqli_fetch_array($resultKey)) {
                              echo ''.(read_fild(NULL, 'tbl_mohtava_creator') == $readKey['tbl_user_id'] ? $readKey['tbl_user_name'].',' :'');
                          }
                          echo '</p></div>';
                          if(!empty(read_fild(NULL, 'tbl_mohtava_producer'))) {
                          echo '<hr><div class="form-group">
                        <label class="red">' . $lang[1951] . '</label><p>';
                        $resultKey = Query("SELECT * From tbl_user Where tbl_user_contentcreator='1' and tbl_user_bakhsh='1' ORDER BY tbl_user_id DESC ");
                        while ($readKey = mysqli_fetch_array($resultKey)) {
                            echo ' '.(read_fild(NULL, 'tbl_mohtava_producer') == $readKey['tbl_user_id'] ? $readKey['tbl_user_name'].',' :'');
                        }
                        echo '</p></div>';
                    }

                   
                    $file = explode(",",read_fild(NULL, 'tbl_mohtava_file'));
                    $ext = explode(".",$file[0]);
                    $ext = end($ext);
                    echo  read_fild(NULL, 'tbl_mohtava_text');
                    if(!empty($file[0])) {
                        if ($ext == 'mp4' || $ext == 'MP4') {
                            echo '<video controls crossorigin playsinline poster="' . (!empty($file[1]) ? '../upload/course/' . $file[1] : '../assets/img/no-image.jpg') . '" class="player">
                                <source src="' .(!empty(read_fild(NULL, 'tbl_mohtava_foldername')) ? '../uploadcourse/'.read_fild(NULL, 'tbl_mohtava_foldername').'/'. $file[0] : 'upload/course/' . $file[0] )  . '" type="video/mp4" size="576">                             
                            </video>';
                        } else if ($ext == 'mp3' || $ext == 'MP3') {
                            echo '<audio crossorigin playsinline>
                                    <source src="' . (!empty(read_fild(NULL, 'tbl_mohtava_foldername')) ? '../uploadcourse/'.read_fild(NULL, 'tbl_mohtava_foldername').'/'. $file[0] : 'upload/course/' . $file[0] )  . '" type="audio/mp3">
                                </audio>';
                        } else {
                                echo '<div class="download"><a href="' . (!empty(read_fild(NULL, 'tbl_mohtava_foldername')) ? '../uploadcourse/'.read_fild(NULL, 'tbl_mohtava_foldername').'/'. $file[0] : 'upload/course/' . $file[0] )  . '" target="_blank">'.$lang[851].'</a></div>';
                        }
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