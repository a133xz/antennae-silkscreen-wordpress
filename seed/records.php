<?php
/**
 * Demo content mirrored from antennae-silkscreen/src/content/records.ts
 */

return array(
    'settings' => array(
        'tagline' => 'Film production company based in London, UK',
        'copyright' => '©silkscreen 2026',
        'social_handle' => '@silkscreen_ent',
        'social_url' => 'https://instagram.com/silkscreen_ent',
        'statement' => 'Silkscreen is a film production company that prioritises artist-driven storytelling, cultural engagement, and bold creative expression.',
        'contact_intro' => 'If you would like to get in touch, you can reach us at the addresses below.',
        'shop_available' => 1,
        'emails' => array(
            array('label' => 'General Inquiries', 'address' => 'hello@silkscreen.com'),
            array('label' => 'Press', 'address' => 'press@silkscreen.com'),
            array('label' => 'Collaborations', 'address' => 'collaborations@silkscreen.com'),
        ),
    ),
    'projects' => array(
        array(
            'title' => 'Two Neighbours',
            'slug' => 'two-neighbours',
            'synopsis' => 'A struggling writer and an insatiable socialite become entangled in a dangerous conflict fueled by a modern-day genie who promises to grant their deepest, darkest wishes.',
            'director' => 'Ondine Viñao',
            'image' => 'still-two-neighbours.png',
            'image_alt' => 'Still from Two Neighbours',
            'stills' => array('still-two-neighbours.png', 'still-two-neighbours-2.png'),
            'poster' => 'poster-two-neighbours-detail.png',
            'poster_alt' => 'Two Neighbours poster',
            'featured' => 1,
            'listed' => 1,
            'on_posters' => 0,
            'order' => 1,
            'credits' => array(
                array('role' => 'directed', 'names' => array('Ondine Viñao')),
                array('role' => 'produced', 'names' => array('Ivy Freeman-Attwood')),
                array('role' => 'executive-producers', 'names' => array('Brendan Brulon', 'Kian Benjamin', 'Sean Croft')),
                array('role' => 'companies', 'names' => array('Silkscreen Entertainment', '& Portal Pictures')),
                array('role' => 'starring', 'names' => array('Anya Chalotra', 'Chloe Cherry', 'Ralph Ineson', 'Jake Simmance', 'Taz Skylar', 'William Hope', 'Samuel Anderson', 'Ivy Freeman-Attwood')),
            ),
        ),
        array('title' => 'A Different Man', 'slug' => 'a-different-man', 'director' => 'Harry Lighton', 'image' => 'still-different-man.png', 'image_alt' => 'Still from A Different Man', 'stills' => array('still-different-man.png'), 'featured' => 1, 'listed' => 1, 'order' => 2),
        array('title' => 'How to Make a Killing', 'slug' => 'how-to-make-a-killing', 'director' => 'John Patton Ford', 'image' => 'still-killing.png', 'image_alt' => 'Still from How to Make a Killing', 'stills' => array('still-killing.png'), 'featured' => 0, 'listed' => 1, 'order' => 3),
        array('title' => 'Sorry, Baby', 'slug' => 'sorry-baby', 'director' => 'Eva Victor', 'image' => 'still-sorry-baby.png', 'image_alt' => 'Still from Sorry, Baby', 'stills' => array('still-sorry-baby.png'), 'featured' => 0, 'listed' => 1, 'order' => 4),
        array('title' => 'Pearl', 'slug' => 'pearl', 'director' => 'Ti West', 'image' => 'poster-pearl.png', 'image_alt' => 'Pearl poster', 'poster' => 'poster-pearl.png', 'poster_alt' => 'Pearl poster', 'featured' => 0, 'listed' => 0, 'on_posters' => 1, 'order' => 1),
        array('title' => 'Aftersun', 'slug' => 'aftersun', 'director' => 'Charlotte Wells', 'image' => 'poster-aftersun.png', 'image_alt' => 'Aftersun poster', 'poster' => 'poster-aftersun.png', 'poster_alt' => 'Aftersun poster', 'featured' => 0, 'listed' => 0, 'on_posters' => 1, 'order' => 2),
        array('title' => 'Priscilla', 'slug' => 'priscilla', 'director' => 'Sofia Coppola', 'image' => 'poster-priscilla.png', 'image_alt' => 'Priscilla poster', 'poster' => 'poster-priscilla.png', 'poster_alt' => 'Priscilla poster', 'featured' => 0, 'listed' => 0, 'on_posters' => 1, 'order' => 3),
        array('title' => 'Eddington', 'slug' => 'eddington', 'director' => 'Ari Aster', 'image' => 'poster-eddington.png', 'image_alt' => 'Eddington poster', 'poster' => 'poster-eddington.png', 'poster_alt' => 'Eddington poster', 'featured' => 0, 'listed' => 0, 'on_posters' => 1, 'order' => 4),
        array('title' => 'Backrooms', 'slug' => 'backrooms', 'director' => '', 'image' => 'poster-backrooms.png', 'image_alt' => 'Backrooms poster', 'poster' => 'poster-backrooms.png', 'poster_alt' => 'Backrooms poster', 'featured' => 0, 'listed' => 0, 'on_posters' => 1, 'order' => 5),
        array('title' => 'The Drama', 'slug' => 'the-drama', 'director' => 'Kristoffer Borgli', 'image' => 'poster-the-drama.png', 'image_alt' => 'The Drama poster', 'poster' => 'poster-the-drama.png', 'poster_alt' => 'The Drama poster', 'featured' => 0, 'listed' => 0, 'on_posters' => 1, 'order' => 6),
    ),
    'news' => array(
        array('title' => 'The soap maker directed by Claire Dennis', 'kicker' => 'Coming soon', 'date_label' => '2.5.26', 'image' => 'news-soap.png', 'image_alt' => 'Portrait of Claire Dennis', 'size' => 'medium', 'excerpt' => 'A new short from Claire Dennis enters production with Silkscreen, extending the company’s work with director-led, character-focused storytelling.', 'featured' => 1, 'order' => 1),
        array('title' => 'The dispute starts filming', 'kicker' => 'Coming soon', 'date_label' => '2.5.26', 'image' => 'news-dispute.png', 'image_alt' => '', 'size' => 'wide', 'excerpt' => 'Principal photography is underway on The Dispute, with cast and crew now on location for the first block of shooting.', 'featured' => 1, 'order' => 2),
        array('title' => 'Two neighbours premieres at Edinburgh film festival', 'kicker' => 'Premiere', 'date_label' => '2.5.26', 'image' => 'news-edinburgh.png', 'image_alt' => 'Festival still from Two Neighbours', 'size' => 'narrow', 'excerpt' => 'Two Neighbours will premiere at Edinburgh, marking the film’s first festival outing ahead of its wider release.', 'project_slug' => 'two-neighbours', 'featured' => 1, 'order' => 3),
        array('title' => 'Behind the scenes at the dispute', 'kicker' => 'BTS', 'date_label' => '2.5.26', 'image' => 'news-bts-frame.png', 'image_alt' => 'Behind the scenes portrait', 'size' => 'half', 'excerpt' => 'On-set photography from The Dispute, captured during a key sequence in the current production block.', 'featured' => 0, 'order' => 4),
        array('title' => 'Two neighbours European premiere', 'kicker' => 'Award', 'date_label' => '2.5.26', 'image' => 'news-europe-frame.png', 'image_alt' => 'Two Neighbours at a European festival', 'size' => 'half-short', 'excerpt' => 'Two Neighbours continues its festival run with an European premiere following strong reception on the circuit.', 'project_slug' => 'two-neighbours', 'featured' => 0, 'order' => 5),
        array('title' => 'The dispute starts filming', 'kicker' => 'Coming soon', 'date_label' => '2.5.26', 'image' => 'news-dispute.png', 'image_alt' => '', 'size' => 'wide', 'featured' => 0, 'order' => 6),
        array('title' => 'Two neighbours premieres at Edinburgh film festival', 'kicker' => 'Premiere', 'date_label' => '2.5.26', 'image' => 'news-edinburgh-short.png', 'image_alt' => 'Festival still from Two Neighbours', 'size' => 'small', 'project_slug' => 'two-neighbours', 'featured' => 0, 'order' => 7),
        array('title' => 'The soap maker directed by Claire Dennis', 'kicker' => 'Coming soon', 'date_label' => '2.5.26', 'image' => 'news-soap.png', 'image_alt' => 'Portrait of Claire Dennis', 'size' => 'medium', 'featured' => 0, 'order' => 8),
    ),
    'products' => array(
        array('title' => 'Tote bag', 'slug' => 'tote-bag', 'price' => 50, 'image' => 'product-tote-cut.png', 'alt' => 'Silkscreen tote bag', 'featured' => 0),
        array('title' => 'T-shirt', 'slug' => 't-shirt', 'price' => 30, 'image' => 'product-tshirt.png', 'alt' => 'Black Silkscreen t-shirt', 'featured' => 0),
        array('title' => 'Zine', 'slug' => 'zine', 'price' => 20, 'image' => 'product-zine.png', 'alt' => 'Silkscreen zine', 'featured' => 0),
        array('title' => 'Lighter', 'slug' => 'lighter', 'price' => 50, 'image' => 'product-lighter-cut.png', 'alt' => 'Silkscreen lighter', 'featured' => 1),
        array('title' => 'Postcards', 'slug' => 'postcards', 'price' => 50, 'image' => 'product-postcards-cut.png', 'alt' => 'Stack of Silkscreen postcards', 'featured' => 1),
        array('title' => 'Skateboard', 'slug' => 'skateboard', 'price' => 80, 'image' => 'product-skateboard-cut.png', 'alt' => 'Two Silkscreen skateboards', 'featured' => 1),
    ),
);
