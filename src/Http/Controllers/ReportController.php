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
     * Report tool index page.
     * @param Request $request
     * @param string $reportId
     *
     * @return Response|ResponseFactory
     */
    public function inertia(Request $request, string $reportId): \Inertia\Response|\Inertia\ResponseFactory
    {
        $_token = $this->getToken();
        $report = JshxlReport::query()
            ->where('uuid', $reportId)
            ->whereJsonContains('auth_users', $request->user()->id)
            ->select('status', 'de_type', 'de_id', 'chart_id', 'report_name')
            ->first();
        if (is_null($report) || $report->status !== 1) abort(403);
        return inertia('Report', [
            'token'    => $_token,
            'report'   => $report->report_name,
            'deType'   => $report->de_type,
            'deId'     => $report->de_id,
            'chartId'  => strval($report->chart_id),
            'DEDomain' => config('jshxl_report.data_ease_domain'),
        ]);
    }

    /**
     * 获取人员清单
     * @param Request $request
     *
     * @return JsonResponse
     * */
    public function getUsers(Request $request): JsonResponse
    {
        $shops = DB::table('user_departments')
            ->leftJoin('departments', 'departments.department_id', '=', 'user_departments.department_id')
            ->selectRaw('STRING_AGG(departments.department_name, \',\') AS departments')
            ->addSelect('user_departments.user_id')
            ->groupBy('user_departments.user_id');
        $users = User::query()
            ->leftJoin(DB::raw('(' . $shops->toSql() . ') AS d'), 'd.user_id', '=', 'users.id')
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
     * 获取DataEase内嵌Token
     *
     * @return string
     * */
    private function getToken(): string
    {
        $_appid = config('jshxl_report.data_ease_id');
        $secret = config('jshxl_report.data_ease_secret');
        if (empty($_appid) || empty($secret))
            abort(412, 'DataEase App ID or Secret not set! Publish the config file and set the values!');

        $h = $this->base64url(json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256',
        ]));
        $p = $this->base64url(json_encode([
            'appId'   => $_appid,
            'account' => 'admin',
        ]));
        $s = $h . '.' . $p;
        return $s . '.' . $this->base64url(hash_hmac('sha256', $s, $secret, true));
    }

    /**
     * Base64 URL Encode
     * @param string $input
     *
     * @return string
     * */
    private function base64url(string $input): string
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }
}
