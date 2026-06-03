<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class UserComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Always ensure the authenticated user is available in views
        $user = Auth::user();
        
        $currentSchoolAddons = [];
        if ($user && $user->school_id) {
            $user->load('school.addons');
            $currentSchoolAddons = $user->school
                ? $user->school->addons->where('enabled', true)->pluck('feature_key')->toArray()
                : [];
        }
        $view->with([
            'currentUser' => $user,
            'userType' => $user ? $user->user_type : 'guest',
            'userRoles' => $user && method_exists($user, 'roles') ? $user->roles->pluck('name')->toArray() : [],
            'currentSchoolAddons' => $currentSchoolAddons,
        ]);
    }
}

