<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A raiz redireciona para o idioma preferido.
     */
    public function test_the_application_redirects_root_to_locale(): void
    {
        $this->get('/')->assertRedirect();
    }
}
