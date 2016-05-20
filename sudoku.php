<?php

const SIZE_MATRIX = 9;  
const CELL_EMPTY = -1;  

$a = array();   // матрица судоку

/*
 * Анализирует строку параметров скрипта, и заполняет данными матрицу $a
 * Возвращает:
 *      true - если данные корректны и матрица успешно заполнена
 *      false - при некорректных данных
 */
function parseArg(){
    global $argc;
    global $argv;
    global $a;
    
    for ($i=0; $i < SIZE_MATRIX; $i++){
        $a[$i] = array();
    }    
    if ($argc != 2){
        echo "неправильное количество аргументов\n";
        echo "используйте строку запуска: ./sudoku.php <data>\n";        
        echo "например: ./sudoku.php 75...9..4.1...89....6.4...184.2.7...............4.1.284...9.6....51...3.1..8...45\n";                
        return 1;        
    }
    $cmdStr = $argv[1];
    if (strlen($cmdStr) != SIZE_MATRIX*SIZE_MATRIX){
        echo "неправильное количество данных (требуется строка из ".SIZE_MATRIX*SIZE_MATRIX." символа)\n";
        return 1;
    }
    $strCmd = $argv[1];
    $x=0;
    $y=0;
    $i=0;
    while ($i < SIZE_MATRIX*SIZE_MATRIX){
        if ($cmdStr[$i] == '.'){
            $a[$x][$y] = CELL_EMPTY;
        }else{
            if ($cmdStr[$i] < '0' || $cmdStr[$i] > '9'){
                echo "некорректные данные в ячейке: [".($x+1)."][".($y+1)."]\n";
                return 1;                
            }
            $a[$x][$y] = $cmdStr[$i] - '0';
        }
        $i++;
        nextXY($x, $y, $x, $y);
    }
    return 0;
}


/*
 * Проверяет входные данные 
 * Возвращает:
 *      true - если данные корректны
 *      false - если данные с ошибками
 */
function checkInputData() {
    global $a;
    for ($y=0; $y < SIZE_MATRIX; $y++){
        for ($x=0; $x < SIZE_MATRIX; $x++){            
            if ($a[$x][$y] != CELL_EMPTY){
                if (!checkCell($x, $y, $a[$x][$y])){
                    echo "некорректные данные в ячейке: [".($x+1)."][".($y+1)."]\n";                    
                    return false;
                }
            }            
        }
    }    
    return true;
}


/*
 * Печатает матрицу
 */
function printMatrix() {
    global $a;
    
    for ($y=0; $y < SIZE_MATRIX; $y++){
        for ($x=0; $x < SIZE_MATRIX; $x++){
            if ($a[$x][$y] == CELL_EMPTY){
                echo ". ";
            }else{
                echo $a[$x][$y]." ";
            }            
        }
        echo "\n";
    }
}


/*
 * Печатает матрицу в одну строку
 */
function printMatrixLine() {
    global $a;
    
    for ($y=0; $y < SIZE_MATRIX; $y++){
        for ($x=0; $x < SIZE_MATRIX; $x++){
            if ($a[$x][$y] == CELL_EMPTY){
                echo ".";
            }else{
                echo $a[$x][$y];
            }            
        }
    }
    echo "\n";    
}


/*
 * Определяет следующие соседние координаты для обхода матрицы
 * слева направо и сверху вниз
 * $x, $y - исходные координаты
 * Результат: $newX, $newY - следующие координаты
 */
function nextXY($x, $y, &$newX, &$newY) {
    $newX = $x+1;
    $newY = $y;
    if ($newX >= SIZE_MATRIX){
        $newX=0;
        $newY++;
    }
}


/*
 * Определяет диапазон координат для региона (квадрат 3x3)
 * для одной из координатных осей и координаты $xy
 * Результат: $xy1 - $xy2
 */
function getXYRegion($xy, &$xy1, &$xy2) {
    if ($xy < 3){
        $xy1 = 0;
        $xy2 = 2;
    }else{
        if ($xy > 5){
            $xy1 = 6;
            $xy2 = 8;
        }else{
            $xy1 = 3;
            $xy2 = 5;
        }
    }    
}

/*
 * Проверяет - можно ли (по правилам судоку) в ячейку с координатами $x, $y 
 * записать значение $v
 * Возвращает:
 *      true - если если можно
 *      false - если нельзя
 */
function checkCell($x, $y, $v) {
    global $a;
    for ($i=0; $i < SIZE_MATRIX; $i++){
        if ($i != $x &&  $a[$i][$y] == $v){
            return false;
        }
        if ($i != $y &&  $a[$x][$i] == $v){
            return false;
        }        
    }
    getXYRegion($x, $xR1, $xR2);
    getXYRegion($y, $yR1, $yR2);    
    for ($yR = $yR1; $yR <= $yR2; $yR++){
        for ($xR = $xR1; $xR <= $xR2; $xR++){
            if ($yR == $y && $xR == $x){
                continue;
            }
            if ($a[$xR][$yR] == $v){
                return false;
            }
        }        
    }    
    return true;
}

/*
 * решает рекурсивно задачу судоку
 * решение продолжается с ячейки с координатами $x, $y
 * возвращает:
 *      true - если решение найдено
 *      false - если решение не найдено
 */
function sudoku($x, $y) {
    global $a;
    if ($y > (SIZE_MATRIX-1)){  
        return true;
    }
    if ($a[$x][$y] == CELL_EMPTY){
        for ($v=0; $v < 9; $v++){
            if (checkCell($x, $y, $v)){
                $a[$x][$y] = $v;
                nextXY($x, $y, $newX, $newY);
                if (sudoku($newX, $newY)){
                    return true;
                }else{
                    $a[$x][$y] = CELL_EMPTY;
                }
            }
        }
        return false;
    }else{
        nextXY($x, $y, $newX, $newY);
        return sudoku($newX, $newY);
    }
}

if (parseArg() == 0){
    //printMatrix();
    if (checkInputData()){
        if (sudoku(0, 0)){
            echo "решение найдено:\n";
            //printMatrix();            
            printMatrixLine();      
        }else{
            echo "решение ненайдено:\n";            
        }
    }
}

