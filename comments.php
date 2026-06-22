<?php
/**
 * Comments template.
 *
 * @package PoeTheme
 */

if ( post_password_required() ) {
    return;
}
?>
<section id="comments" class="mt-10" aria-labelledby="comments-title">
    <?php if ( have_comments() ) : ?>
        <h2 id="comments-title" class="text-2xl font-semibold mb-6">
            <?php
            printf(
                esc_html( _n( '%1$s Comment', '%1$s Comments', get_comments_number(), 'poetheme' ) ),
                number_format_i18n( get_comments_number() )
            );
            ?>
        </h2>

        <ol class="space-y-6">
            <?php
            wp_list_comments(
                array(
                    'style'      => 'ol',
                    'short_ping' => true,
                    'avatar_size'=> 64,
                    'callback'   => null,
                )
            );
            ?>
        </ol>

        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
            <nav class="comment-navigation mt-6" aria-label="<?php esc_attr_e( 'Comments navigation', 'poetheme' ); ?>">
                <div class="flex justify-between">
                    <div><?php previous_comments_link( esc_html__( 'Older Comments', 'poetheme' ) ); ?></div>
                    <div><?php next_comments_link( esc_html__( 'Newer Comments', 'poetheme' ) ); ?></div>
                </div>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ( ! comments_open() && get_comments_number() ) : ?>
        <p class="text-sm text-gray-500"><?php esc_html_e( 'Comments are closed.', 'poetheme' ); ?></p>
    <?php endif; ?>

    <?php
    $commenter     = wp_get_current_commenter();
    $require_names = (bool) get_option( 'require_name_email' );
    $aria_required = $require_names ? ' aria-required="true" required' : '';
    $input_class   = 'w-full border border-gray-300 rounded-md px-3 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200';

    comment_form(
        array(
            'title_reply_before' => '<h2 id="reply-title" class="text-xl font-semibold mt-8">',
            'title_reply_after'  => '</h2>',
            'class_form'         => 'mt-6 space-y-6',
            'class_submit'       => 'poetheme-cta-button submit px-4 py-2 rounded-md',
            'comment_field'      => sprintf(
                '<p class="comment-form-comment space-y-2"><label for="comment" class="font-medium">%1$s</label><textarea id="comment" name="comment" class="%2$s" rows="6" required></textarea></p>',
                esc_html__( 'Comment', 'poetheme' ),
                esc_attr( $input_class )
            ),
            'fields'             => array(
                'author' => sprintf(
                    '<p class="comment-form-author space-y-2"><label for="author" class="font-medium">%1$s</label><input id="author" name="author" type="text" class="%2$s" value="%3$s"%4$s /></p>',
                    esc_html__( 'Name', 'poetheme' ),
                    esc_attr( $input_class ),
                    esc_attr( $commenter['comment_author'] ),
                    $aria_required
                ),
                'email'  => sprintf(
                    '<p class="comment-form-email space-y-2"><label for="email" class="font-medium">%1$s</label><input id="email" name="email" type="email" class="%2$s" value="%3$s"%4$s /></p>',
                    esc_html__( 'Email', 'poetheme' ),
                    esc_attr( $input_class ),
                    esc_attr( $commenter['comment_author_email'] ),
                    $aria_required
                ),
                'url'    => sprintf(
                    '<p class="comment-form-url space-y-2"><label for="url" class="font-medium">%1$s</label><input id="url" name="url" type="url" class="%2$s" value="%3$s" /></p>',
                    esc_html__( 'Website', 'poetheme' ),
                    esc_attr( $input_class ),
                    esc_attr( $commenter['comment_author_url'] )
                ),
            ),
        )
    );
    ?>
</section>
