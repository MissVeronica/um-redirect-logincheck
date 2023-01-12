<?php
/**
 * Plugin Name:     Ultimate Member - Redirect at Login
 * Description:     Extension to Ultimate Member to redirect user at login if account status is not approved.
 * Version:         1.0.0 
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica?tab=repositories
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'UM' ) ) return;

remove_filter( 'authenticate', 'um_wp_form_errors_hook_logincheck', 50, 3 );
add_filter( 'authenticate', 'custom_wp_form_errors_hook_logincheck', 50, 3 );
add_filter( 'um_settings_structure', 'um_settings_structure_redirect_logincheck', 10, 1 );

function custom_wp_form_errors_hook_logincheck( $user, $username, $password ) {

	if ( isset( $user->ID ) ) {

		um_fetch_user( $user->ID );
		$status = um_user( 'account_status' );

		switch( $status ) {
			case 'inactive':
				if( ! empty( UM()->options()->get( 'um_redirect_login_inactive' ) )) {
                    um_reset_user();
                    wp_redirect( esc_url( UM()->options()->get( 'um_redirect_login_inactive' ) ));
                    exit;
                } else {
                    return new WP_Error( $status, __( 'Your account has been disabled.', 'ultimate-member' ) );
                    break;
                }
			case 'awaiting_admin_review':
                if( ! empty( UM()->options()->get( 'um_redirect_login_awaiting_admin_review' ) )) {
                    um_reset_user();
                    wp_redirect( esc_url( UM()->options()->get( 'um_redirect_login_awaiting_admin_review' ) ));
                    exit;
                } else {
                    return new WP_Error( $status, __( 'Your account has not been approved yet.', 'ultimate-member' ) );
                    break;
                }
			case 'awaiting_email_confirmation':
                if( ! empty( UM()->options()->get( 'um_redirect_login_awaiting_email_confirmation' ) )) {
                    um_reset_user();
                    wp_redirect( esc_url( UM()->options()->get( 'um_redirect_login_awaiting_email_confirmation' ) ));
                    exit;
                } else {
                    return new WP_Error( $status, __( 'Your account is awaiting e-mail verification.', 'ultimate-member' ) );
                    break;
                }
			case 'rejected':
                if( ! empty( UM()->options()->get( 'um_redirect_login_rejected' ) )) {
                    um_reset_user();
                    wp_redirect( esc_url( UM()->options()->get( 'um_redirect_login_rejected' ) ));
                    exit;
                } else {
                    return new WP_Error( $status, __( 'Your membership request has been rejected.', 'ultimate-member' ) );
                    break;
                }
		}

	}

	return $user;

}

function um_settings_structure_redirect_logincheck( $settings_structure ) {

    $settings_structure['appearance']['sections']['login_form']['fields'][] = array(
            'id'            => 'um_redirect_login_inactive',
            'type'          => 'text',
            'label'         => __( 'Redirect Login - Inactive', 'ultimate-member' ),
            'size'          => 'medium',
            'tooltip'       => __( 'URL for redirection. Empty field default UM error text exit.', 'ultimate-member' )
            );

    $settings_structure['appearance']['sections']['login_form']['fields'][] = array(
            'id'            => 'um_redirect_login_awaiting_admin_review',
            'type'          => 'text',
            'label'         => __( 'Redirect Login - Awaiting admin review', 'ultimate-member' ),
            'size'          => 'medium',
            'tooltip'       => __( 'URL for redirection. Empty field default UM error text exit.', 'ultimate-member' )
            );

    $settings_structure['appearance']['sections']['login_form']['fields'][] = array(
            'id'            => 'um_redirect_login_awaiting_email_confirmation',
            'type'          => 'text',
            'label'         => __( 'Redirect Login - Awaiting email confirmation', 'ultimate-member' ),
            'size'          => 'medium',
            'tooltip'       => __( 'URL for redirection. Empty field default UM error text exit.', 'ultimate-member' )
            );

    $settings_structure['appearance']['sections']['login_form']['fields'][] = array(
            'id'            => 'um_redirect_login_rejected',
            'type'          => 'text',
            'label'         => __( 'Redirect Login - Rejected', 'ultimate-member' ),
            'size'          => 'medium',
            'tooltip'       => __( 'URL for redirection. Empty field default UM error text exit.', 'ultimate-member' )
            );

    return $settings_structure;
}
