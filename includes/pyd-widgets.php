<?php
    /**
     * User: mgyura
     * Date: 8/12/12
     */


    /*-----------------------------------------------------------------------------------*/
    /* The widget Vimeo Videos */
    /*-----------------------------------------------------------------------------------*/


    function pyd_vimeo_register_widgets() {
        register_widget( 'pyd_vimeo_videos_widget' );
    }


    add_action( 'widgets_init', 'pyd_vimeo_register_widgets' );


    class pyd_vimeo_videos_widget
        extends WP_Widget {

        function pyd_vimeo_videos_widget() {
            $pyd_vimeo_videos_widget_ops = array(
                'classname'   => 'pyd_vimeo_videos_widget_class',
                'description' => 'Displays Vimeo videos from your account'
            );
            $this->WP_Widget( 'pyd_vimeo_videos_widget', 'Vimeo Album', $pyd_vimeo_videos_widget_ops );
        }


        function form( $instance ) {
            global $pyd_vimeo_user_data;

            $pyd_vimeo_album_ids = unserialize( file_get_contents( 'http://vimeo.com/api/v2/' . $pyd_vimeo_user_data[ 'username' ] . '/albums.php' ) );

            $defaults  = array(
                'title'         => '',
                'album'         => '',
                'showas'        => '',
                'vidwidth'      => 500,
                'vidheight'     => 287,
            );
            $instance  = wp_parse_args( ( array )$instance, $defaults );
            $title     = $instance[ 'title' ];
            $album     = $instance[ 'album' ];
            $showas    = $instance[ 'showas' ];
            $vidwidth  = $instance[ 'vidwidth' ];
            $vidheight = $instance[ 'vidheight' ];
            ?>

        <p>Title: <input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

        <p>Album to display<br />
            <select name="<?php echo $this->get_field_name( 'album' ); ?>">
                <option value=""> Select the album to insert</option>

                <?php
                foreach ( $pyd_vimeo_album_ids as $pyd_vimeo_album_id ) {
                    ?>

                    <option value='<?php echo $pyd_vimeo_album_id[ 'id' ]; ?>' <?php if ( $pyd_vimeo_album_id[ 'id' ] == $album ) {
                        echo 'selected="selected"';
                    } ?>>
                        <?php echo $pyd_vimeo_album_id[ 'title' ]; ?>
                    </option>

                    <?php
                }
                ?>

            </select>
        </p>

        <p>Display videos as<br />
            <select name="<?php echo $this->get_field_name( 'showas' ); ?>" style="width: 80px;">
                <option value="list" <?php if ( 'list' == $showas ) {
                    echo 'selected="selected"';
                } ?>> Text list </option>
                <option value="icon" <?php if ( 'icon' == $showas ) {
                    echo 'selected="selected"';
                } ?>> Icons </option>
            </select>
        </p>

        <p>
            Video Width: <input class="widefat" name="<?php echo $this->get_field_name( 'vidwidth' ); ?>" type="text" style="width: 40px;" value="<?php echo esc_attr( $vidwidth ); ?>" />px
        </p>
        <p>
            Video Height: <input class="widefat" name="<?php echo $this->get_field_name( 'vidheight' ); ?>" type="text" style="width: 40px;" value="<?php echo esc_attr( $vidheight ); ?>" />px
        </p>

        <?php
        }


        function update( $new_instance, $old_instance ) {
            $instance                = $old_instance;
            $instance[ 'title' ]     = strip_tags( $new_instance[ 'title' ] );
            $instance[ 'album' ]     = strip_tags( $new_instance[ 'album' ] );
            $instance[ 'showas' ]    = strip_tags( $new_instance[ 'showas' ] );
            $instance[ 'vidwidth' ]  = strip_tags( $new_instance[ 'vidwidth' ] );
            $instance[ 'vidheight' ] = strip_tags( $new_instance[ 'vidheight' ] );
            return $instance;
        }


        function widget( $args, $instance ) {
            global $add_my_script;

            $add_my_script = true;

            extract( $args );

            $title     = apply_filters( 'widget_title', $instance[ 'title' ] );
            $album     = $instance[ 'album' ];
            $showas    = $instance[ 'showas' ];
            $vidwidth  = $instance[ 'vidwidth' ];
            $vidheight = $instance[ 'vidheight' ];


            echo $before_widget;

            if ( $title ) {
                echo $before_title . $title . $after_title;
            }


            $pyd_vimeo_albums_get_trans = get_transient( 'pyd_vimeo_albums_' . $album . $showas );

            if ( !$pyd_vimeo_albums_get_trans ) {
                $pyd_vimeo_albums_raw = unserialize( file_get_contents( 'http://vimeo.com/api/v2/album/' . $album . '/videos.php' ) );
                set_transient( 'pyd_vimeo_albums_' . $album . $showas, $pyd_vimeo_albums_raw, 3600 );
            }

            $pyd_vimeo_albums = get_transient( 'pyd_vimeo_albums_' . $album . $showas );


            if ( $showas == 'icon' ) {
                echo '<div class="pyd_vimeo_side_container pyd_notext">';
                foreach ( $pyd_vimeo_albums as $pyd_vimeo_vid ) {
                    ?>

                <div class="pyd_vimeo_videos video_side_thumbnail_small pyd_left">
                    <a href="#TB_inline?height=<?php echo $vidheight; ?>&amp;width=<?php echo $vidwidth; ?>&amp;inlineId=<?php echo 'pyd_vimeo_side_' . $pyd_vimeo_vid[ 'id' ]; ?>" title="<?php echo $pyd_vimeo_vid[ 'title' ]; ?>" class="thickbox"><img src="<?php echo $pyd_vimeo_vid[ 'thumbnail_small' ]; ?>" /></a>

                </div>

                <div id="<?php echo 'pyd_vimeo_side_' . $pyd_vimeo_vid[ 'id' ]; ?>" class="pyd_vimeo_video" style="display:none;">
                    <iframe src="http://player.vimeo.com/video/<?php echo $pyd_vimeo_vid[ 'id' ]; ?>?title=0&amp;byline=0&amp;portrait=0&amp;wmode=transparent" width="<?php echo $vidwidth; ?>" height="<?php echo $vidheight; ?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                </div>

                <?php
                }
                echo '</div>';

            }

            else {
                echo '<ul>';

                foreach ( $pyd_vimeo_albums as $pyd_vimeo_vid ) {
                    ?>
                <li>
                    <a href="#TB_inline?height=<?php echo $vidheight; ?>&amp;width=<?php echo $vidwidth; ?>&amp;inlineId=<?php echo 'pyd_vimeo_side_' . $pyd_vimeo_vid[ 'id' ]; ?>" title="<?php echo $pyd_vimeo_vid[ 'title' ]; ?>" class="thickbox">
                        <?php echo  $pyd_vimeo_vid[ 'title' ]; ?>
                    </a>
                </li>

                <div id="<?php echo 'pyd_vimeo_side_' . $pyd_vimeo_vid[ 'id' ]; ?>" class="pyd_vimeo_video" style="display:none;">
                    <iframe src="http://player.vimeo.com/video/<?php echo $pyd_vimeo_vid[ 'id' ]; ?>?title=0&amp;byline=0&amp;portrait=0&amp;wmode=transparent" width="<?php echo $vidwidth; ?>" height="<?php echo $vidheight; ?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                </div>
                <?php
                }

                echo '</ul>';
            }


            echo $after_widget;

        }
    }