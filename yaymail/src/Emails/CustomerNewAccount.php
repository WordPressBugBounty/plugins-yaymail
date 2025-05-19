<?php

namespace YayMail\Emails;

use YayMail\Abstracts\BaseEmail;
use YayMail\Elements\ElementsLoader;
use YayMail\Utils\SingletonTrait;

/**
 * CustomerNewAccount Class
 *
 * @method static CustomerNewAccount get_instance()
 */
class CustomerNewAccount extends BaseEmail {
    use SingletonTrait;

    public $email_types = [ YAYMAIL_NON_ORDER_EMAILS ];

    protected function __construct() {
        $emails          = \WC_Emails::instance()->get_emails();
        $email           = $emails['WC_Email_Customer_New_Account'];
        $this->id        = $email->id;
        $this->title     = $email->get_title();
        $this->recipient = $email->is_customer_email() ? __( 'Customer', 'woocommerce' ) : __( 'Admin', 'woocommerce' );

        add_filter( 'wc_get_template', [ $this, 'get_template_file' ], 10, 3 );
    }

    public function get_default_elements() {
        $email_title = __( 'Welcome to {site_title}', 'woocommerce' );
        $email_title = str_replace( '{site_title}', '', $email_title );
        // translators: customer username.
        $email_hi = sprintf( esc_html__( 'Hi %s,', 'woocommerce' ), '[yaymail_customer_username]' );
        // translators: %1$s: site name, %2$s: customer username, %3$s: account url .
        $email_text        = sprintf( esc_html__( 'Thanks for creating an account on %1$s. Your username is %2$s. You can access your account area to view orders, change your password, and more at: %3$s', 'woocommerce' ), '[yaymail_site_name]', '<strong>[yaymail_customer_username]</strong>', '[yaymail_user_account_url]' );
        $email_text_1      = __( 'We look forward to seeing you soon.', 'woocommerce' );
        $password_generate = '[yaymail_set_password_link]';

        $default_elements = ElementsLoader::load_elements(
            [
                [
                    'type' => 'Logo',
                ],
                [
                    'type'       => 'Heading',
                    'attributes' => [
                        'rich_text' => $email_title . '[yaymail_site_name]',
                    ],
                ],
                [
                    'type'       => 'Text',
                    'attributes' => [
                        'rich_text' => '<p><span>' . $email_hi . '<br><br>' . $email_text . '</span></p><p style=\"margin: 26px 0px 0px 0px;\"><span>' . $password_generate . '</span></p><p style=\"margin: 26px 0px 0px 0px;\"><span>' . $email_text_1 . '</span></p>',
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
        return YAYMAIL_PLUGIN_PATH . 'templates/emails/customer-new-account.php';
    }
}
