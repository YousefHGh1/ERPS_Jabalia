<select class="form-control form-control-lg" name="type" id="type">
    <option value=""  >اختر</option>
    <option value="مصنف" {{ old('type') === 'مصنف' ? 'selected' : '' }}>مصنف (مثبت) </option>
    <option value="غير مصنف" {{ old('type') === 'غير مصنف' ? 'selected' : '' }}>غير مصنف </option>
    <option value="عقد موازي" {{ old('type') === 'عقد موازي' ? 'selected' : '' }}>عقد موازي
    </option>
    <option value="عقد مقطوع" {{ old('type') === 'عقد مقطوع' ? 'selected' : '' }}> عقد مقطوع </option>
    <option value="عقد خاص" {{ old('type') === 'عقد خاص' ? 'selected' : '' }}>عقد خاص </option>
    <option value="كرت عمل يومي" {{ old('type') === 'كرت عمل يومي' ? 'selected' : '' }}> كرت عمل يومي
    </option>
    <option value="متقاعد" {{ old('type') === 'متقاعد' ? 'selected' : '' }}> متقاعد</option>
</select>
