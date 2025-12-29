<?php
/**
 * Custom Widgets for Less Theme
 */

// Register Sidebar and Widgets
function less_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( '首页侧边栏', 'less' ),
        'id'            => 'sidebar-home',
        'description'   => esc_html__( '首页显示的侧边栏小工具。', 'less' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm mb-6">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title text-lg font-medium mb-4 pb-2 border-b border-gray-100 dark:border-gray-700 text-gray-900 dark:text-gray-100">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => esc_html__( '列表页侧边栏', 'less' ),
        'id'            => 'sidebar-archive',
        'description'   => esc_html__( '文章分类、搜索等列表页显示的侧边栏小工具。', 'less' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm mb-6">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title text-lg font-medium mb-4 pb-2 border-b border-gray-100 dark:border-gray-700 text-gray-900 dark:text-gray-100">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => esc_html__( '文章页侧边栏', 'less' ),
        'id'            => 'sidebar-single',
        'description'   => esc_html__( '文章详情页显示的侧边栏小工具。', 'less' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm mb-6">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title text-lg font-medium mb-4 pb-2 border-b border-gray-100 dark:border-gray-700 text-gray-900 dark:text-gray-100">',
        'after_title'   => '</h3>',
    ) );

    register_widget( 'Less_Recent_Posts_Widget' );
    register_widget( 'Less_Popular_Posts_Widget' );
    register_widget( 'Less_Popular_Tags_Widget' );
    register_widget( 'Less_Image_Widget' );
}
add_action( 'widgets_init', 'less_widgets_init' );

/**
 * Recent Posts Widget
 */
class Less_Recent_Posts_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'less_recent_posts',
            esc_html__( 'Less: 最新文章', 'less' ),
            array( 'description' => esc_html__( '显示带有缩略图的最新文章。', 'less' ) )
        );
    }

    public function widget( $args, $instance ) {
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $is_sticky = isset( $instance['is_sticky'] ) ? (bool) $instance['is_sticky'] : false;

        if ( $is_sticky ) {
            // Try a more aggressive replacement if the specific one fails, but keep the specific one first
            // If class="widget exists, replace it.
            if ( strpos( $args['before_widget'], 'class="widget' ) !== false ) {
                 $args['before_widget'] = str_replace( 'class="widget', 'class="widget widget-sticky', $args['before_widget'] );
            } else {
                 // Fallback: just insert after class="
                 $args['before_widget'] = str_replace( 'class="', 'class="widget-sticky ', $args['before_widget'] );
            }
        }

        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        
        $r = new WP_Query( array(
            'posts_per_page'      => $number,
            'no_found_rows'       => true,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
        ) );

        if ( $r->have_posts() ) :
            echo '<ul class="space-y-4">';
            while ( $r->have_posts() ) : $r->the_post();
                ?>
                <li class="flex gap-3">
                    <div class="w-24 h-16 bg-gray-200 dark:bg-gray-700 rounded shrink-0 overflow-hidden relative">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="block w-full h-full">
                                <?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover transform hover:scale-110 transition-transform duration-300' ) ); ?>
                            </a>
                        <?php else: ?>
                            <a href="<?php the_permalink(); ?>" class="block w-full h-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                <?php echo less_get_icon( 'image' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-[15px] leading-snug mb-1 text-gray-800 dark:text-gray-200 line-clamp-2">
                            <a href="<?php the_permalink(); ?>" class="hover:text-primary transition-colors"><?php the_title(); ?></a>
                        </h4>
                        <span class="text-xs text-gray-400"><?php echo get_the_date(); ?></span>
                    </div>
                </li>
                <?php
            endwhile;
            echo '</ul>';
            wp_reset_postdata();
        endif;

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $is_sticky = isset( $instance['is_sticky'] ) ? (bool) $instance['is_sticky'] : false;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( '标题:', 'less' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( '显示文章数量:', 'less' ); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $is_sticky ); ?> id="<?php echo $this->get_field_id( 'is_sticky' ); ?>" name="<?php echo $this->get_field_name( 'is_sticky' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'is_sticky' ); ?>"><?php esc_html_e( '随动（固定在侧边栏）', 'less' ); ?></label>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title']  = sanitize_text_field( $new_instance['title'] );
        $instance['number'] = (int) $new_instance['number'];
        $instance['is_sticky'] = isset( $new_instance['is_sticky'] ) ? (bool) $new_instance['is_sticky'] : false;
        return $instance;
    }
}

/**
 * Popular Posts Widget
 */
class Less_Popular_Posts_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'less_popular_posts',
            esc_html__( 'Less: 热门文章', 'less' ),
            array( 'description' => esc_html__( '根据浏览量显示热门文章。', 'less' ) )
        );
    }

    public function widget( $args, $instance ) {
        $options = get_option( 'less_options' );
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $is_sticky = isset( $instance['is_sticky'] ) ? (bool) $instance['is_sticky'] : false;

        if ( $is_sticky ) {
             if ( strpos( $args['before_widget'], 'class="widget' ) !== false ) {
                  $args['before_widget'] = str_replace( 'class="widget', 'class="widget widget-sticky', $args['before_widget'] );
             } else {
                  $args['before_widget'] = str_replace( 'class="', 'class="widget-sticky ', $args['before_widget'] );
             }
        }

        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        
        $r = new WP_Query( array(
            'posts_per_page'      => $number,
            'no_found_rows'       => true,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'meta_key'            => 'post_views_count',
            'orderby'             => 'meta_value_num',
            'order'               => 'DESC',
        ) );

        if ( $r->have_posts() ) :
            echo '<ul class="space-y-4">';
            while ( $r->have_posts() ) : $r->the_post();
                ?>
                <li class="flex gap-3">
                    <div class="w-24 h-16 bg-gray-200 dark:bg-gray-700 rounded shrink-0 overflow-hidden relative">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="block w-full h-full">
                                <?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover transform hover:scale-110 transition-transform duration-300' ) ); ?>
                            </a>
                        <?php else: ?>
                             <a href="<?php the_permalink(); ?>" class="block w-full h-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                <?php echo less_get_icon( 'image' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-[15px] leading-snug mb-1 text-gray-800 dark:text-gray-200 line-clamp-2">
                            <a href="<?php the_permalink(); ?>" class="hover:text-primary transition-colors"><?php the_title(); ?></a>
                        </h4>
                        <div class="flex items-center gap-3 text-xs text-gray-400">
                            <?php 
                            if ( isset( $options['show_views'] ) && $options['show_views'] ) : 
                            ?>
                            <span class="inline-flex items-center"><?php echo less_get_icon( 'eye', 'mr-1', 'regular' ); ?><?php echo get_post_views( get_the_ID() ); ?></span>
                            <?php endif; ?>
                            <span><?php echo get_the_date(); ?></span>
                        </div>
                    </div>
                </li>
                <?php
            endwhile;
            echo '</ul>';
            wp_reset_postdata();
        endif;

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $is_sticky = isset( $instance['is_sticky'] ) ? (bool) $instance['is_sticky'] : false;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( '标题:', 'less' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( '显示文章数量:', 'less' ); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $is_sticky ); ?> id="<?php echo $this->get_field_id( 'is_sticky' ); ?>" name="<?php echo $this->get_field_name( 'is_sticky' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'is_sticky' ); ?>"><?php esc_html_e( '随动（固定在侧边栏）', 'less' ); ?></label>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title']  = sanitize_text_field( $new_instance['title'] );
        $instance['number'] = (int) $new_instance['number'];
        $instance['is_sticky'] = isset( $new_instance['is_sticky'] ) ? (bool) $new_instance['is_sticky'] : false;
        return $instance;
    }
}

/**
 * Popular Tags Widget
 */
