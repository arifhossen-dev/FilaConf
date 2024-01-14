<?php

namespace App\Livewire;

use App\Models\Attendee;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Livewire\Component;

class ConferenceSignUpPage extends Component implements HasForms, HasActions
{

    use InteractsWithActions;
    use InteractsWithForms;

    public int $conferenceId;
    public int $price = 500;

    public function mount(int $conferenceId = 1)
    {
        $this->conferenceId = $conferenceId;
    }

    public function signUpAction(): Action
    {
        return Action::make('SignUp')
            ->slideOver()
            ->form([
                Placeholder::make('total_price')
                    ->hiddenLabel()
                    ->content(function (Get $get) {
                        return '$' . count($get('attendees')) * $this->price;
                    }),
                Repeater::make('attendees')
                    ->schema(Attendee::getForm())
            ])
            ->action(function (array $data) {
                collect($data['attendees'])->each(function ($attendee) use ($data) {
                    Attendee::create([
                        'conference_id' => $this->conferenceId,
                        'ticket_cost' => $this->price,
                        'name' => $attendee['name'],
                        'email' => $attendee['email'],
                        'is_paid' => true,
                    ]);
                });
            })
            ->after(function () {
                Notification::make()
                    ->success()
                    ->title('Success')
                    ->body('Your registration was successful')
                    ->send();
            });
    }

    public function render()
    {
        return view('livewire.conference-sign-up-page');
    }
}
