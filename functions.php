<?php
/**
 * Less functions and definitions
 */

if ( ! function_exists( 'less_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	function less_setup() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails on posts and pages.
		add_theme_support( 'post-thumbnails' );

		// Register navigation menus.
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary Menu', 'less' ),
			'footer'  => esc_html__( 'Footer Menu', 'less' ),
		) );

		// Switch default core markup for search form, comment form, and comments to output valid HTML5.
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
	}
endif;
add_action( 'after_setup_theme', 'less_setup' );

/**
 * Initialize Dark Mode in Head (Prevents FOUC)
 */
function less_dark_mode_init() {
    $options = get_option( 'less_options' );
    $color_mode = isset( $options['color_mode'] ) ? $options['color_mode'] : 'light';
    ?>
    <script>
        // Check user preference first
        if ('theme' in localStorage) {
            if (localStorage.theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        } else {
            // No user preference, check Admin Setting
            var colorMode = '<?php echo esc_js( $color_mode ); ?>';
            if (colorMode === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (colorMode === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                // Auto: Use system preference
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        }
    </script>
    <?php
}

/**
 * Theme Options Panel
 */
require get_template_directory() . '/inc/theme-options.php';

/**
 * Custom Widgets
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Post Views Count
 */
function get_post_views( $postID ) {
    $count_key = 'post_views_count';
    $count = get_post_meta( $postID, $count_key, true );
    if ( $count == '' ) {
        delete_post_meta( $postID, $count_key );
        add_post_meta( $postID, $count_key, '0' );
        return "0";
    }
    return $count;
}

function set_post_views( $postID ) {
    $count_key = 'post_views_count';
    $count = get_post_meta( $postID, $count_key, true );
    if ( $count == '' ) {
        $count = 0;
        delete_post_meta( $postID, $count_key );
        add_post_meta( $postID, $count_key, '0' );
    } else {
        $count++;
        update_post_meta( $postID, $count_key, $count );
    }
}

// Track views on single post load
function track_post_views( $post_id ) {
    if ( ! is_single() ) return;
    if ( empty( $post_id ) ) {
        global $post;
        $post_id = $post->ID;
    }
    set_post_views( $post_id );
}
add_action( 'wp_head', 'track_post_views' );
add_action( 'wp_head', 'less_dark_mode_init', 1 );

/**
 * SEO Title Filter
 */
function less_seo_title( $title ) {
    if ( is_front_page() || is_home() ) {
        $options = get_option( 'less_options' );
        if ( ! empty( $options['seo_home_title'] ) ) {
            $title['title'] = $options['seo_home_title'];
        }
    }
    return $title;
}
add_filter( 'document_title_parts', 'less_seo_title' );

/**
 * Enqueue scripts and styles.
 */
function less_scripts() {
	// Tailwind CSS (Local)
	wp_enqueue_style( 'less-tailwind', get_template_directory_uri() . '/assets/css/style.min.css', array(), '1.0.4' );

	// Font Awesome
	// wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.5.1' );

	// Theme Styles
	wp_enqueue_style( 'less-style', get_stylesheet_uri(), array(), '1.0.4' );

	// Theme Main JS
	wp_enqueue_script( 'less-main', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), '1.0.4', true );
    
    // Get Theme Options
    $options = get_option( 'less_options' );
    $hero_interval = isset( $options['hero_interval'] ) ? intval( $options['hero_interval'] ) : 5;
    // Ensure minimum 1 second
    if ( $hero_interval < 1 ) $hero_interval = 5;

    // Localize Script for AJAX
    wp_localize_script( 'less-main', 'less_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'less-like-nonce' ),
        'hero_interval' => $hero_interval * 1000 // Convert to milliseconds
    ) );
}
add_action( 'wp_enqueue_scripts', 'less_scripts' );

/**
 * Post Likes System
 */
function get_post_likes( $postID ) {
    $count_key = 'post_likes_count';
    $count = get_post_meta( $postID, $count_key, true );
    if ( $count == '' ) {
        delete_post_meta( $postID, $count_key );
        add_post_meta( $postID, $count_key, '0' );
        return "0";
    }
    return $count;
}

function less_like_post() {
    // Verify nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'less-like-nonce' ) ) {
        exit( 'No naughty business please' );
    }

    $post_id = intval( $_POST['post_id'] );
    
    // Check if user has already liked (using cookie)
    if ( isset( $_COOKIE['less_liked_' . $post_id] ) ) {
        echo get_post_likes( $post_id );
        exit;
    }

    $count_key = 'post_likes_count';
    $count = get_post_meta( $post_id, $count_key, true );
    
    if ( $count == '' ) {
        $count = 0;
        delete_post_meta( $post_id, $count_key );
        add_post_meta( $post_id, $count_key, '0' );
    } else {
        $count++;
        update_post_meta( $post_id, $count_key, $count );
        
        // Set cookie
        setcookie( 'less_liked_' . $post_id, '1', time() + 3600 * 24 * 365, '/' ); // 1 year
    }
    
    echo $count;
    exit;
}
add_action( 'wp_ajax_less_like', 'less_like_post' );
add_action( 'wp_ajax_nopriv_less_like', 'less_like_post' );

