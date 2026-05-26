@extends('layouts.app')
@section('content')
<div class="actions" style="justify-content:space-between">
    <h1>الإشعارات</h1>
    <div class="actions">
        @if($unreadCount > 0)
            <form method="post" action="{{ route('admin.notifications.markAllRead') }}">@csrf<button class="btn secondary">تحديد الكل كمقروء</button></form>
        @endif
        <a class="btn secondary" href="{{ route('admin.dashboard') }}">لوحة الإدارة</a>
    </div>
</div>

@forelse($notifications as $notification)
    <div class="panel" style="margin-bottom:10px;{{ $notification->read_at ? 'opacity:.7' : 'border-right:4px solid var(--accent)' }}">
        <div class="actions" style="justify-content:space-between;margin-top:0">
            <div>
                <strong>{{ $notification->data['title'] ?? 'إشعار' }}</strong>
                <span class="privacy" style="margin-right:10px">{{ $notification->created_at->diffForHumans() }}</span>
                @if(!$notification->read_at)
                    <span class="badge ok">جديد</span>
                @endif
            </div>
        </div>
        <p style="margin:8px 0">{{ $notification->data['body'] ?? '' }}</p>
        <div class="actions" style="margin-top:8px">
            @if(!empty($notification->data['url']))
                <form method="post" action="{{ route('admin.notifications.markAsRead', $notification->id) }}">@csrf<button class="btn">فتح</button></form>
            @endif
            @if(!$notification->read_at && empty($notification->data['url']))
                <form method="post" action="{{ route('admin.notifications.markAsRead', $notification->id) }}">@csrf<button class="btn secondary">تحديد كمقروء</button></form>
            @endif
        </div>
    </div>
@empty
    <div class="panel">
        <p class="privacy" style="text-align:center;padding:30px 0">لا توجد إشعارات بعد.</p>
    </div>
@endforelse

<div style="margin-top:14px">{{ $notifications->links() }}</div>
@endsection
