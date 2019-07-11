<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Params;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/list",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="domain",
     *         in="query",
     *         required=true,
     *         description="登入站別(必填), default站別為0",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="pid",
     *         in="query",
     *         description="上層id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="group_id",
     *         in="query",
     *         description="群組id 預設1",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="帳號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="名稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="帳號狀態: 0: 鎖定, 1: 開啟",
     *         @OA\Schema(type="string", enum={0, 1})
     *     ),
     *     @OA\Parameter(
     *         name="last_ip",
     *         in="query",
     *         description="最後登入IP",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="field[]",
     *         in="query",
     *         description="所需欄位名稱, 如需group, 必要group_id (沒輸入為'*')",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort[]",
     *         in="query",
     *         description="排序欄位",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="order[]",
     *         in="query",
     *         description="可設定asc或desc",
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="頁數, 預設 1",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="max_results",
     *         in="query",
     *         description="回傳筆數, 預設20",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="success",
     *         @OA\JsonContent(
     *             example=
     *             {
     *                 "status": "success",
     *                 "data": {{"id":13,"domain":0,"pid":0,"email":"1@gmai.com","password":"examplePassword","username":"bethel49","name":"Mr. Ali Hand IV","status":1,"last_login":null,"last_ip":"127.0.0.1","created_at":"2019-06-19 15:16:40","updated_at":"2019-06-19 15:16:40","group":{"id":1,"name":"test","permission":null,"status":"1"}}},
     *                 "pagination": {"last_page":1,"total":1},
     *             }
     *         )
     *     )
     * )
     */
    public function list(Request $request)
    {
        $query = $request->input();

        $domain = $query['domain'] ?? 0;
        $pid = $query['pid'] ?? 0;
        $groupId = $query['group_id'] ?? '';
        $username = $query['username'] ?? '';
        $email = $query['email'] ?? '';
        $name = $query['name'] ?? '';
        $status = $query['status'] ?? '';
        $lastIp = $query['last_ip'] ?? '';
        $sort = $query['sort'] ?? [];
        $order = $query['order'] ?? [];
        $page = $query['page'] ?? 1;
        $field = $query['field'] ?? '*';
        $maxResults = $query['max_results'] ?? 20;
        $criteria = [];

        if (!empty($domain)) {
            $criteria['domain'] = $domain;
        }

        if (!empty($pid)) {
            $criteria['pid'] = $pid;
        }

        if (!empty($groupId)) {
            $criteria['group_id'] = $groupId;
        }

        if (!empty($email)) {
            $criteria['email'] = $email;
        }

        if (!empty($username)) {
            $criteria['username'] = $username;
        }

        if (!empty($name)) {
            $criteria['name'] = $name;
        }

        if (!empty($status)) {
            $criteria['status'] = $status;
        }

        if (!empty($lastIp)) {
            $criteria['lastIp'] = $lastIp;
        }

        if (!is_array($sort)) {
            $sort = [$sort];
        }

        if (!is_array($order)) {
            $order = [$order];
        }

        // default select user
        $user = User::where($criteria);

        if ($field == '*') {
            $user = $user->with(['group']);
        }

        // field檢查
        if ($field != '*') {
            if (!is_array($field)) {
                $field = [$field];
            }

            if (in_array('group_id', $field)) {
                $user = $user->with(['group']);
            }

            $field = Params::checkField(new User, $field);
        }


        // 整理order
        $multiOrders = Params::getMultiOrders($sort, $order);

        foreach ($multiOrders as $key => $sequence) {
            $user = $user->orderBy($key, $sequence);
        }

        $user = $user->select($field)
            ->paginate($maxResults)
            ->toArray();

        $pagination = [
            'last_page' => $user['last_page'],
            'total' => $user['total']
        ];

        return new JsonResponse([
            'result' => 'success',
            'data' => $user['data'],
            'pagination' => $pagination
        ]);
    }

    /**
     * @OA\Get(
     *     path="/user/info/{id}",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="user info",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="success",
     *         @OA\JsonContent(
     *             example=
     *             {
     *                 "status": "success",
     *                 "data": {"id":13,"domain":0,"pid":0,"email":"1@gmai.com","password":"examplePassword","username":"bethel49","name":"Mr. Ali Hand IV","status":1,"last_login":null,"last_ip":"127.0.0.1","created_at":"2019-06-19 15:16:40","updated_at":"2019-06-19 15:16:40","group":{"id":1,"name":"test","permission":null,"status":"1"}}
     *             }
     *         )
     *     )
     * )
     */
    public function info(Request $request)
    {
        $query = $request->input();

        $id = $request->id;

        // 必備
        if ($id == '') {
            return errorResponse('missing id');
        }

        $user = User::where('id', $id);
        $user = $user->with('group')->first();

        return new JsonResponse([
            'result' => 'success',
            'data' => $user
        ]);
    }

    /**
     * @OA\Post(
     *     path="/user/add",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="domain",
     *         in="query",
     *         required=true,
     *         description="登入站別(必填), default站別為0",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="pid",
     *         in="query",
     *         description="上層id, 預設0",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="group_id",
     *         in="query",
     *         description="群組id, 預設1",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         required=true,
     *         description="帳號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         required=true,
     *         description="密碼",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         description="email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="名稱",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="帳號狀態: 0: 鎖定, 1: 開啟",
     *         @OA\Schema(type="string", enum={0, 1})
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="success",
     *         @OA\JsonContent(
     *             example=
     *             {
     *                 "status":"success",
     *                 "msg":"",
     *             }
     *         )
     *     )
     * )
     */
    public function add(Request $request)
    {
        $query = $request->input();

        $domain = $query['domain'] ?? 0;
        $pid = $query['pid'] ?? 0;
        $groupId = $query['group_id'] ?? 1;
        $username = $query['username'] ?? '';
        $password = $query['password'] ?? '';
        $email = $query['email'] ?? '';
        $name = $query['name'] ?? '';
        $status = $query['status'] ?? 1;
        $lastIp = $query['last_ip'] ?? '';
        $addData = [];

        if (!empty($domain)) {
            $addData['domain'] = $domain;
        }

        if (!empty($pid)) {
            $addData['pid'] = $pid;
        }

        if (!empty($groupId)) {
            $addData['group_id'] = $groupId;
        }

        if (!empty($status)) {
            $addData['status'] = $status;
        }

        if (!empty($lastIp)) {
            $addData['lastIp'] = $lastIp;
        }

        // 必填
        if (empty($username)) {
            return errorResponse('missing username');
        }

        if (empty($name)) {
            return errorResponse('missing name');
        }

        $addData['name'] = $name;


        if (empty($email)) {
            return errorResponse('missing email');
        }

        if (empty($password)) {
            return errorResponse('missing password');
        }

        $addData['password'] = app('hash')->make($password);

        // 檢查username 是否重複
        $usernameIsExisted = User::where('username', $username)->exists();

        if ($usernameIsExisted) {
            return errorResponse('username is existed');
        }

        $addData['username'] = $username;

        // 檢查email 是否重複
        $emailIsExisted = User::where('email', $email)->exists();

        if ($emailIsExisted) {
            return errorResponse('email is existed');
        }

        $addData['email'] = $email;
        
        try {
            User::create($addData);
        } catch (\Exception $e) {
            return errorResponse('database error', $e->getMessage());
        }

        return new JsonResponse([
            'result' => 'success',
            'msg' => '',
        ]);
    }

    /**
     * @OA\Put(
     *     path="/user/edit",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         required=true,
     *         in="query",
     *         description="user id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="domain",
     *         in="query",
     *         description="登入站別",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="pid",
     *         in="query",
     *         description="上層id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="group_id",
     *         in="query",
     *         description="群組id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="帳號",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="密碼",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="名稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="帳號狀態: 0: 鎖定, 1: 開啟",
     *         @OA\Schema(type="string", enum={0, 1})
     *     ),
     *     @OA\Parameter(
     *         name="last_login",
     *         in="query",
     *         description="最後登入時間 yyyy-mm-dd HH:ii:ss",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="last_ip",
     *         in="query",
     *         description="最後登入ip",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="success",
     *         @OA\JsonContent(
     *             example=
     *             {
     *                 "status":"success",
     *                 "msg":"",
     *             }
     *         )
     *     )
     * )
     */
    public function edit(Request $request)
    {
        $query = $request->input();

        $id = $query['id'] ?? '';
        $domain = $query['domain'] ?? '';
        $pid = $query['pid'] ?? '';
        $groupId = $query['group_id'] ?? '';
        $username = $query['username'] ?? '';
        $password = $query['password'] ?? '';
        $name = $query['name'] ?? '';
        $email = $query['email'] ?? '';
        $status = $query['status'] ?? '';
        $lastLogin = $query['last_login'] ?? '';
        $lastIp = $query['last_ip'] ?? '';
        $updateData = [];

        if (empty($id)) {
            return errorResponse('id does not exist');
        }

        if (!empty($domain)) {
            $updateData['domain'] = $domain;
        }

        if (!empty($pid)) {
            $updateData['pid'] = $pid;
        }

        if (!empty($groupId)) {
            $updateData['group_id'] = $groupId;
        }

        if (!empty($name)) {
            $updateData['name'] = $name;
        }

        if (!empty($password)) {
            $updateData['password'] = app('hash')->make($password);
        }

        if (!empty($status)) {
            $updateData['status'] = $status;
        }

        if (!empty($lastIp)) {
            $updateData['last_ip'] = $lastIp;
        }

        if (!empty($lastLogin)) {
            $updateData['last_login'] = $lastLogin;
        }

        // 檢查user 是否存在
        $user = User::where('id', $id)->first();

        if (is_null($user)) {
            return errorResponse('user does not exist');
        }

        if (!empty($username)) {
            // 檢查username 是否重複
            $usernameIsExisted = User::where('username', $username)
                ->where('id', '!=', $id)
                ->exists();

            if ($usernameIsExisted) {
                return errorResponse('username is existed');
            }

            $updateData['username'] = $username;
        }

        if (!empty($email)) {
            // 檢查email 是否重複
            $emailIsExisted = User::where('email', $email)
                ->where('id', '!=', $id)
                ->exists();

            if ($emailIsExisted) {
                return errorResponse('email is existed');
            }

            $updateData['username'] = $username;
        }

        try {
            User::where('id', $id)->update($updateData);
        } catch (\Exception $e) {
            return errorResponse('database error', $e->getMessage());
        }

        return new JsonResponse([
            'result' => 'success',
            'msg' => '',
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/user/delete",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="id",
     *         required=true,
     *         in="query",
     *         description="user id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="success",
     *         @OA\JsonContent(
     *             example=
     *             {
     *                 "status":"success",
     *                 "msg":"",
     *             }
     *         )
     *     )
     * )
     */
    public function delete(Request $request)
    {
        $query = $request->input();

        $id = $query['id'] ?? '';

        if (empty($id)) {
            return errorResponse('id does not exist');
        }

        // 檢查user 是否存在
        $user = User::where('id', $id)->first();

        if (is_null($user)) {
            return errorResponse('user does not exist');
        }

        try {
            User::where('id', $id)->delete();
        } catch (\Exception $e) {
            return errorResponse('database error', $e->getMessage());
        }

        return new JsonResponse([
            'result' => 'success',
            'msg' => '',
        ]);
    }
}
