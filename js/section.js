// Generated by CoffeeScript 1.10.0

/*
  Deals with sections (used in edit/view)
 */

(function() {
  var Section;

  Section = (function() {
    function Section() {}

    Section.init = function() {
      return this.sectionNumberToPanels(this.computeSectionNumber());
    };


    /*
      Changes the url in the address bar according to the new parameters.
     */

    Section.updateUrl = function(parameters) {
      return $.ajax("api/url.php", {
        type: 'GET',
        data: {
          parameters: parameters
        },
        dataType: 'text',
        success: (function(_this) {
          return function(url) {
            return window.history.pushState("", "", url);
          };
        })(this)
      });
    };

    Section.performToggle = function(element) {
      var sectionNumber;
      this.toggleSection(element, 'slow');
      sectionNumber = this.panelsToSectionNumber(element);
      this.sectionNumberToPanels(sectionNumber);
      this.updateUrl({
        params: window.current.parameters,
        section: sectionNumber
      });
      return this.storeSectionNumber(sectionNumber);
    };


    /*
      Collapses a section
     */

    Section.hideSection = function(element, speed) {
      element.find(".glyphicon").removeClass("glyphicon-chevron-down").addClass('glyphicon-chevron-right');
      element.siblings('.panel-collapse').hide(speed);
      return $(element).addClass('collapsed');
    };


    /*
     Expands a section
     */

    Section.showSection = function(element, speed) {
      element.find(".glyphicon").removeClass("glyphicon-chevron-right").addClass('glyphicon-chevron-down');
      element.siblings('.panel-collapse').show(speed);
      return $(element).removeClass('collapsed');
    };


    /*
      Collapses/Expands a section.
     */

    Section.toggleSection = function(element, speed) {
      element.find(".glyphicon").toggleClass("glyphicon-chevron-right glyphicon-chevron-down");
      element.siblings('.panel-collapse').toggle(speed);
      return $(element).toggleClass('collapsed');
    };


    /*
      Computes a section number from expansion state of panels.
     */

    Section.panelsToSectionNumber = function(element) {
      var counter, section;
      section = 0;
      counter = 1;
      element.closest('.scope').find('.panel-heading').each(function() {
        if (!$(this).hasClass('collapsed')) {
          section += counter;
        }
        return counter *= 2;
      });
      return section;
    };


    /*
      Expands/collapses panels according to the section number.
     */

    Section.sectionNumberToPanels = function(sectionNumber) {
      return $('.scope').each(function() {
        var code, panel, panelNumber, panels, results, sec;
        panels = $(this).find('.panel-heading');
        panelNumber = panels.length - 1;
        sec = sectionNumber;
        Section.showSection(panels, 0);
        results = [];
        while (sec >= 0 && panelNumber >= 0) {
          code = Math.pow(2, panelNumber);
          if (sec >= code) {
            sec -= code;
          } else {
            panel = $(panels[panelNumber]);
            Section.hideSection(panel, 0);
          }
          results.push(panelNumber--);
        }
        return results;
      });
    };


    /*
      Extract the correct section number
     */

    Section.computeSectionNumber = function() {
      var section;
      if (((section = window.current.section) != null)) {
        return section;
      } else if (localStorage && ((section = localStorage.getItem('section_' + window.current.action)) != null)) {
        return section;
      } else {
        return window.defaults.section;
      }
    };


    /*
      Stores a section number into the browser's local storage.
     */

    Section.storeSectionNumber = function(sectionNumber) {
      return localStorage.setItem('section_' + window.current.action, sectionNumber);
    };

    return Section;

  })();

  Section.init();

  $(function() {
    return $('.panel-heading').click(function() {
      return Section.performToggle($(this));
    });
  });

}).call(this);
