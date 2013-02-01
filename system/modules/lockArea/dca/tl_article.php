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
$GLOBALS['TL_DCA']['tl_article']['list']['operations']['edit']['old_button_callback'] = $GLOBALS['TL_DCA']['tl_article']['list']['operations']['edit']['button_callback'];
$GLOBALS['TL_DCA']['tl_article']['list']['operations']['edit']['button_callback']     = array('LockArea', 'editArticle');

/**
 * Check the cut btt
 */
$GLOBALS['TL_DCA']['tl_article']['list']['operations']['cut']['old_button_callback'] = $GLOBALS['TL_DCA']['tl_article']['list']['operations']['cut']['button_callback'];
$GLOBALS['TL_DCA']['tl_article']['list']['operations']['cut']['button_callback']     = array('LockArea', 'cutArticle');

/**
 * Check the delete btt
 */
$GLOBALS['TL_DCA']['tl_article']['list']['operations']['delete']['old_button_callback'] = $GLOBALS['TL_DCA']['tl_article']['list']['operations']['delete']['button_callback'];
$GLOBALS['TL_DCA']['tl_article']['list']['operations']['delete']['button_callback']     = array('LockArea', 'cutArticle');

/**
 * Check the edit overview
 */
$GLOBALS['TL_DCA']['tl_article']['config']['onload_callback'][] = array('LockArea', 'checkPermission');

?>