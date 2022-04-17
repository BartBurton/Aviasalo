<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_transaction', function (Blueprint $table) {
            $table->id()->autoIncrement();

            $table->string('transaction_date');

            $table->boolean('is_closed');
            $table->string('transaction_token');

            $table->string('name');
            $table->string('surname');
            $table->string('dob');
            $table->string('doc');
            $table->string('email');

            $table->string('cost');
            $table->string('departure');
            $table->string('arrival');
            $table->string('departure_date');
            $table->string('arrival_date');
            $table->string('aircompany');
            
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_transaction');
    }
}
