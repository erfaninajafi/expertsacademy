<?php
include './includer_site.php';
header('Access-Control-Allow-Origin: *');

if (isset($do) && $do == 'registeritem') {
    if (empty($_SESSION['userid'])) {
        $_SESSION['router'] = 'show-content';
        header("Location: " . (strpos($_SERVER["REQUEST_URI"] ?? "", "/expertsacademy/") === 0 ? "/expertsacademy" : "") . "/$lng/register/login/");
        exit();
    } else {
        $codeInput = $_GET['code'] ?? null;
        $code = is_string($codeInput) ? sqi($codeInput) : '';
        if ($code === '') {
            header("Location: $site_url/$lng/");
            exit();
        }
        $date = jdate("Y/m/d : A:h:s");
        $dateen = jdate("Y/m/d");
        $codetrack = Code("", "no", 7);
        $user_id = $_SESSION['userid'];
        $resultMohtavaView = QueryExpert("select * from tbl_courseparent where tbl_courseparent_class='$code' ");
        if (mysqli_num_rows($resultMohtavaView) <= 0) {
            header("Location: $site_url/$lng/");
            exit();
        }
        $readMohtavaView = mysqli_fetch_array($resultMohtavaView);
        // $price = $readMohtavaView['tbl_mohtava_price'];
        // $sellprice = $readMohtavaView['tbl_mohtava_sellprice'];
        // $percentmalek = $readMohtavaView['tbl_mohtava_percentmalek'];
        // $percentdepartement = $readMohtavaView['tbl_mohtava_percentdepartement'];
        // $percentacademy = $readMohtavaView['tbl_mohtava_percentacademy'];
        // $pricedate = $readMohtavaView['tbl_mohtava_pricedate'];

        $result = QueryExpert("select * from tbl_mohtavaregister where tbl_mohtavaregister_section='1' and tbl_mohtavaregister_mohtava='$code' and tbl_mohtavaregister_user='$user_id' ");
        if (mysqli_num_rows($result) <= 0) {
            QueryExpert("insert into tbl_mohtavaregister (
        tbl_mohtavaregister_mohtava,
        tbl_mohtavaregister_user,
        tbl_mohtavaregister_dateregister,
        tbl_mohtavaregister_code,
        tbl_mohtavaregister_dateen,
        tbl_mohtavaregister_model,
        tbl_mohtavaregister_price,
        tbl_mohtavaregister_sellprice,
        tbl_mohtavaregister_percentmalek,
        tbl_mohtavaregister_percentdepartement,
        tbl_mohtavaregister_percentacademy,
        tbl_mohtavaregister_pricedate,
        tbl_mohtavaregister_section) values(
        '$code',
        '$user_id',
        '$date',
        '$codetrack',
        '$dateen',
        '0',
        '0',
        '0',
        '0',
        '0',
        '0',
        '0',
        '1')");
            header("Location: $site_url/$lng/mypanel/mycourseview/$code/");
        }
    }
}


