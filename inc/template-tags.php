<?php
/**
 * Template tags and helpers.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Retrieve settings for the current page.
 *
 * @param int|null $post_id Optional post ID.
 * @return array
 */
function poetheme_get_page_settings( $post_id = null ) {
    $defaults = poetheme_get_default_page_settings();

    if ( null === $post_id ) {
        $post_id = get_queried_object_id();
    }

    if ( ! $post_id ) {
        return $defaults;
    }

    $stored = get_post_meta( $post_id, '_poetheme_page_settings', true );

    if ( ! is_array( $stored ) ) {
        $stored = array();
    }

    $normalized = array();
    foreach ( $defaults as $key => $default ) {
        $normalized[ $key ] = ! empty( $stored[ $key ] );
    }

    return $normalized;
}

/**
 * Helper to retrieve a specific page setting flag.
 *
 * @param string   $key     Setting key.
 * @param int|null $post_id Optional post ID.
 * @return bool
 */
function poetheme_get_page_setting_flag( $key, $post_id = null ) {
    $settings = poetheme_get_page_settings( $post_id );

    return isset( $settings[ $key ] ) ? (bool) $settings[ $key ] : false;
}

/**
 * Determine if the current page removes top padding.
 *
 * @param int|null $post_id Optional post ID.
 * @return bool
 */
function poetheme_page_has_no_top_padding( $post_id = null ) {
    return poetheme_get_page_setting_flag( 'remove_top_padding', $post_id );
}

/**
 * Determine if the current page should display the title.
 *
 * @param int|null $post_id Optional post ID.
 * @return bool
 */
function poetheme_should_display_page_title( $post_id = null ) {
    return ! poetheme_get_page_setting_flag( 'hide_title', $post_id );
}

/**
 * Determine if the subheader is enabled globally.
 *
 * @return bool
 */
function poetheme_subheader_is_enabled() {
    $options = poetheme_get_subheader_options();
    return ! empty( $options['enable_subheader'] );
}

/**
 * Determine if the current view should display the subheader title.
 *
 * @return bool
 */
function poetheme_subheader_should_display_title() {
    if ( ! poetheme_subheader_is_enabled() ) {
        return false;
    }

    $options = poetheme_get_subheader_options();

    if ( empty( $options['show_title'] ) ) {
        return false;
    }

    if ( is_page() && ! poetheme_should_display_page_title() ) {
        return false;
    }

    return true;
}

/**
 * Determine if the current layout hides breadcrumbs regardless of settings.
 *
 * @param string $layout Layout key.
 * @return bool
 */
function poetheme_subheader_layout_hides_breadcrumbs( $layout ) {
    return in_array( $layout, array( 'title-left-only', 'title-right-only', 'title-center-only' ), true );
}

/**
 * Determine if breadcrumbs should be displayed in the subheader for current view.
 *
 * @return bool
 */
function poetheme_subheader_should_display_breadcrumbs() {
    if ( ! poetheme_subheader_is_enabled() ) {
        return false;
    }

    $options = poetheme_get_subheader_options();

    if ( empty( $options['show_breadcrumbs'] ) ) {
        return false;
    }

    if ( poetheme_subheader_layout_hides_breadcrumbs( $options['layout'] ) ) {
        return false;
    }

    if ( is_page() && poetheme_get_page_setting_flag( 'hide_breadcrumbs' ) ) {
        return false;
    }

    if ( is_front_page() ) {
        return false;
    }

    return true;
}

/**
 * Retrieve the configured breadcrumbs separator.
 *
 * @return string
 */
function poetheme_get_breadcrumbs_separator() {
    $options   = poetheme_get_subheader_options();
    $separator = isset( $options['breadcrumbs_separator'] ) ? trim( (string) $options['breadcrumbs_separator'] ) : '/';

    if ( '' === $separator ) {
        $separator = '/';
    }

    return $separator;
}

