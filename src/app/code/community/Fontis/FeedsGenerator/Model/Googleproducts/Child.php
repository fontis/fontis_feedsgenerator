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

class Fontis_FeedsGenerator_Model_Googleproducts_Child extends Fontis_FeedsGenerator_Model_Child
{
    public function __construct($config)
    {
        parent::__construct($config);

        $this->use_variant_configurables = Mage::getStoreConfig(
            'fontis_feedsgenerator/googleproductsfeed/variant_configurables',
            $this->config->store_id
        );

        $this->linkAttributes = array();

        // load the Magento to Google variant attributes array
        $linkAttributesSetting = unserialize(Mage::getStoreConfig('fontis_feedsgenerator/googleproductsfeed/link_attributes', $this->config->store_id));

        if ($linkAttributesSetting) {
            foreach ($linkAttributesSetting as $linkAttribute) {
                $this->linkAttributes[$linkAttribute['magento']] = $linkAttribute['xmlfeed'];
            }
        }
    }

    /**
     * Don't generate an entry for configurable products; instead, generate
     * an entry for each of their child products, adding on the
     * g:item_group_id field containing the parent product's SKU to each.
     *
     * @var Mage_Catalog_Model_Product $product
     * @return array
     */
    public function processProduct(Mage_Catalog_Model_Product $parentProduct)
    {
        if ($this->use_variant_configurables && $parentProduct->getTypeId() == 'configurable') {
            // Get all configurable attributes from the parent product and see
            // if mappings have been provided for them from Magento to
            // Google's variant attributes. If a mapping is found, add a new
            // atttribute specifier object to the list that's processed by
            // getProductData().
            $variantAttributes = array();
            foreach ($parentProduct->getTypeInstance()->getUsedProductAttributes($parentProduct) as $attribute) {
                if (isset($this->linkAttributes[$attribute->getAttributeCode()])) {
                    $variantAttributes[$this->linkAttributes[$attribute->getAttributeCode()]] = (object)array(
                        'magento' => $attribute->getAttributeCode(),
                        'feed' => $this->linkAttributes[$attribute->getAttributeCode()],
                        'type' => 'product_attribute',
                    );
                }
            }

            // Use the parent's link rather than the child's, as the child
            // won't normally be directly accessible.
            $variantAttributes['product_link'] = (object)array(
                'value' => $this->getCleanProductUrl($parentProduct),
                'feed' => 'g:link',
                'type' => 'constant',
            );

            $products = array();
            foreach ($parentProduct->getTypeInstance()->getUsedProducts(null, $parentProduct) as $child) {
                $childData = $this->getProductData($child, $variantAttributes);
                $childData['g:item_group_id'] = $parentProduct->getSku();
                $products[] = $childData;
            }
        } else {
            $products = array($this->getProductData($parentProduct));
        }

        return $products;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param array $additionalAttributes
     * @return array
     */
    public function getProductData(Mage_Catalog_Model_Product $product, $additionalAttributes = array())
    {
        $productData = parent::getProductData($product, $additionalAttributes);

        $identifierAttributes = Mage::getModel('feedsgenerator/googleproducts_config_feedAttributes')->identifierFields;
        $hasIdentifier = false;
        foreach ($identifierAttributes as $identifier) {
            if (!empty($productData[$identifier])) {
                $hasIdentifier = true;
                break;
            }
        }
        $productData['g:identifier_exists'] = $hasIdentifier === true ? "true" : "false";

        return $productData;
    }
}
