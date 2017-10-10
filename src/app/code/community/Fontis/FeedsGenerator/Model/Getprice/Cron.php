<?php
/**
 * Fontis FeedsGenerator Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Fontis
 * @package    Fontis_FeedsGenerator
 * @author     Peter Spiller
 * @copyright  Copyright (c) 2014 Fontis Pty. Ltd. (http://www.fontis.com.au)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Fontis_FeedsGenerator_Model_GetPrice_Cron extends Fontis_FeedsGenerator_Model_FeedCronBase_Xml
{
    /**
     * @var string
     */
    public $_configPath = 'getpricefeed';

    /**
     * @var bool
     */
    public $generateCategories = true;

    /**
     * @var bool
     */
    protected $_generateProductCategory = true;

    /**
     * The fields to be put into the feed.
     *
     * @var array
     */
    protected $_requiredFields = array(
        array(
            'magento'   => 'entity_id',
            'feed'      => 'product_num',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'sku',
            'feed'      => 'upc',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'name',
            'feed'      => 'product_name',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'description',
            'feed'      => 'description',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'category_last',
            'feed'      => 'category_name',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'image_url',
            'feed'      => 'image',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'final_price',
            'feed'      => 'price',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'currency',
            'feed'      => 'currency',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'product_link',
            'feed'      => 'product_url',
            'type'      => 'computed',
        ),
    );

    protected function setupAppData()
    {
        if ($this->getConfig('manufacturer')) {
            $this->_requiredFields[] = array(
                'magento'   => 'manufacturer',
                'feed'      => 'manufacturer',
                'type'      => 'product_attribute',
            );
        }
    }

    protected function setupStoreData()
    {
        parent::setupStoreData();

        $storeNode = $this->_dom->createElement('store');
        $storeNode->setAttribute('url', $this->info("store_url"));
        $storeNode->setAttribute('date', $this->info("date"));
        $storeNode->setAttribute('time', $this->info("time"));
        $storeNode->setAttribute('name', $this->info("shop_name"));
        $this->_dom->appendChild($storeNode);

        $this->_productsNode = $this->_dom->createElement('products');
        $storeNode->appendChild($this->_productsNode);
    }

    protected function getCategoriesXml()
    {
        $result = array();
        /** @var $categories Mage_Catalog_Model_Category[]|Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
        $categories = Mage::getModel('catalog/category')->getCollection()
                ->setStoreId($this->_store)
                ->addAttributeToFilter('is_active', 1);
        $categories->load()->getItems();

        $fullCategories = array();

        foreach ($categories as $category) {
            $id = $category->getId();
            $category = Mage::getModel('catalog/category')->load($id);

            $children = $category->getAllChildren(true);
            if (count($children) <= 1) {
                $fullCategories[] = $category;
            }
        }

        $categoryDOM = new DOMDocument('1.0', 'UTF-8');
        $categoryDOM->preserveWhiteSpace = false;
        $categoryDOM->formatOutput = true;

        $catStoreNode = $categoryDOM->createElement('store');
        $catStoreNode->setAttribute('url', $this->info('store_url'));
        $catStoreNode->setAttribute('date', $this->info('date'));
        $catStoreNode->setAttribute('time', $this->info('time'));
        $catStoreNode->setAttribute('name', $this->info('shop_name'));
        $categoryDOM->appendChild($catStoreNode);

        foreach ($fullCategories as $category) {
            $categoryNode = $categoryDOM->createElement('cat');
            $catStoreNode->appendChild($categoryNode);

            $nameNode = $categoryDOM->createElement('name');
            $cdataNode = $categoryDOM->createCDATASection($category->getName());
            $nameNode->appendChild($cdataNode);
            $categoryNode->appendChild($nameNode);

            $url = Mage::getStoreConfig('web/unsecure/base_url', $this->_store) .
                $this->getConfig('output') .
                $this->info('clean_store_name') . '-products-' . $category->getId() . '.xml';
            $linkNode = $categoryDOM->createElement('link');
            $cdataNode = $categoryDOM->createCDATASection($url);
            $linkNode->appendChild($cdataNode);
            $categoryNode->appendChild($linkNode);

            $result['link_ids'][] = $category->getId();
        }

        // Create categories XML feed
        $this->createXml(array(
            'name' => 'categories',
            'doc' => $categoryDOM
        ));

        return $result;
    }

    /**
     * @param array $data
     */
    protected function createXml($data = array())
    {
        // Write DOM to file
        $filename = Mage::getModel('catalog/product_url')->formatUrlKey($this->_store->getName()) . '-' . $data['name'] . '.xml'; // Sanitize file name
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));
        $dom = $data['doc'];
        $io->write($filename, $dom->saveXML());
        $io->close();
    }

    /**
     * @param int|null $catId
     */
    protected function finaliseStoreData($catId = null)
    {
        $this->createXml(array(
            'name' => $catId ? 'products-' . $catId : 'products',
            'doc' => $this->_dom
        ));
    }
}
