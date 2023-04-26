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
      data: { },
      success: function (response) {
        // Handle die Antwort vom Controller
        $('body').html(response);

        history.pushState(null, null, "/play");
      },
      error: function (xhr) {
        console.log(xhr.responseText);
      }
    });
  });


  $('#replay').click(function () {
    $.ajax({
      url: "/",
      type: "POST",
      data: { },
      success: function (response) {
        $('body').html(response);

        history.pushState(null, null, "/play");
      },
      error: function (xhr) {
        console.log(xhr.responseText);
      }
    });
  });


  $('.tic-tac-toe-cell').click(function() {
    const typed_cell = $(this).attr('id');
    var player = $('.turn-info').children().attr('id');

    $(this).html('X');
    $.ajax({
      url: "/manager",
      type: "POST",
      data: {
        'typed_cell': typed_cell,
        'player': player
      },
      success: function (response) {
        $('.bottom-field').html(response);

        history.pushState(null, null, "/manager");
      },
      error: function (xhr) {
        console.log(xhr.responseText);
      }
    });
  });


});

