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

class Fontis_FeedsGenerator_Model_Shoptab_Cron extends Fontis_FeedsGenerator_Model_FeedCronBase_Csv
{
    /**
     * @var string
     */
    public $_configPath = 'shoptabfeed';

    /**
     * @var bool
     */
    public $generateCategories = false;

    /**
     * @var string
     */
    protected $_separator = "\t";

    /**
     * The fields to be put into the feed.
     *
     * @var array
     */
    protected $_requiredFields = array(
        array(
            'magento'   => 'name',
            'feed'      => 'title',
            'type'      => 'product_attribute',
        ),
        array(
            'magento'   => 'description',
            'feed'      => 'description',
            'type'      => 'product_attribute',
            'normalise_whitespace' => true,
        ),
        array(
            'magento'   => 'product_link',
            'feed'      => 'link',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'image_url',
            'feed'      => 'image_link',
            'type'      => 'computed',
        ),
        array(
            'magento'   => 'final_price',
            'feed'      => 'price',
            'type'      => 'computed',
        ),
    );

    protected function setupAppData()
    {
        $this->_requiredFields[] = array(
            'magento'   => 'FONTIS_UNSET_CONDITION',
            'feed'      => 'condition',
            'type'      => 'product_attribute',
            'default'   => $this->getConfig('default_condition'),
        );
    }
}