/**
 * Retrieve the CSS classes to apply to the subheader wrapper based on layout.
 *
 * @param string $layout Layout key.
 * @return array
 */
function poetheme_get_subheader_layout_classes( $layout ) {
    $classes = array( 'poetheme-subheader' );

    switch ( $layout ) {
        case 'stack-left':
            $classes[] = 'poetheme-subheader--stack';
            $classes[] = 'poetheme-subheader--align-left';
            break;
        case 'stack-right':
            $classes[] = 'poetheme-subheader--stack';
            $classes[] = 'poetheme-subheader--align-right';
            break;
        case 'stack-center':
            $classes[] = 'poetheme-subheader--stack';
            $classes[] = 'poetheme-subheader--align-center';
            break;
        case 'title-left-only':
            $classes[] = 'poetheme-subheader--title-only';
            $classes[] = 'poetheme-subheader--align-left';
            break;
        case 'title-right-only':
            $classes[] = 'poetheme-subheader--title-only';
            $classes[] = 'poetheme-subheader--align-right';
            break;
        case 'title-center-only':
            $classes[] = 'poetheme-subheader--title-only';
            $classes[] = 'poetheme-subheader--align-center';
            break;
        case 'split-title-right':
            $classes[] = 'poetheme-subheader--split';
            $classes[] = 'poetheme-subheader--title-right';
            break;
        case 'split-title-left':
        default:
            $classes[] = 'poetheme-subheader--split';
            $classes[] = 'poetheme-subheader--title-left';
            break;
    }

    return $classes;
}

/**
 * Retrieve the list of classes for the subheader title based on context.
 *
 * @return array
 */
function poetheme_get_subheader_title_classes() {
    $classes = array( 'poetheme-subheader__title' );

    if ( is_singular( 'post' ) ) {
        $classes[] = 'poetheme-post-title';
    } elseif ( is_singular() ) {
        $classes[] = 'poetheme-page-title';
    } elseif ( is_category() || is_tag() || is_tax() || is_post_type_archive() ) {
        $classes[] = 'poetheme-category-title';
    } else {
        $classes[] = 'poetheme-page-title';
    }

    return $classes;
}

/**
 * Retrieve the human readable title for the current view.
 *
 * @return string
 */
function poetheme_get_subheader_title_text() {
    if ( is_singular() ) {
        return get_the_title();
    }

    if ( is_home() ) {
        $posts_page_id = (int) get_option( 'page_for_posts' );
        if ( $posts_page_id ) {
            return get_the_title( $posts_page_id );
        }
        return get_bloginfo( 'name' );
    }

    if ( is_search() ) {
        return sprintf( __( 'Risultati della ricerca per "%s"', 'poetheme' ), get_search_query() );
    }

    if ( is_404() ) {
        return __( 'Pagina non trovata', 'poetheme' );
    }

    if ( is_archive() ) {
        return get_the_archive_title();
    }

    return get_bloginfo( 'name' );
}

/**
 * Render the page subheader containing title and breadcrumbs.
 */
