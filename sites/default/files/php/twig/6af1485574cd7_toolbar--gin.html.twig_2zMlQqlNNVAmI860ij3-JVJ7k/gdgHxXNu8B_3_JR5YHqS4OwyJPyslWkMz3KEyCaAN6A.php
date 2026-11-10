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

/* @gin/navigation/toolbar--gin.html.twig */
class __TwigTemplate_e3e804b0169f38c710b0fe3784bad663 extends Template
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
        $context["gin_toolbar_id"] = ((((($context["toolbar_variant"] ?? null) != "classic") || (($tmp = ($context["core_navigation"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp))) ? ("gin-toolbar-bar") : ("toolbar-bar"));
        // line 24
        yield "<div";
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", ["toolbar"], "method", false, false, true, 24), "html", null, true);
        yield ">
  <nav";
        // line 25
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["toolbar_attributes"] ?? null), "addClass", ["toolbar-bar", "clearfix"], "method", false, false, true, 25), "setAttribute", ["id", ($context["gin_toolbar_id"] ?? null)], "method", false, false, true, 25), "html", null, true);
        yield ">
    <h2 class=\"visually-hidden\">";
        // line 26
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["toolbar_heading"] ?? null), "html", null, true);
        yield "</h2>
    ";
        // line 27
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["tabs"] ?? null));
        foreach ($context['_seq'] as $context["key"] => $context["tab"]) {
            // line 28
            yield "      ";
            $context["tray"] = (($_v0 = ($context["trays"] ?? null)) && is_array($_v0) || $_v0 instanceof ArrayAccess && in_array($_v0::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v0[(($_v1 = $context["key"]) instanceof \Stringable && (is_array($_v0) || $_v0 instanceof \ArrayObject || $_v0 instanceof \ArrayIterator) ? (string) $_v1 : $_v1)] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["trays"] ?? null), $context["key"], [], "array", false, false, true, 28));
            // line 29
            yield "      ";
            $context["user_menu"] = (((($tmp = (($_v2 = CoreExtension::getAttribute($this->env, $this->source, ($context["tray"] ?? null), "links", [], "any", false, false, true, 29)) && is_array($_v2) || $_v2 instanceof ArrayAccess && in_array($_v2::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v2["user_links"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["tray"] ?? null), "links", [], "any", false, false, true, 29), "user_links", [], "array", false, false, true, 29))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("user-menu") : (false));
            // line 30
            yield "      ";
            if (((            // line 31
($context["toolbar_variant"] ?? null) != "classic") && ((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,             // line 32
$context["tab"], "attributes", [], "any", false, false, true, 32), "id", [], "any", false, false, true, 32) == "admin-toolbar-search-tab") || (CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,             // line 33
$context["tab"], "attributes", [], "any", false, false, true, 33), "id", [], "any", false, false, true, 33) == "responsive-preview-toolbar-tab")))) {
                // line 36
                yield "        ";
                // line 37
                yield "      ";
            } else {
                // line 38
                yield "        ";
                if (((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "attributes", [], "any", false, false, true, 38), "id", [], "any", false, false, true, 38) == "toolbar-tab-tour") && (($context["toolbar_variant"] ?? null) != "classic"))) {
                    // line 39
                    yield "        <div";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "attributes", [], "any", false, false, true, 39), "addClass", ["toolbar-tab", ($context["user_menu"] ?? null), (((($tmp = (($_v3 = CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "link", [], "any", false, false, true, 39)) && is_array($_v3) || $_v3 instanceof ArrayAccess && in_array($_v3::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v3["#id"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "link", [], "any", false, false, true, 39), "#id", [], "array", false, false, true, 39))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? (("toolbar-tab--" . (($_v4 = CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "link", [], "any", false, false, true, 39)) && is_array($_v4) || $_v4 instanceof ArrayAccess && in_array($_v4::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v4["#id"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "link", [], "any", false, false, true, 39), "#id", [], "array", false, false, true, 39)))) : (null))], "method", false, false, true, 39), "removeAttribute", ["id"], "method", false, false, true, 39), "html", null, true);
                    yield ">
        ";
                } else {
                    // line 41
                    yield "        <div";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "attributes", [], "any", false, false, true, 41), "addClass", ["toolbar-tab", ($context["user_menu"] ?? null), (((($tmp = (($_v5 = CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "link", [], "any", false, false, true, 41)) && is_array($_v5) || $_v5 instanceof ArrayAccess && in_array($_v5::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v5["#id"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "link", [], "any", false, false, true, 41), "#id", [], "array", false, false, true, 41))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? (("toolbar-tab--" . (($_v6 = CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "link", [], "any", false, false, true, 41)) && is_array($_v6) || $_v6 instanceof ArrayAccess && in_array($_v6::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v6["#id"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "link", [], "any", false, false, true, 41), "#id", [], "array", false, false, true, 41)))) : (null))], "method", false, false, true, 41), "html", null, true);
                    yield ">
        ";
                }
                // line 43
                yield "          ";
                if (((($_v7 = CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "link", [], "any", false, false, true, 43)) && is_array($_v7) || $_v7 instanceof ArrayAccess && in_array($_v7::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v7["#id"] ?? null) : CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "link", [], "any", false, false, true, 43), "#id", [], "array", false, false, true, 43)) == "toolbar-item-administration")) {
                    // line 44
                    yield "            <a class=\"toolbar-menu__logo\" href=\"";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->getPath("system.admin_content"));
                    yield "\" aria-label=\"";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Toolbar Menu Logo"));
                    yield "\">
              <span class=\"visually-hidden\">";
                    // line 45
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Toolbar Menu Logo"));
                    yield "</span>
            </a>
            ";
                    // line 47
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "link", [], "any", false, false, true, 47), "html", null, true);
                    yield "
          ";
                } else {
                    // line 49
                    yield "            ";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["tab"], "link", [], "any", false, false, true, 49), "html", null, true);
                    yield "
          ";
                }
                // line 51
                yield "          <div";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["tray"] ?? null), "attributes", [], "any", false, false, true, 51), "html", null, true);
                yield ">
            ";
                // line 52
                if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, ($context["tray"] ?? null), "label", [], "any", false, false, true, 52)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 53
                    yield "              <nav class=\"toolbar-lining clearfix\" role=\"navigation\" aria-label=\"";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["tray"] ?? null), "label", [], "any", false, false, true, 53), "html", null, true);
                    yield "\">
                <h3 class=\"toolbar-tray-name visually-hidden\">";
                    // line 54
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["tray"] ?? null), "label", [], "any", false, false, true, 54), "html", null, true);
                    yield "</h3>
            ";
                } else {
                    // line 56
                    yield "              <nav class=\"toolbar-lining clearfix\" role=\"navigation\">
            ";
                }
                // line 58
                yield "            ";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["tray"] ?? null), "links", [], "any", false, false, true, 58), "html", null, true);
                yield "
            </nav>
          </div>
        </div>
      ";
            }
            // line 63
            yield "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['key'], $context['tab'], $context['_parent']);
        $context = array_intersect_key($context, $_parent);
        $context += $_parent;
        // line 64
        yield "  </nav>
  ";
        // line 65
        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["remainder"] ?? null), "html", null, true);
        yield "
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["toolbar_variant", "core_navigation", "attributes", "toolbar_attributes", "toolbar_heading", "tabs", "trays", "remainder"]);        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "@gin/navigation/toolbar--gin.html.twig";
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
        return array (  162 => 65,  159 => 64,  152 => 63,  143 => 58,  139 => 56,  134 => 54,  129 => 53,  127 => 52,  122 => 51,  116 => 49,  111 => 47,  106 => 45,  99 => 44,  96 => 43,  90 => 41,  84 => 39,  81 => 38,  78 => 37,  76 => 36,  74 => 33,  73 => 32,  72 => 31,  70 => 30,  67 => 29,  64 => 28,  60 => 27,  56 => 26,  52 => 25,  47 => 24,  45 => 23,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "@gin/navigation/toolbar--gin.html.twig", "/var/www/html/themes/gin/templates/navigation/toolbar--gin.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["set" => 23, "for" => 27, "if" => 30];
        static $filters = ["escape" => 24, "t" => 44];
        static $functions = ["path" => 44];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "set", 1 => "for", 2 => "if"],
                [0 => "escape", 1 => "t"],
                [0 => "path"],
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
