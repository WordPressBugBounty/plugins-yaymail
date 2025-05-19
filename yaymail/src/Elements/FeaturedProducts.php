<?php
namespace YayMail\Elements;

use YayMail\Abstracts\BaseElement;
use YayMail\Utils\SingletonTrait;

/**
 * Featured Products Elements
 */
class FeaturedProducts extends BaseElement {

    use SingletonTrait;

    protected static $type = 'featured_products';

    public $available_email_ids = [];

    public static function get_data( $attributes = [] ) {
        self::$icon = '<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 20 20">
  <g>
    <path d="M7.7,2.7v9.75H2.5V2.7h5.2M8.7,1.2H1.5c-.28,0-.5.22-.5.5v11.75c0,.28.22.5.5.5h7.2c.28,0,.5-.22.5-.5V1.7c0-.28-.22-.5-.5-.5h0Z"/>
    <g>
      <path d="M8,16.49H2.21c-.41,0-.75-.34-.75-.75s.34-.75.75-.75h5.79c.41,0,.75.34.75.75s-.34.75-.75.75Z"/>
      <path d="M7.19,18.95H3.01c-.41,0-.75-.34-.75-.75s.34-.75.75-.75h4.19c.41,0,.75.34.75.75s-.34.75-.75.75Z"/>
    </g>
  </g>
  <g>
    <path d="M17.5,2.7v9.75h-5.2V2.7h5.2M18.5,1.2h-7.2c-.28,0-.5.22-.5.5v11.75c0,.28.22.5.5.5h7.2c.28,0,.5-.22.5-.5V1.7c0-.28-.22-.5-.5-.5h0Z"/>
    <path d="M17.79,16.49h-5.79c-.41,0-.75-.34-.75-.75s.34-.75.75-.75h5.79c.41,0,.75.34.75.75s-.34.75-.75.75Z"/>
    <path d="M16.99,18.95h-4.19c-.41,0-.75-.34-.75-.75s.34-.75.75-.75h4.19c.41,0,.75.34.75.75s-.34.75-.75.75Z"/>
  </g>
</svg>';

        return [
            'id'              => uniqid(),
            'type'            => self::$type,
            'name'            => __( 'Featured Products', 'yaymail' ),
            'icon'            => self::$icon,
            'group'           => 'block',
            'available'       => false,
            'disabled_reason' => 'This element is available in YayMail Pro',
            'position'        => 220,
            'data'            => [],
        ];
    }
}
