<?php

class IMEmap
{
  var $dcmap = array("kk"=>"っk",
        "tt"=>"っt",
        "cc"=>"っc",
        "ss"=>"っs",
        "pp"=>"っp",
        "mm"=>"んm",
        "mt"=>"んt",
        "mb"=>"んb",
        "mp"=>"んp",  // no one said a "double consonant" had to be the same one =)
        "nt"=>"んt",
        "nb"=>"んb",
        "np"=>"んp");

  var $map;

  // unicode range  3040-309F
  var $hira = array("ぁ","あ","ぃ","い","ぅ","う","ぇ","え","ぉ","お",
        "か","が","き","ぎ","く","ぐ","け","げ","こ","ご",
        "さ","ざ","し","じ","す","ず","せ","ぜ","そ","ぞ",
        "た","だ","ち","ぢ","っ","つ","づ","て","で","と","ど",
        "な","に","ぬ","ね","の",
        "は","ば","ぱ","ひ","び","ぴ","ふ","ぶ","ぷ","へ","べ","ぺ","ほ","ぼ","ぽ",
        "ま","み","む","め","も",
        "ゃ","や","ゅ","ゆ","ょ","よ",
        "ら","り","る","れ","ろ",
        "ゎ","わ","ゐ","ゑ","を",
        "ん","ゔ","ゕ","ゖ",
        "わ゛","ゐ゛","ゑ゛","を゛");

  // unicode range 30A0-30FF
  var $kata = array("ァ","ア","ィ","イ","ゥ","ウ","ェ","エ","ォ","オ",
        "カ","ガ","キ","ギ","ク","グ","ケ","ゲ","コ","ゴ",
        "サ","ザ","シ","ジ","ス","ズ","セ","ゼ","ソ","ゾ",
        "タ","ダ","チ","ヂ","ッ","ツ","ヅ","テ","デ","ト","ド",
        "ナ","ニ","ヌ","ネ","ノ",
        "ハ","バ","パ","ヒ","ビ","ピ","フ","ブ","プ","ヘ","ベ","ペ","ホ","ボ","ポ",
        "マ","ミ","ム","メ","モ",
        "ャ","ヤ","ュ","ユ","ョ","ヨ",
        "ラ","リ","ル","レ","ロ",
        "ヮ","ワ","ヰ","ヱ","ヲ",
        "ン","ヴ","ヵ","ヶ",
        "ヷ","ヸ","ヹ","ヺ");

