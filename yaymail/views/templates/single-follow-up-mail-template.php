<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YayMail\Page\Source\UpdateElement;
$custom_shortcode = new YayMail\MailBuilder\Shortcodes( $template, '', false );
$customCss        = $custom_shortcode->customCss();
$arrData          = array( $custom_shortcode, $args, $template );
do_action_ref_array( 'yaymail_addon_defined_shorcode', array( &$arrData ) );

$updateElement        = new UpdateElement();
$yaymail_elements     = get_post_meta( $postID, '_yaymail_elements', true );
$yaymail_elements     = $updateElement->merge_new_props_to_elements( $yaymail_elements );
$yaymail_settings     = get_option( 'yaymail_settings' );
$emailBackgroundColor = get_post_meta( $postID, '_email_backgroundColor_settings', true ) ? get_post_meta( $postID, '_email_backgroundColor_settings', true ) : '#ECECEC';
$text_link_color      = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#7f54b3';
$general_attrs        = array( 'tableWidth' => str_replace( 'px', '', $yaymail_settings['container_width'] ) );
$yaymail_template     = get_post_meta( $postID, '_yaymail_template', true );
?>
<!DOCTYPE html><html <?php language_attributes(); ?> ><head><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="x-apple-disable-message-reformatting"><style>a{color: <?php echo esc_attr( $text_link_color ); ?> !important}h1{font-family:inherit;text-shadow:unset;text-align:inherit}h2,h3{font-family:inherit;color:inherit;text-align:inherit}.yaymail-inline-block{display:inline-block} <?php echo $customCss; ?>
													<?php
													if ( is_plugin_active( 'yaymail-addon-for-automatewoo/yaymail-automatewoo.php' ) || is_plugin_active( 'email-customizer-automatewoo/yaymail-automatewoo.php' ) ) {
														if ( function_exists( 'aw_get_template' ) ) {
															aw_get_template( 'email/styles.php' );
														}
													}
													?>
				</style></head><body style="background: <?php echo esc_attr( $emailBackgroundColor ); ?>" <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0"><table style="background: <?php echo esc_attr( $emailBackgroundColor ); ?>" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" class="yaymail-customizer-email-template-container <?php echo esc_attr( 'yaymail-template-' . $yaymail_template ); ?>"> 
				<?php
				foreach ( $yaymail_elements as $key => $element ) {
					?>
					<tr><td> 
					<?php
						$reg_pattern = '/\[([a-z0-9A-Z_]+)\]/';
					if ( isset( $element['settingRow']['content'] ) ) {
						$content      = $element['settingRow']['content'];
						$contentTitle = isset( $element['settingRow']['contentTitle'] ) ? $element['settingRow']['contentTitle'] : '';

						// Add $atts for content if has shortcode
						preg_match_all( $reg_pattern, $content, $result );
						if ( ! empty( $result[0] ) ) {
							foreach ( $result[0] as $key => $shortcode ) {
									$textcolor     = isset( $element['settingRow']['textColor'] ) ? ' textcolor=' . $element['settingRow']['textColor'] : '';
									$bordercolor   = isset( $element['settingRow']['borderColor'] ) ? ' bordercolor=' . $element['settingRow']['borderColor'] : '';
									$titlecolor    = isset( $element['settingRow']['titleColor'] ) ? ' titlecolor=' . $element['settingRow']['titleColor'] : '';
									$fontfamily    = isset( $element['settingRow']['family'] ) ? ' fontfamily=' . str_replace( ' ', '', str_replace( array( '\'', '"' ), '', $element['settingRow']['family'] ) ) : '';
									$newshortcode  = substr( $shortcode, 0, -1 );
									$newshortcode .= $textcolor . $bordercolor . $titlecolor . $fontfamily . ']';
									$content       = str_replace( $shortcode, $newshortcode, $content );
							}
							$element['settingRow']['content'] = $content;
						}
						// Add $atts for contentTitle if has shortcode
						if ( $contentTitle ) {
							preg_match_all( $reg_pattern, $contentTitle, $result );
							if ( ! empty( $result[0] ) ) {
								foreach ( $result[0] as $key => $shortcode ) {
									$textcolor     = isset( $element['settingRow']['textColor'] ) ? ' textcolor=' . $element['settingRow']['textColor'] : '';
									$bordercolor   = isset( $element['settingRow']['borderColor'] ) ? ' bordercolor=' . $element['settingRow']['borderColor'] : '';
									$titlecolor    = isset( $element['settingRow']['titleColor'] ) ? ' titlecolor=' . $element['settingRow']['titleColor'] : '';
									$fontfamily    = isset( $element['settingRow']['family'] ) ? ' fontfamily=' . str_replace( ' ', '', str_replace( array( '\'', '"' ), '', $element['settingRow']['family'] ) ) : '';
									$newshortcode  = substr( $shortcode, 0, -1 );
									$newshortcode .= $textcolor . $bordercolor . $titlecolor . $fontfamily . ']';
									$contentTitle  = str_replace( $shortcode, $newshortcode, $contentTitle );
								}
								$element['settingRow']['contentTitle'] = $contentTitle;
							}
						}

						// Add $atts for content of shipment tracking if has shortcode
						if ( '[yaymail_order_meta:_wc_shipment_tracking_items]' === $content ) {
							$shortcode                        = $content;
							$textcolor                        = isset( $element['settingRow']['textColor'] ) ? ' textcolor=' . $element['settingRow']['textColor'] : '';
							$bordercolor                      = isset( $element['settingRow']['borderColor'] ) ? ' bordercolor=' . $element['settingRow']['borderColor'] : '';
							$titlecolor                       = isset( $element['settingRow']['titleColor'] ) ? ' titlecolor=' . $element['settingRow']['titleColor'] : '';
							$fontfamily                       = isset( $element['settingRow']['family'] ) ? ' fontfamily=' . str_replace( ' ', '', str_replace( array( '\'', '"' ), '', $element['settingRow']['family'] ) ) : '';
							$newshortcode                     = substr( $shortcode, 0, -1 );
							$newshortcode                    .= $textcolor . $bordercolor . $titlecolor . $fontfamily . ']';
							$content                          = str_replace( $shortcode, $newshortcode, $content );
							$element['settingRow']['content'] = $content;
						}
					}
					if ( has_filter( 'yaymail_addon_for_conditional_logic' ) && isset( $element['settingRow']['arrConditionLogic'] ) ) {
						if ( ! empty( $element['settingRow']['arrConditionLogic'] ) ) {
							$conditional_Logic = apply_filters( 'yaymail_addon_for_conditional_logic', false, $args, $element['settingRow'] );
							if ( $conditional_Logic ) {
									do_action( 'Yaymail' . $element['type'], $args, $element['settingRow'], $general_attrs, $element['id'], $postID, $isInColumns = false );
							}
						} else {
							if ( 'OneColumn' === $element['type'] || 'TwoColumns' === $element['type'] || 'ThreeColumns' === $element['type'] || 'FourColumns' === $element['type'] ) {
								for ( $column = 1; $column <= 4; $column++ ) {
									if ( isset( $element['settingRow'][ 'column' . $column ] ) ) {
										foreach ( $element['settingRow'][ 'column' . $column ] as $column_key => $column_element ) {
											if ( isset( $column_element['settingRow']['arrConditionLogic'] ) && ! empty( $column_element['settingRow']['arrConditionLogic'] ) ) {
												$conditional_Logic = apply_filters( 'yaymail_addon_for_conditional_logic', false, $args, $column_element['settingRow'] );
												if ( ! $conditional_Logic ) {
													unset( $element['settingRow'][ 'column' . $column ][ $column_key ] );
												}
											}
										}
									}
								}
							}
							do_action( 'Yaymail' . $element['type'], $args, $element['settingRow'], $general_attrs, $element['id'], $postID, $isInColumns = false );
						}
					} else {
							do_action( 'Yaymail' . $element['type'], $args, $element['settingRow'], $general_attrs, $element['id'], $postID, $isInColumns = false );
					}

					?>
						</td></tr> 
						<?php
				}
				?>
				</table></body></html>


