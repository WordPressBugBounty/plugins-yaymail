<?php
/**
 * Filled bar variant for Order Progress.
 *
 * Variables expected from parent template:
 * - $steps, $current_step_index, $align, $table_style
 * - $connector_height, $connector_active_color, $connector_inactive_color
 * - $icon_size, $label_font_size, $label_font_family
 * - $legacy_label_active_color, $legacy_label_inactive_color
 */

defined( 'ABSPATH' ) || exit;

use YayMail\Utils\OrderProgressVariantHelpers;
use YayMail\Utils\TemplateHelpers;

if ( empty( $args['element'] ) ) {
    return;
}

$element = $args['element'];
$data    = $element['data'];

$yaymail_op_max_steps = 5;
$display_steps        = array_slice( $steps, 0, $yaymail_op_max_steps );
$step_count           = count( $display_steps );

if ( $step_count < 1 ) {
    return;
}

$active_index = min( max( 0, (int) $current_step_index ), max( 0, $step_count - 1 ) );

$bubble_pad       = 6;
$bubble_size      = $icon_size + ( $bubble_pad * 2 );
$bubble_cell_size = max( 44, $bubble_size );

// Connector bar: at least 1px (customizer clamps connector_height to 1–10).
$bar_height = max( 1, (int) $connector_height );

$yaymail_step_marker_presets = OrderProgressVariantHelpers::get_step_marker_presets();

