<?php namespace Modules\Package\Sidebar;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Contracts\Authentication;

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
            $group->item(trans('package::packages.title.packages'), function (Item $item) {
                $item->icon('fa fa-suitcase');
                $item->weight(10);
                // $item->append('admin.package.package.create');
                $item->route('admin.package.package.index');
                $item->authorize(
                    $this->auth->hasAccess('package.packages.index')
                );
                // $item->weight(10);
                // $item->authorize(
                //      $this->auth->hasAccess('package.packages.index')
                // );
                // $item->item(trans('package::packages.title.packages'), function (Item $item) {
                //     $item->icon('fa fa-arrow-circle-o-right');
                //     $item->weight(0);
                //     // $item->append('admin.package.package.create');
                //     $item->route('admin.package.package.index');
                //     $item->authorize(
                //         $this->auth->hasAccess('package.packages.index')
                //     );
                // });
                // $item->item(trans('package::packageservices.title.packageservices'), function (Item $item) {
                //     $item->icon('fa fa-arrow-circle-o-right');
                //     $item->weight(1);
                //     // $item->append('admin.package.packageservice.create');
                //     $item->route('admin.package.packageservice.index');
                //     $item->authorize(
                //         $this->auth->hasAccess('package.packageservices.index')
                //     );
                // });
// append


            });
        });

        return $menu;
    }
}
