<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.model');
class ieModelTrades extends JModelLegacy 
{
    var $cronDb;
    public function __construct() { parent::__construct(); $this->cronDb = new CronDb(); }
    public function getMyItems($userid)
    {
        if (!is_numeric($userid) || $userid == 0) { echo "ERROR - invalid userid"; return false; }
        $query = "SELECT *, a.date_added as date_posted FROM #__itemexch_items as a INNER JOIN #__directcron_items as b ON a.itemid = b.id WHERE a.userid= $userid";
        $rows = $this->cronDb->getRows($query);
        return $rows;
    }
}


	//defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

	/*global $mosConfig_absolute_path, $mosConfig_offset_user, $mainframe;

	// load UDDEIM (PMS) component configuration
	require_once($mainframe->getCfg('absolute_path') . "/administrator/components/com_uddeim/config.class.php");
	// load UDDEIM component encryption file
	require_once($mainframe->getCfg('absolute_path') . "/components/com_uddeim/crypt.class.php");	
	require_once($mainframe->getCfg('absolute_path') . "/administrator/components/com_uddeim/admin.shared.php");	
*/
class mosDBTable
{
    
}
	class synJmoviesPost extends mosDBTable
	{
		/** @var int */
		var $id = null;
		/** @var int */
		var $user_id = null;
		/** @var int */
		var $jmovies_id = null;
		/** @var int */
		var $trade_points = null;
		/** @var date */
		var $post_date = null;
		/** @var int */
		var $is_requested = null;
		
		function synJmoviesPost( &$db )
		{
			$this->mosDBTable( '#__jmovies_posts', 'id', $db );
		}
		
		function loadMovie( $movieid )
		{
			global $database;
			
			$query = 'SELECT id' 
					. ' FROM #__jmovies_posts' 
					. ' WHERE jmovies_id =' . (int)$movieid;
			$database->setQuery( $query );
			$id = $database->loadResult();
			
			$this->load( $id ? $id : 0 );
		}
		function getMovie($movieid)//Redacron alteration: this function was added to get information about a movie
                {
                    global $database;

			$database->setQuery("SELECT id, titolo, pathtrailer, widthtrailer, heighttrailer, autotrailer, duratatrailer, fontetrailer, allvideosfonte, allvideosfonteid, allvideostype, allvideospath, countervideo FROM #__jmovies WHERE id = ".(int)$movieid). " LIMIT 1";
		        $result1 = $database->query();
		        $rows = $database->loadObjectList();
                        return $rows[0];

                }
		function post( $user_id, $jmovies_id, $trade_points, $post_date, $is_requested )
		{
			$this->id = 0;
			$this->user_id = $user_id;
			$this->jmovies_id = $jmovies_id;
			$this->trade_points = $trade_points;
			$this->post_date = $post_date;
			$this->is_requested = $is_requested;
			
			$this->check();
			
			if( $this->store() )
			{
				return true;
			} else {
				return false;
			}
		}
		
		function updateRequestStatus( $new_status )
		{
			global $database;
			$query = 'UPDATE #__jmovies_posts'
					. ' SET is_requested =' . (int)$new_status
					. ' WHERE jmovies_id=' . (int)$this->jmovies_id.' AND is_requested = 0 LIMIT 1';//Redacron Alteration
			/*
                         * This is the original query, which uses the row id instead:
                         *
                         * $query = 'UPDATE #__jmovies_posts'
					. ' SET is_requested =' . (int)$new_status
					. ' WHERE id=' . (int)$this->id;//Redacron Alteration
                                        //We limit this to 1 because there might be more of the same movie*/
			$database->setQuery( $query );
			$database->query();

			return true;
		}
		
		function deletePost()
		{
			global $database;
			
			$query = 'DELETE FROM #__jmovies_posts'
					. ' WHERE id=' . (int)$this->id;
			$database->setQuery( $query );
			if( $database->query() )
			{
				return true;
			} else {
				return false;
			}
		}
		
		function getTradeCount()
		{
			global $database;
			
			$query = 'SELECT COUNT(jmovies_id)'
					. ' FROM #__jmovies_posts'
					. ' WHERE jmovies_id =' . (int)$this->jmovies_id
					. ' AND is_requested=0';
			$database->setQuery( $query );

			return $database->loadResult();
		}
		
		function isMovieSold()
		{
			if(is_null($this->jmovies_id)) {  return true;  }
		}
		
		function getItems()
		{
			global $database;
			
			$query = 'SELECT jp.user_id, jp.jmovies_id, jp.trade_points, jp.post_date, jp.is_requested'
					. ', j.titolo'
					. ' FROM #__jmovies_points jp'
					. ' LEFT JOIN #__jmovies j'
					. ' ON jp.jmovies_id = j.id';
			$database->setQuery( $query );
			return $database->loadAssocList();
		}
	}
	
	class synJmoviesTrading extends mosDBTable
	{
		/** @var int */
		var $id = null;
		/** @var int */
		var $trade_no = null;
		/** @var int */
		var $buyer = null;
		/** @var int */
		var $seller = null;
		/** @var post_id */
		var $post_id = null;
		/** @var date */
		var $trade_date = null;
		/** @var int */
		var $is_varified = null;
		/** @var int */
		var $is_sent = null;
		/** @var text */
		var $tracking = null;
		/** @var int */
		var $show_buyer = null;
		/** @var int */
		var $show_seller = null;
		
		function synJmoviesTrading( &$db )
		{
			$this->mosDBTable( '#__jmovies_trades', 'id', $db );
		}
		
		function loadPost( $post_id )
		{
			global $database;
			
			$query = 'SELECT id'
					. ' FROM #__jmovies_trades'
					. ' WHERE post_id=' . (int)$post_id;
			$database->setQuery( $query );
			
			$this->load($id ? $id : 0);			
		}
	        function getMovieId( $post_id)//Redacron function
                {
                     global $database;

			$query = 'SELECT jmovies_id'
					. ' FROM #__jmovies_posts'
					. ' WHERE id=' . (int)$post_id . ' LIMIT 1';
			$database->setQuery( $query );

			$id = $database->loadResult();
                        return (int) $id;
                }
		function getListByMovie( $movie_id )
		{
			global $database;
			
			$query = 'SELECT jt.*, j.titolo, j.titolo2, u1.name `Buyer`, u2.name `Seller`'
					. ' FROM #__jmovies_trades jt, #__jmovies_posts jp, #__jmovies j, #__users u1, #__users u2'
					. ' WHERE j.id = jp.jmovies_id'
					. ' AND jp.is_requested = 1'
					. ' AND jp.id = jt.post_id'
					. ' AND u1.id = jt.buyer'
					. ' AND u2.id = jt.seller'
					. ' AND jp.jmovies_id =' . (int)$movie_id;
			$database->setQuery( $query );
			
			return $database->loadAssocList();
		}

		function getListByBuyer( $buyer_id )
		{
			global $database;
			
			$query = 'SELECT jt.*, j.titolo, j.titolo2, u1.name `Buyer`, u2.name `Seller`'
					. ' FROM #__jmovies_trades jt, #__jmovies_posts jp, #__jmovies j, #__users u1, #__users u2'
					. ' WHERE j.id = jp.jmovies_id'
					. ' AND jp.is_requested = 1'
					. ' AND jp.id = jt.post_id'
					. ' AND u1.id = jt.buyer'
					. ' AND u2.id = jt.seller'
					. ' AND jt.buyer =' . (int)$buyer_id;
			$database->setQuery( $query );
			
			return $database->loadAssocList();
		}

		function getListBySeller( $seller_id )
		{
			global $database;
			
			$query = 'SELECT jt.*, j.titolo, j.titolo2, u1.name `Buyer`, u2.name `Seller`'
					. ' FROM #__jmovies_trades jt, #__jmovies_posts jp, #__jmovies j, #__users u1, #__users u2'
					. ' WHERE j.id = jp.jmovies_id'
					. ' AND jp.is_requested = 1'
					. ' AND jp.id = jt.post_id'
					. ' AND u1.id = jt.buyer'
					. ' AND u2.id = jt.seller'
					. ' AND jt.seller =' . (int)$seller_id;
			$database->setQuery( $query );
			
			return $database->loadAssocList();
		}
		
		function verifyTrade( $status, $buyerid = '')//Redacron alteration, there was no ID here before
		{
			global $database;
			
			if(is_bool($status))
			{
				if($status) {
					$new_status = 1;
				} else {
					$new_status = 0;
				}
			}
			elseif (is_numeric($status))
			{
				if($status == 0) {
					$new_status = 0;
				} else {
					$new_status = 1;
				}
			}
			else { return false; }
			
			
			// update varified status and 
			// if status is 1 (verify) then
			// remove trade from seller list
                        /* Alteration by Redacron Studios: show_seller is no longer being set to 1 */
			$query = 'UPDATE #__jmovies_trades'
					. ' SET is_varified=' . (int)$new_status
					/*. ( $new_status == 1 ? ' , show_seller=1' : '' )*/
					. ' WHERE id=' . (int)$this->id;
			$database->setQuery( $query );
                        $database->query();
			//Redacron alteration, put buyer as dealer now:
                        $query = "UPDATE #__jmovies_posts SET is_requested=0, post_date='".date( 'Y-m-d H:i:s' )."' ";
                        if (is_numeric($buyerid)) $query .= ", user_id = $buyerid ";
                        $query .= "WHERE id=" . (int)$this->post_id;
			$database->setQuery( $query );
			$database->query();
			if($database->getErrorNum()) {
				return false;
			}
			if($database->query())
			{
				@$this->__SendVerifiedMailToBuyer( false );
				return true;
			} else {
				return false;
			}
		}
		
		function sendItem( $status )
		{
			global $database;

			if(is_bool($status))
			{
				if($status) {
					$new_status = 1;
				} else {
					$new_status = 0;
				}
			}
			elseif (is_numeric($status))
			{
				if($status == 0) {
					$new_status = 0;
				} else {
					$new_status = 1;
				}
			}
			else { return false; }
			
			$query = 'UPDATE #__jmovies_trades'
					. ' SET is_sent=' . (int)$new_status
					. ' WHERE id=' . (int)$this->id;
			$database->setQuery( $query );
			
			if($database->query())
			{
				return true;
			} else {
				return false;
			}		
		}
		
		function updateTracking( $tracking_details )
		{
			global $database;
			
			$query = 'UPDATE #__jmovies_trades'
					. ' SET tracking=\'' . $tracking_details . '\''
					. ' WHERE id=' . (int)$this->id;
			$database->setQuery( $query );
			
			if($database->query())
			{
				return true;
			} else {
				return false;
			}
		}
		
		function tradeItem( $buyer, $seller, $post_id, $trade_date, $is_varified, $is_sent, $tracking=null )
		{
			$this->id = 0;
			$this->trade_no = time();
			$this->buyer = $buyer;
			$this->seller = $seller;
			$this->post_id = $post_id;
			$this->trade_date = $trade_date;
			$this->is_varified = $is_varified;
			$this->is_sent = $is_sent;
			if($tracking != null)
			{
				$this->tracking = $tracking;
			}
			$this->show_buyer = 1;
			$this->show_seller = 1;
			
			$this->check();
			
			if($this->store())
			{
				@$this->__SendMailToSeller( false );//Redacron enabled this
				@$this->__SendVerificationMailToBuyer( false );//Redacron enabled this
				return true;
			} else {
				return false;
			}
		}
		
		function deleteTrade()
		{
			global $database, $cinConfig;
			$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
                                if ( file_exists($api_AUP))
                                {
                                   require_once ($api_AUP);
                                }
                                else return false;
			if($this->is_varified == 1)
			{
				//$objSeller = new mosUser( $database );
				//$objSeller->load( $this->seller );
                                /*
                         * We now get the points from the seller
                         * From now on,
                         * The tradepoints we are going to use come from AlphaUserPoints:
                         *  Eventually we need to get rid of var tradepoints in the class in order to avoid trouble
                         * Alteration made by Redacron.com for mydvdtrader.com on November 22nd. 2009
                         *
                         */
                     
                                   $points = AlphaUserPointsHelper::getUserInfo ('', $this->seller );
                                   $tradepoints = $points->points;

                                
                                
				//$objSeller->tradepoints -= 1;
                                //$tradepoints -= 1;

                                $aupid = AlphaUserPointsHelper::getAnyUserReferreID( $this->seller );
                                AlphaUserPointsHelper::newpoints( 'plgaup_request_item',$aupid, '', '', -1);

				//if(!$objSeller->store()) {  return false;  }
			}
			//code by seowebmedia
			$objPost = new synJmoviesPost( $database );
			$objPost->load($this->post_id);
			$mov_id = $objPost->jmovies_id;
			$points = AlphaUserPointsHelper::getUserInfo ('', $this->buyer );
                        $tradepoints = $points->points;
			/*$objBuyer = new mosUser( $database );
			$objBuyer->load( $this->buyer );*/
			//$objBuyer->tradepoints += (int)$cinConfig['trade_cost'];
			//added by seowebmedia
			//$objBuyer->tradepoints += getReedemPointForMovie($mov_id);
                        $aupid = AlphaUserPointsHelper::getAnyUserReferreID( $this->buyer );
                        AlphaUserPointsHelper::newpoints( 'plgaup_request_item',$aupid, '', '', getReedemPointForMovie($mov_id));
			//if(!$objBuyer->store()) {  return false;  }
			
			$query = "UPDATE #__jmovies_posts SET is_requested=0 WHERE id=" . (int)$this->post_id;
			$database->setQuery( $query );
			$database->query();
			if($database->getErrorNum()) {
				return false;
			}
			/*$query = "DELETE FROM #__jmovies_posts WHERE id=" . (int)$this->post_id;
			$database->setQuery( $query );
			$database->query();
			if($database->getErrorNum()) {
				return false;
			}*/
			
			$query = "DELETE FROM #__jmovies_trades WHERE id=" . (int)$this->id;
			$database->setQuery( $query );
			$database->query();
			if($database->getErrorNum()) {
				return false;
			}
			
			$this->__SendTradeCancelMailToBuyer( false );//Redacron enabled this
			$this->__SendTradeCancelMailToSeller( false );//Redacron enabled this
			return true;
		}
		
		function removeFromBuyerList()
		{
			$this->show_buyer = 0;
			return $this->store();
		}
		
		function __SendTradeCancelMailToBuyer( $pms_only=true )
		{
			global $database, $mosConfig_fromname, $mosConfig_mailfrom, $mosConfig_sitename, $cinConfig;
			$objPost = new synJmoviesPost( $database );
			$objPost->load( (int)$this->post_id );

			$pms_admin = $cinConfig['pms_admin'];
		
			$buyer = new synJMoviesUser( $database );
			$buyer->loadUser( (int)$this->buyer );

			$seller = new synJMoviesUser( $database );
			$seller->loadUser( (int)$this->seller );

			$query = "SELECT titolo `title`, titolo2 `title2`, descrizione `description` FROM #__jmovies WHERE id=" . (int)$objPost->jmovies_id;
			$database->setQuery( $query );
			$database->loadObject( $movie );

			$pms_message = " This is an automated message informaing you that, your trade [b]" . $this->trade_no . "[/b], in which you requested the";
			$pms_message .= " item entitled [u]" . $movie->title . "[/u] has been cancelled by the website administrators.\n";
			$pms_message .= "If you have any questions you may contact us at [b]" . $mosConfig_mailfrom . "[/b].\n\n";
			$pms_message .= "\n\n" . "Thank you for using " . $mosConfig_sitename;
			$pms_message .= "\n\n" . "[u]This is an automated message. Please do not reply.[/u]";

			$pms_mailer = new synJMoviesPMS();
			$pms_mailer->saveMessage( (int)$pms_admin, (int)$this->buyer, $pms_message, $buyer->username, true );

			if( !$pms_only )
			{
				$body = '<div style="font-family:Verdana, Tahoma, Arial; font-size:12px;">';
				$body .= " This is an automated message informaing you that, your trade " . $this->trade_no . ", in which you requested the";
				$body .= " item entitled '" . $movie->title . "' has been cancelled by the website administrators.<br>";
				$body .= 'If you have any questions you may contact us at <a href="mailto:' . $mosConfig_mailfrom . '">' . $mosConfig_mailfrom . '</a><br><br>';
				$body .= "<br><br>" . "Thank you for using " . $mosConfig_sitename;
				$body .= "<br><br>" . "This is an automated message. Please do not reply.";
				$body .= "</div>";
				$subject = "Trade Cancelled";
				                                if (class_exists('JUtility'))//Redacron Alteration: mosMail may soon be a thing of the past
/*$ret = JUTility:: sendMail($mosConfig_mailfrom, $mosConfig_fromname,  $buyer->email, $subject, $body, 0, null, null, null, null, null);
else */mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $buyer->email, $subject, $body, 1 );
			}
		}

		function __SendTradeCancelMailToSeller( $pms_only=true )
		{
			global $database, $mosConfig_fromname, $mosConfig_mailfrom, $mosConfig_sitename, $cinConfig;
			$objPost = new synJmoviesPost( $database );
			$objPost->load( (int)$this->post_id );
			
			$pms_admin = $cinConfig['pms_admin'];

			$buyer = new synJMoviesUser( $database );
			$buyer->loadUser( (int)$this->buyer );

			$seller = new synJMoviesUser( $database );
			$seller->loadUser( (int)$this->seller );

			$query = "SELECT titolo `title`, titolo2 `title2`, descrizione `description` FROM #__jmovies WHERE id=" . (int)$objPost->jmovies_id;
			$database->setQuery( $query );
			$database->loadObject( $movie );

			$pms_message = " This is an automated message informaing you that, trade [b]" . $this->trade_no . "[/b], for ";
			$pms_message .= " item entitled [u]" . $movie->title . "[/u] which is requested by [u]" . $seller->name . "[/u] has been cancelled";
			$pms_message .= " by the website administrators.\n";
			$pms_message .= "If you have any questions you may contact us at [b]" . $mosConfig_mailfrom . "[/b]\n\n";
			$pms_message .= "\n\n" . "Thank you for using " . $mosConfig_sitename;
			$pms_message .= "\n\n[u]" . "This is an automated message. Please do not reply.[/u]";

			$pms_mailer = new synJMoviesPMS();
			$pms_mailer->saveMessage( (int)$pms_admin, (int)$this->seller, $pms_message, $seller->username, true );

			if( !$pms_only )
			{
				$body = '<div style="font-family:Verdana, Tahoma, Arial; font-size:12px;">';
				$body .= " This is an automated message informaing you that, trade " . $this->trade_no . ", for ";
				$body .= " item entitled '" . $movie->title . "' which is requested by " . $seller->name . " has been cancelled";
				$body .= " by the website administrators.<br>";
				$body .= 'If you have any questions you may contact us at <a href="mailto:' . $mosConfig_mailfrom . '">' . $mosConfig_mailfrom . '</a><br><br>';
				$body .= "<br><br>" . "Thank you for using " . $mosConfig_sitename;
				$body .= "<br><br>" . "This is an automated message. Please do not reply.";
				$body .= "</div>";
				$subject = "Trade Cancelled";
	
                                if (class_exists('JUtility'))//Redacron Alteration: mosMail may soon be a thing of the past
/*$ret = JUTility:: sendMail($mosConfig_mailfrom, $mosConfig_fromname,  $buyer->email, $subject, $body, 0, null, null, null, null, null);
else*/ mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $buyer->email, $subject, $body, 1 );
			}
		}
		
		function __SendVerifiedMailToBuyer( $pms_only=true )
		{
			global $database, $mosConfig_fromname, $mosConfig_mailfrom, $mosConfig_sitename, $cinConfig;
			$objPost = new synJmoviesPost( $database );
			$objPost->load( (int)$this->post_id );
			
			$pms_admin = $cinConfig['pms_admin'];

			$buyer = new synJMoviesUser( $database );
			$buyer->loadUser( (int)$this->buyer );

			$query = "SELECT titolo `title`, titolo2 `title2`, descrizione `description` FROM #__jmovies WHERE id=" . (int)$objPost->jmovies_id;
			$database->setQuery( $query );
			$database->loadObject( $movie );
			
			$pms_message = " You have just verified the trade number [b]'" . $this->trade_no . "'[/b]. In which you received the";
			$pms_message .= " item entitled [u]" . $movie->title . "[/u]. If you did not initiate this verification, please notify";
			$pms_message .= " us immediately at [b]" . $mosConfig_mailfrom . "[/b].\n\n";
			$pms_message .= "\n\n" . "Thank you for using " . $mosConfig_sitename;
			$pms_message .= "\n\n[u]" . "This is an automated message. Please do not reply.[/u]";
			
			$pms_mailer = new synJMoviesPMS();
			$pms_mailer->saveMessage( (int)$pms_admin, (int)$this->buyer, $pms_message, $buyer->username, true );
			
			if(!$pms_only)
			{
				$body = '<div style="font-family:Verdana, Tahoma, Arial; font-size:12px;">';
				$body .= " You have just verified the trade number '" . $this->trade_no . "'. In which you received the";
				$body .= " item entitled '" . $movie->title . "'. If you did not initiate this verification, please notify";
				$body .= " us immediately at " . $mosConfig_mailfrom . ".<br><br>";
				$body .= "<br><br>" . "Thank you for using " . $mosConfig_sitename;
				$body .= "<br><br>" . "This is an automated message. Please do not reply.";
				$body .= "</div>";
				$subject = "Trade Verified";

			                       // if (class_exists('JUtility'))//Redacron Alteration: mosMail may soon be a thing of the past
/*$ret = JUTility:: sendMail($mosConfig_mailfrom, $mosConfig_fromname,  $buyer->email, $subject, $body, 0, null, null, null, null, null);
else*/ //mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $buyer->email, $subject, $body, 1 );
			}
		}
		
		function __SendVerificationMailToBuyer( $pms_only=true )
		{
			global $database, $mosConfig_fromname, $mosConfig_mailfrom, $mosConfig_sitename, $cinConfig;



			$pms_admin = $cinConfig['pms_admin'];
			
			$objPost = new synJmoviesPost( $database );
			$objPost->load( (int)$this->post_id );
			
			$seller = new synJMoviesUser( $database );
			$seller->loadUser( (int)$this->seller );
			
			$buyer = new synJMoviesUser( $database );
			$buyer->loadUser( (int)$this->buyer );

			$query = "SELECT titolo `title`, titolo2 `title2`, descrizione `description` FROM #__jmovies WHERE id=" . (int)$objPost->jmovies_id;
			$database->setQuery( $query );
			$database->loadObject( $movie );
			
			$pms_message = "This message is to confirm your recent trade request on " . $mosConfig_sitename;
			$pms_message .= "\n\n" . "You requested an the item entitled '" . $movie->title . "'. Your trade request has been";
			$pms_message .= " delivered to [b]" . $seller->name . "[/b], one of our users who posesses this item.";
			$pms_message .= "\n\n" . "Please be sure to verify the trade through your account management panel after you have successfully";
			$pms_message .= " received this item. If there is a problem, you are required to report it to us at " . $mosConfig_mailfrom;
			$pms_message .= " and quote the trade ID number [b]'" . $this->trade_no . "'[/b]. If you do not report";
			$pms_message .= " any problems within 2 weeks, the trade will be automatically verified.";
			$pms_message .= "\n\n" . "Thank you for using " . $mosConfig_sitename;
             $pms_message .= "\n\n<br /><p>All verified trades are subjected to automatic reposting, so they can become available again for other ";
             $pms_message .= "users in the community. We suggest at this time, that you kindly remove immediatly any reposted trades in your ";
             $pms_message .= "inventory, that you dont desire to trade again,to avoid future issues for yourself and others in the community.</p>";
			$pms_message .= "\n\n[u]" . "This is an automated message. Please do not reply.[/u]";
			
			$pms_mailer = new synJMoviesPMS();
			$pms_mailer->saveMessage( (int)$pms_admin, (int)$this->buyer, $pms_message, $buyer->username, true );
			
			if( !$pms_only )
			{
				$body = '<div style="font-family:Verdana, Tahoma, Arial; font-size:12px;">';
				$body .= "This message is to confirm your recent trade request on " . $mosConfig_sitename;
				$body .= "<br><br>" . "You requested an the item entitled '" . $movie->title . "'. Your trade request has been";
				$body .= " delivered to " . $seller->name . ", one of our users who posesses this item.";
				$body .= "<br><br>" . "You may contact " . $seller->name . " at <a href=\"mailto:" . $seller->email . "\">" . $seller->email . "</a>.";
				$body .= "<br><br>" . "Please be sure to verify the trade through your account management panel after you have successfully";
				$body .= " received this item. If there is a problem, you are required to report it to us at <a href=\"mailto:" . $mosConfig_mailfrom;
				$body .= "\">" . $mosConfig_mailfrom . "</a> and quote the trade ID number '" . $this->trade_no . "'. If you do not report";
				$body .= " any problems within 2 weeks, the trade will be automatically verified.";
                 $body .= "<p>All verified trades are subjected to automatic reposting, so they can become available again for other ";
             $body .= "users in the community. We suggest at this time,that you kindly remove immediatly any reposted trades in your ";
             $body .= "inventory, that you dont desire to trade again, to avoid future issues for yourself and others in the community.</p>";
				$body .= "<br><br>" . "Thank you for using " . $mosConfig_sitename;
				$body .= "<br><br>" . "This is an automated message. Please do not reply.</div>";
				
				$subject = "Trade Initiated";
				

                        if (class_exists('JUtility'))//Redacron Alteration: mosMail may soon be a thing of the past
/*$ret = JUTility:: sendMail($mosConfig_mailfrom, $mosConfig_fromname,  $buyer->email, $subject, $body, 0, null, null, null, null, null);
else*/ mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $buyer->email, $subject, $body, 1 );

                        }
		}
		
		function __SendMailToSeller( $pms_only=true )
		{
			global $database, $mosConfig_fromname, $mosConfig_mailfrom, $mosConfig_sitename, $cinConfig;

			$pms_admin = $cinConfig['pms_admin'];
			
			$objPost = new synJmoviesPost( $database );
			$objPost->load( (int)$this->post_id );
			
			$buyer = new synJMoviesUser( $database );
			$buyer->loadUser( (int)$this->buyer );
		
			$seller = new synJMoviesUser( $database );
			$seller->loadUser( (int)$this->seller );
			
			$query = "SELECT titolo `title`, titolo2 `title2`, descrizione `description` FROM #__jmovies WHERE id=" . (int)$objPost->jmovies_id;
			$database->setQuery( $query );
			$database->loadObject( $movie );
			
			$buyer_address = $buyer->name . "\n" . $buyer->address1 . "\n" . $buyer->address2 . "\n" . 
						$buyer->city . "\n" . $buyer->postcode . "\n" . $buyer->state;

			$pms_message = "A trade has been initiated for an item that you have posted to " . $mosConfig_sitename . ".";
			$pms_message .= "\n\n" . "Please send your item entitled '" . $movie->title . "' to:";
			$pms_message .= "\n" . $buyer_address;
			$pms_message .= "\n\n" . "Trade Reference ID: [b]" . $this->trade_no . "[/b]";
			$pms_message .= "\n\n" . "The user who request this item is [b]" . $buyer->username . "[/b].";
			$pms_message .= "\n\n" . "In order to assist you trading member with a better trading experience, please dont forget";
			$pms_message .= " to include a USPS confirmation number, along with reply.";
			$pms_message .= "\n\n" . "Thank you for using " . $mosConfig_sitename;
			$pms_message .= "\n\n[u]" . "This is an automated message. Please do not reply.[/u]";
            $pms_message .=  "\n\n<br /><p>Warning: Please do not delete any items within your account until your trade has been verified by your ";
            $pms_message .= "intended receiver. This helps to avoid future related issues for yourself and that of your intended receiver.</p>";
			
			$pms_mailer = new synJMoviesPMS();
			$pms_mailer->saveMessage( (int)$pms_admin, (int)$this->seller, $pms_message, $seller->username, true );
			
			if(!$pms_only)	{
				$subject = "Earn " . $mosConfig_sitename . " points - A trade has been requested";

				$buyer_address = $buyer->name . "<br>" . $buyer->address1 . "<br>" . $buyer->address2 . "<br>" . 
							$buyer->city . "<br>" . $buyer->postcode . "<br>" . $buyer->state;

				$body = '<div style="font-family:Verdana, Tahoma, Arial; font-size:12px;">';
				$body .= "A trade has been initiated for an item that you have posted to " . $mosConfig_sitename . ".";
				$body .= "<br><br>" . "Please send your item entitled '" . $movie->title . "' to:";
				$body .= "<br>" . $buyer_address;
				$body .= "<br><br>" . "Trade Reference ID: " . $this->trade_no;
				$body .= "<br><br>" . "You may contact this user at <a href=\"mailto:" . $buyer->email . "\">" . $buyer->email . "</a>";
				$body .= "<br><br>" . "In order to assist you trading member with a better trading experience, please dont forget";
				$body .= " to include a USPS confirmation number, along with reply.";
				$body .= "<br><br>" . "Thank you for using " . $mosConfig_sitename;
				$body .= "<br><br>" . "This is an automated message. Please do not reply.";
                $body .= "<p><br />Warning: Please do not delete any items within your account until your trade has been verified by your ";
                $body .= "intended receiver. This helps to avoid future related issues for yourself and that of your intended receiver.</p>";
				$body .= "</div>";
				mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $seller->email, $subject, $body, 1 );



                        }
		}
	}
	
	class synJMoviesUser extends mosDBTable
	{
		/** @var int */
		var $id = null;
		/** @var text */
		var $name = null;
		/** @var text */
		var $username = null;
		/** @var text */
		var $email = null;
		/** @var text */
		var $address1 = null;
		/** @var text */
		var $address2 = null;
		/** @var text */
		var $postcode = null;
		/** @var int */
		var $state_id = null;
		/** @var text */
		var $state = null;
		/** @var int */
		var $city_id = null;
		/** @var text */
		var $city = null;
		/** @var text */
		var $phone = null;
		/** @var text */
		var $fax = null;
		/** @var int */
		var $tradepoints = null;
		
		function synJMoviesUser( &$db )
		{
			$this->mosDBTable( '#__users', 'id', $db );
                       
		}
		
		function loadUser( $id )
		{
			global $database;
			//Redacron altereation: query deprecated:
			/*$query = 'SELECT u.id, u.name, u.username, u.email, u.tradepoints, p.cb_address, p.cb_suburb, p.cb_city, p.cb_state,'
					. ' p.cb_postcode, p.cb_phone, s.state_name, c.city'
					. ' FROM #__users u, #__comprofiler p'
					. ' LEFT JOIN #__states s ON p.cb_state = s.state_id'
					. ' LEFT JOIN #__cities c ON p.cb_city = c.id'
					. ' WHERE p.id = u.id AND u.id=' . (int)$id;**/
                                  $query = 'SELECT u.id, u.name, u.username, u.email, p.cb_address, p.cb_suburb, p.cb_city, p.cb_state,'
					. ' p.cb_postcode, p.cb_phone'
					. ' FROM #__users u, #__comprofiler p'
					. ' WHERE p.id = u.id AND u.id=' . (int)$id;

                                        
			$database->setQuery( $query );
			$database->loadObject( $row );
                        //echo "<br />";print_r($row);echo"<br />";
			if( !$row )	{
				return false;
			} else {
				$this->id 			= $row->id;
				$this->name 		= $row->name;
				$this->username 	= $row->username;
				$this->email 		= $row->email;
				$this->address1 	= $row->cb_address;
				$this->address2 	= $row->cb_suburb;
				$this->postcode 	= $row->cb_postcode;
				$this->state_id 	= $row->state_id;
				$this->state 		= $row->cb_state;
				$this->city_id 		= $row->city_id;
				$this->city 		= $row->cb_city;
				$this->phone 		= $row->cb_phone;
				$this->fax 			= $row->fax;
				//$this->tradepoints 	= $row->tradepoints;
                                /*
                                 *
                                 * From now on, we get the points from the AlphaUserPoints component instead:
                                 * Alteration made by Redacron.com on November 22nd, 2009
                                 * 
                                 */
//print_r($this);
                                $api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
                                if ( file_exists($api_AUP))
                                {
                                   require_once ($api_AUP);
                                   $points = AlphaUserPointsHelper::getUserInfo ('', $row->id);
                                   $this->tradepoints = $points->points;
                                  
                                }
                                else $this->tradepoints = "ERROR";
			}
		}
		
		function canPostItem( $movie_id, &$reason, $user_id=0, $amazon=false  )
		{
			global $database, $my;
			
			if(is_null($user_id) || $user_id == 0)
			{
				if(is_null($this->id))	{
					$reason = 'NOUSER';
					return false;
				} else {
					$user_id = $this->id;
				}
			}
                        //Make sure the user has an address:
			$seller = new synJMoviesUser( $database );
			$seller->loadUser($this->id);
                        
                        //Now we must make sure the user gives ua an address (Redacron addition):
                        if ($seller->address1 == '' || $seller->city == '' || $seller->postcode == '' || $seller->state == '')
                        {$reason = "You need to give us your entire address for this work."; return false;}

			if((is_null($movie_id) || (int)$movie_id <= 0) && !$amazon) {  $reason='NOMOVIE'; return false;  }
			
			if((is_null($movie_id) || (int)$movie_id <= 0) && $amazon) {  $reason='NOMOVIE'; return true;  }

			$query = "SELECT COUNT(*) FROM #__jmovies_posts WHERE user_id=" . (int)$user_id . " AND 
						jmovies_id=" . (int)$movie_id . " AND is_requested=0";
			
                        /******************************************************************************************
                         * 
                         *
                         *  Alteration Made by Redacron Studios on November 28, 2009
                         *  Purpose: allowing a user to post an item more than once, but not more than three times
                         * 
                         * 
                         ******************************************************************************************/
                        $database->setQuery( $query );
			if((int)$database->loadResult() > 2) {
				$reason='SELFPOST';
				return false;
			}
			
			$query = "SELECT COUNT(*) FROM #__jmovies_posts WHERE jmovies_id=" . (int)$movie_id . " AND is_requested=0";
			$database->setQuery( $query );
			if((int)$database->loadResult() > 2) {
				$reason = "ALREADYPOSTED";
				return false;
			}
			
			return true;
		}
		
		function canRequestItem( $movie_id, &$reason, $user_id=0 )
		{
			global $database, $my, $mainframe, $cinConfig;

			// No user id specified
			if(is_null($user_id) || $user_id == 0)
			{
				if(is_null($this->id))	{
					$reason = 'NOUSER';
					return false;
				} else {
					$user_id = $this->id;
				}
			}

			// Already requested 2 items on current date
			$query = "SELECT COUNT(*) FROM #__jmovies_posts WHERE user_id=" . (int)$user_id . " AND
						post_date=now()";
                   
			$database->setQuery( $query );
			if((int)$database->loadResult() >= 3)//it would be a good idea to have more than 2 (Redacron)
			{
				$reason='TOOMANYREQUEST';
				return false;
			}
			//Redacron Alteration: there may not be an address:

                        $buyer = new synJMoviesUser( $database );
			$buyer->loadUser($this->id);
                        
                        //Now we must make sure the user gives ua an address (Redacron addition):
                        if ($buyer->address1 == '' || $buyer->city == '' || $buyer->postcode == '' || $buyer->state == '')
                        {$reason = "<a href='".JRoute::_("index.php?option=com_comprofiler&Itemid=300099&task=userDetails")."'>You need to give us your entire address for this work.</a>"; return false;}

			// User status is not active
			include_once($mainframe->getCfg('absolute_path') . "/components/com_acctexp/acctexp.class.php");
			$subscription = new Subscription( $database );
			$subscription->loadUserid( $my->id );
			if($subscription->status != 'Active')
			{
				$reason='INVALIDSTATUS';
				return false;
			}
			
			// Item is requested by the user himself
			if(is_null($movie_id) || (int)$movie_id <= 0) {  return false;  }
			$query = "SELECT COUNT(*) FROM #__jmovies_posts WHERE jmovies_id=" . (int)$movie_id . " AND 
					user_id=" . (int)$user_id . " AND is_requested=0";
			$database->setQuery( $query );
			if((int)$database->loadResult() > 0) {
				$reason = 'SELFREQUEST';
				return false;
			} else {
				// Item is already requested by another user and not yet received
                                /********************************************************************
                                 *
                                 *
                                 *  Redacron Studios alteration made on November 28
                                 *  Purpose, if we don't do it, one movie requiest will be enough to make movie
                                 * unavailable even though there might be more than one
                                 *
                                 *
                                 ****************************************************************/
				$query = "SELECT COUNT(*) FROM #__jmovies_posts WHERE jmovies_id=" . (int)$movie_id . " AND is_requested=0";
			$database->setQuery( $query );
			$quantity = (int)$database->loadResult();//thnis is the movie quantity

                                $query = "SELECT COUNT(*) FROM #__jmovies_posts jp, #__jmovies_trades jt WHERE
					jt.post_id = jp.id AND jp.is_requested=1 AND jp.user_id !=" . (int)$user_id . " AND 
					jt.buyer !=" . (int)$user_id . " AND is_sent=0 AND jp.jmovies_id=" . (int)$movie_id;
				$database->setQuery( $query );
				if((int)$database->loadResult() >= $quantity)
				{
					$reason = 'NOTRADEPOSTED';
					return false;
				}
			}/*
                         *
                         * From now on,
                         * The tradepoints we are going to use come from AlphaUserPoints:
                         *  Eventually we need to get rid of var tradepoints in the class in order to avoid trouble
                         * Alteration made by Redacron.com for mydvdtrader.com on November 22nd. 2009
                         * 
                         */
                     $api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
                                if ( file_exists($api_AUP))
                                {
                                   require_once ($api_AUP);
                                   $points = AlphaUserPointsHelper::getUserInfo ('', $user_id);
                                   $this->tradepoints = $points->points;

                                }
                                else $this->tradepoints = "ERROR";
			
			// Tradepoints are not enought to request current item (int)$cinConfig['trade_cost'] //change by seowebmedia
			if((int)$this->tradepoints == 0 || $this->tradepoints < getReedemPointForMovie($movie_id))
			{
				$reason='NOTENOUGHTRADEPOINTS';
				return false;
			}
/*$objTrade = new synJmoviesTrading( $database );
$objTrade->__SendMailToSeller( false );*/


return true;
		}
		
	}
	
	class synJMoviesAmazon
	{
	
		function makeRequest($locale, $accesskey, $secretkey, $where, $function, $ItemId = '', $ResponseGroup = '')
                {
                    $timeStamp = gmdate("Y-m-d\TH:i:s\Z");
                    $element = "AWSAccessKeyId=$accesskey";
                    if ($where != '') $element .= "&Keywords=$where";
                    if ($ItemId != '') $element .= "&ItemId=$ItemId";
                    $element .= "&Operation=$function";
                    /*. "&PowerSearch=title:not XXXFAKETITLE";*/
                    if ($ResponseGroup != '') $element .= "&ResponseGroup=$ResponseGroup";
                    //$element .= "&SearchIndex=DVD"
                    /* PowerSearch=title:not XXXFAKETITLE  */
                    $element .= "&Service=AWSECommerceService"
                    . "&Timestamp=$timeStamp"
                    . "&Version=2009-03-31";
                    $urlarray = parse_url($locale);
                    $url = $urlarray['host'];
                    $path = $urlarray['path'];
                    $SecretAccessKey = $secretkey;
                    $String = $element;
                    $String = preg_replace('/[\r\n]/', '', $String);
                    $String =  str_replace("%3D", "=",  str_replace("%26", "&", rawurlencode($String)));
                    $PrependString = "GET\n$url\n$path\n" . $String;
                    $Signature = rawurlencode(base64_encode(hash_hmac("sha256", $PrependString, $SecretAccessKey, True)));
                    $request = "http://{$url}{$path}?" . $String . "&Signature=" . $Signature;
                     return $request;
                }
                function callAmazonService( $Operation, $ResponseGroup, $ItemId='',$cinConfig = '')
		{
			global $database;
                        if ($cinConfig == '') global $cinConfig;
                        //print_r($cinConfig);
                        $objMambot = new mosMambot( $database );
			$objMambot->load( $cinConfig['amazon_mambot'] );
                        //print_r($objMambot);
			$objParams = new mosParameters( $objMambot->params );
			$locale = $objParams->get( 'locale', '' );
			$accesskey = $objParams->get( 'AccessKey', '0NC8AB75W0YAHHKF9802' );
			$refid = $objParams->get( 'RefId', '' );
			if(!$refid)
			{
				switch($locale)
				{
					 case 'http://ecs.amazonaws.ca/onca/xml':
						   $refid='joomlashops09-20';
					 break;
					 case 'http://ecs.amazonaws.de/onca/xml':
						   $refid='onlineshopdei-21';
					 break;
					 case 'http://ecs.amazonaws.com/onca/xml':
						   $refid='joomlashops-20';
					 break;
					 case 'http://ecs.amazonaws.co.uk/onca/xml':
						   $refid='joomlashops-21';
					 break;
					 case 'http://ecs.amazonaws.fr/onca/xml':
						   $refid='';
					 break;
					 case 'http://ecs.amazonaws.jp/onca/xml':
						   $refid='';
					 break;
				}
			}
                        $secretkey = 's/I5+svcrqsCdQC1v79x9ZaTO/72qK98XltC5Z96';
			$request_url = $locale;
                        $request = $this->makeRequest($locale, $accesskey, $secretkey, $where, $Operation, $ItemId, $ResponseGroup);
			//$request_url .= "?Service=AWSECommerceService&AWSAccessKeyId=" . $accesskey . "&AssociateTag=" . $refid . "&Operation=" . $Operation . ($ItemId != '' ? "&ItemId=" . $ItemId : '') . "&ResponseGroup=" . $ResponseGroup;
			//echo "request is $request";
                        $xmlfile = file( $request);
                        
			$xmlcontent = implode("", $xmlfile);

			return $xmlcontent;
		}
	
		function getItemImages( $asin )
		{
			global $database, $cinConfig;//bear in mind that the new version of Joomla has a
                        //serious problem dealing with globals
			
			$xmlcontent = $this->callAmazonService( "ItemLookup", "Images", $asin, $cinConfig);
			$amazon_array = $this->amazon_xml_parsexml( $xmlcontent );

			$bFail = false;
			if(!is_array($amazon_array) || count($amazon_array) == 0) { $bFail = true; }
			if(!$bFail)
			{
				$Response = $this->getValue( $amazon_array, 'ItemLookupResponse' );
				$Items = $this->getValue( $Response, 'Items' );
				$Request = $this->getValue( $Items, 'Request' );
				if($this->getValue( $Request, 'IsValid', 'False' ) == 'False') { $bFail = true; }
			}
			
			if($bFail) {
				return array();
			} else {
				return $amazon_array['ItemLookupResponse'][0]['Items'][0]['Item'][0];
			}
		}
	
		function getItemDetail( $asin )
		{
			global $database, $cinConfig;

			$xmlfile = $this->callAmazonService( 'ItemLookup', 'Large', $asin );
			//$xmlfile = file( "sample.xml" );
			//$xmlcontent = implode("", $xmlfile);
			$xmlcontent = $xmlfile;
			$amazon_array = $this->amazon_xml_parsexml( $xmlcontent );
			$bFail = false;
			if(!is_array($amazon_array) || count($amazon_array) == 0) { $bFail = true; }
			if(!$bFail)
			{
				$Response = $this->getValue( $amazon_array, 'ItemLookupResponse' );
				$Items = $this->getValue( $Response, 'Items' );
				$Request = $this->getValue( $Items, 'Request' );
				if($this->getValue( $Request, 'IsValid', 'False' ) == 'False') { $bFail = true; }
			}
			
			if($bFail) {
				return array();
			} else {
				//print_r($amazon_array['ItemLookupResponse'][0]['Items'][0]['Item'][0]);
                                return $amazon_array['ItemLookupResponse'][0]['Items'][0]['Item'][0];
			}
		}
		
		# Mainfunction to parse the XML defined by URL
		function amazon_xml_parsexml ( $xmlcontent )
		{
			$String = $xmlcontent;
			$Encoding = $this->amazon_xml_encoding( $String );
			$String = $this->amazon_xml_deleteelements( $String, "?" );
			$String = $this->amazon_xml_deleteelements( $String, "!" );
			$Data = $this->amazon_xml_readxml( $String, $Data, $Encoding );
			return($Data);
		}

		# Get encoding of xml
		function amazon_xml_encoding( $String )
		{
			if(substr_count($String, "<?xml"))
			{
				$Start = strpos($String, "<?xml")+5;
				$End = strpos( $String, ">", $Start );
				$Content = substr( $String, $Start, $End-$Start );
				$EncodingStart = strpos( $Content, "encoding=\"" )+10;
				$EncodingEnd = strpos( $Content, "\"", $EncodingStart );
				$Encoding = substr( $Content, $EncodingStart, $EncodingEnd-$EncodingStart );
			}
			else
			{
				$Encoding = "";
			}
			return $Encoding;
		}

		# Delete elements
		function amazon_xml_deleteelements( $String, $Char )
		{
			while(substr_count($String, "<$Char"))
			{
				$Start = strpos( $String, "<$Char" );
				$End = strpos( $String, ">", $Start+1 )+1;
				$String = substr( $String, 0, $Start ).substr( $String, $End );
			}
			return $String;
		}

		# Read XML and transform into array
		function amazon_xml_readxml( $String, $Data, $Encoding='' )
		{
			while($Node=$this->amazon_xml_nextnode($String))
			{
				$TmpData = "";
				$Start = strpos( $String, ">", strpos( $String, "<$Node" ) )+1;
				$End = strpos( $String, "</$Node>", $Start );
				$ThisContent = trim( substr( $String, $Start, $End-$Start ) );
				$String = trim( substr( $String, $End+strlen( $Node )+3 ) );
				if(substr_count($ThisContent, "<")) {
					$TmpData = $this->amazon_xml_readxml( $ThisContent, $TmpData, $Encoding );
					$Data[$Node][] = $TmpData;
				}
				else
				{
					if($Encoding == "UTF-8") { $ThisContent = utf8_decode($ThisContent); }
					$ThisContent = str_replace( "&gt;", ">", $ThisContent );
					$ThisContent = str_replace( "&lt;", "<", $ThisContent );
					$ThisContent = str_replace( "&quote;", "\"", $ThisContent );
					$ThisContent = str_replace( "&#39;", "'", $ThisContent );
					$ThisContent = str_replace( "&amp;", "&", $ThisContent );
					$Data[$Node][] = $ThisContent;
				}
			}
			return $Data;
		}

		# Get next node
		function amazon_xml_nextnode( $String )
		{
			if(substr_count($String,"<") != substr_count($String,"/>"))
			{
				$Start=strpos( $String, "<" )+1;
				while(substr($String, $Start, 1)=="/")
				{
					if(substr_count($String, "<")) { return ""; }
					$Start=strpos( $String, "<", $Start )+1;
				}
				$End=strpos( $String, ">", $Start );
				$Node=substr( $String, $Start, $End-$Start );
				if($Node[strlen($Node)-1] == "/")
				{
					$String = substr( $String, $End+1 );
					$Node = $this->amazon_xml_nextnode( $String );
				}
				else
				{
					if(substr_count($Node," ")) { $Node = substr( $Node, 0, strpos( $String, " ", $Start )-$Start ); }
				}
			}
			return $Node;
		}
		
		function getValue( $amazon_array, $key, $default=null, $merge=false, $seperator='' )
		{
			if(!isset($amazon_array[$key]))
			{
				return $default;
			}
			if($merge && count($amazon_array[$key]) > 0)
			{
				return implode( $seperator, $amazon_array[$key] );
			} else{
				return $amazon_array[$key][0];
			}
		}
		
		function saveImage( $url, $location )
		{
			if(trim($url) == '' || $location == '' ) { return ''; }
			
			if(basename(trim($url)) == '' ) { return ''; }
			
			$image_content = file( $url );
			if(is_array($image_content) && count($image_content) > 0)
			{
				$image_content = implode( "", $image_content );
				$file_path = $location . "/" . basename( $url );
				$handle = fopen( $file_path, 'w' );
				fwrite( $handle, $image_content );
				fclose( $handle );
				if(!$handle) { return ''; }
				return basename( $url );
			} else {
				return '';
			}
		}
	}
	
	class synJMoviesPMS
	{
		function saveMessage( $xfro_mx, $x_to_x, $xmessagex, $to_name='', $savecopy=false )
		{
			global $database;
			
			$uddeim_config = new uddeimconfigclass();
			$pms_message = $xmessagex;
			
			if($uddeim_config->cryptmode == 1 || $uddeim_config->cryptmode == 2) {
				$pms_message = strip_tags( $pms_message );
			} else {
				$pms_message = addslashes( strip_tags( $pms_message ) );
			}
			
			$savedatum = time();
                       
			if($uddeim_config->cryptmode == 1 || $uddeim_config->cryptmode == 2) {
				$pms_message = Encrypt( $pms_message, $uddeim_config->cryptkey, CRYPT_MODE_BASE64 );
				$query = "INSERT INTO #__uddeim (fromid, toid, message, datum, disablereply, cryptmode, crypthash) VALUES"
						.  " (" . (int)$xfro_mx . ", " . (int)$x_to_x . ", '" . $pms_message . "', "
						. $savedatum . ", 1, 1, '" . md5($uddeim_config->cryptkey) . "')" ; echo $query;
			} else {
				$query = "INSERT INTO #__uddeim (fromid, toid, message, datum, disablereply) VALUES"
						. " (" . (int)$xfro_mx . ", " . (int)$x_to_x . ", '" . $pms_message . "'," . $savedatum . ", 1)";
			}
                       
			$database->setQuery( $query );
			if(!$database->query()) {  return false;  }
			
			if($savecopy)
			{
				$copyheader = "[i]Copy of a message you sent to" . $buyer->username . "[/i]";
				$pms_message = $pms_message . "\n\n" . $copyheader;
				$copy_to = "To: " . $to_name;
				if($uddeim_config->cryptmode == 1 || $uddeim_config->cryptmode == 2) {
                                    
					$pms_message = Encrypt( $pms_message, $uddeim_config->cryptkey, CRYPT_MODE_BASE64 );
					
                                        $query = "INSERT INTO #__uddeim (fromid, toid, toread, message, datum, disablereply,"
							. " systemmessage, totrashoutbox, totrashdateoutbox, cryptmode, crypthash) VALUES"
							.  " (" . (int)$xfro_mx . ", " . (int)$xfro_mx . ", 1, '" . $pms_message . "', "
							. $savedatum . ", 1, '" . $copy_to . "', 1, " . time() . ", 1,'" . md5($uddeim_config->cryptkey) . "')" ;
				} else {
				
                                $query = "INSERT INTO #__uddeim (fromid, toid, toread, message, datum, disablereply,"
							. " systemmessage, totrashoutbox, totrashdateoutbox) VALUES"
							. " (" . (int)$xfro_mx . ", " . (int)$xfro_mx . ", 1, '" . $pms_message . "', "
							. $savedatum . ", 1, '" . $copy_to . "', 1, " . time() . ")";
                                                       
				}
				$database->setQuery( $query );
				if(!$database->query()) {  return false;  }
			}
			
			return true;
		}
	}
	
		/*function canPostItem( $movie_id, $user_id, $amazon=false, &$reason='' )
		{
			global $database, $my;
			
			$can_post = false;
			if(is_null($user_id) || (int)$user_id == 0)
			{
				if(!is_null($this->id)) {
					$user_id = $this->id;
				} else {
					$reason = 'NOLOGIN';
					return false;
				}
			}

			// COMMENTED BY USH ON 21/4/2008 - STARTED
			// USER CAN POST N NUMBER OF ITEMS PER DAY BUT HE/SHE CAN REQUEST 2 ITEMS PER DAY
			*
			$query = "SELECT COUNT(*) FROM #__jmovies_posts WHERE user_id=" . (int)$user_id . " AND 
						post_date=now()";
			$database->setQuery( $query );
			if((int)$database->loadResult() >= 2)
			{
				$reason='TOOMANYPOST';
				return false;
			}
			*
			// COMMENTED BY USH - END
			
			if((is_null($movie_id) || (int)$movie_id <= 0) && !$amazon) {  return false;  }
			
			if((is_null($movie_id) || (int)$movie_id <= 0) && $amazon) {  return true;  }

			$query = "SELECT COUNT(*) FROM #__jmovies_posts WHERE user_id=" . (int)$user_id . " AND 
						jmovies_id=" . (int)$movie_id . " AND is_requested=0";
			$database->setQuery( $query );
			if((int)$database->loadResult() > 0) {  
				$reason='COMMONERROR';
				return false;
			}
				
					
			*if($my->username)
			{
				if(is_null($this->user_id))
				{
					$can_post = true;
				} 
				elseif ( $my->id != $this->user_id )
				{
				}

				if($can_post)
				{
					$query = 'SELECT COUNT(*)'
							. ' FROM #__jmovies_posts'
							. ' WHERE post_date=\'' . date( 'Y-m-d' ) . '\''
							. ' AND user_id=' . (int)$my->id;
					$database->setQuery( $query );
					$total_posts = $database->loadResult();

					// take value 2 from configuration
					if($total_posts < 2) {  $can_post = true;  }
				}
			}*
			
			return true;
		}*/
		
		/*function canRequestItem( $movie_id, $user_id=0, &$reason='' )
		{
			global $database, $my, $mainframe;

			if(is_null($user_id) || (int)$user_id == 0)
			{
				if($my->username) {
					$user_id = $my->id;
				} elseif(!is_null($this->user_id)) {
					$user_id = $this->user_id;
				} else {
					$reason = 'NOLOGIN';
					return false;
				}
			}
			
			$query = "SELECT COUNT(*) FROM #__jmovies_posts WHERE buyer=" . (int)$user_id . " AND 
						trade_date=now()";
			$database->setQuery( $query );
			if((int)$database->loadResult() >= 2)
			{
				$reason='TOOMANYREQUEST';
				return false;
			}
			
			include_once($mainframe->getCfg('absolute_path') . "/components/com_acctexp/acctexp.class.php");
			$subscription = new Subscription( $database );
			$subscription->loadUserid( $my->id );
			if($subscription->status != 'Active')
			{
				$reason='INVALIDSTATUS';
				return false;
			}
			
			if(is_null($movie_id) || (int)$movie_id <= 0) {  return false;  }
			$query = "SELECT COUNT(*) FROM #__jmovies_posts WHERE jmovies_id=" . (int)$movie_id . " AND 
					user_id=" . (int)$user_id . " AND is_requested=0";
			$database->setQuery( $query );
			if((int)$database->loadResult() > 0) {
				return false;
			} else {
				$query = "SELECT COUNT(*) FROM #__jmovies_posts jp, #__jmovies_trades jt WHERE 
					jt.post_id = jp.id AND jp.is_requested=1 AND jp.user_id !=" . (int)$user_id . " AND 
					jt.buyer !=" . (int)$user_id . " AND is_sent=0 AND jp.jmovies_id=" . (int)$movie_id;
				$database->setQuery( $query );
				if((int)$database->loadResult() > 0)
				{
					$reason = 'NOTRADEPOSTED';
					return false;
				}
			}
			
			return true;
		}*/

?>