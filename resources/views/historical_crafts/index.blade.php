@extends('layout.master')

@section('title', 'البيانات التاريخية للحرف')

@section('content')
    <div class="container">
        <h3>البيانات التاريخية للحرف</h3>
        <form method="get" class="form-inline mb-3">
            <div class="form-group mr-2">
                <label class="mr-2">العمود</label>
                <select name="column" class="form-control">
                    <option value="">-- اختر --</option>
                    @foreach ($columns as $col)
                        <option value="{{ $col }}" {{ isset($column) && $column == $col ? 'selected' : '' }}>
                            {{ $col }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mr-2">
                <label class="mr-2">القيمة</label>
                <input name="value" value="{{ $value ?? '' }}" class="form-control" />
            </div>
            <button class="btn btn-primary">بحث</button>
            <a href="{{ route('historical_crafts.index') }}" class="btn btn-secondary ml-2">مسح</a>
        </form>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>المصدر</th>
                    <th>رقم الملف</th>
                    <th>المالك</th>
                    <th>الحالة</th>
                    <th>العنوان</th>
                    <th>ملخص</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    @php
                        $record = $item->data ?? [];
                        $fullName = trim(
                            implode(
                                ' ',
                                array_filter(
                                    [
                                        $record['first_name'] ?? null,
                                        $record['father_name'] ?? null,
                                        $record['grand_name'] ?? null,
                                        $record['family_name'] ?? null,
                                    ],
                                    fn($value) => !empty($value),
                                ),
                            ),
                        );
                        $fileNo = $record['file_no'] ?? null;
                        $address = $record['address'] ?? ($record['craft_address'] ?? null);
                        $note = !empty($record['note']) ? $record['note'] : null;
                        $oner = !empty($record['oner']) ? $record['oner'] : null;
                        $balance = !empty($record['balance']) ? $record['balance'] : null;
                        $status = $record['craft_status'] ?? ($record['status'] ?? null);
                        $summary = trim(
                            implode(
                                ' | ',
                                array_filter(
                                    [
                                        !empty($note) ? 'note: ' . $note : null,
                                        !empty($oner) ? 'oner: ' . $oner : null,
                                        !empty($address) ? 'address: ' . $address : null,
                                        !empty($balance) ? 'balance: ' . $balance : null,
                                        !empty($fileNo) ? 'file_no: ' . $fileNo : null,
                                    ],
                                    fn($value) => !empty($value),
                                ),
                            ),
                        );
                    @endphp
                    <tr>
                        <td>حرف وصناعات</td>
                        <td>{{ !empty($fileNo) ? $fileNo : '—' }}</td>
                        <td>{{ !empty($fullName) ? $fullName : '—' }}</td>
                        <td>{{ !empty($status) ? $status : '—' }}</td>
                        <td>{{ !empty($address) ? $address : '—' }}</td>
                        <td>{{ !empty($summary) ? $summary : '—' }}</td>
                        <td>{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $items->links() }}
    </div>

@endsection
