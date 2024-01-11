<?php

namespace App\Filament\Resources\TalkResource\Pages;

use App\Enums\TalkStatus;
use App\Filament\Resources\TalkResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListTalks extends ListRecords
{
    protected static string $resource = TalkResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All talks'),
            'approved' => Tab::make('Approved')
                            ->modifyQueryUsing(fn($query)=>$query->whereStatus(TalkStatus::APPROVED)),
            'submitted' => Tab::make('Submitted')
                            ->modifyQueryUsing(fn($query)=>$query->whereStatus(TalkStatus::SUBMITTED)),
            'rejected' => Tab::make('Rejected')
                            ->modifyQueryUsing(fn($query)=>$query->whereStatus(TalkStatus::REJECTED)),
        ];
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
