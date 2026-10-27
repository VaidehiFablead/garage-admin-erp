@extends('layout.app')

@section('title', 'View Credit Note')

@section('content')
    <style>
        .invoice-box {
            max-width: 100%;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            background: #fff;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .credit-note-title {
            color: #ff9f43;
            font-size: 32px;
            font-weight: bold;
            text-transform: uppercase;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>View Credit Note</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('credit-notes-items.index') }}" class="btn btn-added">Back to List</a>
            </div>
        </div>

        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td class="title">
                                    <img src="{{ $compenyinfo->logo ? env('ImagePath') . 'storage/' . $compenyinfo->logo : env('ImagePath') . '/admin/assets/img/logo.png' }}"
                                        style="width:100%; max-width:150px;">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="6">
                        <table style="width: 100%;">
                            <tr style="display: flex; justify-content:space-between">
                                <td style="width: 50%;">
                                    <div class="credit-note-title">Credit Note</div>
                                    <br> <br>
                                    <strong id="customerName"></strong><br>
                                    <span style="font-weight: bold;">Address: </span><span id="customerAddress"></span><br>
                                    <span style="font-weight: bold;">Phone: </span><span id="customerPhone"></span>
                                </td>
                                <td>
                                    <table style="text-align: right;">
                                        <strong>{{ $compenyinfo->name ?? '' }}</strong><br>
                                        {!! nl2br(e($compenyinfo->address ?? '')) !!}
                                        <br> <br>
                                        <tr>
                                            <td style="text-align: right; font-weight: bold;">Date:</td>
                                            <td id="creditNoteDate"></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; font-weight: bold;" id="orderLabel">Order Number:</td>
                                            <td id="orderNumber"></td>
                                        </tr>
                                    </table>


                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="heading">
                    <td> Reason</td>
                    <td style="text-align: center;">Type</td>
                    <td style="text-align: center;">Total Amount</td>
                    <td style="text-align: center;">Settlement Amount</td>
                    <td style="text-align: center;">Final Total</td>
                </tr>

                <tr class="item">
                    <td id="reason"></td>
                    <td style="text-align: center;" id="typeName"></td>
                    <td style="text-align: center;" id="totalAmt"></td>
                    <td style="text-align: center;" id="settlementAmt"></td>
                    <td style="text-align: center;" id="finalTotal"></td>
                </tr>

                {{-- <tr class="total">
                    <td colspan="3"></td>
                    <td colspan="2">
                        Subtotal:
                        <span id="subtotalAmount"></span>
                    </td>
                </tr> --}}

                <tr class="total">
                    <td colspan="3"></td>
                    <td colspan="2" style="font-size: 20px;">
                        TOTAL:
                        <span id="totalAmount"></span>
                    </td>
                </tr>

            </table>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {

            let id = "{{ $id }}";
            let currencySymbol = "{{ $currencySymbol }}";
            let currencyPosition = "{{ $currencyPosition }}";
            let selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            function formatCurrency(amount) {
                let formatted = parseFloat(amount).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                return currencyPosition === 'left' ?
                    currencySymbol + formatted :
                    formatted + currencySymbol;
            }

            $.ajax({
                url: `/api/credit-note-items-api/${id}`,
                type: "GET",
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("authToken")
                },
                data: {
                    selectedSubAdminId: selectedSubAdminId
                },
                success: function(res) {

                    if (!res.status) return;

                    let d = res.data;

                    // ✅ Customer/Vendor info
                    if (d.type_id == 2 || d.type_id == 'payment') {
                        $('#customerName').text(d.purchase_invoice?.vendor?.name ?? 'N/A');
                        $('#customerPhone').text(d.purchase_invoice?.vendor?.phone ?? 'N/A');
                        $('#orderLabel').text('Invoice Number:');
                        $('#orderNumber').text(d.purchase_invoice?.invoice_number ?? 'N/A');

                        if (d.purchase_invoice?.vendor?.userDetail) {
                            $('#customerAddress').html(
                                d.purchase_invoice.vendor.userDetail.address.replace(/\n/g, '<br>')
                            );
                        } else {
                            $('#customerAddress').text('N/A');
                        }
                    } else {
                        if (d.order) {
                            $('#customerName').text(d.order?.user?.name ?? 'N/A');
                            $('#customerPhone').text(d.order?.user?.phone ?? 'N/A');
                            $('#orderLabel').text('Order Number:');
                            $('#orderNumber').text(d.order?.order_number ?? 'N/A');

                            if (d.order?.user?.userDetail) {
                                $('#customerAddress').html(
                                    d.order.user.userDetail.address.replace(/\n/g, '<br>')
                                );
                            } else {
                                $('#customerAddress').text('N/A');
                            }
                        } else if (d.jobcard) {
                            $('#customerName').text(d.jobcard?.customer?.name ?? d.jobcard?.customer_name ?? 'N/A');
                            $('#customerPhone').text(d.jobcard?.customer?.phone ?? d.jobcard?.mobile ?? 'N/A');
                            $('#orderLabel').text('Job Card Number:');
                            $('#orderNumber').text(d.jobcard?.job_card_number ?? 'N/A');

                            if (d.jobcard?.customer?.userDetail?.address) {
                                $('#customerAddress').html(
                                    d.jobcard.customer.userDetail.address.replace(/\n/g, '<br>')
                                );
                            } else if (d.jobcard?.address) {
                                $('#customerAddress').text(d.jobcard.address);
                            } else {
                                $('#customerAddress').text('N/A');
                            }
                        } else {
                            $('#customerName').text('N/A');
                            $('#customerPhone').text('N/A');
                            $('#orderLabel').text('Job Card / Order Number:');
                            $('#orderNumber').text('N/A');
                            $('#customerAddress').text('N/A');
                        }
                    }

                    // ✅ Date
                    let dateObj = new Date(d.created_at);
                    let formattedDate = dateObj.toLocaleDateString('en-IN', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    $('#creditNoteDate').text(formattedDate);

                    // ✅ Credit note row
                    $('#reason').text(d.reason);
                    $('#typeName').text(d.credit_note?.type_name ?? 'N/A');
                    $('#totalAmt').text(formatCurrency(d.total_amt));
                    $('#settlementAmt').text(formatCurrency(d.settlement_amount));
                    $('#finalTotal').text(formatCurrency(d.total));

                    // ✅ Totals
                    $('#subtotalAmount').text(formatCurrency(d.total));
                    $('#totalAmount').text(formatCurrency(d.total));
                },
                error: function(err) {
                    console.error(err);
                    alert('Unable to load credit note');
                }
            });
        });
    </script>
@endpush
