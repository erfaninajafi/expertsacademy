<?php

function Query($query)
{
    global $dbserver, $dbname, $dbusername, $dbpassword;
    $con =  mysqli_connect("$dbserver", "$dbusername", "$dbpassword", "$dbname");
    mysqli_set_charset($con, 'utf8');
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $select = mysqli_select_db($con, "$dbname");
    $result = mysqli_query($con, $query);
    return $result;
}
function QueryExpert($query)
{
    global $dbserverExpert, $dbnameExpert, $dbusernameExpert, $dbpasswordExpert;
    $con =  mysqli_connect("$dbserverExpert", "$dbusernameExpert", "$dbpasswordExpert", "$dbnameExpert");
    mysqli_set_charset($con, 'utf8');
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $select = mysqli_select_db($con, "$dbnameExpert");
    $result = mysqli_query($con, $query);
    return $result;
}
function sqi($val)
{
    $symbol = array(',', '', '', "'", '"', '!', '?', '/', '[', ']', '+', '=', '#', '\x00', '\n', '\r', '\x1a', '&', '$');
    $val = preg_replace("/<.*?>/", "", $val);
    $val = str_replace($symbol, "", $val);
    return $val;
}

function watermark_image($target, $wtrmrk_file, $newcopy)
{
    $watermark = imagecreatefrompng($wtrmrk_file);
    imagealphablending($watermark, false);
    imagesavealpha($watermark, true);
    $img = imagecreatefromjpeg($target);
    $img_w = imagesx($img);
    $img_h = imagesy($img);
    $wtrmrk_w = imagesx($watermark);
    $wtrmrk_h = imagesy($watermark);
    $dst_x = ($img_w / 2) - ($wtrmrk_w / 2); // For centering the watermark on any image
    $dst_y = ($img_h / 2) - ($wtrmrk_h / 2); // For centering the watermark on any image
    @imagecopy($img, $watermark, $dst_x, $dst_y, 0, 0, $wtrmrk_w, $wtrmrk_h);
    @imagejpeg($img, $newcopy, 100);
    @imagedestroy($img);
    @imagedestroy($watermark);
}

function sqii($val)
{
    $symbol = array(',', '', '', "'", '"', '!', '?', '[', ']', '+', '=', '#', '\x00', '\n', '\r', '\x1a', '&', '$');
    $val = preg_replace("/<.*?>/", "", $val);
    $val = str_replace($symbol, "", $val);
    return $val;
}
function isValidEmail($email)
{
    if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) return false;
    return true;
}
function SafeInput($val)
{
    return $val;
}

function paginationotherproduct($site_url, $page, $count_result, $show_per_page)
{
    if ($count_result > $show_per_page) {
        if (isset($page)) {
            $pn = $page;
        } else {
            $pn = 1;
        }
        $count_result = ceil($count_result / $show_per_page);
?>

        <div class="col-12 col-md-12">
            <div class="pagination-collection">
                <?php
                if ($page != 'all') {
                    if (($page >= 1) && ($pn > 1)) {
                ?>
                        <a class="previous" id="prev-page" href="<?= $site_url . '&page=' . ($pn - 1); ?>" title="Previous Page"><span class="icon-chevron-right"></span></a>
                    <?php }
                    if (($pn - 1) > 1) {
                    ?>
                        <a href='<?= $site_url . '&page=1'; ?>'>
                            <div class='page-a-link'> 1 </div>
                        </a>
                        <div class='page-before-after'>...</div>
                    <?php
                    }
                    for ($i = ($pn - 1); $i <= ($pn + 1); $i++) {
                        if ($i < 1)
                            continue;
                        if ($i > $count_result)
                            break;
                        if ($i == $pn) {
                            $class = "active";
                        } else {
                            $class = "page-a-link";
                        }
                    ?>
                        <a href='<?= $site_url . '&page=' . $i; ?>'>
                            <div class='<?php echo $class; ?>'><?php echo $i; ?></div>
                        </a>
                    <?php
                    }
                    if (($count_result - ($pn + 1)) >= 1) {
                    ?>
                        <div class='page-before-after'>...</div>
                    <?php
                    }
                    if (($count_result - ($pn + 1)) > 0) {
                        if ($pn == $count_result) {
                            $class = "active";
                        } else {
                            $class = "page-a-link";
                        }
                    ?>
                        <a href='<?= $site_url . '&page=' . $count_result; ?>'>
                            <div class='<?php echo $class; ?>'><?php echo $count_result; ?></div>
                        </a>
                    <?php
                    }
                    ?>
                    <?php
                    if (($page >= 1) && ($pn < $count_result)) {
                    ?>
                        <a class="next" id="next-page" href="<?= $site_url . '&page=' . ($pn + 1); ?>" title="Next Page"><span class="icon-chevron-left"></span></a>
                    <?php
                    }
                    ?>
                <?php } ?>
            </div>
        </div>

<?php
    }
}
function title($value)
{
    $value = str_replace("  ", " ", $value);
    $value = str_replace(".", "-", $value);
    $value = str_replace("_", "-", $value);
    $value = str_replace("'", "-", $value);
    $value = str_replace('"', "-", $value);
    $value = str_replace("/", "-", $value);
    $value = str_replace('{', "-", $value);
    $value = str_replace("}", "-", $value);
    $value = str_replace("@", "-", $value);
    $value = str_replace(".", "-", $value);
    $value = str_replace(" \ ", "-", $value);
    $value = str_replace("|", "-", $value);
    $value = str_replace(",", "", $value);
    $value = str_replace("+", "", $value);
    $value = str_replace(" ", "-", $value);
    $value = str_replace("----", "-", $value);
    $value = str_replace("---", "-", $value);
    $value = str_replace("--", "-", $value);


    return $value;
}
function grade($val)
{
    $arr = array('a', 'b', 'h', 'v', 'c', 'd', 'z', 'e', 'f', 'g');
    return $arr[$val];
}
function randomPassword()
{
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}





