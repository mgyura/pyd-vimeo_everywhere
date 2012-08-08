<?php
    /*
     Plugin Name: Vimeo Albums Display
     Plugin URI: http://pokayoke.co
     Description: Creates a shortcode for displaying a group of videos from your Vimeo albums.
     Version: 1.00
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