$(document).ready(
    function () {
        $(function() {
            $("#campofecha").datepicker({
                changeMonth: true,
                changeYear: true,
                minDate: '-100Y',
                maxDate: '+10Y'
                //dateFormat: 'yy-mm-dd'

            });
        });
        $(function() {
            $("#campofecha_").datepicker({
                changeMonth: true,
                changeYear: true,
                minDate: '-100Y',
                maxDate: '+10Y',
                dateFormat: 'dd-mm-yy'

            });
        });
        $(function() {
            $("#fecha_alta_laboral").datepicker({
                changeMonth: true,
                changeYear: true,
                minDate: '-100Y',
                maxDate: '+10Y',
                dateFormat: 'dd-mm-yy'

            });
        });
        
        $(function() {
            $(".datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                minDate: '-100Y',
                maxDate: '+10Y'
                //dateFormat: 'yy-mm-dd'

            });
        });
    }
    );
