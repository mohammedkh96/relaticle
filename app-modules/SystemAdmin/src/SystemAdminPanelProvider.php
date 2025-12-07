<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin;

use Awcodes\Overlook\OverlookPlugin;
use Awcodes\Overlook\Widgets\OverlookWidget;
use Exception;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Relaticle\SystemAdmin\Filament\Pages\Dashboard;

final class SystemAdminPanelProvider extends PanelProvider
{
    /**
     * @throws Exception
     */
    public function panel(Panel $panel): Panel
    {
        $panel = $panel->id('sysadmin');

        // Configure domain or path based on environment
        if ($domain = config('app.sysadmin_domain')) {
            $panel->domain($domain);
        } else {
            $panel->path(config('app.sysadmin_path', 'sysadmin'));
        }

        return $panel
            ->login()
            ->emailVerification()
            ->authGuard('sysadmin')
            ->authPasswordBroker('system_administrators')
            ->strictAuthorization()
            ->spa()
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->brandName('Invest Expo CRM')
            ->homeUrl(fn(): string => Dashboard::getUrl())
            ->discoverResources(in: base_path('app-modules/SystemAdmin/src/Filament/Resources'), for: 'Relaticle\\SystemAdmin\\Filament\\Resources')
            ->discoverPages(in: base_path('app-modules/SystemAdmin/src/Filament/Pages'), for: 'Relaticle\\SystemAdmin\\Filament\\Pages')
            ->discoverWidgets(in: base_path('app-modules/SystemAdmin/src/Filament/Widgets'), for: 'Relaticle\\SystemAdmin\\Filament\\Widgets')
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Invest Expo')
                    ->icon('heroicon-o-calendar')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('CRM'),
                NavigationGroup::make()
                    ->label('Task Management'),
                NavigationGroup::make()
                    ->label('Communications'),
                NavigationGroup::make()
                    ->label('User Management'),
                NavigationGroup::make()
                    ->label('Content'),
            ])
            ->navigation(function (\Filament\Navigation\NavigationBuilder $builder): \Filament\Navigation\NavigationBuilder {
                $events = \App\Models\Event::orderBy('year', 'desc')->get();

                // Build sub-navigation items for Participations
                $participationItems = [
                    \Filament\Navigation\NavigationItem::make('All Participations')
                        ->url('/sysadmin/participations')
                        ->icon('heroicon-o-building-storefront')
                        ->isActiveWhen(fn() => request()->is('sysadmin/participations') && !request()->query('event')),
                ];

                foreach ($events as $event) {
                    $participationItems[] = \Filament\Navigation\NavigationItem::make("{$event->year}")
                        ->url("/sysadmin/participations?event={$event->id}")
                        ->icon('heroicon-o-calendar-days')
                        ->isActiveWhen(fn() => request()->query('event') == $event->id && request()->is('sysadmin/participations*'));
                }

                // Build sub-navigation items for Visitors
                $visitorItems = [
                    \Filament\Navigation\NavigationItem::make('All Visitors')
                        ->url('/sysadmin/visitors')
                        ->icon('heroicon-o-users')
                        ->isActiveWhen(fn() => request()->is('sysadmin/visitors') && !request()->query('event')),
                ];

                foreach ($events as $event) {
                    $visitorItems[] = \Filament\Navigation\NavigationItem::make("{$event->year}")
                        ->url("/sysadmin/visitors?event={$event->id}")
                        ->icon('heroicon-o-calendar-days')
                        ->isActiveWhen(fn() => request()->query('event') == $event->id && request()->is('sysadmin/visitors*'));
                }

                return $builder
                    ->groups([
                        \Filament\Navigation\NavigationGroup::make('Invest Expo')
                            ->icon('heroicon-o-calendar')
                            ->collapsible()
                            ->items([
                                \Filament\Navigation\NavigationItem::make('Events')
                                    ->url('/sysadmin/events')
                                    ->icon('heroicon-o-calendar')
                                    ->badge(\App\Models\Event::count())
                                    ->isActiveWhen(fn() => request()->is('sysadmin/events*')),
                                \Filament\Navigation\NavigationItem::make('Participations')
                                    ->url('/sysadmin/participations')
                                    ->icon('heroicon-o-building-storefront')
                                    ->badge(\App\Models\Participation::count())
                                    ->isActiveWhen(fn() => request()->is('sysadmin/participations*'))
                                    ->childItems($participationItems),
                                \Filament\Navigation\NavigationItem::make('Visitors')
                                    ->url('/sysadmin/visitors')
                                    ->icon('heroicon-o-users')
                                    ->badge(\App\Models\Visitor::count())
                                    ->isActiveWhen(fn() => request()->is('sysadmin/visitors*'))
                                    ->childItems($visitorItems),
                            ]),
                    ])
                    ->items(\Relaticle\SystemAdmin\Filament\Resources\CompanyResource::getNavigationItems())
                    ->items(\Relaticle\SystemAdmin\Filament\Resources\PeopleResource::getNavigationItems())
                    ->items(\Relaticle\SystemAdmin\Filament\Resources\OpportunityResource::getNavigationItems())
                    ->items(\Relaticle\SystemAdmin\Filament\Resources\TaskResource::getNavigationItems())
                    ->items(\Relaticle\SystemAdmin\Filament\Resources\NoteResource::getNavigationItems())
                    ->items(\Relaticle\SystemAdmin\Filament\Resources\TeamResource::getNavigationItems())
                    ->items(\Relaticle\SystemAdmin\Filament\Resources\UserResource::getNavigationItems())
                    ->items(\Relaticle\SystemAdmin\Filament\Pages\CommunicationSettingsPage::getNavigationItems())
                    ->items(\Relaticle\SystemAdmin\Filament\Pages\BulkWhatsAppPage::getNavigationItems())
                    ->items(\Relaticle\SystemAdmin\Filament\Pages\BulkEmailPage::getNavigationItems());
            })
            ->globalSearch()
            ->darkMode()
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                OverlookWidget::class,
            ])
            ->plugins([
                OverlookPlugin::make()
                    ->sort(0)
                    ->abbreviateCount(false)
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'md' => 3,
                        'lg' => 4,
                        'xl' => 5,
                        '2xl' => null,
                    ]),
            ])
            ->databaseNotifications()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
