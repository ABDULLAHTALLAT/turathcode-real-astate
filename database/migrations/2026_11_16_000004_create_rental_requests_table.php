<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable()->unique();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();

            // بيانات المستأجر وقت الطلب
            $table->string('purpose', 30);                       // الغرض من الشقة
            $table->string('marital_status', 20)->nullable();    // متزوج / أعزب
            $table->unsignedTinyInteger('occupants')->nullable();
            $table->date('start_date')->nullable();
            $table->text('message')->nullable();

            // العرض الحالي (يتغير مع التفاوض)
            $table->unsignedInteger('proposed_rent');
            $table->unsignedTinyInteger('lease_years');
            $table->string('last_offer_by', 10)->default('tenant');   // tenant | owner

            // pending | negotiating | accepted | completed | rejected | cancelled
            $table->string('status', 20)->default('pending')->index();

            // بعد الاتفاق: القيمة والعمولة (تتحفظ كما هي وقت الاتفاق)
            $table->unsignedBigInteger('total_value')->nullable();
            $table->decimal('owner_commission_percent', 5, 2)->nullable();
            $table->decimal('tenant_commission_percent', 5, 2)->nullable();
            $table->unsignedBigInteger('owner_commission')->nullable();
            $table->unsignedBigInteger('tenant_commission')->nullable();
            $table->timestamp('owner_commission_paid_at')->nullable();
            $table->timestamp('tenant_commission_paid_at')->nullable();

            $table->timestamp('tenant_terms_accepted_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_requests');
    }
};
