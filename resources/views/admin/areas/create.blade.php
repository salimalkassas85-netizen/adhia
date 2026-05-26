@extends('layouts.app')

@section('content')
<div class="panel">
    <h1>إضافة منطقة</h1>

    <form method="post" action="{{ route('admin.areas.store') }}">
        @csrf

        @include('admin.areas.form')

        <div class="actions">
            <button type="submit">حفظ</button>
            <a class="btn secondary" href="{{ route('admin.areas.index') }}">رجوع</a>
        </div>
    </form>
</div>
@endsection
