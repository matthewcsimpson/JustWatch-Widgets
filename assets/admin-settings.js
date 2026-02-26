(() => {
  const bindToggle = (toggleId, targetId) => {
    const toggleElement = document.getElementById(toggleId);
    const targetElement = document.getElementById(targetId);
    if (!toggleElement || !targetElement) return;

    const sync = () => {
      targetElement.disabled = !toggleElement.checked;
    };

    toggleElement.addEventListener("change", sync);
    sync();
  };

  const bindOfferLabelPreview = () => {
    const selectElement = document.getElementById("jw_widgets_offer_label");
    const previewImageElement = document.getElementById(
      "jw_widgets_offer_label_preview",
    );
    if (!selectElement || !previewImageElement) return;

    const syncPreview = () => {
      if (selectElement.value === "price") {
        previewImageElement.src = previewImageElement.dataset.previewPrice;
        return;
      }

      if (selectElement.value === "none") {
        previewImageElement.src = previewImageElement.dataset.previewNone;
        return;
      }

      previewImageElement.src = previewImageElement.dataset.previewType;
    };

    selectElement.addEventListener("change", syncPreview);
    syncPreview();
  };

  const bindScalePreview = () => {
    const selectElement = document.getElementById("jw_widgets_scale");
    const previewElement = document.getElementById("jw_icon_preview");
    if (!selectElement || !previewElement) return;

    const baseFontSizePx = 11.56;

    const update = () => {
      let scale = parseFloat(selectElement.value || "1.0");
      if (!Number.isFinite(scale) || scale <= 0) scale = 1.0;
      previewElement.style.fontSize = baseFontSizePx * scale + "px";
    };

    selectElement.addEventListener("change", update);
    update();
  };

  const bindHeadingRowsToggle = () => {
    const toggleElement = document.getElementById("jw_widgets_show_heading");
    if (!toggleElement) return;

    const headingFieldIds = [
      "jw_widgets_heading_text",
      "jw_widgets_heading_level",
      "jw_widgets_heading_outside_border",
    ];

    const resolveRow = (fieldId) => {
      const labelElement = document.querySelector(`label[for="${fieldId}"]`);
      if (labelElement && labelElement.closest("tr"))
        return labelElement.closest("tr");

      const fieldElement = document.getElementById(fieldId);
      if (!fieldElement) return null;
      return fieldElement.closest("tr");
    };

    const toggleRows = () => {
      headingFieldIds.forEach((fieldId) => {
        const rowElement = resolveRow(fieldId);
        if (!rowElement) return;
        rowElement.style.display = toggleElement.checked ? "" : "none";
      });
    };

    toggleElement.addEventListener("change", toggleRows);
    toggleRows();
  };

  const bindHeadingPreview = () => {
    const selectElement = document.getElementById(
      "jw_widgets_heading_outside_border",
    );
    const previewImageElement = document.getElementById(
      "jw_widgets_heading_position_preview",
    );
    if (!selectElement || !previewImageElement) return;

    const syncPreview = () => {
      const isOutside = selectElement.value === "1";
      previewImageElement.src = isOutside
        ? previewImageElement.dataset.previewOutside
        : previewImageElement.dataset.previewInside;
    };

    selectElement.addEventListener("change", syncPreview);
    syncPreview();
  };

  const init = () => {
    bindToggle("jw_widgets_language_override_enabled", "jw_widgets_language");
    bindToggle("jw_widgets_max_offers_enabled", "jw_widgets_max_offers");
    bindToggle("jw_widgets_border_enabled", "jw_widgets_border_colour");
    bindToggle(
      "jw_widgets_label_colour_override_enabled",
      "jw_widgets_label_colour",
    );
    bindOfferLabelPreview();
    bindScalePreview();
    bindHeadingRowsToggle();
    bindHeadingPreview();
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
