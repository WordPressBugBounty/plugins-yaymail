<?php

namespace YayMail\Controllers;

use YayMail\Abstracts\BaseController;
use YayMail\TemplateLibrary\TemplateLibraryService;
use YayMail\Utils\SingletonTrait;

/**
 * Template Library Controller
 *
 * @method static TemplateLibraryController get_instance()
 */
class TemplateLibraryController extends BaseController {
    use SingletonTrait;

    protected function __construct() {
        $this->init_hooks();
    }

    /**
     * Register REST routes.
     *
     * @return void
     */
    protected function init_hooks() {
        register_rest_route(
            YAYMAIL_REST_NAMESPACE,
            '/template-library',
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'exec_get_templates' ],
                    'permission_callback' => [ $this, 'permission_callback' ],
                    'args'                => [
                        'email_type' => [
                            'type'     => 'string',
                            'required' => true,
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Exec wrapper for getting list of templates.
     *
     * @param \WP_REST_Request $request Request.
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function exec_get_templates( \WP_REST_Request $request ) {
        return $this->exec( [ $this, 'get_templates' ], $request );
    }

    /**
     * Get list of template summaries for current email type.
     *
     * @param \WP_REST_Request $request Request.
     *
     * @return array
     */
    public function get_templates( \WP_REST_Request $request ) {
        $email_type = sanitize_text_field( $request->get_param( 'email_type' ) );

        if ( empty( $email_type ) ) {
            return [
                'success' => false,
                'message' => __( 'Template name is required.', 'yaymail' ),
            ];
        }

        $templates = TemplateLibraryService::get_instance()->get_list( $email_type );

        return [
            'success'   => true,
            'templates' => $templates,
        ];
    }
}
