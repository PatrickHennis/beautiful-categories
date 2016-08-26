<?php
/**
 * Plugin Name: Beautiful Categories
 * Plugin URI: http://www.glacieren.com/beautiful-categories.html/
 * Description: Display a beautiful listing of posts using the [bc] shortcode
 * Version: 0.0.5
 * Author: Patrick Hennis
 * Author URI: http://www.glacieren.com
 * License: GPLv2 or any later version
 *
 * @author Patrick Hennis <patrick@glacieren.com>
 * @copyright Copyright (c) 2016, Patrick Hennis
 * @link http://www.glacieren.com/beautiful-categories.html/
 */
//add custom style
add_action('init', 'load_css');
function beautiful_categories_register_style() {
    wp_register_style( 'bc-style', plugins_url('/css/bc-style.css', __FILE__), false, '1.0.0', 'all');
}
add_action('wp_enqueue_scripts', 'load_style');
function beautiful_categories_enqueue_style(){
   wp_enqueue_style( 'bc-style' );
}

//add shortcode
add_shortcode( 'bc', 'beautiful_categories' );
//function for shortcode
function beautiful_categories( $atts ) {
  
  //gets params from shortcode
  extract( shortcode_atts( array(
		'number_of_posts' => 5,
		'category'        => '',
    'title_align'     => 'center',
		'img'             => false,
	), $atts ) );
 
  $title_class = 'bc-center';
  if($title_align == 'center') {
    $title_class = 'bc-center';
  } else if($title_align == 'left') {
    $title_class = 'bc-left';
  } else if($title_align == 'right') {
    $title_class = 'bc-right';
  }
  
  //if img was entered as a string, sets variable to boolean false
  if($img == 'false'){
	   $img = false;
  }
  
  //get $number_of_posts from category
  $postlist = get_posts('category_name='.$category.'&posts_per_page='.$number_of_posts);
  wp_reset_postdata();
  
  //final output
  global $output;
  //start div for container
  $output = '<div class="bc_main">';
  
  //foreach loop to go through all posts
  foreach($postlist as $post){
    //get postID
    $postID = $post->ID;
    //get link for post
    $postLink = get_permalink($postID);
    
    //get post title
    $postTitle = $post->post_title;
    //create <a> tag for title
    $title = '<h2 class="bc-title '.$title_class.'"><a href="'.$postLink.'">'.$postTitle.'</a></h2>';
    
    //if image true, create image element
    if($img){
      $image = '<a class="bc-img" href="' . $postLink . ' ">'.get_the_post_thumbnail($postID, 'thumbnail').'</a>';
    }
    
    //get all main text from post
    $content = $post->post_content;
    
    //shorten text to 400 characters to closest word
    $line = $content;
    if (preg_match('/^.{1,400}\b/s', $content, $match)) {
      $line = $match[0];
    }
    //remove whitespace from front and back
    $content = trim($line);
    //add elipse to end of string
    $content .= '...';
    //create <p> tag for content
    $body = '<p>'.$content.'</p>';
    
    //create <a> tag for read more link
    $read_more = '<a class="bc-read-more" href="' . $postLink . ' ">'.'Read More'.'</a>';
    
    //string all elements together and add to final output
    $output .= '<div class="bc-item">' . $title . $image . $body . $read_more . '</div>';
  }
  //closing div
  $output .= '</div>';
  //return output
  return $output;
}?>