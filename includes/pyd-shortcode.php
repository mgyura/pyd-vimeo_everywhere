<?php
    /**
     * User: mgyura
     * Date: 8/7/12
     */

    /*-----------------------------------------------------------------------------------*/
    /* Create shortcode for Vimeo Albums [pydvimeoalbums] */
    /*-----------------------------------------------------------------------------------*/


    add_shortcode( 'pydvimeoalbums', 'pyd_vimeo_albums_shortcode' );

    function pyd_vimeo_albums_shortcode( $atts ) {
        global $add_my_script;

        $add_my_script = true;

        extract(
            shortcode_atts(
                array(
                     'albumid'   => '',
                ), $atts
            )
        );
        ob_start();

        echo 'works';


        $pyd_vimeo_albums = unserialize( file_get_contents( 'http://vimeo.com/api/v2/album/' . $albumid . '/videos.php' ) );

        echo '<pre>';
        print_r( $pyd_vimeo_albums );
        echo '</pre>';


        foreach ( $pyd_vimeo_albums as $pyd_vimeo_album ) {

            echo '<div class="pyd_vimeo_videos">';
            echo '<img src="' . $pyd_vimeo_album[ 'thumbnail_medium' ] . '" />';
            echo '<p>' . $pyd_vimeo_album[ 'title' ] . '</p>';
            echo '</div>';

            //echo '<iframe src="http://player.vimeo.com/video/' . $pyd_vimeo_album['id'] . '?title=0&amp;byline=0&amp;portrait=0" width="300" height="169" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        }


        $output_string = ob_get_contents();
        ob_end_clean();
        return $output_string;
    }