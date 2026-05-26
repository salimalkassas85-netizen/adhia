@extends('layouts.app')

@section('content')
<div class="panel">
    <h1>تعديل عضو توزيع</h1>

    <form method="post" action="{{ route('admin.agents.update', $agent) }}">
        @csrf
        @method('put')

        @include('admin.agents.form')

        <div class="actions">
            <button type="submit">تحديث</button>
            <a class="btn secondary" href="{{ route('admin.agents.index') }}">رجوع</a>
        </div>
    </form>
</div>
@endsection
