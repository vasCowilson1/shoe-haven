@extends('admin.layouts.admin')

@section('title', ($product ? 'Edit' : 'Add') . ' Product — ' . config('app.name', 'Shoe Haven'))
@section('page-title', $product ? 'Edit Product' : 'Add Product')

@push('styles')
<style>
    .sh-preview { width: 120px; height: 120px; object-fit: cover; border-radius: 10px; border: 2px dashed #ddd; background: #f9f9f9; }
    .sh-preview-wrap { position: relative; display: inline-block; }
    .sh-img-input { font-size: .85rem; }
    .sh-dropzone {
        border: 2px dashed #ccc;
        border-radius: 10px;
        background: #fff;
        padding: 1rem .5rem;
        cursor: pointer;
        transition: border-color .15s ease, background .15s ease;
    }
    .sh-dropzone:hover, .sh-dropzone.sh-dragover { border-color: #f97316; background: #fff7ed; }
    .sh-dropzone p { margin: 0; }
</style>
@endpush

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-3">
        <form action="{{ $product ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($product) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-8">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold">Product Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $product?->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold">Brand</label>
                            <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror"
                                   value="{{ old('brand', $product?->brand) }}" required>
                            @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label small fw-semibold">Price (UGX)</label>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price', $product?->price) }}" required min="0">
                            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label small fw-semibold">Original Price</label>
                            <input type="number" name="original_price" class="form-control @error('original_price') is-invalid @enderror"
                                   value="{{ old('original_price', $product?->original_price) }}" min="0">
                            @error('original_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label small fw-semibold">Discount (%)</label>
                            <input type="number" name="discount" class="form-control @error('discount') is-invalid @enderror"
                                   value="{{ old('discount', $product?->discount) }}" min="0" max="100">
                            @error('discount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold">Category</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Select</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product?->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-semibold">Stock Quantity</label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                   value="{{ old('stock', $product?->stock?->quantity ?? 0) }}" required min="0">
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_new" class="form-check-input" id="isNew"
                                       value="1" {{ old('is_new', $product?->is_new) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-semibold" for="isNew">Mark as New Arrival</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded-3 p-3 text-center bg-light">
                        <label class="form-label small fw-semibold d-block">Product Image</label>
                        <div class="sh-preview-wrap mb-2">
                            <img src="{{ old('image', $product?->image) ? (Str::startsWith(old('image', $product?->image ?? ''), 'http') ? old('image', $product?->image) : asset(old('image', $product?->image ?? ''))) : 'https://via.placeholder.com/120' }}"
                                 alt="" class="sh-preview" id="imagePreview"
                                 onerror="this.src='https://via.placeholder.com/120'">
                        </div>

                        <div class="sh-dropzone" id="dropZone">
                            <i class="bi bi-cloud-arrow-up fs-3 text-muted"></i>
                            <p class="small text-muted mb-1" id="dropZoneText">Drag &amp; drop image here</p>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="browseBtn">Browse files</button>
                            <input type="file" name="image_file" id="imageFile" class="d-none" accept="image/*">
                        </div>
                        @error('image_file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                        <div class="text-muted small mt-2">— or paste an image URL (leave blank to keep the current image) —</div>
                        <input type="text" name="image" class="form-control form-control-sm sh-img-input @error('image') is-invalid @enderror"
                               value="{{ old('image') }}"
                               placeholder="https://example.com/image.jpg"
                               oninput="updatePreview()">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-12">
                    <hr class="my-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sh-orange btn-sm">
                            <i class="bi bi-check-lg"></i> {{ $product ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('imageFile');
        const browseBtn = document.getElementById('browseBtn');
        const dropZoneText = document.getElementById('dropZoneText');
        const preview = document.getElementById('imagePreview');
        const placeholder = 'https://via.placeholder.com/120';

        function updatePreview() {
            const urlInput = document.querySelector('input[name="image"]');
            preview.src = (urlInput && urlInput.value.trim())
                ? urlInput.value.trim()
                : placeholder;
        }

        function setFile(file) {
            if (!file || !file.type.startsWith('image/')) return;
            try {
                const transfer = new DataTransfer();
                transfer.items.add(file);
                fileInput.files = transfer.files;
            } catch (e) {}
            dropZoneText.textContent = file.name;
            preview.src = URL.createObjectURL(file);
        }

        browseBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            fileInput.click();
        });

        dropZone.addEventListener('click', function () {
            fileInput.click();
        });

        fileInput.addEventListener('change', function () {
            if (this.files.length) setFile(this.files[0]);
        });

        ['dragenter', 'dragover'].forEach(function (ev) {
            dropZone.addEventListener(ev, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('sh-dragover');
            });
        });

        ['dragleave', 'drop'].forEach(function (ev) {
            dropZone.addEventListener(ev, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('sh-dragover');
            });
        });

        dropZone.addEventListener('drop', function (e) {
            if (e.dataTransfer.files.length) setFile(e.dataTransfer.files[0]);
        });
    })();
</script>
@endpush
