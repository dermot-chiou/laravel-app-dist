<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBundleIdToMobileAppFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobile_app_files', function (Blueprint $table) {
            $table->string('bundle_id');
            $table->string('original_name');
            $table->boolean('tablet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_app_files', function (Blueprint $table) {
            $table->dropColumn('bundle_id');
            $table->dropColumn('original_name');
            $table->dropColumn('tablet');
        });
    }
}
