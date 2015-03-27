<?php
// setup environment
define('BASEDIR', __DIR__ . '/');

// deal with old browsers
// TODO: IE <= 8 not supported by jquery

// load configuration, helpers, authentication
require_once BASEDIR . 'config/config.php';
require_once BASEDIR . 'api/get-url.php';
require_once BASEDIR . 'includes/authentication.php';

// setup and verify action
$_action = isset($_GET['action']) ? $_GET['action'] : DEFAULT_PAGE;
if (!file_exists(BASEDIR . "partials/$_action.phtml"))
    $_action = DEFAULT_PAGE;

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
    $successMsg = $l10n['signed_out']; // TODO: more beautiful system!
    if ($_action == 'edit' || $_action == 'settings')
        $_action = 'view';
}

// make it certain, forever
define('ACTION', $_action);
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= PROJECT_NAME ?></title>

    <link href="<?= BOOTSTRAP_CSS_PATH ?>" rel="stylesheet">
    <link href="css/common.css" rel="stylesheet">
    <?php if (file_exists('css/' . ACTION . '.css')): ?>
        <link href="css/<?= ACTION ?>.css" rel="stylesheet"/>
    <?php endif ?>
</head>
<body>
<!-- NAVIGATION BAR -->
<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                <span class="sr-only"><?= $l10n['toggle_nav'] ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= url(['action' => 'home']) ?>"><?= PROJECT_NAME ?></a>
        </div>
        <div class="collapse navbar-collapse" id="navbar">
            <ul class="nav navbar-nav">
                <li<?php if (ACTION == 'index'): ?> class="active"<?php endif ?>>
                    <a href="<?= url(['action' => 'index']) ?>"><?= $l10n['index'] ?></a>
                </li>
                <li<?php if (ACTION == 'new'): ?> class="active"<?php endif ?>>
                    <a href="<?= url(['action' => 'new']) ?>"><?= $l10n['new'] ?></a>
                </li>
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
                        <input type="password" class="form-control" name="password"
                               placeholder="<?= $l10n['password'] ?>">
                    </div>
                    <button type="submit" name="signInBtn" class="btn btn-default"><?= $l10n['sign_in'] ?></button>
                    <a class="btn btn-link" href="<?= url(['action' => 'register']) ?>"><?= $l10n['register'] ?></a>
                <?php endif ?>
            </form>
        </div>
    </div>
</nav>

<div class="container">
    <?php if (isset($_GET['aid'])): $aid = $_GET['aid'] ?>
        <?php if (isSignedIn()): // FIXME: only for owners! ?>
            <!-- NAVIGATION MENU FOR ALGORITHMS -->
            <ul class="nav nav-tabs">
                <li role="presentation"<?php if (ACTION == 'view'): ?> class="active"<?php endif ?>>
                    <a href="<?= url(['action' => 'view', 'aid' => $aid]) ?>"><?= $l10n['view'] ?></a>
                </li>
                <li role="presentation"<?php if (ACTION == 'edit'): ?> class="active"<?php endif ?>>
                    <a href="<?= url(['action' => 'edit', 'aid' => $aid]) ?>"><?= $l10n['edit'] ?></a>
                </li>
                <li role="presentation"<?php if (ACTION == 'settings'): ?> class="active"<?php endif ?>>
                    <a href="<?= url(['action' => 'settings', 'aid' => $aid]) ?>"><?= $l10n['settings'] ?></a>
                </li>
            </ul>
        <?php endif ?>
        <!-- The current AID for jquery to use -->
        <div id="aid" data-val="<?= $aid ?>" style="display: none"></div>
    <?php endif ?>
    <!-- The current ACTION for jquery to use -->
    <div id="action" data-val="<?= ACTION ?>" style="display: none"></div>

    <?php if (isset($errorMsg)): ?>
        <!-- MESSAGE BOX FOR ERRORS -->
        <div id="generalAlert" class="alert alert-danger alert-dismissible">
            <button id="generalAlertClose" type="button" class="close">
                <span aria-hidden="true">&times;</span><span class="sr-only"><?= $l10n['close'] ?></span>
            </button>
            <strong><?= $l10n['error'] ?></strong> <?= $errorMsg ?>
        </div>
    <?php endif ?>

    <?php if (isset($successMsg)): ?>
        <!-- MESSAGE BOX FOR SUCCESSES -->
        <div id="generalSuccess" class="alert alert-success alert-dismissible">
            <button id="generalSuccessClose" type="button" class="close">
                <span aria-hidden="true">&times;</span><span class="sr-only"><?= $l10n['close'] ?></span>
            </button>
            <strong><?= $l10n['success'] ?></strong> <?= $successMsg ?>
        </div>
    <?php endif ?>

    <!-- PAGE CONTENT BEGIN -->
    <?php require_once BASEDIR . 'partials/' . ACTION . '.phtml' ?>
    <!-- PAGE CONTENT END -->
</div>

<script type="text/javascript" src="<?= JQUERY_PATH ?>"></script>
<script type="text/javascript" src="<?= BOOTSTRAP_JS_PATH ?>"></script>
<script type="text/javascript" src="js/common.js"></script>
<?php if (ACTION === 'edit' || ACTION === 'view'): ?>
    <script type="text/javascript" src="js/algorithm.js"></script>
<?php endif ?>
<?php if (file_exists('js/' . ACTION . '.js')): ?>
    <script type="text/javascript" src="js/<?= ACTION ?>.js"></script>
<?php endif ?>
<?php if (ACTION === 'edit'): ?>
    <script type="text/javascript" src="<?= JQUERYUI_JS_PATH ?>"></script>
<?php endif ?>
</body>
</html>
