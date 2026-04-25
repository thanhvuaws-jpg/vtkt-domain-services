<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Force drop exact table names visible on phpmyadmin
        Schema::dropIfExists('history');
        Schema::dropIfExists('hostinghistory');
        Schema::dropIfExists('vpshistory');
        Schema::dropIfExists('sourcecodehistory');
    }

    public function down()
    {
        // No rollback needed
    }
};
