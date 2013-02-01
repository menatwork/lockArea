<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    lockArea
 * @license    GNU/LGPL 
 * @filesource
 */

/**
 * Check the edit btt
 */
$GLOBALS['TL_DCA']['tl_content']['list']['operations']['edit']['old_button_callback'] = $GLOBALS['TL_DCA']['tl_content']['list']['operations']['edit']['button_callback'];
$GLOBALS['TL_DCA']['tl_content']['list']['operations']['edit']['button_callback']     = array('LockArea', 'editContent');

/**
 * Check the edit overview
 */
$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = array('LockArea', 'checkPermission');

?>