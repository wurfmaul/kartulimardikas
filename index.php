<?php
// setup environment
define('BASEDIR', __DIR__ . '/');

// deal with old browsers
// TODO: IE <= 8 not supported by jquery

// load configuration, helpers, authentication
require_once BASEDIR . 'config/config.php';
require_once BASEDIR . 'api/get-url.php';
require_once BASEDIR . 'includes/authentication.php';
require_once BASEDIR . "includes/dataModel.php";
$__model = new DataModel();

// setup and verify action
$__action = isset($_GET['action']) ? $_GET['action'] : DEFAULT_PAGE;
if (!file_exists(BASEDIR . "partials/$__action.phtml"))
    $__action = DEFAULT_PAGE;

// deal with authentication
secure_session_start();
// SIGN IN
if ((isset($_POST['signInBtn']) || isset($_POST['registerBtn'])) &&
    isset($_POST['username']) && isset($_POST['password'])
) {
    $username = $_POST['username'];
    if (signIn($username, $_POST['password'])) {
        $successMsg = sprintf($l10n['signed_in'], $username);
        // if the user has just been created
        if (isset($_POST['registerBtn'])) {
            $registerMsg = sprintf($l10n['user_created'], $username);
        }
    } else {
        $errorMsg = $l10n['credentials_invalid'];
    }

// SIGN OUT
} elseif (isset($_POST['signOutBtn'])) {
    signOut();
    $successMsg = $l10n['signed_out'];
}

if (isset($_POST['successMsg'])) {
    $successMsg = $_POST['successMsg'];
}
if (isset($_POST['errorMsg'])) {
    $errorMsg = $_POST['errorMsg'];
}

/** @var int $__uid Currently signed in user or false if not signed in. */
$__uid = isSignedIn();

/** @var int $__aid Current algorithm id or false if no algorithm selected. */
$__aid = false;
if (isset($_GET['aid'])) {
    $__aid = $_GET['aid'];
    $__algorithm = $__model->fetchAlgorithm($__aid);
    if ($__algorithm) {
        /** @var bool $__owner True if the signed in user is the owner of the current algorithm. */
        $__owner = $__algorithm->uid === $__uid;
        /** @var bool $__public True if the algorithm is defined public. */
        $__public = !is_null($__algorithm->date_publish);
    }
}

// make action permanent for this session
define('ACTION', $__action);

// define where the user should be taken after signing out
$signOutAction = "";
if ($__aid && $__algorithm) {
    // if the algorithm is private -> redirect to home action
    if (!$__public) {
        $signOutAction = url();
        // if the algorithm is public -> redirect to view action
    } elseif ($__action === 'edit' || $__action === 'settings') {
        $signOutAction = url(['action' => 'view', 'aid' => $__aid]);
    }
}
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
                <li<?php if (ACTION === 'index'): ?> class="active"<?php endif ?>>
                    <a href="<?= url(['action' => 'index']) ?>"><?= $l10n['index'] ?></a>
                </li>
                <li<?php if (ACTION === 'new'): ?> class="active"<?php endif ?>>
                    <a href="<?= url(['action' => 'new']) ?>"><?= $l10n['new'] ?></a>
                </li>
            </ul>
            <?php if ($__uid): ?>
                <form class="navbar-form navbar-right" role="form" method="post" action="<?= $signOutAction ?>">
                    <span>
                        <?= sprintf($l10n['welcome'],
                            '<a href="' . url(['action' => 'user']) . '">' . $_SESSION['username'] . '</a>') ?>
                    </span>
                    <button type="submit" name="signOutBtn" class="btn btn-default"><?= $l10n['sign_out'] ?></button>
                </form>
            <?php else: ?>
                <form class="navbar-form navbar-right" role="form" method="post">
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
                </form>
            <?php endif ?>
        </div>
    </div>
</nav>

<div class="container">
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
            <strong><?= $l10n['success'] ?></strong>
            <?php if (isset($registerMsg)): ?>
                <ul>
                    <li><?= $registerMsg ?></li>
                    <li><?= $successMsg ?></li>
                </ul>
            <?php else: ?>
                <?= $successMsg ?>
            <?php endif ?>
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
<?php $__model->close() ?>