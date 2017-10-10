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

class Fontis_FeedsGenerator_Model_FeedCronBase_Xml extends Fontis_FeedsGenerator_Model_FeedCronBase
{
    /**
     * @var string
     */
    public $_configPath = 'myshoppingfeed';

    /**
     * @var string
     */
    public $productNodeName = 'product';

    /**
     * @var bool
     */
    public $generateCategories = true;

    /**
     * @var DOMDocument
     */
    protected $_dom;

    /**
     * Node containing product elements
     *
     * @var DOMElement
     */
    protected $_productsNode;

    protected function setupStoreData()
    {
        $this->_dom = new DOMDocument('1.0', 'UTF-8');
        $this->_dom->preserveWhiteSpace = false;
        $this->_dom->formatOutput = true;
    }

    /**
     * Data returned from a child process at the batch level
     *
     * @param array $batchData
     */
    protected function populateFeedWithBatchData($batchData)
    {
        foreach ($batchData as $product) {
            $product_node = $this->_dom->createElement($this->productNodeName);
            foreach ($product as $feed_tag => $value) {
                $value = $this->processBatchField($feed_tag, $value);

                $field_element = $this->_dom->createElement($feed_tag);
                $cdata_node = $this->_dom->createCDATASection($value);
                $field_element->appendChild($cdata_node);
                $product_node->appendChild($field_element);
            }
            $this->_productsNode->appendChild($product_node);
        }
    }

    protected function finaliseStoreData()
    {
        // Write DOM to file
        $filename = Mage::getModel('catalog/product_url')->formatUrlKey($this->_store->getName()) . '-products.xml'; // Sanitize file name
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));
        $io->write($filename, $this->_dom->saveXML());
        $io->close();
    }
}
