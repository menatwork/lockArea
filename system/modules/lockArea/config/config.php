<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    lockArea
 * @license    GNU/LGPL 
 * @filesource
 */

// Add new widget
$GLOBALS['BE_FFL']['articleTree'] = 'ArticleTree';

$GLOBALS['BE_MOD']['system']['tl_lockArea'] = array(
    'tables' => array('tl_lockArea'),
    'icon' => 'system/modules/lockArea/html/iconArea.png'
);

// Include JS/Hooks only for backend
if (TL_MODE == 'BE')
{
    // Javascript
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/lockArea/html/js/articletree.js';

    // Hooks
    $GLOBALS['TL_HOOKS']['executePostActions'][] = array('ArticleTreeAjax', 'executePostActions');
    $GLOBALS['TL_HOOKS']['executePreActions'][] = array('ArticleTreeAjax', 'executePreActions');
}
?>