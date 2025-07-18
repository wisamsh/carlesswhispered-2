<?php

class StoryBook{
    
    
  public $stories ; 
  public $Pages ; 
public function __construct() {
  add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
  $this->stories = get_field('stories', get_the_ID()); 
  $this->Pages = $this->stories['book_pages'] ;
//   echo '<pre>';
// print_r($this->stories['book_pages']);
// echo '</pre>';
  
}
	public function enqueue_assets() {
        // CSS file
        wp_enqueue_style(
            'story-style',
            get_stylesheet_directory_uri() . '/assets/css/story.css',
            [],
            '1.0.0'
        );

        // JS file
        wp_enqueue_script(
            'story-script',
            get_stylesheet_directory_uri() . '/assets/js/story.js',
            ['jquery'], // dependencies
            '1.0.0',
            true // Load in footer
        );
    }



public function renderBookPages($pages) {
    
    $html = '';

    if (!empty($pages)) {
        $total = count($pages);

        for ($i = 0; $i < $total; $i += 2) {
            $html .= '<div class="right">';

            // Left Page (back)
            if (isset($pages[$i])) {
                $left = $pages[$i];
                $left_title = esc_html($left['page_title']);
                $left_content = wp_kses_post($left['book_page']);
                $left_cover = !empty($left['book_page_photo_cover']['url']) ? esc_url($left['book_page_photo_cover']['url']) : '';

                $html .= '<figure class="back" style="background-image: url(' . $left_cover . ');">';
                $html .= '<h6 style="color:'.$left['font_color'].';">' . $left_title . '</h6>';
                $html .= '<div class="book_page_content" style="color:'.$left['font_color'].';">' . strip_tags($left_content) . '</div>';
                $html .= '</figure>';
            }

            // Right Page (front)
            if (isset($pages[$i + 1])) {
                $right = $pages[$i + 1];
                $right_title = esc_html($right['page_title']);
                $right_content = wp_kses_post($right['book_page']);
                $right_cover = !empty($right['book_page_photo_cover']['url']) ? esc_url($right['book_page_photo_cover']['url']) : '';

                $html .= '<figure class="front" style="background-image: url(' . $right_cover . ');">';
                $html .= '<h6 style="color:'.$right['font_color'].';" >' . $right_title . '</h6>';
                $html .= '<div class="book_page_content" style="color:'.$right['font_color'].';">' . strip_tags($right_content) . '</div>';
                $html .= '</figure>';
            }

            $html .= '</div>'; // end .right
        }
    }

    return $html;
}




public function BookCover($coverphoto , $title , $slug = ''){
 $s_title = $slug ? '<p>' . $slug . '</p>'  : ''; 
  return '
    <!-- Page 1 (Cover) -->
    <div class="right">
      <figure class="front bkcover" style="background-image: url('.$coverphoto.');">
        <h2>'.$title.'</h2>';
        $s_title .'
        </figure>
      ';
$html.=$this->BookCover_back($coverphoto , $title , $slug = '', $dir='front');
    $html .='</div>';
}


public function BookCover_back($coverphoto , $title ){
 
  return '
  <div class="right">
      <figure class="back" id="back-cover">
       
         <img src="'.$coverphoto.'"/>
       </figure>
       <figure class="front bkcover" >
         <h2>END</h2>
         <p>Rigts and shit...</p>
       </figure>
     </div>
    ';
}






public function BookHtml(){
  $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');

$html = '';
$html .= 
    '<div class="book-section">
  <div class="container">
    <!-- Book spine strip -->
    <div class="spine"></div>
';


$html .=$this->BookCover_back(esc_url($thumbnail_url) ,  get_the_title() );

// $html .='
//     <!-- Page 3 (Back Cover) -->
//     <div class="right">
//       <figure class="back" id="back-cover">
//         <h2>Back Cover</h2>
//         <p>Thanks for reading!</p>
//       </figure>
//       <figure class="front" style="background-image: url();">
//         <h2>Page 3</h2>
//         <p>This is the content on the right side of page 3.</p>
//       </figure>
//     </div>

//     <!-- Page 2 -->
//     <div class="right">
//       <figure class="back" style="background-image: url();">
//         <h2>Page 2</h2>
//         <p>This is the content on the left side of page 2.</p>
//       </figure>
//       <figure class="front" style="background-image: url();">
//         <h2>Page 2</h2>
//         <p>This is the content on the right side of page 2.</p>
//       </figure>
//     </div>

//     <!-- Page 1 -->
//     <div class="right">
//       <figure class="back" style="background-image: url();">
//         <h2>Page 1</h2>
//         <p>This is the content on the left side of page 1.</p>
        
//       </figure>
//       <figure class="front" style="background-image: url();">
//         <h2>Page 1</h2>
//         <p>This is the content on the right side of page 1.</p>
//       </figure>
//     </div>
// ';

$html .= $this->renderBookPages(array_reverse($this->Pages));

$html .= $this->BookCover(esc_url($thumbnail_url) ,  get_field('story_title', get_the_ID()) , get_field('summery', get_the_ID()));
 


 $html .='</div></div>

  <button onclick="turnLeft()">Prev</button>
  <button onclick="turnRight()">Next</button>
</div>

';
return $html;
}





}
?>