'use strict';
/**
 * admin calendar
 */
$(document).ready(function () {
    var calendar = $('#calendar').fullCalendar({
        theme: true,
        themeSystem: 'bootstrap4',
        editable: false,
        weekNumbers: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        events: 'showCalendar',
        timeFormat: 'H(:mm)',
    });
});