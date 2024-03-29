/**

  This is a quick and dirty toolkit for HTLM element manipulation.
  While it might look like a jQuery-a-like, it has nothing that
  makes jQuery so powerful, so if you're already using jQuery
  on a site: you have no reason to use this toolkit.

  That said, it is pretty damn small compared to jQuery... So,
  use your best judgement?

  Note that I make no pretenses at backward compatibility.
  This library is only for browsers that natively support canvas,
  which rules out all the ancient browsers. I Look forward to
  the days when we look back at this period and laugh at the legacy.

  - Pomax

**/
(function(window, document, body){

  // don't overload
  if(window["Toolkit"]) return;

  /**
   * Toolkit object, for accessing the update() function
   */
  var Toolkit = {
    // runs on element creation (or manually)
    update: function(element) {
      return element;
    },

    // allows plugins to hook into the update process
    addUpdate: function(f) {
      var oldFn = this.update;
      this.update = function(element) {
        return f(oldFn(element));
      };
    }
  };

  /**
   * bind Toolkit object
   */
  window["Toolkit"] = Toolkit;

  /**
   * universal document.createElement()
   */
  window["create"] = function(tagname,attributes,content) {
    var element = window["extend"](document.createElement(tagname));
    // element attributes
    if(attributes) {
      for(property in attributes) {
        if(Object.hasOwnProperty(attributes,property)) continue;
        element.setAttribute(property, attributes[property]);
      }
    }
    // element innerHTML
    if(content) { element.innerHTML = content; }
    return Toolkit.update(element);
  };

  /**
   * universal ajax get
   */
  window["get"] = function(url, callback) {
    var xhr = new XMLHttpRequest();
    if(callback) {
      xhr.onreadystatechange = function() { if(xhr.readyState === 4) { callback(xhr); }}
    }
    xhr.open("GET", url, (callback? true : false));
    xhr.send(null);
    if(!callback) { return xhr.responseText; }
  };

  /**
   * 'does e exist?' evaluation function
   */
  window["exists"] = (function(_) { return function(e) { return (e!==_) && (e!==null); }}());

  /*
   * class list container, for modifying html element class attributes
   */
  var ClassList = function(owner) {
    var classAttr = owner.getAttribute("class");
    var classes = (!classAttr ? [] : classAttr.split(/\s+/));
    var __update = function() {
      owner.setAttribute("class", classes.join(" "));
    };
    this.add = function(clstring) {
      if(classes.indexOf(clstring)===-1) { classes.push(clstring); }
      __update();
      return owner;
    };
    this.remove = function(clstring) {
      var pos = classes.indexOf(clstring);
      if(pos>-1) { classes.splice(pos, 1); __update(); }
      return owner;
    };
    this.contains = function(clstring) {
      return (classes.indexOf(clstring) !== -1);
    };
  };

  // shorthand "try to bind" function
  var bind = function(e, name, func) {
    if(!exists(e[name])) {
      e[name] = func;
    }
  };

  /**
   * extend HTML elements with a few useful (chainable) functions
   */
  var extend = function(e) {

    // shortcut: don't extend if element is nothing
    if(!exists(e)) return;

    // shortcut 2: don't extend if extended
    if(exists(e["__ttk_extended"])) return e;

    /**
     * contextual finding
     */
    bind(e, "find", function(selector) {
      return find(e, selector);
    });

    /**
     * get/set css properties
     */
    bind(e, "css", function(prop, val) {
      if(val && val!=="") { e.style[prop] = val; return e; }
      if(val==="") {
        var s = e.get("style");
        if(s) {
          s = s.replace(new RegExp(prop+"\\s*:\\s*"+val,''),'');
          e.set("style",s);
        }
        return e;
      }
      if(!val && typeof prop === "object") {
        for(p in prop) {
          if(Object.hasOwnProperty(prop,p)) continue;
          e.css(p,prop[p]); }
        return e;
      }
      return document.defaultView.getComputedStyle(e,null).getPropertyValue(prop) || e.style[prop];
    });

    /**
     * common dimensions
     */
    bind(e, "position", function() { return e.getBoundingClientRect(); });

    /**
     * HTML element class manipulation
     */
    bind(e, "classes", function() {
      if(!e.__ttk_clobj) {
        e.__ttk_clobj = new ClassList(e);
      }
      return e.__ttk_clobj;
    });

    /**
     * show/hide
     */
    bind(e, "show", function(yes) {
      if(yes) { e.removeAttribute("data-tiny-toolkit-hidden"); }
      else { e.set("data-tiny-toolkit-hidden",""); }
      return e;
    });

    bind(e, "toggle", function() {
      e.show(exists(e.get("data-tiny-toolkit-hidden")));
      return e;
    });

    /**
     * get/set inner HTML
     */
    bind(e, "html", function(html) {
      if(exists(html)) {
        e.innerHTML = html;
        return e;
      }
      return e.innerHTML;
    });

    /**
     * get (extend()ed) parent
     */
    bind(e, "parent", function() {
      return extend(e.parentNode);
    });

    /**
     * add a child element
     */
    bind(e, "add", function() {
      for(var i=0, last=arguments.length; i<last; i++) {
        if(exists(arguments[i])) {
          e.appendChild(arguments[i]);
        }
      }
      return e;
    });

    /**
     * replace a child element, with logical old/new ordering
     */
    bind(e, "replace", function(o,n) {
      if(exists(o.parentNode)) {
        o.parentNode.replaceChild(n,o);
      }
      return n;
    });

    /**
     * remove self from parent, or child element (either by number or reference)
     */
    bind(e, "remove", function(a) {
      // remove self
      if(!a) { e.parentNode.removeChild(e); return; }
      // remove child by number
      if(parseInt(a)==a) { e.removeChild(e.children[a]); }
      // remove child by reference
      else{ e.removeChild(a); }
      return e;
    });

    /**
     * clear all children
     */
    bind(e, "clear", function() {
      while(e.children.length>0) {
        e.remove(e.get(0));
      }
      return e;
    });

    /**
     * get object property values
     */
    bind(e, "get", function(a) {
      if(a == parseInt(a)) {
        return extend(e.children[a]);
      }
      return e.getAttribute(a);
    });

    /**
     * set object property values
     */
    bind(e, "set", function(a,b) {
      if(!exists(b)) {
        for(prop in a) {
          if(!Object.hasOwnProperty(a,prop)) {
            e.setAttribute(prop,a[prop]);
          }
        }
      }
      else { e.setAttribute(a,b); }
      return e;
    });

    /**
     * One-time event listening
     * (with automatic cleanup)
     */
    bind(e, "listenOnce", function(s, f, b) {
      var _ = function() {
        e.removeEventListener(s, _, b|false);
        f.call(arguments);
      };
      e.addEventListener(s, _, b|false);
      return e;
    });

    /**
     * Permanent event listening
     */
    bind(e, "listen", function(s, f, b) {
      e.addEventListener(s, f, b|false);
      return e;
    });

    /**
     * homogenise with set API
     */
    bind(e, "do", function(f) { f(e); return e; });
    e.length = 1;

    // chaining return
    e["__ttk_extended"] = true;
    return e;
  };

  /**
   * universal toolkit extend function
   */
  window["extend"] = extend;

  // shorthand passthrough function
  var passThrough = function(elements, ns, functor, arguments) {
    for(var i=0, last=elements.length; i<last; i++) {
      window["extend"](exists(ns) ? elements[i][ns]() : elements[i])[functor].apply(elements[i], arguments);
    }
    return elements;
  };

  // used in extendSet and find
  var emptySet = [], noop = function() { return emptySet; };
  emptySet["classes"] = { add: noop, remove: noop };
  emptySet["remove"] = noop;
  emptySet["do"] = noop;

  /**
   * API-extend this array for functions that make sense
   */
  var extendSet = function(elements) {
    // passthrough functions
    var passThroughList = ["css", "show", "toggle", "set", "listen", "listenOnce"],
        last = passThroughList.length, i, term;

    // set up all passthroughs
    for(i=0; i<last; i++) {
      term = passThroughList[i];
      elements[term] = (function(functor) {
        return function() {
          return passThrough(elements, null, functor, arguments);
        };
      }(term));
      emptySet[term] = noop;
    }

    // passthrough with explicit namespace for classes
    var classobj = {
      add: function() {
        return passThrough(elements, "classes", "add", arguments);
      },
      remove: function() {
        return passThrough(elements, "classes", "remove", arguments);
      }
    };

    elements["classes"] = function() {
      return classobj;
    };

    // passthrough, but return empty list
    elements["remove"] = function() {
      passThrough(elements, "remove", arguments); return emptySet;
    };

    // different kind of pass-through
    elements["do"] = function(f) {
      for(var i=0, last=elements.length; i<last; i++) {
        f(elements[i]);
      }
      return elements;
    };

    // chaining return
    return elements;
  };

  /**
   * The thing that makes it all happen
   */
  var find = function(context, selector) {
    var nodeset = context.querySelectorAll(selector),
        elements = [];
    if(nodeset.length==0) return emptySet;
    // single?
    if(nodeset.length==1) { return window["extend"](nodeset[0]); }
    // multiple results
    for(var i=0, last=nodeset.length; i<last; i++) {
      elements[i] = window["extend"](nodeset[i]); }
    return extendSet(elements);
  };

  /**
   * set up a special CSS rule for hiding elements. Rather than change the
   * element's CSS properties, we simply tack this attribute onto any element
   * that needs to not be shown, or remove it to reveal the element again.
   */
  (function(){
    var ttkh = create("style", {type: "text/css"}, "*[data-tiny-toolkit-hidden]{display:none!important;visibility:hidden!important;opacity:0!important;}");
    document.head.appendChild(ttkh); }());

  /**
   * extend document and body, since they're just as HTML-elementy as everything else
   */
  window["extend"](document).listenOnce("DOMContentLoaded", function() { window["extend"](body); });

  /**
   * univeral element selector
   */
  window["find"] = function(selector) { return find(document,selector); };

}(window,document,document.body));