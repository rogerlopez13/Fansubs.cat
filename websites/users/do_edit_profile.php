<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("queries.inc.php");

function string_ends_with($haystack, $needle) {
	$length = strlen($needle);
	if(!$length) {
		return TRUE;
	}
	return substr($haystack, -$length) === $needle;
}

function edit_profile(){
	global $user;
	//Check if we have all the data
	if (empty($user) || empty($_POST['email_address']) || empty($_POST['birthday_day']) || empty($_POST['birthday_month']) || empty($_POST['birthday_year']) || empty($_POST['avatar']) || !is_numeric($_POST['birthday_day']) || !is_numeric($_POST['birthday_month']) || !is_numeric($_POST['birthday_year'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables
	$email_address = $_POST['email_address'];
	$birth_day = $_POST['birthday_day'];
	$birth_month = $_POST['birthday_month'];
	$birth_year = $_POST['birthday_year'];
	$avatar = $_POST['avatar'];

	//Check for valid date
	if (!checkdate($birth_month, $birth_day, $birth_year)) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 4);
	}

	//Check for valid year
	if ($birth_year<1900) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 5);
	}
	if (time() < strtotime('-1 hour', date_timestamp_get(date_create_from_format('Y-m-d', $birth_year.'-'.$birth_month.'-'.$birth_day)))) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 6);
	}

	//Check for younger than 13
	if (time() < strtotime('+13 years', date_timestamp_get(date_create_from_format('Y-m-d', $birth_year.'-'.$birth_month.'-'.$birth_day)))) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 7);
	}

	//Check correctly formed e-mail address
	if (!preg_match("/.*@.*\\..*/",$email_address)) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 8);
	}

	//Check not from a blocked domain
	foreach (BLACKLISTED_EMAIL_DOMAINS as $domain) {
		if (string_ends_with($email_address, $domain)) {
			http_response_code(400);
			return array('result' => 'ko', 'code' => 9);
		}
	}

	//Check if email exists
	$result = query_user_by_email_except_self($email_address, $user['id']);
	if (mysqli_num_rows($result)>0){
		http_response_code(400);
		mysqli_free_result($result);
		return array('result' => 'ko', 'code' => 3);
	}

	if (str_starts_with($avatar, 'http')) {
		query_update_user_profile($user['id'], $email_address, $birth_year."-".$birth_month."-".$birth_day, NULL);
	} else {
		$avatar_filename = get_nanoid().'.png';
		file_put_contents(STATIC_DIRECTORY.'/images/avatars/'.$avatar_filename, file_get_contents($avatar));
		query_update_user_profile($user['id'], $email_address, $birth_year."-".$birth_month."-".$birth_day, $avatar_filename);
	}

	return array('result' => 'ok');
}

echo json_encode(edit_profile());
?>
