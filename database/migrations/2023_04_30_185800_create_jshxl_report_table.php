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

            $table->string('uuid', 64)->nullable()->unique();               // 报表唯一标识
            $table->string('name', 64);
            $table->string('group_name', 64)->nullable();                   // 分组名称
            $table->integer('sort_no')->default(1);                         // 报表排序
            $table->text('sql')->nullable();

            $table->text('fields')->default('[]');                          // 报表字段

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