class Less_Popular_Tags_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'less_popular_tags',
            esc_html__( 'Less: 热门标签', 'less' ),
            array( 'description' => esc_html__( '显示使用最多的标签。', 'less' ) )
        );
    }

    public function widget( $args, $instance ) {
        $number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : 20;
        $is_sticky = isset( $instance['is_sticky'] ) ? (bool) $instance['is_sticky'] : false;

        if ( $is_sticky ) {
             if ( strpos( $args['before_widget'], 'class="widget' ) !== false ) {
                  $args['before_widget'] = str_replace( 'class="widget', 'class="widget widget-sticky', $args['before_widget'] );
             } else {
                  $args['before_widget'] = str_replace( 'class="', 'class="widget-sticky ', $args['before_widget'] );
             }
        }

        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        
        $tags = get_tags( array(
            'orderby' => 'count',
            'order'   => 'DESC',
            'number'  => $number,
        ) );

        if ( $tags ) {
            echo '<div class="flex flex-wrap gap-2">';
            foreach ( $tags as $tag ) {
                echo '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" class="inline-block px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm rounded-full hover:bg-primary hover:text-white transition-colors duration-200">' . esc_html( $tag->name ) . '</a>';
            }
            echo '</div>';
        }

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 20;
        $is_sticky = isset( $instance['is_sticky'] ) ? (bool) $instance['is_sticky'] : false;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( '标题:', 'less' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( '显示标签数量:', 'less' ); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $is_sticky ); ?> id="<?php echo $this->get_field_id( 'is_sticky' ); ?>" name="<?php echo $this->get_field_name( 'is_sticky' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'is_sticky' ); ?>"><?php esc_html_e( '随动（固定在侧边栏）', 'less' ); ?></label>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title']  = sanitize_text_field( $new_instance['title'] );
        $instance['number'] = (int) $new_instance['number'];
        $instance['is_sticky'] = isset( $new_instance['is_sticky'] ) ? (bool) $new_instance['is_sticky'] : false;
        return $instance;
    }
}

/**
 * Image Widget (Simple Wrapper)
 */
