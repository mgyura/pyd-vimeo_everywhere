<?php
    /**
     * User: mgyura
     * Date: 8/7/12
     */

    /*-----------------------------------------------------------------------------------*/
    /* Create shortcode for Vimeo Albums [pydvimeovideos] */
    /*-----------------------------------------------------------------------------------*/


    add_shortcode( 'pydvimeovideos', 'pyd_vimeo_albums_shortcode' );

    function pyd_vimeo_albums_shortcode( $atts ) {
        global $add_my_script;

        $add_my_script = true;

        extract(
            shortcode_atts(
                array(
                     'albumid'         => '',
                     'videoid'         => '',
                     'albumtitle'      => '',
                     'vidtitle'        => '',
                     'iconsize'        => 'video_thumbnail_medium',
                     'vidheight'       => 281,
                     'vidwidth'        => 500,
                     'iconfloat'       => 'pyd_left',
                ), $atts
            )
        );

        //start to build the page
        ob_start();


        /*-----------------------------------------------------------------------------------*/
        /* Code if showing albums on a page or post */
        /*-----------------------------------------------------------------------------------*/

        if ( $albumid ) {

            //delete transient on post save
            add_action( 'save_post', 'pyd_vimeo_delete_album_trans' );

            function fpcnash_delete_short_trans() {
                delete_transient( 'pyd_vimeo_albums_' . $albumid );
            }


            //build some cache out of the data
            $pyd_vimeo_albums_get_trans = get_transient( 'pyd_vimeo_albums_' . $albumid );

            if ( !$pyd_vimeo_albums_get_trans ) {

                //get raw date from Vimeo
                $pyd_vimeo_albums_raw     = unserialize( file_get_contents( 'http://vimeo.com/api/v2/album/' . $albumid . '/videos.php' ) );
                $pyd_vimeo_album_info_raw = unserialize( file_get_contents( 'http://vimeo.com/api/v2/album/' . $albumid . '/info.php' ) );

                foreach ( $pyd_vimeo_albums_raw as $pyd_vimeo_albums_data => $albumvalue ) {
                    $pyd_vimeo_albums_save[ ] = array(
                        'album_title'                    => $pyd_vimeo_album_info_raw[ 'title' ],
                        'video_id'                       => $albumvalue[ 'id' ],
                        'video_title'                    => $albumvalue[ 'title' ],
                        'video_description'              => $albumvalue[ 'description' ],
                        'video_url'                      => $albumvalue[ 'url' ],
                        'video_upload_date'              => $albumvalue[ 'upload_date' ],
                        'video_mobile_url'               => $albumvalue[ 'mobile_url' ],
                        'video_thumbnail_small'          => $albumvalue[ 'thumbnail_small' ],
                        'video_thumbnail_medium'         => $albumvalue[ 'thumbnail_medium' ],
                        'video_thumbnail_large'          => $albumvalue[ 'thumbnail_large' ],
                        'video_user_name'                => $albumvalue[ 'user_name' ],
                        'video_user_url'                 => $albumvalue[ 'user_url' ],
                        'video_user_portrait_small'      => $albumvalue[ 'user_portrait_small' ],
                        'video_user_portrait_medium'     => $albumvalue[ 'user_portrait_medium' ],
                        'video_user_portrait_large'      => $albumvalue[ 'user_portrait_large' ],
                        'video_user_portrait_huge'       => $albumvalue[ 'user_portrait_huge' ],
                        'video_stats_number_of_likes'    => $albumvalue[ 'stats_number_of_likes' ],
                        'video_stats_number_of_plays'    => $albumvalue[ 'stats_number_of_plays' ],
                        'video_stats_number_of_comments' => $albumvalue[ 'stats_number_of_comments' ],
                        'video_duration'                 => $albumvalue[ 'duration' ],
                        'video_width'                    => $albumvalue[ 'width' ],
                        'video_height'                   => $albumvalue[ 'height' ],
                        'video_tags'                     => $albumvalue[ 'tags' ],
                        'video_embed_privacy'            => $albumvalue[ 'embed_privacy' ],
                    );
                }

                set_transient( 'pyd_vimeo_albums_' . $albumid, $pyd_vimeo_albums_save, 3600 );
            }

            $pyd_vimeo_albums = get_transient( 'pyd_vimeo_albums_' . $albumid );


            ?>
        <div class="pyd_vimeo_container <?php if ( $vidtitle ) {
            echo ' pyd_text';
        }
        else {
            echo ' pyd_notext';
        } ?>">

            <?php

            if ( $albumtitle ) {
                echo '<h2>' . $pyd_vimeo_albums[ 0 ][ 'album_title' ] . '</h2>';
            }

            foreach ( $pyd_vimeo_albums as $pyd_vimeo_album ) {
                ?>

            <div class="pyd_vimeo_videos <?php echo $iconsize . ' ' . $iconfloat; ?>">

                <a href="#TB_inline?height=<?php echo $vidheight; ?>&amp;width=<?php echo $vidwidth; ?>&amp;inlineId=<?php echo 'pyd_vimeo_' . $pyd_vimeo_album[ 'video_id' ]; ?>" title="<?php echo $pyd_vimeo_album[ 'video_title' ]; ?>" class="thickbox"><img src="<?php echo $pyd_vimeo_album[ $iconsize ]; ?>" /></a>
                <?php if ( $vidtitle ) { ?>
                <p><a href="#TB_inline?height=<?php echo $vidheight; ?>&amp;width=<?php echo $vidwidth; ?>&amp;inlineId=<?php echo 'pyd_vimeo_' . $pyd_vimeo_album[ 'video_id' ]; ?>" title="<?php echo $pyd_vimeo_album[ 'video_title' ]; ?>" class="thickbox"><?php echo $pyd_vimeo_album[ 'video_title' ]; ?></a></p>
                <?php } ?>
            </div>

            <div id="<?php echo 'pyd_vimeo_' . $pyd_vimeo_album[ 'video_id' ]; ?>" class="pyd_vimeo_video" style="display:none;">
                <iframe src="http://player.vimeo.com/video/<?php echo $pyd_vimeo_album[ 'video_id' ]; ?>?title=0&amp;byline=0&amp;portrait=0&amp;wmode=transparent" width="<?php echo $vidwidth; ?>" height="<?php echo $vidheight; ?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
            </div>

                <?php
            }

            echo'<div class="pydClear"></div></div>';

        }


        /*-----------------------------------------------------------------------------------*/
        /* Code if showing a single video on a page or post */
        /*-----------------------------------------------------------------------------------*/

        elseif ( $videoid ) {

            $pyd_vimeo_video_raw = unserialize( file_get_contents( 'http://vimeo.com/api/v2/video/' . $videoid . '.php' ) );

            //build some cache out of the data
            $pyd_vimeo_videos_get_trans = get_transient( 'pyd_vimeo_video_' . $videoid . '_' . $albumtitle );

            if ( !$pyd_vimeo_videos_get_trans ) {

                foreach ( $pyd_vimeo_video_raw as $pyd_vimeo_videos_data => $videovalue ) {
                    $pyd_vimeo_video_save = array(
                        'video_id'                       => $videovalue[ 'id' ],
                        'video_title'                    => $videovalue[ 'title' ],
                        'video_description'              => $videovalue[ 'description' ],
                        'video_url'                      => $videovalue[ 'url' ],
                        'video_upload_date'              => $videovalue[ 'upload_date' ],
                        'video_mobile_url'               => $videovalue[ 'mobile_url' ],
                        'video_thumbnail_small'          => $videovalue[ 'thumbnail_small' ],
                        'video_thumbnail_medium'         => $videovalue[ 'thumbnail_medium' ],
                        'video_thumbnail_large'          => $videovalue[ 'thumbnail_large' ],
                        'video_user_name'                => $videovalue[ 'user_name' ],
                        'video_user_url'                 => $videovalue[ 'user_url' ],
                        'video_user_portrait_small'      => $videovalue[ 'user_portrait_small' ],
                        'video_user_portrait_medium'     => $videovalue[ 'user_portrait_medium' ],
                        'video_user_portrait_large'      => $videovalue[ 'user_portrait_large' ],
                        'video_user_portrait_huge'       => $videovalue[ 'user_portrait_huge' ],
                        'video_stats_number_of_likes'    => $videovalue[ 'stats_number_of_likes' ],
                        'video_stats_number_of_plays'    => $videovalue[ 'stats_number_of_plays' ],
                        'video_stats_number_of_comments' => $videovalue[ 'stats_number_of_comments' ],
                        'video_duration'                 => $videovalue[ 'duration' ],
                        'video_width'                    => $videovalue[ 'width' ],
                        'video_height'                   => $videovalue[ 'height' ],
                        'video_tags'                     => $videovalue[ 'tags' ],
                        'video_embed_privacy'            => $videovalue[ 'embed_privacy' ],
                    );
                }

                set_transient( 'pyd_vimeo_video_' . $videoid . '_' . $albumtitle, $pyd_vimeo_video_save, 3600 );
            }

            $pyd_vimeo_video = get_transient( 'pyd_vimeo_video_' . $videoid . '_' . $albumtitle );

            ?>

        <div class="pyd_vimeo_container <?php if ( $vidtitle ) {
            echo ' pyd_text';
        }
        else {
            echo ' pyd_notext';
        } ?>">
            <div class="pyd_vimeo_video <?php echo $iconsize . ' ' . $iconfloat; ?>">
                <a href="#TB_inline?height=<?php echo $vidheight; ?>&amp;width=<?php echo $vidwidth; ?>&amp;inlineId=<?php echo 'pyd_vimeo_' . $pyd_vimeo_video[ 'video_id' ]; ?>" title="<?php echo $pyd_vimeo_video[ 'video_title' ]; ?>" class="thickbox"><img src="<?php echo $pyd_vimeo_video[ $iconsize ]; ?>" /></a>
                <?php if ( $vidtitle ) { ?>
                <p><a href="#TB_inline?height=<?php echo $vidheight; ?>&amp;width=<?php echo $vidwidth; ?>&amp;inlineId=<?php echo 'pyd_vimeo_' . $pyd_vimeo_video[ 'video_id' ]; ?>" title="<?php echo $pyd_vimeo_video[ 'video_title' ]; ?>" class="thickbox"><?php echo $pyd_vimeo_video[ 'video_title' ]; ?></a></p>
                <?php } ?>
            </div>

            <div id="<?php echo 'pyd_vimeo_' . $pyd_vimeo_video[ 'video_id' ]; ?>" class="pyd_vimeo_video" style="display:none;">
                <iframe src="http://player.vimeo.com/video/<?php echo $pyd_vimeo_video[ 'video_id' ]; ?>?title=0&amp;byline=0&amp;portrait=0&amp;wmode=transparent" width="<?php echo $vidwidth; ?>" height="<?php echo $vidheight; ?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
            </div>
        </div>

            <?php
        }

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
        $pyd_vimeo_video_ids = unserialize( file_get_contents( 'http://vimeo.com/api/v2/' . $pyd_vimeo_username[ 'username' ] . '/videos.php' ) );
        ?>

    <script>
        function pydvimeoinsertshort() {
            var album_id = jQuery("#pyd_vimeo_video_album_id").val();

            var video_id = jQuery("#pyd_vimeo_video_id").val();

            var album_title = jQuery("#pyd_vimeo_video_album_title").val();

            var video_title = jQuery("#pyd_vimeo_video_title").val();

            var icon_size = jQuery("#pyd_vimeo_video_icon").val();

            var icon_float = jQuery("#pyd_vimeo_video_icon_float").val();

            var video_width = jQuery("#pyd_vimeo_video_width").val();

            var video_height = jQuery("#pyd_vimeo_video_height").val();

            parent.send_to_editor("[pydvimeovideos " + album_id + video_id + video_title + album_title + " iconsize=\"" + icon_size + "\" iconfloat=\"" + icon_float + "\" vidwidth=\"" + video_width + "\" vidheight=\"" + video_height + "\" ]");
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

                    <p>Display Videos From an Album<br />
                        <select id="pyd_vimeo_video_album_id">
                            <option value=""> Select the album to insert</option>
                            <?php
                            foreach ( $pyd_vimeo_album_ids as $pyd_vimeo_album_id ) {
                                ?>
                                <option value='albumid="<?php echo $pyd_vimeo_album_id[ 'id' ]; ?>"'><?php echo $pyd_vimeo_album_id[ 'title' ]; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </p>

                    <p><b>OR</b></p>

                    <p>Display a Single Video<br />
                        <select id="pyd_vimeo_video_id">
                            <option value=""> Select the video to insert</option>
                            <?php
                            foreach ( $pyd_vimeo_video_ids as $pyd_vimeo_video_id ) {
                                ?>
                                <option value='videoid="<?php echo $pyd_vimeo_video_id[ 'id' ]; ?>"'><?php echo $pyd_vimeo_video_id[ 'title' ]; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </p>

                    <hr />
                    <p>Display Album or Gallery Title:
                        <select id="pyd_vimeo_video_album_title">
                            <option value=' albumtitle="1"'> Yes</option>
                            <option value=''> No</option>
                        </select>
                    </p>

                    <p>Display Video Title:
                        <select id="pyd_vimeo_video_title">
                            <option value=' vidtitle="1"'> Yes</option>
                            <option value=''> No</option>
                        </select>
                    </p>

                    <p>Display Icon Size:
                        <select id="pyd_vimeo_video_icon">
                            <option value="video_thumbnail_medium"> Select an icon size</option>
                            <option value="video_thumbnail_small"> Small </option>
                            <option value="video_thumbnail_medium"> Medium </option>
                            <option value="video_thumbnail_large"> Large </option>
                        </select>
                    </p>

                    <p>Display Icon Float:
                        <select id="pyd_vimeo_video_icon_float">
                            <option value=""> Select direction to float icon</option>
                            <option value="pyd_left"> Left </option>
                            <option value="pyd_right"> Right </option>
                            <option value="pyd_none"> None </option>
                        </select>
                    </p>

                    <p>Playback Window Size: <br />
                       Width: <input size="6" type="text" id="pyd_vimeo_video_width" value="500" />
                       Height: <input size="6" type="text" id="pyd_vimeo_video_height" value="281" />
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