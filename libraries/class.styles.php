<?php
/******************************************************************************************
DirectCron - Advanced Directory and Partner Links Management Extension for Joomla!
* Copyright 20012-2014 Redacron-Extensions
* 
* This file is part of DirectCron
* 
* Directcron is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* 
* Directcron is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
* 
* @author Redacron.com
* @link http://wwww.redacron.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL  
******************************************************************************************/
defined('_JEXEC') or die('Restricted access');

define('_JVSE_BASE_URL', 'ff');
#************************************************************************************************************
class DRCStyles
{
    var $user;
    var $style_name;
    var $style_live_path;
    var $style_abs_path;
    var $cfg;
    var $dpage_id;
    var $catid;
    var $bs_titles = array();
    var $bs_links = array();
    var $stylevars = array();
#************************************************************************************************************        
    function __construct($displayed_page_id=0, $catid=0)
    {
        $this->user = JFactory::getUser();
        $this->dpage_id = $displayed_page_id;
        $this->catid = $catid;
        $this->cfg = DRCronSettings::getInstance();
        $this->style_name = $this->cfg->get('style_dir'); 
        $this->style_live_path = _DRCRON_STYLES.$this->style_name."/style.css";
        $this->style_abs_path = DRC_TMPL_BASEPATH; //USED      
       /*
        $this->stylevars['menu_top'] = $this->stylevars['menu_bottom'] = 0;
        switch ($this->cfg->get('menu_position')) {
            case 1: $this->stylevars['menu_top'] = 1; break;
            case 2: $this->stylevars['menu_bottom'] = 1; break;
            case 0: $this->stylevars['menu_top'] = $this->stylevars['menu_bottom'] = 1; break;
            default:
            case 3: break;
        }        
        
        $this->stylevars['menu'] = ($this->stylevars['menu_top'] || $this->stylevars['menu_bottom']) ? $this->setMenuInfo() : "";
        $this->stylevars['credits'] = ($this->cfg->get('powered_by')) ? $this->setCreditsInfo() : '';
        $this->stylevars['pathway'] = '';
        $this->stylevars['ad_top'] = $this->setAdInfo(_NORTH);
        $this->stylevars['ad_bottom'] = $this->setAdInfo(_SOUTH);         
        $this->stylevars['latest_links'] = $this->setLatestlinksInfo();
        $this->stylevars['featured_links'] = $this->setFeaturedLinksInfo();
        $this->stylevars['directory_stats'] = $this->setDirectoryStatsInfo();
        $this->stylevars['catfooterlinks'] = $this->setCategoryFooterLinksInfo();
        $this->stylevars['alphabar'] = $this->setAlphaBarInfo();
        $this->stylevars['search_form'] = (($this->dpage_id == _PAGE_DIRHOME) && ($this->cfg->get('search_show'))) ? 1 : 0;    */        
    }
#************************************************************************************************************
    function getName() {
        return $this->style_name;
    }
#************************************************************************************************************    
    function getStyleFile() {
        return $this->style_live_path;    
    }   
#************************************************************************************************************         
    function getTemplatePath() {
        $settings = DRCronSettings::getInstance();
        $style = $settings->get('style_dir');
        return $this->style_abs_path.DS.$style;    
    }   
#************************************************************************************************************
    function setPageTitle($pagetitle) {
        $this->stylevars['pagetitle'] = ($pagetitle == $this->cfg->get("dirhome_title")) ? $pagetitle : (($this->cfg->get('dirhome_prepend_title')) ? ($this->cfg->get("dirhome_title") . " - " . $pagetitle) : $pagetitle);        
    }
#************************************************************************************************************
    function setMetaData($pagetitle, $metakeys, $metadesc='') {
        $document =  JFactory::getDocument(); 
        $document->setTitle($pagetitle);
        $document->setMetaData('keywords', implode(",",$metakeys));
        $document->setMetaData('description', (($metadesc == '') ? $pagetitle : $metadesc));
            
    }
#************************************************************************************************************
    function setMenuInfo() {
        $linkstatus_lnk = JRoute::_(_JVSE_BASE_URL.'&controller=pages&task=checklinkstatus&view=checklinkstatus');
        $addlink_lnk = JRoute::_(_JVSE_BASE_URL.'&task=addlink&view=addlink');
        $recweb_lnk = JRoute::_(_JVSE_BASE_URL.'&controller=pages&task=recommend&view=recommend');
        $suggestcat_lnk = JRoute::_(_JVSE_BASE_URL.'&controller=pages&task=suggestcat&view=suggestcat');
        $felinks_lnk = JRoute::_(_JVSE_BASE_URL.'&controller=pages&task=featuredlinks&view=featuredlinks');
        $mylinks_lnk = JRoute::_(_JVSE_BASE_URL.'&controller=manage&view=manage');
        $home_lnk = JRoute::_(_JVSE_BASE_URL.'&view=jvse');
        
        $buffer = '';
        
        if (($this->cfg->get('mylinks_en')) && ($this->cfg->get('mylinks_menu_show')))
            $buffer .= '<a rel="nofollow" title="'.JText::_("A83").'" href="'.$mylinks_lnk.'">'.JText::_("A83").'</a> | ';
        
        if ($this->cfg->get('linkstatus_en'))
            $buffer .= '<a title="'.JText::_("A84").'" href="'.$linkstatus_lnk.'">'.JText::_("A84").'</a> | ';
        
        if (($this->cfg->get('oway_en')) || ($this->cfg->get('tway_en')))
            $buffer .= '<a title="'.JText::_("A85").'" href="'.$addlink_lnk.'">'.JText::_("A85").'</a> | ';

        if ($this->cfg->get('recawebsite_en'))
            $buffer .= '<a title="'.JText::_("A86").'" href="'.$recweb_lnk.'">'.JText::_("A86").'</a> | ';
         
        if ($this->cfg->get('suggestcat_en'))
            $buffer .= '<a title="'.JText::_("A360").'" href="'.$suggestcat_lnk.'">'.JText::_("A360").'</a> | ';
         
        if ($this->cfg->get('felinks_en'))
            $buffer .= '<a title="'.JText::_("A87").'" href="'.$felinks_lnk.'">'.JText::_("A87").'</a> | ';

        $usertype = ($this->user->get('id')) ? $this->user->get('usertype') : 'Guest';
        if (($usertype == 'Super Administrator') || 
            ($usertype == 'Administrator') || 
            (($usertype == 'Publisher') && ($this->cfg->get('action_publisher'))) || 
            (($usertype == 'Editor') && ($this->cfg->get('action_editor'))))            
        {
            $actnow_url = JRoute::_(_JVSE_BASE_URL."&controller=manage&view=manage&task=pendinglinks");
            $buffer .= '<a rel="nofollow" href="'.$actnow_url.'" title="'.JText::_("A363").'">'.JText::_("A373").'</a> | ';
        }    
         
        $buffer .= '<a title="'.htmlspecialchars(stripslashes($this->cfg->get("dirhome_title"))).'" href="'.$home_lnk.'">'.htmlspecialchars(stripslashes($this->cfg->get("dirhome_title"))).'</a>';
        return $buffer;
    }
#************************************************************************************************************
    function setCreditsInfo() {
        $url = "http://www.jv-extensions.com";
        if ($this->cfg->get('affil_id') != '') 
            $url .= "/affiliates/uid/".$this->cfg->get('affil_id');
                
        return '<a href="'.$url.'" target="_blank" title="Get High Quality Joomla! Extensions at JV-Extensions">'.JText::_("A82").'</a>';
    }
#************************************************************************************************************
    function getPathwayInfo() {
        $buffer = '';        
        if ($this->cfg->get('pathway')) {           
            $home_link = JRoute::_(_JVSE_BASE_URL."&view=jvse");
            $buffer .= '<a href="'.$home_link.'">'.$this->cfg->get("dirhome_title").'</a>';
            for ($stops='',$i=0;$i<count($this->bs_titles);$i++) {
                if ($this->bs_links[$i] != '')
                    $stops .= '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;<a href="'.$this->bs_links[$i].'">'.stripslashes($this->bs_titles[$i]).'</a>';
                else
                    $stops .= '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;'.stripslashes($this->bs_titles[$i]);
            }
            $buffer .= $stops;
        }        
        return $buffer;                
    } 
#************************************************************************************************************
    function setPathway($title, $link = '') {
         
            $app = JFactory::getApplication();
            $pathway = $app->getPathway();
            if ($link != '')
                $pathway->addItem($title, $link);
            else
                $pathway->addItem($title);            
        
    }
#************************************************************************************************************    
    function setAdInfo($where) {
        $ad_pages = array();        
        if ($where == _NORTH) {
            $ad = $this->cfg->get('ad_1');    
            $ad_pages = explode(',',$this->cfg->get('ad_1_pages'));            
        }
        else if ($where == _SOUTH) {
            $ad = $this->cfg->get('ad_2');    
            $ad_pages = explode(',',$this->cfg->get('ad_2_pages'));
        }
        else
            return '';
        
        if (($ad_pages[0] != 0) && (!in_array($this->dpage_id, $ad_pages)))
            return '';
        
        return $ad;
    }
#************************************************************************************************************
    function setLatestLinksInfo() { return;
        $buffer = '';
        try {
            $showall = 0;
            
            switch ($this->cfg->get('latlinks_display')) {
                case 2: {
                    throw new Exception("dont display latest links"); 
                    break;
                }
                case 0: {
                    $showall = ($this->cfg->get('latlinks_catspecific') == 1) ? 1 : 0;
                    break;
                }
                default:
                case 1: {
                    if ($this->dpage_id != _PAGE_DIRHOME) 
                        throw new Exception("display latest links in home page only, but it is not home page");
                    
                    $showall = 1;    
                    break;
                }
            }
            
            $sql = "select L.id LID, P.title PTITLE from `#__jvse_links` L, `#__jvse_linkprof` P ";
            $sql .= ($showall) ? " " : " , #__jvse_categories C ";            
            $sql .= " where L.id = P.lid and L.link_status != '"._RECYCLED."' ";                
            $sql .= ($showall) ? " " : " and L.link_category like CONCAT('%/', C.id, '/%') and C.path like '/".(int)$this->catid."/%' ";    
            $sql .= ($this->cfg->get('latlinks_linktype')) ? " and L.link_status  = '"._ESTABLISHED."' " : " ";
            $sql .= " group by L.id ";
            $sql .= ($this->cfg->get('latlinks_linktype')) ? " order by L.link_published_on desc " : " order by L.link_added_on desc ";
            $sql .= " limit 0, ".$this->cfg->get('latlinks_numlinks');

            $rows = JvseDb::getRows($sql);
            if (count($rows) == 0)
                throw new Exception("Zero results");
                
            $buffer .= '<ul>';
            foreach ($rows as $link) {
                $lurl = JRoute::_(_JVSE_BASE_URL."&task=detail&view=jvse&lid=".(int)$link->LID);
                $buffer .= '<li><a href="'.$lurl.'" title="'.htmlspecialchars($link->PTITLE).'">'.$link->PTITLE.'</a></li>';
            }
            $buffer .= '</ul>'; 
            
        } catch (Exception $ex) {
            // echo $ex->getMessage();
            return '';
        }
        
        return $buffer;        
    }
#************************************************************************************************************     
    function setDirectoryStatsInfo() { return;
        $buffer = '';
        try {
            switch ($this->cfg->get('dirstats_display')) {
                case 2: {
                    throw new Exception("dont display directory stats"); 
                    break;
                }
                case 0: {
                    break;
                }
                default:
                case 1: {
                    if ($this->dpage_id != _PAGE_DIRHOME) 
                        throw new Exception("display directory stats in home page only, but it is not home page");

                    break;
                }
            }
            
            $n1 = JvseDb::getCount("select count(id) as CNT from #__jvse_links where link_status = '"._ESTABLISHED."'");
            $n2 = JvseDb::getCount("select count(id) as CNT from #__jvse_links where link_status = '"._WEBMASTER_PENDING."'");         
            $n4 = JvseDb::getCount("select count(id) as CNT from #__jvse_links");
            $n5 = JvseDb::getCount("select count(id) as CNT from #__jvse_categories where cpid = '0'"); 
            $n6 = JvseDb::getCount("select count(id) as CNT from #__jvse_links where from_unixtime(link_published_on, '%Y-%m-%d') = curdate()"); // CS:74 
            $n7 = JvseDb::getCount("select count(id) as CNT from #__jvse_categories where cpid != '0'"); 
        
            $buffer .= '<ul>';
            $buffer .= '<li><strong>'.JText::_("A319").'</strong>: '.$n1.'</li>';
            $buffer .= '<li><strong>'.JText::_("A320").'</strong>: '.$n2.'</li>';
        
            if ($this->cfg->get('felinks_en')) {
                $n3 = JvseDb::getCount("select count(id) as CNT from #__jvse_links where link_status = '"._ESTABLISHED."' and featured = '1'");
                $buffer .= '<li><strong>'.JText::_("A321").'</strong>: '.$n3.'</li>';
            }
            
            $buffer .= '<li><strong>'.JText::_("A322").'</strong>: '.$n4.'</li>';
            $buffer .= '<li><strong>'.JText::_("A380").'</strong>: '.$n6.'</li>';
            $buffer .= '<li><strong>'.JText::_("A323").'</strong>: '.$n5.'</li>';
            $buffer .= '<li><strong>'.JText::_("A381").'</strong>: '.$n7.'</li>';
            $buffer .= '</ul>';  
            
        } catch (Exception $ex) {
            // echo $ex->getMessage();
            return '';
        }
        
        return $buffer;        
    }    
#************************************************************************************************************
    function setCategoryFooterLinksInfo() {
        $buffer = '';
        try {
            switch ($this->cfg->get('dirhome_footer_catlinks')) {
                case 0: {
                    throw new Exception("dont display category footer links"); 
                    break;
                }
                default:
                case 1: {
                    if ($this->dpage_id != _PAGE_DIRHOME) 
                        throw new Exception("display category footer links in home page only, but it is not home page");

                    break;
                }
            }
            
            $cats = JvseDb::getRows("select id, name from #__jvse_categories where visibility = '1'");
            if (!count($cats))
                throw new Exception("Zero results");
            
            $i=1;            
            foreach ($cats as $cat) {
                if ($i%_JVLD_FOOTERLINKS_NUMCATS_PER_ROW == 0)
                    $buffer .= '<br />';
                
                $url = JRoute::_(_JVSE_BASE_URL."&task=links&view=jvse&cid=".$cat->id);
                $buffer .= '<a href="'.$url.'" class="snap_noshots" title="'.htmlspecialchars($cat->name).'">'.$i.'</a>&nbsp;';
                $i++;                
            }
            
        } catch (Exception $ex) {
            // echo $ex->getMessage();
            return '';
        }
        
        return $buffer;        
    }    
#************************************************************************************************************    
    function setFeaturedLinksInfo() {
        $buffer = '';
        try {
            $showall = 0;
            
            if (!$this->cfg->get('felinks_en'))
                throw new Exception("Featured links is disabled");
            
            switch ($this->cfg->get('felinks_display')) {
                case 2: {
                    throw new Exception("dont display featured links"); 
                    break;
                }
                case 0: {
                    $showall = ($this->cfg->get('felinks_catspecific') == 1) ? 1 : 0;
                    break;
                }
                default:
                case 1: {
                    if ($this->dpage_id != _PAGE_DIRHOME) 
                        throw new Exception("display featured links in home page only, but it is not home page");
                    
                    $showall = 1;    
                    break;
                }
            }
            
            $sql = "select L.id from `#__jvse_links` L ";
            $sql .= ($showall) ? " " : " , #__jvse_categories C ";            
            $sql .= " where L.link_status != '"._RECYCLED."' and L.featured = '1' ";                
            $sql .= ($showall) ? " " : " and L.link_category like CONCAT('%/', C.id, '/%') and C.path like '/".(int)$this->catid."/%' ";    
            $sql .= ($this->cfg->get('felinks_linktype')) ? " and L.link_status  = '"._ESTABLISHED."' " : " ";
            $sql .= " group by L.id ";
            $sql .= ($this->cfg->get('felinks_recrand')) ? " order by RAND() " : " order by L.link_published_on desc ";
            $sql .= " limit 0, ".$this->cfg->get('felinks_numlinks');

            $rows = JvseDb::getRows($sql);
            if (count($rows) == 0)
                throw new Exception("Zero results");
                            
            if ($this->cfg->get('felinks_dispmode')) { // regular display
                $pp = 0;
                $buffer .= '<table align="center" border="0" cellpadding="4" cellspacing="2" width="100%">'; 
                foreach ($rows as $link) {
                    $mclass = "jvld_row".($pp%2);
                    $buffer .= '<tr class="'.$mclass.'"><td valign="top">';
                    
                    $linkobj = new JvseLinkinfo($link->id, _JVLD_STW_SFX_FLBLOCK);                    
                    $buffer .= $linkobj->getThumbnailPreview();
                    $buffer .= $linkobj->showHref();
                    
                    $buffer .= '</td></tr>';
                    $pp++;
                }            
                $buffer .= '</table>';
            } else {
                $document =& JFactory::getDocument(); 
                $document->addScript('components/com_jvse/assets/js/felinks_slider.js');        
                $buffer .=      '<div id="marqueecontainer" onmouseover="copyspeed=pausespeed" onmouseout="copyspeed=marqueespeed">
                                    <div id="vmarquee" style="position: absolute; width: 98%;">';
                foreach ($rows as $link) {
                    $buffer .= '<div style="width:auto;clear:both;">';
                    
                    $linkobj = new JvseLinkinfo($link->id, _JVLD_STW_SFX_FLBLOCK);                    
                    $buffer .= $linkobj->getThumbnailPreview();
                    $buffer .= $linkobj->showHref();
                    $buffer .= '</div>';
                    $buffer .= '<p class="jv_gap">&nbsp;</p>';
                }
                $buffer .=      '   </div>
                                 </div>';                
            }
            
            $furl = JRoute::_(_JVSE_BASE_URL."&controller=pages&task=addfeatured&view=addfeatured");
            $buffer .= '<p class="jv_rline"><a href="'.$furl.'" title="'.JText::_("A90").'">'.JText::_("A90").'</a></p>';
            
        } catch (Exception $ex) {
            // echo $ex->getMessage();
            return '';
        }
        
        return $buffer;        
    }
#************************************************************************************************************     
    function setAlphaBarInfo() {
        $buffer = '';
        try {
            switch ($this->cfg->get('alphabar_show')) {
                case 0: {
                    throw new Exception("dont display alpha bar"); 
                    break;
                }
                default:
                case 1: {
                    if ($this->dpage_id != _PAGE_DIRHOME) 
                        throw new Exception("display alpha bar in home page only, but it is not home page");

                    break;
                }
            }
            
            $al_var = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","...");
            $al_val = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","others");
        
            for ($listing='',$i=0;$i<count($al_var);$i++) {
                $alink = JRoute::_(_JVSE_BASE_URL."&controller=pages&task=category&view=alphabar&alpha=".$al_val[$i]);                
                $buffer .= '&nbsp;<a rel="nofollow" title="'.JText::_('A92').'" href="'.$alink.'">'.$al_var[$i].'</a>&nbsp;';
            }                              
        } catch (Exception $ex) {
            // echo $ex->getMessage();
            return '';
        }
        
        return $buffer;        
    }    

}

