<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
$Tabpage = '11';
include './header.inc.php';
$show_per_page = '30';
    $pagetitle = 'محصولات';
    
    $cat_manager_id = '0';
    $thisaccess = '1,3';
    $img_item = '10';
$check_level = '0,0';
?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php

                    if (!isset($do)) {

                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table></tr><td class="col-sm-9 padding-clear"><a class="btn btn-primary margin-left" href="?do=add">'.$lang[31].'</a></td>
                    <td class="col-sm-6" style="padding-left: 0px;">
                        <form action="product.php?do=search" method="POST">
                            <input type="text" class="form-control" id="search_content_title" name="keyword" value="'.(!empty($keyword) ? $keyword : '').'">
                    </td>
                    <td class="col-sm-2" style="padding-left: 0px;">
                        <button type="submit" name="go" class="btn btn-primary">' . $lang[77] . '</button>
                        </form>
                    </td>
                    </tr>
                    </table>';


                    $result_for_count = Query("SELECT * From tbl_section Where (tbl_section_title LIKE '%$keyword%' or tbl_section_brand_name like '%$keyword%') ");
                    $count_result = mysqli_num_rows($result_for_count);
                    $start_contnet = ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);

                    $result = Query("SELECT * From tbl_section Where (tbl_section_title LIKE '%$keyword%' or tbl_section_brand_name like '%$keyword%') ORDER BY tbl_section_id DESC LIMIT $start_contnet,$show_per_page ");

                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                    echo '<div class="margin-top">' . $lang[57] . '</div>';
                    }
                    echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                            <thead class="table-dark">
                                <tr>
                                    <th>ردیف</th>
                                    <th>عنوان</th>
                                    <th>نام برند</th>
                                    <th>کوئری</th>
                                    <th>نام دسته بندی</th>
                                    <th>بروز رسانی</th>
                                    <th>تعداد محصولات</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>';
                                $i = 1;
                                while ($read = mysqli_fetch_array($result)) {
                                    $pro = Query("select * from tbl_product where tbl_product_refcode='$read[tbl_section_code]' ");

                                echo '<tr>
                                    <td style="width: 1%">' . $i . '</td>
                                    <td class="col-sm-2"><a href="?do=edit&code=' . $read['tbl_section_id'] . '">' . $read['tbl_section_title'] . '</a></td>
                                    <td class="col-sm-2">' . $read['tbl_section_brand_name'] . '</td>
                                    <td class="col-sm-2">' . $read['tbl_section_qu'] . '</td>
                                    <td class="col-sm-2">' . $read['tbl_section_category_name'] . '</td>
                                    <td class="col-sm-2">' . $read['tbl_section_dateupdate'] . '</td>
                                    <td class="col-sm-2">' . mysqli_num_rows($pro) . '</td>
                                    <td class="col-sm-1">
                                        <a href="?do=edit&code=' . $read['tbl_section_id'] . '" class="btn btn-xs btn-success ">' . $lang[23] . '</a>
                                        <a href="?do=delete&code=' . $read['tbl_section_id'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a>
                                        <a href="run.php?code=' . $read['tbl_section_code'] . '" class="btn btn-xs btn-warning" target="_blank">بروز رسانی</a>
                                        <a href="https://ronixiran.ir/product.php?code=' . $read['tbl_section_code'] . '" class="btn btn-xs btn-dark" target="_blank">لینک فریم</a>
                                    </td>
                                </tr>';
                                $i++;
                                }

                                echo '</tbody>
                        </table>';

                    if ($page > $page_number) {
                    echo '<div class="margin-top">' . $lang[57] . '</div>';
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
                            <td class="col-lg-6">
                                <ul class="pagination">
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword   ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword  ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="product.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
                                    <div class="form-group">
                                        <button type="submit" name="gotopage" class="btn btn-success" style="float: left;"><?= $lang[76] ?></button>
                                        <input type="text" class="form-control go-fld" name="page_number" style="width: 50px;float: left;height: 34px;border-radius: 2px;">
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
                            Query("update tbl_section set tbl_section_fld5='$val' Where tbl_section_code = '$value' ");
                        }
                        header("Location: product.php?page=$page");
                    }
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: product.php?page=$page_number");
                        }
                    }

                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: product.php?keyword=$keyword_search");
                        }
                    }

                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-8">
                          <form action="?do=additem" method="POST" enctype="multipart/form-data" >
                           <input type="hidden" name="code" value="' . Code("", "no", 9) . '" >';
                            echo '<div class="form-group">
                            <label>' . $lang[50] . '</label>
                            <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="form-group">
                            <label>برند آی دی</label>
                            <input type="text" name="brand" class="form-control" >
                            </div>
                            <div class="form-group">
                            <label>نام برند</label>
                            <input type="text" name="brand_name" class="form-control" >
                            </div>
                            <div class="form-group">
                            <label>دسته بندی آی دی</label>
                            <input type="text" name="category" class="form-control" >
                            </div>
                            <div class="form-group">
                            <label>نام دسته بندی</label>
                            <input type="text" name="category_name" class="form-control" >
                            </div>
                            <div class="form-group">
                            <label>میزان افزایش قیمت</label>
                            <input type="text" name="afzayesh" class="form-control" >
                            </div>
                            <div class="form-group">
                            <label>کوئری جستجو</label>
                            <input type="text" name="qu" class="form-control" >
                            </div>
                            <div class="form-group">
                            <label>لینک خرید آنلاین</label>
                            <input type="text" name="link" class="form-control" >
                            </div>
                            <div class="form-group">
                            <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary">
                            </div>
                            </form></div>';
                    }
                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $title = $_POST['title'];
                            $brand = $_POST['brand'];
                            $qu = $_POST['qu'];
                            $brand_name = $_POST['brand_name'];
                            $category = $_POST['category'];
                            $category_name = $_POST['category_name'];
                            $afzayesh = $_POST['afzayesh'];
                            $link = $_POST['link'];
                            $dateadd = jdate("Y/m/d");
                                Query("INSERT INTO tbl_section (
                                tbl_section_code,
                                tbl_section_title,
                                tbl_section_brand,
                                tbl_section_brand_name,
                                tbl_section_category,
                                tbl_section_category_name,
                                tbl_section_afzayesh,
                                tbl_section_link,
                                tbl_section_qu,
                                tbl_section_dateadd
                                ) 
                                values('$code',
                                '$title',
                                '$brand',
                                '$brand_name',
                                '$category',
                                '$category_name',
                                '$afzayesh',
                                '$link',
                                '$qu',
                                '$dateadd')");

                                echo "INSERT INTO tbl_section (
                                tbl_section_code,
                                tbl_section_title,
                                tbl_section_brand,
                                tbl_section_brand_name,
                                tbl_section_category,
                                tbl_section_category_name,
                                tbl_section_afzayesh,
                                tbl_section_link,
                                tbl_section_qu,
                                tbl_section_dateadd
                                ) 
                                values('$code',
                                '$title',
                                '$brand',
                                '$brand_name',
                                '$category',
                                '$category_name',
                                '$afzayesh',
                                '$link',
                                '$qu',
                                '$dateadd')";

                                $_SESSION['error'] = $lang[32];
                                //header("Location: product.php");
                            
                        }
                    }

                    if (isset($do) && $do == 'delete') {
                       

                        $result = Query("SELECT * from tbl_section  Where tbl_section_id = '$code'  ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-8">' . $lang[25] . '<br /><h2>' . $read['tbl_section_title'] . '</h2><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">';

                        

                        echo  '<input type="hidden" value="' . $code . '" name="code" >
                            <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                            <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $code= $_POST['code'];
                            Query("DELETE from tbl_section Where tbl_section_id = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: product.php");
                            exit();
                        } elseif (isset($_POST['nodelete'])) {
                            header("Location: product.php");
                            exit();
                        }
                    }

                    if (isset($do) && $do == 'edit') {

                        $result = Query("SELECT * FROM tbl_section Where tbl_section_id =  '$code'   ");
                        $read = mysqli_fetch_array($result);

                        echo '<div class="col-lg-8">
                          <form action="?do=edititem" method="POST" enctype="multipart/form-data" >
                          <input type="hidden" name="code" value="' . $code . '" >';

                        echo '<div class="form-group">
                            <label>' . $lang[50] . '</label>
                            <input type="text" name="title" value="'.$read['tbl_section_title'].'" class="form-control" required>
                            </div>
                            <div class="form-group">
                            <label>برند آی دی</label>
                            <input type="text" name="brand" value="'.$read['tbl_section_brand'].'" class="form-control" >
                            </div>
                            <div class="form-group">
                            <label>نام برند</label>
                            <input type="text" name="brand_name" value="'.$read['tbl_section_brand_name'].'" class="form-control" >
                            </div>
                            <div class="form-group">
                            <label>دسته بندی آی دی</label>
                            <input type="text" name="category" value="'.$read['tbl_section_category'].'" class="form-control" >
                            </div>
                            <div class="form-group">
                            <label>نام دسته بندی</label>
                            <input type="text" name="category_name" value="'.$read['tbl_section_category_name'].'" class="form-control" >
                            </div>
                            <div class="form-group">
                            <label>میزان افزایش قیمت</label>
                            <input type="text" name="afzayesh" value="'.$read['tbl_section_afzayesh'].'" class="form-control" >
                            </div>
                            <div class="form-group">
                            <label>کوئری جستجو</label>
                            <input type="text" name="qu" class="form-control" value="'.$read['tbl_section_qu'].'" >
                            </div>
                            <div class="form-group">
                            <label>لینک خرید آنلاین</label>
                            <input type="text" name="link" class="form-control" value="'.$read['tbl_section_link'].'" >
                            </div>';

                        echo  '<div class="form-group">
                                  <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary">
                                  </div>
                                  </form></div>';
                    }

                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['updateitem'])) {
                            $code = $_POST['code'];
                            $title = $_POST['title'];
                            $brand = $_POST['brand'];
                            $brand_name = $_POST['brand_name'];
                            $category = $_POST['category'];
                            $qu = $_POST['qu'];
                            $category_name = $_POST['category_name'];
                            $afzayesh = $_POST['afzayesh'];
                            $link = $_POST['link'];
                                Query("Update tbl_section SET 
                                    tbl_section_title='$title' ,
                                    tbl_section_brand = '$brand' ,
                                    tbl_section_brand_name = '$brand_name' ,
                                    tbl_section_category = '$category' ,
                                    tbl_section_category_name = '$category_name' ,
                                    tbl_section_afzayesh = '$afzayesh' ,
                                    tbl_section_link = '$link',
                                    tbl_section_qu = '$qu'
                                    Where tbl_section_id= '$code'");
                           
                            $_SESSION['error'] = $lang[21];
                            header("Location: product.php");
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