<?php

return [
	'name' => 'AuditTrail',
	'entity' => [
		'Modules\Customer\Entities\Customer' => 'Customer',
		'Modules\Vendor\Entities\Vendor' => 'Vendor',
		'Modules\Advertisement\Entities\Advertisement' => 'Advertisement',
		'Modules\Category\Entities\Category' => 'Category',
		'Modules\Category\Entities\SubCategory' => 'Sub Category',
		'Modules\Comment\Entities\Vendorcomment' => 'Comment',
		'Modules\Comment\Entities\Comment' => 'Review',
		'Modules\Report\Entities\Comment' => 'Report Comment',
		'Modules\Report\Entities\Review' => 'Report Review',
		'Modules\Portfolio\Entities\Portfolio' => 'Portfolio',
		'Modules\PriceRange\Entities\PriceRange' => 'PriceRange',
		'Modules\Page\Entities\Page' => 'Page',
		'Modules\User\Entities\Sentinel\User' => 'User',
	],
];