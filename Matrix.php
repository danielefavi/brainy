<?php


class Matrix {

	/**
	 * It applies the function RELU or SIGMOID or LOG or NEGATIVE or TANH
	 * to each element of the matrix
	 *
	 * @param	string	$operation	what kind of operation has to be performed in each element of the matrix
	 * @param	array		$matrix			the matrix
	 * @return array							it return the matrix with the operation applied to all elements
	 */
	public function matrixOperation($operation, $matrix) {
		// I commented the following check in order to emprove performance
		//$allowed_op = ['relu', 'sigmoid', 'log', 'negative','tanh'];
		//if (!in_array($operation, $allowed_op)) throw new Exception("The operation does not exist!");

		$ret = [];

		foreach($matrix as $row_number => $values) {
			foreach($values as $col_number => $value) {
				$ret[$row_number][$col_number] = $this->$operation($value);
			}
		}

		return $ret;
	}


	/**
	 * It performs RELU function on the param
	 *
	 * @link https://en.wikipedia.org/wiki/Rectifier_(neural_networks)
	 * @param numeric	$x
	 * @return numeric
	 */
	public function relu($x) {
		return $x * (($x > 0) ? 1 : 0);
	}


	/**
	 * It performs the natural logarithm
	 *
	 * @param numeric	$x
	 * @return numeric
	 */
	public function log($x) {
		return log($x);
	}


	/**
	 * It performs SIGMOID function
	 *
	 * @link http://mathworld.wolfram.com/SigmoidFunction.html
	 * @param numeric	$x
	 * @return numeric
	 */
	public function sigmoid($x) {
		return ( 1 / (1 + 1/(exp($x)) ) );
	}


	/**
	 * It makes a number negative
	 *
	 * @param numeric	$x
	 * @return numeric
	 */
	public function negative($x) {
		return $x * (-1);
	}


	/**
	 * It performs hyperbolic tangent function
	 *
	 * @link http://mathworld.wolfram.com/HyperbolicTangent.html
	 * @param numeric	$x
	 * @return numeric
	 */
	public function tanh($x) {
		return tanh($x);
	}


	/**
	 * It performs the softmax on a matrix
	 *
	 * @link https://en.wikipedia.org/wiki/Softmax_function
	 * @param	array $matrix
	 * @return array
	 */
	public function matrixSoftmax($matrix) {
		$softmaxed = [];

		foreach($matrix as $row_number => $row) {
			$row_somm= 0;
			foreach($row as $col_number => $value) {
				$row_somm += exp($value);
			}
			foreach($row as $col_number => $value) {
				if ($row_somm == 0) $softmaxed[$row_number][$col_number] = isset($softmaxed[$row_number][$col_number]) ? $softmaxed[$row_number][$col_number] : 0;
				else $softmaxed[$row_number][$col_number] = exp($value) / $row_somm;
			}
		}

		return $softmaxed;
	}


	/**
	 * It sums a scalar value to all the elements of a matrix
	 * As $val_par is accepted a numeric value or a matrix with dimention 1x1
	 *
	 * @param array $matrix the matrix
	 * @param mixed $val_par the scalar value or matrix with dimension 1x1
	 * @return array
	 */
	public function matrixSumValue($matrix, $val_par) {
		$ret = [];
		// extracting the value in case of a matrix 1x1 dimention
		$val_par = $this->extractValue($val_par);

		foreach($matrix as $row_number => $values) {
			foreach($values as $col_number => $value) {
				$ret[$row_number][$col_number] = $value + $val_par;
			}
		}

		return $ret;
	}


	/**
	 * It multiplies a scalar value to all the elements of a matrix
	 * As $val_par is accepted a numeric value or a matrix with dimention 1x1
	 *
	 * @param array $matrix the matrix
	 * @param mixed $val_par the scalar value or matrix with dimension 1x1
	 * @return array
	 */
	public function matrixTimesValue($matrix, $val_par) {
		$ret = [];
		// extracting the value in case of a matrix 1x1 dimention
		$val_par = $this->extractValue($val_par);

		foreach($matrix as $row_number => $values) {
			foreach($values as $col_number => $value) {
				$ret[$row_number][$col_number] = $value * $val_par;
			}
		}

		return $ret;
	}


