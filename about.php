<!-- The first include should be config.php  第一個include應該是config.php-->
<?php require_once('config.php') ?>
<?php require_once(ROOT_PATH . '/includes/registration_login.php') ?>
<?php require_once(ROOT_PATH . '/includes/head_section.php') ?>
<link href="static/css/about_styling.css" rel="stylesheet" type="text/css" />
<title>personal</title>
<?php
//抓留言板資料
$dbname = 'mes';
if (!$conn) {
	die("Could not connect: " . mysql_error());
}
mysqli_select_db($conn, $dbname);
mysqli_query($conn, "SET NAMES utf8");

$mes = "SELECT * FROM `mes`";
$mesresult = mysqli_query($conn, $mes) or die('MySQL query error');
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">

</head>

<body>
	<!-- navbar 導航欄 -->
	<div class="navbar">


		<div class="logo_div">
			<a href="index.php">
				<h1>MyBlog</h1>
			</a>
		</div>
		<ul>
			<li><a class="active" href="index.php">Home</a></li>
			<li><a href="member.php">Administration</a></li>
			<li><a href="about.php">About</a></li>
		</ul>
	</div>

	<div class="container">
		<!--<?php include(ROOT_PATH . '/includes/navbar.php') ?>-->
		<div id="logo">
			<h1><a href="#">About the Blog </a></h1>
		</div>
		<div id="page">
			<div id="content">
				<div id="welcome">
					<p style="margin: 0;"><img src="static/images/img02.jpg" alt="" width="160" height="200" class="left" /></p>
					<h2 class="title">Welcome here!</h2>
					<p><strong>This Blog</strong> is a free platform .
						The Blog was made using XAMP(X=Windows10，A=Apache，M=MySQL，P=Php).
						You can use any of the following functions here. <em>Enjoy :)</em> </p>
				</div>
				<!-- end #welcome -->
				<div style="clear: left;">
					<h2>Functional Service</h2>
					<div class="content">
						<h3>1.Member exclusive:Create and publish articles.</h3>
						<h3>2.Administrator exclusive:Have authority to manage all user data.</h3>
						<h3>3.If you want to be an administrator or author, leave a message to me.</h3>
						<br>
						<div class="container">
							<h1>Message board</h1>
							<form role="form" action="mes.php?method=add" method="post">
								<div class="form-group">
									<label for="title">Title</label>
									<input type="text" class="form-control" id="title" placeholder="title" name="title" />
								</div>
								<div class="form-group">
									<label for="content">Content</label>
									<input type="text" class="form-control" id="content" placeholder="content" name="content" />
								</div>
								<button type="submit" class="btn btn-primary">Add</button>
							</form>
							<?php
							while ($row = mysqli_fetch_array($mesresult)) {
							?>
								<div class="card">
									<h4 class="card-header">Title：<?php echo $row['title']; ?>
										<?php if (@in_array($_SESSION['user']['role'], ["Admin", "Author"])) { ?>
											<a href="mes.php?method=del&id=<?php echo $row["id"] ?>" class="btn btn-danger mybtn">刪除</a>

										<?php } ?>
									</h4>
									<div class="card-body">
										<h5 class="card-title">Author：<?php echo $row["uid"]; ?></h5>
										<p class="card-text">
											<?php echo $row["content"]; ?>
										</p>
									</div>
								</div>

							<?php
							}
							?>
						</div>
					</div>
				</div>

				<!-- end #content -->
				<div style="clear: both; height: 1px;"></div>
			</div>
		</div>
		<!-- end #page -->
		<div class="footerabout">
			<p>觀看人數:<br><?php View_Counter(); ?></p>
			<p>MyViewers &copy; <?php echo date('Y'); ?></p>
		</div>
		<!-- // footer -->

	</div>
	<!-- // container -->
</body>

</html>