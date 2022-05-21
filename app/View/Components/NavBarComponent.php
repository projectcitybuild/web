<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class NavBarComponent extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        $canAccessPanel = false;
        $account = Auth::user();
        if ($account !== null) {
            $canAccessPanel = $account->canAccessPanel();
        }

        $isLoggedIn = Auth::check();

        return view('v2.front.components.navbar')
            ->with(compact(['canAccessPanel', 'isLoggedIn']));
    }
}
