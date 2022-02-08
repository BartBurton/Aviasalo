<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropForeignKeysInTicketsPassangers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets_passangers', function (Blueprint $table) {
            $table->dropColumn(['ticket_id', 'passanger_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets_passangers', function (Blueprint $table) {
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('passanger_id')->constrained('passangers')->cascadeOnDelete();
        });
    }
}
