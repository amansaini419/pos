<?php

namespace App\Filament\Resources;

use App\Enums\SubAdmin\SubAdminRoleEnum;
use App\Filament\Resources\WarehouseResource\Pages;
use App\Filament\Resources\WarehouseResource\RelationManagers;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                /* TextColumn::make('product.name')
                    ->label('Product'),
                TextColumn::make('salesAgent.name')
                    ->label('Sales Agent')
                    ->visible(auth()->user()->hasPermission('warehouse')), */
                TextColumn::make('quantity')
                    ->label('')
                    ->summarize(Sum::make()->label('Total Stock')),
            ])
            ->groups([
                'product.name',
                'salesAgent.name'
            ])
            ->defaultGroup('product.name')
            ->groupsOnly()
            ->filters([
                SelectFilter::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name'),
                SelectFilter::make('salesagent_id')
                    ->label('Sales Agent')
                    ->relationship('salesAgent', 'name')
                    ->visible(auth()->user()->hasPermission('warehouse')),
            ])
            /* ->defaultGroup('product.name')
            ->groupsOnly() */;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWarehouses::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        if(auth()->user()->hasRole(SubAdminRoleEnum::SALESAGENT->value)){
            return parent::getEloquentQuery()->where('salesagent_id', auth()->id());
        }
        return parent::getEloquentQuery();
    }
}
