@extends('layouts.app')
@section('content')
<div class="actions" style="justify-content:space-between">
    <h1>أدمنز المناطق</h1>
    <a class="btn" href="{{ route('admin.admin-users.create') }}">إضافة أدمن منطقة</a>
</div>

<table class="table">
    <thead><tr><th>الاسم</th><th>البريد</th><th>النطاق</th><th>التعهد</th><th></th></tr></thead>
    <tbody>
    @foreach($admins as $admin)
        <tr>
            <td>{{ $admin->name }}</td>
            <td>{{ $admin->email }}</td>
            <td>{{ $admin->area?->name ?? 'أدمن عام' }}</td>
            <td>{{ $admin->pledge_accepted_at ? 'مقبول' : 'بانتظار' }}</td>
            <td><a class="btn secondary" href="{{ route('admin.admin-users.edit', $admin) }}">تعديل</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $admins->links() }}
@endsection
