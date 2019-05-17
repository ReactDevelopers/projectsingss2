<?php namespace Modules\PriceRange\Sidebar;

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
            $group->item(trans('pricerange::priceranges.title.priceranges'), function (Item $item) {
                $item->icon('fa fa-suitcase');
                $item->weight(10);
                // $item->append('admin.pricerange.pricerange.create');
                $item->route('admin.pricerange.pricerange.index');
                $item->authorize(
                     $this->auth->hasAccess('pricerange.priceranges.index')
                );
// append

            });
        });

        return $menu;
    }
}
