<?php namespace Modules\Review\Sidebar;

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
            $group->item(trans('Reviews'), function (Item $item) {
                $item->icon('fa fa-suitcase');
                $item->weight(10);
                // $item->append('admin.pricerange.pricerange.create');

                $item->item(trans('comment::comments.title.reviews'), function (Item $item) {
                    $item->weight(0);
                    $item->icon('fa fa-arrow-circle-o-right');
                    $item->route('admin.comment.comment.index');
                    $item->authorize(
                        $this->auth->hasAccess('comment.comments.index')
                    );
                });

                $item->item(trans('review::reviews.title.request reviews'), function (Item $item) {
                    $item->weight(1);
                    $item->icon('fa fa-arrow-circle-o-right');
                    $item->route('admin.review.review.index');
                    $item->authorize(
                        $this->auth->hasAccess('review.reviews.index')
                    );
                });

            });
        });

        return $menu;
    }
}
