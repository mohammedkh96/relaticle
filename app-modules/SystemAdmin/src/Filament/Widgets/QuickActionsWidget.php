<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Widgets;

use Filament\Widgets\Widget;
use Relaticle\SystemAdmin\Models\SystemAdministrator;

class QuickActionsWidget extends Widget
{
    protected string $view = 'system-admin::widgets.quick-actions-widget';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        return [
            'user' => auth()->user(),
        ];
    }
}
