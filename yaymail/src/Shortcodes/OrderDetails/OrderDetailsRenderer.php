<?php

namespace YayMail\Shortcodes\OrderDetails;

use YayMail\Utils\TemplateHelpers;
use YayMail\Utils\Helpers;

/**
 * @method: static OrderDetailsRenderer get_instance()
 */
class OrderDetailsRenderer {

    public $item_totals = [];

    public $order = null;

    public $order_note = '';

    public $element_data = null;

    public $is_placeholder = false;

    public $titles = [];

    public $show_product_item_cost = false;

    public $colspan_value = '2';

    public function __construct( $order, $element_data, $is_placeholder ) {
        $yaymail_settings             = yaymail_settings();
        $this->show_product_item_cost = isset( $yaymail_settings['show_product_item_cost'] ) ? boolval( $yaymail_settings['show_product_item_cost'] ) : false;
        $this->colspan_value          = $is_placeholder ? '{{show_product_item_cost}}' : ( $this->show_product_item_cost ? '3' : '2' );
        $this->element_data           = $element_data;
        $this->is_placeholder         = $is_placeholder;
        $this->initialize_titles();

        if ( ! Helpers::is_woocommerce_order( $order ) ) {
            $this->initialize_sample_data();
        } else {
            $this->initialize_order_data( $order );
        }
    }

    public function initialize_titles() {
        $this->titles = [
            'product'        => isset( $this->element_data['product_title'] ) ? $this->element_data['product_title'] : TemplateHelpers::get_content_as_placeholder( 'product_title', esc_html__( 'Product', 'woocommerce' ), $this->is_placeholder ),
            'cost'           => isset( $this->element_data['cost_title'] ) ? $this->element_data['cost_title'] : TemplateHelpers::get_content_as_placeholder( 'cost_title', esc_html__( 'Cost', 'woocommerce' ), $this->is_placeholder ),
            'quantity'       => isset( $this->element_data['quantity_title'] ) ? $this->element_data['quantity_title'] : TemplateHelpers::get_content_as_placeholder( 'quantity_title', esc_html__( 'Quantity', 'woocommerce' ), $this->is_placeholder ),
            'price'          => isset( $this->element_data['price_title'] ) ? $this->element_data['price_title'] : TemplateHelpers::get_content_as_placeholder( 'price_title', esc_html__( 'Price', 'woocommerce' ), $this->is_placeholder ),
            'cart_subtotal'  => isset( $this->element_data['cart_subtotal_title'] ) ? $this->element_data['cart_subtotal_title'] : TemplateHelpers::get_content_as_placeholder( 'cart_subtotal_title', esc_html__( 'Subtotal:', 'woocommerce' ), $this->is_placeholder ),
            'shipping'       => isset( $this->element_data['shipping_title'] ) ? $this->element_data['shipping_title'] : TemplateHelpers::get_content_as_placeholder( 'shipping_title', esc_html__( 'Shipping:', 'woocommerce' ), $this->is_placeholder ),
            'discount'       => isset( $this->element_data['discount_title'] ) ? $this->element_data['discount_title'] : TemplateHelpers::get_content_as_placeholder( 'discount_title', esc_html__( 'Discount:', 'woocommerce' ), $this->is_placeholder ),
            'payment_method' => isset( $this->element_data['payment_method_title'] ) ? $this->element_data['payment_method_title'] : TemplateHelpers::get_content_as_placeholder( 'payment_method_title', esc_html__( 'Payment method:', 'woocommerce' ), $this->is_placeholder ),
            'order_total'    => isset( $this->element_data['order_total_title'] ) ? $this->element_data['order_total_title'] : TemplateHelpers::get_content_as_placeholder( 'order_total_title', esc_html__( 'Total:', 'woocommerce' ), $this->is_placeholder ),
            'order_note'     => isset( $this->element_data['order_note_title'] ) ? $this->element_data['order_note_title'] : TemplateHelpers::get_content_as_placeholder( 'order_note_title', esc_html__( 'Note:', 'woocommerce' ), $this->is_placeholder ),
        ];
    }

    private function initialize_sample_data() {
        $this->item_totals = [
            'cart_subtotal'  => [
                'label' => $this->titles['cart_subtotal'],
                'value' => wc_price( 18 ),
            ],
            'shipping'       => [
                'label' => $this->titles['shipping'],
                'value' => __( 'Free shipping', 'yaymail' ),
            ],
            'payment_method' => [
                'label' => $this->titles['payment_method'],
                'value' => __( 'Direct bank transfer', 'yaymail' ),
            ],
            'order_total'    => [
                'label' => $this->titles['order_total'],
                'value' => wc_price( 18 ),
            ],
        ];
        $this->order_note  = 'YayMail';
    }

