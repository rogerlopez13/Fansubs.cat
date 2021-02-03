<?php
include_once('db.inc.php');
ob_start();
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$max_items = 20;

function is_outdated_fansubs_app(){
	$ua = $_SERVER['HTTP_USER_AGENT'];
	return strpos($ua,'FansubsCatApp/Android/')===0 && explode(' [', explode('FansubsCatApp/Android/', $ua)[1])[0]<'1.0.2';
}

function show_invalid($reason) {
	http_response_code(400);
	$response = array(
		'status' => 'ko',
		'error' => array(
				'code' => 'INVALID_REQUEST',
				'description' => $reason
			)
	);
	echo json_encode($response);
}

$method = array_shift($request);
if ($method == 'refresh') {
	$token = array_shift($request);
	if ($token!=NULL){
		$result = mysqli_query($db_connection, "SELECT slug FROM fansub WHERE ping_token='".mysqli_real_escape_string($db_connection, $token)."'") or crash('Internal error: '.mysqli_error($db_connection));
		if ($row = mysqli_fetch_assoc($result)){
			system("cd $services_path && /usr/bin/php fetch.php {$row['slug']} > /dev/null &");
			$response = array(
				'status' => 'ok',
				'result' => 'A refresh operation has been scheduled for your fansub.'
			);
			echo json_encode($response);
		}
		else{
			http_response_code(401);
			$response = array(
				'status' => 'ko',
				'error' => array(
					'code' => 'UNAUTHORIZED',
					'description' => 'The provided refresh token is invalid.'
				)
			);
			echo json_encode($response);
		}
	}
	else{
		show_invalid('No refresh token has been provided.');
	}
}
else if ($method == 'fansubs'){
	$active_result = mysqli_query($db_connection, "SELECT DISTINCT f.id FROM fansub f WHERE f.status=1 AND f.name<>'Fansub independent'") or crash('Internal error: '.mysqli_error($db_connection));
	$active_fansubs = array();
	while($row = mysqli_fetch_assoc($active_result)){
		$active_fansubs[] = $row['id'];
	}

	$result = mysqli_query($db_connection, "SELECT id, slug, name, url, historical, archive_url FROM fansub UNION SELECT NULL id, 'fansubs-cat' slug, 'Fansubs.cat' name, NULL, 0, NULL ORDER BY name ASC") or crash('Internal error: '.mysqli_error($db_connection));
	$elements = array();
	while($row = mysqli_fetch_assoc($result)){
		$elements[] = array(
			'id' => $row['slug'],
			'name' => $row['name'],
			'url' => $row['url'],
			'logo_url' => 'https://www.fansubs.cat/images/fansub_logos/'.$row['id'].'.png',
			'icon_url' => ($row['slug']=='fansubs-cat' ? 'https://www.fansubs.cat/favicon.ico' : 'https://www.fansubs.cat/images/fansub_icons/'.$row['id'].'.png'),
			'is_historical' => ($row['historical']==1),
			'is_active' => (in_array($row['id'], $active_fansubs)),
			'is_visible' => ($row['slug']!='fansubs-cat' && $row['slug']!='fansubs-independents'),
			'is_own' => ($row['slug']=='fansubs-cat'),
			'archive_url' => $row['archive_url']
		);
	}

	$response = array(
		'status' => is_outdated_fansubs_app() ? 'must_update' : 'ok',
		'result' => $elements
	);
	echo json_encode($response);
}
else if (substr($method, 0, 4) === "news"){
	$page = isset($_GET['page']) ? $_GET['page'] : NULL;
	$search = isset($_GET['search']) ? $_GET['search'] : NULL;
	$fansub_ids = isset($_GET['fansub_ids']) ? $_GET['fansub_ids'] : NULL;
	if ($page!=NULL && is_numeric($page) && $page>=0){
		$page = (int)$page*25;

		$search_extra="";
		$fansub_ids_extra="";

		if ($search!=NULL && $search!=''){
			$search = mysqli_real_escape_string($db_connection, $search);
			$search_extra = " AND (n.title LIKE '%$search%' OR n.contents LIKE '%$search%')";
		}

		if ($fansub_ids!=NULL && count($fansub_ids)>0){
			foreach ($fansub_ids as &$fansub_id){
				$fansub_id = mysqli_real_escape_string($db_connection, $fansub_id);
			}
			$fansub_ids_extra = " AND IFNULL(f.slug,'fansubs-cat') IN ('" . implode("', '", $fansub_ids) . "')";
		}

		$result = mysqli_query($db_connection, "SELECT n.*, IFNULL(f.slug,'fansubs-cat') fansub_slug, f.name fansub_name FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE 1" . $search_extra . $fansub_ids_extra . " ORDER BY n.date DESC LIMIT 25 OFFSET $page") or crash('Internal error: ' . mysqli_error($db_connection));
		$elements = array();
		while($row = mysqli_fetch_assoc($result)){
			$elements[] = array(
				'date' => date_create_from_format('Y-m-d H:i:s', $row['date'])->getTimestamp(),
				'fansub_id' => $row['fansub_slug'],
				'fansub_name' => $row['fansub_name'],
				'title' => $row['title'],
				'contents' => $row['contents'],
				'url' => $row['url'],
				'image_url' => $row['image']!=NULL ? 'https://www.fansubs.cat/images/news/'.$row['fansub_slug'].'/'.$row['image'] : NULL
			);
		}

		$response = array(
			'status' => 'ok',
			'result' => $elements
		);
		echo json_encode($response);
	}
	else{
		show_invalid('You can not fetch news if you don\'t provide a valid page number.');
	}
}
else if ($method === 'manga'){
	$submethod = array_shift($request);
	if ($submethod=='popular') {
		$page = array_shift($request);
		if ($page>0) {
			$offset = ($page-1)*20;

			$result = mysqli_query($db_connection, "SELECT a.*, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres
			FROM (SELECT SUM(vi.views) views, fi.manga_version_id, m.* FROM manga m LEFT JOIN chapter c ON c.manga_id=m.id LEFT JOIN file fi ON fi.chapter_id=c.id LEFT JOIN manga_views vi ON vi.file_id=fi.id WHERE fi.chapter_id IS NOT NULL GROUP BY fi.manga_version_id, fi.chapter_id) a LEFT JOIN rel_manga_genre mg ON a.id=mg.manga_id LEFT JOIN genre g ON mg.genre_id = g.id
			GROUP BY a.id
			ORDER BY a.rating IS NOT NULL AND a.rating='XXX' ASC, MAX(a.views) DESC, a.name ASC LIMIT $max_items OFFSET $offset") or crash('Internal error: ' . mysqli_error($db_connection));
			$elements = array();
			while($row = mysqli_fetch_assoc($result)){
				$elements[] = array(
					'slug' => ($row['type']=='oneshot' ? 'one-shots/' : 'serialitzats/').$row['slug'],
					'name' => $row['name'],
					'author' => $row['author'],
					'synopsis' => $row['synopsis'],
					'genres' => $row['genres'],
					'status' => $row['chapters']>=1 ? 'finished' : 'ongoing',
					'thumbnail_url' => 'https://manga.fansubs.cat/images/manga/'.$row['id'].'.jpg'
				);
			}

			$response = array(
				'status' => 'ok',
				'result' => $elements
			);
			echo json_encode($response);
		} else {
			show_invalid('No valid input provided.');
		}
	} else if ($submethod=='recent') {
		$page = array_shift($request);
		if ($page>0) {
			$offset = ($page-1)*20;

			$result = mysqli_query($db_connection, "SELECT s.*, (SELECT nv.id FROM manga_version nv WHERE nv.files_updated=MAX(v.files_updated) AND v.manga_id=s.id LIMIT 1) manga_version_id, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(ss.id) FROM volume ss WHERE ss.manga_id=s.id) volumes, s.chapters, (SELECT MAX(ls.created) FROM file ls LEFT JOIN manga_version vs ON ls.manga_version_id=vs.id WHERE vs.manga_id=s.id) last_link_created FROM manga s LEFT JOIN manga_version v ON s.id=v.manga_id LEFT JOIN rel_manga_version_fansub vf ON v.id=vf.manga_version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_manga_genre sg ON s.id=sg.manga_id LEFT JOIN genre g ON sg.genre_id = g.id GROUP BY s.id ORDER BY last_updated DESC LIMIT $max_items OFFSET $offset") or crash('Internal error: ' . mysqli_error($db_connection));
			$elements = array();
			while($row = mysqli_fetch_assoc($result)){
				$elements[] = array(
					'slug' => ($row['type']=='oneshot' ? 'one-shots/' : 'serialitzats/').$row['slug'],
					'name' => $row['name'],
					'author' => $row['author'],
					'synopsis' => $row['synopsis'],
					'genres' => $row['genres'],
					'status' => $row['chapters']>=1 ? 'finished' : 'ongoing',
					'thumbnail_url' => 'https://manga.fansubs.cat/images/manga/'.$row['id'].'.jpg'
				);
			}

			$response = array(
				'status' => 'ok',
				'result' => $elements
			);
			echo json_encode($response);
		} else {
			show_invalid('No valid input provided.');
		}
	} else if ($submethod=='search') {
		$page = array_shift($request);
		$page = explode('?', $page)[0];
		$query = mysqli_real_escape_string($db_connection, $_GET['query']);
		if ($page>0) {
			if (!empty($query)){
				//Improvement: We can add source (Tachiyomi) if we ever want to differentiate
				mysqli_query($db_connection, "INSERT INTO manga_search_history (query,day) VALUES ('$query','".date('Y-m-d')."')") or crash('Internal error: ' . mysqli_error($db_connection));
			}

			$offset = ($page-1)*20;

			$result = mysqli_query($db_connection, "SELECT s.*, (SELECT nv.id FROM manga_version nv WHERE nv.files_updated=MAX(v.files_updated) AND v.manga_id=s.id LIMIT 1) manga_version_id, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(ss.id) FROM volume ss WHERE ss.manga_id=s.id) volumes, s.chapters, (SELECT MAX(ls.created) FROM file ls LEFT JOIN manga_version vs ON ls.manga_version_id=vs.id WHERE vs.manga_id=s.id) last_link_created FROM manga s LEFT JOIN manga_version v ON s.id=v.manga_id LEFT JOIN rel_manga_version_fansub vf ON v.id=vf.manga_version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_manga_genre sg ON s.id=sg.manga_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE (s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' OR s.author LIKE '%$query%' OR s.keywords LIKE '%$query%') OR s.id IN (SELECT mg.manga_id FROM rel_manga_genre mg LEFT JOIN genre g ON mg.genre_id=g.id WHERE g.name='$query') GROUP BY s.id ORDER BY s.name ASC LIMIT $max_items OFFSET $offset") or crash('Internal error: ' . mysqli_error($db_connection));
			$elements = array();
			while($row = mysqli_fetch_assoc($result)){
				$elements[] = array(
					'slug' => ($row['type']=='oneshot' ? 'one-shots/' : 'serialitzats/').$row['slug'],
					'name' => $row['name'],
					'author' => $row['author'],
					'synopsis' => $row['synopsis'],
					'genres' => $row['genres'],
					'status' => $row['chapters']>=1 ? 'finished' : 'ongoing',
					'thumbnail_url' => 'https://manga.fansubs.cat/images/manga/'.$row['id'].'.jpg'
				);
			}

			$response = array(
				'status' => 'ok',
				'result' => $elements
			);
			echo json_encode($response);
		} else {
			show_invalid('No valid input provided.');
		}
	} else if ($submethod=='details') {
		$slug = array_shift($request);
		$slug = mysqli_real_escape_string($db_connection, $slug);
		$result = mysqli_query($db_connection, "SELECT m.*, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres FROM manga m LEFT JOIN rel_manga_genre mg ON m.id=mg.manga_id LEFT JOIN genre g ON mg.genre_id = g.id WHERE m.slug='".$slug."'") or crash('Internal error: ' . mysqli_error($db_connection));
		if($row = mysqli_fetch_assoc($result)){
			$element = array(
				'slug' => ($row['type']=='oneshot' ? 'one-shots/' : 'serialitzats/').$row['slug'],
				'name' => $row['name'],
				'author' => $row['author'],
				'synopsis' => $row['synopsis'],
					'genres' => $row['genres'],
				'status' => $row['chapters']>=1 ? 'finished' : 'ongoing',
				'thumbnail_url' => 'https://manga.fansubs.cat/images/manga/'.$row['id'].'.jpg'
			);

			$response = array(
				'status' => 'ok',
				'result' => $element
			);
			echo json_encode($response);
		} else {
			show_invalid('No valid manga specified.');
		}
	} else if ($submethod=='chapters') {
		$slug = array_shift($request);
		$slug = mysqli_real_escape_string($db_connection, $slug);
		$result = mysqli_query($db_connection, "SELECT fi.id, fi.created, c.number, CONCAT(IF(fi.chapter_id IS NULL,'',IF(c.volume_id IS NULL,'Altres - ',IF(m.show_volumes<>1,'Volum únic',IF(vo.name IS NOT NULL,CONCAT(vo.name, ' - '),IF((SELECT COUNT(*) FROM volume WHERE manga_id=m.id)=1,'Volum únic - ',CONCAT('Volum ',vo.number,' - ')))))),IF(fi.chapter_id IS NULL, CONCAT('Extra - ',fi.extra_name), IF(ct.title IS NOT NULL, IF(m.show_chapter_numbers AND c.number IS NOT NULL,CONCAT('Capítol ', REPLACE(TRIM(c.number)+0,'.',','), ': ',ct.title),ct.title), IF(m.type='oneshot',m.name,IF(c.number IS NOT NULL AND m.show_chapter_numbers=1,CONCAT('Capítol ', REPLACE(TRIM(c.number)+0,'.',',')),'Capítol sense nom'))))) chapter_title, (SELECT GROUP_CONCAT(f.name SEPARATOR ', ') FROM rel_manga_version_fansub vf LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE vf.manga_version_id=mv.id) fansubs FROM manga m LEFT JOIN manga_version mv ON mv.manga_id=m.id LEFT JOIN file fi ON fi.manga_version_id=mv.id LEFT JOIN chapter_title ct ON ct.manga_version_id=mv.id AND ct.chapter_id=fi.chapter_id LEFT JOIN chapter c ON fi.chapter_id=c.id LEFT JOIN volume vo ON c.volume_id=vo.id WHERE m.slug='$slug' AND fi.original_filename IS NOT NULL ORDER BY fi.chapter_id IS NULL ASC, vo.number IS NULL ASC, vo.number DESC, c.number DESC, ct.title DESC, fi.extra_name DESC, fi.created DESC") or crash('Internal error: ' . mysqli_error($db_connection));
		$elements = array();
		while($row = mysqli_fetch_assoc($result)){
			$elements[] = array(
				'id' => $row['id'],
				'title' => $row['chapter_title'],
				'number' => $row['number']==NULL ? 0 : floatval($row['number']),
				'fansub' => $row['fansubs'],
				'created' => strtotime($row['created'])*1000
			);
		}

		$response = array(
			'status' => 'ok',
			'result' => $elements
		);
		echo json_encode($response);
	} else if ($submethod=='pages') {
		$file_id = intval(array_shift($request));

		$base_path="../manga.fansubs.cat/images/storage/$file_id/";

		if (!file_exists($base_path)) {
			show_invalid('No valid file specified.');
		} else {
			$result = mysqli_query($db_connection, "SELECT f.* FROM file f WHERE f.id=$file_id");
			if ($row = mysqli_fetch_assoc($result)) {
				$time_spent=$row['number_of_pages'] * 3;
				$pages_read=$row['number_of_pages'];
				mysqli_query($db_connection, "REPLACE INTO manga_views SELECT $file_id, '".date('Y-m-d')."', IFNULL((SELECT clicks+1 FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT views+1 FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT time_spent+$time_spent FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),$time_spent), IFNULL((SELECT pages_read+$pages_read FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),$pages_read), IFNULL((SELECT api_views+1 FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),1)");
				$user_agent = mysqli_real_escape_string($db_connection, $_SERVER['HTTP_USER_AGENT']);
				mysqli_query($db_connection, "INSERT INTO manga_view_log (file_id, date, api_user_agent) VALUES ($file_id, CURRENT_TIMESTAMP, '$user_agent')");
			}
			$files = scandir($base_path);
			natsort($files);
			$elements = array();
			foreach ($files as $file) {
				if ($file=='.' || $file=='..') {
					continue;
				}
				$elements[] = array(
					'url' => 'https://manga.fansubs.cat/images/storage/'.$file_id.'/'.$file
				);
			}

			$response = array(
				'status' => 'ok',
				'result' => $elements
			);
			echo json_encode($response);
		}
	} else {
		show_invalid('No valid submethod specified.');
	}
}
else{
	show_invalid('No valid method specified.');
}

mysqli_close($db_connection);
?>
