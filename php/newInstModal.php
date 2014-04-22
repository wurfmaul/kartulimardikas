
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
											<select class="form-control slct-allVars"
												id="addAssignTarget"></select>
										</div>
									</div>
									<div class="form-group index-field" id="addAssignTargetIndexField"
										style="display: none;">
										<label for="addAssignTargetIndex"
											class="col-sm-2 control-label">Index</label>
										<div class="col-sm-10">
											<div class="input-group">
												<span class="input-group-addon"> <input type="checkbox"
													value="addAssignTargetIndex" class="activate-input"
													id="addAssignTargetIndexCheck" />
												</span> <input type="text" id="addAssignTargetIndex"
													class="form-control" disabled placeholder="index" />
											</div>
										</div>
									</div>
									<div class="form-group" id="addAssignValueField">
										<label for="addAssignValueTabs" class="col-sm-2 control-label">Value</label>
										<div class="col-sm-10">
											<ul class="nav nav-tabs" id="addAssignValueTabs">
												<li class="active"><a href="#addAssignValueTab"
													data-toggle="tab">value</a></li>
												<li><a href="#addAssignVarTab" data-toggle="tab">variable</a></li>
												<li><a href="#addAssignInstTab" data-toggle="tab">instruction</a></li>
											</ul>
											<div class="tab-content">

												<!-- Tab Value -->
												<div class="tab-pane active" id="addAssignValueTab">
													<div class="panel panel-default panel-topless">
														<div class="panel-body">
															<div class="form-group" id="addAssignValueField">
																<div class="col-sm-12">
																	<p>Assign the following value to the variable:</p>
																	<input type="text" class="form-control"
																		id="addAssignValue" placeholder="42">
																</div>
															</div>
														</div>
													</div>
												</div>

												<!-- Tab variable -->
												<div class="tab-pane" id="addAssignVarTab">
													<div class="panel panel-default panel-topless">
														<div class="panel-body">
															<p>Assign the following variable to the variable above:</p>
															<div class="form-group">
																<div class="col-sm-12">
																	<select class="form-control slct-allVars"
																		id="addAssignVar"></select>
																</div>
															</div>
															<div class="form-group index-field" id="addAssignVarIndexField"
																style="display: none;">
																<label for="addAssignVarIndex"
																	class="col-sm-2 control-label">Index</label>
																<div class="col-sm-10">
																	<div class="input-group">
																		<span class="input-group-addon"> <input
																			type="checkbox" value="addAssignVarIndex"
																			class="activate-input" id="addAssignVarIndexCheck" />
																		</span> <input type="text" id="addAssignVarIndex"
																			class="form-control" disabled placeholder="index" />
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>

												<!-- Tab Instruction -->
												<div class="tab-pane" id="addAssignInstTab">
													<div class="panel panel-default panel-topless">
														<div class="panel-body">
															<p>Assign the result of another instruction to the
																variable.</p>
															<select class="form-control slct-allInsts" id="addAssignInst"></select>
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
									<div id="alert-inc"></div>
									<div class="form-group" id="addIncrementVarField">
										<label for="addIncrementVar" class="col-sm-2 control-label">Variable</label>
										<div class="col-sm-10">
											<select class="form-control slct-allVars" id="addIncrementVar"></select>
										</div>
									</div>
									<div class="form-group index-field" id="addIncVarIndexField" style="display: none;">
										<label for="addIncVarIndex" class="col-sm-2 control-label">Index</label>
										<div class="col-sm-10">
											<input type="text" id="addIncVarIndex" class="form-control" placeholder="index" />
										</div>
									</div>
									<div class="form-group">
										<label for="addIncDec"
											class="col-sm-2 control-label">Inc/Dec</label>
										<div class="col-sm-2">
											<div class="btn-group btn-group-justified" id="addIncDec"
												data-toggle="buttons">
												<label id="addIncBtn"
													class="btn btn-default btn-narrow active"> <input
													type="radio" name="options">++
												</label> <label id="addDecBtn"
													class="btn btn-default btn-narrow"> <input type="radio"
													name="options">--
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
									<div class="form-group" id="addCompareLeftField">
										<label for="addCompareLeftValueTabs" class="col-sm-2 control-label">Left operand</label>
										<div class="col-sm-10">
											<ul class="nav nav-tabs" id="addCompareLeftValueTabs">
												<li class="active"><a href="#addCompareLeftValueTab" data-toggle="tab">value</a></li>
												<li><a href="#addCompareLeftVarTab" data-toggle="tab">variable</a></li>
												<li><a href="#addCompareLeftNullTab" data-toggle="tab">null</a></li>
											</ul>
											<div class="tab-content">
												<!-- Tab Value -->
												<div class="tab-pane active" id="addCompareLeftValueTab">
													<div class="panel panel-default panel-topless">
														<div class="panel-body">
															<div class="form-group" id="addCompareLeftValueField">
																<div class="col-sm-12">
																	<input type="text" class="form-control"
																		id="addCompareLeftValue" placeholder="42">
																</div>
															</div>
														</div>
													</div>
												</div>
												<!-- Tab Variable -->
												<div class="tab-pane" id="addCompareLeftVarTab">
													<div class="panel panel-default panel-topless">
														<div class="panel-body">
															<div class="form-group">
																<div class="col-sm-12">
																	<select class="form-control slct-allVars"
																		id="addCompareLeftVar"></select>
																</div>
															</div>
															<div class="form-group index-field" id="addCompareLeftVarIndexField" style="display: none;">
																<label for="addCompareLeftVarIndex"
																	class="col-sm-2 control-label">Index</label>
																<div class="col-sm-10">
																	<div class="input-group">
																		<span class="input-group-addon"> <input
																			type="checkbox" value="addCompareLeftVarIndex"
																			class="activate-input" id="addCompareLeftVarIndexCheck" />
																		</span> <input type="text" id="addCompareLeftVarIndex"
																			class="form-control" disabled placeholder="index" />
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<!-- Tab null -->
												<div class="tab-pane" id="addCompareLeftNullTab">
													<div class="panel panel-default panel-topless">
														<div class="panel-body">
															<div class="form-group col-sm-10">
																<p>Use the <code>null</code> value for this comparison. Allowed operations: <code>=</code> and <code>&ne;</code>.</p>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="addCompareOp" class="col-sm-2 control-label">Operator</label>
										<div class="col-sm-10">
											<div class="btn-group btn-group-justified"
												data-toggle="buttons">
												<label id="addCompareOpLt" class="btn btn-default btn-cmpOp">
													<input type="radio" name="options" id="addCompareOgLt">&lt;
												</label> <label id="addCompareOpLeq"
													class="btn btn-default btn-cmpOp"> <input type="radio"
													name="options" id="addCompareOgLeq">&le;
												</label> <label id="addCompareOpEq"
													class="btn btn-default btn-cmpOp active"> <input
													type="radio" name="options" id="addCompareOgEq">=
												</label> <label id="addCompareOpNeq"
													class="btn btn-default btn-cmpOp"> <input
													type="radio" name="options" id="addCompareOgNeqq">&ne;
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
									<div class="form-group" id="addCompareRightField">
										<label for="addCompareRightValueTabs" class="col-sm-2 control-label">Right operand</label>
										<div class="col-sm-10">
											<ul class="nav nav-tabs" id="addCompareRightValueTabs">
												<li class="active"><a href="#addCompareRightValueTab" data-toggle="tab">value</a></li>
												<li><a href="#addCompareRightVarTab" data-toggle="tab">variable</a></li>
												<li><a href="#addCompareRightNullTab" data-toggle="tab">null</a></li>
											</ul>
											<div class="tab-content">
												<!-- Tab Value -->
												<div class="tab-pane active" id="addCompareRightValueTab">
													<div class="panel panel-default panel-topless">
														<div class="panel-body">
															<div class="form-group" id="addCompareRightValueField">
																<div class="col-sm-12">
																	<input type="text" class="form-control"
																		id="addCompareRightValue" placeholder="42">
																</div>
															</div>
														</div>
													</div>
												</div>
												<!-- Tab Variable -->
												<div class="tab-pane" id="addCompareRightVarTab">
													<div class="panel panel-default panel-topless">
														<div class="panel-body">
															<div class="form-group">
																<div class="col-sm-12">
																	<select class="form-control slct-allVars"
																		id="addCompareRightVar"></select>
																</div>
															</div>
															<div class="form-group index-field" id="addCompareRightVarIndexField" style="display: none;">
																<label for="addCompareRightVarIndex"
																	class="col-sm-2 control-label">Index</label>
																<div class="col-sm-10">
																	<div class="input-group">
																		<span class="input-group-addon"> <input
																			type="checkbox" value="addCompareRightVarIndex"
																			class="activate-input" id="addCompareRightVarIndexCheck" />
																		</span> <input type="text" id="addCompareRightVarIndex"
																			class="form-control" disabled placeholder="index" />
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<!-- Tab null -->
												<div class="tab-pane" id="addCompareRightNullTab">
													<div class="panel panel-default panel-topless">
														<div class="panel-body">
															<div class="form-group col-sm-10">
																<p>Use the <code>null</code> value for this comparison. Allowed operations: <code>=</code> and <code>&ne;</code>.</p>
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
															<p>Create the beginning of an if, using the conditiopn
																below.</p>
															<div class="form-group" id="addIfCondField">
																<label for="addIfCond" class="col-sm-2 control-label">Condition</label>
																<div class="col-sm-10">
																	<select class="form-control slct-allBools" id="addIfCond"></select>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="tab-pane" id="addElseIfTab">
													<div class="panel panel-default panel-topless">
														<div class="panel-body">
															<p>Create the beginning of an if, using the conditiopn
																below.</p>
															<div class="form-group" id="addElseIfCondField">
																<label for="addElseIfCond"
																	class="col-sm-2 control-label">Condition</label>
																<div class="col-sm-10">
																	<select class="form-control slct-allBools" id="addElseIfCond"></select>
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
															<p>
																Create a for-loop and use further options:
																<code>for (init; condition; after)</code>
															</p>
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