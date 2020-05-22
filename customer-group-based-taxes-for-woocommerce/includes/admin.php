<?php
/**
 * @since             1.0.0
 * @package           Customer_Group_Based_Taxes_For_Woocommerce/includes
 *
 */ 

// Prevent direct file access
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
	* @since 1.0.0
	* Create custom post type wc-customer-group
	*/ 
if ( !function_exists( 'micgbtfw_customer_group_meta_box' ) ) {

	function micgbtfw_create_posttype_customer_group() {
	  register_post_type( 'wc-customer-group',
	    array(
	      'labels' => array(
	        'name'               => _x( 'Customer Groups', 'Post Type General Name', 'customer-group-based-taxes-for-woocommerce' ),
	        'singular_name'      => _x( 'Customer Group', 'Post Type Singular Name', 'customer-group-based-taxes-for-woocommerce' ),
	        'menu_name'          => __( 'Customer Groups', 'customer-group-based-taxes-for-woocommerce' ),
	        'all_items'          => __( 'All Groups', 'customer-group-based-taxes-for-woocommerce' ),
	        'view_item'          => __( 'View Group', 'customer-group-based-taxes-for-woocommerce' ),
	        'add_new_item'       => __( 'Add New Group', 'customer-group-based-taxes-for-woocommerce' ),
	        'add_new'            => __( 'Add New', 'customer-group-based-taxes-for-woocommerce' ),
	        'edit_item'          => __( 'Edit Group', 'customer-group-based-taxes-for-woocommerce' ),
	        'update_item'        => __( 'Update Group', 'customer-group-based-taxes-for-woocommerce' ),
	        'search_items'       => __( 'Search Groups', 'customer-group-based-taxes-for-woocommerce' ),
	        'not_found'          => __( 'No groups found.', 'customer-group-based-taxes-for-woocommerce' ),
	        'not_found_in_trash' => __( 'No groups found in trash.', 'customer-group-based-taxes-for-woocommerce' ),
	      ),
	      'menu_icon'          => 'dashicons-groups',
	      'public' 							=> false,
	      'has_archive'				 	=> false,
	      'supports'            => array( 'title' ),
	      'rewrite' 						=> false,
	      'exclude_from_search' => true,
				'publicly_queryable' 	=> false,
				'show_ui' 						=> true,
				'show_in_nav_menus' 	=> false,
				'has_archive' 				=> false,
	    )
	  );
	}
	add_action( 'init', 'micgbtfw_create_posttype_customer_group' );

}

/**
	* @since 1.0.0
	* Create metabox and data
	*/ 
if ( !function_exists( 'micgbtfw_customer_group_meta_box' ) ) {
	
	function micgbtfw_customer_group_meta_box() {
	  add_meta_box(
	    'woocommerce-cg-tax-class',
	    __( 'WooCommerce', 'customer-group-based-taxes-for-woocommerce' ),
	    'micgbtfw_customer_group_meta_box_callback',
	    'wc-customer-group'
	  );
	}
	add_action( 'add_meta_boxes', 'micgbtfw_customer_group_meta_box' );

	// metabox callback function
	function micgbtfw_customer_group_meta_box_callback() {
		global $post;
		// Nonce field to validate form request came from current site
		wp_nonce_field( basename( __FILE__ ), 'wc_tax_field' );
		// Get the tax_class data if it's already been entered
		$tax_class = get_post_meta( $post->ID, 'micgbtfw_tax_class', true );
		$tax_array = WC_Tax::get_tax_classes();
		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					Customer Group Tax Class
				</th>
				<td>
					<select name="micgbtfw_tax_class" id="" style="min-width: 180px;" required="">
						<option value="">Select Class</option>
						<option value="Standard" <?php selected( $tax_class, 'Standard'); ?>>Standard</option>
						<?php foreach ($tax_array as $key => $value) : ?>
						<option value="<?php echo $value; ?>" <?php selected( $tax_class, $value); ?>><?php echo $value; ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description" style="margin-top: 10px;">Please select WooCommerce tax class in this list.</p>
					<p><a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=tax' ); ?>">Click to add custom tax class.</a></p>
				</td>
			</tr>
		</table>
	<?php
	}

}

/**
	* @since 1.0.0
	* Save customer group data from custom post
	*/ 
