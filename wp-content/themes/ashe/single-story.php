<?php 


require_once(get_template_directory().'/inc/classes/story.class.php');
$StoryBook = new StoryBook;
get_header();

echo $StoryBook->BookHtml();


?>
<?php 
get_footer();
?>