<?php namespace Ebussola\Statefull\Models;

use Model;

/**
 * GetParamBlacklist Model
 */
class GetParamBlacklist extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'ebussola_statefull_get_param_blacklists';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['*'];

    /**
     * @var array List of attribute names which are json encoded and decoded from the database.
     */
    protected $jsonable = ['values'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

}