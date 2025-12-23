<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'ENUM pour ajouter les nouveaux types
        DB::statement("ALTER TABLE bars MODIFY COLUMN type_pdv ENUM('dakar', 'regions', 'chr', 'fanzone', 'fanzone_public', 'fanzone_hotel') DEFAULT 'dakar'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à l'ancien ENUM (attention: peut perdre des données)
        DB::statement("ALTER TABLE bars MODIFY COLUMN type_pdv ENUM('dakar', 'regions', 'chr', 'fanzone') DEFAULT 'dakar'");
    }
};
