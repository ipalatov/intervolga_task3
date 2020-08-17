<?php

/**
 * Данная реализация тестовой задачи позволяет "закрасить" наибольший по площади прямоугольник сгенерированного 
 * поля с некоторыми занятыми ячейками.
 * Принцип следующий:
 * - находим координаты всех нулей и записываем в массив
 * - находим крайнюю правую координату Y строки нулей для каждого нуля
 * - аналогично находим крайнюю нижнюю координату X столбца для каждого нуля
 * - находим все прямоугольники по следующему принципу: проходя по циклу всей строки Y делаем внутри цикл 
 * вниз по X для вычисления условной высоты прямоугольника. Критерием нового прямоугольника является 
 * уменьшение его высоты на следующем цикле Y или конец всех циклов Y при неизменной высоте. 
 * Также в конце добавляем массив со строками найденый ранее, т.к. строки - тоже прямоугольники.
 * - у массива со всеми прямоугольниками вычисляем площади каждого, находим максимум и выдаем координаты 
 * для "закрашивания"
 * @author Ivan Palatov 
 *  * 
 */

// $array = [
//     [1, 0, 0, 1, 0],
//     [1, 0, 0, 0, 1],
//     [1, 0, 0, 0, 0],
//     [1, 0, 0, 0, 0],
//     [1, 1, 1, 1, 0],
// ];



$array = generateArray(10, 10, 25);
echo 'Начальная поле';
printArray($array);

$X1Y1 = searchX1Y1($array);
// echo 'Массив с координатами всех нулей';
// printArray($X1Y1);

$X1Yn = searchRowLength($array, $X1Y1);
// echo 'Массив со строками нулей (координаты всех нулей  и координатой Y)';
// printArray($X1Yn);

$XnYn = searchColHeight($array, $X1Yn);
// echo 'Массив с координатами всех нулей и крайними координатами X  Y';
// printArray($XnYn);

$RectXY = searchRectangles($array, $XnYn, $X1Yn);
// echo 'Массив с координатами всех прямоугольников';
// printArray($RectXY);

$maxAreaRectangle = getMaxAreaRectangle($RectXY);
// echo 'Координаты самого большого по площади прямоугольника ';
// echo implode(' ', $maxAreaRectangle);

$pArray = paintRectArea($array, $maxAreaRectangle);
echo 'Поле с закрашенным "2" наибольшим по площади прямоуольником';
printArray($pArray);



// дан двумерный массив
// $array = [
//     [1, 0, 0, 1],
//     [0, 0, 0, 0],
// ];

$array = [
    [1, 0, 0, 0],
    [0, 0, 0, 1],
    [0, 0, 0, 0],
    [0, 1, 0, 0],
];

/**
 * Генерирует поле для морского боя размером n*m. Стреляли/не стреляли задается как "1"/"0". 
 * По умолчанию "0", у случайных позиций "1".
 * @param int $n Число строк не более 20, не менее 3
 * @param int $m Число столбцов не более 20, не менее 3
 * @param int $shotCount Число выстрелов
 * @return int[][] Двумерный массив - начальное поле
 */
function generateArray(int $n = 5, int $m = 5, int $shotCount = 5)
{
    if ($n < 3) $n = 3;
    if ($n > 20) $n = 20;
    if ($m < 3) $m = 3;
    if ($m > 20) $m = 20;

    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $m; $j++) {
            $array[$i][$j] = 0;
        }
    }

    for ($k = 0; $k < $shotCount; $k++) {
        $shootPos[] = [rand(0, $n - 1), rand(0, $m - 1)];
    }

    foreach ($shootPos as $key => $shot) {
        $x = $shot[0];
        $y = $shot[1];
        $array[$x][$y] = 1;
    }
    return $array;
}
/**
 * Выводит на экран двумерный массив
 * @param mixed[][] $array Двумерный массив
 * @return void
 */
function printArray($array)
{
    echo '<pre>';
    foreach ($array as $row) {
        echo "<br>";
        foreach ($row as $item) echo ' ' . $item;
    }
    echo '</pre>';
}

/**
 * Поиск координат всех нулей - верхних левых углов будущих прямоугольников
 * @param int[][] $array Двумерный начальный массив
 * @return int[][] Двумерный массив с координатами всех нулей
 */
function searchX1Y1(array $array)
{
    foreach ($array as $i => $row) {
        foreach ($row as $j => $item) {
            if ($item == 1) {
                continue;
            } else {
                $X1Y1[] = [$i, $j];
            }
        }
    }
    return $X1Y1;
}
/**
 * Поиск строки нулей (координаты , до которой продолжается строка нулей) для каждой точки.
 * @param int[][] $array Двумерный начальный массив
 * @param int[][] $X1Y1 Двумерный массив с координатами всех нулей
 * @return int[][] Двумерный массив с координатами начала и конца строки нулей
 */
