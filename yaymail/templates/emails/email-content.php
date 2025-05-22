<?php
use YayMail\Elements\ElementsLoader;
use YayMail\Utils\TemplateHelpers;

defined( 'ABSPATH' ) || exit;

/**
 * $args includes
 * $template
 * $render_data
 * $settings
 */

$template = isset( $args['template'] ) ? $args['template'] : null;
// YayMailTemplate instance
$render_data = isset( $args['render_data'] ) ? $args['render_data'] : [];
// Render data
$yaymail_settings    = yaymail_settings();
$container_direction = yaymail_get_email_direction();
$container_width     = isset( $yaymail_settings['container_width'] ) && is_numeric( $yaymail_settings['container_width'] ) ? $yaymail_settings['container_width'] : '605';

$style_container = TemplateHelpers::get_style(
    [
        'background' => $template->get_background_color(),
        'direction'  => $container_direction,
        'margin'     => '0 auto',
    ]
);

$style_container_wrap = TemplateHelpers::get_style(
    [
        'background'     => $template->get_background_color(),
        'direction'      => $container_direction,
        'margin'         => '0 auto',
        'width'          => '100%',
        'border-spacing' => '0',
    ]
);

if ( ! empty( $template ) ) :
    ?>

    <?php do_action( 'yaymail_before_email_content', $template ); ?>
    <table style="<?php echo esc_attr( $style_container_wrap ); ?>" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
        <tr>
            <td style="padding: 0;">
                <table class="yaymail-template-content-container" style="width: <?php echo esc_attr( $container_width ); ?>px; margin: auto;border-spacing: 0;padding: 50px 0 20px;">
                    <tr>
                        <td style="padding: 0;">
                            <table style="<?php echo esc_attr( $style_container ); ?>" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" class="yaymail-customizer-email-template-container <?php echo esc_attr( 'yaymail-template-' . $template->get_name() ); ?>">
                                <?php ElementsLoader::render_elements( $template->get_elements(), $args ); ?>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <?php do_action( 'yaymail_after_email_content', $template ); ?>
<?php endif; ?>
