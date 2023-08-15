<?php
require_once("queries.inc.php");

//Versions to avoid site caching
const JS_VER=57;
const CS_VER=24;
const VS_VER=6;
const PL_VER=6;

//Regexp used for determining types of links
const REGEXP_MEGA='/https:\/\/mega(?:\.co)?\.nz\/(?:#!|embed#!|file\/|embed\/)?([a-zA-Z0-9]{0,8})[!#]([a-zA-Z0-9_-]+)/';
const REGEXP_DL_LINK='/^https:\/\/(?:drive\.google\.com|mega\.nz|mega\.co\.nz).*/';
const REGEXP_STORAGE='/^storage:\/\/.*/';

function validate_hentai() {
	global $user;
	if (SITE_IS_HENTAI && !empty($user) && !is_adult()) {
		$_GET['code']=403;
		http_response_code(403);
		include('error.php');
		die();
	}
}

function validate_hentai_ajax() {
	global $user;
	if (SITE_IS_HENTAI && !empty($user) && !is_adult()) {
		$_GET['code']=403;
		http_response_code(403);
		die();
	}
}

function get_fansub_preposition_name($text){
	$first = mb_strtoupper(substr($text, 0, 1));
	if (($first == 'A' || $first == 'E' || $first == 'I' || $first == 'O' || $first == 'U') && substr($text, 0, 4)!='One '){ //Ugly...
		return "d’$text";
	}
	return "de $text";
}

function get_rating($text){
	switch ($text){
		case 'TP':
			return "Tots els públics";
		case '+7':
			return "Majors de 7 anys";
		case '+13':
			return "Majors de 13 anys";
		case '+16':
			return "Majors de 16 anys";
		case '+18':
			return "Majors de 18 anys";
		case 'XXX':
			return "Majors de 18 anys (contingut pornogràfic)";
		default:
			return $text;
	}
}

function get_provider($links){
	$methods = array();
	foreach ($links as $link) {
		if (preg_match(REGEXP_MEGA,$link['url'])){
			array_push($methods, 'mega');
		} else if (preg_match(REGEXP_STORAGE,$link['url'])) {
			array_push($methods, 'storage');
		} else {
			array_push($methods, 'direct-video');
		}
	}
	$output = '';
	if (in_array('mega', $methods)){
		if ($output!='') {
			$output.=", ";
		}
		$output.="MEGA";
	}
	if (in_array('direct-video', $methods) || in_array('storage', $methods)){
		if ($output!='') {
			$output.=", ";
		}
		$output.="Vídeo incrustat";
	}
	return $output;
}

function get_storage_url($url, $clean=FALSE) {
	if (count(STORAGES)>0 && strpos($url, "storage://")===0) {
		$rand = rand(0, count(STORAGES)-1);
		if ($clean) {
			return str_replace("storage://", STORAGES[$rand], $url);
		} else {
			return generate_storage_url(str_replace("storage://", STORAGES[$rand], $url));
		}
	} else {
		return $url;
	}
}

function list_remote_files($url) {
	$contents = @file_get_contents($url);
	preg_match_all("|href=[\"'](.*?)[\"']|", $contents, $hrefs);
	$hrefs = array_slice($hrefs[1], 1);
	
	$files = array();
	foreach ($hrefs as $href) {
		array_push($files, $url.$href);
	}
	return $files;
}

function filter_links($links){
	$methods = array();
	$links_mega = array();
	$links_storage = array();
	$links_direct = array();
	foreach ($links as $link) {
		if (preg_match(REGEXP_MEGA,$link['url'])){
			array_push($links_mega, $link);
		} else if (preg_match(REGEXP_STORAGE,$link['url'])){
			array_push($links_storage, $link);
		} else {
			array_push($links_direct, $link);
		}
	}

	//This establishes the preferences order:
	//Storage > Direct video > MEGA

	if (count($links_storage)>0 && count(STORAGES)>0) {
		return $links_storage;
	}

	if (count($links_direct)>0) {
		return $links_direct;
	}

	if (count($links_mega)>0) {
		return $links_mega;
	}
}

