<?php

namespace YayMail\Engine;

use YayMail\Utils\SingletonTrait;
use YayMail\Migrations\MainMigration;
/**
 * Activate and deactive method of the plugin and relates.
 */
class ActDeact {
    use SingletonTrait;

    protected function __construct() {}

    public static function activate() {
        MainMigration::get_instance()->migrate();
    }

    public static function deactivate() {
    }
}

// Shortcodes to be replaced
