<?php
// Admin user variables 管理員用戶變量
$admin_id = 0;
$isEditingUser = false;
$username = "";
$role = "";
$email = "";

// Topics variables 主題變量
$topic_id = 0;
$isEditingTopic = false;
$topic_name = "";

// general variables 一般變量
$errors = [];

/* - - - - - - - - - - 
-  Admin users actions 
   管理員用戶操作
- - - - - - - - - - -*/
// if user clicks the create admin button 如果用戶單擊創建管理員按鈕
if (isset($_POST['create_admin'])) {
	createAdmin($_POST);
}
// if user clicks the Edit admin button 如果用戶單擊“編輯管理員”按鈕
if (isset($_GET['edit-admin'])) {
	$isEditingUser = true;
	$admin_id = $_GET['edit-admin'];
	editAdmin($admin_id);
}
// if user clicks the update admin button 如果用戶單擊更新管理員按鈕
if (isset($_POST['update_admin'])) {
	updateAdmin($_POST);
}
// if user clicks the Delete admin button 如果用戶單擊刪除管理員按鈕
if (isset($_GET['delete-admin'])) {
	$admin_id = $_GET['delete-admin'];
	deleteAdmin($admin_id);
}

/* - - - - - - - - - - 
-  Topic actions 
   主題動作
- - - - - - - - - - -*/
// if user clicks the create topic button 如果用戶單擊創建主題按鈕
if (isset($_POST['create_topic'])) {
	createTopic($_POST);
}
// if user clicks the Edit topic button 如果用戶單擊“編輯主題”按鈕
if (isset($_GET['edit-topic'])) {
	$isEditingTopic = true;
	$topic_id = $_GET['edit-topic'];
	editTopic($topic_id);
}
// if user clicks the update topic button 如果用戶單擊更新主題按鈕
if (isset($_POST['update_topic'])) {
	updateTopic($_POST);
}
// if user clicks the Delete topic button 如果用戶單擊“刪除主題”按鈕
if (isset($_GET['delete-topic'])) {
	$topic_id = $_GET['delete-topic'];
	deleteTopic($topic_id);
}


