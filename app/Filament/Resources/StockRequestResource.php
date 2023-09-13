<?php

namespace App\Filament\Resources;

use App\Enums\SubAdmin\SubAdminRoleEnum;
use App\Enums\Warehouse\StockRequestStatusEnum;
use App\Filament\Resources\StockRequestResource\Pages;
use App\Filament\Resources\StockRequestResource\RelationManagers;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\StockRequest;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockRequestResource extends Resource
{
    protected static ?string $model = StockRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Select::make('product_id')
                            ->options(Product::where('is_visible', true)->get()->pluck('name', 'id'))
                            ->label('Product')
                            ->required(),
                        TextInput::make('quantity')
                            ->required()
                            ->default(1)
                            ->numeric()
                            ->minValue(1)
                            ->step(1),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Quantity'),
                IconColumn::make('request_status')
                    ->label('Status')
                    ->icon(fn (int $state): string => match ($state) {
                        StockRequestStatusEnum::Pending->value => 'heroicon-o-clock',
                        StockRequestStatusEnum::Approved->value => 'heroicon-o-check',
                        StockRequestStatusEnum::Rejected->value => 'heroicon-o-no-symbol',
                    })
                    ->color(fn (int $state): string => match ($state) {
                        StockRequestStatusEnum::Pending->value => 'warning',
                        StockRequestStatusEnum::Approved->value => 'success',
                        StockRequestStatusEnum::Rejected->value => 'danger',
                    }),
                TextColumn::make('requestedBy.name')
                    ->label('Requested By')
                    ->visible(auth()->user()->hasPermission('stockRequest:viewRequestedByColumn')),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('request_status')
                    ->label('Status')
                    ->options([
                        StockRequestStatusEnum::Pending->value => StockRequestStatusEnum::Pending->name,
                        StockRequestStatusEnum::Approved->value => StockRequestStatusEnum::Approved->name,
                        StockRequestStatusEnum::Rejected->value => StockRequestStatusEnum::Rejected->name,
                    ]),
                SelectFilter::make('requested_by')
                    ->label('Sales Agent')
                    ->relationship('requestedBy', 'name')
                    ->visible(auth()->user()->hasPermission('stockRequest:viewRequestedByFilter')),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function (StockRequest $record): void {
                            if(Inventory::where('product_id', $record->product_id)->sum('quantity') >= $record->quantity){
                                $record->update(['request_status' => StockRequestStatusEnum::Approved->value]);
                                Notification::make()
                                    ->success()
                                    ->title('Stock request approved')
                                    ->body('The stock request has been approved successfully.')
                                    ->send();
                            }
                            else{
                                Notification::make()
                                    ->warning()
                                    ->title('Stock request approved warning')
                                    ->body('You don\'t have stock in main inventory to proceed.')
                                    ->send();
                            }
                        })
                        ->hidden(fn (StockRequest $record) => ($record->request_status === StockRequestStatusEnum::Approved->value || !auth()->user()->hasPermission('stockRequest:approve'))),

                    Action::make('reject')
                        ->label('Reject')
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->action(function (StockRequest $record) {
                            $record->update(['request_status' => StockRequestStatusEnum::Rejected->value]);
                            Notification::make()
                                ->success()
                                ->title('Stock request rejected')
                                ->body('The stock request has been rejected successfully.')
                                ->send();
                        })
                        ->hidden(fn (StockRequest $record) => ($record->request_status !== StockRequestStatusEnum::Pending->value || !auth()->user()->hasPermission('stockRequest:reject'))),
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
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
            'index' => Pages\ListStockRequests::route('/'),
            'create' => Pages\CreateStockRequest::route('/create'),
            'edit' => Pages\EditStockRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        if(auth()->user()->hasRole(SubAdminRoleEnum::SALESAGENT->value)){
            return parent::getEloquentQuery()->where('requested_by', auth()->id())->latest();
        }
        return parent::getEloquentQuery()->latest();
    }
}
