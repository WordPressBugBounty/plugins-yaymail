<?php
namespace YayMail\Elements;

use YayMail\Abstracts\BaseElement;
use YayMail\Utils\SingletonTrait;

/**
 * Simple Offer Elements
 */
class SimpleOffer extends BaseElement {

    use SingletonTrait;

    protected static $type = 'simple_offer';

    public $available_email_ids = [];

    public static function get_data( $attributes = [] ) {
        self::$icon = '<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 20 20">
  <path d="M17.56,3.61H7.3c-.27,0-.45.09-.63.27l-.27.27-.27-.27c-.18-.18-.36-.27-.63-.27h-3.06c-.81,0-1.44.63-1.44,1.35v9.99c0,.81.63,1.44,1.44,1.44h3.06c.27,0,.45-.09.63-.27l.27-.27.27.27c.18.18.45.27.63.27h10.26c.81,0,1.44-.63,1.44-1.44V4.96c0-.72-.63-1.35-1.44-1.35ZM17.65,14.86c0,.09-.09.18-.18.18H7.48l-.54-.54v-1.71h-1.17v1.71l-.54.54h-2.7c-.09,0-.18-.09-.18-.18V5.14c0-.09.09-.18.18-.18h2.7l.54.54v1.71h1.08v-1.71l.54-.54h9.99c.09,0,.18.09.18.18v9.72h.09ZM5.77,10.54h1.08v1.08h-1.08v-1.08ZM5.77,8.29h1.08v1.08h-1.08v-1.08ZM10.27,9.55c.9,0,1.71-.72,1.71-1.71,0-.9-.72-1.71-1.71-1.71-.9,0-1.71.72-1.71,1.71.09.99.81,1.71,1.71,1.71ZM10.27,7.3c.36,0,.63.27.63.63s-.27.54-.54.54-.54-.27-.54-.54c-.09-.36.09-.63.45-.63ZM15.49,7.03l-5.58,6.75c-.09.09-.27.18-.45.18-.09,0-.27,0-.36-.09-.27-.18-.27-.54-.09-.81l5.58-6.75c.18-.18.54-.27.72-.09.36.18.36.54.18.81ZM14.23,10.45c-.9,0-1.71.72-1.71,1.71,0,.9.72,1.71,1.71,1.71s1.71-.72,1.71-1.71-.72-1.71-1.71-1.71ZM14.23,12.7c-.27,0-.54-.27-.54-.54s.27-.54.54-.54.54.27.54.54c.09.27-.18.54-.54.54Z"/>
</svg>';

        return [
            'id'              => uniqid(),
            'type'            => self::$type,
            'name'            => __( 'Simple Offer', 'yaymail' ),
            'icon'            => self::$icon,
            'group'           => 'block',
            'available'       => false,
            'disabled_reason' => 'This element is available in YayMail Pro',
            'position'        => 230,
            'data'            => [],
        ];
    }
}