  function __construct()
  {
    $this->map = array();

    $this->addMapping('a', 'あ');
    $this->addMapping('i', 'い');
    $this->addMapping('u', 'う');
    $this->addMapping('e', 'え');
    $this->addMapping('o', 'お');

    $this->addMapping('yi', 'い');
    $this->addMapping('wu', 'う');
    $this->addMapping('whu', 'う');

    $this->addMapping('la', 'ぁ');
    $this->addMapping('li', 'ぃ');
    $this->addMapping('lu', 'ぅ');
    $this->addMapping('le', 'ぇ');
    $this->addMapping('lo', 'ぉ');

    $this->addMapping('xa', 'ぁ');
    $this->addMapping('xi', 'ぃ');
    $this->addMapping('xu', 'ぅ');
    $this->addMapping('xe', 'ぇ');
    $this->addMapping('xo', 'ぉ');

    $this->addMapping('lyi', 'ぃ');
    $this->addMapping('xyi', 'ぃ');
    $this->addMapping('lye', 'ぇ');
    $this->addMapping('xye', 'ぇ');
    $this->addMapping('ye', 'いぇ');

    $this->addMapping('wi', 'うぃ');
    $this->addMapping('we', 'うぇ');

    $this->addMapping('wha', 'うぁ');
    $this->addMapping('whi', 'うぃ');
    $this->addMapping('whe', 'うぇ');
    $this->addMapping('who', 'うぉ');

    $this->addMapping('vu', 'ヴ');
    $this->addMapping('va', 'ヴぁ');
    $this->addMapping('vi', 'ヴぃ');
    $this->addMapping('vyi', 'ヴぃ');
    $this->addMapping('ve', 'ヴぇ');
    $this->addMapping('vye', 'ヴぇ');
    $this->addMapping('vo', 'ヴぉ');
    $this->addMapping('vya', 'ヴゃ');
    $this->addMapping('vyu', 'ヴゅ');
    $this->addMapping('vyo', 'ヴょ');

    $this->addMapping('ka', 'か');
    $this->addMapping('ki', 'き');
    $this->addMapping('ku', 'く');
    $this->addMapping('ke', 'け');
    $this->addMapping('ko', 'こ');

    $this->addMapping('ca', 'か');
    $this->addMapping('cu', 'く');
    $this->addMapping('co', 'こ');
    $this->addMapping('qu', 'く');

    $this->addMapping('kya', 'きゃ');
    $this->addMapping('kyi', 'きぃ');
    $this->addMapping('kyu', 'きゅ');
    $this->addMapping('kye', 'きぇ');
    $this->addMapping('kyo', 'きょ');

    $this->addMapping('qya', 'くゃ');
    $this->addMapping('qyu', 'くゅ');
    $this->addMapping('qyo', 'くょ');

    $this->addMapping('lka', 'ヵ');
    $this->addMapping('xka', 'ヵ');
    $this->addMapping('lke', 'ヶ');
    $this->addMapping('xke', 'ヶ');

    $this->addMapping('qwa', 'くぁ');
    $this->addMapping('qwi', 'くぃ');
    $this->addMapping('qwu', 'くぅ');
    $this->addMapping('qwe', 'くぇ');
    $this->addMapping('qwo', 'くぉ');

    $this->addMapping('qa', 'くぁ');
    $this->addMapping('qi', 'くぃ');
    $this->addMapping('qe', 'くぇ');
    $this->addMapping('qo', 'くぉ');

    $this->addMapping('kwa', 'くぁ');
    $this->addMapping('qyi', 'くぃ');
    $this->addMapping('qye', 'くぇ');

    $this->addMapping('ga', 'が');
    $this->addMapping('gi', 'ぎ');
    $this->addMapping('gu', 'ぐ');
    $this->addMapping('ge', 'げ');
    $this->addMapping('go', 'ご');

    $this->addMapping('gya', 'ぎゃ');
    $this->addMapping('gyi', 'ぎぃ');
    $this->addMapping('gyu', 'ぎゅ');
    $this->addMapping('gye', 'ぎぇ');
    $this->addMapping('gyo', 'ぎょ');

    $this->addMapping('gwa', 'ぐぁ');
    $this->addMapping('gwi', 'ぐぃ');
    $this->addMapping('gwu', 'ぐぅ');
    $this->addMapping('gwe', 'ぐぇ');
    $this->addMapping('gwo', 'ぐぉ');

    $this->addMapping('shi', 'し');

    $this->addMapping('sa', 'さ');
    $this->addMapping('si', 'し');
    $this->addMapping('su', 'す');
    $this->addMapping('se', 'せ');
    $this->addMapping('so', 'そ');

    $this->addMapping('ci', 'し');
    $this->addMapping('ce', 'せ');

    $this->addMapping('sha', 'しゃ');
    $this->addMapping('shu', 'しゅ');
    $this->addMapping('she', 'しぇ');
    $this->addMapping('sho', 'しょ');

    $this->addMapping('sya', 'しゃ');
    $this->addMapping('syi', 'しぃ');
    $this->addMapping('syu', 'しゅ');
    $this->addMapping('sye', 'しぇ');
    $this->addMapping('syo', 'しょ');

    $this->addMapping('swa', 'すぁ');
    $this->addMapping('swi', 'すぃ');
    $this->addMapping('swu', 'すぅ');
    $this->addMapping('swe', 'すぇ');
    $this->addMapping('swo', 'すぉ');

    $this->addMapping('ji', 'じ');

    $this->addMapping('za', 'ざ');
    $this->addMapping('zi', 'じ');
    $this->addMapping('zu', 'ず');
    $this->addMapping('ze', 'ぜ');
    $this->addMapping('zo', 'ぞ');

    $this->addMapping('ja', 'じゃ');
    $this->addMapping('ju', 'じゅ');
    $this->addMapping('je', 'じぇ');
    $this->addMapping('jo', 'じょ');

    $this->addMapping('jya', 'じゃ');
    $this->addMapping('jyi', 'じぃ');
    $this->addMapping('jyu', 'じゅ');
    $this->addMapping('jye', 'じぇ');
    $this->addMapping('jyo', 'じょ');

    $this->addMapping('zya', 'じゃ');
    $this->addMapping('zyi', 'じぃ');
    $this->addMapping('zyu', 'じゅ');
    $this->addMapping('zye', 'じぇ');
    $this->addMapping('zyo', 'じょ');

    $this->addMapping('chi', 'ち');
    $this->addMapping('tsu', 'つ');

    $this->addMapping('ta', 'た');
    $this->addMapping('ti', 'ち');
    $this->addMapping('tu', 'つ');
    $this->addMapping('te', 'て');
    $this->addMapping('to', 'と');

    $this->addMapping('cha', 'ちゃ');
    $this->addMapping('chu', 'ちゅ');
    $this->addMapping('che', 'ちぇ');
    $this->addMapping('cho', 'ちょ');

    $this->addMapping('tya', 'ちゃ');
    $this->addMapping('tyi', 'ちぃ');
    $this->addMapping('tyu', 'ちゅ');
    $this->addMapping('tye', 'ちぇ');
    $this->addMapping('tyo', 'ちょ');

    $this->addMapping('cya', 'ちゃ');
    $this->addMapping('cyi', 'ちぃ');
    $this->addMapping('cyu', 'ちゅ');
    $this->addMapping('cye', 'ちぇ');
    $this->addMapping('cyo', 'ちょ');

    $this->addMapping('ltu', 'っ');
    $this->addMapping('xtu', 'っ');
    $this->addMapping('ltsu', 'っ');

    $this->addMapping('tsa', 'つぁ');
    $this->addMapping('tsi', 'つぃ');
    $this->addMapping('tse', 'つぇ');
    $this->addMapping('tso', 'つぉ');

    $this->addMapping('tha', 'てゃ');
    $this->addMapping('thi', 'てぃ');
    $this->addMapping('thu', 'てゅ');
    $this->addMapping('the', 'てぇ');
    $this->addMapping('tho', 'てょ');

    $this->addMapping('twa', 'とぁ');
    $this->addMapping('twi', 'とぃ');
    $this->addMapping('twu', 'とぅ');
    $this->addMapping('twe', 'とぇ');
    $this->addMapping('two', 'とぉ');

    $this->addMapping('dzu', 'づ');    // deviation from standard - I disagree with this mapping missing so much I refuse to not offer it.
    $this->addMapping('dzi', 'ぢ');    // deviation from standard - I disagree with this mapping missing so much I refuse to not offer it.

    $this->addMapping('da', 'だ');
    $this->addMapping('di', 'ぢ');
    $this->addMapping('du', 'づ');
    $this->addMapping('de', 'で');
    $this->addMapping('do', 'ど');

    $this->addMapping('dya', 'ぢゃ');
    $this->addMapping('dyi', 'ぢぃ');
    $this->addMapping('dyu', 'ぢゅ');
    $this->addMapping('dye', 'ぢぇ');
    $this->addMapping('dyo', 'ぢょ');

    $this->addMapping('dha', 'でゃ');
    $this->addMapping('dhi', 'でぃ');
    $this->addMapping('dhu', 'でゅ');
    $this->addMapping('dhe', 'でぇ');
    $this->addMapping('dho', 'でょ');

    $this->addMapping('dwa', 'どぁ');
    $this->addMapping('dwi', 'どぃ');
    $this->addMapping('dwu', 'どぅ');
    $this->addMapping('dwe', 'どぇ');
    $this->addMapping('dwo', 'どぉ');

    $this->addMapping('na', 'な');
    $this->addMapping('ni', 'に');
    $this->addMapping('nu', 'ぬ');
    $this->addMapping('ne', 'ね');
    $this->addMapping('no', 'の');

    $this->addMapping('nya', 'にゃ');
    $this->addMapping('nyi', 'にぃ');
    $this->addMapping('nyu', 'にゅ');
    $this->addMapping('nye', 'にぇ');
    $this->addMapping('nyo', 'にょ');

    $this->addMapping('fu', 'ふ');

    $this->addMapping('ha', 'は');
    $this->addMapping('hi', 'ひ');
    $this->addMapping('hu', 'ふ');
    $this->addMapping('he', 'へ');
    $this->addMapping('ho', 'ほ');

    $this->addMapping('hya', 'ひゃ');
    $this->addMapping('hyi', 'ひぃ');
    $this->addMapping('hyu', 'ひゅ');
    $this->addMapping('hye', 'ひぇ');
    $this->addMapping('hyo', 'ひょ');

    $this->addMapping('fya', 'ふゃ');
    $this->addMapping('fyi', 'ふぃ');
    $this->addMapping('fyu', 'ふゅ');
    $this->addMapping('fye', 'ふぇ');
    $this->addMapping('fyo', 'ふょ');

    $this->addMapping('fa', 'ふぁ');
    $this->addMapping('fi', 'ふぃ');
    $this->addMapping('fe', 'ふぇ');
    $this->addMapping('fo', 'ふぉ');

    $this->addMapping('ba', 'ば');
    $this->addMapping('bi', 'び');
    $this->addMapping('bu', 'ぶ');
    $this->addMapping('be', 'べ');
    $this->addMapping('bo', 'ぼ');

    $this->addMapping('bya', 'びゃ');
    $this->addMapping('byi', 'びぃ');
    $this->addMapping('byu', 'びゅ');
    $this->addMapping('bye', 'びぇ');
    $this->addMapping('byo', 'びょ');

    $this->addMapping('va', 'ヴぁ');
    $this->addMapping('vi', 'ヴぃ');
    $this->addMapping('vu', 'ヴ');
    $this->addMapping('ve', 'ヴぇ');
    $this->addMapping('vo', 'ヴぉ');

    $this->addMapping('vya', 'ヴゃ');
    $this->addMapping('vyi', 'ヴぃ');
    $this->addMapping('vyu', 'ヴゅ');
    $this->addMapping('vye', 'ヴぇ');
    $this->addMapping('vyo', 'ヴょ');

    $this->addMapping('pa', 'ぱ');
    $this->addMapping('pi', 'ぴ');
    $this->addMapping('pu', 'ぷ');
    $this->addMapping('pe', 'ぺ');
    $this->addMapping('po', 'ぽ');

    $this->addMapping('pya', 'ぴゃ');
    $this->addMapping('pyi', 'ぴぃ');
    $this->addMapping('pyu', 'ぴゅ');
    $this->addMapping('pye', 'ぴぇ');
    $this->addMapping('pyo', 'ぴょ');

    $this->addMapping('ma', 'ま');
    $this->addMapping('mi', 'み');
    $this->addMapping('mu', 'む');
    $this->addMapping('me', 'め');
    $this->addMapping('mo', 'も');

    $this->addMapping('mya', 'みゃ');
    $this->addMapping('myi', 'みぃ');
    $this->addMapping('myu', 'みゅ');
    $this->addMapping('mye', 'みぇ');
    $this->addMapping('myo', 'みょ');

    $this->addMapping('ya', 'や');
    $this->addMapping('yu', 'ゆ');
    $this->addMapping('yo', 'よ');

    $this->addMapping('lya', 'ゃ');
    $this->addMapping('lyu', 'ゅ');
    $this->addMapping('lyo', 'ょ');

    $this->addMapping('xya', 'ゃ');
    $this->addMapping('xyu', 'ゅ');
    $this->addMapping('xyo', 'ょ');

    $this->addMapping('ra', 'ら');
    $this->addMapping('ri', 'り');
    $this->addMapping('ru', 'る');
    $this->addMapping('re', 'れ');
    $this->addMapping('ro', 'ろ');

    $this->addMapping('rya', 'りゃ');
    $this->addMapping('ryi', 'りぃ');
    $this->addMapping('ryu', 'りゅ');
    $this->addMapping('rye', 'りぇ');
    $this->addMapping('ryo', 'りょ');

    $this->addMapping('wa', 'わ');
    $this->addMapping('wyi', 'ゐ'); // deviation from standard
    $this->addMapping('wye', 'ゑ'); // deviation from standard
    $this->addMapping('wo', 'を');

    $this->addMapping('lwa', 'ゎ');
    $this->addMapping('xwa', 'ゎ');

    $this->addMapping('n', 'ん');

    $this->addMapping('nnn', 'んn');  // special parsing for triple n
    $this->addMapping('nn', 'ん');
    $this->addMapping('xn', 'ん');
    $this->addMapping("n'", 'ん');
    $this->addMapping("nʺ", 'ん');  // to counterarct input validation and safifying

    $this->addMapping('-', 'ー');  // long vowel mark

  /*
    $this->addMapping(' ', '　');  // japanese space
    $this->addMapping('[', '「');  // japanese opening quote
    $this->addMapping(']', '」');  // japanese opening quote
    $this->addMapping('*', '＊');  // japanese asterisk
    $this->addMapping('?', '？');  // japanese question mark
    $this->addMapping('.', '。');  // japanese full stop
    $this->addMapping(',', '、');  // japanese comma
  */


  }

