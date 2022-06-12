<?php

	// variable declaration 變量聲明
	$username = "";
	$email    = "";
	$errors = array();

	// REGISTER USER 註冊用戶
	if (isset($_POST['reg_user'])) {
		// receive all input values from the form 從表單接收所有輸入值
		$username = esc($_POST['username']);
		$email = esc($_POST['email']);
		$password_1 = esc($_POST['password_1']);
		$password_2 = esc($_POST['password_2']);

		// form validation: ensure that the form is correctly filled 表單驗證：確保正確填寫表單
		if (empty($username)) {  array_push($errors, "Uhmm...We gonna need your username"); }
		if (empty($email)) { array_push($errors, "Oops.. Email is missing"); }
		if (empty($password_1)) { array_push($errors, "uh-oh you forgot the password"); }
		if ($password_1 != $password_2) { array_push($errors, "The two passwords do not match");}

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
		// register user if there are no errors in the form 如果表格中沒有錯誤，請註冊用戶
		if (count($errors) == 0) {
			$password = md5($password_1);//encrypt the password before saving in the database
                                         //保存在數據庫中之前先對密碼進行加密
			$query = "INSERT INTO users (username, email, password, created_at, updated_at)
					  VALUES('$username', '$email', '$password', now(), now())";
			mysqli_query($conn, $query);

			// get id of created user 獲取創建用戶的ID
			$reg_user_id = mysqli_insert_id($conn);

			// put logged in user into session array 將已登錄的用戶放入會話數組
			$_SESSION['user'] = getUserById($reg_user_id);

			// if user is admin, redirect to admin area 如果用戶是管理員，請重定向到管理員區域
			if ( in_array($_SESSION['user']['role'], ["Admin", "Author"])) {
				$_SESSION['message'] = "You are now logged in";
				// redirect to admin area 重定向到管理區域
				header('location: ' . BASE_URL . 'admin/dashboard.php');
				exit(0);
			} else {
				$_SESSION['message'] = "You are now logged in";
				// redirect to public area 重定向到公共區域
				header('location: index.php');
				exit(0);
			}
		}
	}

	// LOG USER IN 登錄用戶
	if (isset($_POST['login_btn'])) {
		$username = esc($_POST['username']);
		$password = esc($_POST['password']);

		if (empty($username)) { array_push($errors, "Username required"); }
		if (empty($password)) { array_push($errors, "Password required"); }
		if (empty($errors)) {
			$password = md5($password); // encrypt password
			$sql = "SELECT * FROM users WHERE username='$username' and password='$password' LIMIT 1";

			$result = mysqli_query($conn, $sql);
			if (mysqli_num_rows($result) > 0) {
				// get id of created user 獲取創建用戶的ID
				$reg_user_id = mysqli_fetch_assoc($result)['id'];

				// put logged in user into session array 將已登錄的用戶放入會話數組
				$_SESSION['user'] = getUserById($reg_user_id);

				// if user is admin, redirect to admin area 如果用戶是管理員，請重定向到管理員區域
				if ( in_array($_SESSION['user']['role'], ["Admin", "Author"])) {
					$_SESSION['message'] = "You are now logged in";
					// redirect to admin area 重定向到管理區域
					header('location: admin/dashboard.php');
				} else {
					$_SESSION['message'] = "You are now logged in";
					// redirect to public area 重定向到公共區域
					header('location: index.php');
					exit(0);
				}
			} else {
				array_push($errors, 'Wrong credentials');
			}
		}
	}
	// escape value from form 從表格中逃脫價值
	function esc(String $value)
	{
		// bring the global db connect object into function
        //使全局數據庫連接對像生效
		global $conn;

		$val = trim($value); // remove empty space sorrounding string
                             // 移出空白空間環繞字符串
		$val = mysqli_real_escape_string($conn, $value);

		return $val;
	}
	// Get user info from user id 從用戶ID獲取用戶信息
	function getUserById($id)
	{
		global $conn;
		$sql = "SELECT * FROM users WHERE id=$id LIMIT 1";

		$result = mysqli_query($conn, $sql);
		$user = mysqli_fetch_assoc($result);

		// returns user in an array format: 以數組格式返回用戶：
		// ['id'=>1 'username' => 'Awa', 'email'=>'a@a.com', 'password'=> 'mypass']
		return $user;
	}
