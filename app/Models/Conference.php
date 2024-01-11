<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'venue_id' => 'integer',
        'region' => Region::class,
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->label('Conference Name')
                ->required()
                ->maxLength(255),
            MarkdownEditor::make('description')
                ->required(),
            DateTimePicker::make('start_date')
                ->required()
                ->native(false),
            DateTimePicker::make('end_date')
                ->required()
                ->native(false),
            Toggle::make('is_published')
                ->default(true),
            Select::make('status')
                ->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'archived' => 'Archived',
                ])
                ->required(),
            Select::make('region')
                ->live()
                ->enum(Region::class)
                ->options(Region::class),
            Select::make('venue_id')
                ->searchable()
                ->preload()
                ->createOptionForm(Venue::getForm())
                ->editOptionForm(Venue::getForm())
                ->relationship('venue', 'name', modifyQueryUsing:function(Builder $query, Get $get){
                    return $query->where('region', $get('region'));
                }),
        ];
    }
}
