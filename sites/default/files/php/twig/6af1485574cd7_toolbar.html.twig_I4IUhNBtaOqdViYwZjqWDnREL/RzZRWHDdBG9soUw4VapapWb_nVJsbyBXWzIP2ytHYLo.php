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

/* modules/gin_toolbar/templates/toolbar.html.twig */
class __TwigTemplate_2318d906659241187e0c3dbf5a4b9d03 extends Template
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
        // line 7
        if ((($tmp = ($context["core_navigation"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 8
            yield "  ";
            if ((($tmp = ($context["secondary_toolbar_frontend"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 9
                yield "    ";
                try {
                    $_v0 = $this->load("@gin/navigation/toolbar--gin--secondary.html.twig", 9);
                } catch (LoaderError $e) {
                    // ignore missing template
                    $_v0 = null;
                }
                if ($_v0) {
                    yield from $_v0->unwrap()->yield($context);
                }
                // line 10
                yield "  ";
            }
        } else {
            // line 12
            yield "  ";
            try {
                $_v1 = $this->load("@gin/navigation/toolbar--gin.html.twig", 12);
            } catch (LoaderError $e) {
                // ignore missing template
                $_v1 = null;
            }
            if ($_v1) {
                yield from $_v1->unwrap()->yield($context);
            }
            // line 13
            yield "
  ";
            // line 14
            if (((($tmp = ($context["secondary_toolbar_frontend"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp) && (($context["toolbar_variant"] ?? null) != "classic"))) {
                // line 15
                yield "    ";
                try {
                    $_v2 = $this->load("@gin_toolbar/toolbar--gin--secondary--frontend.html.twig", 15);
                } catch (LoaderError $e) {
                    // ignore missing template
                    $_v2 = null;
                }
                if ($_v2) {
                    yield from $_v2->unwrap()->yield($context);
                }
                // line 16
                yield "  ";
            }
        }
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["core_navigation", "secondary_toolbar_frontend", "toolbar_variant"]);        return; yield;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "modules/gin_toolbar/templates/toolbar.html.twig";
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
        return array (  92 => 16,  81 => 15,  79 => 14,  76 => 13,  65 => 12,  61 => 10,  50 => 9,  47 => 8,  45 => 7,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "modules/gin_toolbar/templates/toolbar.html.twig", "/var/www/html/modules/gin_toolbar/templates/toolbar.html.twig");
    }
    
    public function ensureSecurityChecked(): void
    {
        if ($this->sandbox->isSandboxed($this->source)) {
            $this->checkSecurity();
        }
    }
    
    public function checkSecurity()
    {
        static $tags = ["if" => 7, "include" => 9];
        static $filters = [];
        static $functions = [];
        static $tests = [];

        try {
            $this->sandbox->checkSecurity(
                [0 => "if", 1 => "include"],
                [],
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
