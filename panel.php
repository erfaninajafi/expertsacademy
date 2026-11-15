<?php
include './includer.php';
if (!empty($do) && $do == 'show-content') {
    $_SESSION['show-content'] = $code;
}
if (empty($_SESSION['userid'])) {
    if (isset($do) && $do == 'show-content') {
        $_SESSION['router'] = 'show-content';
        header("location: $site_url/$lng/register/login/");
        exit();
    } else {
        header("location: $site_url/$lng/register/login/");
        exit();
    }
} else {
    $user_id = $_SESSION['userid'];
}
$resultMember = Query("select tbl_member_color,tbl_member_fluid,tbl_member_navbarStyle,tbl_member_model from tbl_member where tbl_member_id = '$user_id' ");
$readMember = mysqli_fetch_array($resultMember);
$themecolor = $readMember['tbl_member_color'];
$fluid = $readMember['tbl_member_fluid'];
$userModel = $readMember['tbl_member_model'];
$navbarStyle = $readMember['tbl_member_navbarStyle'];
@$pagetitle .= ' - ' . $lang[126];
if (!isset($do)) {
    $title = $lang[485];
    $active[1] = 'active';
    @$pagetitle = ' - ' . $lang[485];
} else if (isset($do) && $do == 'editprofile') {
    $title = $lang[382];
    $active[3] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'examcourse') {
    $title = $lang[476];
    $active[7] = 'active';
    @$pagetitle = ' - ' . $lang[476];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'success') {
    $title = $lang[475];
    $active[7] = 'active';
    @$pagetitle = ' - ' . $lang[475];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'courseparent') {
    $thisCode = explode("-", $code);
    $resultClass = Query("SELECT tbl_class_title From tbl_class where tbl_class_lng='$lng' and tbl_class_code ='$thisCode[0]' ");
    if (mysqli_num_rows($resultClass) <= 0) {
        header("location: $site_url/$lng/mypanel/");
        exit();
    }
    $readClass = mysqli_fetch_array($resultClass);
    $title = $readClass['tbl_class_title'];
    $active[7] = 'active';
    @$pagetitle = ' - ' . $title;
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'mycourseview') {
    $resultClass = Query("SELECT tbl_class_title From tbl_class where tbl_class_lng='$lng' and tbl_class_code ='$code' ");
    if (mysqli_num_rows($resultClass) <= 0) {
        header("location: $site_url/$lng/mypanel/");
        exit();
    }
    $readClass = mysqli_fetch_array($resultClass);
    $title = $readClass['tbl_class_title'];
    $active[7] = 'active';
    @$pagetitle = ' - ' . $title;
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'course') {

    $thisCode = explode("-", $code);

    $resultcourse = Query("SELECT tbl_course_title,tbl_course_class From tbl_course where tbl_course_code='$thisCode[0]' and tbl_course_lng='$lng'  ORDER BY tbl_course_order DESC , tbl_course_id DESC limit 0,1");
    if (mysqli_num_rows($resultcourse) <= 0) {
        header("location: $site_url/$lng/mypanel/");
        exit();
    }
    $readcourse = mysqli_fetch_array($resultcourse);
    $class = $readcourse['tbl_course_class'];

    $result_course_parent = Query("SELECT tbl_courseparent_class,tbl_courseparent_title From tbl_courseparent where tbl_courseparent_code ='$class'");
    $read_course_parent = mysqli_fetch_array($result_course_parent);

    $resultClass = Query("SELECT tbl_class_code,tbl_class_title From tbl_class where tbl_class_lng='$lng' and tbl_class_code ='$read_course_parent[tbl_courseparent_class]' ");
    $readClass = mysqli_fetch_array($resultClass);

    $title = $readcourse['tbl_course_title'];
    $active[7] = 'active';
    @$pagetitle = ' - ' . $title;
    $thisCode = explode("-", $code);
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/courseparent/' . $readClass['tbl_class_code'] . '-' . $thisCode[1] . '/">' . $readClass['tbl_class_title'] . '</a></span>
    <span><a href="#">' . $read_course_parent['tbl_courseparent_title'] . '</a></span>' . $title;
} else if (isset($do) && $do == 'courseview') {

    $thisCode = $code;

    $resultcourse = Query("SELECT tbl_course_title,tbl_course_class From tbl_course where tbl_course_code='$thisCode' and tbl_course_lng='$lng'  ORDER BY tbl_course_order DESC , tbl_course_id DESC limit 0,1");
    if (mysqli_num_rows($resultcourse) <= 0) {
        header("location: $site_url/$lng/mypanel/");
        exit();
    }
    $readcourse = mysqli_fetch_array($resultcourse);
    $class = $readcourse['tbl_course_class'];

    $result_course_parent = Query("SELECT tbl_courseparent_class,tbl_courseparent_title From tbl_courseparent where tbl_courseparent_code ='$class'");
    $read_course_parent = mysqli_fetch_array($result_course_parent);

    $resultClass = Query("SELECT tbl_class_code,tbl_class_title From tbl_class where tbl_class_lng='$lng' and tbl_class_code ='$read_course_parent[tbl_courseparent_class]' ");
    $readClass = mysqli_fetch_array($resultClass);

    $title = $readcourse['tbl_course_title'];
    $active[7] = 'active';
    @$pagetitle = ' - ' . $title;
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/mycourseview/' . $readClass['tbl_class_code'] . '/">' . $readClass['tbl_class_title'] . '</a></span>
    <span><a href="#">' . $read_course_parent['tbl_courseparent_title'] . '</a></span>' . $title;
}
//  else if (isset($do) && $do == 'resume') {
//     $title = $lang[411];
//     $active[6] = 'active';
//     @$pagetitle = ' - ' . $lang[382];
//     $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
// } 
else if (isset($do) && $do == 'addedu') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'addskill') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'addjob') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'editedu') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'editjob') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'editskill') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'deleteedu') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'deleteitemresume') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'deletejob') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'deleteskill') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'deletejobconfirm') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'deleteeduconfirm') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'deleteitemresumeconfirm') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'deleteskillconfirm') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'resumeupdate') {
    $title = $lang[411];
    $active[6] = 'active';
    @$pagetitle = ' - ' . $lang[382];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'editprofileimg') {
    $title = $lang[1178];
    $active[4] = 'active';
    @$pagetitle = ' - ' . $lang[1178];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'ticket') {
    $title = $lang[598];
    $active[2] = 'active';
    @$pagetitle = ' - ' . $lang[598];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'addticket') {
    $title = $lang[599];
    $active[2] = 'active';
    @$pagetitle = ' - ' . $lang[599];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'sendticket') {
    $title = $lang[599];
    $active[2] = 'active';
    @$pagetitle = ' - ' . $lang[599];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/ticket/">' . $lang[599] . '</a></span>' . $title;
} else if (isset($do) && $do == 'viewticket') {
    $title = $lang[87];
    $active[2] = 'active';
    @$pagetitle = ' - ' . $lang[87];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/ticket/">' . $lang[599] . '</a></span>' . $title;
} else if (isset($do) && $do == 'sendreplayticket') {
    $title = $lang[87];
    $active[2] = 'active';
    @$pagetitle = ' - ' . $lang[87];
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/ticket/">' . $lang[599] . '</a></span>' . $title;
} else if (isset($do) && $do == 'logout') {
} else if (isset($do) && $do == 'document') {
    $title = $lang[1113];
    $active[5] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'deletedocument') {
    $title = $lang[1114];
    $active[5] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/document/">' . $lang[1113] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'adddocument') {
    $title = $lang[1115];
    $active[5] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span><span><a href="' . $site_url . $lng . '/mypanel/document/">' . $lang[1113] . '</a></span>' . $title;
} else if (isset($do) && $do == 'adddocumentconfirm') {
    $title = $lang[1113];
    $active[5] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span><span><a href="' . $site_url . $lng . '/mypanel/document/">' . $lang[1113] . '</a></span>' . $title;
} else if (isset($do) && $do == 'deletedocumentconfirm') {
    $title = $lang[1113];
    $active[5] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span><span><a href="' . $site_url . $lng . '/mypanel/document/">' . $lang[1113] . '</a></span>' . $title;
} else if (isset($do) && $do == 'skill') {
    $title = $lang[718];
    $active[7] = 'active';
    $result = Query("SELECT tbl_memberskill_planname From tbl_memberskill where tbl_memberskill_id ='$code' and tbl_memberskill_user='$user_id' ");
    $read =  mysqli_fetch_array($result);
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span><span><a href="' . $site_url . $lng . '/mypanel/skilllist/">' . $lang[718] . '</a></span>' . $read['tbl_memberskill_planname'];
} else if (isset($do) && $do == 'skillview') {
    $title = $lang[597];
    $active[7] = 'active';
    $result = Query("SELECT tbl_memberskill_planname From tbl_memberskill where tbl_memberskill_contract ='$code' and tbl_memberskill_user='$user_id' ");
    $read =  mysqli_fetch_array($result);
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span><span><a href="' . $site_url . $lng . '/mypanel/skilllist/">' . $lang[718] . '</a></span>' . $read['tbl_memberskill_planname'];
} else if (isset($do) && $do == 'skilldetail') {
    $title = $lang[718];
    $active[7] = 'active';
    $result = Query("SELECT tbl_plan_contract From tbl_plan where tbl_plan_skill ='$code' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ORDER BY tbl_plan_order DESC limit 0,1 ");
    $read = mysqli_fetch_array($result);
    if (mysqli_num_rows($result) > 0) {
        $contract = $read['tbl_plan_contract'];
        $resultPlan = Query("SELECT tbl_memberskill_planname,tbl_memberskill_id From tbl_memberskill where tbl_memberskill_contract ='$contract' and tbl_memberskill_user='$user_id'  ");
        $readPlan = mysqli_fetch_array($resultPlan);
        $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$code' and tbl_categories_lng='$lng' ");
        $readSkill = mysqli_fetch_array($resultSkill);
        $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilllist/">' . $lang[718] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skill/' . $readPlan['tbl_memberskill_id'] . '/">' . $readPlan['tbl_memberskill_planname'] . '</a></span>
    ' . $readSkill['tbl_categories_title'];
    } else {
        $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>';
    }
} else if (isset($do) && $do == 'skilllist') {
    $title = $lang[718];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'skilllistarchive') {
    $title = $lang[1364];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'assessment') {
    $title = $lang[1295];
    $active[9] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'checkskill') {
    $title = $lang[730];
    $active[7] = 'active';
    $result = Query("SELECT tbl_plan_skill,tbl_plan_contract From tbl_plan where tbl_plan_code='$code' and tbl_plan_lng='$lng' and tbl_plan_menti='$user_id' and tbl_plan_delete ='0' ");
    $read = mysqli_fetch_array($result);
    $skill = $read['tbl_plan_skill'];
    $contract = $read['tbl_plan_contract'];
    $resultPlan = Query("SELECT tbl_memberskill_planname,tbl_memberskill_id From tbl_memberskill where tbl_memberskill_contract ='$contract' and tbl_memberskill_user='$user_id'  ");
    $readPlan = mysqli_fetch_array($resultPlan);
    $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$skill' and tbl_categories_lng='$lng' ");
    $readSkill = mysqli_fetch_array($resultSkill);
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilllist/">' . $lang[718] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skill/' . $readPlan['tbl_memberskill_id'] . '/">' . $readPlan['tbl_memberskill_planname'] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilldetail/' . $contract . '/' . $skill . '/">' . $readSkill['tbl_categories_title'] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'interview') {
    $title = $lang[856];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'interviewupdate') {
    $title = $lang[856];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'coach') {
    $title = $lang[713];
    $active[7] = 'active';

    $result = Query("SELECT tbl_plan_skill,tbl_plan_contract From tbl_plan where tbl_plan_code='$code' and tbl_plan_lng='$lng' and tbl_plan_menti='$user_id' and tbl_plan_delete ='0' ");
    $read = mysqli_fetch_array($result);
    $skill = $read['tbl_plan_skill'];
    $contract = $read['tbl_plan_contract'];
    $resultPlan = Query("SELECT tbl_memberskill_planname,tbl_memberskill_id From tbl_memberskill where tbl_memberskill_contract ='$contract' and tbl_memberskill_user='$user_id'  ");
    $readPlan = mysqli_fetch_array($resultPlan);
    $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$skill' and tbl_categories_lng='$lng' ");
    $readSkill = mysqli_fetch_array($resultSkill);
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilllist/">' . $lang[718] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skill/' . $readPlan['tbl_memberskill_id'] . '/">' . $readPlan['tbl_memberskill_planname'] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilldetail/' . $contract . '/' . $skill . '/">' . $readSkill['tbl_categories_title'] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'coachmessage') {
    $title = $lang[378];
    $active[7] = 'active';

    $result = Query("SELECT tbl_plan_skill,tbl_plan_contract From tbl_plan where tbl_plan_code='$code' and tbl_plan_lng='$lng' and tbl_plan_menti='$user_id' and tbl_plan_delete ='0' ");
    $read = mysqli_fetch_array($result);
    $skill = $read['tbl_plan_skill'];
    $contract = $read['tbl_plan_contract'];
    $resultPlan = Query("SELECT tbl_memberskill_planname,tbl_memberskill_id From tbl_memberskill where tbl_memberskill_contract ='$contract' and tbl_memberskill_user='$user_id'  ");
    $readPlan = mysqli_fetch_array($resultPlan);
    $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$skill' and tbl_categories_lng='$lng' ");
    $readSkill = mysqli_fetch_array($resultSkill);
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilllist/">' . $lang[718] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skill/' . $readPlan['tbl_memberskill_id'] . '/">' . $readPlan['tbl_memberskill_planname'] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilldetail/' . $contract . '/' . $skill . '/">' . $readSkill['tbl_categories_title'] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'sendcoachmessage') {
    $title = $lang[378];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'submitcoachmessage') {
    $title = $lang[378];
    $active[7] = 'active';

    $result = Query("SELECT tbl_plan_skill,tbl_plan_contract From tbl_plan where tbl_plan_code='$code' and tbl_plan_lng='$lng' and tbl_plan_menti='$user_id' and tbl_plan_delete ='0' ");
    $read = mysqli_fetch_array($result);
    $skill = $read['tbl_plan_skill'];
    $contract = $read['tbl_plan_contract'];
    $resultPlan = Query("SELECT tbl_memberskill_planname,tbl_memberskill_id From tbl_memberskill where tbl_memberskill_contract ='$contract' and tbl_memberskill_user='$user_id'  ");
    $readPlan = mysqli_fetch_array($resultPlan);
    $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$skill' and tbl_categories_lng='$lng' ");
    $readSkill = mysqli_fetch_array($resultSkill);
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilllist/">' . $lang[718] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skill/' . $readPlan['tbl_memberskill_id'] . '/">' . $readPlan['tbl_memberskill_planname'] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilldetail/' . $contract . '/' . $skill . '/">' . $readSkill['tbl_categories_title'] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'certificate') {
    $title = $lang[851];
    $active[9] = 'active';
} else if (isset($do) && $do == 'cerview') {
    $title = $lang[851];
    $active[9] = 'active';
} else if (isset($do) && $do == 'skillmessage') {
    $title = $lang[857];
    $active[8] = 'active';

    $result = Query("SELECT tbl_plan_skill,tbl_plan_contract From tbl_plan where tbl_plan_code='$code' and tbl_plan_lng='$lng' and tbl_plan_menti='$user_id' and tbl_plan_delete ='0' ");
    $read = mysqli_fetch_array($result);
    $skill = $read['tbl_plan_skill'];
    $contract = $read['tbl_plan_contract'];
    $resultPlan = Query("SELECT tbl_memberskill_planname,tbl_memberskill_id From tbl_memberskill where tbl_memberskill_contract ='$contract' and tbl_memberskill_user='$user_id'  ");
    $readPlan = mysqli_fetch_array($resultPlan);
    $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$skill' and tbl_categories_lng='$lng' ");
    $readSkill = mysqli_fetch_array($resultSkill);
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilllist/">' . $lang[718] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skill/' . $readPlan['tbl_memberskill_id'] . '/">' . $readPlan['tbl_memberskill_planname'] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilldetail/' . $contract . '/' . $skill . '/">' . $readSkill['tbl_categories_title'] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'viewcoachmessage') {
    $title = $lang[378];
    $active[7] = 'active';

    $resultMessage = Query("SELECT tbl_coachmessage_plan From tbl_coachmessage where (tbl_coachmessage_note='0' or tbl_coachmessage_note is NULL) and tbl_coachmessage_code='$code' and tbl_coachmessage_user='$user_id' and tbl_coachmessage_delete ='0' ");
    $readMessage = mysqli_fetch_array($resultMessage);
    $plan = $readMessage['tbl_coachmessage_plan'];

    $result = Query("SELECT tbl_plan_skill,tbl_plan_contract From tbl_plan where tbl_plan_code='$plan' and tbl_plan_lng='$lng' and tbl_plan_menti='$user_id' and tbl_plan_delete ='0' ");
    $read = mysqli_fetch_array($result);
    $skill = $read['tbl_plan_skill'];
    $contract = $read['tbl_plan_contract'];
    $resultPlan = Query("SELECT tbl_memberskill_planname,tbl_memberskill_id From tbl_memberskill where tbl_memberskill_contract ='$contract' and tbl_memberskill_user='$user_id'  ");
    $readPlan = mysqli_fetch_array($resultPlan);
    $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$skill' and tbl_categories_lng='$lng' ");
    $readSkill = mysqli_fetch_array($resultSkill);
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilllist/">' . $lang[718] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skill/' . $readPlan['tbl_memberskill_id'] . '/">' . $readPlan['tbl_memberskill_planname'] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilldetail/' . $contract . '/' . $skill . '/">' . $readSkill['tbl_categories_title'] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'viewskillmessage') {
    $title = $lang[857];
    $active[8] = 'active';

    $resultMessage = Query("SELECT tbl_skillmessage_plan From tbl_skillmessage where (tbl_skillmessage_note='0' or tbl_skillmessage_note is NULL) and tbl_skillmessage_code='$code' and tbl_skillmessage_user='$user_id' and tbl_skillmessage_delete ='0' ");
    $readMessage = mysqli_fetch_array($resultMessage);
    $plan = $readMessage['tbl_skillmessage_plan'];

    $result = Query("SELECT tbl_plan_skill,tbl_plan_contract From tbl_plan where tbl_plan_code='$plan' and tbl_plan_lng='$lng' and tbl_plan_menti='$user_id' and tbl_plan_delete ='0' ");
    $read = mysqli_fetch_array($result);
    $skill = $read['tbl_plan_skill'];
    $contract = $read['tbl_plan_contract'];
    $resultPlan = Query("SELECT tbl_memberskill_planname,tbl_memberskill_id From tbl_memberskill where tbl_memberskill_contract ='$contract' and tbl_memberskill_user='$user_id'  ");
    $readPlan = mysqli_fetch_array($resultPlan);
    $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$skill' and tbl_categories_lng='$lng' ");
    $readSkill = mysqli_fetch_array($resultSkill);
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilllist/">' . $lang[718] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skill/' . $readPlan['tbl_memberskill_id'] . '/">' . $readPlan['tbl_memberskill_planname'] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilldetail/' . $contract . '/' . $skill . '/">' . $readSkill['tbl_categories_title'] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'submitskillmessage') {
    $title = $lang[857];
    $active[8] = 'active';

    $result = Query("SELECT tbl_plan_skill,tbl_plan_contract From tbl_plan where tbl_plan_code='$code' and tbl_plan_lng='$lng' and tbl_plan_menti='$user_id' and tbl_plan_delete ='0' ");
    $read = mysqli_fetch_array($result);
    $skill = $read['tbl_plan_skill'];
    $contract = $read['tbl_plan_contract'];
    $resultPlan = Query("SELECT tbl_memberskill_planname,tbl_memberskill_id From tbl_memberskill where tbl_memberskill_contract ='$contract' and tbl_memberskill_user='$user_id'  ");
    $readPlan = mysqli_fetch_array($resultPlan);
    $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$skill' and tbl_categories_lng='$lng' ");
    $readSkill = mysqli_fetch_array($resultSkill);
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilllist/">' . $lang[718] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skill/' . $readPlan['tbl_memberskill_id'] . '/">' . $readPlan['tbl_memberskill_planname'] . '</a></span>
    <span><a href="' . $site_url . $lng . '/mypanel/skilldetail/' . $contract . '/' . $skill . '/">' . $readSkill['tbl_categories_title'] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'sendskillmessage') {
    $title = $lang[857];
    $active[8] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'message') {
    $title = $lang[857];
    $active[8] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'coachreplayticket') {
    $title = $lang[378];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'skillreplayticket') {
    $title = $lang[1123];
    $active[8] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'check') {
    $title = $lang[491];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'opplatform') {
    $title = $lang[1120];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'opsend') {
    $title = $lang[1120];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'opmentor') {
    $title = $lang[1121];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'opteacher') {
    $title = $lang[1381];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'opteachersend') {
    $title = $lang[1381];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'opmentorsend') {
    $title = $lang[1121];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'opcoach') {
    $title = $lang[1380];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'opcoachsend') {
    $title = $lang[1380];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'exam') {
    $title = $lang[491];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'examskill') {
    $title = $lang[491];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'page') {
    $resultPages = Query("Select * from tbl_pages Where tbl_pages_code = '$code' and tbl_pages_lng='$lng'  ");
    $readPages = mysqli_fetch_array($resultPages);
    $title = $readPages['tbl_pages_title'];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'showanswer') {
    $title = $lang[1353];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'showinterview') {
    $title = $lang[1354];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>
    ' . $title;
} else if (isset($do) && $do == 'library') {
    $title = $lang[1344];
    $active[7] = 'active';
    //$navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'library-cat') {
    $title = $lang[1344];
    $active[7] = 'active';
    //$navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'library-filter') {
    $title = $lang[1344];
    $active[7] = 'active';
    //$navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'search') {
    $title = $lang[77];
    $active[7] = 'active';
    //$navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'show-content') {
    $title = $lang[1402];
    $active[7] = 'active';

    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'mycontentview') {
    $title = $lang[1402];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'mycontent') {
    $title = $lang[1411];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else if (isset($do) && $do == 'mycourse') {
    $title = $lang[1417];
    $active[7] = 'active';
    $navigation = '<span><a href="' . $site_url . $lng . '/mypanel/">' . $lang[485] . '</a></span>' . $title;
} else {
    header("Location : $site_url/404.php");
    exit();
}
include './header_panel.inc.php';

echo '<div class="modal fade" id="result" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . $lang[484] . '</button>
        </div>
      </div>
    </div>
  </div>';


// Do not Set
if (!isset($do)) {



    $date = strtotime(date("Y/m/d"));



    $resultPlan = Query("SELECT * From tbl_plan where tbl_plan_menti='$user_id' and tbl_plan_lng='$language_default' and tbl_plan_delete='0'  ");
    while ($readPlan = mysqli_fetch_array($resultPlan)) {
        $endate = strtotime($readPlan['tbl_plan_dateenglish']);
        if (($endate - $date) <= 259200) {
            //echo $readPlan['tbl_plan_dateenglish'].'<br>';
            // $all = ' 
            // <div class="alert alert-danger" role="alert"><strong>'.$lang[855].'</strong>' . $lang[714] . '</div>';

            $all = '<div class="alert alert-danger border-2 d-flex align-items-center" role="alert">
        <div class="bg-danger me-3 icon-item"><span class="fas fa-times-circle text-white fs-3"></span></div>
        <p class="mb-0 flex-1">' . $lang[855] . '</strong>' . $lang[714] . '</p>
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';

            break;
        } else {
            $all = '';
        }
    }

    $resultQuestion = Query("select * from tbl_memberquestion where tbl_memberquestion_check='0' and tbl_memberquestion_admincheck='0'  and tbl_memberquestion_userid='$user_id' ");
    if (mysqli_num_rows($resultQuestion) > 0) {
        echo '<div class="alert alert-danger border-2 d-flex align-items-center" role="alert">
        <div class="bg-danger me-3 icon-item"><span class="fas fa-times-circle text-white fs-3"></span></div>
        <p class="mb-0 flex-1">' . $lang[855] . '</strong>' . $lang[1330] . '</p>
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }

    $resultInterview = Query("SELECT * From tbl_interview where ( tbl_interview_reportdate='' or tbl_interview_reportdate is NULL ) and (tbl_interview_model='0' or tbl_interview_model is NULL ) and tbl_interview_menti='$user_id' and tbl_interview_lng='$lng' and tbl_interview_delete='0'  ");
    if (mysqli_num_rows($resultInterview) > 0) {

        echo '<div class="alert alert-danger border-2 d-flex align-items-center" role="alert">
        <div class="bg-danger me-3 icon-item"><span class="fas fa-times-circle text-white fs-3"></span></div>
        <p class="mb-0 flex-1">' . $lang[855] . '</strong>' . $lang[1332] . '</p>
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }

    //echo '<div class="alert alert-danger" role="alert"><strong>'.$lang[855].'</strong>' . $lang[714] . '</div>';
?>
    <div class="row">
        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card h-180">
                        <?php
                        $result_profile = Query("Select * from tbl_member where tbl_member_id = '$user_id' ");
                        $read_profile = mysqli_fetch_array($result_profile);
                        $lastLogin = (!empty($read_profile['tbl_member_lastlogin']) ? explode(" ", $read_profile['tbl_member_lastlogin']) : '');
                        $importDate = (!empty($read_profile['tbl_member_importdate']) ? explode(" ", $read_profile['tbl_member_importdate']) : '');
                        ?>
                        <div class="card-body d-flex gap-3 flex-column flex-sm-row align-items-center index-profile"><img class="rounded-3" src="<?= (!empty($read_profile['tbl_member_profile']) ? $site_url . 'upload/profile/' . $read_profile['tbl_member_profile'] : $site_url . 'assets/img/no-image.jpg') ?>" alt="" width="112">
                            <table class="table table-borderless fs--1 fw-medium mb-0">

                                <tbody>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[92] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none" href="tel:<?= $read_profile['tbl_member_mobile'] ?>"><?= $read_profile['tbl_member_fname'] . ' ' . $read_profile['tbl_member_lname'] ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[1348] ?></td>
                                        <td class="p-1"><a class="text-600 text-decoration-none" href="tel:<?= $read_profile['tbl_member_mobile'] ?>"><?= toPersianNum($read_profile['tbl_member_mobile']) ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[1285] ?></td>
                                        <td class="p-1 text-600"><?= (!empty($lastLogin[0]) ? toPersianNum($lastLogin[0]) : '') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" style="width: 35%;"><?= $lang[1286] ?></td>
                                        <td class="p-1 text-600"><?= (!empty($importDate[0]) ? toPersianNum($importDate[0]) : toPersianNum($read_profile['tbl_member_regdate'])) ?></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2">
            <div class="card h-180">
                <div class="bg-holder bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-1.png);">
                </div>
                <?php
                $resultPages = Query("Select * from tbl_pages Where tbl_pages_code = 'FC137F0' and tbl_pages_lng='$lng'  ");
                $readPages = mysqli_fetch_array($resultPages);
                $textPages = explode("[short]", $readPages['tbl_pages_text']);
                ?>
                <div class="card-body position-relative pages">
                    <h5 class="text-warning"><?= $readPages['tbl_pages_title'] ?></h5>
                    <p class="fs--1 mb-0"><?= $textPages[0] ?> <?= (!empty($textPages[0]) ? '<a href="' . $site_url . $lng . '/mypanel/page/' . $readPages['tbl_pages_code'] . '/">' . $lang[1342] . '</a>' : '') ?></p>
                </div>
            </div>
        </div>

    </div>

    <?php
    if ($userModel == '0') {
        $date = strtotime(date("Y/m/d"));
        $resultPlan = Query("SELECT * From tbl_plan where tbl_plan_menti='$user_id' and tbl_plan_lng='$language_default' and tbl_plan_delete='0'  ");
        while ($readPlan = mysqli_fetch_array($resultPlan)) {
            $endate = strtotime($readPlan['tbl_plan_dateenglish']);
            if (($endate - $date) <= 259200) {
                //echo $readPlan['tbl_plan_dateenglish'].'<br>';
                //     $all = ' <div class="col-12 col-lg-12 col-md-12"><div class="alert alert-warning">
                //     <a href="#" class="close" data-dismiss="alert">&times;</a>
                //     <strong>'.$lang[855].'</strong>' . $lang[714] . '
                // </div></div>';

                $all = '<div class="alert alert-danger border-2 d-flex align-items-center" role="alert">
    <div class="bg-danger me-3 icon-item"><span class="fas fa-times-circle text-white fs-3"></span></div>
    <p class="mb-0 flex-1">' . $lang[855] . '</strong>' . $lang[714] . '</p>
    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';

                break;
            } else {
                $all = '';
            }
        }


        (!empty($all) ? $all : '');

        $result = Query("Select * from tbl_member where tbl_member_id = '$user_id' ");
        $read = mysqli_fetch_array($result);
        $profile_img = $read['tbl_member_profile'];
        $email = $read['tbl_member_email'];
        if ($email == '') {
            $_SESSION['error_profile'] = '
        <div class="alert alert-danger border-2 d-flex align-items-center" role="alert">
  <div class="bg-danger me-3 icon-item"><span class="fas fa-times-circle text-white fs-3"></span></div>
  <p class="mb-0 flex-1">' . $lang[502] . '</strong>' . $lang[500] . '</p>
  <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
</div>';
        } else {
            $_SESSION['error_profile'] = 'true';
        }
        if ($_SESSION['error_profile'] != 'true') {
            echo $_SESSION['error_profile'];
        }
    ?>

        <?php
        $result = Query("SELECT DISTINCT tbl_memberskill_contract,tbl_memberskill_data,tbl_memberskill_attach,tbl_memberskill_comment,tbl_memberskill_id,tbl_memberskill_startdate,tbl_memberskill_planname From tbl_memberskill where ( tbl_memberskill_datereport is null or tbl_memberskill_datereport='') and (tbl_memberskill_startdate !='') and tbl_memberskill_user='$user_id' ORDER BY tbl_memberskill_id DESC LIMIT 0,8 ");
        if (mysqli_num_rows($result) > 0) {
        ?>

            <div class="row gx-3 mb-2">
                <div class="col-xxl-12 col-xl-12 p-0">
                    <div class="card">
                        <div class="card-header border-bottom border-200 px-0">
                            <div class="d-lg-flex justify-content-between">
                                <div class="row flex-between-center gy-2 px-x1">
                                    <div class="col-auto pe-0">
                                        <h4 class="mb-0"><?= $lang[718] ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-4.png);">
                        </div>
                        <div class="card-body p-0 position-relative">
                            <div class="table-responsive scrollbar">
                                <?php
                                $result = Query("SELECT DISTINCT tbl_memberskill_mentor,tbl_memberskill_datereport,tbl_memberskill_contract,tbl_memberskill_data,tbl_memberskill_attach,tbl_memberskill_comment,tbl_memberskill_id,tbl_memberskill_startdate,tbl_memberskill_planname From tbl_memberskill where ( tbl_memberskill_datereport is null or tbl_memberskill_datereport='') and (tbl_memberskill_startdate !='') and tbl_memberskill_user='$user_id' ORDER BY tbl_memberskill_id DESC LIMIT 0,8 ");
                                if (mysqli_num_rows($result) > 0) {
                                ?>
                                    <table class="table table-sm mb-0 fs--1 table-view-tickets">
                                        <thead class="text-800 bg-light">
                                            <tr>
                                                <th><?= $lang[1318] ?></th>
                                                <th><?= $lang[1319] ?></th>
                                                <th><?= $lang[401] ?></th>
                                                <th><?= $lang[1320] ?></th>
                                                <th class="text-left"><?= $lang[1321] ?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="list">
                                            <?php
                                            while ($read =  mysqli_fetch_array($result)) {
                                                $skill = explode(",", $read['tbl_memberskill_data']);
                                                $contract = $read['tbl_memberskill_contract'];
                                                $resultMessage = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_reply='0' and tbl_skillmessage_contract='$contract' and tbl_skillmessage_user='$user_id' and tbl_skillmessage_delete ='0'  ");
                                                $mentor = $read['tbl_memberskill_mentor'];

                                                $resultSkill = Query("SELECT 
                                        b.tbl_categories_catcode
                                        From 
                                        tbl_categories a 
                                        INNER JOIN tbl_categories b ON a.tbl_categories_bakhsh = b.tbl_categories_code and a.tbl_categories_code = '$skill[0]' limit 0,1 ;");
                                                $readSkill = mysqli_fetch_array($resultSkill);
                                                $sectionCode = $readSkill['tbl_categories_catcode'];
                                            ?>
                                                <tr>
                                                    <td class="align-middle pe-3 py-2 pe-4 cat-code"><?= $sectionCode ?></td>
                                                    <td class="align-middle pe-3 py-2 pe-4 ">
                                                        <h6 class="mb-0 cat-title"><?= $read['tbl_memberskill_planname'] ?></h6>
                                                    </td>
                                                    <td class="align-middle subject py-2 pe-4"><?= $read['tbl_memberskill_startdate'] ?></td>
                                                    <td class="align-middle subject py-2 pe-4"></td>
                                                    <td class="align-middle status py-2 pe-2">
                                                        <a href="<?= $site_url . $lng . '/mypanel/skill/' . $read['tbl_memberskill_id'] ?>/"><small class="badge rounded badge-subtle-warning false" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1189] . ' ' . toPersianNum(mysqli_num_rows($resultMessage)) ?>"><span class="bi-chat-left-dots-fill"></span></small></a>
                                                        <?php
                                                        $resultQuestion = Query("select * from tbl_memberquestion where tbl_memberquestion_userid='$user_id' ");
                                                        if (mysqli_num_rows($resultQuestion) > 0) {
                                                        ?>
                                                            <a href="<?= $site_url . $lng . '/mypanel/skillview/' . $read['tbl_memberskill_contract'] ?>/"><small class="badge rounded badge-subtle-secondary dark__bg-1000" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[597] ?>"><span class="bi-check-square-fill"></span></small></a>

                                                        <?php
                                                        }
                                                        $resultQuestion = Query("SELECT * From tbl_interview where tbl_interview_contract='$contract' and tbl_interview_model='0' and tbl_interview_menti='$user_id' and tbl_interview_lng='$lng' and tbl_interview_delete='0'  ");
                                                        if (mysqli_num_rows($resultQuestion) > 0) {
                                                        ?>
                                                            <a href="<?= $site_url . $lng . '/mypanel/interview/' . $read['tbl_memberskill_contract'] ?>/"><small class="badge rounded badge-subtle-success false" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[859] ?>"><span class="bi-mic-fill"></span></small></a>
                                                        <?php
                                                        }
                                                        if ($read['tbl_memberskill_datereport'] != '') {
                                                        ?>
                                                            <a href="<?= $site_url . $lng . '/mypanel/opplatform/' . $read['tbl_memberskill_contract'] ?>/"><small class="badge rounded badge-subtle-danger false" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1120] ?>"><span class="bi-card-heading"></span></small></a>
                                                            <a href="<?= $site_url . $lng . '/mypanel/opmentor/' . $read['tbl_memberskill_contract'] ?>/"><small class="badge rounded badge-subtle-danger false" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1121] ?>"><span class="bi-person-check-fill"></span></small></a>

                                                        <?php } ?>
                                                    </td>
                                                </tr>

                                                <?php
                                                foreach ($skill as $key => $value) {
                                                    if (!empty($value)) {

                                                        $resultAll = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$contract' and tbl_plan_skill ='$value' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ");
                                                        $rowAll = mysqli_num_rows($resultAll);

                                                        $resultConfirm = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$contract' and tbl_plan_status='1' and tbl_plan_skill ='$value' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ");
                                                        $rowConfirm = mysqli_num_rows($resultConfirm);

                                                        $resultUnConfirm = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$contract' and tbl_plan_status='0' and tbl_plan_skill ='$value' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ");
                                                        $rowUnConfirm = mysqli_num_rows($resultUnConfirm);

                                                        $resultMentor = Query("select tbl_user_name from tbl_user where tbl_user_id='$mentor' ");
                                                        $readMentor = mysqli_fetch_array($resultMentor);

                                                        $resultSkill = Query("SELECT tbl_categories_title,tbl_categories_code,tbl_categories_catcode From tbl_categories Where tbl_categories_code = '$value' and tbl_categories_lng='$lng' ");
                                                        $readSkill = mysqli_fetch_array($resultSkill);
                                                ?>
                                                        <tr>
                                                            <td class="align-middle pe-3 py-2 pe-4 cat-code"><?= $readSkill['tbl_categories_catcode'] ?></td>
                                                            <td class="align-middle pe-3 py-2 pe-4"><a class="link-primary ajaxlink" data-title="<?= $read['tbl_memberskill_planname'] ?>" data-skill="<?= $readSkill['tbl_categories_code'] ?>" data-contract="<?= $contract ?>" data-lng="<?= $lng ?>" href="#"><?= $readSkill['tbl_categories_title'] ?></a>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="progress me-3 rounded-3 bg-200" style="height: 5px; width: 80px" role="progressbar" aria-valuenow="<?= ($rowConfirm == 0 ? $rowConfirm : round(abs(((($rowAll - $rowConfirm) / $rowAll) * 100) - 100))) ?>" aria-valuemin="0" aria-valuemax="100">
                                                                        <div class="progress-bar rounded-pill" style="width: <?= ($rowConfirm == 0 ? $rowConfirm : round(abs(((($rowAll - $rowConfirm) / $rowAll) * 100) - 100))) ?>%"></div>
                                                                    </div>
                                                                    <div class="text-small p-1"><?= ($rowConfirm == 0 ? $rowConfirm : round(abs(((($rowAll - $rowConfirm) / $rowAll) * 100) - 100))) ?>%</div>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle pe-3 py-2 pe-4"></td>
                                                            <td class="align-middle pe-3 py-2 pe-4"><?= $readMentor['tbl_user_name'] ?></td>
                                                            <td class="align-middle status py-2 pe-2">
                                                                <a href="<?= $site_url . $lng . '/mypanel/skilldetail/' . $read['tbl_memberskill_contract'] ?>/<?= $readSkill['tbl_categories_code'] ?>/"><small class="badge rounded badge-subtle-success false" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1315] ?>"><span class="bi-eye-fill"></span></small></a>

                                                            </td>
                                                <?php
                                                    }
                                                }
                                                echo '</tr>';
                                            }
                                                ?>
                                        </tbody>
                                    </table>
                                <?php } else { ?>
                                    <div class="text-center d-none" id="tickets-table-fallback">
                                        <p class="fw-bold fs-1 mt-3"><?= $lang[1283] ?></p>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ajaxresult">
                <?php
                $result = Query("SELECT DISTINCT tbl_memberskill_contract,tbl_memberskill_data,tbl_memberskill_attach,tbl_memberskill_comment,tbl_memberskill_id,tbl_memberskill_startdate,tbl_memberskill_planname From tbl_memberskill where tbl_memberskill_datereport='' and tbl_memberskill_startdate !='' and tbl_memberskill_user='$user_id' ORDER BY tbl_memberskill_id DESC LIMIT 0,1 ");
                if (mysqli_num_rows($result) > 0) {
                    $titleFirst = '';
                    while ($read = mysqli_fetch_array($result)) {
                        $skill = explode(",", $read['tbl_memberskill_data']);
                        $contract = $read['tbl_memberskill_contract'];
                        $resultContract = Query("SELECT tbl_contract_customer From tbl_contract where tbl_contract_id ='$contract' limit 0,1 ");
                        $readContract = mysqli_fetch_array($resultContract);
                        $resultPlan = Query("SELECT tbl_plan_title FROM tbl_plan where tbl_plan_delete='0' and tbl_plan_lng='$lng' and tbl_plan_menti = '$user_id' and tbl_plan_contract ='$contract' and tbl_plan_skill='$skill[0]'");
                        $resultPlanConfirm = Query("SELECT tbl_plan_title FROM tbl_plan where tbl_plan_status='1' and tbl_plan_delete='0' and tbl_plan_lng='$lng' and tbl_plan_menti = '$user_id' and tbl_plan_contract ='$contract' and tbl_plan_skill='$skill[0]'");
                        $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$skill[0]' and tbl_categories_lng='$lng' ");
                        $readSkill = mysqli_fetch_array($resultSkill);
                        $titleFirst = $readSkill['tbl_categories_title'];
                ?>
                        <div class="row">
                            <div class="col-lg-12 col-xl-12 col-xxl-12 mb-0 p-lg-2">
                                <div class="card h-lg-100">
                                    <div class="card-body d-flex align-items-center">
                                        <h4 class="mb-0"><?= $titleFirst ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xl-6 col-xxl-6 mb-0 p-lg-2">
                                <div class="card height-one">
                                    <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-1.png);">
                                    </div>
                                    <div class="card-body p-0 position-relative">
                                        <div class="row g-0 justify-content-center">
                                            <?php

                                            $resultNumberModel = Query("SELECT tbl_plan_rank,tbl_plan_vazn From tbl_plan where tbl_plan_model='1236' and tbl_plan_skill='$skill[0]' and tbl_plan_contract='$contract' and tbl_plan_menti='$user_id' and tbl_plan_delete='0'   ORDER BY tbl_plan_id DESC,tbl_plan_order DESC LIMIT 0,1 ");
                                            $readNumberModel = mysqli_fetch_array($resultNumberModel);
                                            @$firstRank = $readNumberModel['tbl_plan_rank'];
                                            @$firstVazn = $readNumberModel['tbl_plan_vazn'];
                                            $resultNumberModel = Query("SELECT tbl_plan_rank,tbl_plan_vazn From tbl_plan where tbl_plan_model='1237' and tbl_plan_skill='$skill[0]' and tbl_plan_contract='$contract' and tbl_plan_menti='$user_id' and tbl_plan_delete='0'   ORDER BY tbl_plan_id DESC,tbl_plan_order DESC LIMIT 0,1 ");
                                            $readNumberModel = mysqli_fetch_array($resultNumberModel);
                                            @$secondRank = $readNumberModel['tbl_plan_rank'];
                                            $resultAll = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$contract' and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ");
                                            $rowAll = mysqli_num_rows($resultAll);

                                            $resultAllNazari = Query("SELECT tbl_plan_id From tbl_plan where (tbl_plan_model ='1230' or tbl_plan_model='1231' or tbl_plan_model ='1232' or tbl_plan_model='1233') and tbl_plan_contract='$contract' and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ");
                                            $rowAllNazari = (mysqli_num_rows($resultAllNazari) == 0 ? '0' : mysqli_num_rows($resultAllNazari));

                                            $resultConfirmNazari = Query("SELECT tbl_plan_id From tbl_plan where (tbl_plan_model ='1230' or tbl_plan_model='1231' or tbl_plan_model ='1232' or tbl_plan_model='1233') and tbl_plan_contract='$contract' and tbl_plan_status='1' and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ");
                                            $rowConfirmNazari = (mysqli_num_rows($resultConfirmNazari) == 0 ? '0' : mysqli_num_rows($resultConfirmNazari));


                                            $resultAllAmali = Query("SELECT tbl_plan_id From tbl_plan where (tbl_plan_model ='1234' or tbl_plan_model='1235') and tbl_plan_contract='$contract' and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ");
                                            $rowUnAllAmali = (mysqli_num_rows($resultAllAmali) == 0 ? '0' : mysqli_num_rows($resultAllAmali));

                                            $resultConfirmAmali = Query("SELECT tbl_plan_id From tbl_plan where (tbl_plan_model ='1234' or tbl_plan_model='1235') and tbl_plan_contract='$contract' and tbl_plan_status='1' and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ");
                                            $rowUnConfirmAmali = (mysqli_num_rows($resultConfirmAmali) == 0 ? '0' : mysqli_num_rows($resultConfirmAmali));



                                            $resultAzmoonAmali = Query("SELECT tbl_plan_rank From tbl_plan where tbl_plan_model ='1236' and tbl_plan_contract='$contract'  and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' order by tbl_plan_expire DESC limit 0,1 ");
                                            if (mysqli_num_rows($resultAzmoonAmali) == 0) {
                                                $readAzmoonAmali = 0;
                                            } else {
                                                $readAzmoonAmaliRes = mysqli_fetch_array($resultAzmoonAmali);
                                                $readAzmoonAmali = $readAzmoonAmaliRes['tbl_plan_rank'];
                                            }


                                            $resultAzmoonKatbi = Query("SELECT tbl_plan_rank From tbl_plan where tbl_plan_model ='1237' and tbl_plan_contract='$contract'  and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' order by tbl_plan_expire DESC limit 0,1 ");
                                            $readAzmoonKatbi = mysqli_fetch_array($resultAzmoonKatbi);


                                            @$secondVazn = (!empty($firstVazn) ? 100 - $firstVazn : 0);

                                            echo ' <div class="col-sm-6  pe-lg-2 mb-2">
                    <div class=" h-100">
                        <div class="card-body d-flex flex-center flex-column">
                        <div class="circle_percent" data-percent="' . (!empty($rowAllNazari) && $rowAllNazari != 0 ? round(abs(((($rowAllNazari - $rowConfirmNazari) / $rowAllNazari) * 100) - 100)) : '0') . '"> 
                        <div class="circle_inner">
                            <div class="round_per"></div>
                        </div>
                    </div>
                            <div class="text-center mt-3">
                                <h6 class="fs-0 mb-1">
                                    <span class="fas fa-check text-primary me-1" data-fa-transform="shrink-2"></span>' . (!empty($rowAllNazari) && $rowAllNazari != 0 ? round(abs(((($rowAllNazari - $rowConfirmNazari) / $rowAllNazari) * 100) - 100)) : '0') . ' 
                                </h6>
                                <p class="fs--1 mb-0">' . $lang[1325] . '</p>
                            </div>
                        </div>
                    </div>
                </div>';

                                            echo ' <div class="col-sm-6  pe-lg-2 mb-2">
                    <div class=" h-100">
                        <div class="card-body d-flex flex-center flex-column">
                        <div class="circle_percent" data-percent="' . (!empty($rowUnAllAmali) && $rowUnAllAmali != 0 ? round(abs(((($rowUnAllAmali - $rowUnConfirmAmali) / $rowUnAllAmali) * 100) - 100)) : '0') . '"> 
                        <div class="circle_inner">
                            <div class="round_per"></div>
                        </div>
                    </div>
                            <div class="text-center mt-3">
                                <h6 class="fs-0 mb-1">
                                    <span class="fas fa-check text-primary me-1" data-fa-transform="shrink-2"></span>' . (!empty($rowUnAllAmali) && $rowUnAllAmali != 0 ? round(abs(((($rowUnAllAmali - $rowUnConfirmAmali) / $rowUnAllAmali) * 100) - 100)) : '0') . ' 
                                </h6>
                                <p class="fs--1 mb-0">' . $lang[1326] . '</p>
                            </div>
                        </div>
                    </div>
                </div>';

                                            $shayestegi = 0;
                                            $emtehan = 0;
                                            $resultCheckNumber = Query("select tbl_number_shayestegi,tbl_number_emtehan from tbl_number where tbl_number_user='$user_id' and tbl_number_contract='$contract' and tbl_number_skill='$skill[0]' ");
                                            if (mysqli_num_rows($resultCheckNumber) > 0) {
                                                $readCheckNumber = mysqli_fetch_array($resultCheckNumber);
                                                $shayestegi = $readCheckNumber['tbl_number_shayestegi'];
                                                $emtehan = $readCheckNumber['tbl_number_emtehan'];
                                            }


                                            $emtehanNumber = (($firstRank * $firstVazn) / 100) + (($secondRank * $secondVazn) / 100);

                                            //     echo ' <div class="col-sm-2  pe-lg-2 mb-2">
                                            //     <div class=" h-100">
                                            //         <div class="card-body d-flex flex-center flex-column">
                                            //         <div class="circle_percent" data-percent="'.round(abs((((100 - $emtehanNumber) / 100) * 100)-100)).'"> 
                                            //         <div class="circle_inner">
                                            //             <div class="round_per"></div>
                                            //         </div>
                                            //     </div>
                                            //             <div class="text-center mt-3">
                                            //                 <h6 class="fs-0 mb-1">
                                            //                     <span class="fas fa-check text-primary me-1" data-fa-transform="shrink-2"></span>'.round(abs((((100 - $emtehanNumber) / 100) * 100)-100)).' 
                                            //                 </h6>
                                            //                 <p class="fs--1 mb-0">'.$lang[1312].'</p>
                                            //             </div>
                                            //         </div>
                                            //     </div>
                                            // </div>';

                                            //     echo ' <div class="col-sm-2  pe-lg-2 mb-2">
                                            //     <div class=" h-100">
                                            //         <div class="card-body d-flex flex-center flex-column">
                                            //         <div class="circle_percent" data-percent="'.round(abs((((100 - $shayestegi) / 100) * 100)-100)).'"> 
                                            //         <div class="circle_inner">
                                            //             <div class="round_per"></div>
                                            //         </div>
                                            //     </div>
                                            //             <div class="text-center mt-3">
                                            //                 <h6 class="fs-0 mb-1">
                                            //                     <span class="fas fa-check text-primary me-1" data-fa-transform="shrink-2"></span>'.round(abs((((100 - $shayestegi) / 100) * 100)-100)).' 
                                            //                 </h6>
                                            //                 <p class="fs--1 mb-0">'.$lang[1313].'</p>
                                            //             </div>
                                            //         </div>
                                            //     </div>
                                            // </div>';



                                            $finalNumber = abs($emtehan - 100);
                                            $finalEmtehanNumber = ($emtehanNumber * $emtehan) / 100;
                                            $finalShayestegiNumber = ($shayestegi * $finalNumber) / 100;
                                            $finalRank = $finalEmtehanNumber + $finalShayestegiNumber;

                                            //         echo ' <div class="col-sm-2  pe-lg-2 mb-2">
                                            //     <div class=" h-100">
                                            //         <div class="card-body d-flex flex-center flex-column">
                                            //         <div class="circle_percent" data-percent="'.round(abs((((100 - $finalRank) / 100) * 100)-100)).'"> 
                                            //         <div class="circle_inner">
                                            //             <div class="round_per"></div>
                                            //         </div>
                                            //     </div>
                                            //             <div class="text-center mt-3">
                                            //                 <h6 class="fs-0 mb-1">
                                            //                     <span class="fas fa-check text-primary me-1" data-fa-transform="shrink-2"></span>'.round(abs((((100 - $finalRank) / 100) * 100)-100)).' 
                                            //                 </h6>
                                            //                 <p class="fs--1 mb-0">'.$lang[1314].'</p>
                                            //             </div>
                                            //         </div>
                                            //     </div>
                                            // </div>';



                                            // echo '<hr>
                                            // <a href="' . $site_url . $lng . '/mypanel/skilldetail/'.$contract.'/'. $skill[0] . '/" class="text-center"><button class="btn btn-primary me-3 mb-3" type="button">'.$lang[1315].'
                                            // </button></a>';
                                            ?>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $now = date("Y/m/d");
                            $next_days = strtotime('+10 days');
                            $nextTenDays = date('Y/m/d', $next_days);
                            $resultPlan = Query("select * from tbl_plan where tbl_plan_lng='$lng' and tbl_plan_contract='$contract' and tbl_plan_skill ='$skill[0]' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_dateenglish BETWEEN '$now' and '$nextTenDays' order by tbl_plan_dateenglish ASC ");

                            ?>

                            <div class="col-lg-6 col-xl-6 col-xxl-6 mb-0 p-lg-2">
                                <div class="row g-0 justify-content-center">
                                    <div class="col-xxl-12 col-xl-12 mb-0 pe-0">
                                        <div class="card height-one">
                                            <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-1.png);">
                                            </div>
                                            <div class="card-body p-3 position-relative">
                                                <div class="row g-0 justify-content-center">
                                                    <div class="col-lg-12  p-3 mb-2">
                                                        <h5><?= $lang[1365] ?></h5>
                                                        <div class="box-content-two mt-2">
                                                            <?php
                                                            if (mysqli_num_rows($resultPlan) <= 0) {
                                                                echo '<p>' . $lang[1366] . '</p>';
                                                            } else {
                                                                while ($readPlan = mysqli_fetch_array($resultPlan)) {
                                                                    $expire = explode("/", $readPlan['tbl_plan_expire']);
                                                                    $contractId = $readPlan['tbl_plan_contract'];
                                                                    $faragir = $readPlan['tbl_plan_menti'];

                                                                    $resultContract = Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                                                                    $readContract =  mysqli_fetch_array($resultContract);

                                                                    $resultMember = Query("select * from tbl_member where tbl_member_id ='$faragir' ");
                                                                    $readMember =  mysqli_fetch_array($resultMember);
                                                                    $a = jdate_words(array('mm' => $expire[1]));
                                                                    echo '<div class="d-flex btn-reveal-trigger mb-2 mt-2">
                                        <div class="calendar"><span class="calendar-month">' . $a['mm'] . '</span><span class="calendar-day">' . toPersianNum($expire[2]) . '</span></div>
                                        <div class="flex-1 position-relative ps-3">
                                            <h6 class="fs-0 mb-2"><a class="item-info"  href="#" data-value="' . $readPlan['tbl_plan_code'] . '-' . $lng . '">' . ($readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233' || $readPlan['tbl_plan_model'] == '1234' ? PlanModel($readPlan['tbl_plan_model']) : PlanModel($readPlan['tbl_plan_model'])) . '</a></h6>';
                                                                    echo '<p class="fs-0  mb-1">' . ($readPlan['tbl_plan_model'] == '1230' || $readPlan['tbl_plan_model'] == '1231' || $readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233'  ? $lang[1367] . ' : ' : '') .
                                                                        ($readPlan['tbl_plan_model'] == '1234' ? $lang[62] . ' : ' : '') .
                                                                        ($readPlan['tbl_plan_model'] == '1235' ? $lang[62] . ' : ' : '') .
                                                                        ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? $lang[1637] . ' : ' : '') .
                                                                        toPersianNum($readPlan['tbl_plan_expire']) . '</p>
                                        </div>
                                    </div><hr>';
                                                                }
                                                            }
                                                            ?>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 col-xl-12 col-xxl-12 mb-0 p-lg-2">
                                <div class="row g-0 justify-content-center">
                                    <div class="col-xxl-4 col-xl-4 mb-2 pe-2">
                                        <div class="card height-two-color color-one">
                                            <div class="card-body p-3 position-relative">
                                                <span class="bi-card-checklist"></span>
                                                <h4><?= $lang[1322] ?></h4>
                                                <h3 class="number"><?= (!empty($readAzmoonKatbi['tbl_plan_rank']) ? $readAzmoonKatbi['tbl_plan_rank']  : '0') ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-4 col-xl-4 mb-2 pe-2">
                                        <div class="card height-two-color color-two">
                                            <div class="card-body p-3 position-relative">
                                                <span class="bi-pencil-square"></span>
                                                <h4><?= $lang[1323] ?></h4>
                                                <h3 class="number"><?= (!empty($readAzmoonAmali) ? $readAzmoonAmali : '0') ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-4 col-xl-4 mb-2 pe-0">
                                        <div class="card height-two-color color-three">
                                            <div class="card-body p-3 position-relative">
                                                <span class="bi-bookmark-star"></span>
                                                <h4><?= $lang[1324] ?></h4>
                                                <h3 class="number"><?= ($shayestegi != '0' ? $finalRank : '0') ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        <?php } ?>

                        </div>
            </div>
        <?php }
        ?>
        </div>
<?php
        }
    } else {
        //echo 'dd';
    }
} else {
    include "components/$do.php";
}

// Do not Set end
include './footer_panel.inc.php';
?>