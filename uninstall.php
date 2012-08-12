<?php
    /**
     * User: mgyura
     * Date: 8/11/12
     */

    if ( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
        exit();
    }

    else {
        delete_option( 'pyd_vimeo_videos' );

        unregister_setting( 'pyd-vimeo-videos-group', 'pyd_vimeo_videos' );
    }