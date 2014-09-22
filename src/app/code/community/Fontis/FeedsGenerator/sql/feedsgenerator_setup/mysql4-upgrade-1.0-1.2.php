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

/** @var $installer Mage_Eav_Model_Entity_Setup */

const PROCESS_ID = 'google_taxonomy';

$indexProcess = new Mage_Index_Model_Process();
$indexProcess->setId(PROCESS_ID);

if (!$indexProcess->isLocked()) {
    $indexProcess->lockAndBlock();

    $installer = $this;

    $installer->startSetup();

    // Create (or re-create) the table containing all of the Google taxonomy information. This is a list
    // of available taxonomy values.
    $installer->run("
        DROP TABLE IF EXISTS {$this->getTable('google_taxonomy')};
        CREATE TABLE {$this->getTable('google_taxonomy')} (
          `taxonomy_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `taxonomy_name` varchar(255) NOT NULL,
          PRIMARY KEY (`taxonomy_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

    // Fill the taxonomy table with values provided by Google. Values are inserted in batches of
    // 1000 to increase processing speed.
    $filename = __DIR__ . '/taxonomy.en-AU.txt';
    if (file_exists($filename) && is_readable($filename)) {
        $taxonomies = file($filename);

        // Remove the first line as it's a comment
        array_shift($taxonomies);

        $values = array();
        $i = 0;

        foreach ($taxonomies as $taxonomy) {
            $values[] = "('" . addslashes(trim($taxonomy)) . "')";

            // Process the file in batches
            if($i++ % 1000 == 0) {
                $insertValues = implode(',', $values);
                $installer->run("INSERT INTO {$this->getTable('google_taxonomy')} (`taxonomy_name`) VALUES {$insertValues};");
                $values = array();
            }
        }

        // Process any remaining values
        if(count($values)) {
            $insertValues = implode(',', $values);
            $installer->run("INSERT INTO {$this->getTable('google_taxonomy')} (`taxonomy_name`) VALUES {$insertValues};");
        }
    }

    // Add a new category attribute to allow setting the taxonomy, to be displayed on the General Information
    // tab underneath the usual core attributes
    $installer->addAttribute('catalog_category', 'google_product_category', array(
        'group'         => 'General Information',
        'input'         => 'select',
        'label'         => 'Google Product Category',
        'source'        => 'feedsgenerator/googleproducts_source_taxonomy',
        'sort_order'    => 15,
        'type'          => 'int',
    ));

    $installer->endSetup();

    $indexProcess->unlock();
}
