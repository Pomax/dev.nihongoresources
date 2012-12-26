var AutoSuggest = (function () {

  // helper function for getting a computer style value
  var getStyle = function(el,styleProp) {
    return document.defaultView.getComputedStyle(el,null).getPropertyValue(styleProp);
  };

  // helper function for getting element positions on a page
  var findPos = function (obj) {
    var curleft = curtop = 0;
    if (obj.offsetParent) {
      do {
        curleft += obj.offsetLeft;
        curtop += obj.offsetTop;
      } while (obj = obj.offsetParent);
    }
    return {left: curleft, top: curtop};
  };

  // return a suggestionbox-building object
  return {
    suggestionBoxes: [],
    getSuggestionBox : function (idx) {
      return this.suggestionBoxes[idx];
    },
    clearAll : function() {
      this.suggestionBoxes.forEach(function(box) {
        box.clear();
      });
    },
    addSuggestionBox : function (element, callback) {
      var suggestionbox = {
        element: null,
        suggestionbox: null,
        current: -1,
        keyUp: function(evt, key, locally) {
          if(key === 38) {
            this.suggestionbox.focus();
            this.prev();
          }
          else if(key === 40) {
            this.suggestionbox.focus();
            this.next();
          }
          else if(key === 10 || key === 13) {
            var suggestion = this.getCurrentSuggestion();
            if (suggestion !== false) {
              this.useSuggestion(suggestion);
            }
            this.none();
            this.hide();
          }
          else if (key === 27) {
            this.none();
            this.hide();
          }
          else if(locally) {
            this.none();
            if (key === 8) {
              var value = this.element.value;
              var backspaced = value.substring(0,value.length-1);
              this.element.value = backspaced;
            }
            // FIXME: for some reason event redispatching does not work...
            /*
            else if (32 <= key && key <= 126) {
              var kevt = document.createEvent("KeyboardEvent");
              kevt.initEvent(evt.type, evt.bubbles, evt.cancelable, evt.view,
                             evt.ctrlKey, evt.altKey, evt.shiftKey, evt.metaKey,
                             evt.keyCode, evt.charCode);
              this.element.dispatchEvent(kevt);
            }
            */
            this.element.focus();
          }
          else {
            if(callback) {
              callback(this, this.element, key);
            }
          }
        },
        useSuggestion: function(suggestion) {
          if (this.element) {
            this.element.value = suggestion;
            this.element.focus();
            this.suggestionbox.style.display = "none";
          }
        },
        addSuggestion: function (suggestion, id, title) {
          if (this.suggestionbox) {
            // show suggestion box
            this.suggestionbox.style.display = "block";
            // add entry
            var entry = document.createElement("div");
            entry.setAttribute("class","suggestion");
            if (id) {
              entry.id = id;
            }
            if (title) {
              entry.title = title;
            }
            entry.innerHTML = suggestion;
            entry.style.cursor = "default";
            var sbox = this;
            entry.addEventListener("click", function() {
              sbox.useSuggestion(suggestion);
            }, false);
            this.suggestionbox.appendChild(entry);
          }
        },
        clear: function () {
          this.suggestionbox.style.display = "none";
          this.suggestionbox.innerHTML = "";
        },
        show: function() {
          this.suggestionbox.style.display = "block";
        },
        hide: function() {
          this.none();
          this.suggestionbox.style.display = "none";
          this.element.focus();
        },
        next: function() {
          var c;
          if(this.current===-1) {
            this.current = 0;
            c = this.suggestionbox.children[this.current];
            c.setAttribute("class", "suggestion selected");
          } else {
            c = this.suggestionbox.children[this.current];
            c.setAttribute("class", "suggestion");
            this.current++;
            if(this.current >= this.suggestionbox.children.length) {
              this.current = 0;
            }
            c = this.suggestionbox.children[this.current];
            c.setAttribute("class", "suggestion selected");
          }

        },
        prev: function() {
          var c;
          if(this.current===-1) {
            this.current = this.suggestionbox.children.length-1;
            c = this.suggestionbox.children[this.current];
            c.setAttribute("class", "suggestion selected");
          } else {
            c = this.suggestionbox.children[this.current];
            c.setAttribute("class", "suggestion");
            this.current--;
            if(this.current < 0) {
              this.current = this.suggestionbox.children.length-1;
            }
            c = this.suggestionbox.children[this.current];
            c.setAttribute("class", "suggestion selected");
          }
        },
        none: function () {
          if(this.current !== -1) {
            var c = this.suggestionbox.children[this.current];
            c.setAttribute("class", "suggestion");
          }
          this.current = -1;
        },
        getCurrentSuggestion: function() {
          if (this.current === -1) {
            return false;
          }
          return this.suggestionbox.children[this.current].innerHTML
        }
      };

      // add an autosuggestion box to the page, tied to the input element.
      var parent = element.parentNode;
      var boxelement = document.createElement("div");
      boxelement.setAttribute("tabindex", 0);
      boxelement.setAttribute("class", "suggestionbox");
      boxelement.style.display = "none";
      boxelement.style.position = "absolute";
      boxelement.style.zIndex = "99999";

      // fix suggestion box position and dimensions
      var position = findPos(element);
      var top = (position.top + element.offsetHeight);
      var shift = getStyle(element,"border-bottom-width").replace("px","");
      boxelement.style.top   = (top - shift) + "px";
      boxelement.style.left  = (position.left) + "px";
      boxelement.style.width = element.offsetWidth + "px";

      // add this suggestionbox right after where the element is located in the DOM
      if(element.nextSibling) {
        parent.insertBefore(boxelement, element.nextSibling);
      } else {
        parent.appendChild(boxelement);
      }

      // correct width for CSS-applied borders!
      var w = boxelement.style.width.replace("px","");
      var lborder = parseInt(getStyle(boxelement,"border-left-width").replace("px",""),10);
      var rborder = parseInt(getStyle(boxelement,"border-right-width").replace("px","",10));
      boxelement.style.width =  (w - (lborder + rborder)) + "px";

      // add bindings so that typing on the element passes that information to the suggestor object
      element.addEventListener("keyup", function(e){
        var evt = window.event || e;
        var key = evt.keyCode
        suggestionbox.keyUp(evt, key);
      }, false);

      // add bindings so that key handling for element is "faked"
      boxelement.addEventListener("keyup", function(e){
        var evt = window.event || e;
        var key = evt.keyCode;
        suggestionbox.keyUp(evt, key, true);
      }, false);


      // return our suggestion box element
      suggestionbox.element = element;
      suggestionbox.suggestionbox = boxelement;
      this.suggestionBoxes.push(suggestionbox);
      return suggestionbox;
    }
  };
}());