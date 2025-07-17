<?php
namespace YayMail\Elements;

use YayMail\Abstracts\BaseElement;
use YayMail\Utils\SingletonTrait;
/**
 * RatingStars Elements
 */
class RatingStars extends BaseElement {

    use SingletonTrait;

    protected static $type = 'rating_stars';

    public $available_email_ids = [ YAYMAIL_ALL_EMAILS ];

    public static function get_data( $attributes = [] ) {
        self::$icon = '<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 20 20">
  <path d="M6.98,9.32c-1.07,0-1.93-.87-1.93-1.93s.87-1.93,1.93-1.93,1.93.87,1.93,1.93-.87,1.93-1.93,1.93ZM6.98,6.95c-.24,0-.43.19-.43.43s.19.43.43.43.43-.19.43-.43-.19-.43-.43-.43Z"/>
  <path d="M10,2.5c4.14,0,7.5,3.36,7.5,7.5s-3.36,7.5-7.5,7.5-7.5-3.36-7.5-7.5,3.36-7.5,7.5-7.5M10,1C5.03,1,1,5.03,1,10s4.03,9,9,9,9-4.03,9-9S14.97,1,10,1h0Z"/>
  <path d="M4.12,16.28c-.22,0-.43-.09-.58-.27-.26-.32-.22-.79.1-1.06l8.42-6.92c.31-.25.75-.22,1.02.06l4.65,4.93c.29.3.27.78-.03,1.06-.3.28-.78.27-1.06-.03l-4.17-4.42-7.88,6.48c-.14.11-.31.17-.48.17Z"/>
</svg>';

        return [
            'id'        => uniqid(),
            'type'      => self::$type,
            'name'      => __( 'Rating Stars', 'yaymail' ),
            'icon'      => self::$icon,
            'group'     => 'basic',
            'available' => true,
            'position'  => 151,
            'data'      => [
                'align'                => ElementsHelper::get_align( $attributes ),
                'padding'              => [
                    'value_path'    => 'padding',
                    'component'     => 'Spacing',
                    'title'         => __( 'Padding', 'yaymail' ),
                    'default_value' => isset( $attributes['padding'] ) ? $attributes['padding'] : [
                        'top'    => '15',
                        'right'  => '15',
                        'bottom' => '15',
                        'left'   => '15',
                    ],
                    'type'          => 'style',
                ],
                'total_stars'          => [
                    'value_path'    => 'total_stars',
                    'component'     => 'NumberInput',
                    'title'         => __( 'Total stars', 'yaymail' ),
                    'default_value' => isset( $attributes['total_stars'] ) ? $attributes['total_stars'] : '5',
                    'extras_data'   => [
                        'min' => 1,
                        'max' => 10,
                    ],
                    'type'          => 'style',
                ],
                'active_stars'         => [
                    'value_path'    => 'active_stars',
                    'component'     => 'NumberInput',
                    'title'         => __( 'Active stars', 'yaymail' ),
                    'default_value' => isset( $attributes['active_stars'] ) ? $attributes['active_stars'] : '5',
                    'extras_data'   => [
                        'min'            => 0,
                        'max'            => 10,
                        'max_dependency' => 'total_stars',
                    ],
                    'type'          => 'style',
                ],
                'active_stars_color'   => ElementsHelper::get_color(
                    $attributes,
                    [
                        'title'         => __( 'Active stars color', 'yaymail' ),
                        'description'   => __( 'The color of the active stars', 'yaymail' ),
                        'value_path'    => 'active_stars_color',
                        'default_value' => isset( $attributes['active_stars_color'] ) ? $attributes['active_stars_color'] : '#FFD700',
                    ]
                ),
                'inactive_stars_color' => ElementsHelper::get_color(
                    $attributes,
                    [
                        'title'         => __( 'Inactive stars color', 'yaymail' ),
                        'description'   => __( 'The color of the inactive stars', 'yaymail' ),
                        'value_path'    => 'inactive_stars_color',
                        'default_value' => isset( $attributes['inactive_stars_color'] ) ? $attributes['inactive_stars_color'] : '#E0E0E0',
                    ]
                ),
                'background_color'     => ElementsHelper::get_color(
                    $attributes,
                    [
                        'default_value' => isset( $attributes['background_color'] ) ? $attributes['background_color'] : '#fff',
                    ]
                ),
                'size'                 => ElementsHelper::get_dimension(
                    $attributes,
                    [
                        'value_path'    => 'size',
                        'title'         => __( 'Size', 'yaymail' ),
                        'default_value' => isset( $attributes['size'] ) ? $attributes['size'] : '40',
                        'extras_data'   => [
                            'min' => 10,
                            'max' => 100,
                        ],
                    ]
                ),
                'spacing'              => ElementsHelper::get_dimension(
                    $attributes,
                    [
                        'value_path'    => 'spacing',
                        'title'         => __( 'Spacing', 'yaymail' ),
                        'default_value' => isset( $attributes['spacing'] ) ? $attributes['spacing'] : '10',
                        'extras_data'   => [
                            'min' => 10,
                            'max' => 25,
                        ],
                        'type'          => 'style',
                    ]
                ),
            ],
        ];
    }
}
