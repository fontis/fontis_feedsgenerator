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

class Fontis_FeedsGenerator_Model_ShoppingDotCom_Cron extends Fontis_FeedsGenerator_Model_FeedCronBase_Xml
{
    /**
     * @var string
     */
    public $_configPath = 'shoppingdotcomfeed';

    /**
     * @var bool
     */
    public $generateCategories = false;

    /**
     * The fields to be put into the feed.
     *
     * @var array
     */
    protected $_requiredFields = array(
        array(
            'magento'   => 'sku',
            'feed'      => 'Merchant_SKU',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'name',
            'feed'      => 'Product_Name',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'product_link',
            'feed'      => 'Product_URL',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'image_url',
            'feed'      => 'Image_URL',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'final_price',
            'feed'      => 'Current_Price',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'instock_y_n',
            'feed'      => 'Stock_Availability',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'manufacturer',
            'feed'      => 'Manufacturer',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'description',
            'feed'      => 'Product_Description',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'category_path',
            'feed'      => 'Category',
            'type'      => 'computed',
        ),
    );

    protected function setupStoreData()
    {
        parent::setupStoreData();

        $this->_productsNode = $this->_dom->createElement('products');
        $this->_dom->appendChild($this->_productsNode);
    }

    /**
     * @param string $feedTag
     * @param mixed $value
     * @return mixed
     */
    protected function processBatchField($feedTag, $value)
    {
        if ($feedTag == 'Category') {
            $value = implode(' > ', $value);
        }

        return $value;
    }
}
