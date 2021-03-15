<?php 

/**
 * WP News Post Type & Related News Widget
 *
 * @package           WPRelatedNews
 * @author            Joshua Jenks
 * @copyright         2021 Joshua Jennks
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       wp-related-news
 * Plugin URI:        https://newfang.digital/wp-related-news
 * Description:       This plugin is an creates a news item post type and a shortcode to display 5 recent posts.
 * Version:           1.0.0
 **/

class WPRelatedNews {
	public function __construct()
	{
		add_action( 'init', array( $this, 'create_news_post_type' ) );
		add_shortcode('related_news', array($this, 'displayNewsItems'));
	}


	public function displayNewsItems()
	{

		// extract the attributes into variables
    	extract(shortcode_atts(array(
        	'num_items' => 5,
    	), $atts));

		// Capturing current post data
    	$post_id = get_the_ID();
    	$cat_ids = array();
    	$categories = get_the_category( $post_id );

	    if(!empty($categories) && !is_wp_error($categories)):
	        foreach ($categories as $category):
	            array_push($cat_ids, $category->term_id);
	        endforeach;
    	endif;

	    $news_post_type = 'yoko_news';

	    // Custom WP query relatednews in category of current post
	    $query_args = array( 
	        'category__in'   => $cat_ids,
	        'post_type'      => $news_post_type,
	        'post__not_in'    => array($post_id),
	        'posts_per_page'  => $atts['num_items'],
	     );

	    $related_news = new WP_Query( $query_args );


	    if($related_news->have_posts()):
	         while($related_news->have_posts()): $related_news->the_post(); ?>
	            <ul>
	                <li>
	                    <a href="<?php the_permalink(); ?>">
	                        <?php the_title(); ?>
	                    </a>
	                    <?php the_content(); ?>
	                </li>
	            </ul>
	        <?php endwhile;

	        // Restore original Post Data
	        wp_reset_postdata();
	     endif;

	}

    function create_news_post_type() {

        $name = 'News';
        $singular_name = 'News';
        register_post_type( 
            'yoko_' . strtolower( $name ),
            array(
                'labels' => array(
                    'menu_name'          => esc_html__('News Items', 'news-items'),
		            'name_admin_bar'     => esc_html__('News Item', 'news-items'),
		            'add_new'            => esc_html__('Add News Item', 'news-items'),
		            'add_new_item'       => esc_html__('Add new News Item', 'news-items'),
		            'new_item'           => esc_html__('New News Item', 'news-items'),
		            'edit_item'          => esc_html__('Edit News Item', 'news-items'),
		            'view_item'          => esc_html__('View News Item', 'news-items'),
		            'update_item'        => esc_html__('View News Item', 'news-items'),
		            'all_items'          => esc_html__('All News Items', 'news-items'),
		            'search_items'       => esc_html__('Search News Items', 'news-items'),
		            'parent_item_colon'  => esc_html__('Parent News Item', 'news-items'),
		            'not_found'          => esc_html__('No News Items found', 'news-items'),
		            'not_found_in_trash' => esc_html__('No News Items found in Trash', 'news-items'),
		            'name'               => esc_html__('News Items', 'news-items'),
		            'singular_name'      => esc_html__('News Item', 'news-items'),
                ),
                'public'             => true,
                'has_archive'        => strtolower($taxonomy_name),
                'hierarchical'       => false,
                'rewrite'            => array( 'slug' => $name ),
                'menu_icon'          => 'dashicons-carrot',
                'taxonomies'          => array( 'category' ),
            )
        );
    }
}

$wpRelatedNews = new WPRelatedNews();

?>