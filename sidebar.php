<aside class="hidden lg:block lg:col-span-1 space-y-6">
    <?php
    $sidebar_id = 'sidebar-home'; // Default to home

    if ( is_single() || is_page() ) {
        $sidebar_id = 'sidebar-single';
    } elseif ( is_archive() || is_search() || is_home() && !is_front_page() ) {
        $sidebar_id = 'sidebar-archive';
    }

    if ( is_active_sidebar( $sidebar_id ) ) :
        dynamic_sidebar( $sidebar_id );
    else :
        // Fallback content if sidebar is empty (optional, keeping original fallback logic just in case, but usually better to leave empty or show default widgets)
        // For now, let's just show the default static content ONLY if it's home and empty, or maybe just generic fallback.
        // Given the request is to "add different widgets", if they don't add any, it might be better to show nothing or a default set.
        // Let's stick to the previous fallback but make it generic or only for home if that was the intent.
        // Actually, the user wants "can add different widgets".
        // Let's keep the fallback for Home if 'sidebar-home' is empty, but maybe simple fallback for others?
        // Let's just use the previous static content as a universal fallback if the specific sidebar is not active.
        ?>
        <!-- Widget: Image Card -->
        <div class="rounded-lg shadow-sm overflow-hidden">
            <a href="#" class="block group">
                <img src="https://placehold.co/600x400" alt="Promotion" class="w-full h-auto object-cover hover:opacity-95 transition-opacity">
            </a>
        </div>

        <!-- Widget: Recent Posts -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm">
            <h3 class="text-lg font-medium mb-4 pb-2 border-b border-gray-100 dark:border-gray-700">最新文章</h3>
            <ul class="space-y-4">
                <?php
                $recent_posts = new WP_Query( array(
                    'posts_per_page' => 5,
                    'ignore_sticky_posts' => 1,
                ) );

                if ( $recent_posts->have_posts() ) :
                    while ( $recent_posts->have_posts() ) : $recent_posts->the_post();
                        ?>
                        <li class="flex gap-3">
                            <div class="w-20 aspect-[3/2] bg-gray-200 dark:bg-gray-700 rounded shrink-0 overflow-hidden">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php the_permalink(); ?>" class="block w-full h-full">
                                        <?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover' ) ); ?>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php the_permalink(); ?>" class="block w-full h-full bg-gray-200 dark:bg-gray-600"></a>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-base leading-snug mb-1 text-gray-800 dark:text-gray-200"><a href="<?php the_permalink(); ?>" class="hover:text-primary"><?php the_title(); ?></a></h4>
                                <span class="text-sm text-gray-400"><?php echo get_the_date(); ?></span>
                            </div>
                        </li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <li class="text-gray-500 dark:text-gray-400">暂无文章</li>
                    <?php
                endif;
                ?>
            </ul>
        </div>

        <!-- Widget: Tags -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm">
            <h3 class="text-lg font-medium mb-4 pb-2 border-b border-gray-100 dark:border-gray-700 text-gray-900 dark:text-gray-100">热门标签</h3>
            <div class="flex flex-wrap gap-2">
                <?php
                $tags = get_tags( array(
                    'orderby' => 'count',
                    'order'   => 'DESC',
                    'number'  => 15,
                ) );
                if ( $tags ) {
                    foreach ( $tags as $tag ) {
                        echo '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-sm text-gray-600 dark:text-gray-400 rounded hover:bg-primary hover:text-white transition-colors">' . esc_html( $tag->name ) . '</a>';
                    }
                } else {
                    echo '<span class="text-sm text-gray-500 dark:text-gray-400">暂无标签</span>';
                }
                ?>
            </div>
        </div>
    <?php endif; ?>
</aside>
