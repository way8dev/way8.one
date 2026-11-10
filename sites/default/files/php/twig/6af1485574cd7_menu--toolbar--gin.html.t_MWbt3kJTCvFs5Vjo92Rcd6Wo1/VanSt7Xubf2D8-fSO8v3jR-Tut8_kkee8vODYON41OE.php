<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\MacroNamespace;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Sandbox\SecurityNotAllowedTestError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* themes/gin/templates/navigation/menu--toolbar--gin.html.twig */
class __TwigTemplate_855845712600296b3469b3d12b765eca extends Template
{
    private Source $source;
    /**
     * @var array<string, MacroNamespace>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        $this->sandbox = $env->getExtension(SandboxExtension::class)->getChecker();
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 23
        $macros["menus"] = $this->macros["menus"] = $this->getMacroNamespace();
        // line 24
        yield "
";
        // line 29
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($macros["menus"] ?? $this->throwUninitializedMacroNamespace(29))->call("menu_links", [($context["items"] ?? null), ($context["attributes"] ?? null), 0, false, ($context["menu_name"] ?? null), ($context["icon_default"] ?? null), ($context["icon_path"] ?? null), ($context["toolbar_variant"] ?? null)], $context, 29, $this->source));
        yield "

";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["_self", "items", "attributes", "menu_name", "icon_default", "icon_path", "toolbar_variant", "menu_level", "loop", "parent"]);        return; yield;
    }

    protected function loadDeclaredMacros(): array
    {
        return [
            "menu_links" => new \Twig\TwigMacro("menu_links", function ($items = null, $attributes = null, $menu_level = null, $parent = null, $menu_name = null, $icon_default = null, $icon_path = null, $toolbar_variant = null, ...$varargs): string|Markup {
                // line 31
                $macros = $this->macros;
                $context = [
                    "items" => $items,
                    "attributes" => $attributes,
                    "menu_level" => $menu_level,
                    "parent" => $parent,
                    "menu_name" => $menu_name,
                    "icon_default" => $icon_default,
                    "icon_path" => $icon_path,
                    "toolbar_variant" => $toolbar_variant,
                    "varargs" => $varargs,
                ] + $this->env->getGlobals();

                $blocks = [];

                return ('' === $tmp = implode('', iterator_to_array((function () use (&$context, $macros, $blocks) {
                    // line 32
                    yield "  ";
                    $macros["menus"] = $this->getMacroNamespace();
                    // line 33
                    yield "  ";
                    if ((($tmp = ($context["items"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                        // line 34
                        yield "    ";
                        if ((($context["menu_level"] ?? null) == 0)) {
                            // line 35
                            yield "      <ul";
                            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", ["toolbar-menu"], "method", false, false, true, 35), "html", null, true);
                            yield ">
    ";
                        } else {
                            // line 37
                            yield "      <ul class=\"toolbar-menu\">
    ";
                        }
                        // line 39
                        yield "    ";
                        $context['_parent'] = $context;
                        $context['_seq'] = CoreExtension::ensureTraversable(($context["items"] ?? null));
                        $context['loop'] = [
                          'parent' => $context['_parent'],
                          'index0' => 0,
                          'index'  => 1,
                          'first'  => true,
                        ];
                        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof \Countable)) {
                            $length = count($context['_seq']);
                            $context['loop']['revindex0'] = $length - 1;
                            $context['loop']['revindex'] = $length;
                            $context['loop']['length'] = $length;
                            $context['loop']['last'] = 1 === $length;
                        }
                        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                            // line 40
                            yield "      ";
                            // line 41
                            $context["classes"] = ["menu-item", ((((($tmp = CoreExtension::getAttribute($this->env, $this->source,                             // line 43
$context["item"], "is_expanded", [], "any", false, false, true, 43)) && $tmp instanceof Markup ? (string) $tmp : $tmp) && (($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 43)) && $tmp instanceof Markup ? (string) $tmp : $tmp))) ? ("menu-item--expanded") : ("")), (((($tmp = CoreExtension::getAttribute($this->env, $this->source,                             // line 44
$context["item"], "is_collapsed", [], "any", false, false, true, 44)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("menu-item--collapsed") : ("")), (((($tmp = CoreExtension::getAttribute($this->env, $this->source,                             // line 45
$context["item"], "in_active_trail", [], "any", false, false, true, 45)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("menu-item--active-trail") : ("")), (((($tmp = CoreExtension::getAttribute($this->env, $this->source,                             // line 46
$context["item"], "gin_id", [], "any", false, false, true, 46)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? (("menu-item__" . CoreExtension::getAttribute($this->env, $this->source, $context["item"], "gin_id", [], "any", false, false, true, 46))) : (""))];
                            // line 49
                            yield "
      ";
                            // line 51
                            yield "      ";
                            if (((((($context["menu_level"] ?? null) == 0) && (CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 51) == 1)) &&  !Twig\Extension\CoreExtension::testEmpty(CoreExtension::getAttribute($this->env, $this->source, $context["item"], "gin_id", [], "any", false, false, true, 51))) && (CoreExtension::getAttribute($this->env, $this->source, $context["item"], "gin_id", [], "any", false, false, true, 51) != "admin_toolbar_tools-help"))) {
                                // line 52
                                yield "        <li class=\"menu-item menu-item--expanded menu-item__tools\">
          ";
                                // line 53
                                if (((($context["icon_default"] ?? null) == false) && (($context["icon_path"] ?? null) != ""))) {
                                    // line 54
                                    yield "            <a href=\"";
                                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->getPath("<front>"));
                                    yield "\" class=\"toolbar-logo\" data-drupal-link-system-path=\"<front>\">
              <img src=\"";
                                    // line 55
                                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->getFileUrl(($context["icon_path"] ?? null)), "html", null, true);
                                    yield "\" class=\"toolbar-icon-home\" alt=\"";
                                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Home"));
                                    yield "\" />
            </a>
          ";
                                } else {
                                    // line 58
                                    yield "            <a href=\"";
                                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->getPath("<front>"));
                                    yield "\" class=\"toolbar-icon toolbar-icon-admin-toolbar-tools-help toolbar-icon-default\" data-drupal-link-system-path=\"<front>\">
              ";
                                    // line 59
                                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Home"));
                                    yield "
            </a>
          ";
                                }
                                // line 62
                                yield "        </li>
      ";
                            }
                            // line 64
                            yield "
      ";
                            // line 68
                            yield "
      ";
                            // line 70
                            yield "      ";
                            if (((($context["menu_level"] ?? null) == 1) && (CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 70) == 1))) {
                                // line 71
                                yield "        <li class=\"menu-item-title\">
          <h2 class=\"toolbar-menu__title\">
            <a href=\"";
                                // line 73
                                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["parent"] ?? null), "url", [], "any", false, false, true, 73), "html", null, true);
                                yield "\">";
                                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["parent"] ?? null), "title", [], "any", false, false, true, 73), "html", null, true);
                                yield "</a>
          </h2>
        </li>
        ";
                                // line 76
                                if ((($context["toolbar_variant"] ?? null) == "vertical")) {
                                    // line 77
                                    yield "          <li class=\"menu-item\">
            <a href=\"";
                                    // line 78
                                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["parent"] ?? null), "url", [], "any", false, false, true, 78), "html", null, true);
                                    yield "\" class=\"toolbar-icon\">";
                                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Overview"));
                                    yield "</a>
          </li>
        ";
                                }
                                // line 81
                                yield "      ";
                            } elseif (((($context["menu_level"] ?? null) > 1) && (CoreExtension::getAttribute($this->env, $this->source, $context["loop"], "index", [], "any", false, false, true, 81) == 1))) {
                                // line 82
                                yield "        <li class=\"menu-item-title\">
          <h3 class=\"toolbar-menu__sub-title\">
            <a href=\"";
                                // line 84
                                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["parent"] ?? null), "url", [], "any", false, false, true, 84), "html", null, true);
                                yield "\">";
                                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["parent"] ?? null), "title", [], "any", false, false, true, 84), "html", null, true);
                                yield "</a>
          </h3>
        </li>
        ";
                                // line 87
                                if ((($context["toolbar_variant"] ?? null) == "vertical")) {
                                    // line 88
                                    yield "          <li class=\"menu-item\">
            <a href=\"";
                                    // line 89
                                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["parent"] ?? null), "url", [], "any", false, false, true, 89), "html", null, true);
                                    yield "\" class=\"toolbar-icon\">";
                                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Overview"));
                                    yield "</a>
          </li>
        ";
                                }
                                // line 92
                                yield "      ";
                            }
                            // line 93
                            yield "
      <li";
                            // line 94
                            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["item"], "attributes", [], "any", false, false, true, 94), "addClass", [($context["classes"] ?? null)], "method", false, false, true, 94), "html", null, true);
                            yield ">
        ";
                            // line 95
                            if ((((CoreExtension::getAttribute($this->env, $this->source, $context["item"], "gin_id", [], "any", false, false, true, 95) == "admin_toolbar_tools-help") && (($context["icon_default"] ?? null) == false)) && (($context["icon_path"] ?? null) != ""))) {
                                // line 96
                                yield "          <a href=\"";
                                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->getPath("<front>"));
                                yield "\" class=\"toolbar-logo\" data-drupal-link-system-path=\"<front>\">
            <img src=\"";
                                // line 97
                                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->getFileUrl(($context["icon_path"] ?? null)), "html", null, true);
                                yield "\" class=\"toolbar-icon-home\" alt=\"";
                                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Home"));
                                yield "\" />
          </a>
        ";
                            } elseif ((CoreExtension::getAttribute($this->env, $this->source,                             // line 99
$context["item"], "gin_id", [], "any", false, false, true, 99) == "admin_toolbar_tools-help")) {
                                // line 100
                                yield "          ";
                                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->getLink("Drupal", CoreExtension::getAttribute($this->env, $this->source, $context["item"], "url", [], "any", false, false, true, 100), ["class" => ["toolbar-icon-default"]]), "html", null, true);
                                yield "
        ";
                            } else {
                                // line 102
                                yield "          ";
                                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->getLink(CoreExtension::getAttribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 102), CoreExtension::getAttribute($this->env, $this->source, $context["item"], "url", [], "any", false, false, true, 102), ["class" => [(((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["item"], "in_active_trail", [], "any", false, false, true, 102)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("is-active") : (""))]]), "html", null, true);
                                yield "
        ";
                            }
                            // line 104
                            yield "        ";
                            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 104)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                                // line 105
                                yield "          ";
                                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($macros["menus"] ?? $this->throwUninitializedMacroNamespace(105))->call("menu_links", [CoreExtension::getAttribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 105), ($context["attributes"] ?? null), (($context["menu_level"] ?? null) + 1), $context["item"], ($context["menu_name"] ?? null), ($context["icon_default"] ?? null), ($context["icon_path"] ?? null), ($context["toolbar_variant"] ?? null)], $context, 105, $this->source));
                                yield "
        ";
                            }
                            // line 107
                            yield "      </li>
    ";
                            ++$context['loop']['index0'];
                            ++$context['loop']['index'];
                            $context['loop']['first'] = false;
                            if (isset($context['loop']['revindex0'], $context['loop']['revindex'])) {
                                --$context['loop']['revindex0'];
                                --$context['loop']['revindex'];
                                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                            }
                        }
                        $_parent = $context['_parent'];
                        unset($context['_seq'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
                        $context = array_intersect_key($context, $_parent);
                        $context += $_parent;
                        // line 109
                        yield "    </ul>
  ";
                    }
                    // line 111
                    yield "
  ";
                    // line 112
                    if ((($context["menu_level"] ?? null) == 0)) {
                        // line 113
                        yield "    ";
                        // line 114
                        yield "    <a href=\"#\" class=\"toolbar-menu__trigger trigger\" role=\"button\" aria-pressed=\"false\">";
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Close"));
                        yield "</a>
  ";
                    }
                    return; yield;
                })(), false))) ? '' : new Markup($tmp, $this->env->getCharset());
            }, ["items" => false, "attributes" => false, "menu_level" => false, "parent" => false, "menu_name" => false, "icon_default" => false, "icon_path" => false, "toolbar_variant" => false], false),
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/gin/templates/navigation/menu--toolbar--gin.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDefaultEscapeStrategy(): string|false
    {
        return "html";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  300 => 114,  298 => 113,  296 => 112,  293 => 111,  289 => 109,  273 => 107,  267 => 105,  264 => 104,  258 => 102,  252 => 100,  250 => 99,  243 => 97,  238 => 96,  236 => 95,  232 => 94,  229 => 93,  226 => 92,  218 => 89,  215 => 88,  213 => 87,  205 => 84,  201 => 82,  198 => 81,  190 => 78,  187 => 77,  185 => 76,  177 => 73,  173 => 71,  170 => 70,  167 => 68,  164 => 64,  160 => 62,  154 => 59,  149 => 58,  141 => 55,  136 => 54,  134 => 53,  131 => 52,  128 => 51,  125 => 49,  123 => 46,  122 => 45,  121 => 44,  120 => 43,  119 => 41,  117 => 40,  99 => 39,  95 => 37,  89 => 35,  86 => 34,  83 => 33,  80 => 32,  63 => 31,  50 => 29,  47 => 24,  45 => 23,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/gin/templates/navigation/menu--toolbar--gin.html.twig", "/var/www/html/themes/gin/templates/navigation/menu--toolbar--gin.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["import" => 23, "macro" => 31, "if" => 33, "for" => 39, "set" => 41];
        static $filters = ["escape" => 35, "t" => 55];
        static $functions = ["path" => 54, "file_url" => 55, "link" => 100];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "import", 1 => "macro", 2 => "if", 3 => "for", 4 => "set"],
                [0 => "escape", 1 => "t"],
                [0 => "path", 1 => "file_url", 2 => "link"],
                [],
                $this->source
            );
        } catch (SecurityError $e) {
            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            } elseif ($e instanceof SecurityNotAllowedTestError && isset($tests[$e->getTestName()])) {
                $e->setTemplateLine($tests[$e->getTestName()]);
            }

            throw $e;
        }

    }
}
