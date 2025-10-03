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
    comment_form(
        array(
            'title_reply_before' => '<h2 id="reply-title" class="text-xl font-semibold mt-8">',
            'title_reply_after'  => '</h2>',
            'class_submit'       => 'bg-indigo-600 text-white px-4 py-2 rounded-md focus:outline-none focus:ring-4 focus:ring-indigo-300',
        )
    );
    ?>
</section>
