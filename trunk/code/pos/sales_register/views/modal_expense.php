<div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close close-model" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove"></i></button>
				<h4 class="modal-title" id="addExpenseModalLabel">Add Petty Expense</h4>
			</div>
			<form method="post" name="add-expense-form" id="add-expense-form">
				
			<div class="modal-body">
				<table class="table table-striped">
					<tbody>
						<tr>
							<td width="50%">Date 
							</td>
							<td width="50%">
								<span class="inv_cus_con"> 
									<input type="text" id="expense_date" class="form-control datepicker" data-provide="datepicker-inline" data-date-format="yyyy/mm/dd" name="expense_date"/>	
								</span>
							</td>
						</tr>
						<tr>
							<td width="50%">Head 
							</td>
							<td width="50%">
								<span class="inv_cus_con"> 
									<input type="text" id="expense_head" class="form-control" name="expense_head"/>	
								</span>
							</td>
						</tr>
						<tr>
							<td width="50%">Purpose 
							</td>
							<td width="50%">
								<span class="inv_cus_con"> 
									<input type="text" id="expense_purpose" class="form-control" name="expense_purpose" />	
								</span>
							</td>
						</tr>
						<tr>
							<td width="50%">Amount 
							</td>
							<td width="50%">
								<span class="inv_cus_con"> 
									<input type="text" id="expense_amount" class="form-control" name="expense_amount" />	
								</span>
							</td>
						</tr>
						<tr>
							<td width="50%">Done By 
							</td>
							<td width="50%">
								<span class="inv_cus_con"> 
									<input type="text" id="expense_done_by" class="form-control autocomplete" strict="true" name="expense_done_by" target="expense_done_by_id"  />	
								</span>
							</td>
						</tr>
						<tr>
							<td width="50%">Approved By
							</td>
							<td width="50%">
								<span class="inv_cus_con"> 
									<input type="text" id="expense_approved_by" class="form-control autocomplete" name="expense_approved_by" strict="true" target="expense_approved_by_id"/>	
								</span>
							</td>
						</tr>
						
						</tbody>

					</table>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" id="submit-expense">Submit</button>
					<button type="button" class="close-model btn btn-primary" data-dismiss="modal">Close</button>
					
				</div>
				</form>
			</div>
		</div>
	</div>