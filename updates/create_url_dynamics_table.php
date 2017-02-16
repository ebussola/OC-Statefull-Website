<?php namespace Ebussola\Statefull\Updates;

use Illuminate\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class CreateUrlDynamicsTable extends Migration
{

    public function up()
    {
        Schema::create('ebussola_statefull_url_dynamics', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->string('url')->unique();
            $table->longText('parameters_lists');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ebussola_statefull_url_dynamics');
    }

}