function Code($orginalfilename = "", $Extension = "no", $count = 0, $preText = "")
{
    // Make file name used in uploads, etc.
    if (($count == 0) || ($count > 10)) $count = 9;

    if ($orginalfilename) {
        $outPut = substr(md5(rand(1000, 99999) . $orginalfilename), 0, $count);
    } else {
        $outPut = substr(md5(rand(10000, 999999999)), 0, $count);
    }
    $outPut = strtoupper($outPut);
    if ($Extension != "no") {
        $outPut .= "." . $this->FileExtension($orginalfilename);
    }
    return $preText . $outPut;
}

function echocsv($fields)
{
    $separator = '';
    foreach ($fields as $field) {
        if (preg_match('/\\r|\\n|,|"/', $field)) {
            $field = '"' . str_replace('"', '""', $field) . '"';
        }
        echo $separator . $field;
        $separator = ',';
        $separator = iconv('UTF-8', 'UTF-8', $separator);
    }
    echo "\r\n";
}

function limit_words($string, $word_limit)
{
    $string = strip_tags($string);
    $words = explode(" ", $string);
    return implode(" ", array_splice($words, 0, $word_limit));
}

function protect($str)
{
    $str = sqi($str);
    return $str;
}

function custom_file_exists($filePath)
{
    if ((is_file($filePath)) && (file_exists($filePath))) {
        return true;
    }
    return false;
}


