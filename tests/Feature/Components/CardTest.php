<?php

it('renders the requested element and slot', function () {
    $this->blade('<x-old.ui.card as="article" tone="sage" padding="sm" rounded="xl">Content</x-old.ui.card>')
        ->assertSee('<article', false)
        ->assertSee('Content')
        ->assertDontSee('tone=', false)
        ->assertDontSee('padding=', false)
        ->assertDontSee('rounded=', false);
});

it('merges extra classes and forwards attributes', function () {
    $this->blade('<x-old.ui.card as="section" tone="plain" class="card-hover" data-testid="card">Content</x-old.ui.card>')
        ->assertSee('<section', false)
        ->assertSee('card-hover', false)
        ->assertSee('data-testid="card"', false);
});
