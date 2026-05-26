@extends('layouts.app')
@section('content')
<div class="panel">
    <h1>تعديل أدمن</h1>
    <form method="post" action="{{ route('admin.admin-users.update', $adminUser) }}">
        @csrf
        @method('put')
        @include('admin.admin-users.form')
        <button>تحديث</button>
    </form>
</div>
@endsection
