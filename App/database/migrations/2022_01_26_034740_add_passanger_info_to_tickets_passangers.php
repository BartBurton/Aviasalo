<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPassangerInfoToTicketsPassangers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets_passangers', function (Blueprint $table) {
            $table->string('name');
            $table->string('surname');
            $table->string('dob');
            $table->string('doc');
            $table->string('email');
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
            $table->dropColumn('name');    
            $table->dropColumn('surname');    
            $table->dropColumn('dob');    
            $table->dropColumn('doc');    
            $table->dropColumn('email');    
        });
    }
}
