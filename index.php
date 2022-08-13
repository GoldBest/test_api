<?php

if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '/') {
    
    $query = explode('/',$_SERVER['REQUEST_URI'])[2]; // разбиваем строку запроса на "функцию" и "запрос" и берем "запрос"

    if (strripos($_SERVER['REQUEST_URI'], '/s/') !== false) { // страница для функции поиска количества повторений
        echo search($query);
    } elseif (strripos($_SERVER['REQUEST_URI'], '/p/') !== false) { // страница для проверки на палиндром
        echo palindrome($query);
    } else {
        echo 'Неверный запрос!'; // типа 404
    }
} else {
    echo 'Hello!'; // страница на которой нет функций
}

// вспомогательная функция для работы с русским языком
function mb_strrev(string $string, string $encoding = null): string {
    $chars = mb_str_split($string, 1, $encoding ?: mb_internal_encoding());
    return implode('', array_reverse($chars));
}

function search($str) {
    if ($str == '') return 'Строка пустая!';
    $str = urldecode($str);
    $chars = mb_str_split(str_replace(' ','',(mb_strtolower($str)))); // пробелы убрал из условия, хотя это тоже символ
    if (count($chars) == 1) return 'Строка должна содержать минимум два символа!'; // с одним символом и смысла нет в задаче
    $arr = [];
    foreach ($chars as $char) {
        $arr[$char][] = 1; // собираем массив где ключи это буквы, а значения сколько раз они встречаются
    }
    arsort($arr); // сортируем массив по убыванию
    $keys = array_keys($arr); // получаем массив из ключей, то есть название буквы

    if (!isset($keys[1])) return 'Строка должна содержать разные символы!'; // еще проверка

    if (isset($keys[2])) { // если в строке есть больше двух повторяющихся символов
        $max = 0;
        $next_k = $keys[0];
        foreach ($arr as $k => $v) { // находим второй по встречаемости ключ
            if (count($v) < $max) {
                $next_k = $k;
                break;
            }
            $max = count($v);
        }
    } else {
        $next_k = $keys[1];
    }
    
    if (count($keys) != 1 && count($arr[$keys[0]]) != count($arr[$next_k])) { // есть условия которые не были учтены в задании, поэтому обошел их так
        return 'Символ "' . $next_k.'" второй по встречаемости ('.count($arr[$next_k]).'), первый - "'.$keys[0].'" ('.count($arr[$keys[0]]).')';
    } else {
        return 'Символы "' . implode(', ', $keys) .'" встречаются одинаковое количество раз - '.count($arr[$keys[0]]);
    }
    
    // ps У данной функции есть ньюанс, она берет только один вариант, поясню: если будет строка например: ааабббвввгггг, то здесь первое место это 4 буквы Г,
    // а второе место занимает буква А, буква Б и буква В - потому что их по три, то есть одинаковое количество, тут играет роль в каком порядке буквы были в строке 
}

function palindrome($str) {
    if ($str == '') return 'Строка пустая!';
    $str = urldecode($str);
    $new_str = str_replace(' ','',str_replace(',','',mb_strtolower($str))); // убираем все лишнее
    if ($new_str === mb_strrev($new_str)) { // переворачиваем строку и сравниванием ее
        return 'Строка "' . $str . '" является палиндромом.';
    } else {
        return 'Строка "' . $str . '" не является палиндромом.';
    }  
}