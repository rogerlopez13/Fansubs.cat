<?php
//Database access
$db_host="YOUR_DB_HOST_HERE";
$db_name="YOUR_DB_NAME_HERE";
$db_user="YOUR_DB_USER_HERE";
$db_passwd="YOUR_DB_PASS_HERE";

//Memcached access (for storing remote requests cache)
$memcached_host='YOUR_MEMCACHED_HOST_HERE';
$memcached_port=YOUR_MEMCACHED_PORT_HERE;
$memcached_expiry_time=12*3600;

//Lock file for MEGA sync processes
$mega_lock_file='/tmp/mega_fetch.lock';

//Password salt for hashes
$password_salt='YOUR_PASSWORD_SALT_HERE';
?>