  function addMapping($ime, $jp)
  {
    $len = strlen($ime);
    $this->map[$len][$ime] = $jp;
  }

  function convert($input)
  {
    // double vowels
    $kata = str_replace(array("aa","ii","uu","ee","oo"), array("a-","i-","u-","e-","o-"),$input);
    // double consonants
    $kata = str_replace(array_keys($this->dcmap),array_values($this->dcmap),$kata);
    $hira = str_replace(array_keys($this->dcmap),array_values($this->dcmap),$input);
    // step two, successively replace
    for($i=4;$i>0;$i--) {
      if(isset($this->map[$i])) {
        $kata = str_replace(array_keys($this->map[$i]),array_values($this->map[$i]),$kata);
        $hira = str_replace(array_keys($this->map[$i]),array_values($this->map[$i]),$hira); }}
    // do the final katakana conversion step
    $kata = $this->hira_to_kata($kata);
    // if $kata contains any western letters at this point, this wasn't romaji
    if(preg_match("/[\x{0041}-\x{007A}]/u",$kata)>0) { return false; }
    // it was romaji, but the hiragana word contains any katakana, it's not a hira word
    if(preg_match("/[\x{30A0}-\x{30FF}]/u",$hira)>0) { $hira = false; }
    return array("hiragana"=>$hira, "katakana"=>$kata);
  }

