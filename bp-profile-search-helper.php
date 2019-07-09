<?php
/**
 * Thanks to Andrea <http://dontdream.it/>
 * The plugin is now compatible with BP Profile Search
 */
// Don't allow direct access over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

// If Our Xprofile member type field plugin is already active, do not load this class.
if ( ! class_exists( 'BD_Xprofile_Member_Type_Field_Search_Helper' ) ) :

	/**
	 * Thanks to Andrea <http://dontdream.it/>
	 * The plugin is now compatible with BP Profile Search
	 */
	class BD_Xprofile_Member_Type_Field_Search_Helper {

		/**
		 * Singleton instance
		 *
		 * @var BD_Xprofile_Member_Type_Field_Search_Helper
		 */
		private static $instance;

		/**
		 * Constructor
		 */
		private function __construct() {
			$this->setup();
		}

		/**
		 * Get singleton instance
		 *
		 * @return BD_Xprofile_Member_Type_Field_Search_Helper
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Setup hooks.
		 */
		private function setup() {
			add_filter( 'bps_custom_field', array( $this, 'register_field' ) );
		}

		/**
		 * Register field for Search form.
		 *
		 * @param stdClass $field field object.
		 */
		public function register_field( $field ) {

			if ( 'membertype' !== $field->type && 'membertypes' !== $field->type ) {
				return;
			}

			$field->format = 'text';
			// callback.
			$field->search = array( $this, 'search' );
			// options.
			$field->options = array();

			$registered_member_types = bp_get_member_types( null, 'object' );

			foreach ( $registered_member_types as $type_name => $member_type_object ) {
				$field->options[ $type_name ] = $member_type_object->labels['singular_name'];
			}
		}

		/**
		 * Get IDS for the member type.
		 *
		 * @param Object $field field.
		 *
		 * @return array
		 */
		public function search( $field ) {
			$value = is_array( $field->value ) ? array_filter( array_map( 'trim', $field->value ) ) : trim( $field->value );

			if ( empty( $value ) ) {
				return array();
			}

			$search_type = $field->filter;

			$args = array( 'populate_extras' => false );

			if ( 'one_of' === $search_type ) {
				$args['member_type__in'] = $value;
			} else {
				$args['member_type'] = $value;
			}

			// in all other cases.
			add_filter( 'bp_wp_user_query_args', array( $this, 'limit_to_ids' ) );
			$user_query = new BP_User_Query( $args );
			remove_filter( 'bp_wp_user_query_args', array( $this, 'limit_to_ids' ) );

			return $user_query->user_ids;
		}

		/**
		 * Do not let BuddyPress Query for all fields.
		 *
		 * @param array $args args.
		 *
		 * @return array
		 */
		public function limit_to_ids( $args ) {
			$args['fields'] = array( 'ID' ); // sorry, specifying simply 'ID' break BP Query.
			return $args;
		}

	}

// initialize.
	BD_Xprofile_Member_Type_Field_Search_Helper::get_instance();
endif;
