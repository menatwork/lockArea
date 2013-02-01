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
$GLOBALS['TL_DCA']['tl_page']['list']['operations']['edit']['old_button_callback'] = $GLOBALS['TL_DCA']['tl_page']['list']['operations']['edit']['button_callback'];
$GLOBALS['TL_DCA']['tl_page']['list']['operations']['edit']['button_callback']     = array('LockArea', 'editPage');

/**
 * Check the edit btt
 */
$GLOBALS['TL_DCA']['tl_page']['list']['operations']['delete']['old_button_callback'] = $GLOBALS['TL_DCA']['tl_page']['list']['operations']['delete']['button_callback'];
$GLOBALS['TL_DCA']['tl_page']['list']['operations']['delete']['button_callback']     = array('LockArea', 'deletePage');

/**
 * Check the edit overview
 */
$GLOBALS['TL_DCA']['tl_page']['config']['onload_callback'][] = array('LockArea', 'checkPermission');

?>