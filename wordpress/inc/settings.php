<?php

function silkscreen_settings() {
    static $settings = null;
    if ($settings !== null) {
        return $settings;
    }

    $front_id = silkscreen_front_id();

    $emails = array();
    if ($front_id && function_exists('get_field')) {
        $rows = get_field('contact_emails', $front_id);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $label = isset($row['label']) ? trim((string) $row['label']) : '';
                $address = isset($row['address']) ? trim((string) $row['address']) : '';
                if ($address !== '') {
                    $emails[] = array('label' => $label, 'address' => $address);
                }
            }
        }
    }
    if (!$emails) {
        $emails = array(
            array('label' => 'General Inquiries', 'address' => 'hello@silkscreen.com'),
            array('label' => 'Press', 'address' => 'press@silkscreen.com'),
            array('label' => 'Collaborations', 'address' => 'collaborations@silkscreen.com'),
        );
    }

    $hero = array();
    $slides = $front_id ? silkscreen_gallery('hero_slides', $front_id) : array();
    if ($slides) {
        $hero = $slides;
    } else {
        foreach (array('hero-1.png', 'hero-2.png', 'hero-3.png', 'hero-4.png') as $i => $file) {
            $hero[] = array(
                'src' => silkscreen_asset('media/' . $file),
                'alt' => 'Silkscreen hero still ' . ($i + 1),
            );
        }
    }

    $settings = array(
        'tagline' => silkscreen_text('tagline', $front_id, SILKSCREEN_TAGLINE),
        'copyright' => silkscreen_text('copyright', $front_id, SILKSCREEN_COPYRIGHT),
        'social_handle' => silkscreen_text('social_handle', $front_id, SILKSCREEN_SOCIAL_HANDLE),
        'social_url' => silkscreen_url('social_url', $front_id, SILKSCREEN_SOCIAL_URL),
        'statement' => silkscreen_text('statement', $front_id, SILKSCREEN_STATEMENT),
        'contact_intro' => silkscreen_text('contact_intro', $front_id, SILKSCREEN_CONTACT_INTRO),
        'emails' => $emails,
        'shop_enabled' => silkscreen_bool('shop_enabled', $front_id, true),
        'posters_enabled' => silkscreen_bool('posters_enabled', $front_id, true),
        'hero' => $hero,
    );

    return $settings;
}
