<?php
namespace YayMail\Templates\DefaultTemplate;

defined( 'ABSPATH' ) || exit;

class CustomerFailedOrder {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function getTemplates() {
		/*
		@@@ Html default send email.
		@@@ Note: Add characters '\' before special characters in a string.
		@@@ Example: font-family: \'Helvetica Neue\'...
		*/
		$emailTitle          = esc_html__( 'Sorry, your order was unsuccessful', 'woocommerce' );
		$emailText1          = esc_html__( 'Hi ', 'woocommerce' ) . esc_html( do_shortcode( '[yaymail_billing_first_name]' ) ) . ',';
		$emailText2          = esc_html__( 'Unfortunately, we couldn\'t complete your order due to an issue with your payment method.', 'woocommerce' );
		$emailText3          = esc_html__( "If you'd like to continue with your purchase, please return to ", 'woocommerce' ) . esc_html( do_shortcode( '[yaymail_site_name]' ) ) . esc_html__( ' and try a different method of payment.', 'woocommerce' );
		$emailText4          = esc_html__( 'Your order details are as follows:', 'woocommerce' );
		$textShippingAddress = esc_html__( 'Shipping Address', 'woocommerce' );
		$textBillingAddress  = esc_html__( 'Billing Address', 'woocommerce' );

		/*
		@@@ Elements default when reset template.
		@@@ Note 1: Add characters '\' before special characters in a string.
		@@@ example 1: "family": "\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif",

		@@@ Note 2: Add characters '\' before special characters in a string.
		@@@ example 2: "<h1 style=\"font-family: \'Helvetica Neue\',...."
		*/

		// Elements
		$elements =
		'[{
        "id": "8ffa62b5-7258-42cc-ba53-7ae69638c1fe",
        "type": "Logo",
        "nameElement": "Logo",
        "settingRow": {
          "backgroundColor": "#ECECEC",
          "align": "center",
          "pathImg": "",
          "paddingTop": "15",
          "paddingRight": "0",
          "paddingBottom": "15",
          "paddingLeft": "0",
          "width": "172",
          "url": "#"
        }
      }, {
        "id": "802bfe24-7af8-48af-ac5e-6560a81345b3",
        "type": "ElementText",
        "nameElement": "Email Heading",
        "settingRow": {
          "content": "<h1 style=\"font-size: 30px; font-weight: 300; line-height: normal; margin: 0px; color: inherit;\">' . $emailTitle . '</h1>",
          "backgroundColor": "#7f54b3",
          "textColor": "#ffffff",
          "family": "Helvetica,Roboto,Arial,sans-serif",
          "paddingTop": "41",
          "paddingRight": "48",
          "paddingBottom": "41",
          "paddingLeft": "48"
        }
      }, {
        "id": "b035d1f1-0cfe-41c5-b79c-0478f144ef5f",
        "type": "ElementText",
        "nameElement": "Text",
        "settingRow": {
          "content": "<p style=\"margin: 0px;\"><span style=\"font-size: 14px;\">' . $emailText1 . '</span></p><br/><p style=\"margin: 0px;\"><span style=\"font-size: 14px;\">' . $emailText2 . '</span></p><br/><p style=\"margin: 0px;\"><span style=\"font-size: 14px;\">' . $emailText3 . '</span></p><br/><p style=\"margin: 0px;\"><span style=\"font-size: 14px;\">' . $emailText4 . '</span></p>",
          "backgroundColor": "#fff",
          "textColor": "#636363",
          "family": "Helvetica,Roboto,Arial,sans-serif",
          "paddingTop": "32",
          "paddingRight": "50",
          "paddingBottom": "0",
          "paddingLeft": "50"
        }
      },
      {
        "id": "ad422370-f762-4a26-92de-c4cf3878h0oi",
        "type": "OrderItem",
        "nameElement": "Order Item",
        "settingRow": {
          "contentBefore": "[yaymail_items_border_before]",
          "contentAfter": "[yaymail_items_border_after]",
          "contentTitle": "[yaymail_items_border_title]",
          "content": "[yaymail_items_border_content]",
          "backgroundColor": "#fff",
          "titleColor" : "#7f54b3",
          "textColor": "#636363",
          "borderColor": "#e5e5e5",
          "family": "Helvetica,Roboto,Arial,sans-serif",
          "paddingTop": "15",
          "paddingRight": "50",
          "paddingBottom": "15",
          "paddingLeft": "50"
        }
      },';
		if ( class_exists( 'WC_Subscription' ) ) {
			$elements .= '{
        "id": "os422370-f762-4a26-92de-c4cf3878h0oi",
        "type": "AddonWooSubscriptionInformation",
        "nameElement": "Woo Subscription Information",
        "settingRow": {
          "backgroundColor":"#fff",
          "borderColor":"#e5e5e5",
          "content":"This is content",
          "contentAfter":"[yaymail_items_border_after]",
          "contentBefore":"[yaymail_items_border_before]",
          "contentTitle":"Subscription information",
          "family":"Helvetica,Roboto,Arial,sans-serif",
          "paddingBottom":"15",
          "paddingLeft":"50",
          "paddingRight":"50",
          "paddingTop":"15",
          "textColor":"#636363",
          "titleColor":"#7f54b3",
          "titleID": "ID",
          "titleStartDate": "Start date",
          "titleEndDate": "End date",
          "titleRecurringTotal": "Recurring total"
        }
      },';
		}
		$elements .= '{
      "id": "de242956-a617-4213-9107-138842oi4tch",
      "type": "BillingAddress",
      "nameElement": "Billing Shipping Address",
      "settingRow": {
        "nameColumn": "BillingShippingAddress",
        "contentTitle": "[yaymail_billing_shipping_address_title]",
        "checkBillingShipping": "[yaymail_billing_shipping_address_title]",
        "titleBilling": "' . $textBillingAddress . '",
        "titleShipping": "' . $textShippingAddress . '",
        "content": "[yaymail_billing_shipping_address_content]",
        "titleColor" : "#7f54b3",
        "backgroundColor": "#fff",
        "borderColor": "#e5e5e5",
        "textColor": "#636363",
        "family": "Helvetica,Roboto,Arial,sans-serif",
        "paddingTop": "15",
        "paddingRight": "50",
        "paddingBottom": "15",
        "paddingLeft": "50"
      }
    },
    {
      "id": "b39bf2e6-8c1a-4384-a5ec-37663da27c8ds",
      "type": "ElementText",
      "nameElement": "Footer",
      "settingRow": {
        "content": "<p style=\"font-size: 14px;margin: 0px 0px 16px; text-align: center;\">[yaymail_site_name]&nbsp;- Built with <a style=\"color: #7f54b3; font-weight: normal; text-decoration: underline;\" href=\"https://woocommerce.com\" target=\"_blank\" rel=\"noopener\">WooCommerce</a></p>",
        "backgroundColor": "#ececec",
        "textColor": "#8a8a8a",
        "family": "Helvetica,Roboto,Arial,sans-serif",
        "paddingTop": "15",
        "paddingRight": "50",
        "paddingBottom": "15",
        "paddingLeft": "50"
      }
    }]';

		// Templates Customer Failed Order
		$templates = array(
			'customer_failed_order' => array(),
		);

		$templates['customer_failed_order']['elements'] = $elements;
		return $templates;
	}
}
