<?php namespace Modules\Report\Sidebar;

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
            $group->item(trans('report::reviews.title.report'), function (Item $item) {
                $item->icon('fa fa-suitcase');
                $item->weight(10);
                $item->authorize(
                     /* append */
                );
                $item->item(trans('report::reviews.title.reviews'), function (Item $item) {
                    $item->icon('fa fa-arrow-circle-o-right');
                    $item->weight(0);
                    $item->route('admin.report.review.index');
                    $item->authorize(
                        $this->auth->hasAccess('report.reviews.index')
                    );
                });
                $item->item(trans('report::comments.title.comments'), function (Item $item) {
                    $item->icon('fa fa-arrow-circle-o-right');
                    $item->weight(0);
                    $item->route('admin.report.comment.index');
                    $item->authorize(
                        $this->auth->hasAccess('report.comments.index')
                    );
                });
// append


            });
        });

        return $menu;
    }
}
