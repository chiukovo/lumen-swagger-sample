<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
	/**
	 * 群組鎖定
	 */
	const STATUS_LOCKED = 0;

	/**
	 * 群組啟用
	 */
	const STATUS_ENABLE = 1;

	/**
	 * table name
	 */
    protected $table = 'group';

    /**
     * 自動更新created_at, updated_at
     */
    public $timestamps = true;

    /**
     * 黑名單
     */
    protected $guarded = [];

    /**
     * all field
     */
    public function allField()
    {
    	return [
    		'id',
    		'domain',
    		'name',
    		'permission',
    		'status',
    		'created_at',
    		'updated_at',
    	];
    }
}
