<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments( 'id');
            $table->longText(   'data'      )->default(null);
            $table->string(     'name'      )->length(1024);
            $table->string(    'parent'    )->length(8);
            $table->integer(    'permission')->unsigned()->default(1);
            $table->string(     'tag'       )->length(8)->unique();
            $table->string(     'type'      )->length(512);
            // $table->unique(['parent','name']);
            $table->timestamps();
        });

        Schema::table('files',function (Blueprint $table){
            $table->softDeletes();
            $table->foreign('parent')->references('tag')->on('folders');
            $table->foreign('permission')->references('id')->on('permissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
