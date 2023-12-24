<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected static function getNavigationLabel(): string
    {
        return __('Home');
    }

    protected function getTitle(): string
    {
        return __('Home');
    }
}
