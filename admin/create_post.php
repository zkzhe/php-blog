<?php include('../config.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/admin_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/post_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/head_section.php'); ?>
<!-- Get all topics 獲取所有主題-->
<?php $topics = getAllTopics();	?>
<title>Admin | Create Post</title>
</head>

<body>
	<!-- admin navbar 管理員導航欄-->
	<?php include(ROOT_PATH . '/admin/includes/navbar.php') ?>

	<div class="container content">
		<!-- Left side menu  左側選單-->
		<?php include(ROOT_PATH . '/admin/includes/menu.php') ?>

		<!-- Middle form - to create and edit  中間形式-創建和編輯-->
		<div class="action create-post-div">
			<h1 class="page-title">Create/Edit Post</h1>
			<form method="post" enctype="multipart/form-data" action="<?php echo BASE_URL . 'admin/create_post.php'; ?>">
				<!-- validation errors for the form 表單的驗證錯誤-->
				<?php include(ROOT_PATH . '/includes/errors.php') ?>

				<!-- if editing post, the id is required to identify that post 如果是編輯帖子，則需要id來標識該帖子-->

				<?php if ($isEditingPost === true) : ?>
					<input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
				<?php endif ?>

				<input type="text" name="title" value="<?php echo $title; ?>" placeholder="Title">
				<label style="float: left; margin: 5px auto 5px;">Featured image</label>
				<input type="file" name="featured_image">
				<textarea name="body" id="body" cols="30" rows="10"><?php echo $body; ?></textarea>
				<select name="topic_id">
					<option value="" selected disabled>Choose topic</option>
					<?php foreach ($topics as $topic) : ?>
						<option value="<?php echo $topic['id']; ?>">
							<?php echo $topic['name']; ?>
						</option>
					<?php endforeach ?>
				</select>

				<!-- Only admin users can view publish input field 僅管理員用戶可以查看發布輸入字段-->
				<?php if ($_SESSION['user']['role'] == "Admin") : ?>
					<!-- display checkbox according to whether post has been published or not 根據帖子是否已發布顯示複選框-->
					<?php if ($published == true) : ?>
						<label for="publish">
							Publish
							<input type="checkbox" value="1" name="publish" checked="checked">&nbsp;
						</label>
					<?php else : ?>
						<label for="publish">
							Publish
							<input type="checkbox" value="1" name="publish">&nbsp;
						</label>
					<?php endif ?>
				<?php endif ?>

				<!-- if editing post, display the update button instead of create button 如果是編輯帖子，則顯示更新按鈕而不是創建按鈕-->
				<?php if ($isEditingPost === true) : ?>
					<button type="submit" class="btn" name="update_post">UPDATE</button>
				<?php else : ?>
					<button type="submit" class="btn" name="create_post">Save Post</button>
				<?php endif ?>

			</form>
		</div>
		<!-- // Middle form - to create and edit -->
	</div>
</body>

</html>

<script>
	CKEDITOR.replace('body');
</script>