function get_resolution($links){
	$max_res=0;
	$max_res_text = "";
	foreach ($links as $link) {
		if (count(explode('x',$link['resolution']))>1) {
			$cur_res = explode('x',$link['resolution'])[1];
		} else {
			$cur_res=preg_replace("/[^0-9]/", '', $link['resolution']);
		}
		if ($cur_res>$max_res) {
			$max_res = $cur_res;
			$max_res_text = $link['resolution'];
		}
	}
	return $max_res_text;
}

function get_resolution_short($links){
	$max_res=0;
	$max_res_text = "";
	foreach ($links as $link) {
		if (count(explode('x',$link['resolution']))>1) {
			$cur_res = explode('x',$link['resolution'])[1];
		} else {
			$cur_res=preg_replace("/[^0-9]/", '', $link['resolution']);
		}
		if ($cur_res>$max_res) {
			$max_res = $cur_res;
			$max_res_text = $link['resolution'];
		}
	}
	return $max_res.'p';
}

function get_resolution_css($links){
	$resolution = str_replace('p', '', get_resolution_short($links));
	if ($resolution>=1800) {
		return "4k";
	} else if ($resolution>=900) {
		return "hd1080";
	} else if ($resolution>=650) {
		return "hd720";
	} else {
		return "sd";
	}
}

function get_episode_player_title($fansub_name, $series_name, $series_subtype, $episode_title, $is_extra){
	if ($series_name==$episode_title || ($series_subtype==CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID && !$is_extra)){
		if (!empty($episode_title)) {
			return $fansub_name . ' - ' . $episode_title;
		} else {
			return $fansub_name . ' - ' . $series_name;
		}
	} else {
		return $fansub_name . ' - ' . $series_name . ' - '. $episode_title;
	}
}

function get_episode_player_title_short($series_name, $series_subtype, $episode_title, $is_extra){
	if ($series_name==$episode_title || ($series_subtype==CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID && !$is_extra)){
		if (!empty($episode_title)) {
			return $episode_title;
		} else {
			return $series_name;
		}
	} else {
		return $episode_title;
	}
}

function get_hours_or_minutes_formatted($time){
	if ($time>=3600) {
		$hours = floor($time/3600);
		$time = $time-$hours*3600;
		echo $hours." h ".round($time/60)." min";
	} else {
		echo round($time/60)." min";
	}
}

function get_comic_type($comic_type){
	switch ($comic_type) {
		case 'manga':
			return 'Manga';
		case 'manhwa':
			return 'Manhwa';
		case 'manhua':
			return 'Manhua';
		default:
			return 'Còmic';
	}
}

function get_type_depending_on_catalogue($series) {
	if ($series['type']=='manga') {
		return (CATALOGUE_ITEM_TYPE!='manga' ? get_comic_type($series['comic_type']).' • ' : '');
	} else if ($series['type']=='anime') {
		return (CATALOGUE_ITEM_TYPE!='anime' ? 'Anime • ' : '');
	} else {
		return (CATALOGUE_ITEM_TYPE!='liveaction' ? 'Imatge real • ' : '');
	}
}

function get_episode_title($series_subtype, $show_episode_numbers, $episode_number, $linked_episode_id, $title, $series_name, $extra_name, $is_extra) {
	if ($is_extra) {
		return $extra_name;
	}

	if ($show_episode_numbers && !empty($episode_number) && empty($linked_episode_id)) {
		if (!empty($title)){
			return 'Capítol '.str_replace('.',',',floatval($episode_number)).': '.$title;
		}
		else {
			return 'Capítol '.str_replace('.',',',floatval($episode_number));
		}
	} else {
		if (!empty($title)){
			return $title;
		} else if ($series_subtype==CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID) {
			return $series_name;
		} else {
			return 'Capítol sense nom';
		}
	}
}

