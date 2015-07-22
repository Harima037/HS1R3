google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);
function drawChart() {

  var data = google.visualization.arrayToDataTable([
    ['Task', 'Hours per Day'],
    ['Work',     11],
    ['Eat',      13]
  ]);

  var options = {
    legend:{position:'top'}
  };

  var chart = new google.visualization.PieChart(document.getElementById('grafica-general'));

  chart.draw(data, options);
}