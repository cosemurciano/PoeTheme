<?php
/**
 * Navigation helpers and custom walkers.
 *
 * Responsibility: define navigation-related helpers and walker classes.
 * It must NOT register theme options or enqueue assets.
 *
 * @package PoeTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'PoeTheme_Nav_Walker' ) ) {
    /**
     * Custom walker used to render theme navigation menus.
     */
    class PoeTheme_Nav_Walker extends Walker_Nav_Menu {
        /**
         * Menu layout (primary or top-info).
         *
         * @var string
         */
        protected $layout = 'primary';

        /**
         * Menu variant (desktop or mobile).
         *
         * @var string
         */
        protected $variant = 'desktop';

        /**
         * Stack of menu item IDs while traversing submenu levels.
         *
         * @var array
         */
        protected $submenu_stack = array();

        /**
         * Map of menu item IDs to submenu element IDs.
         *
         * @var array
         */
        protected $submenu_ids = array();

        /**
         * Constructor.
         *
         * @param array|string $args Walker configuration.
         */
        public function __construct( $args = array() ) {
            if ( is_string( $args ) ) {
                $args = array( 'layout' => $args );
            }

            $args          = wp_parse_args(
                $args,
                array(
                    'layout'  => 'primary',
                    'variant' => 'desktop',
                )
            );
            $this->layout  = $args['layout'];
            $this->variant = $args['variant'];
        }

        /**
         * Starts the list before the elements are added.
         *
         * @param string   $output Passed by reference. Used to append additional content.
         * @param int      $depth  Depth of menu item. Used for padding.
         * @param stdClass $args   An object of wp_nav_menu() arguments.
         */
        public function start_lvl( &$output, $depth = 0, $args = null ) {
            $indent = str_repeat( "\t", $depth );

            $parent_id = ! empty( $this->submenu_stack ) ? end( $this->submenu_stack ) : 0;
            $submenu_id = $parent_id && isset( $this->submenu_ids[ $parent_id ] ) ? $this->submenu_ids[ $parent_id ] : '';

            if ( 'mobile' === $this->variant ) {
                $classes = array( 'poetheme-submenu', 'pl-4', 'border-l', 'border-gray-200', 'space-y-2', 'mt-2' );
                if ( $depth > 0 ) {
                    $classes[] = 'ml-2';
                }

                $attributes  = $submenu_id ? " id='" . esc_attr( $submenu_id ) . "'" : '';
                $attributes .= " class='" . esc_attr( implode( ' ', $classes ) ) . "'";
                $attributes .= " data-poetheme-submenu='true'";
                $attributes .= " data-depth='" . esc_attr( (string) ( $depth + 1 ) ) . "'";
                $attributes .= " aria-hidden='true'";

                $output .= "\n{$indent}<ul{$attributes}>\n";
                return;
            }

            $classes = array(
                'poetheme-submenu',
                'absolute',
                'z-30',
                'hidden',
                'bg-white',
                'rounded-lg',
                'shadow-lg',
                'py-2',
                'w-56',
                'space-y-1',
                'group-hover:block',
                'group-focus-within:block',
                'transition',
                'duration-150',
            );

            if ( 0 === $depth ) {
                $classes[] = 'left-0';
                $classes[] = 'mt-2';
            } else {
                $classes[] = 'left-full';
                $classes[] = 'top-0';
                $classes[] = 'ml-1';
            }

            $attributes  = $submenu_id ? " id='" . esc_attr( $submenu_id ) . "'" : '';
            $attributes .= " class='" . esc_attr( implode( ' ', $classes ) ) . "'";
            $attributes .= " data-poetheme-submenu='true'";
            $attributes .= " data-depth='" . esc_attr( (string) ( $depth + 1 ) ) . "'";
            $attributes .= " aria-hidden='true'";

            $output .= "\n{$indent}<ul{$attributes}>\n";
        }

        /**
         * Ends the list of after the elements are added.
         *
         * @param string   $output Passed by reference. Used to append additional content.
         * @param int      $depth  Depth of menu item. Used for padding.
         * @param stdClass $args   An object of wp_nav_menu() arguments.
         */
        public function end_lvl( &$output, $depth = 0, $args = null ) {
            $indent = str_repeat( "\t", $depth );
            $output .= "{$indent}</ul>\n";
        }

        /**
         * Starts the element output.
         *
         * @param string   $output Passed by reference. Used to append additional content.
         * @param WP_Post  $item   Menu item data object.
         * @param int      $depth  Depth of menu item.
         * @param stdClass $args   An object of wp_nav_menu() arguments.
         * @param int      $id     Current item ID.
         */
        public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
            $indent      = ( $depth ) ? str_repeat( "\t", $depth ) : '';
            $classes     = empty( $item->classes ) ? array() : (array) $item->classes;
            $classes[]   = 'menu-item-' . $item->ID;
            $has_children = in_array( 'menu-item-has-children', $classes, true );
            $submenu_id   = '';

            if ( $has_children ) {
                $submenu_id                           = 'poetheme-submenu-' . $item->ID;
                $this->submenu_ids[ $item->ID ]       = $submenu_id;
                $this->submenu_stack[]                = $item->ID;
            }

            $li_classes = array( 'poetheme-menu-item', 'relative' );

            if ( 'mobile' !== $this->variant && $has_children ) {
                $li_classes[] = 'group';
            }

            if ( ! empty( $classes ) ) {
                foreach ( $classes as $class ) {
                    $sanitized = sanitize_html_class( $class );
                    if ( $sanitized ) {
                        $li_classes[] = $sanitized;
                    }
                }
            }

            $class_names = implode( ' ', array_unique( $li_classes ) );
            $class_names = $class_names ? " class='" . esc_attr( $class_names ) . "'" : '';

            $output .= $indent . '<li' . $class_names . '>';

            $atts           = array();
            $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
            $atts['target'] = ! empty( $item->target ) ? $item->target : '';
            $atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
            $atts['href']   = ! empty( $item->url ) ? $item->url : '';

            if ( $has_children ) {
                $atts['aria-haspopup'] = 'true';
                $atts['aria-expanded'] = 'false';
                if ( $submenu_id ) {
                    $atts['aria-controls']        = $submenu_id;
                    $atts['data-poetheme-toggle'] = 'submenu';
                    $atts['data-poetheme-target'] = $submenu_id;
                }
            }

            $link_classes = $this->get_link_classes( $depth, $has_children, $item );
            if ( $link_classes ) {
                $atts['class'] = trim( $link_classes );
            }

            $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

            $attributes = '';
            foreach ( $atts as $attr => $value ) {
                if ( empty( $value ) ) {
                    continue;
                }

                $value      = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }

            $title = apply_filters( 'the_title', $item->title, $item->ID );

            $icon     = get_post_meta( $item->ID, 'poetheme_menu_icon', true );
            $is_bold  = (bool) get_post_meta( $item->ID, 'poetheme_menu_bold', true );
            $icon_svg = '';

            if ( $icon ) {
                $icon_svg = '<i data-lucide="' . esc_attr( $icon ) . '" class="w-4 h-4"></i>';
            }

            $indicator = '';
            if ( $has_children ) {
                if ( 'mobile' === $this->variant ) {
                    $indicator_icon = 'chevron-down';
                } else {
                    $indicator_icon = ( 0 === $depth ) ? 'chevron-down' : 'chevron-right';
                }

                $indicator_classes = array( 'poetheme-submenu-indicator', 'w-4', 'h-4', 'ml-1', 'text-gray-400' );

                if ( 'mobile' === $this->variant ) {
                    $indicator_classes[] = 'transition-transform';
                    $indicator_classes[] = 'duration-200';
                }

                $indicator = '<i data-lucide="' . esc_attr( $indicator_icon ) . '" class="' . esc_attr( implode( ' ', $indicator_classes ) ) . '"></i>';
            }

            $title_markup = '<span class="menu-item-text' . ( $is_bold ? ' font-semibold' : '' ) . '">' . esc_html( $title ) . '</span>';

            $item_output  = $args->before;
            $item_output .= '<a' . $attributes . '>';
            $item_output .= $args->link_before;

            if ( $icon_svg ) {
                $item_output .= '<span class="menu-item-icon">' . $icon_svg . '</span>';
            }

            $item_output .= $title_markup;

            if ( $indicator ) {
                $item_output .= $indicator;
            }

            $item_output .= $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        }

        /**
         * Ends the element output, if needed.
         *
         * @param string   $output Passed by reference. Used to append additional content.
         * @param WP_Post  $item   Page data object. Not used.
         * @param int      $depth  Depth of menu item. Not Used.
         * @param stdClass $args   An object of wp_nav_menu() arguments.
         */
        public function end_el( &$output, $item, $depth = 0, $args = null ) {
            if ( ! empty( $this->submenu_stack ) && end( $this->submenu_stack ) === $item->ID ) {
                array_pop( $this->submenu_stack );
            }

            if ( isset( $this->submenu_ids[ $item->ID ] ) ) {
                unset( $this->submenu_ids[ $item->ID ] );
            }

            $output .= "</li>\n";
        }

        /**
         * Build the CSS classes for a menu link.
         *
         * @param int     $depth        Menu depth.
         * @param bool    $has_children Whether the item has children.
         * @param WP_Post $item         The menu item.
         *
         * @return string
         */
        protected function get_link_classes( $depth, $has_children, $item ) {
            $classes = array( 'inline-flex', 'items-center', 'gap-2', 'transition', 'duration-150', 'ease-out' );
            $is_primary_layout = ( 'primary' === $this->layout );

            if ( 'mobile' === $this->variant ) {
                $classes = array( 'flex', 'items-center', 'gap-2', 'w-full', 'text-left', 'transition', 'duration-150' );
                $classes[] = ( 0 === $depth ) ? 'text-base' : 'text-sm';
                $classes[] = 'text-gray-800';
                $classes[] = 'hover:text-blue-600';
                $classes[] = 'py-1.5';
                return implode( ' ', array_filter( $classes ) );
            }

            if ( 'top-info' === $this->layout ) {
                if ( 0 === $depth ) {
                    $classes[] = 'text-sm';
                    $classes[] = 'text-gray-200';
                    $classes[] = 'hover:text-white';
                } else {
                    $classes[] = 'px-4';
                    $classes[] = 'py-2';
                    $classes[] = 'text-sm';
                    $classes[] = 'text-gray-700';
                    $classes[] = 'hover:bg-gray-100';
                }
            } else {
                if ( 0 === $depth ) {
                    $classes[] = 'text-sm';
                    $classes[] = 'text-gray-700';
                    $classes[] = 'hover:text-blue-600';
                    if ( $is_primary_layout ) {
                        $classes[] = 'poetheme-nav-link--level-0';
                    } else {
                        $classes[] = 'py-2';
                    }
                } else {
                    $classes[] = 'px-4';
                    $classes[] = 'py-2';
                    $classes[] = 'text-sm';
                    $classes[] = 'text-gray-700';
                    $classes[] = 'hover:bg-gray-100';
                    if ( $is_primary_layout ) {
                        $classes[] = 'flex';
                        $classes[] = 'w-full';
                        $classes[] = 'poetheme-nav-link--submenu';
                    }
                }
            }

            return implode( ' ', array_filter( $classes ) );
        }
    }
}

