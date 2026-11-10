# Codebase metrics

A measured snapshot of the Domain codebase and its submodules,
counted with [`cloc`](https://github.com/AlDanial/cloc). Charts show *code* lines
(blank and comment lines excluded) unless stated otherwise.

!!! note "Snapshot"
    Measured on the `3.x` branch, 2026-07-02. **450 files**,
    **32,316 lines of code**, 12,787 comment lines,
    6,788 blank (51,891 lines in total). See
    [Methodology](#methodology) to reproduce.

## At a glance

| Code | Comment | Blank | Files | Production | Test |
|-----:|--------:|------:|------:|-----------:|-----:|
| 32,316 | 12,787 | 6,788 | 450 | 21,065 | 11,251 |

## Production vs test

Domain ships **11,251** lines of test code backing **21,065** lines of
production code, a **53%** test-to-production ratio.

<div style="margin:1.6rem 0">
<svg viewBox="0 0 560 46" width="100%" role="img" style="display:block;width:100%;height:auto;font-family:ui-monospace,SFMono-Regular,Menlo,monospace">
<rect x="0.0" y="0" width="365.0" height="46" fill="#b07d22"></rect>
<text x="12.0" y="18" font-size="12.5" fill="#fff" font-weight="600">Production</text>
<text x="12.0" y="34" font-size="12" fill="#fff" opacity="0.9">21,065 &#183; 65%</text>
<rect x="365.0" y="0" width="195.0" height="46" fill="#4d7a88"></rect>
<text x="377.0" y="18" font-size="12.5" fill="#fff" font-weight="600">Test</text>
<text x="377.0" y="34" font-size="12" fill="#fff" opacity="0.9">11,251 &#183; 35%</text>
</svg>
</div>

## Lines by language

<div style="margin:1.6rem 0">
<svg viewBox="0 0 560 236" width="100%" role="img" style="display:block;width:100%;height:auto;font-family:ui-monospace,SFMono-Regular,Menlo,monospace">
<text x="190" y="20.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">PHP</text>
<rect x="200.0" y="11.0" width="294.0" height="17" rx="2.5" fill="#b07d22"></rect>
<text x="502.0" y="20.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">21,174</text>
<text x="190" y="48.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">Markdown</text>
<rect x="200.0" y="39.0" width="78.3" height="17" rx="2.5" fill="#6b7488"></rect>
<text x="286.3" y="48.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">5,642</text>
<text x="190" y="76.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">YAML</text>
<rect x="200.0" y="67.0" width="70.3" height="17" rx="2.5" fill="#4d7a88"></rect>
<text x="278.3" y="76.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">5,062</text>
<text x="190" y="104.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">CSS</text>
<rect x="200.0" y="95.0" width="3.3" height="17" rx="2.5" fill="#7a5a78"></rect>
<text x="211.3" y="104.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">236</text>
<text x="190" y="132.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">JavaScript</text>
<rect x="200.0" y="123.0" width="1.7" height="17" rx="2.5" fill="#a9663f"></rect>
<text x="209.7" y="132.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">122</text>
<text x="190" y="160.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">Text</text>
<rect x="200.0" y="151.0" width="0.5" height="17" rx="2.5" fill="#9aa3b0"></rect>
<text x="208.5" y="160.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">37</text>
<text x="190" y="188.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">JSON</text>
<rect x="200.0" y="179.0" width="0.3" height="17" rx="2.5" fill="#9aa3b0"></rect>
<text x="208.3" y="188.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">25</text>
<text x="190" y="216.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">Twig</text>
<rect x="200.0" y="207.0" width="0.2" height="17" rx="2.5" fill="#6b7488"></rect>
<text x="208.2" y="216.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">18</text>
</svg>
</div>

| Language | Code | Comment | Blank | Files |
|----------|-----:|--------:|------:|------:|
| PHP | 21,174 | 12,573 | 4,761 | 294 |
| Markdown | 5,642 | 0 | 1,873 | 37 |
| YAML | 5,062 | 66 | 71 | 110 |
| CSS | 236 | 69 | 69 | 1 |
| JavaScript | 122 | 65 | 14 | 4 |
| Text | 37 | 0 | 0 | 2 |
| JSON | 25 | 0 | 0 | 1 |
| Twig | 18 | 14 | 0 | 1 |

## Composition

Across all languages, code is 62% of the lines, comments
25%, and blank lines 13%. The
comment share reflects Drupal's docblock conventions.

<div style="margin:1.6rem 0">
<svg viewBox="0 0 560 46" width="100%" role="img" style="display:block;width:100%;height:auto;font-family:ui-monospace,SFMono-Regular,Menlo,monospace">
<rect x="0.0" y="0" width="348.7" height="46" fill="#b07d22"></rect>
<text x="12.0" y="18" font-size="12.5" fill="#fff" font-weight="600">Code</text>
<text x="12.0" y="34" font-size="12" fill="#fff" opacity="0.9">32,316 &#183; 62%</text>
<rect x="348.7" y="0" width="138.0" height="46" fill="#4d7a88"></rect>
<text x="360.7" y="18" font-size="12.5" fill="#fff" font-weight="600">Comment</text>
<text x="360.7" y="34" font-size="12" fill="#fff" opacity="0.9">12,787 &#183; 25%</text>
<rect x="486.7" y="0" width="73.3" height="46" fill="#9aa3b0"></rect>
</svg>
</div>

## By submodule

Production (brass) and test (steel) code per submodule, largest first.

<div style="margin:1.6rem 0">
<svg viewBox="0 0 560 180" width="100%" role="img" style="display:block;width:100%;height:auto;font-family:ui-monospace,SFMono-Regular,Menlo,monospace">
<text x="240" y="18.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">domain</text>
<rect x="250.0" y="11.0" width="134.7" height="13" rx="2.5" fill="#b07d22"></rect>
<rect x="384.7" y="11.0" width="109.3" height="13" rx="2.5" fill="#4d7a88"></rect>
<text x="502.0" y="18.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">8,998</text>
<text x="240" y="42.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">domain_access</text>
<rect x="250.0" y="35.0" width="54.5" height="13" rx="2.5" fill="#b07d22"></rect>
<rect x="304.5" y="35.0" width="47.5" height="13" rx="2.5" fill="#4d7a88"></rect>
<text x="360.0" y="42.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">3,762</text>
<text x="240" y="66.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">domain_content</text>
<rect x="250.0" y="59.0" width="81.4" height="13" rx="2.5" fill="#b07d22"></rect>
<rect x="331.4" y="59.0" width="7.1" height="13" rx="2.5" fill="#4d7a88"></rect>
<text x="346.6" y="66.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">3,266</text>
<text x="240" y="90.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">domain_source</text>
<rect x="250.0" y="83.0" width="38.0" height="13" rx="2.5" fill="#b07d22"></rect>
<rect x="288.0" y="83.0" width="48.9" height="13" rx="2.5" fill="#4d7a88"></rect>
<text x="344.9" y="90.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">3,204</text>
<text x="240" y="114.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">domain_alias</text>
<rect x="250.0" y="107.0" width="44.7" height="13" rx="2.5" fill="#b07d22"></rect>
<rect x="294.7" y="107.0" width="39.8" height="13" rx="2.5" fill="#4d7a88"></rect>
<text x="342.6" y="114.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">3,118</text>
<text x="240" y="138.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">domain_config_ui</text>
<rect x="250.0" y="131.0" width="33.1" height="13" rx="2.5" fill="#b07d22"></rect>
<rect x="283.1" y="131.0" width="28.8" height="13" rx="2.5" fill="#4d7a88"></rect>
<text x="319.9" y="138.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">2,281</text>
<text x="240" y="162.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">domain_config</text>
<rect x="250.0" y="155.0" width="24.5" height="13" rx="2.5" fill="#b07d22"></rect>
<rect x="274.5" y="155.0" width="23.6" height="13" rx="2.5" fill="#4d7a88"></rect>
<text x="306.1" y="162.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">1,772</text>
</svg>
</div>

| Submodule | Production | Test | Total |
|-----------|-----------:|-----:|------:|
| domain | 4,968 | 4,030 | 8,998 |
| domain_access | 2,010 | 1,752 | 3,762 |
| domain_content | 3,003 | 263 | 3,266 |
| domain_source | 1,400 | 1,804 | 3,204 |
| domain_alias | 1,649 | 1,469 | 3,118 |
| domain_config_ui | 1,219 | 1,062 | 2,281 |
| domain_config | 902 | 870 | 1,772 |

## Test suite

Domain ships **108** automated test classes
(247 test methods), counted by type below.

<div style="margin:1.6rem 0">
<svg viewBox="0 0 560 124" width="100%" role="img" style="display:block;width:100%;height:auto;font-family:ui-monospace,SFMono-Regular,Menlo,monospace">
<text x="190" y="20.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">Kernel</text>
<rect x="200.0" y="11.0" width="86.7" height="17" rx="2.5" fill="#4d7a88"></rect>
<text x="294.7" y="20.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">23</text>
<text x="190" y="48.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">Functional</text>
<rect x="200.0" y="39.0" width="294.0" height="17" rx="2.5" fill="#b07d22"></rect>
<text x="502.0" y="48.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">78</text>
<text x="190" y="76.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">FunctionalJavascript</text>
<rect x="200.0" y="67.0" width="22.6" height="17" rx="2.5" fill="#a9663f"></rect>
<text x="230.6" y="76.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">6</text>
<text x="190" y="104.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">Unit</text>
<rect x="200.0" y="95.0" width="3.8" height="17" rx="2.5" fill="#7d8a5f"></rect>
<text x="211.8" y="104.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">1</text>
</svg>
</div>

| Test type | Classes | Methods |
|-----------|--------:|--------:|
| Kernel | 23 | 135 |
| Functional | 78 | 104 |
| FunctionalJavascript | 6 | 6 |
| Unit | 1 | 2 |
| **Total** | **108** | **247** |

## Architecture surface

What the module contains, counted across Domain and its submodules:
the Drupal building blocks (services, plugins, hooks, routes, entities) that make
up its public and internal API.

| Element | Count |
|---------|------:|
| Services | 65 |
| Event subscribers | 0 |
| Hook implementations | 50 |
| Routes | 19 |
| Permissions | 24 |
| Forms | 9 |
| Controllers | 5 |
| Content entity types | 0 |
| Config entity types | 2 |
| Config schema types | 42 |
| Plugins | 49 |

Plugins break down by type as follows, largest first:

<div style="margin:1.6rem 0">
<svg viewBox="0 0 560 180" width="100%" role="img" style="display:block;width:100%;height:auto;font-family:ui-monospace,SFMono-Regular,Menlo,monospace">
<text x="200" y="18.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">Action</text>
<rect x="210.0" y="11.0" width="284.0" height="13" rx="2.5" fill="#b07d22"></rect>
<text x="502.0" y="18.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">16</text>
<text x="200" y="42.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">views</text>
<rect x="210.0" y="35.0" width="230.8" height="13" rx="2.5" fill="#b07d22"></rect>
<text x="448.8" y="42.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">13</text>
<text x="200" y="66.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">Validation</text>
<rect x="210.0" y="59.0" width="177.5" height="13" rx="2.5" fill="#b07d22"></rect>
<text x="395.5" y="66.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">10</text>
<text x="200" y="90.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">Block</text>
<rect x="210.0" y="83.0" width="88.8" height="13" rx="2.5" fill="#b07d22"></rect>
<text x="306.8" y="90.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">5</text>
<text x="200" y="114.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">Condition</text>
<rect x="210.0" y="107.0" width="35.5" height="13" rx="2.5" fill="#b07d22"></rect>
<text x="253.5" y="114.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">2</text>
<text x="200" y="138.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">EntityReferenceSelection</text>
<rect x="210.0" y="131.0" width="35.5" height="13" rx="2.5" fill="#b07d22"></rect>
<text x="253.5" y="138.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">2</text>
<text x="200" y="162.0" text-anchor="end" dominant-baseline="central" font-size="12.5" fill="currentColor">LanguageNegotiation</text>
<rect x="210.0" y="155.0" width="17.8" height="13" rx="2.5" fill="#b07d22"></rect>
<text x="235.8" y="162.0" dominant-baseline="central" font-size="12" fill="currentColor" opacity="0.75">1</text>
</svg>
</div>

## Methodology

Counts are produced with [`cloc`](https://github.com/AlDanial/cloc) from the
module root, excluding `node_modules`:

```bash
cloc --exclude-dir=node_modules .
```

Production and test totals split on the test directory:

```bash
# production
cloc --exclude-dir=node_modules --not-match-d='(^|/)tests(/|$)' .
# test
cloc --exclude-dir=node_modules --match-d='(^|/)tests(/|$)' .
```

Test classes are counted by their directory under `tests/src` (`Unit`, `Kernel`,
`Functional`, `FunctionalJavascript`); the method column counts `test`-prefixed
methods plus those annotated `@test` or `#[Test]`. The architecture surface counts
service definitions in `*.services.yml`, route and permission keys in
`*.routing.yml` and `*.permissions.yml`, `#[Hook]` attributes, plugin classes
under `src/Plugin/`, forms and controllers under `src/Form` and `src/Controller`,
entity-type attributes in `src/Entity`, and top-level types in
`config/schema/*.yml`, all summed across the module and its submodules.

The charts are static inline SVG generated from the JSON that cloc emits, so
they need no JavaScript or charting plugin. This page is refreshed periodically.
