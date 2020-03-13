document.addEventListener('DOMContentLoaded', function() {

  var events = [

  ]

  var calendarEl = document.getElementById('zoom-calendar');

  console.log( calendarEl )

  var calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: [ 'dayGrid' ],
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,dayGridDay'
    },
    defaultDate: '2020-03-07',
    navLinks: true, // can click day/week names to navigate views
    businessHours: true, // display business hours
    editable: true,
    height: 'auto',
    contentHeight: 'auto',
    events: events
  });

  console.log( calendar )

  calendar.render();

});
