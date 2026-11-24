<table>
    @php
        $leftRows = [];
        foreach($liabilityGroups as $groupName => $accounts) {
            $groupTotal = array_sum($accounts);
            $leftRows[] = ['name' => $groupName, 'amount' => $groupTotal, 'is_group' => true];
            foreach($accounts as $accName => $accAmt) {
                $leftRows[] = ['name' => $accName, 'amount' => $accAmt, 'is_group' => false];
            }
        }

        $rightRows = [];
        foreach($assetGroups as $groupName => $accounts) {
            $groupTotal = array_sum($accounts);
            $rightRows[] = ['name' => $groupName, 'amount' => $groupTotal, 'is_group' => true];
            foreach($accounts as $accName => $accAmt) {
                $rightRows[] = ['name' => $accName, 'amount' => $accAmt, 'is_group' => false];
            }
        }

        $maxRows = max(count($leftRows), count($rightRows));
    @endphp

    <thead>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold; font-size: 16px;">{{ $companyName ?? 'Company Name' }}</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center;">{{ $companyAddress ?? 'Address' }}</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center;">Contact: {{ $companyPhone ?? '' }}</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold; font-size: 14px;">Balance Sheet</th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center;">From {{ \Carbon\Carbon::parse($startDate)->format('d-M-y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d-M-y') }}</th>
        </tr>
        <tr>
            <th colspan="4"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border-bottom: 1px solid #000; border-top: 1px solid #000; width: 40px;">Liabilities</th>
            <th style="font-weight: bold; border-bottom: 1px solid #000; border-top: 1px solid #000; text-align: right; width: 20px;">as at {{ date('d-M-y') }}</th>
            <th style="font-weight: bold; border-bottom: 1px solid #000; border-top: 1px solid #000; width: 40px;">Assets</th>
            <th style="font-weight: bold; border-bottom: 1px solid #000; border-top: 1px solid #000; text-align: right; width: 20px;">as at {{ date('d-M-y') }}</th>
        </tr>
    </thead>
    <tbody>
        @for($i = 0; $i < $maxRows; $i++)
            <tr>
                @if(isset($leftRows[$i]))
                    <td style="{{ $leftRows[$i]['is_group'] ? 'font-weight: bold;' : '' }}">
                        {{ ($leftRows[$i]['is_group'] ? '' : '    ') . $leftRows[$i]['name'] }}
                    </td>
                    <td style="{{ $leftRows[$i]['is_group'] ? 'font-weight: bold;' : '' }} text-align: right;" data-format="#,##0.00">
                        {{ $leftRows[$i]['amount'] }}
                    </td>
                @else
                    <td></td><td></td>
                @endif

                @if(isset($rightRows[$i]))
                    <td style="{{ $rightRows[$i]['is_group'] ? 'font-weight: bold;' : '' }}">
                        {{ ($rightRows[$i]['is_group'] ? '' : '    ') . $rightRows[$i]['name'] }}
                    </td>
                    <td style="{{ $rightRows[$i]['is_group'] ? 'font-weight: bold;' : '' }} text-align: right;" data-format="#,##0.00">
                        {{ $rightRows[$i]['amount'] }}
                    </td>
                @else
                    <td></td><td></td>
                @endif
            </tr>
        @endfor
        <tr>
            <td style="font-weight: bold; border-top: 1px solid #000; border-bottom: 2px solid #000;">Total</td>
            <td style="font-weight: bold; border-top: 1px solid #000; border-bottom: 2px solid #000; text-align: right;" data-format="#,##0.00">{{ $totals['liabilities_equity'] ?? 0 }}</td>
            <td style="font-weight: bold; border-top: 1px solid #000; border-bottom: 2px solid #000;">Total</td>
            <td style="font-weight: bold; border-top: 1px solid #000; border-bottom: 2px solid #000; text-align: right;" data-format="#,##0.00">{{ $totals['assets'] ?? 0 }}</td>
        </tr>
    </tbody>
</table>
