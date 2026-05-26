@extends('layouts.app')
@section('content')
<div class="auth-shell panel">
    <h1>دخول فريق المبادرة</h1>
    <form method="post" action="{{ route('login.store') }}">
        @csrf
        <div class="field"><label>البريد الإلكتروني</label><input name="email" type="email" value="{{ old('email') }}" required></div>
        <div class="field"><label>كلمة المرور</label><input name="password" type="password" required></div>
        <label><input style="width:auto" type="checkbox" name="remember" value="1"> تذكرني</label>
        <div class="actions"><button type="submit">دخول</button></div>
    </form>
</div>
@endsection
