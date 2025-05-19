<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use YayMail\Utils\TemplateHelpers;

$template        = ! empty( $args['template'] ) ? $args['template'] : null;
$text_link_color = ! empty( $template ) ? $template->get_text_link_color() : YAYMAIL_COLOR_WC_DEFAULT;
$data            = isset( $args['element']['data'] ) ? $args['element']['data'] : [];
$is_placeholder  = isset( $args['is_placeholder'] ) ? $args['is_placeholder'] : false;


$product_title  = isset( $data['table_titles']['product_title'] ) ? $data['table_titles']['product_title'] : TemplateHelpers::get_content_as_placeholder( 'product_title', esc_html__( 'Products', 'woocommerce' ), $is_placeholder );
$expires_title  = isset( $data['table_titles']['expires_title'] ) ? $data['table_titles']['expires_title'] : TemplateHelpers::get_content_as_placeholder( 'expires_title', esc_html__( 'Expires', 'woocommerce' ), $is_placeholder );
$download_title = isset( $data['table_titles']['download_title'] ) ? $data['table_titles']['download_title'] : TemplateHelpers::get_content_as_placeholder( 'download_title', esc_html__( 'Download', 'woocommerce' ), $is_placeholder );

$table_td_style = TemplateHelpers::get_style(
    [
        'font-size'   => '14px',
        'padding'     => '12px',
        'text-align'  => yaymail_get_text_align(),
        'font-family' => TemplateHelpers::get_font_family_value( isset( $data['font_family'] ) ? $data['font_family'] : 'inherit' ),
        'color'       => isset( $data['text_color'] ) ? $data['text_color'] : 'inherit',
        'border'      => isset( $data['border_color'] ) ? '1px solid ' . $data['border_color'] : 'inherit',
    ]
);

$table_link_style = TemplateHelpers::get_style(
    [
        'color'       => $text_link_color,
        'font-family' => TemplateHelpers::get_font_family_value( isset( $data['font_family'] ) ? $data['font_family'] : 'inherit' ),
    ]
);
?>

<tbody style="<?php echo esc_attr( $table_td_style ); ?>">
    <tr style="<?php echo esc_attr( $table_td_style ); ?>">
        <th class="td" colspan="1" scope="col" style="<?php echo esc_attr( $table_td_style ); ?>"><?php yaymail_kses_post_e( $product_title ); ?></th>
        <th class="td" colspan="1" scope="col" style="<?php echo esc_attr( $table_td_style ); ?>"><?php yaymail_kses_post_e( $expires_title ); ?></th>
        <th class="td" colspan="1" scope="col" style="<?php echo esc_attr( $table_td_style ); ?>"><?php yaymail_kses_post_e( $download_title ); ?></th>
    </tr>
    <tr style="<?php echo esc_attr( $table_td_style ); ?>">
        <td class="td" colspan="1" scope="col" style="<?php echo esc_attr( $table_td_style ); ?>">
            <a href="" style="<?php echo esc_attr( $table_link_style ); ?>" > <?php esc_html_e( 'Downloadable Product', 'yaymail' ); ?></a>
        </td>
        <td class="td" colspan="1" scope="col" style="<?php echo esc_attr( $table_td_style ); ?>">
            <time datetime="2021-02-13" title="1613174400"> <?php echo wp_kses_post( wc_format_datetime( new WC_DateTime() ) ); ?></time>
        </td>
        <td class="td" colspan="1" scope="col" style="<?php echo esc_attr( $table_td_style ); ?>">
            <a href="" style="<?php echo esc_attr( $table_link_style ); ?>" ><?php esc_html_e( 'Download.doc', 'yaymail' ); ?></a>
        </td>
    </tr>
</tbody>

