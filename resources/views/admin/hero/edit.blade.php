@extends('admin.layouts.admin')

@section('title', 'Homepage — Admin')
@section('page-title', 'Homepage / Hero')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div><h2 class="h5 fw-bold mb-1">Homepage Hero</h2><p class="text-muted small mb-0">Control the first message and promotional image visitors see.</p></div>
</div>

<form action="{{ route('admin.hero.update') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="row g-4">
    <div class="col-xl-8">
        <div class="card admin-card mb-4">
            <div class="card-header"><h5 class="mb-0 fw-bold">Hero Content</h5></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Headline</label>
                    <input type="text" name="headline" class="form-control @error('headline') is-invalid @enderror" value="{{ old('headline',$hero->headline ?? '') }}" required>
                    <div class="form-text">HTML highlighting such as &lt;span&gt; may be used if your storefront supports it.</div>
                    @error('headline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Subtitle</label>
                    <textarea name="subtitle" rows="4" class="form-control @error('subtitle') is-invalid @enderror" required>{{ old('subtitle',$hero->subtitle ?? '') }}</textarea>
                    @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-semibold">Primary button text</label><input name="button_text" class="form-control" value="{{ old('button_text',$hero->button_text ?? 'Shop Now') }}" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Primary button URL</label><input name="button_url" class="form-control" value="{{ old('button_url',$hero->button_url ?? '/shop') }}" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Secondary button text</label><input name="secondary_button_text" class="form-control" value="{{ old('secondary_button_text',$hero->secondary_button_text ?? 'Explore Collection') }}" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Secondary button URL</label><input name="secondary_button_url" class="form-control" value="{{ old('secondary_button_url',$hero->secondary_button_url ?? '/shop') }}" required></div>
                </div>
            </div>
        </div>

        <div class="card admin-card">
            <div class="card-header"><h5 class="mb-0 fw-bold">Homepage Statistics</h5></div>
            <div class="card-body">
                <div class="row g-3">
                @php $stats=old('stats',$hero->stats ?? []); @endphp
                @for($i=0;$i<3;$i++)
                    @php $stat=is_array($stats)&&isset($stats[$i])?$stats[$i]:[]; @endphp
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 bg-light">
                            <label class="form-label small fw-semibold">Value</label>
                            <input name="stat_value_{{ $i }}" class="form-control mb-2" value="{{ old('stat_value_'.$i,$stat['value']??'') }}" placeholder="15K+">
                            <label class="form-label small fw-semibold">Label</label>
                            <input name="stat_label_{{ $i }}" class="form-control" value="{{ old('stat_label_'.$i,$stat['label']??'') }}" placeholder="Happy Customers">
                        </div>
                    </div>
                @endfor
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card admin-card">
            <div class="card-header"><h5 class="mb-0 fw-bold">Hero Image</h5></div>
            <div class="card-body">
                <div class="rounded-3 overflow-hidden bg-light mb-3" style="aspect-ratio:16/10">
                    @if($hero->image ?? false)
                        <img src="{{ asset($hero->image) }}" id="heroPreview" class="w-100 h-100" style="object-fit:cover" alt="Current hero">
                    @else
                        <div id="heroPreviewEmpty" class="h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image fs-1"></i></div>
                        <img src="" id="heroPreview" class="w-100 h-100 d-none" style="object-fit:cover" alt="Preview">
                    @endif
                </div>
                <label class="form-label fw-semibold">Upload replacement</label>
                <input type="file" name="image" id="heroImage" class="form-control" accept="image/jpeg,image/png,image/webp">
                <div class="form-text">Recommended: wide landscape image, WebP/JPEG/PNG, max 2 MB.</div>
                @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <button class="btn btn-sh-orange w-100 mt-4"><i class="bi bi-check-lg me-1"></i>Save Homepage</button>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('heroImage')?.addEventListener('change', function () {
    const file=this.files[0], preview=document.getElementById('heroPreview'), empty=document.getElementById('heroPreviewEmpty');
    if(!file) return;
    preview.src=URL.createObjectURL(file); preview.classList.remove('d-none'); empty?.classList.add('d-none');
});
</script>
@endpush
