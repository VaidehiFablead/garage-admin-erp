<script src="{{ env('ImagePath').'/admin/assets/js/jquery-3.6.0.min.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/js/feather.min.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/js/jquery.slimscroll.min.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/js/jquery.dataTables.min.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/js/dataTables.bootstrap4.min.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/js/bootstrap.bundle.min.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/plugins/apexchart/apexcharts.min.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/plugins/apexchart/chart-data.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/js/moment.min.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/plugins/flot/jquery.flot.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/plugins/flot/jquery.flot.fillbetween.js' }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flot.tooltip/0.9.0/jquery.flot.tooltip.min.js"></script>
<script src="{{ env('ImagePath').'/admin/assets/plugins/flot/jquery.flot.pie.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/plugins/flot/chart-data.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/js/script.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/js/bootstrap-datetimepicker.min.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/plugins/owlcarousel/owl.carousel.min.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/plugins/select2/js/select2.min.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/plugins/sweetalert/sweetalert2.all.min.js' }}"></script>
<script src="{{ env('ImagePath').'/admin/assets/plugins/sweetalert/sweetalerts.min.js' }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script>
    $(document).ready(function() {
        if (sessionStorage.getItem("showAppointments") === "1") {
            if ($('#todayAppointmentsModal').length) { // ✅ check exists
                $('#todayAppointmentsModal').modal('show');
            }
            sessionStorage.removeItem("showAppointments");
        }
    });
</script>









