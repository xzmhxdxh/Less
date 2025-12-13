<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
    // Get Options
    $options = get_option( 'less_options' );

    // Favicon
    if ( ! empty( $options['favicon_url'] ) ) {
        echo '<link rel="icon" href="' . esc_url( $options['favicon_url'] ) . '" sizes="32x32" />' . "\n";
        echo '<link rel="icon" href="' . esc_url( $options['favicon_url'] ) . '" sizes="192x192" />' . "\n";
        echo '<link rel="apple-touch-icon" href="' . esc_url( $options['favicon_url'] ) . '" />' . "\n";
    }

    // SEO Meta

    // Homepage
    if ( is_front_page() || is_home() ) {
        if ( ! empty( $options['seo_home_desc'] ) ) {
            echo '<meta name="description" content="' . esc_attr( $options['seo_home_desc'] ) . '">' . "\n";
        }
        if ( ! empty( $options['seo_home_keywords'] ) ) {
            echo '<meta name="keywords" content="' . esc_attr( $options['seo_home_keywords'] ) . '">' . "\n";
        }
    } 
    // Single Post
    elseif ( is_single() ) {
        // Description
        if ( ! empty( $options['seo_auto_description'] ) ) {
            $description = '';
            if ( has_excerpt() ) {
                $description = get_the_excerpt();
            } else {
                $post_content = get_post_field( 'post_content', get_the_ID() );
                // Strip shortcodes and tags, then trim
                $description = wp_trim_words( strip_shortcodes( strip_tags( $post_content ) ), 100, '' );
            }
            if ( $description ) {
                echo '<meta name="description" content="' . esc_attr( strip_tags( $description ) ) . '">' . "\n";
            }
        }
        
        // Keywords
        if ( ! empty( $options['seo_tags_as_keywords'] ) ) {
            $tags = get_the_tags();
            if ( $tags ) {
                $keywords = array();
                foreach ( $tags as $tag ) {
                    $keywords[] = $tag->name;
                }
                echo '<meta name="keywords" content="' . esc_attr( implode( ',', $keywords ) ) . '">' . "\n";
            }
        }
    }
    // Archive / List Pages
    elseif ( is_archive() ) {
        // Description
        $description = get_the_archive_description();
        if ( $description ) {
             echo '<meta name="description" content="' . esc_attr( strip_tags( $description ) ) . '">' . "\n";
        }
        
        // Keywords
        if ( is_category() || is_tag() || is_tax() ) {
            $term_name = single_term_title( '', false );
             echo '<meta name="keywords" content="' . esc_attr( $term_name ) . '">' . "\n";
        }
    }
    ?>
    <?php wp_head(); ?>
    <?php 
    // Custom Header Code
    if ( ! empty( $options['code_head'] ) ) {
        echo $options['code_head'] . "\n";
    }
    
    // Custom CSS
    if ( ! empty( $options['code_css'] ) ) {
        echo '<style>' . $options['code_css'] . '</style>' . "\n";
    }
    ?>