if (isset($do) && $do == 'registerlib') {
    if (empty($_SESSION['userid'])) {
        $_SESSION['router'] = 'show-content';
        header("Location: " . (strpos($_SERVER["REQUEST_URI"] ?? "", "/expertsacademy/") === 0 ? "/expertsacademy" : "") . "/$lng/register/login/");
        exit();
    } else {
        $codeInput = $_GET['code'] ?? null;
        $code = is_string($codeInput) ? sqi($codeInput) : '';
        if ($code === '') {
            header("Location: $site_url/$lng/");
            exit();
        }
        $date = jdate("Y/m/d : A:h:s");
        $dateen = jdate("Y/m/d");
        $codetrack = Code("", "no", 7);
        $user_id = $_SESSION['userid'];
        $resultMohtavaView = QueryExpert("select * from tbl_mohtava where tbl_mohtava_code='$code' ");
        if (mysqli_num_rows($resultMohtavaView) <= 0) {
            header("Location: $site_url/$lng/");
            exit();
        }
        $readMohtavaView = mysqli_fetch_array($resultMohtavaView);
        $price = $readMohtavaView['tbl_mohtava_price'];
        $sellprice = $readMohtavaView['tbl_mohtava_sellprice'];
        $percentmalek = $readMohtavaView['tbl_mohtava_percentmalek'];
        $percentdepartement = $readMohtavaView['tbl_mohtava_percentdepartement'];
        $percentacademy = $readMohtavaView['tbl_mohtava_percentacademy'];
        $pricedate = $readMohtavaView['tbl_mohtava_pricedate'];

        $result = QueryExpert("select * from tbl_mohtavaregister where tbl_mohtavaregister_mohtava='$code' and tbl_mohtavaregister_user='$user_id' ");
        if (mysqli_num_rows($result) <= 0) {
            QueryExpert("insert into tbl_mohtavaregister (
            tbl_mohtavaregister_mohtava,
            tbl_mohtavaregister_user,
            tbl_mohtavaregister_dateregister,
            tbl_mohtavaregister_code,
            tbl_mohtavaregister_dateen,
            tbl_mohtavaregister_model,
            tbl_mohtavaregister_price,
            tbl_mohtavaregister_sellprice,
            tbl_mohtavaregister_percentmalek,
            tbl_mohtavaregister_percentdepartement,
            tbl_mohtavaregister_percentacademy,
            tbl_mohtavaregister_pricedate,
            tbl_mohtavaregister_section) values(
            '$code',
            '$user_id',
            '$date',
            '$codetrack',
            '$dateen',
            '0',
            '$price',
            '$sellprice',
            '$percentmalek',
            '$percentdepartement',
            '$percentacademy',
            '$pricedate',
            '0')");
            header("Location: $site_url/$lng/mypanel/mycontentview/$code/");
        }
    }
}


if (isset($do) && $do == 'contact') {
    $contactLanguage = ($_POST['lng'] ?? '') === 'en' ? 'en' : 'fa';
    $requiredContactFields = ['fld1', 'fld2', 'fld3', 'section'];
    $invalidContact = false;
    foreach ($requiredContactFields as $field) {
        if (!isset($_POST[$field]) || !is_string($_POST[$field]) || trim($_POST[$field]) === '') {
            $invalidContact = true;
            break;
        }
    }
    if ($invalidContact || !in_array($_POST['section'], ['1', '2'], true) || !filter_var($_POST['fld2'], FILTER_VALIDATE_EMAIL)) {
        echo $contactLanguage === 'en'
            ? '<span class="text-danger" role="alert">Please complete the required contact fields correctly.</span>'
            : '<span class="text-danger" role="alert">لطفاً اطلاعات ضروری فرم تماس را به‌درستی تکمیل کنید.</span>';
        exit;
    }

    $arr = $_POST;
    $data = array_fill_keys(['lng', 'fld1', 'fld2', 'fld3', 'fld4', 'fld5', 'fld6', 'fld7', 'fld8', 'fld9', 'fld10', 'fld11', 'fld12', 'section', 'kind'], '');
    foreach ($arr as $key => $value) {
        if (!is_string($value)) {
            continue;
        }
        if (!empty($value)) {
            $data[$key] = limit_words(strip_tags(addslashes($arr["$key"])), 500);
        } else {
            $data[$key] = '';
        }
    }
    $lng = ($data['lng'] ?? '') === 'en' ? 'en' : 'fa';
    $data['lng'] = $lng;
    include __DIR__ . '/include_site/lang/' . $lng . '_lng.php';
    $date = jdate("Y/m/d - a:h:s");
    if (isset($_FILES['file']['name'])) {
        $file = $_FILES['file']['name'];
        $read_file_name = explode(".", $file);
        $extn = end($read_file_name);
        $final_name = 'contact_' . time() . '.' . $extn;
        if ($extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'doc' || $extn == 'DOC' || $extn == 'DOCX' || $extn == 'docx') {
            $file_upload_name = $final_name;
            move_uploaded_file($_FILES['file']["tmp_name"], "upload_site/" . $final_name);
        } else {
            $file_upload_name = '';
        }
    } else {
        $file_upload_name = '';
    }

    $section = (!empty($data['section']) ? $data['section'] : '');
    $kind = (!empty($data['kind']) ? $data['kind'] : '');

    Query("insert into tbl_form (
    tbl_form_lng,
    tbl_form_fld1,
    tbl_form_fld2,
    tbl_form_fld3,
    tbl_form_fld4,
    tbl_form_fld5,
    tbl_form_fld6,
    tbl_form_fld7,
    tbl_form_fld8,
    tbl_form_fld9,
    tbl_form_fld10,
    tbl_form_fld11,
    tbl_form_fld12,
    tbl_form_model,
    tbl_form_kind,
    tbl_form_date,
    tbl_form_status,
    tbl_form_section,
    tbl_form_file) 
    values('$data[lng]','$data[fld1]','$data[fld2]','$data[fld3]','$data[fld4]','$data[fld5]','$data[fld6]','$data[fld7]','$data[fld8]','$data[fld9]','$data[fld10]','$data[fld11]','$data[fld12]','$section','$kind','$date','0','contact','$file_upload_name') ");

    echo $lang[117];
}

