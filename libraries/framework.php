<?php
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

if(!defined('CRONFRAME_ROOT')) {
	define('CRONFRAME_ROOT', dirname(dirname(dirname(__FILE__))) . DS . 'com_cronframe');
}

if (!file_exists(CRONFRAME_ROOT . DS . 'basics.php')) {
	?>
	<div style="font-size:14px;border:1px solid #000;background:#eee; padding:10px;">
	The <b>CronFrame Component</b> is required to run <i>DirectCron</i> and <i>ReviewCron</i>. It is not installed. Please install CronFrame.
	</div>
	<?php
	exit;
} 
else 
{
    define('CRON_FRAMEWORK', TRUE);
    include_once(CRONFRAME_ROOT . DS . 'basics.php');
    
     
}
?>
