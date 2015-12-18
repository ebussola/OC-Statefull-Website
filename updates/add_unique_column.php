<?php namespace Ebussola\Statefull\Updates;

use Illuminate\Database\Schema\Blueprint;
use Schema;
use October\Rain\Database\Updates\Migration;

class AddUniqueColumn extends Migration
{

    public function up()
    {
        Schema::table('ebussola_statefull_url_dynamics', function(Blueprint $table)
        {
            $table->dropUnique('ebussola_statefull_url_dynamics_url_unique');
            $table->unique(['url', 'internal_url']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ebussola_statefull_url_dynamics');
    }

}
