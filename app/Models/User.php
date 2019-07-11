<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model implements ModelInterface
{
	/**
	 * 帳號鎖定
	 */
	const STATUS_LOCKED = 0;

	/**
	 * 帳號啟用
	 */
	const STATUS_ENABLE = 1;

	/**
	 * table name
	 */
    protected $table = 'user';

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
    		'pid',
    		'group_id',
    		'username',
    		'password',
    		'name',
    		'email',
    		'status',
    		'last_login',
    		'last_ip',
    		'created_at',
    		'updated_at',
    	];
    }

    /**
     * 對應group
     */
    public function group()
    {
        return $this->hasOne('App\Models\Group', 'id', 'group_id')
        	->select(['id', 'name', 'permission', 'status']);
    }
}
