<?php

namespace YayMail\Emails;

use YayMail\Abstracts\BaseEmail;
use YayMail\Elements\ElementsLoader;
use YayMail\Utils\SingletonTrait;

/**
 * CustomerRefundedOrder Class
 *
 * @method static CustomerRefundedOrder get_instance()
 */
class CustomerRefundedOrder extends BaseEmail {
    use SingletonTrait;

    protected function __construct() {
        $emails          = \WC_Emails::instance()->get_emails();
        $email           = $emails['WC_Email_Customer_Refunded_Order'];
        $this->id        = $email->id;
        $this->title     = $email->get_title();
        $this->recipient = $email->is_customer_email() ? __( 'Customer', 'woocommerce' ) : __( 'Admin', 'woocommerce' );

        add_filter( 'wc_get_template', [ $this, 'get_template_file' ], 10, 3 );
    }

    public function get_default_elements() {
        $email_title = '[yaymail_get_heading]';
        // translators: customer name.
        $email_hi = sprintf( esc_html__( 'Hi %s,', 'woocommerce' ), '[yaymail_billing_first_name]' );
        // translators: site name.
        $email_text      = sprintf( esc_html__( 'Your order on %s has been refunded. There are more details below for your reference:', 'woocommerce' ), '[yaymail_site_name]' );
        $additional_text = __( 'We hope to see you again soon.', 'woocommerce' );

        $default_elements = ElementsLoader::load_elements(
            [
                [
                    'type' => 'Logo',
                ],
                [
                    'type'       => 'Heading',
                    'attributes' => [
                        'rich_text' => $email_title,
                    ],
                ],
                [
                    'type'       => 'Text',
                    'attributes' => [
                        'rich_text' => '<p><span>' . $email_hi . '<br /></span></p><p><span>' . $email_text . '</span></p>',
                    ],
                ],
                [
                    'type' => 'OrderDetails',
                ],
                [
                    'type' => 'BillingShippingAddress',
                ],
                [
                    'type'       => 'Text',
                    'attributes' => [
                        'rich_text' => '<p><span>' . $additional_text . '</span></p>',
                        'padding'   => [
                            'top'    => '0',
                            'right'  => '50',
                            'bottom' => '38',
                            'left'   => '50',
                        ],
                    ],
                ],
                [
                    'type' => 'Footer',
                ],
            ]
        );

        return $default_elements;
    }


    public function get_template_path() {
        return YAYMAIL_PLUGIN_PATH . 'templates/emails/customer-refunded-order.php';
    }
}
