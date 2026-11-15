<?php

const SILKSCREEN_STATEMENT = 'Silkscreen is a film production company that prioritises artist-driven storytelling, cultural engagement, and bold creative expression.';
const SILKSCREEN_COPYRIGHT = '©silkscreen 2026';
const SILKSCREEN_SOCIAL_HANDLE = '@silkscreen_ent';
const SILKSCREEN_SOCIAL_URL = 'https://instagram.com/silkscreen_ent';
const SILKSCREEN_CONTACT_INTRO = 'If you would like to get in touch, you can reach us at the addresses below.';

const SILKSCREEN_CONTACT_PRIMARY_SLOTS = 3;

function silkscreen_default_contact_emails() {
    return array(
        array('label' => 'General Inquiries', 'address' => 'hello@silkscreen.com'),
        array('label' => 'Press', 'address' => 'press@silkscreen.com'),
        array('label' => 'Collaborations', 'address' => 'collaborations@silkscreen.com'),
    );
}

function silkscreen_default_hero_slides() {
    $hero = array();
    foreach (array('hero-1.png', 'hero-2.png', 'hero-3.png', 'hero-4.png') as $i => $file) {
        $hero[] = array(
            'src' => silkscreen_asset('media/' . $file),
            'alt' => 'Silkscreen hero still ' . ($i + 1),
        );
    }
    return $hero;
}

const SILKSCREEN_PROJECT_STILL_SLOTS = 5;
const SILKSCREEN_PROJECT_CREDIT_SLOTS = 8;

const SILKSCREEN_PROJECT_POST_TYPE = 'silkscreen_project';
const SILKSCREEN_NEWS_POST_TYPE = 'silkscreen_news';
