Invest Expo CRM - Complete Project Documentation
Project Overview
Invest Expo CRM is a next-generation open-source Customer Relationship Management platform built with Laravel 12 and Filament v4. It's designed for managing events, companies, visitors, participations, and comprehensive communication workflows.

Tech Stack
Backend: Laravel 12.37.0, PHP 8.4.5
Frontend: Livewire 3.0, Filament v4
Database: SQLite (development), MySQL (production ready)
Queue: Laravel Queue with database driver
Email: Laravel Mail with SMTP support
WhatsApp: Meta WhatsApp Cloud API integration
Icons: Heroicons
Styling: Tailwind CSS (via Filament)
Architecture
Multi-Panel Structure
App Panel (/app): Tenant-scoped panel for regular users
SystemAdmin Panel (/sysadmin): Global admin panel without tenant scoping
Module Structure
app-modules/
├── SystemAdmin/
│   └── src/
│       ├── Filament/
│       │   ├── Pages/
│       │   ├── Resources/
│       │   └── Imports/
│       └── SystemAdminServiceProvider.php
├── Documentation/
└── OnboardSeed/
Core Features
1. Resource Management
Companies
Full CRUD operations
Fields: name, email, phone, website, address, industry, size, revenue
Relationships: people, opportunities, participations
Account owner assignment
WhatsApp integration
CSV/Excel import support
People
Contact management
Linked to companies
Email and phone tracking
Position and department
Events
Event planning and management
Date tracking (start/end)
Location and description
Visitor and participation tracking
CSV/Excel import
Visitors
Visitor registration
Contact information
Event participation tracking
Duplicate detection on import
CSV/Excel import
Participations
Links visitors to events and companies
Tracks attendance and engagement
Status management
CSV/Excel import
Opportunities
Sales pipeline management
Stage tracking
Value and probability
Expected close dates
Linked to companies
Tasks
Task management with custom fields
Assignees (many-to-many relationship)
Creator tracking
Status and priority
Due dates
Kanban board view
2. Communication Features
Bulk Email
Dedicated Page: /sysadmin/bulk-email-page
Table Action: Available on resource tables
Recipients:
All companies with email
All visitors with email
Import from CSV
Manual email entry
Features:
Rich email composer
Queue-based sending
Background processing
Email templates (Mailable)
Bulk WhatsApp
Dedicated Page: /sysadmin/bulk-whatsapp-page
Table Action: Available on resource tables
Message Types:
Free Text: For 24-hour window (user-initiated conversations)
Templates: Pre-approved Meta templates for business-initiated messages
Template Support:
Template name input
Dynamic parameters ({{1}}, {{2}}, etc.)
Repeater for parameter values
Recipients:
All companies with phone
All visitors with phone
Import from CSV
Manual phone entry
Features:
Queue-based sending
WhatsApp Cloud API integration
Rate limiting compliance
Communication Settings UI
Location: /sysadmin/communication-settings-page
Tabbed Interface:
Email Settings Tab: SMTP configuration
WhatsApp Settings Tab: Meta API credentials
Database Storage: Settings stored in settings table
Runtime Override: Overrides .env values via AppServiceProvider
Fields:
Email: mailer, host, port, username, password, encryption, from address, from name
WhatsApp: API URL, phone number ID, access token
3. Import/Export
CSV/Excel Import
Resources: Companies, Events, Visitors, Participations
Features:
Column mapping
Validation
Duplicate detection (Visitors)
Progress tracking
Error reporting
Templates: Sample CSV files in .agent/import_templates/
4. Task Board
Kanban View: Drag-and-drop task management
Team Filtering: Filter by assigned team
Status Columns: Customizable workflow stages
Quick Actions: Create, edit, assign tasks
Available In: Both App and SystemAdmin panels
5. Performance Optimizations
Navigation Badge Caching
Cached counts for resource badges
Tenant-scoped caching
Auto-invalidation via Observer
Cache keys: navigation_badge_{resource}_{tenant_id}
Observer Pattern
ClearNavigationBadgeCacheObserver
Registered for: Company, People, Opportunity, Task, Event, Visitor, Participation
Clears cache on: created, updated, deleted, restored
6. WhatsApp Integration
Service Layer
App\Services\WhatsAppService
Methods:
sendMessage(string $phone, string $message): Free-form text
sendTemplate(string $phone, string $templateName, array $parameters): Template messages
Error handling and logging
Graceful degradation (logs if API not configured)
Queue Jobs
App\Jobs\SendWhatsAppMessage
Supports both text and template messages
Automatic retry on failure
Error logging
Actions
App\Filament\Actions\BulkWhatsAppAction: Table bulk action
SendWhatsAppAction: Single record action (Companies, Participations)
7. Email Integration
Mailable
App\Mail\BulkEmail
Blade template: resources/views/emails/bulk-email.blade.php
Queue-based sending
Actions
App\Filament\Actions\BulkEmailAction: Table bulk action
Database Schema
Key Tables
companies: Company records
people: Contact persons
events: Event management
visitors: Event visitors
participations: Event attendance (pivot)
opportunities: Sales pipeline
tasks: Task management
task_user: Task assignees (pivot)
settings: Spatie Laravel Settings storage
Relationships
Company → People (one-to-many)
Company → Opportunities (one-to-many)
Company → Participations (one-to-many)
Event → Participations (one-to-many)
Event → Visitors (through participations)
Task → Assignees (many-to-many via task_user)
Task → Creator (belongs to User)
Configuration
Environment Variables
Email (SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"
WhatsApp (Meta Cloud API)
WHATSAPP_API_URL=https://graph.facebook.com/v21.0
WHATSAPP_API_TOKEN=your_access_token
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
Queue
QUEUE_CONNECTION=database
Settings Package
Package: spatie/laravel-settings
Config: config/settings.php
Settings Class: App\Settings\CommunicationSettings
Migration: Seeded with null values for all properties
Key Files & Locations
Resources
app-modules/SystemAdmin/src/Filament/Resources/
├── CompanyResource.php
├── PeopleResource.php
├── EventResource.php
├── VisitorResource.php
├── ParticipationResource.php
├── OpportunityResource.php
└── TaskResource.php
Pages
app-modules/SystemAdmin/src/Filament/Pages/
├── Dashboard.php
├── TasksBoard.php
├── BulkEmailPage.php
├── BulkWhatsAppPage.php
└── CommunicationSettingsPage.php
Importers
app-modules/SystemAdmin/src/Filament/Imports/
├── CompanyImporter.php
├── EventImporter.php
├── VisitorImporter.php
└── ParticipationImporter.php
Actions
app/Filament/Actions/
├── BulkEmailAction.php
└── BulkWhatsAppAction.php
Jobs
app/Jobs/
└── SendWhatsAppMessage.php
Services
app/Services/
└── WhatsAppService.php
Observers
app/Observers/
└── ClearNavigationBadgeCacheObserver.php
Views
resources/views/
├── filament/pages/
│   ├── bulk-email.blade.php
│   ├── bulk-whatsapp.blade.php
│   └── communication-settings-page.blade.php
└── emails/
    └── bulk-email.blade.php
User Roles & Authentication
System Administrator
Email: sysadmin@relaticle.com
Password: password
Panel: /sysadmin
Access: All resources, no tenant scoping
Regular User
Email: manuk.minasyan1@gmail.com
Password: password
Panel: /app
Access: Tenant-scoped resources
Running the Application
Development
# Install dependencies
composer install
npm install
# Setup environment
cp .env.example .env
php artisan key:generate
# Run migrations
php artisan migrate
# Start queue worker (REQUIRED for emails/WhatsApp)
php artisan queue:work
# Start dev server
php artisan serve
# Compile assets
npm run dev
Production
# Optimize
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
# Build assets
npm run build
# Run queue worker as daemon
php artisan queue:work --daemon
Important Notes
Filament v4 Specifics
Section component is in Filament\Schemas\Components\Section (not Forms)
Tabs component is in Filament\Schemas\Components\Tabs
Form schemas use Schema instead of Form class
Property types must match parent class exactly
WhatsApp Compliance
24-Hour Rule: Free text only works within 24 hours of user message
Templates Required: Business-initiated messages need pre-approved templates
Template Creation: Done in Meta Business Manager
Parameters: Use {{1}}, {{2}} format in templates
Queue Worker
Critical: Must run php artisan queue:work for:
Bulk emails to send
Bulk WhatsApp messages to send
Monitoring: Check jobs and failed_jobs tables
Retry: Use php artisan queue:retry all for failed jobs
Cache Management
Navigation badges cached per tenant
Auto-cleared on model changes
Manual clear: php artisan cache:clear
CSV Import Templates
Located in .agent/import_templates/:

companies_template.csv
events_template.csv
visitors_template.csv
participations_template.csv
bulk_email_template.csv
bulk_whatsapp_template.csv
Future Enhancements
Potential Features
Email Templates: Visual template builder
WhatsApp Templates: In-app template management
Analytics Dashboard: Communication metrics
Scheduled Campaigns: Time-based sending
Segmentation: Advanced recipient filtering
A/B Testing: Campaign optimization
Webhook Handlers: WhatsApp delivery status
Multi-language: Template translations
Contact Lists: Saved recipient groups
Campaign History: Audit trail
Troubleshooting
Common Issues
1. Class "Filament\Forms\Components\Section" not found

Solution: Use Filament\Schemas\Components\Section instead
2. MissingSettings Exception

Solution: Run migration to seed settings table
Command: php artisan migrate
3. Messages not sending

Solution: Ensure queue worker is running
Command: php artisan queue:work
4. WhatsApp API errors

Check credentials in Communication Settings
Verify Meta app is in production mode
Ensure templates are approved
5. Email not sending

Verify SMTP settings in Communication Settings
Check queue worker is running
Review failed_jobs table
License
AGPL-3.0

Credits
Built with Laravel, Filament, and Livewire.