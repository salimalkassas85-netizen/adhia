<div class="grid grid-2">
    <div class="field">
        <label for="agent-name">الاسم</label>
        <input id="agent-name" name="name" value="{{ old('name', $agent->name ?? '') }}" required>
    </div>

    <div class="field">
        <label for="agent-email">البريد الإلكتروني</label>
        <input id="agent-email" name="email" type="email" value="{{ old('email', $agent->email ?? '') }}" required>
    </div>

    <div class="field">
        <label for="agent-password">كلمة المرور</label>
        <input id="agent-password" name="password" type="password" @if(!isset($agent)) required @endif>
        <p class="hint">اتركها فارغة عند التعديل للاحتفاظ بكلمة المرور الحالية.</p>
    </div>

    <div class="field">
        <label for="agent-area">المنطقة</label>
        <select id="agent-area" name="area_id">
            <option value="">بدون منطقة ثابتة</option>
            @foreach($areas as $area)
                <option value="{{ $area->id }}" @selected(old('area_id', $agent->area_id ?? null) == $area->id)>{{ $area->name }}</option>
            @endforeach
        </select>
        <p class="hint">عضو التوزيع يرى فقط الطلبات التي يسندها له أدمن المنطقة.</p>
    </div>
</div>
