<?php
include_once('db.inc.php');
ob_start();
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$method = array_shift($request);
if ($method == 'refresh') {
	$token = array_shift($request);
	if ($token!=NULL){
		$result = mysqli_query($db_connection, "SELECT id FROM fansubs WHERE ping_token='".mysqli_real_escape_string($db_connection, $token)."'") or crash('Internal error: '.mysqli_error($db_connection));
		if ($row = mysqli_fetch_assoc($result)){
			system("cd $services_path && /usr/bin/php fetch.php {$row['id']} > /dev/null &");
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
				'error' => 'The provided refresh token is invalid.'
			);
			echo json_encode($response);
		}
	}
	else{
		http_response_code(400);
		$response = array(
			'status' => 'ko',
			'error' => 'No refresh token has been provided.'
		);
		echo json_encode($response);
	}
}
else if ($method == 'fansubs'){
	$result = mysqli_query($db_connection, "SELECT * FROM fansubs ORDER BY name ASC") or crash('Internal error: '.mysqli_error($db_connection));
	$elements = array();
	while($row = mysqli_fetch_assoc($result)){
		$elements[] = array(
			'id' => $row['id'],
			'name' => $row['name'],
			'url' => $row['url'],
			'logo_url' => 'http://www.fansubs.cat/images/fansubs/logos/'.$row['logo_image'],
			'icon_url' => 'http://www.fansubs.cat/images/fansubs/favicons/'.$row['favicon_image'],
			'is_historical' => ($row['is_historical']==1),
			'is_visible' => ($row['is_visible']==1),
			'is_own' => ($row['is_own']==1),
			'archive_url' => $row['archive_url']
		);
	}

	$response = array(
		'status' => 'ok',
		'result' => $elements
	);
	echo json_encode($response);
}
else if ($method == 'news'){
	$page = array_shift($request);
	if ($page!=NULL && is_numeric($page) && $page>=0){
		$page = (int)$page*25;
		$result = mysqli_query($db_connection, "SELECT * FROM news ORDER BY date DESC LIMIT 25 OFFSET $page") or crash('Internal error: '.mysqli_error($db_connection));
		$elements = array();
		while($row = mysqli_fetch_assoc($result)){
			$elements[] = array(
				'date' => $row['date'],
				'fansub_id' => $row['fansub_id'],
				'title' => $row['title'],
				'contents' => $row['contents'],
				'url' => $row['url'],
				'image_url' => 'http://www.fansubs.cat/images/news/'.$row['fansub_id'].'/'.$row['image']
			);
		}

		$response = array(
			'status' => 'ok',
			'result' => $elements
		);
		echo json_encode($response);
	}
	else{
		$response = array(
			'status' => 'ko',
			'result' => 'You can not fetch news if you don\'t provide a valid page number.'
		);
		echo json_encode($response);
	}
}
else{
	$response = array(
		'status' => 'ko',
		'result' => 'No valid method specified.'
	);
	echo json_encode($response);
}

mysqli_close($db_connection);
?>