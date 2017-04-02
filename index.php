<?php

/**
 * Index file for the Easy Cronjob Handler
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Scripts
 * @package   TheEasyCronjobHandler
 * @author    Eric Bruggema <ericbruggema@hotmail.com>
 * @copyright 2013 Eric bruggema
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 */
 
error_reporting(E_ALL);
session_start();
session_regenerate_id();


$options = array(30        => '30 seconds',
                 60        => 'Minute',
                 120       => '2 minutes',
                 300       => '5 minutes',
                 600       => '10 minutes',
                 900       => '15 minutes',
                 1800      => 'Half hour',
                 2700      => '45 minutes',
                 3600      => 'Hour', 
                 7200      => '2 hours', 
                 14400     => '4 hours', 
                 43200     => '12 hours',
                 86400     => 'Day', 
                 172800    => '2 days', 
                 259200    => '3 days', 
                 604800    => 'Week', 
                 209600    => '2 weeks', 
                 2629743   => 'Month');
				 
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

function updateCronjobs($id = '') {
	if (@file_put_contents(dirname(__FILE__) . '/cronjobs.dat.php', '<' . '?php /*' . base64_encode(serialize($_SESSION['cronjobs'])) . '*/')) {
		$_SESSION['notices'][] = 'Database saved';
		
		// create 'backup'
		@file_put_contents(dirname(__FILE__) . '/cronjobs.backup-' . date('Y-m-d') . '.php', '<' . '?php /*' . base64_encode(serialize($_SESSION['cronjobs'])) . '*/');
	}
	else {
		$_SESSION['errors'][] = 'Database not saved, could not create database file on server, please check write rights of this script';
	}
	
	// remove old cronjob backup files
	$files = glob(dirname(__FILE__). '/cronjobs.backup*.php');
	foreach ($files as $file) {
		if (is_file($file) && time() - filemtime($file) >= 2*24*60*60) { // 2 days
			unlink($file);
		}
    }
    
	if ($id != '' && is_numeric($id)) {
		header('Location: ?m=edit&id=' . $id);
	}
	else {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}
    exit;
}

if (file_exists(dirname(__FILE__) . '/cronjobs.dat.php')) {
	$data = @unserialize(@base64_decode(substr(file_get_contents(dirname(__FILE__) . '/cronjobs.dat.php'), 7, -2)));
	if (is_array($data)) {
		$_SESSION['cronjobs'] = $data;
	}
}
elseif (isset($_SESSION['cronjobs'])) {
    $_SESSION = null;
}
    
date_default_timezone_set(isset($_SESSION['cronjobs']['settings']['timezone']) ? $_SESSION['cronjobs']['settings']['timezone'] : 'Europe/Amsterdam');
	
