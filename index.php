<?php
// setup environment
define('BASEDIR', __DIR__ . '/');
require_once(BASEDIR . 'includes/authentication.php');
// deal with authentication
secure_session_start();

// load configuration, helpers
require_once(BASEDIR . 'includes/settings.php');
require_once(BASEDIR . 'includes/dataModel.php');
require_once(BASEDIR . 'includes/helper/urlHelper.php');
require_once(BASEDIR . 'includes/helper/browserHelper.php');

global $l10n;
$__model = new DataModel();

// setup and verify action
$__action = isset($_GET['action']) ? $_GET['action'] : DEFAULT_PAGE;
if (!file_exists(BASEDIR . "partials/$__action.phtml"))
    $__action = DEFAULT_PAGE;
define('ACTION', $__action);

// SIGN IN
if ((isset($_POST['signInBtn']) || isset($_POST['registerBtn'])) && isset($_POST['username'], $_POST['password'])) {
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

/**
 * @var int $__uid Currently signed in user or false if not signed in.
 * @var int $__rights The user rights of the currently signed in user (user => 0, admin => 1, super-admin => 2).
 */
$__uid = isSignedIn();
$__rights = $__uid ? $__model->fetchUser($__uid)->rights : 0;

// DEAL WITH NEW ALGORITHMS
if (ACTION === 'new' && $__uid) {
    if (isset($_GET['aid'])) {
        // clone existing algorithm
        $aid = $__model->cloneAlgorithm($_GET['aid'], $__uid);
    } else {
        // create a new algorithm
        $aid = $__model->insertAlgorithm($__uid);
    }
    // redirect to the edit-page
    header("Location:" . url(['action' => 'edit', 'aid' => $aid], false, false));
    die();
}

// REDIRECT MESSAGES
if (isset($_POST['successMsg'])) {
    $successMsg = $_POST['successMsg'];
}
if (isset($_POST['errorMsg'])) {
    $errorMsg = $_POST['errorMsg'];
}

// ALGORITHM SETTINGS
/**
 * @var int $__aid Current algorithm id or false if no algorithm selected.
 * @var bool $__owner True if the signed in user is the owner of the current algorithm.
 * @var bool $__public True if the algorithm is defined public.
 * @var stdClass|bool $__algorithm Contains the currently loaded algorithm or false.
 */
if (isset($_GET['aid']) && $__algorithm = $__model->fetchAlgorithm($__aid = $_GET['aid'])) {
    $__owner = $__algorithm->uid === $__uid;
    $__public = !is_null($__algorithm->date_publish);
    $__algorithm->tags = $__model->fetchTags($__aid);
} else {
    $__aid = false;
    $__owner = false;
    $__public = false;
    $__algorithm = false;
}

// define where the user should be taken after signing out
$signOutAction = "";
if ($__algorithm) {
    if (!$__public) {
        // if the algorithm is private -> redirect to home action
        $signOutAction = url();
    } elseif ($__action === 'edit' || $__action === 'settings') {
        // if the algorithm is public -> redirect to view action
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
    <link href="<?= FONT_AWESOME_PATH ?>" rel="stylesheet">

    <?php if (DEBUG_MODE): ?>
        <link href="css/common.css" rel="stylesheet">
        <?php if (file_exists('css/' . ACTION . '.css')): ?>
            <link href="css/<?= ACTION ?>.css" rel="stylesheet"/>
        <?php endif ?>
    <?php elseif (file_exists('css/' . ACTION . '.min.css')): // !DEBUG_MODE: ?>
        <link href="css/<?= ACTION ?>.min.css" rel="stylesheet"/>
    <?php endif // DEBUG_MODE ?>

    <?php if (ACTION === 'edit' || ACTION === 'view'): ?>
        <link href="<?= JQUERYUI_CSS_PATH ?>" rel="stylesheet"/>
    <?php elseif (ACTION === 'admin' || ACTION === 'index' || ACTION === 'user'): ?>
        <link href="<?= TABLESORTER_CSS_PATH ?>" rel="stylesheet"/>
        <link href="<?= TABLESORTER_PAGER_CSS_PATH ?>" rel="stylesheet"/>
    <?php endif ?>

    <script type="text/javascript">
        window.current = {
            'action': '<?= ACTION ?>',
            'section': <?= isset($_GET['section']) ? $_GET['section'] : 'null' ?>,
            'parameters': '<?= json_encode($_GET) ?>',
            'lang': '<?= LANG ?>'
        };
    </script>
</head>
<body>
<div class="content-wrapper">
    <!-- NAVIGATION BAR TOP -->
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
                    <li class="dropdown <?= (ACTION === 'index') ? 'active' : '' ?>">
                        <a data-target="#" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false">
                            <?= $l10n['index'] ?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li<?php if (ACTION === 'index' && isset($_GET['tab']) && $_GET['tab'] === 'overview'): ?> class="active"<?php endif ?>>
                                <a href="<?= url(['action' => 'index', 'tab' => 'overview']) ?>"><?= $l10n['overview'] ?></a>
                            </li>
                            <li<?php if (ACTION === 'index' && isset($_GET['tab']) && $_GET['tab'] === 'users'): ?> class="active"<?php endif ?>>
                                <a href="<?= url(['action' => 'index', 'tab' => 'users']) ?>"><?= $l10n['users'] ?></a>
                            </li>
                            <li<?php if (ACTION === 'index' && isset($_GET['tab']) && $_GET['tab'] === 'algorithms'): ?> class="active"<?php endif ?>>
                                <a href="<?= url(['action' => 'index', 'tab' => 'algorithms']) ?>"><?= $l10n['algorithms'] ?></a>
                            </li>
                            <li<?php if (ACTION === 'index' && isset($_GET['tab']) && $_GET['tab'] === 'tags'): ?> class="active"<?php endif ?>>
                                <a href="<?= url(['action' => 'index', 'tab' => 'tags']) ?>"><?= $l10n['tags'] ?></a>
                            </li>
                        </ul>
                    </li>
                    <?php if ($__rights > 0): ?>
                        <li class="dropdown <?= (ACTION === 'admin') ? 'active' : '' ?>">
                            <a data-target="#" data-toggle="dropdown" role="button" aria-haspopup="true"
                               aria-expanded="false">
                                <?= $l10n['administration'] ?>
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li<?php if (ACTION === 'admin' && isset($_GET['tab']) && $_GET['tab'] === 'users'): ?> class="active"<?php endif ?>>
                                    <a href="<?= url(['action' => 'admin', 'tab' => 'users']) ?>"><?= $l10n['users'] ?></a>
                                </li>
                                <li<?php if (ACTION === 'admin' && isset($_GET['tab']) && $_GET['tab'] === 'algorithms'): ?> class="active"<?php endif ?>>
                                    <a href="<?= url(['action' => 'admin', 'tab' => 'algorithms']) ?>"><?= $l10n['algorithms'] ?></a>
                                </li>
                            </ul>
                        </li>
                    <?php endif ?>
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
                        <button type="submit" name="signOutBtn"
                                class="btn btn-default"><?= $l10n['sign_out'] ?></button>
                    </form>
                <?php else: ?>
                    <form class="navbar-form navbar-right" role="form" method="post">
                        <div class="form-group">
                            <label class="sr-only" for="login-username"><?= $l10n['username'] ?></label>
                            <input class="form-control" id="login-username" name="username"
                                   placeholder="<?= $l10n['username'] ?>">
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="login-password"><?= $l10n['password'] ?></label>
                            <input type="password" class="form-control" id="login-password" name="password"
                                   placeholder="<?= $l10n['password'] ?>">
                        </div>
                        <button type="submit" name="signInBtn" class="btn btn-default"><?= $l10n['sign_in'] ?></button>
                        <a class="btn btn-link" href="<?= url(['action' => 'register']) ?>"><?= $l10n['register'] ?></a>
                    </form>
                <?php endif ?>
            </div>
        </div>
    </nav>
    <!-- NAVIGATION BAR TOP END -->

    <div class="container">
        <noscript>
            <!-- MESSAGE BOX FOR DISABLED JAVASCRIPT -->
            <div class="alert alert-danger" role="alert">
                <strong><?= $l10n['enable_js'] ?></strong><br/>
                <?= $l10n['no_script_warning'] ?>
                <ul>
                    <li><a href="http://www.enable-javascript.com/<?= LANG ?>/" target="_blank"><?= $l10n['enable_js'] ?></a></li>
                </ul>
            </div>
        </noscript>
        <?php if (($_browser = BrowserHelper::isUnsupported())): ?>
            <!-- MESSAGE BOX FOR UNSUPPORTED BROWSER -->
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" aria-label="<?= $l10n['close'] ?>"><span aria-hidden="true">&times;</span></button>
                <strong><?= $l10n['unsupported_browser'] ?></strong>: <?= $_browser ?><br/>
                <?= $l10n['unsupported_browser_warning'] ?>
                <ul>
                    <li><a href="http://choosebrowser.com/" target="_blank"><?= $l10n['choose_browser'] ?></a></li>
                </ul>
            </div>
        <?php endif ?>
        <?php if (isset($errorMsg)): ?>
            <!-- MESSAGE BOX FOR ERRORS -->
            <div id="generalAlert" class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" aria-label="<?= $l10n['close'] ?>"><span aria-hidden="true">&times;</span></button>
                <strong><?= $l10n['error'] ?></strong> <?= $errorMsg ?>
            </div>
        <?php endif ?>
        <?php if (isset($successMsg)): ?>
            <!-- MESSAGE BOX FOR SUCCESSES -->
            <div id="generalSuccess" class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" aria-label="<?= $l10n['close'] ?>"><span aria-hidden="true">&times;</span></button>
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
        <?php require_once(BASEDIR . 'partials/' . ACTION . '.phtml') ?>
        <!-- PAGE CONTENT END -->

        <div class="footer-placeholder"></div>
    </div>
</div>
<nav class="footer navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-footer">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-footer">
                <span class="sr-only"><?= $l10n['toggle_nav'] ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= url(['action' => 'home']) ?>"><?= PROJECT_NAME ?></a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-footer">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="https://github.com/wurfmaul/kartulimardikas" target="_blank">GitHub</a></li>
                <li<?php if (ACTION === 'notice'): ?> class="active"<?php endif ?>>
                    <a href="<?= url(['action' => 'notice']) ?>"><?= $l10n['notice'] ?></a>
                </li>
                <li class="dropup">
                    <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <span class="glyphicon glyphicon-globe"></span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <?php foreach (Language::getInstance()->availableLanguages as $code => $name): ?>
                            <li<?php if ($code === LANG): ?> class="disabled"<?php endif ?>>
                                <a role="menuitem" tabindex="-1" href="<?= url(['lang' => $code], $_GET, true, false) ?>"><?= $name ?></a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- LIBRARIES -->
<script type="text/javascript" src="<?= JQUERY_PATH ?>"></script>
<script type="text/javascript" src="<?= BOOTSTRAP_JS_PATH ?>"></script>
<?php if (ACTION === 'edit' || ACTION === 'view'): ?>
    <script type="text/javascript" src="<?= JQUERYUI_JS_PATH ?>"></script>
<?php elseif (ACTION === 'admin' || ACTION === 'index' || ACTION === 'user'): ?>
    <script type="text/javascript" src="<?= TABLESORTER_JS_PATH ?>"></script>
    <script type="text/javascript" src="<?= TABLESORTER_WIDGETS_JS_PATH ?>"></script>
    <script type="text/javascript" src="<?= TABLESORTER_PAGER_JS_PATH ?>"></script>
<?php endif ?>
<!-- LIBRARIES END -->

<!-- SCRIPTS -->
<?php if (DEBUG_MODE): ?>
    <script type="text/javascript" src="js/common.js"></script>
<?php if (ACTION === 'edit' || ACTION === 'view'): ?>
    <script type="text/javascript" src="js/section.js"></script>
    <script type="text/javascript" src="js/autocomplete.js"></script>
    <script type="text/javascript" src="js/value.js"></script>
    <script type="text/javascript" src="js/node.js"></script>
    <script type="text/javascript" src="js/memory.js"></script>
    <script type="text/javascript" src="js/tree.js"></script>
<?php elseif (ACTION === 'admin' || ACTION === 'index' || ACTION === 'user'): ?>
    <script type="text/javascript" src="js/table.js"></script>
<?php endif ?>
<?php if (file_exists('js/' . ACTION . '.js')): ?>
    <script type="text/javascript" src="js/<?= ACTION ?>.js"></script>
<?php endif ?>
<?php elseif (file_exists('js/' . ACTION . '.min.js')): // !DEBUG_MODE: ?>
    <script type="text/javascript" src="js/<?= ACTION ?>.min.js"></script>
<?php endif  // DEBUG_MODE ?>
<!-- SCRIPTS END -->

</body>
</html>
<?php $__model->close() ?>