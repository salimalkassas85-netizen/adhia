@extends('layouts.app')
@section('content')
<div class="actions"><h1>المناطق</h1><a class="btn" href="{{ route('admin.areas.create') }}">إضافة منطقة</a></div>
<table class="table"><thead><tr><th>الاسم</th><th>الحالة</th><th></th></tr></thead><tbody>@foreach($areas as $area)<tr><td>{{ $area->name }}</td><td>{{ $area->active ? 'نشطة' : 'متوقفة' }}</td><td><a href="{{ route('admin.areas.edit',$area) }}">تعديل</a></td></tr>@endforeach</tbody></table>{{ $areas->links() }}
@endsection