  // FIXME: make hira/kata arrays point to the already defined ones?
  function hira_to_kata($hira)
  {
    // do initial replacement
    $hiragana = array("ぁ","あ","ぃ","い","ぅ","う","ぇ","え","ぉ","お",
          "か","が","き","ぎ","く","ぐ","け","げ","こ","ご",
          "さ","ざ","し","じ","す","ず","せ","ぜ","そ","ぞ",
          "た","だ","ち","ぢ","っ","つ","づ","て","で","と","ど",
          "な","に","ぬ","ね","の",
          "は","ば","ぱ","ひ","び","ぴ","ふ","ぶ","ぷ","へ","べ","ぺ","ほ","ぼ","ぽ",
          "ま","み","む","め","も",
          "ゃ","や","ゅ","ゆ","ょ","よ",
          "ら","り","る","れ","ろ",
          "ゎ","わ","ゐ","ゑ","を",
          "ん","ゔ","ゕ","ゖ",
          "わ゛","ゐ゛","ゑ゛","を゛");
    $katakana = array("ァ","ア","ィ","イ","ゥ","ウ","ェ","エ","ォ","オ",
          "カ","ガ","キ","ギ","ク","グ","ケ","ゲ","コ","ゴ",
          "サ","ザ","シ","ジ","ス","ズ","セ","ゼ","ソ","ゾ",
          "タ","ダ","チ","ヂ","ッ","ツ","ヅ","テ","デ","ト","ド",
          "ナ","ニ","ヌ","ネ","ノ",
          "ハ","バ","パ","ヒ","ビ","ピ","フ","ブ","プ","ヘ","ベ","ペ","ホ","ボ","ポ",
          "マ","ミ","ム","メ","モ",
          "ャ","ヤ","ュ","ユ","ョ","ヨ",
          "ラ","リ","ル","レ","ロ",
          "ヮ","ワ","ヰ","ヱ","ヲ",
          "ン","ヴ","ヵ","ヶ",
          "ヷ","ヸ","ヹ","ヺ");
    $initial = str_replace($hiragana, $katakana, $hira);

    // take care of double vowels
    $hira_longs = array("/([アァカガサザタダナハバパマヤャラワ])ア/u",
            "/([イィキギシジチヂニヒビピミリ])イ/u",
            "/([ウゥクグスズツヅヌフブプムユュル])ウ/u",
            "/([エェカゲセゼテデネヘベペメレ])エ/u",
            "/([オォコゴソゾトドノホボポモヨョロヲ])オ/u");
    $final = preg_replace($hira_longs, "$1ー", $initial);

    // and return
    return $final;
  }

