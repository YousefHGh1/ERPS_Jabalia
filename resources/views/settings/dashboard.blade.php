@extends('layout.master')

@section('title')
    {{ __('لوحة تحكم الصلاحيات') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الإعدادات') }} / {{ __('لوحة تحكم الصلاحيات') }}
@endsection

@section('content')
<div class="container-fluid settings-page perm-dash">
    <div class="card card-custom settings-card gutter-b perm-shell">
        <x-page-header
            title="{{ __('لوحة تحكم الصلاحيات') }}"
            subtitle="{{ __('نظرة شاملة على المستخدمين والأدوار والصلاحيات مع إجراءات سريعة') }}"
            icon="fa-shield-halved"
        >
            <x-slot name="actions">
                <a href="{{ route($prefix.'users.index') }}" class="btn btn-light-primary font-weight-bolder px-4 mb-2">
                    <i class="fas fa-users ml-1"></i>{{ __('المستخدمون') }}
                </a>
                <a href="{{ route($prefix.'roles.index') }}" class="btn btn-light-success font-weight-bolder px-4 mb-2">
                    <i class="fas fa-user-tag ml-1"></i>{{ __('الأدوار') }}
                </a>
                <a href="{{ route($prefix.'permissions.index') }}" class="btn btn-light-warning font-weight-bolder px-4 mb-2">
                    <i class="fas fa-key ml-1"></i>{{ __('الصلاحيات') }}
                </a>
                <a href="{{ route($prefix.'export.permission-matrix') }}" class="btn btn-light-info font-weight-bolder px-4 mb-2">
                    <i class="fas fa-file-excel ml-1"></i>{{ __('المصفوفة') }}
                </a>
            </x-slot>
        </x-page-header>

        <div class="card-body perm-body">
            {{-- بطاقات المؤشرات --}}
            <div class="row perm-kpis">
                <div class="col-xl-3 col-sm-6">
                    <div class="perm-kpi perm-kpi--blue">
                        <div class="perm-kpi-top">
                            <span class="perm-kpi-icon"><i class="fas fa-users"></i></span>
                            <a href="{{ route($prefix.'users.index') }}" class="perm-kpi-link">{{ __('عرض الكل') }}<i class="fas fa-angle-left mr-1"></i></a>
                        </div>
                        <div class="perm-kpi-num">{{ number_format($usersCount) }}</div>
                        <div class="perm-kpi-label">{{ __('إجمالي المستخدمين') }}</div>
                        <div class="perm-kpi-bar"><span style="width:{{ $coveragePct }}%"></span></div>
                        <div class="perm-kpi-meta"><span>{{ number_format($assignedUsers) }} {{ __('بأدوار') }}</span><b>{{ $coveragePct }}%</b></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="perm-kpi perm-kpi--green">
                        <div class="perm-kpi-top">
                            <span class="perm-kpi-icon"><i class="fas fa-user-tag"></i></span>
                            <a href="{{ route($prefix.'roles.index') }}" class="perm-kpi-link">{{ __('عرض الكل') }}<i class="fas fa-angle-left mr-1"></i></a>
                        </div>
                        <div class="perm-kpi-num">{{ number_format($rolesCount) }}</div>
                        <div class="perm-kpi-label">{{ __('إجمالي الأدوار') }}</div>
                        <div class="perm-kpi-bar"><span style="width:{{ $rolesCoveragePct }}%"></span></div>
                        <div class="perm-kpi-meta"><span>{{ number_format($rolesWithUsers) }} {{ __('مستخدمة') }}</span><b>{{ $rolesCoveragePct }}%</b></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="perm-kpi perm-kpi--amber">
                        <div class="perm-kpi-top">
                            <span class="perm-kpi-icon"><i class="fas fa-key"></i></span>
                            <a href="{{ route($prefix.'permissions.index') }}" class="perm-kpi-link">{{ __('عرض الكل') }}<i class="fas fa-angle-left mr-1"></i></a>
                        </div>
                        <div class="perm-kpi-num">{{ number_format($permissionsCount) }}</div>
                        <div class="perm-kpi-label">{{ __('إجمالي الصلاحيات') }}</div>
                        <div class="perm-kpi-bar"><span style="width:{{ $permsUsagePct }}%"></span></div>
                        <div class="perm-kpi-meta"><span>{{ number_format($usedPermissions) }} {{ __('مربوطة بأدوار') }}</span><b>{{ $permsUsagePct }}%</b></div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="perm-kpi perm-kpi--violet">
                        <div class="perm-kpi-top">
                            <span class="perm-kpi-icon"><i class="fas fa-user-check"></i></span>
                            <span class="perm-kpi-alert">{{ number_format($unassignedUsers) }} {{ __('بدون دور') }}</span>
                        </div>
                        <div class="perm-kpi-num">{{ $coveragePct }}<small>%</small></div>
                        <div class="perm-kpi-label">{{ __('تغطية المستخدمين بالأدوار') }}</div>
                        <div class="perm-kpi-bar"><span style="width:{{ $coveragePct }}%"></span></div>
                        <div class="perm-kpi-meta"><span>{{ __('أدوار فارغة') }}: {{ number_format($emptyRoles) }}</span><span>{{ __('غير مستخدمة') }}: {{ number_format($unusedPermissions) }}</span></div>
                    </div>
                </div>
            </div>

            {{-- توزيع الصلاحيات + الأكثر استخداماً --}}
            <div class="row">
                <div class="col-xl-7 mb-4">
                    <div class="perm-panel h-100">
                        <div class="perm-panel-head">
                            <div>
                                <h3>{{ __('توزيع الصلاحيات حسب النوع') }}</h3>
                                <p>{{ __('فهم سريع لطبيعة الصلاحيات في النظام') }}</p>
                            </div>
                            <a href="{{ route($prefix.'permissions.index') }}" class="btn btn-sm btn-light-primary font-weight-bolder">{{ __('إدارة الصلاحيات') }}</a>
                        </div>
                        <div class="perm-types">
                            @foreach($typeStats as $t)
                            <div class="perm-type">
                                <span class="perm-type-ic" style="--t:{{ $t['color'] }}"><i class="fas {{ $t['icon'] }}"></i></span>
                                <div class="perm-type-info">
                                    <div class="perm-type-row"><b>{{ $t['label'] }}</b><span>{{ number_format($t['count']) }} · {{ $t['pct'] }}%</span></div>
                                    <div class="perm-type-bar"><span style="width:{{ $t['pct'] }}%;background:{{ $t['color'] }}"></span></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 mb-4">
                    <div class="perm-panel h-100">
                        <div class="perm-panel-head">
                            <div>
                                <h3>{{ __('أكثر الأدوار استخداماً') }}</h3>
                                <p>{{ __('الأدوار الأعلى بعدد المستخدمين') }}</p>
                            </div>
                            <a href="{{ route($prefix.'roles.index') }}" class="btn btn-sm btn-light-success font-weight-bolder">{{ __('كل الأدوار') }}</a>
                        </div>
                        <div class="perm-top-roles">
                            @forelse($topRoles as $r)
                            <a class="perm-top-role" href="{{ route($prefix.'roles.edit', $r) }}">
                                <span class="perm-avatar">{{ mb_substr(role_name_ar($r->name), 0, 1) }}</span>
                                <span class="perm-top-role-info">
                                    <b>{{ role_name_ar($r->name) }}</b>
                                    <small>{{ number_format($r->permissions_count) }} {{ __('صلاحية') }} · {{ number_format($r->users_count) }} {{ __('مستخدم') }}</small>
                                </span>
                                <span class="perm-count">{{ number_format($r->users_count) }}</span>
                            </a>
                            @empty
                            <div class="perm-empty"><i class="fas fa-user-tag"></i><span>{{ __('لا توجد أدوار بعد') }}</span></div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- أحدث العناصر --}}
            <div class="row">
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="perm-panel h-100">
                        <div class="perm-panel-head">
                            <div>
                                <h3>{{ __('أحدث المستخدمين') }}</h3>
                                <p>{{ __('آخر المنضمين للنظام') }}</p>
                            </div>
                            <a href="{{ route($prefix.'users.index') }}" class="btn btn-sm btn-light-primary font-weight-bolder">{{ __('عرض الكل') }}</a>
                        </div>
                        <div class="perm-list">
                            @forelse($recentUsers as $u)
                            <div class="perm-row">
                                <span class="perm-avatar">{{ mb_substr($u->name ?? '?', 0, 1) }}</span>
                                <span class="perm-row-info">
                                    <b>{{ $u->name }}</b>
                                    <small>{{ $u->email }}</small>
                                </span>
                                @if($u->roles->count())
                                <span class="badge badge-light-primary">{{ role_name_ar($u->roles->first()->name) }}</span>
                                @else
                                <span class="badge badge-light-warning">{{ __('بدون دور') }}</span>
                                @endif
                            </div>
                            @empty
                            <div class="perm-empty"><i class="fas fa-users"></i><span>{{ __('لا يوجد مستخدمون') }}</span></div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="perm-panel h-100">
                        <div class="perm-panel-head">
                            <div>
                                <h3>{{ __('أحدث الأدوار') }}</h3>
                                <p>{{ __('الأدوار المضافة حديثاً') }}</p>
                            </div>
                            <a href="{{ route($prefix.'roles.index') }}" class="btn btn-sm btn-light-success font-weight-bolder">{{ __('عرض الكل') }}</a>
                        </div>
                        <div class="perm-list">
                            @forelse($recentRoles as $r)
                            <div class="perm-row">
                                <span class="perm-avatar perm-avatar--green">{{ mb_substr(role_name_ar($r->name), 0, 1) }}</span>
                                <span class="perm-row-info">
                                    <b>{{ role_name_ar($r->name) }}</b>
                                    <small>{{ number_format($r->permissions_count) }} {{ __('صلاحية') }} · {{ number_format($r->users_count) }} {{ __('مستخدم') }}</small>
                                </span>
                                <a href="{{ route($prefix.'roles.edit', $r) }}" class="perm-row-btn" title="{{ __('تعديل') }}"><i class="fas fa-pen"></i></a>
                            </div>
                            @empty
                            <div class="perm-empty"><i class="fas fa-user-tag"></i><span>{{ __('لا توجد أدوار') }}</span></div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="perm-panel h-100">
                        <div class="perm-panel-head">
                            <div>
                                <h3>{{ __('أحدث الصلاحيات') }}</h3>
                                <p>{{ __('الصلاحيات المضافة حديثاً') }}</p>
                            </div>
                            <a href="{{ route($prefix.'permissions.index') }}" class="btn btn-sm btn-light-warning font-weight-bolder">{{ __('عرض الكل') }}</a>
                        </div>
                        <div class="perm-list">
                            @forelse($recentPermissions as $p)
                            <div class="perm-row">
                                <span class="perm-avatar perm-avatar--amber"><i class="fas fa-key"></i></span>
                                <span class="perm-row-info">
                                    <b>{{ perm_name_ar($p->name) }}</b>
                                    <small>{{ $p->name }}</small>
                                </span>
                                <span class="badge badge-light-info">{{ number_format($p->roles_count) }} {{ __('دور') }}</span>
                            </div>
                            @empty
                            <div class="perm-empty"><i class="fas fa-key"></i><span>{{ __('لا توجد صلاحيات') }}</span></div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- إجراءات سريعة --}}
            <div class="perm-panel mb-4">
                <div class="perm-panel-head">
                    <div>
                        <h3>{{ __('إجراءات سريعة') }}</h3>
                        <p>{{ __('روابط مباشرة لأهم العمليات والتصدير') }}</p>
                    </div>
                </div>
                <div class="perm-actions">
                    <a href="{{ route($prefix.'users.index') }}" class="perm-action perm-action--blue">
                        <i class="fas fa-users"></i><span>{{ __('المستخدمون') }}</span>
                    </a>
                    <a href="{{ route($prefix.'roles.index') }}" class="perm-action perm-action--green">
                        <i class="fas fa-user-tag"></i><span>{{ __('الأدوار') }}</span>
                    </a>
                    <a href="{{ route($prefix.'permissions.index') }}" class="perm-action perm-action--amber">
                        <i class="fas fa-key"></i><span>{{ __('الصلاحيات') }}</span>
                    </a>
                    <a href="{{ route($prefix.'permissions.create') }}" class="perm-action perm-action--violet">
                        <i class="fas fa-plus"></i><span>{{ __('صلاحية جديدة') }}</span>
                    </a>
                    <a href="{{ route($prefix.'roles.create') }}" class="perm-action perm-action--teal">
                        <i class="fas fa-shield-halved"></i><span>{{ __('دور جديد') }}</span>
                    </a>
                    <a href="{{ route($prefix.'export.users') }}" class="perm-action perm-action--slate">
                        <i class="fas fa-file-excel"></i><span>{{ __('تصدير المستخدمين') }}</span>
                    </a>
                    <a href="{{ route($prefix.'export.roles') }}" class="perm-action perm-action--slate">
                        <i class="fas fa-file-excel"></i><span>{{ __('تصدير الأدوار') }}</span>
                    </a>
                    <a href="{{ route($prefix.'export.permissions') }}" class="perm-action perm-action--slate">
                        <i class="fas fa-file-excel"></i><span>{{ __('تصدير الصلاحيات') }}</span>
                    </a>
                </div>
            </div>

            {{-- إنشاء أدوار سريعة --}}
            <div class="perm-panel">
                <div class="perm-panel-head">
                    <div>
                        <h3>{{ __('إنشاء أدوار سريعة') }}</h3>
                        <p>{{ __('أدخل اسم الدور ثم اختر نوع الصلاحيات المراد منحها') }}</p>
                    </div>
                </div>
                <div class="perm-quick">
                    @foreach([
                        ['type' => 'view',   'label' => __('عرض فقط'),    'hint' => __('index, show, view'),   'icon' => 'fa-eye',   'class' => 'perm-quick--blue'],
                        ['type' => 'store',  'label' => __('إضافة فقط'),  'hint' => __('store, create'),       'icon' => 'fa-plus',  'class' => 'perm-quick--green'],
                        ['type' => 'update', 'label' => __('تعديل فقط'),  'hint' => __('update, edit'),        'icon' => 'fa-pen',   'class' => 'perm-quick--amber'],
                        ['type' => 'delete', 'label' => __('حذف فقط'),    'hint' => __('delete, destroy'),     'icon' => 'fa-trash', 'class' => 'perm-quick--red'],
                        ['type' => 'all',    'label' => __('جميع الصلاحيات'), 'hint' => __('كل صلاحيات النظام'), 'icon' => 'fa-crown', 'class' => 'perm-quick--violet'],
                    ] as $quick)
                    <form action="{{ route($prefix.'roles.create-by-type') }}" method="POST" class="perm-quick-card {{ $quick['class'] }}">
                        @csrf
                        <input type="hidden" name="type" value="{{ $quick['type'] }}">
                        <div class="perm-quick-head">
                            <span class="perm-quick-ic"><i class="fas {{ $quick['icon'] }}"></i></span>
                            <div>
                                <b>{{ $quick['label'] }}</b>
                                <small>{{ $quick['hint'] }}</small>
                            </div>
                        </div>
                        <div class="input-group perm-quick-input">
                            <input type="text" class="form-control" name="role_name" placeholder="{{ __('اسم الدور') }}" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn"><i class="fas fa-check"></i></button>
                            </div>
                        </div>
                    </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
