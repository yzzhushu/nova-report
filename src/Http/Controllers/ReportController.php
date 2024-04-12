<?php

namespace Jshxl\Report\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Inertia\ResponseFactory;
use Jshxl\Report\Models\JshxlReport;

class ReportController
{
    /**
     * 支持的字段结构
     *
     * @var array
     * */
    protected array $structure = [
        '{start_date}' => '开始日期',
        '{final_date}' => '结束日期',
        '{start_time}' => '开始时间',
        '{final_time}' => '结束时间',
        '{department}' => '业务门店',
        '{categories}' => '商品分类',
        '{products}'   => '商品编码',
    ];

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

    /**
     * 获取报表结构
     * @param Request $request
     *
     * @return JsonResponse
     * */
    public function getStructure(Request $request): JsonResponse
    {
        $report = JshxlReport::where('uuid', $request->input('reportId'))->first();
        if (is_null($report)) return \response()->json(['error' => '报表不存在']);
        if ($report->status !== 1) return \response()->json(['error' => '该报表还未启用']);

        $struct = [];
        foreach ($this->structure as $key => $val) {
            if (!str_contains($report->sql, $key)) continue;
            $struct[] = [
                'key'   => $key,
                'value' => $val,
            ];
        }
        return \response()->json([
            'name' => $report->name,
            'fields' => [
                ['field' => 'id', 'header' => '编码'],
                ['field' => 'name', 'header' => '姓名'],
            ],
            'structures' => $struct,
        ]);
    }
}
