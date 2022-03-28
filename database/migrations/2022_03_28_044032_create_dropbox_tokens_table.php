<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDropboxTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dropbox_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('access_token');
            $table->string('token_type');
            $table->integer('expires_in');
            $table->string('refresh_token');
            $table->text('scope');
            $table->bigInteger('uid');
            $table->string('account_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dropbox_tokens');
    }
}
