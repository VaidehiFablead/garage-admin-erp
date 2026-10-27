@extends('layout.app')

@section('title', 'Sales Invoice')

@section('content')
    <style>
        .logo_img {
            max-width: 150px;
            height: auto;
        }

        .invoice-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-box table td {
            white-space: nowrap;
        }
        .info-text {
            white-space: normal;    /* allow wrapping */
            word-wrap: break-word;  /* break long words */
            overflow-wrap: anywhere; /* modern way */
        }

        /* Adjust columns for mobile, keep same layout */
        @media (max-width: 767px) {
            .invoice-box {
                overflow-x: auto;
            }

            .card-sales-split {
                flex-direction: row;
            }

            .row {
                display: flex;
                flex-wrap: wrap;
            }

            .col-lg-6.table_view {
                flex: 0 0 50%;
                max-width: 50%;
            }

            .col-lg-6.total-order {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .invoice-box,
            .invoice-box table,
            .invoice-box td,
            .invoice-box h4,
            .invoice-box h5 {
                font-size: 12px;
                line-height: 1.2;
            }

            .logo_img {
                max-width: 130px;
                height: auto;
            }

            font {
                line-height: 22px !important;
            }
        }
    </style>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Sale Invoice</h4>

            </div>

            <div class="page-btn d-flex">
                <ul style="padding: .5rem;">
                    <li>
                        <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="downloadPDF({{ $view_id }})" title="Download Invoice">
                            Download PDF
                        </a>
                    </li>
                </ul>
                <ul style="padding: .5rem;">
                    <li>
                        <a href="{{ route('sales.list') }}" class="btn btn-added">
                            Back
                        </a>
                    </li>
                </ul>
            </div>
        </div>


        <div class="card">
            <div class="card-body">
                <input type="hidden" name="selse_id" id="selse_id" value="{{ $view_id }}">

                <div class="download_pdf">
                    <div class="invoice-box table-height"
                        style="max-width: 1600px;width:100%;margin:15px auto;padding: 0;font-size: 14px;line-height: 24px;color: #555;">
                        <table cellpadding="0" cellspacing="0" style="width: 100%;line-height: inherit;text-align: left;">
                            <tbody id="product-details">

                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="total-order w-100 max-widthauto m-auto mb-4">
                                </div>
                            </div>
                            @php
                                // Default subtotal
                                $subtotal = $totalAmount;

                                // Determine GST option (default to 'without' if not set)
                                $gstOption = $sales->gst_option ?? 'without';

                                // Discount percentage
                                $discount = $sales->discount ?? 0;

                                // Convert percentage into price
                                // $discount = ($subtotal * $discountPercent) / 100;
                                $subtotal = $subtotal - $discount;

                                // Prepare tax details
                                $taxDetails = [];
                                $totalTaxAmount = 0;

                                if ($gstOption === 'with') {
                                    foreach ($taxes as $tax) {
                                        $taxAmount = ($subtotal * $tax->tax_rate) / 100;
                                        $taxDetails[] = [
                                            'name' => $tax->tax_name,
                                            'rate' => $tax->tax_rate,
                                            'amount' => $taxAmount,
                                        ];
                                        $totalTaxAmount += $taxAmount;
                                    }
                                }

                                // Final total
                                $finalTotal = $subtotal + $totalTaxAmount;

                                // Helper function for formatting currency
                                if (!function_exists('formatCurrency')) {
                                    function formatCurrency($amount, $symbol = '₹', $position = 'left')
                                    {
                                        return $position === 'right'
                                            ? number_format($amount, 2) . $symbol
                                            : $symbol . number_format($amount, 2);
                                    }
                                }
                            @endphp

                            <div class="col-lg-6">
                                <div class="total-order w-100 max-widthauto m-auto mb-4">
                                    <ul>
                                        <li>
                                            <h4>Subtotal</h4>
                                            <h5>{{ formatCurrency($subtotal, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}
                                            </h5>
                                        </li>
                                        <li>
                                            <h4>Discount</h4>
                                            <h5>{{ formatCurrency($discount, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}
                                            </h5>
                                        </li>
                                        @if($gstOption === 'with')
                                            @foreach($taxDetails as $tax)
                                                <li>
                                                    <h4>{{ $tax['name'] }} Tax</h4>
                                                    <h5>{{ $tax['rate'] }}%
                                                        ({{ formatCurrency($tax['amount'], $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }})
                                                    </h5>
                                                </li>
                                            @endforeach
                                        @endif

                                        <li class="total">
                                            <h4>Total</h4>
                                            <h5>{{ formatCurrency($finalTotal, $setting->currency_symbol ?? '₹', $setting->currency_position ?? 'left') }}
                                            </h5>
                                        </li>
                                    </ul>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div style="text-align: center; margin-top: 100px;">
                        <h2 style="color: #555555; font-size: 24px; margin-bottom: 10px;">Thank You for Your Purchase!</h2>
                        <p style="font-size: 16px; color: #555;">We appreciate your business and hope to serve you again
                            soon.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
    <script>
        const userAddress = @json($userAddress);
        function downloadPDF(id) {
            let url = "{{ route('sales.invoice.pdf-view', ':id') }}";
            url = url.replace(':id', id);
            window.location.href = url; // open in same tab
            // or use window.open(url, '_blank'); to open in new tab
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        // function downloadPDF() {
        //     // Get the element you want to convert to PDF
        //     const element = document.querySelector('.download_pdf');

        //     // Options for the PDF
        //     const opt = {
        //         margin: 10,
        //         filename: 'invoice_SL0101.pdf',
        //         image: {
        //             type: 'jpeg',
        //             quality: 0.98
        //         },
        //         html2canvas: {
        //             scale: 2
        //         },
        //         jsPDF: {
        //             unit: 'mm',
        //             format: 'a4',
        //             orientation: 'portrait'
        //         }
        //     };

        //     // Generate the PDF
        //     html2pdf().set(opt).from(element).save();
        // }

        function formatCurrency(amount, symbol = '₹', position = 'left') {
            amount = parseFloat(amount).toFixed(2);
            return position === 'left' ? `${symbol}${amount}` : `${amount}${symbol}`;
        }
        $(document).ready(function () {
            var authToken = localStorage.getItem("authToken");
            let salse_id = $("#selse_id").val();

            if (salse_id) {
                fetchsalseDetails(salse_id);
            }

            function fetchsalseDetails(salse_id) {
                $.ajax({
                    url: `/api/getsalseById/${salse_id}`,
                    type: "GET",
                    dataType: "json",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                    },
                    success: function (response) {
                        const dateStr = response.order_items[0].date;
                        const dateObj = new Date(dateStr);

                        // Options for the formatter
                        const options = {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        };

                        // Using Intl.DateTimeFormat
                        const formattedDate = new Intl.DateTimeFormat('en-GB', options).format(dateObj);


                        if (response.error) {
                            console.error(response.error);
                            return;
                        }
                        // console.log(response.company_info);

                        const sale = response.sales;
                        const company_info = response.company_info;
console.log(sale);

                        // Update customer and invoice info
                        $(".customer-name").text(sale.user_name || "walk-in-customer");
                        $(".customer-phone").text(sale.user_phone || "N/A");
                        $(".invoice-id").text(sale.order_number);
                        $(".payment-status").text(sale.payment_status.charAt(0).toUpperCase() + sale.payment_status.slice(1));
                        $(".order-status").text("Completed"); // or dynamically from status if available

                        const items = sale.order_items;
                        let productRows = `
                                                    <tr class="top">
                                                        <td colspan="6" style="padding: 10px; vertical-align: top;">

                                                            <div class="row position-relative align-items-center m-4">
                                                                <!-- Logo on the left -->
                                                                <div class="col-auto">
                                                                    <img 
                                                                    src="{{ $setting->logo ? asset('storage/' . $setting->logo) : asset('admin/assets/img/logso.png') }}" 
                                                                    alt="logo" 
                                                                    class="logo_img">

                                                                </div>

                                                                <!-- Title centered -->
                                                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                                                    <h1 class="mb-2">Invoice</h1>
                                                                    <h4>#${sale.order_number}</h4>
                                                                </div>
                                                            </div>
                                                            <hr> 
                                                            <div style="text-align: center;">
                                                                <table class="product-list">
                                                                    <tbody>
                                                                    <tr>
                                                                        <!-- Customer Info -->
                                                                        <td>
                                                                        <span class="section-title">Customer Info</span>
                                                                        <span class="info-text"><span class="label">Name:</span> ${sale.user_name || "walk-in-customer"}</span>
                                                                        ${sale.user_phone ? `<span class="info-text"><span class="label">Phone:</span> ${sale.user_phone}</span>` : ''}
                                                                        ${sale.car_model_name ? `<span class="info-text"><span class="label">Model:</span> ${sale.car_model_name}</span>` : ''}
                                                                        ${sale.vehicle_no ? `<span class="info-text"><span class="label">Vehicle No.:</span> ${sale.vehicle_no}</span>` : ''}
                                                                        </td>

                                                                        <!-- Company Info -->
                                                                        <td>
                                                                        <span class="section-title">Company Info</span>
                                                                        ${company_info.name ? `<span class="info-text"><span class="label">Name:</span> ${company_info.name}</span>` : ''}
                                                                        ${company_info.email ? `<span class="info-text"><span class="label">Email:</span> ${company_info.email}</span>` : ''}
                                                                        ${company_info.phone ? `<span class="info-text"><span class="label">Phone:</span> ${company_info.phone}</span>` : ''}
                                                                        ${company_info.address ? `<span class="info-text"><span class="label">Address:</span> ${company_info.address}</span>` : ''}
                                                                        ${company_info.gst_num ? `<span class="info-text"><span class="label">GST:</span> ${company_info.gst_num}</span>` : ''}
                                                                        </td>

                                                                        <!-- Order Info + Values combined -->
                                                                        <td colspan="2">
                                                                        <span class="section-title">Order Info</span>
                                                                        <span class="info-text"><span class="label">Order Number:</span> #${sale.order_number}</span>
                                                                        <span class="info-text"><span class="label">Order Date:</span> ${formattedDate}</span>
                                                                        <span class="info-text"><span class="label">Payment Status:</span> ${sale.payment_status.charAt(0).toUpperCase() + sale.payment_status.slice(1)}</span>
                                                                        <span class="info-text"><span class="label">Payment Method:</span> ${sale.payment_method}</span>
                                                                        <span class="info-text"><span class="label">Warranty Date:</span> ${sale.next_warranty_date}</span>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                                </div>


                                                        </td>
                                                    </tr>
                                                    <tr class="heading" style="background: #F3F2F7;">
                                                            <td style="border: 1px solid #ccc; padding: 10px; font-weight: 600; color: #5E5873; font-size: 14px; width: 40%; text-align: left;">
                                                                Product Name
                                                            </td>
                                                            <td style="border: 1px solid #ccc; padding: 10px; font-weight: 600; color: #5E5873; font-size: 14px; width: 15%; text-align: left;">
                                                                QTY
                                                            </td>
                                                            <td style="border: 1px solid #ccc; padding: 10px; font-weight: 600; color: #5E5873; font-size: 14px; width: 20%; text-align: left;">
                                                                Price
                                                            </td>
                                                            <td style="border: 1px solid #ccc; padding: 10px; font-weight: 600; color: #5E5873; font-size: 14px; width: 25%; text-align: left;">
                                                                Subtotal
                                                            </td>
                                                        </tr>

                        `;


                        items.forEach((item) => {
                            const product = item.product;
                            const taxAmount = 0; // You can calculate tax if required from tax_ids
                            const discount = sale.discount || 0;

                            let imagePath = '';
                            let imageBasePath = '{{ env('ImagePath') }}'; // inject from .env

                            try {
                                const images = JSON.parse(product.images);
                                console.log(images);

                                if (images && images.length > 0 && images[0]) {
                                    imagePath = `${imageBasePath}/storage/${images[0]}`;
                                } else {
                                    imagePath = `${imageBasePath}/admin/assets/img/product/noimage.png`;
                                }
                            } catch (e) {
                                imagePath = `${imageBasePath}/admin/assets/img/product/noimage.png`;
                            }
                            const unitPrice = formatCurrency(item.price, company_info.currency_symbol, company_info.currency_position);
                            const totalAmount = formatCurrency(item.total_amount, company_info.currency_symbol, company_info.currency_position);
                            // let orderSubtotal = 0;
                            productRows += `
                                <tr class="details" style="border-bottom:1px solid #E9ECEF;">
                                    <td style="padding: 10px; vertical-align: top; width:40%;">
                                        <a href="/product-view/${product.id}" class="d-flex align-items-center text-decoration-none text-dark">
                                            <img src="${imagePath}" alt="img" class="me-2" style="width:40px; height:40px;">
                                            ${product.name}
                                        </a>
                                    </td>
                                    <td style="padding: 10px; vertical-align: top; width:15%;">${item.quantity}</td>
                                    <td style="padding: 10px; vertical-align: top; width:20%;">${unitPrice}</td>
                                    <td style="padding: 10px; vertical-align: top; width:25%;">${totalAmount}</td>
                                </tr>`;

                        });
                        $("#product-details").html(productRows);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching sales details:", error);
                    }
                });
            }
        });
    </script>
    <style>
        .product-list {
            width: 80%;
            border-collapse: collapse;
            margin: 0 auto 20px auto;
            /* center table horizontally */
            table-layout: fixed;
            /* equal width columns */
        }

        .product-list td {
            border: 1px solid #ccc;
            padding: 10px;
            vertical-align: top;
            font-size: 14px;
            line-height: 1.6;
            width: 25%;
            text-align: left;
            /* all text left aligned */
        }

        .section-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        .info-text {
            display: block;
            margin-bottom: 3px;
        }

        .info-text span.label {
            font-weight: 600;
            margin-right: 5px;
        }
    </style>


@endpush