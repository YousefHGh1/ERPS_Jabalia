{{--
  بطاقة للهيكل الإداري (دائرة / قسم / شعبة)
  الاستخدام: @include('components.admin.structure-card', ['item' => $department, 'type' => 'department', ...])
--}}
@props([
    'item',
    'type' => 'department', // department|section|branch
    'managerName' => null,
    'count' => 0,
    'countLabel' => null,
    'countIcon' => 'fa-th-list',
    'editPermission' => null,
    'deletePermission' => null,
    'editModalTarget' => null,
    'destroyRoute' => null,
    'destroyParam' => null,
])

@php
    $resource = match($type) {
        'branch' => 'branches',
        default => $type . 's',
    };
    $editPermission = $editPermission ?? 'update ' . $resource;
    $deletePermission = $deletePermission ?? 'delete ' . $resource;
    $destroyUrl = $destroyRoute ?? route($resource . '.destroy', $destroyParam ?? $item);
@endphp

<div class="card admin-structure-card h-100">
    <div class="card-body position-relative">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h5 class="card-title font-weight-bolder text-dark mb-0 text-center flex-grow-1">{{ $item->name }}</h5>
            <div class="admin-card-actions">
                @can($editPermission)
                    <button type="button" class="btn btn-icon btn-sm btn-light-primary" data-toggle="modal" data-target="#editModal{{ $item->id }}" title="{{ __('Edit') }}">
                        <i class="fas fa-edit"></i>
                    </button>
                @endcan
                @can($deletePermission)
                    <form action="{{ $destroyUrl }}" method="POST" class="d-inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-icon btn-sm btn-light-danger" onclick="return confirm('{{ __('confirm_message') }}')" title="{{ __('Delete') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                @endcan
            </div>
        </div>
        <x-admin.manager-info :name="$managerName" />
        @if($countLabel !== null)
            <x-admin.count-badge :count="$count" :label="$countLabel" :icon="$countIcon" />
        @endif
    </div>
</div>
