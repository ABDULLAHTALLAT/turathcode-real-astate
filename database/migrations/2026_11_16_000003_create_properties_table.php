<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('property_type', 30)->index();

            // العنوان بالتفصيل
            $table->string('governorate', 60)->index();
            $table->string('city', 80)->index();
            $table->string('district', 120)->nullable();
            $table->string('street')->nullable();
            $table->string('building_number', 30)->nullable();
            $table->string('floor_number', 30)->nullable();
            $table->string('unit_number', 30)->nullable();
            $table->string('landmark')->nullable();          // علامة مميزة
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // المواصفات
            $table->unsignedInteger('area_m2')->nullable();
            $table->unsignedTinyInteger('rooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->unsignedTinyInteger('floors_count')->nullable();   // للمباني الكاملة
            $table->string('finishing_stage', 30)->index();
            $table->text('finishing_notes')->nullable();
            $table->text('description')->nullable();
            $table->json('utilities')->nullable();           // كهرباء/مياه/غاز/مصعد...
            $table->json('allowed_purposes')->nullable();    // سكن عائلي/إداري/تجاري...
            $table->unsignedTinyInteger('vacant_since_years')->nullable();

            // الإيجار والمدة
            $table->unsignedInteger('market_rent')->nullable();      // سعر السوق (مثلاً 20 ألف)
            $table->unsignedInteger('offered_rent');                 // السعر المعروض (مثلاً 15 ألف)
            $table->unsignedTinyInteger('min_lease_years')->default(1);
            $table->unsignedTinyInteger('max_lease_years')->default(5);
            $table->unsignedTinyInteger('rent_free_months')->default(0);
            $table->unsignedTinyInteger('annual_increase_percent')->default(0);
            $table->string('finishing_by', 20)->default('tenant');   // tenant | owner | shared

            // الحالة والإدارة
            $table->string('status', 20)->default('pending')->index(); // pending|approved|rejected|rented|hidden
            $table->string('reject_reason')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views')->default(0);
            $table->timestamp('terms_accepted_at')->nullable();      // موافقة المالك على نسبة المنصة
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('property_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->boolean('is_cover')->default(false);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        // تفاصيل التشطيب: كل بند وحالته (تم / جزئي / ناقص / غير موجود)
        Schema::create('property_finishing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('item_key', 40);
            $table->string('status', 15);   // done | partial | missing | na
            $table->string('note')->nullable();
            $table->timestamps();
            $table->unique(['property_id', 'item_key']);
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'property_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('property_finishing_items');
        Schema::dropIfExists('property_images');
        Schema::dropIfExists('properties');
    }
};
