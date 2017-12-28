<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);


include 'Brainy.php';

// tanh     : 30000   0.01    3   -1
// sigmoid  : 30000   0.01    3   -1
// relu     : 3000    0.01    3   0

// choose the tot number of epochs
$epochs = 30000;
// choose the learning rate
$learning_rate = 0.001;
// numbers of hidden neurons of the first (and only one) layer
$hidden_layer_neurons = 3;
// activation functions: relu , tanh , sigmoid
$activation_fun = 'relu';

$brain = new Brainy($learning_rate , $activation_fun);

// this is the input XOR matrix
// remember to replace the zeros with -1 when you use TanH or Sigmoid
$xor_in = [
			[0,0],
			[0,1],
			[1,0],
			[1,1],
];

// this is the output of the XOR
// remember to replace the zeros with -1 when you use TanH or Sigmoid
$xor_out = [
			[1],
			[0],
			[0],
			[1],
];


$input_neurons = count($xor_in[0]);
$output_neurons = count($xor_out[0]);

// getting the W1 weights random matrix (layer between input and the hidden layer) with size 2 x $hidden_layer_neurons
$w1 = $w1_before = $brain->getRandMatrix($input_neurons, $hidden_layer_neurons);

// getting the W2 weights random vector (layer between hidden layer and output) with size $hidden_layer_neurons x 1
$w2 = $w2_before = $brain->getRandMatrix($hidden_layer_neurons , $output_neurons);

// getting the B1 bies random vector with size $hidden_layer_neurons
$b1 = $b1_before = $brain->getRandMatrix($hidden_layer_neurons , 1);

// getting the B2 bies random vector. The size is 1x1 because there is only one output neuron
$b2 = $b2_before =  $brain->getRandMatrix($output_neurons, 1);



$w1 = $w1_before = [
	[ -0.43 , -0.21 , -0.58 ],
	[ -0.05 ,  0.84 , -0.07 ],
];

$b1 = $b1_before = [
	[ -0.86 ],
	[ -0.76 ],
	[  0.93 ],
];

$w2 = $w2_before = [
	[ 0.61 ],
	[ 0.02 ],
	[ 0.94 ],
];
$b2 = $b1_before = [
	[ -0.88 ],
];



// this is for the chart
$graph = [];
$denom = 0;
$correct = 0;
$points_checker = $epochs / 100 * 4;
if ($points_checker < 10) $points_checker = 10;



// preparing the arrays
foreach($xor_in as $index => $input) {
	$xor_in[$index] = $brain->arrayTranspose($input);
	$xor_out[$index] = $brain->arrayTranspose($xor_out[$index]);
}


$execution_start_time = microtime(true);

for ($i=0; $i<$epochs; $i++) {
	foreach($xor_in as $index => $input) {
		// forward the input and get the output
 		$forward_response = $brain->forward($input, $w1, $b1, $w2, $b2);
	
		// backprotagating the error and finding the new weights and biases
		$new_setts = $brain->backPropagation($forward_response, $input, $xor_out[$index], $w1, $w2, $b1, $b2);
		$w1 = $new_setts['w1'];
		$w2 = $new_setts['w2'];
		$b1 = $new_setts['b1'];
		$b2 = $new_setts['b2'];
		
		// this is only for che accuracy chart
		$f1 = round($brain->getScalarValue($forward_response['A']) , 2);
		$f2 = round($brain->getScalarValue($xor_out[$index]) , 2);
		if ($f2 < 0) $f2 = 0;
		if ($f1 == $f2) $correct++;
		$denom++;

	} // end foreach

	// this is only for che accuracy chart
	if (!($i % $points_checker)) {
		$graph[] = $rate = $correct / $denom;
		$denom = 0;
		$correct = 0;
	}

} // end for $epochs


$execution_time = round( microtime(true) - $execution_start_time ,2);


