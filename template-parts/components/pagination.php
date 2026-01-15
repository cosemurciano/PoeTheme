<?php
/**
 * Pagination component.
 *
 * @package PoeTheme
 */

if ( ! have_posts() ) {
    return;
}
?>
<div class="poetheme-pagination mt-10">
    <?php poetheme_the_posts_navigation(); ?>
</div>
