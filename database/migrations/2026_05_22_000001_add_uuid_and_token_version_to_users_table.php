<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // UUID público — exposto na API em vez do ID numérico (evita IDOR e enumeração)
            $table->uuid('uuid')->nullable()->unique()->after('id');

            // Versão do token — incrementada a cada troca/reset de senha para invalidar
            // todos os JWTs emitidos anteriormente, em qualquer dispositivo.
            $table->unsignedInteger('token_version')->default(1)->after('remember_token');
        });

        // Preenche o UUID de usuários já existentes no banco
        DB::table('users')->whereNull('uuid')->orderBy('id')->each(function (object $user) {
            DB::table('users')->where('id', $user->id)->update(['uuid' => (string) Str::uuid()]);
        });

        // Torna o UUID obrigatório após o backfill
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'token_version']);
        });
    }
};