$g_labes = $g_vals = '';
foreach($graph as $num => $val) {
    $g_labes .= ($num*$points_checker) . ',';
    $g_vals .= (round( $val, 2)) . ',';
}
$g_labes = trim($g_labes, ',');
$g_vals = trim($g_vals, ',');

?>


<html>
<head>
	<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.1.1/Chart.min.js"></script>
	<style>
		body { font-family: monospace; margin: 50px; }
		circle { display:none; }
		.center { text-align:center; }
	</style>
</head>
<body>

<h2 class="center">Activation funcion: <?= ucwords($activation_fun) ?></h2>

<div class="chart" style="width:600px; margin:20px auto;">
	<canvas height="200" id="lineChart" style="height:400px; margin:20px auto;"></canvas>
</div>


<br />
<h3>Hidden neurons: <?= $hidden_layer_neurons ?></h3>
<h3>Learning rate: <?= $learning_rate ?></h3>
<h3>Epochs: <?= $epochs ?></h3>
<h3>Execution time: <?= $execution_time ?> sec</h3>

<br />
<br />





<?php
	echo '<hr /><h1>Prediction:</h1>';
	foreach($xor_in as $index => $input) {
		$prediction = $brain->forward($input, $w1, $b1, $w2, $b2);

		sm( $prediction['A'] );
	}

	echo '<hr /><h1>Before</h1>';
	echo '<br /><h4>Weights matrix W1</h4>';
	sm($w1_before);
	echo '<br /><h4>Bias matrix B1</h4>';
	sm($b1_before);
	echo '<br /><h4>Weights matrix W2</h4>';
	sm($w2_before);
	echo '<br /><h4>Bias matrix B2</h4>';
	sm($b2_before);
	
	echo '<hr /><h1>After</h1>';
	echo '<br /><h4>Weights matrix W1</h4>';
	sm($w1);
	echo '<br /><h4>Bias matrix B1</h4>';
	sm($b1);
	echo '<br /><h4>Weights matrix W2</h4>';
	sm($w2);
	echo '<br /><h4>Bias matrix B2</h4>';
	sm($b2);
	echo '<hr />';
	
	$str  = '$w1 = $w1_before = '.var_export($w1_before, true).';' ."\n";
	$str .= '$b1 = $b1_before = '.var_export($b1_before, true).';' ."\n";
	$str .= '$w2 = $w2_before = '.var_export($w2_before, true).';' ."\n";
	$str .= '$b2 = $b2_before = '.var_export($b2_before, true).';' ."\n";

	dd($str, false);
?>




<script>
  $(function () {
	  
        var areaChartData = {
          labels: [<?= $g_labes ?>],
          datasets: [
            {
              fillColor: "rgba(60,141,188,0.9)",
              strokeColor: "rgba(60,141,188,0.8)",
              pointColor: "#3b8bba",
              pointStrokeColor: "rgba(60,141,188,1)",
              pointHighlightFill: "#fff",
              pointHighlightStroke: "rgba(60,141,188,1)",
              data: [<?= $g_vals ?>],
            }
          ]
        };

        var areaChartOptions = {
           showScale: true,
           scaleShowGridLines: true,
           scaleGridLineColor: "rgba(0,0,0,.05)",
           scaleGridLineWidth: 1,
           scaleShowHorizontalLines: true,
           scaleShowVerticalLines: true,
           bezierCurve: true,
           bezierCurveTension: 0.3,
           pointDot: false,
           pointDotRadius: 4,
           pointDotStrokeWidth: 1,
           pointHitDetectionRadius: 20,
           datasetStroke: true,
           datasetStrokeWidth: 2,
           datasetFill: true,
           maintainAspectRatio: false,
           responsive: true,
         };
	
	    var lineChartCanvas = $("#lineChart").get(0).getContext("2d");
	    var lineChart = new Chart(lineChartCanvas);
	    var lineChartOptions = areaChartOptions;
	    lineChartOptions.datasetFill = false;
	    lineChart.Line(areaChartData, lineChartOptions);
  });

</script>


</body>
</html>
