document.addEventListener('DOMContentLoaded', function() {

  var events = []

  wpZoomWebinars.forEach( function( item, index ) {
    var event = {
      title: item.topic,
      start: item.start_time
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
    defaultDate: '2020-03-07',
    navLinks: true, // can click day/week names to navigate views
    businessHours: true, // display business hours
    editable: true,
    height: 'auto',
    contentHeight: 'auto',
    events: events,
    eventClick: function( info ) {

      console.log(info)
      console.log( calendar )

      //calendar.gotoDate(info.event.start);
      calendar.changeView('dayGrid', info.event.start);



    }
  });

  calendar.render();

});
