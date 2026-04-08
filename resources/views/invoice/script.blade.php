<script src="{{ asset('js/jquery.min.js') }} "></script>
<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
    function closeScript() {
        setTimeout(function () {
            window.open(window.location, '_self').close();
        }, 1000);
    }

    function downloadInvoice() {
        var element = document.getElementById('boxes');
        var opt = {
            margin: [0.5, 0, 0.5, 0],
            filename: '{{Utility::customerInvoiceNumberFormat($invoice->invoice_id)}}',
            image: {type: 'jpeg', quality: 1},
            html2canvas: {scale: 4, dpi: 72, letterRendering: true},
            jsPDF: {unit: 'in', format: 'A4'}
        };
        html2pdf().set(opt).from(element).save();
    }

    function printInvoice() {
        // Hide action buttons during print
        var actionButtons = document.querySelector('.invoice-actions');
        if (actionButtons) {
            actionButtons.style.display = 'none';
        }
        
        // Trigger print
        window.print();
        
        // Show buttons again after print dialog closes
        setTimeout(function() {
            if (actionButtons) {
                actionButtons.style.display = 'block';
            }
        }, 1000);
    }

    // Automatic download disabled - user will click button instead
    // $(window).on('load', function () {
    //     var element = document.getElementById('boxes');
    //     var opt = {
    //         margin: [0.5, 0, 0.5, 0],
    //         filename: '{{Utility::customerInvoiceNumberFormat($invoice->invoice_id)}}',
    //         image: {type: 'jpeg', quality: 1},
    //         html2canvas: {scale: 4, dpi: 72, letterRendering: true},
    //         jsPDF: {unit: 'in', format: 'A4'}
    //     };
    //     html2pdf().set(opt).from(element).save().then(closeScript);
    // });

</script>
