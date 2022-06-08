<?php

require_once('config.php');
require_once(ROOT_PATH . '/includes/registration_login.php');
$i = $_SESSION['user']['username'];
if ($i == null) $i = "anonym";
switch ($_GET["method"]) {
	case "add":
		add();
		break;
	case "update":
		update();
		break;
	case "del":
		del();
		break;
	default:
		break;
}

function add()
{
	global $i;
	$uid = $i;
	$title = $_POST["title"];
	$content = $_POST["content"];
	$sql = "INSERT INTO `mes` (uid, title, content)
		VALUES ('$uid', '$title', '$content')";
	global $conn;
	$result = mysqli_query($conn, $sql) or die('MySQL query error');
	echo "<script type='text/javascript'>";
	echo "alert('Post success');";
	echo "location.href='about.php';";
	echo "</script>";
}



function del()
{
	$id = $_GET["id"];
	$sql = "DELETE FROM `mes` WHERE id = $id";
	global $conn;
	$result = mysqli_query($conn, $sql) or die('MySQL query error');
	echo "<script type='text/javascript'>";
	echo "alert('Delete success');";
	echo "location.href='about.php';";
	echo "</script>";
}
