<?php
defined( 'ABSPATH' ) || exit;

use YayMail\Elements\ElementsLoader;
use YayMail\Utils\TemplateHelpers;
use YayMail\Utils\Helpers;

/**
 * $args includes
 * $element
 * $render_data
 * $is_nested
 */
if ( empty( $args['element'] ) ) {
    return;
}

$element                 = $args['element'];
$data                    = $element['data'];
$list_elements           = $args['template']->get_elements();
$parent_element          = TemplateHelpers::find_parent_element( $element['id'], $list_elements );
$row_avg_padding         = $parent_element['data']['column_spacing'] ?? 0;
$total_columns           = $parent_element['data']['amount_of_columns'] ?? 1;
$current_column_index    = array_search( $element['id'], array_column( $parent_element['children'] ?? [], 'id' ), true );
$is_responsive_on_mobile = Helpers::is_true( $parent_element['data']['responsive_on_mobile'] ?? false );


$wrapper_style = TemplateHelpers::get_style(
    array_merge(
        [
            'width'          => "{$data['width']}%",
            'max-width'      => "{$data['width']}%",
            'vertical-align' => $parent_element['data']['vertical_align'] ?? 'top',
        ],
        $current_column_index === 0 ? [
            'padding-left'  => '0',
            'padding-right' => TemplateHelpers::get_dimension_value( $row_avg_padding / 2 ),
        ] : [
            'padding-right' => TemplateHelpers::get_dimension_value( $row_avg_padding / 4 ),
            'padding-left'  => TemplateHelpers::get_dimension_value( $row_avg_padding / 4 ),
        ],
        $total_columns - 1 === $current_column_index ? [
            'padding-right' => '0',
            'padding-left'  => TemplateHelpers::get_dimension_value( $row_avg_padding / 2 ),
        ] : [],
    )
);

?>

<style>
    <?php if ( $is_responsive_on_mobile ) { ?>
    @media only screen and (max-width: 400px) {
        .yaymail-element-<?php echo esc_attr( $parent_element['id'] ); ?>  .yaymail-customizer-element-column {
            width: 100% !important;
            display: block;
            max-width: 100% !important;
        }
    }
    <?php } ?>
</style>


<td class="yaymail-customizer-element-column" style="<?php echo esc_attr( $wrapper_style ); ?>">
    <div class="yaymail-customizer-element-nested-column-content">
        <?php
        if ( ! empty( $element['children'] ) ) {
            $args['is_nested'] = true;
            ElementsLoader::render_elements(
                $element['children'],
                $args
            );
        }
        ?>
    </div>
</td>
