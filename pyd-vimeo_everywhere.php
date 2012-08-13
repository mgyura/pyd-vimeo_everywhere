<?php
    /*
     Plugin Name: Vimeo Everywhere
     Plugin URI: http://pokayoke.co
     Description: Display your public Vimeo videos, albums or channels on a WordPress website.  Use the shortcode generator to place your videos on pages, post or custom post types.  Use the admin panel to place your videos in the dashboard (for example, make a learning center for your clients).  Use the widget to put a video list in the sidebar.
     Version: 1.06
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
    require_once( dirname( __FILE__ ) . '/includes/pyd-admin_videos.php' );
    require_once( dirname( __FILE__ ) . '/includes/pyd-widgets.php' );


    add_action( 'admin_enqueue_scripts', 'pyd_vimeo_albums_admin_scripts' );

    function pyd_vimeo_albums_admin_scripts() {
        wp_register_style( 'pydvimeoadmintyles', plugins_url( '/includes/adminstyle.css', __FILE__ ) );
        wp_enqueue_style( 'pydvimeoadmintyles' );
        wp_enqueue_style( 'thickbox' );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'thickbox' );
    }


    add_action( 'wp_enqueue_scripts', 'pyd_vimeo_albums_register_scripts' );

    function pyd_vimeo_albums_register_scripts() {
        wp_register_style( 'pydviemoalbumsstyle', plugins_url( '/includes/style.css', __FILE__ ) );
    }


    add_action( 'wp_footer', 'vimeo_albums_print_scripts' );

    function vimeo_albums_print_scripts() {
        global $add_my_script;

        if ( !$add_my_script ) {
            return;
        }

        wp_enqueue_style( 'thickbox' );
        wp_enqueue_style( 'pydviemoalbumsstyle' );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'thickbox' );
    }


    /*-----------------------------------------------------------------------------------*/
    /* Register settings function */
    /*-----------------------------------------------------------------------------------*/

    function pyd_vimeo_videos_settings() {

        register_setting( 'pyd-vimeo-videos-group', 'pyd_vimeo_videos' );
    }

    add_action( 'admin_init', 'pyd_vimeo_videos_settings' );


    /*-----------------------------------------------------------------------------------*/
    /* Setup functions to use */
    /*-----------------------------------------------------------------------------------*/

    $pyd_vimeo_user_data = get_option( 'pyd_vimeo_videos' );


    /*-----------------------------------------------------------------------------------*/
    /* Admin Message when plugin needs to be authorized by SmugMug */
    /*-----------------------------------------------------------------------------------*/

    function pyd_vimeo_albums_showMessage() {
        global $pyd_vimeo_user_data;
        if ( $pyd_vimeo_user_data[ 'username' ] == null ) {
            echo '<div id="message" class="error"><p><strong>The Vimeo Everywhere plugin needs to be linked with an account.  Please <a href="/wp-admin/options-general.php?page=vimeovideos-settings" title="authorize Vimeo Everywhere">click here</a> to add your Vimeo user name</strong></p></div>';
        }
    }

    function pyd_vimeo_albums_showAdminMessages() {
        pyd_vimeo_albums_showMessage( "The Vimeo Everywhere plugin needs to be linked with an account.", true );
    }

    add_action( 'admin_notices', 'pyd_vimeo_albums_showAdminMessages' );


    /*-----------------------------------------------------------------------------------*/
    /* Delete Trasients on post save */
    /*-----------------------------------------------------------------------------------*/

    add_action( 'save_post', 'pyd_vimeo_delete_trans' );

    function pyd_vimeo_delete_trans() {
        global $pyd_vimeo_post_id, $albumid, $videoid, $channelid;

        delete_transient( 'pyd_vimeo_albums_' . $albumid . $pyd_vimeo_post_id );
        delete_transient( 'pyd_vimeo_albums_' . $videoid . $pyd_vimeo_post_id );
        delete_transient( 'pyd_vimeo_channel_' . $channelid . $pyd_vimeo_post_id );
    }

    /*-----------------------------------------------------------------------------------*/
    /* Activation Hook.  Check WP Version and set defaults */
    /*-----------------------------------------------------------------------------------*/

    register_activation_hook( __FILE__, 'pyd_vimeo_activation_shit' );

    function pyd_vimeo_activation_shit() {
        global $wp_version;

        if ( version_compare( $wp_version, "3.3", "<" ) ) {

            deactivate_plugins( basename( __file__ ) );
            wp_die( "This plugin requires WordPress version 3.3 or higher." );
        }

        add_option(
            'pyd_vimeo_videos', array(
                                     'username'         => '',
                                     'title'            => 'Vimeo Videos',
                                     'admin_vid_width'  => 703,
                                     'admin_vid_height' => 395,
                                     'admin_menu' => 'upload.php',
                                )
        );
    }
