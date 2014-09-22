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
class Fontis_FeedsGenerator_Block_Googleproducts_LinkFieldMapping extends Fontis_FeedsGenerator_Block_FieldMapping
{
    /**
     * @var string
     */
    protected $_feedFieldLabel = 'Google Products variant attribute';

    /**
     * @var string
     */
    protected $_feedAttributesModelSpecifier = 'feedsgenerator/googleproducts_config_linkAttributes';

    /**
     * @var array
     */
    protected $_magentoOptions = array();
}
