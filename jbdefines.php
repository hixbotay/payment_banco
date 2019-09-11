<?php

/**
 * @package 	Bookpro
 * @author 		Ngo Van Quan
 * @link 		http://joombooking.com
 * @copyright 	Copyright (C) 2011 - 2012 Ngo Van Quan
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: defines.php 104 2012-08-29 18:01:09Z quannv $
 **/
defined('_JEXEC') or die('Restricted access');
define('DS',DIRECTORY_SEPARATOR);
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!defined('JPATH_BASE'))
{
	define('JPATH_BASE', (dirname(dirname(dirname(__DIR__)))));
	require_once JPATH_BASE . '/includes/defines.php';
	
}
require_once JPATH_BASE . '/includes/framework.php';
require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/cms.php';

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}
define('OPTION', 'com_bookpro');
define('ADMIN_ROOT', JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . OPTION);
define('JPATH_COMPONENT', JPATH_ROOT.'/components');
define('SITE_ROOT', JPATH_ROOT . DS . 'components' . DS . OPTION);
//Display component name
define('COMPONENT_NAME', 'BookPro');

define('NAME', 'bookpro');

//default component encoding
define('ENCODING', 'UTF-8');

//Component table prefix
define('PREFIX', 'bookpro');
include (JPATH_ADMINISTRATOR.DS.'components/com_bookpro/helpers/importer.php');
//include (JPATH_ROOT.DS.'plugins/system/jbdebug/libs/basic.php');

AImporter::helper('bookpro','factory');



?>