if (isset($_SESSION['cronjobs'], $_SESSION['cronjobs']['settings'], $_SESSION['cronjobs']['settings']['password']) && 
    (!isset($_SESSION['login']) OR time() < ($_SESSION['login'] - (60 * 15)))) {     
     $template = 'index_login';
     
     if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'])) {
        sleep(2);
        if ($_POST['password'] == $_SESSION['cronjobs']['settings']['password']) {
            $_SESSION['login'] = time();
            
            header('Location: ' . basename($_SERVER['PHP_SELF']));
            exit;
        }
		else {
			$_SESSION['errors'][] = 'Password incorrect, try again';
		}
    }
}
else {
    $_SESSION['login'] = time();

    $m = isset($_GET['m']) ? $_GET['m'] : '';

    $template = 'index';
    $content  = '';
    
    if (!isset($_SESSION['cronjobs']['settings'])) {
        $_SESSION['notices'][] = 'First time here?<br />If not? the script crashed, please check out a backup of past days. <br /><br />Installation of this script, please check the <strong>settings</strong> page and fill in the required information to make the cronjob script work!';
    }
    

    switch ($m) {        
    
        case 'quit';
        
            $_SESSION = null;
            session_destroy();
            
            header('Location: ' . basename($_SERVER['PHP_SELF']));
            exit;
            
        break;        
            
        case 'settings':
        
            $template = 'index_settings';
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			
				$good = true;	
				if (!isset($_POST['password']) OR !preg_match('/([a-zA-Z0-9_ ]{4,})/i', $_POST['password'])) {
					$_SESSION['errors'][] = 'Your password contains wrong characters, minimum of 4 letters and numbers';
				}
                
				if (strlen(trim($_POST['cronjobpassword'])) < 2) {
                    $_SESSION['errors'][] = 'Your cronjob script cannot run without a password, Your cronjob password contains wrong characters, minimum of 4 letters and numbers';
					$good = false;
                }
				
				$found = false;
				foreach($timezones as $region => $list) {
					foreach($list as $timezone => $name) {
						if ($timezone == $_POST['timezone']) {
							$found = true;
							break;
						}
					}
				}
				
				if ($found == false) {
					$_SESSION['errors'][] = 'You need to select a correct timezone';
					$good = false;
				}
				
				if ($good == true) {
					$_SESSION['cronjobs']['settings'] = array('password'        => $_POST['password'],
															  'cronjobpassword' => $_POST['cronjobpassword'],
															  'timezone'        => $_POST['timezone'],
															  'timeout'         => (isset($_POST['timeout']) && is_numeric($_POST['timeout']) ? $_POST['timeout'] : 30));
					updateCronjobs();
			    }
            }
            
            if (isset($_SESSION['cronjobs']['settings']) && !isset($good)) { 
                $_POST = $_SESSION['cronjobs']['settings'];
            }
            
        break;
        
        case 'new':
        
            $template = 'index_new';
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['url'], $_POST['time'], $_POST['each'])) {
                if (filter_var($_POST['url'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                    $found = false;
                    if (isset($_SESSION['cronjobs'], $_SESSION['cronjobs']['jobs']) && count($_SESSION['cronjobs']['jobs']) > 0) {
                        foreach ($_SESSION['cronjobs']['jobs'] as $null => $cronjob) {
                            if ($cronjob['url'] == $_POST['url']) {
                                $found = true;
                            }
                        }
                    }
                    
                    if ($found == false) {
                        if ($_POST['time'] == '' && $_POST['each'] == '') {
                            $_SESSION['errors'][] = 'Time settings missing, please add time settings';
                        }
                        else {
                            if (isset($_POST['maillog'], $_POST['maillogaddress']) && !filter_var($_POST['maillogaddress'], FILTER_VALIDATE_EMAIL)) {
                                $_SESSION['errors'][] = 'Email address is invalid!';
                            }
                            
                            $_SESSION['cronjobs']['jobs'][] = array('url'            => $_POST['url'],
                                                                    'time'           => ((isset($_POST['time']) && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/',  $_POST['time'])) ? $_POST['time'] : ''),
                                                                    'each'           => ((isset($_POST['each']) && is_numeric($_POST['each'])) ? $_POST['each'] : ''),
                                                                    'eachtime'       => ((isset($_POST['eachtime']) && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/',  $_POST['eachtime'])) ? $_POST['eachtime'] : ''),
                                                                    'lastrun'        => '',
                                                                    'runned'         => 0,
                                                                    'savelog'        => (isset($_POST['savelog']) ? true : false),
                                                                    'maillog'        => (isset($_POST['maillog']) ? true : false),
                                                                    'maillogaddress' => ((isset($_POST['maillogaddress']) && filter_var($_POST['maillogaddress'], FILTER_VALIDATE_EMAIL)) ? $_POST['maillogaddress'] : ''));

                            updateCronjobs(count($_SESSION['cronjobs']['jobs']));
                        }
                    }
                    else {
                        $_SESSION['errors'][] = 'Cronjob already known in this system, if you would like to use more of the same use ?=randomnumber or if ? already is being used add &=randomnumber';
                    }
                }
                else {
                    $_SESSION['errors'][] = 'Cronjob URL is wrong';
                }
            }
                        
        break;
        
        case 'edit':
        
            $template = 'index_edit';
            $update = true;
            
            if (isset($_GET['id'], $_SESSION['cronjobs'], $_SESSION['cronjobs']['jobs'], $_SESSION['cronjobs']['jobs'][$_GET['id']])) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['url'], $_POST['time'], $_POST['each'])) {
                    if (filter_var($_POST['url'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                        if (isset($_POST['maillog'], $_POST['maillogaddress']) && !filter_var($_POST['maillogaddress'], FILTER_VALIDATE_EMAIL)) {
                            $_SESSION['errors'][] = 'Email address is invalid and not saved to database!';
                        }
                        
                        $_SESSION['cronjobs']['jobs'][$_GET['id']] = array('url'            => $_POST['url'],
                                                                           'time'           => ((isset($_POST['time']) && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/',  $_POST['time'])) ? $_POST['time'] : ''),
                                                                           'each'           => ((isset($_POST['each']) && is_numeric($_POST['each'])) ? $_POST['each'] : ''),
                                                                           'eachtime'       => ((isset($_POST['eachtime']) && preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/',  $_POST['eachtime'])) ? $_POST['eachtime'] : ''),
                                                                           'lastrun'        => $_SESSION['cronjobs']['jobs'][$_GET['id']]['lastrun'],
                                                                           'runned'         => $_SESSION['cronjobs']['jobs'][$_GET['id']]['runned'],
                                                                           'savelog'        => (isset($_POST['savelog']) ? true : false),
                                                                           'maillog'        => (isset($_POST['maillog']) ? true : false),
                                                                           'maillogaddress' => ((isset($_POST['maillogaddress']) && filter_var($_POST['maillogaddress'], FILTER_VALIDATE_EMAIL)) ? $_POST['maillogaddress'] : ''));

                        updateCronjobs();
                    }
                    else {
                        $_SESSION['errors'][] = 'Current URL is not correct, must contact http(s):// and a path';
                        $update = false;
                    }
                }
                
                if ($update == true) {
                    $_POST = $_SESSION['cronjobs']['jobs'][$_GET['id']];
                }
            }
            else {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        
        break;
            
        case 'log':
        
            $template = 'index_log';
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['clean'])) {
                $_SESSION['notices'][] = 'Cronjob log cleaned';
                file_put_contents('cronjobs.log', '');
                
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
            
        break;
        
        case 'about':
            
            $template = 'index_about';
        
        break;
        
        case 'logs':
        
            $template = 'index_logs';
            
            if (isset($_GET['id'], $_SESSION['cronjobs'], $_SESSION['cronjobs']['jobs'], $_SESSION['cronjobs']['jobs'][$_GET['id']])) {
                $files = glob('./logs/*' . preg_replace('/[^A-Za-z0-9 ]/', '', $_SESSION['cronjobs']['jobs'][$_GET['id']]['url']) . '.log');
                if (is_array($files) && count($files) > 0) {
                    arsort($files);
                }

                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cronjobs'])) {
                    foreach ($_POST['cronjobs'] as $k => $v) {
                        if (isset($files[$k]) && file_exists($k)) {
							if (!@unlink($files[$k])) {
								$_SESSION['errors'][] = 'File ' . $files[$k] . ' could not be removed from the server, please do it manualy';
							}
						}
                    }
                    
                    $_SESSION['notices'][] = 'Removed ' . count($_POST['cronjobs']) . ' logs from the server';
                    
                    header('Location: ' . basename($_SERVER['PHP_SELF']) . '?m=logs&id=' .  $_GET['id']);
                    exit;
                }
            }
            else {
                header('Location: ' . basename($_SERVER['PHP_SELF']));
                exit;
            }
            
        break;

        default:
        
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cronjobs']) && is_array($_POST['cronjobs'])) {
                // remove from session
                foreach ($_POST['cronjobs'] as $k => $v) {
                    // get log files, if exists;
                    if (is_dir('./logs/')) {
                        $files = glob('./logs/*' . preg_replace('/[^A-Za-z0-9 ]/', '', $_SESSION['cronjobs']['jobs'][$k]['url']) . '.log');
                        // files found?
                        if (is_array($files) && count($files) > 0) {
                            // remove all!!
                            foreach ($files as $k => $file) {
                                if (!@unlink($file)) {
									$_SESSION['errors'][] = 'Could not remove file ' . $file . ' from server, please do this manually';
								}
                            }
                        }
                    }
                    unset($_SESSION['cronjobs']['jobs'][$k]);
                }
                
                $_SESSION['notices'][] = count($_POST['cronjobs']) . ' cronjobs removed';
                
                updateCronjobs();
            }
            
        break;
    }
}

if (file_exists($template . '.tpl')) {
    ob_start();
    include $template . '.tpl';
    $content = ob_get_contents();
    ob_end_clean();
}
elseif (!file_exists('layout.tpl')) {
	die('Main template could not be loaded, aborting');
}
else {
    die('Template can not be found, how irritating...');
}

include 'layout.tpl';