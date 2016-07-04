<?php

/**
 * Plugin Name: BuddyPress Xprofile Member Type Field
 * Plugin URI: http://buddydev.com/plugins/bp-xprofile-member-type-field/
 * Version: 1.0.4
 * Author: BuddyDev.Com
 * Author URI: http://buddydev.com
 * Description: Allow site admins to use member type as xprofile field. It will update the member type of the user when they update their profile field
 */


class BD_Xprofile_Member_Type_Field_Helper {
    
    /**
     *
     * @var BD_Xprofile_Member_Type_Field_Helper
     */
    private static $instance;
    /**
     * Path to this plugin directory
     * @var string 
     */
    private $path = '';
    
    /**
     * The url to this plugin directory
     * @var string url 
     */
    private $url = '';
    
	private $shown_fields = array();// hack to make it work with older version of themes
	private $field_types	= array();
	
    private function __construct() {
        
        $this->path = plugin_dir_path( __FILE__ ); //with trailing slash
        $this->url  = plugin_dir_url( __FILE__ ); //with trailing slash
        
        add_action( 'bp_loaded', array( $this, 'load' ) );
        
        //add_action( 'admin_print_scripts', array( $this, 'load_admin_js' ) );
		
		add_filter( 'bp_xprofile_get_field_types', array( $this, 'add_field_types' ) );
		//for old theme compat
		add_action( 'bp_custom_profile_edit_fields_pre_visibility', array( $this, 'may_be_show_field' ) );
		
		//update member type when field is updated
		
		add_action( 'xprofile_data_after_save', array( $this, 'update_member_type' ) );
	    //sync field on member type update
		add_action( 'bp_set_member_type', array( $this, 'update_field_data' ), 10, 3 );
		//remove membertype dropdown from Admin->Users->Edit Profile->Sidebar
		add_action( 'bp_members_admin_user_metaboxes', array( $this, 'remove_membertype_metabox' ) );

    }
    
    /**
     * 
     * @return BD_Xprofile_Member_Type_Field_Helper
     */
    public static function get_instance() {
        
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
		}	
        
        return self::$instance;
    }
    
    public function load() {
        
        
        $files = array(
			'fields/class-member-type-field.php',
			'bp-profile-search-helper.php',
        );
        
        foreach ( $files as $file ) {
            require_once $this->path . $file;
		}
        
    }
    
	public function add_field_types( $filed_types ) {
		
		//you may be wondering why I am using array instead of $filed_types['membertype'] = 'class name'. Just for future updates to add more field types
		$our_field_types = array(
			
			'membertype'	=> 'BD_XProfile_Field_Type_MemberType',
			
			);
		//store our list in the $this->field_types array
		$this->field_types = array_keys( $our_field_types );
		
		return array_merge( $filed_types, $our_field_types );
	}
	/**
	 * Mark the field as shown
	 * 
	 * @param type $field_id
	 */
	public function set_shown( $field_id ) {
		
		$this->shown_fields['field_' . $field_id ] = true;
	}
	/**
	 * Check if the given field was shown
	 * 
	 * @param type $field_id
	 * @return type
	 */
	public function was_shown( $field_id ) {
		
		return isset( $this->shown_fields['field_' . $field_id ] );
	}
	//a work around for the themes that does not support newer hook
	public function may_be_show_field( ) {
		
		$field_id = bp_get_the_profile_field_id();
		
		if ( ! $this->was_shown( $field_id ) && in_array( bp_get_the_profile_field_type(), $this->field_types ) ) {
			
			$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
			$field_type->edit_field_html();
		}

	}
	

	/**
	 * Update the member type of a user when member type field is updated
	 * 
	 * @param type $data_field
	 * @return type
	 */
	public function update_member_type( $data_field ) {
	
		$field = xprofile_get_field( $data_field->field_id ) ;

		//we only need to worry about member type field

		if ( $field->type != 'membertype' ) {
			return ;
		}

		$user_id = $data_field->user_id;
		$member_type = maybe_unserialize( $data_field->value );
		
		//validate too?
		if ( empty( $member_type ) ) {
			
			//remove all member type?
			bp_set_member_type( $user_id, '' );
			return ;
		}
		//should we validate member type here? I don't think as only validated data will be passed here
		bp_set_member_type( $user_id, $member_type );
	}

	public function update_field_data(  $user_id, $member_type, $append ) {
		global $wpdb;
		//if the account is being deleted, there is no need to syc the fields
		//it will also help us break the cyclic dependency(infinite loop)
		if ( did_action( 'delete_user') || did_action( 'wpmu_delete_user' ) ) {
			return ;
		}

		$fields = $this->get_membertype_fields();

		if ( empty( $fields ) ) {
			return ;
		}

		$list = '('.join( ',', $fields ) .')';

		$table = buddypress()->profile->table_name_data;

		$query = $wpdb->prepare( "SELECT field_id, value FROM {$table} WHERE field_id IN {$list} AND user_id = %d", $user_id );

		$data_fields = $wpdb->get_results( $query );

		foreach ( $data_fields as $row ) {
			if ( $row->value == $member_type ) {
				continue;
				//no need to update
			}
			//if we are here, sync it
			xprofile_set_field_data( $row->field_id, $user_id, $member_type );
		}

	}

	public function get_membertype_fields() {
		global $wpdb;
		$table =  buddypress()->profile->table_name_fields;

		$query = "SELECT id FROM {$table} WHERE type = %s";
		$fields_ids = $wpdb->get_col( $wpdb->prepare( $query, 'membertype' ) );

		return $fields_ids;
	}
	
	public function remove_membertype_metabox() {
		remove_meta_box( 'bp_members_admin_member_type', get_current_screen()->id,  'side' );
	}

}

bp_xprofile_member_type_field_helper();
/**
 * 
 * @return BD_Xprofile_Member_Type_Field_Helper
 */
function bp_xprofile_member_type_field_helper() {
	
	return BD_Xprofile_Member_Type_Field_Helper::get_instance();
}
