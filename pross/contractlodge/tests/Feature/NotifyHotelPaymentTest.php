<?php

namespace Tests\Feature;

use App\Bill;
use App\User;
use App\Payment;
use App\RaceHotel;
use Tests\TestCase;
use Illuminate\Support\Facades\Bus;
use App\Jobs\Users\NotifyHotelPayment as NotifyHotelPaymentJob;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotifyHotelPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_profile_checkbox_exists()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/settings');

        $this->assertAuthenticated();

        $response->assertStatus(200)
                 ->assertSessionHasNoErrors()
                 ->assertSee('name="notify_of_hotel_payment_schedule"');
    }

    public function test_user_profile_checkbox_updates()
    {
        $user = factory(User::class)->create([
            'notify_of_hotel_payment_schedule' => false,
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => $user->email,
            'notify_of_hotel_payment_schedule' => true
        ]);

        $response = $this->actingAs($user)->json('PUT', '/settings/notification', [
            'notify_of_hotel_payment_schedule' => true
        ]);

        $response->assertStatus(200)
                 ->assertSessionHasNoErrors()
                 ->assertSee('true');

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'notify_of_hotel_payment_schedule' => true
        ]);
    }

    public function test_artisan_command_does_not_send_to_users_who_dont_want_to_be_notified()
    {
        // Mock the NotifyHotelPayment job by using Bus::fake()
        Bus::fake();

        $user = factory(User::class)->create([
            'notify_of_hotel_payment_schedule' => false,
        ]);

        // Setup a hotel payment with a due date which should trigger
        // a notification if the user wanted to be notified

        factory(Bill::class)->create()->each(function ($bill) {
            $bill->payments()->save(factory(Payment::class)->make([
                    'due_on' => date('Y-m-d', strtotime('+7 days')),
                    'paid_on' => null,
                    'amount_paid' => 0,
                ]
            ));
        });

        $this->artisan('notify:hotel-payment')
             ->assertExitCode(0);

        Bus::assertNotDispatched(NotifyHotelPaymentJob::class);
    }

    public function test_artisan_command_sends_to_users_who_want_to_be_notified()
    {
        // Mock the NotifyHotelPayment job by using Bus::fake()
        Bus::fake();

        $user = factory(User::class)->create([
            'notify_of_hotel_payment_schedule' => true,
        ]);

        // Setup a hotel payment with a due date which should trigger
        // a notification if the user wanted to be notified

        factory(Bill::class)->create()->each(function ($bill) {
            $bill->payments()->save(factory(Payment::class)->make([
                    'due_on' => date('Y-m-d', strtotime('+7 days')),
                    'paid_on' => null,
                    'amount_paid' => 0,
                ]
            ));
        });

        $bill = Bill::find(1);

        $this->artisan('notify:hotel-payment')
             ->assertExitCode(0);

        Bus::assertDispatched(NotifyHotelPaymentJob::class, function ($job) use ($bill, $user) {
            if (
                $job->users[0]->id === $user->id
                && $job->bills[0]->payments[0]->id === $bill->payments[0]->id
            ) {
                return true;
            }

            return false;
        });
    }
}
