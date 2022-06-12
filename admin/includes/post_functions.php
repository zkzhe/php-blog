<?php 
// Post variables 發布變量
$post_id = 0; 
$isEditingPost = false;
$published = 0;
$title = "";
$post_slug = "";
$body = "";
$featured_image = "";
$post_topic = "";

/* - - - - - - - - - - 
-  Post functions 發布功能
- - - - - - - - - - -*/
// get all posts from DB 從數據庫獲取所有帖子
function getAllPosts()
{
	global $conn;
	
	// Admin can view all posts 管理員可以查看所有帖子
	// Author can only view their posts 作者只能查看其帖子
	if ($_SESSION['user']['role'] == "Admin") {
		$sql = "SELECT * FROM posts";
	} elseif ($_SESSION['user']['role'] == "Author") {
		$user_id = $_SESSION['user']['id'];
		$sql = "SELECT * FROM posts WHERE user_id=$user_id";
	}
	$result = mysqli_query($conn, $sql);
	$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

	$final_posts = array();
	foreach ($posts as $post) {
		$post['author'] = getPostAuthorById($post['user_id']);
		array_push($final_posts, $post);
	}
	return $final_posts;
}
// get the author/username of a post 獲取帖子的作者/用戶名
function getPostAuthorById($user_id)
{
	global $conn;
	$sql = "SELECT username FROM users WHERE id=$user_id";
	$result = mysqli_query($conn, $sql);
	if ($result) {
		// return username 返回用戶名
		return mysqli_fetch_assoc($result)['username'];
	} else {
		return null;
	}
}


/* - - - - - - - - - - 
-  Post actions 發布動作
- - - - - - - - - - -*/
// if user clicks the create post button 如果用戶單擊創建帖子按鈕
if (isset($_POST['create_post'])) { createPost($_POST); }
// if user clicks the Edit post button
if (isset($_GET['edit-post'])) {
	$isEditingPost = true;
	$post_id = $_GET['edit-post'];
	editPost($post_id);
}
// if user clicks the update post button
if (isset($_POST['update_post'])) {
	updatePost($_POST);
}
// if user clicks the Delete post button
if (isset($_GET['delete-post'])) {
	$post_id = $_GET['delete-post'];
	deletePost($post_id);
}