$preset_key            = min( 5, max( 1, $step_count ) );
$column_width_percents = $yaymail_step_marker_presets[ $preset_key ]['widths'];
?>
<table class="yaymail-element-order-progress yaymail-element-order-progress--filled-bar" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="<?php echo esc_attr( $table_style ); ?>">
    <tbody>
        <tr class="yaymail-element-order-progress--filled-bar-track-row">
            <td colspan="<?php echo esc_attr( (string) $step_count ); ?>" style="<?php echo esc_attr( TemplateHelpers::get_style( [ 'padding' => '0' ] ) ); ?>">
                <table class="yaymail-element-order-progress--step-marker-track-inner yaymail-element-order-progress--filled-bar-track-inner" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="<?php echo esc_attr( $table_style ); ?>">
                    <tbody>
                        <tr>
                            <?php foreach ( $display_steps as $index => $step ) : ?> 
                                <?php
                                $is_step_active = $index <= $active_index;

                                $image_active_url   = is_array( $step ) ? (string) ( $step['image_active_url'] ?? '' ) : '';
                                $image_inactive_url = is_array( $step ) ? (string) ( $step['image_inactive_url'] ?? '' ) : '';

                                $image_url = OrderProgressVariantHelpers::resolve_step_image_url( $is_step_active, $image_active_url, $image_inactive_url );

                                $raw_image_bg_color = is_array( $step ) ? (string) ( $step['image_bg_color'] ?? '' ) : '';
                                $image_bg_color     = OrderProgressVariantHelpers::resolve_filled_bar_icon_background(
                                    $is_step_active,
                                    $raw_image_bg_color,
                                    $connector_active_color
                                );

                                $pct = isset( $column_width_percents[ $index ] ) ? (int) $column_width_percents[ $index ] : (int) floor( 100 / max( 1, $step_count ) );

                                $segment_colors = OrderProgressVariantHelpers::get_connector_segment_colors(
                                    $index,
                                    $active_index,
                                    $step_count,
                                    $connector_active_color,
                                    $connector_inactive_color
                                );
                                $left_color     = $segment_colors['left'];
                                $right_color    = $segment_colors['right'];

                                $icon_wrap_style = TemplateHelpers::get_style(
                                    [
                                        'display'          => 'inline-block',
                                        'background-color' => $image_bg_color ? $image_bg_color : 'transparent',
                                        'border-radius'    => '9999px',
                                        'padding'          => '10px',
                                        'vertical-align'   => 'middle',
                                    ]
                                );

                                $icon_middle_td_style = TemplateHelpers::get_style(
                                    [
                                        'vertical-align' => 'middle',
                                        'padding'        => '0',
                                        'text-align'     => 'center',
                                        'line-height'    => '0 !important',
                                    ]
                                );
                                ?>
                                <td width="<?php echo esc_attr( (string) $pct ); ?>%" align="center" valign="middle" style="
                                                        <?php
                                                        echo esc_attr(
                                                            TemplateHelpers::get_style(
                                                                [
                                                                    'width'          => $pct . '%',
                                                                    'vertical-align' => 'middle',
                                                                    'padding'        => '0',
                                                                    'padding-left'   => '0',
                                                                    'padding-right'  => '0',
                                                                    'text-align'     => 'center',
                                                                    'line-height'    => '0',
                                                                    'font-size'      => '0',
                                                                ]
                                                            )
                                                        );
                                                        ?>
                                            ">
                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="<?php echo esc_attr( TemplateHelpers::get_style( [ 'border-collapse' => 'collapse' ] ) ); ?>">
                                        <tbody>
                                            <tr>
                                                <?php if ( 0 !== (int) $index ) : ?>
                                                    <td valign="middle" style="
                                                    <?php
                                                    echo esc_attr(
                                                        TemplateHelpers::get_style(
                                                            [
                                                                'vertical-align' => 'middle',
                                                                'padding'        => '0',
                                                            ]
                                                        )
                                                    );
                                                    ?>
                                                    ">
                                                        <div style="
                                                        <?php
                                                        echo esc_attr(
                                                            TemplateHelpers::get_style(
                                                                [
                                                                    'height'           => $bar_height . 'px',
                                                                    'background-color' => $left_color,
                                                                    'width'            => '100%',
                                                                    'font-size'        => '0',
                                                                    'line-height'      => '0',
                                                                ]
                                                            )
                                                        );
                                                        ?>
                                                        ">&nbsp;</div>
                                                    </td>
                                                <?php endif; ?>

                                                <td width="50" align="center" valign="middle" style="<?php echo esc_attr( $icon_middle_td_style ); ?>">
                                                    <?php if ( ! empty( $image_url ) ) : ?>
                                                        <span style="<?php echo esc_attr( $icon_wrap_style ); ?>">
                                                            <img src="<?php echo esc_url( $image_url ); ?>" alt="" width="<?php echo esc_attr( (string) $icon_size ); ?>" height="<?php echo esc_attr( (string) $icon_size ); ?>" style="display:block;margin:0 auto;border:0;outline:none;text-decoration:none;width:<?php echo esc_attr( (string) $icon_size ); ?>px;height:<?php echo esc_attr( (string) $icon_size ); ?>px;"/>
                                                        </span>
                                                    <?php else : ?>
                                                        <span style="
                                                        <?php
                                                        echo esc_attr(
                                                            TemplateHelpers::get_style(
                                                                [
                                                                    'display'    => 'inline-block',
                                                                    'width'      => $bubble_cell_size . 'px',
                                                                    'min-height' => $bubble_cell_size . 'px',
                                                                ]
                                                            )
                                                        );
                                                        ?>
                                                                        ">&nbsp;</span>
                                                    <?php endif; ?>
                                                </td>

                                                <?php if ( (int) $index !== $step_count - 1 ) : ?>
                                                    <td valign="middle" style="
                                                    <?php
                                                    echo esc_attr(
                                                        TemplateHelpers::get_style(
                                                            [
                                                                'vertical-align' => 'middle',
                                                                'padding'        => '0',
                                                            ]
                                                        )
                                                    );
                                                    ?>
                                                        ">
                                                        <div style="
                                                        <?php
                                                        echo esc_attr(
                                                            TemplateHelpers::get_style(
                                                                [
                                                                    'height'           => $bar_height . 'px',
                                                                    'background-color' => $right_color,
                                                                    'width'            => '100%',
                                                                    'font-size'        => '0',
                                                                    'line-height'      => '0',
                                                                ]
                                                            )
                                                        );
                                                        ?>
                                                        ">&nbsp;</div>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>

        <tr class="yaymail-element-order-progress--filled-bar-labels-row">
            <td colspan="<?php echo esc_attr( (string) $step_count ); ?>" style="<?php echo esc_attr( TemplateHelpers::get_style( [ 'padding' => '10px 0 0' ] ) ); ?>">
                <table class="yaymail-element-order-progress--filled-bar-labels-inner" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="<?php echo esc_attr( $table_style ); ?>">
                    <tbody>
                        <tr>
                            <?php foreach ( $display_steps as $index => $step ) : ?>
                                <?php
                                $is_step_active = $index <= $active_index;

                                $title = is_array( $step ) ? (string) ( $step['title'] ?? $step['label'] ?? '' ) : '';

                                $step_label_color          = is_array( $step ) ? (string) ( $step['label_color'] ?? '' ) : '';
                                $step_label_active_color   = is_array( $step ) ? (string) ( $step['label_active_color'] ?? '' ) : '';
                                $step_label_inactive_color = is_array( $step ) ? (string) ( $step['label_inactive_color'] ?? '' ) : '';

                                // Mirror filled-bar.tsx: first column left, last right, else center.
                                $label_td_align = ( 0 === (int) $index )
                                    ? 'left'
                                    : ( (int) $index === $step_count - 1 ? 'right' : 'center' );

                                $label_color = OrderProgressVariantHelpers::resolve_filled_bar_label_color(
                                    $step_label_color,
                                    $is_step_active,
                                    $step_label_active_color,
                                    $step_label_inactive_color,
                                    $legacy_label_active_color,
                                    $legacy_label_inactive_color
                                );

                                $label_pct = isset( $column_width_percents[ $index ] ) ? (int) $column_width_percents[ $index ] : (int) floor( 100 / max( 1, $step_count ) );

                                $label_style = TemplateHelpers::get_style(
                                    [
                                        'margin'      => '8px 0 0',
                                        'padding'     => '0',
                                        'font-size'   => $label_font_size . 'px',
                                        'line-height' => '1.2',
                                        'color'       => $label_color,
                                        'font-family' => $label_font_family,
                                    ]
                                );
                                ?>
                                <td width="<?php echo esc_attr( (string) $label_pct ); ?>%" align="center" style="
                                <?php
                                echo esc_attr(
                                    TemplateHelpers::get_style(
                                        [
                                            'width'      => $label_pct . '%',
                                            'vertical-align' => 'top',
                                            'padding'    => '0',
                                            'text-align' => $label_td_align,
                                        ]
                                    )
                                );
                                ?>
                                                    ">
                                    <?php if ( '' !== $title ) : ?>
                                        <p style="<?php echo esc_attr( $label_style ); ?>"><?php echo esc_html( $title ); ?></p>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>

