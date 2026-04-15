(function () {
  const root = document.querySelector('.ru-weekly-menu-admin');

  if (!root) {
    return;
  }

  const unitSelect = root.querySelector('select[name="ru_unit_id"]');
  const mealCheckboxes = Array.from(root.querySelectorAll('[data-meal-toggle]'));
  let canPrefillFromUnit = root.dataset.prefillFromUnit === '1';

  function getSelectedUnitMeals() {
    if (!unitSelect) {
      return [];
    }

    const option = unitSelect.options[unitSelect.selectedIndex];

    if (!option || !option.dataset.enabledMeals) {
      return [];
    }

    try {
      return JSON.parse(option.dataset.enabledMeals);
    } catch (error) {
      return [];
    }
  }

  function syncMealPanels() {
    mealCheckboxes.forEach((checkbox) => {
      const panel = root.querySelector('[data-meal-panel="' + checkbox.value + '"]');

      if (!panel) {
        return;
      }

      panel.classList.toggle('is-hidden', !checkbox.checked);
    });
  }

  function applyUnitMealDefaults() {
    const enabledMeals = getSelectedUnitMeals();

    mealCheckboxes.forEach((checkbox) => {
      checkbox.checked = enabledMeals.includes(checkbox.value);
    });

    syncMealPanels();
  }

  mealCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', () => {
      canPrefillFromUnit = false;
      syncMealPanels();
    });
  });

  if (unitSelect) {
    unitSelect.addEventListener('change', () => {
      if (canPrefillFromUnit) {
        applyUnitMealDefaults();
      }
    });
  }

  root.addEventListener('click', (event) => {
    const addButton = event.target.closest('.ru-add-weekly-menu-item');
    const removeButton = event.target.closest('.ru-remove-weekly-menu-item');

    if (addButton) {
      const section = addButton.closest('.ru-weekly-menu-section');
      const container = section ? section.querySelector('[data-items-container]') : null;
      const template = section ? section.querySelector('.ru-weekly-menu-item-template') : null;

      if (!container || !template) {
        return;
      }

      event.preventDefault();

      const nextIndex = container.querySelectorAll('[data-item-row]').length;
      const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex));
      container.insertAdjacentHTML('beforeend', html);
      return;
    }

    if (removeButton) {
      const row = removeButton.closest('[data-item-row]');

      if (row) {
        event.preventDefault();
        row.remove();
      }
    }
  });

  syncMealPanels();
})();
