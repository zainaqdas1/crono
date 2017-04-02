<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ARMAN CRON WEB <?php echo (isset($title) ? ' - ' . $title : ''); ?></title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.min.css" rel="stylesheet">
    
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    
    <link href="css/style.css" rel="stylesheet">
    
    <link href="css/pages/reports.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

</head>
<body>

<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<a class="brand" href="./">
				the EasyCronjobHandler <?php if (isset($_SESSION['cronjobs'], $_SESSION['cronjobs']['run']) && $_SESSION['cronjobs']['run'] == true) echo '(Cronjob running)'; ?>				
			</a>		
			<div class="nav-collapse">
				<ul class="nav pull-right">
					<li><a href="?m=quit">Logout</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>
    
<div class="subnavbar">
	<div class="subnavbar-inner">
		<div class="container">
			<ul class="mainnav">
				<li <?php echo (!isset($active) OR $active=='') ? 'class="active"' : '';?>><a href="?m="><i class="icon-dashboard"></i><span>Dashboard</span></a></li>
				<li <?php echo (isset($active) && $active=='new') ? 'class="active"' : '';?>><a href="?m=new"><i class="icon-star"></i><span>New cronjob</span></a></li>
                <li <?php echo (isset($active) && $active=='log') ? 'class="active"' : '';?>><a href="?m=log"><i class="icon-list-alt"></i><span>Log</span></a></li>
                <li <?php echo (isset($active) && $active=='settings') ? 'class="active"' : '';?>><a href="?m=settings"><i class="icon-wrench"></i><span>Settings</span></a></li>
                <li <?php echo (isset($active) && $active=='about') ? 'class="active"' : '';?>><a href="?m=about"><i class="icon-question-sign"></i><span>About</span></a></li>
			</ul>
		</div>
	</div>
</div>

<div class="main">
	<div class="main-inner">
	    <div class="container">

<?php if (isset($_SESSION['errors']) && count($_SESSION['errors']) > 0) { ?>
		<div class="alert alert-block">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<h4>Warning!</h4>
			<?php foreach ($_SESSION['errors'] AS $k=>$v) { echo $v . '<br />'; } ?> 
		</div>
<?php } ?>
<?php if (isset($_SESSION['notices']) && count($_SESSION['notices']) > 0) { ?>
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<h4>Notices</h4>
			<?php foreach ($_SESSION['notices'] AS $k=>$v) { echo $v . '<br />'; } ?> 
		</div>
<?php } ?>
<?php echo $content; ?>

		</div>
	</div>
</div>


<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/base.js"></script>

</body>
</html>
<?php unset($_SESSION['errors']); unset($_SESSION['notices']); ?>