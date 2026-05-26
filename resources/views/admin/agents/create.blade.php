@extends('layouts.app')

@section('content')
<div class="panel">
    <h1>إضافة عضو توزيع</h1>

    <form method="post" action="{{ route('admin.agents.store') }}">
        @csrf

        @include('admin.agents.form')

        <div class="actions">
            <button type="submit">حفظ الحساب</button>
            <a class="btn secondary" href="{{ route('admin.agents.index') }}">رجوع</a>
        </div>
    </form>
</div>
@endsection
