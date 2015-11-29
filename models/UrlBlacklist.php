<?php namespace Ebussola\Statefull\Models;

use Model;

/**
 * UrlBlacklist Model
 */
class UrlBlacklist extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'ebussola_statefull_url_blacklists';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

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