(function () {
  'use strict';
  function init(root) {
    if (!root || root.dataset.faMegaReady) return;
    root.dataset.faMegaReady = '1';
    var trigger = root.querySelector('.fa-mega-trigger');
    var dropdown = root.querySelector('.fa-mega-dropdown');
    var closeButton = root.querySelector('.fa-mega-close');
    var tabs = root.querySelectorAll('.fa-mega-cat');
    function open() { root.classList.add('is-open'); trigger.setAttribute('aria-expanded', 'true'); dropdown.setAttribute('aria-hidden', 'false'); }
    function close() { root.classList.remove('is-open'); trigger.setAttribute('aria-expanded', 'false'); dropdown.setAttribute('aria-hidden', 'true'); }
    function activate(tab) {
      tabs.forEach(function (item) { var panel = root.querySelector('#' + item.dataset.panel); var active = item === tab; item.classList.toggle('is-active', active); item.setAttribute('aria-selected', active ? 'true' : 'false'); if (panel) { panel.classList.toggle('is-active', active); panel.hidden = !active; } });
    }
    trigger.addEventListener('click', function () { root.classList.contains('is-open') ? close() : open(); });
    root.addEventListener('mouseenter', function () { if (window.matchMedia('(hover: hover)').matches) open(); });
    root.addEventListener('mouseleave', function () { if (window.matchMedia('(hover: hover)').matches) close(); });
    closeButton.addEventListener('click', function () { close(); trigger.focus(); });
    tabs.forEach(function (tab) { tab.addEventListener('click', function () { activate(tab); }); tab.addEventListener('mouseenter', function () { if (window.matchMedia('(hover: hover)').matches) activate(tab); }); });
    root.addEventListener('keydown', function (event) { if (event.key === 'Escape') { close(); trigger.focus(); } });
    document.addEventListener('click', function (event) { if (!root.contains(event.target)) close(); });
  }
  function boot(scope) { (scope || document).querySelectorAll('[data-fa-mega-menu]').forEach(init); }
  document.addEventListener('DOMContentLoaded', function () { boot(); });
  window.addEventListener('elementor/frontend/init', function () { if (window.elementorFrontend) elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) { boot($scope[0]); }); });
}());