if (isset($do) && $do == 'faq') {
    $faqLanguage = ($_POST['lng'] ?? '') === 'en' ? 'en' : 'fa';
    $requiredFaqFields = ['fld1', 'fld2', 'fld3', 'fld4', 'fld5'];
    foreach ($requiredFaqFields as $field) {
        if (!isset($_POST[$field]) || !is_string($_POST[$field]) || trim($_POST[$field]) === '') {
            echo $faqLanguage === 'en'
                ? '<span class="text-danger" role="alert">Please complete all required fields.</span>'
                : '<span class="text-danger" role="alert">لطفاً همهٔ بخش‌های ضروری فرم را تکمیل کنید.</span>';
            exit;
        }
    }

    $arr = $_POST;
    $data = array_fill_keys(['lng', 'fld1', 'fld2', 'fld3', 'fld4', 'fld5'], '');
    foreach ($arr as $key => $value) {
        if (!is_string($value)) {
            continue;
        }
        if (!empty($value)) {
            $data[$key] = limit_words(strip_tags(addslashes($arr["$key"])), 500);
        } else {
            $data[$key] = '';
        }
    }
    $lng = ($data['lng'] ?? '') === 'en' ? 'en' : 'fa';
    $data['lng'] = $lng;
    include __DIR__ . '/include_site/lang/' . $lng . '_lng.php';
    $date = jdate("Y/m/d - a:h:s");

    Query("insert into tbl_form (
    tbl_form_lng,
    tbl_form_fld1,
    tbl_form_fld2,
    tbl_form_fld3,
    tbl_form_fld4,
    tbl_form_fld5,
    tbl_form_date,
    tbl_form_status,
    tbl_form_section) 
    values('$data[lng]','$data[fld1]','$data[fld2]','$data[fld3]','$data[fld4]','$data[fld5]','$date','0','faq') ");

    echo $lang[117];
}
if (isset($do) && $do == 'job') {
    $arr = $_POST;
    foreach ($arr as $key => $value) {
        if (!empty($value)) {
            $data[$key] = limit_words(strip_tags(addslashes($arr["$key"])), 500);
        } else {
            $data[$key] = '';
        }
    }

    $lng = ($data['lng'] ?? '') === 'en' ? 'en' : 'fa';
    $data['lng'] = $lng;
    include __DIR__ . '/include_site/lang/' . $lng . '_lng.php';
    $date = jdate("Y/m/d - a:h:s");

    if (isset($_FILES['file']['name'])) {
        $file = $_FILES['file']['name'];
        $read_file_name = explode(".", $file);
        $extn = end($read_file_name);
        $final_name = 'job_' . time() . '.' . $extn;
        if ($extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'doc' || $extn == 'DOC' || $extn == 'DOCX' || $extn == 'docx') {
            $file_upload_name = $final_name;
            move_uploaded_file($_FILES['file']["tmp_name"], "upload_site/" . $final_name);
        } else {
            $file_upload_name = '';
        }
    } else {
        $file_upload_name = '';
    }

    Query("insert into tbl_form (
        tbl_form_lng,
        tbl_form_fld1,
        tbl_form_fld2,
        tbl_form_fld3,
        tbl_form_fld4,
        tbl_form_fld5,
        tbl_form_date,
        tbl_form_status,
        tbl_form_file,
        tbl_form_section) 
        values('$data[lng]','$data[fld1]','$data[fld2]','$data[fld3]','$data[fld4]','$data[fld5]','$date','0','$file_upload_name','job') ");

    echo $lang[117];
}

