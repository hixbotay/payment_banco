<?php

/**
 * Popup element to select destination.
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: destination.php 44 2012-07-12 08:05:38Z quannv $
 **/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.parameter.element');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldJbNote extends JFormFieldList
{
	
	protected function getInput() {
		
		$host = JUri::root();
		$plugin = 'payment_banco';		
		$notify_url = JString::ltrim($host.'plugins/bookpro/'.$plugin.'/notify.php');
		return ''.$notify_url;
	
	}

	

}

?>