  // FIXME: make hira/kata arrays point to the already defined ones?
  function kata_to_hira($kata)
  {
    // do initial replacement
    $katakana = array("ァ","ア","ィ","イ","ゥ","ウ","ェ","エ","ォ","オ",
          "カ","ガ","キ","ギ","ク","グ","ケ","ゲ","コ","ゴ",
          "サ","ザ","シ","ジ","ス","ズ","セ","ゼ","ソ","ゾ",
          "タ","ダ","チ","ヂ","ッ","ツ","ヅ","テ","デ","ト","ド",
          "ナ","ニ","ヌ","ネ","ノ",
          "ハ","バ","パ","ヒ","ビ","ピ","フ","ブ","プ","ヘ","ベ","ペ","ホ","ボ","ポ",
          "マ","ミ","ム","メ","モ",
          "ャ","ヤ","ュ","ユ","ョ","ヨ",
          "ラ","リ","ル","レ","ロ",
          "ヮ","ワ","ヰ","ヱ","ヲ",
          "ン","ヴ","ヵ","ヶ",
          "ヷ","ヸ","ヹ","ヺ");
    $hiragana = array("ぁ","あ","ぃ","い","ぅ","う","ぇ","え","ぉ","お",
          "か","が","き","ぎ","く","ぐ","け","げ","こ","ご",
          "さ","ざ","し","じ","す","ず","せ","ぜ","そ","ぞ",
          "た","だ","ち","ぢ","っ","つ","づ","て","で","と","ど",
          "な","に","ぬ","ね","の",
          "は","ば","ぱ","ひ","び","ぴ","ふ","ぶ","ぷ","へ","べ","ぺ","ほ","ぼ","ぽ",
          "ま","み","む","め","も",
          "ゃ","や","ゅ","ゆ","ょ","よ",
          "ら","り","る","れ","ろ",
          "ゎ","わ","ゐ","ゑ","を",
          "ん","ゔ","ゕ","ゖ",
          "わ゛","ゐ゛","ゑ゛","を゛");
    $initial = str_replace($katakana, $hiragana, $kata);

    // take care of double vowels
    $hira_longs = array("/([あぁかがさざただなはばぱまやゃらわ])ー/u",
            "/([いぃきぎしじちぢにひびぴみり])ー/u",
            "/([うぅくぐすずつづぬふぶぷむゆゅる])ー/u",
            "/([えぇけげせぜてでねへべぺめれ])ー/u",
            "/([おぉこごそぞとどのほぼぽもよょろを])ー/u");
    $kata_longs = array("$1あ","$1い","$1う","$1え","$1お");
    $final = preg_replace($hira_longs, $kata_longs, $initial);

    // and return
    return $final;
  }

