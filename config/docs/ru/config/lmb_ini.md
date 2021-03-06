# lmbIni
lmbIni — класс, который позволяет получать конфигурационные данные из ini-файлов, схожим с parse_ini_file() образом, с некоторыми более продвинутыми возможностями.

## Пример использования:
Допустим, у нас есть файл my.ini следующего содержания:

    some_property = 1

    [group1]
    test = 'bla-bla'

    #это комментарий

    [group2]
     value[apple] = something
     value[banana] = whatever # и это тоже

    [group3]
     value[] = 1
     value[] = 2

Данные этого ini файла можно получить следующим образом:

    $ini = new lmbIni('my.ini');
    echo $ini->getOption('some_property'); // выведет 1
    echo $ini->getOption('test', 'group1'); // выведет bla-bla
    $ini->getGroup('group2'); // получим массив array('value' => array('apple' => 'something', 'banana' => 'whatever'))
    $ini->getGroup('group3'); // получим массив array('value' => array(1, 2))

Кроме этого, класс lmbIni является наследником от lmbSet, поэтому он реализует и более общий интерфейс(для того же файла):

    $ini = new lmbIni('my.ini');
    echo $ini->get('some_property'); // выведет 1
    $ini->get('group2'); // получим массив array('value' => array('apple' => 'something', 'banana' => 'whatever'))
    $ini->get('group3'); // получим массив array('value' => array(1, 2))

## override файлы
Класс lmbIni также ищет так называемые override файлы, которые позволяют перекрывать базовые свойства, определенные в оригинальных файлах. override файл ищется lmbIni там же, где и оригинальный файл. Имя override файла формируется след. образом: до расширения .ini вставляется суффикс .override, например:

    $original_file = '/path/to/file/my.ini';
    $override_file = '/path/to/file/my.override.ini';
