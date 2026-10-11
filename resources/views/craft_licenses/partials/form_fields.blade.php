<div class="row">
    <!-- Right Column (Main Data) -->
    <div class="col-lg-9">
        <!-- Section 1: License Basics -->
        <div class="card card-custom gutter-b shadow-none border">
            <div class="card-header h-auto py-3 border-0">
                <div class="card-title">
                    <h3 class="card-label font-weight-bolder text-primary">Ø¨ÙŠØ§Ù†Ø§Øª Ø§Ù„Ù…Ù„Ù Ø§Ù„Ø£Ø³Ø§Ø³ÙŠØ©</h3>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>DEBT_2017</label>
                            <input type="hidden" name="status" id="status" value="{{ old('status', $license->status ?? 'undefined') }}">
                            <input type="number" name="total_debts" class="form-control border-primary"
                                value="{{ old('total_debts', $license->total_debts ?? 0) }}" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Ø±Ù‚Ù… Ø§Ù„Ù…Ù„Ù</label>
                            <div class="input-group">
                                <input type="text" name="file_number" id="file_number"
                                    class="form-control font-weight-bold"
                                    value="{{ old('file_number', $license->file_number ?? '') }}"
                                    {{ isset($license) && $license->exists ? 'readonly' : '' }} required maxlength="6"
                                    style="{{ !(isset($license) && $license->exists) ? 'background-color: #fffac1;' : '' }}">
                                @if (!(isset($license) && $license->exists))
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" type="button"
                                            onclick="generateFileNumber()">ØªÙˆÙ„ÙŠØ¯</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Ø±Ù‚Ù… Ø§Ù„Ø¬Ø¨Ø§ÙŠØ©</label>
                            <input type="text" name="tax_number" class="form-control"
                                value="{{ old('tax_number', $license->tax_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Ø¢Ø®Ø± Ø±Ø®ØµØ©</label>
                            <input type="text" name="last_license" class="form-control"
                                value="{{ old('last_license', $license->last_license ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Owner & Property Info -->
        <div class="card card-custom gutter-b shadow-none border">
            <div class="card-header h-auto py-3 border-0">
                <div class="card-title">
                    <h3 class="card-label font-weight-bolder text-primary">Ø¨ÙŠØ§Ù†Ø§Øª ØµØ§Ø­Ø¨ Ø§Ù„Ø­Ø±ÙØ© ÙˆØ§Ù„Ø¹Ù‚Ø§Ø±</h3>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Ø±Ù‚Ù… Ù‡ÙˆÙŠØ© ØµØ§Ø­Ø¨ Ø§Ù„Ø­Ø±ÙØ©</label>
                            <input type="text" name="id_number" class="form-control bg-light-info"
                                value="{{ old('id_number', $license->id_number ?? '') }}" maxlength="9">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Ø§Ø³Ù… ØµØ§Ø­Ø¨ Ø§Ù„Ø­Ø±ÙØ©</label>
                            <input type="text" name="owner_name" class="form-control"
                                value="{{ old('owner_name', $license->owner_name ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ø±Ù‚Ù… Ø§Ù„Ù‡Ø§ØªÙ/Ø¬ÙˆØ§Ù„</label>
                            <input type="text" name="phone_number" class="form-control"
                                value="{{ old('phone_number', $license->phone_number ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Ø±Ù‚Ù… Ù‡ÙˆÙŠØ© Ù…Ø§Ù„Ùƒ Ø§Ù„Ø¹Ù‚Ø§Ø±</label>
                            <input type="text" name="property_owner_id" class="form-control bg-light-info"
                                value="{{ old('property_owner_id', $license->property_owner_id ?? '') }}"
                                maxlength="9">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Ù…Ø§Ù„Ùƒ Ø§Ù„Ø¹Ù‚Ø§Ø±</label>
                            <input type="text" name="property_owner" class="form-control"
                                value="{{ old('property_owner', $license->property_owner ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ø¬Ù‡Ø© Ø§Ù„Ù…Ù„ÙƒÙŠØ©</label>
                            <select name="ownership_entity" class="form-control">
                                <option value="">Ø§Ø®ØªØ±...</option>
                                <option value="citizen"
                                    {{ old('ownership_entity', $license->ownership_entity ?? '') == 'citizen' ? 'selected' : '' }}>
                                    Ø§Ù„Ù…ÙˆØ§Ø·Ù†</option>
                                <option value="municipality"
                                    {{ old('ownership_entity', $license->ownership_entity ?? '') == 'municipality' ? 'selected' : '' }}>
                                    Ø¨Ù„Ø¯ÙŠØ© Ø¬Ø¨Ø§Ù„ÙŠØ§ Ø§Ù„Ù†Ø²Ù„Ø©</option>
                                <option value="awqaf"
                                    {{ old('ownership_entity', $license->ownership_entity ?? '') == 'awqaf' ? 'selected' : '' }}>
                                    ÙˆØ²Ø§Ø±Ø© Ø§Ù„Ø£ÙˆÙ‚Ø§Ù</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Craft Details -->
        <div class="card card-custom gutter-b shadow-none border">
            <div class="card-header h-auto py-3 border-0">
                <div class="card-title">
                    <h3 class="card-label font-weight-bolder text-primary">ØªÙØ§ØµÙŠÙ„ Ø§Ù„Ø­Ø±ÙØ©</h3>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ØªØµÙ†ÙŠÙ Ø§Ù„Ø­Ø±ÙØ©</label>
                            <select name="classification" id="classification" class="form-control selectpicker"
                                data-live-search="true" required>
                                <option value="">Ø§Ø®ØªØ± Ø§Ù„ØªØµÙ†ÙŠÙ...</option>
                                <option value="Ù…Ù‡Ù†ÙŠØ©" {{ old('classification', $license->classification ?? '') == 'Ù…Ù‡Ù†ÙŠØ©' ? 'selected' : '' }}>1 - Ù…Ù‡Ù†ÙŠØ©</option>
                                <option value="Ù…Ù‡Ù†ÙŠØ© Ù…ÙˆØ§ØµÙ„Ø§Øª" {{ old('classification', $license->classification ?? '') == 'Ù…Ù‡Ù†ÙŠØ© Ù…ÙˆØ§ØµÙ„Ø§Øª' ? 'selected' : '' }}>2 - Ù…Ù‡Ù†ÙŠØ© Ù…ÙˆØ§ØµÙ„Ø§Øª</option>
                                <option value="ØµÙ†Ø§Ø¹ÙŠØ©" {{ old('classification', $license->classification ?? '') == 'ØµÙ†Ø§Ø¹ÙŠØ©' ? 'selected' : '' }}>3 - ØµÙ†Ø§Ø¹ÙŠØ©</option>
                                <option value="ØªØ¬Ø§Ø±ÙŠØ©" {{ old('classification', $license->classification ?? '') == 'ØªØ¬Ø§Ø±ÙŠØ©' ? 'selected' : '' }}>4 - ØªØ¬Ø§Ø±ÙŠØ©</option>
                                <option value="Ù…Ø·Ø§Ø¹Ù…/Ù…Ø·Ø§Ø¨Ø®" {{ old('classification', $license->classification ?? '') == 'Ù…Ø·Ø§Ø¹Ù…/Ù…Ø·Ø§Ø¨Ø®' ? 'selected' : '' }}>5 - Ù…Ø·Ø§Ø¹Ù…/Ù…Ø·Ø§Ø¨Ø®</option>
                                <option value="Ù…Ø®Ø§Ø¨Ø²" {{ old('classification', $license->classification ?? '') == 'Ù…Ø®Ø§Ø¨Ø²' ? 'selected' : '' }}>6 - Ù…Ø®Ø§Ø¨Ø²</option>
                                <option value="Ù…Ø­Ø±ÙˆÙ‚Ø§Øª" {{ old('classification', $license->classification ?? '') == 'Ù…Ø­Ø±ÙˆÙ‚Ø§Øª' ? 'selected' : '' }}>7 - Ù…Ø­Ø±ÙˆÙ‚Ø§Øª</option>
                                <option value="ØµØ§Ù„Ø§Øª ÙˆÙ…Ù†Ø§Ø³Ø¨Ø§Øª" {{ old('classification', $license->classification ?? '') == 'ØµØ§Ù„Ø§Øª ÙˆÙ…Ù†Ø§Ø³Ø¨Ø§Øª' ? 'selected' : '' }}>8 - ØµØ§Ù„Ø§Øª ÙˆÙ…Ù†Ø§Ø³Ø¨Ø§Øª</option>
                                <option value="Ø®Ø¯Ù…Ø§ØªÙŠØ©" {{ old('classification', $license->classification ?? '') == 'Ø®Ø¯Ù…Ø§ØªÙŠØ©' ? 'selected' : '' }}>9 - Ø®Ø¯Ù…Ø§ØªÙŠØ©</option>
                                <option value="ØºØ°Ø§Ø¦ÙŠØ© A" {{ old('classification', $license->classification ?? '') == 'ØºØ°Ø§Ø¦ÙŠØ© A' ? 'selected' : '' }}>10 - ØºØ°Ø§Ø¦ÙŠØ© A</option>
                                <option value="ØºØ°Ø§Ø¦ÙŠØ© B" {{ old('classification', $license->classification ?? '') == 'ØºØ°Ø§Ø¦ÙŠØ© B' ? 'selected' : '' }}>11 - ØºØ°Ø§Ø¦ÙŠØ© B</option>
                                <option value="ØªØ­Ù„ÙŠØ© Ù…ÙŠØ§Ù‡" {{ old('classification', $license->classification ?? '') == 'ØªØ­Ù„ÙŠØ© Ù…ÙŠØ§Ù‡' ? 'selected' : '' }}>12 - ØªØ­Ù„ÙŠØ© Ù…ÙŠØ§Ù‡</option>
                                <option value="Ø·Ø¨ÙŠØ©" {{ old('classification', $license->classification ?? '') == 'Ø·Ø¨ÙŠØ©' ? 'selected' : '' }}>13 - Ø·Ø¨ÙŠØ©</option>
                                <option value="Ø²Ø±Ø§Ø¹ÙŠØ©" {{ old('classification', $license->classification ?? '') == 'Ø²Ø±Ø§Ø¹ÙŠØ©' ? 'selected' : '' }}>14 - Ø²Ø±Ø§Ø¹ÙŠØ©</option>
                                <option value="Ù…ØµØ§Ù†Ø¹ ØºØ°Ø§Ø¦ÙŠØ©" {{ old('classification', $license->classification ?? '') == 'Ù…ØµØ§Ù†Ø¹ ØºØ°Ø§Ø¦ÙŠØ©' ? 'selected' : '' }}>15 - Ù…ØµØ§Ù†Ø¹ ØºØ°Ø§Ø¦ÙŠØ©</option>
                                <option value="ÙŠØ§ÙØ·Ø§Øª Ùˆ Ø§Ø¹Ù„Ø§Ù†Ø§Øª" {{ old('classification', $license->classification ?? '') == 'ÙŠØ§ÙØ·Ø§Øª Ùˆ Ø§Ø¹Ù„Ø§Ù†Ø§Øª' ? 'selected' : '' }}>16 - ÙŠØ§ÙØ·Ø§Øª Ùˆ Ø§Ø¹Ù„Ø§Ù†Ø§Øª</option>
                                <option value="Ù…ÙˆÙ„Ø¯Ø§Øª" {{ old('classification', $license->classification ?? '') == 'Ù…ÙˆÙ„Ø¯Ø§Øª' ? 'selected' : '' }}>17 - Ù…ÙˆÙ„Ø¯Ø§Øª</option>
                                <option value="Ù…Ø·Ø§Ø­Ù†" {{ old('classification', $license->classification ?? '') == 'Ù…Ø·Ø§Ø­Ù†' ? 'selected' : '' }}>0 - Ù…Ø·Ø§Ø­Ù†</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2" style="display:none;">
                        <div class="form-group">
                            <input type="hidden" name="craft_type" id="craft_type" value="Ø¬Ø§Ø±ÙŠ">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ù†ÙˆØ¹ Ø§Ù„Ø­Ø±ÙØ© Ø§Ù„ØªÙØµÙŠÙ„ÙŠ</label>
                            <select name="detailed_craft_type" id="detailed_craft_type" class="form-control selectpicker"
                                data-live-search="true" required>
                                @if (isset($license) && $license->exists && $license->detailed_craft_type)
                                    <option value="{{ $license->detailed_craft_type }}" selected>
                                        {{ $license->detailed_craft_type }}
                                    </option>
                                @else
                                    <option value="">Ø§Ø®ØªØ± Ø§Ù„ØªØµÙ†ÙŠÙ Ø£ÙˆÙ„Ø§Ù‹...</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>ØªØ§Ø±ÙŠØ® ØªØ³Ø¬ÙŠÙ„ Ø§Ù„Ù…Ù„Ù</label>
                            <input type="date" name="registration_date" class="form-control"
                                value="{{ old('registration_date', $license->registration_date ?? date('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="text-success font-weight-bold">Ù‚ÙŠÙ…Ø© Ø§Ù„Ø¯ÙØ¹Ø© </label>
                            <input type="number" name="payment_amount" id="payment_amount"
                                class="form-control border-success bg-light-success font-weight-bolder"
                                value="{{ old('payment_amount', $license->payment_amount ?? 0) }}" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>ØªØ§Ø±ÙŠØ® Ø¢Ø®Ø± Ø¯ÙØ¹Ø©</label>
                            <input type="date" name="last_payment_date" class="form-control"
                                value="{{ old('last_payment_date', $license->last_payment_date ?? '') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ø³Ù†Ø© Ø£ÙˆÙ„ ØªØ±Ø®ÙŠØµ</label>
                            <input type="text" name="first_license_year" class="form-control"
                                value="{{ old('first_license_year', $license->first_license_year ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ø£ÙŠØ§Ù… Ø§Ù„Ø¹Ø·Ù„Ø©</label>
                            <select name="holiday_days" class="form-control">
                                <option value="">Ø§Ø®ØªØ± Ø§Ù„ÙŠÙˆÙ…...</option>
                                <option value="Saturday"
                                    {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Saturday' ? 'selected' : '' }}>
                                    Ø§Ù„Ø³Ø¨Øª</option>
                                <option value="Sunday"
                                    {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Sunday' ? 'selected' : '' }}>
                                    Ø§Ù„Ø£Ø­Ø¯</option>
                                <option value="Monday"
                                    {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Monday' ? 'selected' : '' }}>
                                    Ø§Ù„Ø§Ø«Ù†ÙŠÙ†</option>
                                <option value="Tuesday"
                                    {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Tuesday' ? 'selected' : '' }}>
                                    Ø§Ù„Ø«Ù„Ø§Ø«Ø§Ø¡</option>
                                <option value="Wednesday"
                                    {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Wednesday' ? 'selected' : '' }}>
                                    Ø§Ù„Ø£Ø±Ø¨Ø¹Ø§Ø¡</option>
                                <option value="Thursday"
                                    {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Thursday' ? 'selected' : '' }}>
                                    Ø§Ù„Ø®Ù…ÙŠØ³</option>
                                <option value="Friday"
                                    {{ old('holiday_days', $license->holiday_days ?? 'Friday') == 'Friday' ? 'selected' : '' }}>
                                    Ø§Ù„Ø¬Ù…Ø¹Ø©</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ø§Ù„Ø¹Ù„Ø§Ù…Ø© Ø§Ù„ØªØ¬Ø§Ø±ÙŠØ©</label>
                            <input type="text" name="commercial_name" class="form-control"
                                value="{{ old('commercial_name', $license->commercial_name ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ù…Ø´ØªØºÙ„ Ù…Ø±Ø®Øµ</label>
                            <input type="text" name="licensed_operator" class="form-control"
                                value="{{ old('licensed_operator', $license->licensed_operator ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ù‡Ù„ Ø§Ù„Ø­Ø±ÙØ© Ù…Ø±Ø®Ù‘ÙŽØµØ©ØŸ</label>
                            <select name="is_licensed" class="form-control">
                                <option value="1"
                                    {{ old('is_licensed', isset($license) && $license->exists ? (string) ($license->is_licensed ? 1 : 0) : '1') === '1' ? 'selected' : '' }}>
                                    Ù†Ø¹Ù…</option>
                                <option value="0" {{ old('is_licensed', isset($license) && $license->exists ? (string) ($license->is_licensed ? 1 : 0) : '1') === '0' ? 'selected' : '' }}>
                                    Ù„Ø§</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Section 4: Address & Location -->
        <div class="card card-custom gutter-b shadow-none border">
            <div class="card-header h-auto py-3 border-0">
                <div class="card-title">
                    <h3 class="card-label font-weight-bolder text-primary">Ø§Ù„Ø¹Ù†ÙˆØ§Ù† ÙˆØ§Ù„Ù…ÙˆÙ‚Ø¹</h3>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ø§Ù„Ø­ÙŠ</label>
                            <input type="text" name="neighborhood" class="form-control"
                                value="{{ old('neighborhood', $license->neighborhood ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ø§Ù„Ø´Ø§Ø±Ø¹ Ø§Ù„Ø±Ø¦ÙŠØ³ÙŠ</label>
                            <input type="text" name="main_street" class="form-control"
                                value="{{ old('main_street', $license->main_street ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ø¹Ù†ÙˆØ§Ù† Ø§Ù„Ø­Ø±ÙØ©</label>
                            <input type="text" name="address" class="form-control"
                                value="{{ old('address', $license->address ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ø£Ù‚Ø±Ø¨ Ù…Ø³Ø¬Ø¯</label>
                            <input type="text" name="nearest_mosque" class="form-control"
                                value="{{ old('nearest_mosque', $license->nearest_mosque ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="form-group">
                    <label class="font-weight-bold text-dark">Ù…Ù„Ø§Ø­Ø¸Ø§Øª Ø¥Ø¶Ø§ÙÙŠØ©</label>
                    <textarea name="notes" class="form-control border-dark" rows="3">{{ old('notes', $license->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Left Column (Financials & GIS) -->
    <div class="col-lg-3">
        <!-- Section: Financials -->
        <div class="card card-custom gutter-b shadow-none border bg-light">
            <div class="card-header h-auto py-3 border-0">
                <div class="card-title">
                    <h3 class="card-label font-weight-bolder text-success">Ù…Ø·Ø§Ù„Ø¨Ø© Ù…Ø§Ù„ÙŠØ©</h3>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="form-group">
                    <label>Ø¯ÙŠÙˆÙ† Ø­ØªÙ‰ 2019</label>
                    <input type="number" name="total_debts" class="form-control border-primary"
                        value="{{ old('total_debts', $license->total_debts ?? 0) }}">
                </div>
                <div class="form-group">
                    <label>Ø±Ø³ÙˆÙ… Ø§Ù„Ø­Ø±ÙØ©</label>
                    <input type="number" name="craft_fees" id="craft_fees"
                        class="form-control bg-light-warning font-weight-bold"
                        value="{{ old('craft_fees', $license->craft_fees ?? 0) }}" readonly>
                </div>
                <div class="form-group">
                    <label>Ø±Ø³ÙˆÙ… Ø§Ù„ÙŠØ§ÙØ·Ø©</label>
                    <input type="number" name="signboard_fees" class="form-control border-primary"
                        value="{{ old('signboard_fees', $license->signboard_fees ?? 0) }}">
                </div>
                <hr>
                <div class="form-group">
                    <label class="text-danger font-weight-bolder">Ø§Ù„Ù…ØªØ¨Ù‚ÙŠ</label>
                    <input type="number" name="remaining"
                        class="form-control bg-light-danger font-weight-bolder text-danger"
                        value="{{ old('remaining', $license->remaining ?? 0) }}" readonly>
                </div>
                <div class="form-group">
                    <label>Ø§Ù„Ù…ØªØ¨Ù‚ÙŠ Ø³Ù†ÙˆØ§Øª</label>
                    <input type="text" name="remaining_years" class="form-control"
                        value="{{ old('remaining_years', $license->remaining_years ?? '') }}">
                </div>
            </div>
        </div>

        <!-- Section: Advertising Board -->
        <div class="card card-custom gutter-b shadow-none border bg-light">
            <div class="card-header h-auto py-3 border-0">
                <div class="card-title">
                    <h3 class="card-label font-weight-bolder text-info">Ø§Ù„Ù„ÙˆØ­Ø© Ø§Ù„Ø¥Ø¹Ù„Ø§Ù†ÙŠØ©</h3>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="form-group">
                    <label>Ø§Ù„Ù„ÙˆØ­Ø© Ø§Ù„Ø¥Ø¹Ù„Ø§Ù†ÙŠØ©</label>
                    <input type="text" name="advertisement_board" class="form-control"
                        value="{{ old('advertisement_board', $license->advertisement_board ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Ø¹Ø¯Ø¯ Ø§Ù„Ù…Ø®Ø§Ø²Ù†</label>
                    <select name="number_of_stores" class="form-control">
                        <option value="">Ø§Ø®ØªØ±...</option>
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}"
                                {{ old('number_of_stores', $license->number_of_stores ?? '') == $i ? 'selected' : '' }}>
                                {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label>Ø§Ù„Ù„ÙˆØ­Ø© Ø¨Ø§Ù„Ù…ØªØ±</label>
                    <input type="text" name="board_in_meters" class="form-control"
                        value="{{ old('board_in_meters', $license->board_in_meters ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Ø§Ù„Ø³Ø¹Ø± Ø§Ù„Ù†Ù‡Ø§Ø¦ÙŠ</label>
                    <input type="number" name="final_price" class="form-control font-weight-bold"
                        value="{{ old('final_price', $license->final_price ?? 0) }}">
                </div>
            </div>
        </div>

        <!-- Section: GIS -->
        <div class="card card-custom shadow-none border bg-light">
            <div class="card-header h-auto py-3 border-0">
                <div class="card-title">
                    <h3 class="card-label font-weight-bolder text-dark">Ø¨ÙŠØ§Ù†Ø§Øª GIS</h3>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Ø±Ù‚Ù… Ø§Ù„Ù…Ø¨Ù†Ù‰</label>
                            <input type="text" name="gis_building_number" class="form-control"
                                value="{{ old('gis_building_number', $license->gis_building_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Ø±Ù‚Ù… Ø§Ù„Ø´Ø§Ø±Ø¹</label>
                            <input type="text" name="street_number" class="form-control"
                                value="{{ old('street_number', $license->street_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Ø±Ù‚Ù… Ø§Ù„Ø·Ø§Ø¨Ù‚</label>
                            <input type="text" name="floor_number" class="form-control"
                                value="{{ old('floor_number', $license->floor_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Ø±Ù‚Ù… Ø§Ù„ÙˆØ­Ø¯Ø©</label>
                            <input type="text" name="unit_number" class="form-control"
                                value="{{ old('unit_number', $license->unit_number ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
