<?php

namespace YayMail\Shortcodes;

use YayMail\Utils\Helpers;
use YayMail\Utils\SingletonTrait;
use YayMail\Utils\Logger;
/**
 * @method: static OrderMetaShortcodes init()
 */
class OrderMetaShortcodes {

    use SingletonTrait;

    private $logger;

    protected function __construct() {
        $this->logger = new Logger();
        add_filter( 'yaymail_extra_shortcodes', [ $this, 'get_order_meta_shortcodes' ], 10, 2 );
    }

    /**
     * Init order meta shortcodes
     *
     * @param array $shortcodes The shortcodes array.
     * @param array $data The data array.
     *
     * @return array The shortcodes array.
     */
    public function get_order_meta_shortcodes( $shortcodes, $data ) {
        $order = $data['render_data']['order'] ?? null;

        if ( ! $order && ! empty( $data['render_data']['order_id'] ) ) {
            $order = wc_get_order( $data['render_data']['order_id'] );
        }

        if ( empty( $order ) ) {
            return $shortcodes;
        }

        $metadata = $order->get_meta_data();

        foreach ( $metadata as $meta_item ) {
            $data = $meta_item->get_data();

            $field = $data['key'];

            $description = Helpers::snake_case_to_capitalized_words( $field ) . ' (' . $field . ')';

            $new_shortcode = [
                'name'          => "yaymail_order_meta:{$field}",
                'description'   => $description,
                'group'         => 'order_meta',
                'callback'      => [ $this, 'order_meta_callback' ],
                'callback_args' => [
                    'meta_item' => $meta_item,
                    'field'     => $field,
                ],
                'attributes'    => [
                    'is_date' => false,
                ],
            ];

            $shortcodes[] = $new_shortcode;
        }//end foreach

        return $shortcodes;
    }

    public function order_meta_callback( $data, $shortcode_attrs = [] ) {
        $field     = $data['field'] ?? '';
        $meta_item = $data['meta_item'] ?? '';

        if ( empty( $meta_item ) || ! method_exists( $meta_item, 'get_data' ) ) {
            return '';
        }

        $meta_data = $meta_item->get_data();
        $value     = $meta_data['value'];

        if ( $shortcode_attrs['is_date'] ) {
            $date = is_numeric( $value )
                ? \DateTime::createFromFormat( 'U', $value )
                : \DateTime::createFromFormat( 'Ymd', $value );

            if ( $date ) {
                return date_i18n( wc_date_format(), $date->getTimestamp() );
            }

            $this->logger->log( "Order meta shortcode: field {$field} with value {$value} is not a valid date" );
            return nl2br( $value );
        }

        if ( is_array( $value ) || is_object( $value ) ) {
            $str = isset( $value['extra'] ) ? $value['extra'] : implode( ', ', $value );
            return str_replace( '|', '<br />', $str );
        }

        return $value;
    }
}
