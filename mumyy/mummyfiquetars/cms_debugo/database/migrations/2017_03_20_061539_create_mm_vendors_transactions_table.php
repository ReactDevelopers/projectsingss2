<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmVendorsTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        -- ----------------------------
        --  Table structure for `mm_vendors_transactions`
        -- ----------------------------
        DROP TABLE IF EXISTS `mm_vendors_transactions`;
        CREATE TABLE `mm_vendors_transactions` (
          `id` int(11) DEFAULT NULL,
          `user_id` int(11) DEFAULT NULL,
          `package_id` int(11) DEFAULT NULL,
          `package_name` varchar(255) DEFAULT NULL,
          `price` decimal(10,0) DEFAULT NULL,
          `start_date` timestamp NULL DEFAULT NULL,
          `end_date` timestamp NULL DEFAULT NULL,
          `total_price` decimal(10,0) DEFAULT NULL,
          `transaction_message` varchar(255) DEFAULT NULL,
          `transaction_status` varchar(255) DEFAULT NULL,
          `transaction_code` varchar(255) DEFAULT NULL,
          `noted` varchar(255) DEFAULT NULL,
          `phone` varchar(255) DEFAULT NULL,
          `address` varchar(255) DEFAULT NULL,
          `email` varchar(255) DEFAULT NULL,
          `postal_code` varchar(255) DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `payment_type` enum('Credit Card','Bank Transfer','Paypal') DEFAULT NULL,
          `auth_code` varchar(255) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
       Schema::create('mm_vendors_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('package_id')->unsigned()->nullable();
            $table->string('package_name')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('transaction_message')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('transaction_code')->nullable();
            $table->string('noted')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('postal_code')->nullable();
            $table->nullableTimestamps();
            $table->enum('payment_type', ['Credit Card','Bank Transfer','Paypal'])->nullable();
            $table->string('auth_code')->nullable();

            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mm_vendors_transactions');
    }
}
