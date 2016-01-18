<?php namespace Ebussola\Statefull\Updates;

use Illuminate\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class CreateGetParamBlacklistsTable extends Migration
{

    public function up()
    {
        Schema::create('ebussola_statefull_get_param_blacklists', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->string('name')->unique();
            $table->text('values');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ebussola_statefull_get_param_blacklists');
    }

}