if ( ! function_exists( 'poetheme_render_navigation_menu' ) ) {
    /**
     * Helper function to render theme menus with the custom walker.
     *
     * @param string $location Menu location slug.
     * @param string $variant  Menu variant (desktop or mobile).
     * @param array  $args     Additional arguments passed to wp_nav_menu().
     */
    function poetheme_render_navigation_menu( $location, $variant = 'desktop', $args = array() ) {
        $defaults = array(
            'theme_location' => $location,
            'container'      => false,
            'depth'          => 0,
            'items_wrap'     => '<ul id="%1$s" class="%2$s" data-poetheme-nav="1" data-variant="' . esc_attr( $variant ) . '" data-location="' . esc_attr( $location ) . '">%3$s</ul>',
        );

        if ( 'top-info' === $location ) {
            $defaults['fallback_cb'] = false;
        } else {
            $defaults['fallback_cb'] = 'wp_page_menu';
        }

        $walker_args         = array(
            'layout'  => ( 'top-info' === $location ) ? 'top-info' : 'primary',
            'variant' => ( 'mobile' === $variant ) ? 'mobile' : 'desktop',
        );
        $defaults['walker']   = new PoeTheme_Nav_Walker( $walker_args );

        if ( 'mobile' === $variant ) {
            $defaults['depth'] = 0;
        }

        $args = wp_parse_args( $args, $defaults );

        $base_classes = array( 'poetheme-nav', 'poetheme-nav--' . ( 'mobile' === $variant ? 'mobile' : 'desktop' ) );
        if ( $location ) {
            $base_classes[] = 'poetheme-nav--location-' . sanitize_html_class( $location );
        }

        if ( ! empty( $args['menu_class'] ) ) {
            $args['menu_class'] .= ' ' . implode( ' ', $base_classes );
        } else {
            $args['menu_class'] = implode( ' ', $base_classes );
        }

        wp_nav_menu( $args );
    }
}

