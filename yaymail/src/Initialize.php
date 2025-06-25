<?php
namespace YayMail;

use YayMail\Elements\ElementsLoader;
use YayMail\Emails\EmailsLoader;
use YayMail\Engine\ActDeact;
use YayMail\Engine\Backend\SettingsPage;
use YayMail\Engine\RestAPI;
use YayMail\PostTypes\TemplatePostType;
use YayMail\Shortcodes\ShortcodesLoader;
use YayMail\Utils\SingletonTrait;
use YayMail\TemplatePatterns\PatternsLoader;
use YayMail\TemplatePatterns\SectionTemplatesLoader;
use YayMail\PreviewEmail\PreviewEmailsLoader;
use YayMail\Notices\NoticeMain;

/**
 * YayMail Plugin Initializer
 *
 * @method static Initialize get_instance()
 */
class Initialize {

    use SingletonTrait;

    /**
     * The Constructor that load the engine classes
     */
    protected function __construct() {
        I18n::get_instance();
        add_action( 'woocommerce_init', [ $this, 'woocommerce_init' ] );
        add_action( 'init', [ $this, 'yaymail_init' ] );
    }

    public function woocommerce_init() {
        require_once YAYMAIL_PLUGIN_PATH . 'src/Functions.php';
        do_action( 'yaymail_init_start' );
        EmailsLoader::get_instance();
        ElementsLoader::get_instance();
        ShortcodesLoader::get_instance();
    }

    public static function yaymail_init() {

        $version_current        = YAYMAIL_VERSION;
        $version_old            = get_option( 'yaymail_version' );
        $version_current_backup = get_option( 'yaymail_version_backup' );

        if ( $version_current !== $version_old ) {
            if ( $version_current_backup !== $version_current ) {
                \YayMail\Migrations\MainMigration::get_instance()->migrate();

                update_option( 'yaymail_version', YAYMAIL_VERSION );
                update_option( 'yaymail_version_backup', YAYMAIL_VERSION );
            }
        }

        ActDeact::get_instance();

        WooHandler::get_instance();
        /**
         * Preview Email loader
         */

        PreviewEmailsLoader::get_instance();

        /**
         * Supported templates
         */
        SupportedPlugins::get_instance();

        /**
         * Core core filters
         */

        SectionTemplatesLoader::get_instance();
        PatternsLoader::get_instance();

        /**
         * Initialize rest api
         */
        RestAPI::get_instance();

        /**
         * Initialize pages
         */
        SettingsPage::get_instance();

        TemplatePostType::get_instance();
        Ajax::get_instance();

        /**
         * Notices
         */
        NoticeMain::get_instance();

        do_action( 'yaymail_loaded' );
    }
}
