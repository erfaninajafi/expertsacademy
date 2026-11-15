<?php
$Tabpage = '16';
$thisaccess = '1,2,3,4,5,6';
include './header.inc.php';
?>
<section class="content-header">
    <h1>
        دسکتاپ
    </h1>
</section>
<section class="content">
    <?php 
    
   
    if (access_level("1,2", $access) == TRUE) { ?>
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?= $_SESSION['user_name'] ?> خوش آمدید </h5>
                            <p class="mb-4">برای استفاده از امکانات پنل از منو سمت راست استفاده نمایید.</p>

                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="asset/img/illustrations/man-with-laptop-light.png" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png" height="140" style="float: left;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-12 order-2 order-md-3 order-lg-2 mb-4">
            <div class="row">
                <!-- Order Statistics -->
                <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between pb-0">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2">ثبت نام های جدید</h5>
                                <?php
                                    $result_for_count = Query("select * from tbl_register where tbl_register_view !='1'");
                                    ?>
                                <small class="text-muted">مجموع <?= toPersianNum(mysqli_num_rows($result_for_count)) ?>
                                    کاربر</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3" style="position: relative;">


                                <div class="resize-triggers">
                                    <div class="expand-trigger">
                                        <div style="width: 398px; height: 156px;"></div>
                                    </div>
                                    <div class="contract-trigger"></div>
                                </div>
                            </div>
                            <ul class="p-0 m-0">
                                <?php
                                    if ($access == 2) {
                                        $select_this = "where tbl_register_admin='$this_user_id' and tbl_register_view !='1' ";
                                    } else {
                                        $select_this = "where tbl_register_view !='1' ";
                                    }
                                    $result = Query("select * from tbl_register $select_this order by tbl_register_id DESC limit 0,9");
                                    while ($read = mysqli_fetch_array($result)) {
                                        $class = $read['tbl_register_class'];
                                        $user = $read['tbl_register_user'];
                                        $result_class = Query("select tbl_class_title from tbl_class where tbl_class_code='$class' ");
                                        $read_class = mysqli_fetch_array($result_class);
                                        $result_member = Query("select tbl_member_name from tbl_member where tbl_member_code='$user' ");
                                        $read_member = mysqli_fetch_array($result_member);
                                        echo '<li class="d-flex mb-4 pb-1">
                  <div class="avatar flex-shrink-0 me-3">
                      <span class="avatar-initial rounded bg-label-primary"><i
                              class="bx bx-mobile-alt"></i></span>
                  </div>
                  <div
                      class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                      <div class="me-2">
                          <h6 class="mb-0">' . $read_member['tbl_member_name'] . '</h6>
                          <small class="text-muted">کد پیگیری : ' . $read['tbl_register_track'] . '</small>
                      </div>
                      <div class="user-progress">
                          <small class="text-muted"> ' . toPersianNum($read['tbl_register_date']) . '</small>
                      </div>
                  </div>
              </li>';
                                    }
                                    ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--/ Order Statistics -->

                <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between pb-0">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2">کاربران جدید</h5>
                                <?php
                                    $result_for_count = Query("select * from tbl_member ");
                                    ?>
                                <small class="text-muted">مجموع <?= toPersianNum(mysqli_num_rows($result_for_count)) ?>
                                    کاربر</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3" style="position: relative;">


                                <div class="resize-triggers">
                                    <div class="expand-trigger">
                                        <div style="width: 398px; height: 156px;"></div>
                                    </div>
                                    <div class="contract-trigger"></div>
                                </div>
                            </div>
                            <ul class="p-0 m-0">
                                <?php
                                    $result = Query("select * from tbl_member order by tbl_member_id DESC limit 0,9");
                                    while ($read = mysqli_fetch_array($result)) {
                                        echo '<li class="d-flex mb-4 pb-1">
                      <div class="avatar flex-shrink-0 me-3">
                      <a class="users-list-name" href="member.php?do=edit&id=' . $read['tbl_member_id'] . '">
                          ' . (!empty($read['tbl_member_profile']) ? '<img src="../upload/profile/' . $read['tbl_member_profile'] . '" alt="User" class="rounded">' : '<img src="asset/images/no-image.jpg" alt="User" class="rounded">') . '
                      </a>  
                      </div>
                      <div
                          class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                              <small class="text-muted d-block mb-1">' . $read['tbl_member_name'] . '</small>
                              <h6 class="mb-0">' . toPersianNum($read['tbl_member_mobile']) . '</h6>
                          </div>
                      </div>
                  </li>';
                                    }
                                    ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Transactions -->
                <div class="col-md-6 col-lg-4 order-2 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">آخرین تیکتها</h5>

                        </div>
                        <div class="card-body">
                            <ul class="p-0 m-0">

                                <?php
                                    $result = Query("select * from tbl_message where tbl_message_userid !='' order by tbl_message_id DESC limit 0,10");
                                    while ($read = mysqli_fetch_array($result)) {
                                        echo '<li class="d-flex mb-4 pb-1">
                      <div class="avatar flex-shrink-0 me-3">
                          <img src="asset/img/icons/unicons/wallet.png" alt="User" class="rounded">
                      </div>
                      <div
                          class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                              <small class="text-muted d-block mb-1">' . $read['tbl_message_code'] . '</small>
                              <h6 class="mb-0">' . $read['tbl_message_title'] . '</h6>
                          </div>
                      </div>
                  </li>';
                                    }

                                    ?>

                            </ul>
                        </div>
                    </div>
                </div>
                <!--/ Transactions -->
            </div>
        </div>

    </div>
    </div>
    </div>
    <?php }
    if ($access == 6) {
        ?>
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?= $_SESSION['user_name'] ?> خوش آمدید </h5>
                            <p class="mb-4">برای استفاده از امکانات پنل از منو سمت راست استفاده نمایید.</p>

                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="asset/img/illustrations/man-with-laptop-light.png" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png" height="140" style="float: left;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-12 order-2 order-md-3 order-lg-2 mb-4">
            <div class="row">
                <!-- Transactions -->
                <div class="col-md-6 col-lg-4 order-2 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">آخرین پیام ها</h5>

                        </div>
                        <div class="card-body">
                            <ul class="p-0 m-0">

                                <?php
                                    $result = Query("select * from tbl_coachmessage where tbl_coachmessage_coach ='$this_user_id' and tbl_coachmessage_delete ='0' and tbl_coachmessage_reply='0' order by tbl_coachmessage_id DESC limit 0,10");
                                    while ($read = mysqli_fetch_array($result)) {
                                        echo '<li class="d-flex mb-4 pb-1">
                      <div class="avatar flex-shrink-0 me-3">
                          <img src="asset/img/icons/unicons/wallet.png" alt="User" class="rounded">
                      </div>
                      <div
                          class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                              <small class="text-muted d-block mb-1">
                              <a href="coachmessage.php?do=view&code='.$read['tbl_coachmessage_code'].'&skill='.$read['tbl_coachmessage_skill'].'&plan='.$read['tbl_coachmessage_plan'].'&user='.$read['tbl_coachmessage_user'].'&contract='.$read['tbl_coachmessage_contract'].'">' . $read['tbl_coachmessage_title'] . '</a></small>
                              <h6 class="mb-0">' . (!empty($read['tbl_coachmessage_datereplay']) ? $read['tbl_coachmessage_datereplay'] : $read['tbl_coachmessage_dataadd']) . '</h6>
                          </div>
                      </div>
                  </li>';
                                    }

                                    ?>

                            </ul>
                        </div>
                    </div>
                </div>
                <!--/ Transactions -->
            </div>
        </div>

    </div>
    </div>
    </div>

    <?php
    }
    if ($access == 5) {
        $resultMessageRead = Query("SELECT tbl_skillmessage_id From tbl_skillmessage Where tbl_skillmessage_status ='0' and tbl_skillmessage_reply='0' and tbl_skillmessage_mentor='$this_user_id' and tbl_skillmessage_delete ='0' ");
    ?>
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        یک قرارداد بروز شده است
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <div class="alert alert-dark alert-dismissible fade show" role="alert">
        <?=toPersianNum(mysqli_num_rows($resultMessageRead))?> پیام جدید از فراگیرها دریافت شد
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?= $_SESSION['user_name'] ?> خوش آمدید </h5>
                            <p class="mb-4">برای استفاده از امکانات پنل از منو سمت راست استفاده نمایید.</p>
                            <p>sss</p>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="asset/img/illustrations/man-with-laptop-light.png" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png" height="140" style="float: left;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="asset/img/icons/unicons/chart-success.png" alt="chart success" class="rounded">
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">تعداد کل فراگیرها</span>
                            <?php
                                $result_for_count = Query("SELECT * From tbl_menti  where tbl_menti_delete='0' ");
                                ?>
                            <h3 class="card-title mb-2"><?= toPersianNum(mysqli_num_rows($result_for_count)) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="asset/img/icons/unicons/wallet-info.png" alt="Credit Card" class="rounded">
                                </div>
                            </div>
                            <span>تعداد کل قراردادها </span>
                            <?php
                                $result_for_count = Query("SELECT * From tbl_contract Where tbl_contract_mentor = '$userCode' ");
                                ?>
                            <h3 class="card-title text-nowrap mb-1">
                                <?= toPersianNum(mysqli_num_rows($result_for_count)) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Revenue -->
        <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
            <div class="row">
                <!-- Order Statistics -->
                <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between pb-0">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2">آخرین پیامهای ارسالی </h5>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3" style="position: relative;">


                                <div class="resize-triggers">
                                    <div class="expand-trigger">
                                        <div style="width: 398px; height: 156px;"></div>
                                    </div>
                                    <div class="contract-trigger"></div>
                                </div>
                            </div>
                            <ul class="p-0 m-0">
                                <?php

                                    $resultMessage = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_reply='0' and tbl_skillmessage_mentor='$this_user_id' and tbl_skillmessage_delete ='0' limit 0,10 ");
                                    while ($read = mysqli_fetch_array($resultMessage)) {
                                        echo '<li class="d-flex mb-4 pb-1">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-primary"><i
                                                            class="bx bx-mobile-alt"></i></span>
                                                </div>
                                                <div
                                                    class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                    <div class="me-2">
                                                        <h6 class="mb-0"><a href="allmessage.php?do=view&code=' . $read['tbl_skillmessage_code'] . '&user=' . $read['tbl_skillmessage_user'] . '&contract=' . $read['tbl_skillmessage_contract'] . '">' . $read['tbl_skillmessage_title'] . '</a></h6>
                                                        <small class="text-muted">تاریخ ارسال ' . toPersianNum($read['tbl_skillmessage_dataadd']) . '</small>
                                                    </div>
                                                    <div class="user-progress">
                                                        <small class="text-muted"> ' . ($read['tbl_skillmessage_status'] == '0' ? 'منتظر پاسخ' : ' پاسخ داده شده') . '</small>
                                                    </div>
                                                </div>
                                            </li>';
                                    }
                                    ?>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php
    } ?>
</section>
<?php
include './footer.inc.php';
?>