function print_episode($fansub_names, $row, $version_id, $series, $version, $position){
	if (!empty($row['linked_episode_id'])) {
		$result = query("SELECT f.* FROM file f WHERE f.episode_id=".$row['linked_episode_id']." AND f.version_id IN (SELECT v2.id FROM episode e2 LEFT JOIN series s ON e2.series_id=s.id LEFT JOIN version v2 ON v2.series_id=s.id LEFT JOIN rel_version_fansub vf ON v2.id=vf.version_id WHERE vf.fansub_id IN (SELECT fansub_id FROM rel_version_fansub WHERE version_id=$version_id) AND e2.id=${row['linked_episode_id']}) ORDER BY f.variant_name ASC, f.id ASC");
		$results = query("SELECT s.* FROM episode e LEFT JOIN series s ON e.series_id=s.id WHERE e.id=${row['linked_episode_id']}");
		$series = mysqli_fetch_assoc($results);
		mysqli_free_result($results);
		$resultv=query("SELECT v.*, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name FROM version v LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE v.id IN (SELECT v2.id FROM episode e2 LEFT JOIN series s ON e2.series_id=s.id LEFT JOIN version v2 ON v2.series_id=s.id LEFT JOIN rel_version_fansub vf ON v2.id=vf.version_id WHERE vf.fansub_id IN (SELECT fansub_id FROM rel_version_fansub WHERE version_id=$version_id) AND e2.id=${row['linked_episode_id']})");
		$version = mysqli_fetch_assoc($resultv);
		$fansub_names = $version['fansub_name'];
		mysqli_free_result($resultv);
	} else {
		$result = query("SELECT f.* FROM file f WHERE f.episode_id=".$row['id']." AND f.version_id=$version_id ORDER BY f.variant_name ASC, f.id ASC");
	}

	if (mysqli_num_rows($result)==0 && $version['show_unavailable_episodes']!=1){
		return;
	}

	$episode_title=get_episode_title($series['subtype'], $version['show_episode_numbers'],$row['number'],$row['linked_episode_id'],$row['title'],$series['name'], NULL, FALSE);

	internal_print_episode($fansub_names, $episode_title, $result, $series, FALSE, $position);
	mysqli_free_result($result);
}

function print_extra($fansub_names, $row, $version_id, $series, $position){
	$result = query("SELECT f.* FROM file f WHERE f.episode_id IS NULL AND f.extra_name='".escape($row['extra_name'])."' AND f.version_id=$version_id ORDER BY f.id ASC");

	$episode_title=get_episode_title($series['subtype'], NULL,NULL,NULL,NULL,NULL,$row['extra_name'], TRUE);
	
	internal_print_episode($fansub_names, $episode_title, $result, $series, TRUE, $position);
	mysqli_free_result($result);
}

