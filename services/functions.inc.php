<?php
require_once("libs/simple_html_dom.php");
require_once('common.inc.php');

//Removes the unwanted tags, double enters, etc. from descriptions
function parse_description($description){
	$description = strip_tags($description, '<br><b><strong><em><i><ul><li><ol><hr><sub><sup><u><tt><p>');
	$description = str_replace('&nbsp;',' ', $description);
	$description = str_replace(' & ','&amp;', $description);
	$description = str_replace('<br>','<br />', $description);
	$description = preg_replace('/(<br\s*\/?>\s*){3,}/', '<br /><br />', $description);
	return preg_replace('/(?:<br\s*\/?>\s*)+$/', '', preg_replace('/^(?:<br\s*\/?>\s*)+/', '', trim($description)));
}

//Gets the first image in the news content that is not a SVG, if available.
//Then copies it to our website directory
function fetch_and_parse_image($fansub_id, $url, $description){
	global $website_directory;
	preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $description, $matches);

	$first_image_url=NULL;
	if (isset($matches) && isset($matches[1])){
		for ($i=0;$i<count($matches[1]);$i++){
			if (strpos($matches[1][$i], '.svg')===FALSE){
				$first_image_url = $matches[1][$i];
				break;
			}
		}
	}

	if ($first_image_url!=NULL){
		if (strpos($first_image_url,"://")===FALSE){
			$first_image_url=$url.$first_image_url;
		}
		if (!is_dir("$website_directory/images/news/$fansub_id/")){
			mkdir("$website_directory/images/news/$fansub_id/");
		}
		if (@copy($first_image_url, "$website_directory/images/news/$fansub_id/".slugify($first_image_url))){
			return slugify($first_image_url);
		}
		else if (file_exists("$website_directory/images/news/$fansub_id/".slugify($first_image_url))){
			//This means that the file is no longer accessible, but we already have it locally!
			return slugify($first_image_url);
		}
	}
	return NULL;
}

