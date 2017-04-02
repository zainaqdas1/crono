<?php $title = 'Script settings'; $active='settings'; ?>

		<div class="row">
	      	<div class="span12">      		
	      		<div class="widget ">
	      			<div class="widget-header"><i class="icon-wrench"></i><h3>Script settings</h3></div>
					<div class="widget-content">
			
						<form id="edit-profile" class="form-horizontal" method="post">
							<fieldset>
								<div class="control-group">											
									<label class="control-label" for="password">Password</label>
									<div class="controls">
										<input type="text" class="span2" id="password" name="password" value="<?php echo (isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''); ?>">
									</div>
								</div>
										
								<div class="control-group">											
									<label class="control-label" for="cronjobpassword">Cronjob password</label>
									<div class="controls">
										<input type="text" class="span2" id="cronjobpassword" name="cronjobpassword" value="<?php echo (isset($_POST['cronjobpassword']) ? htmlspecialchars($_POST['cronjobpassword']) : ''); ?>"> <br />
                                        Note: this password you need to run the cronjob (use in the cronjob url /domains/username.com/public_html/cronjob.php password=THISPASSWORD)
									</div>
								</div>
                                
								<div class="control-group">											
									<label class="control-label" for="timezone">Timezone</label>
									<div class="controls">
										<select class="text" name="timezone">
<?php
// From: https://gist.github.com/Xeoncross/1204255
$regions = array('Africa'     => DateTimeZone::AFRICA,
                 'America'    => DateTimeZone::AMERICA,
                 'Antarctica' => DateTimeZone::ANTARCTICA,
                 'Aisa'       => DateTimeZone::ASIA,
                 'Atlantic'   => DateTimeZone::ATLANTIC,
                 'Europe'     => DateTimeZone::EUROPE,
                 'Indian'     => DateTimeZone::INDIAN,
                 'Pacific'    => DateTimeZone::PACIFIC);
 
$timezones = array();
foreach ($regions as $name => $mask) {
    $zones = DateTimeZone::listIdentifiers($mask);
    foreach($zones as $timezone) {
		// Lets sample the time there right now
		$time = new DateTime(NULL, new DateTimeZone($timezone));
 
		// Us dumb Americans can't handle millitary time
		$ampm = $time->format('H') > 12 ? ' ('. $time->format('g:i a'). ')' : '';
 
		// Remove region name and add a sample time
		$timezones[$name][$timezone] = substr($timezone, strlen($name) + 1) . ' - ' . $time->format('H:i') . $ampm;
	}
}

foreach($timezones as $region => $list) {
	echo '<optgroup label="' . $region . '">';
	foreach($list as $timezone => $name) {
        $c = (isset($_SESSION['cronjobs']['settings'], $_SESSION['cronjobs']['settings']['timezone']) && $_SESSION['cronjobs']['settings']['timezone'] == $timezone) ? ' selected="selected"' : '';
		echo '<option value="' . $timezone . '"' . $c. '>' . $name . '</option>';
	}
	echo '<optgroup>';
}
?>
										</select>
									</div>
								</div>
																
								<div class="control-group">											
									<label class="control-label" for="timeout">Timeout for URLS in cronjob</label>
									<div class="controls">
										<input type="text" class="span1" id="timeout" name="timeout" value="<?php echo isset($_POST['timeout']) ? htmlspecialchars($_POST['timeout']) : '30';?>"> (Please read the manual for best settings!)
									</div>
								</div>

								<div class="form-actions">
									<input type="submit" name="save" value="Save" class="btn btn-primary" /> 
								</div>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
