<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Models\User;

class UserTest extends TestCase
{

    /**
     * 每次執行都先migration refresh
     */
	use DatabaseMigrations;

    /**
     * init
     */
	public function setUp() :void
	{
	   parent::setUp();

       // 假資料
	   app(DatabaseSeeder::class)->call(UserTableSeeder::class);
	}

    /**
     * 測試不傳入參數取得資料
     */
    public function testGetList()
    {
        $response = $this->call('GET', '/user/list');
        $original = $response->original;

        $this->assertEquals('success', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試不傳入指定field
     */
    public function testGetCustomField()
    {
        $response = $this->call('GET', '/user/list', [
            'field' => ['id', 'group_id']
        ]);

        $original = $response->original;

        $this->assertArrayHasKey('id', $original['data'][0]);
        $this->assertArrayHasKey('group_id', $original['data'][0]);
        $this->assertArrayHasKey('group', $original['data'][0]);
        $this->assertEquals('success', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試新增未傳入username
     */
    public function testAddNoUsername()
    {
        $response = $this->call('POST', '/user/add', [
            'name' => 'test'
        ]);

        $original = $response->original;

        $this->assertEquals('missing username', $original['msg']);
        $this->assertEquals('error', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試新增未傳入name
     */
    public function testAddNoName()
    {
        $response = $this->call('POST', '/user/add', [
            'username' => 'test'
        ]);

        $original = $response->original;

        $this->assertEquals('missing name', $original['msg']);
        $this->assertEquals('error', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試新增未傳入password
     */
    public function testAddNoPassword()
    {
        $response = $this->call('POST', '/user/add', [
            'username' => 'test',
            'name' => 'test',
            'email' => '1@gmail.com',
        ]);

        $original = $response->original;

        $this->assertEquals('missing password', $original['msg']);
        $this->assertEquals('error', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試新增重複username
     */
    public function testAddSameUsername()
    {
        $response = $this->call('POST', '/user/add', [
            'username' => 'test',
            'name' => 'name',
            'email' => '1@gmail.com',
            'password' => 'password',
        ]);

        $original = $response->original;

        $this->assertEquals('', $original['msg']);
        $this->assertEquals('success', $original['result']);
        $this->assertEquals(200, $response->status());

        // 相同username
        $response = $this->call('POST', '/user/add', [
            'username' => 'test',
            'name' => 'name1',
            'email' => '1@gmail.com',
            'password' => 'password',
        ]);

        $original = $response->original;

        $this->assertEquals('username is existed', $original['msg']);
        $this->assertEquals('error', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試新增重複email
     */
    public function testAddSameEmail()
    {
        $response = $this->call('POST', '/user/add', [
            'username' => 'test',
            'name' => 'name',
            'password' => '123',
            'email' => '1@gmail.com',
        ]);

        $original = $response->original;

        $this->assertEquals('', $original['msg']);
        $this->assertEquals('success', $original['result']);
        $this->assertEquals(200, $response->status());

        // 相同email
        $response = $this->call('POST', '/user/add', [
            'username' => 'test1',
            'name' => 'name1',
            'password' => '123',
            'email' => '1@gmail.com',
        ]);

        $original = $response->original;

        $this->assertEquals('email is existed', $original['msg']);
        $this->assertEquals('error', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試新增成功
     */
    public function testAddSuccess()
    {
        $response = $this->call('POST', '/user/add', [
            'username' => 'test1',
            'name' => 'name1',
            'password' => '123',
            'email' => '1@gmail.com',
        ]);

        $original = $response->original;

        $this->assertEquals('', $original['msg']);
        $this->assertEquals('success', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試編輯no id
     */
    public function testEditNoid()
    {
        $response = $this->call('PUT', '/user/edit', [
            'username' => 'test1',
            'name' => 'name1',
        ]);

        $original = $response->original;

        $this->assertEquals('id does not exist', $original['msg']);
        $this->assertEquals('error', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試編輯user id 不存在
     */
    public function testEditNoUser()
    {
        $response = $this->call('PUT', '/user/edit', [
            'id' => '100',
            'name' => 'name1',
        ]);

        $original = $response->original;

        $this->assertEquals('user does not exist', $original['msg']);
        $this->assertEquals('error', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試編輯username已存在
     */
    public function testEditSameUsername()
    {
        $response = $this->call('GET', '/user/list');
        $original = $response->original;

        $response = $this->call('PUT', '/user/edit', [
            'id' => $original['data'][0]['id'],
            'username' => $original['data'][1]['username'],
        ]);

        $original = $response->original;

        $this->assertEquals('username is existed', $original['msg']);
        $this->assertEquals('error', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試編輯email已存在
     */
    public function testEditSameEmail()
    {
        $response = $this->call('GET', '/user/list');
        $original = $response->original;

        $response = $this->call('PUT', '/user/edit', [
            'id' => $original['data'][0]['id'],
            'email' => $original['data'][1]['email'],
        ]);

        $original = $response->original;

        $this->assertEquals('email is existed', $original['msg']);
        $this->assertEquals('error', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試編輯成功
     */
    public function testEditSuccess()
    {
        $response = $this->call('GET', '/user/list');
        $original = $response->original;

        $response = $this->call('PUT', '/user/edit', [
            'id' => '1',
            'username' => 'test',
            'name' => 'test',
            'status' => 1,
            'last_login' => '2019-06-25 15:30:30',
            'last_ip' => '127.0.0.1',
        ]);

        $original = $response->original;

        $this->assertEquals('', $original['msg']);
        $this->assertEquals('success', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試取得user info null
     */
    public function testUserInfoSuccessNull()
    {
        $response = $this->call('GET', '/user/info/0');
        $original = $response->original;

        $this->assertEquals(null, $original['data']);
        $this->assertEquals('success', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試取得user info
     */
    public function testUserInfoSuccess()
    {
        $response = $this->call('GET', '/user/info/1');
        $original = $response->original;

        $this->assertEquals('success', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試刪除無此user
     */
    public function testDeleteNoUser()
    {
        $response = $this->call('DELETE', '/user/delete', [
            'id' => 100
        ]);

        $original = $response->original;

        $this->assertEquals('user does not exist', $original['msg']);
        $this->assertEquals('error', $original['result']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * 測試刪除成功
     */
    public function testDeleteSuccess()
    {
        $response = $this->call('DELETE', '/user/delete', [
            'id' => 1
        ]);

        $original = $response->original;

        $this->assertEquals('', $original['msg']);
        $this->assertEquals('success', $original['result']);
        $this->assertEquals(200, $response->status());
    }
}
