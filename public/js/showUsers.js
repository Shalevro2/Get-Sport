'use strict';

$(document).ready(function () {
    $("#searchInput").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("tbody tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});

// Hook `click` on the table header, but only call our callback if
// that click passes through a `th`
$("#usersTable thead").on("click", "th", function () {
    // Which column is this?
    var index = $(this).index();

    // Get the tbody
    var tbody = $(this).closest("table").find("tbody");

    // Disconnect the rows and get them as an array
    var rows = tbody.children().detach().get();

    // Sort it
    rows.sort(function (left, right) {
        // Get the text of the relevant td from left and right
        var $left = $(left).children().eq(index);
        var $right = $(right).children().eq(index);
        return $left.text().localeCompare($right.text());
    });

    // Put them back in the tbody
    tbody.append(rows);
});