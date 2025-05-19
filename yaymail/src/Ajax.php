<?php

namespace YayMail;

use YayMail\Utils\SingletonTrait;
use YayMail\Models\SettingModel;
use YayMail\Models\TemplateModel;

/**
 * I18n Logic
 *
 * @method static Ajax get_instance()
 */
class Ajax {
    use SingletonTrait;

    protected function __construct() {
        $this->init_hooks();
    }

    protected function init_hooks() {
        add_action( 'wp_ajax_yaymail_preview_mail', [ $this, 'preview_mail' ] );
        add_action( 'wp_ajax_yaymail_preview_mail_for_woo', [ $this, 'preview_mail_for_woo' ] );
        add_action( 'wp_ajax_yaymail_preview_mail_search_order', [ $this, 'preview_mail_search_order' ] );
        add_action( 'wp_ajax_yaymail_send_test_mail', [ $this, 'send_test_mail' ] );
        add_action( 'wp_ajax_yaymail_install_yaysmtp', [ $this, 'install_yaysmtp' ] );
        add_action( 'wp_ajax_yaymail_get_custom_hook_html', [ $this, 'get_custom_hook_html' ] );
        add_action( 'wp_ajax_yaymail_get_template_data_onload', [ $this, 'get_template_data_onload' ] );
        add_action( 'wp_ajax_yaymail_export_templates', [ $this, 'export_templates' ] );
        add_action( 'wp_ajax_yaymail_import_templates', [ $this, 'import_templates' ] );
        add_action( 'wp_ajax_yaymail_review', [ $this, 'yaymail_review' ] );
        add_action( 'wp_ajax_yaymail_change_ghf_tour', [ $this, 'change_ghf_tour' ] );
        add_action( 'wp_ajax_yaymail_dismiss_multi_select_notice', [ $this, 'dismiss_multi_select_notice' ] );
    }

    public function sanitize( $array ) {

        return wp_kses_post_deep( $array );
    }

    public function process_plugin_installer( $slug ) {
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
        require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

        $api = plugins_api(
            'plugin_information',
            [
                'slug'   => $slug,
                'fields' => [
                    'short_description' => false,
                    'sections'          => false,
                    'requires'          => false,
                    'rating'            => false,
                    'ratings'           => false,
                    'downloaded'        => false,
                    'last_updated'      => false,
                    'added'             => false,
                    'tags'              => false,
                    'compatibility'     => false,
                    'homepage'          => false,
                    'donate_link'       => false,
                ],
            ]
        );

        $skin = new \WP_Ajax_Upgrader_Skin();

        $plugin_upgrader = new \Plugin_Upgrader( $skin );

        try {
            $result = $plugin_upgrader->install( $api->download_link );

            if ( is_wp_error( $result ) ) {
                yaymail_get_logger( $result );
            }

            return true;
        } catch ( \Exception $exception ) {
            yaymail_get_logger( $exception );
        }

        return false;
    }

