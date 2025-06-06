<?php
namespace YayMail\TemplatePatterns\SectionTemplates;

use YayMail\Abstracts\BaseSectionTemplate;
use YayMail\Utils\SingletonTrait;

/**
 * Banner Elements
 */
class Banner extends BaseSectionTemplate {

    use SingletonTrait;

    public const TYPE = 'banner';

    private function __construct() {
        $this->id       = uniqid();
        $this->name     = __( 'Banner', 'woocommerce' );
        $this->group    = 'section_template';
        $this->position = 12;
        $this->icon     = '
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 1024 1024">
          <path d="M464 144H160c-8.8 0-16 7.2-16 16v304c0 8.8 7.2 16 16 16h304c8.8 0 16-7.2 16-16V160c0-8.8-7.2-16-16-16zm-52 268H212V212h200v200zm452-268H560c-8.8 0-16 7.2-16 16v304c0 8.8 7.2 16 16 16h304c8.8 0 16-7.2 16-16V160c0-8.8-7.2-16-16-16zm-52 268H612V212h200v200zM464 544H160c-8.8 0-16 7.2-16 16v304c0 8.8 7.2 16 16 16h304c8.8 0 16-7.2 16-16V560c0-8.8-7.2-16-16-16zm-52 268H212V612h200v200zm452-268H560c-8.8 0-16 7.2-16 16v304c0 8.8 7.2 16 16 16h304c8.8 0 16-7.2 16-16V560c0-8.8-7.2-16-16-16zm-52 268H612V612h200v200z"/>
        </svg>';
    }
}
