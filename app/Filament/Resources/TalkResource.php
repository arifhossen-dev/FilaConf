<?php

namespace App\Filament\Resources;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use App\Filament\Resources\TalkResource\Pages;
use App\Models\Talk;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Talk::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->description(fn(Talk $talk) => Str::of($talk->abstract)->limit(50)),
                ImageColumn::make('speaker.avatar')
                    ->label('Speaker Avatar')
                    ->circular()
                    ->defaultImageUrl(fn (Talk $talk) => 'https://ui-avatars.com/api/?background=a0a0a0&color=fff&name='.$talk->speaker->name),
                TextColumn::make('speaker.name')
                    ->sortable(),
                ToggleColumn::make('new_talk'),
                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn($state)=>$state->getColor()),
                IconColumn::make('length')
                    ->icon(function($state){
                        return match($state){
                            TalkLength::NORMAL => 'heroicon-o-megaphone',
                            TalkLength::LIGHTNING => 'heroicon-o-bolt',
                            TalkLength::KEYNOTE => 'heroicon-o-key',
                        };
                    })
            ])
            ->persistFiltersInSession()
            ->filtersTriggerAction(fn($action)=>$action->button()->label('Filter'))
            ->filters([
                TernaryFilter::make('new_talk'),
                SelectFilter::make('speaker')
                    ->relationship('speaker', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Filter::make('has_avatar')
                    ->label('Show only with Avatar')
                    ->toggle()
                    ->query(function (Builder $query) {
                        $query->whereHas('speaker', function (Builder $query) {
                            $query->whereNotNull('avatar');
                        });
                        return $query;
                    })
            ])
            ->actions([
                EditAction::make()
                    ->slideOver(),
                ActionGroup::make([
                    Action::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn(Talk $talk)=>$talk->approve())
                        ->visible(fn(Talk $talk)=>$talk->status === TalkStatus::SUBMITTED)
                        ->after(function(){
                            Notification::make()->success()->title('This talk was approved')
                                ->body('You can now see it in the list of talks')
                                ->send();
                        }),
                    Action::make('reject')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn(Talk $talk)=>$talk->status === TalkStatus::SUBMITTED)
                        ->action(fn(Talk $talk)=>$talk->reject())
                        ->after(function(){
                            Notification::make()->danger()->title('This talk was rejectd')
                                ->body('You can now see it in the list of talks')
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->action(fn(Collection $collection)=> $collection->each->approve()),
                    RestoreBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('export')
                    ->action(function($livewire){
                        dump($livewire->getfilteredTableQuery());
                        // $livewire->emit('export');
                    })
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
        ];
    }
}
