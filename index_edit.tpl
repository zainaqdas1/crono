<?php $title = 'Edit cronjob'; ?>

	      <div class="row">
	      	<div class="span12">      		
	      		<div class="widget ">
	      			<div class="widget-header"><i class="icon-edit"></i><h3>Edit cronjob</h3></div>
					<div class="widget-content">
						<div class="tabbable">
							<br>
							<div class="tab-content">
								<div class="tab-pane active" id="formcontrols">
								<form id="edit-profile" class="form-horizontal" method="post">
									<fieldset>
										<div class="control-group">											
											<label class="control-label" for="url">Cronjob URL</label>
											<div class="controls">
												<input type="text" class="span6" id="url" name="url" value="<?php echo (isset($_POST['url']) ? htmlspecialchars($_POST['url']) : ''); ?>">
											</div> <!-- /controls -->				
										</div> <!-- /control-group -->
										
                                        
                                        <div class="control-group">											
											<label class="control-label">Save log</label>
                                            <div class="controls">
												<label class="checkbox inline"><input type="checkbox" name="savelog" value="on" <?php echo (isset($_POST['savelog']) && $_POST['savelog'] == true) ? ' checked="checked"' : ''; ?> /> Check to save to the server</label>
											</div>
										</div>
                                        
                                        <div class="control-group">											
											<label class="control-label">Mail log</label>
                                            <div class="controls">
												<label class="checkbox inline"><input type="checkbox" name="maillog" value="on" <?php echo (isset($_POST['maillog']) && $_POST['maillog'] == true) ? ' checked="checked"' : ''; ?> /> Mail log </label>
												<br />
												<input class="span3" type="text" name="maillogaddress" value="<?php echo (isset($_POST['maillogaddress']) ? htmlspecialchars($_POST['maillogaddress']) : ''); ?>" /> (email address)
											</div>
										</div>
                                       
                                        <div class="control-group">											
											<label class="control-label">Time</label>
											<div class="controls">
												<label></label>
											    <select class="text" name="time">
													<option value=""></option>
<?php 
for ($x = 0; $x < 24;$x++) {
    for ($y = 0; $y < 4; $y++) {
        $time = ((strlen($x) == 1) ? '0' . $x : $x) . ':' . (($y == 0) ? '00' : ($y * 15));
        
        $s = (isset($_POST['time']) && $_POST['time'] == $time) ? ' selected="selected"' : '';
?>
													<option value="<?php echo $time;?>"<?php echo $s;?>><?php echo $time;?></option>
<?php } } ?>
												</select>
											</div>
										</div>
											
                                        <div class="control-group">											
											<label class="control-label">Each</label>
											<div class="controls">
												<label></label>
												<select class="text" name="each">
													<option value=""></option>
<?php 
foreach ($options as $each => $key) {
    $s = (isset($_POST['each']) && $_POST['each'] == $each) ? ' selected="selected"' : '';
?>
													<option value="<?php echo $each;?>"<?php echo $s;?>><?php echo $key; ?></option>
<?php } ?>
												</select> Time: 
												<select class="text" name="eachtime">
													<option value=""></option>
<?php 
for ($x = 0; $x < 24;$x++) {
    for ($y = 0; $y < 4; $y++) {
        $time = ((strlen($x) == 1) ? '0' . $x : $x) . ':' . (($y == 0) ? '00' : ($y * 15));
        
        $s = (isset($_POST['eachtime']) && $_POST['eachtime'] == $time) ? ' selected="selected"' : '';
?>
													<option value="<?php echo $time;?>"<?php echo $s;?>><?php echo $time;?></option>
<?php } } ?>
												</select>
											</div>
										</div>

										<br />

										<div class="form-actions">
											<input type="submit" name="save" value="Save" class="btn btn-primary" /> 
										</div>
									</fieldset>
								</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
