/*
  additional:
    [x] detect verb form
    [ ] show romanisation

  search as:
    [x] dictionary term
    [x] kanji
    [x] sound effect
    [x] name
    [x] particle

  sort:
    [ ] alphabetically
    [x] kana ordering
    [ ] kanji ordering
        [ ] dictionary grouping
    [ ] reverse sort

  filter results:
    [ ] verbs
        [ ] godan
        [ ] ichidan
        [ ] irregular
    [ ] nouns
    [ ] adjectives
        [ ] verbal
        [ ] nominal
    [ ] adverbs
    [ ] expressions
*/

/**
 * Set up filter option "onclick" behaviour
 */
document.listenOnce("DOMContentLoaded",function() {
  // general options
  var plainForm = find("*[name='general.plain']");
  plainForm.listen("click", function(e) { find(".plain.entry").show(plainForm.checked); });
  var verbForm = find("*[name='general.verb']");
  verbForm.listen("click", function(e) { find(".verb.entry").show(verbForm.checked); });
  var romaji = find("*[name='general.romaji']");
  romaji.listen("click", function(e) { find("rt").show(romaji.checked); });

  // result sets
  var dictSearch = find("*[name='search.dictionary']");
  dictSearch.listen("click", function(e) { find(".dictionary.entry").show(dictSearch.checked); });
  var kanjiSearch = find("*[name='search.kanji']");
  kanjiSearch.listen("click", function(e) { find(".kanji.entry").show(kanjiSearch.checked); });
  var sfxSearch = find("*[name='search.sfx']");
  sfxSearch.listen("click", function(e) { find(".giongo.entry").show(sfxSearch.checked); });

  /*
    sort.alphabetically
    sort.kana
    sort.kanji
    sort.ascending
    sort.reverse
  */

  /*
    filter.verbs
    filter.verbs.godan
    filter.verbs.ichidan
    filter.verbs.irregular
    filter.noun
    filter.adjectives
    filter.adjectives.verbal
    filter.adjectives.nominal
    filter.adverbs
    filter.expressions
  */
});