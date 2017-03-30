

var arrRes,
    systemReservations= [];


var todayDate = moment().startOf('day');
var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
var TODAY = todayDate.format('YYYY-MM-DD');
var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

$(function(){

    CALENDAR = $('#calendar').fullCalendar({
        editable: false,
        aspectRatio: 1,
        default: false,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'agendaDay,agendaWeek,month'
        },
        views: {
            agendaTwoDay: {
                type: 'agenda',
                groupByResource: true
            }
        },
        minTime: "6:00:00",
        maxTime: "23:59:59",
        eventOverlap: false, // will cause the event to take up entire resource height
        defaultView: 'agendaWeek',
        eventRender: function(event, element, view)
        {


            element.css({ "backgroundColor" : "red" });


            //$(this)'updateEvent', event);
        },
        resourceLabelText: 'Rooms',
        resources:
            {
                url: '../Pages/Ajax/resources.php',
                type: 'POST'
            },
        events: {
            url: '../Pages/Ajax/reservations.php',
            type: 'POST',
            error: function() {
                alert('there was an error while fetching events!');
            },

            textColor: 'black' // a non-ajax option
        }
    });

});