function internal_print_episode($fansub_names, $episode_title, $result, $series, $is_extra, $position) {
//TABLE FORMAT: thumbnail, episode title + other data, seen
	$num_variants = mysqli_num_rows($result);
	if ($num_variants==0){ //Episode not available at all
?>
<tr class="episode episode-unavailable">
	<td class="episode-thumbnail-cell">
		<div class="episode-thumbnail">
			<div class="play-button fa fa-fw fa-ban"></div>
		</div>
	</td>
	<td class="episode-title-cell">
		<div class="episode-title"><?php echo htmlspecialchars($episode_title); ?></div>
	</td>
	<td class="episode-seen-cell"></td>
</tr>
<?php
	} else {
		//Iterate all variants
		while ($vrow = mysqli_fetch_assoc($result)){
			if ($vrow['is_lost']==0) {
				if ($series['type']!='manga') {
					$links = array();
					$resulti = query_links_by_file_id($vrow['id']);
					while ($lirow = mysqli_fetch_assoc($resulti)){
						array_push($links, $lirow);
					}
					mysqli_free_result($resulti);
					$links = filter_links($links);
				}
?>
<tr class="file-launcher episode<?php $num_variants>1 ? ' episode-indented' : ''; ?>" data-file-id="<?php echo $vrow['id']; ?>" data-title="<?php echo htmlspecialchars(get_episode_player_title($fansub_names, $series['name'], $series['subtype'], $episode_title, $is_extra)); ?>" data-title-short="<?php echo htmlspecialchars(get_episode_player_title_short($series['name'], $series['subtype'], $episode_title, $is_extra)); ?>" data-thumbnail="<?php echo file_exists(STATIC_DIRECTORY.'/images/files/'.$vrow['id'].'.jpg') ? STATIC_URL.'/images/files/'.$vrow['id'].'.jpg' : STATIC_URL.'/images/covers/'.$series['id'].'.jpg'; ?>" data-position="<?php echo $position; ?>">
	<td class="episode-thumbnail-cell">
<?php
	if (file_exists(STATIC_DIRECTORY.'/images/files/'.$vrow['id'].'.jpg')) {
?>
		<div class="episode-thumbnail">
			<img src="<?php echo STATIC_URL.'/images/files/'.$vrow['id'].'.jpg'; ?>" alt="">
<?php
	} else {
?>
		<div class="episode-thumbnail episode-thumbnail-missing">
<?php
	}
?>
			<span class="progress" style="width: 50%;"></span> <!-- TODO -->
			<div class="play-button fa fa-fw <?php echo $series['type']=='manga' ? 'fa-book-open' : 'fa-play'; ?>"></div>
		</div>
	</td>
	<td class="episode-title-cell">
		<div class="episode-title"><?php echo htmlspecialchars($episode_title); ?><?php echo $num_variants>1 ? '<br>'.htmlspecialchars($vrow['variant_name']): ''; ?></div>
<?php
				if (!empty($vrow['comments'])){
?>
		<span class="version-info tooltip" title="<?php echo str_replace("\n", "<br>", htmlspecialchars($vrow['comments'])); ?>"><span class="fa fa-fw fa-info-circle"></span></span>
<?php
				}
				if ($vrow['created']>=date('Y-m-d', strtotime("-1 week"))) {
?>
		<span class="new-episode tooltip<?php echo in_array($vrow['id'], get_cookie_viewed_files_ids()) ? ' hidden' : ''; ?>" data-file-id="<?php echo $vrow['id']; ?>" title="Publicat fa poc"><span class="fa fa-fw fa-certificate"></span></span>
<?php
				}
				if ($series['type']!='manga') {
?>
		<span class="version-resolution-<?php echo get_resolution_css($links); ?> tooltip tooltip-right" title="Vídeo: <?php echo get_resolution($links); ?>, servei: <?php echo get_provider($links); ?>"><?php echo htmlspecialchars(get_resolution_short($links)); ?></span>
<?php
				}
?>
	</td>
	<td class="episode-seen-cell">
<?php
				if (in_array($vrow['id'], get_cookie_viewed_files_ids())) {
?>
		<span class="viewed-indicator viewed" data-file-id="<?php echo $vrow['id']; ?>" title="Ja l\'has <?php echo $series['type']=='manga' ? 'llegit' : 'vist'; ?>"><span class="fa fa-fw fa-eye"></span></span>
<?php
				} else {
?>
		<span class="viewed-indicator not-viewed" data-file-id="<?php echo $vrow['id']; ?>" title="Encara no l\'has <?php echo $series['type']=='manga' ? 'llegit' : 'vist'; ?>"><span class="fa fa-fw fa-eye-slash"></span></span>
<?php
				}
?>
	</td>
</tr>
<?php
			} else { //Lost file
?>
<tr class="episode episode-unavailable">
	<td class="episode-thumbnail-cell">
		<div class="episode-thumbnail">
			<div class="play-button fa fa-fw fa-ghost version-lost" title="Perdut, ens ajudes?"></div>
		</div>
	</td>
	<td class="episode-title-cell">
		<div class="episode-title"><?php echo htmlspecialchars($episode_title); ?></div>
	</td>
	<td class="episode-seen-cell"></td>
</tr>
<?php
			}
		}
	}
}

function get_recommended_fansub_info($fansub_info, $versions, $specific_version_id) {
	if (!empty($specific_version_id)) {
		//We recreate the array with only one version (if not found, it stays the same)
		foreach ($versions as $version) {
			if ($version['id']==$specific_version_id) {
				$versions = array($version);
				break;
			}
		}
	}
	$result_code='';

	foreach ($versions[0]['fansubs'] as $fansub) {
		$result_code.='<div class="fansub">'.($fansub['type']=='fandub' ? '<i class="fa fa-fw fa-microphone"></i>' : '').'<span class="text">'.htmlspecialchars($fansub['name']).'</span> <img src="'.$fansub['icon'].'" alt=""></div>'."\n";
	}

	return $result_code;
}

