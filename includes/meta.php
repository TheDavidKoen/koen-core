<?php
/**
 * Project meta fields — registration, meta box, and saving.
 *
 * @package KoenCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * The project meta fields schema.
 *
 * @return array<string, array{label: string, type: string}>
 */
function koen_core_project_fields(): array {
	return array(
		'_koen_project_stack'    => array(
			'label' => __( 'Tech Stack (comma-separated)', 'koen-core' ),
			'type'  => 'text',
		),
		'_koen_project_live_url' => array(
			'label' => __( 'Live URL', 'koen-core' ),
			'type'  => 'url',
		),
		'_koen_project_repo_url' => array(
			'label' => __( 'Repository URL', 'koen-core' ),
			'type'  => 'url',
		),
	);
}

/**
 * Register meta so it is available via REST and the block editor.
 */
function koen_core_register_meta(): void {
	foreach ( koen_core_project_fields() as $key => $field ) {
		register_post_meta(
			'project',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'url' === $field['type'] ? 'sanitize_url' : 'sanitize_text_field',
				'auth_callback'     => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'koen_core_register_meta' );

/**
 * Add the Project Details meta box.
 */
function koen_core_add_meta_box(): void {
	add_meta_box(
		'koen-project-details',
		__( 'Project Details', 'koen-core' ),
		'koen_core_render_meta_box',
		'project',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'koen_core_add_meta_box' );

/**
 * Render the Project Details meta box.
 *
 * @param WP_Post $post The current post.
 */
function koen_core_render_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'koen_project_details', 'koen_project_details_nonce' );
	?>
	<table class="form-table" role="presentation">
		<?php foreach ( koen_core_project_fields() as $key => $field ) : ?>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				</th>
				<td>
					<input
						type="<?php echo esc_attr( $field['type'] ); ?>"
						id="<?php echo esc_attr( $key ); ?>"
						name="<?php echo esc_attr( $key ); ?>"
						value="<?php echo esc_attr( get_post_meta( $post->ID, $key, true ) ); ?>"
						class="regular-text"
					>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
}

/**
 * Save the Project Details meta box.
 *
 * @param int $post_id The post being saved.
 */
function koen_core_save_meta_box( int $post_id ): void {
	if ( ! isset( $_POST['koen_project_details_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( $_POST['koen_project_details_nonce'] ), 'koen_project_details' )
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( koen_core_project_fields() as $key => $field ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}

		$raw   = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized on the next line.
		$value = 'url' === $field['type'] ? sanitize_url( $raw ) : sanitize_text_field( $raw );

		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post_project', 'koen_core_save_meta_box' );
