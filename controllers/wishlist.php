<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
class ExchControllerWishList extends JController
{
    function __construct() { parent::__construct(); }
    public function addtoWish()
    {
        $user = & JFactory::getUser();
        $id = $_POST['itemid'];
        if (!is_numeric($user->id) || $user->id == 0) 
        {
            echo DIRCRONLOGINFIRST;
            exit;
        }
        if (!is_numeric($id) || $id == 0)
        {
            echo DIRCRON_NOITEMID;
            exit;
        }
        $cronDB = new CronDb();
        $query = "SELECT id FROM #__itemexch_wish WHERE itemid = $id AND userid= $user->id LIMIT 1";
        $getID = $cronDB->getOneValue($query);
        if (!is_numeric($getID))
        {
            $query = "INSERT INTO #__itemexch_wish (itemid, userid,  date_added) VALUES ($id, $user->id,".time().")";
            $cronDB->insert($query);
            echo JText::_(DIRITEMADDEDWISH);
        }
        else 
        {
              $query = "UPDATE #__itemexch_wish SET date_added= ".time()." WHERE id = $getID";
              $cronDB->insert($query);
              echo JText::_(DIRITEMALREADYWIDH);
        }
        exit;
    }
    public function wishList()
    {
        
    }
}
?>