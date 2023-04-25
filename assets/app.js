/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

// import jquery
import $ from 'jquery';

$(document).ready(function () {
  $('#play').click(function () {
    $.ajax({
      url: "/play",
      type: "POST",
      data: { /* optional: Daten, die an den Controller übergeben werden sollen */},
      success: function (response) {
        // Handle die Antwort vom Controller
        $('body').html(response);

        history.pushState(null, null, "/play");
      },
      error: function (xhr) {
        // Handle die Fehlermeldung, falls der Aufruf fehlschlägt
        console.log(xhr.responseText);
      }
    });
  });


  $('#replay').click(function () {
    $.ajax({
      url: "/",
      type: "POST",
      data: { /* optional: Daten, die an den Controller übergeben werden sollen */},
      success: function (response) {
        // Handle die Antwort vom Controller
        $('body').html(response);

        history.pushState(null, null, "/play");
      },
      error: function (xhr) {
        // Handle die Fehlermeldung, falls der Aufruf fehlschlägt
        console.log(xhr.responseText);
      }
    });
  });

});