function searchRowLength($array, $X1Y1)
{
    $X1Yn = $X1Y1;
    foreach ($X1Yn as $i => $point) {
        $x1 = $point[0];
        $y1 = $point[1];
        $X1Yn[$i][2] = $x1;
        $X1Yn[$i][3] = $y1;
        for ($yIter = $y1 + 1; $yIter < count($array[0]); $yIter++) {
            if ($array[$x1][$yIter] == 0) {
                $X1Yn[$i][3] = $yIter;
            } else {
                break;
            }
        }
    }
    return $X1Yn;
}
/**
 * Поиск столбца нулей (координаты X , до которой продолжается столбец нулей) для каждой точки
 * @param int[][] $array Двумерный начальный массив
 * @param int[][] $X1Yn Двумерный массив с координатами начала и конца строки нулей
 * @return int[][] Двумерный массив с координатами начала и конца строк нулей, и столбцов нулей
 */
function searchColHeight($array, $X1Yn)
{
    $XnYn = $X1Yn;
    foreach ($X1Yn as $i => $point) {
        $x1 = $point[0];
        $y1 = $point[1];
        $xN = $point[2];
        $yN = $point[3];
        $x = $x1;
        if ($x1 < count($array) - 1) {
            for ($xIter = $x1 + 1; $xIter < count($array); $xIter++) {
                if ($array[$xIter][$y1] == 0) {
                    $XnYn[$i][2] = $xIter;
                } else {
                    break;
                }
            }
        }
    }
    return $XnYn;
}

/**
 * Находит все прямоугольники относительно каждой точки "0".
 * @param int[][] $array Двумерный начальный массив
 * @param int[][] $XnYn Двумерный массив с координатами начала и конца строк нулей, и столбцов нулей
 * @param int[][] $X1Yn Двумерный массив с координатами начала и конца строки нулей 
 * @return int[][] Массив с координатами всех прямоугольников
 */
function searchRectangles($array, $XnYn, $X1Yn)
{
    foreach ($XnYn as $i => $point) {
        $x1 = $point[0];
        $y1 = $point[1];
        $xN = $point[2];
        $yN = $point[3];
        if ($x1 < count($array) - 1) {
            $HH = $xN; // начальная высота для цикла по Y
            for ($yPos = $y1; $yPos <= $yN; $yPos++) {
                for ($xPos = $x1; $xPos <= $HH; $xPos++) { // цикл по X до заданной высоты

                    // при встрече в массиве "1" записываем прямоугольник текущей высоты и предыдущей координаты Y
                    // и выходим из цикла X
                    if ($array[$xPos][$yPos] == 1) {
                        $y = $yPos - 1;
                        $RectXY[] = [$x1, $y1, $HH, $y];
                        break;
                    }
                }
                // при выходые из цикла уменьшаем текущую высоту до клетки, лежащей над единицей
                $HH = $xPos - 1;
                // однако если это последняя интерация Y и высота не поменялась, записываем еще один прямоугольник
                if ($xPos = $HH && $yPos == $yN) {
                    $RectXY[] = [$x1, $y1, $HH, $yPos];
                }
            }
        }
    }
    $RectXY = array_merge($RectXY, $X1Yn);

    return $RectXY;
}

/**
 * Находит прямоугольник с наибольшей площадью и выдает его координаты
 * @param int[][] $RectXY Массив с координатами прямоугольников
 * @return int[] Одномерный массив с координатами прямогульника с наибольшей площадью
 */
function getMaxAreaRectangle($RectXY)
{
    $maxArea = 0;
    foreach ($RectXY as $key => $rect) {
        $l = $rect[3] - $rect[1] + 1;
        $h = $rect[2] - $rect[0] + 1;
        $area = $l * $h;

        if ($area > $maxArea) {
            $maxArea = $area;
            $x1 = $rect[0];
            $y1 = $rect[1];
            $xN = $rect[2];
            $yN = $rect[3];
        }
    }
    echo '<pre>';
    echo ' Максимальная площадь=' . $maxArea . '<br>';
    echo '<pre>';

    $maxAreaRectangle = [$x1, $y1, $xN, $yN];

    return $maxAreaRectangle;
}

/**
 * "Закрашивает" в выбранном массиве нужный прямоугольник
 * @param int[][] $array Двумерный начальный массив
 * @param int[] $rect Одномерный массив с координатами прямогульника, который необходимо "закрасить"
 * @return int[][] Двумерный "закрашенный" массив
 *  */
function paintRectArea($array, $rect)
{
    $pArray = $array;
    $x1 = $rect[0];
    $y1 = $rect[1];
    $xN = $rect[2];
    $yN = $rect[3];
    foreach ($pArray as $keyX => $row) {
        if ($keyX >= $x1 && $keyX <= $xN) {
            foreach ($row as $keyY => $item) {
                if ($keyY >= $y1 && $keyY <= $yN) {
                    $pArray[$keyX][$keyY] = 2;
                }
            }
        }
    }
    return $pArray;
}
