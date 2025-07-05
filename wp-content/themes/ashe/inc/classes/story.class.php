<?php

class StoryBook{
    
public function __construct() {
  add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
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



public function BookHtml(){
	return 
    '<div class="book-section">
  <div class="container">
    
    <!-- Page 4 (Back Cover) -->
    <div class="right">
      <figure class="back" id="back-cover">
        <h2>Back Cover</h2>
        <p>Thanks for reading!</p>
      </figure>
      <figure class="front" style="background-image: url();">
        <h2>Page 3</h2>
        <p>This is the content on the right side of page 3.</p>
      </figure>
    </div>

    <!-- Page 3 -->
    <div class="right">
      <figure class="back" style="background-image: url();">
        <h2>Page 2</h2>
        <p>This is the content on the left side of page 2.</p>
      </figure>
      <figure class="front" style="background-image: url();">
        <h2>Page 2</h2>
        <p>This is the content on the right side of page 2.</p>
      </figure>
    </div>

    <!-- Page 2 -->
    <div class="right">
      <figure class="back" style="background-image: url();">
        <h2>Page 1</h2>
        <p>This is the content on the left side of page 1.</p>
      </figure>
      <figure class="front" style="background-image: url();">
        <h2>Page 1</h2>
        <p>This is the content on the right side of page 1.</p>
      </figure>
    </div>

    <!-- Page 1 (Cover) -->
    <div class="right">
      <figure class="back" style="background-image: url();">
        <h2>Inside Cover</h2>
        <p>Welcome to the book!</p>
      </figure>
      <figure class="front" id="cover">
        <h1>Book Title</h1>
        <p>A story of pages turning beautifully.</p>
      </figure>
    </div>

  </div>

  <!-- Navigation Buttons -->
  <button onclick="turnLeft()">Prev</button>
  <button onclick="turnRight()">Next</button>
  <br />
</div>
';
}





}
?>