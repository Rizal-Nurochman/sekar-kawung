<?php

it('renders a link when an href is provided', function () {
    $this->blade('<x-ui.button href="#kalkulator" variant="primary">Hitung</x-ui.button>')
        ->assertSee('<a', false)
        ->assertSee('href="#kalkulator"', false)
        ->assertDontSee('<button', false);
});

it('renders a button with a type when no href is provided', function () {
    $this->blade('<x-ui.button type="submit" variant="primary">Hitung</x-ui.button>')
        ->assertSee('<button', false)
        ->assertSee('type="submit"', false);
});

it('passes Alpine attributes through to the root element', function () {
    $this->blade('<x-ui.button variant="filter" x-on:click="setFilter(\'all\')" x-bind:aria-pressed="filter === \'all\'">Semua</x-ui.button>')
        ->assertSee('x-on:click="setFilter(\'all\')"', false)
        ->assertSee('x-bind:aria-pressed="filter === \'all\'"', false);
});
