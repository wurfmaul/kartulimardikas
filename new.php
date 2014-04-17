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
	<div class="modal fade" id="addVariableModal" tabindex="-1"
		role="dialog" aria-labelledby="Add another data structure"
		aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="addVariableModalLabel">Add new
						variable</h4>
				</div>
				<div class="modal-body">
					<ul class="nav nav-tabs" id="addVariableTab">
						<li class="active"><a href="#add-register" data-toggle="tab">Register</a></li>
						<li><a href="#add-list" data-toggle="tab">List</a></li>
					</ul>
					<div class="tab-content">

						<!-- TAB - ADD REGISTER -->
						<div class="tab-pane active" id="add-register">
							<div class="panel panel-default panel-topless">
								<div class="panel-body">
									<form class="form-horizontal" role="form">
										<div id="alert-register"></div>
										<div class="form-group" id="addRegisterNameField">
											<label for="addRegisterName" class="col-sm-2 control-label">Name</label>
											<div class="col-sm-10">
												<input type="text" class="form-control" id="addRegisterName"
													placeholder="name">
											</div>
										</div>
										<div class="form-group" id="addRegisterValueField">
											<label for="addRegisterValue" class="col-sm-2 control-label">Value</label>
											<div class="col-sm-10">
												<div class="input-group">
													<span class="input-group-addon"> <input type="checkbox" class="activate-input" value="addRegisterValue"
														id="addRegisterCheck" />
													</span> <input type="text" id="addRegisterValue"
														class="form-control" disabled placeholder="uninitialized" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-2 col-sm-10">
												<button type="button" class="btn btn-primary"
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
										<div id="alert-list"></div>
										<div class="form-group" id="addListNameField">
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
													<label id="addListSizeBtn<?=$i?>"
														class="btn btn-default btn-narrow btn-size<?=$i == LIST_DEFAULT_SIZE? " active" : "" ?>">
														<input type="radio" name="options" id="addListSize<?=$i?>"><?=$i?>
													</label>
												<?php }	?>
												</div>
											</div>
										</div>
										<div class="form-group" id="addListValuesField">
											<label for="addListInitTab" class="col-sm-2 control-label">Values</label>
											<div class="col-sm-10">
												<ul class="nav nav-tabs" id="addListInitTab">
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
												<button type="button" class="btn btn-primary"
													id="addListSubmit">Add List</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						Dismiss changes</button>
				</div>
			</div>
		</div>
	</div>

	<!-- MODAL - NEW INSTRUCTION -->
	<div class="modal fade" id="addInstructionModal" tabindex="-1"
		role="dialog" aria-labelledby="Add a new instruction"
		aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="addInstructionModalLabel">Add new
						instruction</h4>
				</div>
				<div class="modal-body">
					<ul class="nav nav-tabs" id="addInstructionTab">
						<li class="active"><a href="#add-assign" data-toggle="tab">Assignment</a></li>
						<li><a href="#add-inc" data-toggle="tab">Increment</a></li>
						<li><a href="#add-compare" data-toggle="tab">Comparison</a></li>
						<li><a href="#add-condition" data-toggle="tab">Condition</a></li>
						<li><a href="#add-loop" data-toggle="tab">Loop</a></li>
					</ul>
					<div class="tab-content">

						<!-- TAB - ADD ASSIGNMENT -->
						<div class="tab-pane active" id="add-assign">
							<div class="panel panel-default panel-topless">
								<div class="panel-body">
									<form class="form-horizontal" role="form">
										<div id="alert-assign"></div>
										<div class="form-group" id="addAssignVarField">
											<label for="addAssignTarget" class="col-sm-2 control-label">Variable</label>
											<div class="col-sm-10">
												<select class="form-control slct-allVars" id="addAssignTarget"></select>
											</div>
										</div>
										<div class="form-group" id="addAssignTargetIndexField" style="display: none;">
											<label for="addAssignTargetIndex" class="col-sm-2 control-label">Index</label>
											<div class="col-sm-10">
												<div class="input-group">
													<span class="input-group-addon">
														<input type="checkbox" value="addAssignTargetIndex" class="activate-input" id="addAssignTargetIndexCheck" />
													</span>
													<input type="text" id="addAssignTargetIndex" class="form-control" disabled placeholder="index" />
												</div>
											</div>
										</div>
										<div class="form-group" id="addAssignValueField">
											<label for="addAssignValueTabs"
												class="col-sm-2 control-label">Value</label>
											<div class="col-sm-10">
												<ul class="nav nav-tabs" id="addAssignValueTabs">
													<li class="active"><a href="#addAssignValueTab"
														data-toggle="tab">value</a></li>
													<li><a href="#addAssignVarTab" data-toggle="tab">variable</a></li>
													<li><a href="#addAssignInstTab" data-toggle="tab">instruction</a></li>
												</ul>

												<!-- Tab panes -->
												<div class="tab-content">
													<div class="tab-pane active" id="addAssignValueTab">
														<div class="panel panel-default panel-topless">
															<div class="panel-body">
																<p>Assign the following value to the variable:</p>
																<input type="text" class="form-control"
																	id="addAssignValue" placeholder="42">
															</div>
														</div>
													</div>
													<div class="tab-pane" id="addAssignVarTab">
														<div class="panel panel-default panel-topless">
															<div class="panel-body">
																<p>Assign the following variable to the variable above:</p>
																<div class="form-group">
																	<div class="col-sm-12">
																		<select class="form-control slct-allVars" id="addAssignVar"></select>
																	</div>
																</div>
																<div class="form-group" id="addAssignVarIndexField" style="display: none;">
																	<label for="addAssignVarIndex" class="col-sm-2 control-label">Index</label>
																	<div class="col-sm-10">
																		<div class="input-group">
																			<span class="input-group-addon">
																				<input type="checkbox" value="addAssignVarIndex" class="activate-input" id="addAssignVarIndexCheck" />
																			</span>
																			<input type="text" id="addAssignVarIndex" class="form-control" disabled placeholder="index" />
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="tab-pane" id="addAssignInstTab">
														<div class="panel panel-default panel-topless">
															<div class="panel-body">
																<p>Assign the result of another instruction to the
																	variable.</p>
																<select class="form-control slct-allInsts"></select>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-2 col-sm-10">
												<button type="button" class="btn btn-primary"
													id="addAssignSubmit">Add Assignment</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						
						<!-- TAB - ADD INC/DEC -->
						<div class="tab-pane" id="add-inc">
							<div class="panel panel-default panel-topless">
								<div class="panel-body">
									<form class="form-horizontal" role="form">
										<div id="alert-assign"></div>
										<div class="form-group" id="addIncrementVarField">
											<label for="addIncrementVar" class="col-sm-2 control-label">Variable</label>
											<div class="col-sm-8">
												<select class="form-control slct-allVars"></select>
											</div>
											<div class="col-sm-2">
												<div class="btn-group btn-group-justified" data-toggle="buttons">
													<label id="addIncBtn" class="btn btn-default btn-narrow active">
														<input type="radio" name="options" id="addInc">++
													</label>
													<label id="addDecBtn" class="btn btn-default btn-narrow">
														<input type="radio" name="options" id="addDec">--
													</label>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-2 col-sm-10">
												<button type="button" class="btn btn-primary"
													id="addIncrementSubmit">Add Increment</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>

						<!-- TAB - ADD COMPARISON -->
						<div class="tab-pane" id="add-compare">
							<div class="panel panel-default panel-topless">
								<div class="panel-body">
									<form class="form-horizontal" role="form">
										<div id="alert-compare"></div>
										<div class="form-group" id="addCompareNameField">
											<label for="addCompareVars" class="col-sm-2 control-label">Variables</label>
											<div class="col-sm-5">
												<select class="form-control slct-allVars"></select>
											</div>
											<div class="col-sm-5">
												<select class="form-control slct-allVars"></select>
											</div>
										</div>
										<div class="form-group">
											<label for="addCompareOp" class="col-sm-2 control-label">Comparison</label>
											<div class="col-sm-10">
												<div class="btn-group btn-group-justified"
													data-toggle="buttons">
													<label id="addCompareOpLt"
														class="btn btn-default btn-cmpOp"> <input type="radio"
														name="options" id="addCompareOgLt">&lt;
													</label> <label id="addCompareOpLeq"
														class="btn btn-default btn-cmpOp"> <input type="radio"
														name="options" id="addCompareOgLeq">&le;
													</label> <label id="addCompareOpEq"
														class="btn btn-default btn-cmpOp active"> <input
														type="radio" name="options" id="addCompareOgEq">==
													</label> <label id="addCompareOpGeq"
														class="btn btn-default btn-cmpOp"> <input type="radio"
														name="options" id="addCompareOgGeq">&ge;
													</label> <label id="addCompareOpGt"
														class="btn btn-default btn-cmpOp"> <input type="radio"
														name="options" id="addCompareOgGt">&gt;
													</label>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-2 col-sm-10">
												<button type="button" class="btn btn-primary"
													id="addCompareSubmit">Add Comparison</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>

						<!-- TAB - ADD CONDITION -->
						<div class="tab-pane" id="add-condition">
							<div class="panel panel-default panel-topless">
								<div class="panel-body">
									<form class="form-horizontal" role="form">
										<div id="alert-cond"></div>
										<div class="form-group" id="addCondTypeField">
											<label for="addCondTypeTabs" class="col-sm-2 control-label">Type</label>
											<div class="col-sm-10">
												<ul class="nav nav-tabs" id="addCondTypeTabs">
													<li class="active"><a href="#addIfTab" data-toggle="tab">If</a></li>
													<li><a href="#addElseIfTab" data-toggle="tab">ElseIf</a></li>
													<li><a href="#addElseTab" data-toggle="tab">Else</a></li>
												</ul>

												<!-- Tab panes -->
												<div class="tab-content">
													<div class="tab-pane active" id="addIfTab">
														<div class="panel panel-default panel-topless">
															<div class="panel-body">
																<p>Create the beginning of an if, using the conditiopn below.</p>
																<div class="form-group" id="addIfCondField">
																	<label for="addIfCond" class="col-sm-2 control-label">Condition</label>
																	<div class="col-sm-10">
																		<select class="form-control slct-allBools"></select>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="tab-pane" id="addElseIfTab">
														<div class="panel panel-default panel-topless">
															<div class="panel-body">
																<p>Create the beginning of an if, using the conditiopn below.</p>
																<div class="form-group" id="addElseIfCondField">
																	<label for="addElseIfCond" class="col-sm-2 control-label">Condition</label>
																	<div class="col-sm-10">
																		<select class="form-control slct-allBools"></select>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="tab-pane" id="addElseTab">
														<div class="panel panel-default panel-topless">
															<div class="panel-body">
																<p>Add an unconditional else branch.</p>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-2 col-sm-10">
												<button type="button" class="btn btn-primary"
													id="addCondSubmit">Add Condition</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>

						<!-- TAB - ADD LOOP -->
						<div class="tab-pane" id="add-loop">
							<div class="panel panel-default panel-topless">
								<div class="panel-body">
									<form class="form-horizontal" role="form">
										<div id="alert-loop"></div>
										<div class="form-group" id="addCondNameField">
											<label for="addCondVars" class="col-sm-2 control-label">Condition</label>
											<div class="col-sm-10">
												<select class="form-control slct-allBools"></select>
											</div>
										</div>
										<div class="form-group" id="addLoopTypeField">
											<label for="addLoopTypeTabs" class="col-sm-2 control-label">Type</label>
											<div class="col-sm-10">
												<ul class="nav nav-tabs" id="addLoopTypeTabs">
													<li class="active"><a href="#addWhileLoopTab"
														data-toggle="tab">While-Loop</a></li>
													<li><a href="#addForLoopTab" data-toggle="tab">For-Loop</a></li>
												</ul>

												<!-- Tab panes -->
												<div class="tab-content">
													<div class="tab-pane active" id="addWhileLoopTab">
														<div class="panel panel-default panel-topless">
															<div class="panel-body">
																<p>Create a while-loop, using the condition above.</p>
															</div>
														</div>
													</div>
													<div class="tab-pane" id="addForLoopTab">
														<div class="panel panel-default panel-topless">
															<div class="panel-body">
																<p>Create a for-loop and use further options:
																<code>for (init; condition; after)</code></p>
																<div class="form-group" id="addForLoopInitField">
																	<label for="addForLoopInit"
																		class="col-sm-2 control-label">Init</label>
																	<div class="col-sm-10">
																		<select class="form-control slct-allInsts"></select>
																	</div>
																</div>
																<div class="form-group" id="addRegisterValueField">
																	<label for="addRegisterValue"
																		class="col-sm-2 control-label">After</label>
																	<div class="col-sm-10">
																		<select class="form-control slct-allInsts"></select>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-2 col-sm-10">
												<button type="button" class="btn btn-primary"
													id="addLoopSubmit">Add Loop</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						Dismiss changes</button>
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
									<table class="table table-hover table-bordered">
										<thead>
											<tr>
												<th>Variable</th>
												<th>Contents</th>
												<th>Modify</th>
											</tr>
										</thead>
										<tbody id="placeVariablesHere">
											<tr>
												<td colspan="3" style="color: gray;">No variables defined
													yet!</td>
											</tr>
										</tbody>
									</table>

									<!-- Button trigger modal -->
									<button class="btn btn-primary btn-lg"
										id="btn-addVariable">+ variable</button>
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
									<!-- Button trigger modal -->
									<button class="btn btn-primary btn-lg" id="btn-addInstruction">
										+ instruction</button>
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