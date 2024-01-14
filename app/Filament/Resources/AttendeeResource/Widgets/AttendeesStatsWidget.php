<?php

namespace App\Filament\Resources\AttendeeResource\Widgets;

use App\Filament\Resources\AttendeeResource\Pages\ListAttendees;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendeesStatsWidget extends BaseWidget
{
    use InteractsWithPageTable;
    protected function getColumns(): int
    {
        return 2;
    }

    protected function getTablePage(): string
    {
        return ListAttendees::class;
    }

    protected function getStats(): array
    {

        return [
            Stat::make('Attendees Count', $this->getPageTableQuery()->count())
                ->description('Total number of Attendees')
                ->color('success')
                ->chart([1, 5, 3, 4, 5, 6, 1, 5, 6]),
            Stat::make('Total Revenue', $this->getPageTableQuery()->sum('ticket_cost') / 100),
        ];
    }
}
