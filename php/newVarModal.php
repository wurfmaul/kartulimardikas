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