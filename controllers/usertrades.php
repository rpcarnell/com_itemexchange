<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
include(JPATH_COMPONENT."/libraries/basics.php");

class ExchControllerusertrades extends JControllerLegacy
{
    public function __construct()
    {
         	parent::__construct();
	  }
    public function allTrades()
    {
         $user =  JFactory::getUser();
         $tradesModel = $this->getModel ( 'trades', 'ieModel');
         $rows = $tradesModel->getAllTrades($user->id);
         if ($rows && is_array($rows))
         { list($rows, $limitstart, $pagination) = $rows; }
         else { $rows = false; }

         $document = JFactory::getDocument();
         $viewType = $document->getType();
         $view  = $this->getView('trades',$viewType);
         $view->assign('basicExchange', new basicsExchange());
         $view->assign('trades', $rows);
         $view->assign('tradesModel', $tradesModel);
         $view->assign('pagination', $pagination);
         $view->display();
    }
    
}
?>
