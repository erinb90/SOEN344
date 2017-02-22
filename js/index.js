/*global window, console, document, $*/

( function() {
    "use strict";
    $( document ).ready( function() {
        var cookieName = "intro-page-cookie";
        var go = $.cookie( cookieName );
        if ( go === null ) {
            $.cookie( cookieName, "starwars", { path: "/", expires: 2 } );
            window.location = "/starwars.html";
        }

        $( "#watchIntro" ).click( function() {
            window.location = "/starwars.html";
        } );

        $( "#myBtn" ).click( function() {
            $( "#myModal" ).modal();
        } );

        $( document ).on( "click", "#login", function() {

            var $clicker = $( "#login" );
            var ser = $( "form#LoginForm" );
            var originalText = $clicker.text();
            $clicker.text( "Logging in..." );
            $clicker.addClass( "disabled" );
            ser.serialize();

            console.log( ser );
            $( "#results" ).html( "" );

            $.ajax( {
                type: "POST",
                url: "includes/Pages/Validation.php", //File name
                data: ser,
                success: function( data ) {
                    $clicker.text( originalText );
                    $( "#results" ).html( data );
                },
                complete: function() {
                    $clicker.text( originalText );
                    $clicker.removeClass( "disabled" );
                },
                error: function() {
                    $clicker.text( originalText );
                }
            } );
        } );
    } );
} )();
