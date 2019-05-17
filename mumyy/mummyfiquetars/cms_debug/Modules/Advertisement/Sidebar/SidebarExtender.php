<?php namespace Modules\Advertisement\Sidebar;

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
            $group->item(trans('advertisement::advertisements.title.advertisements'), function (Item $item) {
                $item->icon('fa fa-suitcase');
                $item->weight(1);
                $item->route('admin.advertisement.advertisement.index');
                $item->authorize(
                    $this->auth->hasAccess('advertisement.advertisements.index')
                );
            });
        });

        return $menu;
    }
}
