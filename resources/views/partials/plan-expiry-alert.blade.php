@php
    $user = auth()->user();
    $showModal = false;
    $planName = '';
    $formattedEndDate = '';
    $daysLeft = 0;

    if ($user && in_array($user->role, ['admin', 'sub-admin']) && request()->routeIs('auth.dashboard')) {
        $plan = $user->getActivePlan();
        if ($plan && !empty($plan->end_date)) {
            $today = \Carbon\Carbon::today('Asia/Kolkata');
            $endDate = \Carbon\Carbon::parse($plan->end_date, 'Asia/Kolkata')->startOfDay();
            $warningStart = $endDate->copy()->subDays(30);

            if ($today->gte($warningStart) && $today->lte($endDate)) {
                $showModal = true;
                $planName = $plan->name ?? 'Active Plan';
                $daysLeft = (int) $today->diffInDays($endDate, false);
                $formattedEndDate = $endDate->format('d F Y');
            }
        }
    }
@endphp

@if ($showModal)
    <script>
        window.willShowCustomPlanExpiry = true;
    </script>
    <div class="modal fade" id="customPlanExpiryModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
            <div class="modal-content border-0" style="border-radius: 20px; padding: 28px 24px 24px; position: relative; box-shadow: 0 20px 50px rgba(0,0,0,0.25);">
                
                {{-- Close Button (X) --}}
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 20px; background: none; border: none; font-size: 22px; font-weight: 700; color: #ff5252; cursor: pointer; line-height: 1;">
                    &times;
                </button>

                <div class="modal-body p-0 text-center">
                    {{-- Warning Red Circle Icon (!) --}}
                    <div style="width: 58px; height: 58px; border-radius: 50%; background: #ff5252; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 30px; font-weight: 800; margin: 0 auto 16px; box-shadow: 0 8px 20px rgba(255, 82, 82, 0.35);">
                        !
                    </div>

                    {{-- Title --}}
                    <h3 style="font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 12px; font-family: 'Outfit', sans-serif;">
                        Plan Expiration Warning
                    </h3>

                    {{-- Subtitle --}}
                    <p style="font-size: 14px; color: #64748b; margin-bottom: 22px; line-height: 1.5;">
                        Your subscription plan will expire in <strong style="color: #334155;">{{ $daysLeft }} days</strong>. Please renew it to avoid interruption in service.
                    </p>

                    {{-- Detail Card (Soft background with green left border) --}}
                    <div style="background: #f8fafc; border-left: 4px solid #22c55e; border-radius: 12px; padding: 14px 18px; margin-bottom: 22px; text-align: left;">
                        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 14px;">
                            <span style="color: #475569; font-weight: 500;">Plan:</span>
                            <strong style="color: #f43f5e; font-weight: 700;">{{ $planName }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 14px;">
                            <span style="color: #475569; font-weight: 500;">Expires On:</span>
                            <strong style="color: #f43f5e; font-weight: 700;">{{ $formattedEndDate }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center" style="font-size: 14px;">
                            <span style="color: #475569; font-weight: 500;">Days Remaining:</span>
                            <strong style="color: #f43f5e; font-weight: 700;">{{ $daysLeft }} days</strong>
                        </div>
                    </div>

                    {{-- Contact Info --}}
                    <div style="text-align: left;">
                        <h5 style="color: #f97316; font-size: 15px; font-weight: 700; margin-bottom: 10px;">
                            Contact us to Renew:
                        </h5>
                        <p style="font-size: 13.5px; color: #475569; margin-bottom: 6px; font-weight: 500;">
                            <i class="fas fa-phone me-2" style="color: #64748b; width: 16px;"></i> +91 9824734531
                        </p>
                        <p style="font-size: 13.5px; color: #475569; margin-bottom: 0; font-weight: 500;">
                            <i class="fas fa-envelope me-2" style="color: #64748b; width: 16px;"></i> info@fableadtechnolabs.com
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById('customPlanExpiryModal');
            if (modalEl && typeof bootstrap !== 'undefined') {
                var myModal = new bootstrap.Modal(modalEl, {
                    backdrop: 'static',
                    keyboard: true
                });
                myModal.show();
            }
        });
    </script>
@endif
