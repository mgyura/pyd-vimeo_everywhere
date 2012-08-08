<?php
    /*
     Plugin Name: Vimeo Videos Display
     Plugin URI: http://pokayoke.co
     Description: Creates a shortcode for displaying videos from your Vimeo account.  Adds a tab to your import media button on pages and posts.
     Version: 1.01
     Author: Michael Gyura
     Author URI: http://gyura.com
    */

    /*  Copyright 2012  Michael Gyura  (email : michael@gyura.com)

        This program is free software; you can redistribute it and/or modify
        it under the terms of the GNU General Public License, version 2, as
        published by the Free Software Foundation.

        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.

        You should have received a copy of the GNU General Public License
        along with this program; if not, write to the Free Software
        Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    */


    /*-----------------------------------------------------------------------------------*/
    /* Bring in required files and scripts */
    /*-----------------------------------------------------------------------------------*/

    require_once( dirname( __FILE__ ) . '/includes/pyd-shortcode.php' );
    require_once( dirname( __FILE__ ) . '/includes/pyd-settings.php' );


    add_action( 'wp_enqueue_scripts', 'pyd_vimeo_albums_register_scripts' );

    function pyd_vimeo_albums_register_scripts() {
        wp_register_style( 'pydviemoalbumsscript', plugins_url( '/includes/style.css', __FILE__ ) );
    }


    add_action( 'wp_footer', 'vimeo_albums_print_scripts' );

    function vimeo_albums_print_scripts() {
        global $add_my_script;

        if ( !$add_my_script ) {
            return;
        }

        wp_enqueue_style( 'thickbox' );
        wp_enqueue_style( 'pydviemoalbumsscript' );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'thickbox' );
    }


    /*-----------------------------------------------------------------------------------*/
    /* Call register settings function */
    /*-----------------------------------------------------------------------------------*/

    function pyd_vimeo_videos_settings() {

        register_setting( 'pyd-vimeo-videos-group', 'pyd_vimeo_videos' );
    }

    add_action( 'admin_init', 'pyd_vimeo_videos_settings' );


    /*-----------------------------------------------------------------------------------*/
    /* Setup functions to use */
    /*-----------------------------------------------------------------------------------*/

    $pyd_vimeo_username = get_option( 'pyd_vimeo_videos' );


    /*-----------------------------------------------------------------------------------*/
    /* Admin Message when plugin needs to be authorized by SmugMug */
    /*-----------------------------------------------------------------------------------*/


    function pyd_vimeo_albums_showMessage() {
        global $pyd_vimeo_username;
        if ( $pyd_vimeo_username['username'] == null ) {
            echo '<div id="message" class="error"><p><strong>The Vimeo Videos plugin needs to be linked with an account.  Please <a href="/wp-admin/options-general.php?page=vimeovideos-settings" title="authorize Vimeo Videos">click here</a> to add your Vimeo user name</strong></p></div>';
        }
    }

    function pyd_vimeo_albums_showAdminMessages() {
        pyd_vimeo_albums_showMessage( "The Vimeo Videos plugin needs to be linked with an account.", true );
    }

    add_action( 'admin_notices', 'pyd_vimeo_albums_showAdminMessages' );
