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
 * @author     Jeremy Champion
 * @copyright  Copyright (c) 2014 Fontis Pty. Ltd. (http://www.fontis.com.au)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Fontis_FeedsGenerator_Model_Googleproducts_Config_FeedAttributes
{
    public $availableFields = array(
        'g:id',
        'title',
        'link',
        'g:price',
        'description',
        'g:condition',
        'g:gtin',
        'g:brand',
        'g:mpn',
        'g:image_link',
        'g:product_type',
        'g:quantity',
        'g:availability',
        'g:feature',
        'g:online_only',
        'g:manufacturer',
        'g:expiration_date',
        'g:shipping_weight',
        'g:product_review_average',
        'g:product_review_count',
        'g:genre',
        'g:featured_product',
        'g:color',
        'g:size',
        'g:year',
        'g:author',
        'g:edition',
        'g:custom_label_0',
        'g:custom_label_1',
        'g:custom_label_2',
        'g:custom_label_3',
        'g:custom_label_4',
    );

    public $identifierFields = array(
        'g:gtin',
        'g:brand',
        'g:mpn',
    );
}
