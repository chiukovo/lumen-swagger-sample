<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Services\Params;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GroupController extends Controller
{
    /**
     * @OA\Get(
     *     path="/group/list",
     *     tags={"Group"},
     *     @OA\Parameter(
     *         name="domain",
     *         in="query",
     *         required=true,
     *         description="登入站別(必填), default站別為0",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="名稱",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="permission",
     *         in="query",
     *         description="最後登入IP",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="帳號狀態: 0: 鎖定, 1: 開啟",
     *         @OA\Schema(type="string", enum={0, 1})
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
     *                 "data": {{"id":13,"domain":0,"pid":0,"email":"1@gmai.com","password":"examplePassword","username":"bethel49","name":"Mr. Ali Hand IV","status":1,"last_login":null,"last_ip":"127.0.0.1","created_at":"2019-06-19 15:16:40","updated_at":"2019-06-19 15:16:40"}},
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
        $name = $query['name'] ?? '';
        $permission = $query['permission'] ?? '';
        $status = $query['status'] ?? '';
        $sort = $query['sort'] ?? [];
        $order = $query['order'] ?? [];
        $page = $query['page'] ?? 1;
        $field = $query['field'] ?? '*';
        $maxResults = $query['max_results'] ?? 20;
        $criteria = [];

        if (!empty($domain)) {
            $criteria['domain'] = $domain;
        }

        if (!empty($permission)) {
            $criteria['permission'] = $permission;
        }

        if (!empty($name)) {
            $criteria['name'] = $name;
        }

        if (!empty($status)) {
            $criteria['status'] = $status;
        }

        if (!is_array($sort)) {
            $sort = [$sort];
        }

        if (!is_array($order)) {
            $order = [$order];
        }

        // default select group
        $group = Group::where($criteria);

        // field檢查
        if ($field != '*') {
            if (!is_array($field)) {
                $field = [$field];
            }

            $field = Params::checkField(new Group, $field);
        }


        // 整理order
        $multiOrders = Params::getMultiOrders($sort, $order);

        foreach ($multiOrders as $key => $sequence) {
            $group = $group->orderBy($key, $sequence);
        }

        $group = $group->select($field)
            ->paginate($maxResults)
            ->toArray();

        $pagination = [
            'last_page' => $group['last_page'],
            'total' => $group['total']
        ];

        return new JsonResponse([
            'result' => 'success',
            'data' => $group['data'],
            'pagination' => $pagination
        ]);
    }

    /**
     * @OA\Get(
     *     path="/group/info/{id}",
     *     tags={"Group"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="group info",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="success",
     *         @OA\JsonContent(
     *             example=
     *             {
     *                 "status": "success",
     *                 "data": {"id":13,"domain":0,"pid":0,"email":"1@gmai.com","password":"examplePassword","username":"bethel49","name":"Mr. Ali Hand IV","status":1,"last_login":null,"last_ip":"127.0.0.1","created_at":"2019-06-19 15:16:40","updated_at":"2019-06-19 15:16:40"}
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

        $group = Group::where('id', $id)->first();

        return new JsonResponse([
            'result' => 'success',
            'data' => $group
        ]);
    }
}