</head>
<body <?php body_class( 'bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans antialiased' ); ?>>
<?php wp_body_open(); ?>

    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50 transition-colors duration-200">
        <div class="container mx-auto px-4 h-16 flex items-center">
            <!-- Logo -->
            <div class="text-2xl font-medium text-primary">
                <?php
                $options = get_option( 'less_options' );
                $logo_light = !empty($options['logo_url_light']) ? $options['logo_url_light'] : (isset($options['logo_url']) ? $options['logo_url'] : '');
                $logo_dark = !empty($options['logo_url_dark']) ? $options['logo_url_dark'] : '';

                if ( $logo_light || $logo_dark ) {
                    echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="flex items-center">';
                    
                    if ( $logo_light ) {
                        $light_class = 'h-8 md:h-10 w-auto';
                        if ( $logo_dark ) {
                            $light_class .= ' dark:hidden';
                        }
                        echo '<img src="' . esc_url( $logo_light ) . '" alt="' . get_bloginfo( 'name' ) . '" class="' . $light_class . '">';
                    }
                    
                    if ( $logo_dark ) {
                        $dark_class = 'h-8 md:h-10 w-auto hidden dark:block';
                        echo '<img src="' . esc_url( $logo_dark ) . '" alt="' . get_bloginfo( 'name' ) . '" class="' . $dark_class . '">';
                    }
                    
                    echo '</a>';
                } else {
                    echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . get_bloginfo( 'name' ) . '</a>';
                }
                ?>
            </div>

            <!-- Mobile Dark Mode Button -->
            <button onclick="toggleDarkMode()" class="lg:hidden block text-gray-600 dark:text-gray-400 hover:text-primary ml-auto mr-4 focus:outline-none">
                <?php echo less_get_icon( 'moon', 'dark:hidden text-xl' ); ?>
                <?php echo less_get_icon( 'sun', 'hidden dark:inline-block text-xl text-yellow-400' ); ?>
            </button>

            <!-- Mobile Search Button -->
            <button onclick="toggleSearch()" class="lg:hidden block text-gray-600 dark:text-gray-400 hover:text-primary mr-4 focus:outline-none">
                <?php echo less_get_icon( 'search', 'text-xl' ); ?>
            </button>

            <!-- Mobile Menu Button -->
            <button onclick="toggleMobileMenu()" class="lg:hidden block text-gray-600 dark:text-gray-400 hover:text-primary focus:outline-none">
                <?php echo less_get_icon( 'bars', 'text-xl' ); ?>
            </button>

            <!-- Nav -->
            <nav class="hidden lg:flex flex-1 items-center justify-between ml-10">
                <?php
                if ( has_nav_menu( 'primary' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'flex space-x-6 text-gray-700 dark:text-gray-300',
                        'container'      => false,
                        'walker'         => new Less_Nav_Walker(),
                    ) );
                } else {
                    ?>
                    <ul class="flex space-x-6 text-gray-700 dark:text-gray-300">
                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block py-4 hover:text-primary transition-colors">首页</a></li>
                        <li class="group relative h-full flex items-center">
                            <a href="#" class="flex items-center gap-1 hover:text-primary transition-colors py-4">
                                演示分类
                                <?php echo less_get_icon( 'chevron-down', 'text-xs opacity-70 group-hover:opacity-100 transition-opacity' ); ?>
                            </a>
                            <ul class="sub-menu absolute top-full left-0 bg-white dark:bg-gray-800 shadow-lg rounded-md py-2 min-w-[160px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 transform translate-y-2 group-hover:translate-y-0 border border-gray-100 dark:border-gray-700">
                                <li><a href="#" class="block px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-primary transition-colors">子菜单项 1</a></li>
                                <li><a href="#" class="block px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-primary transition-colors">子菜单项 2</a></li>
                            </ul>
                        </li>
                    </ul>
                    <?php
                }
                ?>
                
                <div class="flex items-center gap-2 ml-auto">
                    <button onclick="toggleDarkMode()" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none" aria-label="Toggle Dark Mode">
                        <?php echo less_get_icon( 'moon', 'dark:hidden text-lg' ); ?>
                        <?php echo less_get_icon( 'sun', 'hidden dark:inline-block text-lg text-yellow-400' ); ?>
                    </button>
                    <button onclick="toggleSearch()" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none" aria-label="Search">
                        <?php echo less_get_icon( 'search', 'text-lg' ); ?>
                    </button>
                </div>
            </nav>
        </div>

        <!-- Mobile Nav Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
            <div class="flex flex-col p-4 space-y-3 text-gray-700 dark:text-gray-300">
                <?php
                if ( has_nav_menu( 'primary' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'space-y-3',
                    ) );
                } else {
                    ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block hover:text-primary">首页</a>
                    <a href="#" class="block hover:text-primary">演示页面</a>
                    <?php
                }
                ?>
            </div>
        </div>
    </header>
