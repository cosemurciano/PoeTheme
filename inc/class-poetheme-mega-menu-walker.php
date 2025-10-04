<?php
/**
 * Mega menu walker for PoeTheme.
 *
 * @package PoeTheme
 */

if ( ! class_exists( 'PoeTheme_Mega_Menu_Walker' ) ) {
    /**
     * Custom walker that renders a mega menu when children are present.
     */
    class PoeTheme_Mega_Menu_Walker extends Walker_Nav_Menu {
        /**
         * Keep track of items that have children to close wrappers properly.
         *
         * @var array<int, bool>
         */
        protected $items_with_children = array();

        /**
         * Start the list before the elements are added.
         *
         * @param string $output Passed by reference. Used to append additional content.
         * @param int    $depth  Depth of menu item. Used for padding.
         * @param array  $args   An array of arguments. @see wp_nav_menu().
         */
        public function start_lvl( &$output, $depth = 0, $args = array() ) {
            if ( 0 === $depth ) {
                $output .= "\n<div class=\"mega-menu-bridge\" x-show=\"isOpen\" x-cloak @mouseenter=\"open()\" @mouseleave=\"scheduleClose()\"></div>";
                $output .= "\n<div class=\"mega-menu-dropdown align-left bg-white shadow-2xl rounded-lg p-6\" x-show=\"isOpen\" x-transition.opacity.duration.200ms x-cloak :class=\"{ 'is-open': isOpen }\" @mouseenter=\"open()\" @mouseleave=\"scheduleClose()\">";
                $output .= "\n    <div class=\"mega-menu-grid grid gap-6 sm:grid-cols-2 lg:grid-cols-3\">";
            } elseif ( 1 === $depth ) {
                $output .= "\n        <ul class=\"mega-menu-links space-y-2 ml-7\">";
            } else {
                $output .= "\n<ul class=\"sub-menu\">";
            }
        }

        /**
         * Ends the list of after the elements are added.
         *
         * @param string $output Passed by reference. Used to append additional content.
         * @param int    $depth  Depth of menu item. Used for padding.
         * @param array  $args   An array of arguments. @see wp_nav_menu().
         */
        public function end_lvl( &$output, $depth = 0, $args = array() ) {
            if ( 0 === $depth ) {
                $output .= "\n    </div>"; // .mega-menu-grid.
                $output .= "\n</div>"; // .mega-menu-dropdown.
            } elseif ( 1 === $depth ) {
                $output .= "\n        </ul>";
            } else {
                $output .= "\n</ul>";
            }
        }

        /**
         * Starts the element output.
         *
         * @param string   $output            Passed by reference. Used to append additional content.
         * @param WP_Post  $item              Menu item data object.
         * @param int      $depth             Depth of menu item. Used for padding.
         * @param stdClass $args              An object of wp_nav_menu() arguments.
         * @param int      $id                Current item ID.
         */
        public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
            $classes = empty( $item->classes ) ? array() : (array) $item->classes;
            $classes = array_filter( $classes, 'strlen' );

            $has_children = ! empty( $args->has_children ) || in_array( 'menu-item-has-children', $classes, true );
            if ( $has_children ) {
                $this->items_with_children[ $item->ID ] = true;
            }

            $title    = apply_filters( 'the_title', $item->title, $item->ID );
            $is_title = (bool) get_post_meta( $item->ID, '_poetheme_menu_is_title', true );
            $icon     = trim( (string) get_post_meta( $item->ID, '_poetheme_menu_icon', true ) );

            $atts            = array();
            $atts['title']   = ! empty( $item->attr_title ) ? $item->attr_title : '';
            $atts['target']  = ! empty( $item->target ) ? $item->target : '';
            $atts['rel']     = ! empty( $item->xfn ) ? $item->xfn : '';
            $atts['href']    = ! empty( $item->url ) ? $item->url : '';

            if ( in_array( trim( (string) $atts['href'] ), array( '', '#', '#0', '#!' ), true ) ) {
                $atts['href']   = '';
                $atts['target'] = '';
            }
            $atts['aria-haspopup'] = $has_children ? 'true' : '';
            $atts['aria-expanded'] = $has_children ? 'false' : '';

            $attributes = '';
            foreach ( $atts as $attr => $value ) {
                if ( '' === $value ) {
                    continue;
                }

                if ( 'href' === $attr ) {
                    $value = esc_url( $value );
                } else {
                    $value = esc_attr( $value );
                }

                $attributes .= ' ' . $attr . '="' . $value . '"';
            }

            $icon_html = '';
            if ( $icon ) {
                $icon_html = '<i data-lucide="' . esc_attr( $icon ) . '" class="w-5 h-5"></i>';
            }

            $title_classes = $is_title ? 'font-semibold text-gray-900' : 'font-medium text-gray-700';

            if ( 0 === $depth ) {
                $class_names     = implode( ' ', array_map( 'sanitize_html_class', $classes ) );
                $class_attribute = trim( 'menu-item depth-0 ' . $class_names );

                $output .= '<li class="' . esc_attr( $class_attribute ) . '">';

                if ( $has_children ) {
                    $title_attribute = '';
                    if ( ! empty( $atts['title'] ) ) {
                        $title_attribute = ' title="' . esc_attr( $atts['title'] ) . '"';
                    }

                    $output .= '<div class="mega-menu-wrapper relative" x-data="poethemeMegaMenu()" @mouseenter="open()" @mouseleave="scheduleClose()" @focusin="open()" @focusout.window="close()" @keydown.escape.window="close()">';
                    $output .= '<button type="button" class="mega-menu-trigger nav-link inline-flex items-center gap-2 transition-colors duration-150" aria-haspopup="true" x-bind:aria-expanded="isOpen ? \'true\' : \'false\'" @mouseenter="open()" @mouseleave="scheduleClose()" @click="toggle()"' . $title_attribute . '>' . $icon_html . '<span class="' . esc_attr( $title_classes ) . '">' . esc_html( $title ) . '</span><i data-lucide="chevron-down" class="w-4 h-4"></i></button>';
                } else {
                    $output .= '<a' . $attributes . ' class="nav-link inline-flex items-center gap-2 transition-colors duration-150">' . $icon_html . '<span class="' . esc_attr( $title_classes ) . '">' . esc_html( $title ) . '</span></a>';
                }
            } elseif ( 1 === $depth ) {
                $output .= '\n        <div class="mega-menu-group">';
                $link_classes = 'mega-menu-heading inline-flex items-center gap-2 text-sm text-gray-900';
                if ( $is_title ) {
                    $link_classes .= ' font-semibold';
                } else {
                    $link_classes .= ' font-medium';
                }

                if ( $atts['href'] ) {
                    $output .= '<a' . $attributes . ' class="' . esc_attr( $link_classes ) . '">' . $icon_html . '<span>' . esc_html( $title ) . '</span></a>';
                } else {
                    $output .= '<div class="' . esc_attr( $link_classes ) . '">' . $icon_html . '<span>' . esc_html( $title ) . '</span></div>';
                }
            } else {
                $output .= '\n            <li class="menu-item depth-' . intval( $depth ) . '">';
                $link_classes = 'inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition';
                if ( $is_title ) {
                    $link_classes .= ' font-semibold';
                }

                $output .= '<a' . $attributes . ' class="' . esc_attr( $link_classes ) . '">' . $icon_html . '<span>' . esc_html( $title ) . '</span></a>';
            }
        }

        /**
         * Ends the element output, if needed.
         *
         * @param string   $output Passed by reference. Used to append additional content.
         * @param WP_Post  $item   Page data object. Not used.
         * @param int      $depth  Depth of menu item. Used for padding.
         * @param stdClass $args   An object of wp_nav_menu() arguments.
         */
        public function end_el( &$output, $item, $depth = 0, $args = array() ) {
            if ( 0 === $depth ) {
                if ( isset( $this->items_with_children[ $item->ID ] ) ) {
                    $output .= '</div>'; // .mega-menu-wrapper.
                    unset( $this->items_with_children[ $item->ID ] );
                }

                $output .= '</li>';
            } elseif ( 1 === $depth ) {
                $output .= '\n        </div>';
            } else {
                $output .= '</li>';
            }
        }
    }
}