if (isset($do) && $do == 'modal-video') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $videoId = sqi($_POST['id']); // گرفتن ID ویدیو
        $result = Query("SELECT * From tbl_content where tbl_content_code ='$videoId' ");
        if (mysqli_num_rows($result) <= 0) {
            echo 'درخواست نامعتبر است.';
        } else {
            $read = mysqli_fetch_array($result);
            $file = $read['tbl_content_file'];
            echo '<video controls width="100%"><source src="' . $site_url . 'upload_site/' . $file . '" type="video/mp4">مرورگر شما پخش ویدیو را پشتیبانی نمی‌کند.</video>';
        }
    } else {
        echo 'درخواست نامعتبر است.';
    }
}
if (isset($do) && $do == 'product') {
    $val = sqi($_POST['val']);

    $result = Query("SELECT * From tbl_product where tbl_product_id ='$val' ");
    if (mysqli_num_rows($result) > 0) {
        $read = mysqli_fetch_array($result);
        // echo $read['tbl_product_data'];
        // $data = json_decode($read['tbl_product_data'],true);
        // function cmp($a, $b)
        // {
        //   if( $a["price"] == 0 ) { return 1; }
        //   else if( $b["price"] == 0 ) { return -1;}
        //     else { return strcmp($a["price"], $b["price"]); }
        // }
        // usort($data, "cmp");

        //[{"shop_id":5389,"price":2080000,"last_price_change_date":"۸ روز پیش"}]
?>
        <div class="row">

            <div class="col-12 col-lg-12">
                <?= ($read['tbl_product_text'] != '<table class="table table-dark table-striped"></table>' ? '<p>مشخصات فنی</p>' . $read['tbl_product_text'] : '<p>مشخصات فنی موجود نمی باشد</p>') ?>
            </div>
        </div>
    <?php
    }
}



if (isset($do) && $do == 'getdata') {
    $fil = '';
    @$cat = strip_tags(sqi($_POST['cat']));
    @$offset = strip_tags(sqi($_POST['offset']));
    $limit = 30;
    $query = " tbl_product_refcode='$cat' ";
    $request = "<input type='hidden' id='cat' value='$cat'>";


    $resultSection = Query("SELECT tbl_section_link From tbl_section Where tbl_section_code='$cat' ");
    $readSection = mysqli_fetch_array($resultSection);
    $refLink = $readSection['tbl_section_link'];

    $results =  Query("Select * From tbl_product where  $query order by tbl_product_id DESC");
    $row = mysqli_num_rows($results);
    if ($row == 0) {
        echo '<div class="col-lg-12 col-md-12 col-sm-12 ">
                        Oops! No items found.
                    </div>';
    }
    if ($fil == '') {
        echo '<input type="hidden" id="allData" value="' . $row . '">';
    } else {
        echo '<input type="hidden" id="allData" value="' . $row . '">';
    }
    $result = Query("Select * From tbl_product where  $query  order by tbl_product_price = 0   limit " . $offset . ", " . $limit . "");
    $counter = ++$offset;
    while ($read = mysqli_fetch_array($result)) {
        $img = $read['tbl_product_img'];
        $ext = explode(".", $img);
        $fext = end($ext);
        echo '<div class="product-card loadData" id="myData_' . $counter . '" itemscope itemtype="http://schema.org/Offer">

<a class="product-card__image " onclick="ajaxProduct(' . $read['tbl_product_id'] . '); return false" >
              <img src="' . $site_url . (!empty($img) ? ($fext != 'webp' && $fext != 'png' ? 'tn.php?file=uploads/' . $img . '&width=150&height=150' : 'uploads/' . $img) : 'asset/images/no-image.jpg?ver=1') . '" alt="' . $read['tbl_product_title'] . '" />
              </a>
              <div class="product-card__body">
              <p>' . $read['tbl_product_title'] . '</p>
                  <div class="product-card__price price"><span class="price__current" itemprop="price"><p>' . toPersianNum(number_format($read['tbl_product_price'])) . ' تومان</p></span></div>
              </div>
              <div class="product-card__btn ">
                  <button class="btn btn_product" type="button" onclick="ajaxProduct(' . $read['tbl_product_id'] . '); return false"><p>مشخصات فنی</p></button>
              </div>

  </div>';
        $counter++;
    }
    ?>

<?php
}
