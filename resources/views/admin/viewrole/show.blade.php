@extends('layouts.admindashboard')

@section('page-pretitle', 'User Management')
@section('page-title', 'View User Details')

@section('content')
<div class="container-xl">
    {{-- Success/Error Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M5 12l5 5l10 -10"></path>
                </svg>
            </div>
            <div>{{ session('success') }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 9v2m0 4v.01"></path>
                    <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"></path>
                </svg>
            </div>
            <div>{{ session('error') }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 9v2m0 4v.01"></path>
                    <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"></path>
                </svg>
            </div>
            <div>
                <ul class="list-unstyled mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
    @endif

    <div class="row g-4">
        <!-- Back Button -->
        <div class="col-12">
            <a href="{{ route('admin.viewrole.index') }}" class="btn btn-outline-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l14 0" />
                    <path d="M5 12l4 4" />
                    <path d="M5 12l4 -4" />
                </svg>
                Back to User List
            </a>
        </div>

        <!-- User Profile Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="avatar avatar-xl mb-3 rounded">
                            {{ strtoupper(substr(is_array($user['username']) ? ($user['username'][0] ?? '') : $user['username'], 0, 2)) }}
                        </span>
                        <h3 class="mb-0">{{ is_array($user['display_name']) ? ($user['display_name'][0] ?? '') : $user['display_name'] }}</h3>
                        <p class="text-muted">{{ is_array($user['email']) ? ($user['email'][0] ?? '') : $user['email'] }}</p>
                    </div>
                    <div class="mb-3">
                        @if($user['role'] === 'admin')
                            <span class="badge bg-primary">Administrator</span>
                        @else
                            <span class="badge bg-success">Regular User</span>
                        @endif
                        <span class="badge bg-{{ $user['account_status'] === 'Active' ? 'success' : 'danger' }}">
                            {{ $user['account_status'] }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">Last Login</div>
                        <strong>{{ $loginHistory['last_login'] ? \Carbon\Carbon::parse($loginHistory['last_login'])->diffForHumans() : 'Never' }}</strong>
                    </div>
                </div>
                <div class="d-flex">
                    <a href="#" class="card-btn" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 3v6" />
                        </svg>
                        Reset Password
                    </a>
                    <a href="#" class="card-btn" data-bs-toggle="modal" data-bs-target="#editUserModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" />
                            <path d="M13.5 6.5l4 4" />
                        </svg>
                        Edit User
                    </a>
                </div>
            </div>
        </div>

        <!-- User Details Cards -->
        <div class="col-md-8">
            <div class="row row-cards">
                <!-- Account Information -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Account Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Username</div>
                                    <div class="datagrid-content">{{ is_array($user['username']) ? ($user['username'][0] ?? '') : $user['username'] }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Display Name</div>
                                    <div class="datagrid-content">{{ is_array($user['display_name']) ? ($user['display_name'][0] ?? '') : $user['display_name'] }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Email</div>
                                    <div class="datagrid-content">{{ is_array($user['email']) ? ($user['email'][0] ?? '') : $user['email'] }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Account Status</div>
                                    <div class="datagrid-content">
                                        <span class="badge bg-{{ is_array($user['account_status']) ? (($user['account_status'][0] ?? '') === 'Active' ? 'success' : 'danger') : ($user['account_status'] === 'Active' ? 'success' : 'danger') }}">
                                            {{ is_array($user['account_status']) ? ($user['account_status'][0] ?? '') : $user['account_status'] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Login Count</div>
                                    <div class="datagrid-content">{{ is_array($loginHistory['logon_count']) ? ($loginHistory['logon_count'][0] ?? 0) : ($loginHistory['logon_count'] ?? 0) }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Bad Password Count</div>
                                    <div class="datagrid-content">{{ is_array($loginHistory['bad_password_count']) ? ($loginHistory['bad_password_count'][0] ?? 0) : ($loginHistory['bad_password_count'] ?? 0) }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Password Last Set</div>
                                    <div class="datagrid-content">
                                        {{ is_array($loginHistory['password_last_set']) ? ($loginHistory['password_last_set'][0] ? \Carbon\Carbon::parse($loginHistory['password_last_set'][0])->format('Y-m-d H:i:s') : 'Never') : ($loginHistory['password_last_set'] ? \Carbon\Carbon::parse($loginHistory['password_last_set'])->format('Y-m-d H:i:s') : 'Never') }}
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Account Expires</div>
                                    <div class="datagrid-content">
                                        {{ is_array($loginHistory['account_expires']) ? ($loginHistory['account_expires'][0] ? \Carbon\Carbon::parse($loginHistory['account_expires'][0])->format('Y-m-d H:i:s') : 'Never') : ($loginHistory['account_expires'] ? \Carbon\Carbon::parse($loginHistory['account_expires'])->format('Y-m-d H:i:s') : 'Never') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Group Memberships -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Group Memberships</h3>
                        </div>
                        <div class="card-body">
                            @if(count($groups) > 0)
                                <div class="tags">
                                    @foreach($groups as $group)
                                        <span class="badge bg-blue-lt">{{ $group }}</span>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty">
                                    <p class="empty-title">No groups found</p>
                                    <p class="empty-subtitle text-muted">This user is not a member of any groups.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Additional Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                @if(isset($user['title']))
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Title</div>
                                    <div class="datagrid-content">{{ is_array($user['title']) ? ($user['title'][0] ?? '') : $user['title'] }}</div>
                                </div>
                                @endif
                                @if(isset($user['department']))
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Department</div>
                                    <div class="datagrid-content">{{ is_array($user['department']) ? ($user['department'][0] ?? '') : $user['department'] }}</div>
                                </div>
                                @endif
                                @if(isset($user['company']))
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Company</div>
                                    <div class="datagrid-content">{{ is_array($user['company']) ? ($user['company'][0] ?? '') : $user['company'] }}</div>
                                </div>
                                @endif
                                @if(isset($user['manager']))
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Manager</div>
                                    <div class="datagrid-content">{{ is_array($user['manager']) ? ($user['manager'][0] ?? '') : $user['manager'] }}</div>
                                </div>
                                @endif
                                @if(isset($user['telephone']))
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Telephone</div>
                                    <div class="datagrid-content">{{ is_array($user['telephone']) ? ($user['telephone'][0] ?? '') : $user['telephone'] }}</div>
                                </div>
                                @endif
                                @if(isset($user['mobile']))
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Mobile</div>
                                    <div class="datagrid-content">{{ is_array($user['mobile']) ? ($user['mobile'][0] ?? '') : $user['mobile'] }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal modal-blur fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.viewrole.reset-password', ['username' => is_array($user['username']) ? ($user['username'][0] ?? '') : $user['username']]) }}" id="resetPasswordForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reset User Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="form-text mb-3">Enter a new password for this user.</div>
                        @if($errors->has('password'))
                            <div class="alert alert-danger">
                                {{ $errors->first('password') }}
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label required">New Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8" autocomplete="new-password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required minlength="8" autocomplete="new-password">
                            <div class="form-text">Password must be at least 8 characters long.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal modal-blur fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Display Name</label>
                        <input type="text" class="form-control" value="{{ is_array($user['display_name']) ? ($user['display_name'][0] ?? '') : $user['display_name'] }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ is_array($user['email']) ? ($user['email'][0] ?? '') : $user['email'] }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" value="{{ is_array($user['title']) ? ($user['title'][0] ?? '') : ($user['title'] ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" class="form-control" value="{{ is_array($user['department']) ? ($user['department'][0] ?? '') : ($user['department'] ?? '') }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection 