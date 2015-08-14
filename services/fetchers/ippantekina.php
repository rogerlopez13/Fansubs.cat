<?php
include("libs/FeedTypes.php");
include("libs/simple_html_dom.php");

//Creating an instance of FeedWriter class. 
$TestFeed = new RSS2FeedWriter();
$TestFeed->setTitle('Ippantekina Jimaku');
$TestFeed->setLink('http://ippantekina.blogspot.com/');

$html = file_get_html("http://ippantekina.blogspot.com/") or exit(1);

$go_on = TRUE;

while ($go_on){
	//parse through the HTML and build up the RSS feed as we go along
	foreach($html->find('div.post') as $article) {
		//Create an empty FeedItem
		$newItem = $TestFeed->createNewItem();

		//Look up and add elements to the feed item   
		$title = $article->find('h3.post-title a', 0);
		if ($title!=NULL){
			$newItem->setTitle($title->innertext);
		}
		else{
			$newItem->setTitle('(Sense títol)');
		}
		$newItem->setDescription($article->find('div.post-body', 0)->innertext);
		if ($title!=NULL){
			$newItem->setLink($title->href);
		}
		else{
			$newItem->setLink('http://ippantekina.blogspot.com/');
		}
		$newItem->setDate($article->find('abbr.published', 0)->title);

		//Now add the feed item
		$TestFeed->addItem($newItem);
	}

	$texts = $html->find('text');
	$go_on = FALSE;
	foreach ($texts as $text){
		if ($text->plaintext=='Missatges més antics'){
			sleep(1); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
			$html = file_get_html($text->parent->href) or exit(1);
			$go_on = TRUE;
			break;
		}
	}
	
}

$TestFeed->generateFeed();
?>
