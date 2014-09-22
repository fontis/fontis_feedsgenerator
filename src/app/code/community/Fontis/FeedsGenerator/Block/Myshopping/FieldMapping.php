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
class Fontis_FeedsGenerator_Block_Myshopping_FieldMapping extends Fontis_FeedsGenerator_Block_FieldMapping
{
    /**
     * Label for feed attribute column on admin page
     *
     * @var string
     */
    protected $_feedFieldLabel = 'Myshopping feed tag';

    /**
     * Specifier for model that feed attributes are taken from
     *
     * @var string
     */
    protected $_feedAttributesModelSpecifier = 'feedsgenerator/myshopping_config_feedAttributes';

    /**
     * Map in some of the values not normally visible
     *
     * @var array
     */
    protected $_magentoOptions = array(
        'instock'       => 'instock',
        'brand'         => 'brand',
        'final_price'   => 'final_price',
        'product_id'    => 'product_id',
        "category"      => "category",
        "link"          => "link",
        "image_url"     => "image_url"
    );
}
