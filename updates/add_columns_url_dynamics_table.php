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
            if (!in_array("use_internal_url", Schema::getColumnListing($table->getTable()))) {
                $table->boolean('use_internal_url');
            }

            if (!in_array("internal_url", Schema::getColumnListing($table->getTable()))) {
                $table->string('internal_url');
            }
        });
    }

    public function down()
    {
        Schema::dropIfExists('ebussola_statefull_url_dynamics');
    }

}
