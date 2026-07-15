<?php
/**
 * Custom admin list-table columns for Projects.
 *
 * @package KoenCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Define the Projects list-table columns.
 *
 * @param array<string, string> $columns Default columns.
 * @return array<string, string>
 */
function koen_core_project_columns( array $columns ): array {
	return array(
		'cb'                    => $columns['cb'],
		'koen_thumbnail'        => __( 'Image', 'koen-core' ),
		'title'                 => $columns['title'],
		'koen_stack'            => __( 'Stack', 'koen-core' ),
		'taxonomy-project_type' => __( 'Type', 'koen-core' ),
		'date'                  => $columns['date'],
	);
}
add_filter( 'manage_project_posts_columns', 'koen_core_project_columns' );

/**
 * Populate the custom Projects columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Current post ID.
 */
function koen_core_project_column_content( string $column, int $post_id ): void {
	switch ( $column ) {
		case 'koen_thumbnail':
			echo get_the_post_thumbnail( $post_id, array( 60, 60 ) );
			break;
		case 'koen_stack':
			echo esc_html( get_post_meta( $post_id, '_koen_project_stack', true ) );
			break;
	}
}
add_action( 'manage_project_posts_custom_column', 'koen_core_project_column_content', 10, 2 );
