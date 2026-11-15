<?php
$GLOBALS['exacdContactFormIndex'] = ($GLOBALS['exacdContactFormIndex'] ?? 0) + 1;
$contactFormId = 'contact-' . $GLOBALS['exacdContactFormIndex'];
?>
<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-6">
                <form class="form-style js-contact-form" action="#" method="post" id="<?= $contactFormId ?>" enctype="multipart/form-data">
                    <input type="hidden" name="lng" value="<?= $lng ?>">

                    <div class="form-group">
                        <input type="text" name="fld1" placeholder="<?= $lang[91] ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="fld2" placeholder="<?= $lang[98] ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <input type="number" name="fld3" placeholder="<?= $lang[99] ?>" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <label> <input type="radio" value="1" name="section" required> <?= $lang[118] ?> </label>
                        <label> <input type="radio" value="2" name="section" required> <?= $lang[119] ?> </label>
                    </div>


                    <div class="request">
                        <div class="form-group">
                            <input type="text" name="fld4" placeholder="<?= $lang[120] ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="text" name="fld5" placeholder="<?= $lang[121] ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="text" name="fld6" placeholder="<?= $lang[122] ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <textarea name="fld7" placeholder="<?= $lang[123] ?>" class="form-control" rows="5"></textarea>
                        </div>
                    </div>

                    <div class="job">
                        <div class="form-group mb-2">
                            <label> <input type="radio" value="1" name="kind"> <?= $lang[124] ?> </label>
                            <label> <input type="radio" value="2" name="kind"> <?= $lang[125] ?> </label>
                        </div>
                    </div>

                    <div class="jobresult">
                        <div class="form-group">
                            <label><?= $lang[126] ?></label>
                            <input type="file" name="file" class="form-control">
                        </div>
                        <div class="form-group">
                            <textarea name="fld8" placeholder="<?= $lang[127] ?>" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            <input type="text" name="fld9" placeholder="<?= $lang[128] ?>" class="form-control">
                        </div>
                    </div>

                    <div class="jobresulttwo">
                        <div class="form-group">
                            <input type="text" name="fld10" placeholder="<?= $lang[122] ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="text" name="fld11" placeholder="<?= $lang[121] ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <textarea name="fld12" placeholder="<?= $lang[127] ?>" class="form-control" rows="5"></textarea>
                        </div>
                    </div>



                    <input type="submit" name="send" value="<?= $lang[61] ?> " class="btn btn-success">
                    <div class="msgform">

                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
