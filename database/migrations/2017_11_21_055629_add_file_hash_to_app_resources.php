<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileHashToAppResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_resources', function (Blueprint $table) {
            $table->string('md5');
            $table->string('sha1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_resources', function (Blueprint $table) {
            $table->dropColumn('md5');
            $table->dropColumn('sha1');
        });
    }
}
