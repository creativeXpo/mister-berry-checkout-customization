// Initialize the Date & Time Pickers

jQuery(document).ready(function($) {
    $('#delivery_date').datepicker({
        dateFormat: 'yy-mm-dd'
    });

    $('#delivery_time').timepicker({
        timeFormat: 'hh:mm tt',
        interval: 30,
        minTime: '00:00am',
        maxTime: '11:30pm',
        startTime: '00:00',
        dynamic: false,
        dropdown: true,
        scrollbar: true
    });
});