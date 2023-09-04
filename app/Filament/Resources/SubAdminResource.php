<?php

namespace App\Filament\Resources;

use App\Enums\SubAdmin\SubAdminRoleEnum;
use App\Filament\Resources\SubAdminResource\Pages;
use App\Filament\Resources\SubAdminResource\RelationManagers;
use App\Models\SubAdmin;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubAdminResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $label = 'Sub Admins';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

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
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('roles.role')
                    ->state(function (Model $record): string {
                        return match ($record->roles()->role) {
                            SubAdminRoleEnum::ADMIN->value => SubAdminRoleEnum::ADMIN->name,
                            SubAdminRoleEnum::SALESAGENT->value => SubAdminRoleEnum::SALESAGENT->name,
                            SubAdminRoleEnum::FINANCE->value => SubAdminRoleEnum::FINANCE->name,
                            SubAdminRoleEnum::WAREHOUSE->value => SubAdminRoleEnum::WAREHOUSE->name,
                            SubAdminRoleEnum::OPERATIONS->value => SubAdminRoleEnum::OPERATIONS->name,
                        };
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSubAdmins::route('/'),
            'create' => Pages\CreateSubAdmin::route('/create'),
            'edit' => Pages\EditSubAdmin::route('/{record}/edit'),
        ];
    }
}
