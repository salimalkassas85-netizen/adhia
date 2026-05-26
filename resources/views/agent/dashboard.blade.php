@extends('layouts.app')
@section('content')
<h1>لوحة فريق التوزيع</h1>
<div class="stats">
    <div class="card stat"><span>مسندة إليك</span><strong>{{ $assignedCount }}</strong></div>
    <div class="card stat"><span>تم المعالجة</span><strong>{{ $onTheWayCount }}</strong></div>
    <div class="card stat"><span>تم تسليم الأمانة</span><strong>{{ $deliveredCount }}</strong></div>
</div>
<div class="actions">
    <a class="btn" href="{{ route('agent.requests.index') }}">عرض طلبات التوزيع</a>
</div>
@endsection
