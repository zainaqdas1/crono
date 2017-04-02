<?php

set_time_limit(300);

error_reporting(E_ALL);

/**
 * cronjob executer file for the Easy Cronjob Handler
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
 

function getCronjobs() {
    return @unserialize(base64_decode(substr(file_get_contents(dirname(__FILE__) . '/cronjobs.dat.php'),7,-2)));
}

function saveCronjobs($data) {
    if (!@file_put_contents(dirname(__FILE__) . '/cronjobs.dat.php', '<' . '?php /*' . @base64_encode(serialize($data)) . '*/')) {
		die('cannot write to cronjobs database file, please check file rights');
	}
}

function saveLogs($text) {
    if (!@file_put_contents(dirname(__FILE__) . '/cronjobs.log', 
                      date('Y-m-d H:i:s') . ' - ' . $text . PHP_EOL . file_get_contents(dirname(__FILE__) . '/cronjobs.log'))) {
        die('cannot write to cronjobs log file, please check rights');
	}
}

if (file_exists(dirname(__FILE__) . '/cronjobs.dat.php')) {
    $cronjobs = getCronjobs();

	date_default_timezone_set(isset($cronjobs['cronjobs']['settings']['timezone']) ? $cronjobs['cronjobs']['settings']['timezone'] : 'Europe/Amsterdam');
    
    if (!isset($_GET['password']) && !isset($argv[1])) {
        saveLogs('No cronjob password found');
        die(htmlspecialchars('No cronjob password found, use cronjob.php?password=<yourpassword> or full path to cronjob.php <yourpassword>'));
    }
    elseif (isset($_GET['password']) && $_GET['password'] != $cronjobs['settings']['cronjobpassword']) {
        saveLogs('Invalid $_GET password');
        die('Invalid $_GET password');
    }
    elseif (isset($argv[0]) && (substr($argv[1], 0, 8) != 'password' OR substr($argv[1], 9) != $cronjobs['settings']['cronjobpassword'])) {
        saveLogs('Invalid argument password (password=yourpassword)');
        die('Invalid argument password (password=password)');
    }
    
    if (isset($cronjobs['run']) && $cronjobs['run'] == true) {
        die('Cronjob already running');
    }
    
    $cronjobs['run'] = true;
    saveCronjobs($cronjobs);
    
    date_default_timezone_set(isset($cronjobs['settings'], $cronjobs['settings']['timezone']) ? $cronjobs['settings']['timezone'] : 'Europe/Amsterdam');
    
    if (isset($cronjobs['jobs']) && is_array($cronjobs['jobs']) && count($cronjobs['jobs']) > 0) {
        // execute only one job and then exit
        foreach ($cronjobs['jobs'] as $k => $cronjob) {
        
            if (isset($_GET['id']) && $k == $_GET['id']) { 
                $run = true;
            }
            else {
                $run = false;
                if (isset($cronjob['time']) && $cronjob['time'] != '') {
                    // voer alleen uit als tijd ouder is dan vandaag 16.00 uur, maar pas na 16.00 uur
                    if (substr($cronjob['lastrun'], 0, 10) != date('Y-m-d')) {
                        if (strtotime(date('Y-m-d H:i')) > strtotime(date('Y-m-d ') . $cronjob['time'])) {
                            $run = true;
                        }
                    }
                }
                elseif (isset($cronjob['each']) && $cronjob['each'] > 0) {
                    if (strtotime($cronjob['lastrun']) + $cronjob['each'] < strtotime("now")) {
                        $run = true;
                        // if time set, daily after time...
                        if ($cronjob['each'] > (60*60*24) && 
                            isset($cronjob['eachtime']) && 
                            strlen($cronjob['eachtime']) == 5 && 
                            strtotime(date('Y-m-d H:i')) < strtotime(date('Y-m-d') . $cronjob['eachtime'])) {
                            // only run 'today' at or after give time.
                            $run = false;
                        }
                    }
                }
                elseif (substr($cronjob['lastrun'], 0, 10) != date('Y-m-d')) {
                    $run = true;
                }
            }
            
            if ($run == true) {            
                // save as executed
                echo 'Running: ' . $cronjobs['jobs'][$k]['url'] . PHP_EOL;
                
                $cronjobs['jobs'][$k]['lastrun'] = date('Y-m-d H:i:s');
                $cronjobs['jobs'][$k]['runned']++;
                                
                saveCronjobs($cronjobs);
                
                saveLogs($cronjob['url']);                                  
                
                echo 'Connecting to cronjob' . PHP_EOL;
                
                // execute cronjob
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $cronjob['url']);
                @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, (isset($cronjons['settings'], $cronjobs['settings']['timeout']) ? $cronjob['settings']['timeout'] : 5));
                
                $data = curl_exec($ch);
                
                $cronjobs = getCronjobs();
                if (curl_errno($ch)) {
                    echo 'Cronjob error: ' . curl_error($ch) . PHP_EOL;
                    
                    saveLogs($cronjob['url'] . ' - Error: ' . curl_error($ch));
                }
                else {
                    echo 'Cronjob data loaded' . PHP_EOL;
                    
                    if (isset($cronjob['savelog']) && $cronjob['savelog'] == true) {
                        if (!is_dir(dirname(__FILE__) . '/logs')) {
                            mkdir(dirname(__FILE__) . '/logs');
                        }
                        
                        if (is_dir(dirname(__FILE__) . '/logs')) {
                            echo 'Cronjob save log' . PHP_EOL;
                            file_put_contents(dirname(__FILE__) . '/logs/' . date('Y-m-d-H-i-s') . '-' . preg_replace('/[^A-Za-z0-9 ]/', '', $cronjob['url']) . '.log', 
                                              $data);
                        }
                    }
                    
                    if (isset($cronjob['maillog'], $cronjob['maillogaddress']) && $cronjob['maillog'] == true && filter_var($cronjob['maillogaddress'], FILTER_VALIDATE_EMAIL)) {
                        echo 'Cronjob mail to client: ' . $cronjob['maillogaddress'] . PHP_EOL;
                        
                        $random_hash = md5(date('r', time()));
                        $headers  = 'From: ' . $cronjob['maillogaddress'] . "\r\nReply-To: " . $cronjob['maillogaddress']; 
                        $headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-" . $random_hash . "\""; 
                        $attachment = chunk_split(base64_encode($data)); 
                        
                        ob_start();
?> 
--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>" 

--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/plain; charset="iso-8859-1" 
Content-Transfer-Encoding: 7bit

Attatched is your log file from running the cronjob "<?php echo htmlspecialchars($cronjob['url']);?>" on <?php echo $cronjob['lastrun']; ?> 

--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/html; charset="iso-8859-1" 
Content-Transfer-Encoding: 7bit

<p>Attatched is your log file from running the cronjob "<strong><?php echo htmlspecialchars($cronjob['url']);?></strong>" on </strong><?php echo $cronjob['lastrun']; ?></strong</p>

--PHP-alt-<?php echo $random_hash; ?>-- 

--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: application/zip; name="<?php echo date("Y-m-d-H-i-s") . '-' . preg_replace("/[^A-Za-z0-9 ]/", '', $cronjob['url']) . '.log';?>"  
Content-Transfer-Encoding: base64  
Content-Disposition: attachment  

<?php echo $attachment; ?> 
--PHP-mixed-<?php echo $random_hash; ?>-- 

<?php 
                        $message = ob_get_clean(); 
                        $mail_sent = @mail($cronjob['maillogaddress'], 
                                           'Cronjob log ' . date('Y-m-d H:i:s') . ' for ' . htmlspecialchars($cronjob['url']),
                                           $message, 
                                           $headers); 

                        saveLogs($mail_sent ? 'Mail sent' : 'Mail failed'); 
                    }
                }
                
                curl_close($ch);
            }
            
            // update cronjob list as the user can change stuff...
            $cronjobs = getCronjobs();
        }        
    }
    
    $cronjobs['run'] = false;
    saveCronjobs($cronjobs);
}
else {
   die('cronjob database file not found...');
}