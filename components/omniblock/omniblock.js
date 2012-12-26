document.listenOnce("DOMContentLoaded", function focusOnInputField() {

  // set up the omnibox autosuggest
  var input = find("#searchform input[type=text]");
  var suggestions = AutoSuggest.addSuggestionBox(input, function(box, element, key) {
    var result = IME.convert(element.value.toLowerCase());
    if(result) {
      box.clear();
      ["romaji","hiragana","katakana"].forEach(function(key) {
        if(result[key] && result[key] !== "") {
          box.addSuggestion(result[key], key, key);
        }
      });
    }
  });


  // clear suggestions, and focus on input
  suggestions.clear();
  input.focus();


  // filter search results
  var filter = function() {
    var checkedOptions = [];
    find("input[type=checkbox]:checked").do(function(e){
      checkedOptions.push(e.get("name"));
    });
    checkedOptions.forEach(function(option) {
      // code goes here
    });
  };


  // crosslink <st> terms
  var crossLink = function(url, term) {
    find(".content span").do(function(e){
      // FIXME: turn into <cl> elements
      e.classes().add("clickable");
      e.onclick = function(evt) { fetchResults(url, e.innerHTML); }
    });
    var ss = find("#start-search"),
        sub = find("#substring-search"),
        es = find("#end-search");
    [ss, sub, es].forEach(function(s) {
      s.removeAttribute("href");
      s.classes().add("clickable");
    });
    ss.onclick  = function() { fetchResults(url, term + "*");       };
    sub.onclick = function() { fetchResults(url, "*" + term + "*"); };
    es.onclick  = function() { fetchResults(url, "*" + term);       };
  };


  // make sure that "back" button does what it's supposed to
  window.onpopstate = function(e) {
    if(e.state) {
      document.querySelector(".content").innerHTML = e.state.html;
      crossLink(e.state.url, e.state.term);
      find("#searchform input[type=text]").value = e.state.term;
    }
  };

  // collapse an entry-array into span-wrapped elements
  var collapse = function(arr, className) {
    return "<span" + (className ? "class='"+className+"'" : '') + ">" + arr.join("</span><span>") + "</span>";
  };

  // turn a server JSON response into HTML
  var formHTML = function(json) {
    // normal dictionary responses
    var div = create("div");
    var dictionary = json.dictionary;
    if(dictionary.length>0) {
      dictionary.forEach(function(entry) {
        var entryElement = create("div", {"class":"plain dictionary entry"},"");
        var keb = create("div",{"class":"keb"}, collapse(entry.keb));
        entryElement.add(keb);
        var reb = create("div",{"class":"reb" + (keb.innerHTML=="" ? "" : " wk")}, collapse(entry.reb));
        entryElement.add(reb);
        var pos = create("div",{"class":"pos"}, collapse(entry.pos));
        entryElement.add(pos);
        var eng = create("div",{"class":"eng"}, collapse(entry.eng));
        entryElement.add(eng);
        div.add(entryElement);
      });
    }

    // possible verb forms
    var verbforms = json.verbforms;
    if(verbforms.length>0) {
      verbforms.forEach(function(entry) {
        var entryElement = create("div", {"class":"dictionary verb entry"},"");
        var keb = create("div",{"class":"keb"}, collapse(entry.keb));
        entryElement.add(keb);
        var reb = create("div",{"class":"reb" + (keb.innerHTML=="" ? "" : " wk")}, collapse(entry.reb));
        entryElement.add(reb);
        var form = create("div", {"class":"verbform"}, entry.form);
        entryElement.add(form);
        var pos = create("div",{"class":"pos"}, entry.verb);
        entryElement.add(pos);
        var eng = create("div",{"class":"eng"}, collapse(entry.eng));
        entryElement.add(eng);
        div.add(entryElement);
      });
    }

    // possible kanji hits
    var kanji = json.kanji;
    for(var attr in kanji) {
      if(Object.hasOwnProperty(kanji,attr)) continue;
      var entry = kanji[attr];
      var entryElement = create("div", {"class":"kanji entry"},"");
      var k = create("div", {"class":"keb"}, collapse(entry.kanji));
      entryElement.add(k);
      if(entry.jaon) {
        var jaon = create("div", {"class":"reb wk jaon"}, collapse(entry.jaon));
        entryElement.add(jaon); }
      if(entry.jakun) {
        var jakun = create("div", {"class":"reb wk jakun"}, collapse(entry.jakun));
        entryElement.add(jakun); }
      if(entry.nanori) {
        var nanori = create("div", {"class":"reb wk nanori"}, collapse(entry.nanori));
        entryElement.add(nanori); }

      var meta = create("div", {"class":"metadata"}, "");
      var bushu = create("div", {"class":"bushu"}, entry.bushu);
      meta.add(bushu);
      var bushuji = create("div", {"class":"bushuji"}, entry.bushuji);
      meta.add(bushuji);
      var stroke = create("div", {"class":"stroke"}, entry.stroke);
      meta.add(stroke);
      var grade = create("div", {"class":"grade"}, entry.grade);
      meta.add(grade);
      entryElement.add(meta);

      var eng = create("div", {"class":"eng"}, collapse(entry.eng));
      entryElement.add(eng);
      div.add(entryElement);
    }

    // possible giongo/gitaigo
    var giongo = json.giongo;
    for(var attr in giongo) {
      if(Object.hasOwnProperty(giongo,attr)) continue;
      var entryElement = create("div", {"class":"giongo entry"},"");
      var label = create("div", {"class":"reb wk"}, "<span>"+attr+"</span>");
      entryElement.add(label);
      var packed = giongo[attr];
      packed.forEach(function(entry) {
        var category = create("div", {"class":"pos"}, entry.category);
        entryElement.add(category);
        var meaning = create("div", {"class":"eng"}, "<span>"+entry.meaning+"</span>");
        entryElement.add(meaning);
      });
      div.add(entryElement);
    }

    // add in romanisations
    div.find(".reb span").do(function(e) {
      var ruby = create("ruby");
      var rb = create("rb");
      var rt = create("rt");
      ruby.add(rb).add(rt);
      rb.innerHTML = e.innerHTML;
      rt.innerHTML = IME.convert(e.innerHTML).romaji;
      e.parent().replace(e, ruby);
    });

    // we're done
    return div.innerHTML;
  };


  // result fetch function (API call)
  var fetchResults = function(url, term) {
    get(url + "?searchterm=" + term, function(data) {
      find("#searchform input[type=text]").value = term;
      var path = window.location.toString();
      path = path.substring(0, path.lastIndexOf("/"));
      try {
        var formJSON = new Function("return " + data.responseText + ";");
        try {
          var json = formJSON();
          try {
            var html = formHTML(json);
            try {
              find(".content").innerHTML = html;
//              window.history.pushState({html: html, url: url, term: term}, "Search results for "+term, path + "/" + term);
              crossLink(url, term);
              filter();
            }
            catch(bindError) {
              console.log("bind error: could not assign HTML content to the page");
              console.log(bindError);
              console.log({response: data.responseText, formJSON: formJSON, json: json});
              return;
            }
          }
          catch(conversionError) {
            console.log("conversion error: could not convert JSON response to an HTML form");
            console.log(conversionError);
            console.log({response: data.responseText, formJSON: formJSON, json: json});
            return;
          }
        }
        catch(runtimeError) {
          console.log("interpretation error: could not interpret response JSON");
          console.log(runtimeError);
          console.log({response: data.responseText, formJSON: formJSON});
          return;
        }
      }
      catch(syntaxError) {
        console.log("syntax error: did not receive a valid JSON response from server");
        console.log({response: data.responseText});
        console.log(syntaxError);
        return;
      }
    });
  };


  // submission handling
  find("#searchform").onsubmit = function(e) {
    // cancel the actual submit
    if(!e) var e = window.event;
    e.cancelBubble = true;
    e.returnValue = false;
    if (e.stopPropagation) { e.stopPropagation(); }
    if (e.preventDefault) { e.preventDefault(); }

    // clear suggestion box
    AutoSuggest.clearAll();

    // ajax-fetch results
    var form = e.target;
    var input =  form.find("input[type=text]");
    var url = form.action;
    fetchResults(form.action, input.value);
  }
});