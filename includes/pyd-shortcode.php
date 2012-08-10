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

        //get raw date from Vimeo
        $pyd_vimeo_albums_raw = unserialize( file_get_contents( 'http://vimeo.com/api/v2/album/' . $albumid . '/videos.php' ) );
        $pyd_vimeo_album_info_raw = unserialize( file_get_contents( 'http://vimeo.com/api/v2/album/' . $albumid . '/info.php' ) );


        //build some cache out of the data
        $pyd_vimeo_albums_get_trans = get_transient('pyd_vimeo_albums_' . $albumid . '_' . $title);

        if(!$pyd_vimeo_albums_get_trans) {

            foreach($pyd_vimeo_albums_raw as $pyd_vimeo_albums_data => $albumvalue) {
                $pyd_vimeo_albums_save[] = array(
                    'album_title' =>  $pyd_vimeo_album_info_raw['title'],
                    'image_id' => $albumvalue['id'],
                    'image_title' => $albumvalue['title'],
                    'image_description' => $albumvalue['description'],
                    'image_url' => $albumvalue['url'],
                    'image_upload_date' => $albumvalue['upload_date'],
                    'image_mobile_url' => $albumvalue['mobile_url'],
                    'image_thumbnail_small' => $albumvalue['thumbnail_small'],
                    'image_thumbnail_medium' => $albumvalue['thumbnail_medium'],
                    'image_thumbnail_large' => $albumvalue['thumbnail_large'],
                    'image_user_name' => $albumvalue['user_name'],
                    'image_user_url' => $albumvalue['user_url'],
                    'image_user_portrait_small' => $albumvalue['user_portrait_small'],
                    'image_user_portrait_medium' => $albumvalue['user_portrait_medium'],
                    'image_user_portrait_large' => $albumvalue['user_portrait_large'],
                    'image_user_portrait_huge' => $albumvalue['user_portrait_huge'],
                    'image_stats_number_of_likes' => $albumvalue['stats_number_of_likes'],
                    'image_stats_number_of_plays' => $albumvalue['stats_number_of_plays'],
                    'image_stats_number_of_comments' => $albumvalue['stats_number_of_comments'],
                    'image_duration' => $albumvalue['duration'],
                    'image_width' => $albumvalue['width'],
                    'image_height' => $albumvalue['height'],
                    'image_tags' => $albumvalue['tags'],
                    'image_embed_privacy' => $albumvalue['embed_privacy'],
                );
            }

            set_transient('pyd_vimeo_albums_' . $albumid . '_' . $title, $pyd_vimeo_albums_save, 3600 );
        }

        $pyd_vimeo_albums = get_transient('pyd_vimeo_albums_' . $albumid . '_' . $title);


        //start to build the page
        ob_start();

        echo '<div class="pyd_vimeo_container pydClear">';

        if ( $title ) {
            echo '<h2>' . $pyd_vimeo_albums[0]['album_title'] . '</h2>';
        }

        foreach ( $pyd_vimeo_albums as $pyd_vimeo_album ) {
            ?>

        <div class="pyd_vimeo_videos">
            <a href="#TB_inline?height=281&amp;width=500&amp;inlineId=<?php echo 'pyd_vimeo_' . $pyd_vimeo_album[ 'image_id' ]; ?>" title="<?php echo $pyd_vimeo_album[ 'image_title' ]; ?>" class="thickbox"><img src="<?php echo $pyd_vimeo_album[ 'image_thumbnail_medium' ]; ?>" /></a>
            <p><a href="#TB_inline?height=281&amp;width=500&amp;inlineId=<?php echo 'pyd_vimeo_' . $pyd_vimeo_album[ 'image_id' ]; ?>" title="<?php echo $pyd_vimeo_album[ 'image_title' ]; ?>" class="thickbox"><?php echo $pyd_vimeo_album[ 'image_title' ]; ?></a></p>
        </div>

        <div id="<?php echo 'pyd_vimeo_' . $pyd_vimeo_album[ 'image_id' ]; ?>" class="pyd_vimeo_video" style="display:none;">
            <iframe src="http://player.vimeo.com/video/<?php echo $pyd_vimeo_album[ 'image_id' ]; ?>?title=0&amp;byline=0&amp;portrait=0&amp;wmode=transparent" width="500" height="281" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
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

                    <p>Display Title: <input type="checkbox" id="pyd_vimeo_video_album_title" name="title" value="1" /></p>
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