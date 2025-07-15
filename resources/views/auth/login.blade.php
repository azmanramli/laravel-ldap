@extends('layouts.auth')

@section('content')
    <form method="POST" action="{{ route('login') }}" class="card card-md">
        @csrf
        <div class="card-body">
            <h2 class="mb-3 text-center">Sign In</h2>
            
            <div class="mb-3">
                <div class="alert alert-info">
                    <div class="d-flex">
                        <div class="me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="alert-title">Active Directory Authentication</h4>
                            <div class="text-muted">Please use your domain credentials to sign in.</div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-3">
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input class="form-control @error('username') is-invalid @enderror" 
                       type="text" 
                       name="username" 
                       placeholder="Enter your domain username"
                       value="{{ old('username') }}" 
                       required 
                       autofocus 
                       tabindex="1" />
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group input-group-flat">
                    <input class="form-control @error('password') is-invalid @enderror" 
                           type="password" 
                           name="password"
                           placeholder="Enter your domain password" 
                           required 
                           tabindex="2" />
                    <span class="input-group-text">
                        <a href="#" class="link-secondary" title="Show password" data-toggle="tooltip">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <circle cx="12" cy="12" r="2"></circle>
                                <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"></path>
                            </svg>
                        </a>
                    </span>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-2">
                <label class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" tabindex="3" />
                    <span class="form-check-label">Remember me</span>
                </label>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100" tabindex="4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M15 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path>
                        <path d="M21 12h-13"></path>
                        <path d="M18 9l3 3l-3 3"></path>
                    </svg>
                    Sign in with Active Directory
                </button>
            </div>
        </div>
    </form>

    <div class="text-center text-muted mt-3">
        <small>Need help? Contact your system administrator</small>
    </div>
@endsection
