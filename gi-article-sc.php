<?php
   /*
   Plugin Name: GI Topic Article Groups and HTML Shortcodes
   Plugin URI: http://glassinteractive.com
   Description: HTML for easy subtopic creation in posts (gitasc, giplearn & gicode), plus Groups for Posts and Pages.
   Version: 0.9.2
   Change Info: 0.9.2 - Changed name and description to include post and page groups.
   Author: Mike Glass
   Author URI: http://www.glassinteractive.com/wordpress-plugin-jumbotron-3d-post-rotator/
   License: This beta version is free for private use on a personal use, not for commercial use or distribution of any sort. For more information, v=contact
   */
?>
<?php
function gitasc_load_plugin_files () {
    
    $plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'gi_asc_style', $plugin_url . 'gi_asc_style.css' );
}

add_action( 'wp_enqueue_scripts', 'gitasc_load_plugin_files' );



function gi_add_editor_style( $mce_css ){

    $mce_css .= ', ' . plugins_url( 'giasc-editor-style.css', __FILE__ );
    return $mce_css;
}
add_filter( 'mce_css', 'gi_add_editor_style' );



function add_style_select_buttons( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}
add_filter( 'mce_buttons_2', 'add_style_select_buttons' );




//  Add post & page template
function wpse255804_add_page_template ($templates) {
    $templates['gi_parent_article.php'] = 'My Template';
    return $templates;
    }
add_filter ('theme_page_templates', 'wpse255804_add_page_template');
add_filter ('theme_post_templates', 'wpse255804_add_page_template');

function wpse255804_redirect_page_template ($template) {
    if ('gi_parent_article.php' == basename ($template))
        $template = WP_PLUGIN_DIR . '/mypluginname/gi_parent_article.php';
    return $template;
    }
add_filter ('page_template', 'wpse255804_redirect_page_template');
add_filter ('post_template', 'wpse255804_redirect_page_template');



/*
* Callback function to filter the MCE settings
*/
function my_mce_before_init_insert_formats( $init_array ) {  

// Define the style_formats array

	$style_formats = array(  
/*
* Each array child is a format with it's own settings
* Notice that each array has title, block, classes, and wrapper arguments
* Title is the label which will be visible in Formats menu
* Block defines whether it is a span, div, selector, or inline style
* Classes allows you to define CSS classes
* Wrapper whether or not to add a new block-level element around any selected elements
*/
		array(  
			'title' => 'Keyword',  
			'inline' => 'span',  
			'classes' => 'gi-keyword',
			'wrapper' => false,
			
		),  
		array(  
			'title' => 'Code JS',  
			'block' => 'code',  
			'classes' => 'language-javascript',
			'wrapper' => true,
		),
		array(  
			'title' => 'Anchor - No Link',  
			'inline' => 'a',  
			'classes' => 'page-anchor',
			'attributes'   => array('id' => '---add-name---here', 'data' => 'data-value'),
			'wrapper' => true,
		),
		array(  
			'title' => 'Code JS Inline',  
			'inline' => 'code',  
			'classes' => 'code-js-inline',
			'wrapper' => false,
		),		
		array(  
			'title' => 'Code PHP Inline',  
			'inline' => 'code',  
			'classes' => 'code-php-inline',
			'wrapper' => false,
		),
		array(  
			'title' => 'Code html Inline',  
			'inline' => 'code',  
			'classes' => 'code-html-inline',
			'wrapper' => false,
		),
		array(  
			'title' => 'Code CSS Inline',  
			'inline' => 'code',  
			'classes' => 'code-css-inline',
			'wrapper' => false,
		),
	);  
	// Insert the array, JSON ENCODED, into 'style_formats'
	$init_array['style_formats'] = json_encode( $style_formats );  
	
	return $init_array;  
  
} 
// Attach callback to 'tiny_mce_before_init' 
add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' ); 



// Setup custom taxonomy for grouping parent articles
//hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_groups_hierarchical_taxonomy', 0 );
 
//create a custom taxonomy name it topics for your posts
 
function create_groups_hierarchical_taxonomy() {
 
// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI
 
  $labels = array(
    'name' => _x( 'Groups', 'taxonomy general name' ),
    'singular_name' => _x( 'Group', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Subjects' ),
    'all_items' => __( 'All Groups' ),
    'parent_item' => __( 'Parent Group' ),
    'parent_item_colon' => __( 'Parent Group:' ),
    'edit_item' => __( 'Edit Group' ), 
    'update_item' => __( 'Update Group' ),
    'add_new_item' => __( 'Add New Group' ),
    'new_item_name' => __( 'New Group Name' ),
    'menu_name' => __( 'Groups' ),
  );    
 
// Now register the taxonomy
 
  register_taxonomy('groups',array('post', 'page'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'group' ),
  ));
 
}

