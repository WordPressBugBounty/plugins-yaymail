<?php

namespace YayMail\Integrations;

use YayMail\PostTypes\TemplatePostType;
use YayMail\Utils\SingletonTrait;

/**
 * RankMath
 * * @method static RankMath get_instance()
 */
class RankMath {
    use SingletonTrait;

    protected function __construct() {

        if ( ! class_exists( 'RankMath' ) ) {
            return;
        }

        add_action( 'init', [ $this, 'init' ], PHP_INT_MAX );
    }

    public function init() {
        $titles_settings = get_option( 'rank-math-options-titles', [] );
        if ( empty( $titles_settings ) ) {
            return;
        }

        $title_meta_key = 'pt_' . TemplatePostType::POST_TYPE . '_robots';

        if ( empty( $titles_settings[ $title_meta_key ] ) ) {
            $titles_settings[ $title_meta_key ] = [];
        }

        $titles_settings[ $title_meta_key ][] = 'noindex';

        update_option( 'rank-math-options-titles', $titles_settings );
    }
    
}