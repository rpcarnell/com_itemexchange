<?php include_once('itemexchangemenu.php'); ?>

<?php if ($this->otheruserid) { ?>
<p><a href='#'>Items I am Trading with <?php echo ucwords($this->userData->username);?></a><br />
<a href='#'>Items I have Traded with <?php echo ucwords($this->userData->username);?></a></p>


<div id="itemsReqBy"><h2>Items Requested By <?php echo ucwords($this->userData->username);?>:</h2></div>
<?php } ?>


<br />
<?php
$cronDb = new CronDb();
$urlFormat = new urlFormat();
$itemItemId = $urlFormat->getItemid('com_directcron', 'items');
$az = 0;

if (is_array($this->items)) {
foreach ($this->items as $trad)
{   
	 $itemData = $this->tradesModel->getItem($trad->item_id);
	 $itemImgArray = unserialize($itemData->image);
	 $s = JFactory::getUser($trad->buyer);
?>
<div class="reqFromMeDiv" id="reqfromMe_<?php echo $az; ?>" data-trad='<?php echo json_encode($trad);?>'>
<div class="reqFromMeDi2">
<?php
       $buyerinfo = JFactory::getUser($trad->seller);
?>
      
<div style="float: left; margin-right: 10px;">
<img style="height: 150px;" src='<?php echo JUri::base()."images/".$itemImgArray['image'] ;?>'/>
</div>
<p><b>Item: </b><a href='<?php echo JRoute::_('index.php?option=com_directcron&view=items&task=oneitem&id='.$trad->item_id."&Itemid=".$itemItemId);?>'><?php echo $itemData->item;?></a>
<br /><b>Verified: </b><?php echo ($trad->is_verified) ? "Yes" : "No"; ?>
<br /><b>Sent: </b><?php echo ($trad->is_sent) ? "Yes" : "No"; ?>
<?php echo "<br /><b>Seller:</b> <a href='".JRoute::_('index.php?option=com_itemexchange&task=users&userid='.$trad->seller."&Itemid=".$this->menuid)."'>".$buyerinfo->username."</a><br />"; ?>
</p>
<div style="clear: both;"></div> </div>
<div class="reqFromBuyer">
<?php
      $avatars = $this->basicExchange->getUserAvatar($s->id, $cronDb);
       echo "<h3>Buyer</h3>";
       echo "<img src='".JUri::base().$avatars->thumb."' style='width: 70px; float: left; margin-right: 5px;' />";
      echo "<p><b>Username: </b><a href='".JRoute::_('index.php?option=com_itemexchange&task=users&userid='.$s->id.'&Itemid='.$this->menuid)."'>".$s->username."</a><br />";
      if (! $this->otheruserid)
      {
		  ?>
		  <p><a href='<?php echo JRoute::_('index.php?option=com_itemexchange&task=requestsFrom&userid='.$s->id.'&Itemid='.$this->menuid); ?>'>Items requested by <?php echo $s->username;?></a></p>
		  <?php
	  }
      echo "<b>E-Mail: </b>".$s->email."</p>";
      echo '<div style="clear: both;"></div><br />';
      
?>
</div>
<div style="clear: both;"></div><hr style='color: #444; background: #444;' />
<div class='reqfromMe' id='wantfromMe_<?php echo $az;?>'>What do you want to trade for <span style='font-weight: bold;'><?php echo  $itemData->item; ?></span> with <span style='font-weight: bold;'><?php echo $s->username; //echo  $itemData->item; ?></span>?</div>
<div class='requebyhim' style='display: none;' id='requeBySlid_<?php echo $az;?>'></div>
</div>
<?php
     $az++;
} }
	echo "<br /><br /><br />";
	echo $this->pagination;
?>
<script>
<!--
function startTrade(item_id, item_id2, initial_trade, buyer, trade_2, reqLayer)
{
	 cronframe.jQuery.post( itemexurl+ "index.php?option=com_itemexchange&task=tradenow", {item_id: item_id, item_id2: item_id2, initial_trade: initial_trade, buyer: buyer, trade_2: trade_2},
     function(data)  {
		   data = JSON.parse(data);
		   var css = '';
		   if (data.error == 1) css = "#a00"; else css = "#0a0";
		   if ("NOMSG" == data.msg) data.msg = "You are now trading with <span style='font-weight: bold;'><?php echo $s->username; ?></span>";
		   data.msg = "<div style='padding-left: 10px; color: "+css+"'>"+ data.msg + "</div>";
		   if (data.error == 1) { jQuery('#requeBySlid_' + reqLayer).append(data.msg); }
		   else { jQuery('#requeBySlid_' + reqLayer).html(data.msg); }
	 });
}	
jQuery('.reqfromMe').click(function() 
{     
	 var reqLayer = jQuery(this).parent().attr('id');
	 var trad = jQuery('#'+reqLayer).data('trad');
	 reqOriginal = trad;
	 reqLayer = reqLayer.replace('reqfromMe_', '');
	 jQuery('.requebyhim').slideUp();
	 jQuery('#requeBySlid_' + reqLayer).html('').slideDown(); 
	 cronframe.jQuery.post( itemexurl+ "index.php?option=com_itemexchange&task=reqfromme", {props: trad},
     function(data)  {
		 data = JSON.parse(data);
		 if (data.data == '')
		 {
			  data.error = 1;
			  data.msg = "There are no items to trade with this user";
		 }
		 if (data.error == 1) jQuery('#wantfromMe_' +reqLayer).html(data.msg).css({'padding' : '0px', 'color' : '#a00'});
		 var x = '';
		 for (i = 0; i < data.data.length; i++)
		 {
			 if (typeof(data.data[i]) == 'undefined') continue; 
			 x += '<li style="float: left; margin-right: 10px; width: 250px; min-height: 160px;"><img src="' + itemexurl + "images/" + data.data[i].image + '" class="imgReqFrom" /><p>' + data.data[i].item + "</p><p><a href='javascript:void(0)' class='tradeatlast' data-item2trad='"+data.data[i].item_id+"' data-id2trad='"+data.data[i].id+"'>Request "+ data.data[i].item +" from <?php echo $s->username;?></a></p></li>";
		 }
		 jQuery('#requeBySlid_' + reqLayer).append('<ul class="submisList">'+ x + '</ul><div style="clear: both;"></div><br />');
		 jQuery('.tradeatlast').click(function()
		 {  
			  startTrade(trad.item_id, jQuery(this).data('item2trad'), reqOriginal.id, trad.buyer, jQuery(this).data('id2trad'), reqLayer);
		 });
    });
});
-->
</script>
