<?php

namespace App\Filament\Resources;

use App\Enums\Customer\CustomerStatusEnum;
use App\Enums\Sale\PaymentTypeEnum;
use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Customer;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                ])
                ->schema([
                    Section::make()
                        ->columns(2)
                        ->schema([
                            Select::make('customer_id')
                                ->relationship(
                                    name: 'customer',
                                    titleAttribute: 'full_name',
                                    modifyQueryUsing: fn (Builder $query) => $query->where('customer_status', CustomerStatusEnum::Approved->value),
                                )
                                ->label('Customer')
                                ->searchable()
                                ->preload(),
                            TextInput::make('discount')
                                ->label('Discount (if applicable)')
                                ->suffix('%')
                                ->numeric(),
                            Radio::make('payment_type')
                                ->options([
                                    PaymentTypeEnum::Cash->value => PaymentTypeEnum::Cash->name,
                                    PaymentTypeEnum::Credit->value => PaymentTypeEnum::Credit->name,
                                ])->default(0)
                                ->inline()
                                ->required()
                                ->columnSpanFull(),
                        ]),

                    Repeater::make('saleProducts')
                        ->label('Items')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                                ->relationship(
                                    name: 'product',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: fn (Builder $query) => $query->where('is_visible', true),
                                )
                                ->label('Product')
                                ->required()
                                ->live()
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} - GHs {$record->price}")
                                ->afterStateUpdated(function(Get $get, Set $set, string $state/* , Model $record */){
                                    $set('price', $state/* $record->price *  *//* $get('product_id') */);
                                    //$set('slug', Str::slug($state));
                                }),
                            TextInput::make('quantity')
                                ->required()
                                ->default(1)
                                ->numeric()
                                ->minValue(1)
                                ->step(1)
                                ->live(),
                            TextInput::make('price')
                                ->required()
                                ->disabled()
                                ->dehydrated()
                                ->suffix('GHs')
                                ->numeric()
                        ])->addActionLabel('Add item')
                        ->deleteAction(
                            fn (Action $action) => $action->requiresConfirmation(),
                        )
                        ->columns(3)
                        ->grid(1),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number'),
                TextColumn::make('customer.full_name'),
                TextColumn::make('order_status'),
                TextColumn::make('payment_type'),
                TextColumn::make('total_quantity')
                    ->summarize([
                        Sum::make()
                    ]),
                TextColumn::make('created_at')
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    /* EditAction::make(), */
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
