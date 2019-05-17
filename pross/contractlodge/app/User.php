<?php

namespace App;

use Illuminate\Http\Request;
use Laravel\Spark\User as SparkUser;
// use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends SparkUser // implements MustVerifyEmail
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'notify_of_hotel_payment_schedule',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'country_code',
        'phone',
        'two_factor_reset_code',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'uses_two_factor_auth' => 'boolean',
        'notify_of_hotel_payment_schedule' => 'boolean',
    ];

    /**
     * Update the notification options the user chose
     * @param  Request  $request
     * @param  integer  $user_id
     * @return [type]           [description]
     */
    public static function updateNotificationOptions(Request $request, $user_id)
    {
        $user = User::find($user_id);
        $notify = ! empty($request->input('notify_of_hotel_payment_schedule')) ? true : false;

        if (empty($user)) {
            return false;
        }

        return $user->forceFill([
            'notify_of_hotel_payment_schedule' => $notify
        ])->save();
    }
}
