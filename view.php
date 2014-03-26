<?php
$algo["words"] = file_get_contents('db/algo_words.txt');
$algo["source"] = file_get_contents('db/algo_code.txt');
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
			<div class="col-md-6">
				<!-- left column begin -->
				<div class="panel-group" id="accordion">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion"
									href="#collapseOne"> Selection sort </a>
							</h4>
						</div>
						<div id="collapseOne" class="panel-collapse collapse in">
							<div class="panel-body">
							<?=$algo["words"]?>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion"
									href="#collapseTwo"> Source code </a>
							</h4>
						</div>
						<div id="collapseTwo" class="panel-collapse collapse">
							<div class="panel-body">
							<?=$algo["source"]?>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion"
									href="#collapseThree"> Description </a>
							</h4>
						</div>
						<div id="collapseThree" class="panel-collapse collapse">
							<div class="panel-body">Anim pariatur cliche reprehenderit, enim
								eiusmod high life accusamus terry richardson ad squid. 3 wolf
								moon officia aute, non cupidatat skateboard dolor brunch. Food
								truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon
								tempor, sunt aliqua put a bird on it squid single-origin coffee
								nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica,
								craft beer labore wes anderson cred nesciunt sapiente ea
								proident. Ad vegan excepteur butcher vice lomo. Leggings
								occaecat craft beer farm-to-table, raw denim aesthetic synth
								nesciunt you probably haven't heard of them accusamus labore
								sustainable VHS.</div>
						</div>
					</div>
				</div>
				<div class="btn-group btn-group-justified">
					<div class="btn-group">
						<button type="button" class="btn btn-default"
							data-toggle="tooltip" data-placement="top"
							title="Back to beginning">
							<span class="glyphicon glyphicon-fast-backward"></span>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default"
							data-toggle="tooltip" data-placement="top" title="Step back">
							<span class="glyphicon glyphicon-step-backward"></span>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default"
							data-toggle="tooltip" data-placement="top" title="Play">
							<span class="glyphicon glyphicon-play"></span>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default"
							data-toggle="tooltip" data-placement="top" title="Step forward">
							<span class="glyphicon glyphicon-step-forward"></span>
						</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default"
							data-toggle="tooltip" data-placement="top" title="Forward to end">
							<span class="glyphicon glyphicon-fast-forward"></span>
						</button>
					</div>
				</div>
			</div>
			<!-- left column end -->
			<div class="col-md-6">
				<!-- right column start -->

				<!-- Tab control -->
				<ul class="nav nav-tabs">
					<li class="active"><a href="#memory" data-toggle="tab">Memory</a></li>
					<li><a href="#statistics" data-toggle="tab">Statistics</a></li>
				</ul>

				<!-- Tab contents -->
				<div class="tab-content">
					<div class="tab-pane active" id="memory">
						<table class="table table-hover table-bordered"
							style="border-top: none;">
							<tr>
								<th style="border-top: none;">Variable</th>
								<th style="border-top: none;">Contents</th>
							</tr>
							<tr>
								<td><code>a</code></td>
								<td>
									<div class="btn-group">
										<button type="button" class="btn btn-default"
											disabled="disabled">7</button>
										<button type="button" class="btn btn-default"
											disabled="disabled">3</button>
										<button type="button" class="btn btn-default"
											disabled="disabled">2</button>
										<button type="button" class="btn btn-default"
											disabled="disabled">1</button>
										<button type="button" class="btn btn-default"
											disabled="disabled">9</button>
										<button type="button" class="btn btn-default"
											disabled="disabled">6</button>
										<button type="button" class="btn btn-default"
											disabled="disabled">5</button>
										<button type="button" class="btn btn-default"
											disabled="disabled">4</button>
										<button type="button" class="btn btn-default"
											disabled="disabled">8</button>
									</div></td>
							</tr>
							<tr>
								<td><code>len</code></td>
								<td>
									<div class="btn-group">
										<button type="button" class="btn btn-default"
											disabled="disabled">8</button>
									</div></td>
							</tr>
							<tr>
								<td><code>i</code>
								</td>
								<td>
									<div class="btn-group">
										<button type="button" class="btn btn-default"
											disabled="disabled">0</button>
									</div></td>
							</tr>
							<tr>
								<td><code>min</code>
								</td>
								<td>
									<div class="btn-group">
										<button type="button" class="btn btn-default"
											disabled="disabled">0</button>
									</div></td>
							</tr>
							<tr>
								<td><code>j</code>
								</td>
								<td>
									<div class="btn-group">
										<button type="button" class="btn btn-default"
											disabled="disabled">0</button>
									</div></td>
							</tr>
							<tr>
								<td><code>t</code>
								</td>
								<td>
									<div class="btn-group">
										<button type="button" class="btn btn-default"
											disabled="disabled">0</button>
									</div>
								</td>
							</tr>
						</table>

					</div>
					<div class="tab-pane" id="statistics">
						<table class="table table-hover table-bordered"
							style="border-top: none;">
							<tr>
								<th style="border-top: none;">Metric</th>
								<th style="border-top: none;">Count</th>
							</tr>
							<tr>
								<td>Number of writes</td>
								<td><div class="btn-group">
										<button type="button" class="btn btn-default"
											disabled="disabled">1</button>
									</div></td>
							</tr>
							<tr>
								<td>Number of comparisons</td>
								<td><div class="btn-group">
										<button type="button" class="btn btn-default"
											disabled="disabled">1</button>
									</div></td>
							</tr>
							<tr>
								<td>Number of steps</td>
								<td><div class="btn-group">
										<button type="button" class="btn btn-default"
											disabled="disabled">0</button>
									</div></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<!-- right column end -->
		</div>
	</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/custom.js"></script>
</body>
</html>
