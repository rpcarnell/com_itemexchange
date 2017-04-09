<?php
$app = JFactory::getApplication();
$task = $app->input->getInt('task', '');
$color = "style='color: #000;'";
?>
<div id="itemexchangemenu">
<ul>
<li><a <?php echo ($task == '') ? $color : ''; ?> href='<?php echo JRoute::_('index.php?option=com_itemexchange&Itemid='.$this->menuid); ?>'>My Items</a></li>
<li><a <?php echo ($task == 'alltrades') ? $color : ''; ?> href='<?php echo JRoute::_('index.php?option=com_itemexchange&task=alltrades&Itemid='.$this->menuid); ?>'>All Trades</a></li>	

<li>Current Traders</li>
<li>Past Traders</li>
<li><a <?php echo ($task == 'requested') ? $color : ''; ?> href='<?php echo JRoute::_('index.php?option=com_itemexchange&task=requestsFrom&Itemid='.$this->menuid); ?>'>Requested</a></li>
</ul>
<div style='clear: both;'></div>
</div><br />
