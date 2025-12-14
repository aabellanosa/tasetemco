<x-app-layout>
    <div class="container">
        <x-slot name="header">
            <h3 class="text-center mb-4">
                Financial Statement<br>
                {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}
            </h3>
        </x-slot>
        <div class="text-end mt-4">
            <a
                href="#"
                id="printLink"
                class="btn btn-outline-secondary mb-2">
                    🖨️Print Statement
            </a>
        </div>

        <table class="table table-borderless w-100">
            <tbody>
            @foreach ($lineItems as $item)
                @php
                    $isSubtotal = str_starts_with($item['code'], 'total_');
                    $isGrandTotal = in_array($item['code'], ['total_assets']);
                @endphp

                <tr class="{{ $isSubtotal ? 'subtotal' : '' }} {{ $isGrandTotal ? 'grand-total' : '' }}">
                    <td class="label indent-{{ $item['indent'] }}">
                        {{ $item['title'] }}
                    </td>

                    <td class="amount">
                        {{ $item['col2'] !== null ? number_format($item['col2'], 2) : '' }}
                    </td>
                    <td class="amount">
                        {{ $item['col3'] !== null ? number_format($item['col3'], 2) : '' }}
                    </td>
                    <td class="amount">
                        {{ $item['col4'] !== null ? number_format($item['col4'], 2) : '' }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @push('styles')
        <style>

            /* base indentation – applies everywhere */
            .indent-0 { padding-left: 0 !important; }
            .indent-1 { padding-left: 12px !important; }
            .indent-2 { padding-left: 24px !important; }
            .indent-3 { padding-left: 36px !important; }
            .indent-4 { padding-left: 48px !important; }


            @media print {
                @page {
                    size: A4 portrait;
                    margin: 12mm;
                }

                body {
                    font-family: "Times New Roman", serif;
                    font-size: 11px;
                    line-height: 1.15;
                    margin: 0;
                }

                table {
                    border-collapse: collapse;
                    widows: 100%;
                }

                tr {
                    height: auto !important;
                }

                th, td {
                    padding: 2px 4px !important;
                    line-height: 1.1 !important;
                    vertical-align: middle,
                }

                .label {
                    width: 40%;
                }

                .amount {
                    text-align: right;
                    width: 20%;
                    white-space: nowrap;
                }

                .subtotal td {
                    font-weight: bold;
                    border-bottom: 1px solid #000;
                    padding-bottom: 2px !important;
                }

                .grand-total td {
                    font-weight: bold;
                    border-bottom: 3px double #000;
                    padding-bottom: 2px !important;
                }

                /* Hide everything else */
                nav, .btn {
                    display: none !important;
                }
            }
        </style>
    @endpush    
    
    @push('scripts')
        <script>
            window.onload = () => {
                window.print()
                const printLink = document.getElementById('printLink');

                printLink.addEventListener('click', function (event) {
                    event.preventDefault(); // Prevent navigation
                    window.print(); // Trigger browser print dialog
                });
            };
            
        </script>
    @endpush


</x-app-layout>




    