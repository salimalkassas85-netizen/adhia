@extends('layouts.app')

@section('content')
<div class="panel">
    <h1>تعديل منطقة</h1>

    <form method="post" action="{{ route('admin.areas.update', $area) }}">
        @csrf
        @method('put')

        @include('admin.areas.form')

        <div class="actions">
            <button type="submit">تحديث</button>
            <a class="btn secondary" href="{{ route('admin.areas.index') }}">رجوع</a>
        </div>
    </form>
</div>
@endsection
