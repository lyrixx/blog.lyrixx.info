---
layout: post
title:  Symfony2: Snippet to render all form errors
tags:
    - Symfony2
---
{% verbatim %}

With symfony 2, you can render all errors from a form with a little macro. You
can use it for only one form, or for all your application

### The macro

    {# MyBundle:form.html.twig #}

    {% macro display_error(form) %}
        {% import _self as forms %}
        <ul>
            {% for child in form if not child.vars.valid %}
                <li>
                    {% if child.vars.compound and not child.vars.valid %}
                        {{ child.vars.label|trans|capitalize }}:
                        {{ forms.display_error(child) }}
                    {% else %}
                        <h5>
                            <a href="#{{ child.vars.id }}">
                                {{ child.vars.label|trans|capitalize }}:
                            </a>
                            <small>
                                {% for error in child.vars.errors %}
                                    {{ error.message|capitalize }}
                                {% endfor %}
                            </small>
                        </h5>
                    {% endif %}
                </li>
            {% endfor %}
        </ul>
    {% endmacro %}

### Usage

#### Just for one form:

    {# MyBundle:User:new.html.twig #}

    {% import "MyBundle:form.html.twig" as macros %}

    {% if not form.vars.valid %}
        <!-- Wep, this is some twitter bootstrap markup -->
        <div class="alert alert-block alert-error">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h3>Oh snap!</h3>
            <p><strong>Change a few things up and try submitting again.</strong></p>
            {{ macros.display_error(form) }}
        </div>
    {% endif %}

#### For all you application

Add the layout in your `config.yml`:

    twig:
        form:
            resources:
                - MyBundle::form_div_layout.html.twig

And override the `form_errors` block:

    {# form_div_layout.html.twig #}

    {% block form_errors %}
    {% spaceless %}
        {% import "MyBundle:form.html.twig" as macros %}
        {% if not form.parent and not form.vars.valid %}
             {% import _self as forms %}
            <div class="alert alert-block alert-error">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h3>Oh snap!</h3>
                <p><strong>Change a few things up and try submitting again.</strong></p>
                {{ macros.display_project_error(form) }}
            </div>
        {% else %}
            {{ parent() }}
        {% endif %}
    {% endspaceless %}
    {% endblock %}

{% endverbatim %}
