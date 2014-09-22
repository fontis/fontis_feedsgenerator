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

class Fontis_FeedsGenerator_Model_MyShopping_Cron extends Fontis_FeedsGenerator_Model_FeedCronBase_Xml
{
    /**
     * @var string
     */
    public $_configPath = 'myshoppingfeed';

    /**
     * @var bool
     */
    public $generateCategories = true;

    /**
     * The fields to be put into the feed.
     *
     * @var array
     */
    protected $_requiredFields = array(
        array(
            'magento'   => 'sku',
            'feed'      => 'Code',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'category_last',
            'feed'      => 'Category',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'name',
            'feed'      => 'Name',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'description',
            'feed'      => 'Description',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'product_link',
            'feed'      => 'Product_URL',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'final_price',
            'feed'      => 'Price',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'image_url',
            'feed'      => 'Image_URL',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'manufacturer',
            'feed'      => 'Brand',
            'type'      => 'product_attribute',
            'default'   => 'Generic',
        ),
        array(
            'magento'   => 'instock_y_n',
            'feed'      => 'InStock',
            'type'      => 'computed',
        ),
    );

    protected function setupStoreData()
    {
        parent::setupStoreData();

        $this->_productsNode = $this->_dom->createElement('productset');
        $this->_dom->appendChild($this->_productsNode);
    }
}