//This function does the actual fetching:
//Decides depending on the method and then processes the returned feed items, inserting them to database
function fetch_fansub_fetcher($db_connection, $fansub_id, $fetcher_id, $method, $url, $last_fetched_item_date){
	mysqli_query($db_connection, "UPDATE fetchers SET status='fetching' WHERE id=$fetcher_id") or die(mysqli_error($db_connection));
	switch($method){
		case 'catsub':
			$result = fetch_via_catsub($fansub_id, $url, $last_fetched_item_date);
			break;
		case 'blogspot':
			$result = fetch_via_blogspot($fansub_id, $url, $last_fetched_item_date);
			break;
		case 'blogspot_2nf':
			$result = fetch_via_blogspot_2nf($fansub_id, $url, $last_fetched_item_date);
			break;
		case 'blogspot_dnf':
			$result = fetch_via_blogspot_dnf($fansub_id, $url, $last_fetched_item_date);
			break;
		case 'blogspot_llpnf':
			$result = fetch_via_blogspot_llpnf($fansub_id, $url, $last_fetched_item_date);
			break;
		case 'blogspot_snf':
			$result = fetch_via_blogspot_snf($fansub_id, $url, $last_fetched_item_date);
			break;
		case 'blogspot_tnf':
			$result = fetch_via_blogspot_tnf($fansub_id, $url, $last_fetched_item_date);
			break;
		case 'phpbb_dnf':
			$result = fetch_via_phpbb_dnf($fansub_id, $url, $last_fetched_item_date);
			break;
		case 'weebly_rnnf':
			$result = fetch_via_weebly_rnnf($fansub_id, $url, $last_fetched_item_date);
			break;
		case 'wordpress_ddc':
			$result = fetch_via_wordpress_ddc($fansub_id, $url, $last_fetched_item_date);
			break;
		case 'wordpress_xf':
			$result = fetch_via_wordpress_xf($fansub_id, $url, $last_fetched_item_date);
			break;
		case 'wordpress_ynf':
			$result = fetch_via_wordpress_ynf($fansub_id, $url, $last_fetched_item_date);
			break;
		default:
			$result = array('error_invalid_method',array());
	}

	//Store news with fetcher and fansub id
	if ($result[0]=='ok'){
		if (count($result[1])>0){

			//We get all elements
			$elements = $result[1];
			
			//Empty the original array
			$result[1]=array();

			//We get the lower item date available and reuse last_fetched_item_date
			foreach ($elements as $element){
				if ($element[3]<$last_fetched_item_date){
					$last_fetched_item_date = $element[3];
				}
			}
		
			//We copy to the array ONLY the ones which are higher than the date (NOT equal)
			foreach ($elements as $element){
				if ($element[3]>$last_fetched_item_date){
					$result[1][] = $element;
				}
			}

			//We delete the old ones (higher than the last one date)
			mysqli_autocommit($db_connection, FALSE);
			mysqli_query($db_connection, "DELETE FROM news WHERE fetcher_id=$fetcher_id AND date>'$last_fetched_item_date'") or (mysqli_rollback($db_connection) && $result[0]='error_mysql');

			//And then insert them if everything goes well
			if ($result[0]=='ok'){
				foreach ($result[1] as $element){
					mysqli_query($db_connection, "INSERT INTO news (fansub_id, fetcher_id, title, original_contents, contents, date, url, image) VALUES ('$fansub_id', $fetcher_id, '".mysqli_real_escape_string($db_connection, $element[0])."','".mysqli_real_escape_string($db_connection, $element[1])."','".mysqli_real_escape_string($db_connection, $element[2])."','".$element[3]."','".mysqli_real_escape_string($db_connection, $element[4])."',".($element[5]!=NULL ? "'".mysqli_real_escape_string($db_connection, $element[5])."'" : 'NULL').")") or (mysqli_rollback($db_connection) && $result[0]='error_mysql');
				}
			}
			if ($result[0]=='ok'){
				mysqli_autocommit($db_connection, TRUE);
			}
		}
		else{
			//The feed was empty, don't treat as success
			$result[0]='error_empty';
		}
	}
	
	//Update fetch status
	mysqli_query($db_connection, "UPDATE fetchers SET status='idle',last_fetch_result='".$result[0]."',last_fetch_date='".date('Y-m-d H:i:s')."' WHERE id=$fetcher_id") or die(mysqli_error($db_connection));
}

/** BELOW HERE ARE ALL INDIVIDUAL METHODS OF FETCHING **/
/** THE CODE IS ULTRA UGLY AND HACKY, BUT IT WORKS (as of July 2016). BEWARE! **/