function print_chapter_item($row) {
?>
	<div class="continue-watching-thumbnail-outer">
		<div class="continue-watching-thumbnail">
			<a class="image-link" href="<?php echo SITE_BASE_URL.'/'.$row['series_slug']."?f=".$row['file_id']; ?>">
				<div class="versions"><?php echo get_fansub_icons($row['fansub_info'], get_prepared_versions($row['fansub_info']), $row['version_id']); ?></div>
				<img src="<?php echo file_exists(STATIC_DIRECTORY.'/images/files/'.$row['file_id'].'.jpg') ? STATIC_URL.'/images/files/'.$row['file_id'].'.jpg' : STATIC_URL.'/images/covers/'.$row['series_id'].'.jpg'; ?>" alt="">
				<span class="progress" style="width: <?php echo $row['progress_percent']*100; ?>%;"></span>
				<div class="play-button fa fa-fw fa-<?php echo CATALOGUE_ITEM_TYPE=='manga' ? 'book-open' : 'play'; ?>"></div>
				<div class="close-button fa fa-fw fa-times" onclick="removeFromContinueWatching(this, <?php echo $row['file_id']; ?>); return false;"></div>
			</a>
		</div>
		<div class="title">
			<?php echo $row['series_name']; ?>
		</div>
		<div class="subtitle">
			<?php echo $row['division_name'].(($row['division_name']!='' && $row['episode_number']!='') ? ' • ' : '').($row['episode_number']!='' ? 'Cap. '.$row['episode_number'] : '').((($row['division_name']!='' || $row['episode_number']!='') && $row['episode_title']!='') ? ': ' : '').$row['episode_title']; ?>
		</div>
	</div>
<?php
}

function get_genres_for_featured($genre_names, $type, $rating) {
	if (empty($genre_names)) {
		return "";
	}
	$genres_array = explode(' • ',$genre_names);
	$result_code = '';

	foreach ($genres_array as $genre) {
		$genre_for_url = preg_replace('/\xC2\xA0/', ' ', $genre);
		$genre_for_url = preg_replace('/‑/', '-', $genre_for_url);
		$result_code.='<a class="genre" href="'.get_base_url_from_type_and_rating($type,$rating).'/cerca?categoria='.$genre_for_url.'">'.htmlspecialchars($genre).'</a>';
	}
	return '<i class="fa fa-fw fa-tag fa-flip-horizontal"></i> '.$result_code;
}

