<?php
define ( "LIST_MIN_SIZE", 2 );
define ( "LIST_MAX_SIZE", 13 );
define ( "LIST_DEFAULT_SIZE", 7 );
define ( "LINE_MIN_LEVEL", 0 );
define ( "LINE_MAX_LEVEL", 10);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kartulimardikas</title>

<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/custom.css" rel="stylesheet">
<!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
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

	<!-- MODAL - NEW VARIABLE -->
	<?php require_once 'php/newVarModal.php'; ?>

	<!-- MODAL - NEW INSTRUCTION -->
	<?php require_once 'php/newInstModal.php'; ?>
	
	<!-- MODAL - NEW SCRIPT LINE -->
	<?php require_once 'php/newScriptModal.php'; ?>

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
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#general">General Information</a>
								</h4>
							</div>
							<div id="general" class="panel-collapse collapse">
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
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#data"> Variables </a>
								</h4>
							</div>
							<div id="data" class="panel-collapse collapse in">
								<div class="panel-body">
									<table class="table table-hover table-bordered" id="varTable" style="display: none;">
										<thead>
											<tr>
												<th>Variable</th>
												<th style="border-right: none;">Contents</th>
												<th style="border-left: none; text-align: right;">Modify</th>
											</tr>
										</thead>
										<tbody id="placeVariablesHere"></tbody>
									</table>
									<button class="btn btn-primary btn-lg" id="btn-addVariable">+ variable</button>
								</div>
							</div>
						</div>

						<!-- TAB - INSTRUCTIONS -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#instructions"> Instructions </a>
								</h4>
							</div>
							<div id="instructions" class="panel-collapse collapse in">
								<div class="panel-body">
									<table class="table table-hover table-bordered" id="instTable" style="display: none;">
										<thead>
											<tr>
												<th style="border-right: none;">Instruction</th>
												<th style="border-left: none; text-align: right;">Modify</th>
											</tr>
										</thead>
										<tbody id="placeInstructionsHere"></tbody>
									</table>
									<button class="btn btn-primary btn-lg" id="btn-addInstruction">+ instruction</button>
								</div>
							</div>
						</div>

						<!-- TAB - SCRIPT -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#script"> Script </a>
								</h4>
							</div>
							<div id="script" class="panel-collapse collapse in">
								<div class="panel-body">
									<table class="table table-hover" id="scriptTable" style="display: none;">
										<tbody id="placeLinesHere"></tbody>
									</table>
									<button class="btn btn-primary btn-lg" id="btn-addLine">+ line</button>
								</div>
							</div>
						</div>

						<!-- TAB - STEPS -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#steps"> Steps </a>
								</h4>
							</div>
							<div id="steps" class="panel-collapse collapse">
								<div class="panel-body">Not yet implemented!</div>
							</div>
						</div>
					</div>

					<button type="submit" class="btn btn-primary">Save</button>

				</form>
			</div>
		</div>
	</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/custom.js"></script>
	<script src="js/new.js"></script>
	<script src="js/new-var.js"></script>
	<script src="js/new-inst.js"></script>
	<script src="js/new-script.js"></script>
</body>
</html>