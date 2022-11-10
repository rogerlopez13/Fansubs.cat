<?php
//This is an example file. Edit it accordingly and rename it to "config.inc.php"

//Database access
$db_host="YOUR_DB_HOST_HERE";
$db_name="YOUR_DB_NAME_HERE";
$db_user="YOUR_DB_USER_HERE";
$db_passwd="YOUR_DB_PASS_HERE";

//Website URLs (no final slash)
$static_url="https://static.fansubs.cat";

//Paths (no final slash)
$services_directory='/srv/services/fansubs.cat';
$static_directory='/srv/websites/static.fansubs.cat';

//Specific data
$default_fansub_id=28; //"Fansub independent"

//Used to check internal calls only
$internal_token='YOUR_INTERNAL_TOKEN_HERE';

//Storages
$storages = array(
	'https://YOUR_STORAGE_SERVERS/'
);

//Storage URL customization
function generate_storage_url($url) {
	//Your custom code
	return $url;
}
?>
