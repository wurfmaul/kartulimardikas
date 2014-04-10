<?php
define ( "LIST_MIN_SIZE", 2 );
define ( "LIST_MAX_SIZE", 13 );
define ( "LIST_DEFAULT_SIZE", 7 );
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
					<div class="form-group">
						<label class="sr-only" for="exampleInputPassword2">Password</label>
						<input type="password" class="form-control"
							id="exampleInputPassword2" placeholder="Password">
					</div>
					<button type="submit" class="btn btn-default">Sign in</button>
					<button type="button" class="btn btn-link">Register</button>
				</form>
			</div>
		</div>
	</nav>

	<!-- MODAL - NEW DATA STRUCTURE -->
	<div class="modal fade" id="addStructureModal" tabindex="-1"
		role="dialog" aria-labelledby="Add another data structure"
		aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="addStructureModalLabel">Add new data
						structure</h4>
				</div>
				<div class="modal-body">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#add-register" data-toggle="tab">Register</a></li>
						<li><a href="#add-list" data-toggle="tab">List</a></li>
					</ul>
					<div class="tab-content">

						<!-- TAB - ADD REGISTER -->
						<div class="tab-pane active" id="add-register">
							<div class="panel panel-default panel-topless">
								<div class="panel-body">
									<form class="form-horizontal" role="form">
										<div id="alert-dataStructures"></div>
										<div class="form-group" id="addRegisterNameField">
											<label for="addRegisterName" class="col-sm-2 control-label">Name</label>
											<div class="col-sm-10">
												<input type="text" class="form-control" id="addRegisterName"
													placeholder="name">
											</div>
										</div>
										<div class="form-group">
											<label for="addRegisterValue" class="col-sm-2 control-label">
												Value </label>
											<div class="col-sm-10">
												<div class="input-group">
													<span class="input-group-addon"> <input type="checkbox"
														id="addRegisterCheck" /></span> <input type="text"
														id="addRegisterValue" class="form-control" disabled
														placeholder="uninitialized" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-2 col-sm-10">
												<button type="submit" class="btn btn-primary"
													id="addRegisterSubmit">Add Register</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>

						<!-- TAB - ADD LIST -->
						<div class="tab-pane" id="add-list">
							<div class="panel panel-default panel-topless">
								<div class="panel-body">
									<form class="form-horizontal" role="form">
										<div class="form-group">
											<label for="addListName" class="col-sm-2 control-label">Name</label>
											<div class="col-sm-10">
												<input type="text" class="form-control" id="addListName"
													placeholder="name">
											</div>
										</div>
										<div class="form-group">
											<label for="addListSize" class="col-sm-2 control-label">Size</label>
											<div class="col-sm-10">
												<div class="btn-group btn-group-justified"
													data-toggle="buttons">
												<?php for($i = LIST_MIN_SIZE; $i <= LIST_MAX_SIZE; $i ++) { ?>
													<label
														class="btn btn-default btn-size<?=$i == LIST_DEFAULT_SIZE? " active" : "" ?>">
														<input type="radio" name="options" id="addListSize<?=$i?>"><?=$i?>
													</label>
												<?php }	?>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="addListValue" class="col-sm-2 control-label">
												Values </label>
											<div class="col-sm-10">
												<ul class="nav nav-tabs">
													<li class="active"><a href="#addListUninitialized"
														data-toggle="tab">uninitialized</a></li>
													<li><a href="#addListRandomized" data-toggle="tab">randomized</a></li>
													<li><a href="#addListCustomized" data-toggle="tab">customized</a></li>
												</ul>

												<!-- Tab panes -->
												<div class="tab-content">
													<div class="tab-pane active" id="addListUninitialized">
														<div class="panel panel-default panel-topless">
															<div class="panel-body">The elements of the list are
																going to be uninitialized.</div>
														</div>
													</div>
													<div class="tab-pane" id="addListRandomized">
														<div class="panel panel-default panel-topless">
															<div class="panel-body">
																The elements of the list are going to be initialized
																with random numbers between 0 and <span
																	id="addListMaxValue"><?=LIST_DEFAULT_SIZE-1?></span>.
															</div>
														</div>
													</div>
													<div class="tab-pane" id="addListCustomized">
														<div class="panel panel-default panel-topless">
															<div class="panel-body">
																The elements of the list are going to be initialized
																with the following values (semicolon separated): <input
																	type="text" class="form-control" id="addListValues"
																	placeholder="12; 14; 42; ...">
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-2 col-sm-10">
												<button type="submit" class="btn btn-primary"
													id="addListSubmit" data-dismiss="modal">Add List</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Dismiss
						changes</button>
				</div>
			</div>
		</div>
	</div>

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
									<a data-toggle="collapse" href="#general"> General Information
									</a>
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

						<!-- TAB - DATA STRUCTURES -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#data"> Data Structures </a>
								</h4>
							</div>
							<div id="data" class="panel-collapse collapse in">
								<div class="panel-body">
									<div id="alert-dataStructureDoesNotExist"></div>
									<table class="table table-hover table-bordered">
										<thead>
											<tr>
												<th>Variable</th>
												<th>Contents</th>
												<th>Modify</th>
											</tr>
										</thead>
										<tbody id="placeStructuresHere">
										</tbody>
									</table>

									<!-- Button trigger modal -->
									<button class="btn btn-primary btn-lg" data-toggle="modal"
										data-target="#addStructureModal">+ data structure</button>
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
							<div id="instructions" class="panel-collapse collapse">
								<div class="panel-body"></div>
							</div>
						</div>

						<!-- TAB - SCRIPT -->
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#script"> Script </a>
								</h4>
							</div>
							<div id="script" class="panel-collapse collapse">
								<div class="panel-body"></div>
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
								<div class="panel-body"></div>
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
</body>
</html>