/* - - - - - - - - - - - -
-  Admin users functions
   管理員用戶功能
- - - - - - - - - - - - -*/
/* * * * * * * * * * * * * * * * * * * * * * *
* - Receives new admin data from form 從表單接收新的管理員數據
* - Create new admin user 創建新的管理員用戶
* - Returns all admin users with their roles 返回所有管理員用戶及其角色
* * * * * * * * * * * * * * * * * * * * * * */
function createAdmin($request_values)
{
	global $conn, $errors, $role, $username, $email;
	$username = esc($request_values['username']);
	$email = esc($request_values['email']);
	$password = esc($request_values['password']);
	$passwordConfirmation = esc($request_values['passwordConfirmation']);

	if (isset($request_values['role'])) {
		$role = esc($request_values['role']);
	}
	// form validation: ensure that the form is correctly filled
	// 表單驗證：確保正確填寫表單
	if (empty($username)) {
		array_push($errors, "Uhmm...We gonna need the username");
	}
	if (empty($email)) {
		array_push($errors, "Oops.. Email is missing");
	}
	if (empty($role)) {
		array_push($errors, "Role is required for admin users");
	}
	if (empty($password)) {
		array_push($errors, "uh-oh you forgot the password");
	}
	if ($password != $passwordConfirmation) {
		array_push($errors, "The two passwords do not match");
	}
	// Ensure that no user is registered twice. 確保沒有用戶註冊兩次
	// the email and usernames should be unique 電子郵件和用戶名應該唯一
	$user_check_query = "SELECT * FROM users WHERE username='$username' 
							OR email='$email' LIMIT 1";
	$result = mysqli_query($conn, $user_check_query);
	$user = mysqli_fetch_assoc($result);
	if ($user) { // if user exists 如果用戶存在
		if ($user['username'] === $username) {
			array_push($errors, "Username already exists");
		}

		if ($user['email'] === $email) {
			array_push($errors, "Email already exists");
		}
	}
	// register user if there are no errors in the form 
	// 如果表格中沒有錯誤，請註冊用戶
	if (count($errors) == 0) {
		$password = md5($password); //encrypt the password before saving in the database
		//保存在數據庫中之前先對密碼進行加密
		$query = "INSERT INTO users (username, email, role, password, created_at, updated_at) 
				  VALUES('$username', '$email', '$role', '$password', now(), now())";
		mysqli_query($conn, $query);

		$_SESSION['message'] = "Admin user created successfully";
		header('location: users.php');
		exit(0);
	}
}
/* * * * * * * * * * * * * * * * * * * * *
* - Takes admin id as parameter 將管理員ID作為參數
* - Fetches the admin from database 從數據庫中獲取管理員
* - sets admin fields on form for editing 在表單上設置管理字段以進行編輯
* * * * * * * * * * * * * * * * * * * * * */
function editAdmin($admin_id)
{
	global $conn, $username, $role, $isEditingUser, $admin_id, $email;

	$sql = "SELECT * FROM users WHERE id=$admin_id LIMIT 1";
	$result = mysqli_query($conn, $sql);
	$admin = mysqli_fetch_assoc($result);

	// set form values ($username and $email) on the form to be updated
	// 在要更新的表單上設置表單值（$ username和$ email）
	$username = $admin['username'];
	$email = $admin['email'];
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
* - Receives admin request from form and updates in database
* - 從表單接收管理請求並在數據庫中更新
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
function updateAdmin($request_values)
{
	global $conn, $errors, $role, $username, $isEditingUser, $admin_id, $email;
	// get id of the admin to be updated 獲取要更新的管理員的ID
	$admin_id = $request_values['admin_id'];
	// set edit state to false 將編輯狀態設置為false
	$isEditingUser = false;


	$username = esc($request_values['username']);
	$email = esc($request_values['email']);
	$password = esc($request_values['password']);
	$passwordConfirmation = esc($request_values['passwordConfirmation']);
	if (isset($request_values['role'])) {
		$role = $request_values['role'];
	}
	// register user if there are no errors in the form 
	//如果表格中沒有錯誤，請註冊用戶
	if (count($errors) == 0) {
		//encrypt the password (security purposes)
		//加密密碼（出於安全目的）
		$password = md5($password);

		$query = "UPDATE users SET username='$username', email='$email', role='$role', password='$password' WHERE id=$admin_id";
		mysqli_query($conn, $query);

		$_SESSION['message'] = "Admin user updated successfully";
		header('location: users.php');
		exit(0);
	}
}
// delete admin user 刪除管理員用戶
function deleteAdmin($admin_id)
{
	global $conn;
	$sql = "DELETE FROM users WHERE id=$admin_id";
	if (mysqli_query($conn, $sql)) {
		$_SESSION['message'] = "User successfully deleted";
		header("location: users.php");
		exit(0);
	}
}




/* - - - - - - - - - - 
-  Topics functions 主題功能
- - - - - - - - - - -*/
// get all topics from DB 從數據庫獲取所有主題
function getAllTopics()
{
	global $conn;
	$sql = "SELECT * FROM topics";
	$result = mysqli_query($conn, $sql);
	$topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
	return $topics;
}
function createTopic($request_values)
{
	global $conn, $errors, $topic_name;
	$topic_name = esc($request_values['topic_name']);
	// create slug: if topic is "Life Advice", return "life-advice" as slug
	// 創建子彈：如果主題為“生活建議”，則以“子彈”形式返回“生活建議”
	$topic_slug = makeSlug($topic_name);
	// validate form 驗證表格
	if (empty($topic_name)) {
		array_push($errors, "Topic name required");
	}
	// Ensure that no topic is saved twice. 確保沒有主題被保存兩次
	$topic_check_query = "SELECT * FROM topics WHERE slug='$topic_slug' LIMIT 1";
	$result = mysqli_query($conn, $topic_check_query);
	if (mysqli_num_rows($result) > 0) { // if topic exists 如果存在主題
		array_push($errors, "Topic already exists");
	}
	// register topic if there are no errors in the form
	// 如果表格中沒有錯誤，請註冊主題
	if (count($errors) == 0) {
		$query = "INSERT INTO topics (name, slug) 
				  VALUES('$topic_name', '$topic_slug')";
		mysqli_query($conn, $query);

		$_SESSION['message'] = "Topic created successfully";
		header('location: topics.php');
		exit(0);
	}
}
/* * * * * * * * * * * * * * * * * * * * *
* - Takes topic id as parameter           將主題ID作為參數
* - Fetches the topic from database       從數據庫中獲取主題
* - sets topic fields on form for editing 在表單上設置主題字段以進行編輯
* * * * * * * * * * * * * * * * * * * * * */
function editTopic($topic_id)
{
	global $conn, $topic_name, $isEditingTopic, $topic_id;
	$sql = "SELECT * FROM topics WHERE id=$topic_id LIMIT 1";
	$result = mysqli_query($conn, $sql);
	$topic = mysqli_fetch_assoc($result);
	// set form values ($topic_name) on the form to be updated
	// 在要更新的表單上設置表單值（$ topic_name）
	$topic_name = $topic['name'];
}
function updateTopic($request_values)
{
	global $conn, $errors, $topic_name, $topic_id;
	$topic_name = esc($request_values['topic_name']);
	$topic_id = esc($request_values['topic_id']);
	// create slug: if topic is "Life Advice", return "life-advice" as slug
	// 創建子彈：如果主題為“生活建議”，則以“子彈”形式返回“生活建議”
	$topic_slug = makeSlug($topic_name);
	// validate form 驗證表格
	if (empty($topic_name)) {
		array_push($errors, "Topic name required");
	}
	// register topic if there are no errors in the form
	// 如果表格中沒有錯誤，請註冊主題
	if (count($errors) == 0) {
		$query = "UPDATE topics SET name='$topic_name', slug='$topic_slug' WHERE id=$topic_id";
		mysqli_query($conn, $query);

		$_SESSION['message'] = "Topic updated successfully";
		header('location: topics.php');
		exit(0);
	}
}
// delete topic 刪除主題
function deleteTopic($topic_id)
{
	global $conn;
	$sql = "DELETE FROM topics WHERE id=$topic_id";
	if (mysqli_query($conn, $sql)) {
		$_SESSION['message'] = "Topic successfully deleted";
		header("location: topics.php");
		exit(0);
	}
}






/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
* - Returns all admin users and their corresponding roles
*   返回所有管理員用戶及其相應角色
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
function getAdminUsers()
{
	global $conn, $roles;
	$sql = "SELECT * FROM users WHERE role IS NOT NULL";
	$result = mysqli_query($conn, $sql);
	$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

	return $users;
}
/* * * * * * * * * * * * * * * * * * * * *
* - Escapes form submitted value, hence, preventing SQL injection
*   轉義表單提交的值，從而防止SQL注入
* * * * * * * * * * * * * * * * * * * * * */
function esc(String $value)
{
	// bring the global db connect object into function
	// 使全局數據庫連接對像生效
	global $conn;
	// remove empty space sorrounding string
	// 刪除空格周圍的字符串
	$val = trim($value);
	$val = mysqli_real_escape_string($conn, $value);
	return $val;
}
// Receives a string like 'Some Sample String'
// 接收類似“ Some Sample String”的字符串
// and returns 'some-sample-string'
// 並返回“ some-sample-string”
function makeSlug(String $string)
{
	$string = strtolower($string);
	$slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
	return $slug;
}
