<?php
/**
 * Skill post type — content for the front page "What I do" grid.
 *
 * Default card image = featured image; hover card image = custom meta
 * with a Media Library picker (assets/js/media-picker.js).
 *
 * @package KoenCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Skill post type. Not publicly queryable — skills only
 * appear inside the front page grid, never at their own URLs.
 */
function koen_core_register_skill_post_type(): void {
	register_post_type(
		'skill',
		array(
			'labels'        => array(
				'name'                  => __( 'Skills', 'koen-core' ),
				'singular_name'         => __( 'Skill', 'koen-core' ),
				'add_new_item'          => __( 'Add New Skill', 'koen-core' ),
				'edit_item'             => __( 'Edit Skill', 'koen-core' ),
				'featured_image'        => __( 'Card image (default state)', 'koen-core' ),
				'set_featured_image'    => __( 'Set default card image', 'koen-core' ),
				'remove_featured_image' => __( 'Remove default card image', 'koen-core' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-lightbulb',
			'menu_position' => 6,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'koen_core_register_skill_post_type' );

/**
 * Register the hover-image meta.
 */
function koen_core_register_skill_meta(): void {
	register_post_meta(
		'skill',
		'_koen_skill_hover_id',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => static function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'koen_core_register_skill_meta' );

/**
 * Add the hover-image meta box.
 */
function koen_core_add_skill_meta_box(): void {
	add_meta_box(
		'koen-skill-hover',
		__( 'Card image (hover state)', 'koen-core' ),
		'koen_core_render_skill_hover_box',
		'skill',
		'side'
	);
}
add_action( 'add_meta_boxes', 'koen_core_add_skill_meta_box' );

/**
 * Render the hover-image picker.
 *
 * @param WP_Post $post The current post.
 */
function koen_core_render_skill_hover_box( WP_Post $post ): void {
	wp_nonce_field( 'koen_skill_hover', 'koen_skill_hover_nonce' );
	$hover_id = (int) get_post_meta( $post->ID, '_koen_skill_hover_id', true );
	?>
	<div data-koen-media-picker>
		<input type="hidden" name="_koen_skill_hover_id" value="<?php echo esc_attr( (string) $hover_id ); ?>">
		<div data-koen-media-preview style="margin-bottom: 8px;">
			<?php
			if ( $hover_id ) {
				echo wp_get_attachment_image( $hover_id, 'medium', false, array( 'style' => 'max-width:100%;height:auto;' ) );
			}
			?>
		</div>
		<button type="button" class="button" data-koen-media-select><?php esc_html_e( 'Select image', 'koen-core' ); ?></button>
		<button type="button" class="button" data-koen-media-remove <?php echo $hover_id ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'Remove', 'koen-core' ); ?></button>
	</div>
	<?php
}

/**
 * Save the hover-image meta box.
 *
 * @param int $post_id The post being saved.
 */
function koen_core_save_skill_meta_box( int $post_id ): void {
	if ( ! isset( $_POST['koen_skill_hover_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( $_POST['koen_skill_hover_nonce'] ), 'koen_skill_hover' )
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$hover_id = isset( $_POST['_koen_skill_hover_id'] ) ? absint( $_POST['_koen_skill_hover_id'] ) : 0;

	if ( $hover_id ) {
		update_post_meta( $post_id, '_koen_skill_hover_id', $hover_id );
	} else {
		delete_post_meta( $post_id, '_koen_skill_hover_id' );
	}
}
add_action( 'save_post_skill', 'koen_core_save_skill_meta_box' );

/**
 * Load the media picker on Skill edit screens only.
 *
 * @param string $hook The current admin page hook.
 */
function koen_core_skill_admin_assets( string $hook ): void {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'skill' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'koen-core-media-picker',
		KOEN_CORE_URL . 'assets/js/media-picker.js',
		array(),
		KOEN_CORE_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'koen_core_skill_admin_assets' );
