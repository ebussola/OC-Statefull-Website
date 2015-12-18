<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12/18/15
 * Time: 11:10 AM
 */

namespace Ebussola\Statefull\Models;


use Backend\Models\ImportModel;

class UrlDynamicImporter extends ImportModel
{

    public $rules = [];

    /**
     * Called when data is being imported.
     * The $results array should be in the format of:
     *
     *    [
     *        'db_name1' => 'Some value',
     *        'db_name2' => 'Another value'
     *    ],
     *    [...]
     *
     */
    public function importData($results, $sessionKey = null)
    {
        foreach ($results as $row => $data) {
            try {
                UrlDynamic::create($data);
                $this->logCreated();
            }
            catch (\Exception $e) {
                $this->logError($row, $e->getMessage());
            }
        }
    }

}