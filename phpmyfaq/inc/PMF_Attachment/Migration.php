<?php
/**
 * Attachment migration handler
 *
 * @package    phpMyFAQ
 * @license    MPL
 * @author     Anatoliy Belsky <ab@php.net>
 * @since      2009-09-13
 * @version    SVN: $Id: Migration.php 4885 2009-09-06 20:56:12Z anatoliy $
 * @copyright  2009 phpMyFAQ Team
 *
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 */

/**
 * PMF_Atachment_Migration
 * 
 * @package    phpMyFAQ
 * @license    MPL
 * @author     Anatoliy Belsky <ab@php.net>
 * @since      2009-09-13
 * @version    SVN: $Id: Migration.php 4885 2009-09-06 20:56:12Z anatoliy $
 * @copyright  2009 phpMyFAQ Team
 */
class PMF_Attachment_Migration
{
    /**
     * Migrate 2.0.x, 2.5.x to 2.6+ files without encryption
     */
    const MIGRATION_TYPE1 = 1;
    
    /**
     * Migrate 2.0.x, 2.5.x to 2.6+ files encrypting with default key
     */
    const MIGRATION_TYPE2 = 2;
        
    /**
     * Migrate encrypted to unencrypted.
     * NOTE this will migrate only files encrypted
     * with default key
     */
    const MIGRATION_TYPE3 = 3;

    /**
     * Migrate files encrypted with default key
     * to unencrypted files
     */
    const MIGRATION_TYPE4 = 4;
    
    /**
     * Errors
     * 
     * @var array
     */
    protected $error = array();
    
    /**
     * Warnings
     * 
     * @var array
     */
    protected $warning = array();
    
    
    protected function getOldFileList($dir)
    {
        $list = array();
        $faq  = new PMF_Faq;
        
        $records = reset($faq->getAllRecords());
        while(list(,$item) = each($records)) {
            $itemDir = "$dir/$item";
            print_r($item);
        }
    }
    
    /**
     * Migrate
     * 
     * @param integer $migrationType how to migrate
     * @param array   $options       migration options
     * 
     * @return boolean
     */
    public function doMigrate($migrationType, $options)
    {
        switch($migrationType) {
            case PMF_Attachment_Migration::MIGRATION_TYPE1:
                $list = $this->getOldFileList(PMF_ATTACHMENTS_DIR);
                
                break;
              
            case PMF_Attachment_Migration::MIGRATION_TYPE2:
                /**
                 * Awaiting new default key here
                 */
                if(isset($options['defaultKey'])) {
                    
                } else {
                    $this->error[] = 'Default key empty but required';
                }
                break;
                
            case PMF_Attachment_Migration::MIGRATION_TYPE3:
                // TODO implement this
                $this->error[] = 'not implemented';
                break;
                
            case PMF_Attachment_Migration::MIGRATION_TYPE4:
                //TODO implenemt this
                $this->error[] = 'not implemented';
                break;
                
            default:
                $this->error[] = 'Nothing to do';
                break;
                
        }
        
        return empty($this->error);
    }
    
    /**
     * Get migration errors
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->error;
    }
    
    /**
     * Get migration warnings
     * 
     * @return array
     */
    public function getWarnings()
    {
        return $this->warning;
    }
}