function poetheme_render_subheader() {
    if ( ! poetheme_subheader_is_enabled() ) {
        return;
    }

    $options            = poetheme_get_subheader_options();
    $show_title         = poetheme_subheader_should_display_title();
    $show_breadcrumbs   = poetheme_subheader_should_display_breadcrumbs();
    $breadcrumbs_items  = $show_breadcrumbs ? poetheme_get_breadcrumbs_items() : array();

    if ( $show_title ) {
        $title_text = poetheme_get_subheader_title_text();
        if ( '' === trim( $title_text ) ) {
            $show_title = false;
        }
    }

    if ( ! $show_title && empty( $breadcrumbs_items ) ) {
        return;
    }

    $layout_classes = poetheme_get_subheader_layout_classes( $options['layout'] );
    $wrapper_class  = implode( ' ', array_map( 'sanitize_html_class', $layout_classes ) );
    $title_classes  = implode( ' ', array_map( 'sanitize_html_class', poetheme_get_subheader_title_classes() ) );
    $breadcrumbs_class = 'poetheme-subheader__breadcrumbs';
    $tag            = isset( $options['title_tag'] ) ? strtolower( $options['title_tag'] ) : 'h1';
    $allowed_tags   = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );

    if ( ! in_array( $tag, $allowed_tags, true ) ) {
        $tag = 'h1';
    }

    $container_classes = poetheme_get_layout_container_classes( array( 'poetheme-subheader__inner' ) );
    ?>
    <section class="<?php echo esc_attr( $wrapper_class ); ?>">
        <div class="<?php echo esc_attr( $container_classes ); ?>">
            <?php if ( $show_title ) : ?>
                <<?php echo tag_escape( $tag ); ?> class="<?php echo esc_attr( $title_classes ); ?>" itemprop="headline">
                    <?php echo esc_html( $title_text ); ?>
                </<?php echo tag_escape( $tag ); ?>>
            <?php endif; ?>

            <?php if ( ! empty( $breadcrumbs_items ) ) : ?>
                <nav class="<?php echo esc_attr( $breadcrumbs_class ); ?>" aria-label="<?php esc_attr_e( 'Breadcrumb', 'poetheme' ); ?>">
                    <?php echo poetheme_get_breadcrumbs_markup( $breadcrumbs_items, poetheme_get_breadcrumbs_separator() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </nav>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

/**
 * Retrieve base container classes with optional additions.
 *
 * @param array|string $additional Additional classes.
 * @return string
 */
function poetheme_get_layout_container_classes( $additional = array() ) {
    $classes = array( 'poetheme-container', 'px-4', 'sm:px-6', 'lg:px-8' );

    if ( is_string( $additional ) ) {
        $additional = preg_split( '/\s+/', trim( $additional ) );
    }

    if ( ! is_array( $additional ) ) {
        $additional = array();
    }

    foreach ( $additional as $class ) {
        if ( '' === $class ) {
            continue;
        }
        $classes[] = $class;
    }

    $classes = array_unique( array_filter( $classes ) );

    return implode( ' ', $classes );
}

/**
 * Retrieve main element classes accounting for page settings.
 *
 * @return string
 */
function poetheme_get_main_classes() {
    $additional = array( 'pt-10', 'pb-10' );

    if ( poetheme_page_has_no_top_padding() ) {
        $additional = array_diff( $additional, array( 'pt-10' ) );
    }

    return poetheme_get_layout_container_classes( $additional );
}

/**
 * Get breadcrumbs items.
 *
 * @return array
 */
function poetheme_get_breadcrumbs_items() {
    if ( ! poetheme_subheader_should_display_breadcrumbs() ) {
        return array();
    }

    $items   = array();
    $items[] = array(
        'label' => __( 'Home', 'poetheme' ),
        'url'   => home_url( '/' ),
    );

    if ( is_front_page() ) {
        return array();
    }

    if ( is_home() ) {
        $posts_page_id = (int) get_option( 'page_for_posts' );
        if ( $posts_page_id ) {
            $items[] = array(
                'label' => get_the_title( $posts_page_id ),
                'url'   => get_permalink( $posts_page_id ),
            );
        }

        return $items;
    }

    if ( is_singular() ) {
        $post = get_queried_object();

        if ( $post instanceof WP_Post ) {
            $ancestors = array_reverse( get_post_ancestors( $post ) );

            foreach ( $ancestors as $ancestor_id ) {
                $items[] = array(
                    'label' => get_the_title( $ancestor_id ),
                    'url'   => get_permalink( $ancestor_id ),
                );
            }

            $items[] = array(
                'label' => get_the_title( $post ),
                'url'   => get_permalink( $post ),
            );
        }
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( $term instanceof WP_Term ) {
            if ( $term->parent ) {
                $ancestors = array_reverse( get_ancestors( $term->term_id, $term->taxonomy ) );
                foreach ( $ancestors as $ancestor_id ) {
                    $ancestor = get_term( $ancestor_id, $term->taxonomy );
                    if ( $ancestor && ! is_wp_error( $ancestor ) ) {
                        $ancestor_link = get_term_link( $ancestor );
                        if ( ! is_wp_error( $ancestor_link ) ) {
                            $items[] = array(
                                'label' => $ancestor->name,
                                'url'   => $ancestor_link,
                            );
                        }
                    }
                }
            }

            $term_link = get_term_link( $term );

            if ( ! is_wp_error( $term_link ) ) {
                $items[] = array(
                    'label' => $term->name,
                    'url'   => $term_link,
                );
            }
        }
    } elseif ( is_post_type_archive() ) {
        $post_type = get_post_type();
        if ( $post_type ) {
            $items[] = array(
                'label' => post_type_archive_title( '', false ),
                'url'   => get_post_type_archive_link( $post_type ),
            );
        }
    } elseif ( is_search() ) {
        $items[] = array(
            'label' => sprintf( __( 'Search results for "%s"', 'poetheme' ), get_search_query() ),
            'url'   => '',
        );
    } elseif ( is_404() ) {
        $items[] = array(
            'label' => __( '404 Not Found', 'poetheme' ),
            'url'   => '',
        );
    }

    return $items;
}

/**
 * Output breadcrumbs markup.
 */
function poetheme_the_breadcrumbs() {
    $items = poetheme_get_breadcrumbs_items();

    if ( empty( $items ) ) {
        return;
    }
    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'poetheme' ) . '">';
    echo poetheme_get_breadcrumbs_markup( $items, poetheme_get_breadcrumbs_separator() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo '</nav>';
}

/**
 * Build breadcrumbs markup.
 *
 * @param array  $items      Breadcrumb items.
 * @param string $separator  Separator string.
 * @return string
 */
function poetheme_get_breadcrumbs_markup( $items, $separator ) {
    if ( empty( $items ) ) {
        return '';
    }

    $separator_text = $separator;
    $count          = count( $items );
    $output         = '<ol class="poetheme-breadcrumbs" itemscope itemtype="https://schema.org/BreadcrumbList">';

    foreach ( $items as $index => $item ) {
        $output .= '<li class="poetheme-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';

        $is_last = ( $index === $count - 1 );

        if ( ! empty( $item['url'] ) ) {
            $title = sprintf( __( 'Vai a %s', 'poetheme' ), $item['label'] );
            $attributes = array(
                'href'     => $item['url'],
                'class'    => 'poetheme-breadcrumbs__link',
                'itemprop' => 'item',
                'title'    => $title,
            );

            if ( $is_last ) {
                $attributes['aria-current'] = 'page';
            }

            $attr_string = '';
            foreach ( $attributes as $attr_key => $attr_value ) {
                if ( 'href' === $attr_key ) {
                    $attr_string .= ' href="' . esc_url( $attr_value ) . '"';
                } else {
                    $attr_string .= ' ' . $attr_key . '="' . esc_attr( $attr_value ) . '"';
                }
            }

            $output .= '<a' . $attr_string . '><span itemprop="name">' . esc_html( $item['label'] ) . '</span></a>';
        } else {
            $current_attrs = 'class="poetheme-breadcrumbs__current" itemprop="name"';

            if ( $is_last ) {
                $current_attrs .= ' aria-current="page"';
            }

            $output .= '<span ' . $current_attrs . '>' . esc_html( $item['label'] ) . '</span>';
        }

        $output .= '<meta itemprop="position" content="' . esc_attr( $index + 1 ) . '" />';

        if ( $index < $count - 1 ) {
            $output .= '<span class="breadcrumbs__separator" aria-hidden="true"><span class="breadcrumbs__separator-text">' . esc_html( $separator_text ) . '</span></span>';
        }

        $output .= '</li>';
    }

    $output .= '</ol>';

    return $output;
}

/**
 * Output site logo.
 */
function poetheme_the_logo() {
    $logo_options    = poetheme_get_logo_options();
    $show_site_title = ! empty( $logo_options['show_site_title'] );
    $logo_height     = isset( $logo_options['logo_height'] ) ? absint( $logo_options['logo_height'] ) : 0;
    $title_color     = isset( $logo_options['title_color'] ) ? sanitize_hex_color( $logo_options['title_color'] ) : '';
    $title_size      = isset( $logo_options['title_size'] ) ? (float) $logo_options['title_size'] : 0;

    $site_title   = get_bloginfo( 'name' );
    $site_tagline = get_bloginfo( 'description', 'display' );

    $title_attribute = $site_title;
    if ( $site_tagline ) {
        $title_attribute .= ' – ' . $site_tagline;
    }
    $title_attribute = trim( $title_attribute );

    $anchor_attributes = array(
        'href'  => esc_url( home_url( '/' ) ),
        'class' => 'inline-flex flex-col items-start gap-1 no-underline poetheme-brand-link',
        'rel'   => 'home',
    );

    if ( $title_attribute ) {
        $anchor_attributes['title'] = $title_attribute;
    }

    $attribute_strings = array();
    foreach ( $anchor_attributes as $attribute => $value ) {
        if ( '' === $value ) {
            continue;
        }

        $attribute_strings[] = sprintf( '%s="%s"', $attribute, esc_attr( $value ) );
    }

    $logo_id = 0;

    if ( ! $show_site_title ) {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if ( $custom_logo_id ) {
            $logo_id = $custom_logo_id;
        } elseif ( ! empty( $logo_options['logo_id'] ) ) {
            $logo_id = absint( $logo_options['logo_id'] );
        }
    }

    if ( $logo_id ) {
        $image_attributes = array(
            'class' => 'poetheme-logo-image',
            'alt'   => $title_attribute,
        );

        if ( $title_attribute ) {
            $image_attributes['title'] = $title_attribute;
        }

        if ( $logo_height > 0 ) {
            $image_attributes['style'] = 'height:' . $logo_height . 'px;width:auto;';
        }

        $logo_markup = wp_get_attachment_image( $logo_id, 'full', false, $image_attributes );

        if ( $logo_markup ) {
            echo '<a ' . implode( ' ', $attribute_strings ) . '>';
            echo wp_kses_post( $logo_markup );
            echo '</a>';
            return;
        }
    }

    if ( ! $show_site_title ) {
        $legacy_options = poetheme_get_options();

        if ( ! empty( $legacy_options['custom_logo'] ) ) {
            $style_attr = $logo_height > 0 ? ' style="height:' . esc_attr( $logo_height ) . 'px;width:auto;"' : '';
            echo '<a ' . implode( ' ', $attribute_strings ) . '>';
            echo '<img src="' . esc_url( $legacy_options['custom_logo'] ) . '" alt="' . esc_attr( $title_attribute ) . '" title="' . esc_attr( $title_attribute ) . '" class="poetheme-logo-image"' . $style_attr . ' />';
            echo '</a>';
            return;
        }
    }

    $title_styles = array();
    if ( $title_color ) {
        $title_styles[] = 'color:' . $title_color;
    }
    if ( $title_size > 0 ) {
        $title_styles[] = 'font-size:' . poetheme_format_number_for_css( $title_size ) . 'rem';
    }

    $title_style_attr = $title_styles ? ' style="' . esc_attr( implode( ';', $title_styles ) ) . '"' : '';
    $tagline_style_attr = $title_color ? ' style="' . esc_attr( 'color:' . $title_color . ';opacity:0.75;' ) . '"' : '';

    echo '<a ' . implode( ' ', $attribute_strings ) . '>';
    echo '<span class="poetheme-site-title font-bold leading-tight"' . $title_style_attr . '>' . esc_html( $site_title ) . '</span>';

    if ( $site_tagline ) {
        echo '<span class="poetheme-site-tagline text-sm"' . $tagline_style_attr . '>' . esc_html( $site_tagline ) . '</span>';
    }

    echo '</a>';
}

/**
 * Build the context array used by header templates.
 *
 * @return array
 */
function poetheme_get_header_context() {
    $options = poetheme_get_header_options();

    $top_bar_texts = array();
    if ( isset( $options['top_bar_texts'] ) && is_array( $options['top_bar_texts'] ) ) {
        $top_bar_texts = wp_parse_args(
            $options['top_bar_texts'],
            array(
                'text_1'         => '',
                'email'          => '',
                'phone'          => '',
                'whatsapp'       => '',
                'location_label' => '',
                'location_url'   => '',
            )
        );
    }

    $top_bar_items = array();

    if ( ! empty( $top_bar_texts['text_1'] ) ) {
        $top_bar_items[] = array(
            'type' => 'text',
            'text' => sanitize_text_field( $top_bar_texts['text_1'] ),
        );
    }

    if ( ! empty( $top_bar_texts['email'] ) ) {
        $email = sanitize_email( $top_bar_texts['email'] );
        if ( $email ) {
            $top_bar_items[] = array(
                'type' => 'email',
                'text' => $email,
                'url'  => 'mailto:' . $email,
                'icon' => 'mail',
            );
        }
    }

    if ( ! empty( $top_bar_texts['phone'] ) ) {
        $phone_display = sanitize_text_field( $top_bar_texts['phone'] );
        $phone_href    = preg_replace( '/[^0-9+]+/', '', $phone_display );

        if ( '' !== $phone_display && '' !== $phone_href ) {
            $top_bar_items[] = array(
                'type' => 'phone',
                'text' => $phone_display,
                'url'  => 'tel:' . $phone_href,
                'icon' => 'phone',
            );
        }
    }

    if ( ! empty( $top_bar_texts['whatsapp'] ) ) {
        $whatsapp_display = sanitize_text_field( $top_bar_texts['whatsapp'] );
        $whatsapp_number  = preg_replace( '/\D+/', '', $whatsapp_display );

        if ( '' !== $whatsapp_display && '' !== $whatsapp_number ) {
            $top_bar_items[] = array(
                'type' => 'whatsapp',
                'text' => $whatsapp_display,
                'url'  => 'https://wa.me/' . $whatsapp_number,
                'icon' => 'message-circle',
            );
        }
    }

    if ( ! empty( $top_bar_texts['location_label'] ) && ! empty( $top_bar_texts['location_url'] ) ) {
        $location_label = sanitize_text_field( $top_bar_texts['location_label'] );
        $location_url   = esc_url_raw( $top_bar_texts['location_url'] );

        if ( '' !== $location_label && '' !== $location_url ) {
            $top_bar_items[] = array(
                'type'   => 'location',
                'text'   => $location_label,
                'url'    => $location_url,
                'icon'   => 'map-pin',
                'target' => '_blank',
                'rel'    => 'noopener noreferrer',
            );
        }
    }

    $cta_text = isset( $options['cta_text'] ) ? sanitize_text_field( $options['cta_text'] ) : '';
    $cta_url  = '';
    if ( ! empty( $options['cta_url'] ) ) {
        $cta_url = $options['cta_url'];
    } elseif ( ! empty( $cta_text ) ) {
        $cta_url = home_url( '/' );
    }

    $social_links = array();
    $socials      = poetheme_get_header_social_networks();
    foreach ( $socials as $key => $social ) {
        $social_links[ $key ] = isset( $options['social_links'][ $key ] ) ? $options['social_links'][ $key ] : '';
    }

    return array(
        'layout'             => isset( $options['layout'] ) ? $options['layout'] : 'style-1',
        'show_top_bar'       => ! empty( $options['show_top_bar'] ),
        'show_cta'           => ! empty( $options['show_cta'] ),
        'top_bar_texts'      => $top_bar_texts,
        'top_bar_items'      => $top_bar_items,
        'cta_text'           => $cta_text,
        'cta_url'            => $cta_url,
        'social_links'       => $social_links,
        'social_definitions' => $socials,
    );
}

/**
 * Render the top bar items with consistent markup.
 *
 * @param array $items Items generated by poetheme_get_header_context().
 * @param array $args  Display arguments.
 */
function poetheme_render_top_bar_items( $items, $args = array() ) {
    if ( empty( $items ) || ! is_array( $items ) ) {
        return;
    }

    $defaults = array(
        'container_classes' => 'flex flex-wrap items-center gap-x-6 gap-y-2',
        'text_class'        => '',
        'link_class'        => '',
        'icon_class'        => 'w-4 h-4',
    );

    $args = wp_parse_args( $args, $defaults );

    $container_class = trim( (string) $args['container_classes'] );
    $text_class      = trim( (string) $args['text_class'] );
    $link_class      = trim( (string) $args['link_class'] );
    $icon_class      = trim( (string) $args['icon_class'] );

    echo '<div' . ( $container_class ? ' class="' . esc_attr( $container_class ) . '"' : '' ) . '>';

    foreach ( $items as $item ) {
        if ( empty( $item ) || ! is_array( $item ) || empty( $item['text'] ) ) {
            continue;
        }

        $type = isset( $item['type'] ) ? $item['type'] : 'text';

        if ( 'text' === $type ) {
            $text_attr = $text_class ? ' class="' . esc_attr( $text_class ) . '"' : '';
            echo '<span' . $text_attr . '>' . esc_html( $item['text'] ) . '</span>';
            continue;
        }

        $url = isset( $item['url'] ) ? $item['url'] : '';
        if ( '' === $url ) {
            continue;
        }

        $icon       = isset( $item['icon'] ) ? $item['icon'] : '';
        $aria_label = '';

        switch ( $type ) {
            case 'email':
                $aria_label = __( 'Invia una email', 'poetheme' );
                break;
            case 'phone':
                $aria_label = __( 'Chiama il numero', 'poetheme' );
                break;
            case 'whatsapp':
                $aria_label = __( 'Apri chat WhatsApp', 'poetheme' );
                break;
            case 'location':
                $aria_label = __( 'Apri la posizione su Google Maps', 'poetheme' );
                break;
        }

        $link_attr = $link_class ? ' class="' . esc_attr( $link_class ) . '"' : '';
        if ( $aria_label ) {
            $link_attr .= ' aria-label="' . esc_attr( $aria_label ) . '"';
        }

        if ( ! empty( $item['target'] ) ) {
            $link_attr .= ' target="' . esc_attr( $item['target'] ) . '"';
        }

        if ( ! empty( $item['rel'] ) ) {
            $link_attr .= ' rel="' . esc_attr( $item['rel'] ) . '"';
        }

        echo '<a href="' . esc_url( $url ) . '"' . $link_attr . '>';

        if ( $icon ) {
            echo '<i data-lucide="' . esc_attr( $icon ) . '"' . ( $icon_class ? ' class="' . esc_attr( $icon_class ) . '"' : '' ) . '></i>';
        }

        echo '<span>' . esc_html( $item['text'] ) . '</span>';
        echo '</a>';
    }

    echo '</div>';
}

/**
 * Output accessible pagination.
 */
function poetheme_the_posts_navigation() {
    the_posts_pagination(
        array(
            'mid_size'           => 2,
            'prev_text'          => __( 'Previous', 'poetheme' ),
            'next_text'          => __( 'Next', 'poetheme' ),
            'screen_reader_text' => __( 'Posts navigation', 'poetheme' ),
        )
    );
}
