<?php $title = 'Listing cronjobs'; ?>

		<div class="widget widget-table action-table">
            <div class="widget-header"> <i class="icon-th-list"></i>
              <h3>Listing all cronjobs found in cronjob file</h3>
            </div>
			<form method="post" action="">
            <!-- /widget-header -->
            <div class="widget-content">
              <table class="table table-striped table-bordered">
                <thead>
                  <tr>
					<th>&nbsp;</th>
                    <th>Cronjob</th>
                    <th>Time/Each</th>
					<th>Last run</th>
					<th>Runs</th>
                    <th class="td-actions">Logs/Run</th>
                  </tr>
                </thead>
                <tbody>
<?php if (isset($_SESSION['cronjobs'], $_SESSION['cronjobs']['jobs']) && count($_SESSION['cronjobs']['jobs']) > 0) { ?>
<?php foreach ($_SESSION['cronjobs']['jobs'] as $k=>$cronjob) {?>
				<tr>
					<td><input type="checkbox" value="xx" name="cronjobs[<?php echo $k; ?>]" /></td>
					<td><a href="?m=edit&id=<?php echo $k;?>" title="Edit"><?php echo (strlen($cronjob['url']) > 52) ? substr($cronjob['url'], 0, 50) . '..' : $cronjob['url'];?></a></td>
					<td>Each <?php echo ($cronjob['time'] != '') ? "day on " . $cronjob['time']  . ' hours' : $options[$cronjob['each']] . ((isset($cronjob['eachtime']) && strlen($cronjob['eachtime']) > 0) ? ' at ' . $cronjob['eachtime'] : '');?></td>
					<td><?php echo $cronjob['lastrun'];?></td>
					<td><?php echo $cronjob['runned'];?></td>
					<td><?php echo ($cronjob['savelog'] == true) ? '<a href="?=logs&id=' . $k . '">Yes</a>' : 'No'; ?><?php echo isset($_SESSION['cronjobs']['settings']) ? ' / <a target="_blank" href="cronjob.php?password=' . $_SESSION['cronjobs']['settings']['cronjobpassword'] . '&id=' . $k . '">Run</a>' : '';?></td>
				</tr>
<?php } } else { ?>
				<tr>
					<td colspan="6">No cronjobs found</td>
				</tr>
<?php } ?>
                </tbody>
				</table>
            </div>
			<br />
<?php if (isset($k, $cronjob)) { ?>            
			<p>&nbsp;<input type="submit" value="Delete selected cronjobs" class="btn btn-primary" /></p>
<?php } ?>
			</form>
		</div>
			