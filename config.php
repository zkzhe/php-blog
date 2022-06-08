<?php
session_start();
// 參數設置
$hostname = '';
$username = '';
$password = '';
$database = 'd_blog_php';
$baseUrl = '';

// connect to database 連接數據庫
$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
	die("Error connecting to database: " . mysqli_connect_error());
}
// define global constants 定義全局常數
define('ROOT_PATH', __DIR__);
define('BASE_URL', $baseUrl);
// 紀錄觀看次數
$file = @fopen("View_Counter.txt", "r");
$num = @fgets($file);
@fclose($file);
if (@$_SESSION['come'] != 'v') {
	$num++;
	$file = fopen("View_Counter.txt", "w");
	fwrite($file, $num);
	fclose($file);
	$_SESSION['come'] = 'v';
}

function View_Counter()
{
	global $num;
	$string = strlen($num);
	for ($i = 0; $i < $string; $i++) {
		/* $n=substr($num,$i,1);*/
		echo '<img src=static/images/' . substr($num, $i, 1) . '.png  />';
	}
}
