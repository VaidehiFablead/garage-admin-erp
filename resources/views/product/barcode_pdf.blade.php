<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Barcode - {{ $product->name }}</title>

    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .print-container {
            width: 190mm;
            margin: 0;
            padding: 0;
        }

        /*
         * IMPORTANT:
         * Fixed 190mm A4 content width
         * 3 columns = 63.33mm each
         */
        .barcode-grid {
            width: 190mm;
            margin: 0;
            padding: 0;
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* ONLY direct TD of main barcode table */
        .barcode-grid > tbody > tr > td {
            width: 63.33mm;
            height: 34mm;
            padding: 2mm 1.5mm;
            margin: 0;
            vertical-align: top;
            text-align: center;
        }

        /*
         * Barcode label
         */
        .label-box {
            width: 60mm;
            height: 30mm;
            margin: 0 auto;
            padding: 0;
            border: 1px solid #333;
            border-radius: 4px;
            overflow: hidden;
            background: #fff;
            page-break-inside: avoid;
        }

        /*
         * Inner layout
         */
        .layout-table {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .layout-table td {
            padding: 0;
            margin: 0;
            vertical-align: middle;
        }

        /*
         * LEFT BARCODE
         */
        .left-section {
            width: 35% !important;
            border-right: 1px solid #333;
            text-align: center;
        }

        .left-section img {
            display: block;
            margin: auto;
            transform: rotate(-90deg);
            max-height: 28mm;
            max-width: 18mm;
        }

        /*
         * RIGHT DETAILS
         */
        .right-section {
            width: 65% !important;
            padding: 2mm !important;
            vertical-align: top !important;
            text-align: left;
        }

        .company {
            font-size: 7px;
            font-weight: 800;
            border-bottom: 1px solid #333;
            padding-bottom: 1px;
            margin-bottom: 2px;
            text-transform: uppercase;
            line-height: 1.1;
            word-wrap: break-word;
        }

        .details-table {
            width: 100%;
            font-size: 7px;
            font-weight: 700;
            margin: 0 0 2px 0;
            padding: 0;
            line-height: 1.1;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .details-table td {
            padding: 0;
            margin: 0;
            vertical-align: top;
        }

        .mrp-section {
            font-size: 7px;
            font-weight: 700;
            margin-top: 2px;
        }

        .mrp-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .mrp-table td {
            padding: 0;
            margin: 0;
            vertical-align: top;
        }

        .tax-text {
            font-size: 6px;
            font-weight: bold;
            margin-top: 2px;
        }

        .barcode-item {
            width: 60mm;
            height: 30mm;
            margin: 0 auto;
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    <h4 style="
        margin: 0 0 8px 0;
        text-transform: uppercase;
        font-family: Arial, sans-serif;
        text-align: center;
    ">
        All Product Barcodes
    </h4>

    <div class="print-container">

        @if(count($barcodes) > 0)

            <table class="barcode-grid">

                @foreach($barcodes as $index => $bc)

                    {{-- START ROW --}}
                    @if($index % 3 == 0)
                        <tr>
                    @endif

                    <td>

                        <div class="barcode-item">

                            <div class="label-box">

                                <table class="layout-table">
                                    <tr>

                                        {{-- BARCODE --}}
                                        <td class="left-section">

                                            <img src="{{ $bc['img'] }}">

                                        </td>

                                        {{-- DETAILS --}}
                                        <td class="right-section">

                                            @if(!empty($settings->name))

                                                <div class="company">
                                                    {{ $settings->name }}
                                                </div>

                                            @endif


                                            <table class="details-table">

                                                <tr>
                                                    <td style="width:35%;">
                                                        ITEM
                                                    </td>

                                                    <td style="width:65%;">
                                                        :
                                                        {{ ucwords(strtolower($bc['name'] ?? 'N/A')) }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        CATEGORY
                                                    </td>

                                                    <td>
                                                        :
                                                        {{ ucwords(strtolower($product->category->name ?? 'N/A')) }}
                                                    </td>
                                                </tr>

                                            </table>


                                            {{-- MRP --}}
                                            <div class="mrp-section">

                                                <table class="mrp-table">

                                                    <tr>

                                                        <td style="width:35%;">
                                                            MRP
                                                        </td>

                                                        <td style="width:65%;">

                                                            :

                                                            <span style="font-size:13px;">

                                                                @if($product->price !== null)

                                                                    @if(($settings->currency_position ?? 'left') === 'left')

                                                                        {{ html_entity_decode(
                                                                            $settings->currency_symbol ?? '₹',
                                                                            ENT_QUOTES | ENT_HTML5,
                                                                            'UTF-8'
                                                                        ) }}{{ number_format($product->price, 2) }}

                                                                    @else

                                                                        {{ number_format($product->price, 2) }}{{ html_entity_decode(
                                                                            $settings->currency_symbol ?? '₹',
                                                                            ENT_QUOTES | ENT_HTML5,
                                                                            'UTF-8'
                                                                        ) }}

                                                                    @endif

                                                                @else

                                                                    N/A

                                                                @endif

                                                            </span>

                                                            <div class="tax-text">
                                                                (INCL. OF ALL TAXES)
                                                            </div>

                                                        </td>

                                                    </tr>

                                                </table>

                                            </div>

                                        </td>

                                    </tr>
                                </table>

                            </div>

                        </div>

                    </td>

                    {{-- END ROW AFTER 3 --}}
                    @if($index % 3 == 2)
                        </tr>
                    @endif

                @endforeach


                {{-- LAST INCOMPLETE ROW --}}
                @if(count($barcodes) % 3 != 0)

                    @for($i = count($barcodes) % 3; $i < 3; $i++)
                        <td></td>
                    @endfor

                    </tr>

                @endif

            </table>

        @else

            <div style="text-align:center; color:#888;">
                No barcode assigned to this product.
            </div>

        @endif

    </div>

</body>
</html>