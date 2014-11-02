<?php define('ACTION', isset($_GET['action']) ? $_GET['action'] : 'index') ?><!DOCTYPE html>
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
				<button type="button" class="navbar-toggle" data-toggle="collapse"
					data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
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
				<form class="navbar-form navbar-right" role="form">
					<div class="form-group">
						<label class="sr-only" for="exampleInputEmail2">Username</label> <input
							type="email" class="form-control" id="exampleInputEmail2"
							placeholder="Username">
					</div>
					<!-- 
					<div class="form-group">
						<label class="sr-only" for="exampleInputPassword2">Password</label>
						<input type="password" class="form-control"
							id="exampleInputPassword2" placeholder="Password">
					</div>
					-->
					<button type="submit" class="btn btn-default">Sign in</button>
					<button type="button" class="btn btn-link">Register</button>
				</form>
			</div>
		</div>
	</nav>

	<div class="container">
        <?php switch(ACTION) {
            case 'index': require_once 'partials/index.phtml'; break;
            case 'edit': require_once 'partials/edit.phtml'; break;
            case 'view': require_once 'partials/view.phtml'; break;
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
    <script src="js/edit.js"></script>
    <script src="js/edit-var.js"></script>
    <script src="js/edit-step.js"></script>
<?php elseif (ACTION == 'view'): ?>
    <script src="js-gen/<?=$jsFile?>"></script>
<?php endif ?>
</body>
</html>
