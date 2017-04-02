<?php $title = 'About'; $active='about'; ?>

		<div class="row">
	      	<div class="span12">      		
	      		<div class="widget ">
	      			<div class="widget-header"><i class="icon-question-sign"></i><h3>About</h3></div>
					<div class="widget-content">
					
						<p>Need a easier way to control all your cronjobs in one location via web? Well this is your simple solution! iCronjob is a tiny tool that allows you to set all your cronjobs at once via web base. Set unlimited amount of cronjobs as you like.</p>
						<h4>Features</h4>
						<ul>
							<li>Adding a cronjob</li>
							<li>User frendly UI</li>
							<li>(http://www.yoursite.com/script.php) [select time or timeframe] to run the cronjob</li>
							<li>Unlimited amount of cronjobs</li>
							<li>Multiple cronjobs for 'same cronjob' add a ?cronjob=x to your cronjob url</li>
							<li>Reset project stats</li>
							<li>Email report per job [optional]</li>
							<li>Email Logs [optional]</li>
							<li>No use of an database (like mysql, mssql, etc)</li>
						</ul>
						<p>The cronjob runs every minute and can handle up to thousands of cronjobs daily</p>
						<h4>Adding a cronjob</h4>
						<p>Adding a cronjob is very easy, put the full url to the script (http://www.yoursite.com/script.php) and select a time or timeframe to run the cronjob. That's all!</p>                   
                        <h4>Running the cronjobs</h4>
                        <p>First you'd need to setup the cronjob script, after that you need to setup the cronjob running on the system (in your domain administration panel like plesk, directadmin). After that your cronjobs will automaticly run and do the things you like most. You can also run the cronjob from the script itselve, live, for testing.</p>
						<h4>Reset your project</h4>
						<p>To reset the project, remove the cronjobs.dat file</p>
						<h4>Installing this cronjob</h4>
						<p>To add this cronjob to your system (once) you need to setup the cronjob as this<br />
	*/1 * * * * /usr/local/bin/php -q -f /home/*username*/domains/*yourdomain.com*/public_html/cronjob.php password=*yourpassword*<br />
	*username* as your username for your control panel<br />
	*domain* as your domain name where this script is running<br />
	*yourpassword* as the password set in settings</p>
                        <p>Or to call localy try http://www.yourwebsite.com/yourpath/cronjob.php?password=*yourpassword*</p>
						<p><a href="http://www.cronjob.nl/" target="_blank">Cronjob information in Dutch and English</a> or read <a href="http://www.cyberciti.biz/faq/how-do-i-add-jobs-to-cron-under-linux-or-unix-oses/" target="_blank">this unix/linux tutorial</a></p>
					</div>
				</div>
			</div>
		</div>
		