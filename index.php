<?php
    // setup environment
    define('BASEDIR', __DIR__ . '/');
    define('ACTION', isset($_GET['action']) ? $_GET['action'] : 'index');
    require_once BASEDIR . 'config/config.php';
    require_once BASEDIR . 'includes/authentication.php';
    secure_session_start();

    // deal with authentication
    if (isset($_POST['signInBtn']) && isset($_POST['username']) && isset($_POST['password'])) {
        // SIGN IN
        $username = $_POST['username'];
        $password = $_POST['password'];
        if (signin($username, $password))
            $successMsg = sprintf($l10n['signed_in'], $username);
        else
            $errorMsg = $l10n['credentials_invalid'];
    } elseif (isset($_POST['signOutBtn'])) {
        // SIGN OUT
        signout();
        $successMsg = $l10n['signed_out'];
    }
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= PROJECT_NAME ?></title>

    <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

    <?php if (ACTION == 'edit'): ?>
    <link href="lib/jquery-ui-interactions/jquery-ui.min.css" rel="stylesheet" />
    <link href="css/edit.css" rel="stylesheet" />
    <?php endif ?>
</head>
<body>
	<!-- NAVIGATION BAR -->
	<nav class="navbar navbar-default" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only"><?= $l10n['toggle_nav'] ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.php?action=index"><?= PROJECT_NAME ?></a>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
                    <!-- VIEW -->
                    <?php if (ACTION == 'view'): ?><li class="active"><a href="#"><?= $l10n['view'] ?></a></li>
                    <?php else: ?><li><a href="index.php?action=view"><?= $l10n['view'] ?></a></li><?php endif ?>
                    <!-- EDIT -->
                    <?php if (ACTION == 'edit'): ?><li class="active"><a href="#"><?= $l10n['edit'] ?></a></li>
                    <?php else: ?><li><a href="index.php?action=edit"><?= $l10n['edit'] ?></a></li><?php endif ?>
				</ul>
				<form class="navbar-form navbar-right" role="form" method="post">
                    <?php if (isSignedIn()): ?>
                        <?= sprintf($l10n['welcome'], $_SESSION['username']) ?>!
                        <button type="submit" name="signOutBtn" class="btn btn-default"><?= $l10n['sign_out'] ?></button>
                    <?php else: ?>
                        <div class="form-group">
                            <label class="sr-only" for="username"><?= $l10n['username'] ?></label>
                            <input class="form-control" name="username" placeholder="<?= $l10n['username'] ?>">
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="password"><?= $l10n['password'] ?></label>
                            <input type="password" class="form-control" name="password" placeholder="<?= $l10n['password'] ?>">
                        </div>
                        <button type="submit" name="signInBtn" class="btn btn-default"><?= $l10n['sign_in'] ?></button>
                        <a class="btn btn-link" href="index.php?action=register"><?= $l10n['register'] ?></a>
                    <?php endif ?>
				</form>
			</div>
		</div>
	</nav>

	<div class="container">
        <?php if (isset($errorMsg)): ?>
        <div id="generalAlert" class="alert alert-danger alert-dismissible">
            <button id="generalAlertClose" type="button" class="close">
                <span aria-hidden="true">&times;</span><span class="sr-only"><?= $l10n['close'] ?></span>
            </button>
            <strong><?= $l10n['error'] ?></strong> <?= $errorMsg ?>
        </div>
        <?php endif ?>
        <?php if (isset($successMsg)): ?>
        <div id="generalSuccess" class="alert alert-success alert-dismissible">
            <button id="generalSuccessClose" type="button" class="close">
                <span aria-hidden="true">&times;</span><span class="sr-only"><?= $l10n['close'] ?></span>
            </button>
            <strong><?= $l10n['success'] ?></strong> <?= $successMsg ?>
        </div>
        <?php endif ?>

        <?php switch(ACTION) {
            case 'edit':     require_once BASEDIR . 'partials/edit.phtml'; break;
            case 'view':     require_once BASEDIR . 'partials/view.phtml'; break;
            case 'register': require_once BASEDIR . 'partials/register.phtml'; break;
            default:         require_once BASEDIR . 'partials/index.phtml';
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
