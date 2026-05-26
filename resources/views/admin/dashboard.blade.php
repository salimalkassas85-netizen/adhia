@extends('layouts.app')
@section('content')
<div class="actions" style="justify-content:space-between">
    <div>
        <h1>لوحة الإدارة</h1>
        <p class="privacy">
            نطاق العرض:
            <strong>{{ $isGlobalAdmin ? 'كل المناطق' : ($currentArea?->name ?? 'منطقة غير محددة') }}</strong>
        </p>
    </div>
    <div class="actions">
        <a class="btn" href="{{ route('admin.beneficiary-requests.index') }}">طلبات الهدية</a>
        <a class="btn gold" href="{{ route('admin.donations.index') }}">المساهمات</a>
    </div>
</div>

<div class="stats">
    <a class="card stat" href="{{ route('admin.beneficiary-requests.index') }}"><span>إجمالي الطلبات</span><strong>{{ $totalRequests }}</strong></a>
    <a class="card stat" href="{{ route('admin.beneficiary-requests.index', ['status' => 'pending']) }}"><span>طلبات جديدة</span><strong>{{ $pendingRequests }}</strong></a>
    <a class="card stat" href="{{ route('admin.beneficiary-requests.index') }}"><span>مرتبطة بمساهمة</span><strong>{{ $linkedRequests }}</strong></a>
    <a class="card stat" href="{{ route('admin.beneficiary-requests.index', ['status' => 'delivered']) }}"><span>تم التسليم</span><strong>{{ $deliveredRequests }}</strong></a>
    <a class="card stat" href="{{ route('admin.donations.index') }}"><span>إجمالي المساهمات</span><strong>{{ $totalDonations }}</strong></a>
    <a class="card stat" href="{{ route('admin.donations.index', ['status' => 'pending']) }}"><span>مساهمات جديدة</span><strong>{{ $pendingDonations }}</strong></a>
    <a class="card stat" href="{{ route('admin.donations.index', ['status' => 'received']) }}"><span>استلام أو تسليم</span><strong>{{ $confirmedDonations }}</strong></a>
</div>

<div class="panel" style="margin-top:18px">
    <div class="actions" style="justify-content:space-between">
        <h2>الإشعارات</h2>
        <div class="actions" style="margin-top:0">
            @if($unreadNotificationsCount > 0)
                <span class="badge warn">{{ $unreadNotificationsCount }} غير مقروء</span>
            @endif
            <a href="{{ route('admin.notifications.index') }}">عرض الكل</a>
        </div>
    </div>
    @forelse($notifications as $notification)
        <p style="{{ $notification->read_at ? 'opacity:.6' : '' }}">
            <strong>{{ $notification->data['title'] ?? 'إشعار' }}</strong>
            <span class="privacy">{{ $notification->created_at->diffForHumans() }}</span><br>
            {{ $notification->data['body'] ?? '' }}
            @if(!empty($notification->data['url']))
                <a href="{{ $notification->data['url'] }}">فتح</a>
            @endif
        </p>
    @empty
        <p class="privacy">لا توجد إشعارات بعد.</p>
    @endforelse
</div>

<div class="grid grid-2" style="margin-top:18px">
    <div class="panel">
        <div class="actions" style="justify-content:space-between"><h2>أحدث طلبات هدية العيد</h2><a href="{{ route('admin.beneficiary-requests.index') }}">عرض الكل</a></div>
        <div class="table-responsive">
        <table class="table">
            <thead><tr><th>الرمز</th><th>المنطقة</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
            @forelse($recentRequests as $request)
                <tr>
                    <td>{{ $request->code }}</td>
                    <td>{{ $request->area?->name }}</td>
                    <td><x-status-badge :status="$request->status" /></td>
                    <td><a href="{{ route('admin.beneficiary-requests.show', $request) }}">فتح</a></td>
                </tr>
            @empty
                <tr><td colspan="4">لا توجد طلبات بعد.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="panel">
        <div class="actions" style="justify-content:space-between"><h2>أحدث المساهمات</h2><a href="{{ route('admin.donations.index') }}">عرض الكل</a></div>
        <div class="table-responsive">
        <table class="table">
            <thead><tr><th>الرمز</th><th>المنطقة</th><th>الحالة</th><th></th></tr></thead>
            <tbody>
            @forelse($recentDonations as $donation)
                <tr>
                    <td>{{ $donation->code }}</td>
                    <td>{{ $donation->targetArea?->name ?? $donation->donorArea?->name ?? 'تحتاج تخصيص' }}</td>
                    <td><x-status-badge :status="$donation->status" /></td>
                    <td><a href="{{ route('admin.donations.show', $donation) }}">فتح</a></td>
                </tr>
            @empty
                <tr><td colspan="4">لا توجد مساهمات بعد.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

<div class="grid grid-2" style="margin-top:18px">
    <div class="panel">
        <h2>الطلبات حسب المنطقة</h2>
        @forelse($requestsByArea as $row)
            <p>{{ $row->area?->name ?? 'غير محدد' }}: <strong>{{ $row->total }}</strong></p>
        @empty
            <p class="privacy">لا توجد بيانات بعد.</p>
        @endforelse
    </div>
    <div class="panel">
        <h2>المساهمات حسب المنطقة</h2>
        @forelse($donationsByArea as $row)
            <p>{{ $row->targetArea?->name ?? 'غير مخصصة' }}: <strong>{{ $row->total }}</strong></p>
        @empty
            <p class="privacy">لا توجد بيانات بعد.</p>
        @endforelse
    </div>
</div>

<div class="panel" style="margin-top:18px">
    <h2>نظرة العجز والفائض حسب المنطقة</h2>
    <div class="table-responsive">
    <table class="table">
        <thead><tr><th>المنطقة</th><th>طلبات</th><th>كيلو لحم مؤكد تقريبًا</th><th>المؤشر</th></tr></thead>
        <tbody>
        @foreach($areas as $area)
            @php $coverage = (float) ($area->meat_kg_sum ?? 0); $requests = $area->beneficiary_requests_count; @endphp
            <tr>
                <td>{{ $area->name }}</td>
                <td>{{ $requests }}</td>
                <td>{{ $coverage }}</td>
                <td>{{ $coverage >= $requests ? 'تغطية/فائض' : 'عجز يحتاج متابعة' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection
