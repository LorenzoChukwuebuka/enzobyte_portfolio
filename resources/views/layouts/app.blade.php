<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @vite('resources/js/app.ts')
    @vite('resources/css/app.css')

    {{-- =========================
         Primary Meta
    ========================= --}}
    <title>
        {{ setting('site_name', 'Enzobyte Technology') }}
        - @yield('page_title', setting('site_tagline', 'Innovative Tech Solutions for Modern Businesses'))
    </title>

    <meta name="title" content="{{ setting('site_name', 'Enzobyte Technology') }}">
    <meta name="description" content="@yield('meta_description', setting('meta_description', 'Enzobyte Technology delivers cutting-edge web development, mobile apps, UI/UX design, and digital solutions.'))">
    <meta name="keywords" content="@yield('keywords', setting('meta_keywords', 'web development, mobile app development, software development, digital solutions'))">
    <meta name="author" content="{{ setting('site_name', 'Enzobyte Technology') }}">

    {{-- =========================
         Canonical
    ========================= --}}
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    {{-- =========================
         Open Graph
    ========================= --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:title" content="@yield('og_title', setting('site_name', 'Enzobyte Technology'))">
    <meta property="og:description" content="@yield('og_description', setting('meta_description', 'Transform your business with cutting-edge technology solutions.'))">
    <meta property="og:image" content="@yield('og_image', asset(setting('site_logo') ?? '/images/og-image.jpg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ setting('site_name', 'Enzobyte Technology') }}">

    {{-- =========================
         Twitter
    ========================= --}}
    <meta name="twitter:card" content="summary_large_image">

    <meta name="twitter:url" content="@yield('twitter_url', url()->current())">

    <meta name="twitter:title" content="@yield('twitter_title', setting('site_name', 'Enzobyte Technology'))">

    <meta name="twitter:description" content="@yield('twitter_description', setting('meta_description', 'Transform your business with cutting-edge technology solutions.'))">

    <meta name="twitter:image" content="@yield('twitter_image', asset(setting('site_logo') ?? '/images/og-image.jpg'))">

    {{-- =========================
         SEO Enhancements
    ========================= --}}
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">



    @verbatim
       
        <!-- =======================
                 Enzobyte SEO & Schema
                 ======================= -->

        <!-- Main Organization + LocalBusiness Schema -->
        <script type="application/ld+json">
@json([
    "@context" => "https://schema.org",
    "@type" => "ProfessionalService",
    "name" => setting('site_name'),
    "url" => url('/'),
    "logo" => url(setting('site_logo')),
    "description" => setting('site_tagline'),
    "email" => setting('contact_email'),
    "areaServed" => [
        "@type" => "Country",
        "name" => "Nigeria"
    ],
    "address" => [
        "@type" => "PostalAddress",
        "addressCountry" => "NG",
        "addressRegion" => "Enugu State",
        "addressLocality" => "Enugu"
    ],
    "sameAs" => [
        setting('social_linkedin'),
        setting('social_twitter'),
        setting('social_github')
    ],
    "knowsAbout" => [
        "Web Development",
        "Laravel Development",
        "Vue.js Development",
        "Golang API Development",
        "SaaS Development",
        "Enterprise Software Systems"
    ]
])
</script>

        <!-- Website Schema -->
        <script type="application/ld+json">
@json([
    "@context" => "https://schema.org",
    "@type" => "WebSite",
    "name" => setting('site_name'),
    "url" => url('/'),
    "description" => setting('meta_description'),
    "publisher" => [
        "@type" => "Organization",
        "name" => setting('site_name')
    ]
])
</script>



        <!-- Breadcrumb Schema (Use on Inner Pages) -->
        @if (isset($pageTitle))
            <script type="application/ld+json">
@json([
    "@context" => "https://schema.org",
    "@type" => "BreadcrumbList",
    "itemListElement" => [
        [
            "@type" => "ListItem",
            "position" => 1,
            "name" => "Home",
            "item" => url('/')
        ],
        [
            "@type" => "ListItem",
            "position" => 2,
            "name" => $pageTitle,
            "item" => url()->current()
        ]
    ]
])
</script>
        @endif



    @endverbatim



</head>

<body class="antialiased">
    <div id="app">
        @yield('content')
    </div>
</body>

</html>