function fetch_via_blogspot($fansub_id, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('h3.post-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;
				$item[1]=$article->find('div.post-body', 0)->innertext;
				$item[2]=parse_description($article->find('div.post-body', 0)->innertext);
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_id, $url, $article->find('div.post-body', 0)->innertext);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_2nf($fansub_id, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('h3.post-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;
			
				$description=$article->find('div.post-body', 0)->innertext;
				$item[1]=$description;
				
				$description = preg_replace("/\<p>(.*)Descarrega el capítol!(.*)\<\/p\>/i", '', $description);
				
				$item[2]=parse_description($description);
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_id, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_dnf($fansub_id, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('h3.post-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;
			
				$description=$article->find('div.post-body', 0)->innertext;
				$item[1]=$description;
				
				$description = parse_description($description);
				$description = preg_replace("/Submanga: http(.*)\<br \/\>/i", '', $description);
				$description = preg_replace("/MegaUpload: http(.*)\<br \/\>/i", '', $description);
				$description = preg_replace("/MegaUpload: http(.*)/i", '', $description);
				$description = preg_replace("/Subamanga: http(.*)\<br \/\>/i", '', $description);
				
				$item[2]=$description;
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_id, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_llpnf($fansub_id, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			//We only show news which start with [LlPnF or [DnF from the main blog, or we will also show the series pages...
			//Could be improved, of course...
			if ($article->find('h3.post-title a', 0)!==NULL &&
					(stripos($article->find('h3.post-title a', 0)->innertext,'[LlPnF')===FALSE
					|| stripos($article->find('h3.post-title a', 0)->innertext,'[LlPnF')>0) &&
					(stripos($article->find('h3.post-title a', 0)->innertext,'[DnF')===FALSE
					|| stripos($article->find('h3.post-title a', 0)->innertext,'[DnF')>0)){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item   
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;

				$description = $article->find('div.post-body', 0)->innertext;
				$item[1]=$description;

				//We replace the notiwrapper with an empty string to remove the download links
				foreach ($article->find('div.post-body div.noti_wrapper') as $notiwrapper){
					$description = str_replace($notiwrapper->outertext, '', $description);
				}

				//This helps with the layout here: http://llunaplenanofansub.blogspot.com.es/2015/08/anime-kokoro-connect-01-02-03-i-04.html
				$description = str_replace('</center>','</center><br /><br />', $description);

				//We replace headers with bold text so it doesn't crash our layout
				$description = str_replace('<h1>','<b>', $description);
				$description = str_replace('</h1>','</b><br />', $description);
				$description = str_replace('<h2>','<b>', $description);
				$description = str_replace('</h2>','</b><br />', $description);
				$description = str_replace('<h3>','<b>', $description);
				$description = str_replace('</h3>','</b><br />', $description);

				$item[2]=parse_description($description);

				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->parent->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_id, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;
						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_snf($fansub_id, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('h3.post-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;

				$description = $article->find('div.post-body', 0)->innertext;
				$item[1]=$description;

				//We remove the password string (seems to always be the same)
				$description = str_replace("<b>Contrasenya: snf</b>",'', $description);

				$item[2]=parse_description($description);
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_id, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Missatges més antics'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_blogspot_tnf($fansub_id, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			//This is terribly unoptimal... Try to find a better way!
			if ($article->find('h3.post-title a', 0)!==NULL && ((stripos($article->find('h3.post-title a', 0),'CAPÍTOL')!==FALSE && stripos($article->find('h3.post-title a', 0),'CATALÀ')!==FALSE) || stripos($article->find('h3.post-title a', 0),'NARUTO MANGA')!==FALSE || stripos($article->find('h3.post-title a', 0),'DETECTIU CONAN CAPÍTOL')!==FALSE || stripos($article->find('h3.post-title a', 0),'Naruto: Capítol manga')!==FALSE || stripos($article->find('h3.post-title a', 0),'QUI SOM?')!==FALSE || stripos($article->find('h3.post-title a', 0),'BEELZEBUB CAPÍTOL')!==FALSE || stripos($article->find('h3.post-title a', 0),'Fairy Tail Manga')!==FALSE)){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h3.post-title a', 0);
				$item[0]=$title->innertext;
				$item[1]=$article->find('div.post-body', 0)->innertext;
				$item[2]=parse_description($article->find('div.post-body', 0)->innertext);
				$date = date_create_from_format('Y-m-d\TH:i:sP', $article->find('abbr.published', 0)->title);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
				$item[3]= $date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_id, $url, $article->find('div.post-body', 0)->innertext);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='Entradas antiguas'){
					$tries=1;
					while ($tries<=3){
						sleep($tries*$tries); //Seems to help get rid of 503 errors... probably Blogger is rate-limited
						$error=FALSE;

						$html_text = file_get_contents($text->parent->href) or $error=TRUE;

						if (!$error){
							$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
							tidy_clean_repair($tidy);
							$html = str_get_html(tidy_get_output($tidy));
							break;
						}
						else{
							$tries++;
						}
					}
					if ($tries>3){
						return array('error_connect',array());
					}
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_catsub($fansub_id, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.cs_news') as $article) {
			//Create an empty item
			$item = array();

			//Look up and add elements to the item   
			$item[0]=$article->find('div.cs_newstitle a', 0)->innertext;

			$description = $article->find('div.cs_newscontent', 0)->innertext;
			$item[1]=$description;

			//Remove the download icon, or it will be the first image of the feed
			$description = preg_replace("/\<img (.*)dlicon(.*)Descàrregues\" \/\>/i", '', $description);

			//Remove the post-screenshot text
			if (strpos($description, 'cs_newsimage')!==0){
				$description = preg_replace("/\<span class=\\\"note\\\"\>(.*)\<\/span\>$/i", '', trim($description));
			}

			$item[2]=parse_description($description);

			//We have to explode because the format is: 05/07/2015 a les 19:48 / Ereza
			$datetext = explode(' / ', $article->find('div.cs_date', 0)->innertext)[0];

			$date = date_create_from_format('d/m/Y \a \l\e\s H:i', $datetext);

			$item[3]=$date->format('Y-m-d H:i:s');
			$item[4]=$url . substr($article->find('div.cs_newstitle a', 0)->href, 1);
			$item[5]=fetch_and_parse_image($fansub_id, $url, $description);

			$elements[]=$item;
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				//Compare last date to $last_fetched_item_date, if lower, stop paging
				if ($text->plaintext=='Notícies més antigues &gt;'){
					$html_text = file_get_contents($url . $text->parent->href) or $error_connect=TRUE;
					if ($error_connect){
						return array('error_connect',array());
					}
					$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($tidy);
					$html = str_get_html(tidy_get_output($tidy));
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_phpbb_dnf($fansub_id, $url, $last_fetched_item_date){
	$elements = array();

	$base_url=substr($url,0,strrpos($url,'/'));
	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('a.topictitle') as $topic) {
			$html_text_topic = file_get_contents($base_url.$topic->href) or $error_connect=TRUE;
			if ($error_connect){
				return array('error_connect',array());
			}
			$tidy_topic = tidy_parse_string($html_text_topic, $tidy_config, 'UTF8');
			tidy_clean_repair($tidy_topic);
			$html_topic = str_get_html(tidy_get_output($tidy_topic));

			//Create an empty item
			$item = array();

			//Look up and add elements to the item
			$title=substr($html_topic->find('h1.cattitle', 0)->innertext,6);
			$item[0]=$title;

			$description=$html_topic->find('div.postbody div', 0)->innertext;

			$item[1]=$description;
			$item[2]=parse_description($description);

			$datetext = $html_topic->find('span.postdetails', 1)->innertext;
			//We now have this: <img class="sprite-icon_post_target" src="http://illiweb.com/fa/empty.gif" alt="Missatge" title="Missatge" border="0" />Assumpte: Novetats] Fusió de DnF i LlPnF!&nbsp; &nbsp;<img src="http://illiweb.com/fa/empty.gif" alt="" border="0" />Ds Maig 15, 2010 9:49 pm
			$datetext = substr(strrchr($datetext, ">"), 1);

			$datetext = str_replace('Gen', 'January', $datetext);
			$datetext = str_replace('Feb', 'February', $datetext);
			$datetext = str_replace('Mar', 'March', $datetext);
			$datetext = str_replace('Abr', 'April', $datetext);
			$datetext = str_replace('Maig', 'May', $datetext);
			$datetext = str_replace('Jun', 'June', $datetext);
			$datetext = str_replace('Jul', 'July', $datetext);
			$datetext = str_replace('Ago', 'August', $datetext);
			$datetext = str_replace('Set', 'September', $datetext);
			$datetext = str_replace('Oct', 'October', $datetext);
			$datetext = str_replace('Nov', 'November', $datetext);
			$datetext = str_replace('Des', 'December', $datetext);

			$datetext = str_replace('Dl', 'Mon', $datetext);
			$datetext = str_replace('Dt', 'Tue', $datetext);
			$datetext = str_replace('Dc', 'Wed', $datetext);
			$datetext = str_replace('Dj', 'Thu', $datetext);
			$datetext = str_replace('Dv', 'Fri', $datetext);
			$datetext = str_replace('Ds', 'Sat', $datetext);
			$datetext = str_replace('Dg', 'Sun', $datetext);

			$date = date_create_from_format('D F d, Y H:i a', $datetext);
			$item[3]= $date->format('Y-m-d H:i:s');
			$item[4]=$base_url.$topic->href;
			$item[5]=fetch_and_parse_image($fansub_id, $url, $description);

			$elements[]=$item;
		}

		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			if ($html->find('img.sprite-arrow_prosilver_right', 0)!==NULL){
				$html_text = file_get_contents($base_url . $html->find('img.sprite-arrow_prosilver_right', 0)->parent->href) or $error_connect=TRUE;
				if ($error_connect){
						return array('error_connect',array());
				}
				$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
				tidy_clean_repair($tidy);
				$html = str_get_html(tidy_get_output($tidy));
				$go_on = TRUE;
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_weebly_rnnf($fansub_id, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.blog-post') as $article) {
			if ($article->find('h2.blog-title a', 0)!==NULL && stripos($article->find('h2.blog-title a', 0),'audio en Català')===FALSE && stripos($article->find('h2.blog-title a', 0),'audio en catala')===FALSE && stripos($article->find('h2.blog-title a', 0),'Bleach 001')===FALSE && stripos($article->find('h2.blog-title a', 0),'One Piece - 396, 397 i 298')===FALSE && stripos($article->find('h2.blog-title a', 0),'One Piece - 399, 400 i 401')===FALSE && stripos($article->find('h2.blog-title a', 0),'One Piece - 402, 403 i 404')===FALSE && stripos($article->find('h2.blog-title a', 0),'One Piece 405')===FALSE && stripos($article->find('h2.blog-title a', 0),'InuYasha')===FALSE){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h2.blog-title a', 0);
				$item[0]=$title->innertext;

				$description = $article->find('div.blog-content', 0)->innertext;

				$item[1]=$article->find('div.blog-content', 0)->innertext;
				$item[2]=parse_description($description);

				//The format is: 2013-09-02T14:43:43+00:00
				$datetext = $article->find('p.blog-date span', 0)->innertext;

				$date = date_create_from_format('!m/d/Y', $datetext);

				$item[3]=$date->format('Y-m-d H:i:s');
				$item[4]=$url . substr($title->href, 1);
				$item[5]=fetch_and_parse_image($fansub_id, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='&lt;&lt; Previous'){
					//Not sleeping, Weebly does not appear to be rate-limited
					$html_text = file_get_contents($url . substr($text->parent->href, 1)) or $error_connect=TRUE;
					if ($error_connect){
						return array('error_connect',array());
					}
					$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($tidy);
					$html = str_get_html(tidy_get_output($tidy));
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_wordpress_ddc($fansub_id, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('article') as $article) {
			if ($article->find('h1.entry-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h1.entry-title a', 0);
				$item[0]=$title->innertext;
				$item[1]=$article->find('div.entry-content', 0)->innertext;

				$description = str_replace("text-align:center;","",$article->find('div.entry-content', 0)->innertext);
				$description = preg_replace("/\<img (.*)submanga(.*)w=190\" \/\>/i", '', $description);
				$description = preg_replace("/\<img (.*)mediafire(.*)w=190\" \/\>/i", '', $description);
				$description = preg_replace("/\<img (.*)submanga(.*)w=560\" \/\>/i", '', $description);
				$description = preg_replace("/\<img (.*)mediafire(.*)w=560\" \/\>/i", '', $description);

				$item[2]=parse_description($description);

				//The format is: 2013-09-02T14:43:43+00:00
				$datetext = $article->find('time', 0)->datetime;

				$date = date_create_from_format('Y-m-d\TH:i:sP', $datetext);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));

				$item[3]=$date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_id, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext==' Entradas anteriores'){
					//Not sleeping, Wordpress.com does not appear to be rate-limited
					$html_text = file_get_contents($text->parent->href) or $error_connect=TRUE;
					if ($error_connect){
						return array('error_connect',array());
					}
					$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($tidy);
					$html = str_get_html(tidy_get_output($tidy));
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_wordpress_xf($fansub_id, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('div.post') as $article) {
			if ($article->find('div.post-header h2 a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('div.post-header h2 a', 0);
				$item[0]=$title->innertext;

				$description = $article->find('div.entry', 0)->innertext;
				$item[1]=$description;

				//We replace the sharer with an empty string to remove the share links
				foreach ($article->find('div.entry div#jp-post-flair') as $sharer){
					$description = str_replace($sharer->outertext, '', $description);
				}

				$item[2]=parse_description($description);

				//The format is: març 5, 2013
				$datetext = $article->find('div.post-header div.date a', 0)->innertext;

				$datetext = str_replace('gener', 'January', $datetext);
				$datetext = str_replace('febrer', 'February', $datetext);
				$datetext = str_replace('març', 'March', $datetext);
				$datetext = str_replace('abril', 'April', $datetext);
				$datetext = str_replace('maig', 'May', $datetext);
				$datetext = str_replace('juny', 'June', $datetext);
				$datetext = str_replace('juliol', 'July', $datetext);
				$datetext = str_replace('agost', 'August', $datetext);
				$datetext = str_replace('setembre', 'September', $datetext);
				$datetext = str_replace('octubre', 'October', $datetext);
				$datetext = str_replace('novembre', 'November', $datetext);
				$datetext = str_replace('desembre', 'December', $datetext);

				$date = date_create_from_format('F d, Y H:i:s', $datetext . ' 00:00:00');

				$item[3]=$date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_id, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext=='« Older Entries'){
					//Not sleeping, Wordpress.com does not appear to be rate-limited
					$html_text = file_get_contents($text->parent->href) or $error_connect=TRUE;
					if ($error_connect){
						return array('error_connect',array());
					}
					$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($tidy);
					$html = str_get_html(tidy_get_output($tidy));
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}

function fetch_via_wordpress_ynf($fansub_id, $url, $last_fetched_item_date){
	$elements = array();

	$tidy_config = "tidy.conf";
	$error_connect=FALSE;

	$html_text = file_get_contents($url) or $error_connect=TRUE;
	if ($error_connect){
		return array('error_connect',array());
	}
	$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
	tidy_clean_repair($tidy);
	$html = str_get_html(tidy_get_output($tidy));

	$go_on = TRUE;

	while ($go_on){
		//parse through the HTML and build up the elements feed as we go along
		foreach($html->find('article') as $article) {
			if ($article->find('h1.entry-title a', 0)!==NULL){
				//Create an empty item
				$item = array();

				//Look up and add elements to the item
				$title = $article->find('h1.entry-title a', 0);
				$item[0]=$title->innertext;
				$item[1]=$article->find('div.entry-content', 0)->innertext;

				$description = str_replace("text-align:center;","",$article->find('div.entry-content', 0)->innertext);

				$item[2]=parse_description($description);

				//The format is: 2013-09-02T14:43:43+00:00
				$datetext = $article->find('time', 0)->datetime;

				$date = date_create_from_format('Y-m-d\TH:i:sP', $datetext);
				$date->setTimeZone(new DateTimeZone('Europe/Berlin'));

				$item[3]=$date->format('Y-m-d H:i:s');
				$item[4]=$title->href;
				$item[5]=fetch_and_parse_image($fansub_id, $url, $description);

				$elements[]=$item;
			}
		}

		$texts = $html->find('text');
		$go_on = FALSE;
		if (count($elements)>0 && $elements[count($elements)-1][3]>=$last_fetched_item_date){
			foreach ($texts as $text){
				if ($text->plaintext==' Entrades més antigues'){
					//Not sleeping, Wordpress.com does not appear to be rate-limited
					$html_text = file_get_contents($text->parent->href) or $error_connect=TRUE;
					if ($error_connect){
						return array('error_connect',array());
					}
					$tidy = tidy_parse_string($html_text, $tidy_config, 'UTF8');
					tidy_clean_repair($tidy);
					$html = str_get_html(tidy_get_output($tidy));
					$go_on = TRUE;
					break;
				}
			}
		}
	}
	return array('ok', $elements);
}
?>
