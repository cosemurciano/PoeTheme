<?php
/**
 * Search form template.
 *
 * @package PoeTheme
 */
?>
<form role="search" method="get" class="search-form flex items-center gap-2" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label class="screen-reader-text" for="search-field"><?php esc_html_e( 'Search for:', 'poetheme' ); ?></label>
    <input type="search" id="search-field" class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200" placeholder="<?php esc_attr_e( 'Search …', 'poetheme' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md focus:ring-4 focus:ring-indigo-300">
        <?php esc_html_e( 'Search', 'poetheme' ); ?>
    </button>
</form>
