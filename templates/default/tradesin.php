<?php 
/* JMovies Component Trading System
// Developed By: Aditya Infotech
*/
defined( '_VALID_MOS' ) or die( 'Restricted access' ); 
global $Itemid;
$task = strtolower($_GET['task']);?>
<script language="javascript1.2" type="text/javascript">
function changeTradePage()
{
    var list = document.getElementById('posteditems');
    var listText = list.options[list.selectedIndex].value;
    //alert(listText);
    if (listText == 1) window.location = '<?php echo JRoute::_("index.php?option=com_jmovies&task=posteditems&Itemid=$Itemid");?>';
    if (listText == 2) window.location = '<?php echo JRoute::_("index.php?option=com_jmovies&task=tradesout&Itemid=$Itemid");?>';
     if (listText == 3) window.location = '<?php echo JRoute::_("index.php?option=com_jmovies&task=tradesin&Itemid=$Itemid");?>';

}
</script>
<table width="100%" border="0" cellspacing="5" cellpadding="3">
	<tr>
		<!-- Left Side -->
		<td width="100%" valign="top">
			<table width="100%" border="0" cellspacing="0" cellpadding="3">
				<tr>
					<td width="100%">
					<div id="divLista">
					<div id="div2Lista">
						<div id="divfly">
                                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
									<td class="sectiontableheader" colspan="2" ><?php echo ($trades_out ? _JMOVIES_TRADE_LIST_LEGEND_OUT : _JMOVIES_TRADE_LIST_LEGEND_IN);?></td>
								
                                                                
                                                                    <td>
                                                                            <form>
    <select id="posteditems" name="posteditems" onChange="changeTradePage()">
        <option value="1" <?php if ($task =='posteditems') echo "selected='selected'";?>>Posted Items</option>
        <option value="2" <?php if ($task =='tradesout') echo "selected='selected'";?>>Items You are Sending</option>
        <option value="3" <?php if ($task =='tradesin') echo "selected='selected'";?>>Items You are Receiving</option>
    </select></form>
   
                                                                        </td>
                                                                </tr>
                                                    </table>
							<table width="100%" cellpadding="0" cellspacing="0" border="0">
								

                                                                <tr><td colspan="3"><form method="post">

                                                                             <input type="hidden" name="task" value="posteditems" />
                                                          <?php echo $pageNav->getLimitBox(); echo " ".$pageNav->getPagesCounter(); ?></form>
							<?php echo $pageNav->getPagesLinks();?></td></tr>
							<?php if(count($row) > 0) { ?>
							<?php for($iCounter=0; $iCounter < count($row); $iCounter++) { ?>
								<tr class="<?php echo (($iCounter % 2 == 0) ? 'sectiontableentry2' : 'sectiontableentry1');?>">
									<?php if ($trades_in && (int)$row[$iCounter]['is_varified'] == 1) { ?>
									<td align="center" valign="middle">
										<a href="<?php echo $row[$iCounter]['delete_link']; ?>" title="<?php echo _JMOVIES_TRADE_REMOVE_FROM_BUYER_TITLE;?>">
											<img src="<?php echo $delete_image;?>" alt="<?php echo _JMOVIES_TRADE_REMOVE_FROM_BUYER_TITLE;?>" width="24" height="24" border="0" />
										</a>
									</td>
									<?php } else {?>
									<td align="center" valign="middle"></td>
									<?php } ?>
									<td align="center" valign="middle">
										<a href="<?php echo $row[$iCounter]['movie_link'];?>">
											<img src="<?php echo $row[$iCounter]['image']?>" alt="<?php echo $row['titolo']?>" width="35" height="50" style="border-left : 1px solid #fef; border-top : 1px solid #fef; border-right: 1px solid #eee; border-bottom: 1px solid #ddd; border-right-style: outset; border-bottom-style: outset">
										</a>
									</td>
									<td valign="top" width="100%">
										<a href="<?php echo $row[$iCounter]['movie_link'];?>">
											<strong><?php echo $row[$iCounter]['titolo'];?></strong>
										</a><br /><strong><?php echo ($trades_out ? _JMOVIES_TRADE_OUT_BUYER : _JMOVIES_TRADE_IN_SELLER);?>: </strong><?php echo ($trades_out ? $row[$iCounter]['buyer_name'] : $row[$iCounter]['seller_name']);?><br /><strong><?php echo _JMOVIES_TRADE_OUT_TRADE_DATE;?>: </strong><?php echo mosFormatDate($row[$iCounter]['trade_date'] . " 00:00:00", "%m/%d/%Y");?><br /><strong><?php echo _JMOVIES_TRADE_VARIFIED_LABEL;?>: </strong><span id="trade_verification_<?php echo $iCounter;?>"><?php echo ($row[$iCounter]['is_varified'] == 0 ? _JMOVIES_TRADE_NOT_VERIFIED . ($trades_out ? ' (<a href="' . $row[$iCounter]['trade_info_link'] . '">' . _JMOVIES_TRADE_OUT_VERIFY_LINK . '<a>) ' : ((int)$row[$iCounter]['is_sent'] == 1 ? ' (<a href="javascript:void(null)" onclick="javascript:xajax_ajaxVerifyTrade(' . $row[$iCounter]['id'] . ', ' . $iCounter . ', ' . ($trades_out ? '1' : '0') . ')"><strong>' . _JMOVIES_TRADE_IN_VERIFY_LINK . '</strong></a> ) <span id="verify_msg_' . $iCounter . '"></span>' : ' <b>(Not Yet Sent)</b>' )) : _JMOVIES_TRADE_VERIFIED);?></span><?php echo ($trades_in ? '<br /><a href="'.$row[$iCounter]['trade_info_link'].'">'._JMOVIES_TRADE_SHOW_TRADE_LINK.'</a>' : ($row[$iCounter]['is_varified'] == 1 ? '<br /><a href="'.$row[$iCounter]['trade_info_link'].'">'._JMOVIES_TRADE_SHOW_TRADE_LINK.'</a>' : ''));?>
									</td>
								</tr>
							<?php } 
								 } else { ?>
								<tr>
									<td valign="top">&nbsp; </td>
								</tr>
								<tr>
									<td valign="top"><?php echo ($trades_out ? _JMOVIES_TRADE_OUT_EMPTY : _JMOVIES_TRADE_IN_EMPTY);?></td>
								</tr>
                                                                 <tr>
                                                            <td>
                                                            <a href="<?php echo $row[$iCounter]['movie_link'];?>">
											<strong><?php echo $row[$iCounter]['titolo'];?></strong>
										</a><br />
                                                                                <strong><?php echo ($trades_out ? _JMOVIES_TRADE_OUT_BUYER : _JMOVIES_TRADE_IN_SELLER);?>: </strong><?php echo ($trades_out ? $row[$iCounter]['buyer_name'] : $row[$iCounter]['seller_name']);?><br />

                                                                                <strong><?php echo _JMOVIES_TRADE_OUT_TRADE_DATE;?>: </strong><?php echo mosFormatDate($row[$iCounter]['trade_date'] . " 00:00:00", "%m/%d/%Y");?><br />
                                                                                <strong><?php echo _JMOVIES_TRADE_VARIFIED_LABEL;?>: </strong><span id="trade_verification_<?php echo $iCounter;?>"><?php echo ($row[$iCounter]['is_varified'] == 0 ? _JMOVIES_TRADE_NOT_VERIFIED . ($trades_out ? ' (<a href="' . $row[$iCounter]['trade_info_link'] . '">' . _JMOVIES_TRADE_OUT_VERIFY_LINK . '<a>) ' : ((int)$row[$iCounter]['is_sent'] == 1 ? ' (<a href="javascript:void(null)" onclick="javascript:xajax_ajaxVerifyTrade(' . $row[$iCounter]['id'] . ', ' . $iCounter . ', ' . ($trades_out ? '1' : '0') . ')"><strong>' . _JMOVIES_TRADE_IN_VERIFY_LINK . '</strong></a> ) <span id="verify_msg_' . $iCounter . '"></span>' : ' <b>(Not Yet Sent)</b>' )) : _JMOVIES_TRADE_VERIFIED);?></span>

                                                                                        <?php echo ($trades_in ? '<br /><a href="'.$row[$iCounter]['trade_info_link'].'">'._JMOVIES_TRADE_SHOW_TRADE_LINK.'</a>' : ($row[$iCounter]['is_varified'] == 1 ? '<br /><a href="'.$row[$iCounter]['trade_info_link'].'">'._JMOVIES_TRADE_SHOW_TRADE_LINK.'</a>' : ''));?>

                                                            </td>
                                                        </tr>
							<?php } ?>
							</table>
						</div>
					</div>
					</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>