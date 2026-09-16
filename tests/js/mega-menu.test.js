import { beforeAll, describe, expect, it } from 'vitest';

describe('mega menu', () => {
  let root;
  let trigger;
  let dropdown;
  let closeButton;
  let secondTab;
  let secondPanel;

  beforeAll(async () => {
    window.matchMedia = () => ({ matches: false });
    document.body.innerHTML = `
      <nav data-fa-mega-menu>
        <button class="fa-mega-trigger" aria-expanded="false"></button>
        <section class="fa-mega-dropdown" aria-hidden="true">
          <button class="fa-mega-close"></button>
          <button class="fa-mega-cat is-active" data-panel="panel-one" aria-selected="true"></button>
          <button class="fa-mega-cat" data-panel="panel-two" aria-selected="false"></button>
          <div id="panel-one"></div>
          <div id="panel-two" hidden></div>
        </section>
      </nav>`;

    await import('../../modules/mega-menu/Assets/js/mega-menu.js');
    document.dispatchEvent(new Event('DOMContentLoaded'));

    root = document.querySelector('[data-fa-mega-menu]');
    trigger = root.querySelector('.fa-mega-trigger');
    dropdown = root.querySelector('.fa-mega-dropdown');
    closeButton = root.querySelector('.fa-mega-close');
    secondTab = root.querySelectorAll('.fa-mega-cat')[1];
    secondPanel = root.querySelector('#panel-two');
  });

  it('opens and closes with accessible state', () => {
    trigger.click();
    expect(root.classList.contains('is-open')).toBe(true);
    expect(trigger.getAttribute('aria-expanded')).toBe('true');
    expect(dropdown.getAttribute('aria-hidden')).toBe('false');

    closeButton.click();
    expect(root.classList.contains('is-open')).toBe(false);
    expect(trigger.getAttribute('aria-expanded')).toBe('false');
    expect(dropdown.getAttribute('aria-hidden')).toBe('true');
  });

  it('activates the selected category panel', () => {
    secondTab.click();
    expect(secondTab.classList.contains('is-active')).toBe(true);
    expect(secondTab.getAttribute('aria-selected')).toBe('true');
    expect(secondPanel.hidden).toBe(false);
  });
});
