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
                     'title'     => '',
                ), $atts
            )
        );

        $pyd_vimeo_albums = unserialize( file_get_contents( 'http://vimeo.com/api/v2/album/' . $albumid . '/videos.php' ) );


        ob_start();


        echo '<div class="pyd_vimeo_container pydClear">';

        if ( $title ) {
            echo '<h2>' . $title . '</h2>';
        }

        foreach ( $pyd_vimeo_albums as $pyd_vimeo_album ) {
            ?>

        <div class="pyd_vimeo_videos">
            <a href="#TB_inline?height=281&amp;width=500&amp;inlineId=<?php echo 'pyd_vimeo_' . $pyd_vimeo_album[ 'id' ]; ?>" title="<?php echo $pyd_vimeo_album[ 'title' ]; ?>" class="thickbox"><img src="<?php echo $pyd_vimeo_album[ 'thumbnail_medium' ]; ?>" /></a>
            <p><a href="#TB_inline?height=281&amp;width=500&amp;inlineId=<?php echo 'pyd_vimeo_' . $pyd_vimeo_album[ 'id' ]; ?>" title="<?php echo $pyd_vimeo_album[ 'title' ]; ?>" class="thickbox"><?php echo $pyd_vimeo_album[ 'title' ]; ?></a></p>
        </div>

        <div id="<?php echo 'pyd_vimeo_' . $pyd_vimeo_album[ 'id' ]; ?>" class="pyd_vimeo_video" style="display:none;">
            <iframe src="http://player.vimeo.com/video/<?php echo $pyd_vimeo_album[ 'id' ]; ?>?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1&amp;wmode=transparent" width="500" height="281" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
        </div>

        <?php
        }

        echo'<div class="pydClear"></div></div>';


        $output_string = ob_get_contents();
        ob_end_clean();

        return $output_string;
    }


    /*-----------------------------------------------------------------------------------*/
    /* Create new tab in the media uploader to generate shortcode  fro Vimeo Videos */
    /*-----------------------------------------------------------------------------------*/

    add_filter( 'media_upload_tabs', 'pyd_vimeo_videos_upload_tab' );

    function pyd_vimeo_videos_upload_tab( $tabs ) {
        $newtab = array( 'pyd_vimeo_videos_insert_tab' => __( 'Vimeo Videos', 'insertgmap' ) );
        return array_merge( $tabs, $newtab );
    }


    add_action( 'media_upload_pyd_vimeo_videos_insert_tab', 'pyd_vimeo_videos_media_upload_tab' );
    function pyd_vimeo_videos_media_upload_tab() {
        global $errors;

        return wp_iframe( 'pyd_vimeo_videos_media_upload_form', $errors );
    }

    function pyd_vimeo_videos_media_upload_form() {
        global $add_my_script, $pyd_vimeo_username;

        $add_my_script = true;

        $pyd_vimeo_album_ids = unserialize( file_get_contents( 'http://vimeo.com/api/v2/' . $pyd_vimeo_username[ 'username' ] . '/albums.php' ) );

        echo '<pre>';
        print_r( $pyd_vimeo_album_ids );
        echo '</pre>';

        ?>

    <script>
            function pydvimeoinsertshort() {
                var album_id = jQuery("#pyd_vimeo_video_album_id").val();
                if (album_id == "") {
                    alert("<?php _e( "Please select a gallery to use", "pyd" ) ?>");
                    return;
                }
                var album_title = jQuery("#pyd_vimeo_video_album_title").val();


                parent.send_to_editor("[pydvimeoalbums albumid=\"" + album_id + "\" title=\"" + album_title + "\" ]");
            }
        </script>

    <div id="pyd_vimeo_videos_form">
        <div class="wrap">
            <div>
                <div style="padding:15px 15px 0 15px;">
                    <h3 style="color:#5A5A5A!important; font-family:Georgia,Times New Roman,Times,serif!important; font-size:1.8em!important; font-weight:normal!important;"><?php _e( "Insert Vimeo Videos", "pyd" ); ?></h3>
                    <span>
                        <?php _e( "Select the options below to display your Vimeo Videos on this page.", "pyd" ); ?>
                    </span>
                </div>
                <div style="padding:15px 15px 0 15px;">

                    <p>Select the album to show<br />
                        <select id="pyd_vimeo_video_album_id">
                            <option value=""> Select a gallery to insert</option>
                            <?php
                            foreach ( $pyd_vimeo_album_ids as $pyd_vimeo_album_id ) {
                                echo '<option value="' . $pyd_vimeo_album_id[ 'id' ] . '">' . $pyd_vimeo_album_id[ 'title' ] . '</option>';
                            }
                            ?>
                        </select>
                    </p>

                    <p>
                        Display Title: <input type="checkbox" id="pyd_vimeo_video_album_title" name="title" value="<?php echo $pyd_vimeo_album_id[ 'title' ]; ?>" /><br />
                    </p>
                </div>
                <div style="padding:15px;">
                    <input type="button" class="button-primary" value="Insert Vimeo Videos"
                           onclick="pydvimeoinsertshort();" />&nbsp;&nbsp;&nbsp;
                    <a class="button" style="color:#bbb;" href="#"
                       onclick="tb_remove(); return false;"><?php _e( "Cancel", "pyd" ); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php
    }