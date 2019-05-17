<?php namespace App\Mummy\Api\V1\Service;

class HomeService
{
    public function __construct()
    {

    }

    /**
     * [getSearchNameArray return array name]
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public function getSearchNameArray($str){
        // remove more white space
        $str = preg_replace('/\s+/', ' ', $str);

        $arr = explode(" ", $str);

        return array_unique($arr);
    }

    public function getItems($option = 'list', $page = 1, $limit = 5){

        $offset = ($page - 1) * $limit;
        $query = "SELECT u.*, vp.rating_points, 
                COALESCE(   
                    (SELECT COUNT(*) 
                    FROM mm__user_activities ua  
                    JOIN mm__vendors_portfolios vpo1 ON vpo1.id = ua.portfolio_id AND vpo1.is_deleted IS NULL
                    WHERE ua.vendor_id = u.id AND ua.activity = 5
                    GROUP BY ua.vendor_id), 0
                ) AS likes,
                CASE
                    WHEN u.plan_id  = 3 THEN 'Silver'
                    WHEN u.plan_id  = 4 THEN 'Gold'
                    ELSE 'Free'
                END AS plan_name
                FROM (SELECT u2.*, ps.plan_id 
                        FROM users u2  
                        JOIN mm__plan_subscriptions ps ON ps.user_id = u2.id
                        WHERE ps.canceled_at IS NULL AND u2.is_deleted IS NULL AND u2.status = 1
                        ORDER BY u2.id ASC, ps.id DESC
                ) AS u 
                JOIN role_users r ON r.user_id = u.id AND r.role_id = 3 
                JOIN mm__vendors_profile vp ON vp.user_id = u.id
                JOIN mm__vendors_portfolios vpo ON vpo.vendor_id = u.id AND vpo.status = 1 AND vpo.is_deleted IS NULL
                JOIN mm__vendors_category vc ON vc.user_id = u.id AND vc.is_deleted IS NULL AND vc.status = 1
                GROUP BY u.id
                ORDER BY vp.rating_points DESC, likes DESC, u.plan_id DESC";

        if($option == 'list'){
             $query .= " LIMIT $limit OFFSET $offset";
             return \DB::select($query);
        }
        
        return count(\DB::select($query));
    }

}