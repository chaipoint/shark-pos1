<!-- Modal To Add Petty Expense -->

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
							<td>Date 
							</td>
							<td width="50%">
								<span class="inv_cus_con"> 
									<input type="text" id="expense_date" class="form-control datepicker" data-provide="datepicker-inline" data-date-format="yyyy/mm/dd" name="expense_date"/>	
								</span>
							</td>
						</tr>
						<tr>
							<td>Head 
							</td>
							<td>
								<span class="inv_cus_con"> 
									<select name="expense_head" id="expense_head" class="form-control">
                                     <option value=''>Select Head</option>
                                     <?php foreach ($head_data as $key => $value) { ?>
                                     <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                     <?php } ?>
									</select>	
								</span>
							</td>
						</tr>
						<tr>
							<td>Purpose 
							</td>
							<td>
								<span class="inv_cus_con"> 
									<input type="text" id="expense_purpose" class="form-control" name="expense_purpose" />	
								</span>
							</td>
						</tr>
						<tr>
							<td>Amount 
							</td>
							<td>
								<span class="inv_cus_con"> 
									<input type="text" id="expense_amount" class="form-control" name="expense_amount" />	
								</span>
							</td>
						</tr>
						<tr>
							<td>Done By 
							</td>
							<td>
								<span class="inv_cus_con"> 
									<input type="text" id="expense_done_by" class="form-control autocomplete" strict="true" name="expense_done_by" target="expense_done_by_id"  />	
								</span>
							</td>
						</tr>
						<tr>
							<td>Approved By
							</td>
							<td>
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

<!-- Modal To View Petty Expense -->

<div class="modal fade" id="viewExpenseModal" tabindex="-1" role="dialog" aria-labelledby="viewExpenseModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close close-model" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove"></i></button>
				<h4 class="modal-title" id="viewExpenseModalLabel">Petty Expense ON <?php echo date('d-M-Y');?></h4>
			</div>
	
			<div class="modal-body">
				<table class="table table-condensed">
				  <thead>
				  	<tr>
				  		<th>#</th>
				  		<th>Head</th>
				  		<th>Purpose</th>
				  		<th>Done By</th>
				  		<th>Approved By</th>
				  		<th>Amount</th>
				  	</tr>
				  </thead>
                    <tbody class="text-center">
                    	<?php  
                    	$i = 1; $total = 0;
                    	if(array_key_exists('rows', $expense_data) && count($expense_data['rows'])>0){
                    		foreach ($expense_data['rows'] as $key => $value) { 
                               	echo '<tr>
                                         <td>'.$i.'</td>
				  		      			 <td>'.$value['doc']['expense_head'].'</td>
				  		     			 <td>'.$value['doc']['expense_purpose'].'</td>
				  		     			 <td>'.$value['doc']['expense_done_by'].'</td>
				  		     			 <td>'.$value['doc']['expense_approved_by'].'</td>
				  		      			 <td class="text-right">'.$value['doc']['expense_amount'].'</td>
				  		      		  </tr>';		
                    	$i++; $total += $value['doc']['expense_amount'];
                    	}
                    } 
                    ?>
					</tbody>
                     <tfoot>
                      <tr>
                     	<th class="text-center">Total</th>
                     	<th></th>
                     	<th></th>
                     	<th></th>
                     	<th></th>
                     	<th class="text-right"><?php echo $total; ?></th>
                      </tr>
                    </tfoot>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="close-model btn btn-primary" data-dismiss="modal">Close</button>
                </div>
				
			</div>
		</div>
	</div>

