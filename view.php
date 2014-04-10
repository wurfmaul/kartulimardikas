<?php
// FIXME simulates a database as long as it is not implemented
$algo ["words"] = file_get_contents ( 'db/algo_words.html' );
$algo ["source"] = file_get_contents ( 'db/algo_code.html' );
$algo ["desc"] = file_get_contents ( 'db/algo_desc.html' );

include_once 'machine/generator.php';
generate ( 123 );

$jsFile = "algo.js";
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
				<a class="navbar-brand" href="index.php">Kartulimardikas</a>
			</div>
			<div class="collapse navbar-collapse"
				id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li class="active"><a href="#">View</a></li>
					<li><a href="new.php">New</a></li>
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
	<div class="container">

		<!-- HEADER -->
		<div class="page-header">
			<h1>
				Selection sort <small>Learn how to apply selection sort to an
					unordered list!</small>
			</h1>
		</div>

		<!-- CONTENT -->
		<div class="row">

			<!-- LEFT COLUMN -->
			<div class="col-md-6">

				<!-- ALGORITHM AREA -->
				<div class="panel-group" id="accordion-left">

					<!-- TAB - CONTROLS -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" href="#controls"> Controls </a>
							</h4>
						</div>
						<div id="controls" class="panel-collapse collapse in">
							<div class="panel-body">

								<!-- STEP CONTROLS -->
								<div class="btn-group btn-group-justified">
									<div class="btn-group">
										<button type="button" class="btn btn-default"
											title="Back to beginning" id="btn-reset"
											disabled="disabled">
											<span class="glyphicon glyphicon-fast-backward"></span>
										</button>
									</div>
									<div class="btn-group">
										<button type="button" class="btn btn-default"
											title="Step back" id="btn-stepback"
											disabled="disabled">
											<span class="glyphicon glyphicon-step-backward"></span>
										</button>
									</div>
									<div class="btn-group">
										<button type="button" class="btn btn-default" title="Play"
											id="btn-play" data-toggle="button">
											<span class="glyphicon glyphicon-play" id="img-play"></span>
										</button>
									</div>
									<div class="btn-group">
										<button type="button" class="btn btn-default"
											title="Step forward" id="btn-step">
											<span class="glyphicon glyphicon-step-forward"></span>
										</button>
									</div>
									<div class="btn-group">
										<button type="button" class="btn btn-default"
											title="Forward to end" id="btn-finish">
											<span class="glyphicon glyphicon-fast-forward"></span>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- TAB - PSEUDOCODE -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" href="#pseudocode"> Selection sort </a>
							</h4>
						</div>
						<div id="pseudocode" class="panel-collapse collapse">
							<div class="panel-body">
							<?=$algo["words"]?>
							</div>
						</div>
					</div>

					<!-- TAB - SOURCE CODE -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" href="#sourcecode"> Source code </a>
							</h4>
						</div>
						<div id="sourcecode" class="panel-collapse collapse in">
							<div class="panel-body">
							<?=$algo["source"]?>
							</div>
						</div>
					</div>

					<!-- TAB - DESCRIPTION -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" href="#description"> Description </a>
							</h4>
						</div>
						<div id="description" class="panel-collapse collapse">
							<div class="panel-body">
							<?=$algo["desc"]?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- RIGHT COLUMN -->
			<div class="col-md-6">
				<div class="panel-group" id="accordion-right">

					<!-- TAB - MEMORY -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" href="#memory"> Memory </a>
							</h4>
						</div>
						<div id="memory" class="panel-collapse collapse in">
							<div class="panel-body">

								<table class="table table-hover table-bordered">
									<tr>
										<th style="border-top: none;">Variable</th>
										<th style="border-top: none;">Contents</th>
									</tr>
									<tr>
										<td><code>a</code></td>
										<td>
											<div class="btn-group">
												<input type="button" class="btn btn-default"
													disabled="disabled" value="7" id="btn-a0" /> <input
													type="button" class="btn btn-default" disabled="disabled"
													value="3" id="btn-a1" /> <input type="button"
													class="btn btn-default" disabled="disabled" value="2"
													id="btn-a2" /> <input type="button" class="btn btn-default"
													disabled="disabled" value="1" id="btn-a3" /> <input
													type="button" class="btn btn-default" disabled="disabled"
													value="9" id="btn-a4" /> <input type="button"
													class="btn btn-default" disabled="disabled" value="6"
													id="btn-a5" /> <input type="button" class="btn btn-default"
													disabled="disabled" value="5" id="btn-a6" /> <input
													type="button" class="btn btn-default" disabled="disabled"
													value="4" id="btn-a7" /> <input type="button"
													class="btn btn-default" disabled="disabled" value="8"
													id="btn-a8" />
											</div>
										</td>
									</tr>
									<tr>
										<td><code>len</code></td>
										<td>
											<div class="btn-group">
												<input type="button" class="btn btn-default" id="btn-len"
													disabled="disabled" value="9" />
											</div>
										</td>
									</tr>
									<tr>
										<td><code>i</code></td>
										<td>
											<div class="btn-group">
												<input type="button" class="btn btn-default" id="btn-i"
													disabled="disabled" value="?" />
											</div>
										</td>
									</tr>
									<tr>
										<td><code>min</code></td>
										<td>
											<div class="btn-group">
												<input type="button" class="btn btn-default"
													disabled="disabled" id="btn-min" value="?" />
											</div>
										</td>
									</tr>
									<tr>
										<td><code>j</code></td>
										<td>
											<div class="btn-group">
												<input type="button" class="btn btn-default"
													disabled="disabled" id="btn-j" value="?" />
											</div>
										</td>
									</tr>
									<tr>
										<td><code>t</code></td>
										<td>
											<div class="btn-group">
												<input type="button" class="btn btn-default"
													disabled="disabled" id="btn-t" value="?" />
											</div>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>

					<!-- TAB - STATISTICS -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" href="#statistics"> Statistics </a>
							</h4>
						</div>
						<div id="statistics" class="panel-collapse collapse">
							<div class="panel-body">
								<table class="table table-hover table-bordered">
									<tr>
										<th style="border-top: none;">Metric</th>
										<th style="border-top: none;">Count</th>
									</tr>
									<tr>
										<td>Number of write operations</td>
										<td><div class="btn-group">
												<input type="button" class="btn btn-default"
													disabled="disabled" value="0" id="btn-now" />
											</div></td>
									</tr>
									<tr>
										<td>Number of compare operations</td>
										<td><div class="btn-group">
												<input type="button" class="btn btn-default"
													disabled="disabled" value="0" id="btn-noc" />
											</div></td>
									</tr>
									<tr>
										<td>Number of other operations</td>
										<td><div class="btn-group">
												<input type="button" class="btn btn-default"
													disabled="disabled" value="0" id="btn-noo" />
											</div></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<ul class="pager">
			<li class="previous"><a href="../pres/#/overview">&larr; Back to
					presentation</a></li>
		</ul>
	</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/custom.js"></script>
	<script src="js-gen/<?=$jsFile?>"></script>
</body>
</html>
