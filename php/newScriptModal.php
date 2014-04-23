
<div class="modal fade" id="addLineModal" tabindex="-1" role="dialog"
	aria-labelledby="Add a line to script" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="addLineModalLabel">Add line</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" role="form">
					<div id="alert-script"></div>
					<div class="form-group" id="addLineInstrField">
						<label for="addLineInst" class="col-sm-2 control-label">Instruction</label>
						<div class="col-sm-10">
							<select class="form-control slct-allInsts" id="addLineInstr"></select>
						</div>
					</div>
					<div class="form-group">
						<label for="addLineLevel" class="col-sm-2 control-label">Level</label>
						<div class="col-sm-10">
							<div class="btn-group btn-group-justified" data-toggle="buttons">
							<?php for($i = LINE_MIN_LEVEL; $i <= LINE_MAX_LEVEL; $i ++) { ?>
								<label id="addLineLevelBtn<?=$i?>"
									class="btn btn-default btn-narrow btn-level<?= $i==0 ? " active" : "" ?>">
									<input type="radio" name="options" id="addLineLevel<?=$i?>"><?=$i?>
								</label>
							<?php }	?>
							</div>
						</div>
					</div>
					<div class="form-group" id="addAssignValueField">
						<label for="addLinePlaceTabs" class="col-sm-2 control-label">Place</label>
						<div class="col-sm-10">
							<ul class="nav nav-tabs" id="addLinePlaceTabs">
								<li><a href="#addLineAtBeginTab" data-toggle="tab">at begin</a></li>
								<li><a href="#addLineAfterTab" data-toggle="tab">after...</a></li>
								<li class="active"><a href="#addLineAtEndTab" data-toggle="tab">at end</a></li>
							</ul>
							<div class="tab-content">

								<!-- Tab "beginning" -->
								<div class="tab-pane" id="addLineAtBeginTab">
									<div class="panel panel-default panel-topless">
										<div class="panel-body">
											Add the line at the very first position.
										</div>
									</div>
								</div>

								<!-- Tab variable -->
								<div class="tab-pane" id="addLineAfterTab">
									<div class="panel panel-default panel-topless">
										<div class="panel-body">
											Place the line after the following instruction:
											<div class="form-group" id="addLineAfterField">
												<div class="col-sm-12">
													<select class="form-control slct-allLines" id="addLineAfter"></select>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								<!-- Tab Instruction -->
								<div class="tab-pane active" id="addLineAtEndTab">
									<div class="panel panel-default panel-topless">
										<div class="panel-body">
											Add line at the very last position.
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="button" class="btn btn-primary" id="addLineSubmit">Add
								Line</button>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">
					Dismiss changes</button>
			</div>
		</div>
	</div>
</div>