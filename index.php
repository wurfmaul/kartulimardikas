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
					<li><a href="view.php">View</a>
					</li>
					<li><a href="new.php">New</a>
					</li>
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
		<div class="jumbotron">
			<h1>
				Kartulimardikas <small>Online collection of algorithms</small>
			</h1>
			<p>Welcome to an interactive platform to discuss, demonstrate and
				compare common algorithms.</p>
			<p>
				<a class="btn btn-primary btn-lg" role="button" href="new.php">Define
					new algorithm</a>
			</p>
		</div>
		<div class="page-header">
			<h1>Latest algorithms</h1>
		</div>
		<!-- CONTENT -->

		<dl class="dl-horizontal">
			<dt>
				<a href="view.php?aid=2749">Selection sort</a> <span
					class="label label-default">NEW</span>
			</dt>
			<dd>Selection sort is a sorting algorithm, specifically an in-place
				comparison sort. It has O(n2) time complexity, making it inefficient
				on large lists, and generally performs worse than the similar
				insertion sort. Selection sort is noted for its simplicity, ... <a href="view.php?aid=2749">read more</a></dd>
			<dt>
				<a href="view.php?aid=6930">Insertion sort</a>
			</dt>
			<dd>Insertion sort is a simple sorting algorithm that builds the
				final sorted array (or list) one item at a time. It is much less
				efficient on large lists than more advanced algorithms such as
				quicksort, heapsort, or merge sort... <a href="view.php?aid=6930">read more</a></dd>
			<dt>
				<a href="veww.php?aid=1457">Euclidean algorithm</a>
			</dt>
			<dd>The Euclidean algorithm[a], or Euclid's algorithm, is a method
				for computing the greatest common divisor (GCD) of two (usually
				positive) integers, also known as the greatest common factor (GCF)
				or highest common factor (HCF)... <a href="veww.php?aid=1457">read more</a></dd>
		</dl>
		<div class="well well-sm" style="text-align: center;">
			<ul class="pagination">
				<li class="disabled"><a href="#">&laquo;</a></li>
				<li class="active"><a href="#page1">1 <span class="sr-only">(current)</span>
				</a></li>
				<li><a href="#page2">2 <span class="sr-only">(current)</span> </a></li>
				<li><a href="#page2">&raquo;</a></li>
			</ul>
		</div>
	</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/custom.js"></script>
</body>
</html>
