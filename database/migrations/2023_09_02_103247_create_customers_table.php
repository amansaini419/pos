<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_number');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name')->virtualAs('concat(first_name, \' \', last_name)');
            $table->string('country_code')->default('+233');
            $table->string('phone_number')->unique();
            $table->string('email')->unique();
            $table->smallInteger('customer_type');
            $table->string('registration_number');
            $table->string('customer_photo_path');
            $table->string('ghana_card_number');
            $table->string('ghana_card_path');
            $table->string('business_certificate_path');
            $table->string('location');
            $table->smallInteger('customer_status')->default(0);
            $table->foreignIdFor(User::class, 'added_by');
            $table->foreignIdFor(User::class, 'assigned_to')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
