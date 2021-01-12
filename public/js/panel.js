'use strict';

/**
 * function draw chart of amount users in city.
 * Chart type pie
 */
function drawChartCityUsers() {
    var settings = { //settings for ajax request
        "url": "getNumOfUsersInCity",
        "method": "post",
        "headers": {
            "Content-Type": "application/x-www-form-urlencoded",
        }
    }
    $.ajax(settings).done(function (response) {
        var json = JSON.parse(response); //from array to json
        let data = new google.visualization.DataTable();
        data.addColumn("string", "City Name"); //columns type and title
        data.addColumn("number", "Amount");
        json.forEach(row => { //add rows
            data.addRow([row.cityName, parseInt(row['count(*)'])]); //cityName and amount
        });
        var options = {
            title: 'Amount of Users in city',
            height: 400
        };
        var chart = new google.visualization.PieChart(document.getElementById('usersCityGraph')); //type of pie chart
        chart.draw(data, options); //draw chart into usersCityGraph div
        doc.addImage(chart.getImageURI(), 'PNG', 0, 200, width - 20, 70); //add image to pdf doc
    });
}

/**
 * function draw chart of amount groups in sport category.
 * Chart type bar
 */
function drawChartSportGraph() {
    var settings = { //settings for ajax request
        "url": "getAmountGroups",
        "method": "post",
        "headers": {
            "Content-Type": "application/x-www-form-urlencoded",
        }
    }
    $.ajax(settings).done(function (response) {
        var json = JSON.parse(response);
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Sport Category'); //columns type and title
        data.addColumn('number', 'Football');
        data.addColumn('number', 'BasketBall');
        data.addColumn('number', 'Tennis');
        data.addColumn('number', 'Running');
        data.addColumn('number', 'Gym');

        data.addRow([
            "Sport Category",
            json.Football,
            json.BasketBall,
            json.Tennis,
            json.Running,
            json.Gym
        ]);

        var options = {
            title: 'Amount of groups',
            bars: 'vertical',
            vAxis: {
                format: 'decimal'
            },
            height: 400,
        };

        var chart = new google.visualization.BarChart(document.getElementById('sportGraph'));
        chart.draw(data, options);
        doc.addImage(chart.getImageURI(), 'PNG', 0, 110, width - 20, 70);
    });
}


function drawChartMonth() {
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var settings = {
        "url": "getNumOfGroupsInMonthAction",
        "method": "post",
        "headers": {
            "Content-Type": "application/x-www-form-urlencoded",
        }
    }
    $.ajax(settings).done(function (response) {
        var json = JSON.parse(response);
        let dataTable = new google.visualization.DataTable();

        dataTable.addColumn("string", "Months");
        dataTable.addColumn("number", "Amount");
        dataTable.addColumn({
            type: 'number',
            role: 'annotation'
        });


        json.forEach(row => {
            dataTable.addRow([monthNames[row['month(`datePlay`)'] - 1], parseInt(row['count(*)']), parseInt(row['count(*)'])]);
        });
        let chart = new google.visualization.ColumnChart(document.getElementById("monthGraph"));

        let options = {
            title: "Games per month",
            hAxis: {
                title: "Months",
                gridelines: {
                    count: -1
                }
            },
            vAxis: {
                gridlines: {
                    color: 'none'
                },
                minValue: 0,
                title: "Amount of games",
                format: '#',
                maxValue: 35
            }
        };

        chart.draw(dataTable, options);
        doc.addImage(chart.getImageURI(), 'PNG', 0, 40, width - 20, 50);
    });
}

google.charts.load('current', {
    packages: ['corechart']
});
var doc = new jsPDF("p", "mm", "a4");
var width = doc.internal.pageSize.getWidth();
var height = doc.internal.pageSize.getHeight();
const title = 'GetSport - Report ' + (new Date()).toLocaleDateString();
doc.text(title, width / 2 - title.length, 22);


google.charts.setOnLoadCallback(drawChartSportGraph);
window.addEventListener('resize', drawChartSportGraph, false);
google.charts.setOnLoadCallback(drawChartMonth);
window.addEventListener('resize', drawChartMonth, false);
google.charts.setOnLoadCallback(drawChartCityUsers);
window.addEventListener('resize', drawChartCityUsers, false);

document.getElementById('create_pdf').addEventListener('click', function () {
    doc.save('GetSport_Report');
}, false);