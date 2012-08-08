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

        $pyd_vimeo_albums = unserialize( file_get_contents( 'http://vimeo.com/api/v2/album/' . $albumid . '/videos.php' ) );


        ob_start();


        echo '<div class="pyd_vimeo_container pydClear">';
        foreach ( $pyd_vimeo_albums as $pyd_vimeo_album ) {
            ?>

        <div class="pyd_vimeo_videos">
            <a href="#TB_inline?height=281&amp;width=500&amp;inlineId=<?php echo 'pyd_vimeo_' . $pyd_vimeo_album[ 'id' ]; ?>" class="thickbox"><img src="<?php echo $pyd_vimeo_album[ 'thumbnail_medium' ]; ?>" /></a>
            <p><a href="#TB_inline?height=281&amp;width=500&amp;z-index=99999&amp;inlineId=<?php echo 'pyd_vimeo_' . $pyd_vimeo_album[ 'id' ]; ?>" class="thickbox"><?php echo $pyd_vimeo_album[ 'title' ]; ?></a></p>
        </div>

        <div id="<?php echo 'pyd_vimeo_' . $pyd_vimeo_album[ 'id' ]; ?>" class="pyd_vimeo_video" style="display:none;">
            <iframe src="http://player.vimeo.com/video/<?php echo $pyd_vimeo_album['id']; ?>?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1&amp;wmode=transparent" width="500" height="281" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
        </div>

        <?php
        }

        echo'<div class="pydClear"></div></div>';


        $output_string = ob_get_contents();
        ob_end_clean();

        return $output_string;
    }