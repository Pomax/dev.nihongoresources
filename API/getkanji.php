<?php

  if(isset($_GET["kanji"]))
  {
    require_once("dbinfo.php");
    require_once("lookup.php");

    $start = microtime(true);
    $dbh = new PDO("mysql:host=$server;dbname=$database",$user,$password);
    // make sure we're in UTF8 mode
		$dbh->query("SET NAMES 'utf8'");
		$dbh->query("SET CHARACTER SET 'utf8'");

		// get the kanji information from KanjiDIC
		$kanji = $_GET["kanji"];
		$query = "SELECT * FROM dictionary_kanji AS k, (SELECT id FROM dictionary_kanji WHERE data LIKE '$kanji') AS m WHERE k.id = m.id;";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $result = array();
    while(($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
      $id = $row["id"];
      $type = $row["type"];
      $data = $row["data"];
      $result["codepoint"]=$id;
      if($type=="kanji" || $type == "stroke" || $type == "grade") { $result[$type] = $data; }
      else if($type=="bushu") {
        $bushu = intval($data);
        $result[$type] = $Lookup->bushu[$bushu];
        $result["bushuno"] = $bushu; }
      else { $result[$type][] = $data; }
    }
    $dbh = null;

    // get graphical relations
    $dbh = new PDO("sqlite:db/compositions.db");
    $query = "SELECT * FROM compositions WHERE kanji LIKE '$kanji' OR component LIKE '$kanji'";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $parents  = array();
    $children = array();
    while(($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
      $k = $row["kanji"];
      $c = $row["component"];
      // is this a parent?
      if($k == $kanji && preg_match("/\w/",$c)==0) { $parents[] = $c; }
      // nope, it's a child
      elseif(preg_match("/\w/",$k)==0) { $children[] = $k; }
    }
    $dbh = null;
    $end = microtime(true);

    $json = array();
    foreach($result as $key => $value) {
      if(is_array($value)) {
        $content = implode("','",$value);
        if(trim($content)=="") { continue; }
        $json[] = "$key: ['" . $content . "']"; }
      else { $json[] = "$key: '$value'"; }
    }
    $content = implode("','",$parents);
    if(trim($content)!="") { $json[] = "parents: ['" . $content . "']"; }
    $content = implode("','",$children);
    if(trim($content)!="") { $json[] = "children: ['". $content . "']"; }

    $time = ($end-$start);
    echo "{" . implode(",\n",$json) . ", time: $time}";
  }
?>