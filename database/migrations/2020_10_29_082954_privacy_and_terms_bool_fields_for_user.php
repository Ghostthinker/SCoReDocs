<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PrivacyAndTermsBoolFieldsForUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('accepted_terms_of_usage')->default(false);
            $table->boolean('accepted_privacy_policy')->default(false);
            $table->boolean('uploaded_privacy_agreement')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('accepted_terms_of_usage');
            $table->dropColumn('accepted_privacy_policy');
            $table->dropColumn('uploaded_privacy_agreement');
        });
    }
}