:root {
    --perm-blue: #3699FF;
    --perm-green: #1BC5BD;
    --perm-amber: #FFA800;
    --perm-red: #F64E60;
    --perm-violet: #8950FC;
    --perm-slate: #7E8299;
}
.perm-body { padding: 1rem 1.4rem 1.5rem; }
.perm-kpis { margin-bottom: .25rem; }
.perm-kpis > div { margin-bottom: 1.25rem; }
.perm-kpi {
    position: relative; height: 100%; padding: 1.15rem 1.25rem;
    border-radius: 16px; background: #fff;
    border: 1px solid #edf0f5;
    box-shadow: 0 8px 24px rgba(24, 28, 50, .06);
    overflow: hidden; transition: transform .25s ease, box-shadow .25s ease;
}
.perm-kpi::before {
    content: ''; position: absolute; inset: 0 0 auto 0; height: 4px;
    background: var(--k);
}
.perm-kpi::after {
    content: ''; position: absolute; width: 130px; height: 130px; border-radius: 50%;
    background: var(--k); opacity: .07; inset-inline-end: -40px; top: -50px;
}
.perm-kpi:hover { transform: translateY(-5px); box-shadow: 0 16px 34px rgba(24, 28, 50, .11); }
.perm-kpi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: .6rem; }
.perm-kpi-icon {
    width: 44px; height: 44px; border-radius: 13px; display: inline-flex;
    align-items: center; justify-content: center; font-size: 1.05rem;
    color: var(--k); background: #eef6ff; background: color-mix(in srgb, var(--k) 14%, #fff);
    box-shadow: inset 0 0 0 1px rgba(54, 153, 255, .1);
}
.perm-kpi-link { font-size: .74rem; font-weight: 800; color: var(--k); }
.perm-kpi-link i { transition: transform .2s ease; }
.perm-kpi-link:hover i { transform: translateX(-3px); }
.perm-kpi-num { font-size: 2rem; font-weight: 900; color: #181C32; line-height: 1.1; }
.perm-kpi-num small { font-size: .95rem; font-weight: 800; color: #7E8299; }
.perm-kpi-label { font-size: .82rem; font-weight: 700; color: #7E8299; margin-top: .15rem; }
.perm-kpi-alert {
    font-size: .7rem; font-weight: 800; padding: .2rem .6rem; border-radius: 999px;
    color: var(--perm-red); background: rgba(246, 78, 96, .12);
}
.perm-kpi-bar { height: 6px; border-radius: 999px; background: #f1f4f9; margin: .85rem 0 .5rem; overflow: hidden; }
.perm-kpi-bar span { display: block; height: 100%; border-radius: 999px; background: var(--k); }
.perm-kpi-meta { display: flex; align-items: center; justify-content: space-between; font-size: .74rem; color: #7E8299; font-weight: 600; gap: .5rem; }
.perm-kpi-meta b { color: var(--k); font-weight: 900; }
.perm-kpi--blue { --k: var(--perm-blue); }
.perm-kpi--green { --k: var(--perm-green); }
.perm-kpi--amber { --k: var(--perm-amber); }
.perm-kpi--violet { --k: var(--perm-violet); }
.perm-panel {
    background: #fff; border: 1px solid #edf0f5; border-radius: 16px;
    box-shadow: 0 8px 24px rgba(24, 28, 50, .06); padding: 1.15rem 1.25rem;
}
.perm-panel-head {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 1rem; padding-bottom: .85rem; margin-bottom: .9rem; border-bottom: 1px dashed #eef1f6;
}
.perm-panel-head h3 { font-size: 1rem; font-weight: 900; color: #181C32; margin: 0; }
.perm-panel-head p { font-size: .76rem; color: #9aa1b5; margin: .15rem 0 0; font-weight: 600; }
.perm-types { display: flex; flex-direction: column; gap: .95rem; }
.perm-type { display: flex; align-items: center; gap: .8rem; }
.perm-type-ic {
    width: 42px; height: 42px; border-radius: 12px; flex: none; font-size: .95rem;
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--t); background: #f4f7ff;
}
.perm-type-info { flex: 1; min-width: 0; }
.perm-type-row { display: flex; align-items: center; justify-content: space-between; gap: .6rem; margin-bottom: .35rem; }
.perm-type-row b { font-size: .85rem; font-weight: 800; color: #181C32; }
.perm-type-row span { font-size: .75rem; font-weight: 800; color: #7E8299; }
.perm-type-bar { height: 8px; border-radius: 999px; background: #f1f4f9; overflow: hidden; }
.perm-type-bar span { display: block; height: 100%; border-radius: 999px; min-width: 4px; }
.perm-top-roles { display: flex; flex-direction: column; gap: .55rem; }
.perm-top-role {
    display: flex; align-items: center; gap: .7rem; padding: .6rem .7rem; border-radius: 12px;
    border: 1px solid #f1f4f9; background: #fcfdff; color: #181C32; transition: all .2s ease;
}
.perm-top-role:hover { background: #f8fbff; border-color: #dbe8ff; transform: translateX(-3px); }
.perm-top-role-info { display: flex; flex-direction: column; min-width: 0; flex: 1; }
.perm-top-role-info b { font-size: .85rem; font-weight: 800; }
.perm-top-role-info small { font-size: .72rem; color: #9aa1b5; font-weight: 600; }
.perm-count {
    min-width: 40px; padding: .22rem .5rem; border-radius: 999px; text-align: center;
    font-size: .76rem; font-weight: 900; color: var(--perm-blue); background: rgba(54, 153, 255, .12);
}
.perm-bars { display: flex; flex-direction: column; gap: .85rem; }
.perm-bar-top { display: flex; align-items: center; justify-content: space-between; gap: .6rem; margin-bottom: .35rem; }
.perm-bar-name { display: flex; align-items: center; gap: .45rem; font-size: .84rem; font-weight: 800; color: #181C32; }
.perm-bar-name i { color: var(--perm-slate); }
.perm-bar-counts { font-size: .74rem; font-weight: 700; color: #9aa1b5; }
.perm-bar-counts b { color: #181C32; font-weight: 900; }
.perm-bar-track { height: 9px; border-radius: 999px; background: #f1f4f9; overflow: hidden; }
.perm-bar-fill {
    display: block; height: 100%; border-radius: 999px; min-width: 6px;
    background: linear-gradient(90deg, var(--perm-blue), var(--perm-violet));
}
.perm-rank {
    min-width: 26px; height: 26px; border-radius: 9px; display: inline-flex;
    align-items: center; justify-content: center; font-size: .74rem; font-weight: 900;
    color: #7E8299; background: #f4f6fa;
}
.perm-rank--top { color: #fff; background: linear-gradient(135deg, var(--perm-amber), #ff7d00); }
.perm-list { display: flex; flex-direction: column; gap: .55rem; max-height: 420px; overflow-y: auto; padding-inline-end: .15rem; }
.perm-list::-webkit-scrollbar { width: 6px; }
.perm-list::-webkit-scrollbar-thumb { background: #dfe3ec; border-radius: 999px; }
.perm-row {
    display: flex; align-items: center; gap: .7rem; padding: .6rem .7rem;
    border-radius: 12px; border: 1px solid #f1f4f9; background: #fcfdff;
    transition: background .2s ease, border-color .2s ease, transform .2s ease;
}
.perm-row:hover { background: #f8fbff; border-color: #dbe8ff; transform: translateX(-3px); }
.perm-avatar {
    width: 38px; height: 38px; border-radius: 12px; flex: none; font-weight: 900;
    display: inline-flex; align-items: center; justify-content: center; font-size: .95rem;
    color: var(--perm-blue); background: rgba(54, 153, 255, .12);
}
.perm-avatar--green { color: var(--perm-green); background: rgba(27, 197, 189, .12); }
.perm-avatar--amber { color: var(--perm-amber); background: rgba(255, 168, 0, .14); }
.perm-row-info { display: flex; flex-direction: column; min-width: 0; flex: 1; }
.perm-row-info b { font-size: .85rem; font-weight: 800; color: #181C32; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.perm-row-info small { font-size: .72rem; color: #9aa1b5; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.perm-row-btn {
    width: 30px; height: 30px; border-radius: 9px; flex: none; font-size: .72rem;
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--perm-blue); background: #f2f8ff; transition: all .2s ease;
}
.perm-row-btn:hover { color: #fff; background: var(--perm-blue); }
.perm-empty {
    display: flex; flex-direction: column; align-items: center; gap: .5rem;
    padding: 1.75rem; color: #b5bccd; font-size: .82rem; font-weight: 700;
}
.perm-empty i { font-size: 1.6rem; opacity: .6; }
.perm-actions { display: grid; grid-template-columns: repeat(auto-fill, minmax(165px, 1fr)); gap: .7rem; }
.perm-action {
    --a: var(--perm-blue);
    display: flex; align-items: center; gap: .6rem; padding: .8rem 1rem; border-radius: 13px;
    font-size: .82rem; font-weight: 800; color: #181C32;
    border: 1px solid #edf0f5; background: #fcfdff; transition: all .2s ease;
}
.perm-action i { color: var(--a); font-size: .95rem; }
.perm-action:hover { border-color: var(--a); color: var(--a); transform: translateY(-3px); background: #f8fbff; }
.perm-action--blue { --a: var(--perm-blue); }
.perm-action--green { --a: var(--perm-green); }
.perm-action--amber { --a: var(--perm-amber); }
.perm-action--violet { --a: var(--perm-violet); }
.perm-action--teal { --a: #0BB783; }
.perm-action--slate { --a: var(--perm-slate); }
.perm-quick { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: .8rem; }
.perm-quick-card {
    --a: var(--perm-blue);
    border: 1px solid #edf0f5; border-radius: 14px; padding: .9rem 1rem; background: #fcfdff;
    transition: border-color .2s ease, box-shadow .2s ease;
}
.perm-quick-card:hover { border-color: var(--a); box-shadow: 0 10px 22px rgba(24, 28, 50, .07); }
.perm-quick-head { display: flex; align-items: center; gap: .6rem; margin-bottom: .75rem; }
.perm-quick-ic {
    width: 36px; height: 36px; border-radius: 11px; flex: none;
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--a); background: #f4f7ff; font-size: .9rem;
}
.perm-quick-head b { display: block; font-size: .85rem; font-weight: 900; color: #181C32; }
.perm-quick-head small { display: block; font-size: .7rem; color: #9aa1b5; font-weight: 600; }
.perm-quick-input .form-control { border-inline-end: 0; height: 38px; font-size: .82rem; }
.perm-quick-input .form-control:focus { box-shadow: none; border-color: var(--a); }
.perm-quick-input .btn { height: 38px; width: 42px; color: #fff; background: var(--a); }
.perm-quick--blue { --a: var(--perm-blue); }
.perm-quick--green { --a: var(--perm-green); }
.perm-quick--amber { --a: var(--perm-amber); }
.perm-quick--red { --a: var(--perm-red); }
.perm-quick--violet { --a: var(--perm-violet); }

/* الوضع الليلي — يعتمد على الكلاس body.app-theme-dark المستخدم في المشروع */
body.app-theme-dark .perm-kpi,
body.app-theme-dark .perm-panel { background: #2b2b40; border-color: #323248; box-shadow: none; }
body.app-theme-dark .perm-kpi-num,
body.app-theme-dark .perm-panel-head h3,
body.app-theme-dark .perm-row-info b,
body.app-theme-dark .perm-bar-name,
body.app-theme-dark .perm-quick-head b,
body.app-theme-dark .perm-type-row b,
body.app-theme-dark .perm-top-role-info b,
body.app-theme-dark .perm-top-role { color: #e4e6ef; }
body.app-theme-dark .perm-kpi-num small,
body.app-theme-dark .perm-kpi-meta,
body.app-theme-dark .perm-row-info small,
body.app-theme-dark .perm-top-role-info small { color: #a2a5b8; }
body.app-theme-dark .perm-panel-head { border-color: #323248; }
body.app-theme-dark .perm-row,
body.app-theme-dark .perm-top-role,
body.app-theme-dark .perm-action,
body.app-theme-dark .perm-quick-card { background: #252536; border-color: #323248; }
body.app-theme-dark .perm-row:hover,
body.app-theme-dark .perm-top-role:hover { background: #323248; border-color: #3f3f5c; }
body.app-theme-dark .perm-action { color: #e4e6ef; }
body.app-theme-dark .perm-action:hover { background: #323248; }
body.app-theme-dark .perm-bar-track,
body.app-theme-dark .perm-type-bar,
body.app-theme-dark .perm-kpi-bar { background: #323248; }
body.app-theme-dark .perm-rank { background: #323248; color: #a2a5b8; }
body.app-theme-dark .perm-row-btn { background: rgba(54, 153, 255, .14); }
body.app-theme-dark .perm-quick-ic,
body.app-theme-dark .perm-type-ic { background: rgba(255, 255, 255, .06); }
body.app-theme-dark .perm-quick-input .form-control { background: #2b2b40; border-color: #323248; color: #e4e6ef; }

@media (max-width: 767.98px) {
    .perm-body { padding: .75rem .5rem 1rem; }
    .perm-kpi-num { font-size: 1.6rem; }
    .perm-types { gap: .7rem; }
    .perm-panel { padding: 1rem; }
    .perm-quick { grid-template-columns: 1fr; }
}
</style>
@endpush

