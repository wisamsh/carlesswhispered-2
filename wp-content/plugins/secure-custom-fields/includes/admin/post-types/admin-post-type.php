<?php
/**
 * ACF Admin Post Type Class
 *
 * @class ACF_Admin_Post_Type
 *
 * @package    ACF
 * @subpackage Admin
 */

if ( ! class_exists( 'ACF_Admin_Post_Type' ) ) :

	/**
	 * ACF Admin Post Type Class
	 *
	 * All the logic for editing a post type.
	 */
	class ACF_Admin_Post_Type extends ACF_Admin_Internal_Post_Type {

		/**
		 * The slug for the internal post type.
		 *
		 * @since ACF 6.1
		 * @var string
		 */
		public $post_type = 'acf-post-type';

		/**
		 * The admin body class used for the post type.
		 *
		 * @since ACF 6.1
		 * @var string
		 */
		public $admin_body_class = 'acf-admin-single-post-type';

		/**
		 * This function will customize the message shown when editing a post type.
		 *
		 * @since ACF 5.0.0
		 *
		 * @param array $messages Post type messages.
		 * @return array
		 */
		public function post_updated_messages( $messages ) {
			$messages['acf-post-type'] = array(
				0  => '', // Unused. Messages start at index 1.
				1  => $this->post_type_created_message(), // Updated.
				2  => $this->post_type_created_message(),
				3  => __( 'Post type deleted.', 'secure-custom-fields' ),
				4  => __( 'Post type updated.', 'secure-custom-fields' ),
				5  => false, // Post type does not support revisions.
				6  => $this->post_type_created_message( true ), // Created.
				7  => __( 'Post type saved.', 'secure-custom-fields' ),
				8  => __( 'Post type submitted.', 'secure-custom-fields' ),
				9  => __( 'Post type scheduled for.', 'secure-custom-fields' ),
				10 => __( 'Post type draft updated.', 'secure-custom-fields' ),
			);

			return $messages;
		}

		/**
		 * Renders the post type created message.
		 *
		 * @since ACF 6.1
		 *
		 * @param boolean $created True if the post was just created.
		 * @return string
		 */
		public function post_type_created_message( $created = false ) {
			global $post_id;

			$title = get_the_title( $post_id );

			/* translators: %s post type name */
			$item_saved_text = sprintf( __( '%s post type updated', 'secure-custom-fields' ), $title );

			if ( $created ) {
				/* translators: %s post type name */
				$item_saved_text = sprintf( __( '%s post type created', 'secure-custom-fields' ), $title );
			}

			$add_fields_link = wp_nonce_url(
				admin_url( 'post-new.php?post_type=acf-field-group&use_post_type=' . $post_id ),
				'add-fields-' . $post_id
			);

			$create_post_type_link    = admin_url( 'post-new.php?post_type=acf-post-type' );
			$duplicate_post_type_link = wp_nonce_url(
				admin_url( 'post-new.php?post_type=acf-post-type&use_post_type=' . $post_id ),
				'acfduplicate-' . $post_id
			);

			$create_taxonomy_link = wp_nonce_url(
				admin_url( 'post-new.php?post_type=acf-taxonomy&use_post_type=' . $post_id ),
				'create-taxonomy-' . $post_id
			);

			ob_start(); ?>
			<p class="acf-item-saved-text"><?php echo esc_html( $item_saved_text ); ?></p>
			<div class="acf-item-saved-links">
				<a href="<?php echo esc_url( $add_fields_link ); ?>"><?php esc_html_e( 'Add fields', 'secure-custom-fields' ); ?></a>
				<a class="acf-link-field-groups" href="#"><?php esc_html_e( 'Link field groups', 'secure-custom-fields' ); ?></a>
				<a href="<?php echo esc_url( $create_post_type_link ); ?>"><?php esc_html_e( 'Create post type', 'secure-custom-fields' ); ?></a>
				<a href="<?php echo esc_url( $duplicate_post_type_link ); ?>"><?php esc_html_e( 'Duplicate post type', 'secure-custom-fields' ); ?></a>
				<a href="<?php echo esc_url( $create_taxonomy_link ); ?>"><?php esc_html_e( 'Create taxonomy', 'secure-custom-fields' ); ?></a>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Enqueues any scripts necessary for internal post type.
		 *
		 * @since ACF 5.0.0
		 */
		public function admin_enqueue_scripts() {

			wp_enqueue_style( 'acf-field-group' );

			acf_localize_text(
				array(
					'Post'    => __( 'Post', 'secure-custom-fields' ),
					'Posts'   => __( 'Posts', 'secure-custom-fields' ),
					'Page'    => __( 'Page', 'secure-custom-fields' ),
					'Pages'   => __( 'Pages', 'secure-custom-fields' ),
					'Default' => __( 'Default', 'secure-custom-fields' ),
				)
			);

			parent::admin_enqueue_scripts();

			do_action( 'acf/post_type/admin_enqueue_scripts' );
		}

		/**
		 * Sets up all functionality for the post type edit page to work.
		 *
		 * @since ACF 3.1.8
		 */
		public function admin_head() {

			// global.
			global $post, $acf_post_type;

			// set global var.
			$acf_post_type = acf_get_internal_post_type( $post->ID, $this->post_type );

			if ( ! empty( $acf_post_type['not_registered'] ) ) {
				acf_add_admin_notice(
					__( 'This post type could not be registered because its key is in use by another post type registered by another plugin or theme.', 'secure-custom-fields' ),
					'error'
				);
			}

			// metaboxes.
			add_meta_box( 'acf-basic-settings', __( 'Basic Settings', 'secure-custom-fields' ), array( $this, 'mb_basic_settings' ), 'acf-post-type', 'normal', 'high' );
			add_meta_box( 'acf-advanced-settings', __( 'Advanced Settings', 'secure-custom-fields' ), array( $this, 'mb_advanced_settings' ), 'acf-post-type', 'normal', 'high' );

			// actions.
			add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ), 10, 0 );
			add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ), 10, 0 );

			// filters.
			add_filter( 'screen_settings', array( $this, 'screen_settings' ), 10, 1 );
			add_filter( 'get_user_option_screen_layout_acf-post-type', array( $this, 'screen_layout' ), 10, 1 );
			add_filter( 'get_user_option_metaboxhidden_acf-post-type', array( $this, 'force_basic_settings' ), 10, 1 );
			add_filter( 'get_user_option_closedpostboxes_acf-post-type', array( $this, 'force_basic_settings' ), 10, 1 );
			add_filter( 'get_user_option_closedpostboxes_acf-post-type', array( $this, 'force_advanced_settings' ), 10, 1 );

			// 3rd party hook.
			do_action( 'acf/post_type/admin_head' );
		}

		/**
		 * This action will allow ACF to render metaboxes after the title.
		 */
		public function edit_form_after_title() {

			// globals.
			global $post;

			// render post data.
			acf_form_data(
				array(
					'screen'        => 'post_type',
					'post_id'       => $post->ID,
					'delete_fields' => 0,
					'validation'    => 1,
				)
			);
		}

		/**
		 * This function will add extra HTML to the acf form data element
		 *
		 * @since   ACF 5.3.8
		 *
		 * @param array $args Arguments array to pass through to action.
		 * @return void
		 */
		public function form_data( $args ) {
			do_action( 'acf/post_type/form_data', $args );
		}

		/**
		 * This function will append extra l10n strings to the acf JS object
		 *
		 * @since   ACF 5.3.8
		 *
		 * @param array $l10n The array of translated strings.
		 * @return array $l10n
		 */
		public function admin_l10n( $l10n ) {
			return apply_filters( 'acf/post_type/admin_l10n', $l10n );
		}

		/**
		 * Admin footer third party hook support
		 *
		 * @since ACF 5.3.2
		 */
		public function admin_footer() {
			do_action( 'acf/post_type/admin_footer' );
		}

		/**
		 * Screen settings html output
		 *
		 * @since   ACF 3.6.0
		 *
		 * @param string $html Current screen settings HTML.
		 * @return string $html
		 */
		public function screen_settings( $html ) {
			return $html;
		}

		/**
		 * Sets the "Edit Post Type" screen to use a one-column layout.
		 *
		 * @param integer $columns Number of columns for layout.
		 * @return integer
		 */
		public function screen_layout( $columns = 0 ) {
			return 1;
		}

		/**
		 * Force basic settings to always be visible
		 *
		 * @param  array $hidden_metaboxes The metaboxes hidden on this page.
		 * @return array
		 */
		public function force_basic_settings( $hidden_metaboxes ) {
			if ( ! is_array( $hidden_metaboxes ) ) {
				return $hidden_metaboxes;
			}
			return array_diff( $hidden_metaboxes, array( 'acf-basic-settings' ) );
		}

		/**
		 * Force advanced settings to be visible
		 *
		 * @param array $hidden_metaboxes The metaboxes hidden on this page.
		 * @return array
		 */
		public function force_advanced_settings( $hidden_metaboxes ) {
			if ( ! is_array( $hidden_metaboxes ) ) {
				return $hidden_metaboxes;
			}
			return array_diff( $hidden_metaboxes, array( 'acf-advanced-settings' ) );
		}

		/**
		 * This function will customize the publish metabox
		 *
		 * @since ACF 5.2.9
		 */
		public function post_submitbox_misc_actions() {
			global $acf_post_type;
			$status_label = $acf_post_type['active'] ? _x( 'Active', 'post status', 'secure-custom-fields' ) : _x( 'Inactive', 'post status', 'secure-custom-fields' );

			?>
			<script type="text/javascript">
				(function($) {
					$('#post-status-display').html( '<?php echo esc_html( $status_label ); ?>' );
				})(jQuery);
			</script>
			<?php
		}

		/**
		 * Saves post type data.
		 *
		 * @since ACF 1.0.0
		 *
		 * @param  integer $post_id The post ID.
		 * @param  WP_Post $post    The post object.
		 * @return integer $post_id
		 */
		public function save_post( $post_id, $post ) {
			if ( ! $this->verify_save_post( $post_id, $post ) ) {
				return $post_id;
			}

			// Disable filters to ensure ACF loads raw data from DB.
			acf_disable_filters();

			// phpcs:disable WordPress.Security.NonceVerification.Missing -- Validated in $this->verify_save_post() above.
			// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized when saved.
			$_POST['acf_post_type']['ID']    = $post_id;
			$_POST['acf_post_type']['title'] = isset( $_POST['acf_post_type']['labels']['name'] ) ? $_POST['acf_post_type']['labels']['name'] : '';

			// Save the post type.
			acf_update_internal_post_type( $_POST['acf_post_type'], $this->post_type ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Validated in verify_save_post
			// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			// phpcs:enable WordPress.Security.NonceVerification.Missing

			return $post_id;
		}

		/**
		 * Renders HTML for the basic settings metabox.
		 *
		 * @since ACF 5.0.0
		 */
		public function mb_basic_settings() {
			global $acf_post_type;

			if ( ! acf_is_internal_post_type_key( $acf_post_type['key'], 'acf-post-type' ) ) {
				$acf_post_type['key'] = uniqid( 'post_type_' );
			}

			acf_get_view( $this->post_type . '/basic-settings' );
		}


		/**
		 * Renders the HTML for the advanced settings metabox.
		 *
		 * @since ACF 5.0.0
		 */
		public function mb_advanced_settings() {
			acf_get_view( $this->post_type . '/advanced-settings' );
		}
	}

	new ACF_Admin_Post_Type();
endif; // Class exists check.

?>