  function create_legend() {
    $breaks = array("あ","か","が","さ","ざ","た","だ","な","は","ば","ぱ","ま","や","ら","わ","ヷ","ぁ","ー");
    $japanese = array();
    for($len = 5; $len>0; $len--) {
      if(isset($this->map[$len])) {
        foreach ($this->map[$len] as $ime=>$jp) { $japanese[$jp][] = $ime; }}}
    $ret = "<div>\n";
    $ret .= "<table cellspacing='0' cellpadding='2'>\n";
    $ret .= "<tr style='vertical-align: top; border: none;'>";
    $closable = false;
    $keys = array_keys($japanese);
    usort($keys, array("IMEmap", "ordercompare"));
    foreach($keys as $jp)
    {
      $syllables = preg_split("//u",$jp);
      if(in_array($syllables[1], $breaks))
      {
        if($closable) {
          $ret .= "</tbody>\n";
          $ret .= "</table>\n";
          $ret .= "</td>\n\n"; }
        $ret .= "<td style='border: none; padding:1em;' nowrap='nowrap'>\n";
        $ret .= "<div style='width:100%; text-align: center; font-size:150%;'>$jp</div>\n";
        $ret .= "<table cellspacing='2' cellpadding='2'>\n";
        $ret .= "<thead style='font-weight: bold;'>\n";
        $ret .= "</thead>\n";
        $ret .= "<tbody>\n";
        $closable = true;
      }
      $spacer = str_repeat("　",count($syllables)-3);
      $ret .= "<tr><td class='jp'>". $spacer ."$jp</td><td>" . implode("　",$japanese[$jp]) . "</td></tr>\n";
    }
    $ret .= "</tr>\n";
    $ret .= "</table>\n";
    $ret .= "</div>\n";
    return $ret;
  }

