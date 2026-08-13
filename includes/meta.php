<?php
/**
 * Project meta fields — registration, meta box, and saving.
 *
 * @package KoenCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * The technologies selectable as a project's stack.
 *
 * Labels are matched to icon files by the theme, which lowercases the label
 * and strips non-alphanumerics ("Node.js" -> nodejs.svg). Add a technology
 * here and drop the matching SVG in the theme to have its logo appear.
 *
 * @return array<int, string>
 */
function koen_core_tech_options(): array {
	$koen_options = array(
		'CSS',
		'Django',
		'Express',
		'Firebase',
		'Git',
		'GraphQL',
		'GSAP',
		'HTML5',
		'JavaScript',
		'MongoDB',
		'MySQL',
		'Next.js',
		'Node.js',
		'PHP',
		'Python',
		'React',
		'Sass',
		'Spring',
		'SQLite',
		'Tailwind CSS',
		'Three.js',
		'Vite',
		'WordPress',
	);

	/**
	 * Filters the selectable technologies.
	 *
	 * @param array<int, string> $koen_options Technology labels.
	 */
	return (array) apply_filters( 'koen_core_tech_options', $koen_options );
}

/**
 * The project meta fields schema.
 *
 * @return array<string, array{label: string, type: string}>
 */
function koen_core_project_fields(): array {
	return array(
		'_koen_project_stack'    => array(
			'label' => __( 'Tech Stack', 'koen-core' ),
			'type'  => 'multiselect',
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
			<?php $koen_value = (string) get_post_meta( $post->ID, $key, true ); ?>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				</th>
				<td>
					<?php if ( 'multiselect' === $field['type'] ) : ?>
						<?php $koen_selected = array_map( 'trim', explode( ',', $koen_value ) ); ?>
						<select
							id="<?php echo esc_attr( $key ); ?>"
							name="<?php echo esc_attr( $key ); ?>[]"
							multiple
							size="10"
							style="min-width: 18em;"
						>
							<?php foreach ( koen_core_tech_options() as $koen_option ) : ?>
								<option
									value="<?php echo esc_attr( $koen_option ); ?>"
									<?php selected( in_array( $koen_option, $koen_selected, true ) ); ?>
								>
									<?php echo esc_html( $koen_option ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="description">
							<?php esc_html_e( 'Hold Ctrl (Cmd on Mac) to select multiple technologies.', 'koen-core' ); ?>
						</p>
					<?php else : ?>
						<input
							type="<?php echo esc_attr( $field['type'] ); ?>"
							id="<?php echo esc_attr( $key ); ?>"
							name="<?php echo esc_attr( $key ); ?>"
							value="<?php echo esc_attr( $koen_value ); ?>"
							class="regular-text"
						>
					<?php endif; ?>
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
		if ( 'multiselect' === $field['type'] ) {
			$koen_raw = isset( $_POST[ $key ] ) ? (array) wp_unslash( $_POST[ $key ] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Validated against the option list below.
			// Only known technologies are stored, in the canonical list order.
			$koen_valid = array_values( array_intersect( koen_core_tech_options(), $koen_raw ) );
			$value      = implode( ', ', $koen_valid );
		} else {
			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}

			$raw   = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized on the next line.
			$value = 'url' === $field['type'] ? sanitize_url( $raw ) : sanitize_text_field( $raw );
		}

		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post_project', 'koen_core_save_meta_box' );