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

class Fontis_FeedsGenerator_Model_ShopBot_Cron extends Fontis_FeedsGenerator_Model_FeedCronBase_Xml
{
    /**
     * @var string
     */
    public $_configPath = 'shopbotfeed';

    /**
     * @var bool
     */
    public $generateCategories = false;

    /**
     * @var string
     */
    public $productNodeName = 'item';

    /**
     * The fields to be put into the feed.
     *
     * @var array
     */
    protected $_requiredFields = array(
        array(
            'magento'   => 'sku',
            'feed'      => 'id',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'sku',
            'feed'      => 'mpn',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'category_path',
            'feed'      => 'category',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'name',
            'feed'      => 'productname',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'product_link',
            'feed'      => 'url',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'image_url',
            'feed'      => 'photoUrl',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'final_price',
            'feed'      => 'price',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'qty',
            'feed'      => 'stock',
            'type'      => 'stock_attribute',
        ),
        array(
            'magento'   => 'manufacturer',
            'feed'      => 'brand',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'short_description',
            'feed'      => 'description',
            'type'      => 'product_attribute',
        ),
    );

    /**
     * @param string $feedTag
     * @param mixed $value
     * @return mixed
     */
    protected function processBatchField($feedTag, $value)
    {
        if ($feedTag == 'category') {
            $value = implode(' - ', $value);
        }

        return $value;
    }

    protected function setupStoreData()
    {
        parent::setupStoreData();

        $this->_productsNode = $this->_dom->createElement('items');
        $this->_dom->appendChild($this->_productsNode);
    }
}
