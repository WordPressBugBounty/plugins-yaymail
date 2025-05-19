<?php
defined( 'ABSPATH' ) || exit;
use YayMail\Utils\TemplateHelpers;
use YayMail\Models\TemplateModel;

/**
 * $args includes
 * $element
 * $render_data
 * $is_nested
 */
if ( empty( $args['element'] ) ) {
    return;
}

$element = $args['element'];
$data    = $element['data'];

$template_model = TemplateModel::get_instance();

$global_header_footer = $template_model->get_global_header_and_footer();

$global_header_elements = isset( $global_header_footer['global_header_elements'] ) ? $global_header_footer['global_header_elements'] : [];

if ( ! empty( $data['rich_text'] ) ) {
    $global_header_elements = array_map(
        function( $element ) use ( $data ) {
            if ( isset( $element['type'] ) && 'heading' === $element['type'] && isset( $element['data'] ) ) {
                $element['data']['rich_text'] = $data['rich_text'];
            }
            return $element;
        },
        $global_header_elements
    );
}

ob_start();
?>
<div class="yaymail-skeleton-divider"><div class="yaymail-skeleton yaymail-skeleton-round css-dev-only-do-not-override-scpxro" style="margin-bottom: 10px;"><div class="yaymail-skeleton-content"><ul class="yaymail-skeleton-paragraph"><li style="width: 30%;"></li></ul></div></div><div class="yaymail-skeleton yaymail-skeleton-round yaymail-skeleton-divider__image css-dev-only-do-not-override-scpxro" style="margin-bottom: 10px;"><div class="yaymail-skeleton-content"><ul class="yaymail-skeleton-paragraph"><li style="width: 100%;"></li></ul></div></div><div class="yaymail-skeleton yaymail-skeleton-round css-dev-only-do-not-override-scpxro"><div class="yaymail-skeleton-content"><ul class="yaymail-skeleton-paragraph"><li style="width: 70%;"></li></ul></div></div><div class="yaymail-skeleton yaymail-skeleton-round css-dev-only-do-not-override-scpxro"><div class="yaymail-skeleton-content"><ul class="yaymail-skeleton-paragraph"><li style="width: 100%;"></li></ul></div></div><div class="yaymail-skeleton yaymail-skeleton-round css-dev-only-do-not-override-scpxro"><div class="yaymail-skeleton-content"><ul class="yaymail-skeleton-paragraph"><li style="width: 100%;"></li></ul></div></div></div>
<?php
$element_content = ob_get_clean();

TemplateHelpers::wrap_element_content( $element_content, $element, $wrapper_style );
