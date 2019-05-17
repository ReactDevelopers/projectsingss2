<?php namespace Modules\Vendor\Sidebar;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Contracts\Authentication;
use Modules\Vendor\Repositories\VendorCategoryRepository;
use Maatwebsite\Sidebar\Badge;

class SidebarExtender implements \Maatwebsite\Sidebar\SidebarExtender
{
    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * @param Authentication $auth
     *
     * @internal param Guard $guard
     */
    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Menu $menu
     *
     * @return Menu
     */
    public function extendWith(Menu $menu)
    {
        $menu->group(trans('core::sidebar.mummy'), function (Group $group) {
            $group->weight(1);
            $group->item(trans('vendor::vendors.title.vendors'), function (Item $item) {
                $item->icon('fa fa-suitcase');
                $item->weight(0);
                // $item->append('admin.vendor.vendor.create');
                // $item->route('admin.vendor.vendor.index');
                // $item->authorize(
                //     $this->auth->hasAccess('vendor.vendors.index')
                // );
                $item->item(trans('vendor::vendors.title.vendors'), function (Item $item) {
                    $item->icon('fa fa-arrow-circle-o-right ');
                    $item->weight(0);
                    // $item->append('admin.category.category.create');
                    $item->route('admin.vendor.vendor.index');
                    $item->authorize(
                        $this->auth->hasAccess('vendor.vendors.index')
                    );
                });
                $item->item(trans('vendor::categoryrequest.title.vendors category request'), function (Item $item) {
                    $item->icon('fa fa-arrow-circle-o-right ');
                    $item->weight(1);
                    $item->badge(function (Badge $badge, VendorCategoryRepository $category) {
                        $badge->setClass('bg-red');
                        $badge->setValue($category->countAllRequest());
                    });
                    // $item->append('admin.category.subcategory.create');
                    $item->route('admin.vendor.categoryrequest.index');
                    $item->authorize(
                        $this->auth->hasAccess('vendor.categoryrequest.index')
                    );
                });
            });

            $group->item('AuditTrail', function (Item $item) {
                $item->icon('fa fa-history');
                $item->weight(100);
                $item->authorize(
                        $this->auth->hasAccess('audittrail.*')
                );
                $item->item(trans('audittrail::logs.title.logs'), function (Item $item) {
                    $item->icon('fa fa-history');
                    $item->weight(0);
//                    $item->append('admin.audittrail.log.create');
                    $item->route('admin.audittrail.log.index');
                    $item->authorize(
                        $this->auth->hasAccess('audittrail.logs.index')
                    );
                });
// append

            });
        });

        return $menu;
    }
}
