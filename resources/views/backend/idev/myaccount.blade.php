@extends("easyadmin::backend.parent")
@section("content")
@push('mtitle')
{{$title}}
@endpush

<!-- Include Signature Pad Styles -->
@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/signaturepad/signature.css') }}">
@endpush

<div class="pc-container">
  <div class="pc-content">

    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <h4 class="page-title">My Account</h4>
            <p class="text-muted">Update your profile and signature</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6 col-md-8 col-12">
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="card-title mb-0">Profile Information</h5>
          </div>
          <div class="card-body p-3">
            <form id="form-maccount" action="{{url('update-profile')}}" method="post" enctype="multipart/form-data">
              @csrf
              <div class="row">
                @php $method = "create"; @endphp
                @foreach($fields as $key => $field)
                  @if($field['name'] == 'signature')
                    <!-- Custom Signature Field -->
                    <div class="{{ $field['class'] ?? 'col-md-12 my-2' }}">
                      <label class="form-label fw-bold">{{ $field['label'] }}</label>
                      
                      <!-- Current Signature Display -->
                      @if(!empty($field['value']))
                        <div class="current-signature-display mb-3">
                          <small class="text-muted">Current Signature:</small>
                          <br>
                          <img src="{{ $field['value'] }}" alt="Current Signature" class="current-signature">
                        </div>
                      @endif
                      
                      <!-- Signature Creation Options -->
                      <div class="signature-container">
                        <div class="signature-method-selector">
                          <div class="d-flex justify-content-center gap-3">
                            <label class="form-check-label d-flex align-items-center">
                              <input type="radio" name="signature_method" value="draw" class="form-check-input me-2" checked> 
                              <span>✏️ Draw Signature</span>
                            </label>
                            <label class="form-check-label d-flex align-items-center">
                              <input type="radio" name="signature_method" value="upload" class="form-check-input me-2"> 
                              <span>📁 Upload Image</span>
                            </label>
                          </div>
                        </div>
                        
                        <!-- Draw Signature Section -->
                        <div id="draw-signature-section">
                          <div class="text-center mb-2">
                            <small class="text-muted">Draw your signature in the box below</small>
                          </div>
                          <canvas id="signature-pad" class="signature-pad mx-auto d-block" width="400" height="200"></canvas>
                          <div class="signature-actions">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSignature()">
                              <i class="ti ti-refresh"></i> Clear
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="saveSignature()">
                              <i class="ti ti-device-floppy"></i> Save as SVG
                            </button>
                            <button type="button" class="btn btn-sm btn-info" onclick="previewSignature()">
                              <i class="ti ti-eye"></i> Preview
                            </button>
                          </div>
                          <div id="signature-status" class="mt-2 text-center"></div>
                          <div id="signature-preview-draw" class="signature-preview"></div>
                        </div>
                        
                        <!-- Upload Signature Section -->
                        <div id="upload-signature-section" style="display: none;">
                          <div class="upload-zone">
                            <input type="file" name="signature_file" class="form-control mb-2" accept="image/*" onchange="previewUploadedSignature(this)">
                            <small class="text-muted">
                              <i class="ti ti-info-circle"></i> 
                              Upload PNG, JPG, or GIF (max 2MB)
                            </small>
                          </div>
                          <div id="upload-preview" class="signature-preview"></div>
                        </div>
                      </div>
                      
                      <!-- Hidden input to store signature data -->
                      <input type="hidden" name="signature_data" id="signature-data">
                    </div>
                  @else
                    @include('easyadmin::backend.idev.fields.'.$field['type'])
                  @endif
                @endforeach
              </div>
              <hr>
              <div class="d-flex justify-content-end">
                <button type="button" id="btn-for-form-maccount" class="btn btn-primary" onclick="softSubmit('form-maccount','list')">
                  <i class="ti ti-device-floppy"></i> Update Profile
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<!-- Signature Pad CDN -->
<script src="{{ asset('vendor/signaturepad/signature_pad.umd.min.js') }}"></script>
<script src="{{ asset('vendor/signaturepad/signature-initialize.js') }}"></script>
@endpush

@endsection