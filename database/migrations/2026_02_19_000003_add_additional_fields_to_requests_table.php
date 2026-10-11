<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            // حقول الطلب المشترك
            $table->boolean('join')->nullable()->after('mokalaf_id');
            $table->string('common_name')->nullable()->after('join');
            
            // بيانات العنوان التفصيلية
            $table->string('address')->nullable()->after('common_name');
            $table->string('mobile')->nullable()->after('address');
            $table->string('jebaia_no')->nullable()->after('mobile');
            $table->string('neighborhood')->nullable()->after('jebaia_no');
            $table->string('widget')->nullable()->after('neighborhood');
            $table->string('voucher')->nullable()->after('widget');
            $table->string('divider')->nullable()->after('voucher');
            $table->string('building')->nullable()->after('divider');
            $table->string('floor')->nullable()->after('building');
            $table->string('apartment')->nullable()->after('floor');
            $table->string('owner')->nullable()->after('apartment');
            $table->string('tenant')->nullable()->after('owner');
        });
    }

    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn([
                'join',
                'common_name',
                'address',
                'mobile',
                'jebaia_no',
                'neighborhood',
                'widget',
                'voucher',
                'divider',
                'building',
                'floor',
                'apartment',
                'owner',
                'tenant'
            ]);
        });
    }
};
