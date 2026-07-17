<?php
/**
 * Custom post types and taxonomies.
 *
 * @package KoenCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Project post type.
 */
function koen_core_register_post_types(): void {
	register_post_type(
		'project',
		array(
			'labels'        => array(
				'name'               => __( 'Projects', 'koen-core' ),
				'singular_name'      => __( 'Project', 'koen-core' ),
				'add_new_item'       => __( 'Add New Project', 'koen-core' ),
				'edit_item'          => __( 'Edit Project', 'koen-core' ),
				'new_item'           => __( 'New Project', 'koen-core' ),
				'view_item'          => __( 'View Project', 'koen-core' ),
				'search_items'       => __( 'Search Projects', 'koen-core' ),
				'not_found'          => __( 'No projects found.', 'koen-core' ),
				'not_found_in_trash' => __( 'No projects found in Trash.', 'koen-core' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'has_archive'   => false,
			'rewrite'       => false,
			'menu_icon'     => 'dashicons-portfolio',
			'menu_position' => 5,
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		)
	);
}
add_action( 'init', 'koen_core_register_post_types' );

/**
 * Register the Project Type taxonomy.
 */
function koen_core_register_taxonomies(): void {
	register_taxonomy(
		'project_type',
		'project',
		array(
			'labels'       => array(
				'name'          => __( 'Project Types', 'koen-core' ),
				'singular_name' => __( 'Project Type', 'koen-core' ),
			),
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'project-type' ),
		)
	);
}
add_action( 'init', 'koen_core_register_taxonomies' );
