<?php include('../config.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/admin_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/head_section.php'); ?>
<title>Admin | Dashboard</title>
</head>
<style type="text/css">
	#DIV1 {
		width: 47%;
		line-height: 50px;
		padding: 20px;

		margin-right: 10px;
		float: left;
	}

	#DIV2 {
		width: 40%;
		line-height: 50px;
		padding: 20px;
		margin: 0px auto;
		margin-right: 10px;
		float: left;

	}
</style>

<body>
	<div class="header">
		<div id="DIV1" class="logo">
			<a href="<?php echo BASE_URL . 'admin/dashboard.php' ?>">
				<h1>LifeBlog - Admin</h1>
			</a>
		</div>
		<div id="DIV2" class="logo">
			<a href="<?php echo BASE_URL . 'index.php' ?>">
				<h1>Home</h1>
			</a>
		</div>
		<?php if (isset($_SESSION['user'])) : ?>
			<div class="user-info">
				<span><?php echo $_SESSION['user']['username'] ?></span> &nbsp; &nbsp;
				<a href="<?php echo BASE_URL . '/logout.php'; ?>" class="logout-btn">logout</a>
			</div>
		<?php endif ?>
	</div>
	<div class="container dashboard">
		<h1>Welcome</h1>
		<div class="stats">
			<?php if ($_SESSION['user']['role'] == "Admin") {
				echo '<a href="users.php" class="first">';
				echo '<span></span> <br>';
				echo '<span>Newly registered users</span>';
				echo '</a>';
			} ?>
			<a href="posts.php">
				<span></span> <br>
				<span>Published posts</span>
			</a>
			<a>
				<span></span> <br>
				<span>Published comments</span>
			</a>
		</div>
		<br><br><br>
		<div class="buttons">
			<?php if ($_SESSION['user']['role'] == "Admin") {
				echo "<a href=users.php>Add Users</a>";
			} ?>
			<a href="posts.php">Add Posts</a>
		</div>
	</div>
</body>

</html>