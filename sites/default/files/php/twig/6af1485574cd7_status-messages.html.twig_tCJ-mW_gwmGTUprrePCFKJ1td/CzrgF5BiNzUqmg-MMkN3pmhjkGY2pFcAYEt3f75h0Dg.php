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

/* themes/gin/templates/misc/status-messages.html.twig */
class __TwigTemplate_3db214ccf1a0bae7b7f065e2ff3b9e72 extends Template
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
        // line 25
        yield "<div data-drupal-messages class=\"messages-list\">
  <div class=\"messages__wrapper\">
    ";
        // line 27
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["message_list"] ?? null));
        foreach ($context['_seq'] as $context["type"] => $context["messages"]) {
            // line 28
            yield "      ";
            // line 29
            $context["classes"] = ["messages-list__item", "messages", ("messages--" .             // line 32
$context["type"])];
            // line 35
            yield "      ";
            // line 36
            $context["is_message_with_title"] = (($_v0 = ($context["status_headings"] ?? null)) && is_array($_v0) || $_v0 instanceof ArrayAccess && in_array($_v0::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v0[(($_v1 = $context["type"]) instanceof \Stringable && (is_array($_v0) || $_v0 instanceof \ArrayObject || $_v0 instanceof \ArrayIterator) ? (string) $_v1 : $_v1)] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["status_headings"] ?? null), $context["type"], [], "array", false, false, true, 36));
            // line 38
            yield "      ";
            // line 39
            $context["is_message_with_icon"] = CoreExtension::inFilter($context["type"], ["error", "status", "warning"]);
            // line 41
            yield "
      <div role=\"contentinfo\" aria-labelledby=\"";
            // line 42
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, (($_v2 = ($context["title_ids"] ?? null)) && is_array($_v2) || $_v2 instanceof ArrayAccess && in_array($_v2::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v2[(($_v3 = $context["type"]) instanceof \Stringable && (is_array($_v2) || $_v2 instanceof \ArrayObject || $_v2 instanceof \ArrayIterator) ? (string) $_v3 : $_v3)] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["title_ids"] ?? null), $context["type"], [], "array", false, false, true, 42)), "html", null, true);
            yield "\"";
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->withoutFilter(CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [($context["classes"] ?? null)], "method", false, false, true, 42), "role", "aria-label"), "html", null, true);
            yield ">
        ";
            // line 43
            if (((($tmp = ($context["is_message_with_title"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp) || (($tmp = ($context["is_message_with_icon"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp))) {
                // line 44
                yield "          <div class=\"messages__header\">
            ";
                // line 45
                if ((($tmp = ($context["is_message_with_title"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    // line 46
                    yield "              <h2 id=\"";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, (($_v4 = ($context["title_ids"] ?? null)) && is_array($_v4) || $_v4 instanceof ArrayAccess && in_array($_v4::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v4[(($_v5 = $context["type"]) instanceof \Stringable && (is_array($_v4) || $_v4 instanceof \ArrayObject || $_v4 instanceof \ArrayIterator) ? (string) $_v5 : $_v5)] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["title_ids"] ?? null), $context["type"], [], "array", false, false, true, 46)), "html", null, true);
                    yield "\" class=\"messages__title\">
                ";
                    // line 47
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, (($_v6 = ($context["status_headings"] ?? null)) && is_array($_v6) || $_v6 instanceof ArrayAccess && in_array($_v6::class, CoreExtension::ARRAY_LIKE_CLASSES, true) ? ($_v6[(($_v7 = $context["type"]) instanceof \Stringable && (is_array($_v6) || $_v6 instanceof \ArrayObject || $_v6 instanceof \ArrayIterator) ? (string) $_v7 : $_v7)] ?? null) : CoreExtension::getAttribute($this->env, $this->source, ($context["status_headings"] ?? null), $context["type"], [], "array", false, false, true, 47)), "html", null, true);
                    yield "
              </h2>
            ";
                }
                // line 50
                yield "          </div>
        ";
            }
            // line 52
            yield "        <div class=\"messages__content\">
          ";
            // line 53
            if ((Twig\Extension\CoreExtension::length($this->env->getCharset(), $context["messages"]) > 1)) {
                // line 54
                yield "            <ul class=\"messages__list\">
              ";
                // line 55
                $context['_parent'] = $context;
                $context['_seq'] = CoreExtension::ensureTraversable($context["messages"]);
                foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
                    // line 56
                    yield "                <li class=\"messages__item\">";
                    yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $context["message"], "html", null, true);
                    yield "</li>
              ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_key'], $context['message'], $context['_parent']);
                $context = array_intersect_key($context, $_parent);
                $context += $_parent;
                // line 58
                yield "            </ul>
          ";
            } else {
                // line 60
                yield "            ";
                yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, Twig\Extension\CoreExtension::first($this->env->getCharset(), $context["messages"]), "html", null, true);
                yield "
          ";
            }
            // line 62
            yield "        </div>
        <button type=\"button\" class=\"button button--dismiss js-message-button-hide\" title=\"";
            // line 63
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Hide"));
            yield "\">
          <span class=\"icon-close\"></span>
          ";
            // line 65
            yield (string) $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Hide"));
            yield "
        </button>
      </div>
      ";
            // line 69
            yield "      ";
            $context["attributes"] = CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "removeClass", [($context["classes"] ?? null)], "method", false, false, true, 69);
            // line 70
            yield "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['type'], $context['messages'], $context['_parent']);
        $context = array_intersect_key($context, $_parent);
        $context += $_parent;
        // line 71
        yield "  </div>
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["message_list", "status_headings", "title_ids", "attributes"]);        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/gin/templates/misc/status-messages.html.twig";
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
        return array (  153 => 71,  146 => 70,  143 => 69,  137 => 65,  132 => 63,  129 => 62,  123 => 60,  119 => 58,  109 => 56,  105 => 55,  102 => 54,  100 => 53,  97 => 52,  93 => 50,  87 => 47,  82 => 46,  80 => 45,  77 => 44,  75 => 43,  69 => 42,  66 => 41,  64 => 39,  62 => 38,  60 => 36,  58 => 35,  56 => 32,  55 => 29,  53 => 28,  49 => 27,  45 => 25,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/gin/templates/misc/status-messages.html.twig", "/var/www/html/themes/gin/templates/misc/status-messages.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["for" => 27, "set" => 29, "if" => 43];
        static $filters = ["escape" => 42, "without" => 42, "length" => 53, "first" => 60, "t" => 63];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "for", 1 => "set", 2 => "if"],
                [0 => "escape", 1 => "without", 2 => "length", 3 => "first", 4 => "t"],
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
