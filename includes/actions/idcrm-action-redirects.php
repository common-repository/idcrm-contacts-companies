<?php

namespace idcrm\includes\actions;

use idcrm\idCRM;

if ( ! class_exists( '\idcrm\includes\actions\idCRMActionRedirects' ) ) {
    class idCRMActionRedirects {
		public static function register() {
            $handler = new self();
			add_filter( 'authenticate', array($handler, 'authenticate_redirects'), 101, 3 );
    }

		/** Redirects after autentification */
		function authenticate_redirects( $user, $username, $password ) {
			if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
				if ( is_wp_error( $user ) ) {
					$error_codes = join( ',', $user->get_error_codes() );
					if ( is_page( '/crm/' ) ) {
						$login_url = home_url( '/crm/' );
					} else {
						$login_url = home_url( '/wp-admin/' );
					}
					$login_url = add_query_arg( 'errno', $error_codes, $login_url );
					wp_safe_redirect( $login_url );
					echo '<div id="message" class="error"><p>' . esc_html( $error_codes ) . '</p></div>';
					exit;
				}
			}
			return $user;
		}
    
	}
}

?>
