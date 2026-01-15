<?php
/**
 * Search results template.
 *
 * @package PoeTheme
 */

get_header();

$header_args = array(
    'title'       => sprintf( __( 'Risultati della ricerca per "%s"', 'poetheme' ), get_search_query() ),
    'description' => '',
);

get_template_part( 'template-parts/archive/content', null, array( 'header_args' => $header_args ) );

get_footer();
