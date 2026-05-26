@extends('layouts.app')
@section('content')
<div class="panel">
    <h1>إضافة أدمن منطقة</h1>
    <form method="post" action="{{ route('admin.admin-users.store') }}">
        @csrf
        @include('admin.admin-users.form')
        <button>حفظ الحساب</button>
    </form>
</div>
@endsection
