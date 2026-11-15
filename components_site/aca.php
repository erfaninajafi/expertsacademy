<?php
// Use a separate prefix and an instance counter to avoid duplicate Bootstrap targets.
$acaSectionIndex = isset($acaSectionIndex) ? $acaSectionIndex + 1 : 1;
$acaAccordionId = 'aca-accordion-' . $acaSectionIndex;
?>
<section class="section-content faq-section">
    <div class="container">
        <div class="faq-panel">
            <h3 class="faq-heading"><?= htmlspecialchars($lang[110], ENT_QUOTES, 'UTF-8') ?></h3>
            <div class="accordion faq-accordion" id="<?= $acaAccordionId ?>">
                <?php
                $acaItemIndex = 0;
                while ($acaItem = mysqli_fetch_assoc($resultContent)):
                    $acaItemId = $acaAccordionId . '-item-' . $acaItemIndex;
                    $isOpen = $acaItemIndex === 0;
                ?>
                    <div class="accordion-item faq-item">
                        <h4 class="accordion-header" id="<?= $acaItemId ?>-heading">
                            <button
                                class="accordion-button<?= $isOpen ? '' : ' collapsed' ?>"
                                type="button"
                                id="<?= $acaItemId ?>-trigger"
                                data-bs-toggle="collapse"
                                data-bs-target="#<?= $acaItemId ?>"
                                aria-expanded="<?= $isOpen ? 'true' : 'false' ?>"
                                aria-controls="<?= $acaItemId ?>"
                            ><?= htmlspecialchars($acaItem['tbl_content_title'], ENT_QUOTES, 'UTF-8') ?></button>
                        </h4>
                        <div
                            id="<?= $acaItemId ?>"
                            class="accordion-collapse collapse<?= $isOpen ? ' show' : '' ?>"
                            aria-labelledby="<?= $acaItemId ?>-heading"
                            data-bs-parent="#<?= $acaAccordionId ?>"
                        >
                            <div class="accordion-body faq-answer"><?= $acaItem['tbl_content_text'] ?></div>
                        </div>
                    </div>
                    <?php $acaItemIndex++; ?>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>
