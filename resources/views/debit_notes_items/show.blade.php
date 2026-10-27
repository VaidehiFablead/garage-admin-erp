@extends('layout.app')

@section('title', 'View Debit Note')

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

        .debit-note-title {
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
                <h4>View Debit Note</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('debit-notes-items.index') }}" class="btn btn-added">Back to List</a>
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
                                    <div class="debit-note-title">Debit Note</div>
                                    <br> <br>
                                    <strong id="displayName"></strong><br>
                                    <span style="font-weight: bold;">Phone: </span><span id="displayPhone"></span>
                                </td>
                                <td>
                                    <table style="text-align: right;">
                                        <strong>{{ $compenyinfo->name ?? '' }}</strong><br>
                                        {!! nl2br(e($compenyinfo->address ?? '')) !!}
                                        <br> <br>
                                        <tr>
                                            <td style="text-align: right; font-weight: bold;">Date:</td>
                                            <td id="debitNoteDate"></td>
                                        </tr>
                                        <tr>
                                            <td id="transactionLabel" style="text-align: right; font-weight: bold;">Invoice Number:</td>
                                            <td id="transactionNumber"></td>
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
            let currencySymbol = "{{ $currencySymbol ?? '₹' }}";
            let currencyPosition = "{{ $currencyPosition ?? 'left' }}";
            let selectedSubAdminId = localStorage.getItem('selectedSubAdminId');

            function formatCurrency(amount) {
                let formatted = parseFloat(amount || 0).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                return currencyPosition === 'left' ?
                    currencySymbol + formatted :
                    formatted + currencySymbol;
            }

            $.ajax({
                url: `/api/debit-note-items/${id}`,
                type: "GET",
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("authToken")
                },
                data: {
                    selectedSubAdminId: selectedSubAdminId
                },
                success: function(res) {
                    if (res.status !== 'success') return;

                    let d = res.data;

                    if (d.transaction_type === 'receipt') {
                        if (d.job_card?.customer) {
                            $('#displayName').text(d.job_card.customer.name ?? 'N/A');
                            $('#displayPhone').text(d.job_card.customer.phone ?? 'N/A');
                            $('#transactionLabel').text('Job Card Number:');
                            $('#transactionNumber').text(d.job_card.job_card_number ?? 'N/A');
                        } else {
                            $('#displayName').text(d.order?.user?.name ?? 'N/A');
                            $('#displayPhone').text(d.order?.user?.phone ?? 'N/A');
                            $('#transactionLabel').text('Order Number:');
                            $('#transactionNumber').text(d.order?.order_number ?? 'N/A');
                        }
                    } else {
                        $('#displayName').text(d.purchase_invoice?.vendor?.name ?? 'N/A');
                        $('#displayPhone').text(d.purchase_invoice?.vendor?.phone ?? 'N/A');
                        $('#transactionLabel').text('Invoice Number:');
                        $('#transactionNumber').text(d.invoice_number ?? 'N/A');
                    }

                    // ✅ Date
                    let dateObj = new Date(d.created_at);
                    let formattedDate = dateObj.toLocaleDateString('en-IN', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    $('#debitNoteDate').text(formattedDate);

                    // ✅ Debit note row
                    $('#reason').text(d.reason);
                    $('#typeName').text(d.credit_note_type?.type_name ?? 'N/A');
                    $('#totalAmt').text(formatCurrency(d.grand_total));
                    $('#settlementAmt').text(formatCurrency(d.settlement_amount));
                    $('#finalTotal').text(formatCurrency(d.total));

                    // ✅ Totals
                    $('#totalAmount').text(formatCurrency(d.total));
                },
                error: function(err) {
                    console.error(err);
                    alert('Unable to load debit note');
                }
            });
        });
    </script>
@endpush
