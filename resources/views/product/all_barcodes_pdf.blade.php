<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Product Barcodes</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4 portrait;
            margin: 8mm 7mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            background: #fff;
            color: #000;
        }

        /* ── Company Header Section ── */
        .page-header-block {
            width: 100%;
            margin-bottom: 2mm;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
        }

        hr.divider {
            height: 1px;
            background: #ccc;
            border: none;
            margin: 3px 0 5px 0;
        }

        .section-title {
            text-align: center;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #111;
        }

        /* ── 3-Column Barcode Grid (with clear separation between cards) ── */
        .barcode-grid-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 1.5mm;
        }

        .barcode-grid-table td.grid-col {
            width: 33.333%;
            vertical-align: top;
            padding: 2mm 3.5mm;
            box-sizing: border-box;
        }

        .barcode-grid-table tr {
            page-break-inside: avoid !important;
        }

        .page-break {
            page-break-after: always;
        }

        /* ── Individual Barcode Card ── */
        .label-card {
            width: 100%;
            height: 28.5mm;
            border: 1px solid #222;
            border-radius: 5px;
            background: #fff;
            padding: 1.5mm 1.5mm;
            box-sizing: border-box;
            overflow: hidden;
            page-break-inside: avoid !important;
        }

        .card-company-header {
            text-align: center;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #222;
            padding-bottom: 1px;
            margin-bottom: 1.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.1;
            letter-spacing: 0.2px;
        }

        .card-body-table {
            width: 100%;
            border-collapse: collapse;
            height: 20.5mm;
        }

        .card-body-table td {
            padding: 0;
        }

        .card-code-col {
            width: 35%;
            vertical-align: middle;
            text-align: center;
        }

        .card-code-img {
            max-width: 18mm;
            max-height: 18mm;
            display: block;
            margin: 0 auto;
        }

        .card-code-img.barcode-1d {
            transform: rotate(-90deg);
            max-height: 16mm;
            max-width: 13.5mm;
        }

        .card-info-col {
            width: 65%;
            vertical-align: top;
            padding-left: 2px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.5pt;
            font-weight: bold;
            line-height: 1.2;
        }

        .details-table td {
            padding: 0.5px 0;
            vertical-align: top;
        }

        .details-table .key {
            width: 38%;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .details-table .val {
            width: 62%;
            text-transform: uppercase;
            word-break: break-word;
        }

        .mrp-val {
            font-size: 8.5pt;
            font-weight: bold;
        }

        .taxes-subtext {
            font-size: 4.8pt;
            font-weight: bold;
            margin-top: 0.5px;
            color: #333;
        }
    </style>
</head>
<body>

    @php
        $currSym = html_entity_decode($settings->currency_symbol ?? '₹', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $isQr = ($barcodeType ?? '') === 'qrcode' || ($barcodeType ?? '') === 'QRCODE';
        $firstPageItems = $products->slice(0, 12);
        $remainingItems = $products->slice(12);
    @endphp

    @if($products->count() > 0)
        {{-- PAGE 1 (Header + 4 rows × 3 columns = 12 cards) --}}
        <div class="page-header-block">
            <table class="header-table">
                <tr>
                    <td style="width: 130px;">
                        @if(!empty($settings->logo) && file_exists(storage_path('app/public/' . $settings->logo)))
                            @php
                                $logoPath = storage_path('app/public/' . $settings->logo);
                                $logoData = base64_encode(file_get_contents($logoPath));
                                $logoMime = mime_content_type($logoPath);
                            @endphp
                            <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Logo"
                                 style="max-height: 40px; max-width: 120px; object-fit: contain;">
                        @endif
                    </td>
                    <td style="text-align: right; padding-left: 10px;">
                        <div style="font-size: 13px; font-weight: bold; text-transform: uppercase; color: #000;">
                            {{ $settings->name ?? 'COMPANY NAME' }}
                        </div>
                        <div style="font-size: 8pt; color: #444; margin-top: 2px; text-transform: uppercase; line-height: 1.3;">
                            {{ str_replace(["\r\n", "\r", "\n"], ', ', $settings->address ?? '') }}<br>
                            @if(!empty($settings->phone))PHONE: {{ $settings->phone }}@endif
                            @if(!empty($settings->email)) | EMAIL: {{ strtoupper($settings->email) }}@endif
                        </div>
                    </td>
                </tr>
            </table>

            <hr class="divider">
            <div class="section-title">All Product Barcodes</div>
        </div>

        <table class="barcode-grid-table">
            @foreach($firstPageItems->chunk(3) as $row)
                <tr>
                    @foreach($row as $bc)
                        <td class="grid-col">
                            <div class="label-card">
                                <div class="card-company-header">
                                    {{ $settings->name ?? 'COMPANY NAME' }}
                                </div>
                                <table class="card-body-table">
                                    <tr>
                                        <td class="card-code-col">
                                            @php
                                                $imgSrc = $barcodeImages[$bc->id] ?? ($bc->barcode_image ?? ($bc->img ?? null));
                                            @endphp
                                            @if(!empty($imgSrc))
                                                <img src="{{ $imgSrc }}" class="card-code-img {{ $isQr ? '' : 'barcode-1d' }}">
                                            @endif
                                        </td>
                                        <td class="card-info-col">
                                            <table class="details-table">
                                                <tr>
                                                    <td class="key">ITEM</td>
                                                    <td class="val">: {{ strtoupper($bc->name ?? $bc->display_name ?? 'N/A') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="key">CATEGORY</td>
                                                    <td class="val">: {{ strtoupper($bc->category ?? $bc->category_name ?? ($bc->category->name ?? 'N/A')) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="key" style="padding-top: 1px;">MRP</td>
                                                    <td class="val" style="padding-top: 1px;">
                                                        : <span class="mrp-val">
                                                            @if(isset($bc->mrp) && $bc->mrp !== null)
                                                                {{ $currSym }}{{ number_format((float)$bc->mrp, 2) }}
                                                            @elseif(isset($bc->price) && $bc->price !== null)
                                                                {{ $currSym }}{{ number_format((float)$bc->price, 2) }}
                                                            @else
                                                                {{ $currSym }}0.00
                                                            @endif
                                                        </span>
                                                        <div class="taxes-subtext">(INCL. OF ALL TAXES)</div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    @endforeach
                    @for($i = count($row); $i < 3; $i++)
                        <td class="grid-col" style="border: none;"></td>
                    @endfor
                </tr>
            @endforeach
        </table>

        {{-- SUBSEQUENT PAGES (5 rows × 3 columns = 15 cards per page) --}}
        @if($remainingItems->count() > 0)
            @foreach($remainingItems->chunk(15) as $chunk)
                <div class="page-break"></div>
                <table class="barcode-grid-table">
                    @foreach($chunk->chunk(3) as $row)
                        <tr>
                            @foreach($row as $bc)
                                <td class="grid-col">
                                    <div class="label-card">
                                        <div class="card-company-header">
                                            {{ $settings->name ?? 'COMPANY NAME' }}
                                        </div>
                                        <table class="card-body-table">
                                            <tr>
                                                <td class="card-code-col">
                                                    @php
                                                        $imgSrc = $barcodeImages[$bc->id] ?? ($bc->barcode_image ?? ($bc->img ?? null));
                                                    @endphp
                                                    @if(!empty($imgSrc))
                                                        <img src="{{ $imgSrc }}" class="card-code-img {{ $isQr ? '' : 'barcode-1d' }}">
                                                    @endif
                                                </td>
                                                <td class="card-info-col">
                                                    <table class="details-table">
                                                        <tr>
                                                            <td class="key">ITEM</td>
                                                            <td class="val">: {{ strtoupper($bc->name ?? $bc->display_name ?? 'N/A') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="key">CATEGORY</td>
                                                            <td class="val">: {{ strtoupper($bc->category ?? $bc->category_name ?? ($bc->category->name ?? 'N/A')) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="key" style="padding-top: 1px;">MRP</td>
                                                            <td class="val" style="padding-top: 1px;">
                                                                : <span class="mrp-val">
                                                                    @if(isset($bc->mrp) && $bc->mrp !== null)
                                                                        {{ $currSym }}{{ number_format((float)$bc->mrp, 2) }}
                                                                    @elseif(isset($bc->price) && $bc->price !== null)
                                                                        {{ $currSym }}{{ number_format((float)$bc->price, 2) }}
                                                                    @else
                                                                        {{ $currSym }}0.00
                                                                    @endif
                                                                </span>
                                                                <div class="taxes-subtext">(INCL. OF ALL TAXES)</div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            @endforeach
                            @for($i = count($row); $i < 3; $i++)
                                <td class="grid-col" style="border: none;"></td>
                            @endfor
                        </tr>
                    @endforeach
                </table>
            @endforeach
        @endif
    @else
        <div style="text-align: center; color: #888; margin-top: 20px;">No products found.</div>
    @endif

</body>
</html>
