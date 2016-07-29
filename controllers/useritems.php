<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
include(JPATH_COMPONENT."/libraries/basics.php");
class ExchControlleruseritems extends JControllerLegacy
{
    public function __construct() { parent::__construct(); }
    public function allYourItems()//default method. These are my items. Not another user's
    {
         $user =  JFactory::getUser();
         $document = JFactory::getDocument();
         $viewType = $document->getType();
         $view  = $this->getView('items',$viewType);
         $tradesModel = $this->getModel ( 'trades', 'ieModel');
         $cronDB = new CronDb();
         $rows = $tradesModel->getMyItems($user->id);
            jscssScripts::jsInclude( 'com_itemexchange', 'assets/css/styles.css');   
         $view->assign('userid', $user->id);
          $view->assign('otheruserid', $user->id);
         $view->assign('items', $rows);
         $view->assign('cronDB', $cronDB);
         $view->display();
         
    }
    private function throwAjaxError($msg)
    {
        $error = [];
        $error['error'] = 1;
        $error['msg'] = $msg;
        echo json_encode($error);
        exit;
    }
    public function userFinal()
    {
        $myUser = JFactory::getUser();
        if ( $myUser->id == 0) { $this->throwAjaxError('Please log in'); }
        $post = $_POST;
        $props = (isset($post['props'])) ? $post['props'] : '';
        if ($props == '') { $this->throwAjaxError('Invalid Data'); }
        else $props = json_decode($props);
        $optionIndex = $props->optionindex;
        //print_r($props);
        if ($optionIndex == 3) { $userid = $props->userid; 
             if ( $myUser->id == $userid && $optionIndex != 1) { $this->throwAjaxError('You are requesting from yourself'); }
        }
        $itemId = $props->itemid;
        if ($optionIndex == 2 || $optionIndex == 1) {  $msg = $this->_Iownthis($itemId, $myUser->id); }
        elseif ($optionIndex == 3) {  $msg = $this->_Iwantthis($itemId, $myUser->id, $props->userid); }
        $row = new stdClass();
        $row->msg = $msg;
        $row->error = 0;
        $row->itemid = $itemId;
        echo json_encode($row);
        exit;
    }
    public function useroptions()
    {
       // print_r(json_encode($_POST));
        $optionIndex = $_POST['optionindex'];
        $itemId = $_POST['itemid'];
        $userId = $_POST['userid'];
        if (!is_numeric($itemId) || !is_numeric($userId)) exit;
        $myUser = JFactory::getUser();
        $cronDB = new CronDb();
        if ( $myUser->id == 0) { $this->throwAjaxError('Please log in'); }
        if ( $myUser->id == $_POST['userid'] && $optionIndex != 1) { $this->throwAjaxError('You are requesting from yourself'); }
        $query = "SELECT * FROM #__itemexch_items WHERE userid = $myUser->id AND itemid = $itemId";
        $row = $cronDB->getRow($query);
        $row3 = new stdClass();
        if ($optionIndex == 1)
        {
             
             $data = array();
             if (!$row) 
             {
                 $row = new stdClass();
                 $row->msg = "Are you sure you want to trade this?";
                 $row->optionindex = 1;
                 $row->itemid = $itemId;
                 $row->error = 0;
             }
             else
             {
                $row->msg = "You are already trading this ($row->numof copies). You want to trade more?";
                $row->optionindex = 2;
                $row->error = 0;
             }
             $row3->yesno = 1;
             $row3 = $row;
        }
        elseif ($optionIndex == 0)//I want this
        {
            $otherUser = JFactory::getUser($userId );
             $query = "SELECT num_of FROM #__itemexch_trades WHERE buyer = $myUser->id AND seller = $otherUser->id AND item_id = $itemId LIMIT 1";
            $row_2 = $cronDB->getRow($query);
          
            $row3->yesno = 1;
            if ($row_2 && isset($row_2->num_of) && isset($row->numof) && (int)$row->numof <  (int)$row_2->num_of){
                $row3->msg = "You have already requested this item $row_2->num_of times";
                 $row3->yesno = 0;
            }
            elseif ($row_2 && isset($row_2->num_of) && $row_2->num_of > 0)
            {
                $row3->msg = "You are already trading this with ".$otherUser->username." ($row_2->num_of). Are you sure you want another copy?";
            }
            else { $row3->msg = "Are you sure you want to trade with ".$otherUser->username."?"; }
            $row3->optionindex = 3;
            $row3->userid = $otherUser->id;
            $row3->itemid = $itemId;
            $row3->error = 0; 
        }
        echo json_encode($row3);
        exit;
    }
    private function _Iownthis($itemId, $userId)
    {
        $cronDB = new CronDb();
        $values['userid'] = $userId;
        $values['itemid'] = $itemId;
        $values['is_requested'] = 0;
        $values['numof'] = 1;
        $values['date_added'] = time();
        $query = "SELECT * FROM #__itemexch_items WHERE userid = $userId AND itemid = $itemId";
        $row = $cronDB->getRow($query);
        if (!$row) 
        {
            $query = $cronDB->buildQuery( 'INSERT', '#__itemexch_items', $values);
            $cronDB->insert($query);
        }
        else
        {
            $values['numof'] = $row->numof + 1;
            $query = $cronDB->buildQuery( 'UPDATE', '#__itemexch_items', $values, " WHERE userid = $userId AND itemid = $itemId LIMIT 1");
            $cronDB->update($query);
        }
        return "You are now trading this";
    }
    private function _Iwantthis($itemId, $buyer, $seller)
    {
        $cronDB = new CronDb();
        $values['buyer'] = $buyer;
        $values['item_id'] = $itemId;
        $values['seller'] = $seller;
        $values['num_of'] = 1;
        $values['trade_date'] = time();
        $query = "SELECT * FROM #__itemexch_trades WHERE buyer = $buyer AND seller = $seller AND item_id = $itemId LIMIT 1";
        $row = $cronDB->getRow($query);
        if (!$row) 
        {
            $query = $cronDB->buildQuery( 'INSERT', '#__itemexch_trades', $values);
            $cronDB->insert($query);
        }
        else
        {
            $values['num_of'] = $row->num_of + 1;
            $query = $cronDB->buildQuery( 'UPDATE', '#__itemexch_trades', $values, " WHERE buyer = $buyer AND seller = $seller AND item_id = $itemId LIMIT 1");
            $cronDB->update($query);
        }
        return "Item successfully requested";
    }
    public function users()//items that belongs to other users, not you
    {
        $app = JFactory::getApplication();
        $user =  JFactory::getUser();
        $userid = $app->input->getInt('userid', 0);
        $document = JFactory::getDocument();
        $tradesModel = $this->getModel ( 'trades', 'ieModel');
        $rows = $tradesModel->getMyItems($userid);
        $viewType = $document->getType();
        $view  = $this->getView('items',$viewType);
        $view->assign('userid', $user->id);
        $view->assign('otheruserid', $userid);
        $view->assign('userData', JFactory::getUser($userid));
        $view->assign('items', $rows);
        $cronDB = new CronDb();
        $view->assign('cronDB', $cronDB);
        jscssScripts::jsInclude( 'com_itemexchange', 'assets/css/styles.css');   
        $view->display();
    }
    public function userProfile()
    {
        $data = $_POST;
        $userid = (isset($data['userid'])) ? $data['userid'] : 0;
        $be = new basicsExchange();
        echo JRoute::_("index.php?option=com_itemexchange&view=useritems&task=users&Itemid=".$be->getItemId()."&userid=".$userid);
        //echo JRoute::_("index.php?option=com_community&view=profile&Itemid=".$be->getItemId()."&userid=".$userid);
        exit;
    }
    public function itemRequests()
    {
        $data = $_POST;
        $db = JFactory::getDbo();
        $query = "SELECT a.*, b.username, c.thumb FROM #__itemexch_trades as a LEFT JOIN #__community_users as c ON a.buyer = c.userid INNER JOIN #__users as b on a.buyer = b.id WHERE a.seller = ".(int)$data['userid']." AND a.item_id = ".(int)$data['itemid']." LIMIT 100";
        $rows = $db->setQuery($query)->loadObjectList();
        $rows_2 = false;
        if ($rows)
        {
            $i = 0;
            $rows_2 = array();
            foreach ($rows as $rw)
            {
                $rows_2[$i] = $rw;
                $i++;
            }
        }
        echo ($rows_2) ? json_encode($rows_2) : 0;
        exit;
    }
    public function tradesIn()
    {
        /*
         * ery = "SELECT jt.*, j.titolo, j.titolo2, j.filename, j.thumbname, u.name `seller_name`, jp.jmovies_id,
					jt.is_varified, jt.is_sent
					FROM #__jmovies_trades jt, #__jmovies_posts jp, 
					#__jmovies j, #__users u WHERE 
					buyer=" . (int)$my->id . " AND jt.post_id=jp.id AND j.id=jp.jmovies_id AND u.id=jt.seller 
					AND jt.show_buyer = 1
					ORDER BY trade_date DESC"
                                       . " LIMIT " . $pageNav->limitstart . "," . $pageNav->limit;
         */
        $user = & JFactory::getUser();
        $query = "SELECT * FROM #__itemexch_trades as a INNER JOIN #__directcron_items as b ON a.item_id = b.id WHERE a.buyer= $user->id";
        echo $query;
        $cronDB = new CronDb();
        $rows = $cronDB->getRows($query);
        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $view  = $this->getView('items',$viewType);
        if (count($rows))
        {
           $view->assign('items', $rows);
           $view->assign('cronDB', $cronDB);
           $view->display();
        }
    }
    public function tradesOut()
    {
        /*
         * query = "SELECT COUNT(*) FROM #__jmovies_trades jt, #__jmovies_posts jp,
					#__jmovies j, #__users u
					WHERE seller=" . (int)$my->id . " AND jt.post_id=jp.id AND j.id=jp.jmovies_id AND u.id=jt.buyer
					AND jt.show_seller=1
					ORDER BY trade_date DESC";
         */
    }
    public function ownItem()
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
        $query = "SELECT id FROM #__itemexch_items WHERE itemid = $id AND userid= $user->id AND type='offer' LIMIT 1";
        $getID = $cronDB->getOneValue($query);
        if (!is_numeric($getID))
        {
            $query = "INSERT INTO #__itemexch_items (itemid, userid, type, numof, date_added) VALUES ($id, $user->id,'offer', 1, ".time().")";
            $cronDB->insert($query);
            echo JText::_(DIRITEMOFFER);
        }
        else {
           // echo JText::_(DIRALREADYOFFER);
            $query = "UPDATE #__itemexch_items SET numof = numof + 1 WHERE id = $getID";
              $cronDB->insert($query);
              echo JText::_(DIRITEMREQUESTED);
        }
        exit;
    }
    public function requestItem()
    {
        $user = & JFactory::getUser();
        $id = $_POST['itemid'];
        $api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
	$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
        if ( file_exists($api_AUP))
        {
           require_once ($api_AUP);
           //$tradepoints = AlphaUserPointsHelper::getUserInfo ('', $user->id);
            $aupid = AlphaUserPointsHelper::getAnyUserReferreID($user->id);
           $result = AlphaUserPointsHelper::checkRuleEnabled( 'plgaup_movie_request' );
           $feasible = AlphaUserPointsHelper::operationIsFeasible ( $aupid, $result[0]->points );
            if($feasible === false) { echo "Not enough points"; }
        }
        
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
         list($row, $values) = $this->getTradeData($id, $user->id, $cronDB);
         if ((int)($row->id) > 0) { $verify = $this->verifyRequest($row, $user->id, $cronDB);
         if ($verify === "ALREADY" ) { echo YOUCANNOTREQUESTYOURSELF; exit; }
         if ($verify === "TOOMANYITEM" ) { echo TOOMANYITEM; exit; }
         elseif ($verify == 0)
         {
             $values['num_of'] = 1;
             $query = $cronDB->buildQuery('INSERT', '#__itemexch_trades', $values);
             echo $query;
             $cronDB->insert($query);
         }
         elseif ($verify > 0)
         {
            /* $values['num_of'] = $verify + 1;
             $query = $cronDB->buildQuery('UPDATE', '#__itemexch_trades', $values, "WHERE buyer = $user->id AND seller = $row->userid AND post_id = $row->id LIMIT 1");
             echo $query;
             $cronDB->insert($query);*/
         }
         }
         exit;
    }
    private function getTradeData($id, $userid, & $cronDB)
    {
         $query = "SELECT id, itemid, userid FROM #__itemexch_items WHERE itemid = $id LIMIT 1";
         $row = $cronDB->getRow($query);
         $values = array();
         $values['buyer'] = $userid;
         $values['seller'] = $row->userid;
         $values['item_id'] = $row->itemid;
         $values['post_id'] = $row->id;
         $values['trade_date'] = date('Y-m-d', time());
         return array($row, $values);
    }
    private function verifyRequest($row, $userid, & $cronDB)
    {  
       // if ($row->userid == $userid) { return "ALREADY"; }
        $query = "SELECT num_of FROM #__itemexch_trades WHERE buyer = $userid AND seller = $row->userid AND post_id = $row->id LIMIT 1";
        $numof = $cronDB->getOneValue($query);
        if ($numof >= 5) { return "TOOMANYITEM"; }
        if (!is_numeric($numof)) $numof = 0;
        return $numof;
    }
    
}
?>