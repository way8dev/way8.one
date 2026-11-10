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

/* core/modules/locale/templates/locale-translation-update-info.html.twig */
class __TwigTemplate_6a40b094cb844c85a0e88f671c3ebda3 extends Template
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
        // line 18
        yield "<div class=\"locale-translation-update__wrapper\" tabindex=\"0\" role=\"button\">
  <span class=\"locale-translation-update__prefix visually-hidden\">Show description</span>
  ";
        // line 20
        if ((($tmp = ($context["modules"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 21
            yield "    ";
            $context["module_list"] = $this->extensions['Drupal\Core\Template\TwigExtension']->safeJoin($this->env, ($context["modules"] ?? null), ", ");
            // line 22
            yield "    <span class=\"locale-translation-update__message\">";
            yield t("Updates for: @module_list", ["@module_list" => $this->env->getExtension(\Drupal\Core\Template\TwigExtension::class)->renderVar(($context["module_list"] ?? null)), ]);
            yield "</span>
  ";
        } elseif ((($tmp =         // line 23
($context["not_found"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 24
            yield "    <span class=\"locale-translation-update__message\">";
            // line 25
            yield \Drupal::translation()->formatPlural(abs(Twig\Extension\CoreExtension::length($this->env->getCharset(),             // line 27
($context["not_found"] ?? null))), "Missing translations for one project", "Missing translations for @count projects", []);
            // line 30
            yield "</span>
  ";
        }
        // line 32
        yield "  ";
        if (((($tmp = ($context["updates"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp) || (($tmp = ($context["not_found"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp))) {
            // line 33
            yield "    <div class=\"locale-translation-update__details\">
      ";
            // line 34
            if ((($tmp = ($context["updates"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 35
                yield "        <ul>
          ";
                // line 36
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable(($context["updates"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["update"]) {
                    // line 37
                    yield "            <li>";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["update"], "name", [], "any", false, false, true, 37), "html", null, true);
                    yield " (";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->env->getFilter('format_date')->getCallable()(CoreExtension::getAttribute($this->env, $this->source, $context["update"], "timestamp", [], "any", false, false, true, 37), "html_date"), "html", null, true);
                    yield ")</li>
          ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_key'], $context['update'], $context['_parent']);
                $context = array_intersect_key($context, $_parent);
                $context += $_parent;
                // line 39
                yield "        </ul>
      ";
            }
            // line 41
            yield "      ";
            if ((($tmp = ($context["not_found"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 42
                yield "        ";
                // line 46
                yield "        ";
                if ((($tmp = ($context["updates"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 47
                    yield "          ";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Missing translations for:"));
                    yield "
        ";
                }
                // line 49
                yield "        ";
                if ((($tmp = ($context["not_found"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 50
                    yield "          <ul>
            ";
                    // line 51
                    $context['_parent'] = $context;
                    $context['_seq'] = CoreExtension::ensureTraversable(($context["not_found"] ?? null));
                    foreach ($context['_seq'] as $context["_key"] => $context["update"]) {
                        // line 52
                        yield "              <li>";
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["update"], "name", [], "any", false, false, true, 52), "html", null, true);
                        yield " (";
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ((CoreExtension::getAttribute($this->env, $this->source, $context["update"], "version", [], "any", true, true, true, 52)) ? (Twig\Extension\CoreExtension::default(CoreExtension::getAttribute($this->env, $this->source, $context["update"], "version", [], "any", false, false, true, 52), t("no version"))) : (t("no version"))), "html", null, true);
                        yield "). ";
                        yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, $context["update"], "info", [], "any", false, false, true, 52), "html", null, true);
                        yield "</li>
            ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_key'], $context['update'], $context['_parent']);
                    $context = array_intersect_key($context, $_parent);
                    $context += $_parent;
                    // line 54
                    yield "          </ul>
        ";
                }
                // line 56
                yield "      ";
            }
            // line 57
            yield "    </div>
  ";
        }
        // line 59
        yield "</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["modules", "not_found", "updates"]);        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "core/modules/locale/templates/locale-translation-update-info.html.twig";
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
        return array (  150 => 59,  146 => 57,  143 => 56,  139 => 54,  125 => 52,  121 => 51,  118 => 50,  115 => 49,  109 => 47,  106 => 46,  104 => 42,  101 => 41,  97 => 39,  85 => 37,  81 => 36,  78 => 35,  76 => 34,  73 => 33,  70 => 32,  66 => 30,  64 => 27,  63 => 25,  61 => 24,  59 => 23,  54 => 22,  51 => 21,  49 => 20,  45 => 18,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "core/modules/locale/templates/locale-translation-update-info.html.twig", "/var/www/html/core/modules/locale/templates/locale-translation-update-info.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["if" => 20, "set" => 21, "trans" => 22, "for" => 36];
        static $filters = ["safe_join" => 21, "escape" => 22, "length" => 27, "format_date" => 37, "t" => 47, "default" => 52];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "if", 1 => "set", 2 => "trans", 3 => "for"],
                [0 => "safe_join", 1 => "escape", 2 => "length", 3 => "format_date", 4 => "t", 5 => "default"],
                [],
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
