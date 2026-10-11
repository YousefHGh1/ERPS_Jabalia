{{-- @if ($message = Session::get('success'))
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>&nbsp;&nbsp;
    </div>
@endif
@if ($message = Session::get('danger'))
    <div class="alert alert-danger alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>&nbsp;&nbsp;
    </div>
@endif
@if ($message = Session::get('warning'))
    <div class="alert alert-warning alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>&nbsp;&nbsp;
    </div>
@endif
@if ($message = Session::get('info'))
    <div class="alert alert-info alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>&nbsp;&nbsp;
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        تحقق من الأخطاء الموجودة : &nbsp;
    </div>
@endif --}}

@section('css')
    <style>
        .swal2-popup .swal2-icon {
            margin: 0 !important;
        }
    </style>
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (Session::has('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var message = {!! json_encode(__(\Session::get('success'))) !!};
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: message,
                showConfirmButton: true,
                confirmButtonText: 'حسناً',
                timer: 3000,
            });
        });
    </script>
@endif

@if (Session::has('danger'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var message = {!! json_encode(__(\Session::get('danger'))) !!};
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: message,
                showConfirmButton: true,
                confirmButtonText: 'حسناً',
                timer: 3000,
            });
        });
    </script>
@endif

@if (Session::has('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var message = {!! json_encode(__(\Session::get('warning'))) !!};
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: message,
                showConfirmButton: true,
                confirmButtonText: 'حسناً',
                timer: 3000,
            });
        });
    </script>
@endif

@if (Session::has('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var message = {!! json_encode(__(\Session::get('info'))) !!};
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: message,
                showConfirmButton: true,
                confirmButtonText: 'حسناً',
                timer: 3000,
            });
        });
    </script>
@endif

@if (Session::has('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var message = {!! json_encode(__(\Session::get('error'))) !!};
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: message,
                showConfirmButton: true,
                confirmButtonText: 'حسناً',
                timer: 3000,
            });
        });
    </script>
@endif
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var errorMessages = [];
            @foreach ($errors->all() as $error)
                errorMessages.push({!! json_encode(__($error)) !!});
            @endforeach
            
            var message = errorMessages.length > 0 ? errorMessages.join('<br>') : 'انتبه تأكد من صحة البيانات';
            
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: 'تحذير',
                html: message,
                showConfirmButton: true,
                confirmButtonText: 'حسناً',
                timer: 4000,
            });
        });
    </script>
@endif
