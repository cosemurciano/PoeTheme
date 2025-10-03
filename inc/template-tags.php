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
 * Get breadcrumbs items.
 *
 * @return array
 */
function poetheme_get_breadcrumbs_items() {
    $options = poetheme_get_options();

    if ( ! $options['enable_breadcrumbs'] ) {
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
    ?>
    <nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'poetheme' ); ?>">
        <ol class="flex space-x-2" itemscope itemtype="https://schema.org/BreadcrumbList">
            <?php foreach ( $items as $index => $item ) : ?>
                <li class="flex items-center" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <?php if ( ! empty( $item['url'] ) ) : ?>
                        <a href="<?php echo esc_url( $item['url'] ); ?>" itemprop="item">
                            <span itemprop="name"><?php echo esc_html( $item['label'] ); ?></span>
                        </a>
                    <?php else : ?>
                        <span itemprop="name"><?php echo esc_html( $item['label'] ); ?></span>
                    <?php endif; ?>
                    <meta itemprop="position" content="<?php echo esc_attr( $index + 1 ); ?>" />
                    <?php if ( $index < count( $items ) - 1 ) : ?>
                        <span class="mx-2" aria-hidden="true">/</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
    <?php
}

/**
 * Output site logo.
 */
function poetheme_the_logo() {
    if ( has_custom_logo() ) {
        the_custom_logo();
        return;
    }

    $logo_options = poetheme_get_logo_options();

    if ( ! empty( $logo_options['logo_id'] ) ) {
        $logo_markup = wp_get_attachment_image(
            $logo_options['logo_id'],
            'full',
            false,
            array(
                'class' => 'h-12 w-auto',
                'alt'   => get_bloginfo( 'name' ),
            )
        );

        if ( $logo_markup ) {
            echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="inline-flex" rel="home">';
            echo wp_kses_post( $logo_markup );
            echo '</a>';
            return;
        }
    }

    $options = poetheme_get_options();

    if ( ! empty( $options['custom_logo'] ) ) {
        $alt = esc_attr( get_bloginfo( 'name' ) );
        echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="inline-flex" rel="home">';
        echo '<img src="' . esc_url( $options['custom_logo'] ) . '" alt="' . $alt . '" class="h-12 w-auto" />';
        echo '</a>';
        return;
    }

    echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="text-2xl font-bold" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
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
                'text_1'   => '',
                'email'    => '',
                'phone'    => '',
                'whatsapp' => '',
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
        }

        $link_attr = $link_class ? ' class="' . esc_attr( $link_class ) . '"' : '';
        if ( $aria_label ) {
            $link_attr .= ' aria-label="' . esc_attr( $aria_label ) . '"';
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
