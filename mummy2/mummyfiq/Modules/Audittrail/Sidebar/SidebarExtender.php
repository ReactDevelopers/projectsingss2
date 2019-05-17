<?php namespace Modules\Audittrail\Sidebar;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
// use Modules\User\Contracts\Authentication;
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
//         $menu->group(trans('core::sidebar.content'), function (Group $group) {
//             $group->item('AuditTrail', function (Item $item) {
//                 $item->icon('fa fa-history');
//                 $item->weight(10);
//                 $item->authorize(
//                         $this->auth->hasAccess('audittrail.*')
//                 );
//                 $item->item(trans('audittrail::logs.title.logs'), function (Item $item) {
//                     $item->icon('fa fa-history');
//                     $item->weight(0);
// //                    $item->append('admin.audittrail.log.create');
//                     $item->route('admin.audittrail.log.index');
//                     $item->authorize(
//                         $this->auth->hasAccess('audittrail.logs.index')
//                     );
//                 });
// // append

//             });
//         });

        return $menu;
    }
}
