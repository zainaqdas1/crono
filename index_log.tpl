<?php $title = 'List cronjobs log'; $active='log'; ?>

		<div class="row">
	      	<div class="span12">      		
	      		<div class="widget ">
	      			<div class="widget-header"><i class="icon-list-alt"></i><h3>List cronjobs log</h3></div>
					<div class="widget-content">
                    <form id="edit-profile" class="form-horizontal" method="post">
                        <fieldset>
							<div class="control-group">											
								<textarea style="width: 100%; height: 400px;"><?php echo (file_exists('cronjobs.log') ? file_get_contents('cronjobs.log') : 'No log found'); ?></textarea>
							</div>
						</fieldset>
						<div class="form-actions">
							<input type="submit" name="clean" value="Clean log" class="btn btn-primary" /> 
						</div>					
					</form>
					</div>
				</div>
			</div>
		</div>
