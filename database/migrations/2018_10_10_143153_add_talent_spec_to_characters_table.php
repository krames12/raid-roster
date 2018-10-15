<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTalentSpecToCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->string('talent_spec')->after('class');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('talent_spec');
        });
    }
}
