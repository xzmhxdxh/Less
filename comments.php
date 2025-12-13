<?php
/**
 * The template for displaying comments
 */

if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area mt-10 pt-10 border-t border-gray-100 dark:border-gray-700">

    <?php
    // You can start editing here -- including this comment!
    if ( have_comments() ) :
        ?>
        <h3 class="comments-title text-xl font-medium mb-6 text-gray-900 dark:text-gray-100 border-l-4 border-primary pl-3 leading-none">
            <?php
            $less_comment_count = get_comments_number();
            if ( '1' === $less_comment_count ) {
                printf(
                    /* translators: 1: title. */
                    esc_html__( '“%1$s”有 1 条评论', 'less' ),
                    '<span>' . get_the_title() . '</span>'
                );
            } else {
                printf( 
                    /* translators: 1: comment count number, 2: title. */
                    esc_html( _nx( '“%2$s”有 %1$s 条评论', '“%2$s”有 %1$s 条评论', $less_comment_count, 'comments title', 'less' ) ),
                    number_format_i18n( $less_comment_count ),
                    '<span>' . get_the_title() . '</span>'
                );
            }
            ?>
        </h3><!-- .comments-title -->

        <ul class="comment-list space-y-6">
            <?php
            wp_list_comments( array(
                'style'      => 'ul',
                'short_ping' => true,
                'callback'   => 'less_comment',
            ) );
            ?>
        </ul><!-- .comment-list -->

        <?php
        the_comments_navigation();

        // If comments are closed and there are comments, let's leave a little note, shall we?
        if ( ! comments_open() ) :
            ?>
            <p class="no-comments p-4 bg-gray-50 dark:bg-gray-700/50 rounded text-gray-500 text-center mt-6"><?php esc_html_e( '评论已关闭。', 'less' ); ?></p>
            <?php
        endif;

    endif; // Check for have_comments().

    // Custom Comment Form Args
    $commenter = wp_get_current_commenter();
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );

    $fields = array(
        'author' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">' .
                    '<div><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:text-gray-200" placeholder="' . __( '姓名', 'less' ) . ( $req ? '*' : '' ) . '"' . $aria_req . '></div>',
        
        'email'  => '<div><input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:text-gray-200" placeholder="' . __( '邮箱', 'less' ) . ( $req ? '*' : '' ) . '"' . $aria_req . '></div></div>',
        
        'url'    => '<div class="mb-4"><input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:text-gray-200" placeholder="' . __( '网址', 'less' ) . '"></div>',
    );

    comment_form( array(
        'fields'             => $fields,
        'comment_field'      => '<div class="mb-4"><textarea id="comment" name="comment" cols="45" rows="5" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary dark:text-gray-200" placeholder="' . __( '撰写评论...', 'less' ) . '" aria-required="true"></textarea></div>',
        'submit_button'      => '<button name="%1$s" type="submit" id="%2$s" class="%3$s px-6 py-2 bg-primary hover:bg-blue-600 text-white rounded transition-colors">%4$s</button>',
        'class_submit'       => 'submit',
        'title_reply'        => __( '发表评论', 'less' ),
        'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title text-xl font-medium mb-6 text-gray-900 dark:text-gray-100 border-l-4 border-primary pl-3 leading-none mt-10">',
        'title_reply_after'  => '</h3>',
    ) );
    ?>

</div><!-- #comments -->
