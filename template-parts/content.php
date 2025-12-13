<article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm hover:shadow-md transition-shadow flex flex-col md:flex-row gap-5' ); ?>>
    <div class="w-full md:w-60 aspect-[3/2] bg-gray-200 dark:bg-gray-700 rounded shrink-0 overflow-hidden relative group">
        <?php 
        $categories = get_the_category();
        if ( ! empty( $categories ) ) : ?>
            <a href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>" class="absolute top-2 left-2 bg-primary text-white text-xs px-2 py-1 rounded shadow-sm hover:bg-blue-600 transition-colors z-10"><?php echo esc_html( $categories[0]->name ); ?></a>
        <?php endif; ?>
        
        <a href="<?php the_permalink(); ?>" class="block w-full h-full">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover transition-transform duration-300 group-hover:scale-105' ) ); ?>
            <?php else: ?>
                <div class="w-full h-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-gray-500">
                    <?php echo less_get_icon( 'image', 'text-2xl' ); ?>
                </div>
            <?php endif; ?>
        </a>
    </div>
    <div class="flex flex-col justify-between flex-1">
        <div>
            <h2 class="text-xl font-medium text-gray-800 dark:text-gray-100 hover:text-primary mb-4 leading-snug">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>
            <div class="hidden md:block">
                <div class="text-gray-500 dark:text-gray-400 text-base mb-4 line-clamp-2 leading-relaxed">
                    <?php echo strip_tags( get_the_excerpt() ); ?>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-400">
            <div class="flex items-center gap-4">
                <?php
                $options = get_option( 'less_options' );
                if ( isset( $options['show_author'] ) && $options['show_author'] ) : ?>
                    <span class="flex items-center gap-1"><?php echo less_get_icon( 'user', '', 'regular' ); ?> <?php the_author(); ?></span>
                <?php endif; ?>
                <span class="flex items-center gap-1"><?php echo less_get_icon( 'clock', '', 'regular' ); ?> <?php echo get_the_date(); ?></span>
            </div>
            
            <div class="flex items-center gap-4">
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
                        <span class="like-count text-gray-400"><?php echo get_post_likes( get_the_ID() ); ?></span>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>