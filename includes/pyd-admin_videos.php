<?php
    /**
     * User: mgyura
     * Date: 8/11/12
     */

    /*-----------------------------------------------------------------------------------*/
    /* Create settings menu for Vimeo Videos */
    /*-----------------------------------------------------------------------------------*/

    function pyd_vimeo_admin_menu() {
        global $pyd_vimeo_username;

        add_submenu_page( 'upload.php', $pyd_vimeo_username[ 'title' ], $pyd_vimeo_username[ 'title' ], 'edit_posts', 'vimeovideos-display', 'pyd_vimeo_videos_option_settings' );
    }

    add_action( 'admin_menu', 'pyd_vimeo_admin_menu' );