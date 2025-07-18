<?php 


require_once(get_template_directory().'/inc/classes/story.class.php');
$StoryBook = new StoryBook;
get_header();
?>
<div class="book_wrapper">
<?php
echo $StoryBook->BookHtml();
?>
</div>
<?php 
get_footer();
?>