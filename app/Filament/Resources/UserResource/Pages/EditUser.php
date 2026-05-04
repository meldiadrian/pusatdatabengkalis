<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     $data['created_by'] ??= auth()->id();

    //     return $data;
    // }


    protected function authorizeAccess(): void
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && $this->record->id !== $user->id) {
            abort(403);
        }
    }
}