    public function install_yaysmtp() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'yaymail_frontend_nonce' ) ) {
            return wp_send_json_error( [ 'mess' => __( 'Verify nonce failed', 'yaymail' ) ] );
        }
        try {
            $is_installed = $this->process_plugin_installer( 'yaysmtp' );

            if ( false === $is_installed ) {
                wp_send_json_error( [ 'message' => $is_installed ] );
            }

            $result = activate_plugin( 'yaysmtp/yay-smtp.php' );

            if ( is_wp_error( $result ) ) {
                return wp_send_json_error( [ 'mess' => esc_html( $result->get_error_message() ) ] );
            }

            wp_send_json_success(
                [
                    'installed' => null === $result,
                ]
            );

        } catch ( \Error $error ) {
            yaymail_get_logger( $error );
        } catch ( \Exception $exception ) {
            yaymail_get_logger( $exception );
        }//end try
    }

    public function send_test_mail() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'yaymail_frontend_nonce' ) ) {
            return wp_send_json_error( [ 'mess' => __( 'Verify nonce failed', 'yaymail' ) ] );
        }
        try {
            $template_name = isset( $_POST['template_name'] ) ? sanitize_text_field( wp_unslash( $_POST['template_name'] ) ) : '';
            $order_id      = isset( $_POST['order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) : 'sample_order';
            $email         = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';

            if ( empty( $template_name ) ) {
                return wp_send_json_error( [ 'mess' => __( 'Can\'t find template', 'yaymail' ) ] );
            }

            if ( empty( $order_id ) ) {
                return wp_send_json_error( [ 'mess' => __( 'Can\'t find order', 'yaymail' ) ] );
            }

            if ( empty( $email ) ) {
                return wp_send_json_error( [ 'mess' => __( 'Can\'t find email', 'yaymail' ) ] );
            }

            $template = new YayMailTemplate( $template_name );

            $render_data = [];

            if ( empty( $order_id ) || ( 'sample_order' === $order_id ) ) {
                $render_data['is_sample'] = true;
            } else {
                $render_data['order'] = wc_get_order( $order_id );
            }

            $render_data['is_customized_preview'] = true;
            // check if email template on preview and send test mail

            update_option( 'yaymail_default_email_test', $email );

            $html = $template->get_content( $render_data );

            $headers        = "Content-Type: text/html\r\n";
            $class_wc_email = \WC_Emails::instance();
            $subject        = __( 'Email Test', 'yaymail' );
            $send_mail      = $class_wc_email->send( $email, $subject, $html, $headers, [] );

            wp_send_json_success(
                [
                    'email'             => $email,
                    'send_mail_success' => $send_mail,
                ]
            );
        } catch ( \Error $error ) {
            yaymail_get_logger( $error );
        } catch ( \Exception $exception ) {
            yaymail_get_logger( $exception );
        }//end try
    }

    public function preview_mail() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'yaymail_frontend_nonce' ) ) {
            return wp_send_json_error( [ 'mess' => __( 'Verify nonce failed', 'yaymail' ) ] );
        }
        try {
            $order_id         = isset( $_POST['order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) : 'sample_order';
            $template_data    = isset( $_POST['template_data'] ) ? $this->sanitize( wp_unslash( $_POST['template_data'] ) ) : [];
            $unsaved_settings = isset( $_POST['unsaved_settings'] ) ? $this->sanitize( wp_unslash( $_POST['unsaved_settings'] ) ) : [];

            if ( empty( $template_data ) ) {
                return wp_send_json_error( [ 'mess' => __( 'Can\'t find template', 'yaymail' ) ] );
            }

            if ( empty( $order_id ) ) {
                return wp_send_json_error( [ 'mess' => __( 'Can\'t find order', 'yaymail' ) ] );
            }

            $template = new YayMailTemplate( $template_data['name'] );

            if ( ! empty( $unsaved_settings ) ) {
                global $yaymail_unsaved_settings;
                $yaymail_unsaved_settings = $unsaved_settings;
            }

            $template->set_background_color( $template_data['background_color'] );
            $template->set_text_link_color( $template_data['text_link_color'] );

            $template->set_elements( $template_data['elements'] );

            $render_data = [];

            if ( empty( $order_id ) || ( 'sample_order' === $order_id ) ) {
                $render_data['is_sample'] = true;
            } else {
                $render_data['order'] = wc_get_order( $order_id );
            }

            $render_data['is_customized_preview'] = true;
            // check if email template on preview and send test mail

            $html = $template->get_content( $render_data );

            // TODO: render with passing settings

            $email_address        = wp_get_current_user()->user_email ?? 'sample@example.com';
            $email_preview_output = apply_filters( 'yaymail_preview_email', [], $order_id, $template_data['name'], $email_address );
            if ( empty( $email_preview_output ) ) {
                $email_preview_output = PreviewEmail\PreviewEmailWoo::email_preview_output( $order_id, $template_data['name'], $email_address, true );
            }

            if ( ! empty( $email_address ) && ! empty( $email_preview_output['html'] ) ) {

                $subject = $email_preview_output['subject'];
            }

            wp_send_json_success(
                [
                    'html'          => $html,
                    'subject'       => $subject,
                    'email_address' => $email_address,
                ]
            );
        } catch ( \Error $error ) {
            yaymail_get_logger( $error );
        } catch ( \Exception $exception ) {
            yaymail_get_logger( $exception );
        }//end try
    }

    public function preview_mail_for_woo() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'yaymail_frontend_nonce' ) ) {
            return wp_send_json_error( [ 'mess' => __( 'Verify nonce failed', 'yaymail' ) ] );
        }
        try {
            $template_name   = isset( $_POST['template_name'] ) ? sanitize_text_field( wp_unslash( $_POST['template_name'] ) ) : '';
            $search_order_id = isset( $_POST['search_order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['search_order_id'] ) ) : null;
            $email_address   = isset( $_POST['email_address'] ) ? sanitize_text_field( wp_unslash( $_POST['email_address'] ) ) : '';

            $email_preview_output = apply_filters( 'yaymail_preview_email', [], $search_order_id, $template_name, $email_address );
            if ( empty( $email_preview_output ) ) {
                $email_preview_output = PreviewEmail\PreviewEmailWoo::email_preview_output( $search_order_id, $template_name, $email_address );
            }

            if ( ! empty( $email_address ) && ! empty( $email_preview_output['html'] ) ) {
                $headers        = "Content-Type: text/html\r\n";
                $class_wc_email = \WC_Emails::instance();
                $subject        = __( 'Email Preview', 'yaymail' );
                $send_mail      = $class_wc_email->send( $email_address, $subject, $email_preview_output['html'], $headers, [] );
            }

            wp_send_json_success(
                [
                    'html'              => ! empty( $email_preview_output['html'] ) ? $email_preview_output['html'] : __( 'No email content found', 'yaymail' ),
                    'current_template'  => ! empty( $email_preview_output['current_template'] ) ? $email_preview_output['current_template'] : __( 'No template found', 'yaymail' ),
                    'subject'           => ! empty( $email_preview_output['subject'] ) ? $email_preview_output['subject'] : __( 'No subject found', 'yaymail' ),
                    'send_mail_success' => $send_mail,
                ]
            );
        } catch ( \Error $error ) {
            yaymail_get_logger( $error );
        } catch ( \Exception $exception ) {
            yaymail_get_logger( $exception );
        }//end try
    }

    public function preview_mail_search_order() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'yaymail_frontend_nonce' ) ) {
            return wp_send_json_error( [ 'mess' => __( 'Verify nonce failed', 'yaymail' ) ] );
        }
        try {
            global $wpdb;

            $order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) : 'sample_order';

            $list_order = [];

            $table_name = $wpdb->prefix . 'wc_orders';
            $query      = $wpdb->prepare( "SELECT ID FROM $table_name WHERE type = 'shop_order' AND CAST(ID AS CHAR) LIKE %s", $wpdb->esc_like( $order_id ) . '%' );

            if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) { // phpcs:ignore
                $table_name = "{$wpdb->prefix}posts";
                $query      = $wpdb->prepare( "SELECT ID FROM $table_name WHERE post_type = 'shop_order' AND CAST(ID AS CHAR) LIKE %s", $wpdb->esc_like( $order_id ) . '%' );
            }

            $order_ids = $wpdb->get_col( $query ); // phpcs:ignore

            if ( $order_ids ) {
                foreach ( $order_ids as $order_id ) {
                    $list_order[] = [
                        'value' => $order_id,
                        'label' => '#order: ' . $order_id,
                    ];
                }
            }

            wp_send_json_success(
                [
                    'list_order' => $list_order,
                ]
            );
        } catch ( \Error $error ) {
            yaymail_get_logger( $error );
        } catch ( \Exception $exception ) {
            yaymail_get_logger( $exception );
        }//end try
    }



    public function export_templates() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'yaymail_frontend_nonce' ) ) {
            return wp_send_json_error( [ 'mess' => __( 'Verify nonce failed', 'yaymail' ) ] );
        }
        try {
            $templates = isset( $_POST['templates'] ) ? $_POST['templates'] : [];
            // TODO: sanitize
            $default     = [
                'post_type'      => 'yaymail_template',
                'post_status'    => [ 'publish', 'pending', 'future' ],
                'posts_per_page' => '-1',
                'meta_query'     => [
                    [
                        'key'     => '_yaymail_template',
                        'value'   => $templates,
                        'compare' => 'IN',
                    ],
                ],
            ];
            $export_data = [];
            $query       = new \WP_Query( $default );
            if ( $query->have_posts() ) {
                $posts = $query->get_posts();
                foreach ( $posts as $post ) {
                    $template_name = get_post_meta( $post->ID, '_yaymail_template', true );
                    $elements      = get_post_meta( $post->ID, '_yaymail_elements', true );
                    $language      = get_post_meta( $post->ID, '_yaymail_template_language', true );
                    $file_name     = "{$template_name}.json";
                    if ( empty( $language ) ) {
                        $export_data[] = [
                            'file_name'      => $file_name,
                            'templates_data' => [
                                'template' => $template_name,
                                'elements' => $elements,
                                'language' => $language,
                            ],
                        ];
                    }
                }
            }
            wp_reset_postdata();
            wp_send_json_success(
                [
                    'message'   => __( 'Export successfully', 'yaymail' ),
                    'data'      => $export_data,
                    'file_name' => 'yaymail_customizer_templates_' . gmdate( 'm-d-Y' ),
                ]
            );
        } catch ( \Error $error ) {
            yaymail_get_logger( $error );
        } catch ( \Exception $exception ) {
            yaymail_get_logger( $exception );
        }//end try
    }

    public function import_templates() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'yaymail_frontend_nonce' ) ) {
            return wp_send_json_error( [ 'mess' => __( 'Verify nonce failed', 'yaymail' ) ] );
        }
        try {
            if ( ! empty( $_FILES ) ) {
                $import_count = $this->process_import( $_FILES );
                if ( $import_count > 0 ) {
                    wp_send_json_success( [ 'message' => __( 'Imported successfully ', 'yaymail' ) . $import_count . __( ' templates', 'yaymail' ) ] );
                } else {
                    wp_send_json_error( [ 'message' => __( 'Import failed.', 'yaymail' ) ] );
                }
            } else {
                wp_send_json_error( [ 'message' => __( 'Not found import files.', 'yaymail' ) ] );
            }
        } catch ( \Error $error ) {
            yaymail_get_logger( $error );
        } catch ( \Exception $exception ) {
            yaymail_get_logger( $exception );
        }
    }

    public function process_import( $files ) {
        global $wp_filesystem;
        if ( empty( $wp_filesystem ) ) {
            require_once ABSPATH . '/wp-admin/includes/file.php';
            WP_Filesystem();
        }
        $import_count = 0;
        foreach ( $files as $file ) {
            if ( isset( $file['type'] ) ) {
                if ( 'application/json' === $file['type'] ) {
                    if ( ! empty( $file['tmp_name'] ) ) {
                        $file_tmp_name = sanitize_text_field( $file['tmp_name'] );
                        $file_content  = $wp_filesystem->get_contents( $file_tmp_name );
                        $file_content  = json_decode( $file_content, true );
                        if ( ! isset( $file_content['template'] ) ) {
                            continue;
                        }
                        $import_template = $file_content['template'];
                        $import_elements = $file_content['elements'];
                        $import_language = $file_content['language'];
                        if ( ! empty( $import_template ) ) {
                            $query_args = [
                                'post_type'      => 'yaymail_template',
                                'post_status'    => [ 'publish', 'pending', 'future' ],
                                'posts_per_page' => '-1',
                                'meta_query'     => [
                                    'relation' => 'AND',
                                    [
                                        'key'     => '_yaymail_template',
                                        'value'   => $import_template,
                                        'compare' => '=',
                                    ],
                                    [
                                        'key'     => '_yaymail_template_language',
                                        'value'   => ( empty( $import_language ) || 'en' === $import_language ) ? '' : $import_language,
                                        'compare' => ( empty( $import_language ) || 'en' === $import_language ) ? 'NOT EXISTS' : '=',
                                    ],
                                ],
                            ];

                            $query = new \WP_Query( $query_args );
                            if ( $query->have_posts() ) {
                                $posts = $query->get_posts();
                                foreach ( $posts as $post ) {
                                    update_post_meta( $post->ID, '_yaymail_elements', $import_elements );
                                }
                            }
                            ++$import_count;
                            wp_reset_postdata();
                        }//end if
                    }//end if
                }//end if
            }//end if
        }//end foreach
        return $import_count;
    }

    /**
     * Process a custom hook request and generate HTML content.
     *
     * This function handles a custom hook request, generates HTML content based on the provided data and attributes.
     * It is designed to be used as an AJAX callback.
     *
     * @example $_POST['data'] =
     * [
     *     'template_data' => YayMail\YayMailTemplate,
     *     'order_id' => 'sample_order',
     *     'attributes' => [
     *         [
     *             'name' => 'hook',
     *             'value' => 'your_hook'
     *         ],
     *         [
     *             'name' => 'background_color',
     *             'value' => '#ffffff'
     *         ]
     *     ]
     * ]
     *
     * @return void This function sends a JSON response with HTML content or error messages.
     */
    public function get_custom_hook_html() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'yaymail_frontend_nonce' ) ) {
            return wp_send_json_error( [ 'mess' => __( 'Verify nonce failed', 'yaymail' ) ] );
        }
        try {
            $attributes = isset( $_POST['data']['attributes'] ) ? $_POST['data']['attributes'] : []; // phpcs:ignore
            if ( empty( $attributes ) ) {
                return wp_send_json_error( [ 'mess' => __( 'Attributes empty', 'yaymail' ) ] );
            }

            /**
             * Build data for shortcode
             */
            $template_model = \YayMail\Models\TemplateModel::get_instance();
            $data           = [];
            if ( ! empty( $_POST['data']['template_data'] ) ) {
                $data = \YayMail\Models\TemplateModel::get_shortcode_executor_data( sanitize_text_field( wp_unslash( $_POST['data']['template_data'] ) ), sanitize_text_field( wp_unslash( $_POST['data']['order_id'] ) ) );

                $data['template']->set_props( sanitize_text_field( wp_unslash( $_POST['data']['template_data'] ) ) );
            }

            $hook_shortcodes = \YayMail\Shortcodes\HookShortcodes::get_instance();
            $html            = $hook_shortcodes->yaymail_handle_custom_hook_shortcode( $data, $attributes );
            wp_send_json_success(
                [
                    'html' => $html,
                ]
            );
        } catch ( \Error $error ) {
            yaymail_get_logger( $error );
        } catch ( \Exception $exception ) {
            yaymail_get_logger( $exception );
        }//end try
    }

    /**
     * Get all needed data when load YayMail template to customizer.
     */
    public function get_template_data_onload() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'yaymail_frontend_nonce' ) ) {
            return wp_send_json_error( [ 'mess' => __( 'Verify nonce failed', 'yaymail' ) ] );
        }
        try {
            $setting_model = SettingModel::get_instance();
            $settings_data = $setting_model->find_all();

            $template_name = isset( $_POST['data']['template_name'] ) ? sanitize_text_field( $_POST['data']['template_name'] ) : 'new_order';
            $order_id      = isset( $_POST['data']['order_id'] ) ? sanitize_text_field( $_POST['data']['order_id'] ) : 'sample_order';

            $template_model = TemplateModel::get_instance();

            $shortcodes_data = $template_model->get_shortcodes_by_template_name_and_order_id( $template_name, $order_id );

            $templates_data = apply_filters( 'yaymail_get_all_templates', $template_model->find_all() );

            $selected_template_data = $template_model->find_by_name( $template_name );

            $elements_data = TemplateModel::get_elements_for_template( $template_name );

            wp_send_json_success(
                [
                    'settings_data'          => $settings_data,
                    'templates_data'         => $templates_data,
                    'selected_template_data' => $selected_template_data,
                    'elements_data'          => $elements_data,
                    'shortcodes_data'        => $shortcodes_data,
                ]
            );
        } catch ( \Error $error ) {
            yaymail_get_logger( $error );
            wp_send_json_error( [ 'mess' => $error->getMessage() ] );
        } catch ( \Exception $exception ) {
            yaymail_get_logger( $exception );
            wp_send_json_error( [ 'mess' => $exception->getMessage() ] );
        }//end try
    }

    public function yaymail_review() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'yaymail_frontend_nonce' ) ) {
            return wp_send_json_error( [ 'mess' => __( 'Verify nonce failed', 'yaymail' ) ] );
        }
        try {

            $yaymail_review = update_option( 'yaymail_review', true );

            wp_send_json_success(
                [
                    'reviewed' => $yaymail_review,
                ]
            );

        } catch ( \Error $error ) {
            yaymail_get_logger( $error );
        } catch ( \Exception $exception ) {
            yaymail_get_logger( $exception );
        }
    }

    public function change_ghf_tour() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'yaymail_frontend_nonce' ) ) {
            return wp_send_json_error( [ 'mess' => __( 'Verify nonce failed', 'yaymail' ) ] );
        }

        try {
            $next_move = isset( $_POST['next_move'] ) ? sanitize_text_field( wp_unslash( $_POST['next_move'] ) ) : 'initial';
            $ghf_tour  = update_option( 'yaymail_ghf_tour', $next_move );

            wp_send_json_success(
                [
                    'ghf_tour' => $ghf_tour,
                ]
            );
        } catch ( \Error $error ) {
            yaymail_get_logger( $error );
        } catch ( \Exception $exception ) {
            yaymail_get_logger( $exception );
        }
    }

    public function dismiss_multi_select_notice() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'yaymail_frontend_nonce' ) ) {
            return wp_send_json_error( [ 'mess' => __( 'Verify nonce failed', 'yaymail' ) ] );
        }

        try {
            update_option( 'yaymail_show_multi_select_notice', 'no' );

            wp_send_json_success(
                [
                    'show_multi_select_notice' => 'no',
                ]
            );
        } catch ( \Error $error ) {
            yaymail_get_logger( $error );
        } catch ( \Exception $exception ) {
            yaymail_get_logger( $exception );
        }
    }
}
