<?php

namespace App\Http\Controllers;

use Nette\Utils\Image;
use App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
// use Intervention\Image\Image;
// use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{

    /**
     * Display the Edit Profile page
     * 
     * @return \Illuminate\View\View
     */
    public function editProfile(){
        $devices = DB::table('sessions')->where('user_id', Auth::user()->id)->get()->reverse();
        $ldapEmail = null;
        $ldapTitle = null;
        $ldapDepartment = null;
        $ldapAttributes = [];
        // Try to fetch all LDAP attributes for the current user
        try {
            $username = Auth::user()->username;
            $connection = \LdapRecord\Container::getDefaultConnection();
            $query = $connection->query();
            $query->in(config('ldap.connections.default.base_dn'));
            $query->where('samaccountname', '=', $username);
            $results = $query->get();
            if (count($results) > 0) {
                $user = $results[0];
                if (is_object($user) && method_exists($user, 'getAttributes')) {
                    $ldapAttributes = $user->getAttributes();
                } elseif (is_array($user)) {
                    $ldapAttributes = $user;
                } else {
                    $ldapAttributes = (array) $user;
                }
                // Extract common fields for convenience
                $ldapEmail = is_array($ldapAttributes['mail'] ?? null) ? ($ldapAttributes['mail'][0] ?? '') : ($ldapAttributes['mail'] ?? null);
                $ldapTitle = is_array($ldapAttributes['title'] ?? null) ? ($ldapAttributes['title'][0] ?? '') : ($ldapAttributes['title'] ?? null);
                $ldapDepartment = is_array($ldapAttributes['department'] ?? null) ? ($ldapAttributes['department'][0] ?? '') : ($ldapAttributes['department'] ?? null);
            }
        } catch (\Exception $e) {
            $ldapEmail = null;
            $ldapTitle = null;
            $ldapDepartment = null;
            $ldapAttributes = [];
        }
        return view('profile.edit', [
            'devices' => $devices,
            'ldap_email' => $ldapEmail,
            'ldap_title' => $ldapTitle,
            'ldap_department' => $ldapDepartment,
            'ldap_attributes' => $ldapAttributes
        ]);
    }

    // Avatar/photo upload and removal methods removed as requested

    /**
     * Remove unused device
     * 
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function removeDevice(Request $request, $id){
        $delete = DB::table('sessions')->where('id', $id)->delete();
        return Redirect::back()->with('success', 'The device has been deleted successfully!');
    }
}
