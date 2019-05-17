<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // User Related Events...
        'Laravel\Spark\Events\Subscription\UserSubscribed' => [
            'Laravel\Spark\Listeners\Subscription\UpdateActiveSubscription',
            'Laravel\Spark\Listeners\Subscription\UpdateTrialEndingDate',
        ],

        'Laravel\Spark\Events\Profile\ContactInformationUpdated' => [
            'Laravel\Spark\Listeners\Profile\UpdateContactInformationOnStripe',
        ],

        'Laravel\Spark\Events\PaymentMethod\VatIdUpdated' => [
            'Laravel\Spark\Listeners\Subscription\UpdateTaxPercentageOnStripe',
        ],

        'Laravel\Spark\Events\PaymentMethod\BillingAddressUpdated' => [
            'Laravel\Spark\Listeners\Subscription\UpdateTaxPercentageOnStripe',
        ],

        'Laravel\Spark\Events\Subscription\SubscriptionUpdated' => [
            'Laravel\Spark\Listeners\Subscription\UpdateActiveSubscription',
        ],

        'Laravel\Spark\Events\Subscription\SubscriptionCancelled' => [
            'Laravel\Spark\Listeners\Subscription\UpdateActiveSubscription',
        ],

        // Team Related Events...
        'Laravel\Spark\Events\Teams\TeamCreated' => [
            'Laravel\Spark\Listeners\Teams\UpdateOwnerSubscriptionQuantity',
        ],

        'Laravel\Spark\Events\Teams\TeamDeleted' => [
            'Laravel\Spark\Listeners\Teams\UpdateOwnerSubscriptionQuantity',
        ],

        'Laravel\Spark\Events\Teams\TeamMemberAdded' => [
            'Laravel\Spark\Listeners\Teams\UpdateTeamSubscriptionQuantity',
        ],

        'Laravel\Spark\Events\Teams\TeamMemberRemoved' => [
            'Laravel\Spark\Listeners\Teams\UpdateTeamSubscriptionQuantity',
        ],

        'Laravel\Spark\Events\Teams\Subscription\TeamSubscribed' => [
            'Laravel\Spark\Listeners\Teams\Subscription\UpdateActiveSubscription',
            'Laravel\Spark\Listeners\Teams\Subscription\UpdateTrialEndingDate',
        ],

        'Laravel\Spark\Events\Teams\Subscription\SubscriptionUpdated' => [
            'Laravel\Spark\Listeners\Teams\Subscription\UpdateActiveSubscription',
        ],

        'Laravel\Spark\Events\Teams\Subscription\SubscriptionCancelled' => [
            'Laravel\Spark\Listeners\Teams\Subscription\UpdateActiveSubscription',
        ],

        'Laravel\Spark\Events\Teams\UserInvitedToTeam' => [
            'Laravel\Spark\Listeners\Teams\CreateInvitationNotification',
        ],

        'App\Events\RoomingListDeleted' => [
            'App\Listeners\RoomingLists\DeleteRoomingListGuests',
        ],

        'App\Events\RoomingListPrepared' => [
            'App\Listeners\RoomingLists\RenumberRoomingListGuests',
        ],

        'App\Events\InventoryChanged' => [
            'App\Listeners\RoomingLists\PrepareForInventory',
        ],

        'App\Events\RoomsConfirmed' => [
            'App\Listeners\RoomingLists\AssignRoomsToClient',
        ],

        'App\Events\ClientAssigned' => [
            'App\Listeners\RoomingLists\RenumberAfterAssigningClient',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
