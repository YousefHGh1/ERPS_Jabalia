@extends('layout.master')

@section('title')
    {{ __('تعديل الطلب') }}
@endsection

@section('page_title')
    {{ __('controll_panel') }}
@endsection
@section('sub_main')
    {{ __('home') }}
@endsection
@section('sub_title')
    {{ __('الطلبات') }} / {{ __('تعديل') }} #{{ $requestModel->id }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-end mb-5">
            <a href="{{ route('requests.index') }}" class="btn btn-light-danger font-weight-bold">
                <i class="fas fa-arrow-right"></i> {{ __('عودة للقائمة') }}
            </a>
        </div>
        <div class="request-edit-page-wrap">
            @include('requests.partials.edit_request_form_inner', [
                'requestModel' => $requestModel,
                'requestTypes' => $requestTypes,
                'mokalafs' => $mokalafs,
                'fieldPrefix' => '',
                'layoutMode' => 'page',
                'nestedInModal' => false,
            ])
        </div>
    </div>
@endsection

@push('js')
@include('requests.partials.fee_amount_modal_script')
@include('requests.partials.edit_request_interactions_script')
<script>
    $(function () {
        bindRequestEditInteractions($('.request-edit-page-wrap'));
    });
</script>
@endpush
