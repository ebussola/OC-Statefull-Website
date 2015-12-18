<?php namespace Ebussola\Statefull\Updates;

use Illuminate\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class AddColumnsUrlDynamicsTable extends Migration
{

    public function up()
    {
        Schema::table('ebussola_statefull_url_dynamics', function(Blueprint $table)
        {
            $table->boolean('use_internal_url');
            $table->string('internal_url');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ebussola_statefull_url_dynamics');
    }

}
