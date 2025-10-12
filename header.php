<?php
/**
 * Header template.
 *
 * @package PoeTheme
 */
?><!doctype html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    $options = poetheme_get_options();
    $description = ! empty( $options['tagline'] ) ? $options['tagline'] : get_bloginfo( 'description', 'display' );
    if ( $description ) {
        echo '<meta name="description" content="' . esc_attr( $description ) . '">';
    }
    ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-gray-50 text-gray-900 leading-relaxed' ); ?>>
<?php wp_body_open(); ?>
<?php do_action( 'poetheme_before_header' ); ?>
<?php
$header_context = poetheme_get_header_context();
$layout         = isset( $header_context['layout'] ) ? $header_context['layout'] : 'style-1';
$template_slug  = 'template-parts/header/' . $layout;

if ( ! locate_template( array( $template_slug . '.php' ), false, false ) ) {
    $template_slug = 'template-parts/header/style-1';
}

get_template_part( $template_slug, null, $header_context );
?>

<main id="primary-content" class="<?php echo esc_attr( poetheme_get_main_classes() ); ?>" tabindex="-1">
    <?php poetheme_render_subheader(); ?>
