<!-- The first include should be config.php  第一個include應該是config.php-->
<?php require_once('config.php') ?>
<?php require_once(ROOT_PATH . '/includes/registration_login.php') ?>
<?php require_once(ROOT_PATH . '/includes/head_section.php') ?>
<link href="static/css/member_styling.css" rel="stylesheet" type="text/css" />

<!--判斷是否登入了，沒有會返回登入頁面*/-->
<?php if (!isset($_SESSION['user']['username'])) {
	header('location: login.php');
}
?>
<?php if (@in_array($_SESSION['user']['role'], ["Admin", "Author"])) {
	header('location: ' . BASE_URL . 'admin/dashboard.php');
}
?>

<title>personal</title>

</head>

<body>
	<!-- navbar 導航欄 -->
	<div class="container">
		<?php include(ROOT_PATH . '/includes/navbar.php') ?>
		<div id="logo">
			<h1><a href="#">Member profile</a></h1>
			<h2><span>Option</span></h2>
		</div>
		<div id="page">
			<div id="sidebar">
				<div id="menu">
					<h2>Navigation Menu</h2>
					<ul>
						<form method="post" action="logout.php">
							<button type="submit" class="btn" name="reg_user">Manage articles</button>
						</form>
						<!--	<li><a href="#" accesskey="4" title="">Manage articles</a></li>
				<div style="width: 10%; margin: 20px auto;">
					<form method="post" action="logout.php" >
						<button type="submit" class="btn" name="reg_user">Logout</button>
					</form>
				</div>
			</ul>
		</div>
		<!-- end #menu -->

				</div>
				<div id="content">
					<div id="welcome">
						<p style="margin: 0;"><img src="static/images/user.jpg" alt="" width="160" height="200" class="left" /></p>
						<h2 class="title">Username: <?php echo $_SESSION['user']['username'] ?> </h2>
						<p><strong>Member profile</strong> is a free template from <a href="http://templated.co" rel="nofollow">TEMPLATED</a> released under the <a href="http://templated.co/license">Creative Commons Attribution License</a>. The photos are from <a href="http://www.pdphoto.org/">PDPhoto.org</a>. You're free to use it for both commercial or personal use. We only ask that you credit us in some way. <em>Enjoy :)</em></p>
					</div>
					<div class="action">
						<h1 class="page-title">Edit</h1>

						<form method="post" action="<?php echo BASE_URL . 'admin/users.php'; ?>">
							<input type="email" name="email" value="<?php echo $email ?>" placeholder="Email">
							<input type="password" name="password" placeholder="Password">
							<input type="password" name="passwordConfirmation" placeholder="Password confirmation">

							<button type="submit" class="btn" name="create_admin">Update</button>

						</form>
					</div>

					<!-- end #welcome -->

				</div>

				<!-- end #content -->

				<!-- end #sidebar -->
				<div style="clear: both; height: 1px;"></div>
			</div>
			<!-- end #page -->
			<?php include(ROOT_PATH . '/includes/footer.php'); ?>