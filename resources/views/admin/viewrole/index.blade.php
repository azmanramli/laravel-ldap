@extends('layouts.admindashboard')

@section('content')
<div class="container-xl">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Active Directory Users & Roles Management
                    </h2>
                    <div class="text-muted mt-1">View all Active Directory users and their assigned roles</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.home') }}" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M9 11l3 3l8 -8"></path>
                                <path d="M20.12 12.004a9 9 0 1 1 -8.124 -8.953"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row row-deck row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total AD Users</div>
                    </div>
                    <div class="h1 mb-3">{{ $totalUsers }}</div>
                    <div class="d-flex mb-2">
                        <div>All Active Directory users</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Admin Users</div>
                    </div>
                    <div class="h1 mb-3 text-primary">{{ $adminUsers }}</div>
                    <div class="d-flex mb-2">
                        <div>Users with admin privileges</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Regular Users</div>
                    </div>
                    <div class="h1 mb-3 text-success">{{ $regularUsers }}</div>
                    <div class="d-flex mb-2">
                        <div>Users with standard access</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Logged In Users</div>
                    </div>
                    <div class="h1 mb-3 text-warning">{{ $loggedInUsers }}</div>
                    <div class="d-flex mb-2">
                        <div>Users who have logged in</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Search & Filter Users</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('admin.viewrole.search') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search by username or display name..." value="{{ $search ?? '' }}">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <circle cx="10" cy="10" r="7"></circle>
                                <path d="M21 21l-6 -6"></path>
                            </svg>
                            Search
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('admin.viewrole.filter') }}" method="GET" class="d-flex">
                        <select name="role" class="form-select me-2">
                            <option value="all" {{ ($role ?? '') === 'all' ? 'selected' : '' }}>All Roles</option>
                            <option value="admin" {{ ($role ?? '') === 'admin' ? 'selected' : '' }}>Admin Only</option>
                            <option value="user" {{ ($role ?? '') === 'user' ? 'selected' : '' }}>User Only</option>
                        </select>
                        <button type="submit" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M3 4a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v2.586a1 1 0 0 1 -.293 .707l-6.414 6.414a1 1 0 0 0 -.293 .707v6.586a1 1 0 0 1 -1.414 .914l-4 -2a1 1 0 0 1 -.586 -.914v-4.586a1 1 0 0 0 -.293 -.707l-6.414 -6.414a1 1 0 0 1 -.293 -.707v-2.586z"></path>
                            </svg>
                            Filter
                        </button>
                    </form>
                </div>
            </div>
            @if(isset($search) || isset($role))
            <div class="mt-3">
                <a href="{{ route('admin.viewrole.index') }}" class="btn btn-outline-danger btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M18 6l-12 12"></path>
                        <path d="M6 6l12 12"></path>
                    </svg>
                    Clear Filters
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Active Directory Users ({{ $users->total() }} total)</h3>
        </div>
        <div class="card-body">
            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User Information</th>
                            <th>Account Status & Security</th>
                            <th>Organizational Information</th>
                            <th>Contact & Communication</th>
                            <th>Technical Information</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $index => $user)
                        @php
                            $username = is_array($user['username']) ? ($user['username'][0] ?? '') : $user['username'];
                            $displayName = is_array($user['display_name']) ? ($user['display_name'][0] ?? '') : $user['display_name'];
                            $email = is_array($user['email']) ? ($user['email'][0] ?? '') : $user['email'];
                            
                            // Safe handling for all new AD attributes
                            $getSafeValue = function($value) {
                                if (is_array($value)) {
                                    return $value[0] ?? '';
                                }
                                return $value ?? '';
                            };
                            
                            $department = $getSafeValue($user['department']);
                            $title = $getSafeValue($user['title']);
                            $company = $getSafeValue($user['company']);
                            $division = $getSafeValue($user['division']);
                            $officeLocation = $getSafeValue($user['office_location']);
                            $manager = $getSafeValue($user['manager']);
                            $employeeId = $getSafeValue($user['employee_id']);
                            
                            $telephone = $getSafeValue($user['telephone']);
                            $mobile = $getSafeValue($user['mobile']);
                            $ipPhone = $getSafeValue($user['ip_phone']);
                            $fax = $getSafeValue($user['fax']);
                            $homePhone = $getSafeValue($user['home_phone']);
                            $pager = $getSafeValue($user['pager']);
                            
                            $userPrincipalName = $getSafeValue($user['user_principal_name']);
                            $accountType = $getSafeValue($user['account_type']);
                            $homeDirectory = $getSafeValue($user['home_directory']);
                            $profilePath = $getSafeValue($user['profile_path']);
                            $scriptPath = $getSafeValue($user['script_path']);
                            $primaryGroupId = $getSafeValue($user['primary_group_id']);
                            $userWorkstations = $getSafeValue($user['user_workstations']);
                            
                            // Additional safe handling for remaining fields
                            $logonCount = $getSafeValue($user['logon_count']);
                            $badPasswordCount = $getSafeValue($user['bad_password_count']);
                            $accountStatus = $getSafeValue($user['account_status']);
                        @endphp
                        <tr>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar me-2 bg-primary-lt">
                                        {{ strtoupper(substr(is_array($username) ? ($username[0] ?? '') : $username, 0, 2)) }}
                                    </span>
                                    <div>
                                        <div class="font-weight-medium">{{ $username }}</div>
                                        <div class="text-muted">{{ $displayName }}</div>
                                        <div class="text-muted small">{{ $email ?: 'No email' }}</div>
                                        <div class="mt-1">
                                            @if($user['role'] === 'admin')
                                            <span class="badge bg-primary">Admin</span>
                                            @else
                                            <span class="badge bg-success">User</span>
                                            @endif
                                            <span class="badge bg-secondary">{{ count($user['groups']) }} groups</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="mb-1">
                                    @if($accountStatus === 'Active')
                                        <span class="badge bg-success">{{ $accountStatus }}</span>
                                    @elseif($accountStatus === 'Disabled')
                                        <span class="badge bg-danger">{{ $accountStatus }}</span>
                                    @elseif($accountStatus === 'Locked')
                                        <span class="badge bg-warning">{{ $accountStatus }}</span>
                                    @elseif($accountStatus === 'Expired')
                                        <span class="badge bg-danger">{{ $accountStatus }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $accountStatus }}</span>
                                    @endif
                                </div>
                                <div class="text-muted small">
                                    <div>Logons: {{ $logonCount }}</div>
                                    <div>Bad PWD: {{ $badPasswordCount }}</div>
                                    @if($user['has_logged_in'])
                                        <div class="text-success">✓ Logged In</div>
                                    @else
                                        <div class="text-muted">✗ Never Logged In</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small">
                                    @if($department)
                                        <div><strong>Dept:</strong> {{ $department }}</div>
                                    @endif
                                    @if($title)
                                        <div><strong>Title:</strong> {{ $title }}</div>
                                    @endif
                                    @if($company)
                                        <div><strong>Company:</strong> {{ $company }}</div>
                                    @endif
                                    @if($officeLocation)
                                        <div><strong>Office:</strong> {{ $officeLocation }}</div>
                                    @endif
                                    @if($manager)
                                        <div><strong>Manager:</strong> {{ $manager }}</div>
                                    @endif
                                    @if($employeeId)
                                        <div><strong>ID:</strong> {{ $employeeId }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small">
                                    @if($telephone)
                                        <div><strong>Phone:</strong> {{ $telephone }}</div>
                                    @endif
                                    @if($mobile)
                                        <div><strong>Mobile:</strong> {{ $mobile }}</div>
                                    @endif
                                    @if($ipPhone)
                                        <div><strong>IP Phone:</strong> {{ $ipPhone }}</div>
                                    @endif
                                    @if($fax)
                                        <div><strong>Fax:</strong> {{ $fax }}</div>
                                    @endif
                                    @if($homePhone)
                                        <div><strong>Home:</strong> {{ $homePhone }}</div>
                                    @endif
                                    @if($pager)
                                        <div><strong>Pager:</strong> {{ $pager }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small">
                                    @if($userPrincipalName)
                                        <div><strong>UPN:</strong> {{ $userPrincipalName }}</div>
                                    @endif
                                    @if($accountType)
                                        <div><strong>Type:</strong> {{ $accountType }}</div>
                                    @endif
                                    @if($homeDirectory)
                                        <div><strong>Home Dir:</strong> {{ $homeDirectory }}</div>
                                    @endif
                                    @if($profilePath)
                                        <div><strong>Profile:</strong> {{ $profilePath }}</div>
                                    @endif
                                    @if($scriptPath)
                                        <div><strong>Script:</strong> {{ $scriptPath }}</div>
                                    @endif
                                    @if($userWorkstations)
                                        <div><strong>Workstations:</strong> {{ $userWorkstations }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('admin.viewrole.show', ['username' => is_array($user['username']) ? ($user['username'][0] ?? '') : $user['username']]) }}" class="btn btn-sm btn-outline-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                            <path d="M21 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"></path>
                                        </svg>
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
            </div>
            @else
            <div class="empty">
                <div class="empty-img">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                        <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                        <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855"></path>
                    </svg>
                </div>
                <p class="empty-title">No users found</p>
                <p class="empty-subtitle text-muted">
                    @if(isset($search))
                        No Active Directory users match your search criteria "{{ $search }}".
                    @elseif(isset($role) && $role !== 'all')
                        No Active Directory users found with role "{{ $role }}".
                    @else
                        No Active Directory users found. Please check your LDAP connection.
                    @endif
                </p>
                <div class="empty-action">
                    <a href="{{ route('admin.viewrole.index') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 5l0 14"></path>
                            <path d="M5 12l14 0"></path>
                        </svg>
                        View All Users
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div class="modal modal-blur fade" id="userDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function viewUserDetails(user) {
    const modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
    
    // Helper function to safely get values (handle arrays)
    const getSafeValue = (value) => {
        if (Array.isArray(value)) {
            return value[0] || '';
        }
        return value || '';
    };
    
    const groupsList = user.groups && user.groups.length > 0 
        ? user.groups.map(group => `<span class="badge bg-secondary me-1">${getSafeValue(group)}</span>`).join('')
        : '<span class="text-muted">No groups</span>';
    
    const loginStatus = user.has_logged_in 
        ? `<span class="badge bg-success">Logged In</span>`
        : `<span class="badge bg-warning">Never Logged In</span>`;
    
    const lastLoginInfo = user.last_login_at 
        ? `<div class="text-muted">Last login: ${new Date(user.last_login_at).toLocaleString()}</div>`
        : '';
    
    const accountStatusBadge = getSafeValue(user.account_status) === 'Active' ? 'bg-success' : 
                              getSafeValue(user.account_status) === 'Disabled' ? 'bg-danger' :
                              getSafeValue(user.account_status) === 'Locked' ? 'bg-warning' :
                              getSafeValue(user.account_status) === 'Expired' ? 'bg-danger' : 'bg-secondary';
    
    document.getElementById('userDetailsContent').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Basic Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Username:</strong></td><td>${getSafeValue(user.username)}</td></tr>
                    <tr><td><strong>Display Name:</strong></td><td>${getSafeValue(user.display_name)}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>${getSafeValue(user.email) || 'Not set'}</td></tr>
                    <tr><td><strong>Role:</strong></td><td><span class="badge ${getSafeValue(user.role) === 'admin' ? 'bg-primary' : 'bg-success'}">${getSafeValue(user.role)}</span></td></tr>
                    <tr><td><strong>Login Status:</strong></td><td>${loginStatus}</td></tr>
                    ${lastLoginInfo ? `<tr><td></td><td>${lastLoginInfo}</td></tr>` : ''}
                </table>
            </div>
            <div class="col-md-6">
                <h6>Account Status & Security</h6>
                <table class="table table-sm">
                    <tr><td><strong>Status:</strong></td><td><span class="badge ${accountStatusBadge}">${getSafeValue(user.account_status)}</span></td></tr>
                    <tr><td><strong>Logon Count:</strong></td><td>${getSafeValue(user.logon_count)}</td></tr>
                    <tr><td><strong>Bad Password Count:</strong></td><td>${getSafeValue(user.bad_password_count)}</td></tr>
                    <tr><td><strong>Password Last Set:</strong></td><td>${getSafeValue(user.password_expires) || 'Not available'}</td></tr>
                    <tr><td><strong>Last Logoff:</strong></td><td>${getSafeValue(user.last_logoff) || 'Not available'}</td></tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <h6>Organizational Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Department:</strong></td><td>${getSafeValue(user.department) || 'Not set'}</td></tr>
                    <tr><td><strong>Title:</strong></td><td>${getSafeValue(user.title) || 'Not set'}</td></tr>
                    <tr><td><strong>Company:</strong></td><td>${getSafeValue(user.company) || 'Not set'}</td></tr>
                    <tr><td><strong>Division:</strong></td><td>${getSafeValue(user.division) || 'Not set'}</td></tr>
                    <tr><td><strong>Office Location:</strong></td><td>${getSafeValue(user.office_location) || 'Not set'}</td></tr>
                    <tr><td><strong>Manager:</strong></td><td>${getSafeValue(user.manager) || 'Not set'}</td></tr>
                    <tr><td><strong>Employee ID:</strong></td><td>${getSafeValue(user.employee_id) || 'Not set'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Contact & Communication</h6>
                <table class="table table-sm">
                    <tr><td><strong>Telephone:</strong></td><td>${getSafeValue(user.telephone) || 'Not set'}</td></tr>
                    <tr><td><strong>Mobile:</strong></td><td>${getSafeValue(user.mobile) || 'Not set'}</td></tr>
                    <tr><td><strong>IP Phone:</strong></td><td>${getSafeValue(user.ip_phone) || 'Not set'}</td></tr>
                    <tr><td><strong>Fax:</strong></td><td>${getSafeValue(user.fax) || 'Not set'}</td></tr>
                    <tr><td><strong>Home Phone:</strong></td><td>${getSafeValue(user.home_phone) || 'Not set'}</td></tr>
                    <tr><td><strong>Pager:</strong></td><td>${getSafeValue(user.pager) || 'Not set'}</td></tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <h6>Technical Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>User Principal Name:</strong></td><td>${getSafeValue(user.user_principal_name) || 'Not set'}</td></tr>
                    <tr><td><strong>Account Type:</strong></td><td>${getSafeValue(user.account_type)}</td></tr>
                    <tr><td><strong>Home Directory:</strong></td><td>${getSafeValue(user.home_directory) || 'Not set'}</td></tr>
                    <tr><td><strong>Profile Path:</strong></td><td>${getSafeValue(user.profile_path) || 'Not set'}</td></tr>
                    <tr><td><strong>Script Path:</strong></td><td>${getSafeValue(user.script_path) || 'Not set'}</td></tr>
                    <tr><td><strong>Primary Group ID:</strong></td><td>${getSafeValue(user.primary_group_id) || 'Not set'}</td></tr>
                    <tr><td><strong>User Workstations:</strong></td><td>${getSafeValue(user.user_workstations) || 'Not set'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Active Directory Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Distinguished Name:</strong></td><td class="text-muted small">${getSafeValue(user.distinguished_name) || 'Not available'}</td></tr>
                    <tr><td><strong>Account Created:</strong></td><td>${getSafeValue(user.when_created) || 'Not available'}</td></tr>
                    <tr><td><strong>Last AD Login:</strong></td><td>${getSafeValue(user.last_logon) || 'Not available'}</td></tr>
                    <tr><td><strong>Object SID:</strong></td><td class="text-muted small">${getSafeValue(user.object_sid) || 'Not available'}</td></tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6>Group Memberships</h6>
                <div class="mb-2">
                    ${groupsList}
                </div>
                <div class="text-muted small">
                    Total groups: ${user.groups ? user.groups.length : 0}
                </div>
            </div>
        </div>
    `;
    modal.show();
}
</script>
@endpush 