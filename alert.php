<?

define('WIDTH', 800);
define('MAX_WIDTH', 700);
define('HEIGHT', 100);
define('FONT_NAME', '/usr/share/nginx/html/tovaryplus.new/tovaryplus.ru/public/css/fonts/MLC.ttf');

if (isset($_REQUEST['debug'])) {
    include_once '/var/www/sites/tovaryplus.ru/src/Classes/SmsServiceApi.php';

    $api_params = array(
        'pid' => 20626,
        'sender' => 'IC_Kegeles',
        'to' => '79109632490',
        'text' => 'Testing'
    );
    try {
        $sender = new \App\Classes\SmsServiceApi(19075, 'ypuebqazt3');
        $result = $sender->send('delivery.sendSms', $api_params);
        var_dump($result);
    } catch (Exception $e) {
        var_dump($e);
    }
    exit();
}

$text = '...';
$allowed_groups = array(1, 2, 3, 4, 5, 6, 7, 8);
$group = 7;
if (isset($_GET['group']) and ( intval($_GET['group']) > 0 and in_array(intval($_GET['group']), $allowed_groups))) {
    $group = intval($_GET['group']);
}

if (isset($_GET['text']) and strlen(trim($_GET['text']))) {
    $text = $_GET['text'];
}

switch ($group) {
    case 1:
        define('FONT_SIZE', 45);
        define('KEG', 9);
        break;
    case 5:
    case 8:
        define('FONT_SIZE', 60);
        define('KEG', 12);
        break;
    default:
        define('FONT_SIZE', 40);
        define('KEG', 8);
}

function utf8_str_split($str) {
    $split = 1;
    $array = array();
    for ($i = 0; $i < strlen($str);) {
        $value = ord($str[$i]);
        if ($value > 127) {
            if ($value >= 192 && $value <= 223)
                $split = 2;
            elseif ($value >= 224 && $value <= 239)
                $split = 3;
            elseif ($value >= 240 && $value <= 247)
                $split = 4;
        }else {
            $split = 1;
        }
        $key = NULL;
        for ($j = 0; $j < $split; $j++, $i++) {
            $key .= $str[$i];
        }
        array_push($array, $key);
    }
    return $array;
}

function win2uni($s) {
    $s = convert_cyr_string($s, 'w', 'i'); // преобразование win1251 -> iso8859-5
    // преобразование iso8859-5 -> unicode:
    for ($result = '', $i = 0; $i < strlen($s); $i++) {
        $charcode = ord($s[$i]);
        $result .= ($charcode > 175) ? "&#" . (1040 + ($charcode - 176)) . ";" : $s[$i];
    }
    return $result;
}

function GetBlockSize($text, $font_name, $font_size, $keg = 1) {
    $coord = imagettfbbox(
            $font_size, // размер шрифта
            0, // угол наклона шрифта (0 = не наклоняем)
            $font_name, // имя шрифта, а если точнее, ttf-файла
            $text  // собственно, текст
    );

    return ['width' => $keg == 1 ? $coord[2] - $coord[0] : strlen(utf8_decode($text)) * $keg, 'height' => $coord[1] - $coord[7]];
}

function StringToLines($text, $max_width, $font_name, $font_size, $keg = 1) {
    $boxSize = GetBlockSize($text, $font_name, $font_size, $keg); //размер блока с фикс шириной символов

    $lines = array();
    $lines['count'] = ceil($boxSize['width'] / $max_width); //количество строк, по ширине не превышающих $max_width
    $lines['totalheight'] = 0;

    $words = preg_split('~[ ]~', $text); //количество слов в тексте
    //$wordsInLine = floor(count($words) / $lines['count']); //среднее количество слов в строке

    $tmpLine = '';
    $line = 0;
    for ($l = 0; $l < count($words); $l++) {
        $tmpLine .= $words[$l] . ' ';

        $tmpLineSize = GetBlockSize($tmpLine, $font_name, $font_size, $keg);

        if ($tmpLineSize['width'] > $max_width) {
            $lines['text'][$line]['string'] = $tmpLine;
            $lines['text'][$line]['size'] = $tmpLineSize;
            $tmpLine = '';
            $line++;
        } else if (count($words) == $l + 1) {
            $lines['text'][$line]['string'] = $tmpLine;
            $lines['text'][$line]['size'] = $tmpLineSize;
            $tmpLine = '';
        }
    }
    foreach ($lines['text'] as $ln) {
        $lines['totalheight'] += $ln['size']['height'];
        $lines['count'] ++;
    }
    return $lines;
}

//$text = 'Не определен';
//$text = 'Выполняются работы, не требующие допусков СРО, если иное не указано при размещении рекламы';
//$text = 'Имеются противопоказания к применению и использованию, необходимо ознакомиться с инструкцией по применению';
//$text = 'Имеются противопоказания к применению и использованию, необходимо  получить консультацию специалиста';
//$text = 'Курение вредит вашему здоровью';
//$text = 'Чрезмерное употребление алкоголя вредит вашему здоровью';
//$text = 'Чрезмерное употребление пива вредит вашему здоровью';


$textsize = GetBlockSize($text, FONT_NAME, FONT_SIZE, KEG);
$shortstring = ($textsize['width']) > MAX_WIDTH ? false : true;

if ($shortstring) {
    $blockSize = GetBlockSize($text, FONT_NAME, FONT_SIZE, KEG);

    $X = ((WIDTH - $blockSize['width']) / 2);
    $Y = ((HEIGHT + $blockSize['height']) / 2) - 2;

    $image = imagecreatetruecolor(WIDTH, HEIGHT) or die('Cannot create image');
    $black = imagecolorallocate($image, 0, 0, 0);
    $gray = imagecolorallocate($image, 53, 53, 53);
    $white = imagecolorallocate($image, 255, 255, 255);
    //imagefill($image, 0, 0, $gray);
    imagecolortransparent($image, $gray);

    $utftext = utf8_str_split($text);
    for ($i = 0; $i < count($utftext); $i++) {
        imagettftext(
                $image, FONT_SIZE, 0, $X, $Y, $white, FONT_NAME, $utftext[$i]
        );
        $X += KEG;
    }
} else {
    $lines = StringToLines($text, MAX_WIDTH, FONT_NAME, FONT_SIZE, KEG);

    $image = imagecreatetruecolor(WIDTH, $lines['totalheight'] + 2 * $lines['count'] > HEIGHT ? $lines['totalheight'] + 2 * $lines['count'] : HEIGHT) or die('Cannot create image');
    $black = imagecolorallocate($image, 0, 0, 0);
    $gray = imagecolorallocate($image, 53, 53, 53);
    $white = imagecolorallocate($image, 255, 255, 255);
    //imagefill($image, 0, 0, $gray);
    imagecolortransparent($image, $gray);
    $height = 0;
    foreach ($lines['text'] as $k => $line) {
        $blockSize = $line['size'];
        $height += $blockSize['height'] + 3;

        $X = (WIDTH / 2) - ($blockSize['width'] / 2);
        $Y = $height;
        $utftext = utf8_str_split($line['string']);
        for ($i = 0; $i < count($utftext); $i++) {
            imagettftext(
                    $image, FONT_SIZE, 0, $X, $Y, $white, FONT_NAME, $utftext[$i]
            );
            $X += KEG;
        }
    }
}

/*  imagettftext(
  $image,
  9,
  0,
  80,
  43,
  $black,
  'Arial',
  "x=" . (($blockSize['width'] / 2))
  );
 */
/* $x = 0;
  for ($j = 0; $j < 100; $j++) {
  imagettftext(
  $image,
  5,
  0,
  $x,
  40,
  $black,
  'Arial',
  '|'
  );
  $x = $x + 7.7;
  } */
header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
