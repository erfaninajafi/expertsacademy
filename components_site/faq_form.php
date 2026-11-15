<?php
// The collaboration form is an optional, independent page section.
$faqFormIndex = isset($faqFormIndex) ? $faqFormIndex + 1 : 1;
$faqFormId = 'faq-form-' . $faqFormIndex;
?>
<section class="section-content faq-section faq-form-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <form
                    class="form-style faq-form faq-panel"
                    action="#"
                    method="post"
                    id="<?= $faqFormId ?>"
                    data-error-message="<?= $lng === 'en' ? 'The form could not be submitted. Please try again.' : 'ارسال فرم انجام نشد. لطفاً دوباره تلاش کنید.' ?>"
                >
                    <h3 class="faq-heading"><?= htmlspecialchars($lang['faq_form_heading'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <input type="hidden" name="lng" value="<?= htmlspecialchars($lng, ENT_QUOTES, 'UTF-8') ?>">

                    <?php for ($field = 1; $field <= 5; $field++): ?>
                        <?php
                        $fieldId = $faqFormId . '-field-' . $field;
                        $fieldLabel = htmlspecialchars($lang[90 + $field], ENT_QUOTES, 'UTF-8');
                        ?>
                        <div class="form-group faq-field">
                            <label class="faq-label" for="<?= $fieldId ?>"><?= $fieldLabel ?></label>
                            <input
                                type="text"
                                id="<?= $fieldId ?>"
                                name="fld<?= $field ?>"
                                placeholder="<?= $fieldLabel ?>"
                                class="form-control"
                                required
                            >
                        </div>
                    <?php endfor; ?>

                    <input type="submit" name="send" value="<?= htmlspecialchars($lang[96], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-success faq-submit">
                    <div class="msgform faq-status" role="status" aria-live="polite"></div>
                </form>
            </div>
        </div>
    </div>
</section>
