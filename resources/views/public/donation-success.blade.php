@extends('layouts.app')
@section('content')
<div class="panel">
    <h1>تم تسجيل المساهمة</h1>
    <p>بارك الله في عطائك. ستتابع الإدارة المساهمة بما يحفظ الأمانة والخصوصية.</p>
    <p>رمز المتابعة: <strong>{{ $code }}</strong></p>
    <p>الحالة الحالية: <x-status-badge :status="$status" /></p>
</div>
@endsection
