@extends('layout.master')

@section('title')
محرر الهيكلية اليدوي — {{ config('app.name') }}
@endsection

@section('page_title')
{{ __('controll_panel') }}
@endsection
@section('sub_main')
{{ __('home') }}
@endsection
@section('sub_title')
محرر الهيكلية اليدوي
@endsection

@section('css')
@include('components.admin.structure-styles')
<style>
    .chart-builder-page {
        --builder-blue: #1b84ff;
        --builder-red: #d92d20;
        --builder-gold: #d99a00;
        --builder-green: #0f766e;
        --builder-gray: #4b5563;
        --builder-line: #98a6ba;
        --builder-surface: #f6f8fb;
        --builder-border: #e4e8f0;
        --builder-shadow: 0 12px 32px rgba(24, 28, 50, 0.08);
    }
    .builder-shell {
        display: grid;
        grid-template-columns: 330px minmax(0, 1fr) 280px;
        gap: 1rem;
        align-items: start;
    }
    .builder-panel,
    .builder-workspace-card {
        border: 1px solid var(--builder-border);
        border-radius: 10px;
        background: #fff;
        box-shadow: var(--builder-shadow);
    }
    .builder-panel {
        overflow: hidden;
    }
    .builder-panel-head {
        padding: 1rem;
        border-bottom: 1px solid #edf0f5;
        background: #fbfcfe;
    }
    .builder-panel-body {
        padding: 1rem;
    }
    .builder-search {
        border-radius: 8px;
    }
    .builder-palette {
        max-height: calc(100vh - 330px);
        overflow: auto;
        padding-left: 0.25rem;
    }
    .builder-group-title {
        color: #7e8299;
        font-size: 0.76rem;
        font-weight: 800;
        margin: 1rem 0 0.5rem;
        text-transform: uppercase;
    }
    .builder-drag-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        width: 100%;
        padding: 0.7rem 0.8rem;
        margin-bottom: 0.5rem;
        border: 1px solid #edf0f5;
        border-radius: 8px;
        background: #fff;
        cursor: grab;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }
    .builder-drag-item:hover {
        border-color: #cdd9ea;
        box-shadow: 0 6px 16px rgba(24, 28, 50, 0.08);
        transform: translateY(-1px);
    }
    .builder-drag-item:active {
        cursor: grabbing;
    }
    .builder-item-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #f3f6f9;
        color: var(--builder-gray);
    }
    .builder-drag-item[data-type="department"] .builder-item-icon { background: #fff1f0; color: var(--builder-red); }
    .builder-drag-item[data-type="section"] .builder-item-icon { background: #eef6ff; color: var(--builder-blue); }
    .builder-drag-item[data-type="branch"] .builder-item-icon { background: #fff7df; color: var(--builder-gold); }
    .builder-drag-item[data-type="employee"] .builder-item-icon { background: #edf7f6; color: var(--builder-green); }
    .builder-item-title {
        font-size: 0.86rem;
        font-weight: 800;
        color: #181c32;
        line-height: 1.45;
    }
    .builder-item-subtitle {
        display: block;
        margin-top: 0.2rem;
        font-size: 0.74rem;
        color: #7e8299;
        font-weight: 600;
    }
    .builder-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding: 1rem;
        border-bottom: 1px solid #edf0f5;
        background: #fff;
    }
    .builder-workspace {
        height: calc(100vh - 280px);
        min-height: 580px;
        overflow: auto;
        background:
            linear-gradient(#e9edf4 1px, transparent 1px),
            linear-gradient(90deg, #e9edf4 1px, transparent 1px),
            var(--builder-surface);
        background-size: 28px 28px;
        position: relative;
    }
    .manual-canvas {
        width: 1600px;
        height: 980px;
        position: relative;
        margin: 1.5rem;
    }
    .builder-connectors {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        overflow: visible;
        z-index: 1;
    }
    .builder-node {
        position: absolute;
        width: 250px;
        min-height: 86px;
        border: 1px solid var(--builder-border);
        border-top: 4px solid var(--builder-gray);
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(24, 28, 50, 0.09);
        z-index: 2;
        cursor: move;
        user-select: none;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .builder-node.is-selected {
        border-color: var(--builder-blue);
        box-shadow: 0 0 0 3px rgba(27, 132, 255, 0.16), 0 12px 26px rgba(24, 28, 50, 0.12);
    }
    .builder-node[data-type="department"] { border-top-color: var(--builder-red); }
    .builder-node[data-type="section"] { border-top-color: var(--builder-blue); }
    .builder-node[data-type="branch"] { border-top-color: var(--builder-gold); }
    .builder-node[data-type="employee"] { border-top-color: var(--builder-green); width: 230px; min-height: 76px; }
    .builder-node-inner {
        padding: 0.85rem;
    }
    .builder-node-title {
        font-size: 0.9rem;
        font-weight: 800;
        color: #181c32;
        line-height: 1.45;
        word-break: break-word;
    }
    .builder-node-subtitle {
        margin-top: 0.35rem;
        font-size: 0.75rem;
        color: #7e8299;
        font-weight: 600;
        line-height: 1.5;
        word-break: break-word;
    }
    .builder-node-type {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-top: 0.6rem;
        padding: 0.22rem 0.5rem;
        border-radius: 6px;
        background: #f3f6f9;
        color: #5e6278;
        font-size: 0.7rem;
        font-weight: 800;
    }
    .builder-node-remove {
        position: absolute;
        top: 0.45rem;
        left: 0.45rem;
        width: 26px;
        height: 26px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .builder-node:hover .builder-node-remove,
    .builder-node.is-selected .builder-node-remove {
        opacity: 1;
    }
    .builder-empty-hint {
        position: absolute;
        top: 2rem;
        right: 2rem;
        max-width: 420px;
        padding: 1rem;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        background: rgba(255,255,255,0.88);
        color: #7e8299;
        font-weight: 700;
        z-index: 0;
    }
    .builder-detail-row {
        padding: 0.75rem 0;
        border-bottom: 1px dashed #edf0f5;
    }
    .builder-detail-row:last-child {
        border-bottom: 0;
    }
    .builder-type-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.55rem;
        border-radius: 6px;
        background: #f3f6f9;
        color: #5e6278;
        font-size: 0.76rem;
        font-weight: 800;
    }
    .builder-status {
        min-height: 1.25rem;
        color: #7e8299;
        font-size: 0.8rem;
        font-weight: 700;
    }
    @media (max-width: 1199.98px) {
        .builder-shell {
            grid-template-columns: 1fr;
        }
        .builder-palette {
            max-height: 420px;
        }
        .builder-workspace {
            height: 680px;
        }
    }
    @media print {
        header, footer, .sidebar, .nav, .builder-panel, .builder-toolbar { display: none !important; }
        .builder-shell { display: block !important; }
        .builder-workspace-card { border: 0 !important; box-shadow: none !important; }
        .builder-workspace { height: auto !important; overflow: visible !important; background: #fff !important; }
        .manual-canvas { margin: 0 !important; }
        .builder-node-remove, .builder-empty-hint { display: none !important; }
    }
</style>
@endsection

@section('content')
@php
    $savedNodes = data_get($layout->layout, 'nodes', []);
@endphp

<div class="container-fluid chart-builder-page">
    {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
    <x-page-header
        title="محرر الهيكلية اليدوي"
        subtitle="اسحب الدوائر والأقسام والشعب والموظفين إلى مساحة العمل، ثم احفظ المخطط أو صدّره كصورة."
    >
        <x-slot name="actions">
            <a href="{{ route('structure.org-chart') }}" class="btn btn-light-primary font-weight-bold">
                <i class="fas fa-sitemap ml-1"></i>
                {{ __('org_chart') }}
            </a>
        </x-slot>
    </x-page-header>

    <x-admin.structure-tabs active="builder" />

    <div class="builder-shell">
        <aside class="builder-panel">
            <div class="builder-panel-head">
                <div class="font-weight-bolder text-dark mb-3">العناصر</div>
                <input type="search" class="form-control builder-search" id="builderSearch" placeholder="بحث في الدوائر أو الموظفين...">
            </div>
            <div class="builder-panel-body builder-palette" id="builderPalette">
                <div class="builder-group-title">أساس المخطط</div>
                <div class="builder-drag-item" draggable="true" data-type="root" data-id="" data-title="{{ __('Municipality of Jabalia Al-Nazlah') }}" data-subtitle="{{ __('Structural') }}" data-parent-key="">
                    <span class="builder-item-icon"><i class="fas fa-landmark"></i></span>
                    <span>
                        <span class="builder-item-title">{{ __('Municipality of Jabalia Al-Nazlah') }}</span>
                        <span class="builder-item-subtitle">{{ __('Structural') }}</span>
                    </span>
                </div>

                <div class="builder-group-title">{{ __('departments') }}</div>
                @foreach($departments as $department)
                    <div class="builder-drag-item" draggable="true" data-type="department" data-id="{{ $department->id }}" data-title="{{ $department->name }}" data-subtitle="{{ __('department') }}" data-parent-key="root">
                        <span class="builder-item-icon"><i class="fas fa-building"></i></span>
                        <span>
                            <span class="builder-item-title">{{ $department->name }}</span>
                            <span class="builder-item-subtitle">{{ $department->sections->count() }} {{ __('sections') }}</span>
                        </span>
                    </div>

                    @foreach($department->sections as $section)
                        <div class="builder-drag-item" draggable="true" data-type="section" data-id="{{ $section->id }}" data-title="{{ $section->name }}" data-subtitle="{{ $department->name }}" data-parent-key="department:{{ $department->id }}">
                            <span class="builder-item-icon"><i class="fas fa-layer-group"></i></span>
                            <span>
                                <span class="builder-item-title">{{ $section->name }}</span>
                                <span class="builder-item-subtitle">{{ $department->name }}</span>
                            </span>
                        </div>

                        @foreach($section->branches as $branch)
                            <div class="builder-drag-item" draggable="true" data-type="branch" data-id="{{ $branch->id }}" data-title="{{ $branch->name }}" data-subtitle="{{ $section->name }}" data-parent-key="section:{{ $section->id }}">
                                <span class="builder-item-icon"><i class="fas fa-code-branch"></i></span>
                                <span>
                                    <span class="builder-item-title">{{ $branch->name }}</span>
                                    <span class="builder-item-subtitle">{{ $section->name }}</span>
                                </span>
                            </div>
                        @endforeach
                    @endforeach
                @endforeach

                <div class="builder-group-title">{{ __('employees') }}</div>
                @foreach($employees as $employee)
                    @php
                        $parentKey = $employee->branch_id
                            ? 'branch:' . $employee->branch_id
                            : ($employee->section_id
                                ? 'section:' . $employee->section_id
                                : ($employee->department_id ? 'department:' . $employee->department_id : ''));
                    @endphp
                    <div class="builder-drag-item" draggable="true" data-type="employee" data-id="{{ $employee->id }}" data-title="{{ $employee->name }}" data-subtitle="{{ $employee->job?->name ?? __('employees') }}" data-parent-key="{{ $parentKey }}">
                        <span class="builder-item-icon"><i class="fas fa-user"></i></span>
                        <span>
                            <span class="builder-item-title">{{ $employee->name }}</span>
                            <span class="builder-item-subtitle">{{ $employee->job?->name ?? __('employees') }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </aside>

        <main class="builder-workspace-card">
            <div class="builder-toolbar">
                <button type="button" class="btn btn-sm btn-primary font-weight-bold" id="saveBuilder">
                    <i class="fas fa-save ml-1"></i>
                    {{ __('save') }}
                </button>
                <button type="button" class="btn btn-sm btn-light-info font-weight-bold" id="exportBuilderPng">
                    <i class="fas fa-image ml-1"></i>
                    تصدير صورة
                </button>
                <button type="button" class="btn btn-sm btn-light-success font-weight-bold" id="autoLayoutBuilder">
                    <i class="fas fa-magic ml-1"></i>
                    إدراج الهيكل الحالي
                </button>
                <button type="button" class="btn btn-sm btn-light-warning font-weight-bold" id="clearBuilder">
                    <i class="fas fa-eraser ml-1"></i>
                    مسح مساحة العمل
                </button>
                <button type="button" class="btn btn-sm btn-light-dark font-weight-bold" id="printBuilder">
                    <i class="fas fa-print ml-1"></i>
                    {{ __('print') }}
                </button>
                <span class="builder-status align-self-center mr-auto" id="builderStatus"></span>
            </div>
            <div class="builder-workspace" id="builderWorkspace">
                <div class="manual-canvas" id="manualCanvas">
                    <svg class="builder-connectors" id="builderConnectors"></svg>
                    <div class="builder-empty-hint" id="builderEmptyHint">
                        اسحب عنصرًا من القائمة إلى هنا. عند إسقاط قسم أو شعبة أو موظف سيتم ربطه تلقائيًا بالأب المرتبط إذا كان موجودًا في المخطط.
                    </div>
                </div>
            </div>
        </main>

        <aside class="builder-panel">
            <div class="builder-panel-head">
                <div class="font-weight-bolder text-dark">خصائص العنصر</div>
            </div>
            <div class="builder-panel-body">
                <div id="builderNoSelection" class="text-muted font-weight-bold font-size-sm">
                    اختر عنصرًا من المخطط لتعديل علاقته أو حذفه.
                </div>
                <div id="builderSelectionPanel" style="display:none;">
                    <div class="builder-detail-row">
                        <span class="builder-type-pill" id="selectedType">-</span>
                    </div>
                    <div class="builder-detail-row">
                        <label class="font-weight-bold text-dark font-size-sm">الاسم</label>
                        <div class="text-muted font-weight-bold" id="selectedTitle">-</div>
                    </div>
                    <div class="builder-detail-row">
                        <label for="selectedParent" class="font-weight-bold text-dark font-size-sm">العنصر الأب</label>
                        <select class="form-control" id="selectedParent"></select>
                    </div>
                    <div class="builder-detail-row">
                        <button type="button" class="btn btn-sm btn-light-danger font-weight-bold" id="deleteSelected">
                            <i class="fas fa-trash ml-1"></i>
                            حذف العنصر
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

@section('scripts')
@php
    $hierarchy = $departments->map(function ($department) use ($employeeGroups) {
        return [
            'id' => $department->id,
            'name' => $department->name,
            'employees' => ($employeeGroups['department']->get($department->id) ?? collect())->map(fn ($employee) => [
                'id' => $employee->id,
                'name' => $employee->name,
                'job' => optional($employee->job)->name,
            ])->values(),
            'sections' => $department->sections->map(function ($section) use ($employeeGroups) {
                return [
                    'id' => $section->id,
                    'name' => $section->name,
                    'employees' => ($employeeGroups['section']->get($section->id) ?? collect())->map(fn ($employee) => [
                        'id' => $employee->id,
                        'name' => $employee->name,
                        'job' => optional($employee->job)->name,
                    ])->values(),
                    'branches' => $section->branches->map(function ($branch) use ($employeeGroups) {
                        return [
                            'id' => $branch->id,
                            'name' => $branch->name,
                            'employees' => ($employeeGroups['branch']->get($branch->id) ?? collect())->map(fn ($employee) => [
                                'id' => $employee->id,
                                'name' => $employee->name,
                                'job' => optional($employee->job)->name,
                            ])->values(),
                        ];
                    })->values(),
                ];
            })->values(),
        ];
    })->values();
@endphp
<script>
(function () {
    var savedNodes = @json($savedNodes);
    var hierarchy = @json($hierarchy);

    var canvas = document.getElementById('manualCanvas');
    var workspace = document.getElementById('builderWorkspace');
    var connectors = document.getElementById('builderConnectors');
    var emptyHint = document.getElementById('builderEmptyHint');
    var statusEl = document.getElementById('builderStatus');
    var nodes = Array.isArray(savedNodes) ? savedNodes : [];
    var selectedUid = null;
    var dragPayload = null;

    var typeLabels = {
        root: 'الجذر',
        department: '{{ __('department') }}',
        section: '{{ __('section') }}',
        branch: '{{ __('branch') }}',
        employee: '{{ __('employees') }}'
    };

    function setStatus(message, tone) {
        statusEl.textContent = message || '';
        statusEl.className = 'builder-status align-self-center mr-auto';
        if (tone) statusEl.classList.add('text-' + tone);
    }

    function nodeKey(node) {
        return node.type + ':' + (node.entity_id || 'root');
    }

    function uidFor(type, id) {
        return type === 'root' ? 'root-root' : type + '-' + id;
    }

    function findByUid(uid) {
        return nodes.find(function (node) { return node.uid === uid; });
    }

    function findByKey(key) {
        if (!key) return null;
        if (key === 'root') return nodes.find(function (node) { return node.type === 'root'; }) || null;
        return nodes.find(function (node) { return nodeKey(node) === key; }) || null;
    }

    function clampPosition(node) {
        node.x = Math.max(12, Math.min(Number(node.x) || 12, canvas.offsetWidth - 280));
        node.y = Math.max(12, Math.min(Number(node.y) || 12, canvas.offsetHeight - 110));
    }

    function normalizeNode(node) {
        node.uid = node.uid || uidFor(node.type, node.entity_id);
        node.entity_id = node.entity_id || null;
        node.title = node.title || '-';
        node.subtitle = node.subtitle || '';
        node.parent_uid = node.parent_uid || null;
        node.x = Number(node.x) || 40;
        node.y = Number(node.y) || 40;
        clampPosition(node);
        return node;
    }

    function createNodeFromPayload(payload, x, y) {
        var type = payload.type;
        var entityId = payload.id ? Number(payload.id) : null;
        var uid = uidFor(type, entityId);
        var existing = findByUid(uid);

        if (existing) {
            selectedUid = existing.uid;
            setStatus('العنصر موجود مسبقًا في المخطط.', 'warning');
            render();
            return;
        }

        var parent = findByKey(payload.parentKey);
        nodes.push(normalizeNode({
            uid: uid,
            type: type,
            entity_id: entityId,
            title: payload.title,
            subtitle: payload.subtitle,
            parent_uid: parent ? parent.uid : null,
            x: x,
            y: y
        }));
        selectedUid = uid;
        setStatus('تمت إضافة العنصر. لا تنس الحفظ.', 'primary');
        render();
    }

    function removeNode(uid) {
        nodes = nodes
            .filter(function (node) { return node.uid !== uid; })
            .map(function (node) {
                if (node.parent_uid === uid) node.parent_uid = null;
                return node;
            });
        if (selectedUid === uid) selectedUid = null;
        render();
    }

    function render() {
        canvas.querySelectorAll('.builder-node').forEach(function (nodeEl) { nodeEl.remove(); });
        nodes = nodes.map(normalizeNode);
        emptyHint.style.display = nodes.length ? 'none' : 'block';

        nodes.forEach(function (node) {
            var el = document.createElement('div');
            el.className = 'builder-node' + (node.uid === selectedUid ? ' is-selected' : '');
            el.dataset.uid = node.uid;
            el.dataset.type = node.type;
            el.style.right = node.x + 'px';
            el.style.top = node.y + 'px';
            el.innerHTML =
                '<button type="button" class="btn btn-xs btn-light-danger builder-node-remove" title="حذف"><i class="fas fa-times"></i></button>' +
                '<div class="builder-node-inner">' +
                    '<div class="builder-node-title"></div>' +
                    '<div class="builder-node-subtitle"></div>' +
                    '<span class="builder-node-type"><i class="' + iconFor(node.type) + '"></i>' + (typeLabels[node.type] || node.type) + '</span>' +
                '</div>';
            el.querySelector('.builder-node-title').textContent = node.title;
            el.querySelector('.builder-node-subtitle').textContent = node.subtitle || '';
            el.querySelector('.builder-node-remove').addEventListener('click', function (event) {
                event.stopPropagation();
                removeNode(node.uid);
            });
            el.addEventListener('pointerdown', startNodeDrag);
            el.addEventListener('click', function () {
                selectedUid = node.uid;
                render();
            });
            canvas.appendChild(el);
        });

        renderConnectors();
        renderSelection();
    }

    function iconFor(type) {
        return {
            root: 'fas fa-landmark',
            department: 'fas fa-building',
            section: 'fas fa-layer-group',
            branch: 'fas fa-code-branch',
            employee: 'fas fa-user'
        }[type] || 'fas fa-circle';
    }

    function renderConnectors() {
        connectors.innerHTML = '';
        connectors.setAttribute('viewBox', '0 0 ' + canvas.offsetWidth + ' ' + canvas.offsetHeight);
        nodes.forEach(function (node) {
            if (!node.parent_uid) return;
            var parentEl = canvas.querySelector('[data-uid="' + CSS.escape(node.parent_uid) + '"]');
            var childEl = canvas.querySelector('[data-uid="' + CSS.escape(node.uid) + '"]');
            if (!parentEl || !childEl) return;

            var parent = findByUid(node.parent_uid);
            var child = findByUid(node.uid);
            var x1 = parent.x + parentEl.offsetWidth / 2;
            var y1 = parent.y + parentEl.offsetHeight;
            var x2 = child.x + childEl.offsetWidth / 2;
            var y2 = child.y;
            var midY = y1 + Math.max(28, (y2 - y1) / 2);
            var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', 'M ' + x1 + ' ' + y1 + ' C ' + x1 + ' ' + midY + ', ' + x2 + ' ' + midY + ', ' + x2 + ' ' + y2);
            path.setAttribute('fill', 'none');
            path.setAttribute('stroke', 'var(--builder-line)');
            path.setAttribute('stroke-width', '3');
            path.setAttribute('stroke-linecap', 'round');
            connectors.appendChild(path);
        });
    }

    function renderSelection() {
        var selected = findByUid(selectedUid);
        document.getElementById('builderNoSelection').style.display = selected ? 'none' : '';
        document.getElementById('builderSelectionPanel').style.display = selected ? '' : 'none';
        if (!selected) return;

        document.getElementById('selectedType').innerHTML = '<i class="' + iconFor(selected.type) + '"></i>' + (typeLabels[selected.type] || selected.type);
        document.getElementById('selectedTitle').textContent = selected.title;

        var parentSelect = document.getElementById('selectedParent');
        parentSelect.innerHTML = '<option value="">بدون أب</option>';
        nodes
            .filter(function (node) { return node.uid !== selected.uid && node.type !== 'employee'; })
            .forEach(function (node) {
                var option = document.createElement('option');
                option.value = node.uid;
                option.textContent = node.title + ' - ' + (typeLabels[node.type] || node.type);
                if (node.uid === selected.parent_uid) option.selected = true;
                parentSelect.appendChild(option);
            });
    }

    function startNodeDrag(event) {
        if (event.target.closest('button')) return;
        var el = event.currentTarget;
        var node = findByUid(el.dataset.uid);
        if (!node) return;

        selectedUid = node.uid;
        renderSelection();
        el.classList.add('is-selected');

        var startX = event.clientX;
        var startY = event.clientY;
        var originalX = node.x;
        var originalY = node.y;
        el.setPointerCapture(event.pointerId);

        function move(moveEvent) {
            node.x = originalX - (moveEvent.clientX - startX);
            node.y = originalY + (moveEvent.clientY - startY);
            clampPosition(node);
            el.style.right = node.x + 'px';
            el.style.top = node.y + 'px';
            renderConnectors();
        }

        function up() {
            el.removeEventListener('pointermove', move);
            el.removeEventListener('pointerup', up);
            setStatus('تم تعديل الموقع. لا تنس الحفظ.', 'primary');
        }

        el.addEventListener('pointermove', move);
        el.addEventListener('pointerup', up);
    }

    document.querySelectorAll('.builder-drag-item').forEach(function (item) {
        item.addEventListener('dragstart', function (event) {
            dragPayload = {
                type: item.dataset.type,
                id: item.dataset.id,
                title: item.dataset.title,
                subtitle: item.dataset.subtitle,
                parentKey: item.dataset.parentKey
            };
            event.dataTransfer.effectAllowed = 'copy';
            event.dataTransfer.setData('text/plain', JSON.stringify(dragPayload));
        });
    });

    workspace.addEventListener('dragover', function (event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'copy';
    });

    workspace.addEventListener('drop', function (event) {
        event.preventDefault();
        var payload = dragPayload;
        try {
            payload = JSON.parse(event.dataTransfer.getData('text/plain')) || payload;
        } catch (e) {}
        if (!payload) return;

        var canvasRect = canvas.getBoundingClientRect();
        var x = canvasRect.right - event.clientX;
        var y = event.clientY - canvasRect.top;
        createNodeFromPayload(payload, x, y);
        dragPayload = null;
    });

    document.getElementById('selectedParent').addEventListener('change', function () {
        var selected = findByUid(selectedUid);
        if (!selected) return;
        selected.parent_uid = this.value || null;
        setStatus('تم تعديل العلاقة. لا تنس الحفظ.', 'primary');
        renderConnectors();
    });

    document.getElementById('deleteSelected').addEventListener('click', function () {
        if (!selectedUid) return;
        removeNode(selectedUid);
        setStatus('تم حذف العنصر. لا تنس الحفظ.', 'warning');
    });

    document.getElementById('builderSearch').addEventListener('input', function () {
        var q = this.value.trim().toLowerCase();
        document.querySelectorAll('.builder-drag-item').forEach(function (item) {
            var text = item.textContent.toLowerCase();
            item.style.display = !q || text.indexOf(q) !== -1 ? '' : 'none';
        });
    });

    document.getElementById('clearBuilder').addEventListener('click', function () {
        if (!confirm('هل تريد مسح مساحة العمل الحالية؟')) return;
        nodes = [];
        selectedUid = null;
        setStatus('تم مسح مساحة العمل. اضغط حفظ لتثبيت التغيير.', 'warning');
        render();
    });

    document.getElementById('autoLayoutBuilder').addEventListener('click', function () {
        nodes = buildAutoLayout();
        selectedUid = null;
        setStatus('تم إدراج الهيكل الحالي كتخطيط مبدئي. يمكنك سحب العناصر وتعديلها ثم الحفظ.', 'primary');
        render();
    });

    function buildAutoLayout() {
        var result = [];
        result.push(normalizeNode({
            uid: 'root-root',
            type: 'root',
            entity_id: null,
            title: '{{ __('Municipality of Jabalia Al-Nazlah') }}',
            subtitle: '{{ __('Structural') }}',
            parent_uid: null,
            x: 675,
            y: 40
        }));

        var deptX = 90;
        var deptY = 190;
        hierarchy.forEach(function (department, departmentIndex) {
            var deptUid = uidFor('department', department.id);
            var x = deptX + (departmentIndex * 285);
            result.push(normalizeNode({
                uid: deptUid,
                type: 'department',
                entity_id: department.id,
                title: department.name,
                subtitle: '{{ __('department') }}',
                parent_uid: 'root-root',
                x: x,
                y: deptY
            }));

            var y = deptY + 150;
            department.sections.forEach(function (section) {
                var sectionUid = uidFor('section', section.id);
                result.push(normalizeNode({
                    uid: sectionUid,
                    type: 'section',
                    entity_id: section.id,
                    title: section.name,
                    subtitle: department.name,
                    parent_uid: deptUid,
                    x: x,
                    y: y
                }));
                y += 130;

                section.branches.forEach(function (branch) {
                    var branchUid = uidFor('branch', branch.id);
                    result.push(normalizeNode({
                        uid: branchUid,
                        type: 'branch',
                        entity_id: branch.id,
                        title: branch.name,
                        subtitle: section.name,
                        parent_uid: sectionUid,
                        x: x + 30,
                        y: y
                    }));
                    y += 118;
                });
            });
        });

        return result;
    }

    document.getElementById('saveBuilder').addEventListener('click', function () {
        setStatus('جاري الحفظ...', 'primary');
        fetch('{{ route('structure.org-chart.builder.save') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nodes: nodes })
        })
            .then(function (response) {
                if (!response.ok) throw new Error('save failed');
                return response.json();
            })
            .then(function () {
                setStatus('تم حفظ المخطط بنجاح.', 'success');
            })
            .catch(function () {
                setStatus('تعذر حفظ المخطط. تحقق من الاتصال أو الصلاحيات.', 'danger');
            });
    });

    document.getElementById('printBuilder').addEventListener('click', function () {
        window.print();
    });

    document.getElementById('exportBuilderPng').addEventListener('click', exportPng);

    function collectStyles() {
        var styles = '';
        Array.prototype.forEach.call(document.styleSheets, function (sheet) {
            try {
                Array.prototype.forEach.call(sheet.cssRules || [], function (rule) {
                    styles += rule.cssText + '\n';
                });
            } catch (e) {}
        });
        return styles;
    }

    function exportPng() {
        selectedUid = null;
        render();
        setStatus('جاري تجهيز الصورة...', 'primary');

        var clone = canvas.cloneNode(true);
        clone.querySelectorAll('.builder-node-remove, .builder-empty-hint').forEach(function (el) { el.remove(); });
        clone.style.margin = '0';
        clone.style.background = '#f6f8fb';

        var width = canvas.offsetWidth;
        var height = canvas.offsetHeight;
        var html = '<div xmlns="http://www.w3.org/1999/xhtml"><style>' + collectStyles() + '</style>' + clone.outerHTML + '</div>';
        var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' + width + '" height="' + height + '">' +
            '<foreignObject width="100%" height="100%">' + html + '</foreignObject></svg>';
        var blob = new Blob([svg], { type: 'image/svg+xml;charset=utf-8' });
        var url = URL.createObjectURL(blob);
        var image = new Image();

        image.onload = function () {
            var exportCanvas = document.createElement('canvas');
            exportCanvas.width = width;
            exportCanvas.height = height;
            var context = exportCanvas.getContext('2d');
            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, width, height);
            context.drawImage(image, 0, 0);
            URL.revokeObjectURL(url);

            var link = document.createElement('a');
            link.download = 'manual-org-chart-' + new Date().toISOString().slice(0, 10) + '.png';
            link.href = exportCanvas.toDataURL('image/png');
            link.click();
            setStatus('تم تصدير الصورة.', 'success');
        };

        image.onerror = function () {
            URL.revokeObjectURL(url);
            setStatus('تعذر تصدير الصورة من المتصفح الحالي.', 'danger');
        };

        image.src = url;
    }

    if (!nodes.length) {
        nodes = [];
    }
    render();
})();
</script>
@endsection
