<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Helpers\Template;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // Length declaration 
            $exshort = Template::getDataLength("exshort_char");
            $short = Template::getDataLength("short_char");
            $med = Template::getDataLength("med_char");
            $exmed = Template::getDataLength("exmed_char");
            
            $table->uuid('id')->primary();
            $table->string('username', $short);
            $table->string('fullname', $med);
            $table->string('role', $exshort);
            $table->string('email', $exmed);
            $table->dateTime('email_verified_at', $precision = 0)->nullable();
            $table->string('password', $exmed);

            // Properties
            $table->dateTime('created_at', $precision = 0);
            $table->dateTime('updated_at', $precision = 0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
