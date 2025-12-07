<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Exports;

use Filament\Actions\Exports\Exporter;

abstract class SystemAdminBaseExporter extends Exporter
{
    // SystemAdmin exporters don't need team scoping
}
