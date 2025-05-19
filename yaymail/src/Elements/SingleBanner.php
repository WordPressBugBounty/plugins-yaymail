<?php
namespace YayMail\Elements;

use YayMail\Abstracts\BaseElement;
use YayMail\Utils\SingletonTrait;

/**
 * Single Banner Elements
 */
class SingleBanner extends BaseElement {

    use SingletonTrait;

    protected static $type = 'single_banner';

    public $available_email_ids = [];

    public static function get_data( $attributes = [] ) {
        self::$icon = '<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 20 20">
  <path d="M12.51,17.63H2.75c-.91,0-1.66-.79-1.66-1.75V3.1c0-.96.74-1.75,1.66-1.75h14.51c.91,0,1.66.79,1.66,1.75v11.72h-1.5V3.1c0-.15-.09-.25-.16-.25H2.75c-.06,0-.16.1-.16.25v12.79c0,.15.09.25.16.25h9.76v1.5Z"/>
  <path d="M12.1,13.59c-.65,0-1.29-.28-1.73-.82l-2.53-3.06c-.13-.16-.31-.25-.51-.27-.19-.02-.39.04-.54.17l-4.26,3.6c-.31.27-.79.23-1.06-.09-.27-.32-.23-.79.09-1.06l4.26-3.61c.46-.39,1.06-.58,1.64-.52.6.05,1.15.34,1.53.81l2.53,3.06c.26.31.69.37,1.02.13l5-3.73c.33-.25.8-.18,1.05.15.25.33.18.8-.15,1.05l-5,3.73c-.4.3-.87.44-1.33.44Z"/>
  <path d="M13.26,8.57c-1.09,0-1.98-.89-1.98-1.98s.89-1.98,1.98-1.98,1.98.89,1.98,1.98-.89,1.98-1.98,1.98ZM13.26,6.12c-.26,0-.48.21-.48.48s.21.48.48.48.48-.21.48-.48-.21-.48-.48-.48Z"/>
  <g>
    <rect x="14.79" y="14.95" width="1.5" height="3.56" transform="translate(-5.02 6.79) rotate(-21.32)"/>
    <path d="M13.51,16.55l.64-3.37,1.52,1.13s.02.01.03.02l1.21.9-1.97.77s-.01,0-.02,0l-1.41.55Z"/>
  </g>
</svg>';

        return [
            'id'              => uniqid(),
            'type'            => self::$type,
            'name'            => __( 'Single Banner', 'yaymail' ),
            'icon'            => self::$icon,
            'group'           => 'block',
            'available'       => false,
            'disabled_reason' => 'This element is available in YayMail Pro',
            'position'        => 240,
            'data'            => [],
        ];
    }
}