	/**
	 * It returns the value the value of a matrix 1x1
	 * or it returns just the parameter $val when it is numeric
	 *
	 * @param mixed $val matrix 1x1
	 * @return numeric
	 */
	public function extractValue($val) {
		if (is_numeric($val)) return $val;

		if (count($val) > 1) throw new Exception('Not correct value! count($val) > 1 ');
		if (!isset($val[0])) throw new Exception('Not correct value! !isset($val[0])');
		if (count($val[0]) > 1) throw new Exception('Not correct value! count($val[0]) > 1');

		return $val[0][0];
	}


	/**
	 * It sums two matrices
	 *
	 * @param array $m1 first matrix
	 * @param array $m2 second matrix
	 * @return array
	 */
	public function matrixSum($m1, $m2) {
		// checking if the two matrices have the same dimentions
		$rows_m1 = count($m1);
		$cols_m1 = count($m1[0]);
		$rows_m2 = count($m2);
		$cols_m2 = count($m2[0]);
		if (($cols_m1 != $cols_m2) or ($rows_m1 != $rows_m2)) throw new Exception('The matrices cannot be added!');

		$sum = [];

		for ($r=0; $r<$rows_m1; $r++) {
			for ($c=0; $c<$cols_m1; $c++) {
				$sum[$r][$c] = $m1[$r][$c] + $m2[$r][$c];
			}
		}

		return $sum;
	}


	/**
	 * It subtracts two matrices
	 *
	 * @param array $m1 first matrix
	 * @param array $m2 second matrix
	 * @return array
	 */
	public function matrixSub($m1, $m2) {
		// checking if the two matrices have the same dimentions
		$rows_m1 = count($m1);
		$cols_m1 = count($m1[0]);
		$rows_m2 = count($m2);
		$cols_m2 = count($m2[0]);
		if (($cols_m1 != $cols_m2) or ($rows_m1 != $rows_m2)) throw new Exception('The matrices cannot be subtracted!');

		$sum = [];

		for ($r=0; $r<$rows_m1; $r++) {
			for ($c=0; $c<$cols_m1; $c++) {
				$sum[$r][$c] = $m1[$r][$c] - $m2[$r][$c];
			}
		}

		return $sum;
	}


	/**
	 * It multiplies two matrices value by value
	 * This is not a DOT PRODUCT
	 *
	 * @param array $m1 first matrix
	 * @param array $m2 second matrix
	 * @return array
	 */
	public function matrixProductValueByValue($m1, $m2) {
		// checking if the two matrices have the same dimentions
		$rows_m1 = count($m1);
		$cols_m1 = count($m1[0]);
		$rows_m2 = count($m2);
		$cols_m2 = count($m2[0]);
		if (($cols_m1 != $cols_m2) or ($rows_m1 != $rows_m2)) throw new Exception('The matrices cannot be multiplied Value by Value!');

		$sum = [];

		for ($r=0; $r<$rows_m1; $r++) {
			for ($c=0; $c<$cols_m1; $c++) {
				$sum[$r][$c] = $m1[$r][$c] * $m2[$r][$c];
			}
		}

		return $sum;
	}


	/**
	 * It returns the index of the highest value in an array
	 *
	 * @param array $array
	 * @return numeric
	 */
	public function arrayArgmax($array) {
		if (!count($array)) return false;

		$res = array_keys($array, max($array));
		if (isset($res[0])) return $res[0];

		return false;
	}


	/**
	 * It returns the DOT product of two matrices
	 *
	 * @param array $m1 first matrix
	 * @param array $m2 second matrix
	 * @return array
	 */
	public function matrixDotProduct($m1, $m2) {
		// checking if the matrices can be multiplied
		$rows_m1 = count($m1);
		$cols_m1 = count($m1[0]);
		$rows_m2 = count($m2);
		$cols_m2 = count($m2[0]);
		if ($cols_m1 != $rows_m2) throw new Exception('The matrices cannot be multiplied!');

		$prod = [];

		for ($i=0; $i<$rows_m1; $i++) {
			for ($j=0; $j<$cols_m2; $j++) {
				$prod[$i][$j] = 0;
				for ($k=0; $k<$rows_m2; $k++) {
					$prod[$i][$j] += $m1[$i][$k] * $m2[$k][$j];
				}
			}
		}

		return $prod;
	}


	/**
	 * It returns the trasposition of a matrix
	 *
	 * @param array $matrix
	 * @return array
	 */
	public function matrixTranspose($matrix) {
		$transpose= [];

		foreach($matrix as $row_number => $values) {
			foreach($values as $col_number => $value) {
				$transpose[$col_number][$row_number] = $value;
			}
		}

		return $transpose;
	}


