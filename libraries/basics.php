<?php
class basicsExchange
{
    public function __construct() {}
    public function getProfileItemID()
    {
        $query = "SELECT id FROM #__menu WHERE link='index.php?option=com_community&view=frontpage' LIMIT 1";
        $db = JFactory::getDbo();
        $id = $db->setQuery($query)->loadResult();
        if (!is_numeric($id) || $id == 0)
        {
              $input = JFactory::getApplication()->input;
              $itemid = $input->get('itemid');
              if (!is_numeric($itemid)) $itemid = 0;//okay, let's make it zero then. No other choice.
        }
        else $itemid = $id;
        return $itemid;
    }
    public function getItemId()
    {
        $query = "SELECT id FROM #__menu WHERE link='index.php?option=com_itemexchange' LIMIT 1";
        $db = JFactory::getDbo();
        $id = $db->setQuery($query)->loadResult();
        if (!is_numeric($id)) { return 0; }
        return $id;
    }
    public function getUserAvatar($id, & $cronDb)
    {
         if (!is_numeric($id)) return false;
         $query = "SELECT avatar, thumb FROM #__community_users WHERE userid = $id LIMIT 1";
         $rows = $cronDb->getRow($query);
         return $rows ;
    }
}
?>
