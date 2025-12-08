@php
    $settings = app(\App\Settings\InvoiceSettings::class);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact;
            }
        }

        .glass {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-900">

    <div
        class="max-w-4xl mx-auto my-10 bg-white shadow-2xl rounded-xl overflow-hidden glass print:shadow-none print:m-0 print:border-none print:rounded-none">

        <!-- Header Section -->
        <div class="p-8 pb-0 flex justify-between items-start">
            <div class="w-1/2">
                @if($settings->company_logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($settings->company_logo) }}" alt="Logo"
                        class="h-16 object-contain mb-4">
                @else
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="h-16 object-contain mb-4">
                @endif

                <div class="text-sm text-gray-500 space-y-1">
                    @if($settings->company_address)
                        <p class="whitespace-pre-line">{{ $settings->company_address }}</p>
                    @endif
                    @if($settings->company_phone)
                        <p>Tel: {{ $settings->company_phone }}</p>
                    @endif
                    @if($settings->company_email)
                        <p>Email: {{ $settings->company_email }}</p>
                    @endif
                </div>
            </div>

            <div class="text-right w-1/2">
                <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight">INVOICE</h2>
                <p class="text-gray-500 mt-1 font-medium">#{{ $invoice->invoice_number }}</p>
                <div class="mt-4 inline-block">
                    <span
                        class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        {{ $invoice->status === \App\Enums\InvoiceStatus::PAID ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                        {{ $invoice->status instanceof \App\Enums\InvoiceStatus ? $invoice->status->getLabel() : $invoice->status }}
                    </span>
                </div>
            </div>
        </div>

        <hr class="border-gray-100 my-8 mx-8">

        <!-- Client Info & Dates -->
        <div class="px-8 grid grid-cols-2 gap-12">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Billed To</p>
                <h3 class="font-bold text-xl text-gray-800">{{ $invoice->company->name }}</h3>
                <div class="text-sm text-gray-500 mt-1 space-y-1">
                    @if($invoice->company->address)
                    <p>{{ $invoice->company->address }}</p> @endif
                    @if($invoice->company->city)
                    <p>{{ $invoice->company->city }}, {{ $invoice->company->country }}</p> @endif
                    <p>{{ $invoice->company->email }}</p>
                    @if($invoice->company->phone)
                    <p>{{ $invoice->company->phone }}</p> @endif
                </div>
            </div>
            <div class="flex justify-end text-right">
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Issue Date</p>
                        <p class="font-semibold text-gray-800">{{ $invoice->issue_date->format('F d, Y') }}</p>
                    </div>
                    @if($invoice->due_date)
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Due Date</p>
                            <p class="font-semibold text-gray-800">{{ $invoice->due_date->format('F d, Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Single Item Description -->
        <div class="p-8">
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-100">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-bold text-gray-800">Description</h4>
                    <h4 class="font-bold text-gray-800 text-right">Amount</h4>
                </div>
                <div class="border-t border-gray-200 my-2"></div>

                @if($invoice->items && count($invoice->items) > 0)
                    @foreach($invoice->items as $item)
                        <div class="flex justify-between items-start py-2">
                            <div class="text-gray-700 whitespace-pre-line">{{ $item['description'] ?? 'Event Participation' }}
                            </div>
                            <div class="font-semibold text-gray-900">${{ number_format($item['amount'] ?? 0, 2) }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="flex justify-between items-start py-2">
                        <div class="text-gray-700">Event Participation</div>
                        <div class="font-semibold text-gray-900">${{ number_format($invoice->total_amount, 2) }}</div>
                    </div>
                @endif
            </div>

            <div class="flex justify-end mt-4">
                <div class="w-64">
                    <div class="flex justify-between items-center py-2 border-t border-gray-900">
                        <span class="text-lg font-bold text-gray-900">Total</span>
                        <span
                            class="text-2xl font-bold text-indigo-600">${{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Notes -->
        <div class="bg-gray-50 p-8 border-t border-gray-100 flex justify-between items-end">
            <div class="w-2/3">
                @if($invoice->notes)
                    <div class="mb-4">
                        <h5 class="font-bold text-xs text-gray-400 uppercase tracking-wider mb-1">Notes</h5>
                        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $invoice->notes }}</p>
                    </div>
                @endif

                @if($settings->invoice_note)
                    <div class="text-xs text-gray-500 whitespace-pre-line border-t border-gray-200 pt-4 mt-4">
                        {{ $settings->invoice_note }}
                    </div>
                @endif
            </div>

            <div class="w-1/3 flex justify-end">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('filament.sysadmin.resources.invoices.view', $invoice)) }}"
                    alt="QR Code" class="h-24 w-24 opacity-80 mix-blend-multiply">
            </div>
        </div>
    </div>

    <div class="fixed bottom-10 right-10 no-print">
        <button onclick="window.print()"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            Print
        </button>
    </div>

</body>

</html>