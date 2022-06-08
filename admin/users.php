<?php include('../config.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/admin_functions.php'); ?>
<?php
// Get all admin users from DB 從數據庫獲取所有管理員用戶
$admins = getAdminUsers();
$roles = ['Admin', 'Author'];
?>
<?php include(ROOT_PATH . '/admin/includes/head_section.php'); ?>
<title>Admin | Manage users</title>
</head>

<body>
	<!-- admin navbar 管理員導航欄-->
	<?php include(ROOT_PATH . '/admin/includes/navbar.php') ?>
	<div class="container content">
		<!-- Left side menu -->
		<?php include(ROOT_PATH . '/admin/includes/menu.php') ?>
		<!-- Middle form - to create and edit  中間形式-創建和編輯 -->
		<div class="action">
			<h1 class="page-title">Create/Edit Admin User</h1>

			<form method="post" action="<?php echo BASE_URL . 'admin/users.php'; ?>">

				<!-- validation errors for the form 表單的驗證錯誤 -->
				<?php include(ROOT_PATH . '/includes/errors.php') ?>

				<!-- if editing user, the id is required to identify that user -->
				<!-- 如果是編輯用戶，則需要id來標識該用戶 -->
				<?php if ($isEditingUser === true) : ?>
					<input type="hidden" name="admin_id" value="<?php echo $admin_id; ?>">
				<?php endif ?>

				<input type="text" name="username" value="<?php echo $username; ?>" placeholder="Username">
				<input type="email" name="email" value="<?php echo $email ?>" placeholder="Email">
				<input type="password" name="password" placeholder="Password">
				<input type="password" name="passwordConfirmation" placeholder="Password confirmation">
				<select name="role">
					<option value="" selected disabled>Assign role</option>
					<?php foreach ($roles as $key => $role) : ?>
						<option value="<?php echo $role; ?>"><?php echo $role; ?></option>
					<?php endforeach ?>
				</select>

				<!-- if editing user, display the update button instead of create button -->
				<!-- 如果是編輯用戶，則顯示更新按鈕而不是創建按鈕-->
				<?php if ($isEditingUser === true) : ?>
					<button type="submit" class="btn" name="update_admin">UPDATE</button>
				<?php else : ?>
					<button type="submit" class="btn" name="create_admin">Save User</button>
				<?php endif ?>
			</form>
		</div>
		<!-- // Middle form - to create and edit -->

		<!-- Display records from DB 顯示數據庫中的記錄 -->
		<div class="table-div">
			<!-- Display notification message 顯示通知消息 -->
			<?php include(ROOT_PATH . '/includes/messages.php') ?>

			<?php if (empty($admins)) : ?>
				<h1>No admins in the database.</h1>
			<?php else : ?>
				<table class="table">
					<thead>
						<th>N</th>
						<th>Admin</th>
						<th>Role</th>
						<th colspan="2">Action</th>
					</thead>
					<tbody>
						<?php foreach ($admins as $key => $admin) : ?>
							<tr>
								<td><?php echo $key + 1; ?></td>
								<td>
									<?php echo $admin['username']; ?>, &nbsp;
									<?php echo $admin['email']; ?>
								</td>
								<td><?php echo $admin['role']; ?></td>
								<td>
									<a class="fa fa-pencil btn edit" href="users.php?edit-admin=<?php echo $admin['id'] ?>">
									</a>
								</td>
								<td>
									<a class="fa fa-trash btn delete" href="users.php?delete-admin=<?php echo $admin['id'] ?>">
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