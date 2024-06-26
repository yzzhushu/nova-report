<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jshxl_report', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 64)->unique();                           // 唯一标识

            $table->string('group_name', 64)->nullable();                   // 分组名称
            $table->string('report_name', 64);                              // 报表名称
            $table->integer('display_sort')->default(1);                    // 报表排序

            $table->string('de_type', 64)->default('dashboard');            // DataEase报表类型
            $table->string('de_id', 128)->nullable();                       // 仪表板/数据大屏ID
            $table->string('chart_id', 128)->nullable();                    // 视图ID

            $table->text('auth_users')->default('[]');                      // 授权用户
            $table->smallInteger('status')->default(1);                     // 报表状态：0、停用；1、启用

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jshxl_report');
    }
};
