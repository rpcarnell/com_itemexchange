<?php
if ($this->otheruserid != $this->userid)
{
   echo "<h1>User ".$this->userData->username." Items</h1>";
}
else
{
    echo "<h1>My Items</h1>";
}
 
?>
<div>
    <div id='fuff'>
<?php
if ($this->items && is_array($this->items))
{
    $i = 0;
foreach ($this->items as $item)
{  
    ?>

<div style="background: #def; margin:6px;">
    <?php $img = unserialize($item->image);?>
   <img alt='<?php echo $item->item;?>' src='<?php echo JURI::base().DS."images/".$img['image'];?>' style="height: 200px; margin-right: 5px; float: left;" />
   <h2><?php echo $item->item;?></h2>
     <?php $query = "SELECT category FROM #__directcron_categories WHERE id = ".$item->category." LIMIT 1";
   //print_r($item);
     ?>
     <p><?php echo substr($item->description, 0, 200); echo (strlen($item->description) > 200) ? "..." : ''; ?></p>
    <?php $category = $this->cronDB->getOneValue($query);?>
    <p>Category: <?php echo $category; ?></p>
    
    
    
    <p>Quantity: <?php echo $item->numof; ?></p>
    
   <?php echo "<p>Last Update: ".date('m/d/Y', $item->date_posted)."</p>";?>
    <div style="clear: both;"></div>
    <?php
    if ($this->otheruserid != $this->userid)
    {
       include('subtemp/itemsother.php');  
    }
    else { include('subtemp/itemsown.php'); }
    ?>
    
</div>
    <?php
    $i++;
    echo "<br /><br />";
}
?>
</div></div>
<script>var itemexurl = '<?php echo JUri::base();?>';</script>
<script type="text/babel" src="<?php echo JUri::root()."components/com_itemexchange/assets/js/basics.js";?>"></script>
<script type="text/babel" src="<?php echo JUri::root()."components/com_itemexchange/assets/js/wantown.js";?>"></script>

<?php
} else echo "<p><b>There are no items yet</b></p>";
?>
