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
 * @author     Thai Phan
 * @copyright  Copyright (c) 2014 Fontis Pty. Ltd. (http://www.fontis.com.au)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Fontis_FeedsGenerator_Model_Googleproducts_Taxonomy
{
    public function toOptionArray()
    {
        $options = array();
        // Set the default value
        $options[] = array('value' => 0, 'label' => '');

        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');

        $taxonomies = $read->query('SELECT * FROM google_taxonomy')->fetchAll();

        foreach ($taxonomies as $taxonomy) {
            $id = $taxonomy['taxonomy_id'];
            $name = $taxonomy['taxonomy_name'];
            $options[] = array('value' => $id, 'label' => $name);
        }

        return $options;
    }
}