/* - - - - - - - - - - 
-  Post functions 發布功能
- - - - - - - - - - -*/
function createPost($request_values)
	{
		global $conn, $errors, $title, $featured_image, $topic_id, $body, $published;
		$title = esc($request_values['title']);
		$body = htmlentities(esc($request_values['body']));
		if (isset($request_values['topic_id'])) {
			$topic_id = esc($request_values['topic_id']);
		}
		if (isset($request_values['publish'])) {
			$published = esc($request_values['publish']);
		}
		// create slug: if title is "The Storm Is Over", return "the-storm-is-over" as slug
        // 創建子彈：如果標題為“風暴結束”，則將“ the-storm-is-over”返回為子彈
		$post_slug = makeSlug($title);
		// validate form 驗證表格
		if (empty($title)) { array_push($errors, "Post title is required"); }
		if (empty($body)) { array_push($errors, "Post body is required"); }
		if (empty($topic_id)) { array_push($errors, "Post topic is required"); }
		// Get image name 獲取圖像名稱
	  	$featured_image = $_FILES['featured_image']['name'];
	  	if (empty($featured_image)) { array_push($errors, "Featured image is required"); }
	  	// image file directory 圖像文件目錄
	  	$target = "../static/images/" . basename($featured_image);
	  	if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
	  		array_push($errors, "Failed to upload image. Please check file settings for your server");
	  	}
		// Ensure that no post is saved twice. 確保沒有帖子被保存兩次。
		$post_check_query = "SELECT * FROM posts WHERE slug='$post_slug' LIMIT 1";
		$result = mysqli_query($conn, $post_check_query);

		if (mysqli_num_rows($result) > 0) { // if post exists 如果帖子存在
			array_push($errors, "A post already exists with that title.");
		}
		// create post if there are no errors in the form 如果表單中沒有錯誤，請創建帖子
		if (count($errors) == 0) {
			$user_id = $_SESSION['user']['id'];
			$query = "INSERT INTO posts (user_id, title, slug, image, body, published, created_at, updated_at) VALUES('$user_id', '$title', '$post_slug', '$featured_image', '$body', $published, now(), now())";
			if(mysqli_query($conn, $query)){ // if post created successfully 如果帖子創建成功
				$inserted_post_id = mysqli_insert_id($conn);
				// create relationship between post and topic 建立帖子與主題之間的關係
				$sql = "INSERT INTO post_topic (post_id, topic_id) VALUES($inserted_post_id, $topic_id)";
				mysqli_query($conn, $sql);

				$_SESSION['message'] = "Post created successfully";
				header('location: posts.php');
				exit(0);
			}
		}
	}

	/* * * * * * * * * * * * * * * * * * * * *
	* - Takes post id as parameter           將帖子ID作為參數
	* - Fetches the post from database       從數據庫中獲取帖子
	* - sets post fields on form for editing 在表單上設置發布字段以進行編輯
	* * * * * * * * * * * * * * * * * * * * * */
	function editPost($role_id)
	{
		global $conn, $title, $post_slug, $body, $published, $isEditingPost, $post_id;
		$sql = "SELECT * FROM posts WHERE id=$role_id LIMIT 1";
		$result = mysqli_query($conn, $sql);
		$post = mysqli_fetch_assoc($result);
		// set form values on the form to be updated 在要更新的表單上設置表單值
		$title = $post['title'];
		$body = $post['body'];
		$published = $post['published'];
	}

	function updatePost($request_values)
	{
		global $conn, $errors, $post_id, $title, $featured_image, $topic_id, $body, $published;

		$title = esc($request_values['title']);
		$body = esc($request_values['body']);
		$post_id = esc($request_values['post_id']);
		if (isset($request_values['topic_id'])) {
			$topic_id = esc($request_values['topic_id']);
		}
		// create slug: if title is "The Storm Is Over", return "the-storm-is-over" as slug
        // 創建子彈：如果標題為“風暴結束”，則將“ the-storm-is-over”返回為子彈
		$post_slug = makeSlug($title);

		if (empty($title)) { array_push($errors, "Post title is required"); }
		if (empty($body)) { array_push($errors, "Post body is required"); }
		// if new featured image has been provided 如果提供了新的特色圖片
		if (isset($_POST['featured_image'])) {
			// Get image name 獲取圖像名稱
		  	$featured_image = $_FILES['featured_image']['name'];
		  	// image file directory 圖像文件目錄
		  	$target = "../static/images/" . basename($featured_image);
		  	if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
		  		array_push($errors, "Failed to upload image. Please check file settings for your server");
		  	}
		}

		// register topic if there are no errors in the form
        // 如果表格中沒有錯誤，請註冊主題
		if (count($errors) == 0) {
			$query = "UPDATE posts SET title='$title', slug='$post_slug', views=0, image='$featured_image', body='$body', published=$published, updated_at=now() WHERE id=$post_id";
			// attach topic to post on post_topic table
            // 將主題附加到post_topic表上
			if(mysqli_query($conn, $query)){ // if post created successfully
                                             // 如果帖子創建成功
				if (isset($topic_id)) {
					$inserted_post_id = mysqli_insert_id($conn);
					// create relationship between post and topic
                    // 建立帖子與主題之間的關係
					$sql = "INSERT INTO post_topic (post_id, topic_id) VALUES($inserted_post_id, $topic_id)";
					mysqli_query($conn, $sql);
					$_SESSION['message'] = "Post created successfully";
					header('location: posts.php');
					exit(0);
				}
			}
			$_SESSION['message'] = "Post updated successfully";
			header('location: posts.php');
			exit(0);
		}
	}
	// delete blog post 刪除博客文章
	function deletePost($post_id)
	{
		global $conn;
		$sql = "DELETE FROM posts WHERE id=$post_id";
		if (mysqli_query($conn, $sql)) {
			$_SESSION['message'] = "Post successfully deleted";
			header("location: posts.php");
			exit(0);
		}
	}

	// if user clicks the publish post button
    // 如果用戶單擊“發布帖子”按鈕
	if (isset($_GET['publish']) || isset($_GET['unpublish'])) {
		$message = "";
		if (isset($_GET['publish'])) {
			$message = "Post published successfully";
			$post_id = $_GET['publish'];
			$status = '1';
		} else if (isset($_GET['unpublish'])) {
			$message = "Post successfully unpublished";
			$post_id = $_GET['unpublish'];
			$status = '0';
		}
		togglePublishPost($post_id, $message, $status);
	}
	// delete blog post 刪除博客文章
	function togglePublishPost($post_id, $message,string $status)
	{
		global $conn;
		$sql = "UPDATE posts SET published=$status WHERE id=$post_id";
		
		if (mysqli_query($conn, $sql)) {
			$_SESSION['message'] = $message;
			header("location: posts.php");
			exit(0);
		}
	}
