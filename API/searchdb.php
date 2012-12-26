<?php
  $timing = array();

  /**
   * Probe the database for entry ids, and
   * merge them into the current id list.
   */
  function probe($dbh, &$ids, $probe) {
    $stmt = $dbh->prepare($probe);
    $stmt->execute();
    while(($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
      $id = $row["id"];
      if(!in_array($id,$ids)) {
        $ids[] = $id;
      }
    }
  }

  /**
   * Actually query the database.
   */
  function query($dbh, $query) {
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $results = array();
    $lastid = 0;
    $found = 1;
    while(($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
      $id = $row["id"];
      if($id != $lastid) { $lastid = $id; $found = 1; }
      $data = $row["data"];
      if(!isset($results[$id])) { $results[$id] = array(); }
      if($data != "") { $results[$id][] = $data; }
    }
    return $results;
  }

  /**
   * extend an associative array with another associative array
   */
  function extend(&$array, $array2) {
    foreach($array2 as $key=>$val) {
      $array[$key] = $val;
    }
  }

  /**
   * Convert aggregate data into a single HTML string
   */
  function collapse($data, $crosslink=false, $enumerate=false, $ucase=false)
  {
    $string = "";
    for($i=0; $i<count($data); $i++) {
      $term = $data[$i];
      if($enumerate) { $string .= "<wbr><num>" . ($i+1) . "</num>"; }
      else if($i>0) { $string .= ", "; }
      $string .= ($crosslink? "<st>" : "");
      $string .= ($ucase? ucfirst($term) : $term);
      $string .= ($crosslink? "</st>" : "");
    }
    return $string;
  }

  /**
   * Slightly different collapse for kanji terms, because we want to
   * crosslink every kanji in the word.
   */
  function collapse_kanji($data)
  {
    $string = "";
    $pattern = '/([\x{4E00}-\x{9FFF}])/u';
    $replacement = "<st>$" . "{1}</st>";
    for($i=0; $i<count($data); $i++) {
      if($i>0) { $string .= ", "; }
      $string .= preg_replace($pattern, $replacement, $data[$i]);
    }
    return $string;
  }

  /**
   * try to load a cache file. return false if it doesn't exist
   */
  function load_cache($searchterm)
  {
    /*
      // we're going to overwrite the content for these arrays:
      global $ids, $vids, $eng, $reb, $keb, $pos, $com, $verbs;

      // get the cache
      $dir = "db/cache/";
      $file = $dir . md5($searchterm) . ".php.gz";
      if(file_exists($file)) {
        eval(implode("",gzfile($file)));
        if(!isset($cache_term) || $cache_term != $searchterm) {
          // fail, because this is the wrong cache file!
          return false; }
        // success!
        touch($file);
        return true;
      }
    */

    // fail, because there is no cache to load.
    return false;
  }

  /**
   * Write database results to disk, as instaloadable .php source code
   */
  function write_cache($searchterm)
  {
    /*
      // we're going to write the content for these arrays to disk:
      global $ids, $vids, $eng, $reb, $keb, $pos, $com, $verbs;
      $dir = "db/cache/";
      $file = $dir . md5($searchterm) . ".php.gz";
      $fh = gzopen($file,"w9");
      gzwrite($fh, '$cache_timestamp = "' . time() . '";' . "\n");
      gzwrite($fh, '$cache_term = "' . $searchterm . '";' . "\n");
      gzwrite($fh, '$ids = ' . var_export($ids, true) . ";\n");
      gzwrite($fh, '$vids = '. var_export($vids,true) . ";\n");
      gzwrite($fh, '$eng = ' . var_export($eng, true) . ";\n");
      gzwrite($fh, '$reb = ' . var_export($reb, true) . ";\n");
      gzwrite($fh, '$keb = ' . var_export($keb, true) . ";\n");
      gzwrite($fh, '$pos = ' . var_export($pos, true) . ";\n");
      gzwrite($fh, '$com = ' . var_export($com, true) . ";\n");
      gzwrite($fh, '$verbs = '.var_export($verbs,true). ";\n");
      gzclose($fh);
    */
  }

// ========== ENTRY POINT ==========

  // global arrays, filled either from cache, or from database queries
  $ids = array();
  $vids = array();
  $eng = array();
  $reb = array();
  $keb = array();
  $pos = array();
  $com = array();
  $verbs = array();

  $entries =  array();
  $verbentries = array();
  $kanjientries = array();
  $giongoentries = array();
  $namesentries = array();


  // Let's see if we have any work to do
  if(isset($_GET["searchterm"]))
  {
    $timing["overall"] = microtime(true);

    require_once("quicksort.php");
    require_once("common.php");
    require_once("utf8functions.php");
    require_once("imemap.php");
    require_once("lookup.php");
    require_once("dbinfo.php");
    require_once("searchdb.php");

    // grab prop:val entries (used as search directives)
    $input = $_GET["searchterm"];
    preg_match_all("/(\w+):(\w+)/",$input,$matches,PREG_SET_ORDER);
    $input = preg_replace("/\w+:\w+/","",$input);
    // default directives
    $directives = array("sort"=>"reb", "direction"=>"up", "reverse"=>false);
    // overrides
    foreach($matches as $match) { $directives[$match[1]] = $match[2]; }

    // get the search term
    $searchterm = $Lookup->secure_keyword($input);
    $probeterm = wildcard_for_SQL_LIKE($searchterm);

    // perform language detection
    $methods = $Lookup->detectinputmethod($searchterm);

    // if previously cached, load from cache. Else, normal database polling run
    $start = microtime(true);
    if(!load_cache($searchterm))
    {
      $dbh = new PDO("mysql:host=$server;dbname=$database",$user,$password);
      // make sure we're in UTF8 mode
      $dbh->query("SET NAMES 'utf8'");
      $dbh->query("SET CHARACTER SET 'utf8'");

      // SEARCH ENGLISH
      if($methods["western"]) {
        // enable searching for Japanese
        $converted = $IMEmap->convert(wildcard_for_SQL_LIKE($searchterm));
        if($converted!==false) {
          $searchterm = "";
          $methods["hira"] = $converted["hiragana"];
          $methods["kata"] = $converted["katakana"];
        }
        // if there are special characters in the probe term, do "normal" searching
        if(preg_match("/\W/",str_replace("*","",$searchterm))>0 || preg_match("/\w\*\w/",$searchterm)) {
          $probeterm = wildcard_for_SQL_RLIKE($searchterm);
          $timing["english"] = microtime(true);
          probe($dbh, $ids, "SELECT DISTINCT id FROM dictionary_eng WHERE data REGEXP '[[:<:]]${probeterm}[[:>:]]'");
          $timing["english"] = microtime(true) - $timing["english"];
        } else {
          // optimised searching is possible.
          $start = (substr($probeterm,0,1)=="%");
          $rev   = strrev($probeterm);
          $end   = (substr($rev,0,1)=="%");
          $probeterm = str_replace("%",'',$probeterm);
          $rev       = str_replace("%",'',$rev);

          $wheres    = array();
          if($start) { $wheres[] = "revterm LIKE '$rev%'";    }
          if($end)   { $wheres[] = "term LIKE '$probeterm%'"; }
          if(!$start && !$end) { $wheres[] = "term LIKE '$probeterm'"; }
          $where = implode(" OR ", $wheres);
          $query = "SELECT DISTINCT id FROM dictionary_eng_index WHERE " . $where;

          $timing["english_idx"] = microtime(true);
          probe($dbh, $ids, $query);
          $timing["english_idx"] = microtime(true) - $timing["english_idx"];
        }
      }

      // SEARCH KANJI
      if ($methods["kanji"]) {
        // if the words ends on hiragana, it might be a verb
        if(preg_match("/[\x{3041}-\x{3093}]$/u",$probeterm)>0) {
          $verbs = $Lookup->detect_verb($probeterm, " →<wbr> "); }

        // if there are wildcards inside the string, do normal searching
        if(preg_match("/.%./u",$probeterm)>0) {
          $timing["kanji"] = microtime(true);
          probe($dbh, $ids, "SELECT DISTINCT id FROM dictionary_keb WHERE data LIKE '$probeterm'");
          $timing["kanji"] = microtime(true);
        } else {
          // optimised searching is possible.
          $probeterm = wildcard_for_SQL_LIKE($searchterm);
          $start = (substr($probeterm,0,1)=="%");
          $rev   = mb_strrev($probeterm);
          $end   = (substr($rev,0,1)=="%");
          $probeterm = str_replace("%",'',$probeterm);
          $rev       = str_replace("%",'',$rev);

          $wheres    = array();
          if($start) { $wheres[] = "revterm LIKE '$rev%'";    }
          if($end)   { $wheres[] = "term LIKE '$probeterm%'"; }
          if(!$start && !$end) { $wheres[] = "term LIKE '$probeterm'"; }
          $where = implode(" OR ", $wheres);
          $query = "SELECT DISTINCT id FROM dictionary_keb_index WHERE " . $where;

          $timing["kanji_idx"] = microtime(true);
          probe($dbh, $ids, $query);
          $timing["kanji_idx"] = microtime(true) - $timing["kanji_idx"];
        }
      }

      // SEARCH KANA
      if ($methods["hira"] || $methods["kata"]) {
        // set from western search?
        if($searchterm=="" && $methods["hira"]) { $probeterm = $methods["hira"]; }

        // if hiragana, do verb detection
        if($methods["hira"]) { $verbs = $Lookup->detect_verb($probeterm, " →<wbr> "); }

        // if there are wildcards inside the string, do normal searching
        if(preg_match("/.%./u",$probeterm)>0) {
          $wheres = array();
          if($searchterm=="") {
            if($methods["hira"]) { $wheres[] = "data LIKE '". $methods["hira"] ."'";    }
            if($methods["kata"]) { $wheres[] = "data LIKE '". $methods["kata"] ."'";    }
          } else { $wheres[] = "data LIKE '$probeterm'"; }
          $where = implode(" OR ", $wheres);
          $timing["kana"] = microtime(true);
          probe($dbh, $ids, "SELECT DISTINCT id FROM dictionary_reb WHERE $where");
          $timing["kana"] = microtime(true) - $timing["kana"];

        } else {
          // optimised searching is possible
          $terms = array();
          if($searchterm=="") {
            if($methods["hira"]) { $terms[] = $methods["hira"]; }
            if($methods["kata"]) { $terms[] = $methods["kata"]; }
          } else { $terms[] = $probeterm; }

          $wheres = array();
          foreach($terms as $probeterm) {
            $start = (substr($probeterm,0,1)=="%");
            $rev   = mb_strrev($probeterm);
            $end   = (substr($rev,0,1)=="%");
            $probeterm = str_replace("%",'',$probeterm);
            $rev       = str_replace("%",'',$rev);
            if($start) { $wheres[] = "revterm LIKE '$rev%'";    }
            if($end)   { $wheres[] = "term LIKE '$probeterm%'"; }
            if(!$start && !$end) { $wheres[] = "term LIKE '$probeterm'"; }}
          $where = implode(" OR ", $wheres);
          $query = "SELECT DISTINCT id FROM dictionary_reb_index WHERE $where";

          $timing["kana_idx"] = microtime(true);
          probe($dbh, $ids, $query);
          $timing["kana_idx"] = microtime(true) - $timing["kana_idx"];
        }
      }

      // if there were any results, fill the arrays
      if(count($ids)>0)
      {
        // find all these entries for the separate tables
        $id_list = join(",",$ids);

        $timing["eng"] = microtime(true);
        $eng = query($dbh, "SELECT id, data FROM dictionary_eng WHERE id IN($id_list)");
        $timing["eng"] = microtime(true) - $timing["eng"];

        $timing["reb"] = microtime(true);
        $reb = query($dbh, "SELECT id, data FROM dictionary_reb WHERE id IN($id_list)");
        $timing["reb"] = microtime(true) - $timing["reb"];

        $timing["keb"] = microtime(true);
        $keb = query($dbh, "SELECT id, data FROM dictionary_keb WHERE id IN($id_list)");
        $timing["keb"] = microtime(true) - $timing["keb"];

        $timing["pos"] = microtime(true);
        $pos = query($dbh, "SELECT id, data FROM dictionary_pos WHERE id IN($id_list)");
        $timing["pos"] = microtime(true) - $timing["pos"];
      }

      // resolve possible verb forms
      if(count($verbs)>0) {
        $keys = array();
        $shortest = "";
        $len = 999;
        foreach($verbs as $data) {
          $tmpl = $UTF8Functions->utf8_strlen($data[0]);
          if($tmpl < $len) {
            $shortest = $data[0];
            $len = $tmpl;
          }
        }
        $stem = $UTF8Functions->utf8_substr($shortest, 0, $len-1);

        // do we need to look in the reading, or kanji table?
        $table = "reb";
        if ($methods["kanji"]) { $table = "keb"; }

        // create temporary table
        $tmptable = "nihongoresources_temp.verb_" . str_replace('.', '', ''.microtime(true));
        $timing["verb_temp_table"] = microtime(true);
        $stmt = $dbh->prepare("CREATE TEMPORARY TABLE $tmptable (SELECT * FROM dictionary_$table WHERE data LIKE '$stem%')");
        $success = $stmt->execute();
        $timing["verb_temp_table"] = microtime(true) - $timing["verb_temp_table"];
        if(!$success) {
          // fallback to normal searching, even though it's a lot slower.
          $tmptable = "dictionary_" . $table;
          $timing["verb_temp_table"] = "failed";
        }

        // build query for matching the list of verbs
        $where = "";
        foreach($verbs as $data) { $where .= "data LIKE '" . $data[0] . "' OR "; }
        $where = substr($where, 0, strlen($where)-4);
        $timing["verbfind"] = microtime(true);
        probe($dbh, $vids, "SELECT DISTINCT id FROM $tmptable WHERE " . $where);
        $timing["verbfind"] = microtime(true) - $timing["verbfind"];

        // get additional verb results
        if(count($vids)>0)
        {
          $id_list = join(",",$vids);

          $timing["eng_V"] = microtime(true);
          extend($eng, query($dbh, "SELECT id, data FROM dictionary_eng WHERE id IN($id_list)"));
          $timing["eng_V"] = microtime(true) - $timing["eng_V"];

          $timing["reb_V"] = microtime(true);
          extend($reb, query($dbh, "SELECT id, data FROM dictionary_reb WHERE id IN($id_list)"));
          $timing["reb_V"] = microtime(true) - $timing["reb_V"];

          $timing["keb_V"] = microtime(true);
          extend($keb, query($dbh, "SELECT id, data FROM dictionary_keb WHERE id IN($id_list)"));
          $timing["keb_V"] = microtime(true) - $timing["keb_V"];

          $timing["pos_V"] = microtime(true);
          extend($pos, query($dbh, "SELECT id, data FROM dictionary_pos WHERE id IN($id_list)"));
          $timing["pos_V"] = microtime(true) - $timing["pos_V"];
        }
      }

      // cache this search result
      write_cache($searchterm);
    }

    $timing["buildtime"] = microtime(true);

    // run through the results
    for($i=0, $e=count($ids); $i<$e; $i++) {
      $id = $ids[$i];
      if(in_array($id, $vids)) { unset($ids[$i]); continue; } // skip over verb entries, we deal with them later
      $entries[] = array("id"=>$id, "keb" => $keb[$id], "reb" => $reb[$id], "eng" => $eng[$id], "pos" => $pos[$id]);
    }

    // quicksort these entries (this is WAY faster than usort with a comparator)
    $entries = quicksort($entries, "reb");

    // possible verb matches come after the normal results:
    if(count($vids)>0) {
      $container =& $reb;
      if ($methods["kanji"]) { $container =& $keb; }

      // copy the vids list to a new list, and then clear it.
      // we will be refilling it only with ids for entries that
      // actually match the verb criteria.
      $list = $vids;
      $vids = array();
      foreach($list as $id)
      {
        $entry = array("reading"=>$container[$id], "pos"=>$pos[$id]);

        // does this entry have a reading that matches one of the decompositions,
        // while also matching the required postag? If not, skip this entry.
        $skipentry = true;
        foreach($verbs as $data) {
          $correct_reading = in_array($data[0], $entry["reading"]);
          $correct_postag = in_array($data[1], $entry["pos"]);
          if($correct_reading && $correct_postag) {
            $entry["verbtype"] = $data[1];
            $entry["form"] = $data[2];
            $skipentry = false;
            break; }}

        // no match was found, skip to the next entry
        if($skipentry) { continue; }

        // if a match, verb criteria are fulfilled, and this id is "real".
        $ventry = array(
          "id" => $id,
          "verb" => trim(preg_replace("/with .* ending/", "", strtolower($entry["verbtype"]))),
          "keb" => $keb[$id],
          "reb" => $reb[$id],
          "eng" => $eng[$id],
          "form" => $entry["form"]
        );
        $verbentries[] = $ventry;
      }
    }

    // search kanji dictionary (general search), if this term is a single kanji
    if ($Lookup->kanjichar($searchterm)) {
      $timing["kanji"] = microtime(true);

      // TODO: add in * wildcard awareness

      $query = "SELECT DISTINCT id FROM dictionary_kanji WHERE type = 'kanji' AND data ='$searchterm'";
      $ids = array();
      probe($dbh, $ids, $query);
      if(count($ids)>0) {
        $id_list = "'" . implode("','", $ids) . "'";
        $stmt = $dbh->prepare("SELECT id, type, data FROM dictionary_kanji WHERE id IN($id_list)");
        $stmt->execute();
        $kanjientries = array();
        while(($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
          $id = $row["id"];
          $data = $row["data"];
          if($data=="") continue;
          $type = $row["type"];
          if(!isset($kanjientries[$id])) { $results[$id] = array(); }
          // single value entries
          if($type == "stroke" || $type == "bushu" || $type == "grade") { $kanjientries[$id][$type] = $data; }
          // possibly multi-value entries
          else { $kanjientries[$id][$type][] = $data; }
          if($type == "bushu") {
            $kanjientries[$id]["bushuji"] = $Lookup->bushu[intval($data)];
          }
        }
      }
      $timing["kanji"] = microtime(true) - $timing["kanji"];
    }

    // search the giongo dictionary (general search), if there is a hiragana form available
    if(($methods["hira"] || $methods["kata"]) && !$methods["kanji"]) {
      $timing["giongo"] = microtime(true);

      // TODO: add in * wildcard awareness

      if($methods["hira"]==1) { $methods["hira"] = $searchterm; }
      if($methods["kata"]==1) { $methods["hira"] = $Lookup->convert_kata_to_hira($searchterm); }
      $query = "SELECT DISTINCT giongoid as id FROM dictionary_giongo WHERE giongo ='" . $methods["hira"] . "'";
      $ids = array();
      probe($dbh, $ids, $query);
      if(count($ids)>0) {
        $id_list = implode(",", $ids);
        $query = "SELECT t1.giongoid AS id, giongo, meaning, category FROM dictionary_giongo AS t1, dictionary_giongo_categories AS t2, dictionary_giongo_meanings AS t3 WHERE t1.giongoid in ($id_list) AND t3.giongoid = t1.giongoid AND t2.id = t3.categoryid";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $giongoentries = array();
        while(($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
          $id = $row["giongo"];
          $meaning = $row["meaning"];
          $category = $row["category"];
          if(!isset($giongoentries[$id])) { $results[$id] = array(); }
          $giongoentries[$id][] = array("meaning"=>$meaning, "category" => $category);
        }
      }
      $timing["giongo"] = microtime(true) - $timing["giongo"];
    }

    // search the names dictionary (general search)
    //
      // TODO: ...code goes here...
    //

    // record time taken
    $timing["buildtime"] = microtime(true) - $timing["buildtime"];
    $timing["overall"] = microtime(true) - $timing["overall"];
  }

  echo "{ dictionary: " . json_encode($entries) . ",\n";
  echo "  verbforms:  " . json_encode($verbentries) . ",\n";
  echo "  kanji: " . json_encode($kanjientries) . ",\n";
  echo "  giongo: " . json_encode($giongoentries) . ",\n";
  echo "  names: [],\n";
  echo "  timing: " . json_encode($timing) . "\n";
  echo "}";
?>