    private function initialize_order_data( $order ) {
        $this->item_totals = $order->get_order_item_totals();
        $this->order_note  = $order->get_customer_note();
        $this->order       = $order;
    }

    public function get_styles() {
        return TemplateHelpers::get_style(
            [
                'padding'      => '12px',
                'font-size'    => '14px',
                'text-align'   => yaymail_get_text_align(),
                'font-family'  => TemplateHelpers::get_font_family_value( isset( $this->element_data['font_family'] ) ? $this->element_data['font_family'] : 'inherit' ),
                'color'        => isset( $this->element_data['text_color'] ) ? $this->element_data['text_color'] : 'inherit',
                'border-width' => '1px',
                'border-style' => 'solid',
                'border-color' => isset( $this->element_data['border_color'] ) ? $this->element_data['border_color'] : 'inherit',
            ]
        );
    }

    public function get_styles_product_image() {
        return TemplateHelpers::get_style(
            [
                'margin-bottom' => '5px',
                'margin-right'  => '5px',
            ]
        );
    }

    public function get_structure_table() {
        return apply_filters(
            'yaymail_order_details_structure_table',
            [
                'items'  => [
                    'product'  => [
                        'label'    => $this->titles['product'],
                        'col_span' => apply_filters( 'yaymail_order_item_product_title_colspan', 1, $this->element_data ),
                        'style'    => [
                            'word-wrap' => 'break-word',
                            // Width value must match CSS in /email-template-container/elements/order-details/index.scss line 40
                            'width'     => $this->show_product_item_cost ? '40%' : '45%',
                        ],
                    ],
                    'cost'     => [
                        'label'     => $this->titles['cost'],
                        'col_span'  => apply_filters( 'yaymail_order_item_cost_colspan', 1, $this->element_data ),
                        'min-width' => '65px',
                    ],
                    'quantity' => [
                        'label'     => $this->titles['quantity'],
                        'col_span'  => apply_filters( 'yaymail_order_item_quantity_colspan', 1, $this->element_data ),
                        'min-width' => '85px',
                    ],
                    'price'    => [
                        'label'    => $this->titles['price'],
                        'col_span' => apply_filters( 'yaymail_order_item_price_colspan', 1, $this->element_data ),
                        'style'    => [
                            'word-wrap' => 'break-word',
                            // Width value must match CSS in /email-template-container/elements/order-details/index.scss line 40
                            'width'     => $this->show_product_item_cost ? '28%' : '38%',
                        ],

                    ],
                ],
                'footer' => [
                    'label_col_span' => $this->colspan_value,
                    'value_col_span' => 1,
                    'hidden_rows'    => [],
                ],
            ]
        );
    }

    public function render() {
        $style = $this->get_styles() . 'padding: 0; border-collapse: collapse;';
        ?>
            <table class="yaymail-order-details-table" cellspacing="0" cellpadding="6" width="100%" style="<?php echo esc_attr( $style ); ?>" border="1">
            <?php
            $this->render_heading();
            $this->render_order_items();
            $this->render_footer();
            ?>
            </table>
            <?php
    }

    public function render_heading() {
        $styles          = $this->get_styles();
        $structure_table = $this->get_structure_table();
        $structure_items = isset( $structure_table['items'] ) ? $structure_table['items'] : [];
        if ( isset( $structure_items['cost'] ) && ! $this->show_product_item_cost && ! $this->is_placeholder ) {
            unset( $structure_items['cost'] );
        }
        ?>
            <thead class="yaymail_element_head_order_details yaymail_element_head_order_item">
                <tr>
                    <?php
                    foreach ( $structure_items as $key => $item ) :
                        if ( isset( $item['width'] ) ) {
                            $width = 'width: ' . $item['width'] . ';';
                        } else {
                            $width = '';
                        }
                        echo '<th class="td yaymail_item_' . esc_attr( $key ) . '_title" colspan="' . esc_attr( $item['col_span'] ) . '" scope="col" style="' . esc_attr( $styles ) . ';' . esc_attr( $width ) . ';"><span>' . esc_html( $item['label'] ) . '</span></th>';
                    endforeach;
                    ?>
                </tr>
            </thead>
            <?php
    }

    public function render_order_items() {
        $structure_table = $this->get_structure_table();
        $structure_items = isset( $structure_table['items'] ) ? $structure_table['items'] : [];
        if ( isset( $structure_items['cost'] ) && ! $this->show_product_item_cost && ! $this->is_placeholder ) {
            unset( $structure_items['cost'] );
        }
        ?>
        <tbody class="yaymail_element_body_order_details yaymail_element_body_order_item">
        <?php
        if ( null === $this->order ) {
            $this->render_sample_items( $structure_items );
        } else {
            $this->render_real_items( $structure_items );
        }
        ?>
        </tbody>
        <?php
    }

