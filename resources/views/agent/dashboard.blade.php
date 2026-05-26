@extends('layouts.app')
@section('content')
<h1>لوحة فريق التوزيع</h1>
<div class="stats">
    <div class="card stat"><span>مسندة إليك</span><strong>{{ $assignedCount }}</strong></div>
    <div class="card stat"><span>في الطريق</span><strong>{{ $onTheWayCount }}</strong></div>
    <div class="card stat"><span>تم التسليم</span><strong>{{ $deliveredCount }}</strong></div>
    <div class="card stat"><span>استلامات مسندة</span><strong>{{ $pickupCount }}</strong></div>
</div>
<div class="actions">
    <a class="btn" href="{{ route('agent.requests.index') }}">عرض طلبات التوزيع</a>
    <a class="btn secondary" href="{{ route('agent.pickups.index') }}">عرض استلامات المساهمات</a>
</div>
@endsection
