<?php

include_once "Matrix.php";

class Brainy extends Matrix {
  private $learning_rate = 0.01;
  private $activation_fun = 'relu';


  /**
   * Set you neural network with the learning rate anf the activation function you prefer
   *
   * @param numeric $learning_rate  choose your learning rate
   * @param string $activation_fun  choose your activation funciton: RELU or SIGMOID or TANH
   */
  public function __construct($learning_rate, $activation_fun) {
    $activation_fun = strtolower($activation_fun);
    if (!is_numeric($learning_rate)) throw new Exception('The learning rate is not numeric');
    if (!in_array($activation_fun, ['relu', 'sigmoid', 'tanh'])) throw new Exception('The allowed activation funciton are: RELU, SIGMOID, TANH');

    $this->learning_rate = $learning_rate;
    $this->activation_fun = $activation_fun;
  }


  /**
   * Forward propagation: it gets the output (matrix A)
   *
   * @param array $input  is the input vector
   * @param array $w1     the weights matrix (between input and hidden layer)
   * @param array $b1     the bias vector (between input and hidden layer)
   * @param array $w2     the weights matrix (between the hidden layer and the output)
   * @param array $b2     the bias vector (between the hidden layer and the output)
   * @return array        it returns the matrix A, which is the final output, and Z, which is the output of the hidden layer
   */
  public function forward($input, $w1, $b1, $w2, $b2) {
    // Z = TANH( W1°X + B1 )    --> TANH or RELU or SIGMOID
    $dot = $this->matrixDotProduct($this->matrixTranspose($w1), $input);
    $sum = $this->matrixSum($dot, $b1);
    $z = $this->matrixOperation($this->activation_fun, $sum);

    // A = SOFTMAX( W2°Z + B2 )
    $dot = $this->matrixDotProduct($this->matrixTranspose($w2), $z);
    $sum = $this->matrixSum($dot, $b2);
    $a = $this->matrixSoftmax($sum);

    return [
       'A' => $a // output
      ,'Z' => $z // output of the hidden layer
    ];
  }


  /**
   * This is the function that corrects the weights propapating back the errors.
   * The function is using the gradient descent in order to find the minimum.
   *
   * @param array $r      if the matrix from the forard matrix
   * @param array $input  is the input vector
   * @param array $output the expected output vector
   * @param array $w1     the weights matrix (between input and hidden layer)
   * @param array $b1     the bias vector (between input and hidden layer)
   * @param array $w2     the weights matrix (between the hidden layer and the output)
   * @param array $b2     the bias vector (between the hidden layer and the output)
   * @return array        matrices of the weights (W1 and W2) and bias (B1 and B2) with new values
   */
  public function backPropagation($r, $input, $output, $w1, $w2, $b1, $b2) {
    // correcting the weights W2
    // W2 = W2 - ( learning_rate * (A - Out) ° Z^T )^T
    $diff = $this->matrixSub($r['A'], $output);
    $z_t = $this->matrixTranspose($r['Z']);
    $prod = $this->matrixDotProduct($diff, $z_t);
    $corr_mat = $this->matrixTimesValue($prod, $this->learning_rate);
    $w2 = $this->matrixSub($w2, $this->matrixTranspose($corr_mat));

    // correcting the bias B2
    // B2 = B2 - learning_rate * (A - Out)
    $corr_mat = $this->matrixTimesValue($diff, $this->learning_rate);
    $b2 = $this->matrixSub($b2, $corr_mat);

    // calculating the derivative depending on the activate funciton
    // dZ = (W2 ° (A - Out)) * DERIVATIVE(Z)
    if ($this->activation_fun == 'tanh') $z_der = $this->tanhDerivative($r['Z']);
    else if ($this->activation_fun == 'sigmoid') $z_der = $this->sigmoidDerivative($r['Z']);
    else if ($this->activation_fun == 'relu') $z_der = $this->reluDerivate($r['Z']);

    $prod = $this->matrixDotProduct($w2, $diff);
    $dZ = $this->matrixProductValueByValue($prod, $z_der);

    // correctiong the weights W1
    // W1 = W1 - ( learning_rate * (dZ ° In^T) )^T
    $prod = $this->matrixDotProduct($dZ, $this->matrixTranspose($input));
    $corr_mat = $this->matrixTimesValue($prod, $this->learning_rate);
    $w1 = $this->matrixSub($w1,$this->matrixTranspose($corr_mat) );

    // correcting the bias B1
    // B1 = B1 - learning_rate * dZ
    $corr_mat = $this->matrixTimesValue($dZ, $this->learning_rate);
    $b1 = $this->matrixSub($b1, $corr_mat);

    return [
       'w1' => $w1
      ,'w2' => $w2
      ,'b1' => $b1
      ,'b2' => $b2
    ];
  }


  /**
   * It calculates the Sigmoid derivative of a given matrix
   * d/dX sigm = sigm (1 - sigm)
   *
   * @param array $matrix   the matrix where you want to calculate the derivative
   * @return array          the final matrix
   */
  public function sigmoidDerivative($z) {
    $z_2 = $this->matrixProductValueByValue($z,$z);
    return $this->matrixSub($z, $z_2);
  }


  /**
   * It calculates the Relu derivative of a given matrix
   * d/dX relu =  if (x > 0) then 1 else 0
   *
   * @param array $matrix   the matrix where you want to calculate the derivative
   * @return array          the final matrix
   */
  public function reluDerivate($z) {
    $relu_der = [];

    foreach($z as $row_num => $row) {
      foreach($row as $col_num => $val) {
        $relu_der[$row_num][$col_num] = ($val > 0) ? 1 : 0;
      }
    }

    return $relu_der;
  }


  /**
   * It calculates the Hyperbolic Tangent derivative of a given matrix
   * d/dX tanh = (1-(tanh)^2)
   *
   * @param array $matrix   the matrix where you want to calculate the derivative
   * @return array          the final matrix
   */
  public function tanhDerivative($matrix) {
    $matrix_square = $this->matrixProductValueByValue($matrix,$matrix);
    $matrix_neg = $this->matrixTimesValue($matrix_square, -1);
    return $this->matrixSumValue($matrix_neg, 1);
  }


}
