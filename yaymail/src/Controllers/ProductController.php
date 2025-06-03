<?php

namespace YayMail\Controllers;

use YayMail\Abstracts\BaseController;
use YayMail\Models\ProductModel;
use YayMail\Utils\SingletonTrait;

/**
 * Product Controller
 *
 * @method static ProductController get_instance()
 */
class ProductController extends BaseController {
    use SingletonTrait;

    private $model = null;

    protected function __construct() {
        $this->model = ProductModel::get_instance();
        $this->init_hooks();
    }

    protected function init_hooks() {

        register_rest_route(
            YAYMAIL_REST_NAMESPACE,
            '/product/categories',
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'exec_get_categories' ],
                    'permission_callback' => [ $this, 'permission_callback' ],
                ],
            ]
        );
        register_rest_route(
            YAYMAIL_REST_NAMESPACE,
            '/product/tags',
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'exec_get_tags' ],
                    'permission_callback' => [ $this, 'permission_callback' ],
                ],
            ]
        );
        register_rest_route(
            YAYMAIL_REST_NAMESPACE,
            '/product',
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'exec_get_products' ],
                    'permission_callback' => [ $this, 'permission_callback' ],
                ],
            ]
        );
    }


    /**
     * Handle get product categories
     */
    public function exec_get_categories( \WP_REST_Request $request ) {
        return $this->exec( [ $this, 'get_categories' ], $request );
    }
    public function get_categories( \WP_REST_Request $request ) {
        $params['search_string'] = sanitize_text_field( $request->get_param( 'search_string' ) );
        $params['page_num']      = sanitize_text_field( $request->get_param( 'page_num' ) );
        $params['page_size']     = sanitize_text_field( $request->get_param( 'page_size' ) );
        $params['term_type']     = 'product_cat';

        $result = $this->model->get_terms( $params, [ 'id' => 'term_id' ] );

        return $result;
    }

    /**
     * Handle get product tags
     */
    public function exec_get_tags( \WP_REST_Request $request ) {
        return $this->exec( [ $this, 'get_tags' ], $request );
    }
    public function get_tags( \WP_REST_Request $request ) {
        $params['search_string'] = sanitize_text_field( $request->get_param( 'search_string' ) );
        $params['page_num']      = sanitize_text_field( $request->get_param( 'page_num' ) );
        $params['page_size']     = sanitize_text_field( $request->get_param( 'page_size' ) );
        $params['term_type']     = 'product_tag';
        $result                  = $this->model->get_terms( $params, [ 'id' => 'term_id' ] );

        return $result;
    }

    /**
     * Handle get products
     */
    public function exec_get_products( \WP_REST_Request $request ) {
        return $this->exec( [ $this, 'get_products' ], $request );
    }
    public function get_products( \WP_REST_Request $request ) {
        $params['search_string'] = sanitize_text_field( $request->get_param( 'search_string' ) );
        $params['page_num']      = sanitize_text_field( $request->get_param( 'page_num' ) );
        $params['page_size']     = sanitize_text_field( $request->get_param( 'page_size' ) );
        $result                  = $this->model->get_terms( $params );

        return $result;
    }
}
