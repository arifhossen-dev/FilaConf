<?php

namespace App\Filament\Resources\AttendeeResource\Widgets;

use App\Models\Attendee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendeesStatsWidget extends BaseWidget
{
    protected function getColumns():int
    {
        return 2;
    }
    protected function getStats(): array
    {

        return [
            Stat::make('Attendees Count', Attendee::count())
            ->description('Total number of Attendees')
            ->color('success')
            ->chart([1,5,3,4,5,6,1,5,6]),
            Stat::make('Total Revenue', Attendee::sum('ticket_cost')/100),
        ];
    }
}
