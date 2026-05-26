@extends('layouts.app')
@section('content')
<div class="auth-shell">
    <div class="panel">
        <h1>متابعة تبرعات طلب الهدية</h1>
        <p class="lead">أدخل رقم الطلب لمعرفة إجمالي التبرعات المرتبطة بطلبك وحالة كل تبرع على حدة.</p>

        <form method="post" action="{{ route('public.request.status.lookup') }}">
            @csrf
            <div class="field">
                <label for="code">رقم الطلب</label>
                <input id="code" name="code" value="{{ old('code') }}" placeholder="مثال: REQ-XXXX" required>
                @error('code')<p class="errors">{{ $message }}</p>@enderror
                <p class="hint">رقم الطلب ظهر لك بعد تسجيل طلب هدية العيد.</p>
            </div>
            <button type="submit">عرض حالة الطلب</button>
        </form>

        <p class="privacy" style="margin-top:14px">لا تعرض هذه الصفحة بيانات المتبرعين حفاظًا على الخصوصية.</p>
    </div>
</div>
@endsection
