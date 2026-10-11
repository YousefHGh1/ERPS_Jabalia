<?php

/**
 * One-time script: apply Auditable trait to all Eloquent models.
 * Run: php scripts/apply_auditable_trait.php
 */

$modelsPath = __DIR__ . '/../app/Models';
$exclude = ['Concerns', 'ZKData.php', 'Role.php', 'Permission.php'];

$files = glob($modelsPath . '/*.php');

foreach ($files as $file) {
    $basename = basename($file);
    if (in_array($basename, $exclude, true)) {
        continue;
    }

    $content = file_get_contents($file);
    $original = $content;

    if (str_contains($content, 'use App\\Models\\Concerns\\Auditable;')) {
        echo "SKIP (already has Auditable): {$basename}\n";
        continue;
    }

    // Remove standalone LogsActivity if present (will be replaced by Auditable)
    $content = preg_replace('/use Spatie\\\\Activitylog\\\\Traits\\\\LogsActivity;\s*/', '', $content);
    $content = preg_replace('/,?\s*LogsActivity/', '', $content);

    // Remove getActivitylogOptions method block (models will use Auditable defaults or auditAttributes override)
    $content = preg_replace(
        '/\s*public function getActivitylogOptions\(\)[^{]*\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}\s*/s',
        "\n",
        $content
    );

    // Add Auditable import after namespace
    if (!str_contains($content, 'use App\Models\Concerns\Auditable;')) {
        $content = preg_replace(
            '/(namespace App\\\\Models;\s*\n)/',
            "$1\nuse App\\Models\\Concerns\\Auditable;\n",
            $content,
            1
        );
    }

    // Add Auditable to class use statement
    if (preg_match('/class \w+ extends \w+\s*\n\{\s*\n(\s*)use /', $content, $m)) {
        $indent = $m[1];
        $content = preg_replace(
            '/(class \w+ extends \w+\s*\n\{\s*\n\s*use [^;]+;)/',
            '$1' . "\n{$indent}use Auditable;",
            $content,
            1
        );
    } elseif (preg_match('/class \w+ extends \w+\s*\n\{\s*\n/', $content)) {
        $content = preg_replace(
            '/(class \w+ extends \w+\s*\n\{\s*\n)/',
            "$1    use Auditable;\n\n",
            $content,
            1
        );
    }

    // For models that had custom logOnly, add auditLogName and auditAttributes from known map
    $auditConfig = getAuditConfig($basename);
    if ($auditConfig && !str_contains($content, 'auditLogName')) {
        $methods = "\n    protected static function auditLogName(): string\n    {\n        return '{$auditConfig['logName']}';\n    }\n\n    protected function auditAttributes(): array\n    {\n        return " . var_export($auditConfig['attributes'], true) . ";\n    }\n";
        $content = preg_replace('/(\n\})\s*$/', $methods . '$1', $content);
    }

    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "UPDATED: {$basename}\n";
    } else {
        echo "NO CHANGE: {$basename}\n";
    }
}

function getAuditConfig(string $basename): ?array
{
    $map = [
        'Request.php' => [
            'logName' => 'requests',
            'attributes' => ['text', 'request_type_id', 'status_id', 'status_reason', 'mokalaf_id', 'deadline', 'is_urgent'],
        ],
        'CraftLicense.php' => [
            'logName' => 'craft_licenses',
            'attributes' => ['file_number', 'owner_name', 'commercial_name', 'status', 'craft_type', 'remaining'],
        ],
        'Notice.php' => [
            'logName' => 'notices',
            'attributes' => ['notice_number', 'notice_date', 'notice_reason_id', 'rent_location_id', 'status'],
        ],
        'Document.php' => [
            'logName' => 'documents',
            'attributes' => ['document_number', 'document_type', 'title', 'status'],
        ],
        'Reservation.php' => [
            'logName' => 'reservations',
            'attributes' => ['reservation_number', 'reservation_date', 'court_name', 'judge_name', 'case_number', 'status'],
        ],
        'ImprisonmentOrder.php' => [
            'logName' => 'imprisonment_orders',
            'attributes' => ['order_number', 'order_date', 'court_name', 'defendant_name', 'status'],
        ],
    ];

    return $map[$basename] ?? null;
}

echo "Done.\n";
