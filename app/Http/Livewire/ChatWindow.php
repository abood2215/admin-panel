<?php

namespace App\Livewire;

use Livewire\Component;

class ChatWindow extends Component
{
    public array $messages = [];
    public string $newMessage = '';

    public function sendMessage(): void
    {
        if (trim($this->newMessage) === '') return;

        $this->messages[] = ['from' => 'user', 'text' => $this->newMessage];
        $this->messages[] = ['from' => 'bot',  'text' => '🤖 وصلت رسالتك!'];

        $this->newMessage = '';
    }

    public function render()
    {
        return view('livewire.chat-window');
    }
}