function print_featured_item($series, $special_day=NULL, $specific_version=TRUE) {
	$versions = get_prepared_versions($series['fansub_info']);
	$number_of_versions = count($versions);
	echo "\t\t\t\t\t\t\t".'<div class="recommendation" data-series-id="'.$series['id'].'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<img class="background" src="'.STATIC_URL.'/images/featured/'.$series['id'].'.jpg" alt="'.htmlspecialchars($series['name']).'">'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="status" title="'.get_status_description($series['best_status']).'"><div class="status-indicator '.get_status_css_icons($series['best_status']).'"></div><span class="text">'.get_status_description_short($series['best_status']).'</span></div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="infoholder" data-swiper-parallax="-30%">'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="coverholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<a href="'.get_base_url_from_type_and_rating($series['type'],$series['rating']).'/'.$series['slug'].(($specific_version && $number_of_versions>1) ? "?v=".$series['version_id'] : "").'"><img class="cover" src="'.STATIC_URL.'/images/covers/'.$series['id'].'.jpg" alt="'.htmlspecialchars($series['name']).'"></a>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'<div class="dataholder">'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="title">'.htmlspecialchars($series['name']).'</div>'."\n";
	if ($series['subtype']=='oneshot') {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">One-shot</div>'."\n";
	} else if ($series['subtype']=='serialized') {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">Serialitzat • '.($series['divisions']==1 ? "1 volum" : $series['divisions'].' volums').' • '.($series['number_of_episodes']==-1 ? 'En publicació' : ($series['number_of_episodes']==1 ? "1 capítol" : $series['number_of_episodes'].' capítols')).'</div>'."\n";
	} else if ($series['subtype']=='movie' && $series['number_of_episodes']>1) {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">Conjunt de '.$series['number_of_episodes'].' films</div>'."\n";
	} else if ($series['subtype']=='movie') {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">Film</div>'."\n";
	} else if ($series['divisions']>1) {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">Sèrie • '.$series['divisions'].' temporades • '.($series['number_of_episodes']==-1 ? 'En emissió' : $series['number_of_episodes'].' capítols').'</div>'."\n";
	} else {
		echo "\t\t\t\t\t\t\t\t\t\t".'<div class="divisions">Sèrie • '.($series['number_of_episodes']==-1 ? 'En emissió' : ($series['number_of_episodes']==1 ? "1 capítol" : $series['number_of_episodes'].' capítols')).'</div>'."\n";
	}
	echo "\t\t\t\t\t\t\t\t\t\t".'<div class="synopsis">'."\n";

	$Parsedown = new Parsedown();
	$synopsis = $Parsedown->setBreaksEnabled(true)->line($series['synopsis']);

	echo "\t\t\t\t\t\t\t\t\t\t\t".$synopsis."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t\t\t".'<a class="watchbutton" href="'.get_base_url_from_type_and_rating($series['type'],$series['rating']).'/'.$series['slug'].(($specific_version && $number_of_versions>1) ? "?v=".$series['version_id'] : "").'">'.($series['type']=='manga' ? 'Llegeix-lo ara' : 'Mira’l ara').'</a>'."\n";
	echo "\t\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
	echo "\t\t\t\t\t\t\t\t".'<div class="fansubs">'.get_recommended_fansub_info($series['fansub_info'], $versions, $series['version_id']).'</div>'."\n";
	if (!empty($special_day)) {
		if ($special_day=='fools') {
			echo "\t\t\t\t\t\t\t\t".'<div class="special-day"><i class="fa fa-fw fa-trophy"></i><span class="text">Els millors de l’any</span></div>'."\n";
		} else if ($special_day=='sant_jordi') {
			echo "\t\t\t\t\t\t\t\t".'<div class="special-day"><i class="fa fa-fw fa-dragon"></i><span class="text">Especial Sant Jordi</span></div>'."\n";
		} if ($special_day=='tots_sants') {
			echo "\t\t\t\t\t\t\t\t".'<div class="special-day"><i class="fa fa-fw fa-ghost"></i><span class="text">Especial Tots Sants</span></div>'."\n";
		}
	}
	echo "\t\t\t\t\t\t\t\t".'<div class="genres">'.get_genres_for_featured($series['genre_names'], $series['type'], $series['rating']).'</div>'."\n";
	echo "\t\t\t\t\t\t\t".'</div>'."\n";
}

function get_tadaima_info($thread_id) {
	global $memcached;

	$response = $memcached->get("tadaima_post_$thread_id");
	if ($response==FALSE) {
		$ch = curl_init("https://tadaima.cat/api/get_topic_detail/$thread_id");

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
			array(
				'User-Agent: Fansubscat/Anime/1.0.0',
				'X-Tadaima-App-Id: TadaimaApp',
				'X-Tadaima-Api-Version: 1'
			)
		);

		$response = curl_exec($ch);
		if($response!==FALSE) {
			$memcached->set("tadaima_post_$thread_id", $response, MEMCACHED_EXPIRY_TIME);
		}
		curl_close($ch);
	}
	if($response===FALSE) {
		return "Comenta-ho a Tadaima.cat";
	} else {
		$json_response = json_decode($response);
		if ($json_response->status!='ok') {
			return "Comenta-ho a Tadaima.cat";
		} else {
			$number_of_posts = count($json_response->result->posts);
			if ($number_of_posts==1){
				return "Comenta-ho a Tadaima.cat (1 comentari)";
			} else {
				return "Comenta-ho a Tadaima.cat ($number_of_posts comentaris)";
			}
		}
	}
}

function is_mobile_user_agent($user_agent) {
	return preg_match('/android|bb\d+|meego|mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$user_agent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($user_agent,0,4));
}

function get_view_source_type($user_agent, $is_casted) {
	if ($is_casted) {
		return 'cast';
	}
	if(preg_match('/\[via API\]/', $user_agent)){
		return 'api';
	}
	if(is_mobile_user_agent($user_agent)){
		return 'mobile';
	}
	return 'desktop';
}
?>
