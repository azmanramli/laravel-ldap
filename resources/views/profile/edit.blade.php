@extends('layouts.dashboard')

@section('page-pretitle', 'Your Information')
@section('page-title', 'Edit Your Profile')

@section('content')
<div class="content">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Update Profile Settings</h3>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            {{ $errors->first() }}
                        </div>
                        @endif

                        @if(session('status') == 'two-factor-authentication-enabled')
                        {{-- Show SVG QR Code, After Enabling 2FA --}}
                        <div class="alert alert-success" id="enable2fa">
                            <p class="mb-2">
                                {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application.') }}
                            </p>
                            <div>{!! auth()->user()->twoFactorQrCodeSvg() !!}</div>
                        </div>
                        <style>
                            #enable2fa svg {
                                border: 10px solid #fff;
                            }
                        </style>
                        @endif

                        {{-- AD-style User Details --}}
                        <div class="row mb-4">
                            <div class="col-md-3 text-center mb-4">
                                <div class="card shadow-sm p-3">
                                    @php
                                        $adPhoto = null;
                                        if (!empty($ldap_attributes['thumbnailphoto'] ?? null)) {
                                            $photoData = is_array($ldap_attributes['thumbnailphoto']) ? $ldap_attributes['thumbnailphoto'][0] : $ldap_attributes['thumbnailphoto'];
                                            if ($photoData) {
                                                $adPhoto = 'data:image/jpeg;base64,' . base64_encode($photoData);
                                            }
                                        }
                                    @endphp
                                    @if ($adPhoto)
                                        <img src="{{ $adPhoto }}" alt="User Photo" class="avatar avatar-xl mb-3 rounded-circle" style="object-fit:cover;width:8rem;height:8rem;" />
                                    @elseif (Auth::user()->avatar)
                                        <img src="{{ asset(Auth::user()->avatar) }}" alt="User Photo" class="avatar avatar-xl mb-3 rounded-circle" style="object-fit:cover;width:8rem;height:8rem;" />
                                    @else
                                        <img src="https://api.proxeuse.com/avatars/api/?name={{ urlencode(Auth::user()->name) }}&color=fff&background={{ substr(md5(Auth::user()->name), 0, 6) }}&size=500" alt="User Photo" class="avatar avatar-xl mb-3 rounded-circle" style="object-fit:cover;width:8rem;height:8rem;" />
                                    @endif
                                    <h4 class="mb-0">{{ is_array($ldap_attributes['displayname'] ?? null) ? ($ldap_attributes['displayname'][0] ?? '') : ($ldap_attributes['displayname'] ?? '') }}</h4>
                                    <div class="text-muted small mb-2">{{ is_array($ldap_attributes['title'] ?? null) ? ($ldap_attributes['title'][0] ?? '') : ($ldap_attributes['title'] ?? '') }}</div>
                                    <span class="badge bg-{{ (is_array($ldap_attributes['useraccountcontrol'] ?? null) ? ($ldap_attributes['useraccountcontrol'][0] ?? '') : ($ldap_attributes['useraccountcontrol'] ?? '')) == '512' ? 'success' : 'secondary' }} mb-2">
                                        {{ (is_array($ldap_attributes['useraccountcontrol'] ?? null) ? ($ldap_attributes['useraccountcontrol'][0] ?? '') : ($ldap_attributes['useraccountcontrol'] ?? '')) == '512' ? 'Active' : 'Inactive' }}
                                    </span>
                                    <div class="text-muted small">{{ is_array($ldap_attributes['mail'] ?? null) ? ($ldap_attributes['mail'][0] ?? '') : ($ldap_attributes['mail'] ?? '') }}</div>
                                    <hr>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card shadow-sm p-3 h-100">
                                            <h5 class="card-title mb-3"><i class="ti ti-id-badge me-2"></i>Account Information</h5>
                                            <div class="mb-2"><strong>Username:</strong> <span class="text-primary">{{ is_array($ldap_attributes['samaccountname'] ?? null) ? ($ldap_attributes['samaccountname'][0] ?? '') : ($ldap_attributes['samaccountname'] ?? '') }}</span></div>
                                            <div class="mb-2"><strong>Display Name:</strong> {{ is_array($ldap_attributes['displayname'] ?? null) ? ($ldap_attributes['displayname'][0] ?? '') : ($ldap_attributes['displayname'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Email:</strong> {{ is_array($ldap_attributes['mail'] ?? null) ? ($ldap_attributes['mail'][0] ?? '') : ($ldap_attributes['mail'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Account Status:</strong> <span class="badge bg-{{ (is_array($ldap_attributes['useraccountcontrol'] ?? null) ? ($ldap_attributes['useraccountcontrol'][0] ?? '') : ($ldap_attributes['useraccountcontrol'] ?? '')) == '512' ? 'success' : 'secondary' }}">
                                                {{ (is_array($ldap_attributes['useraccountcontrol'] ?? null) ? ($ldap_attributes['useraccountcontrol'][0] ?? '') : ($ldap_attributes['useraccountcontrol'] ?? '')) == '512' ? 'Active' : 'Inactive' }}
                                            </span></div>
                                            <div class="mb-2"><strong>Staff ID:</strong> {{ is_array($ldap_attributes['employeeid'] ?? null) ? ($ldap_attributes['employeeid'][0] ?? '') : ($ldap_attributes['employeeid'] ?? '') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card shadow-sm p-3 h-100">
                                            <h5 class="card-title mb-3"><i class="ti ti-building me-2"></i>Organizational Info</h5>
                                            <div class="mb-2"><strong>Title:</strong> {{ is_array($ldap_attributes['title'] ?? null) ? ($ldap_attributes['title'][0] ?? '') : ($ldap_attributes['title'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Department:</strong> {{ is_array($ldap_attributes['department'] ?? null) ? ($ldap_attributes['department'][0] ?? '') : ($ldap_attributes['department'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Company:</strong> {{ is_array($ldap_attributes['company'] ?? null) ? ($ldap_attributes['company'][0] ?? '') : ($ldap_attributes['company'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Manager:</strong> {{ is_array($ldap_attributes['manager'] ?? null) ? ($ldap_attributes['manager'][0] ?? '') : ($ldap_attributes['manager'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Office Location:</strong> {{ is_array($ldap_attributes['physicaldeliveryofficename'] ?? null) ? ($ldap_attributes['physicaldeliveryofficename'][0] ?? '') : ($ldap_attributes['physicaldeliveryofficename'] ?? '') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card shadow-sm p-3 h-100">
                                            <h5 class="card-title mb-3"><i class="ti ti-phone me-2"></i>Contact Info</h5>
                                            <div class="mb-2"><strong>Telephone:</strong> {{ is_array($ldap_attributes['telephonenumber'] ?? null) ? ($ldap_attributes['telephonenumber'][0] ?? '') : ($ldap_attributes['telephonenumber'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Mobile:</strong> {{ is_array($ldap_attributes['mobile'] ?? null) ? ($ldap_attributes['mobile'][0] ?? '') : ($ldap_attributes['mobile'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Fax:</strong> {{ is_array($ldap_attributes['facsimiletelephonenumber'] ?? null) ? ($ldap_attributes['facsimiletelephonenumber'][0] ?? '') : ($ldap_attributes['facsimiletelephonenumber'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Home Phone:</strong> {{ is_array($ldap_attributes['homephone'] ?? null) ? ($ldap_attributes['homephone'][0] ?? '') : ($ldap_attributes['homephone'] ?? '') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card shadow-sm p-3 h-100">
                                            <h5 class="card-title mb-3"><i class="ti ti-server me-2"></i>Technical Info</h5>
                                            <div class="mb-2"><strong>User Principal Name:</strong> {{ is_array($ldap_attributes['userprincipalname'] ?? null) ? ($ldap_attributes['userprincipalname'][0] ?? '') : ($ldap_attributes['userprincipalname'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Home Directory:</strong> {{ is_array($ldap_attributes['homedirectory'] ?? null) ? ($ldap_attributes['homedirectory'][0] ?? '') : ($ldap_attributes['homedirectory'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Profile Path:</strong> {{ is_array($ldap_attributes['profilepath'] ?? null) ? ($ldap_attributes['profilepath'][0] ?? '') : ($ldap_attributes['profilepath'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Script Path:</strong> {{ is_array($ldap_attributes['scriptpath'] ?? null) ? ($ldap_attributes['scriptpath'][0] ?? '') : ($ldap_attributes['scriptpath'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Groups:</strong> {{ is_array($ldap_attributes['memberof'] ?? null) ? implode(', ', $ldap_attributes['memberof']) : ($ldap_attributes['memberof'] ?? '') }}</div>
                                            <div class="mb-2"><strong>Distinguished Name:</strong> {{ is_array($ldap_attributes['distinguishedname'] ?? null) ? ($ldap_attributes['distinguishedname'][0] ?? '') : ($ldap_attributes['distinguishedname'] ?? '') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h2>{{ __('Active Sessions') }}</h2>

                        <div class="table-responsive">
                            <table class="table table-vcenter datatable">
                                <thead>
                                    <tr>
                                        <th>User Agent</th>
                                        <th>IP Address</th>
                                        <th>Last Activity</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($devices as $device)
                                    <tr>
                                        <td>{{ $device->user_agent }}</td>
                                        <td>
                                            {{ $device->ip_address }}
                                        </td>
                                        <td>
                                            {{ Carbon\Carbon::createFromTimestamp($device->last_activity)->locale(str_replace('_', '-', app()->getLocale()))->diffForHumans() }}
                                        </td>
                                        <td>
                                            @if(\Session::getId() == $device->id)
                                            <button disabled="disabled" class="btn btn-primary">Current Device</button>
                                            @else
                                            <form action="{{ route('profile.deletedevice', ['id' => $device->id]) }}"
                                                method="post">
                                                @csrf
                                                @method('DELETE')
                                                <input type="submit" class="btn btn-danger" value="Remove" />
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
