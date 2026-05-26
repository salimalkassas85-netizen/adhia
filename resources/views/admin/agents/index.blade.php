@extends('layouts.app')
@section('content')
<div class="actions"><h1>فريق التوزيع</h1><a class="btn" href="{{ route('admin.agents.create') }}">إضافة حساب</a></div>
<table class="table"><thead><tr><th>الاسم</th><th>البريد</th><th>المنطقة</th><th>التعهد</th><th></th></tr></thead><tbody>@foreach($agents as $agent)<tr><td>{{ $agent->name }}</td><td>{{ $agent->email }}</td><td>{{ $agent->area?->name ?? 'كل المناطق عند الإسناد' }}</td><td>{{ $agent->pledge_accepted_at ? 'مقبول' : 'بانتظار' }}</td><td><a href="{{ route('admin.agents.edit',$agent) }}">تعديل</a></td></tr>@endforeach</tbody></table>{{ $agents->links() }}
@endsection
