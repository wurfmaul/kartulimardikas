<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kartulimardikas</title>

<link href="lib/css/bootstrap.min.css" rel="stylesheet">
<link href="lib/css/jquery-ui.min.css" rel="stylesheet">
<link href="css/custom.css" rel="stylesheet">
<!--[if lt IE 9]>
      <script src="lib/js/html5shiv.min.js"></script>
      <script src="lib/js/respond.min.js"></script>
<![endif]-->
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
				<a class="navbar-brand" href="index.php#">Kartulimardikas</a>
			</div>
			<div class="collapse navbar-collapse"
				id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li><a href="view.php#">View</a></li>
					<li class="active"><a href="#">New</a></li>
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
		<!-- HEADER -->
		<div class="page-header">
			<h1>
				New algorithm <small>Define your own algorithm!</small>
			</h1>
		</div>
		<!-- CONTENT -->
		<div class="row">
			<!-- LEFT COLUMN -->
			<div class="col-md-6">
				<form class="form-horizontal" role="form">
					<div class="panel-group" id="accordion-left">

						<!-- TAB - GENERAL INFORMATION -->
						<div class="panel panel-default">
							<div class="panel-heading" data-toggle="collapse" data-target="#genPanel">
								<h4 class="panel-title">
									<span class="glyphicon glyphicon-chevron-right"></span> General Information
								</h4>
							</div>
							<div id="genPanel" class="panel-collapse collapse">
								<div class="panel-body">
									<div class="form-group">
										<label for="in-name" class="control-label col-sm-3">Algorithm
											name</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="in-name"
												placeholder="Algorithm name">
										</div>
									</div>
									<div class="form-group">
										<label for="in-title" class="control-label col-sm-3">Description</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="in-title"
												placeholder="Description in a couple of words">
										</div>
									</div>
									<textarea class="form-control" rows="3">Long description...</textarea>
								</div>
							</div>
						</div>

						<!-- TAB - VARIABLES -->
						<div class="panel panel-default">
							<div class="panel-heading" data-toggle="collapse" data-target="#varPanel">
								<h4 class="panel-title">
									<span class="glyphicon glyphicon-chevron-down"></span> Variables
								</h4>
							</div>
							<div id="varPanel" class="panel-collapse collapse in">
								<div class="panel-body" style="text-align: center;">
									<div id="alert-var"></div>
									<div class="btn-group">
  									<div class="btn btn-success btn-lg" id="btnAddVar"><span class="glyphicon glyphicon-plus" style="width: 100px;"></span></div>
									</div>
									<table class="table table-condensed table-bordered" style="margin-top: 10px;">
										<tbody id="insertVarsHere"></tbody>
									</table>
								</div>
							</div>
						</div>

						<!-- TAB - STEPS -->
						<div class="panel panel-default">
							<div class="panel-heading" data-toggle="collapse" data-target="#stepPanel">
								<h4 class="panel-title">
									<span class="glyphicon glyphicon-chevron-right"></span> Steps
								</h4>
							</div>
							<div id="stepPanel" class="panel-collapse collapse">
								<div class="panel-body">
									<table class="table table-hover table-condensed table-bordered" id="placeStepsHere" style="display: none;"></table>
									<button class="btn btn-primary" id="btn-addStep">+ step</button>
								</div>
							</div>
						</div>
					</div>

					<button type="submit" class="btn btn-primary btn-lg">Save</button>

				</form>
			</div>
		</div>
	</div>
	<script src="lib/js/jquery.min.js"></script>
	<script src="lib/js/jquery-ui.min.js"></script>
	<script src="lib/js/jquery.keystroke.min.js"></script>
	<script src="lib/js/bootstrap.min.js"></script>
	<script src="js/custom.js"></script>
	<script src="js/new.js"></script>
	<script src="js/new-var.js"></script>
	<script src="js/new-step.js"></script>
</body>
</html>