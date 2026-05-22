<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h1 class="h5 mb-0">Settings & branding</h1>
            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.dashboard') }}">Back</a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <div class="col-12 col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="institute_name">Institute / platform name</label>
                            <input class="form-control" id="institute_name" name="institute_name" type="text" value="{{ old('institute_name', $values['institute_name']) }}" required>
                            <x-input-error :messages="$errors->get('institute_name')" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="primary_color">Primary color</label>
                            <input class="form-control form-control-color" id="primary_color" name="primary_color" type="color" value="{{ old('primary_color', $values['primary_color']) }}">
                            <div class="form-text">Used for buttons/links (more will be added later).</div>
                            <x-input-error :messages="$errors->get('primary_color')" />
                        </div>

                        <div class="mb-0">
                            <label class="form-label" for="logo">Logo</label>
                            <input class="form-control" id="logo" name="logo" type="file" accept="image/*">
                            <div class="form-text">PNG/SVG/JPG. Keep it small for low-end devices.</div>
                            <x-input-error :messages="$errors->get('logo')" />
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top">
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="fw-semibold mb-2">Preview</div>

                        <div class="d-flex align-items-center gap-2">
                            @php
                                $logoPath = old('logo_path', $values['logo_path']);
                            @endphp
                            @if ($logoPath)
                                <img src="{{ asset('storage/'.$logoPath) }}" alt="Logo" style="width: 32px; height: 32px; object-fit: contain;">
                            @else
                                <x-application-logo style="width:32px;height:32px;" />
                            @endif
                            <div class="fw-semibold">{{ old('institute_name', $values['institute_name']) }}</div>
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-sm" type="button" style="background: {{ old('primary_color', $values['primary_color']) }}; color: #fff;">
                                Primary color
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-admin-layout>