    public function render_sample_items( $structure_items ) {
        $yaymail_settings = yaymail_settings();
        $style            = $this->get_styles();
        $is_placeholder   = $this->is_placeholder;

        $show_image         = isset( $yaymail_settings['show_product_image'] ) ? boolval( $yaymail_settings['show_product_image'] ) : false;
        $image_position     = isset( $yaymail_settings['product_image_position'] ) ? $yaymail_settings['product_image_position'] : 'top';
        $image_height       = isset( $yaymail_settings['product_image_height'] ) ? $yaymail_settings['product_image_height'] : '30';
        $image_width        = isset( $yaymail_settings['product_image_width'] ) ? $yaymail_settings['product_image_width'] : '30';
        $image_position     = isset( $yaymail_settings['product_image_position'] ) ? $yaymail_settings['product_image_position'] : 'top';
        $show_image         = isset( $yaymail_settings['show_product_image'] ) ? boolval( $yaymail_settings['show_product_image'] ) : false;
        $show_sku           = isset( $yaymail_settings['show_product_sku'] ) ? boolval( $yaymail_settings['show_product_sku'] ) : false;
        $show_des           = isset( $yaymail_settings['show_product_description'] ) ? boolval( $yaymail_settings['show_product_description'] ) : false;
        $show_hyper_links   = isset( $yaymail_settings['show_product_hyper_links'] ) ? boolval( $yaymail_settings['show_product_hyper_links'] ) : false;
        $show_regular_price = isset( $yaymail_settings['show_product_regular_price'] ) ? boolval( $yaymail_settings['show_product_regular_price'] ) : false;

        $image_url             = wc_placeholder_img_src();
        $image                 = $is_placeholder ? "<img width='{{product_image_width}}px' height='{{product_image_height}}px' src='{$image_url}' alt='product image'/>" : "<img width='{$image_width}px' height='{$image_height}px' src='{$image_url}' alt='product image'/>";
        $sku                   = __( 'sku', 'yaymail' );
        $short_description     = __( 'Product short description', 'yaymail' );
        $product_name          = __( 'Happy YayCommerce', 'yaymail' );
        $product_permalink     = '#';
        $product_hyper_link    = "<a href='{$product_permalink}' target='_blank'>{$product_name}</a>";
        $product_regular_price = '10';
        ?>
        <tr class="order_item">
            <?php foreach ( $structure_items as $key => $structure_item ) : ?>
                <?php
                if ( isset( $structure_item['width'] ) ) {
                    $width = 'width: ' . $structure_item['width'] . ';';
                } else {
                    $width = '';
                }
                $item_style = isset( $structure_item['style'] ) ? $structure_item['style'] : [];
                if ( ! empty( $item_style ) ) {
                    $item_style_string = TemplateHelpers::get_style( $item_style );
                } else {
                    $item_style_string = '';
                }
                $column_style = $style . $width . $item_style_string;
                ?>
                <td colspan="<?php echo esc_attr( $structure_item['col_span'] ); ?>" class="td yaymail_item_<?php echo esc_attr( $key ); ?>_content" scope="row" style="<?php echo esc_attr( $column_style ); ?>">
                    <?php
                    switch ( $key ) :
                        case 'product':
                            // Show title/image etc.
                            if ( ( $show_image && 'bottom' !== $image_position ) || $is_placeholder ) {
                                echo wp_kses_post( "<div class='yaymail-product_image_position__top'>" );
                                require YAYMAIL_PLUGIN_PATH . 'templates/shortcodes/order-details/order-items/image-content.php';
                                echo ( '</div>' );
                            }
                            ?>

                            <!-- Product details -->
                            <div class='yaymail-product-details'>
                            <?php

                            // Product name.
                            require YAYMAIL_PLUGIN_PATH . 'templates/shortcodes/order-details/order-items/product-name-content.php';

                            // SKU.
                            if ( ( $show_sku && ! empty( $sku ) ) || ( $is_placeholder && ! empty( $sku ) ) ) {
                                require YAYMAIL_PLUGIN_PATH . 'templates/shortcodes/order-details/order-items/sku-content.php';
                            }

                            // Product Description.
                            if ( ( $show_des && ! empty( $short_description ) ) || ( $is_placeholder && ! empty( $short_description ) ) ) {
                                require YAYMAIL_PLUGIN_PATH . 'templates/shortcodes/order-details/order-items/product-short-description-content.php';
                            }

                            // Show title/image etc in bottom.
                            if ( ( $show_image && 'bottom' === $image_position ) || $is_placeholder ) {
                                echo wp_kses_post( "<div class='yaymail-product_image_position__bottom'>" );
                                require YAYMAIL_PLUGIN_PATH . 'templates/shortcodes/order-details/order-items/image-content.php';
                                echo ( '</div>' );
                            }
                            ?>
                            </div>
                            <!-- End Product details -->
                            <?php
                            break;
                        case 'cost':
                            echo wp_kses_post( wc_price( 9 ) );
                            break;
                        case 'quantity':
                            esc_html_e( '2', 'yaymail' );
                            break;
                        case 'price':
                            // Show product regular price.
                            if ( ( $show_regular_price && ! empty( $product_regular_price ) ) || ( $is_placeholder && ! empty( $product_regular_price ) ) ) {
                                ?>
                                <del class="yaymail-product-regular-price" style="padding-right:5px"> <?php echo wp_kses_post( wc_price( $product_regular_price ) ); ?> </del>
                                <?php
                            }
                            echo wp_kses_post( wc_price( 18 ) );
                            break;
                        default:
                            echo wp_kses_post( do_action( 'yaymail_order_details_item_' . $key . '_content', null, $this->order, $this->element_data, true ) );
                            break;
                    endswitch;
                    ?>
                </td>
            <?php endforeach; ?>
        </tr>
            <?php
    }

