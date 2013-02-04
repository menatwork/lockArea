<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    lockArea
 * @license    GNU/LGPL 
 * @filesource
 */

class ArticleTreeAjax extends System
{

    private static $objInstance;

    protected function __construct()
    {
        parent::__construct();
    }

    public static function getInstance()
    {
        if (!is_object(self::$objInstance))
        {
            self::$objInstance = new self();
        }

        return self::$objInstance;
    }

    private function __clone()
    {
        if (!is_object(self::$objInstance))
        {
            self::$objInstance = new self();
        }

        return self::$objInstance;
    }

    // Hooks -------------------------------------------------------------------

    public function executePostActions($strAction, DataContainer $dc)
    {
        if ($strAction == 'loadArticleTree')
        {
            // Do it like contao :)
            header('Content-Type: text/html; charset=' . $GLOBALS['TL_CONFIG']['characterSet']);

            $strAjaxId   = preg_replace('/.*_([0-9a-zA-Z]+)$/i', '$1', $this->Input->post('id'));
            $strAjaxName = preg_replace('/.*_([0-9a-zA-Z]+)$/i', '$1', $this->Input->post('name'));

            $arrData = array();
            $arrData['strTable'] = $dc->table;
            $arrData['id']       = strlen($strAjaxName) ? $strAjaxName : $dc->id;
            $arrData['name']     = $this->Input->post('name');

            // Call toggle
            $this->executePreActions('toggleArticleTree', true);
            
            $objWidget = new $GLOBALS['BE_FFL']['articleTree']($arrData, $dc);
            echo $objWidget->generateAjax($strAjaxId, $this->Input->post('field'), intval($this->Input->post('level')));
            
            exit();
        }
    }

    public function executePreActions($strAction, $blnState = null)
    {
        if ($strAction == 'toggleArticleTree')
        {
            $strAjaxId  = preg_replace('/.*_([0-9a-zA-Z]+)$/i', '$1', $this->Input->post('id'));
            $strAjaxKey = str_replace('_' . $strAjaxId, '', $this->Input->post('id'));

            if ($this->Input->get('act') == 'editAll')
            {
                $strAjaxKey  = preg_replace('/(.*)_[0-9a-zA-Z]+$/i', '$1', $strAjaxKey);
                $strAjaxName = preg_replace('/.*_([0-9a-zA-Z]+)$/i', '$1', $this->Input->post('name'));
            }

            // Check if we are called by user or system
            if ($blnState != null)
            {
                $nodes             = $this->Session->get($strAjaxKey);
                $nodes[$strAjaxId] = intval($blnState);
                $this->Session->set($strAjaxKey, $nodes);
                return;
            }
            else
            {
                $nodes             = $this->Session->get($strAjaxKey);
                $nodes[$strAjaxId] = intval($this->Input->post('state'));
                $this->Session->set($strAjaxKey, $nodes);
                exit;
            }
        }
    }

}