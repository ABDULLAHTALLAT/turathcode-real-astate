<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * الصور بتتخزن جوه قاعدة البيانات (مش في storage):
 *  - media          : بيانات الصورة (ميتاداتا فقط) عشان الاستعلامات تفضل خفيفة
 *  - media_variants : النسخ المعالجة (thumb / medium / large) كـ WebP binary
 * كل صورة ليها ULID مش قابل للتخمين + بصمة sha256 لمنع التكرار.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->char('sha256', 64)->unique();
            $table->string('mime', 40)->default('image/webp');
            $table->unsignedSmallInteger('width');
            $table->unsignedSmallInteger('height');
            $table->unsignedInteger('bytes');
            $table->string('alt', 190)->nullable();
            $table->timestamps();
        });

        Schema::create('media_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('name', 12); // thumb | medium | large
            $table->unsignedSmallInteger('width');
            $table->unsignedSmallInteger('height');
            $table->binary('data');
            $table->unique(['media_id', 'name']);
        });

        // الـ BLOB العادي في MySQL حده 64KB، فنكبّره
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE media_variants MODIFY data MEDIUMBLOB NOT NULL');
        }

        Schema::table('property_images', function (Blueprint $table) {
            $table->foreignId('media_id')->nullable()->after('property_id')->constrained('media')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('avatar_media_id')->nullable()->constrained('media')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropConstrainedForeignId('avatar_media_id'));
        Schema::table('property_images', fn (Blueprint $t) => $t->dropConstrainedForeignId('media_id'));
        Schema::dropIfExists('media_variants');
        Schema::dropIfExists('media');
    }
};
