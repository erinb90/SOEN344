

var arrRes,
    systemReservations= [];


var todayDate = moment().startOf('day');
var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
var TODAY = todayDate.format('YYYY-MM-DD');
var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

$(function(){

    CALENDAR = $('#calendar').fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        editable: false,
        theme: true,
        default: 'h(:mm)a',
        aspectRatio: 1,

        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'timelineDay,agendaWeek,month, listMonth'
        },
        defaultView: 'timelineDay',

        resourceLabelText: 'Rooms',
        minTime: "0:00:00",
        maxTime: "23:59:59",
        eventOverlap: true, // will cause the event to take up entire resource height

        eventRender: function(event, element, view)
        {

            element.css({ "color" : "white" });
            if(USER_ID == event.uid )
            {
                if (event.waitlisted)
                {
                    element.css({ "background-color" : "orange" });
                    element.css({ "color" : "black" });
                }
                else
                {
                    element.css({ "background-color" : "green" });
                }


            }



            //$(this)'updateEvent', event);
        },
        resourceLabelText: 'Rooms',
        resources: '../Pages/Ajax/resources.php',

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
