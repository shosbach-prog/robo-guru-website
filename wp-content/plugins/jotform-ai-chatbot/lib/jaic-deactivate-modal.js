/* eslint-disable prefer-destructuring */
// eslint-disable-next-line no-undef
const pluginSlug = jaicPluginData.pluginSlug;
// const pluginSlug = 'jotform-ai-chatbot';
document.addEventListener('DOMContentLoaded', () => {
  const deactivateRow = document.querySelector(`tr[data-slug="${pluginSlug}"]`);
  const deactivateLink = deactivateRow.querySelector('.deactivate #deactivate-jotform-ai-chatbot');
  const form = document.getElementById('jaic_deactivate_form');
  const submitButton = document.querySelector('.jaic.primary');
  const cancelButton = document.querySelector('.jaic.secondary');
  const hiddenIframe = document.getElementById('jaic_hidden_iframe');
  const allRadioInputs = document.querySelectorAll('input[name="q4_feedback"]');
  const detailWrapper = document.getElementById('jaic_detail_text_wrapper');
  const detailInput = document.getElementById('jaic_detail_text');

  const placeholders = {
    "jaic_not_working": "Please describe where it failed",
    "jaic_missing_features": "Which features are missing for you?",
    "jaic_better_alternative": "Which alternative did you switch to?",
    "jaic_other": "Tell us more…"
  };

  const triggerReasons = ["not_working", "missing_features", "better_alternative", "other"];

  let deactivateUrl = '';

  document.addEventListener('click', (e) => {
    if (e.target.closest(`tr[data-slug="${pluginSlug}"] .deactivate a`)) {
      deactivateUrl = e.target.href;
      document.querySelector('.jaic_modal').style.display = 'flex';
    }
  });

  if (deactivateLink) {
    deactivateLink.addEventListener('click', (e) => {
      // deactivateLink.innerHTML = 'disabled';
      e.preventDefault();
      deactivateUrl = deactivateLink.href;
      document.querySelector('.jaic_modal').style.display = 'flex';
    });
  }

  function hideDetailInput() {
    detailWrapper.style.display = 'none';
    detailInput.value = '';
    detailInput.placeholder = '';
    detailInput.required = false;
    detailInput.setAttribute('aria-required', 'false');
  }

  allRadioInputs.forEach(radio => {
    radio.addEventListener('change', () => {
      hideDetailInput();

      const reasonId = radio.id.replace("jaic_", "");
      if (triggerReasons.includes(reasonId)) {
        detailWrapper.style.display = 'flex';
        detailInput.placeholder = placeholders[radio.id];
        const optionElement = radio.closest('.jaic_option');
        optionElement.insertAdjacentElement('afterend', detailWrapper);

        const ph = placeholders[radio.id] || '';
        detailInput.placeholder = ph;
        detailWrapper.style.display = 'flex';

        const shouldRequire = (reasonId === 'other');
        detailInput.required = shouldRequire;
        detailInput.setAttribute('aria-required', shouldRequire ? 'true' : 'false');
      }

      updateRequirementError();
    });
  });

  function updateRequirementError() {
    const selectedRadio = Array.from(allRadioInputs).find(i => i.checked);
    const isRadioSelected = !!selectedRadio;
    const selectedReason = selectedRadio ? selectedRadio.id.replace('jaic_', '') : null;
    const requiresDetailForOther = selectedReason === 'other';
    const detailFilled = detailInput.value.trim() !== '';

    if (!isRadioSelected) {
      submitButton.classList.add('disabled');
      return;
    }

    if (requiresDetailForOther && !detailFilled) {
      submitButton.classList.add('disabled');
      return;
    }

    submitButton.classList.remove('disabled');
  }

  detailInput.addEventListener('input', updateRequirementError);

  form.addEventListener('submit', (e) => {
    updateRequirementError();

    if (submitButton.classList.contains('disabled')) {
      e.preventDefault();
      return;
    }

    submitButton.classList.add('disabled');
    cancelButton.classList.add('disabled');
    form.style.pointerEvents = 'none';
    submitButton.querySelector('.jaic_text').style.opacity = '0';
    submitButton.querySelector('.jaic_loader').style.opacity = '1';

    setTimeout(() => {
      form.reset();
      hideDetailInput();
      window.location.href = deactivateUrl;
    }, 1500);
  });

  if (cancelButton) {
    cancelButton.addEventListener('click', () => {
      if (form) {
        form.reset();
      }
      document.querySelector('.jaic_modal').style.display = 'none';
    });
  }

  hideDetailInput();
  updateRequirementError();
});
