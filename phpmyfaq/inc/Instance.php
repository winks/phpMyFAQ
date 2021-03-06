<?php
/**
 * The main phpMyFAQ instances class
 *
 * PHP Version 5.3
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at http://mozilla.org/MPL/2.0/.
 *
 * @category  phpMyFAQ
 * @package   PMF_Instance
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2012 phpMyFAQ Team
 * @license   http://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      http://www.phpmyfaq.de
 * @since     2012-02-20
 */

if (!defined('IS_VALID_PHPMYFAQ')) {
    exit();
}

/**
 * PMF_Instance
 *
 * @category  phpMyFAQ
 * @package   PMF_Instance
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2012 phpMyFAQ Team
 * @license   http://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      http://www.phpmyfaq.de
 * @since     2012-02-20
 */
class PMF_Instance
{
    /**
     * Configuration
     *
     * @var PMFconfiguration
     */
    protected $config = null;

    /**
     * Instance ID
     *
     * @var integer
     */
    protected $id;

    /**
     * Instance configuration
     *
     * @var array
     */
    protected $instanceConfig = array();

    /**
     * Constructor
     *
     * @param PMF_Configuration $config
     *
     * @return PMF_Instance
     */
    public function __construct(PMF_Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Adds a new instance
     *
     * @param array $data
     *
     * @return integer $id
     */
    public function addInstance(Array $data)
    {
        $this->setId($this->config->getDb()->nextId(SQLPREFIX . 'faqinstances', 'id'));

        $insert = sprintf(
            "INSERT INTO %sfaqinstances VALUES (%d, '%s', '%s', '%s', NOW(), NOW())",
            SQLPREFIX,
            $this->getId(),
            $data['url'],
            $data['instance'],
            $data['comment']
        );

        if (! $this->config->getDb()->query($insert)) {
            return 0;
        }

        return $this->getId();
    }

    /**
     * Sets the instance ID
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * Returns the current instance id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns all instances
     *
     * @return array
     */
    public function getAllInstances()
    {
        $select = sprintf(
            "SELECT * FROM %sfaqinstances",
            SQLPREFIX
        );

        $result = $this->config->getDb()->query($select);

        return $this->config->getDb()->fetchAll($result);
    }

    /**
     * Returns the instance
     *
     * @param integer $id
     *
     * @return array
     */
    public function getInstanceById($id)
    {
        $select = sprintf(
            "SELECT * FROM %sfaqinstances WHERE id = %d",
            SQLPREFIX,
            (int)$id
        );

        $result = $this->config->getDb()->query($select);

        return $this->config->getDb()->fetchAll($result);
    }

    /**
     * Returns the instance
     *
     * @param string $url
     *
     * @return array
     */
    public function getInstanceByUrl($url)
    {
        $select = sprintf(
            "SELECT * FROM %sfaqinstances WHERE url = '%s'",
            SQLPREFIX,
            $url
        );

        $result = $this->config->getDb()->query($select);

        return $this->config->getDb()->fetchAll($result);
    }

    /**
     * Returns the configuration of the given instance ID
     * @param $id
     *
     * @return PMF_DB_Driver
     */
    public function getInstanceConfig($id)
    {
        $query = sprintf("
            SELECT
                config_name, config_value
            FROM
                %sfaqinstances_config
            WHERE
                instance_id = %d",
            SQLPREFIX,
            $id
        );

        $result = $this->config->getDb()->query($query);
        $config = $this->config->getDb()->fetchAll($result);

        foreach ($config as $items) {
            $this->instanceConfig[$items->config_name] = $items->config_value;
        }
    }

    /**
     * Deletes an instance
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function removeInstance($id)
    {
        $deletes = array(
            sprintf(
                "DELETE FROM %sfaqinstances WHERE id = %d",
                SQLPREFIX,
                (int)$id
            ),
            sprintf(
                "DELETE FROM %sfaqinstancesconfig WHERE instanceid = %d",
                SQLPREFIX,
                (int)$id
            ),
        );

        foreach ($deletes as $delete) {
            $success = $this->config->getDb()->query($delete);
            if (! $success) {
                return false;
            }
        }

        return true;
    }

    /**
     * Adds a configuration item for the database
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return boolean
     */
    public function addConfig($name, $value)
    {
        $insert = sprintf(
            "INSERT INTO
                %sfaqinstances_config
            VALUES
                (%d, '%s', '%s')",
            SQLPREFIX,
            $this->getId(),
            $this->config->getDb()->escape(trim($name)),
            $this->config->getDb()->escape(trim($value))
        );

        return $this->config->getDb()->query($insert);
    }

    /**
     * Returns the configuration value
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getConfig($name)
    {
        if (! isset($this->instanceConfig[$name])) {
            $this->getInstanceConfig($this->getId());
        }

        switch ($this->instanceConfig[$name]) {
            case 'true':
                return true;
                break;
            case 'false':
                return false;
                break;
            default:
                return $this->instanceConfig[$name];
                break;
        }
    }
}