<?php

namespace Jshxl\Report\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Inertia\ResponseFactory;

class ReportController
{
    /**
     * Report tool index page.
     * @param Request $request
     * @param string $reportId
     *
     * @return Response|ResponseFactory
     */
    public function inertia(Request $request, string $reportId): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Report', ['reportId' => $reportId]);
    }

    /**
     * 获取人员清单
     * @param Request $request
     *
     * @return JsonResponse
     * */
    public function getUsers(Request $request): JsonResponse
    {
        $department = DB::table('user_departments')
            ->leftJoin('departments', 'departments.department_id', '=', 'user_departments.department_id')
            ->selectRaw('STRING_AGG(departments.department_name, \',\') AS departments')
            ->addSelect('user_departments.user_id')
            ->groupBy('user_departments.user_id');

        $users = User::query()
            ->leftJoin(DB::raw('(' . $department->toSql() . ') AS d'), 'd.user_id', '=', 'users.id')
            ->where('status', 1)
            ->selectRaw('users.id, users.name + \'—\' + d.departments AS name')
            ->orderBy('d.departments')
            ->get()->map(function ($user) {
                return [
                    'id'   => $user->id,
                    'name' => str_replace(' ', '', $user->name),
                ];
            })->toArray();
        return \response()->json(['resources' => $users]);
    }
}
