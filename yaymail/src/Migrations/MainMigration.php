<?php
namespace YayMail\Migrations;

use YayMail\Migrations\MigrationHelper;
use YayMail\Utils\SingletonTrait;
use YayMail\Utils\Logger;

/**
 * Database migration Main class
 */
class MainMigration {
    use SingletonTrait;

    private $logger;

    private $old_version;
    private $new_version;

    private function __construct() {
        if ( ! defined( 'YAYMAIL_VERSION' ) ) {
            return;
        }
        $this->logger      = new Logger();
        $this->new_version = MigrationHelper::format_version_number( YAYMAIL_VERSION );

        $old_version = get_option( 'yaymail_version' );
        // YayMail's version from db
        $this->old_version = MigrationHelper::format_version_number( $old_version ?? '3.9.9' );
    }

    public function migrate() {
        if ( empty( $this->old_version ) ) {
            $this->logger->log( 'YayMail is freshly installed, no migrations needed!' );
            return false;
        }
        $this->logger->log( '***** Start migration transaction *****' );
        global $wpdb;
        $wpdb->query( 'START TRANSACTION' );

        try {
            $core_migrations = [
                '4.0.0' => '\YayMail\Migrations\Versions\Ver_4_0_0',
                // '4.1.0' => '\YayMail\Migrations\Versions\Ver_4_1_0',
            ];
            $this->logger->log( 'Start core migrations' );

            $filtered_migrations = MigrationHelper::filter_migrations( $core_migrations, $this->old_version, $this->new_version );

            if ( ! empty( $filtered_migrations ) ) {
                MigrationHelper::perform_migrations( $filtered_migrations );
                update_option( 'yaymail_version', $this->new_version );
            }

            $this->logger->log( 'Finish core migrations' );

            do_action( 'yaymail_run_addon_migrations' );
            $wpdb->query( 'COMMIT' );

            return true;
        } catch ( \Exception $e ) {
            $wpdb->query( 'ROLLBACK' );
            $this->logger->log( "[Migration failed] {$e->getMessage()}" );
            return false;
        } finally {
            $this->logger->log( '***** End migration transaction *****' );
        }//end try
    }
}