// Custom taxonomy fields
add_action( 'groups_add_form_fields', 'add_group_order_field', 10, 2 );
function add_group_order_field($taxonomy) {
    global $group_orders;
    ?><div class="form-field term-group">
        <label for="featuret-group"><?php _e('Feature Group', 'gi_atsc'); ?></label>
        <select class="postform" id="equipment-group" name="group-order">
            <option value="-1"><?php _e('none', 'gi_atsc'); ?></option><?php foreach ($group_orders as $_group_key => $_group) : ?>
                <option value="<?php echo $_group_key; ?>" class=""><?php echo $_group; ?></option>
            <?php endforeach; ?>
        </select>
    </div><?php
}

add_action( 'created_groups', 'save_feature_meta', 10, 2 );


$group_orders = array(
    '1' => __('1', 'gi_atsc'),
    '2' => __('2', 'gi_atsc'),
    '3' => __('3', 'gi_atsc'),
    '4' => __('4', 'gi_atsc'),
    '5' => __('5', 'gi_atsc'),
    '6' => __('6', 'gi_atsc'),
    '7' => __('7', 'gi_atsc'),
    '8' => __('8', 'gi_atsc'),
    '9' => __('9', 'gi_atsc'),
    '10' => __('10', 'gi_atsc'),
    '11' => __('11', 'gi_atsc'),
    '12' => __('12', 'gi_atsc'),
    '13' => __('13', 'gi_atsc'),
    '14' => __('14', 'gi_atsc'),
    '15' => __('15', 'gi_atsc')
);


function save_feature_meta( $term_id, $tt_id ){
    if( isset( $_POST['group-order'] ) && ’ !== $_POST['group-order'] ){
        $group = sanitize_title( $_POST['group-order'] );
        add_term_meta( $term_id, 'group-order', $group, true );
    }
}

add_action( 'groups_edit_form_fields', 'edit_group_order_field', 10, 2 );

function edit_group_order_field( $term, $taxonomy ){

    global $group_orders;

    // get current group
    $group_order = get_term_meta( $term->term_id, 'group-order', true );

    ?><tr class="form-field term-group-wrap">
        <th scope="row"><label for="group-order"><?php _e( 'Feature Group', 'gi_atsc' ); ?></label></th>
        <td><select class="postform" id="group-order" name="group-order">
            <option value="-1"><?php _e( 'none', 'gi_atsc' ); ?></option>
            <?php foreach( $group_orders as $_group_key => $_group ) : ?>
                <option value="<?php echo $_group_key; ?>" <?php selected( $group_order, $_group_key ); ?>><?php echo $_group; ?></option>
            <?php endforeach; ?>
        </select></td>
    </tr><?php
}

add_action( 'edited_groups', 'update_feature_meta', 10, 2 );

function update_feature_meta( $term_id, $tt_id ){

    if( isset( $_POST['group-order'] ) && ’ !== $_POST['group-order'] ){
        $group = sanitize_title( $_POST['group-order'] );
        update_term_meta( $term_id, 'group-order', $group );
    }
}

add_filter('manage_edit-groups_columns', 'add_group_order_column' );

function add_group_order_column( $columns ){
    $columns['group_order'] = __( 'Order', 'gi_atsc' );
    return $columns;
}

add_filter('manage_groups_custom_column', 'add_group_order_column_content', 10, 3 );

function add_group_order_column_content( $content, $column_name, $term_id ){
    global $group_orders;

    if( $column_name !== 'group_order' ){
        return $content;
    }

    $term_id = absint( $term_id );
    $group_order = get_term_meta( $term_id, 'group-order', true );

    if( !empty( $group_order ) ){
        $content .= esc_attr( $group_orders[ $group_order ] );
    }

    return $content;
}

add_filter( 'manage_edit-groups_sortable_columns', 'add_group_order_column_sortable' );

function add_group_order_column_sortable( $sortable ){
    $sortable[ 'group_order' ] = 'group_order';
    return $sortable;
}



//////////////////////////////////////////////////////////
// Setup custom taxonomy for ordering posts within groups
///////////////////////////////////////////////////////////

add_action( 'init', 'create_positions_hierarchical_taxonomy', 0 );
 
//create a custom taxonomy name it topics for your posts
 
function create_positions_hierarchical_taxonomy() {
 
// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI
 
  $labels = array(
    'name' => _x( 'Positions', 'taxonomy general name' ),
    'singular_name' => _x( 'Position', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Positions' ),
    'all_items' => __( 'All Positions' ),
    'parent_item' => __( 'Parent Position' ),
    'parent_item_colon' => __( 'Parent Position:' ),
    'edit_item' => __( 'Edit Position' ), 
    'update_item' => __( 'Update Position' ),
    'add_new_item' => __( 'Add New Position' ),
    'new_item_name' => __( 'New Position Name' ),
    'menu_name' => __( 'Positions' ),
  );    
 
// Now register the taxonomy
 
  register_taxonomy('positions',array('post', 'page'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'position' ),
  ));
 
}

