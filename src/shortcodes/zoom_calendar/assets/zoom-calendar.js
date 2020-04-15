document.addEventListener('DOMContentLoaded', function() {

  var events = []

  wpZoomWebinars.forEach( function( item, index ) {
    var event = {
      title: item.title,
      start: item.start,
      register: item.register
    }
    events.push( event )
  })


  var calendarEl = document.getElementById('zoom-calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: [ 'dayGrid' ],
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,dayGridDay'
    },
    navLinks: true, // can click day/week names to navigate views
    businessHours: true, // display business hours
    editable: true,
    height: 'auto',
    contentHeight: 'auto',
    events: events,
    eventClick: function( info ) {
      info.jsEvent.preventDefault();
      window.open(info.event.extendedProps.register);
    }
  });

  calendar.render();

});
