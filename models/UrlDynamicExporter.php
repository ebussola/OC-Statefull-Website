<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12/18/15
 * Time: 11:10 AM
 */

namespace Ebussola\Statefull\Models;


use Backend\Models\ExportModel;

class UrlDynamicExporter extends ExportModel
{

    /**
     * Called when data is being exported.
     * The return value should be an array in the format of:
     *
     *   [
     *       'db_name1' => 'Some attribute value',
     *       'db_name2' => 'Another attribute value'
     *   ],
     *   [...]
     *
     */
    public function exportData($columns, $sessionKey = null)
    {
        return UrlDynamic::all($columns)->toArray();
    }
}