<?php
    define('ACTION', isset($_GET['action']) ? $_GET['action'] : 'index');

    require_once 'includes/authentication.php';
    secure_session_start();

    if (isset($_POST['signInBtn']) && isset($_POST['username']) && isset($_POST['password'])) {
        // SIGN IN
        $username = $_POST['username'];
        $password = $_POST['password'];
        if (signin($username, $password))
            $successMsg = "Successfully signed in as '$username'.";
        else
            $errorMsg = "Username and password do not seem to be valid.";
    } elseif (isset($_POST['signOutBtn'])) {
        // SIGN OUT
        signout();
        $successMsg = "Successfully logged out.";
    }
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartulimardikas</title>

    <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

    <?php if (ACTION == 'edit'): ?>
    <link href="lib/jquery-ui-interactions/jquery-ui.min.css" rel="stylesheet">
    <?php endif ?>
</head>
<body>
	<!-- NAVIGATION BAR -->
	<nav class="navbar navbar-default" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.php?action=index">Kartulimardikas</a>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
                    <!-- VIEW -->
                    <?php if (ACTION == 'view'): ?><li class="active"><a href="#">View</a></li>
                    <?php else: ?><li><a href="index.php?action=view">View</a></li><?php endif ?>
                    <!-- EDIT -->
                    <?php if (ACTION == 'edit'): ?><li class="active"><a href="#">Edit</a></li>
                    <?php else: ?><li><a href="index.php?action=edit">Edit</a></li><?php endif ?>
				</ul>
				<form class="navbar-form navbar-right" role="form" method="post">
                    <?php if (isSignedIn()): ?>
                        Hello, <?= $_SESSION['username'] ?>!
                        <button type="submit" name="signOutBtn" href="#" class="btn btn-default">Sign out</button>
                    <?php else: ?>
                        <div class="form-group">
                            <label class="sr-only" for="username">Username</label>
                            <input class="form-control" name="username" placeholder="Username">
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="password">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password">
                        </div>
                        <button type="submit" name="signInBtn" href="#" class="btn btn-default">Sign in</button>
                        <a class="btn btn-link" href="index.php?action=register">Register</a>
                    <?php endif ?>
				</form>
			</div>
		</div>
	</nav>

	<div class="container">
        <?php if (isset($errorMsg)): ?>
        <div id="generalAlert" class="alert alert-danger alert-dismissible">
            <button id="generalAlertClose" type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <strong>Error!</strong> <?= $errorMsg ?>
        </div>
        <?php endif ?>
        <?php if (isset($successMsg)): ?>
        <div id="generalSuccess" class="alert alert-success alert-dismissible">
            <button id="generalSuccessClose" type="button" class="close"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <strong>Success!</strong> <?= $successMsg ?>
        </div>
        <?php endif ?>

        <?php switch(ACTION) {
            case 'edit': require_once 'partials/edit.phtml'; break;
            case 'view': require_once 'partials/view.phtml'; break;
            case 'register': require_once 'partials/register.phtml'; break;
            default: require_once 'partials/index.phtml';
        } ?>
	</div>

    <!--[if lt IE 9]>
    <script src="lib/html5shiv/html5shiv.min.js"></script>
    <script src="lib/respond/respond.min.js"></script>
    <![endif]-->
	<script src="lib/jquery/jquery.min.js"></script>
	<script src="lib/bootstrap/js/bootstrap.min.js"></script>
	<script src="js/common.js"></script>

<?php if (ACTION == 'edit'): ?>
    <script src="lib/jquery-ui-interactions/jquery-ui.min.js"></script>
    <script src="lib/js-keystroke/jquery.keystroke.min.js"></script>
    <script src="lib/jquery-base64/jquery.base64.js"></script>
    <script src="js/edit.js"></script>
    <script src="js/edit-var.js"></script>
    <script src="js/edit-step.js"></script>
<?php elseif (ACTION == 'register'): ?>
    <script src="js/register.js"></script>
<?php elseif (ACTION == 'view'): ?>
    <script src="js-gen/<?=$jsFile?>"></script>
<?php endif ?>
</body>
</html>
