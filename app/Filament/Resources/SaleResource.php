<?php

namespace App\Filament\Resources;

use App\Enums\Customer\CustomerStatusEnum;
use App\Enums\Sale\OrderStatusEnum;
use App\Enums\Sale\PaymentTypeEnum;
use App\Enums\SubAdmin\SubAdminRoleEnum;
use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
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
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                                    modifyQueryUsing: fn (Builder $query) => (auth()->user()->hasRole(SubAdminRoleEnum::SALESAGENT->value)) ? ($query->where('customer_status', CustomerStatusEnum::Approved->value)->where('assigned_to', auth()->id())) : ($query->where('customer_status', CustomerStatusEnum::Approved->value)),
                                )
                                ->label('Customer')
                                //->searchable()
                                //->preload()
                                ->required()
                                ->afterStateUpdated(function(Set $set, string $state/* , Model $record */){
                                    $set('payment_type', Customer::find($state)->customer_type);
                                    //$set('slug', Str::slug($state));
                                }),
                            TextInput::make('discount')
                                ->label('Discount (if applicable)')
                                ->suffix('%')
                                ->default(0)
                                ->numeric(),
                            Radio::make('payment_type')
                                ->options([
                                    PaymentTypeEnum::Cash->value => PaymentTypeEnum::Cash->name,
                                    PaymentTypeEnum::Credit->value => PaymentTypeEnum::Credit->name,
                                ])->default(0)
                                ->disabled()
                                ->dehydrated()
                                ->inline()
                                ->required()
                                /* ->columnSpanFull() */,
                            TextInput::make('totalPrice')
                                ->label('Total Price')
                                ->default(0)
                                ->numeric()
                                ->disabled()
                                ->suffix('GHs'),
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
                                //->unique(ignoreRecord: true)
                                ->live(debounce: 500)
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} - GHs {$record->price}")
                                ->afterStateUpdated(function(Get $get, Set $set, string $state){
                                    $totalPrice = $get('../../totalPrice');
                                    $oldPrice = $get('productPrice');
                                    $unitPrice = Product::find($state)->price;
                                    $newPrice = $unitPrice * $get('quantity');
                                    $set('price', $unitPrice);
                                    $set('productPrice', $newPrice);
                                    $set('../../totalPrice', ($totalPrice + $newPrice - $oldPrice));
                                    //$set('slug', Str::slug($state));
                                })
                                /* ->disableOptionWhen(fn (Select $component, string $value): bool => $value === '1') */,
                            TextInput::make('quantity')
                                ->required()
                                ->default(1)
                                ->numeric()
                                ->minValue(1)
                                ->step(1)
                                ->live(debounce: 500)
                                ->afterStateUpdated(function(Get $get, Set $set, string $state){
                                    $totalPrice = $get('../../totalPrice');
                                    $oldPrice = $get('productPrice');
                                    $newPrice = ($get('product_id') ? Product::find($get('product_id'))->price : 0) * $state;
                                    $set('productPrice', $newPrice);
                                    $set('../../totalPrice', ($totalPrice + $newPrice - $oldPrice));
                                }),
                            TextInput::make('productPrice')
                                ->required()
                                ->disabled()
                                ->suffix('GHs')
                                ->numeric(),
                            Hidden::make('price')
                                ->disabled()
                                ->dehydrated()
                        ])->addActionLabel('Add item')
                        ->deleteAction(
                            fn (Action $action) => $action->requiresConfirmation(),
                        )
                        ->columns(3)
                        ->grid(1)
                        ->minItems(1)
                        ->maxItems(Product::where('is_visible', true)->count()),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.full_name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('saleProducts.product.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable()
                    ->listWithLineBreaks(),
                TextColumn::make('saleProducts.quantity')
                    ->label('Quantity')
                    /* ->summarize([
                        Sum::make()
                    ]) */
                    ->listWithLineBreaks(),
                TextColumn::make('customer.assignedTo.name')
                    ->label('Sales Agent')
                    ->placeholder('Not Assigned')
                    ->searchable()
                    ->sortable()
                    ->visible(auth()->user()->hasPermission('sale:viewAssignedToColumn')),
                TextColumn::make('payment_type')
                    ->state(function (Model $record): string {
                        return match ((int)$record->payment_type) {
                            PaymentTypeEnum::Cash->value => PaymentTypeEnum::Cash->name,
                            PaymentTypeEnum::Credit->value => PaymentTypeEnum::Credit->name,
                        };
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->date()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_type')
                    ->label('Payment Type')
                    ->options([
                        PaymentTypeEnum::Cash->value => PaymentTypeEnum::Cash->name,
                        PaymentTypeEnum::Credit->value => PaymentTypeEnum::Credit->name,
                    ]),
                SelectFilter::make('assigned_to')
                    ->label('Sales Agent')
                    ->relationship('customer', 'first_name')
                    ->visible(auth()->user()->hasPermission('sale:viewAssignedToFilter')),
            ])
            ->actions([
                ActionGroup::make([
                    ActionsAction::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn (Sale $record) => $record->update(['order_status' => OrderStatusEnum::Approved->value]))
                        ->hidden(fn (Sale $record) => ($record->order_status === OrderStatusEnum::Approved->value || !auth()->user()->hasPermission('sale:approve'))),
                    ActionsAction::make('reject')
                        ->label('Reject')
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->action(fn (Sale $record) => $record->update(['order_status' => OrderStatusEnum::Rejected->value]))
                        ->hidden(fn (Sale $record) => ($record->order_status === OrderStatusEnum::Rejected->value || !auth()->user()->hasPermission('sale:reject'))),
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

    public static function getEloquentQuery(): Builder
    {
        if(auth()->user()->hasRole(SubAdminRoleEnum::SALESAGENT->value)){
            return parent::getEloquentQuery()->withWhereHas('customer', function ($query) {
                    $query->where('assigned_to', auth()->id());
                });
        }
        return parent::getEloquentQuery();
    }
}
