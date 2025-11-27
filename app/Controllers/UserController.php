<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\User;
use App\Core\Container;

class UserController extends Controller
{

    // api phân trang 
    public function index(Request $request)
    {
        $page = (int) ($request->query('page') ?? 1);
        $limit = (int) ($request->query('limit') ?? 10);

        $filters = [];
        if ($search = $request->query('search')) {
            $filters['search'] = $search;
        }
        if ($role = $request->query('role')) {
            $filters['role'] = $role;
        }

        $result = User::paginate($page, $limit, $filters);

        // lấy cả thông tin số lượng khảo sát và phản hồi
        $db = Container::get('db');
        $users = array_map(function ($u) use ($db) {
            $uid = $u->getId();

            $stmt1 = $db->prepare('SELECT COUNT(*) as c FROM surveys WHERE maNguoiTao = :uid');
            $stmt1->execute([':uid' => $uid]);
            $surveys = (int) $stmt1->fetch()['c'];

            $stmt2 = $db->prepare('SELECT COUNT(*) as c FROM user_responses WHERE maNguoiDung = :uid');
            $stmt2->execute([':uid' => $uid]);
            $responses = (int) $stmt2->fetch()['c'];

            $arr = $u->toArray();
            return [
                'id' => $arr['id'],
                'code' => $arr['code'] ?? '',
                'name' => $arr['name'],
                'email' => $arr['email'],
                'phone' => $arr['phone'] ?? '',
                'gender' => $arr['gender'] ?? 'other',
                'role' => $arr['role'],
                'status' => 'active',
                'surveys' => $surveys,
                'responses' => $responses,
                'joinedAt' => $arr['created_at'],
                'avatar' => '',
            ];
        }, $result['users']);

        return $this->json([
            'error' => false,
            'data' => $users,
            'meta' => [
                'total' => $result['total'],
                'page' => $result['page'],
                'limit' => $result['limit'],
                'totalPages' => $result['totalPages'],
            ],
        ]);
    }
}
