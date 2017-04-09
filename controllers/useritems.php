<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
include(JPATH_COMPONENT."/libraries/basics.php");
class ExchControlleruseritems extends JControllerLegacy
{
	var $itemid = 0;
    public function __construct()
    {
		parent::__construct();
		$app = JFactory::getApplication();
        $itemid = $app->input->getInt('Itemid', 0);
	    $be = new basicsExchange();
        $this->itemid = ($itemid != 0) ? $itemid : $be->getItemId();
    }
    public function alltrades()
    {
		$user =  JFactory::getUser();
        if ($user->id == 0) { ?><script>window.location="<?php echo JRoute::_("index.php?option=com_users"); ?>"</script><?php }
        $app = JFactory::getApplication();
        $tradeType = $app->input->getInt('trty', '');
        $document = JFactory::getDocument();
        $tradesModel = $this->getModel ( 'trades', 'ieModel');
        $rows = $tradesModel->getMyTrades($user->id, $tradeType);
        if ($rows && is_array($rows)) { list($rows, $limitstart, $pagination) = $rows; }
        $viewType = $document->getType();
        jscssScripts::jsInclude( 'com_itemexchange', 'assets/css/styles.css');
        $view  = $this->getView('alltrades',$viewType);
        $view->assign('basicExchange', new basicsExchange());
        $view->assign('userid', $user->id);
        //$view->assign('otheruserid', $userid);
        $view->assign('tradesModel', $tradesModel);
        $view->assign('tradeType', $tradeType);
        $view->assign('menuid', $this->itemid);
        if (!isset($pagination)) $pagination = '';
        $view->assign('pagination', $pagination);
       // $view->assign('userData', JFactory::getUser($userid));
        $view->assign('items', $rows);
        $cronDB = new CronDb();
        $view->assign('cronDB', $cronDB);
        
        $view->display();
	}
    public function tradenow()
    {
        $user =  JFactory::getUser();
        if ($user->id == 0) $this->throwAjaxError("Please log in");
        $item_id = $_POST['item_id'];//this is what I want the user to send to me
        if (!is_numeric($item_id)) $this->throwAjaxError("Invalid Data");
        $buyer = $_POST['buyer'];//this is the user that will trade with ME
        if (!is_numeric($buyer)) $this->throwAjaxError("Invalid Data");
        $initial_trade = $_POST['initial_trade'];//this is the trade I started
        if (!is_numeric($initial_trade)) $this->throwAjaxError("Invalid Data");
        $item_id2 = $_POST['item_id2'];//this is what the user wants ME to send to him
        if (!is_numeric($item_id2)) $this->throwAjaxError("Invalid Data");
        $trade_2 = $_POST['trade_2'];//this is the trade user started
        if (!is_numeric($trade_2)) $this->throwAjaxError("Invalid Data");
        $db = JFactory::getDbo();
        $query = "SELECT * FROM #__itemexch_trades WHERE id = $initial_trade AND seller = $user->id LIMIT 1";
        $db->setQuery($query);
        $trad1_row = $db->loadObject();
        if (!$trad1_row) { $this->throwAjaxError("ERROR - Invalid Data. Item $initial_trade DOES NOT EXIST"); }
        else { if ((int)$trad1_row->num_of <= 0) { $this->throwAjaxError("Item $trad1_row->item_id is already been traded."); } }
        $query = "SELECT * FROM #__itemexch_trades WHERE id = $trade_2 AND seller = $buyer LIMIT 1";
        $db->setQuery($query);
        $trad2_row = $db->loadObject();
        if (!$trad2_row) { $this->throwAjaxError("ERROR - Invalid Data. Item $trade_2 DOES NOT EXIST"); }
        else { if ((int)$trad2_row->num_of <= 0) { $this->throwAjaxError("Item $trad2_row->item_id is already been traded."); } }
        $query = "SELECT * FROM #__itemexch_fulltrade WHERE dealer_1 = '".$user->id."' AND trade_id_1 = $initial_trade AND dealer_2 = $buyer AND trade_id_2 = $trade_2 LIMIT 1";
        $db->setQuery($query);
        $row = $db->loadObject();
        if ($row)
        {
            if ($row->status == 1) { $this->throwAjaxError("You have already initiated this trade"); }
            elseif ($row->status == 0) { $this->throwAjaxError("This trade was canceled"); }
        }
        else
        {
           $query = "INSERT INTO #__itemexch_fulltrade (`id`, `trade_id_1`, `item_id_1`, `dealer_1`, `trade_id_2`, `item_id_2`, `dealer_2`, `status`, `date_started`) 
           VALUES (NULL, '$initial_trade', '$item_id', '".$user->id."', '$trade_2', '$item_id2', '$buyer', '1', '".time()."');";
           $db->setQuery($query);
           $db->Query();
           $query = "UPDATE #__itemexch_trades SET num_of = (num_of -1) WHERE id = $initial_trade AND seller = $user->id LIMIT 1";
           $db->setQuery($query);
           $db->Query();
           $query = "UPDATE #__itemexch_items SET numof = (numof -1) WHERE userid = ".$user->id." AND itemid = $item_id LIMIT 1";
           $db->setQuery($query);
           $db->Query();
           $query = "UPDATE #__itemexch_trades SET num_of = (num_of -1) WHERE id = $trade_2 AND seller = $buyer LIMIT 1";
           $db->setQuery($query);
           $db->Query();
           $query = "UPDATE #__itemexch_items SET numof = (numof -1) WHERE userid = $buyer AND itemid = $item_id2 LIMIT 1";
           $db->setQuery($query);
           $db->Query();
           $msg['error'] = 0;
           $msg['msg'] = 'NOMSG';
           echo json_encode($msg);
           exit;
	}
    }
    public function reqfromme()
    {
        if (!is_numeric($_POST['props']['buyer'])) $this->throwAjaxError("Invalid Data");
        $user =  JFactory::getUser();
        if ($user->id == 0) $this->throwAjaxError("Please log in");
        $tradesModel = $this->getModel ( 'trades', 'ieModel');
        if (!is_numeric($_POST['props']['item_id'])) $this->throwAjaxError("Invalid Data");
        else $itemId = $_POST['props']['item_id'];
        $myItemData = $this->_userTrading($user->id, $_POST['props']['item_id']);
        if ($myItemData->numof == 0) 
        { 
		     $tradesModel = $this->getModel ( 'trades', 'ieModel');
             $rows = $tradesModel->getItem($_POST['props']['item_id']);
             $this->throwAjaxError("You are already trading ".$rows->item);	
		}
        $query = "SELECT a.*, b.item, b.image FROM #__itemexch_trades as a INNER JOIN #__directcron_items as b ON a.item_id = b.id WHERE a.num_of >= 1 AND a.buyer = ".$user->id." AND a.seller=".(int)$_POST['props']['buyer']." AND a.is_sent = 0 AND a.is_verified = 0 ORDER BY a.id ASC LIMIT 10";
        $db = JFactory::getDbo();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $msg = [];
        $msg['error'] = 0;
        $msg['msg'] = '';
        if ($rows) {
		$a = 0; 
	        foreach ($rows as $row)
	        {   
			if ($row->image)
		        {    
				      $r = unserialize($row->image);
				      if (isset($r['image'])) $rows[$a]->image = $r['image'];
				      if (isset($r['thumbnail'])) $rows[$a]->thumb = $r['thumbnail'];
			}
			$a++;
		}
	    }
        $msg['data']  = ($rows) ? $rows : '';
        echo json_encode($msg);
	exit;
    }
    public function itemFinal()
    {
		$user =  JFactory::getUser();
		if (!is_numeric( $user->id) || $user->id == 0) { $this->throwAjaxError("Please log in"); }
	    $post = $_POST['props'];
        $post = json_decode($post);
        $title = $post->title;
        $option = $post->optionindex;
        $itemId = $post->itemid;
        if (!is_numeric($itemId)) { $this->throwAjaxError("Invalid Data"); }
        if ($option == 1 || $option == 2)
        {
			$this->_Iownthis($itemId, $user->id);
			if (isset($post->numof)) $copy = ($post->numof + 1)." copies of";
			else $copy = "one copy of";
			$msg = "You are now trading $copy $title";
		}
		$post->msg = $msg;
		echo json_encode($post);
        exit;
    }
    public function itemoptions()
    {
         $row3 = new stdClass();
         $row3->yesno = 1;
         $user =  JFactory::getUser();
         if ($user->id == 0)
         {
			 $msg = "Please log in";
			 $this->throwAjaxError($msg);
		 }
         $row3->optionindex = $_POST['optionindex'];
            $row3->userid = $user->id;
            $row3->itemid = $_POST['itemid'];
            if (!is_numeric($row3->itemid) || $row3->itemid == 0)
            {
				$msg = "Item ID is invalid";
				$this->throwAjaxError($msg);
		    }
            $row3->error = 0;
        if ($_POST['optionindex'] == 1) { $row3 = $this->_userTrading($user->id, $row3->itemid, $_POST['itemTitle']); }
        elseif ($_POST['optionindex'] == 2)
        {
		    $row3 = $this->_wishList($user->id, $row3->itemid, $_POST['itemTitle']);
		    $row3->yesno = 0;
        }
        echo json_encode($row3);
        exit;
    }
    private function _wishList($myUserid, $itemId, $title = '')
    {
		 $cronDB = new CronDb();
		 if (!is_numeric($myUserid) || !is_numeric($itemId))
		 {
			 $msg = "Invalid Data";
			 $this->throwAjaxError($msg);
		 }
		 $query = "SELECT * FROM #__itemexch_wish WHERE userid = $myUserid AND itemid = $itemId LIMIT 1";
		 $row = $cronDB->getRow($query);
		 if (!$row)
         {
			 $values = array();
			 $values['userid'] = $myUserid;
			 $values['itemid'] = $itemId;
			 $values['date_added'] = time();
			 $query = $cronDB->buildQuery( 'INSERT', '#__itemexch_wish', $values);
             $cronDB->insert($query);
             $row = new stdClass();
             $row->msg = ucwords($title)." is now on your wish list.";
             $row->optionindex = 4;
             $row->itemid = $itemId;
             $row->error = 0;
         }
         else
        {
             $row->msg = "You are already have ".$title." on your wish list.";
             $row->optionindex = 4;
             $row->error = 0;
        }
        return $row;
	}
    private function _userTrading($myUserid, $itemId, $title = '')
    {
		if ($title == '') $title = "this";
		if (!is_numeric($myUserid) || !is_numeric($itemId))
		{
			 $msg = "Invalid Data";
			 $this->throwAjaxError($msg);
		}
		$cronDB = new CronDb();
		$query = "SELECT * FROM #__itemexch_items WHERE userid = $myUserid AND itemid = $itemId";
        $row = $cronDB->getRow($query);
        $data = array();
             if (!$row)
             {
                 $row = new stdClass();
                 $row->msg = "Are you sure you want to trade ".$title."?";
                 $row->optionindex = 1;
                 $row->itemid = $itemId;
                 $row->error = 0;
             }
             else
             {
                $row->msg = "You are already trading ".$title." ($row->numof copies). You want to trade more?";
                $row->optionindex = 2;
                $row->error = 0;
             }
         $row->yesno = 1;
         return $row;
    }
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
          $view->assign('menuid', $this->itemid);
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
        //print_r($row);
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
            if ($row_2 && isset($row_2->num_of) && isset($row->numof) && ((int)$row->numof <  (int)$row_2->num_of)){
                $row3->msg = "You have already requested this item  $row_2->num_of times";
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
    public function canbetraded()
    {
		if (!isset($_POST['props'])) { $this->throwAjaxError("Invalid Data"); }
		$data = json_decode($_POST['props']);
	    $user =  JFactory::getUser();
	    $tradesModel = $this->getModel ( 'trades', 'ieModel');
        list($rows, $limitstart, $pagination) = $tradesModel->getRequstedByUser($data->userid, $user->id);
	    echo ($rows) ? json_encode($rows) : 0;
		exit;
	}
    public function requestsFrom()//items that belongs to other users, not you
    {
        $app = JFactory::getApplication();
        $user =  JFactory::getUser();
        $userid = $app->input->getInt('userid', 0);
        //if (!is_numeric($userid) || $userid == 0) { echo "ERROR - invalid data"; exit; }
        $document = JFactory::getDocument();
        $tradesModel = $this->getModel ( 'trades', 'ieModel');
        $rows = $tradesModel->getRequstedByUser($user->id, $userid);
        if ($rows && is_array($rows)) { list($rows, $limitstart, $pagination) = $rows; }
        $viewType = $document->getType();
        jscssScripts::jsInclude( 'com_itemexchange', 'assets/css/styles.css');
        $view  = $this->getView('userrequested',$viewType);
        $view->assign('basicExchange', new basicsExchange());
        $view->assign('userid', $user->id);
        $view->assign('otheruserid', $userid);
        $view->assign('tradesModel', $tradesModel);
         $view->assign('menuid', $this->itemid);
        if (!isset($pagination)) $pagination = '';
        $view->assign('pagination', $pagination);
        $view->assign('userData', JFactory::getUser($userid));
        $view->assign('items', $rows);
        $cronDB = new CronDb();
        $view->assign('cronDB', $cronDB);
        
        $view->display();
    }
    public function userProfile()
    {
        $data = $_POST;
        $userid = (isset($data['userid'])) ? $data['userid'] : 0;
        echo JRoute::_("index.php?option=com_itemexchange&view=useritems&task=users&Itemid=".$this->itemid."&userid=".$userid);
        //echo JRoute::_("index.php?option=com_community&view=profile&Itemid=".$be->getItemId()."&userid=".$userid);
        exit;
    }
    public function itemRequests()
    {
        $data = $_POST;
        $db = JFactory::getDbo();
        $query = "SELECT a.*, b.username, c.thumb FROM #__itemexch_trades as a LEFT JOIN #__community_users as c ON a.buyer = c.userid INNER JOIN #__users as b on a.buyer = b.id WHERE a.seller = ".(int)$data['userid']." AND a.item_id = ".(int)$data['itemid']." LIMIT 100";
        $rows = $db->setQuery($query)->loadObjectList();
        //echo $query;
        $rows_2 = false;

        if ($rows)
        {
            $i = 0;
            $rows_2 = array();
            foreach ($rows as $rw)
            {
               $rw->userurl = JRoute::_('index.php?option=com_itemexchange&task=users&userid='.$rw->buyer."&Itemid=".$this->itemid);
               $userHasRequested = $this->userHasRequested($rw->seller, $rw->buyer);
               $rw->requested = $userHasRequested;
               
               $rw->requestedURL = ($userHasRequested) ? JRoute::_('index.php?option=com_itemexchange&task=requestsFrom&userid='.$rw->buyer."&Itemid=".$this->itemid) : '';
               
               $rows_2[$i] = $rw;
                $i++;
            }
        }
        echo ($rows_2) ? json_encode($rows_2) : 0;
        exit;
    }
    private function userHasRequested($buyer, $seller, $is_sent = 0, $is_verified = 0)
    {
	    $query = "SELECT count(*) FROM #__itemexch_trades WHERE is_sent = $is_sent AND is_verified = $is_verified AND buyer = $buyer AND seller = $seller LIMIT 1";
	    $cronDB = new CronDb();
	    $verifyRequests = $cronDB->getOneValue($query);
        return ($verifyRequests) ? true : false;
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
        //echo $query;
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
    public function ownItem()//deprecated???
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
