<?php $title = 'List cronjob logs'; ?>

		<div class="row">
	      	<div class="span12">      		
	      		<div class="widget ">
	      			<div class="widget-header"><i class="icon-list-alt"></i><h3>Logs</h3></div>
					<div class="widget-content">
			
						<form action="" method="post" style="margin:0">
						<div class="text">
							<p>Listing all cronjob logs found from cronjob for url: <strong><?php echo $_SESSION['cronjobs']['jobs'][$_GET['id']]['url'];?></strong></p>
						</div>
						
						 <table class="table table-striped table-bordered">
							<thead>
							  <tr>
								<th>&nbsp;</th>
								<th>Log file, right click to download</th>
							  </tr>
							</thead>
							<tbody>
<?php 
if (is_array($files) && count($files) > 0) {
	foreach ($files as $k => $file) {
?>
							<tr>
								<td><input type="checkbox" value="xx" name="cronjobs[<?php echo $k; ?>]" /></td>
								<td><a href="<?php echo $file; ?>"><?php echo substr($file, 7); ?></a></td>
							</tr>
<?php } } else { ?>
							<tr>
								<td colspan="2">Geen cronjob logs gevonden.</td>
							</tr>
<?php } ?>
							</tbody>
							</table>						
									
							<input type="submit" name="clean" value="remove logs" class="submit" />
						</form>
					</div>
				</div>
			</div>
		</div>
			
