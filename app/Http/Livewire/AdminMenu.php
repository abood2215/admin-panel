<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AdminMenu extends Component
{
    public bool $open = false;

    protected $listeners = [
        'closeUserMenu' => 'close',
    ];

    public function toggle()
    {
        $this->open = ! $this->open;
    }

    public function close()
    {
        $this->open = false;
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.admin-menu', [
            'user' => Auth::user(),
        ]);
    }
}
