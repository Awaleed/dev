<?php

namespace App\Filament\Actions;

use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ApproveBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'approve';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Approve'));

        $this->modalHeading(
            fn (): string =>
            $this->getPluralModelLabel(),
            // __('filament-support::actions\delete.multiple.modal.heading', ['label' => $this->getPluralModelLabel()])
        );

        $this->modalButton(__('Approve'));

        $this->successNotificationTitle(__('filament-support::actions\delete.multiple.messages.deleted'));

        $this->color('success');

        $this->icon('heroicon-s-check-circle');

        $this->requiresConfirmation();

        $this->action(function (): void {
            $this->process(static fn (Collection $records) => $records->each(fn (Model $record) => $record->update(['approval_status' => 'accepted'])));

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();

        $this->hidden(function (HasTable $livewire): bool {
            $trashedFilterState = $livewire->getTableFilterState(TrashedFilter::class) ?? [];

            if (!array_key_exists('value', $trashedFilterState)) {
                return false;
            }

            if ($trashedFilterState['value']) {
                return false;
            }

            return filled($trashedFilterState['value']);
        });
    }
}
