<?php
require_once __DIR__ . '/component_registry.php';

/** A layout row is the explicit switch; an empty builder page is still a builder page. */
function exacdCmsBuilderIsActive($pageCode, $lng)
{
    if (!preg_match('/\A[A-Za-z0-9_-]{1,255}\z/D', (string) $pageCode) || !in_array($lng, ['fa', 'en'], true)) {
        return false;
    }
    try {
        $result = Query("SELECT mode FROM tbl_page_builder_layouts WHERE page_code='$pageCode' AND locale='$lng' LIMIT 1");
        $layout = $result ? mysqli_fetch_assoc($result) : null;
        return $layout && $layout['mode'] === 'builder';
    } catch (Throwable $error) {
        // A deployment without the additive builder tables remains on legacy rendering.
        error_log('CMS builder layout lookup failed');
        return false;
    }
}

/** null means a query failure; [] means a deliberately empty builder layout. */
function exacdCmsBuilderInstances($pageCode, $lng)
{
    if (!preg_match('/\A[A-Za-z0-9_-]{1,255}\z/D', (string) $pageCode) || !in_array($lng, ['fa', 'en'], true)) {
        return null;
    }
    try {
        $result = Query("SELECT id, type_key, marker FROM tbl_page_component_instances
            WHERE page_code='$pageCode' AND deleted_at IS NULL AND enabled=1
            AND (locale_scope='shared' OR locale_scope='$lng')
            ORDER BY sort_order ASC, id ASC");
        if (!$result) {
            return null;
        }
        $instances = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $instances[] = $row;
        }
        return $instances;
    } catch (Throwable $error) {
        error_log('CMS builder instance lookup failed');
        return null;
    }
}

/** The only source of template paths is the code-owned component registry. */
function exacdCmsRenderSection($definition, $parent, $lng, $skipMissingContent, $position = 0)
{
    global $lang, $site_url, $faqSectionIndex, $faqFormIndex, $acaSectionIndex;

    if ($definition === null || empty($definition['template'])) {
        return false;
    }
    $template = __DIR__ . '/../' . $definition['template'];
    $thisCode = $parent;
    // Existing templates (notably the slider) use $key for first-section spacing.
    $key = $position;
    $marker = $definition['marker'];
    $resultContent = null;
    try {
        if ($definition['storage'] === 'text') {
            $resultContent = Query("SELECT * FROM tbl_textsection WHERE tbl_textsection_type='58'
                AND tbl_textsection_lng='$lng' AND tbl_textsection_section='$marker'
                AND tbl_textsection_parent='$thisCode'");
        } elseif ($definition['storage'] === 'content') {
            $resultContent = Query("SELECT * FROM tbl_content WHERE tbl_content_type='58'
                AND tbl_content_fld1='$lng' AND tbl_content_section='$marker'
                AND tbl_content_parent='$thisCode' ORDER BY tbl_content_order ASC"
                . ($skipMissingContent ? ', tbl_content_id ASC' : ''));
        }
    } catch (Throwable $error) {
        error_log('CMS component content lookup failed');
        return false;
    }
    if ($definition['storage'] !== 'none') {
        if (!$resultContent) {
            return false;
        }
        if ($skipMissingContent && mysqli_num_rows($resultContent) === 0) {
            // Do not display another language's content for an untranslated instance.
            return false;
        }
    }
    include $template;
    return true;
}

function exacdCmsRenderPage($page, $lng)
{
    global $lang, $site_url;

    $pageCode = (string) $page['tbl_categories_code'];
    if (exacdCmsBuilderIsActive($pageCode, $lng)) {
        $instances = exacdCmsBuilderInstances($pageCode, $lng);
        if ($instances !== null) {
            $spacerShown = false;
            foreach ($instances as $position => $instance) {
                $id = (string) $instance['id'];
                if (!preg_match('/\A[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\z/iD', $id)) {
                    error_log('CMS builder skipped invalid instance ID');
                    continue;
                }
                $definition = exacdComponentDefinitionByTypeAndMarker($instance['type_key'], $instance['marker']);
                if ($definition === null) {
                    error_log('CMS builder skipped unregistered component');
                    continue;
                }
                if (!$spacerShown) {
                    echo '<div class="col-12 section-margin-top-custom"></div>';
                    $spacerShown = true;
                }
                exacdCmsRenderSection($definition, $id, $lng, true, $position);
            }
            return;
        }
    }

    // Unconverted pages still use their original marker order and content keys.
    $sections = explode(PHP_EOL, (string) $page['tbl_categories_design']);
    foreach ($sections as $key => $value) {
        if (empty($value)) {
            continue;
        }
        if ($key === 0) {
            echo '<div class="col-12 section-margin-top-custom"></div>';
        }
        $marker = trim(str_replace(['[', ']'], '', $value));
        $definition = exacdComponentDefinitionByMarker($marker);
        if ($definition === null) {
            error_log('CMS legacy page has an unknown component marker');
            continue;
        }
        exacdCmsRenderSection($definition, $pageCode, $lng, false, $key);
    }
}
