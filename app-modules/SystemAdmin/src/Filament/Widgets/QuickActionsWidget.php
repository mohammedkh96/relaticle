<?php

namespace Relaticle\SystemAdmin\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected string $view = 'system-admin::filament.widgets.quick-actions-widget';

    protected int|string|array $columnSpan = 'full';
}
