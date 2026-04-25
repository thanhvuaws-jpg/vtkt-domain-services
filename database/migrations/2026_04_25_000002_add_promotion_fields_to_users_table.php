<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'lucky_draw_played')) {
                $table->tinyInteger('lucky_draw_played')->default(0)->after('tien');
            }
            if (!Schema::hasColumn('users', 'registration_ip')) {
                $table->string('registration_ip')->nullable()->after('lucky_draw_played');
            }
            if (!Schema::hasColumn('users', 'device_fingerprint')) {
                $table->string('device_fingerprint')->nullable()->after('registration_ip');
            }
            if (!Schema::hasColumn('users', 'referrer_id')) {
                $table->unsignedBigInteger('referrer_id')->nullable()->after('device_fingerprint');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['lucky_draw_played', 'registration_ip', 'device_fingerprint', 'referrer_id']);
        });
    }
};