// Custom taxonomy fields
add_action( 'positions_add_form_fields', 'add_position_order_field', 10, 2 );
function add_position_order_field($taxonomy) {
    global $position_orders;
    ?><div class="form-field term-position">
        <label for="positiond-position"><?php _e('Position Value', 'gi_atsc'); ?></label>
        <select class="postform" id="equipment-position" name="position-order">
            <option value="-1"><?php _e('none', 'gi_atsc'); ?></option><?php foreach ($position_orders as $_position_key => $_position) : ?>
                <option value="<?php echo $_position_key; ?>" class=""><?php echo $_position; ?></option>
            <?php endforeach; ?>
        </select>
    </div><?php
}

add_action( 'created_positions', 'save_position_meta', 10, 2 );


$position_orders = array(
    '1' => __('1', 'gi_atsc'),
    '2' => __('2', 'gi_atsc'),
    '3' => __('3', 'gi_atsc'),
    '4' => __('4', 'gi_atsc'),
    '5' => __('5', 'gi_atsc'),
    '6' => __('6', 'gi_atsc'),
    '7' => __('7', 'gi_atsc'),
    '8' => __('8', 'gi_atsc'),
    '9' => __('9', 'gi_atsc'),
    '10' => __('10', 'gi_atsc'),
    '11' => __('11', 'gi_atsc'),
    '12' => __('12', 'gi_atsc'),
    '13' => __('13', 'gi_atsc'),
    '14' => __('14', 'gi_atsc'),
    '15' => __('15', 'gi_atsc')
);


function save_position_meta( $term_id, $tt_id ){
    if( isset( $_POST['position-order'] ) && ’ !== $_POST['position-order'] ){
        $position = sanitize_title( $_POST['position-order'] );
        add_term_meta( $term_id, 'position-order', $position, true );
    }
}

add_action( 'positions_edit_form_fields', 'edit_position_order_field', 10, 2 );

function edit_position_order_field( $term, $taxonomy ){

    global $position_orders;

    // get current position
    $position_order = get_term_meta( $term->term_id, 'position-order', true );

    ?><tr class="form-field term-position-wrap">
        <th scope="row"><label for="position-order"><?php _e( 'Position Value', 'gi_atsc' ); ?></label></th>
        <td><select class="postform" id="position-order" name="position-order">
            <option value="-1"><?php _e( 'none', 'gi_atsc' ); ?></option>
            <?php foreach( $position_orders as $_position_key => $_position ) : ?>
                <option value="<?php echo $_position_key; ?>" <?php selected( $position_order, $_position_key ); ?>><?php echo $_position; ?></option>
            <?php endforeach; ?>
        </select></td>
    </tr><?php
}

add_action( 'edited_positions', 'update_position_meta', 10, 2 );

function update_position_meta( $term_id, $tt_id ){

    if( isset( $_POST['position-order'] ) && ’ !== $_POST['position-order'] ){
        $position = sanitize_title( $_POST['position-order'] );
        update_term_meta( $term_id, 'position-order', $position );
    }
}

add_filter('manage_edit-positions_columns', 'add_position_order_column' );

function add_position_order_column( $columns ){
    $columns['position_order'] = __( 'Order', 'gi_atsc' );
    return $columns;
}

add_filter('manage_positions_custom_column', 'add_position_order_column_content', 10, 3 );

function add_position_order_column_content( $content, $column_name, $term_id ){
    global $position_orders;

    if( $column_name !== 'position_order' ){
        return $content;
    }

    $term_id = absint( $term_id );
    $position_order = get_term_meta( $term_id, 'position-order', true );

    if( !empty( $position_order ) ){
        $content .= esc_attr( $position_orders[ $position_order ] );
    }

    return $content;
}

add_filter( 'manage_edit-positions_sortable_columns', 'add_position_order_column_sortable' );

function add_position_order_column_sortable( $sortable ){
    $sortable[ 'group_order' ] = 'group_order';
    return $sortable;
}


// /////////////////////////////////
// function wpse10691_alter_query( $query )
// {
   
//         // $query->set( 'orderby', 'modified' );
//         $query->set( 'order', 'ASC' );
//         $query->set( 'tax_query', 'positions' );
    
 
        

// }
// add_action( 'pre_get_posts', 'wpse10691_alter_query' );


////////////////////////