if ( !function_exists( 'micgbtfw_customer_group_save_data' ) ) {

	function micgbtfw_customer_group_save_data( $post_id, $post ) {
		// Return if the user doesn't have edit permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// because save_post can be triggered at other times.
		if ( ! isset( $_POST['micgbtfw_tax_class'] ) || ! wp_verify_nonce( $_POST['wc_tax_field'], basename(__FILE__) ) ) {
			return $post_id;
		}

		// This sanitizes the data from the field
		$micgbtfw_tax_class_meta['micgbtfw_tax_class'] = esc_textarea( $_POST['micgbtfw_tax_class'] );

		foreach ( $micgbtfw_tax_class_meta as $key => $value ) {

			// Don't store custom data twice
			if ( 'revision' === $post->post_type ) {
				return;
			}

			if ( get_post_meta( $post_id, $key, false ) ) {
				// If the custom field already has a value, update it.
				update_post_meta( $post_id, $key, $value );
			} else {
				// If the custom field doesn't have a value, add it.
				add_post_meta( $post_id, $key, $value);
			}

			if ( ! $value ) {
				// Delete the meta key if there's no value
				delete_post_meta( $post_id, $key );
			}

		}
	}
	add_action( 'save_post', 'micgbtfw_customer_group_save_data', 1, 2 );

}

/**
	* @since 1.0.0
	* Add customer group field in user profile
	*/ 
if ( !function_exists( 'micgbtfw_show_customer_group_fields' ) ) { 

	function micgbtfw_show_customer_group_fields( $user ) { ?>
	  <h3>WooCommerce Customer Group</h3>
	  <table class="form-table">
	    <tr>
	      <th><label for="cutomergroup">Customer Group</label></th>
	      <td>
	        <select name="micgbtfw_cg" id="cutomergroup" >
	          <option value="" <?php selected( '', get_the_author_meta( 'micgbtfw_cg', $user->ID ) ); ?>>Dafault (Standard Tax)</option>
	      		<?php
	      		$query = new WP_Query( array( 'post_type' => 'wc-customer-group', 'post_status' => 'publish', 'posts_per_page' => -1, ) );

	      		if ( $query->have_posts() ) : ?>
	      		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
	          <option value="<?php echo get_the_ID(); ?>" <?php selected( get_the_ID(), get_the_author_meta( 'micgbtfw_cg', $user->ID ) ); ?>><?php the_title(); ?></option>
	      		<?php endwhile; wp_reset_postdata(); ?>
	      		<!-- show pagination here -->
	      		<?php else : ?>
	      		<!-- show 404 error here -->
	      		<?php endif; ?>
	      		?>
	        </select>
	      </td>
	    </tr>
	  </table>
	<?php }
	add_action( 'show_user_profile', 'micgbtfw_show_customer_group_fields' );
	add_action( 'edit_user_profile', 'micgbtfw_show_customer_group_fields' );

}

/**
	* @since 1.0.0
	* Save customer group field in user profile
	*/ 
if ( !function_exists( 'micgbtfw_save_customer_group_fields' ) ) {

	function micgbtfw_save_customer_group_fields( $user_id ) {
	  if ( !current_user_can( 'edit_user', $user_id ) )
	      return false;
	  // update meta
	  update_user_meta( $user_id, 'micgbtfw_cg', $_POST['micgbtfw_cg'] );
	}
	add_action( 'personal_options_update', 'micgbtfw_save_customer_group_fields' );
	add_action( 'edit_user_profile_update', 'micgbtfw_save_customer_group_fields' );

}

/**
	* @since 1.0.0
	* Assign tax as per customer group
	*/ 
if ( !function_exists( 'micgbtfw_customer_group_assign_tax' ) ) {

	function micgbtfw_customer_group_assign_tax( $tax_class, $product ) {
	  $user_id = get_current_user_id();
	  $user_group_id = get_the_author_meta( 'micgbtfw_cg', $user_id );
	  if(!empty($user_group_id)) {
	  	$my_tax_class = get_post_meta( $user_group_id, 'micgbtfw_tax_class' );
			if ( is_user_logged_in() && $my_tax_class[0] != "Standard" ) {
				$tax_class = $my_tax_class[0];
			}
	  }
		return $tax_class;
	}
	add_filter( 'woocommerce_product_tax_class', 'micgbtfw_customer_group_assign_tax', 1, 2 );

}	

/**
	* @since 1.0.0
	* Add Customer Group link to plugin diretory
	*/ 
if ( !function_exists( 'micgbtfw_customer_group' ) ) {	

	function micgbtfw_customer_group( $links ) {
		return array_merge(
			array(
				'css-editor' => '<a href="' . admin_url( 'edit.php?post_type=wc-customer-group' ) . '">' . __( 'Add Customer Group', 'customer-group-based-taxes-for-woocommerce' ) . '</a>',
			),
			$links
		);
	}
	add_filter( 'plugin_action_links_' . plugin_basename( MICGBTFW_PLUGIN_FILE_URL ), 'micgbtfw_customer_group' );

}

