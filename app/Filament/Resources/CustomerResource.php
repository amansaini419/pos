<?php

namespace App\Filament\Resources;

use App\Enums\Customer\CustomerStatusEnum;
use App\Enums\Customer\CustomerTypeEnum;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('first_name')
                                    ->alpha()
                                    ->required()
                                    //->live(onBlur: true)
                                    ->afterStateUpdated(function(string $operation, $state, Set $set){
                                        if($operation !== 'create' && $operation !== 'edit'){
                                            return;
                                        }
                                        $set('first_name', Str::title($state));
                                    }),
                                TextInput::make('last_name')
                                    ->required()
                                    ->alpha()
                                    //->live(onBlur: true)
                                    ->afterStateUpdated(function(string $operation, $state, Set $set){
                                        if($operation !== 'create' && $operation !== 'edit'){
                                            return;
                                        }
                                        $set('last_name', Str::title($state));
                                    }),
                                /* TextInput::make('customer_number')
                                    ->label('Customer Number')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->unique(Customer::class, 'customer_number', ignoreRecord: true), */
                                TextInput::make('phone_number')
                                    ->tel()
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    //->live(onBlur: true)
                                    ->afterStateUpdated(function(string $operation, $state, Set $set){
                                        if($operation !== 'create' && $operation !== 'edit'){
                                            return;
                                        }
                                        $set('email', Str::lower($state));
                                    }),
                                Radio::make('customer_type')
                                    ->options([
                                        CustomerTypeEnum::Cash->value => CustomerTypeEnum::Cash->name,
                                        CustomerTypeEnum::Credit->value => CustomerTypeEnum::Credit->name,
                                    ])->inline()
                                    ->default(0)
                                    ->required(),
                                TextInput::make('location')
                                    ->required()
                                    //->live(onBlur: true)
                                    ->afterStateUpdated(function(string $operation, $state, Set $set){
                                        if($operation !== 'create' && $operation !== 'edit'){
                                            return;
                                        }
                                        $set('location', Str::title($state));
                                    }),
                                TextInput::make('registration_number')
                                    ->required(),
                                TextInput::make('ghana_card_number')
                                    ->required(),

                                Grid::make([
                                    'default' => 3,
                                ])
                                ->schema([
                                    FileUpload::make('customer_photo_path')
                                        ->label('Customer Photo')
                                        ->directory('customer-photo')
                                        //->preserveFilenames()
                                        //->image()
                                        ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg', 'application/pdf'])
                                        ->imageEditor()
                                        ->required(),
                                    FileUpload::make('ghana_card_path')
                                        ->label('Ghana Card')
                                        ->directory('ghana-card')
                                        //->preserveFilenames()
                                        //->image()
                                        ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg', 'application/pdf'])
                                        ->imageEditor()
                                        ->required(),
                                    FileUpload::make('business_certificate_path')
                                        ->label('Business Certificate')
                                        ->directory('business-certificate')
                                        //->preserveFilenames()
                                        //->image()
                                        ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg', 'application/pdf'])
                                        ->imageEditor()
                                        ->required(),
                                ]),

                                Select::make('assigned_to')
                                    ->hidden(auth()->user()->hasRole('sales_agent'))
                                    ->options(User::all()->load('roles')->where('role', 'like', 'sales_agent')->pluck('name', 'id'))
                                    ->label('Sales Agent')
                                    ->searchable()
                            ])->columns(2)
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_number')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name'])
                    ->label('Customer name'),
                IconColumn::make('customer_status')
                    ->label('Status')
                    ->icon(fn (int $state): string => match ($state) {
                        CustomerStatusEnum::Pending->value => 'heroicon-o-clock',
                        CustomerStatusEnum::Approved->value => 'heroicon-o-check',
                        CustomerStatusEnum::Blacklisted->value => 'heroicon-o-no-symbol',
                    })
                    ->color(fn (int $state): string => match ($state) {
                        CustomerStatusEnum::Pending->value => 'warning',
                        CustomerStatusEnum::Approved->value => 'success',
                        CustomerStatusEnum::Blacklisted->value => 'danger',
                    }),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('phone_number')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('assignedTo.name')
                    ->label('Sales Agent')
                    ->placeholder('Not Assigned')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_type')
                    ->state(function (Model $record): string {
                        return match ($record->customer_type) {
                            CustomerTypeEnum::Cash->value => CustomerTypeEnum::Cash->name,
                            CustomerTypeEnum::Credit->value => CustomerTypeEnum::Credit->name,
                        };
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('customer_type')
                    ->label('Customer Type')
                    ->options([
                        CustomerTypeEnum::Cash->value => CustomerTypeEnum::Cash->name,
                        CustomerTypeEnum::Credit->value => CustomerTypeEnum::Credit->name,
                    ]),
                SelectFilter::make('assigned_to')
                    ->label('Sales Agent')
                    ->relationship('assignedTo', 'name'),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('assign')
                        ->label('Assign Sales Agent')
                        ->icon('heroicon-o-user')
                        ->color('warning')
                        ->action(fn (Customer $record) => $record->update(['customer_status' => CustomerStatusEnum::Blacklisted->value]))
                        ->hidden(fn (Customer $record) => $record->assigned_to !== 0),
                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn (Customer $record) => $record->update(['customer_status' => CustomerStatusEnum::Approved->value]))
                        ->hidden(fn (Customer $record) => $record->customer_status === CustomerStatusEnum::Approved->value),
                    Action::make('blacklist')
                        ->label('Blacklist')
                        ->color('danger')
                        ->icon('heroicon-o-no-symbol')
                        ->action(fn (Customer $record) => $record->update(['customer_status' => CustomerStatusEnum::Blacklisted->value]))
                        ->hidden(fn (Customer $record) => $record->customer_status === CustomerStatusEnum::Blacklisted->value),
                    ViewAction::make()
                        ->color('info'),
                    EditAction::make()
                        ->color('info'),
                ])
            ])
            ->bulkActions([
                /* Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]), */
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/new'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
