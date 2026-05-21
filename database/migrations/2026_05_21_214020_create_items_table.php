<?php

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
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // Item Basic Details
            $table->string('item_code')->unique();
            $table->string('item_name');
            $table->text('description')->nullable();

            // Item Identification
            $table->string('serial_number')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('barcode')->nullable();

            // Relationships
            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('supplier');

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained('brands');

            $table->foreignId('category_id')
                ->constrained('categories');

            $table->foreignId('sub_category_id')
                ->nullable()
                ->constrained('sub_categories');

            // Pricing Information
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->decimal('list_price', 12, 2)->default(0);
            $table->decimal('avg_price', 12, 2)->default(0);

            // Inventory Details
            $table->decimal('unit_pack', 10, 2)->nullable();
            $table->string('unit_of_measure', 50)->nullable();

            // Item Classification
            $table->string('group_code')->nullable();
            $table->string('type_code')->nullable();
            $table->string('price_level')->nullable();

            // Status
            $table->boolean('status')->default(true);

            $table->string('image_url')->nullable();
            // Audit Fields
            $table->foreignId('created_by')
                ->constrained('users');

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users');

            $table->foreignId('deleted_by')
                ->nullable()
                ->constrained('users');

            // Soft Delete & Timestamps
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