if ( ! function_exists( 'poetheme_menu_item_custom_fields' ) ) {
    /**
     * Output custom fields for nav menu items.
     *
     * @param int     $item_id Menu item ID.
     * @param WP_Post $item    Menu item object.
     */
    function poetheme_menu_item_custom_fields( $item_id, $item, $depth, $args ) {
        $is_bold   = (bool) get_post_meta( $item_id, 'poetheme_menu_bold', true );
        $icon      = get_post_meta( $item_id, 'poetheme_menu_icon', true );
        $icon_name = $icon ? $icon : '';
        ?>
        <p class="description description-wide poetheme-menu-bold-field">
            <label for="poetheme-menu-bold-<?php echo esc_attr( $item_id ); ?>">
                <input type="checkbox" id="poetheme-menu-bold-<?php echo esc_attr( $item_id ); ?>" name="poetheme_menu_bold[<?php echo esc_attr( $item_id ); ?>]" value="1" <?php checked( $is_bold ); ?> />
                <?php esc_html_e( 'Mostra il testo in grassetto', 'poetheme' ); ?>
            </label>
        </p>
        <div class="description description-wide poetheme-menu-icon-field">
            <label for="poetheme-menu-icon-<?php echo esc_attr( $item_id ); ?>"><?php esc_html_e( 'Icona Lucide', 'poetheme' ); ?></label>
            <div class="poetheme-menu-icon-control">
                <input type="hidden" id="poetheme-menu-icon-<?php echo esc_attr( $item_id ); ?>" class="poetheme-menu-icon-input" name="poetheme_menu_icon[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $icon_name ); ?>" data-item-id="<?php echo esc_attr( $item_id ); ?>" />
                <div class="poetheme-menu-icon-preview" data-empty-label="<?php esc_attr_e( 'Nessuna icona selezionata', 'poetheme' ); ?>">
                    <?php if ( $icon_name ) : ?>
                        <span class="poetheme-menu-icon-example"><i data-lucide="<?php echo esc_attr( $icon_name ); ?>" class="w-4 h-4"></i></span>
                        <span class="poetheme-menu-icon-name"><?php echo esc_html( $icon_name ); ?></span>
                    <?php else : ?>
                        <span class="poetheme-menu-icon-placeholder"><?php esc_html_e( 'Nessuna icona selezionata', 'poetheme' ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="poetheme-menu-icon-actions">
                    <button type="button" class="button button-secondary poetheme-open-icon-picker" data-target="#poetheme-menu-icon-<?php echo esc_attr( $item_id ); ?>"><?php esc_html_e( 'Scegli icona', 'poetheme' ); ?></button>
                    <button type="button" class="button button-link-delete poetheme-clear-icon" data-target="#poetheme-menu-icon-<?php echo esc_attr( $item_id ); ?>"><?php esc_html_e( 'Rimuovi icona', 'poetheme' ); ?></button>
                </div>
            </div>
        </div>
        <?php
    }
    add_action( 'wp_nav_menu_item_custom_fields', 'poetheme_menu_item_custom_fields', 10, 4 );
}

if ( ! function_exists( 'poetheme_save_menu_item_meta' ) ) {
    /**
     * Save custom nav menu item meta.
     *
     * @param int   $menu_id         The menu ID.
     * @param int   $menu_item_db_id The menu item ID.
     */
    function poetheme_save_menu_item_meta( $menu_id, $menu_item_db_id ) {
        $bold_values = isset( $_POST['poetheme_menu_bold'] ) ? wp_unslash( $_POST['poetheme_menu_bold'] ) : array();
        $icon_values = isset( $_POST['poetheme_menu_icon'] ) ? wp_unslash( $_POST['poetheme_menu_icon'] ) : array();

        $is_bold = isset( $bold_values[ $menu_item_db_id ] ) ? '1' : '';

        if ( $is_bold ) {
            update_post_meta( $menu_item_db_id, 'poetheme_menu_bold', '1' );
        } else {
            delete_post_meta( $menu_item_db_id, 'poetheme_menu_bold' );
        }

        if ( isset( $icon_values[ $menu_item_db_id ] ) ) {
            $icon = sanitize_text_field( $icon_values[ $menu_item_db_id ] );
            if ( $icon ) {
                update_post_meta( $menu_item_db_id, 'poetheme_menu_icon', $icon );
            } else {
                delete_post_meta( $menu_item_db_id, 'poetheme_menu_icon' );
            }
        } else {
            delete_post_meta( $menu_item_db_id, 'poetheme_menu_icon' );
        }
    }
    add_action( 'wp_update_nav_menu_item', 'poetheme_save_menu_item_meta', 10, 2 );
}

if ( ! function_exists( 'poetheme_get_lucide_icon_groups' ) ) {
    /**
     * Returns grouped Lucide icons for the picker UI.
     *
     * @return array
     */
    function poetheme_get_lucide_icon_groups() {
        return array(
            array(
                'label' => __( 'Interfaccia', 'poetheme' ),
                'icons' => array( 'home', 'menu', 'settings', 'search', 'star', 'heart', 'bell', 'bookmark', 'check', 'x', 'chevron-down', 'chevron-right', 'circle', 'square' ),
            ),
            array(
                'label' => __( 'Contenuti', 'poetheme' ),
                'icons' => array( 'file', 'folder', 'image', 'film', 'music', 'book', 'camera', 'layout-dashboard', 'list', 'grid', 'align-left', 'type' ),
            ),
            array(
                'label' => __( 'Comunicazione', 'poetheme' ),
                'icons' => array( 'mail', 'phone', 'message-circle', 'message-square', 'send', 'share-2', 'users', 'user', 'user-plus', 'user-check' ),
            ),
            array(
                'label' => __( 'E-commerce', 'poetheme' ),
                'icons' => array( 'shopping-cart', 'shopping-bag', 'credit-card', 'wallet', 'tag', 'gift', 'percent', 'package' ),
            ),
            array(
                'label' => __( 'Varie', 'poetheme' ),
                'icons' => array( 'globe', 'map-pin', 'sun', 'moon', 'cloud', 'umbrella', 'coffee', 'leaf', 'sparkles', 'shield', 'rocket' ),
            ),
        );
    }
}

if ( ! function_exists( 'poetheme_admin_menu_assets' ) ) {
    /**
     * Enqueue assets for the nav menu admin screen.
     *
     * @param string $hook Current admin page hook.
     */
    function poetheme_admin_menu_assets( $hook ) {
        if ( 'nav-menus.php' !== $hook ) {
            return;
        }

        wp_enqueue_style( 'poetheme-menu-icons', POETHEME_URI . '/assets/css/menu-icons.css', array(), poetheme_get_asset_version( 'assets/css/menu-icons.css' ) );
        wp_enqueue_script( 'poetheme-menu-icons', POETHEME_URI . '/assets/js/menu-icons.js', array( 'jquery' ), poetheme_get_asset_version( 'assets/js/menu-icons.js' ), true );

        $lucide = function_exists( 'poetheme_get_cdn_asset' ) ? poetheme_get_cdn_asset( 'poetheme-lucide-admin' ) : array();
        $lucide_src = isset( $lucide['src'] ) ? $lucide['src'] : 'https://cdn.jsdelivr.net/npm/lucide@0.294.0/dist/umd/lucide.min.js';
        $lucide_ver = isset( $lucide['version'] ) ? $lucide['version'] : POETHEME_VERSION;
        wp_enqueue_script( 'poetheme-lucide-admin', $lucide_src, array(), $lucide_ver, true );

        wp_localize_script(
            'poetheme-menu-icons',
            'poethemeMenuIcons',
            array(
                'groups'        => poetheme_get_lucide_icon_groups(),
                'searchLabel'   => __( 'Cerca tra le icone...', 'poetheme' ),
                'noResults'     => __( 'Nessuna icona corrisponde ai criteri di ricerca.', 'poetheme' ),
                'closeLabel'    => __( 'Chiudi', 'poetheme' ),
                'selectLabel'   => __( "Seleziona un'icona", 'poetheme' ),
                'allLabel'      => __( 'Tutte', 'poetheme' ),
            )
        );
    }
    add_action( 'admin_enqueue_scripts', 'poetheme_admin_menu_assets' );
}