class Less_Image_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'less_image_widget',
            esc_html__( 'Less: 图片广告', 'less' ),
            array( 'description' => esc_html__( '显示带链接的图片。', 'less' ) )
        );
    }

    public function widget( $args, $instance ) {
        // Special wrapper for image widget to remove padding if needed, but for now we keep consistency
        // To match the hardcoded design: <div class="rounded-lg shadow-sm overflow-hidden">
        
        // We override the before_widget to remove padding for this specific widget if we want it to be full-bleed "card" style
        // But the user's hardcoded example had it as a separate card.
        // Let's use the standard widget wrapper but maybe we can add a class or just put the content inside.
        
        // Actually, the hardcoded example was:
        // <div class="rounded-lg shadow-sm overflow-hidden"> <a...> <img...> </a> </div>
        // Our sidebar registers widgets with:
        // before_widget => <div class="widget ... bg-white ... p-5 ...">
        // The p-5 (padding) might be too much for an "Ad" image that wants to go edge-to-edge?
        // Let's check the hardcoded sidebar again.
        // It had: <div class="rounded-lg shadow-sm overflow-hidden">...</div> (No padding, no bg-white on the outer wrapper, just the image)
        
        // So for this widget, we might want to override the default styling or just accept it.
        // Let's output it standardly first. If we want "edge-to-edge", we might need a different sidebar area or a specific class.
        // However, standard widgets usually have titles and padding.
        // If this is for "Ads", usually they don't have titles and padding.
        
        // Let's provide options for Image URL and Link URL.
        
        $image_url = ! empty( $instance['image_url'] ) ? $instance['image_url'] : '';
        $link_url  = ! empty( $instance['link_url'] ) ? $instance['link_url'] : '#';
        $open_new  = isset( $instance['open_new'] ) ? (bool) $instance['open_new'] : true;
        $is_sticky = isset( $instance['is_sticky'] ) ? (bool) $instance['is_sticky'] : false;

        if ( empty( $image_url ) ) return;

        // Custom output to match "Image Card" style - we might want to bypass standard wrapper if possible,
        // but we are inside dynamic_sidebar loop.
        // We can just output the content.
        
        if ( $is_sticky ) {
             if ( strpos( $args['before_widget'], 'class="widget' ) !== false ) {
                  $args['before_widget'] = str_replace( 'class="widget', 'class="widget widget-sticky', $args['before_widget'] );
             } else {
                  $args['before_widget'] = str_replace( 'class="', 'class="widget-sticky ', $args['before_widget'] );
             }
        }
        
        echo $args['before_widget'];
        // Title is not displayed on frontend as requested
        /*
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        */
        
        echo '<div class="-m-5 rounded-lg overflow-hidden">'; // Negative margin to counteract the widget padding
        echo '<a href="' . esc_url( $link_url ) . '" class="block group" ' . ( $open_new ? 'target="_blank"' : '' ) . '>';
        echo '<img src="' . esc_url( $image_url ) . '" alt="Ad" class="w-full h-auto object-cover hover:opacity-95 transition-opacity">';
        echo '</a>';
        echo '</div>';

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $image_url = isset( $instance['image_url'] ) ? esc_attr( $instance['image_url'] ) : '';
        $link_url  = isset( $instance['link_url'] ) ? esc_attr( $instance['link_url'] ) : '';
        $open_new  = isset( $instance['open_new'] ) ? (bool) $instance['open_new'] : true;
        $is_sticky = isset( $instance['is_sticky'] ) ? (bool) $instance['is_sticky'] : false;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( '标题 (可选，前端不显示):', 'less' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <div style="margin-bottom:15px;">
            <label for="<?php echo $this->get_field_id( 'image_url' ); ?>"><?php esc_html_e( '图片地址:', 'less' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'image_url' ); ?>" name="<?php echo $this->get_field_name( 'image_url' ); ?>" type="text" value="<?php echo $image_url; ?>" />
            <input type="button" class="button button-secondary js-upload-image" data-target="#<?php echo $this->get_field_id( 'image_url' ); ?>" value="<?php esc_attr_e( '上传图片', 'less' ); ?>" style="margin-top: 5px;" />
            <?php if ( ! empty( $image_url ) ) : ?>
                <div class="less-image-preview" style="margin-top:10px;"><img src="<?php echo esc_url( $image_url ); ?>" style="max-height: 100px;"></div>
            <?php endif; ?>
        </div>
        <p>
            <label for="<?php echo $this->get_field_id( 'link_url' ); ?>"><?php esc_html_e( '链接地址:', 'less' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'link_url' ); ?>" name="<?php echo $this->get_field_name( 'link_url' ); ?>" type="text" value="<?php echo $link_url; ?>" />
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $open_new ); ?> id="<?php echo $this->get_field_id( 'open_new' ); ?>" name="<?php echo $this->get_field_name( 'open_new' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'open_new' ); ?>"><?php esc_html_e( '在新窗口打开链接', 'less' ); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $is_sticky ); ?> id="<?php echo $this->get_field_id( 'is_sticky' ); ?>" name="<?php echo $this->get_field_name( 'is_sticky' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'is_sticky' ); ?>"><?php esc_html_e( '随动（固定在侧边栏）', 'less' ); ?></label>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title']     = sanitize_text_field( $new_instance['title'] );
        $instance['image_url'] = esc_url_raw( $new_instance['image_url'] );
        $instance['link_url']  = esc_url_raw( $new_instance['link_url'] );
        $instance['open_new']  = isset( $new_instance['open_new'] ) ? (bool) $new_instance['open_new'] : false;
        $instance['is_sticky'] = isset( $new_instance['is_sticky'] ) ? (bool) $new_instance['is_sticky'] : false;
        return $instance;
    }
}

/**
 * Enqueue Media Scripts for Widgets
 */
function less_widgets_scripts( $hook ) {
    if ( 'widgets.php' !== $hook && 'customize.php' !== $hook ) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script( 'less-admin-js', get_template_directory_uri() . '/assets/js/admin.js', array( 'jquery' ), '1.0.4', true );
}
add_action( 'admin_enqueue_scripts', 'less_widgets_scripts' );
