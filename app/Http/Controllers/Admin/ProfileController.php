<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ProfileController as BaseProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends BaseProfileController
{
    /**
     * Display the Admin Edit Profile page
     * 
     * @return \Illuminate\View\View
     */
    public function editProfile()
    {
        // Get the base profile data from parent controller
        $data = parent::editProfile()->getData();
        
        // Add admin-specific data
        $data['is_admin'] = true;
        $data['total_users'] = DB::table('ldap_users')->count();
        $data['active_sessions'] = DB::table('sessions')->count();
        
        return view('admin.profile.edit', $data);
    }
} 