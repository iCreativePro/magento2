<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Core\Model\File\Storage\Database;

/**
 * Class AbstractDatabase
 */
abstract class AbstractDatabase extends \Magento\Core\Model\AbstractModel
{
    /**
     * Store media base directory path
     *
     * @var string
     */
    protected $_mediaBaseDirectory = null;

    /**
     * Core file storage database
     *
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $_coreFileStorageDb = null;

    /**
     * Date model
     *
     * @var \Magento\Core\Model\Date
     */
    protected $_date;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Helper\File\Storage\Database $coreFileStorageDb
     * @param \Magento\Core\Model\Date $dateModel
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param string|null $connectionName
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Helper\File\Storage\Database $coreFileStorageDb,
        \Magento\Core\Model\Date $dateModel,
        \Magento\Core\Model\App $app,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        $connectionName = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_app = $app;
        $this->_coreFileStorageDb = $coreFileStorageDb;
        $this->_date = $dateModel;
        if (!$connectionName) {
            $connectionName = $this->getConfigConnectionName();
        }
        $this->setConnectionName($connectionName);
    }

    /**
     * Retrieve connection name saved at config
     *
     * @return string
     */
    public function getConfigConnectionName()
    {
        $connectionName = $this->_app->getConfig()
            ->getValue(\Magento\Core\Model\File\Storage::XML_PATH_STORAGE_MEDIA_DATABASE, 'default');
        if (empty($connectionName)) {
            $connectionName = 'default_setup';
        }
        return $connectionName;
    }

    /**
     * Get resource instance
     *
     * @return \Magento\Core\Model\Resource\AbstractResource
     */
    protected function _getResource()
    {
        $resource = parent::_getResource();
        $resource->setConnectionName($this->getConnectionName());

        return $resource;
    }

    /**
     * Prepare data storage
     *
     * @return \Magento\Core\Model\File\Storage\Database
     */
    public function prepareStorage()
    {
        $this->_getResource()->createDatabaseScheme();

        return $this;
    }

    /**
     * Specify connection name
     *
     * @param  $connectionName
     * @return \Magento\Core\Model\File\Storage\Database
     */
    public function setConnectionName($connectionName)
    {
        if (!empty($connectionName)) {
            $this->setData('connection_name', $connectionName);
            $this->_getResource()->setConnectionName($connectionName);
        }

        return $this;
    }
}
