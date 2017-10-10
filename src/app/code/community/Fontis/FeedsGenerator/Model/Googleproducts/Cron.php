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
 * @author     Chris Norton
 * @copyright  Copyright (c) 2014 Fontis Pty. Ltd. (http://www.fontis.com.au)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Fontis_FeedsGenerator_Model_Googleproducts_Cron extends Fontis_FeedsGenerator_Model_FeedCronBase_Xml
{
    /**
     * @var string
     */
    public $_configPath = 'googleproductsfeed';

    /**
     * @var bool
     */
    public $generateCategories = false;

    protected $_childClass = 'feedsgenerator/googleproducts_child';

    /**
     * @var XMLWriter
     */
    protected $doc;

    /**
     * The fields to be put into the feed.
     *
     * @var array
     */
    protected $_requiredFields = array(
        array(
            'magento'   => 'sku',
            'feed'      => 'g:id',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'name',
            'feed'      => 'title',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'product_link',
            'feed'      => 'g:link',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'final_price',
            'feed'      => 'g:price',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'availability_google',
            'feed'      => 'g:availability',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'description',
            'feed'      => 'description',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'image_url',
            'feed'      => 'g:image_link',
            'type'      => 'computed',
        ),
    );

    protected function setupAppData()
    {
        $this->_requiredFields[] = array(
            'magento'   => 'FONTIS_UNSET_CONDITION',
            'feed'      => 'g:condition',
            'type'      => 'product_attribute',
            'default'   => $this->getConfig('default_condition'),
        );

        $excludedCategories = explode(',', $this->getConfig('exclude_cats'));
        $this->_requiredFields[] = array(
            'magento'       => 'category_path',
            'feed'          => 'g:product_type',
            'type'          => 'computed',
            'exclude_cats'  => $excludedCategories,
        );
        $this->_requiredFields[] = array(
            'magento'   => 'google_taxonomy',
            'feed'      => 'g:google_product_category',
            'type'      => 'computed',
            'exclude_cats'  => $excludedCategories,
        );
    }

    /*
     * Instantiate the XML object
     */
    protected function setupStoreData()
    {
        // Using XMLWriter because SimpleXML namespaces on attribute names
        $this->doc = new XMLWriter();
        $this->doc->openMemory();
        $this->doc->setIndent(true);
        $this->doc->setIndentString('    ');
        $this->doc->startDocument('1.0', 'UTF-8');
        $this->doc->startElement('feed');
        $this->doc->writeAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        $this->doc->writeAttribute('xmlns:g', 'http://base.google.com/ns/1.0');

        $this->doc->writeElement('title', $this->getConfig('title'));

        $this->doc->startElement('link');
        $this->doc->writeAttribute('rel', 'self');
        $this->doc->writeAttribute('href', $this->_store->getBaseUrl());
        $this->doc->endElement();

        $date = new Zend_Date();
        $this->doc->writeElement('updated', $date->get(Zend_Date::ATOM));

        $this->doc->startElement('author');
        $this->doc->writeElement('name', $this->getConfig('author'));
        $this->doc->endElement();

        $url = $this->_store->getBaseUrl();
        $day = $date->toString('yyyy-MM-dd');
        $path = $this->getConfig('output');
        $filename = $path . '/' . str_replace('+', '-', strtolower(urlencode($this->_store->getName()))) . '-products.xml';

        $this->doc->writeElement('id', 'tag:' . $url . ',' . $day . ':' . $filename);
    }

    /**
     * Data returned from a child process at the batch level
     *
     * @param array $batchData
     */
    protected function populateFeedWithBatchData($batchData)
    {
        foreach ($batchData as $product) {
            // Google Merchant does not support products that have no price
            $price = floatval($product['g:price']);
            if (empty($price)) {
                continue;
            }

            $this->doc->startElement('entry');

            if (isset($product['g:product_type'])) {
                $product['g:product_type'] = implode(' > ', $product['g:product_type']);
            }

            foreach ($product as $feedTag => $value) {
                $safeString = null;

                switch ($feedTag) {
                    case 'g:google_product_category':
                        if ($value != 'No') {
                            $safeString = $value;
                        }
                        break;

                    case 'g:custom_label_0':
                    case 'g:custom_label_1':
                    case 'g:custom_label_2':
                    case 'g:custom_label_3':
                    case 'g:custom_label_4':
                        if ($value && $value != 'No') {
                            $safeString = $value;
                        }
                        break;

                    case 'g:link':
                        // Links must be written as an attribute
                        $this->doc->startElement($feedTag);
                        $this->doc->writeAttribute('href', $value);
                        $this->doc->endElement();
                        break;

                    case 'g:image_link':
                        if ($value == 'no_selection') {
                            $safeString = '';
                        } else {
                            $safeString = $value;

                            // Check if the link is a full URL
                            if (substr($value, 0, 5) != 'http:' && substr($value, 0, 6) != 'https:') {
                                $safeString = $this->_store->getBaseUrl('media') . 'catalog/product' . $value;
                            }
                        }
                        break;

                    default:
                        // Google doesn't like HTML tags in the feed
                        $safeString = strip_tags($value);
                        break;
                }

                if ($safeString !== null) {
                    $this->doc->startElement($feedTag);
                    $this->doc->writeCData($safeString);
                    $this->doc->endElement();
                }
            }

            $this->doc->endElement();
        }
    }

    protected function finaliseStoreData()
    {
        // Write the end of the xml document
        $this->doc->endElement();
        $this->doc->endDocument();

        // Write dom to file
        $cleanStoreName = str_replace('+', '-', Mage::getModel('catalog/product_url')->formatUrlKey($this->_store->getName())); // Sanitize file name
        $filename = $cleanStoreName . '-products.xml';
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));
        $io->write($filename, $this->doc->outputMemory());
        $io->close();
    }
}