function imjection($text)
{
    $text = str_replace("'", "/**-**/", $text);
    return $text;
}
function exjection($text)
{
    $text = str_replace("/**-**/", "'", $text);
    return $text;
}
function myip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
function check_status($val)
{
    global $lang;
    for ($i = 1; $i <= 8; $i++) {
        if ($val == $i) {
            $this_val = $lang[487 + $i];
        }
    }
    return $this_val;
}
function check_name($val)
{
    $result = Query("select * from tbl_member where tbl_member_id ='$val' ");
    $read = mysqli_fetch_array($result);
    if ($read['tbl_member_lang'] == 'fa') {
        return $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'];
    } else {
        return $read['tbl_member_fnameen'] . ' ' . $read['tbl_member_lnameen'];
    }
}
function toPersianNum($number)
{
    if (!empty($number)) {
        $number = str_replace("1", "۱", $number);
        $number = str_replace("2", "۲", $number);
        $number = str_replace("3", "۳", $number);
        $number = str_replace("4", "۴", $number);
        $number = str_replace("5", "۵", $number);
        $number = str_replace("6", "۶", $number);
        $number = str_replace("7", "۷", $number);
        $number = str_replace("8", "۸", $number);
        $number = str_replace("9", "۹", $number);
        $number = str_replace("0", "۰", $number);
        $number = str_replace(",", ",", $number);
        return $number;
    } else {
        return '۰';
    }
}
function toEnglishNum($number)
{
    if (!empty($number)) {
        $number = str_replace("۱", "1", $number);
        $number = str_replace("۲", "2", $number);
        $number = str_replace("۳", "3", $number);
        $number = str_replace("۴", "4", $number);
        $number = str_replace("۵", "5", $number);
        $number = str_replace("۶", "6", $number);
        $number = str_replace("۷", "7", $number);
        $number = str_replace("۸", "8", $number);
        $number = str_replace("۹", "9", $number);
        $number = str_replace("۰", "0", $number);
        return $number;
    } else {
        return '0';
    }
}
function  rank($val)
{
    global $lang;
    if (isset($val) && $val == 1) {
        return $lang[545];
    }
    if (isset($val) && $val == 2) {
        return $lang[546];
    }
    if (isset($val) && $val == 3) {
        return $lang[547];
    }
}
function  ticket($val)
{
    global $lang;
    if (isset($val) && $val == 0) {
        return $lang[603];
    }
    if (isset($val) && $val == 1) {
        return $lang[604];
    }
}
function generateContent($input)
{
    if (preg_match('/\[start-acardeon\](.*?)\[end-acardeon\]/s', $input, $accordionMatch)) {
        $beforeAccordion = strtok($input, '[start-tab]') ?: '';
        $afterAccordion = strstr($input, '[end-acardeon]') ?: '';
        $afterAccordion = substr($afterAccordion, strlen('[end-acardeon]'));

        $accordionHtml = '';
        preg_match_all('/\[start-box\](.*?)\/\/\/(.*?)\[end-box\]/s', $accordionMatch[1], $matches, PREG_SET_ORDER);
        foreach ($matches as $key => $match) {
            $title = trim(strip_tags($match[1]));
            $text = trim($match[2]);
            $show = $key === 0 ? 'show' : '';
            $aria = $key === 0 ? 'true' : 'false';
            $collapsed = $key === 0 ? '' : 'collapsed';

            $accordionHtml .= "<div class='accordion-item'>";
            $accordionHtml .= "<h2 class='accordion-header'><button class='accordion-button $collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#collapse$key' aria-expanded='$aria' aria-controls='collapse$key'>{$title}</button></h2>";
            $accordionHtml .= "<div id='collapse$key' class='accordion-collapse collapse $show' data-bs-parent='#accordion'><div class='accordion-body'>{$text}</div></div>";
            $accordionHtml .= "</div>";
        }
        return $beforeAccordion . "<div class='accordion' id='accordion'>{$accordionHtml}</div>" . $afterAccordion;
    } elseif (preg_match('/\[start-tabset\](.*?)\[end-tabset\]/s', $input, $tabsetMatch)) {
        $beforeTabset = strtok($input, '[start-tabset]') ?: '';
        $afterTabset = strstr($input, '[end-tabset]') ?: '';
        $afterTabset = substr($afterTabset, strlen('[end-tabset]'));

        $tabHtml = '<ul class="nav nav-tabs" id="myTab" role="tablist">';
        $tabContentHtml = '<div class="tab-content" id="myTabContent">';

        preg_match_all('/\[start-box\](.*?)\/\/\/(.*?)\[end-box\]/s', $tabsetMatch[1], $matches, PREG_SET_ORDER);
        foreach ($matches as $key => $match) {
            $title = trim(strip_tags($match[1]));
            $text = trim($match[2]);
            $active = $key === 0 ? 'active' : '';

            $tabHtml .= "<li class='nav-item' role='presentation'><button class='nav-link $active' id='tab$key-tab' data-bs-toggle='tab' data-bs-target='#tab$key' type='button' role='tab' aria-controls='tab$key' aria-selected='" . ($key === 0 ? 'true' : 'false') . "'>{$title}</button></li>";
            $tabContentHtml .= "<div class='tab-pane fade $active show' id='tab$key' role='tabpanel' aria-labelledby='tab$key-tab'>{$text}</div>";
        }
        $tabHtml .= '</ul>';
        $tabContentHtml .= '</div>';

        return $beforeTabset . "<div class='tabsetion'>{$tabHtml}{$tabContentHtml}</div>" . $afterTabset;
    } else {
        return $input;
    }
}
