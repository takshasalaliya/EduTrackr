@extends('admin.layout')

@section('title', 'WhatsApp Device Management')
{{-- @section('page_title', 'Manage WhatsApp Devices') --}} {{-- Keeping this commented --}}

@section('subject_table') {{-- Keeping this section name as per your usage --}}

{{-- Manual Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
    <h1 class="h2 page-header-title"><i class="bi bi-whatsapp me-2"></i>WhatsApp Device Management</h1>
    {{-- No "Add" button here as the add form is on this page --}}
</div>

<div class="container-fluid">

    {{-- Session Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Add Device Form --}}
    <div class="card card-custom mb-4">
        <div class="card-header">
            <i class="bi bi-plus-circle-fill me-2"></i>Add New WhatsApp Device
        </div>
        <div class="card-body">
            <form action="{{ url('whatsapp') }}" method="get"> {{-- Assuming GET method for adding based on your original form --}}
                {{-- @csrf --}} {{-- Not strictly needed for GET, but doesn't harm --}}
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="add_device_name" class="form-label">Device Name <span class="text-danger">*</span></label>
                        <input type="text" id="add_device_name" name="device" class="form-control @error('device') is-invalid @enderror" value="{{ old('device') }}" placeholder="e.g., Marketing Phone" required>
                        @error('device') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="add_device_number" class="form-label">Mobile Number (with country code) <span class="text-danger">*</span></label>
                        <input type="number" id="add_device_number" name="number" class="form-control @error('number') is-invalid @enderror" value="{{ old('number') }}" placeholder="e.g., 919876543210" required>
                        @error('number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-lg me-2"></i>Add Device
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Registered Devices List --}}
    <div class="card card-custom">
        <div class="card-header">
            <i class="bi bi-list-ul me-2"></i>Registered WhatsApp Devices
        </div>
        <div class="card-body p-0">
            @if(isset($datas) && count($datas) > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Device Name</th>
                            <th scope="col">Registered Number</th>
                            <th scope="col">Remaining Messages (Quota)</th>
                            <th scope="col">Status</th>
                            <th scope="col">Token</th>
                            @if(isset($otp_message) && $otp_message == 'yes')
                            <th scope="col" style="min-width: 250px;">OTP Verification</th>
                            @endif
                            {{-- Add other actions if needed, e.g., Delete Device --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['name'] ?? 'N/A' }}</td>
                            <td>{{ $row['device'] ?? 'N/A' }}</td>
                            <td>{{ $row['quota'] ?? 'N/A' }}</td>
                            <td>
                                @if(isset($row['status']) && $row['status'] == 'disconnect')
                                    <a href="{{ url('/whatsapp_img/'.$row['token'].'/'.$row['device']) }}" class="btn btn-sm btn-warning" title="Reconnect - Click to see QR">
                                        <i class="bi bi-qr-code-scan me-1"></i> Disconnected
                                    </a>
                                @elseif(isset($row['status']) && $row['status'] == 'connect')
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Connected</span>
                                @else
                                    <span class="badge bg-secondary">{{ $row['status'] ?? 'Unknown' }}</span>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark text-truncate" style="max-width: 150px;" title="{{ $row['token'] ?? 'N/A' }}">{{ $row['token'] ?? 'N/A' }}</span></td>

                            @if(isset($otp_message) && $otp_message == 'yes')
                            <td>
                                @if(isset($otp) && $otp) {{-- Assuming $otp is specific to a row or context, this might need adjustment --}}
                                    <p class="mb-1"><strong class="text-success">Current OTP: {{ $otp }}</strong></p>
                                @endif
                                <form action="{{ url('/otp_whatsapp/'.$row['name']) }}" method="get" class="d-flex gap-1">
                                    <input type="number" name="otp" class="form-control form-control-sm @error('otp') is-invalid @enderror" placeholder="Enter OTP" style="max-width: 120px;" required>
                                    <input type="hidden" value="{{ $row['token'] ?? '' }}" name="token">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Submit OTP</button>
                                    @error('otp') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center p-4">
                <i class="bi bi-hdd-stack-fill display-4 text-muted mb-3"></i>
                <p class="text-muted mb-0">No WhatsApp devices have been added yet.</p>
                <p class="small text-muted mt-2">Use the form above to add a new device.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- QR Code Display Area --}}
    @if(isset($img) && $img)
    <div class="card card-custom mt-4">
        <div class="card-header">
            <i class="bi bi-qr-code me-2"></i>Scan QR Code to Connect Device
        </div>
        <div class="card-body text-center">
            <p class="text-muted">Scan this QR code with your WhatsApp application on the device you are trying to connect.</p>
            <img src="data:image/png;base64,{{ $img }}" alt="WhatsApp QR Code" class="img-fluid border rounded" style="max-width: 300px;">
            <p class="mt-3">
                <small>If the QR code has expired or is not working, try clicking the "Disconnected" button again for a new one.</small>
            </p>
        </div>
    </div>
    @endif
</div>
@endsection