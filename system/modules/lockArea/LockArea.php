<?php

/**
 * Contao Open Source CMS
 *
 * @copyright  MEN AT WORK 2013 
 * @package    lockArea
 * @license    GNU/LGPL 
 * @filesource
 */

class LockArea extends Backend
{

    /**
     * @var LockArea 
     */
    protected static $instance       = null;
    protected static $arrPageIdCache = array();
    protected static $arrArticleIdCache = array();

    /**
     * Construct
     */
    protected function __construct()
    {
        parent::__construct();

        $this->loadCache();
    }

    /**
     * 
     * @return LockArea
     */
    public static function getInstance()
    {
        if (!is_object(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Load the ids for blocked pages/articles
     * 
     * @return void
     */
    protected function loadCache()
    {
        // Get the lockstates page
        $mixLockStates = $GLOBALS['TL_CONFIG']['lockArea_pageTree'];
        $mixLockStates = deserialize($mixLockStates);

        if (is_array($mixLockStates) && !empty($mixLockStates))
        {
            self::$arrPageIdCache = $mixLockStates;
        }

        // Get the lockstates article
        $mixLockStates = $GLOBALS['TL_CONFIG']['lockArea_articleTree'];
        $mixLockStates = deserialize($mixLockStates);

        if (is_array($mixLockStates) && !empty($mixLockStates))
        {
            self::$arrArticleIdCache = $mixLockStates;
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // All
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Redirect with error
     * 
     * @param string $strRedirect
     */
    protected function lockAreaRedirect($strRedirect)
    {
        $this->addErrorMessage($GLOBALS['TL_DCA']['ERR']['no_access']);
        $this->redirect($strRedirect);

        exit();
    }

    /**
     * Check some options
     */
    public function checkPermission()
    {
        $strDo    = $this->Input->get('do');
        $strAct   = $this->Input->get('act');
        $strTable = $this->Input->get('table');
        $intID    = $this->Input->get('id');

        // Edit ----------------------------------------------------------------

        $arrActChecks = array('edit', 'delete', 'cut');

        // Check page edit
        if ($strDo == 'page' && in_array($strAct, $arrActChecks) && in_array($intID, self::$arrPageIdCache))
        {
            $this->lockAreaRedirect('contao/main.php?do=page');
        }

        // Check article edit
        if ($strDo == 'article' && in_array($strAct, $arrActChecks) && !empty($intID))
        {
            $objArticle = $this->Database
                    ->prepare("SELECT * FROM tl_article WHERE id=?")
                    ->execute($intID);

            // Check page
            if ($objArticle->numRows != 0 && in_array($objArticle->pid, self::$arrPageIdCache))
            {
                $this->lockAreaRedirect('contao/main.php?do=article');
            }

            // Check article
            if ($objArticle->numRows != 0 && in_array($objArticle->id, self::$arrArticleIdCache))
            {
                $this->lockAreaRedirect('contao/main.php?do=article');
            }
        }

        // Check content edit
        if ($strDo == 'article' && in_array($strAct, $arrActChecks) && $strTable == 'tl_content' && !empty($intID))
        {
            $objContent = $this->Database
                    ->prepare("SELECT pid FROM tl_content WHERE id=?")
                    ->execute($intID);

            if ($objContent->numRows == 0)
            {
                return;
            }

            $objArticle = $this->Database
                    ->prepare("SELECT pid FROM tl_article WHERE id=?")
                    ->execute($objContent->pid);

            if ($objArticle->numRows != 0 && in_array($objArticle->pid, self::$arrPageIdCache))
            {
                $this->addErrorMessage($GLOBALS['TL_DCA']['ERR']['no_access']);
                $this->redirect('contao/main.php?do=article');
            }
        }

        // Overview ------------------------------------------------------------
        // Check content overview
        if ($strDo == 'article' && $strAct == '' && $strTable == 'tl_content' && !empty($intID))
        {
            $objArticle = $this->Database
                    ->prepare("SELECT * FROM tl_article WHERE id=?")
                    ->execute($intID);

            // Check page
            if ($objArticle->numRows != 0 && in_array($objArticle->pid, self::$arrPageIdCache))
            {
                $this->lockAreaRedirect('contao/main.php?do=article');
            }

            // Check article
            if ($objArticle->numRows != 0 && in_array($objArticle->id, self::$arrArticleIdCache))
            {
                $this->lockAreaRedirect('contao/main.php?do=article');
            }
        }

        // All -----------------------------------------------------------------

        $arrCurrentIdChecks = array('editAll', 'overrideAll', 'deleteAll');
        $arrClipboardIdChecks = array('cutAll');

        // Page all operations
        if ($strDo == 'page' && in_array($strAct, $arrCurrentIdChecks) && $strTable == '')
        {
            $arrSession = $this->Session->getData();

            foreach ($arrSession['CURRENT']['IDS'] as $intKey => $intPageID)
            {
                if (in_array($intPageID, self::$arrPageIdCache))
                {
                    unset($arrSession['CURRENT']['IDS'][$intKey]);
                }
            }

            $this->Session->setData($arrSession);
        }

        // Article all operations
        if ($strDo == 'article' && in_array($strAct, $arrCurrentIdChecks) && $strTable == '')
        {
            $arrSession = $this->Session->getData();

            foreach ($arrSession['CURRENT']['IDS'] as $intKey => $intArticlID)
            {
                // Check page ids
                $objPage = $this->Database
                        ->prepare("SELECT pid FROM tl_article WHERE id=?")
                        ->execute($intArticlID);

                if ($objPage->numRows != 0 && in_array($objPage->pid, self::$arrPageIdCache))
                {
                    unset($arrSession['CURRENT']['IDS'][$intKey]);
                    continue;
                }

                // Check article ids
                if (in_array($intArticlID, self::$arrArticleIdCache))
                {
                    unset($arrSession['CURRENT']['IDS'][$intKey]);
                    continue;
                }
            }

            $this->Session->setData($arrSession);
        }

        // Article checks for clipboard functions
        if ($strDo == 'article' && in_array($strAct, $arrClipboardIdChecks) && $strTable == '')
        {
            $arrClipboard = $this->Session->get('CLIPBOARD');

            foreach ($arrClipboard['tl_article']['id'] as $intKey => $intArticlID)
            {
                // Check page ids
                $objPage = $this->Database
                        ->prepare("SELECT pid FROM tl_article WHERE id=?")
                        ->execute($intArticlID);

                if ($objPage->numRows != 0 && in_array($objPage->pid, self::$arrPageIdCache))
                {
                    unset($arrClipboard['tl_article']['id'][$intKey]);
                    continue;
                }

                // Check article ids
                if (in_array($intArticlID, self::$arrArticleIdCache))
                {
                    unset($arrClipboard['tl_article']['id'][$intKey]);
                    continue;
                }
            }

            $this->Session->set('CLIPBOARD', $arrClipboard);
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // Page Btt
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Return the edit page button
     * 
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     * 
     * @return string
     */
    public function editPage($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->functionPage($row, $href, $label, $title, $icon, $attributes, 'edit');
    }

    /**
     * Return the edit page button
     * 
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     * 
     * @return string
     */
    public function deletePage($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->functionPage($row, $href, $label, $title, $icon, $attributes, 'delete');
    }

    ////////////////////////////////////////////////////////////////////////////
    // Article Btt
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Return the edit page button
     * 
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     * 
     * @return string
     */
    public function editArticle($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->functionArticle($row, $href, $label, $title, $icon, $attributes, 'edit');
    }

    /**
     * Return the edit page button
     * 
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     * 
     * @return string
     */
    public function cutArticle($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->functionArticle($row, $href, $label, $title, $icon, $attributes, 'cut');
    }

    /**
     * Return the edit page button
     * 
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     * 
     * @return string
     */
    public function deleteArticle($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->functionArticle($row, $href, $label, $title, $icon, $attributes, 'delete');
    }

    ////////////////////////////////////////////////////////////////////////////
    // Article Btt: edit page
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Return the edit page button
     * 
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     * 
     * @return string
     */
    public function editContent($row, $href, $label, $title, $icon, $attributes)
    {
        return $this->functionContent($row, $href, $label, $title, $icon, $attributes, 'edit');
    }

    ////////////////////////////////////////////////////////////////////////////
    // Core functions
    ////////////////////////////////////////////////////////////////////////////

    /**
     * 
     * @param type $row
     * @param type $href
     * @param type $label
     * @param type $title
     * @param type $icon
     * @param type $attributes
     * @param type $strFunktion
     * @return string
     */
    protected function functionPage($row, $href, $label, $title, $icon, $attributes, $strFunktion)
    {
        if (empty(self::$arrPageIdCache))
        {
            return $this->callParentFunctionPage($row, $href, $label, $title, $icon, $attributes, $strFunktion);
        }

        if (in_array($row['id'], self::$arrPageIdCache))
        {
            return $this->generateImage(preg_replace('/\.gif$/i', '_.gif', $icon)) . ' ';
        }

        return $this->callParentFunctionPage($row, $href, $label, $title, $icon, $attributes, $strFunktion);
    }

    /**
     * 
     * @param type $row
     * @param type $href
     * @param type $label
     * @param type $title
     * @param type $icon
     * @param type $attributes
     * @param type $strFunktion
     * @return string
     */
    protected function callParentFunctionPage($row, $href, $label, $title, $icon, $attributes, $strFunction)
    {
        $arrParent = $GLOBALS['TL_DCA']['tl_page']['list']['operations'][$strFunction]['old_button_callback'];

        if (!is_array($arrParent))
        {
            return '';
        }

        $this->import($arrParent[0]);
        return $this->$arrParent[0]->$arrParent[1]($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * 
     * @param type $row
     * @param type $href
     * @param type $label
     * @param type $title
     * @param type $icon
     * @param type $attributes
     * @param type $strFunktion
     * @return string
     */
    protected function functionArticle($row, $href, $label, $title, $icon, $attributes, $strFunktion)
    {
        if (empty(self::$arrPageIdCache) && empty(self::$arrArticleIdCache))
        {
            return $this->callParentFunctionArticle($row, $href, $label, $title, $icon, $attributes, $strFunktion);
        }

        // Check page
        if (in_array($row['pid'], self::$arrPageIdCache))
        {
            return $this->generateImage(preg_replace('/\.gif$/i', '_.gif', $icon)) . ' ';
        }

        // Check article
        if (in_array($row['id'], self::$arrArticleIdCache))
        {
            return $this->generateImage(preg_replace('/\.gif$/i', '_.gif', $icon)) . ' ';
        }

        return $this->callParentFunctionArticle($row, $href, $label, $title, $icon, $attributes, $strFunktion);
    }

    /**
     * 
     * @param type $row
     * @param type $href
     * @param type $label
     * @param type $title
     * @param type $icon
     * @param type $attributes
     * @param type $strFunktion
     * @return string
     */
    protected function callParentFunctionArticle($row, $href, $label, $title, $icon, $attributes, $strFunction)
    {
        $arrParent = $GLOBALS['TL_DCA']['tl_article']['list']['operations'][$strFunction]['old_button_callback'];

        if (!is_array($arrParent))
        {
            return '';
        }

        $this->import($arrParent[0]);
        return $this->$arrParent[0]->$arrParent[1]($row, $href, $label, $title, $icon, $attributes);
    }

    /**
     * 
     * @param type $row
     * @param type $href
     * @param type $label
     * @param type $title
     * @param type $icon
     * @param type $attributes
     * @param type $strFunktion
     * @return string
     */
    protected function functionContent($row, $href, $label, $title, $icon, $attributes, $strFunktion)
    {
        if (empty(self::$arrPageIdCache) && empty(self::$arrArticleIdCache))
        {
            return $this->callParentFunctionArticle($row, $href, $label, $title, $icon, $attributes, $strFunktion);
        }

        $objArticle = $this->Database
                ->prepare("SELECT pid FROM tl_article WHERE id=?")
                ->execute($row['pid']);

        // Check page
        if ($objArticle->numRows != 0 && in_array($objArticle->pid, self::$arrPageIdCache))
        {
            return $this->generateImage(preg_replace('/\.gif$/i', '_.gif', $icon)) . ' ';
        }

        // Check article
        if (in_array($objArticle->id, self::$arrArticleIdCache))
        {
            return $this->generateImage(preg_replace('/\.gif$/i', '_.gif', $icon)) . ' ';
        }

        return $this->callParentFunctionContent($row, $href, $label, $title, $icon, $attributes, $strFunktion);
    }

    /**
     * 
     * @param type $row
     * @param type $href
     * @param type $label
     * @param type $title
     * @param type $icon
     * @param type $attributes
     * @param type $strFunktion
     * @return string
     */
    protected function callParentFunctionContent($row, $href, $label, $title, $icon, $attributes, $strFunction)
    {
        $arrParent = $GLOBALS['TL_DCA']['tl_content']['list']['operations'][$strFunction]['old_button_callback'];

        if (!is_array($arrParent))
        {
            return '';
        }

        $this->import($arrParent[0]);
        return $this->$arrParent[0]->$arrParent[1]($row, $href, $label, $title, $icon, $attributes);
    }

}

?>
