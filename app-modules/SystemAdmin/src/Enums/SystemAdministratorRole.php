<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Enums;

enum SystemAdministratorRole: string
{
    case SuperAdministrator = 'super_administrator';
    case Administrator = 'administrator';
    case Viewer = 'viewer';

    public function getLabel(): string
    {
        return match ($this) {
            self::SuperAdministrator => 'Super Administrator',
            self::Administrator => 'Administrator',
            self::Viewer => 'Viewer',
        };
    }

    /**
     * Check if role can create records
     */
    public function canCreate(): bool
    {
        return match ($this) {
            self::SuperAdministrator, self::Administrator => true,
            self::Viewer => false,
        };
    }

    /**
     * Check if role can edit records
     */
    public function canEdit(): bool
    {
        return match ($this) {
            self::SuperAdministrator, self::Administrator => true,
            self::Viewer => false,
        };
    }

    /**
     * Check if role can delete records
     */
    public function canDelete(): bool
    {
        return match ($this) {
            self::SuperAdministrator => true,
            self::Administrator, self::Viewer => false,
        };
    }

    /**
     * Check if role can manage other administrators
     */
    public function canManageAdmins(): bool
    {
        return $this === self::SuperAdministrator;
    }

    /**
     * Check if role is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this === self::SuperAdministrator;
    }
}

