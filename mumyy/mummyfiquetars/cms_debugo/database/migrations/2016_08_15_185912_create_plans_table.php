<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mm__plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 7, 2)->default('0.00');
            $table->string('interval')->default('month');
            $table->smallInteger('interval_count')->default(1);
            $table->smallInteger('trial_period_days')->nullable();
            $table->smallInteger('sort_order')->nullable();
            $table->timestamps();
        });

        // add records
        \DB::table('mm__plans')->insert([
            [
                'id'                => '2', 
                'name'              => 'Free',
                'description'       => 'Free plan package',
                'price'             => '0.00',
                'interval'          => 'month',
                'interval_count'    => '12',
                'trial_period_days' => '0',
                'sort_order'        => '1',
                'created_at'        => Carbon\Carbon::now(),
                'updated_at'        => Carbon\Carbon::now()
            ],
            [
                'id'                => '3', 
                'name'              => 'Sliver',
                'description'       => 'Sliver plan package',
                'price'             => '30.00',
                'interval'          => 'month',
                'interval_count'    => '1',
                'trial_period_days' => '0',
                'sort_order'        => '2',
                'created_at'        => Carbon\Carbon::now(),
                'updated_at'        => Carbon\Carbon::now()
            ],
            [
                'id'                => '4', 
                'name'              => 'Gold',
                'description'       => 'Gold plan package',
                'price'             => '59.00',
                'interval'          => 'month',
                'interval_count'    => '1',
                'trial_period_days' => '0',
                'sort_order'        => '3',
                'created_at'        => Carbon\Carbon::now(),
                'updated_at'        => Carbon\Carbon::now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mm__plans');
    }
}