/**
 * Load More Posts AJAX Handler
 */
function less_load_more_posts() {
    // Verify nonce - using the same nonce as like system for simplicity, or create a new one
    // Ideally we should have a general nonce for theme actions
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'less-like-nonce' ) ) {
        exit( 'No naughty business please' );
    }

    $args = json_decode( stripslashes( $_POST['query_vars'] ), true );
    $args['paged'] = $_POST['page'] + 1;
    $args['post_status'] = 'publish';

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/content', get_post_format() );
        }
    }
    wp_reset_postdata();

    exit;
}
add_action( 'wp_ajax_less_load_more', 'less_load_more_posts' );
add_action( 'wp_ajax_nopriv_less_load_more', 'less_load_more_posts' );

/**
 * Get SVG Icon
 */
function less_get_icon( $name, $classes = '', $style = 'solid' ) {
    $icon_path = get_template_directory() . '/assets/icons/' . $style . '/' . $name . '.svg';
    
    if ( file_exists( $icon_path ) ) {
        $svg = file_get_contents( $icon_path );
        
        // Add attributes
        $attributes = ' fill="currentColor" width="1em" height="1em"';
        if ( ! empty( $classes ) ) {
            $attributes .= ' class="' . esc_attr( $classes ) . '"';
        }
        
        $svg = str_replace( '<svg', '<svg' . $attributes, $svg );
        
        return $svg;
    }
    
    return '';
}

/**
 * Custom walker for Tailwind Navigation
 */
class Less_Nav_Walker extends Walker_Nav_Menu {
    // Start Level (Submenu wrapper)
    function start_lvl( &$output, $depth = 0, $args = null ) {
        $classes = array(
            'sub-menu', // Standard WP class
            'absolute', 'top-full', 'left-0', 'bg-white', 'dark:bg-gray-800',
            'shadow-lg', 'rounded-md', 'py-2', 'min-w-[160px]',
            'opacity-0', 'invisible', 'group-hover:opacity-100', 'group-hover:visible',
            'transition-all', 'duration-200', 'z-50',
            'transform', 'translate-y-2', 'group-hover:translate-y-0',
            'border', 'border-gray-100', 'dark:border-gray-700'
        );
        $class_names = implode( ' ', $classes );
        $output .= "<ul class=\"{$class_names}\">";
    }

    // Start Element (Li and A)
    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        
        // Add 'group' and 'relative' to parent items that have children (for hover effect)
        if ( $args->walker->has_children ) {
            $classes[] = 'group relative h-full flex items-center';
        }

        // Filter classes
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $output .= '<li' . $class_names . '>';

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

        // Style the link based on depth
        if ( $depth === 0 ) {
            // Top level links
            $atts['class'] = 'flex items-center gap-1 hover:text-primary transition-colors py-4 px-2';
        } else {
            // Submenu links
            $atts['class'] = 'block px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-primary transition-colors';
        }

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters( 'the_title', $item->title, $item->ID );

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . $title . $args->link_after;
        
        // Add arrow icon for parents at top level
        if ( $args->walker->has_children && $depth === 0 ) {
            $item_output .= ' ' . less_get_icon( 'chevron-down', 'text-xs opacity-70 group-hover:opacity-100 transition-opacity ml-1' );
        }

        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}

/**
 * Custom Comment Callback
 */