    public function render_real_items( $structure_items ) {
        $style_image_position_left = TemplateHelpers::get_style(
            [
                'float' => 'left',
            ]
        );

        $args_data = [
            'order'                => $this->order,
            'text_style'           => $this->get_styles(),
            'styles_product_image' => isset( yaymail_settings()['product_image_position'] ) & 'left' === yaymail_settings()['product_image_position'] ? $this->get_styles_product_image() . $style_image_position_left : $this->get_styles_product_image(),
            'is_placeholder'       => $this->is_placeholder,
            'structure_items'      => $structure_items,
        ];

        // Just has data when send mail
        if ( isset( $args['element'] ) && ! empty( $args['element'] ) ) {
            $args_data['element'] = $args['element'];
        }
        $path_data    = apply_filters( 'yaymail_order_details_items', 'templates/shortcodes/order-details/order-items/main.php' );
        $html         = yaymail_get_content( $path_data, $args_data );
        $allowed_html = TemplateHelpers::wp_kses_allowed_html();
        echo wp_kses( $html, $allowed_html );
    }

    public function render_footer() {
        $structure_table  = $this->get_structure_table();
        $structure_footer = isset( $structure_table['footer'] ) ? $structure_table['footer'] : [];

        if ( empty( $structure_footer ) ) {
            return;
        }
        // TODO: change class name
        ?>
        <tfoot class="yaymail_element_foot_order_details yaymail_element_foot_order_item">
        <?php
        $this->render_item_totals( $structure_footer );

        if ( ! empty( $this->order ) && $this->order->get_customer_note() ) {
            $this->render_customer_note( $structure_footer );
        }
        ?>
        </tfoot>
        <?php
    }

    public function render_item_totals( $structure_footer ) {
        $index = 0;
        foreach ( $this->item_totals as $key => $total ) {
            if ( in_array( $key, $structure_footer['hidden_rows'], true ) ) {
                continue;
            }
            ++$index;
            $tr_class              = "yaymail-order-detail-row-{$key}";
            $can_apply_placeholder = $this->is_placeholder && isset( $this->titles[ $key ] );
            $label                 = TemplateHelpers::get_content_as_placeholder( "{$key}_title", esc_html( isset( $this->titles[ $key ] ) ? $this->titles[ $key ] : $total['label'] ), $can_apply_placeholder );
            $style                 = $this->get_styles() . TemplateHelpers::get_style(
                [
                    'border-top-width' => 1 === $index ? '4px' : '0',
                ]
            );
            ?>
            <tr class="<?php echo esc_attr( $tr_class ); ?>">
                <th class="td" scope="row" colspan="<?php echo esc_attr( isset( $structure_footer['label_col_span'] ) ? $structure_footer['label_col_span'] : $this->colspan_value ); ?>" style="<?php echo esc_attr( $style ); ?>"><?php echo wp_kses_post( $label ); ?></th>
                <td class="td" colspan="<?php echo esc_attr( isset( $structure_footer['value_col_span'] ) ? $structure_footer['value_col_span'] : 1 ); ?>" style="<?php echo esc_attr( $style ); ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
            </tr>
            <?php
        }
    }

    public function render_customer_note() {
        if ( ! empty( $this->order_note ) ) :
            $style = $this->get_styles();
            ?>
        <tr class="yaymail-order-detail-row-order_note">
            <th class="td" scope="row" colspan="<?php echo esc_attr( $this->colspan_value ); ?>" style="<?php echo esc_attr( $style ); ?>;"><?php echo esc_html( $this->titles['order_note'] ); ?></th>
            <td class="td" style="<?php echo esc_attr( $style ); ?>;"><?php echo wp_kses_post( nl2br( wptexturize( $this->order_note ) ) ); ?></td>
        </tr>
            <?php
            endif;
    }
}
