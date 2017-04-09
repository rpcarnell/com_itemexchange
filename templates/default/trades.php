<ul>
<li>All Trades</li>	
<li>Trades In</li>
<li>Trades Out</li>

<li></li>
</ul>
<?php
$cronDb = new CronDb();
$urlFormat = new urlFormat();
$itemItemId = $urlFormat->getItemid('com_directcron', 'items');
foreach ($this->trades as $trad)
{   
	 $itemData = $this->tradesModel->getItem($trad->item_id);
	 $itemImgArray = unserialize($itemData->image);
	 
     ?>
     <div style='border: 1px solid #999; border-radius: 5px; width: 60%; margin-bottom: 10px;'>
		  <div style="padding: 10px; background: #ddd;">
	  
	   <div style="float: left; margin-right: 10px;"><img style="height: 150px;" src='<?php echo JUri::base()."images/".$itemImgArray['image'] ;?>'/></div>
	  <p><b>Item: </b><a href='<?php echo JRoute::_('index.php?option=com_directcron&view=items&task=oneitem&id='.$trad->item_id."&Itemid=".$itemItemId);?>'><?php echo $itemData->item;?></a>
      <br /><b>Verified: </b><?php echo ($trad->is_verified) ? "Yes" : "No"; ?>
      <br /><b>Sent: </b><?php echo ($trad->is_sent) ? "Yes" : "No"; ?>
      
      
      </p>
      <div style="clear: both;"></div> </div>
     <?php
     $avatars = $this->basicExchange->getUserAvatar($trad->buyer, $cronDb);
     $buyerinfo = JFactory::getUser($trad->buyer);
     
     ?>
     <div style='padding: 5px; width: 50%; float: left;'>
     <?php
      echo "<img src='".JUri::base().$avatars->thumb."' style='float: right; margin-right: 5px;'/>";
      echo "<h3>Dealer</h3>";
      echo "<p><b>Username: </b><a href='".JRoute::_('index.php?option=com_itemexchange&task=users&userid='.$trad->buyer)."'>".$buyerinfo->username."</a><br />";
      echo "<b>E-Mail: </b>".$buyerinfo->email."</p>";
      $avatars = $this->basicExchange->getUserAvatar($trad->seller, $cronDb);
      ?></div>
      <div style='border-left: 1px solid #777; padding: 5px; width: 50%; float: right;'>
      <?php
      $buyerinfo = JFactory::getUser($trad->seller);
      echo "<img src='".JUri::base().$avatars->thumb."' style='float: right; margin-right: 5px;' />";
      echo "<h3>Seller</h3>";
      echo "<p><b>Username: </b><a href='".JRoute::_('index.php?option=com_itemexchange&task=users&userid='.$trad->seller)."'>".$buyerinfo->username."</a><br />";
      echo "<b>E-Mail: </b>".$buyerinfo->email."</p>";
     ?>
   </div>
   <div style="clear: both;"></div>
  
  
   </div>
     <?php
}
echo $this->pagination;
?>
