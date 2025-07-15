<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LdapAuthController;
use App\Http\Controllers\Admin\ViewRoleController;
use App\Http\Controllers\AdPasswordController;

Route::get('/', function () {
    return view('welcome');
});

// Test route to verify routing is working
Route::get('/test', function () {
    return 'Test route is working!';
});

Route::view('dashboard', 'dashboard')
	->name('dashboard')
	->middleware(['auth', 'verified']);

Route::view('home', 'home')
	->name('home')
	->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified', 'is_admin'])->group(function () {
    Route::view('/admin/home', 'admin.home')->name('admin.home');
	Route::view('/admin/dashboard', 'admin.dashboard')->name('admin.dashboard');
    
    // Admin Profile Routes
    Route::get('/admin/profile', [App\Http\Controllers\Admin\ProfileController::class, 'editProfile'])->name('admin.profile');
    Route::post('/admin/profile/avatar', [App\Http\Controllers\Admin\ProfileController::class, 'updateAvatar'])->name('admin.profile.avatar');
    Route::delete('/admin/profile/avatar', [App\Http\Controllers\Admin\ProfileController::class, 'removeOldAvatar'])->name('admin.profile.deleteavatar');
    Route::delete('/admin/profile/device/{id}', [App\Http\Controllers\Admin\ProfileController::class, 'removeDevice'])->name('admin.profile.deletedevice');
    
    // ViewRole routes
    Route::get('/admin/viewrole', [ViewRoleController::class, 'index'])->name('admin.viewrole.index');
    Route::get('/admin/viewrole/search', [ViewRoleController::class, 'search'])->name('admin.viewrole.search');
    Route::get('/admin/viewrole/filter', [ViewRoleController::class, 'filterByRole'])->name('admin.viewrole.filter');
    Route::get('/admin/viewrole/{username}', [ViewRoleController::class, 'show'])->name('admin.viewrole.show');
    Route::post('/admin/viewrole/{username}/reset-password', [App\Http\Controllers\Admin\ViewRoleController::class, 'resetPassword'])->name('admin.viewrole.reset-password');

    // Show all LDAP attributes for a specific user (by username/samaccountname)
    Route::get('/admin/test/{username?}', function ($username = null) {
        try {
            $connection = \LdapRecord\Container::getDefaultConnection();
            $query = $connection->query();
            $query->in(config('ldap.connections.default.base_dn'));
            if ($username) {
                $query->where('samaccountname', '=', $username);
            } else {
                $query->where('samaccountname', '*');
                $query->limit(1);
            }
            $results = $query->get();
            if (count($results) > 0) {
                $user = $results[0];
                // If it's an object, get all attributes
                if (is_object($user) && method_exists($user, 'getAttributes')) {
                    $attributes = $user->getAttributes();
                } elseif (is_array($user)) {
                    $attributes = $user;
                } else {
                    $attributes = (array) $user;
                }
                // Recursively encode all values as UTF-8 or base64 for binary
                $safeAttributes = function ($value) use (&$safeAttributes) {
                    if (is_array($value)) {
                        $out = [];
                        foreach ($value as $k => $v) {
                            $out[$k] = $safeAttributes($v);
                        }
                        return $out;
                    } elseif (is_string($value)) {
                        $utf8 = @mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                        if (!mb_check_encoding($utf8, 'UTF-8')) {
                            return '[base64] ' . base64_encode($value);
                        }
                        return $utf8;
                    } elseif (is_object($value)) {
                        return '[object]';
                    } elseif (is_resource($value)) {
                        return '[resource]';
                    } elseif (is_bool($value)) {
                        return $value ? 'true' : 'false';
                    } elseif (is_null($value)) {
                        return null;
                    } else {
                        return '[base64] ' . base64_encode((string)$value);
                    }
                };
                $attributes = $safeAttributes($attributes);
                // If request expects JSON, return JSON
                if (request()->wantsJson()) {
                    return response()->json($attributes);
                }
                // Otherwise, render a simple HTML table
                $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>LDAP Attributes</title>';
                $html .= '<style>body{font-family:sans-serif;}table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ccc;padding:6px;}th{background:#eee;}tr:nth-child(even){background:#f9f9f9;}pre{margin:0;white-space:pre-wrap;word-break:break-all;}</style>';
                $html .= '</head><body>';
                $html .= '<h2>LDAP Attributes for <code>' . htmlspecialchars($username ?? 'first user') . '</code></h2>';
                $html .= '<table><thead><tr><th>Attribute</th><th>Value</th></tr></thead><tbody>';
                foreach ($attributes as $key => $value) {
                    $display = '';
                    if (is_array($value)) {
                        $display = '<pre>' . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
                    } elseif (is_null($value)) {
                        $display = '<span style="color:#888">null</span>';
                    } else {
                        $display = '<pre>' . htmlspecialchars((string)$value) . '</pre>';
                    }
                    $html .= '<tr><td>' . htmlspecialchars($key) . '</td><td>' . $display . '</td></tr>';
                }
                $html .= '</tbody></table>';
                $html .= '</body></html>';
                return $html;
            } else {
                return response()->json(['error' => 'No LDAP users found.']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    });

    // List all possible attribute names across the directory (from first 100 users)
    Route::get('/admin/ldap-attributes', function () {
        try {
            $connection = \LdapRecord\Container::getDefaultConnection();
            $query = $connection->query();
            $query->in(config('ldap.connections.default.base_dn'));
            $query->where('samaccountname', '*');
            $query->limit(100);
            $results = $query->get();
            $attributeNames = [];
            foreach ($results as $user) {
                if (is_object($user) && method_exists($user, 'getAttributes')) {
                    $attributes = $user->getAttributes();
                } elseif (is_array($user)) {
                    $attributes = $user;
                } else {
                    $attributes = (array) $user;
                }
                foreach (array_keys($attributes) as $attr) {
                    $attributeNames[$attr] = true;
                }
            }
            $attributeNames = array_keys($attributeNames);
            sort($attributeNames);
            if (request()->wantsJson()) {
                return response()->json($attributeNames);
            }
            $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>LDAP Attribute Names</title>';
            $html .= '<style>body{font-family:sans-serif;}ul{columns:3;-webkit-columns:3;-moz-columns:3;}li{margin-bottom:4px;}</style>';
            $html .= '</head><body>';
            $html .= '<h2>All LDAP Attribute Names (first 100 users)</h2>';
            $html .= '<ul>';
            foreach ($attributeNames as $attr) {
                $html .= '<li><code>' . htmlspecialchars($attr) . '</code></li>';
            }
            $html .= '</ul>';
            $html .= '</body></html>';
            return $html;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    });
});

// Regular user profile routes (non-admin)
Route::middleware(['web', 'auth', 'not_admin'])->group(function(){
	Route::get('/profile', [ProfileController::class, 'editProfile'])->name('profile');
	Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
	Route::delete('/profile/avatar', [ProfileController::class, 'removeOldAvatar'])->name('profile.deleteavatar');
	Route::delete('/profile/device/{id}', [ProfileController::class, 'removeDevice'])->name('profile.deletedevice');
});

// LDAP Authentication Routes (now the default login)
Route::get('/login', [LdapAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LdapAuthController::class, 'login'])->name('login');
Route::post('/logout', [LdapAuthController::class, 'logout'])->name('logout');

Route::get('/ldap-user-check', function () {
    try {
        $user = \LdapRecord\Models\ActiveDirectory\User::where('samaccountname', 'B131')->first();
        return $user ? $user->getName() : 'User not found';
    } catch (\Exception $e) {
        return 'LDAP error: ' . $e->getMessage();
    }
});

// AD Password Change Routes
Route::middleware(['web', 'auth'])->group(function() {
    Route::get('/password/change', [AdPasswordController::class, 'showChangePasswordForm'])
        ->name('password.change.form');
    Route::post('/password/change', [AdPasswordController::class, 'changePassword'])
        ->name('password.change.ad');
});




