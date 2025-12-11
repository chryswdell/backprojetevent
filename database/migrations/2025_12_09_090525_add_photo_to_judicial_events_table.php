<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('judicial_events', function (Blueprint $table) {
            // chemin du fichier dans storage (ex: "judicial_photos/xxx.jpg")
            $table->string('photo_path')->nullable()->after('observation');
        });
    }

    public function down(): void
    {
        Schema::table('judicial_events', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};
