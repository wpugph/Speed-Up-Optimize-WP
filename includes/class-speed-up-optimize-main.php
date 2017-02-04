<?php
/**
 * Contains the mian functionalities, customization will mainly happen here.
 *
 * @package Speed Up Optimize \ Main Functionalities
 * @author Carl Alberto
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class used for the main plugin functions.
 */
class Speed_Up_Optimize_Main {

	/**
	 * Diplays the scipts and css in the footer.
	 */
	public function display_scripts() {
		if ( current_user_can( 'manage_options' ) ) {
			global $wp_scripts, $wp_styles;
			echo 'Scripts ( Handle - URL )<br>';
		    foreach ( $wp_scripts->queue as $script ) {
				echo sprintf( '<strong>%1s</strong> - %2s <br>', esc_html( $script ),  esc_html( $wp_scripts->registered[ $script ]->src ) );
			}
			echo '<br><br>Styles ( Handle - URL )<br>';
			foreach ( $wp_styles->queue as $style ) {
				echo sprintf( '<strong>%1s</strong> - %2s <br>', esc_html( $style ), esc_html( $wp_styles->registered[ $style ]->src ) );
			}
		}

	}

}
