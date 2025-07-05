<?php 

class WisamPostTypes{

public function __construct() {
		add_action('init', array($this, 'register_story_post_type'));
	}

	public function register_story_post_type() {
		$labels = array(
			'name'                     => 'Story',
			'singular_name'            => 'Story',
			'menu_name'                => 'Story',
			'all_items'                => 'All Stories',
			'edit_item'                => 'Edit Story',
			'view_item'                => 'View Story',
			'view_items'               => 'View Story',
			'add_new_item'             => 'Add New Story',
			'add_new'                  => 'Add New Story',
			'new_item'                 => 'New Story',
			'parent_item_colon'        => 'Parent Story:',
			'search_items'             => 'Search Story',
			'not_found'                => 'No story found',
			'not_found_in_trash'       => 'No story found in Trash',
			'archives'                 => 'Story Archives',
			'attributes'               => 'Story Attributes',
			'insert_into_item'         => 'Insert into story',
			'uploaded_to_this_item'    => 'Uploaded to this story',
			'filter_items_list'        => 'Filter story list',
			'filter_by_date'           => 'Filter story by date',
			'items_list_navigation'    => 'Story list navigation',
			'items_list'               => 'Story list',
			'item_published'           => 'Story published.',
			'item_published_privately' => 'Story published privately.',
			'item_reverted_to_draft'   => 'Story reverted to draft.',
			'item_scheduled'           => 'Story scheduled.',
			'item_updated'             => 'Story updated.',
			'item_link'                => 'Story Link',
			'item_link_description'    => 'A link to a story.',
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'show_in_rest'       => true,
			'menu_position'      => 1,
			'menu_icon'          => 'dashicons-book',
			'supports'           => array(
				'title',
				'author',
				'comments',
				'excerpt',
				'revisions',
				'thumbnail',
				'custom-fields',
			),
			'taxonomies'         => array('category', 'post_tag'),
			'delete_with_user'   => false,
		);

		register_post_type('story', $args);
	} 


}