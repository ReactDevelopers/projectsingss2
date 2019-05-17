<?php namespace Modules\Portfolio\Sidebar;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Contracts\Authentication;
use Modules\Portfolio\Repositories\PortfolioRequestRepository;
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
                $group->item(trans('Portfolios'), function (Item $item) {
                    $item->icon('fa fa-suitcase');
                    $item->weight(10);
                    // $item->append('admin.pricerange.pricerange.create');
                    // $item->route('admin.portfolio.portfolio.index');
                    // $item->authorize(
                    //      $this->auth->hasAccess('portfolio.portfolios.index')
                    // );
                    $item->item(trans('portfolio::portfolios.title.portfolios'), function (Item $item) {
                        $item->icon('fa fa-arrow-circle-o-right ');
                        $item->weight(0);
                        // $item->append('admin.category.category.create');
                        $item->route('admin.portfolio.portfolio.index');
                        $item->authorize(
                            $this->auth->hasAccess('portfolio.portfolios.index')
                        );
                    });
                    $item->item(trans('portfolio::portfoliorequest.title.portfolio request'), function (Item $item) {
                        $item->icon('fa fa-arrow-circle-o-right ');
                        $item->weight(1);
                        $item->badge(function (Badge $badge, PortfolioRequestRepository $portfolioRequest) {
                            $badge->setClass('bg-red');
                            $badge->setValue($portfolioRequest->countAllRequest());
                        });
                        // $item->append('admin.category.subcategory.create');
                        $item->route('admin.portfolio.portfoliorequest.index');
                        $item->authorize(
                            $this->auth->hasAccess('portfolio.portfoliorequest.index')
                        );
                    });

            });
        });

        return $menu;
    }
}
