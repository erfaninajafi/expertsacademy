<?php

include './includer.php';
$thisaccess = '1,2,3,4,5,6,7,8,9';
include './header.inc.php';
$resultPages = Query("Select * from tbl_pages Where tbl_pages_code = '$code'  ");
if(mysqli_num_rows($resultPages) <= 0)
{
    header("location: index.php");
    exit();
}
            $readPages = mysqli_fetch_array($resultPages);
    ?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $readPages['tbl_pages_title'] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 position-relative">
                <?php
            echo $readPages['tbl_pages_text'];
    ?>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
<?php
include './footer.inc.php';

?>