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

                                <h1 class="text-2xl md:text-3xl lg:text-4xl font-medium mb-6 leading-tight text-gray-900 dark:text-gray-100"><?php the_title(); ?></h1>
                            </div>

                            <!-- Content -->
                            <div class="bg-white dark:bg-gray-800 rounded-b-lg p-6 md:p-8 text-lg leading-relaxed text-gray-700 dark:text-gray-300 entry-content">
                                <div class="space-y-6 prose dark:prose-invert max-w-none">
                                    <?php the_content(); ?>
                                </div>
                            </div>
                        </article>

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
