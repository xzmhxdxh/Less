<?php get_header(); ?>

    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Content Area -->
            <div class="lg:col-span-2">
                <?php
                if ( have_posts() ) :
                    while ( have_posts() ) : the_post();
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                            <!-- Header -->
                            <div class="bg-white dark:bg-gray-800 rounded-t-lg p-6 md:p-8 border-b border-gray-100 dark:border-gray-700">
                                <!-- Breadcrumbs (Simple) -->
                                <div class="text-xs text-gray-400 mb-4 flex items-center gap-2">
                                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-primary">首页</a> &gt; 
                                    <?php 
                                    $categories = get_the_category();
                                    if ( ! empty( $categories ) ) {
                                        echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '" class="hover:text-primary">' . esc_html( $categories[0]->name ) . '</a> &gt;';
                                    }
                                    ?>
                                    <span>正文</span>
                                </div>
                                <h1 class="text-2xl md:text-3xl lg:text-4xl font-medium mb-6 leading-tight text-gray-900 dark:text-gray-100"><?php the_title(); ?></h1>
                                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                    <?php
                                    $options = get_option( 'less_options' );
                                    if ( isset( $options['show_author'] ) && $options['show_author'] ) : ?>
                                        <span class="flex items-center gap-1"><?php echo less_get_icon( 'user', '', 'regular' ); ?> <?php the_author(); ?></span>
                                    <?php endif; ?>
                                    <span class="flex items-center gap-1"><?php echo less_get_icon( 'clock', '', 'regular' ); ?> <?php echo get_the_date(); ?></span>
                                    <?php
                                    if ( isset( $options['show_views'] ) && $options['show_views'] ) : ?>
                                        <span class="flex items-center gap-1"><?php echo less_get_icon( 'eye', '', 'regular' ); ?> <?php echo get_post_views( get_the_ID() ); ?></span>
                                    <?php endif; ?>
                                    <?php if ( isset( $options['show_comments'] ) && $options['show_comments'] ) : ?>
                                        <span class="flex items-center gap-1"><?php echo less_get_icon( 'comment-dots', '', 'regular' ); ?> <?php echo get_comments_number(); ?></span>
                                    <?php endif; ?>
                                    <?php if ( isset( $options['show_likes'] ) && $options['show_likes'] ) : ?>
                                        <span class="flex items-center gap-1 cursor-pointer js-like-post group transition-colors <?php echo isset($_COOKIE['less_liked_' . get_the_ID()]) ? 'liked' : ''; ?>" data-post-id="<?php the_ID(); ?>">
                                            <?php 
                                            $liked = isset($_COOKIE['less_liked_' . get_the_ID()]);
                                            echo less_get_icon( 'heart', 'icon-regular transition-colors ' . ($liked ? 'hidden' : ''), 'regular' );
                                            echo less_get_icon( 'heart', 'icon-solid ' . ($liked ? '' : 'hidden'), 'solid' );
                                            ?>
                                            <span class="like-count text-gray-500 dark:text-gray-400"><?php echo get_post_likes( get_the_ID() ); ?></span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $categories ) ) : ?>
                                        <span class="flex items-center gap-1"><?php echo less_get_icon( 'folder', '', 'regular' ); ?> <?php echo esc_html( $categories[0]->name ); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="bg-white dark:bg-gray-800 rounded-b-lg p-6 md:p-8 leading-relaxed text-gray-700 dark:text-gray-300 entry-content">
                                <div class="space-y-6 prose dark:prose-invert max-w-none">
                                    <?php the_content(); ?>
                                </div>

                                <!-- Tags -->
                                <?php
                                $tags = get_the_tags();
                                if ( $tags ) : ?>
                                    <div class="mt-10 pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-wrap items-center gap-3 entry-tags">
                                         <span class="inline-flex items-center text-gray-400 text-sm"><?php echo less_get_icon( 'tags', 'mr-1' ); ?> 标签:</span>
                                         <?php foreach ( $tags as $tag ) : ?>
                                            <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="inline-block px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm rounded-full hover:bg-primary hover:text-white transition-colors duration-200"><?php echo esc_html( $tag->name ); ?></a>
                                         <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Post Like Button (Bottom) -->
                                <?php if ( isset( $options['show_likes'] ) && $options['show_likes'] ) : ?>
                                    <div class="mt-10 flex justify-center pb-4">
                                        <button class="js-like-post group flex flex-col items-center gap-2 focus:outline-none <?php echo isset($_COOKIE['less_liked_' . get_the_ID()]) ? 'liked' : ''; ?>" data-post-id="<?php the_ID(); ?>">
                                            <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 group-hover:bg-gray-200 dark:group-hover:bg-gray-600 flex items-center justify-center transition-colors duration-200">
                                                <?php 
                                                $liked = isset($_COOKIE['less_liked_' . get_the_ID()]);
                                                // Regular icon
                                                echo less_get_icon( 'heart', 'icon-regular text-3xl text-gray-400 transition-colors ' . ($liked ? 'hidden' : ''), 'regular' );
                                                // Solid icon
                                                echo less_get_icon( 'heart', 'icon-solid text-3xl text-gray-400 ' . ($liked ? '' : 'hidden'), 'solid' );
                                                ?>
                                            </div>
                                            <span class="text-sm text-gray-500 dark:text-gray-400 transition-colors">
                                                <span class="like-text"><?php echo $liked ? '已赞' : '点赞'; ?></span> 
                                                (<span class="like-count"><?php echo get_post_likes( get_the_ID() ); ?></span>)
                                            </span>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>

                        <!-- Related Posts -->
                        <?php
                        $related_args = array(
                            'posts_per_page' => 2,
                            'post__not_in'   => array( get_the_ID() ),
                            'orderby'        => 'rand', // Random for variety
                        );
                        // Try to get related by category
                        if ( ! empty( $categories ) ) {
                            $related_args['cat'] = $categories[0]->term_id;
                        }
                        $related_query = new WP_Query( $related_args );

                        if ( $related_query->have_posts() ) : ?>
                            <div class="mt-8">
                                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                                    <h3 class="text-lg font-medium border-l-4 border-primary pl-3 leading-none text-gray-900 dark:text-gray-100">相关推荐</h3>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
                                        <article class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm flex gap-4">
                                            <a href="<?php the_permalink(); ?>" class="block shrink-0">
                                                <div class="w-24 aspect-[3/2] bg-gray-200 dark:bg-gray-700 rounded overflow-hidden">
                                                    <?php if ( has_post_thumbnail() ) : ?>
                                                        <?php the_post_thumbnail( 'thumbnail', array( 'class' => 'w-full h-full object-cover' ) ); ?>
                                                    <?php else: ?>
                                                        <div class="w-full h-full bg-gray-300 dark:bg-gray-600"></div>
                                                    <?php endif; ?>
                                                </div>
                                            </a>
                                            <div>
                                                 <h4 class="text-base mb-1 leading-snug"><a href="<?php the_permalink(); ?>" class="hover:text-primary text-gray-800 dark:text-gray-200"><?php the_title(); ?></a></h4>
                                                 <div class="text-xs text-gray-400"><?php echo get_the_date(); ?></div>
                                            </div>
                                        </article>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>

                        <!-- Comments Section -->
                        <?php
                        $options = get_option( 'less_options' );
                        $enable_comments = isset($options['enable_comments']) && $options['enable_comments'] == 1;
                        if ( $enable_comments && ( comments_open() || get_comments_number() ) ) :
                            comments_template();
                        endif;
                        ?>

                        <?php
                    endwhile;
                endif;
                ?>
            </div>

            <!-- Sidebar -->
            <?php get_sidebar(); ?>
            
        </div>
    </div>

<?php get_footer(); ?>
