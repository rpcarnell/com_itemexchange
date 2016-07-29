<?php
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_COMPONENT.DS.'controllers'.DS.'controller.php');
require_once(JPATH_ADMINISTRATOR.DS."components/com_itemexchange/common".DS."settings.php");
require_once (JPATH_COMPONENT.DS.'libraries/class.styles.php');
require_once (JPATH_COMPONENT.DS.'libraries/framework.php');
$view 	 				= JFactory::getApplication()->input->get('view', 'useritems');
if( $view != '') {
 
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$view.'.php';
	if (file_exists($path)) {
		require_once $path;
                $controller = $view;
	}  
}
$classname  = 'ExchController'.$controller;
$controller = new $classname( );
$task = JFactory::getApplication()->input->get('task', 'allyouritems');
$controller->execute($task);
// Redirect if set by the controller
$controller->redirect();

?>