<?php include('../config.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/admin_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/head_section.php'); ?>
<!-- Get all topics from DB 從數據庫獲取所有主題-->
<?php $topics = getAllTopics();	?>
<title>Admin | Manage Topics</title>
</head>

<body>
	<!-- admin navbar 管理員導航欄-->
	<?php include(ROOT_PATH . '/admin/includes/navbar.php') ?>
	<div class="container content">
		<!-- Left side menu 左側選單-->
		<?php include(ROOT_PATH . '/admin/includes/menu.php') ?>

		<!-- Middle form - to create and edit 中間形式-創建和編輯-->
		<div class="action">
			<h1 class="page-title">Create/Edit Topics</h1>
			<form method="post" action="<?php echo BASE_URL . 'admin/topics.php'; ?>">
				<!-- validation errors for the form 表單的驗證錯誤-->
				<?php include(ROOT_PATH . '/includes/errors.php') ?>
				<!-- if editing topic, the id is required to identify that topic 如果是編輯主題，則需要ID來標識該主題-->
				<?php if ($isEditingTopic === true) : ?>
					<input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>">
				<?php endif ?>
				<input type="text" name="topic_name" value="<?php echo $topic_name; ?>" placeholder="Topic">
				<!-- if editing topic, display the update button instead of create button -->
				<!-- 如果正在編輯主題，則顯示更新按鈕而不是創建按鈕-->
				<?php if ($isEditingTopic === true) : ?>
					<button type="submit" class="btn" name="update_topic">UPDATE</button>
				<?php else : ?>
					<button type="submit" class="btn" name="create_topic">Save Topic</button>
				<?php endif ?>
			</form>
		</div>
		<!-- // Middle form - to create and edit -->

		<!-- Display records from DB 顯示數據庫中的記錄 -->
		<div class="table-div">
			<!-- Display notification message 顯示通知消息 -->
			<?php include(ROOT_PATH . '/includes/messages.php') ?>
			<?php if (empty($topics)) : ?>
				<h1>No topics in the database.</h1>
			<?php else : ?>
				<table class="table">
					<thead>
						<th>N</th>
						<th>Topic Name</th>
						<th colspan="2">Action</th>
					</thead>
					<tbody>
						<?php foreach ($topics as $key => $topic) : ?>
							<tr>
								<td><?php echo $key + 1; ?></td>
								<td><?php echo $topic['name']; ?></td>
								<td>
									<a class="fa fa-pencil btn edit" href="topics.php?edit-topic=<?php echo $topic['id'] ?>">
									</a>
								</td>
								<td>
									<a class="fa fa-trash btn delete" href="topics.php?delete-topic=<?php echo $topic['id'] ?>">
									</a>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			<?php endif ?>
		</div>
		<!-- // Display records from DB -->
	</div>
</body>

</html>