<?php namespace Modules\Banner\Sidebar;

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
//         $menu->group(trans('core::sidebar.mummy'), function (Group $group) {
//             $group->item(trans('banner::banners.title.banners'), function (Item $item) {
//                 $item->icon('fa fa-copy');
//                 $item->weight(10);
//                 $item->authorize(
//                      /* append */
//                 );
//                 $item->item(trans('banner::banners.title.banners'), function (Item $item) {
//                     $item->icon('fa fa-copy');
//                     $item->weight(0);
//                     $item->append('admin.banner.banner.create');
//                     $item->route('admin.banner.banner.index');
//                     $item->authorize(
//                         $this->auth->hasAccess('banner.banners.index')
//                     );
//                 });
// // append

//             });
//         });

        $menu->group(trans('core::sidebar.mummy'), function (Group $group) {
            $group->weight(1);
            $group->item(trans('banner::banners.title.banners'), function (Item $item) {
                $item->icon('fa fa-suitcase');
                $item->weight(1);
                $item->route('admin.banner.banner.index');
                $item->authorize(
                    $this->auth->hasAccess('banner.banners.index')
                );
            });
        });

        return $menu;
    }
}