function less_comment( $comment, $args, $depth ) {
    ?>
    <li <?php comment_class( 'mb-6' ); ?> id="li-comment-<?php comment_ID(); ?>">
        <div id="comment-<?php comment_ID(); ?>" class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg">
            <div class="flex items-start gap-4">
                <div class="shrink-0">
                    <?php echo get_avatar( $comment, 48, '', '', array( 'class' => 'rounded-full' ) ); ?>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="font-medium text-gray-900 dark:text-gray-100"><?php echo get_comment_author_link(); ?></h5>
                        <span class="text-xs text-gray-500 dark:text-gray-400"><?php printf( _x( '%s前', '%s = human-readable time difference', 'less' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ); ?></span>
                    </div>
                    
                    <?php if ( '0' == $comment->comment_approved ) : ?>
                        <p class="text-sm text-yellow-600 mb-2">您的评论正在等待审核。</p>
                    <?php endif; ?>

                    <div class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-3">
                        <?php comment_text(); ?>
                    </div>

                    <div class="flex items-center gap-4 text-xs">
                        <?php 
                        comment_reply_link( array_merge( $args, array( 
                            'depth'     => $depth, 
                            'max_depth' => $args['max_depth'],
                            'reply_text' => __( '回复', 'less' ),
                            'before'    => '<span class="text-primary hover:text-blue-700 transition-colors">' . less_get_icon( 'reply', 'mr-1' ),
                            'after'     => '</span>'
                        ) ) ); 
                        ?>
                        <?php edit_comment_link( __( '编辑', 'less' ), '<span class="text-gray-400 hover:text-gray-600 transition-colors">' . less_get_icon( 'edit', 'mr-1' ), '</span>' ); ?>
                    </div>
                </div>
            </div>
        </div>
    <!-- </li> is closed by WordPress -->
    <?php
}

/**
 * Add classes to footer menu links
 */
function less_footer_menu_classes( $atts, $item, $args ) {
    if ( $args->theme_location == 'footer' ) {
        $atts['class'] = 'hover:text-primary transition-colors flex items-center gap-2 before:content-[""] before:w-1.5 before:h-1.5 before:bg-blue-500 before:rounded-full before:inline-block';
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'less_footer_menu_classes', 10, 3 );

/**
 * Excerpt Filters
 */
function less_excerpt_length( $length ) {
    return 80;
}
add_filter( 'excerpt_length', 'less_excerpt_length', 999 );

function less_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'less_excerpt_more' );

/**
 * Theme Integrity Check & Footer Credit Protection
 */
function less_verify_site_integrity( $buffer ) {
    // Skip check for admins to avoid locking out (optional, but safer to keep strict as requested)
    // if ( current_user_can( 'manage_options' ) ) return $buffer;

    // 1. Remove HTML comments to prevent hiding via <!-- -->
    $clean_buffer = preg_replace( '/<!--[\s\S]*?-->/', '', $buffer );
    
    // 2. Check for required link and text
    // We check for the specific link and the brand name
    if ( 
        strpos( $clean_buffer, 'href="https://less-theme.com"' ) === false || 
        strpos( $clean_buffer, 'Designed by' ) === false 
    ) {
        // Return error page content instead of the site
        return '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Theme Integrity Error</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f3f4f6; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
                .error-box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); text-align: center; max-width: 500px; width: 90%; }
                h1 { color: #ef4444; margin-top: 0; font-size: 24px; }
                p { color: #374151; line-height: 1.5; }
                .credit { margin-top: 20px; font-size: 14px; color: #6b7280; border-top: 1px solid #e5e7eb; pt-4; }
            </style>
        </head>
        <body>
            <div class="error-box">
                <h1>Theme Copyright Protected</h1>
                <p>The theme copyright credit has been modified, removed, or hidden.</p>
                <p>Please restore the "Designed by LessTheme" link in the footer to restore website access.</p>
                <p class="credit">Powered by Less Theme Protection</p>
            </div>
        </body>
        </html>';
    }
    
    return $buffer;
}

function less_start_integrity_buffer() {
    if ( ! is_admin() && ! is_feed() && ! is_robots() && ! is_trackback() ) {
        // Ensure this is the first buffer if possible, or at least active
        if ( ob_get_level() === 0 ) {
            ob_start( 'less_verify_site_integrity' );
        } else {
            // If other buffers exist, we append ours. 
            // Note: Output buffers stack. The last started is the first to close.
            // We want to be the OUTERMOST or INNERMOST? 
            // If we start here, we are inner. We process content before it goes to the outer buffer.
            // This is fine.
            ob_start( 'less_verify_site_integrity' );
        }
    }
}
// Change priority to be very early
add_action( 'template_redirect', 'less_start_integrity_buffer', 1 );
// Also try hooking into get_header to be safer if template_redirect is skipped (unlikely but possible in some setups)
add_action( 'get_header', 'less_start_integrity_buffer', 1 );

// Static file check (Backup check)
function less_check_footer_file() {
    $footer_path = get_template_directory() . '/footer.php';
    if ( file_exists( $footer_path ) ) {
        $content = file_get_contents( $footer_path );
        // Check if file content is suspiciously empty or missing the link raw string
        if ( strpos( $content, 'href="https://less-theme.com"' ) === false ) {
             wp_die( '<h1>Theme Error</h1><p>Footer file has been tampered with. Please restore the original footer.php.</p>' );
        }
    }
}
add_action( 'template_redirect', 'less_check_footer_file', 5 );

/**
 * Modify Main Query
 */
function less_pre_get_posts( $query ) {
    if ( ! is_admin() && $query->is_main_query() ) {
        if ( is_archive() || is_search() ) {
            $options = get_option( 'less_options' );
            if ( ! empty( $options['posts_per_page_archive'] ) ) {
                $query->set( 'posts_per_page', intval( $options['posts_per_page_archive'] ) );
            }
        }
    }
}
add_action( 'pre_get_posts', 'less_pre_get_posts' );
