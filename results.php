<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Výsledky předmajálesové hry</title>
  <link rel="stylesheet" href="basicstyles.css">

  <style>
    @media screen and (orientation: portrait) {
      #body {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        align-items: center;
      }

      .chart-containter {
        width: 90vw;
      }
    }

    @media screen and (orientation: landscape) {
      #body {
        display: flex;
        flex-direction: row;
        justify-content: space-around;
        align-items: center;
      }

      .chart-containter {
        width: 30vw;
      }
    }
  </style>
</head>

<body>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <h1>Předmajálesová hra</h1>
  <h4><a href="index.php">zpět na úvod</a></h4>
  <h2>Výsledky</h2>
  <?php
  $config = parse_ini_file('config.ini');

  $sql_servername = $config['sql_servername'];
  $sql_username = $config['sql_username'];
  $sql_password = $config['sql_password'];

  try {
    $conn = new PDO("mysql:host=$sql_servername;dbname=predmajalesova_hra", $sql_username, $sql_password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
    http_response_code(500);
  }
  ?>

  <div id="body">
    <div class="category-div flex flex-column">
      <div class="chart-containter">
        <canvas class="chart"></canvas>
      </div>
      <div class="chart-containter">
        <canvas class="chart"></canvas>
      </div>
    </div>
    <div class="category-div flex flex-column">
      <div class="chart-containter">
        <canvas class="chart"></canvas>
      </div>
      <div class="chart-containter">
        <canvas class="chart"></canvas>
      </div>
    </div>
    <div class="category-div flex flex-column">
      <div class="chart-containter">
        <canvas class="chart"></canvas>
      </div>
      <div class="chart-containter">
        <canvas class="chart"></canvas>
      </div>
    </div>
  </div>

  <script>
    let POINTS_BY_SPOT = [100, 70, 50, 40, 30, 20];

    charts = Array(3);

    function gradientArrayGen(values, color1, color2) {
      let gradientArray = [];
      let color1Array = color1.match(/\d+/g);
      let color2Array = color2.match(/\d+/g);
      for (let i = 0; i < values.length; i++) {
        let colorArray = [];
        for (let j = 0; j < 3; j++) {
          colorArray.push(parseInt(color1Array[j]) + (parseInt(color2Array[j]) - parseInt(color1Array[j])) * values[i]);
        }
        gradientArray.push(`hsl(${colorArray[0]}, ${colorArray[1]}%, ${colorArray[2]}%)`);
      }
      return gradientArray;
    }

    function transparentize(color, opacity) {
      let colorArray = color.match(/\d+/g);
      return (`hsla(${colorArray[0]}, ${colorArray[1]}%, ${colorArray[2]}%, ${opacity})`);
    }

    function linearArrayGen(values) {
      let linearArray = [];
      for (let i = 0; i < values; i++) {
        linearArray.push(i / (values - 1));
      }
      return linearArray;
    }

    color = "hsl(0, 70%, 69%)";
    color2 = "hsl(260, 70%, 70%)";

    class RoundResults {
      round_id;
      category;
      results;
      constructor(round_id, category) {
        this.round_id = round_id;
        this.category = category;
      }
    }
    rounds = [];

    <?php
    $stmt = $conn->prepare("SELECT * FROM rounds ORDER BY id ASC");
    $stmt->execute();

    $result = $stmt->fetch();

    while ($result != false) {
      echo "rounds.push(new RoundResults(" . $result['id'] . ", " . $result['category'] . "));\n";
      $result = $stmt->fetch();
    }
    ?>

    rounds.forEach(element => {
      element.results = fetch('get_results.php?round_id=' + element.round_id)
        .then((response) => {
          return response.json()
        })
    });

    class category {
      bargraphdatasets = [];
      linegraphdatasets = [];
      teamnames = [];
      round_ids = [];
    }

    let categories = Array(3);

    for (let i = 0; i < 3; i++) {
      categories[i] = new category();
    }

    <?php
    $stmt = $conn->prepare("SELECT name, category FROM teams WHERE category != 0 AND name != \"admin\" ORDER BY id ASC");
    $stmt->execute();
    $result = $stmt->fetch();
    while ($result != false) {
      echo "categories[" . ($result['category'] - 1) . "].teamnames.push('" . $result['name'] . "');\n";
      echo "categories[" . ($result['category'] - 1) . "].bargraphdatasets.push(new Array());\n";
      echo "categories[" . ($result['category'] - 1) . "].linegraphdatasets.push(new Array());\n";
      $result = $stmt->fetch();
    }
    ?>

    console.log("rounds:", rounds);
    console.log("categories:", categories);

    Promise.all(rounds.map(round => round.results)).then((promised_results) => {
      rounds.forEach((round, index) => {
        round.results = promised_results[index];
      });

      rounds.forEach(round => {
        category_spots = Array(3).fill(0);
        let category_create = round.category - 1;
        console.log("promisedresults", round.results);

        if (round.category == 3) {
          for (let i = 0; i < 3; i++) {
            if (categories[i].round_ids.indexOf(round.round_id) == -1) {
              console.log("create round", round.round_id, "for category", i + 1);
              categories[i].round_ids.push(round.round_id);
              categories[i].bargraphdatasets.forEach(dataset => {
                dataset.push(0);
              });
              categories[i].linegraphdatasets.forEach((dataset, index) => {
                if (dataset.length == 0) {
                  console.log("category:", i + 1, "dataset: ", index, dataset, dataset.length);
                  dataset.push(0);
                  console.log("lenght after", dataset.length);
                } else {
                  dataset.push(dataset[dataset.length - 1]);
                  console.log("YAAAA", "dataset:", index, dataset, dataset.length);
                }
              });
            }
          }
        } else {
          if (categories[2].round_ids.indexOf(round.round_id) == -1) {
            console.log("create round", round.round_id, "for category", 2 + 1);
            categories[2].round_ids.push(round.round_id);
            categories[2].bargraphdatasets.forEach(dataset => {
              dataset.push(0);
            });
            categories[2].linegraphdatasets.forEach(dataset => {
              if (dataset.length == 0) {
                dataset.push(0);
              } else {
                dataset.push(dataset[dataset.length - 1]);
              }
            });
          }
        }

        for (let i = 0; i < round.results.length; i++) {
          let result = round.results[i];
          console.log("result", result);

          let category = result['category'] - 1;
          let teamname = result['name'];
          let time = result['time'];
          let verified = result['verified'];

          let round_index = categories[category].round_ids.indexOf(round.round_id);
          console.log("round_index", round_index, "round_id", categories[category].round_ids[round_index]);
          if (round_index == -1) {
            console.log("round not found", round.round_id);
            return;
          }

          let team_index = categories[category].teamnames.indexOf(teamname);
          if (team_index == -1) {
            console.log("team not found", teamname);
            return;
          }

          categories[category].bargraphdatasets[team_index][round_index] = POINTS_BY_SPOT[category_spots[category]];

          console.log("linegraphdatasets for round" + categories[category].round_ids[round_index] + ":", POINTS_BY_SPOT[category_spots[category]], "ADDED TO", categories[category].linegraphdatasets[team_index][round_index]);
          categories[category].linegraphdatasets[team_index][round_index] += POINTS_BY_SPOT[category_spots[category]];

          console.log("bargraphdatasets for round", categories[category].bargraphdatasets);
          console.log("linegraphdatasets for round", categories[category].linegraphdatasets);

          charts.forEach(charties => {
            try {
              charties.forEach(chart => {
                chart.update();
              });
            } catch (error) {
              console.error(error);
            }
          });

          category_spots[category]++;
        }
      });

      categories.forEach(category => {
        console.log(category);
      });

      console.log("creating charts");

      Array.prototype.forEach.call(document.getElementsByClassName("chart"), (canvas) => {
        canvas.width = parent.offsetWidth;
        canvas.height = parent.offsetWidth;
      });

      for (let i = 0; i < 3; i++) {
        charts[i] = [
          new Chart(document.getElementsByClassName('category-div')[i].getElementsByClassName('chart')[0], {
            type: 'line',
            options: {
              indexAxis: 'x',
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                x: {
                  text: 'kola'
                },
                y: {
                  text: 'body'
                }
              },
              plugins: {
                legend: {
                  display: true,
                  position: 'right'
                },
                title: {
                  display: true,
                  text: 'Vývoj v kategorii ' + (i + 1)
                }
              },
            },

            data: {
              labels: categories[i].round_ids,

              datasets: categories[i].linegraphdatasets.map((data, index) => {
                // console.log("data", data);
                // console.log("index", index);
                // console.log("teamnames", categories[i].teamnames);
                return {
                  label: categories[i].teamnames[index],
                  data: data,
                  backgroundColor: transparentize(gradientArrayGen(linearArrayGen(categories[i].teamnames.length), color, color2)[index], 0.5),
                  borderColor: gradientArrayGen(linearArrayGen(categories[i].teamnames.length), color, color2)[index],
                  borderWidth: 1
                }
              })
            }
          }),
          new Chart(document.getElementsByClassName('category-div')[i].getElementsByClassName('chart')[1], {
            type: 'bar',
            options: {
              indexAxis: 'y',
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                x: {
                  stacked: true
                },
                y: {
                  stacked: true
                }
              },
              plugins: {
                legend: {
                  display: true,
                  position: 'right'
                },
                title: {
                  display: true,
                  text: 'Body kol v kategorii ' + (i + 1)
                }
              },
            },

            data: {
              labels: categories[i].round_ids,

              datasets: categories[i].bargraphdatasets.map((data, index) => {
                // console.log("data", data);
                // console.log("index", index);
                // console.log("teamnames", categories[i].teamnames);
                return {
                  label: categories[i].teamnames[index],
                  data: data,
                  backgroundColor: transparentize(gradientArrayGen(linearArrayGen(categories[i].teamnames.length), color, color2)[index], 0.5),
                  borderColor: gradientArrayGen(linearArrayGen(categories[i].teamnames.length), color, color2)[index],
                  borderWidth: 1
                }
              })
            }
          }),
        ];
      }

    });
  </script>
</body>

</html>