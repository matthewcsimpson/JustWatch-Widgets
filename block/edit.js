import { InspectorControls, useBlockProps } from "@wordpress/block-editor";
import {
  PanelBody,
  SelectControl,
  TextControl,
  TextareaControl,
  ToggleControl,
  Notice,
} from "@wordpress/components";
import { Fragment, useMemo } from "@wordpress/element";

const Edit = ({ attributes, setAttributes }) => {
  const {
    objectType,
    idType,
    externalId,
    overridesEnabled,
    overridesInitialized,
  } = attributes;

  const editorDefaults = {
    offerLabel: "",
    scale: "1.0",
    maxOffersEnabled: false,
    maxOffers: "10",
    languageEnabled: false,
    language: "en",
    showHeading: true,
    headingText: "Now streaming on:",
    headingLevel: "h3",
    headingPosition: "inside",
    borderEnabled: true,
    borderColour: "#dcdcdc",
    textColourOverrideEnabled: false,
    textColour: "",
    noOffersMessage:
      "There are no links for {{title}} right now, but check back soon!",
    titleNotFoundMessage:
      "There are no links for this title right now, but check back soon!",
    ...(window.jwWidgetsGlobalDefaults || {}),
  };

  const blockProps = useBlockProps();

  const isValid = useMemo(() => {
    if (!externalId) return false;
    if (!["movie", "show"].includes(objectType)) return false;
    if (!["tmdb", "imdb"].includes(idType)) return false;
    return true;
  }, [objectType, idType, externalId]);

  const controls = (
    <>
      <SelectControl
        label="Type"
        value={objectType}
        options={[
          { label: "Movie", value: "movie" },
          { label: "TV Series", value: "show" },
        ]}
        onChange={(value) => setAttributes({ objectType: value })}
      />

      <SelectControl
        label="ID Type"
        value={idType}
        options={[
          { label: "IMDB", value: "imdb" },
          { label: "TMDB", value: "tmdb" },
        ]}
        onChange={(value) => setAttributes({ idType: value })}
      />

      <TextControl
        label={idType === "imdb" ? "IMDB ID" : "TMDB ID"}
        value={externalId}
        onChange={(value) => setAttributes({ externalId: value.trim() })}
        placeholder={idType === "imdb" ? "tt1234567" : "603"}
        help={idType === "imdb" ? "Example: tt10548174" : "Example: 603"}
      />
    </>
  );

  const languageOptions = [
    { label: "Arabic", value: "ar" },
    { label: "Chinese", value: "zh" },
    { label: "Czech", value: "cs" },
    { label: "French", value: "fr" },
    { label: "German", value: "de" },
    { label: "Italian", value: "it" },
    { label: "Polish", value: "pl" },
    { label: "Portugese", value: "pt" },
    { label: "Romanian", value: "ro" },
    { label: "Russian", value: "ru" },
    { label: "Spanish", value: "es" },
  ];

  const scaleOptions = [
    "0.6",
    "0.7",
    "0.8",
    "0.9",
    "1.0",
    "1.1",
    "1.2",
    "1.3",
    "1.4",
    "1.5",
    "1.6",
    "1.7",
    "1.8",
    "1.9",
    "2.0",
  ].map((value) => ({
    label: `${Math.round(parseFloat(value) * 100)}%`,
    value,
  }));

  const maxOfferOptions = Array.from({ length: 20 }, (_, index) => {
    const value = String(index + 1);
    return { label: value, value };
  });

  const overridesControls = (
    <>
      <ToggleControl
        label="Enable per-block overrides"
        checked={!!overridesEnabled}
        onChange={(value) => {
          if (!value) {
            setAttributes({ overridesEnabled: false });
            return;
          }

          if (!overridesInitialized) {
            setAttributes({
              overridesEnabled: true,
              overridesInitialized: true,
              overrideOfferLabel: editorDefaults.offerLabel,
              overrideScale: editorDefaults.scale,
              overrideMaxOffersEnabled: !!editorDefaults.maxOffersEnabled,
              overrideMaxOffers: editorDefaults.maxOffers,
              overrideLanguageEnabled: !!editorDefaults.languageEnabled,
              overrideLanguage: editorDefaults.language,
              overrideShowHeading: !!editorDefaults.showHeading,
              overrideHeadingText: editorDefaults.headingText,
              overrideHeadingLevel: editorDefaults.headingLevel,
              overrideHeadingPosition: editorDefaults.headingPosition,
              overrideBorderEnabled: !!editorDefaults.borderEnabled,
              overrideBorderColour: editorDefaults.borderColour,
              overrideTextColourOverrideEnabled:
                !!editorDefaults.textColourOverrideEnabled,
              overrideTextColour: editorDefaults.textColour,
              overrideNoOffersMessage: editorDefaults.noOffersMessage,
              overrideTitleNotFoundMessage: editorDefaults.titleNotFoundMessage,
            });
            return;
          }

          setAttributes({ overridesEnabled: true });
        }}
      />

      {overridesEnabled && (
        <>
          <SelectControl
            label="Offer Label"
            value={attributes.overrideOfferLabel || ""}
            options={[
              { label: "Use global default", value: "" },
              { label: "Monetization Type", value: "monetization_type" },
              { label: "Price", value: "price" },
              { label: "None", value: "none" },
            ]}
            onChange={(value) => setAttributes({ overrideOfferLabel: value })}
          />

          <SelectControl
            label="Icon Size"
            value={attributes.overrideScale || ""}
            options={[
              { label: "Use global default", value: "" },
              ...scaleOptions,
            ]}
            onChange={(value) => setAttributes({ overrideScale: value })}
          />

          <ToggleControl
            label="Override max streaming services"
            checked={!!attributes.overrideMaxOffersEnabled}
            onChange={(value) =>
              setAttributes({ overrideMaxOffersEnabled: value })
            }
          />

          {attributes.overrideMaxOffersEnabled && (
            <SelectControl
              label="Max Streaming Services"
              value={attributes.overrideMaxOffers || "10"}
              options={maxOfferOptions}
              onChange={(value) => setAttributes({ overrideMaxOffers: value })}
            />
          )}

          <ToggleControl
            label="Override language"
            checked={!!attributes.overrideLanguageEnabled}
            onChange={(value) =>
              setAttributes({ overrideLanguageEnabled: value })
            }
          />

          {attributes.overrideLanguageEnabled && (
            <SelectControl
              label="Language"
              value={attributes.overrideLanguage || "en"}
              options={languageOptions}
              onChange={(value) => setAttributes({ overrideLanguage: value })}
            />
          )}

          <ToggleControl
            label="Show Heading"
            checked={
              attributes.overrideShowHeading === undefined
                ? true
                : !!attributes.overrideShowHeading
            }
            onChange={(value) => setAttributes({ overrideShowHeading: value })}
          />

          {(attributes.overrideShowHeading === undefined ||
            attributes.overrideShowHeading) && (
            <>
              <TextControl
                label="Widget Heading Text"
                value={attributes.overrideHeadingText || ""}
                help="Leave blank to use global default."
                onChange={(value) =>
                  setAttributes({ overrideHeadingText: value })
                }
              />

              <SelectControl
                label="Widget Heading Tag"
                value={attributes.overrideHeadingLevel || ""}
                options={[
                  { label: "Use global default", value: "" },
                  { label: "H2", value: "h2" },
                  { label: "H3", value: "h3" },
                  { label: "H4", value: "h4" },
                  { label: "H5", value: "h5" },
                  { label: "H6", value: "h6" },
                  { label: "P", value: "p" },
                ]}
                onChange={(value) =>
                  setAttributes({ overrideHeadingLevel: value })
                }
              />

              <SelectControl
                label="Heading Position"
                value={attributes.overrideHeadingPosition || ""}
                options={[
                  { label: "Use global default", value: "" },
                  { label: "Inside Border", value: "inside" },
                  { label: "Outside Border", value: "outside" },
                ]}
                onChange={(value) =>
                  setAttributes({ overrideHeadingPosition: value })
                }
              />
            </>
          )}

          <ToggleControl
            label="Show Border"
            checked={!!attributes.overrideBorderEnabled}
            onChange={(value) =>
              setAttributes({ overrideBorderEnabled: value })
            }
          />

          {attributes.overrideBorderEnabled && (
            <TextControl
              label="Border Colour"
              value={attributes.overrideBorderColour || ""}
              placeholder="#dcdcdc"
              onChange={(value) =>
                setAttributes({ overrideBorderColour: value })
              }
            />
          )}

          <ToggleControl
            label="Override Label Colour"
            checked={!!attributes.overrideTextColourOverrideEnabled}
            onChange={(value) =>
              setAttributes({ overrideTextColourOverrideEnabled: value })
            }
          />

          {attributes.overrideTextColourOverrideEnabled && (
            <TextControl
              label="Label Colour"
              value={attributes.overrideTextColour || ""}
              placeholder="#000000"
              onChange={(value) => setAttributes({ overrideTextColour: value })}
            />
          )}

          <TextareaControl
            label="No Offers Message"
            value={attributes.overrideNoOffersMessage || ""}
            help="Leave blank to use global default. Supports {{title}}."
            onChange={(value) =>
              setAttributes({ overrideNoOffersMessage: value })
            }
          />

          <TextareaControl
            label="Title Not Found Message"
            value={attributes.overrideTitleNotFoundMessage || ""}
            help="Leave blank to use global default."
            onChange={(value) =>
              setAttributes({ overrideTitleNotFoundMessage: value })
            }
          />
        </>
      )}
    </>
  );

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title="JustWatch Widget" initialOpen={true}>
          {controls}
        </PanelBody>

        <PanelBody title="Overrides" initialOpen={false}>
          {overridesControls}
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div
          style={{
            border: "1px solid #ddd",
            padding: "12px",
            borderRadius: "4px",
          }}
        >
          <strong>JustWatch Widget</strong>

          <div style={{ marginTop: 12 }}>{controls}</div>

          {!isValid && (
            <Notice status="warning" isDismissible={false}>
              Enter a valid type and ID.
            </Notice>
          )}

          {isValid && (
            <Notice status="success" isDismissible={false}>
              ✅ 🎥
            </Notice>
          )}
        </div>
      </div>
    </Fragment>
  );
};

export default Edit;
