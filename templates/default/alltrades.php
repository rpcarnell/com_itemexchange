<?php include_once('itemexchangemenu.php'); ?>
<ul>
	<li><a href='<?php echo JRoute::_('index.php?option=com_itemexchange&task=alltrades&trty=2&Itemid='.$this->menuid);?>'>Verified Trades</a></li>
<li><a href='<?php echo JRoute::_('index.php?option=com_itemexchange&task=alltrades&trty=1&Itemid='.$this->menuid);?>'>un-Verified Trades</a></li>
<li><a href='<?php echo JRoute::_('index.php?option=com_itemexchange&task=alltrades&trty=3&Itemid='.$this->menuid);?>'>Successful Trades</a></li>
<li><a href='<?php echo JRoute::_('index.php?option=com_itemexchange&task=alltrades&trty=0&Itemid='.$this->menuid);?>'>Cancelled Trades</a></li>
</ul>
<div>
<?php
if ($this->items)
{
	foreach ($this->items as $item)
	{
		echo "<br /><br />";
?>
		<div class="reqFromMeDiv">
		<div style="width: 49%; float: left;  padding: 5px;">	
<?php
		print_r($item);
		$trade_1 = $this->tradesModel->getTradeInfo($item->trade_id_1);
		
		$itemData_1 = $this->tradesModel->getItem($item->item_id_1);
		print_r($itemData_1);
?>
</div>
<div style="width: 49%; float: right; padding: 5px; background: #f0f0f0;">			
<?php
		$this->tradesModel->getTradeInfo($item->trade_id_2);
		$itemData_2 = $this->tradesModel->getItem($item->item_id_1);
		print_r($itemData_2);
?>
		</div><div style="clear:both;"></div></div>
<?php
	}
}
else
{
	
	switch ($this->tradeType)
	{
		case 3:
		    $tradeType = 'successful';
		    break; 
		case 2:
		    $tradeType = 'verified';
		    break;     
		case 1:
		    $tradeType = 'un-verified';
		    break; 
		case 0:
		    $tradeType = 'cancelled';
		    break;    
		default: '';
	}
	echo "<br /><h3>There are no $tradeType trades</h3><br />";
}
?>
</div>
