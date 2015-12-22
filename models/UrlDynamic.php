<?php namespace Ebussola\Statefull\Models;

use Model;

/**
 * UrlDynamic Model
 */
class UrlDynamic extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'ebussola_statefull_url_dynamics';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['url', 'parameters_lists', 'use_internal_url', 'internal_url'];

    /**
     * @var array List of attribute names which are json encoded and decoded from the database.
     */
    protected $jsonable = ['parameters_lists'];

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