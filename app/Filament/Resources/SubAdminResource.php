<?php

namespace App\Filament\Resources;

use App\Enums\SubAdmin\SubAdminRoleEnum;
use App\Filament\Resources\SubAdminResource\Pages;
use App\Filament\Resources\SubAdminResource\RelationManagers;
use App\Models\SubAdmin;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Full name')
                            ->autocapitalize('words')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required(),
                        TextInput::make('password')
                            ->password()
                            ->required(),
                        Repeater::make('roles')
                            ->label('Role')
                            ->relationship()
                            ->schema([
                                Select::make('role')
                                    ->label('')
                                    ->options([
                                        SubAdminRoleEnum::ADMIN->value => SubAdminRoleEnum::ADMIN->name,
                                        SubAdminRoleEnum::SALESAGENT->value => SubAdminRoleEnum::SALESAGENT->name,
                                        SubAdminRoleEnum::FINANCE->value => SubAdminRoleEnum::FINANCE->name,
                                        SubAdminRoleEnum::WAREHOUSE->value => SubAdminRoleEnum::WAREHOUSE->name,
                                        SubAdminRoleEnum::OPERATIONS->value => SubAdminRoleEnum::OPERATIONS->name,
                                    ]),
                            ])->addable(false)
                            ->deletable(false)
                            ->required()
                    ])->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.role')
                    ->state(function (Model $record): string {
                        return match ($record->roles[0]->role) {
                            SubAdminRoleEnum::ADMIN->value => SubAdminRoleEnum::ADMIN->name,
                            SubAdminRoleEnum::SALESAGENT->value => SubAdminRoleEnum::SALESAGENT->name,
                            SubAdminRoleEnum::FINANCE->value => SubAdminRoleEnum::FINANCE->name,
                            SubAdminRoleEnum::WAREHOUSE->value => SubAdminRoleEnum::WAREHOUSE->name,
                            SubAdminRoleEnum::OPERATIONS->value => SubAdminRoleEnum::OPERATIONS->name,
                        };
                    })
                    /* ->state(function (Model $record): string {
                        return json_encode($record->roles[0]);
                    }) */
                    ->searchable()
                    ->sortable(),
                TextColumn::make('permission')
                    ->state(function (Model $record): array {
                        return $record->rolePermissions($record->roles[0]->role);
                    })
                    ->listWithLineBreaks()
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
