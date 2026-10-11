@extends('layout.master')

@section('title')
    {{ __('job') }}
@endsection

@section('page_title')
    {{ __('jobs') }}
@endsection

@section('sub_main')
    {{ __('home') }}
@endsection

@section('sub_title')
    {{ __('job') }} #{{ $job->id }}
@endsection

@section('content')
    <div class="container-fluid">
            {{-- ترويسة الصفحة — موحّدة مع هوية القسم --}}
            <x-page-header
                title="{{ $job->name }}"
                subtitle="عرض تفاصيل المسمى الوظيفي"
            >
                <x-slot name="actions">
                    <a href="{{ route('jobs.index') }}" class="btn btn-light-primary font-weight-bolder mr-2">
                                            <i class="fas fa-arrow-right"></i> العودة
                                        </a>
                </x-slot>
            </x-page-header>
        <div class="card card-custom gutter-b shadow-sm border-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">المعلومات العامة</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>{{ __('id') }}:</strong></td>
                                <td>{{ $job->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('name') }}:</strong></td>
                                <td>{{ $job->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('category') }}:</strong></td>
                                <td>{{ $job->category->name ?? __('N/A') }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('created_at') }}:</strong></td>
                                <td>{{ $job->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                            @if ($job->updated_at && $job->updated_at->ne($job->created_at))
                                <tr>
                                    <td><strong>{{ __('updated_at') }}:</strong></td>
                                    <td>{{ $job->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">تفاصيل إضافية</h6>
                        <div class="p-4 bg-light rounded">
                            <p class="mb-2"><strong>{{ __('job') }}:</strong> {{ $job->name }}</p>
                            <p class="mb-2"><strong>{{ __('category') }}:</strong>
                                {{ $job->category->name ?? __('N/A') }}</p>
                            <p class="mb-2"><strong>{{ __('created_at') }}:</strong>
                                {{ $job->created_at->format('Y-m-d') }}</p>
                            @if ($job->updated_at && $job->updated_at->ne($job->created_at))
                                <p class="mb-0"><strong>{{ __('updated_at') }}:</strong>
                                    {{ $job->updated_at->format('Y-m-d') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/pages/widgets.js') }}"></script>
@endsection
