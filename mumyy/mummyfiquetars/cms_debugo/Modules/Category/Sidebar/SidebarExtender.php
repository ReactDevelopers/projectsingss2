<?php namespace Modules\Category\Sidebar;

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
            $group->item(trans('category::categories.title.categories'), function (Item $item) {
                $item->icon('fa fa-suitcase');
                $item->weight(1);
                $item->authorize(
                     /* append */
                );
                $item->item(trans('category::categories.title.categories'), function (Item $item) {
                    $item->icon('fa fa-arrow-circle-o-right ');
                    $item->weight(0);
                    // $item->append('admin.category.category.create');
                    $item->route('admin.category.category.index');
                    $item->authorize(
                        $this->auth->hasAccess('category.categories.index')
                    );
                });
                $item->item(trans('category::subcategories.title.subcategories'), function (Item $item) {
                    $item->icon('fa fa-arrow-circle-o-right ');
                    $item->weight(1);
                    // $item->append('admin.category.subcategory.create');
                    $item->route('admin.category.subcategory.index');
                    $item->authorize(
                        $this->auth->hasAccess('category.subcategories.index')
                    );
                });
// append


            });
        });

        return $menu;
    }
}
