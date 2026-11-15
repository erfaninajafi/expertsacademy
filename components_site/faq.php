<?php
// Keep accordion targets unique when a page contains more than one FAQ section.
$faqSectionIndex = isset($faqSectionIndex) ? $faqSectionIndex + 1 : 1;
$faqAccordionId = 'faq-accordion-' . $faqSectionIndex;
?>
<section class="section-content faq-section">
    <div class="container">
        <div class="faq-panel">
            <h3 class="faq-heading"><?= htmlspecialchars($lang[90], ENT_QUOTES, 'UTF-8') ?></h3>
            <div class="accordion faq-accordion" id="<?= $faqAccordionId ?>">
                <?php
                $faqItemIndex = 0;
                while ($faqItem = mysqli_fetch_assoc($resultContent)):
                    $faqItemId = $faqAccordionId . '-item-' . $faqItemIndex;
                    $isOpen = $faqItemIndex === 0;
                ?>
                    <div class="accordion-item faq-item">
                        <h4 class="accordion-header" id="<?= $faqItemId ?>-heading">
                            <button
                                class="accordion-button<?= $isOpen ? '' : ' collapsed' ?>"
                                type="button"
                                id="<?= $faqItemId ?>-trigger"
                                data-bs-toggle="collapse"
                                data-bs-target="#<?= $faqItemId ?>"
                                aria-expanded="<?= $isOpen ? 'true' : 'false' ?>"
                                aria-controls="<?= $faqItemId ?>"
                            ><?= htmlspecialchars($faqItem['tbl_content_title'], ENT_QUOTES, 'UTF-8') ?></button>
                        </h4>
                        <div
                            id="<?= $faqItemId ?>"
                            class="accordion-collapse collapse<?= $isOpen ? ' show' : '' ?>"
                            aria-labelledby="<?= $faqItemId ?>-heading"
                            data-bs-parent="#<?= $faqAccordionId ?>"
                        >
                            <div class="accordion-body faq-answer"><?= $faqItem['tbl_content_text'] ?></div>
                        </div>
                    </div>
                    <?php $faqItemIndex++; ?>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>
