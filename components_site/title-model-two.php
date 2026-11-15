<?php

$readContent = mysqli_fetch_array($resultContent);
?>
<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <p class="title-index">
                    <?= $readContent['tbl_textsection_title'] ?>
                </p>
                <p class="text-center"><?= $readContent['tbl_textsection_undertitle'] ?></p>
            </div>
        </div>
    </div>
</section>