//////////////////////////////////////////////////
//                   Shortcode
//////////////////////////////////////////////////
function gitasc_shortcode($atts, $content = null) {
    
     // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
 
    // override default attributes with user attributes
    $gitasc_atts = shortcode_atts([
        'id'    =>  $atts['id'],
        'title' =>  $atts['title'],
        'class' =>  $atts['class'],
        ], $atts, $tag);
        
    if(empty($gitasc_atts['id'])) {
        $gitasc_atts['id'] = md5(uniqid(rand(), true)); 
    }
   $article_id = $gitasc_atts['id'];          

    if( strpos($gitasc_atts['class'], 'learn') !== false)  {
        $gitasc_atts['class'] = $gitasc_atts['class'].' learn-mode'; 
    }     
    
   if( !empty($gitasc_atts['title']) )  {
        $the_title = '<h2 class="article-title">'.$gitasc_atts['title'].'</h2>'; 
    }  
    
    $output = '
        <article id="'.$article_id.'" class="topic-article '.$gitasc_atts['class'].'">'.$the_title.'<div class="content"><div class="article-body">'.do_shortcode($content).'</div></div>
        </article>';
    
    return $output;

} 



add_shortcode('gitasc', 'gitasc_shortcode');

function gitasc_b_shortcode($atts, $content = null) {
    
     // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
 
    // override default attributes with user attributes
    $gitasc_atts = shortcode_atts([
        'id'    =>  $atts['id'],
        'title' => $atts['title'],
        'class' => $atts['class']
        ], $atts, $tag);

    if(empty($gitasc_atts['id'])) {
        $gitasc_atts['id'] = md5(uniqid(rand(), true)); 
    }
    $article_id = $gitasc_atts['id'];   

    if( strpos($gitasc_atts['class'], 'learn') !== false)  {
        $gitasc_atts['class'] = $gitasc_atts['class'].' learn-mode'; 
    }
    
   if( !empty($gitasc_atts['title']) )  {
        $the_title = '<h2 class="article-title">'.$gitasc_atts['title'].'</h2>';
    }  
    
    $output = '
        <article id="'.$article_id.'" class="topic-article '.$gitasc_atts['class'].'">'.$the_title.'<div class="content"><div class="article-body">'.do_shortcode($content).'</div></div>
        </article>';
    
    return $output;

} 
add_shortcode('gitasc_b', 'gitasc_b_shortcode');

function gitasc_c_shortcode($atts, $content = null) {
    
     // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
 
    // override default attributes with user attributes
    $gitasc_atts = shortcode_atts([
        'id'    =>  $atts['id'],
        'title' => $atts['title'],
        'class' => $atts['class']
        ], $atts, $tag);

    if(empty($gitasc_atts['id'])) {
        $gitasc_atts['id'] = md5(uniqid(rand(), true)); 
    }
   $article_id = $gitasc_atts['id'];   
   
    if( strpos($gitasc_atts['class'], 'learn') !== false)  {
        $gitasc_atts['class'] = $gitasc_atts['class'].' learn-mode'; 
    }
    
    if( !empty($gitasc_atts['title']) )  {
        $the_title = '<h2 class="article-title">'.$gitasc_atts['title'].'</h2>'; 
    }  
    
    $output = '
        <article id="'.$article_id.'" class="topic-article '.$gitasc_atts['class'].'">'.$the_title.'<div class="content"><div class="article-body">'.do_shortcode($content).'</div></div>
        </article>';
    
    return $output;

} 
add_shortcode('gitasc_c', 'gitasc_c_shortcode');


function gis_shortcode($atts, $content = null) {
               
    $output = '<span class="learn learn-mode">'.do_shortcode($content).'</span>';
    
    return $output;

} 

add_shortcode('gis', 'gis_shortcode');


function giplearn_shortcode($atts, $content = null) {
               
    $output = '<p class="learn learn-mode">'.do_shortcode($content).'</p>';
    
    return $output;

} 

add_shortcode('giplearn', 'giplearn_shortcode');


function gicode_shortcode($atts, $content = null) {
    $space =   '<p></p><p></p>'   ;     
    $output = '<div class="topic-code-wrap"><pre><code class="language-javascript" data-language="java"> '.$content.'<p></p><p></p></code></pre></div>';
    
    return $output;

} 

add_shortcode('gicode', 'gicode_shortcode');

function kw_shortcode($atts, $content = null) {
    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
 
    // override default attributes with user attributes
    $gikw_atts = shortcode_atts([
        'class' => $atts['class']
        ], $atts, $tag);
        
     if( strpos($gitasc_atts['class'], 'learn') !== false)  {
        $gitasc_atts['class'] = $gitasc_atts['class'].' learn-mode'; 
    }
    
    $output = '
        <kw class="'.$gikw_atts['class'].'">'.$content.'
        </kw> 
    ';
    
    return $output;

} 

add_shortcode('kw', 'kw_shortcode');
?>
