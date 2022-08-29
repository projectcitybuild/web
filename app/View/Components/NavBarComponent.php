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
        $account = Auth::user();
        $isLoggedIn = $account !== null;
        $canAccessPanel = $account?->canAccessPanel() ?? false;

        return view('front.components.navbar')
            ->with(compact(['canAccessPanel', 'isLoggedIn']));
    }
}
