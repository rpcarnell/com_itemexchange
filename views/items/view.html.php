<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');

class exchViewItems extends JViewLegacy
{
	function display($tpl = null)//ifd tpl = 2, then the template will be default_2, and so on
	{
            $style = new DRCStyles();
            $style->setPathway(DRCCATEGORY);
            $style->setPageTitle(DRCCATEGORY);
            jscssScripts::declareVariable('itemexurl', JUri::base());
            jscssScripts::declareVariable('traderID', JFactory::getUser()->id);
            jscssScripts::jsInclude('com_cronframe', 'thirdparty/javascripts/React/react.min.js');
            jscssScripts::jsInclude('com_cronframe', 'thirdparty/javascripts/React/react-dom.min.js');
            jscssScripts::jsInclude('com_cronframe', 'thirdparty/javascripts/React/browser.min.js');
           // jscssScripts::jsInclude('com_itemexchange', 'assets/js/basics.js');
            $keywords = array();
            //echo "<hr/>";
            for ($i = 0; $i < 20; $i++)
            {
                if (isset($this->items[$i]) && trim($this->items[$i]->item) != '') { $keywords[$i] = $this->items[$i]->item; }
                else continue;
            }
            $style->setMetaData(DRCCATEGORY, $keywords);
            $this->addTemplatePath($style->getTemplatePath());
             $this->setLayout('allitems');
            //echo $this->catgdata->templatefile;
            parent::display($tpl);
        }
}
?>