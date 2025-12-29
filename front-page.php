<?php get_header(); ?>

    <div class="container mx-auto px-4 py-6">
        <?php
        $options = get_option( 'less_options' );
        if ( isset( $options['show_hero'] ) && $options['show_hero'] ) :
        ?>
        <!-- Hero Section -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
            <!-- Main Slider -->
            <div class="lg:col-span-2 relative w-full aspect-video rounded-lg overflow-hidden group" id="hero-slider">
                <!-- Slides Wrapper -->
                <div class="relative w-full h-full" id="slider-wrapper">
                    <?php
                    $has_custom_slides = false;
                    $custom_slides = array();

                    // Check for custom slides
                    for ( $i = 1; $i <= 3; $i++ ) {
                        if ( ! empty( $options["hero_slide_{$i}_img"] ) ) {
                            $custom_slides[] = array(
                                'img'   => $options["hero_slide_{$i}_img"],
                                'title' => isset( $options["hero_slide_{$i}_title"] ) ? $options["hero_slide_{$i}_title"] : '',
                                'link'  => isset( $options["hero_slide_{$i}_link"] ) ? $options["hero_slide_{$i}_link"] : '#',
                                'new_tab' => ! isset( $options["hero_slide_{$i}_open_new"] ) || $options["hero_slide_{$i}_open_new"] == 1,
                            );
                        }
                    }

                    if ( ! empty( $custom_slides ) ) {
                        $has_custom_slides = true;
                        foreach ( $custom_slides as $index => $slide ) {
                            $opacity_class = ( $index === 0 ) ? 'opacity-100 z-10' : 'opacity-0 z-0';
                            $target = $slide['new_tab'] ? ' target="_blank"' : '';
                            ?>
                            <a href="<?php echo esc_url( $slide['link'] ); ?>"<?php echo $target; ?> class="absolute inset-0 transition-opacity duration-700 <?php echo $opacity_class; ?> slide-item block">
                                <img src="<?php echo esc_url( $slide['img'] ); ?>" class="absolute inset-0 w-full h-full object-cover" alt="<?php echo esc_attr( $slide['title'] ); ?>">
                                
                                <div class="absolute bottom-0 left-0 w-full p-6 bg-gradient-to-t from-black/70 to-transparent text-white">
                                    <?php if ( ! empty( $slide['title'] ) ) : ?>
                                        <h2 class="text-xl md:text-3xl font-medium mb-2 leading-tight"><?php echo esc_html( $slide['title'] ); ?></h2>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <?php
                        }
                        $slide_index = count( $custom_slides );
                    } else {
                        // Fallback to Posts
                        // Query for sticky posts or latest posts for slider
                        $sticky = get_option( 'sticky_posts' );
                        $slider_args = array(
                            'posts_per_page' => 3,
                            'ignore_sticky_posts' => 1,
                        );
                        
                        if ( ! empty( $sticky ) ) {
                            $slider_args['post__in'] = $sticky;
                        }

                        $slider_query = new WP_Query( $slider_args );
                        $slide_index = 0;

                        if ( $slider_query->have_posts() ) :
                            while ( $slider_query->have_posts() ) : $slider_query->the_post();
                                $opacity_class = ( $slide_index === 0 ) ? 'opacity-100 z-10' : 'opacity-0 z-0';
                                ?>
                                <a href="<?php the_permalink(); ?>" class="absolute inset-0 transition-opacity duration-700 <?php echo $opacity_class; ?> slide-item block">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'full', array( 'class' => 'absolute inset-0 w-full h-full object-cover' ) ); ?>
                                    <?php else: ?>
                                        <div class="absolute inset-0 bg-gradient-to-tr from-blue-600 to-blue-400"></div>
                                    <?php endif; ?>
                                    
                                    <div class="absolute bottom-0 left-0 w-full p-6 bg-gradient-to-t from-black/70 to-transparent text-white">
                                        <?php 
                                        $categories = get_the_category();
                                        if ( ! empty( $categories ) ) : ?>
                                            <span class="bg-primary text-white text-xs px-2 py-1 rounded mb-2 inline-block"><?php echo esc_html( $categories[0]->name ); ?></span>
                                        <?php endif; ?>
                                        <h2 class="text-xl md:text-3xl font-medium mb-2 leading-tight"><?php the_title(); ?></h2>
                                        <div class="hidden md:block text-sm md:text-base opacity-90 line-clamp-2"><?php the_excerpt(); ?></div>
                                    </div>
                                </a>
                                <?php
                                $slide_index++;
                            endwhile;
                            wp_reset_postdata();
                        else :
                            ?>
                            <div class="absolute inset-0 bg-gray-200 flex items-center justify-center">
                                <p class="text-gray-500">请发布一些文章以显示幻灯片。</p>
                            </div>
                            <?php
                        endif;
                    }
                    ?>
                </div>

                <!-- Dots Navigation -->
                <div class="absolute bottom-4 right-4 z-20 flex space-x-2" id="slider-dots">
                    <?php for ( $i = 0; $i < $slide_index; $i++ ) : ?>
                        <button class="w-2 h-2 rounded-full bg-white <?php echo ( $i === 0 ) ? 'opacity-100' : 'opacity-50'; ?> hover:opacity-100 transition-opacity duration-300 focus:outline-none" onclick="goToSlide(<?php echo $i; ?>)"></button>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Secondary Cards -->
            <div class="lg:col-span-1 hidden sm:grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-6">
                 <!-- Card 1 -->
                 <?php
                 $card1_img = isset($options['hero_card_1_img']) ? $options['hero_card_1_img'] : '';
                 $card1_title = isset($options['hero_card_1_title']) ? $options['hero_card_1_title'] : '极简主义设计指南';
                 $card1_link = isset($options['hero_card_1_link']) ? $options['hero_card_1_link'] : '#';
                 $card1_target = ( ! isset($options['hero_card_1_open_new']) || $options['hero_card_1_open_new'] == 1 ) ? ' target="_blank"' : '';
                 ?>
                 <div class="relative w-full aspect-video rounded-lg overflow-hidden group">
                    <a href="<?php echo esc_url($card1_link); ?>"<?php echo $card1_target; ?> class="absolute inset-0 z-20"></a>
                    <?php if ( ! empty( $card1_img ) ) : ?>
                        <img src="<?php echo esc_url($card1_img); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" alt="<?php echo esc_attr($card1_title); ?>">
                    <?php else : ?>
                        <div class="absolute inset-0 bg-gradient-to-tr from-red-400 to-pink-500"></div>
                    <?php endif; ?>
                    <div class="absolute bottom-0 left-0 w-full p-4 bg-gradient-to-t from-black/60 to-transparent text-white">
                        <h3 class="text-lg font-medium"><?php echo esc_html($card1_title); ?></h3>
                    </div>
                </div>

                <!-- Card 2 -->
                <?php
                 $card2_img = isset($options['hero_card_2_img']) ? $options['hero_card_2_img'] : '';
                 $card2_title = isset($options['hero_card_2_title']) ? $options['hero_card_2_title'] : '如何提高工作效率';
                 $card2_link = isset($options['hero_card_2_link']) ? $options['hero_card_2_link'] : '#';
                 $card2_target = ( ! isset($options['hero_card_2_open_new']) || $options['hero_card_2_open_new'] == 1 ) ? ' target="_blank"' : '';
                 ?>
                <div class="relative w-full aspect-video rounded-lg overflow-hidden group">
                    <a href="<?php echo esc_url($card2_link); ?>"<?php echo $card2_target; ?> class="absolute inset-0 z-20"></a>
                    <?php if ( ! empty( $card2_img ) ) : ?>
                        <img src="<?php echo esc_url($card2_img); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" alt="<?php echo esc_attr($card2_title); ?>">
                    <?php else : ?>
                        <div class="absolute inset-0 bg-gradient-to-tr from-teal-400 to-cyan-300"></div>
                    <?php endif; ?>
                    <div class="absolute bottom-0 left-0 w-full p-4 bg-gradient-to-t from-black/60 to-transparent text-white">
                        <h3 class="text-lg font-medium"><?php echo esc_html($card2_title); ?></h3>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Main Content Wrapper -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Content Area -->
            <div class="lg:col-span-2">
                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                    <h3 class="text-lg font-medium border-l-4 border-primary pl-3 leading-none text-gray-800 dark:text-gray-100">最新文章</h3>
                    <!-- <a href="<?php echo get_permalink( get_option( 'page_for_posts' ) ); ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary">全部</a> -->
                </div>

                <div class="space-y-6" id="post-list">
                    <?php
                    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                    $posts_per_page = isset($options['posts_per_page_home']) ? intval($options['posts_per_page_home']) : 10;
                    
                    $args = array(
                        'post_type' => 'post',
                        'paged' => $paged,
                        'posts_per_page' => $posts_per_page,
                    );
                    $main_query = new WP_Query( $args );

                    if ( $main_query->have_posts() ) :
                        while ( $main_query->have_posts() ) : $main_query->the_post();
                            get_template_part( 'template-parts/content' );
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<p>暂无文章。</p>';
                    endif;
                    ?>
                </div>

                <?php
                // Load More Button
                if ( $main_query->max_num_pages > 1 ) {
                    echo '<div class="text-center mt-8">';
                    echo '<button id="load-more-posts" class="px-6 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-gray-600 dark:text-gray-300 hover:text-primary hover:border-primary transition-all shadow-sm" data-page="1" data-max="' . $main_query->max_num_pages . '" data-vars="' . esc_attr( json_encode( $args ) ) . '">加载更多</button>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Sidebar -->
            <?php get_sidebar(); ?>
            
        </div>
    </div>

<?php get_footer(); ?>
