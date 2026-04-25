<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Xóa hoàn toàn 4 bảng rác khỏi Database
        Schema::dropIfExists('history');
        Schema::dropIfExists('hosting_historis');
        Schema::dropIfExists('vps_historis');
        Schema::dropIfExists('sourcode_historis');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Quá trình này không thể rollback vì ta không muốn dựng lại rác
        // Tuy nhiên có thể ghi log ở đây nếu cần.
    }
};
