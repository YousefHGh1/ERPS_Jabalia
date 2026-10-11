<?php

/**
 * ربط مصادر الإيرادات في الصندوق المركزي بأقسام الهيكل التنظيمي (sections).
 * عيّن معرّف القسم (من جدول sections) لكل مصدر، أو استخدم أسماء البيئة أدناه.
 */
return [

    'revenue_sources' => [
        'crafts' => [
            'section_id' => env('CENTRAL_TREASURY_SECTION_CRAFTS_ID'),
        ],
        'engineering' => [
            'section_id' => env('CENTRAL_TREASURY_SECTION_ENGINEERING_ID'),
        ],
        'public_pen_requests' => [
            'section_id' => env('CENTRAL_TREASURY_SECTION_REQUESTS_ID'),
        ],
    ],

];