	/**
	 * It transform a simple array into vector
	 *
	 * @param array $array
	 * @return array
	 */
	public function arrayTranspose($array) {
		if (is_array($array[0])) {
			//return $array;
			throw new Exception('$array is not an array of numbers!');
		}

		$transpose= [];

		foreach($array as $row_number => $value) {
			$transpose[$row_number][0] = $value;
		}

		return $transpose;
	}


	/**
	 * It sums a matrix with a vector; eg: the first element of the vector
	 * will be sum to all the elements of the first row of the matrix, and so on...
	 *
	 * @param array $m1
	 * @param array $m2
	 * @return array
	 */
	public function sumMatrixVertically($m1, $m2) {
		// checking the two matrix are compatible to each other
		$rows_m1 = count($m1);
		$cols_m1 = count($m1[0]);
		$rows_m2 = count($m2);
		$cols_m2 = count($m2[0]);
		if ($rows_m1 != $rows_m2) throw new Exception('The matrix have different rows number!');
		if (($cols_m1 != 1) and ($cols_m2 != 1)) throw new Exception('One of the matrix must be a vesctor!');

		// checking who is the matrix and vector between $m1 or $m1
		$matrix = ($cols_m1 != 1) ? $m1 : $m2;
		$vector = ($cols_m1 == 1) ? $m1 : $m2;

		$new = [];

		foreach($matrix as $row_number => $row) {
			foreach($row as $col_number => $element) {
				$new[$row_number][$col_number] = $element + $vector[$row_number][0];
			}
		}

		return $new;
	}


	/**
	 * It sums all the elements in the matrix row
	 * so in return you will have a vector
	 *
	 * @param array $matrix
	 * @return array
	 */
	public function sumMatrixElementVertically($matrix) {
		$new = [];

		if (is_array($matrix[0])) {
			foreach ($matrix as $line => $row) {
				if (!isset($new[$line])) $new[$line][0] = 0;
				foreach($row as $element) {
					$new[$line][0] += $element;
				}
			}
		}

		return $new;
	}


	
	/**
	 * For a given scalar (matrix 1x1 that is an array or an array of array) it return the value number
	 *
	 * @param matrix $matrix
	 * @return float
	 */
	public function getScalarValue($matrix) {
		if (is_array($matrix)) {
			if (is_array($matrix[0])) return $matrix[0][0];
			else return $matrix[0];
		}
		
		return $matrix;
	}
	
	
	
	/**
	 * It generates a random matrix with dimentions $rows X $cols
	 *
	 * @param numeric $rows rows of the matrix
	 * @param numeric $cols columns of the matrix
	 * @return array
	 */
	public function getRandMatrix($rows, $cols) {
		if ($rows < 1) throw new Exception('Matrix ROWS must be greater than 0!');
		if ($cols< 1) throw new Exception('Matrix COLUMNS must be greater than 0!');

		$matrix = [];
		$aaa_test = 0;
		for($r=0; $r<$rows; $r++) {
			for($c=0; $c<$cols; $c++) {
				$matrix[$r][$c] = rand(-1000000,1000000)/1000000;
			}
		}

		return $matrix;
	}


	/**
	 * It generates a matrix of zeros with dimentions $rows X $cols
	 *
	 * @param numeric $rows rows of the matrix
	 * @param numeric $cols columns of the matrix
	 * @return array
	 */
	public function getZeroMatrix($rows, $cols) {
		if ($rows < 1) throw new Exception('Matrix ROWS must be greater than 0!');
		if ($cols< 1) throw new Exception('Matrix COLUMNS must be greater than 0!');

		$matrix = [];

		for($r=0; $r<$rows; $r++) {
			for($c=0; $c<$cols; $c++) {
				$matrix[$r][$c] = 0;
			}
		}

		return $matrix;
	}

}



/**
 * die and dump function
 * @param mixed $val the value to show
 * @param bool $stop die or not
 */
function dd($val=null, $stop=true) {
	echo '<pre>';
	print_r($val);
	echo '</pre>';
	if ($stop) die();
}


/**
 * it shows the matrix in a nice way -- SM = Show Matrix
 * @param array $matrix
 * @param string $label
 */
function sm($matrix, $label=null) {
	if ($label) echo "<pre> ----------- $label ----------- </pre>";

	echo '<pre>';
	foreach($matrix as $row) {
		echo '|	';
		foreach($row as $num) {
			echo $num . '	';
		}
		echo '	|<br />';
	}
	echo '</pre>';

	if ($label) echo '<pre> --------------------------------- </pre><br />';
}