  static $sortorder = array("あ","い","う","え","お",
          "か","き","く","け","こ",
          "が","ぎ","ぐ","げ","ご",
          "さ","し","す","せ","そ",
          "ざ","じ","ず","ぜ","ぞ",
          "た","ち","つ","て","と",
          "だ","ぢ","づ","で","ど",
          "な","に","ぬ","ね","の",
          "は","ひ","ふ","へ","ほ",
          "ば","び","ぶ","べ","ぼ",
          "ぱ","ぴ","ぷ","ぺ","ぽ",
          "ま","み","む","め","も",
          "や","ゆ","よ",
          "ら","り","る","れ","ろ",
          "わ","ゎ","ゐ","ゑ","を","ん",
          "ヷ","ヸ","ヴ","ヹ","ヺ",
          "ぁ","ぃ","ぅ","ぇ","ぉ","ヵ","ヶ","っ","ゃ","ゅ","ょ",
          "ー","＊","？","「","」","。","、","　");

  static function ordercompare($a, $b) { return IMEmap::ordercompare_a($a, $b, 0); }

  static function ordercompare_a($a, $b, $pos)
  {
    if($pos>mb_strlen($a) && $pos<mb_strlen($b)) { return 1; }
    elseif ($pos>mb_strlen($b) && $pos<mb_strlen($a)) { return -1; }
    else {
      $spl = preg_split("//u",$a);
      $na = $spl[$pos];
      $spl = preg_split("//u",$b);
      $nb = $spl[$pos];
      $id_a = array_search($na, IMEmap::$sortorder);
      $id_b = array_search($nb, IMEmap::$sortorder);
      $cmp = $id_a - $id_b;
      if($cmp!=0) return $cmp;
      return IMEmap::ordercompare_a($a, $b, $pos+1); }
  }
}

$IMEmap = new IMEmap();
?>