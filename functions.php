<?php

include ("array.php");

// Функция getPartsFromFullname, принимающая как аргумент одну строку — склеенное ФИО и возвращающая как результат
// массив из трёх элементов с ключами ‘surname’, ‘name’ и ‘patronymic’.

// "Достанем" из массива example_persons_array строку с ФИО и передадим её значение
// переменной fullNameString:

  $i = 0; // здесь задаётся номер элемента массива example_persons_array
  $fullNameString = $example_persons_array[$i]['fullname'];

// Зададим её в качестве аргумента нашей функции getPartsFromFullname и вернём результат в виде массива:

  function getPartsFromFullname ($fullNameString){
    $fullNameDivision = explode(' ', $fullNameString);
    $surname = $fullNameDivision [0];
    $name = $fullNameDivision [1];
    $patronymic = $fullNameDivision [2];
    $result = ['surname' => $surname, 'name' => $name, 'patronymic' => $patronymic];
    return $result;
  };

//----------------------------------------------------------------------------------------------------------------------------

// Функция getFullnameFromParts, принимающая как аргумент три строки — фамилию, имя и отчество и возвращающая
// как результат их же, но склеенные через пробел.
// Присвоим результат функции getPartsFromFullname (массив из 3-х строк) переменной array и передадим её в качестве аргумента
// нашей функции getFullnameFromParts:

  $array = getPartsFromFullname ($fullNameString);

  function getFullnameFromParts ($array) {
    $result = $array['surname'] . ' ' . $array['name'] . ' ' . $array['patronymic'];
    return $result;
  };

  //----------------------------------------------------------------------------------------------------------------------------

  // Функция getShortName, принимающая как аргумент строку, содержащую ФИО вида «Иванов Иван Иванович» и возвращающую строку
  // вида «Иван И.», где сокращается фамилия и отбрасывается отчество. Для разбиения строки на составляющие используется функцию
  // getPartsFromFullname (переменная $array).

  function getShortName ($array) {
    $surname = $array['surname'];
    $surnameLength = mb_strlen($surname);
    $firstSurnameLetter = mb_substr($surname, 0, -$surnameLength + 1);
    $result = $array['name'] . ' ' . $firstSurnameLetter . '.';
    return $result;
  };

  //----------------------------------------------------------------------------------------------------------------------------

  // Функция getGenderFromName, принимающая как аргумент строку, содержащую ФИО, и определяющая пол человека из массива
  
  function getGenderFromName ($fullNameString) {
    $defaultGender = $genderValue = 0;
    $fullNameDivision = getPartsFromFullname ($fullNameString);
    
    $surname = $fullNameDivision ['surname'];
    $name = $fullNameDivision ['name'];
    $patronymic = $fullNameDivision ['patronymic'];
    
    $surnameLength = mb_strlen($surname);
    $nameLength = mb_strlen($name);
    $patronymicLength = mb_strlen($patronymic);

    // Прописываем признаки пола по окончаниям ФИО:

    $attrSurnameEnd1 = mb_substr($surname, $surnameLength - 1);
    $attrSurnameEnd2 = mb_substr($surname, $surnameLength - 2);
    $attrNameEnd1 = mb_substr($name, $nameLength - 1);
    $attrPatronymicEnd2 = mb_substr($patronymic, $patronymicLength - 2);
    $attrPatronymicEnd3 = mb_substr($patronymic, $patronymicLength - 3);
    
    // Напишем код, возвращающий 1, если сумма признаков мужского пола > 0, возвращающий -1, если сумма
    // признаков женского пола < 0, и возвращающий 0, если сумма признаков = 0 (пол не определён)

    if ($attrPatronymicEnd3 == 'вна') {$genderValue = $defaultGender - 1;}; // в переменной $genderValue накапливается сумма признаков пола
    if ($attrSurnameEnd2 == 'ва') {$genderValue -= 1;};
    if ($attrNameEnd1 == 'а') {$genderValue -= 1;};
    if ($attrPatronymicEnd2 == 'ич') {$genderValue += 1;};
    if ($attrSurnameEnd1 == 'в') {$genderValue += 1;};
    if (($attrNameEnd1 == 'н') || ($attrNameEnd1 == 'й')) {$genderValue += 1;};

    if ($genderValue < 0) {$result = -1;}; // возвращаем -1, если сумма признаков < 0 (женский пол)
    if ($genderValue > 0) {$result = 1;}; // возвращаем 1, если сумма признаков > 0 (мужской пол)
    if ($genderValue == 0) {$result = 0;}; // возвращаем 0, если сумма признаков = 0 (неопределённый пол)

    return $result;
  };

  //----------------------------------------------------------------------------------------------------------------------------

  // Функция getGenderDescription по определению полового состава аудитории.

  function getGenderDescription ($example_persons_array) {
    $count = count($example_persons_array); // кол-во элементов массива
    $maleVal = 0; // кол-во мужчин
    $femaleVal = 0; // кол-во женщин
    $undefVal = 0; // кол-во неопредлённого пола
    echo ("Гендерный состав аудитории:" . "\n");
    echo ("----------------------------" . "\n");
    foreach ($example_persons_array as $key => $value) {
      $key = $value['fullname'];
      $genderValue = getGenderFromName ($key);
      if($genderValue == 1) {
        $maleVal +=1;        
      };
      if($genderValue == -1) {
        $femaleVal +=1;        
      };
      if($genderValue == 0) {
        $undefVal +=1;
      };
    };
    echo ("Мужчины - ") . (round($maleVal * 100 / $count, 1)) . "%" . "\n";
    echo ("Женщины - ") . (round($femaleVal * 100 / $count, 1)) . "%" . "\n";
    echo ("Не определено - ") . (round($undefVal * 100 / $count, 1)) . "%";
  };


  //----------------------------------------------------------------------------------------------------------------------------
  echo ("=========================================================================" . "\n");
  print_r ("Разбиение ФИО: \n") . print_r (getPartsFromFullname ($fullNameString));
  echo ("=========================================================================" . "\n");
  print_r ("Объединение ФИО: \n") . print_r (getFullnameFromParts ($array)) . print_r ("\n");
  echo ("=========================================================================" . "\n");
  print_r ("Сокращение ФИО: \n") . print_r (getShortName ($array)) . print_r ("\n");
  echo ("=========================================================================" . "\n");
  print_r ("Гендерный признак: '1' - мужчина, '-1' - женщина, '0' - пол не определён: \n")
  . print_r (getGenderFromName ($fullNameString)) . print_r ("\n");
  echo ("=========================================================================" . "\n");
  getGenderDescription ($example_persons_array);
?>