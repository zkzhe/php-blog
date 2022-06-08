<?php
/* * * * * * * * * * * * * * *
* Returns all published posts
* 返回所有已發布的帖子
* * * * * * * * * * * * * * */
function getPublishedPosts()
{
	// use global $conn object in function
	// 在函數中使用全局$ conn對象
	global $conn;
	$sql = "SELECT * FROM posts WHERE published = true";
	$result = mysqli_query($conn, $sql);
	// fetch all posts as an associative array called $posts
	// 以名為$ posts的關聯數組的形式獲取所有帖子
	$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

	$final_posts = array();
	foreach ($posts as $post) {
		$post['topic'] = getPostTopic($post['id']);
		array_push($final_posts, $post);
	}
	//print_r($final_posts); 
	return $final_posts;
}
/* * * * * * * * * * * * * * *
* Receives a post id and
* Returns topic of the post
* 收到帖子ID和返回帖子的主題
* * * * * * * * * * * * * * */
function getPostTopic($post_id)
{
	global $conn;
	$sql = "SELECT * FROM topics WHERE id=
			(SELECT topic_id FROM post_topic WHERE post_id = $post_id) LIMIT 1";
	$result = mysqli_query($conn, $sql);
	$topic = mysqli_fetch_assoc($result);
	return $topic;
}



/* * * * * * * * * * * * * * * *
* Returns all posts under a topic
* 返回主題下的所有帖子
* * * * * * * * * * * * * * * * */
function getPublishedPostsByTopic($topic_id)
{
	global $conn;
	$sql = "SELECT * FROM posts ps 
			WHERE ps.id IN 
			(SELECT pt.post_id FROM post_topic pt 
				WHERE pt.topic_id=$topic_id GROUP BY pt.post_id 
				HAVING COUNT(1) = 1)";
	$result = mysqli_query($conn, $sql);
	// fetch all posts as an associative array called $posts
	$posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

	$final_posts = array();
	foreach ($posts as $post) {
		$post['topic'] = getPostTopic($post['id']);
		array_push($final_posts, $post);
	}
	return $final_posts;
}
/* * * * * * * * * * * * * * * *
* Returns topic name by topic id
* 按主題ID返回主題名稱
* * * * * * * * * * * * * * * * */
function getTopicNameById($id)
{
	global $conn;
	$sql = "SELECT name FROM topics WHERE id=$id";
	$result = mysqli_query($conn, $sql);
	$topic = mysqli_fetch_assoc($result);
	return $topic['name'];
}

/* * * * * * * * * * * * * * *
* Returns a single post
* 返回單個帖子
* * * * * * * * * * * * * * */
function getPost($slug)
{
	global $conn;
	// Get single post slug
	$post_slug = $_GET['post-slug'];
	$sql = "SELECT * FROM posts WHERE slug='$post_slug' AND published=true";
	$result = mysqli_query($conn, $sql);

	// fetch query results as associative array.
	$post = mysqli_fetch_assoc($result);
	if ($post) {
		// get the topic to which this post belongs
		$post['topic'] = getPostTopic($post['id']);
	}
	return $post;
}
/* * * * * * * * * * * *
*  Returns all topics
*  返回所有主題
* * * * * * * * * * * * */
function getAllTopics()
{
	global $conn;
	$sql = "SELECT * FROM topics";
	$result = mysqli_query($conn, $sql);
	$topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
	return $topics;
}
