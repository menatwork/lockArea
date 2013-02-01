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
 * DCA
 */
$GLOBALS['TL_DCA']['tl_lockArea'] = array(
    // Config
    'config' => array(
        'dataContainer' => 'File',
        'closed'        => true,
        'notEditable'   => true,
    ),
    // Palettes
    'palettes'      => array(
        'default' => '{area_legend},lockArea_pageTree,lockArea_articleTree'
    ),
    // Fields
    'fields'  => array(
        'lockArea_pageTree' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_lockArea']['pageTree'],
            'inputType' => 'pageTree',
            'exclude'   => true,
            'eval'      => array(
                'multiple'      => true,
                'fieldType'     => 'checkbox',
                'tl_class'      => 'clr'
            ),
        ),
        'lockArea_articleTree' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_lockArea']['articleTree'],
            'exclude'   => true,
            'inputType' => 'articleTree',
            'eval'      => array(
                'multiple'      => true,
                'fieldType'     => 'checkbox',
                'tl_class'      => 'clr'
            )
        )
    )
);
?>