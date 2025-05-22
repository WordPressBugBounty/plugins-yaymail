<?php

namespace YayMail\PreviewEmail;

use YayMail\Utils\SingletonTrait;
use YayMail\YayMailTemplate;

/**
 *
 * @method static PreviewEmailWoo get_instance()
 */
class PreviewEmailWoo {
    use SingletonTrait;

    public static $recipient;

    public static $order_id_sample = '12345';

    private function __construct() {}

    public static function email_preview_output( $order_id, $email_id, $email_address = '', $is_in_customizer = false ) {
        $emails              = wc()->mailer()->emails;
        $current_email       = null;
        $current_email_class = null;
        foreach ( $emails as $email_class => $email ) {
            if ( $email->id === $email_id ) {
                $current_email       = $email;
                $current_email_class = $email_class;
                break;
            }
        }
        if ( ! isset( $current_email, $current_email_class ) ) {
            throw new \Exception( 'Requested Email does not exist', 500 );
        }

        $order = wc_get_order( $order_id );

        if ( empty( $current_email ) ) {
            return;
        }

        $template = new YayMailTemplate( $email_id );

        if ( $template->is_enabled() ) {
            if ( empty( $order ) ) {
                $order_id = self::$order_id_sample;
            }

            if ( is_email( $email_address ) ) {
                self::$recipient = $email_address;
            } else {
                self::$recipient = '';
            }

            if ( empty( $current_email ) ) {
                return;
            }

            WC()->payment_gateways();
            WC()->shipping();

            $template         = $current_email->get_template( 'template_html' );
            $current_template = self::get_current_template( $current_email, $template );

            add_filter( 'woocommerce_email_recipient_' . $current_email->id, [ __CLASS__, 'no_recipient' ] );

            add_filter( 'woocommerce_new_order_email_allows_resend', '__return_true' );

            // Handle additional data for third party plugins
            $additional_data = apply_filters( 'yaymail_preview_email_woo_additional_order_id', false, $email_id, $order_id, $current_email );

            if ( $additional_data ) {
                if ( empty( $additional_data['error'] ) ) {
                    // Trigger the email for third party plugins
                    do_action( 'yaymail_preview_email_woo_additional_order_trigger', $current_email, $additional_data, $order_id );
                }
            } else {
                self::trigger_email( $email_class, $current_email, $order_id, $is_in_customizer );
            }

            $content = self::get_email_content( $current_email, $additional_data );

            remove_filter( 'woocommerce_new_order_email_allows_resend', '__return_true', 10 );
        } else {
            // Run when order_id = 12345 and template not enable
            add_filter(
                'woocommerce_email_preview_dummy_order',
                function ( $dummy_order ) use ( $order ) {
                    if ( ! empty( $order ) ) {
                        return $order;
                    }
                    return $dummy_order;
                },
                10,
                2
            );

            $email_preview = wc_get_container()->get( \Automattic\WooCommerce\Internal\Admin\EmailPreview\EmailPreview::class );
            $email_preview->set_email_type( $current_email_class );
            $message = $email_preview->render();
            $message = $email_preview->ensure_links_open_in_new_tab( $message );
            $content = $message;
        }//end if

        return [
            'html'             => yaymail_kses_post( $content ),
            'current_template' => $current_template,
            'subject'          => $current_email->get_subject(),
        ];
    }

    public static function get_dummy_order() {
        $product = new \WC_Product();
        $product->set_name( __( 'Dummy Product', 'woocommerce' ) );
        $product->set_price( 25 );

        $variation = new \WC_Product_Variation();
        $variation->set_name( __( 'Dummy Product Variation', 'woocommerce' ) );
        $variation->set_price( 20 );
        $variation->set_attributes(
            [
                __( 'Color', 'woocommerce' ) => __( 'Red', 'woocommerce' ),
                __( 'Size', 'woocommerce' )  => __( 'Small', 'woocommerce' ),
            ]
        );

        $order = new \WC_Order();
        if ( $product ) {
            $order->add_product( $product, 2, [ 'name' => 'Dummy Product' ] );
        }
        if ( $variation ) {
            $order->add_product( $variation, 1, [ 'name' => 'Dummy Product Variation' ] );
        }

        $dummy_order_data = [
            'id'                   => self::$order_id_sample,
            'date_created'         => time(),
            'currency'             => 'USD',
            'discount_total'       => 10,
            'shipping_total'       => 5,
            'total'                => 65,
            'payment_method_title' => __( 'Direct bank transfer', 'woocommerce' ),
            'customer_note'        => __( 'This is a customer note.', 'woocommerce' ),
            'billing_address'      => [
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'company'    => 'Company',
                'email'      => 'john@company.com',
                'phone'      => '555-555-5555',
                'address_1'  => '123 Fake Street',
                'city'       => 'Faketown',
                'postcode'   => '12345',
                'country'    => 'US',
                'state'      => 'CA',
            ],
            'shipping_address'     => [
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'company'    => 'Company',
                'email'      => 'john@company.com',
                'phone'      => '555-555-5555',
                'address_1'  => '123 Fake Street',
                'city'       => 'Faketown',
                'postcode'   => '12345',
                'country'    => 'US',
                'state'      => 'CA',
            ],
        ];

        $order->set_id( $dummy_order_data['id'] );
        $order->set_date_created( $dummy_order_data['date_created'] );
        $order->set_currency( $dummy_order_data['currency'] );
        $order->set_discount_total( $dummy_order_data['discount_total'] );
        $order->set_shipping_total( $dummy_order_data['shipping_total'] );
        $order->set_total( $dummy_order_data['total'] );
        $order->set_payment_method_title( $dummy_order_data['payment_method_title'] );
        $order->set_customer_note( $dummy_order_data['customer_note'] );
        $order->set_billing_address( $dummy_order_data['billing_address'] );
        $order->set_shipping_address( $dummy_order_data['shipping_address'] );

        return $order;
    }



    private static function get_current_template( $email, $template ) {
        $local_file             = $email->get_theme_template_file( $template );
        $core_file              = $email->template_base . $template;
        $template_file          = apply_filters( 'woocommerce_locate_core_template', $core_file, $template, $email->template_base, $email->id );
        $template_dir           = apply_filters( 'woocommerce_template_directory', 'woocommerce', $template );
        $base_template_location = plugin_basename( $template_file );
        return file_exists( $local_file ) ? trailingslashit( basename( get_stylesheet_directory() ) ) . $template_dir . '/' . $template : $base_template_location;
    }

    private static function trigger_email( $email_class, $email, $order_id, $is_in_customizer ) {
        try {
            if ( $email_class === 'WC_Email_Customer_New_Account' ) {
                $email->trigger( get_current_user_id() );
            } else {
                $order = $order_id === self::$order_id_sample ? self::get_dummy_order() : wc_get_order( $order_id );
                $email->set_object( $order );

                if ( ! $is_in_customizer && $order_id !== self::$order_id_sample ) {
                    $email->trigger( $order_id );
                }
            }//end if
        } catch ( \Exception $e ) {
            return [ 'error' => $e ];
        }//end try
    }

    private static function get_email_content( $email, $additional_data ) {
        if ( isset( $additional_data['error'] ) ) {
            return $additional_data['error'];
        } else {
            $email->email_type = 'html';
            $content           = $email->get_content();
            return apply_filters( 'woocommerce_mail_content', $email->style_inline( $content ) );
        }
    }

    public static function no_recipient( $recipient ): string {
        if ( self::$recipient !== '' ) {
            $recipient = self::$recipient;
        } else {
            $recipient = '';
        }
        return $recipient;
    }
}
