(function () {
  'use strict';
  function init(root) {
    if (!root || root.dataset.faMobileNavReady) return;
    root.dataset.faMobileNavReady = '1';
    var levels = root.querySelectorAll('.fa-mobile-nav-level');
    var back = root.querySelector('.fa-mobile-nav-back');
    var crumb = root.querySelector('.fa-mobile-nav-crumb');
    var home = root.dataset.homeLabel || 'خانه';
    var trail = [];

    function show(id) {
      levels.forEach(function (level) { level.hidden = level.dataset.level !== id; });
      back.hidden = trail.length === 0;
      crumb.textContent = [home].concat(trail.map(function (item) { return item.label; })).join(' > ');
    }

    root.addEventListener('click', function (event) {
      var item = event.target.closest('[data-target]');
      if (!item || !root.contains(item)) return;
      event.preventDefault();
      root.querySelectorAll('.fa-mobile-nav-item.is-selected').forEach(function (selected) { selected.classList.remove('is-selected'); });
      item.classList.add('is-selected');
      trail.push({ id: item.dataset.target, label: item.dataset.label || '' });
      show(item.dataset.target);
    });

    back.addEventListener('click', function () {
      trail.pop();
      root.querySelectorAll('.fa-mobile-nav-item.is-selected').forEach(function (selected) { selected.classList.remove('is-selected'); });
      if (trail.length) {
        var previous = root.querySelector('[data-target="' + trail[trail.length - 1].id + '"]');
        if (previous) previous.classList.add('is-selected');
      }
      show(trail.length ? trail[trail.length - 1].id : 'root');
    });
    show('root');
  }
  function boot(scope) { (scope || document).querySelectorAll('[data-fa-mobile-nav]').forEach(init); }
  document.addEventListener('DOMContentLoaded', function () { boot(); });
  window.addEventListener('elementor/frontend/init', function () { if (window.elementorFrontend) elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) { boot($scope[0]); }); });
}());
